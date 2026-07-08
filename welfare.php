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
?>

<style>
    .welfare-page * {
    box-sizing: border-box;
    }

    .welfare-page {
        font-family: inherit;
    }

    body {
        overflow-x: hidden;
    }
    
    /* Hero Section with Background */
    .welfare-hero {
        position: relative;
        min-height: 50vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        overflow: hidden;
        padding: 100px 0;
    }
    
    /* Background Image */
    .welfare-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('assets/img/bg/donation.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        z-index: -2;
    }
    
    /* Dark Overlay */
    .welfare-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.6) 100%);
        z-index: -1;
    }
    
    /* Content Wrapper */
    .welfare-content {
        position: relative;
        z-index: 1;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    /* Title Styles */
    .welfare-title {
        font-size: 56px;
        font-weight: 800;
        margin: 30px;
        color: white;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        animation: fadeInDown 1s ease;
    }
    
    .welfare-title span {
        color: #ff6b6b;
        display: inline-block;
        position: relative;
    }
    
    .welfare-title span::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #ff6b6b, #ff8e8e);
        border-radius: 2px;
    }
    
    .welfare-tagline {
        font-size: 32px;
        margin-bottom: 30px;
        opacity: 0.9;
        color: #fff;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        animation: fadeInUp 1s ease 0.2s both;
    }
    
    /* Counter Section */
    .counter-section {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
        background: none;
        animation: fadeInUp 1s ease 0.4s both;
    }
    
    /* Counter Card */
    .counter-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 40px 20px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .counter-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }
    
    .counter-card:hover::before {
        left: 100%;
    }
    
    .counter-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        background: rgba(255, 255, 255, 0.15);
    }
    
    /* Counter Icon */
    .counter-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #fff;
        transition: all 0.3s ease;
    }
    
    .counter-card:hover .counter-icon {
        background: #ff6b6b;
        color: white;
        transform: rotateY(180deg);
    }
    
    /* Counter Number */
    .counter-number {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 10px;
        color: #fff;
        line-height: 1.2;
    }
    
    .counter-number span {
        font-size: 2rem;
        opacity: 0.8;
    }
    
    /* Counter Label */
    .counter-label {
        font-size: 1.2rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.9);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    /* Counter Description */
    .counter-desc {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.7);
        margin-top: 10px;
        font-style: italic;
    }
    
    /* Donate Button */
    .donate-btn {
        margin-top: 60px;
        animation: fadeInUp 1s ease 0.6s both;
    }
    
    .btn-donate {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 18px 50px;
        font-size: 1.3rem;
        font-weight: 600;
        color: white;
        background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
        border: none;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 2px;
        box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .btn-donate::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s ease, height 0.6s ease;
    }
    
    .btn-donate:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-donate:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(255, 107, 107, 0.4);
        color: white;
    }
    
    .btn-donate i {
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }
    
    .btn-donate:hover i {
        transform: translateX(5px);
    }
    
    /* Payment Details Section - Hidden by default */
    .payment-details {
        max-width: 600px;
        margin: 30px auto 0;
        display: none;
        animation: slideDown 0.4s ease forwards;
    }
    
    .payment-details.show {
        display: block;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Payment Item */
    /* Payment Card */
    .payment-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        border: 1px solid #e9ecef;
    }

    .payment-card:last-child {
        margin-bottom: 0;
    }

    /* Payment Card Header */
    .payment-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f3f5;
    }

    .payment-card-icon {
        width: 45px;
        height: 45px;
        background: #ff6b6b;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: white;
    }

    .payment-card-title {
        font-size: 18px;
        font-weight: 600;
        color: #212529;
        font-family: 'Poppins', sans-serif;
        margin: 0;
    }

    /* Payment Row */
    .payment-row {
        display: flex;
        align-items: flex-start;
        padding: 12px 0;
        border-bottom: 1px dashed #e9ecef;
    }

    .payment-row:last-child {
        border-bottom: none;
    }

    .payment-row-label {
        font-size: 16px;
        font-weight: 500;
        color: #6c757d;
        font-family: 'Inter', sans-serif;
        min-width: 130px;  /* একটু বড় করুন */
        text-align: left;   /* বামে align নিশ্চিত করুন */
    }

    .payment-row-value {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        justify-content: flex-end;
    }

    .payment-row-value span {
        font-size: 16px;
        font-weight: 500;
        color: #212529;
        font-family: 'Poppins', sans-serif;
        word-break: break-word;
        text-align: right;
    }
    
    /* Copy Button */
    .copy-btn {
        background: transparent;
        border: 1px solid #dee2e6;
        color: #6c757d;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
        flex-shrink: 0;
    }

    .copy-btn:hover {
        background: #ff6b6b;
        border-color: #ff6b6b;
        color: white;
        transform: scale(1.05);
    }

    /* Copy notification */
    .copy-notification {
        position: fixed;
        top: 100px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 12px 25px;
        border-radius: 50px;
        font-size: 14px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        transform: translateX(120%);
        transition: transform 0.3s ease;
        z-index: 9999;
        font-family: 'Inter', sans-serif;
    }

    .copy-notification.show {
        transform: translateX(0);
    }
            
    /* Floating Hearts Animation */
    .floating-heart {
        position: absolute;
        color: rgba(255, 107, 107, 0.3);
        font-size: 1rem;
        pointer-events: none;
        z-index: -1;
    }
    
    @keyframes float {
        0% {
            transform: translateY(0) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(-100vh) rotate(360deg);
            opacity: 0;
        }
    }
    
    /* Copy notification */
    .copy-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #ff6b6b;
        color: white;
        padding: 12px 25px;
        border-radius: 50px;
        font-size: 0.9rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        transform: translateX(120%);
        transition: transform 0.3s ease;
        z-index: 9999;
    }
    
    .copy-notification.show {
        transform: translateX(0);
    }
    
    /* Responsive Design */
    @media (max-width: 576px) {
        .payment-card {
            padding: 20px;
        }
        
        .payment-row {
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        
        .payment-row-label {
            min-width: auto;
        }
        
        .payment-row-value {
            margin-left: 0;    
            width: 100%;
            justify-content: space-between;
        }
        
        .payment-row-value span {
            text-align: left;
        }
    }
    
    @media (max-width: 768px) {
        .welfare-title {
            font-size: 2.8rem;
        }
        
        .welfare-tagline {
            font-size: 1.2rem;
        }
        
        .counter-number {
            font-size: 2.5rem;
        }
        
        .btn-donate {
            padding: 15px 40px;
            font-size: 1.1rem;
        }
        
        .payment-item {
            flex-direction: column;
            text-align: center;
        }
        
        .payment-info {
            text-align: center;
        }
        
        .payment-value {
            flex-direction: column;
            gap: 10px;
        }
    }
    
    @media (max-width: 480px) {
        .welfare-title {
            font-size: 2rem;
        }
        
        .counter-section {
            grid-template-columns: 1fr;
        }
        
        .counter-card {
            padding: 30px 15px;
        }
    }
    
    /* Animations */
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Pulse Animation for Numbers */
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }
    
    .counter-number {
        animation: pulse 2s infinite;
    }

    /* ---------- New Styles for the Popup Modal ---------- */
    /* Modal Background */
    .modal-popup {
        display: none; /* Hidden by default */
        position: fixed;
        z-index: 10000; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5); /* Black w/ opacity */
        align-items: center;
        justify-content: center;
    }

    /* Modal Content Box */
    .modal-content {
        background-color: #fff;
        width: 500px;
        height: 610px;
        border-radius: 10px;
        box-shadow: 0 5px 30px rgba(0,0,0,0.3);
        display: flex;
        flex-direction: column;
        position: relative;
        animation: fadeInUp 0.3s ease;
    }

    /* Modal Header */
    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }
    .modal-header h3 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 600;
        color: #212529;
    }
    .close-modal {
        background: none;
        border: none;
        font-size: 28px;
        cursor: pointer;
        color: #6c757d;
        line-height: 1;
        padding: 0 5px;
    }
    .close-modal:hover {
        color: #ff6b6b;
    }

    /* Modal Body (List Area) */
    .modal-body {
        flex: 1;
        overflow-y: auto; /* Enables scroll if content overflows */
        padding: 20px;
    }

    /* Two-column list */
    .people-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 20px;
    }
    .people-list .list-item {
        padding: 5px 0;
        color: #212529;
        font-size: 0.95rem;
        text-align: left;
        border-bottom: 1px dotted #f1f3f5;
    }

    /* Modal Footer (Pagination) */
    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-shrink: 0;
    }
    .pagination-btn {
        padding: 8px 15px;
        border: 1px solid #dee2e6;
        background: white;
        border-radius: 5px;
        cursor: pointer;
        color: #495057;
        font-weight: 500;
        transition: all 0.2s;
    }
    .pagination-btn:hover:not(:disabled) {
        background: #ff6b6b;
        border-color: #ff6b6b;
        color: white;
    }
    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* List item container */
    .people-list .list-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 5px 0;
        color: #212529;
        font-size: 0.95rem;
        text-align: left;
        border-bottom: 1px dotted #f1f3f5;
    }

    /* Serial number style */
    .list-serial {
        display: inline-block;
        min-width: 10px;
        color: #ff6b6b;
        font-weight: 600;
        font-size: 0.9rem;
    }
    /* ------------------------------------------------ */
