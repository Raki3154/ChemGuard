<?php
// chatbot.php
session_start();

// Set timezone to GMT+5:30 (India Standard Time)
date_default_timezone_set('Asia/Kolkata');

// Initialize session variables if not set
if (!isset($_SESSION['initialized'])) {
    $_SESSION['chat'] = [];
    $_SESSION['language'] = 'english';
    $_SESSION['initialized'] = true;
}

// Clear session if new session requested
if (isset($_GET['new_session'])) {
    $_SESSION['chat'] = [];
    header('Location: ' . str_replace('?new_session=true', '', $_SERVER['REQUEST_URI']));
    exit;
}

// Limit chat history to prevent memory issues
if (count($_SESSION['chat']) > 50) {
    array_shift($_SESSION['chat']);
}

// Enhanced Q&A database with multilingual support including sustainability and SDGs
$qa = [
    "safe pressure" => [
        "english" => "The safe operating pressure is between 150-300 bar. Never exceed 300 bar",
        "hindi" => "सुरक्षित ऑपरेटिंग दबाव 150-300 बार के बीच है। 300 बार से अधिक कभी न करें।",
        "tamil" => "பாதுகாப்பான இயக்க அழுத்தம் 150-300 பட்டைக்கு இடையில் உள்ளது. 300 பட்டையை ஒருபோதும் தாண்ட வேண்டாம்."
    ],
    "efficiency" => [
        "english" => "Your current boiler efficiency is ~85%. Try cleaning heat exchangers to improve. This supports SDG 9 by promoting industrial innovation.",
        "hindi" => "आपकी वर्तमान बॉयलर दक्षता ~85% है। सुधार के लिए हीट एक्सचेंजर्स को साफ करने का प्रयास करें। यह एसडीजी 9 का समर्थन करता है जो औद्योगिक नवाचार को बढ़ावा देता है।",
        "tamil" => "உங்கள் தற்போதைய கொதிகலன் திறன் ~85% ஆகும். மேம்படுத்த வெப்ப பரிமாற்றிகளை சுத்தம் செய்ய முயற்சிக்கவும். இது தொழிற்துறை கண்டுபிடிப்புகளை ஊக்குவிப்பதன் மூலம் எஸ்டிஜி 9-ஐ ஆதரிக்கிறது."
    ],
    "reduce emissions" => [
        "english" => "Switching to biofuel or optimizing combustion can reduce CO₂ by 20%. This aligns with SDG 7 (clean energy) and SDG 12 (responsible consumption).",
        "hindi" => "बायोफ्यूल पर स्विच करना या दहन को अनुकूलित करने से CO₂ को 20% तक कम किया जा सकता है। यह एसडीजी 7 (स्वच्छ ऊर्जा) और एसडीजी 12 (जिम्मेदार खपत) के साथ संरेखित होता है।",
        "tamil" => "உயிரி எரிபொருளுக்கு மாறுவது அல்லது எரிப்பை மேம்படுத்துவது CO₂ ஐ 20% குறைக்கும். இது எஸ்டிஜி 7 (சுத்தமான ஆற்றல்) மற்றும் எஸ்டிஜி 12 (பொறுப்பான நுகர்வு) ஆகியவற்றுடன் இணைகிறது."
    ],
    "maintenance" => [
        "english" => "Regular maintenance should be performed every 6 months. Check filters and valves. Proper maintenance supports SDG 12 by reducing waste and extending equipment life.",
        "hindi" => "नियमित रखरखाव हर 6 महीने में किया जाना चाहिए। फिल्टर और वाल्व की जांच करें। उचित रखरखाव एसडीजी 12 का समर्थन करता है क्योंकि यह कचरे को कम करता है और उपकरण के जीवन को बढ़ाता है।",
        "tamil" => "வழக்கமான பராமரிப்பு ஒவ்வொரு 6 மாதங்களிலும் செய்யப்பட வேண்டும். வடிப்பான்கள் மற்றும் வால்வுகளை சரிபார்க்கவும். சரியான பராமரிப்பு கழிவுகளைக் குறைத்து உபகரணங்களின் வாழ்நாளை நீட்டிப்பதன் மூலம் எஸ்டிஜி 12-ஐ ஆதரிக்கிறது."
    ],
    "emergency shutdown" => [
        "english" => "In case of emergency, use the red shutdown button and evacuate immediately.",
        "hindi" => "आपात स्थिति में, लाल शटडाउन बटन का उपयोग करें और तुरंत खाली करें।",
        "tamil" => "அவசர நிலையில், சிவப்பு அணைப்பு பொத்தானைப் பயன்படுத்தி உடனடியாக வெளியேறவும்."
    ],
    "temperature range" => [
        "english" => "Operating temperature should be maintained between 500-600°C.",
        "hindi" => "ऑपरेटिंग तापमान 500-600°C के बीच बनाए रखा जाना चाहिए।",
        "tamil" => "இயக்க வெப்பநிலை 500-600°C க்கு இடையில் பராமரிக்கப்பட வேண்டும்."
    ],
    "water treatment" => [
        "english" => "Use recommended water treatment chemicals to prevent scaling and corrosion. This supports SDG 12 through responsible chemical management.",
        "hindi" => "स्केलिंग और जंग को रोकने के लिए अनुशंसित जल उपचार रसायनों का उपयोग करें। यह जिम्मेदार रासायनिक प्रबंधन के माध्यम से एसडीजी 12 का समर्थन करता है।",
        "tamil" => "அளவிடுதல் மற்றும் அரிப்பைத் தடுக்க பரிந்துரைக்கப்படும் நீர் சிகிச்சை இரசாயனங்களைப் பயன்படுத்தவும். இது பொறுப்பான இரசாயன மேலாண்மை மூலம் எஸ்டிஜி 12-ஐ ஆதரிக்கிறது."
    ],
    "fuel types" => [
        "english" => "Compatible fuels: natural gas, diesel, biofuel. Avoid high-sulfur fuels. Choosing biofuels supports SDG 7 (affordable and clean energy).",
        "hindi" => "संगत ईंधन: प्राकृतिक गैस, डीजल, बायोफ्यूल। उच्च-सल्फर ईंधन से बचें। बायोफ्यूल चुनना एसडीजी 7 (सस्ती और स्वच्छ ऊर्जा) का समर्थन करता है।",
        "tamil" => "இணக்கமான எரிபொருட்கள்: இயற்கை எரிவாயு, டீசல், உயிரி எரிபொருள். அதிக கந்தக எரிபொருட்களைத் தவிர்க்கவும். உயிரி எரிபொருட்களைத் தேர்ந்தெடுப்பது எஸ்டிஜி 7 (மலிவு மற்றும் சுத்தமான ஆற்றல்) ஆதரிக்கிறது."
    ],
    "troubleshooting" => [
        "english" => "Common issues: pressure drops (check valves), efficiency loss (clean heat exchangers).",
        "hindi" => "सामान्य समस्याएं: दबाव गिरना (वाल्व जांचें), दक्षता हानि (हीट एक्सचेंजर्स साफ करें)।",
        "tamil" => "பொதுவான சிக்கல்கள்: அழுத்தம் குறைதல் (வால்வுகளை சரிபார்க்கவும்), செயல்திறன் இழப்பு (வெப்ப பரிமாற்றிகளை சுத்தம் செய்யவும்)."
    ],
    "safety protocols" => [
        "english" => "Always wear PPE: safety glasses, gloves, and protective clothing in the boiler room.",
        "hindi" => "हमेशा पीपीई पहनें: बॉयलर रूम में सुरक्षा चश्मा, दस्ताने और सुरक्षात्मक कपड़े।",
        "tamil" => "எப்போதும் பிபிஇ அணியுங்கள்: கொதிகலன் அறையில் பாதுகாப்பு கண்ணாடிகள், கையுறைகள் மற்றும் பாதுகாப்பு ஆடைகள்."
    ],
    "sustainability" => [
        "english" => "Boiler sustainability involves energy efficiency, emission reduction, and responsible resource use. This supports SDG 7 (Clean Energy), SDG 9 (Industry Innovation), and SDG 12 (Responsible Consumption).",
        "hindi" => "बॉयलर स्थिरता में ऊर्जा दक्षता, उत्सर्जन में कमी और जिम्मेदार संसाधन उपयोग शामिल है। यह एसडीजी 7 (स्वच्छ ऊर्जा), एसडीजी 9 (उद्योग नवाचार) और एसडीजी 12 (जिम्मेदार खपत) का समर्थन करता है।",
        "tamil" => "கொதிகலன் நிலைத்தன்மையில் ஆற்றல் திறன், உமிழ்வு குறைப்பு மற்றும் பொறுப்பான வள பயன்பாடு அடங்கும். இது எஸ்டிஜி 7 (சுத்தமான ஆற்றல்), எஸ்டிஜி 9 (தொழில் கண்டுபிடிப்பு) மற்றும் எஸ்டிஜி 12 (பொறுப்பான நுகர்வு) ஆகியவற்றை ஆதரிக்கிறது."
    ],
    "sdg 7" => [
        "english" => "SDG 7: Affordable and Clean Energy - Our boilers support this through energy efficiency, renewable fuel options, and reduced emissions.",
        "hindi" => "एसडीजी 7: सस्ती और स्वच्छ ऊर्जा - हमारे बॉयलर ऊर्जा दक्षता, नवीकरणीय ईंधन विकल्पों और कम उत्सर्जन के माध्यम से इसका समर्थन करते हैं।",
        "tamil" => "எஸ்டிஜி 7: மலிவு மற்றும் சுத்தமான ஆற்றல் - எங்கள் கொதிகலன்கள் ஆற்றல் திறன், புதுப்பிக்கத்தக்க எரிபொருள் விருப்பங்கள் மற்றும் குறைக்கப்பட்ட உமிழ்வுகள் மூலம் இதை ஆதரிக்கின்றன."
    ],
    "sdg 9" => [
        "english" => "SDG 9: Industry, Innovation and Infrastructure - We contribute through efficient industrial processes, innovative boiler technologies, and sustainable infrastructure development.",
        "hindi" => "एसडीजी 9: उद्योग, नवाचार और बुनियादी ढांचा - हम कुशल औद्योगिक प्रक्रियाओं, अभिनव बॉयलर प्रौद्योगिकियों और स्थायी बुनियादी ढांचे के विकास के माध्यम से योगदान करते हैं।",
        "tamil" => "எஸ்டிஜி 9: தொழில், கண்டுபிடிப்பு மற்றும் உள்கட்டமைப்பு - திறமையான தொழில்துறை செயல்முறைகள், புதுமையான கொதிகலன் தொழில்நுட்பங்கள் மற்றும் நிலையான உள்கட்டமைப்பு மேம்பாடு மூலம் நாங்கள் பங்களிக்கிறோம்."
    ],
    "sdg 12" => [
        "english" => "SDG 12: Responsible Consumption and Production - Our focus on efficient fuel use, waste reduction, and proper maintenance supports sustainable consumption patterns.",
        "hindi" => "एसडीजी 12: जिम्मेदार खपत और उत्पादन - कुशल ईंधन उपयोग, अपशिष्ट में कमी और उचित रखरखाव पर हमारा ध्यान स्थायी खपत पैटर्न का समर्थन करता है।",
        "tamil" => "எஸ்டிஜி 12: பொறுப்பான நுகர்வு மற்றும் உற்பத்தி - திறமையான எரிபொருள் பயன்பாடு, கழிவு குறைப்பு மற்றும் சரியான பராமரிப்பு ஆகியவற்றில் எங்கள் கவனம் நிலையான நுகர்வு முறைகளை ஆதரிக்கிறது."
    ],
    "clean energy" => [
        "english" => "Clean energy solutions for boilers include biofuels, solar thermal integration, and waste heat recovery systems that support SDG 7.",
        "hindi" => "बॉयलर के लिए स्वच्छ ऊर्जा समाधान में बायोफ्यूल, सौर थर्मल एकीकरण और अपशिष्ट ऊष्मा पुनर्प्राप्ति प्रणालियाँ शामिल हैं जो एसडीजी 7 का समर्थन करती हैं।",
        "tamil" => "கொதிகலன்களுக்கான சுத்தமான ஆற்றல் தீர்வுகளில் உயிரி எரிபொருட்கள், சூரிய வெப்ப ஒருங்கிணைப்பு மற்றும் கழிவு வெப்ப மீட்பு அமைப்புகள் ஆகியவை அடங்கும், இவை எஸ்டிஜி 7-ஐ ஆதரிக்கின்றன."
    ],
    "waste reduction" => [
        "english" => "Reduce boiler waste through proper maintenance, water treatment, and efficient operation. This directly supports SDG 12 targets.",
        "hindi" => "उचित रखरखाव, जल उपचार और कुशल संचालन के माध्यम से बॉयलर अपशिष्ट कम करें। यह सीधे एसडीजी 12 लक्ष्यों का समर्थन करता है।",
        "tamil" => "சரியான பராமரிப்பு, நீர் சிகிச்சை மற்றும் திறமையான செயல்பாடு மூலம் கொதிகலன் கழிவுகளைக் குறைக்கவும். இது நேரடியாக எஸ்டிஜி 12 இலக்குகளை ஆதரிக்கிறது."
    ],
    "innovation" => [
        "english" => "Boiler innovation includes smart monitoring systems, AI optimization, and advanced materials that support SDG 9 for sustainable industrialization.",
        "hindi" => "बॉयलर नवाचार में स्मार्ट मॉनिटरिंग सिस्टम, एआई ऑप्टिमाइजेशन और उन्नत सामग्री शामिल हैं जो स्थायी औद्योगिकीकरण के लिए एसडीजी 9 का समर्थन करती हैं।",
        "tamil" => "கொதிகலன் கண்டுபிடிப்புகளில் ஸ்மார்ட் கண்காணிப்பு அமைப்புகள், AI மேம்படுத்தல் மற்றும் மேம்பட்ட பொருட்கள் ஆகியவை அடங்கும், இவை நிலையான தொழில்மயமாக்கலுக்கான எஸ்டிஜி 9-ஐ ஆதரிக்கின்றன."
    ],
    "hello" => [
        "english" => "Hi! I am ChemGuard Bot. Ask me about boiler safety, efficiency, emissions, or sustainability (SDG 7, 9, 12).",
        "hindi" => "नमस्ते! मैं ChemGuard Bot हूं। मुझसे बॉयलर सुरक्षा, दक्षता, उत्सर्जन, या स्थिरता (एसडीजी 7, 9, 12) के बारे में पूछें।",
        "tamil" => "வணக்கம்! நான் ChemGuard Bot. கொதிகலன் பாதுகாப்பு, செயல்திறன், உமிழ்வுகள் அல்லது நிலைத்தன்மை (எஸ்டிஜி 7, 9, 12) பற்றி என்னிடம் கேளுங்கள்."
    ],
    "hi" => [
        "english" => "Hello! How can I assist you with boiler operations or sustainability today?",
        "hindi" => "नमस्ते! आज मैं आपकी बॉयलर संचालन या स्थिरता में कैसे सहायता कर सकता हूं?",
        "tamil" => "வணக்கம்! இன்று கொதிகலன் செயல்பாடுகள் அல்லது நிலைத்தன்மையில் நான் உங்களுக்கு எவ்வாறு உதவ முடியும்?"
    ],
    "help" => [
        "english" => "I can help with: safe pressure, efficiency, emissions, maintenance, sustainability, SDG 7, SDG 9, SDG 12, clean energy, waste reduction, innovation.",
        "hindi" => "मैं इनमें मदद कर सकता हूं: सुरक्षित दबाव, दक्षता, उत्सर्जन, रखरखाव, स्थिरता, एसडीजी 7, एसडीजी 9, एसडीजी 12, स्वच्छ ऊर्जा, अपशिष्ट में कमी, नवाचार।",
        "tamil" => "நான் உதவ முடியும்: பாதுகாப்பான அழுத்தம், செயல்திறன், உமிழ்வுகள், பராமரிப்பு, நிலைத்தன்மை, எஸ்டிஜி 7, எஸ்டிஜி 9, எஸ்டிஜி 12, சுத்தமான ஆற்றல், கழிவு குறைப்பு, கண்டுபிடிப்பு."
    ]
];

