<?php
/**
 * TAB 5 - Personal & Other History
 *
 * Which parts are shown depends on Age and Sex saved in Tab 1:
 *   Socioeconomic classification ............ everybody
 *   Personal History ........................ 18 years or older
 *   Menstrual and reproductive history ...... Female, 12 years or older
 *   Male sexual and genitourinary history ... Male, 12 years or older
 *   Pediatric History ....................... under 12 years
 *   Geriatric History ....................... 60 years or older
 *   General immunization history ............ everybody
 *
 * The questions are written once below (a list of fields, see php/mh-schema.php);
 * the website form, the server check and the PDF are all built from this list.
 */

$YN  = array('Yes', 'No');
$APN = array('Absent', 'Present', 'Not assessed');

$mh5_blocks = array(

    /* ---------------- Socioeconomic classification (everybody) ---------------- */
    array(
        'key' => 'socio', 'cond' => 'all', 'title' => 'Socioeconomic Classification',
        'fields' => array(
            array('t' => 'select', 'k' => 'socio_class', 'l' => 'Socioeconomic classification', 'w' => 'wide',
                  'o' => array('Lower socioeconomic status', 'Lower-middle socioeconomic status', 'Middle socioeconomic status',
                               'Upper-middle socioeconomic status', 'Higher socioeconomic status', 'Undetermined')),
        ),
    ),

    /* ---------------- Personal history (18+) ---------------- */
    array(
        'key' => 'personal', 'cond' => 'adult', 'title' => 'Personal History',
        'fields' => array(
            array('t' => 'ftable', 'k' => 'personal', 'l' => '', 'head' => array('Field', 'Details'), 'rows' => array(
                'diet'        => array('Diet and appetite', 'Structured options + remarks'),
                'bowel'       => array('Bowel habits', 'Normal / abnormal / unknown + details'),
                'bladder'     => array('Bladder habits', 'Normal / abnormal / unknown + details'),
                'sleep'       => array('Sleep', 'Hours, quality, disturbances'),
                'activity'    => array('Physical activity', 'Type, frequency, duration'),
                'tobacco'     => array('Tobacco / nicotine', 'Type, quantity, duration'),
                'alcohol'     => array('Alcohol / substances', 'Type, frequency, quantity, where relevant'),
                'hygiene'     => array('Personal hygiene', 'Relevant structured checklist'),
                'sexual'      => array('Sexual / reproductive history', 'Conditional fields, as clinically appropriate'),
                'occupation'  => array('Occupational exposures', 'Exposure type and duration'),
                'functional'  => array('Functional status', 'Independent / needs assistance'),
                'remarks'     => array("Clinician's remarks", 'Others'),
            )),
        ),
    ),

    /* ---------------- Female, 12+ ---------------- */
    array(
        'key' => 'female', 'cond' => 'female12', 'title' => 'Menstrual and Reproductive History',
        'fields' => array(
            array('t' => 'date', 'k' => 'f_lmp', 'l' => 'Last menstrual period (LMP)'),
            array('t' => 'text', 'k' => 'f_cycle', 'l' => 'Menstrual cycle', 'ph' => 'e.g., every 28 days; duration 5 days', 'w' => 'wide'),
            array('t' => 'select', 'k' => 'f_regularity', 'l' => 'Cycle regularity', 'o' => array('Regular', 'Irregular', 'Not applicable', 'Unknown')),
            array('t' => 'select', 'k' => 'f_pregnancy', 'l' => 'Possibility of current pregnancy', 'o' => array('Yes', 'No', 'Uncertain', 'Not assessed')),

            array('t' => 'sub', 'l' => 'Obstetric history'),
            array('t' => 'note', 'text' => "Enter numbers where known. Use the patient's documented history and clarify the definitions if needed."),
            array('t' => 'text', 'k' => 'f_gravida', 'l' => 'Gravida (G)', 'ph' => 'Total pregnancies', 'max' => 60),
            array('t' => 'text', 'k' => 'f_para', 'l' => 'Para (P)', 'ph' => 'Births meeting threshold', 'max' => 60),
            array('t' => 'text', 'k' => 'f_abortions', 'l' => 'Abortions / pregnancy losses', 'ph' => 'Number and details'),
            array('t' => 'text', 'k' => 'f_children', 'l' => 'Living children', 'ph' => 'Number', 'max' => 60),
            array('t' => 'text', 'k' => 'f_contraception', 'l' => 'Contraception', 'ph' => 'Method, duration, use or none', 'w' => 'wide'),
            array('t' => 'select', 'k' => 'f_menopause', 'l' => 'Menopause status', 'o' => array('Premenopausal', 'Perimenopausal', 'Postmenopausal', 'Not applicable / unknown')),

            array('t' => 'sub', 'l' => 'Gynaecological symptoms'),
            array('t' => 'select', 'k' => 'f_discharge', 'l' => 'Vaginal discharge', 'o' => $APN),
            array('t' => 'select', 'k' => 'f_bleeding', 'l' => 'Abnormal vaginal bleeding', 'o' => $APN),
            array('t' => 'textarea', 'k' => 'f_details', 'l' => 'Additional details',
                  'ph' => 'Pain, discharge characteristics, bleeding pattern, pregnancy test, prior complications, relevant screening...'),
        ),
    ),

    /* ---------------- Male, 12+ ---------------- */
    array(
        'key' => 'male', 'cond' => 'male12', 'title' => 'Male Sexual and Genitourinary History',
        'fields' => array(
            array('t' => 'select', 'k' => 'm_urinary', 'l' => 'Urinary symptoms', 'o' => $APN),
            array('t' => 'select', 'k' => 'm_sexual', 'l' => 'Sexual dysfunction', 'o' => $APN),
            array('t' => 'select', 'k' => 'm_sti', 'l' => 'STI exposure / risk', 'o' => $APN),
            array('t' => 'select', 'k' => 'm_genital', 'l' => 'Genital symptoms', 'o' => $APN),
            array('t' => 'textarea', 'k' => 'm_details', 'l' => 'Details, if present',
                  'hint' => 'Urinary: frequency, urgency, dysuria, weak stream, nocturia, haematuria. Sexual: libido, erection, ejaculation, pain. STI: exposure timing, symptoms, testing. Genital: pain, swelling, lesions, discharge, lumps.',
                  'ph' => 'Describe symptoms, onset, duration, exposure, protection, testing and relevant details...'),
        ),
    ),

    /* ---------------- Pediatric, under 12 ---------------- */
    array(
        'key' => 'ped', 'cond' => 'child', 'title' => 'Pediatric History',
        'intro' => 'Suitable for infants, children, and adolescents. Questions should be directed to the parent or caregiver when appropriate, with developmentally suitable questions for older children.',
        'fields' => array(
            array('t' => 'sub', 'l' => 'A. Patient and caregiver information'),
            array('t' => 'auto', 'src' => 'name', 'l' => "Child's name", 'w' => 'wide'),
            array('t' => 'auto', 'src' => 'pid', 'l' => 'Patient ID / MRN'),
            array('t' => 'auto', 'src' => 'dob', 'l' => 'Date of birth'),
            array('t' => 'auto', 'src' => 'age_full', 'l' => 'Age (years / months / days)', 'w' => 'wide'),
            array('t' => 'auto', 'src' => 'sex', 'l' => 'Sex'),
            array('t' => 'radio', 'k' => 'p_informant', 'l' => 'Informant', 'o' => array('Mother', 'Father', 'Guardian', 'Child', 'Other')),
            array('t' => 'radio', 'k' => 'p_reliability', 'l' => 'Reliability of history', 'o' => array('Reliable', 'Partially reliable', 'Uncertain')),
            array('t' => 'text', 'k' => 'p_complaints', 'l' => 'Presenting complaint(s)', 'w' => 'wide', 'max' => 300),
            array('t' => 'text', 'k' => 'p_duration', 'l' => 'Duration of symptoms'),

            array('t' => 'sub', 'l' => 'B. Birth and neonatal history'),
            array('t' => 'note', 'text' => 'Primarily for infants and young children; obtain older-child birth history when clinically relevant.'),
            array('t' => 'radio', 'k' => 'p_place', 'l' => 'Place of delivery', 'o' => array('Hospital', 'Clinic', 'Home', 'Other')),
            array('t' => 'radio', 'k' => 'p_delivery', 'l' => 'Delivery', 'o' => array('Vaginal', 'Caesarean section', 'Assisted vaginal')),
            array('t' => 'radio', 'k' => 'p_cry', 'l' => 'Birth cry', 'o' => array('Immediate', 'Delayed', 'Unknown')),
            array('t' => 'radio', 'k' => 'p_resus', 'l' => 'Resuscitation required', 'o' => array('Yes', 'No', 'Unknown')),
            array('t' => 'radio', 'k' => 'p_nicu', 'l' => 'NICU admission', 'o' => $YN),
            array('t' => 'radio', 'k' => 'p_premature', 'l' => 'Prematurity', 'o' => $YN),
            array('t' => 'radio', 'k' => 'p_jaundice', 'l' => 'Neonatal jaundice', 'o' => $YN),
            array('t' => 'radio', 'k' => 'p_anomaly', 'l' => 'Congenital anomaly', 'o' => $YN),
            array('t' => 'text', 'k' => 'p_gest_age', 'l' => 'Gestational age', 'u' => 'weeks', 'max' => 20),
            array('t' => 'text', 'k' => 'p_birth_weight', 'l' => 'Birth weight', 'u' => 'kg', 'max' => 20),
            array('t' => 'text', 'k' => 'p_anomaly_details', 'l' => 'Congenital anomaly details', 'w' => 'wide'),
            array('t' => 'text', 'k' => 'p_birth_complications', 'l' => 'Birth complications', 'w' => 'wide', 'max' => 300),
            array('t' => 'text', 'k' => 'p_maternal', 'l' => 'Maternal illness or medication during pregnancy', 'w' => 'wide', 'max' => 300),

            array('t' => 'sub', 'l' => 'C. Feeding and nutrition history'),
            array('t' => 'radio', 'k' => 'p_infant_feeding', 'l' => 'Infant feeding', 'o' => array('Exclusive breastfeeding', 'Formula', 'Mixed feeding', 'Not applicable')),
            array('t' => 'radio', 'k' => 'p_diet', 'l' => 'Current diet', 'o' => array('Age-appropriate', 'Selective eating', 'Poor intake', 'Other')),
            array('t' => 'radio', 'k' => 'p_appetite', 'l' => 'Appetite', 'o' => array('Normal', 'Reduced', 'Increased')),
            array('t' => 'radio', 'k' => 'p_feed_difficulty', 'l' => 'Feeding difficulty / choking / swallowing problem', 'o' => $YN),
            array('t' => 'radio', 'k' => 'p_weight_loss', 'l' => 'Recent weight loss or poor weight gain', 'o' => $YN),
            array('t' => 'text', 'k' => 'p_breastfeeding', 'l' => 'Breastfeeding frequency and difficulties', 'w' => 'wide', 'max' => 300),
            array('t' => 'text', 'k' => 'p_complementary', 'l' => 'Complementary feeding started at', 'u' => 'months', 'max' => 20),
            array('t' => 'text', 'k' => 'p_food_allergy', 'l' => 'Food allergy or intolerance', 'w' => 'wide'),
            array('t' => 'text', 'k' => 'p_supplements', 'l' => 'Vitamin or nutritional supplements', 'w' => 'wide'),

            array('t' => 'sub', 'l' => 'D. Growth and development'),
            array('t' => 'auto', 'src' => 'weight', 'l' => 'Current weight'),
            array('t' => 'auto', 'src' => 'height_cm', 'l' => 'Length / height'),
            array('t' => 'text', 'k' => 'p_head', 'l' => 'Head circumference, if age-appropriate', 'u' => 'cm', 'max' => 20),
            array('t' => 'radio', 'k' => 'p_growth_chart', 'l' => 'Growth chart reviewed', 'o' => $YN),
            array('t' => 'radio', 'k' => 'p_weight_gain', 'l' => 'Weight gain', 'o' => array('Appropriate', 'Inadequate', 'Excessive', 'Unknown')),
            array('t' => 'grid', 'k' => 'p_milestones', 'l' => 'Developmental milestones', 'head' => array('Milestone', 'Status'),
                  'o' => array('Age-appropriate', 'Delayed', 'Concern'), 'rows' => array(
                      'gross' => 'Gross motor', 'fine' => 'Fine motor', 'speech' => 'Speech and language', 'social' => 'Social interaction')),
            array('t' => 'radio', 'k' => 'p_hearing_vision', 'l' => 'Hearing / vision concern', 'o' => $YN),
            array('t' => 'radio', 'k' => 'p_school', 'l' => 'School performance or learning concern', 'o' => array('Yes', 'No', 'Not applicable')),
            array('t' => 'radio', 'k' => 'p_regression', 'l' => 'Developmental regression or loss of skills', 'o' => $YN),
            array('t' => 'textarea', 'k' => 'p_dev_details', 'l' => 'Details and milestone concerns'),

            array('t' => 'sub', 'l' => 'E. Pediatric review of systems'),
            array('t' => 'checks', 'k' => 'p_ros_general', 'l' => 'General', 'o' => array('Fever', 'Weight change', 'Fatigue', 'Poor activity')),
            array('t' => 'checks', 'k' => 'p_ros_resp', 'l' => 'Respiratory', 'o' => array('Cough', 'Wheeze', 'Breathlessness')),
            array('t' => 'checks', 'k' => 'p_ros_cvs', 'l' => 'Cardiovascular', 'o' => array('Cyanosis', 'Syncope', 'Exercise intolerance')),
            array('t' => 'checks', 'k' => 'p_ros_gi', 'l' => 'Gastrointestinal', 'o' => array('Vomiting', 'Diarrhea', 'Abdominal pain')),
            array('t' => 'checks', 'k' => 'p_ros_gu', 'l' => 'Genitourinary', 'o' => array('Painful urination', 'Frequency', 'Reduced urine')),
            array('t' => 'checks', 'k' => 'p_ros_neuro', 'l' => 'Neurological', 'o' => array('Seizure', 'Weakness', 'Altered consciousness')),
            array('t' => 'checks', 'k' => 'p_ros_skin', 'l' => 'Skin', 'o' => array('Rash', 'Itching', 'Pallor')),
            array('t' => 'checks', 'k' => 'p_ros_ent', 'l' => 'ENT / eyes', 'o' => array('Ear pain', 'Hearing concern', 'Red eye', 'Visual concern')),
            array('t' => 'text', 'k' => 'p_ros_other', 'l' => 'Other', 'w' => 'full', 'max' => 300),
        ),
    ),

    /* ---------------- Geriatric, 60+ ---------------- */
    array(
        'key' => 'ger', 'cond' => 'senior', 'title' => 'Geriatric History',
        'fields' => array(
            array('t' => 'sub', 'l' => 'A. Patient and caregiver information'),
            array('t' => 'auto', 'src' => 'name', 'l' => 'Patient name', 'w' => 'wide'),
            array('t' => 'auto', 'src' => 'pid', 'l' => 'Patient ID / MRN'),
            array('t' => 'auto', 'src' => 'age_years', 'l' => 'Age'),
            array('t' => 'auto', 'src' => 'sex', 'l' => 'Sex'),
            array('t' => 'radio', 'k' => 'g_informant', 'l' => 'Informant', 'o' => array('Patient', 'Spouse', 'Child', 'Caregiver', 'Other')),
            array('t' => 'radio', 'k' => 'g_reliability', 'l' => 'Reliability of history', 'o' => array('Reliable', 'Limited', 'Collateral history needed')),
            array('t' => 'radio', 'k' => 'g_living', 'l' => 'Living arrangement', 'o' => array('Alone', 'With spouse', 'With family', 'Care facility')),
            array('t' => 'text', 'k' => 'g_caregiver', 'l' => 'Primary caregiver / contact', 'w' => 'wide'),
            array('t' => 'text', 'k' => 'g_complaints', 'l' => 'Presenting complaint(s)', 'w' => 'wide', 'max' => 300),
            array('t' => 'text', 'k' => 'g_progression', 'l' => 'Duration and progression', 'w' => 'full', 'max' => 300),

            array('t' => 'sub', 'l' => 'B. Present illness'),
            array('t' => 'checks', 'k' => 'g_present', 'l' => 'Symptoms present', 'other' => 'g_present_other', 'o' => array(
                'Fever / infection symptoms', 'Chest pain / palpitations', 'Breathlessness / cough', 'Dizziness / fainting', 'Falls / injury',
                'Weakness / reduced mobility', 'Memory loss / confusion', 'Headache / focal neurological symptoms', 'Poor appetite / weight loss',
                'Constipation / bowel changes', 'Urinary symptoms / incontinence', 'Sleep disturbance', 'Pain / joint stiffness', 'Mood change / social withdrawal')),
            array('t' => 'note', 'text' => 'Record onset, baseline function, recent change, associated symptoms, and effect on daily activities. Acute confusion or a sudden functional decline should be treated as potentially urgent, rather than automatically attributed to aging or dementia.'),

            array('t' => 'sub', 'l' => 'C. Past medical and surgical history'),
            array('t' => 'checks', 'k' => 'g_pmh', 'l' => 'Conditions', 'other' => 'g_pmh_other', 'ph' => 'Other chronic conditions', 'o' => array(
                'Hypertension', 'Diabetes mellitus', 'Ischemic heart disease / heart failure', 'Stroke / transient ischemic attack', 'Chronic kidney disease',
                'Chronic lung disease', 'Osteoarthritis / osteoporosis', 'Parkinsonism / neurological disease', 'Dementia / cognitive impairment',
                'Depression / anxiety', 'Cancer', 'Hearing impairment', 'Visual impairment', 'Thyroid disease', 'Previous fractures / falls')),
            array('t' => 'textarea', 'k' => 'g_surgery', 'l' => 'Previous surgery, hospitalizations, major injuries, and baseline health'),

            array('t' => 'sub', 'l' => 'D. Medication reconciliation'),
            array('t' => 'text', 'k' => 'g_med_rx', 'l' => 'Prescription medicines', 'w' => 'wide', 'max' => 300),
            array('t' => 'text', 'k' => 'g_med_otc', 'l' => 'Over-the-counter medicines', 'w' => 'wide', 'max' => 300),
            array('t' => 'text', 'k' => 'g_med_herbal', 'l' => 'Herbal / traditional medicines', 'w' => 'wide', 'max' => 300),
            array('t' => 'text', 'k' => 'g_med_allergy', 'l' => 'Drug allergies and reactions', 'w' => 'wide', 'max' => 300),
            array('t' => 'radio', 'k' => 'g_med_changes', 'l' => 'Recent medication changes', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_med_missed', 'l' => 'Missed doses / adherence concern', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_med_multi', 'l' => 'Multiple prescribers or pharmacies', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_med_sedative', 'l' => 'Sedatives, sleeping medicines, or other fall-risk medicines', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_med_assist', 'l' => 'Medication assistance required', 'o' => $YN),
            array('t' => 'text', 'k' => 'g_med_side', 'l' => 'Possible side effects or drug interactions', 'w' => 'full', 'max' => 300),
            array('t' => 'note', 'text' => 'Document medicine names, dose, frequency, and indication. Review for potentially inappropriate medicines and adjust treatment to renal function and individual circumstances when relevant.'),

            array('t' => 'sub', 'l' => 'E. Functional assessment'),
            array('t' => 'grid', 'k' => 'g_adl', 'l' => 'Activities of daily living (ADLs)', 'o' => array('Independent', 'Needs assistance', 'Dependent'), 'rows' => array(
                'bathing' => 'Bathing', 'dressing' => 'Dressing', 'toileting' => 'Toileting',
                'transfer' => 'Transferring between bed and chair', 'eating' => 'Eating', 'continence' => 'Continence')),
            array('t' => 'grid', 'k' => 'g_iadl', 'l' => 'Instrumental activities of daily living (IADLs)', 'o' => array('Independent', 'Needs assistance', 'Dependent'), 'rows' => array(
                'medicines' => 'Managing medicines', 'money' => 'Managing money / bills', 'shopping' => 'Shopping', 'cooking' => 'Cooking / preparing meals',
                'housework' => 'Housework', 'telephone' => 'Using telephone / communication', 'transport' => 'Transportation')),
            array('t' => 'checks', 'k' => 'g_mobility_aid', 'l' => 'Mobility aid', 'o' => array('None', 'Cane', 'Walker', 'Wheelchair', 'Other')),
            array('t' => 'radio', 'k' => 'g_decline', 'l' => 'Recent decline in independence', 'o' => $YN),
            array('t' => 'text', 'k' => 'g_home_assist', 'l' => 'Home assistance required', 'w' => 'full', 'max' => 300),

            array('t' => 'sub', 'l' => 'F. Falls and mobility history'),
            array('t' => 'radio', 'k' => 'g_falls', 'l' => 'Falls in the past 12 months', 'o' => array('None', 'One', 'Two or more')),
            array('t' => 'radio', 'k' => 'g_fall_injury', 'l' => 'Injury or head impact', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_fall_loc', 'l' => 'Loss of consciousness', 'o' => array('Yes', 'No', 'Uncertain')),
            array('t' => 'radio', 'k' => 'g_orthostatic', 'l' => 'Dizziness on standing', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_gait', 'l' => 'Gait / balance difficulty', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_fear_fall', 'l' => 'Fear of falling', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_chair_rise', 'l' => 'Difficulty rising from a chair', 'o' => $YN),
            array('t' => 'text', 'k' => 'g_last_fall', 'l' => 'Most recent fall and circumstances', 'w' => 'wide', 'max' => 300),
            array('t' => 'text', 'k' => 'g_home_hazards', 'l' => 'Home hazards (loose rugs, poor lighting, stairs)', 'w' => 'wide', 'max' => 300),
            array('t' => 'note', 'text' => 'Consider gait and balance assessment, orthostatic blood pressure where indicated, vision review, footwear, and medication review.'),

            array('t' => 'sub', 'l' => 'G. Cognitive and mental health history'),
            array('t' => 'radio', 'k' => 'g_memory', 'l' => 'Memory concern', 'o' => array('None', 'Mild', 'Progressive', 'Sudden onset')),
            array('t' => 'radio', 'k' => 'g_attention', 'l' => 'Difficulty with attention or decision-making', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_disorient', 'l' => 'Disorientation', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_behaviour', 'l' => 'Change in behavior or personality', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_sleep_wake', 'l' => 'Sleep-wake disturbance', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_low_mood', 'l' => 'Low mood / loss of interest', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_anxiety', 'l' => 'Anxiety', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_hallucination', 'l' => 'Hallucinations', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_acute_confusion', 'l' => 'Recent acute confusion', 'o' => $YN),
            array('t' => 'text', 'k' => 'g_prev_assessment', 'l' => 'Previous cognitive or mood assessment', 'w' => 'wide', 'max' => 300),
            array('t' => 'text', 'k' => 'g_assessment_notes', 'l' => 'Assessment notes', 'w' => 'wide', 'max' => 300),
            array('t' => 'note', 'text' => 'Use validated tools such as Mini-Cog, GPCOG, or an appropriate depression screening instrument when clinically indicated. Acute confusion requires assessment for causes such as infection, medication effects, metabolic disturbance, pain, or delirium.'),

            array('t' => 'sub', 'l' => 'H. Nutrition and hydration'),
            array('t' => 'auto', 'src' => 'weight', 'l' => 'Weight'),
            array('t' => 'auto', 'src' => 'height_cm', 'l' => 'Height'),
            array('t' => 'auto', 'src' => 'bmi', 'l' => 'BMI'),
            array('t' => 'radio', 'k' => 'g_appetite', 'l' => 'Appetite', 'o' => array('Normal', 'Reduced', 'Increased')),
            array('t' => 'radio', 'k' => 'g_weight_loss', 'l' => 'Unintentional weight loss', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_chewing', 'l' => 'Difficulty chewing / dental problems', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_swallowing', 'l' => 'Difficulty swallowing', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_fluid', 'l' => 'Daily fluid intake concerns', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_malnutrition', 'l' => 'Signs of malnutrition or dehydration', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_food_access', 'l' => 'Food access / ability to prepare meals', 'o' => array('Adequate', 'Concern')),
            array('t' => 'text', 'k' => 'g_special_diet', 'l' => 'Special diet or dietary restrictions', 'w' => 'wide', 'max' => 300),
            array('t' => 'text', 'k' => 'g_nutrition_support', 'l' => 'Nutrition support required', 'w' => 'wide', 'max' => 300),

            array('t' => 'sub', 'l' => 'I. Sensory, sleep, and continence history'),
            array('t' => 'checks', 'k' => 'g_vision', 'l' => 'Vision', 'o' => array('Adequate', 'Reduced', 'Cataract / other eye disease')),
            array('t' => 'radio', 'k' => 'g_glasses', 'l' => 'Glasses used', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_hearing', 'l' => 'Hearing', 'o' => array('Adequate', 'Reduced')),
            array('t' => 'radio', 'k' => 'g_hearing_aid', 'l' => 'Hearing aid', 'o' => $YN),
            array('t' => 'checks', 'k' => 'g_sleep', 'l' => 'Sleep', 'o' => array('Satisfactory', 'Insomnia', 'Daytime sleepiness', 'Frequent waking')),
            array('t' => 'checks', 'k' => 'g_urinary', 'l' => 'Urinary function', 'o' => array('Normal', 'Urgency', 'Frequency', 'Incontinence', 'Retention', 'Painful urination')),
            array('t' => 'checks', 'k' => 'g_bowel', 'l' => 'Bowel function', 'o' => array('Normal', 'Constipation', 'Incontinence', 'Change in bowel habits')),
            array('t' => 'text', 'k' => 'g_sensory_details', 'l' => 'Additional details', 'w' => 'full', 'max' => 300),

            array('t' => 'sub', 'l' => 'J. Social, family, and safeguarding history'),
            array('t' => 'radio', 'k' => 'g_family_involvement', 'l' => 'Family involvement', 'o' => array('Adequate', 'Limited')),
            array('t' => 'radio', 'k' => 'g_isolation', 'l' => 'Social isolation / loneliness', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_financial', 'l' => 'Financial difficulty affecting care', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_home_safety', 'l' => 'Home safety concerns', 'o' => $YN),
            array('t' => 'radio', 'k' => 'g_neglect', 'l' => 'Possible neglect, abuse, or exploitation', 'o' => array('Yes', 'No', 'Not assessed')),
            array('t' => 'text', 'k' => 'g_support', 'l' => 'Main caregiver and available support', 'w' => 'wide', 'max' => 300),
            array('t' => 'text', 'k' => 'g_tobacco_alcohol', 'l' => 'Tobacco / alcohol use', 'w' => 'wide', 'max' => 300),
            array('t' => 'text', 'k' => 'g_care_plan', 'l' => 'Advance care planning or stated care preferences, if appropriate', 'w' => 'full', 'max' => 300),
        ),
    ),

    /* ---------------- Immunization (everybody) ---------------- */
    array(
        'key' => 'imm', 'cond' => 'all', 'title' => 'General Immunization History',
        'intro' => 'Immunization status: select the applicable responses.',
        'fields' => array(
            array('t' => 'select', 'k' => 'i_record', 'l' => 'Immunization record', 'o' => array('Vaccination card available', 'Electronic record available', 'Patient / caregiver recall only', 'No record available')),
            array('t' => 'select', 'k' => 'i_status', 'l' => 'Overall immunization status', 'o' => array('Complete for age', 'Partially completed', 'Not immunized', 'Unknown')),
            array('t' => 'select', 'k' => 'i_missed', 'l' => 'Any missed or overdue vaccines?', 'o' => array('Yes', 'No', 'Unknown / not assessed')),
            array('t' => 'select', 'k' => 'i_reaction', 'l' => 'History of adverse reaction following immunization', 'o' => array('No known reaction', 'Yes', 'Unknown')),
            array('t' => 'textarea', 'k' => 'i_info', 'l' => 'Additional information', 'ph' => 'Previous doses, missing records, contraindications, counselling...'),
        ),
    ),
);

/* =====================================================================
 * MODE: FORM  (every block is printed; the browser shows only the ones for this patient)
 * ===================================================================== */
if ($mh_mode === 'form') {
    echo '<div class="mhf-sc mhf-numbered">';
    echo '<p class="mhf-note mhf-cond-note" id="mh5_profile_note"><i class="fa-solid fa-user-check" aria-hidden="true"></i> <span>The questions below are chosen from the age and sex saved in Tab 1.</span></p>';
    foreach ($mh5_blocks as $block) {
        echo mh_sc_block_form($block);
    }
    echo '</div>';
    return;
}

$flags = mh_profile_flags(isset($mh_ctx['tab1']) ? $mh_ctx['tab1'] : array());

/* =====================================================================
 * MODE: SAVE  (only the blocks that apply to this patient are kept)
 * ===================================================================== */
if ($mh_mode === 'save') {
    $d = array();
    $e = array();
    $d['_blocks'] = array();
    foreach ($mh5_blocks as $block) {
        if (empty($flags[$block['cond']])) {
            continue;
        }
        $d['_blocks'][] = $block['key'];
        mh_sc_save($block['fields'], $mh_input, $d, $e);
    }
    $mh_result = array('data' => $d, 'errors' => $e);
    return;
}

/* =====================================================================
 * MODE: PDF
 * ===================================================================== */
if ($mh_mode === 'pdf') {
    $d    = $mh_data;
    $auto = mh_profile_values(isset($mh_ctx['tab1']) ? $mh_ctx['tab1'] : array(), isset($mh_ctx['pid']) ? $mh_ctx['pid'] : '');
    // blocks chosen when the tab was saved; older saves without that list -> work it out now
    $chosen = (isset($d['_blocks']) && is_array($d['_blocks'])) ? $d['_blocks'] : null;

    echo '<div class="sec-title">' . (int)$mh_no . '. ' . mh_h($mh_title) . '</div>';
    $n = 0;
    foreach ($mh5_blocks as $block) {
        $show = $chosen !== null ? in_array($block['key'], $chosen, true) : !empty($flags[$block['cond']]);
        if (!$show) {
            continue;
        }
        $n++;
        echo mh_pdf_group($n . '. ' . $block['title']);
        echo mh_sc_pdf($block['fields'], $d, $auto);
    }
    return;
}
