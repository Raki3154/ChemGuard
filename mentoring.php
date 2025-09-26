<?php
session_start();
if(!isset($_SESSION['plant_name'])){
    header("Location: login.php");
    exit();
}

// Mentor Info
$mentor_designation = "Senior Boiler Engineer";
$date = date("d-m-Y");
$time = date("H:i:s");

// AI Response Logic (simple keyword matching)
$ai_response = "";
if(isset($_POST['ask'])){
    $question = strtolower($_POST['question']);
    $sdg_selected = isset($_POST['sdg']) ? $_POST['sdg'] : [];

    $responses = [];

    // Fuel & Combustion
    if(strpos($question,'fuel')!==false || strpos($question,'combustion')!==false){
        $responses[] = "Check the air-fuel ratio, inspect burners, and ensure proper coal quality.";
    }
    // Water & Steam
    if(strpos($question,'water')!==false || strpos($question,'steam')!==false){
        $responses[] = "Monitor water level, treat water chemically, and check for leaks.";
    }
    // Heat Transfer
    if(strpos($question,'heat')!==false || strpos($question,'tube')!==false){
        $responses[] = "Clean boiler tubes regularly and monitor temperature sensors to avoid hot spots.";
    }

    // SDG messages
    if(in_array('7',$sdg_selected)){
        $responses[] = "Apply SDG 7: Optimize fuel usage for clean energy efficiency.";
    }
    if(in_array('9',$sdg_selected)){
        $responses[] = "Apply SDG 9: Use AI for predictive maintenance and industrial innovation.";
    }
    if(in_array('12',$sdg_selected)){
        $responses[] = "Apply SDG 12: Reduce waste and emissions for sustainable production.";

    }

    $ai_response = !empty($responses) ? implode("<br>• ", $responses) : "No specific solution found. Please refine your question.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ChemGuard Mentoring</title>
<style>
body {font-family: Arial, sans-serif; background:#0b0c10; color:#fff; padding:20px; margin:0;}
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 20px; /* reduced vertical spacing */
    background: #1f2833;
}
header h2 {
    margin: 0;
    font-size: 1.5em;
    color: #66fcf1;
}
header .nav-links a {
    margin-left: 15px;
    color: #66fcf1;
    text-decoration: none;
    font-weight: bold;
}
header .nav-links a:hover {
    text-decoration: underline;
}
.card {background:#1f2833; padding:20px; border-radius:10px; max-width:800px; margin:20px auto;}
label {display:block; margin-top:10px;}
textarea {width:100%; padding:10px; border-radius:5px; border:none; margin-top:5px;}
button {margin-top:10px; padding:10px 20px; border:none; border-radius:5px; background:#66fcf1; color:#000; cursor:pointer;}
button:hover{background:#45a29e; color:#fff;}
.sdg-options {margin-top:10px;}
.sdg-options label {display:inline-block; margin-right:15px;}
.ai-response {background:#0b0c10; border:1px solid #66fcf1; padding:15px; border-radius:5px; margin-top:20px;}
</style>
</head>
<body>
<header>
    <h2>ChemGuard Mentoring</h2>
	<div class="nav-links">
        <a href="home.php">Home</a>
        <a href="about.php">Mentoring</a>
    </div>
</header>

<div class="card">
    <h3>Mentor Info:</h3>
    <p><strong>Designation:</strong> <?php echo $mentor_designation; ?></p>
    <p><strong>Date:</strong> <?php echo $date; ?></p>
    <p><strong>Time:</strong> <?php echo $time; ?></p>
</div>

<div class="card">
    <h3>Ask a Question:</h3>
    <form method="POST">
        <label for="question">Your Question:</label>
        <textarea name="question" id="question" rows="3" placeholder="Describe the boiler issue..."><?php if(isset($_POST['question'])) echo htmlspecialchars($_POST['question']); ?></textarea>

        <div class="sdg-options">
            <p>Select related SDG goals:</p>
            <label><input type="checkbox" name="sdg[]" value="7" <?php if(isset($_POST['sdg']) && in_array('7', $_POST['sdg'])) echo 'checked'; ?>> SDG 7</label>
            <label><input type="checkbox" name="sdg[]" value="9" <?php if(isset($_POST['sdg']) && in_array('9', $_POST['sdg'])) echo 'checked'; ?>> SDG 9</label>
            <label><input type="checkbox" name="sdg[]" value="12" <?php if(isset($_POST['sdg']) && in_array('12', $_POST['sdg'])) echo 'checked'; ?>> SDG 12</label>
        </div>

        <button type="submit" name="ask">Get Solution</button>
    </form>

    <?php if($ai_response): ?>
        <div class="ai-response">
            <h4>Mentor Solution:</h4>
            <p>• <?php echo $ai_response; ?></p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