// Varied fallback responses with multilingual support
$fallbacks = [
    "english" => [
        "I'm not sure I understand. Could you rephrase your question about boiler operations or sustainability?",
        "I specialize in boiler safety, efficiency, and sustainability (SDG 7, 9, 12). Try asking about pressure, emissions, or clean energy.",
        "That's outside my current knowledge. I can help with operational parameters, safety procedures, and sustainability goals.",
        "Please ask about boiler operations, safety, efficiency, maintenance, or sustainability for specific guidance."
    ],
    "hindi" => [
        "मुझे यकीन नहीं है कि मैं समझ गया। क्या आप बॉयलर संचालन या स्थिरता के बारे में अपना प्रश्न फिर से बना सकते हैं?",
        "मैं बॉयलर सुरक्षा, दक्षता और स्थिरता (एसडीजी 7, 9, 12) में विशेषज्ञता रखता हूं। दबाव, उत्सर्जन, या स्वच्छ ऊर्जा के बारे में पूछने का प्रयास करें।",
        "यह मेरी वर्तमान ज्ञान से बाहर है। मैं परिचालन मापदंडों, सुरक्षा प्रक्रियाओं और स्थिरता लक्ष्यों में मदद कर सकता हूं।",
        "विशिष्ट मार्गदर्शन के लिए कृपया बॉयलर संचालन, सुरक्षा, दक्षता, रखरखाव, या स्थिरता के बारे में पूछें।"
    ],
    "tamil" => [
        "நான் புரிந்து கொண்டேனா என்று எனக்குத் தெரியவில்லை. கொதிகலன் செயல்பாடுகள் அல்லது நிலைத்தன்மை பற்றி உங்கள் கேள்வியை மீண்டும் வடிவமைக்க முடியுமா?",
        "நான் கொதிகலன் பாதுகாப்பு, செயல்திறன் மற்றும் நிலைத்தன்மையில் (எஸ்டிஜி 7, 9, 12) நிபுணத்துவம் பெற்றவன். அழுத்தம், உமிழ்வுகள் அல்லது சுத்தமான ஆற்றல் பற்றி கேட்க முயற்சிக்கவும்.",
        "அது எனது தற்போதைய அறிவுக்கு அப்பாற்பட்டது. செயல்பாட்டு அளவுருக்கள், பாதுகாப்பு நடைமுறைகள் மற்றும் நிலைத்தன்மை இலக்குகளில் நான் உதவ முடியும்.",
        "குறிப்பிட்ட வழிகாட்டுதலுக்கு கொதிகலன் செயல்பாடுகள், பாதுகாப்பு, செயல்திறன், பராமரிப்பு அல்லது நிலைத்தன்மை பற்றி கேளுங்கள்."
    ]
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update language preference
    if (isset($_POST['language'])) {
        $_SESSION['language'] = $_POST['language'];
        // Prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    
    // Clear chat if requested
    if (isset($_POST['clear_chat'])) {
        $_SESSION['chat'] = [];
        // Prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    
    // Process user message
    if (isset($_POST['msg']) && !empty(trim($_POST['msg']))) {
        $userInput = htmlspecialchars(strtolower(trim($_POST['msg'])));
        $currentLanguage = $_SESSION['language'];
        $fallback = $fallbacks[$currentLanguage][array_rand($fallbacks[$currentLanguage])];
        $reply = $fallback;
        
        // Find the best matching response
        $bestMatchScore = 0;
        $bestMatchReply = $reply;
        
        foreach($qa as $q => $a) {
            // Check for direct keyword match
            if (strpos($userInput, $q) !== false) {
                $matchQuality = strlen($q); // Longer matches are better
                if ($matchQuality > $bestMatchScore) {
                    $bestMatchScore = $matchQuality;
                    $bestMatchReply = $a[$currentLanguage];
                }
            }
        }
        
        $reply = $bestMatchReply;
        $_SESSION['chat'][] = [
            "user" => $userInput, 
            "bot" => $reply, 
            "time" => date("H:i:s"), // This will now show GMT+5:30 time
            "language" => $currentLanguage
        ];
        
        // Prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChemGuard Chatbot</title>
    <link rel="stylesheet" href="styles/ai.css">
</head>
<body>
    <div class="chatbox">
        <div class="header">
            <h2>ChemGuard Chatbot</h2>
            <p>Your boiler safety, efficiency and sustainability assistant</p>
            <div class="header-buttons">
                <a href="?new_session=true" class="header-btn">New Chat</a>
                <a href="home.php" class="header-btn">Home</a>
            </div>
        </div>
        
        <div class="controls">
            <div class="language-selector">
                <span>Language:</span>
                <form method="POST" id="languageForm">
                    <select name="language" onchange="document.getElementById('languageForm').submit()">
                        <option value="english" <?php echo ($_SESSION['language'] ?? 'english') == 'english' ? 'selected' : ''; ?>>English</option>
                        <option value="tamil" <?php echo ($_SESSION['language'] ?? 'english') == 'tamil' ? 'selected' : ''; ?>>Tamil</option>
                        <option value="hindi" <?php echo ($_SESSION['language'] ?? 'english') == 'hindi' ? 'selected' : ''; ?>>Hindi</option>
                    </select>
                </form>
            </div>
        </div>
        
        <div class="suggestions">
            <div class="suggestion" onclick="setSuggestion('Hello')">Hello</div>
            <div class="suggestion" onclick="setSuggestion('safe pressure')">Safe Pressure</div>
            <div class="suggestion" onclick="setSuggestion('efficiency')">Efficiency</div>
            <div class="suggestion" onclick="setSuggestion('maintenance')">Maintenance</div>
            <div class="suggestion" onclick="setSuggestion('sustainability')">Sustainability</div>
            <div class="suggestion" onclick="setSuggestion('sdg 7')">SDG 7</div>
            <div class="suggestion" onclick="setSuggestion('sdg 9')">SDG 9</div>
            <div class="suggestion" onclick="setSuggestion('sdg 12')">SDG 12</div>
            <div class="suggestion" onclick="setSuggestion('help')">Help</div>
        </div>
        
        <div class="chat-container" id="chatContainer">
            <?php if (empty($_SESSION['chat'])): ?>
                <div class="welcome-msg">
                    <p>Hello! I'm ChemGuard, your boiler and sustainability assistant. How can I help you today?</p>
                </div>
            <?php else: ?>
                <?php foreach($_SESSION['chat'] as $c): ?>
                    <div class="msg user">
                        <?php echo $c['user']; ?>
                        <div class="msg-time"><?php echo $c['time']; ?> (IST)</div>
                    </div>
                    <div class="msg bot">
                        <?php echo $c['bot']; ?>
                        <div class="msg-time"><?php echo $c['time']; ?> (IST)</div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <form method="POST" class="input-area">
            <input type="text" name="msg" id="userInput" placeholder="Ask about boiler operations, safety, efficiency, or sustainability..." required autocomplete="off">
            <button type="submit">Send</button>
            <button type="submit" name="clear_chat" class="clear-btn">Clear</button>
        </form>
    </div>

    <script>
        // Auto-scroll to bottom of chat
        function scrollToBottom() {
            const container = document.getElementById('chatContainer');
            container.scrollTop = container.scrollHeight;
        }
        
        // Set suggestion text
        function setSuggestion(text) {
            document.getElementById('userInput').value = text;
            document.getElementById('userInput').focus();
        }
        
        // Focus input on load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('userInput').focus();
            scrollToBottom();
        });
        
        // Handle Enter key for submission
        document.getElementById('userInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.querySelector('form').submit();
            }
        });
    </script>
</body>
</html>