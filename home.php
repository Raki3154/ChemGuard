<?php
session_start();
if(!isset($_SESSION['plant_name'])){
    header("Location: login.php");
    exit();
}
$plant_name = $_SESSION['plant_name'];

// DB connection
$conn = new mysqli('localhost','root','raki3154','chemguard');
if($conn->connect_error){ die("Connection failed: ".$conn->connect_error); }

// Fetch latest record
$sql = "SELECT * FROM boiler_data ORDER BY timestamp DESC LIMIT 1";
$result = $conn->query($sql);

if($result && $result->num_rows > 0){
    $data = $result->fetch_assoc();
    $boiler_id   = $data['boiler_id'];
    $temperature = $data['temperature'];
    $pressure    = $data['pressure'];
    $efficiency  = $data['efficiency'];
} else {
    $boiler_id = $temperature = $pressure = $efficiency = "No Data";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ChemGuard Dashboard</title>
<style>
body { font-family: Arial,sans-serif; background: #0b0c10; color:#fff; margin:0; padding:0; }
header {
    padding: 10px 20px;
    background: #1f2833;
    color: #fff;
    display: flex;             /* enable flex layout */
    justify-content: space-between;  /* text left, link right */
    align-items: center;       /* vertical alignment */
}
.card { width:400px; margin:50px auto; padding:20px; background:#1f2833; border-radius:10px; text-align:center; }
button { padding:10px 20px; border:none; border-radius:5px; cursor:pointer; background:#66fcf1; color:#000; }
button:hover{ background:#45a29e; color:#fff;}
#video-container { width:100%; height:300px; background:#0b0c10; border:1px solid #45a29e; margin-bottom:20px; display:flex; justify-content:center; align-items:center; }
video { width:100%; height:100%; border-radius:10px; }
</style>
</head>
<body>
<header>
    <h2>ChemGuard</h2>
    <a href="dashboard.php" style="color:#66fcf1; text-decoration:none;">Dashboard</a>
	<a href="about.php" style="color:#66fcf1; text-decoration:none;">About</a>
	<a href="mentoring.php" style="color:#66fcf1; text-decoration:none;">Mentoring</a>
</header>

<div class="card">
    <h3>Boiler Video</h3>
    <div id="video-container">
        <video controls autoplay loop>
            <source src="videos/boiler.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <p><strong>Boiler ID:</strong> <?php echo $boiler_id; ?></p>
    <p><strong>Temperature:</strong> <?php echo $temperature; ?> °C</p>
    <p><strong>Pressure:</strong> <?php echo $pressure; ?> bar</p>
    <p><strong>Efficiency:</strong> <?php echo $efficiency; ?> %</p>
    <form method="POST" action="detect.php">
        <button type="submit" name="detect">Detect</button>
    </form>
</div>
</body>
</html>
