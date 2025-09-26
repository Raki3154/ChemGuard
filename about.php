<?php
session_start();
if(!isset($_SESSION['plant_name'])){
    header("Location: login.php");
    exit();
}

$plant_id = $_SESSION['plant_id'];
$conn = new mysqli('localhost','root','raki3154','chemguard');
if($conn->connect_error){ die("Connection failed: ".$conn->connect_error); }

// For demo, fetch boiler info and last service info from boiler_data
$sql = "SELECT timestamp, temperature, pressure, efficiency, pH, flow_control FROM boiler_data ORDER BY timestamp ASC";
$result = $conn->query($sql);

if($result->num_rows > 0){
    $data = $result->fetch_assoc();
    $efficiency = $data['efficiency'];
    $temperature = $data['temperature'];
    $pressure = $data['pressure'];
    $pH = $data['pH'];
    $flow = $data['flow_control'];
    $last_serviced = $data['timestamp']; // assuming timestamp is last service
}else{
    $efficiency = $temperature = $pressure = $pH = $flow = $last_serviced = "No Data";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>About ChemGuard</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #0b0c10;
    color: #fff;
    padding: 20px;
    margin: 0;
}
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
.card {
    background: #1f2833;
    padding: 20px;
    border-radius: 10px;
    max-width: 600px;
    margin: 50px auto;
}
h3 {
    color: #66fcf1;
    margin-top: 20px;
}
p {
    margin: 6px 0;
}
</style>
</head>
<body>
<header>
    <h2>ChemGuard - About</h2>
    <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="mentoring.php">Mentoring</a>
    </div>
</header>

<div class="card">
    <h3>Product Manufactured:</h3>
    <p>Pulverized Coal Water Tube Boiler</p>

    <h3>Optimum Values:</h3>
    <p>Temperature: 500°C</p>
    <p>Pressure: 150 bar</p>
    <p>pH Level: 7</p>
    <p>Flow Control: 120 L/min</p>

    <h3>Current Efficiency:</h3>
    <p><?php echo $efficiency; ?>%</p>

    <h3>Last Serviced Date:</h3>
    <p><?php echo $last_serviced; ?></p>

    <h3>Latest Parameters:</h3>
    <p>Temperature: <?php echo $temperature; ?> °C</p>
    <p>Pressure: <?php echo $pressure; ?> bar</p>
    <p>pH Level: <?php echo $pH; ?></p>
    <p>Flow Control: <?php echo $flow; ?> L/min</p>

    <h3>About Project:</h3>
    <p>ChemGuard is an AI-powered analyzer for coal water tube boilers. It monitors real-time parameters, detects issues, and provides actionable insights to improve efficiency and safety.</p>
</div>

</body>
</html>
