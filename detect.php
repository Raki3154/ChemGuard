<?php 
session_start(); 
if(!isset($_SESSION['plant_name'])){
    header("Location: login.php");
    exit();
}  

// Predefined issues (like AI rules)
$issues = [
    ["description"=>"High Pressure Detected","instruction"=>"Reduce coal feed","confidence"=>90],
    ["description"=>"Low Temperature Detected","instruction"=>"Increase airflow","confidence"=>75],
    ["description"=>"Efficiency Drop","instruction"=>"Check fuel quality","confidence"=>65],
    ["description"=>"Abnormal Vibration","instruction"=>"Inspect boiler tubes","confidence"=>80]
];

// Randomly decide if problems exist
$problem_detected = rand(0,1); 
$detected_issues = [];

if($problem_detected){
    // Pick random number of issues (1–3 issues at once)
    shuffle($issues);
    $detected_issues = array_slice($issues,0,rand(1,3));
    $status = "⚠️ Problems Detected!";
}else{
    $status = "✅ Machine is working good.";
}

// Simulation scenarios
$simulations = [
    "temperature" => "Reducing temperature by 10% delays pressure rise by 15 minutes.",
    "fuel" => "Increasing fuel feed by 5% improves temperature stability by 5°C.",
    "airflow" => "Increasing airflow by 10% accelerates combustion and stabilizes pressure.",
    "other" => "Perform scheduled maintenance to avoid unexpected downtime."
];
?>  

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Detection Result & Simulation</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="styles/detect.css">
</head>
<body class="gradient-bg">

<h2><?php echo $status; ?></h2>  

<?php if($problem_detected){ ?>
    <?php foreach($detected_issues as $i=>$issue){ 
        // generate a safe id for input/label
        $safeId = "issue_" . $i;
    ?>
        <div class="issue-box">
            <div class="issue-row">
                <input type="checkbox" id="<?php echo $safeId; ?>" name="issue[]" value="<?php echo htmlspecialchars($issue['description']); ?>" checked>
                <label for="<?php echo $safeId; ?>" class="issue-label"><strong><?php echo $issue['description']; ?></strong></label>
            </div>
            <p style="margin:6px 0;"><em>Instruction:</em> <?php echo $issue['instruction']; ?></p>
            <div class="progress" aria-hidden="true">
                <div class="progress-bar" id="progressBar<?php echo $i; ?>"><?php echo $issue['confidence']; ?>%</div>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="issue-box" style="text-align:center;">
        <strong>No issues detected — machine is operating normally.</strong>
    </div>
<?php } ?>

<div class="card">
    <div class="sim-options">
        <strong>Select Simulation Scenario:</strong>
        <div class="option-group" style="margin-top:8px;">
            <label><input type="radio" name="sim" value="temperature" checked> Reduce Temperature</label>
            <label><input type="radio" name="sim" value="fuel"> Adjust Fuel</label>
            <label><input type="radio" name="sim" value="airflow"> Adjust Airflow</label>
            <label><input type="radio" name="sim" value="other"> Other Maintenance</label>
        </div>
    </div>

    <div class="grid">
        <div>
            <button class="btn" onclick="simulate()">Run What-if Simulation</button>
            <div id="simResult" class="ai-response" style="margin-top:12px; color:#fff; font-weight:600;"></div>
        </div>
        <div>
            <div class="ai-response">
                <h4 style="margin-top:0;">Quick Tips</h4>
                <p class="muted">• Verify sensor calibration regularly.</p>
                <p class="muted">• Maintain optimal air-fuel ratio.</p>
                <p class="muted">• Inspect tubes and seals for leaks.</p>
                <p class="muted">• Check water quality to prevent scaling.</p>
            </div>
        </div>
    </div>
</div>

<div style="margin-top:18px;">
    <a href="home.php" class="btn btn-outline" style="display:inline-block; margin-right:10px;">Back to Home</a>
    <a href="graph.php" class="btn btn-outline">See Graphs</a>
</div>

<!-- Chatbot Trigger -->
<div class="chatbot-trigger" id="chatbotTrigger">
    <i class="fas fa-robot"></i>
</div>

<script>
const simulations = <?php echo json_encode($simulations); ?>;

// Animate each progress bar
window.onload = function() {
    <?php if($problem_detected){ 
        foreach($detected_issues as $i=>$issue){ ?>
        (function(){
            const bar = document.getElementById("progressBar<?php echo $i; ?>");
            const width = <?php echo $issue['confidence']; ?>;
            let current = 0;
            const interval = setInterval(() => {
                if(current >= width){
                    clearInterval(interval);
                } else {
                    current++;
                    bar.style.width = current + "%";
                    bar.innerText = current + "%";
                }
            }, 12);
        })();
    <?php } } ?>
    
    // Animate cards
    const cards = document.querySelectorAll('.card, .issue-box');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = 1;
            card.style.transform = 'translateY(0)';
        }, index * 200);
    });
};

function simulate() {
    const radios = document.getElementsByName('sim');
    let selected = 'cooling';
    for(let i=0; i<radios.length; i++){
        if(radios[i].checked){
            selected = radios[i].value;
            break;
        }
    }
    const simText = simulations[selected];
    const out = document.getElementById("simResult");
    out.innerText = simText;

    // Optional: read simulation result aloud (browser TTS)
    if('speechSynthesis' in window){
        const msg = new SpeechSynthesisUtterance(simText);
        speechSynthesis.speak(msg);
    }
}

// Chatbot trigger functionality
document.getElementById('chatbotTrigger').addEventListener('click', function() {
    window.location.href = 'ai.php';
});
</script>

</body>
</html>