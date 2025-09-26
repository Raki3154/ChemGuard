<?php
session_start();
if(!isset($_SESSION['plant_name'])){
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost','root','raki3154','chemguard');
if($conn->connect_error){ die("Connection failed: ".$conn->connect_error); }

// Fetch **all records**
$sql = "SELECT timestamp, temperature, pressure, pH, flow_control FROM boiler_data ORDER BY timestamp ASC";
$result = $conn->query($sql);

$timestamps = [];
$temperature = [];
$pressure = [];
$pH = [];
$flow = [];

if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $timestamps[] = $row['timestamp'];
        $temperature[] = $row['temperature'];
        $pressure[] = $row['pressure'];
        $pH[] = $row['pH'];
        $flow[] = $row['flow_control'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Data Graphs - ChemGuard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
	padding: 0 8px;
    margin-left: 0;
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
    <h2>ChemGuard - Full Data Graphs</h2>
    <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="about.php">About</a>
    </div>
</header>

<canvas id="temperatureChart" height="100"></canvas>
<canvas id="pressureChart" height="100"></canvas>
<canvas id="pHChart" height="100"></canvas>
<canvas id="flowChart" height="100"></canvas>

<script>
const timestamps = <?php echo json_encode($timestamps); ?>;
const temperature = <?php echo json_encode($temperature); ?>;
const pressure = <?php echo json_encode($pressure); ?>;
const pH = <?php echo json_encode($pH); ?>;
const flow = <?php echo json_encode($flow); ?>;

// Temperature Chart
new Chart(document.getElementById('temperatureChart'), {
    type: 'line',
    data: {
        labels: timestamps,
        datasets: [{
            label: 'Temperature (°C)',
            data: temperature,
            borderColor: '#66fcf1',
            backgroundColor: 'rgba(102,252,241,0.2)',
            fill: true,
            tension: 0.3
        }]
    },
    options: { responsive:true, plugins:{legend:{labels:{color:'#fff'}}}, scales:{x:{ticks:{color:'#fff'}}, y:{ticks:{color:'#fff'}}} }
});

// Pressure Chart
new Chart(document.getElementById('pressureChart'), {
    type: 'line',
    data: {
        labels: timestamps,
        datasets: [{
            label: 'Pressure (bar)',
            data: pressure,
            borderColor: '#45a29e',
            backgroundColor: 'rgba(69,162,158,0.2)',
            fill: true,
            tension: 0.3
        }]
    },
    options: { responsive:true, plugins:{legend:{labels:{color:'#fff'}}}, scales:{x:{ticks:{color:'#fff'}}, y:{ticks:{color:'#fff'}}} }
});

// pH Chart
new Chart(document.getElementById('pHChart'), {
    type: 'line',
    data: {
        labels: timestamps,
        datasets: [{
            label: 'pH Level',
            data: pH,
            borderColor: '#f1c40f',
            backgroundColor: 'rgba(241,196,15,0.2)',
            fill: true,
            tension: 0.3
        }]
    },
    options: { responsive:true, plugins:{legend:{labels:{color:'#fff'}}}, scales:{x:{ticks:{color:'#fff'}}, y:{ticks:{color:'#fff'}}} }
});

// Flow Control Chart
new Chart(document.getElementById('flowChart'), {
    type: 'line',
    data: {
        labels: timestamps,
        datasets: [{
            label: 'Flow Control (L/min)',
            data: flow,
            borderColor: '#e74c3c',
            backgroundColor: 'rgba(231,76,60,0.2)',
            fill: true,
            tension: 0.3
        }]
    },
    options: { responsive:true, plugins:{legend:{labels:{color:'#fff'}}}, scales:{x:{ticks:{color:'#fff'}}, y:{ticks:{color:'#fff'}}} }
});
</script>
</body>
</html>
