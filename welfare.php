<?php
session_start();
include 'header.php';

// Dynamic counters
$total_patient = 500;
$total_donors = 12;
$total_doctors = 35;
$total_districts = 21;

// Bank and bKash details
$bank_name = "Eastern Bank PLC";
$bank_account = "1071450005619";
$account_name = "MD MEHEDI HASSAN";
$bkash_number = "01933-890894 (Personal)";

// --- Dummy data for the people list popup ---
// In a real application, this data would come from a database.
$people_list = [
    "Md. Abdur Rahman", "Fatema Begum", "Md. Shahidul Islam", "Nasrin Sultana", "Md. Rofiqul Islam",
    "Mst. Hasina Akter", "Md. Jahangir Alam", "Rabeya Khatun", "Md. Abul Kalam Azad", "Shirin Akter",
    "Md. Mizanur Rahman", "Parul Begum", "Md. Golam Mostofa", "Selina Begum", "Md. Shamsul Haque",
    "Rokeya Begum", "Md. Delwar Hossain", "Jahanara Begum", "Md. Nurul Islam", "Ayesha Begum",
    "Md. Mahbubur Rahman", "Momena Khatun", "Md. Shafiqul Islam", "Saleha Begum", "Md. Abul Hossain",
    "Amena Begum", "Md. Abdul Mannan", "Kulsum Begum", "Md. Moklesur Rahman", "Nurjahan Begum",
    "Md. Abdur Razzak", "Shahana Akter", "Md. Anwar Hossain", "Laily Begum", "Md. Saiful Islam",
    "Rina Akter", "Md. Shahjahan Mia", "Shahanara Begum", "Md. Jahirul Islam", "Lipi Akter",
    "Md. Khorshed Alam", "Shamima Akter", "Md. Shamsul Islam", "Maksuda Begum", "Md. Aminul Islam",
    "Rokeya Akter", "Md. Faruk Hossain", "Nasima Akter", "Md. Shahin Alam", "Rumi Akter",
    "Md. Nazrul Islam", "Shathi Akter", "Md. Mosharraf Hossain", "Shila Akter", "Md. Kamal Hossain",
    "Sharmin Sultana", "Md. Shafiqul Alam", "Moushumi Akter", "Md. Rafiqul Islam", "Tania Sultana",
    "Md. Hasan Ali", "Shanta Akter", "Md. Jahid Hasan", "Moni Akter", "Md. Shakil Ahmed",
    "Sohel Rana", "Sumon Mia", "Raju Ahmed", "Shakib Khan", "Tanvir Ahmed",
    "Rakib Hasan", "Shahin Alam", "Minhaj Uddin", "Nazmul Hossain", "Rubel Hossain",
    "Sabbir Rahman", "Mehedi Hasan", "Riaz Uddin", "Sojib Mia", "Shohag Ali",
    "Masud Rana", "Tuhin Ahmed", "Nayeem Islam", "Farhan Ahmed", "Imran Hossain",
    "Shamim Reza", "Rana Hossain", "Sagor Islam", "Rimon Hossain", "Sujon Mia",
    "Hasan Mahmud", "Morshed Alam", "Shaon Ahmed", "Shahed Hossain", "Shihab Uddin",
    "Tarikul Islam", "Tomal Hossain", "Shuvo Ahmed", "Shanto Islam", "Shawon Khan",
    "Rifat Hossain", "Rasel Ahmed", "Rubel Rana", "Rony Mia", "Rocky Islam",
    "Mithun Chowdhury", "Milon Hossain", "Mintu Mia", "Manik Khan", "Mamun Islam",
    "Kawsar Ahmed", "Khalid Hasan", "Khokon Mia", "Korim Hossain", "Kamrul Islam",
    "Sohag Hossain", "Shakil Miah", "Shahidul Islam", "Sharif Hossain", "Shafiqul Islam",
    "Sahid Hossain", "Salam Miah", "Sanaul Islam", "Saidur Rahman", "Selim Hossain",
    "Rabiul Islam", "Rafiqul Islam", "Ruhul Amin", "Rustom Ali", "Rafiq Miah",
    "Robiul Hossain", "Ripon Miah", "Raju Miah", "Rana Miah", "Rasel Miah",
    "Jamal Hossain", "Jahangir Alam", "Jashim Uddin", "Jamil Hossain", "Jewel Rana",
    "Imran Miah", "Ibrahim Khalil", "Iqbal Hossain", "Ismail Hossain", "Ilias Miah",
    "Hasan Miah", "Habib Rahman", "Hanif Miah", "Halim Miah", "Helal Uddin",
    "Golam Kibria", "Golam Rabbani", "Golam Mostafa", "Gias Uddin", "Golam Rasul",
    "Farhad Hossain", "Firoz Miah", "Fazlul Haque", "Fazlur Rahman", "Faruq Hossain",
    "Elias Miah", "Enamul Haque", "Emon Hossain", "Emdadul Haque", "Ekramul Haque",
    "Dulal Miah", "Didarul Alam", "Delwar Miah", "Dilder Hossain", "Dinar Hossain",
    "Chan Miah", "Chunnu Miah", "Chandan Miah", "Chanchal Miah", "Chotu Miah",
    "Babul Miah", "Bashir Ahmed", "Badsha Miah", "Bodrul Alam", "Bikash Chowdhury",
    "Anwar Hossain", "Azizul Haque", "Ataur Rahman", "Abul Hashem", "Abdul Jalil",
    "Abdul Hamid", "Abdul Malek", "Abdur Rashid", "Abdus Salam", "Abdul Hakim",
    "Abdul Latif", "Abdul Aziz", "Abdul Karim", "Abdus Sobhan", "Abdul Gafur",
    "Mst. Rina Begum", "Mst. Mina Begum", "Mst. Lovely Begum", "Mst. Lucky Begum", "Mst. Rumi Begum",
    "Mst. Shumi Begum", "Mst. Rima Begum", "Mst. Lima Begum", "Mst. Sima Begum", "Mst. Mita Begum",
    "Mst. Tania Begum", "Mst. Mousumi Begum", "Mst. Sharmin Begum", "Mst. Shathi Begum", "Mst. Shanta Begum",
    "Mst. Rita Begum", "Mst. Mukta Begum", "Mst. Panna Begum", "Mst. Poly Begum", "Mst. Shila Begum",
    "Mst. Mili Begum", "Mst. Molly Begum", "Mst. Sonia Begum", "Mst. Sumi Begum", "Mst. Shimu Begum",
    "Anowara Begum", "Ambia Khatun", "Asma Khatun", "Asia Khatun", "Amina Khatun",
    "Bilkis Begum", "Bithi Akter", "Bina Akter", "Beli Begum", "Bani Akter",
    "Champa Begum", "Chompa Akter", "Chandni Begum", "China Begum", "Chhabi Akter",
    "Dolly Akter", "Dipika Rani", "Dilara Begum", "Dulali Begum", "Doyel Akter",
    "Eti Akter", "Eva Akter", "Ela Begum", "Eshita Akter", "Emona Akter",
    "Ferdousi Begum", "Farida Begum", "Fahmida Akter", "Farzana Akter", "Faria Akter",
    "Golapi Begum", "Gita Rani", "Gulshan Ara", "Golshan Begum", "Gita Akter",
    "Hasina Begum", "Helena Begum", "Hena Begum", "Hosneara Begum", "Hafiza Begum",
    "Ivy Akter", "Irin Akter", "Ishrat Jahan", "Ismat Ara", "Iffat Ara",
    "Julekha Begum", "Jorina Begum", "Jahanara Begum", "Jamila Begum", "Jannatul Ferdous",
    "Kohinoor Begum", "Khodeja Begum", "Khairun Begum", "Khadija Begum", "Kohinoor Akter",
    "Laily Begum", "Lata Begum", "Lovely Begum", "Lima Begum", "Liza Akter",
    "Monwara Begum", "Morium Begum", "Morsheda Begum", "Mahmuda Begum", "Maksuda Begum",
    "Nasima Akter", "Nazma Begum", "Nargis Akter", "Nadia Akter", "Nipa Akter",
    "Omar Faruq", "Obaidul Haque", "Oli Ullah", "Obaidur Rahman", "Omar Sharif",
    "Parvin Akter", "Parul Begum", "Panna Akter", "Poly Akter", "Popy Akter",
    "Rabeya Begum", "Rahima Begum", "Rokeya Begum", "Rina Begum", "Rima Akter",
    "Shahana Akter", "Shirin Akter", "Shamima Akter", "Shathi Akter", "Shanta Akter",
    "Taslima Begum", "Tania Akter", "Tumpa Akter", "Tandra Akter", "Tithi Akter",
    "Uzzal Hossain", "Ujjal Miah", "Uttam Kumar", "Uday Sankar", "Uttam Chanda",
    "Momtaz Begum", "Maksuda Akter", "Mahfuza Akter", "Mahmuda Akter", "Morjina Begum",
    "Shamsunnahar", "Shamsun Nahar", "Shamsunnahar Begum", "Shamsun Nahar Begum", "Shamsunnahar Akter",
    "Nurjahan Begum", "Nurunnahar Begum", "Nurun Nahar", "Nurun Nahar Begum", "Nurjahan Akter",
    "Abdul Goni", "Abdul Matin", "Abdur Rouf", "Abdus Sattar", "Abdul Quader",
    "Abul Kashem", "Abul Kalam", "Abul Hasnat", "Abul Hossain", "Abul Bashar",
    "Mohammad Ali", "Muhammad Yusuf", "Mohammad Ismail", "Muhammad Ibrahim", "Mohammad Hasan",
    "Shah Alam", "Shah Jahan", "Shahidul Alam", "Shahjahan Miah", "Shahnewaz",
    "Kazi Zulfikar", "Kazi Nazrul", "Kazi Saifuddin", "Kazi Shamsul", "Kazi Mukhlesur",
    "Mizanur Rahman", "Mokhlesur Rahman", "Mominur Rahman", "Mozammel Haque", "Mozahar Ali",
    "Saifur Rahman", "Shafiqur Rahman", "Shamsur Rahman", "Samsul Alam", "Samsul Haque",
    "Tofazzal Hossain", "Tofail Ahmed", "Tajul Islam", "Tajuddin Ahmed", "Tazul Islam",
    "Wahiduzzaman", "Wahidul Islam", "Wahid Miah", "Wazed Ali", "Wares Hossain",
    "Yunus Ali", "Yusuf Ali", "Younus Miah", "Yasin Arafat", "Yousuf Hossain",
    "Zahid Hossain", "Zakir Hossain", "Zahirul Islam", "Ziaul Haque", "Zillur Rahman",
    "Nazrul Islam", "Nazim Uddin", "Nazmul Hasan", "Nurul Haque", "Nurul Amin",
    "Sirajul Islam", "Siraj Uddin", "Siraj Miah", "Sirajul Haque", "Sirajul Hoque",
    "Afsana Begum", "Afroza Begum", "Asma Akter", "Asma Begum", "Asia Begum",
    "Bably Akter", "Baby Akter", "Banu Begum", "Basonti Rani", "Bina Rani",
    "Chandana Rani", "Chandni Rani", "Chhabi Rani", "Chompa Rani", "Chumki Akter",
    "Deepa Rani", "Deepali Rani", "Dipa Rani", "Dipali Rani", "Disha Akter",
    "Eity Akter", "Emona Begum", "Esha Akter", "Eshita Begum", "Eti Begum",
    "Farjana Akter", "Farzana Begum", "Ferdousi Akter", "Firoza Begum", "Firoza Akter",
    "Golapi Akter", "Gulbahar Begum", "Gulshan Begum", "Gulshan Akter", "Gulzar Begum",
    "Hafsa Begum", "Hafsa Akter", "Halima Begum", "Halima Akter", "Hasna Hena",
    "Iffat Jahan", "Irin Begum", "Ishrat Jahan", "Ishrat Ara", "Ivy Begum",
    "Jesmin Akter", "Jesmin Begum", "Jhorna Akter", "Jhorna Begum", "Jinnat Ara",
    "Kajal Akter", "Kajal Begum", "Kalpana Rani", "Kamala Rani", "Kamrun Nahar",
    "Laily Akter", "Laily Begum", "Lata Rani", "Lima Begum", "Lipica Akter",
    "Mita Akter", "Mita Begum", "Moni Akter", "Moni Begum", "Moni Rani",
    "Nahar Begum", "Nargis Begum", "Nasrin Akter", "Nasrin Begum", "Nazma Akter",
    "Parvin Begum", "Parvin Akter", "Poly Begum", "Poly Rani", "Popy Begum",
    "Rani Akter", "Rani Begum", "Rina Rani", "Rita Akter", "Rita Begum",
    "Sabina Akter", "Sabina Begum", "Sabina Yasmin", "Sabina Yesmin", "Sadia Akter",
    "Shahana Begum", "Shahida Begum", "Shahinur Akter", "Shahnaj Begum", "Shahnaj Parvin",
    "Shamima Nasrin", "Shamima Sultana", "Sharmin Akter", "Sharmin Begum", "Sharmin Sultana",
    "Tahmina Akter", "Tahmina Begum", "Tahmina Sultana", "Tamanna Akter", "Tamanna Begum",
    "Umme Kulsum", "Umme Salma", "Umme Habiba", "Umme Hani", "Umme Jannat",
    "Rafiqul Islam", "Rafiq Miah", "Rafiq Uddin", "Rafiq Ahmed", "Rafiqul Haque",
    "Shafiqul Islam", "Shafiq Miah", "Shafiq Uddin", "Shafiq Ahmed", "Shafiqul Haque",
    "Hafizur Rahman", "Hafiz Miah", "Hafiz Uddin", "Hafiz Ahmed", "Hafizul Haque",
    "Mozammel Haque", "Mozammel Hossain", "Mozammel Ali", "Mozammel Miah", "Mozammel Islam"
];
// How many names to show per page in the popup
$names_per_page = 20;
$total_names = count($people_list);
$total_pages = ceil($total_names / $names_per_page);

