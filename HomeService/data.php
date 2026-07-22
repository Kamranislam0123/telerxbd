<?php
$home_services = [
    'nursing-care-at-home' => [
        'title' => 'Nursing Care at Home Service',
        'category' => 'Clinical Care',
        'image' => 'assets/services/nursing-care-at-home.svg',
        'short' => 'Professional nursing support at home for patients who need regular clinical care, monitoring and recovery assistance.',
        'overview' => 'TeleRx Nursing Care at Home is designed for patients who need safe, supervised and compassionate clinical support without repeated hospital visits. This service may support elderly patients, post-operative patients, bedridden patients, chronic disease patients and patients who need regular nursing attention at home.',
        'highlights' => ['Trained nursing support', 'Patient monitoring', 'Medication and care routine support', 'Family-friendly coordination'],
        'included' => ['Basic vital signs monitoring', 'Medication reminder and routine support', 'Post-operative observation support', 'Patient hygiene and mobility assistance', 'Care update to family members when required'],
        'ideal_for' => ['Elderly patients', 'Post-surgery recovery', 'Bedridden patients', 'Chronic illness support', 'Patients needing routine care at home'],
        'process' => ['Contact TeleRx with patient condition', 'Care need is reviewed by the team', 'Suitable nursing support is arranged', 'Family receives care updates and next-step guidance'],
        'note' => 'This service supports home-based care. Hospital emergency treatment may still be required for critical conditions.'
    ],
    'caregiver-service-support' => [
        'title' => 'Caregiver Service Support',
        'category' => 'Home Support',
        'image' => 'assets/services/caregiver-service-support.svg',
        'short' => 'Reliable caregiver support for elderly, dependent or recovering patients who need daily assistance at home.',
        'overview' => 'TeleRx Caregiver Service Support helps families arrange day-to-day assistance for patients who need help with movement, feeding, personal care, medicine routine and general supervision. It is suitable for families who need dependable care support at home.',
        'highlights' => ['Daily care assistance', 'Elderly support', 'Mobility assistance', 'Family coordination'],
        'included' => ['Assistance with daily living activities', 'Patient movement and transfer support', 'Feeding and hydration support', 'Basic hygiene assistance', 'Observation and family communication'],
        'ideal_for' => ['Elderly family members', 'Patients living alone', 'Patients recovering from illness', 'Families needing daily support', 'Long-term care needs'],
        'process' => ['Share patient age and condition', 'TeleRx identifies required support level', 'Caregiver schedule is planned', 'Service begins after family confirmation'],
        'note' => 'Caregivers provide non-clinical assistance. Clinical procedures should be handled by qualified medical personnel.'
    ],
    'doctor-home-visit' => [
        'title' => 'Doctor Home Visit',
        'category' => 'Doctor Support',
        'image' => 'assets/services/doctor-home-visit.svg',
        'short' => 'Doctor visit at home for patients who cannot easily travel to a clinic or hospital for consultation.',
        'overview' => 'TeleRx Doctor Home Visit helps patients receive medical consultation at home when visiting a chamber is difficult. The service is useful for elderly patients, mobility-restricted patients, acute but non-emergency illness and family-based medical assessment.',
        'highlights' => ['Doctor consultation at home', 'Suitable for elderly patients', 'Prescription support', 'Follow-up guidance'],
        'included' => ['Home-based doctor consultation', 'Basic physical assessment', 'Digital or written prescription support', 'Investigation advice if needed', 'Follow-up recommendation'],
        'ideal_for' => ['Elderly patients', 'Patients with mobility issues', 'Homebound patients', 'Non-emergency illness', 'Family health assessment'],
        'process' => ['Request a doctor home visit', 'TeleRx checks location and availability', 'Visit time is confirmed', 'Doctor provides consultation and next-step advice'],
        'note' => 'Doctor home visit is not suitable for life-threatening emergencies. For emergencies, call local emergency services or visit the nearest hospital.'
    ],
    'oxygen-cylinder' => [
        'title' => 'Oxygen Cylinder',
        'category' => 'Medical Support',
        'image' => 'assets/services/oxygen-cylinder.svg',
        'short' => 'Oxygen cylinder support for home care needs, subject to availability, prescription and safety requirements.',
        'overview' => 'TeleRx Oxygen Cylinder support helps families arrange oxygen support at home when medically advised. The service focuses on safe coordination, cylinder availability, delivery assistance and basic usage guidance from the service team.',
        'highlights' => ['Home oxygen support', 'Cylinder delivery coordination', 'Safety guidance', 'Refill assistance'],
        'included' => ['Oxygen cylinder arrangement', 'Delivery coordination', 'Basic setup guidance', 'Refill support where available', 'Safety instruction for home use'],
        'ideal_for' => ['Doctor-advised oxygen support', 'Elderly respiratory care', 'Post-hospital support', 'Home care patients', 'Temporary oxygen needs'],
        'process' => ['Share prescription or doctor advice', 'TeleRx checks availability', 'Delivery and setup support is arranged', 'Family receives safety guidance'],
        'note' => 'Oxygen use should follow doctor advice. Severe breathing difficulty requires immediate hospital care.'
    ],
    'physiotherapy-home-service' => [
        'title' => 'Physiotherapy Home Service',
        'category' => 'Rehabilitation',
        'image' => 'assets/services/physiotherapy-home-service.svg',
        'short' => 'Physiotherapy support at home for pain management, mobility improvement and rehabilitation needs.',
        'overview' => 'TeleRx Physiotherapy Home Service supports patients who need guided rehabilitation at home. It may help with mobility limitation, muscle weakness, post-stroke support, joint pain, back pain and post-operative rehabilitation based on assessment.',
        'highlights' => ['Home-based physiotherapy', 'Mobility support', 'Pain management guidance', 'Rehabilitation plan'],
        'included' => ['Initial physiotherapy assessment', 'Exercise and mobility session', 'Pain and posture guidance', 'Recovery progress monitoring', 'Family instruction for safe movement'],
        'ideal_for' => ['Back pain and joint pain', 'Post-stroke rehabilitation', 'Post-surgery rehabilitation', 'Elderly mobility support', 'Sports or muscle injury recovery'],
        'process' => ['Share condition and doctor advice if available', 'Physiotherapy need is reviewed', 'Session schedule is confirmed', 'Therapy is provided at home'],
        'note' => 'Therapy plan depends on patient condition and professional assessment.'
    ],
    'medical-equipment' => [
        'title' => 'Medical Equipment',
        'category' => 'Equipment Support',
        'image' => 'assets/services/medical-equipment.svg',
        'short' => 'Medical equipment purchase or rental support for home care, monitoring and patient comfort.',
        'overview' => 'TeleRx Medical Equipment service helps families access essential healthcare equipment for home use. This may include monitoring devices, mobility support items and home-care equipment depending on availability.',
        'highlights' => ['Equipment purchase support', 'Rental coordination', 'Home care devices', 'Delivery assistance'],
        'included' => ['Equipment requirement guidance', 'Purchase or rental coordination', 'Delivery support', 'Basic usage instruction', 'After-sales coordination where applicable'],
        'ideal_for' => ['Home care patients', 'Elderly care', 'Long-term patient support', 'Families needing monitoring devices'],
        'process' => ['Tell TeleRx what equipment is needed', 'Availability and pricing are checked', 'Delivery or rental arrangement is confirmed', 'Equipment is delivered with basic guidance'],
        'note' => 'Equipment availability, warranty and rental terms may vary by product and location.'
    ],
    'ambulance-service' => [
        'title' => 'Ambulance Service',
        'category' => 'Transport Support',
        'image' => 'assets/services/ambulance-service.svg',
        'short' => 'Ambulance coordination support for patient transfer to hospital, clinic or diagnostic center.',
        'overview' => 'TeleRx Ambulance Service supports patient movement through ambulance coordination. It is designed for families who need timely, organized and location-based patient transfer support.',
        'highlights' => ['Patient transfer support', 'Hospital transfer coordination', 'Location-based arrangement', 'Urgent transport support'],
        'included' => ['Ambulance availability checking', 'Pickup and destination coordination', 'Basic patient transfer assistance', 'Hospital or diagnostic trip support', 'Family communication during arrangement'],
        'ideal_for' => ['Hospital transfer', 'Diagnostic center visit', 'Discharge transport', 'Elderly patient movement', 'Non-emergency patient transfer'],
        'process' => ['Share pickup and destination location', 'TeleRx checks ambulance availability', 'Estimated time and cost are confirmed', 'Ambulance is dispatched after confirmation'],
        'note' => 'For life-threatening emergencies, call local emergency services immediately while arranging transfer.'
    ],
    'home-sample-collection' => [
        'title' => 'Home Sample Collection',
        'category' => 'Diagnostics',
        'image' => 'assets/services/home-sample-collection.svg',
        'short' => 'Sample collection from home for lab tests, coordinated with partner diagnostic support where available.',
        'overview' => 'TeleRx Home Sample Collection makes diagnostic testing easier for busy families, elderly patients and patients who cannot visit a diagnostic center. Sample collection can be arranged from home based on test type, location and availability.',
        'highlights' => ['Lab sample from home', 'Diagnostic coordination', 'Report assistance', 'Convenient scheduling'],
        'included' => ['Test requirement confirmation', 'Sample collection scheduling', 'Partner lab coordination', 'Report delivery support', 'Follow-up consultation guidance if needed'],
        'ideal_for' => ['Elderly patients', 'Routine blood tests', 'Busy professionals', 'Family health check-up', 'Patients with mobility limitations'],
        'process' => ['Share test name or prescription', 'TeleRx confirms availability and price', 'Collection schedule is fixed', 'Report is shared after processing'],
        'note' => 'Fasting, sample timing and test preparation depend on the requested investigation.'
    ],
    'medicine-home-delivery' => [
        'title' => 'Medicine Home Delivery',
        'category' => 'Medicine Support',
        'image' => 'assets/services/medicine-home-delivery.svg',
        'short' => 'Medicine delivery coordination at home based on prescription, availability and delivery area.',
        'overview' => 'TeleRx Medicine Home Delivery helps patients and families receive prescribed medicine at home. This service is useful for elderly patients, chronic disease patients, busy families and post-consultation medicine needs.',
        'highlights' => ['Prescription-based delivery', 'Home delivery support', 'Chronic medicine support', 'Refill coordination'],
        'included' => ['Prescription review for medicine list', 'Availability checking', 'Delivery coordination', 'Refill support where possible', 'Payment and delivery confirmation'],
        'ideal_for' => ['Elderly patients', 'Chronic disease medicine', 'Post-consultation prescriptions', 'Busy families', 'Homebound patients'],
        'process' => ['Send prescription or medicine list', 'Availability and price are confirmed', 'Delivery location is shared', 'Medicine is delivered after confirmation'],
        'note' => 'Prescription medicines should be used only under doctor advice.'
    ],
    'injection-dressing-iv-support' => [
        'title' => 'Injection, Dressing & IV Support',
        'category' => 'Clinical Procedure',
        'image' => 'assets/services/injection-dressing-iv-support.svg',
        'short' => 'Home support for selected clinical procedures such as injection, wound dressing and IV-related assistance when medically advised.',
        'overview' => 'TeleRx Injection, Dressing & IV Support helps patients receive selected basic clinical procedures at home. The service is coordinated based on prescription, patient condition, procedure type and qualified personnel availability.',
        'highlights' => ['Injection support', 'Wound dressing', 'IV-related assistance', 'Prescription-based care'],
        'included' => ['Prescription-based procedure support', 'Wound dressing assistance', 'Injection administration support', 'Basic infection-control practice', 'Family guidance after procedure'],
        'ideal_for' => ['Patients advised by doctor', 'Wound dressing needs', 'Post-operative recovery', 'Elderly patients', 'Patients avoiding repeated clinic visits'],
        'process' => ['Share prescription and procedure details', 'TeleRx checks service suitability', 'Schedule and personnel are confirmed', 'Procedure is provided at home'],
        'note' => 'Clinical procedures must follow doctor advice and patient safety requirements.'
    ],
    'health-monitoring-at-home' => [
        'title' => 'Health Monitoring at Home',
        'category' => 'Preventive Care',
        'image' => 'assets/services/health-monitoring-at-home.svg',
        'short' => 'Regular home-based monitoring support for blood pressure, blood sugar, oxygen saturation and general wellness tracking.',
        'overview' => 'TeleRx Health Monitoring at Home helps families track important health indicators for elderly patients, chronic disease patients and preventive care needs. It can support timely consultation and better follow-up planning.',
        'highlights' => ['BP monitoring', 'Blood sugar tracking', 'SpO2 monitoring', 'Follow-up guidance'],
        'included' => ['Basic vitals monitoring support', 'BP, pulse and oxygen saturation tracking', 'Blood sugar monitoring support where applicable', 'Record keeping assistance', 'Doctor consultation guidance if abnormal values are noticed'],
        'ideal_for' => ['Diabetes patients', 'Hypertension patients', 'Elderly care', 'Post-hospital follow-up', 'Preventive health tracking'],
        'process' => ['Share monitoring requirement', 'TeleRx suggests suitable schedule', 'Home monitoring is arranged', 'Follow-up advice is provided when needed'],
        'note' => 'Abnormal or severe symptoms should be reviewed by a doctor immediately.'
    ],
];

function hs_get_service($slug) {
    global $home_services;
    return $home_services[$slug] ?? null;
}