</style>

<main class="welfare-page">

    <!-- Copy Notification -->
    <div class="copy-notification" id="copyNotification">
        <i class="fas fa-check-circle me-2"></i>
        Copied to clipboard!
    </div>

    <!-- Hero Section with Background -->
    <section class="welfare-hero">
        <!-- Background Image -->
        <div class="welfare-bg" style="background-image: url('assets/img/bg/donation.jpg');"></div>
        <div class="welfare-overlay"></div>
        
        <!-- Floating Hearts (Decoration) -->
        <div class="floating-heart" style="top: 10%; left: 5%;">❤️</div>
        <div class="floating-heart" style="top: 30%; right: 8%;">❤️</div>
        <div class="floating-heart" style="bottom: 20%; left: 10%;">❤️</div>
        <div class="floating-heart" style="bottom: 40%; right: 15%;">❤️</div>
        
        <div class="welfare-content">
            <!-- Title -->
            <h1 class="welfare-title">
                <span>Donate</span> For Humanities
            </h1>
            
            <!-- Tagline -->
            <p class="welfare-tagline">
                <i class="fas fa-quote-left me-2" style="opacity: 0.5;"></i>
                Donation for rural people and help them to survive.
                <i class="fas fa-quote-right ms-2" style="opacity: 0.5;"></i>
            </p>
            <!-- Donate Button -->
            <div class="donate-btn">
                <button class="btn-donate" id="donateBtn">
                    <i class="fas fa-heart"></i>
                    Donate Now
                    <i class="fas fa-chevron-down" id="arrowIcon"></i>
                </button>
            </div>
            
            <!-- Payment Details (Hidden by default) -->
            <div class="payment-details" id="paymentDetails">
                <!-- Bank Details Card -->
                <div class="payment-card">
                    <div class="payment-card-header">
                        <div class="payment-card-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <h5 class="payment-card-title">Bank Details</h5>
                    </div>
                    
                    <!-- Bank Name -->
                    <div class="payment-row">
                        <div class="payment-row-label">Bank Name:</div>
                        <div class="payment-row-value">
                            <span id="bankName"><?php echo $bank_name; ?></span>
                            <button class="copy-btn" onclick="copyToClipboard('bankName', 'Bank name')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Account Number -->
                    <div class="payment-row">
                        <div class="payment-row-label">Account Number:</div>
                        <div class="payment-row-value">
                            <span id="bankAccount"><?php echo $bank_account; ?></span>
                            <button class="copy-btn" onclick="copyToClipboard('bankAccount', 'Account number')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Account Name -->
                    <div class="payment-row">
                        <div class="payment-row-label">Account Name:</div>
                        <div class="payment-row-value">
                            <span id="accountName"><?php echo $account_name; ?></span>
                            <button class="copy-btn" onclick="copyToClipboard('accountName', 'Account name')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- bKash Details Card -->
                <div class="payment-card">
                    <div class="payment-card-header">
                        <div class="payment-card-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h5 class="payment-card-title">bKash Payment</h5>
                    </div>
                    
                    <!-- bKash Number -->
                    <div class="payment-row">
                        <div class="payment-row-label">bKash (Personal):</div>
                        <div class="payment-row-value">
                            <span id="bkashNumber"><?php echo $bkash_number; ?></span>
                            <button class="copy-btn" onclick="copyToClipboard('bkashNumber', 'bKash number')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Counter Section -->
            <div class="counter-section">
                <!-- Total Patient Card -->
                <div class="counter-card" data-aos="fade-up" data-aos-delay="100" data-list-type="patients">
                    <div class="counter-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="counter-number">
                        <?php echo $total_patient; ?><span>+</span>
                    </div>
                    <div class="counter-label">Total Patients</div>
                    <div class="counter-desc">Happy patients & families</div>
                </div>
                
                <!-- Donors Card -->
                <div class="counter-card" data-aos="fade-up" data-aos-delay="200" data-list-type="donors">
                    <div class="counter-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="counter-number">
                        <?php echo $total_donors; ?><span>+</span>
                    </div>
                    <div class="counter-label">Respected Donors</div>
                    <div class="counter-desc">Doctors, NGO's, Personal</div>
                </div>
                
                <!-- Doctors Card -->
                <div class="counter-card" data-aos="fade-up" data-aos-delay="300" data-list-type="doctors">
                    <div class="counter-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="counter-number">
                        <?php echo $total_doctors; ?><span>+</span>
                    </div>
                    <div class="counter-label">Doctors</div>
                    <div class="counter-desc">Specialist & general physicians</div>
                </div>
                
                <!-- District Card -->
                <div class="counter-card" data-aos="fade-up" data-aos-delay="400" data-list-type="districts">
                    <div class="counter-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="counter-number">
                        <?php echo $total_districts; ?><span>+</span>
                    </div>
                    <div class="counter-label">District</div>
                    <div class="counter-desc">Across Bangladesh</div>
                </div>
            </div>
        </div>
    </section>

    <!-- POPUP MODAL for People List -->
    <div id="peopleModal" class="modal-popup">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">People List</h3>
                <button class="close-modal" id="closeModalBtn">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- List will be populated by JavaScript -->
                <div class="people-list" id="peopleListContainer"></div>
            </div>
            <div class="modal-footer" id="modalFooter">
                <!-- Pagination buttons will be shown here if needed -->
                <button class="pagination-btn" id="prevPageBtn" disabled>Previous</button>
                <span id="pageInfo" style="margin: 0 10px; color: #6c757d;">Page 1 / 1</span>
                <button class="pagination-btn" id="nextPageBtn" disabled>Next</button>
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
                document.querySelector('.welfare-hero').appendChild(heart);
                
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