$hm_asset_v = '1';   // change this number after editing the CSS/JS if the browser keeps showing the old design
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/home-modern.css?v=<?php echo $hm_asset_v; ?>">
<link rel="stylesheet" href="assets/css/pages-modern.css?v=<?php echo $hm_asset_v; ?>">

<main class="hm">
    <!-- Notification for copy -->
    <div class="copy-notification" id="copyNotification">
        <i class="fas fa-check-circle"></i> Copied to clipboard!
    </div>

    <!-- ===================== HERO ===================== -->
    <section class="welfare-hero pgx-wf-hero" style="background-image: url('assets/img/bg/donation.jpg');" aria-label="Donate for humanities">
        <div class="hm-wrap">
            <div class="pgx-wf-inner">
                <span class="hm-kicker is-light"><i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i> TeleRx Welfare</span>
                <h1><span class="hm-warm">Donate</span> For Humanities</h1>
                <p class="pgx-wf-quote">
                    <i class="fas fa-quote-left" aria-hidden="true"></i>
                    Donation for rural people and help them to survive.
                    <i class="fas fa-quote-right" aria-hidden="true"></i>
                </p>

                <button type="button" class="hm-btn is-coral" id="donateBtn">
                    <i class="fas fa-heart" aria-hidden="true"></i> Donate Now
                    <i class="fas fa-chevron-down" id="arrowIcon" aria-hidden="true"></i>
                </button>

                <!-- Payment details (hidden until "Donate Now" is clicked) -->
                <div class="payment-details" id="paymentDetails">
                    <div class="payment-card">
                        <div class="payment-card-header">
                            <div class="payment-card-icon"><i class="fas fa-university"></i></div>
                            <h5 class="payment-card-title">Bank Details</h5>
                        </div>
                        <div class="payment-row">
                            <div class="payment-row-label">Bank Name:</div>
                            <div class="payment-row-value">
                                <span id="bankName"><?php echo $bank_name; ?></span>
                                <button type="button" class="copy-btn" aria-label="Copy bank name" onclick="copyToClipboard('bankName', 'Bank name')"><i class="far fa-copy"></i></button>
                            </div>
                        </div>
                        <div class="payment-row">
                            <div class="payment-row-label">Account Number:</div>
                            <div class="payment-row-value">
                                <span id="bankAccount"><?php echo $bank_account; ?></span>
                                <button type="button" class="copy-btn" aria-label="Copy account number" onclick="copyToClipboard('bankAccount', 'Account number')"><i class="far fa-copy"></i></button>
                            </div>
                        </div>
                        <div class="payment-row">
                            <div class="payment-row-label">Account Name:</div>
                            <div class="payment-row-value">
                                <span id="accountName"><?php echo $account_name; ?></span>
                                <button type="button" class="copy-btn" aria-label="Copy account name" onclick="copyToClipboard('accountName', 'Account name')"><i class="far fa-copy"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="payment-card">
                        <div class="payment-card-header">
                            <div class="payment-card-icon"><i class="fas fa-mobile-alt"></i></div>
                            <h5 class="payment-card-title">bKash Payment</h5>
                        </div>
                        <div class="payment-row">
                            <div class="payment-row-label">bKash (Personal):</div>
                            <div class="payment-row-value">
                                <span id="bkashNumber"><?php echo $bkash_number; ?></span>
                                <button type="button" class="copy-btn" aria-label="Copy bKash number" onclick="copyToClipboard('bkashNumber', 'bKash number')"><i class="far fa-copy"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== COUNTERS (click a card to see the list) ===================== -->
    <section class="hm-section" style="padding-top:0">
        <div class="hm-wrap">
            <div class="pgx-wf-counters">
                <div class="counter-card" data-list-type="patients" title="Click to view the list">
                    <div class="counter-icon"><i class="fas fa-users"></i></div>
                    <div class="counter-number"><?php echo $total_patient; ?><span>+</span></div>
                    <div class="counter-label">Total Patients</div>
                    <div class="counter-desc">Happy patients &amp; families</div>
                </div>
                <div class="counter-card" data-list-type="donors" title="Click to view the list">
                    <div class="counter-icon"><i class="fas fa-hand-holding-heart"></i></div>
                    <div class="counter-number"><?php echo $total_donors; ?><span>+</span></div>
                    <div class="counter-label">Respected Donors</div>
                    <div class="counter-desc">Doctors, NGO's, Personal</div>
                </div>
                <div class="counter-card" data-list-type="doctors" title="Click to view the list">
                    <div class="counter-icon"><i class="fas fa-user-md"></i></div>
                    <div class="counter-number"><?php echo $total_doctors; ?><span>+</span></div>
                    <div class="counter-label">Doctors</div>
                    <div class="counter-desc">Specialist &amp; general physicians</div>
                </div>
                <div class="counter-card" data-list-type="districts" title="Click to view the list">
                    <div class="counter-icon"><i class="fas fa-map-marked-alt"></i></div>
                    <div class="counter-number"><?php echo $total_districts; ?><span>+</span></div>
                    <div class="counter-label">District</div>
                    <div class="counter-desc">Across Bangladesh</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal for People List -->
    <div id="peopleModal" class="modal-popup">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">People List</h3>
                <button type="button" class="close-modal" id="closeModalBtn" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- List will be populated by JavaScript -->
                <div class="people-list" id="peopleListContainer"></div>
            </div>
            <div class="modal-footer" id="modalFooter">
                <button type="button" class="pagination-btn" id="prevPageBtn" disabled>Previous</button>
                <span id="pageInfo">Page 1 / 1</span>
                <button type="button" class="pagination-btn" id="nextPageBtn" disabled>Next</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- AOS Animation JS -->
    <script src="assets/js/aos.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize AOS
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 1000,
                    once: true,
                    offset: 100
                });
            }
            
            // Floating hearts animation
            function createHeart() {
                const heart = document.createElement('div');
                heart.classList.add('floating-heart');
                heart.innerHTML = '❤️';
                heart.style.left = Math.random() * 100 + '%';
                heart.style.top = '100%';
                heart.style.fontSize = (Math.random() * 2 + 0.5) + 'rem';
                heart.style.opacity = '0.3';
                heart.style.animation = 'float ' + (Math.random() * 5 + 5) + 's linear infinite';
                const heroBox = document.querySelector('.welfare-hero');
                if (!heroBox) { return; }
                heroBox.appendChild(heart);
                
                setTimeout(() => {
                    heart.remove();
                }, 10000);
            }
            
            // Create hearts every 2 seconds
            setInterval(createHeart, 2000);
            
            // Counter animation on scroll
            function animateCounter(element, start, end, duration) {
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    element.innerHTML = Math.floor(progress * (end - start) + start) + '<span>+</span>';
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                };
                window.requestAnimationFrame(step);
            }
            
            // Animate numbers when they come into view
            const observerOptions = {
                threshold: 0.5,
                rootMargin: '0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const counterNumber = entry.target.querySelector('.counter-number');
                        const originalText = counterNumber.innerText;
                        const endValue = parseInt(originalText.replace('+', ''));
                        counterNumber.innerHTML = '0<span>+</span>';
                        animateCounter(counterNumber, 0, endValue, 2000);
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            
            document.querySelectorAll('.counter-card').forEach(card => {
                observer.observe(card);
            });
            
            // Donate button click handler
            $('#donateBtn').on('click', function() {
                $('#paymentDetails').slideToggle(300);
                $('#arrowIcon').toggleClass('fa-chevron-down fa-chevron-up');
                
                // Smooth scroll to payment details if visible
                if ($('#paymentDetails').is(':visible')) {
                    $('html, body').animate({
                        scrollTop: $('#paymentDetails').offset().top - 100
                    }, 500);
                }
            });

            // ---------- Popup Logic for Counter Cards ----------
            // Dummy data for different card types (in real app, fetch via AJAX)
            const allPeople = <?php echo json_encode($people_list); ?>;
            const namesPerPage = <?php echo $names_per_page; ?>;
            let currentPage = 1;
            let currentListData = []; // Stores the full list for the active card

            // Get modal elements
            const modal = document.getElementById('peopleModal');
            const modalTitle = document.getElementById('modalTitle');
            const peopleListContainer = document.getElementById('peopleListContainer');
            const prevBtn = document.getElementById('prevPageBtn');
            const nextBtn = document.getElementById('nextPageBtn');
            const pageInfo = document.getElementById('pageInfo');
            const closeModalBtn = document.getElementById('closeModalBtn');

            // Function to render the list for the current page
            function renderList() {
                if (!currentListData.length) {
                    peopleListContainer.innerHTML = '<p style="grid-column: span 2; text-align: center;">No data available</p>';
                    return;
                }

                const start = (currentPage - 1) * namesPerPage;
                const end = start + namesPerPage;
                const pageData = currentListData.slice(start, end);

                let html = '';
                pageData.forEach((name, index) => {
                    const serial = start + index + 1; // Serial number calculation
                    html += `<div class="list-item">
                        <span class="list-serial">${serial}.</span>
                        <span>${name}</span>
                    </div>`;
                });
                peopleListContainer.innerHTML = html;

                // Update pagination buttons and info
                const totalPages = Math.ceil(currentListData.length / namesPerPage);
                pageInfo.innerText = `Page ${currentPage} / ${totalPages}`;
                prevBtn.disabled = currentPage <= 1;
                nextBtn.disabled = currentPage >= totalPages;
            }

            // Function to open modal with specific list
            function openModal(listType) {
                let title = '';
                // For this demo, we use the same dummy data for all lists.
                // In a real scenario, you might filter data based on listType.
                // For example, you could have different arrays for patients, donors, etc.
                currentListData = allPeople; // Using same list for all cards for demo
                
                switch(listType) {
                    case 'patients':
                        title = 'Patient List';
                        break;
                    case 'donors':
                        title = 'Donor List';
                        currentListData = ["Dr. Md. Abdur Rahman", "Mrs. Fatema Begum", "Engr. Shahidul Islam", "Adv. Nasrin Sultana", "Alhaj Rofiqul Islam", "Brac", "Asha", "Jagorani Chakra", "Manab Mukti Sangstha", "Rishilpi", "Mr. Jahangir Alam", "Ms. Rabeya Khatun"];
                        break;
                    case 'doctors':
                        title = 'Doctors List';
                        currentListData = ["Dr. Md. Abdur Rahman", "Dr. Mrs. Fatema Begum", "Dr. Md. Shahidul Islam", "Dr. Nasrin Sultana", "Dr. Md. Rofiqul Islam", "Dr. Mst. Hasina Akter", "Dr. Md. Jahangir Alam", "Dr. Rabeya Khatun", "Dr. Md. Abul Kalam Azad", "Dr. Shirin Akter", "Dr. Md. Mizanur Rahman", "Dr. Parul Begum", "Dr. Md. Golam Mostofa", "Dr. Selina Begum", "Dr. Md. Shamsul Haque", "Dr. Rokeya Begum", "Dr. Md. Delwar Hossain", "Dr. Jahanara Begum", "Dr. Md. Nurul Islam", "Dr. Ayesha Begum", "Dr. Md. Mahbubur Rahman", "Dr. Momena Khatun", "Dr. Md. Shafiqul Islam", "Dr. Saleha Begum", "Dr. Md. Abul Hossain", "Dr. Amena Begum", "Dr. Md. Abdul Mannan", "Dr. Kulsum Begum", "Dr. Md. Moklesur Rahman", "Dr. Nurjahan Begum", "Dr. Md. Abdur Razzak", "Dr. Shahana Akter", "Dr. Md. Anwar Hossain", "Dr. Laily Begum", "Dr. Md. Saiful Islam"];
                        break;
                    case 'districts':
                        title = 'Districts Covered';
                        currentListData = ["Dhaka", "Chittagong", "Rajshahi", "Khulna", "Barisal", "Sylhet", "Rangpur", "Mymensingh", "Comilla", "Jessore", "Bogra", "Dinajpur", "Pabna", "Tangail", "Gazipur", "Narayanganj", "Cox's Bazar", "Kushtia", "Faridpur", "Noakhali", "Brahmanbaria"];
                        title = 'District List';
                        break;
                    default:
                        title = 'People List';
                }

                modalTitle.innerText = title;
                currentPage = 1;
                renderList();
                modal.style.display = 'flex'; // Show modal
            }

            // Close modal function
            function closeModal() {
                modal.style.display = 'none';
            }

            // Event listeners for each counter card
            document.querySelectorAll('.counter-card').forEach(card => {
                card.addEventListener('click', function() {
                    const listType = this.getAttribute('data-list-type');
                    openModal(listType);
                });
            });

            // Close modal when X is clicked
            closeModalBtn.addEventListener('click', closeModal);

            // Close modal if user clicks outside the modal content
            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    closeModal();
                }
            });

            // Pagination button events
            prevBtn.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    renderList();
                }
            });

            nextBtn.addEventListener('click', function() {
                const totalPages = Math.ceil(currentListData.length / namesPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    renderList();
                }
            });
            // ------------------------------------------------
        });
        
        // Copy to clipboard function (unchanged)
        function copyToClipboard(elementId, type) {
            var text = document.getElementById(elementId).innerText;
            
            // Create temporary input element
            var tempInput = document.createElement('input');
            tempInput.value = text;
            document.body.appendChild(tempInput);
            
            // Select and copy
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // For mobile
            document.execCommand('copy');
            
            // Remove temporary input
            document.body.removeChild(tempInput);
            
            // Show notification
            var notification = document.getElementById('copyNotification');
            notification.classList.add('show');
            
            // Update notification text
            notification.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + type + ' copied to clipboard!';
            
            // Hide notification after 2 seconds
            setTimeout(function() {
                notification.classList.remove('show');
            }, 2000);
        }
    </script>
</main>
<?php include 'footer.php'; ?>
