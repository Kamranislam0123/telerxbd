<?php
/**
 * Doctor Chat Page
 * Loads conversation between logged-in doctor and a patient (by patient_id or appointment_id)
 */

$config_path = __DIR__ . '/php/config.php';
if (!file_exists($config_path)) {
    header('Location: login.php');
    exit;
}
require_once $config_path;

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['doctor_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$doctor_id = (int)$_SESSION['doctor_id'];
$sender_account = 'doctor_' . $doctor_id;

$conn = null;
try {
    $conn = getDBConnection();

    $patient_id = null;
    if (!empty($_GET['patient_id'])) {
        $patient_id = (int)$_GET['patient_id'];
    } elseif (!empty($_GET['appointment_id'])) {
        $appointment_id = (int)$_GET['appointment_id'];
        $stmt = $conn->prepare("SELECT patient_id FROM appointments WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $appointment_id);
        $stmt->execute();
        $r = $stmt->get_result();
        if ($r && $r->num_rows) {
            $row = $r->fetch_assoc();
            $patient_id = (int)$row['patient_id'];
        }
        $stmt->close();
    }

    if (!$patient_id) {
        throw new Exception('Missing patient_id or appointment context.');
    }

    $receiver_account = 'patient_' . $patient_id;

    // Fetch patient info
    $patient = ['name' => 'Patient', 'profile_image' => 'assets/img/patients/patient.jpg'];
    $stmt = $conn->prepare("SELECT name, profile_image FROM patients WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $patient_id);
    $stmt->execute();
    $r = $stmt->get_result();
    if ($r && $r->num_rows) {
        $patient = array_merge($patient, $r->fetch_assoc());
    }
    $stmt->close();

    // Load messages
    $messages = [];
    $mstmt = $conn->prepare("SELECT id, sender_account, receiver_account, message, created_at FROM chat_messages WHERE (sender_account=? AND receiver_account=?) OR (sender_account=? AND receiver_account=?) ORDER BY created_at ASC");
    if ($mstmt) {
        $mstmt->bind_param('ssss', $sender_account, $receiver_account, $receiver_account, $sender_account);
        $mstmt->execute();
        $mres = $mstmt->get_result();
        while ($mrow = $mres->fetch_assoc()) {
            $messages[] = $mrow;
        }
        $mstmt->close();
    }

    $conn->close();

} catch (Exception $e) {
    error_log('Chat (doctor) error: ' . $e->getMessage());
    die('An error occurred loading the chat.');
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>TeleRx - Doctor Chat</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<!-- Favicon -->
		<link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

		<!-- Theme Settings Js -->
		<script src="assets/js/theme-script.js"></script>
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css">
		
		<!-- Fontawesome CSS -->
		<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
		<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

		<!-- Iconsax CSS-->
		<link rel="stylesheet" href="assets/css/iconsax.css">

		<!-- Feathericon CSS -->
    	<link rel="stylesheet" href="assets/css/feather.css">

		<!-- Swiper CSS -->
		<link rel="stylesheet" href="assets/plugins/swiper/swiper.min.css">
		
		<!-- Main CSS -->
		<link rel="stylesheet" href="assets/css/custom.css">
	</head>
<body class="main-chat-blk">

		<!-- Main Wrapper -->
		<div class="main-wrapper">
		
			<!-- Header -->
			<?php include 'header.php'; ?>
			<!-- /Header -->
			
			<div class="page-wrapper chat-page-wrapper">
				<div class="container">

					<div class="content doctor-content">

						<div class="chat-sec">

							<!-- sidebar group -->
							<div class="sidebar-group left-sidebar chat_sidebar">
								<!-- Chats sidebar -->
								<div id="chats" class="left-sidebar-wrap sidebar active slimscroll">
									<div class="slimscroll-active-sidebar">
									   <!-- Left Chat Title -->
									   <div class="left-chat-title all-chats">
											<div class="setting-title-head">
												<h4> All Chats</h4>
											</div>
									   </div>
									   <!-- /Left Chat Title -->
									   <div class="sidebar-body chat-body" id="chatsidebar">
											<!-- Recent Chat -->
											<div class="d-flex justify-content-between align-items-center ps-0 pe-0">
												<div class="fav-title pin-chat">
													<h6>Conversation</h6>
												</div>
											</div>
											<ul class="user-list">
												<li class="user-list-item active">
													<a href="javascript:void(0);">
														<div class="avatar avatar-online">
															<img src="<?php echo htmlspecialchars($patient['profile_image']); ?>" class="rounded-circle" alt="image">
														</div>
														<div class="users-list-body">
															<div>
																<h5><?php echo htmlspecialchars($patient['name']); ?></h5>
																<p>Online</p>
															</div>
														</div>
													</a>
												</li>
											</ul>
										</div>
									</div>
								</div>
								<!-- / Chats sidebar -->
							</div>
							<!-- /Sidebar group -->

							<!-- Chat -->
							<div class="chat chat-messages" id="middle">
								<div class="slimscroll">
									<div class="chat-inner-header">
										<div class="chat-header">
											<div class="user-details">
												<div class="d-lg-none">
													<ul class="list-inline mt-2 me-2">
														<li class="list-inline-item">
															<a class="text-muted px-0 left_sides" href="#" data-chat="open">
																<i class="fas fa-arrow-left"></i>
															</a>
														</li>
													</ul>
												</div>
												<figure class="avatar avatar-online">
													<img src="<?php echo htmlspecialchars($patient['profile_image']); ?>" alt="image">
												</figure>
												<div class="mt-1">
													<h5><?php echo htmlspecialchars($patient['name']); ?></h5>
													<small class="last-seen">Online</small>
												</div>
											</div>
											<div class="chat-options ">
												<ul class="list-inline">
													<li class="list-inline-item" >
														<a href="javascript:void(0)" class="btn btn-outline-light chat-search-btn" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Search">
															<i class="fa-solid fa-magnifying-glass"></i>
														</a>
													</li>
													<li class="list-inline-item">
														<a class="btn btn-outline-light no-bg" href="#" data-bs-toggle="dropdown">
															<i class="fa-solid fa-ellipsis-vertical"></i>
														</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
									<div class="chat-body" style="height: calc(100vh - 350px); overflow-y: auto;">
										<div class="messages" id="messages">
											<?php foreach ($messages as $m):
												$is_sent = ($m['sender_account'] === $sender_account);
												$msg_html = nl2br(htmlspecialchars($m['message']));
												$created = date('h:i A', strtotime($m['created_at']));
											?>
											<div class="chats <?php echo $is_sent ? 'chats-right' : ''; ?>">
												<?php if (!$is_sent): ?>
												<div class="chat-avatar">
													<img src="<?php echo htmlspecialchars($patient['profile_image']); ?>" class="dreams_chat" alt="image">
												</div>
												<?php endif; ?>
												<div class="chat-content">
													<div class="chat-profile-name <?php echo $is_sent ? 'text-end justify-content-end' : ''; ?>">
														<h6><?php echo $is_sent ? 'You' : htmlspecialchars($patient['name']); ?><span><?php echo $created; ?></span></h6>
													</div>
													<div class="message-content"><?php echo $msg_html; ?></div>
												</div>
												<?php if ($is_sent): ?>
												<div class="chat-avatar">
													<img src="<?php echo htmlspecialchars($_SESSION['profile_image'] ?? 'assets/img/doctors/doctor-01.jpg'); ?>" class="dreams_chat" alt="image">
												</div>
												<?php endif; ?>
											</div>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
								<div class="chat-footer">
									<form id="chat_form">
										<div class="smile-foot">
											<div class="chat-action-btns">
												<div class="chat-action-col">
													<a class="action-circle" href="#" data-bs-toggle="dropdown">
														<i class="fa-solid fa-ellipsis-vertical"></i>
													</a>
													<div class="dropdown-menu dropdown-menu-end" >
														<a href="#" class="dropdown-item "><span><i class="fa-solid fa-file-lines"></i></span>Document</a>
														<a href="#" class="dropdown-item"><span><i class="fa-solid fa-camera"></i></span>Camera</a>
														<a href="#" class="dropdown-item"><span><i class="fa-solid fa-image"></i></span>Gallery</a>
													</div>
												</div>
											</div>
										</div>
										<div class="smile-foot emoj-action-foot">
											<a href="#" class="action-circle"><i class="fa-regular fa-face-smile"></i></a>
										</div>
										<div class="smile-foot">
											<a href="#" class="action-circle"><i class="isax isax-microphone-2"></i></a>
										</div>
										<input type="text" id="message_input" class="form-control chat_form" placeholder="Type your message here...">
										<div class="form-buttons">
											<button class="btn send-btn" type="submit">
												<i class="isax isax-send-25"></i>
											</button>
										</div>
									</form>
								</div>
							</div>
							<!-- /Chat -->
						</div>
					</div>
				</div>
			</div>		   
		</div>
		<!-- /Main Wrapper -->
		
		<!-- jQuery -->
		<script src="assets/js/jquery-3.7.1.min.js"></script>
		<!-- Bootstrap Core JS -->
		<script src="assets/js/bootstrap.bundle.min.js"></script>
		<!-- Swiper JS -->
		<script src="assets/plugins/swiper/swiper.min.js"></script>
		<!-- Custom JS -->
		<script src="assets/js/script.js"></script>

        <!-- Agora RTM SDK -->
        <script src="https://cdn.agora.io/sdk/release/agora-rtm-sdk-1.4.3.js"></script>
        <script src="assets/js/agora-chat.js"></script>

    <script>
    (function(){
        var sender_account = <?php echo json_encode($sender_account); ?>;
        var receiver_account = <?php echo json_encode($receiver_account); ?>;
        var patientName = <?php echo json_encode($patient['name']); ?>;
        var peerImg = <?php echo json_encode($patient['profile_image']); ?>;
        var myImg = <?php echo json_encode($_SESSION['profile_image'] ?? 'assets/img/doctors/doctor-01.jpg'); ?>;

        function appendMessage(msgHtml, sent, time) {
            var cls = sent ? 'chats chats-right' : 'chats';
            var $el = $('<div>').addClass(cls);
            
            if (!sent) {
                $el.append($('<div>').addClass('chat-avatar').append($('<img>').attr('src', peerImg).addClass('dreams_chat')));
            }

            var $content = $('<div>').addClass('chat-content');
            var $profile = $('<div>').addClass('chat-profile-name');
            if (sent) $profile.addClass('text-end justify-content-end');
            
            $profile.append($('<h6>').text(sent ? 'You' : patientName).append($('<span>').text(time)));
            $content.append($profile);
            $content.append($('<div>').addClass('message-content').html(msgHtml));
            
            $el.append($content);

            if (sent) {
                $el.append($('<div>').addClass('chat-avatar').append($('<img>').attr('src', myImg).addClass('dreams_chat')));
            }

            $('#messages').append($el);
            $('.chat-body').scrollTop($('.chat-body')[0].scrollHeight);
        }

        // Global incoming handler for RTM
        window.onIncomingRtmMessage = function(text, peerId) {
            if (peerId === receiver_account) {
                var time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                appendMessage(text.replace(/\n/g, '<br>'), false, time);
            }
        };

        $('#chat_form').on('submit', function(e){
            e.preventDefault();
            var message = $('#message_input').val().trim();
            if (!message) return;

            // 1. Send via RTM for real-time appearance
            if (window.sendRtmMessage) {
                window.sendRtmMessage(receiver_account, message);
            }

            // 2. Persist to DB and update local UI
            $.post('api/send_message.php', { receiver: receiver_account, message: message }, function(resp){
                if (resp && resp.success) {
                    var time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    appendMessage(message.replace(/\n/g, '<br>'), true, time);
                    $('#message_input').val('');
                } else {
                    alert(resp && resp.error ? resp.error : 'Send failed');
                }
            }, 'json');
        });

        // Polling removed in favor of Agora RTM
        $('.chat-body').scrollTop($('.chat-body')[0].scrollHeight);
    })();
    </script>
</body>
</html>
<?php
require_once 'php/config.php';
if (!isset($_SESSION['doctor_id'])) {
    header('Location: login.php');
    exit;
}
$doctor_id = $_SESSION['doctor_id'];

// Get all patients the doctor has appointments with
$conn = getDBConnection();
$stmt = $conn->prepare("
    SELECT DISTINCT p.id, p.name, p.profile_image 
    FROM patients p 
    JOIN appointments a ON p.id = a.patient_id 
    WHERE a.doctor_id = ?
");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$patients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$target_patient_id = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : (count($patients) > 0 ? $patients[0]['id'] : 0);
$target_patient_name = "Select a Patient";
$target_patient_img = "assets/img/patients/patient.jpg";

foreach ($patients as $p) {
    if ($p['id'] == $target_patient_id) {
        $target_patient_name = $p['name'];
        if (!empty($p['profile_image'])) $target_patient_img = $p['profile_image'];
        break;
    }
}
?>
<!DOCTYPE html> 
<html lang="en">
	<head>
		
		<meta charset="utf-8">
		<title>Doccure</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta name="keywords" content="practo clone, doccure, doctor appointment, Practo clone html template, doctor booking template">
		<meta name="author" content="Practo Clone HTML Template - Doctor Booking Template">
		<meta property="og:url" content="https://doccure.dreamstechnologies.com/html/">
		<meta property="og:type" content="website">
		<meta property="og:title" content="Doctors Appointment HTML Website Templates | Doccure">
		<meta property="og:description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta property="og:image" content="assets/img/preview-banner.jpg">
		<meta name="twitter:card" content="summary_large_image">
		<meta property="twitter:domain" content="https://doccure.dreamstechnologies.com/html/">
		<meta property="twitter:url" content="https://doccure.dreamstechnologies.com/html/">
		<meta name="twitter:title" content="Doctors Appointment HTML Website Templates | Doccure">
		<meta name="twitter:description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta name="twitter:image" content="assets/img/preview-banner.jpg">	
		
		<!-- Favicon -->
		<link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

		<!-- Apple Touch Icon -->
		<link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">

		<!-- Theme Settings Js -->
		<script src="assets/js/theme-script.js"></script>
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css">
				
		<!-- Fontawesome CSS -->
		<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
		<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

		<!-- Iconsax CSS-->
		<link rel="stylesheet" href="assets/css/iconsax.css">
		
        <!-- select CSS -->
		<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

		<!-- Feathericon CSS -->
    	<link rel="stylesheet" href="assets/css/feather.css">

    	<!-- Datepicker CSS -->
		<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

		<!-- Owl Carousel CSS -->
		<link rel="stylesheet" href="assets/css/owl.carousel.min.css">

		<!-- Animation CSS -->
		<link rel="stylesheet" href="assets/css/aos.css">
		
		<!-- Main CSS -->
		<link rel="stylesheet" href="assets/css/custom.css">

	</head>		
	<body>

		<!-- Main Wrapper -->
		<div class="main-wrapper home-one">
					
			<!-- Header -->
			<header class="header header-custom header-fixed header-one home-head-one">
				<div class="container">
					<nav class="navbar navbar-expand-lg header-nav">
						<div class="navbar-header">
							<a id="mobile_btn" href="javascript:void(0);">
								<span class="bar-icon">
									<span></span>
									<span></span>
									<span></span>
								</span>
							</a>
							<a href="index.html" class="navbar-brand logo">
								<img src="assets/img/logo.svg" class="img-fluid" alt="Logo">
							</a>
						</div>
						<div class="main-menu-wrapper">
							<div class="menu-header">
								<a href="index.html" class="menu-logo">
									<img src="assets/img/logo.svg" class="img-fluid" alt="Logo">
								</a>
								<a id="menu_close" class="menu-close" href="javascript:void(0);">
									<i class="fas fa-times"></i>
								</a>
							</div>
							<ul class="main-nav">
								<li class="has-submenu megamenu active">
									<a href="index.html">Home </a></li>
								<li><a href="search.php">Doctor List</a></li>
								<li><a href="doctors.php">Doctor List</a></li>
								<li><a href="doctor-profile.php">Doctor Profile</a></li>
								<li><a href="about-us.php">About Us</a></li>
								<li><a href="contact-us.php">Contact</a></li>
								<li><a href="blog.php">Blog</a></li>
								<li class="login-link"><a href="login.php">Login / Signup</a></li>
							</ul>
						</div>
						<ul class="nav header-navbar-rht">
							<!-- Notifications -->
							<li class="nav-item dropdown noti-nav me-3 pe-0">
								<a href="#" class="dropdown-toggle active-dot active-dot-danger nav-link p-0" data-bs-toggle="dropdown">
									<i class="isax isax-notification-bing"></i>
								</a>
								<div class="dropdown-menu notifications dropdown-menu-end ">
									<div class="topnav-dropdown-header">
										<span class="notification-title">Notifications</span>
									</div>
									<div class="noti-content">
										<ul class="notification-list">
											<li class="notification-message">
												<a href="#">
													<div class="notify-block d-flex">
														<span class="avatar">
															<img class="avatar-img" alt="Ruby perin" src="assets/img/clients/client-01.jpg">
														</span>
														<div class="media-body">
															<h6>Travis Tremble <span class="notification-time">18.30 PM</span></h6>
															<p class="noti-details">Sent a amount of $210 for his Appointment  <span class="noti-title">Dr.Ruby perin </span></p>
														</div>
													</div>
												</a>
											</li>
											<li class="notification-message">
												<a href="#">
													<div class="notify-block d-flex">
														<span class="avatar">
															<img class="avatar-img" alt="Hendry Watt" src="assets/img/clients/client-02.jpg">
														</span>
														<div class="media-body">
															<h6>Travis Tremble <span class="notification-time">12 Min Ago</span></h6>
															<p class="noti-details"> has booked her appointment to  <span class="noti-title">Dr. Hendry Watt</span></p>
														</div>
													</div>
												</a>
											</li>
											<li class="notification-message">
												<a href="#">
													<div class="notify-block d-flex">
														<div class="avatar">
															<img class="avatar-img" alt="Maria Dyen" src="assets/img/clients/client-03.jpg">
														</div>
														<div class="media-body">
															<h6>Travis Tremble <span class="notification-time">6 Min Ago</span></h6>
															<p class="noti-details"> Sent a amount  $210 for his Appointment   <span class="noti-title">Dr.Maria Dyen</span></p>
														</div>
													</div>
												</a>
											</li>
											<li class="notification-message">
												<a href="#">
													<div class="notify-block d-flex">
														<div class="avatar avatar-sm">
															<img class="avatar-img" alt="client-image" src="assets/img/clients/client-04.jpg">
														</div>
														<div class="media-body">
															<h6>Travis Tremble <span class="notification-time">8.30 AM</span></h6>
															<p class="noti-details"> Send a message to his doctor</p>
														</div>
													</div>
												</a>
											</li>
										</ul>
									</div>
								</div>
							</li>
							<!-- /Notifications -->

							<!-- Messages -->
							<li class="nav-item noti-nav me-3 pe-0">
								<a href="chat-doctor.html" class="dropdown-toggle nav-link active-dot active-dot-success p-0">
									<i class="isax isax-message-2"></i>
								</a>
							</li>
							<!-- /Messages -->

							<!-- User Menu -->
							<li class="nav-item dropdown has-arrow logged-item">
								<a href="#" class="nav-link ps-0" data-bs-toggle="dropdown">
									<span class="user-img">
										<img class="rounded-circle" src="assets/img/doctors-dashboard/doctor-profile-img.jpg" width="31" alt="Darren Elder">
									</span>
								</a>
								<div class="dropdown-menu dropdown-menu-end">
									<div class="user-header">
										<div class="avatar avatar-sm">
											<img src="assets/img/doctors-dashboard/doctor-profile-img.jpg" alt="User Image" class="avatar-img rounded-circle">
										</div>
										<div class="user-text">
											<h6>Dr Edalin Hendry</h6>
											<p class="text-muted mb-0">Doctor</p>
										</div>
									</div>
									<a class="dropdown-item" href="doctor-dashboard.html">Dashboard</a>
									<a class="dropdown-item" href="doctor-profile-settings.html">Profile Settings</a>
									<a class="dropdown-item" href="login.php">Logout</a>
								</div>
							</li>
							<!-- /User Menu -->
						</ul>
					</nav>
				</div>
			</header>
			<!-- /Header -->		

			

		   
		</div>
		<!-- /Main Wrapper -->
		
		<!-- Voice Call Modal -->
		<div class="modal fade call-modal" id="voice_call">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-body">
					
						<!-- Outgoing Call -->
						<div class="call-box incoming-box">
							<div class="call-wrapper">
								<div class="call-inner">
									<div class="call-user">
										<img alt="User Image" src="assets/img/patients/patient.jpg" class="call-avatar">
										<h4>Richard Wilson</h4>
										<span>Connecting...</span>
									</div>							
									<div class="call-items">
										<a href="javascript:void(0);" class="btn call-item call-end" data-bs-dismiss="modal" aria-label="Close"><i class="material-icons">call_end</i></a>
										<a href="voice-call.html" class="btn call-item call-start"><i class="material-icons">call</i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- Outgoing Call -->

					</div>
				</div>
			</div>
		</div>
		<!-- /Voice Call Modal -->
		
		<!-- Video Call Modal -->
		<div class="modal fade call-modal" id="video_call">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-body">
					
						<!-- Incoming Call -->
						<div class="call-box incoming-box">
							<div class="call-wrapper">
								<div class="call-inner">
									<div class="call-user">
										<img class="call-avatar" src="assets/img/patients/patient.jpg" alt="User Image">
										<h4>Richard Wilson</h4>
										<span>Calling ...</span>
									</div>							
									<div class="call-items">
										<a href="javascript:void(0);" class="btn call-item call-end" data-bs-dismiss="modal" aria-label="Close"><i class="material-icons">call_end</i></a>
										<a href="video-call.html" class="btn call-item call-start"><i class="material-icons">videocam</i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Incoming Call -->
						
					</div>
				</div>
			</div>
		</div>
		<!-- Video Call Modal -->
	  
		<!-- jQuery -->
		<script src="assets/js/jquery-3.7.1.min.js"></script>

		<!-- Slimscroll JS -->
		<!--script src="assets/js/jquery.slimscroll.min.js"></script-->
		
		<!-- Bootstrap Core JS -->
		<script src="assets/js/bootstrap.bundle.min.js"></script>
	
		<!-- Swiper JS -->
		<script src="assets/plugins/swiper/swiper.min.js"></script>
		
		<!-- Agora Chat SDK -->
		<script src="https://cdn.agora.io/sdk/release/agora-rtm-sdk-1.5.0.js"></script>
		<!-- Agora Chat Custom JS -->
		<script src="assets/js/agora-chat.js"></script>

		<!-- Custom JS -->
		<script src="assets/js/script.js"></script>
		
	</body>
</html>

