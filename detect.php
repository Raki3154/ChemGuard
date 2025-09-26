<?php 
session_start(); 
if(!isset($_SESSION['plant_name'])){
    header("Location: login.php");
    exit();
}  

// Simulate AI analysis
$issues = [
    ["description"=>"High Pressure Detected","instruction"=>"Reduce coal feed","confidence"=>90],
    ["description"=>"Low Temperature Detected","instruction"=>"Increase airflow","confidence"=>75]
];

$problem_detected = rand(0,1); 
if($problem_detected){
    $issue = $issues[array_rand($issues)];
    $status = "Problem Detected!";
}else{
    $status = "Machine is working good.";
}

// What-if simulation options
$simulations = [
    "cooling" => "Reducing cooling by 10% delays pressure rise by 15 minutes.",
    "fuel" => "Increasing fuel feed by 5% improves temperature stability by 5°C.",
    "airflow" => "Increasing airflow by 10% accelerates combustion and stabilizes pressure."
];
?>  

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Detection Result & Simulation</title>
<style>
body{
    font-family:Arial,sans-serif; 
    text-align:center; 
    padding:50px; 
    background:#0b0c10; 
    color:#fff;
}
.progress{
    background:#1f2833; 
    border-radius:20px; 
    margin:20px auto; 
    width:50%; 
    height:30px;
    overflow: hidden;
    display: block;
}
.progress-bar{
    background:#66fcf1; 
    height:100%; 
    border-radius:20px; 
    text-align:center; 
    line-height:30px; 
    color:#000;
    display: block;
    width: 0%; /* initial width */
}
button{
    margin-top:20px;
    padding:10px 20px;
    border:none;
    border-radius:5px;
    background:#66fcf1;
    color:#000;
    cursor:pointer;
}
button:hover{background:#45a29e; color:#fff;}
#simResult{
    margin-top:20px;
    padding:15px;
    background:#1f2833;
    border-radius:10px;
    max-width:500px;
    margin-left:auto;
    margin-right:auto;
    min-height:50px;
    text-align:left;
}
.sim-options{
    margin-top:20px;
    text-align:left;
    display:inline-block;
}
.sim-options label{
    display:block;
    margin:5px 0;
}
</style>
</head>
<body>

<h2><?php echo $status; ?></h2>  

<?php if($problem_detected){ ?>
    <p><strong>Issue:</strong> <?php echo $issue['description']; ?></p>
    <p><strong>Instruction:</strong> <?php echo $issue['instruction']; ?></p>
    <div class="progress">
        <div class="progress-bar" id="progressBar"><?php echo $issue['confidence']; ?>%</div>
    </div>
<?php } ?>

<div class="sim-options">
    <strong>Select Simulation Scenario:</strong>
    <label><input type="radio" name="sim" value="temperature reduction" checked> Reduce Cooling</label>
    <label><input type="radio" name="sim" value="fuel"> Adjust Fuel Feed</label>
    <label><input type="radio" name="sim" value="airflow"> Adjust Airflow</label>
</div>

<button onclick="simulate()">Run What-if Simulation</button>

<div id="simResult"></div>

<a href="home.php" style="display:block; margin-top:30px; color:#66fcf1; text-decoration:none;">Back to Home</a>

<script>
const simulations = <?php echo json_encode($simulations); ?>;

// Animate progress bar on load
window.onload = function() {
    const bar = document.getElementById('progressBar');
    const width = <?php echo $problem_detected ? $issue['confidence'] : 0; ?>;
    let current = 0;
    const interval = setInterval(() => {
        if(current >= width){
            clearInterval(interval);
        } else{
            current++;
            bar.style.width = current + "%";
            bar.innerText = current + "%";
        }
    }, 10);
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
    document.getElementById("simResult").innerText = simText;
}
</script>

</body>
</html>
