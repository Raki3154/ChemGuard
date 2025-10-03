<?php
	session_start();
	if(!isset($_SESSION['plant_name']))
	{
		header("Location: login.php");
		exit();
	}
	$plant_id = $_SESSION['plant_id'];
	$conn = new mysqli('localhost','root','raki3154','chemguard');
	if($conn->connect_error){ die("Connection failed: ".$conn->connect_error); }
	$sql = "SELECT timestamp, temperature, pressure, efficiency, pH, flow_control FROM boiler_data ORDER BY timestamp DESC LIMIT 1";
	$result = $conn->query($sql);
	if($result->num_rows > 0)
	{
		$data = $result->fetch_assoc();
		$efficiency = $data['efficiency'];
		$temperature = $data['temperature'];
		$pressure = $data['pressure'];
		$pH = $data['pH'];
		$flow = $data['flow_control'];
		$last_serviced = date('M j, Y', strtotime($data['timestamp']));
	}
	else
	{
		$efficiency = "N/A";
		$temperature = "N/A";
		$pressure = "N/A";
		$pH = "N/A";
		$flow = "N/A";
		$last_serviced = "No Data";
	}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>About ChemGuard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
		<link rel="stylesheet" href="styles/about.css">
    </head>
    <body class="gradient-bg">
        <header>
            <h2>ChemGuard - About</h2>
            <div class="nav-links">
                <a href="home.php">Home</a>
                <a href="mentoring.php">Mentoring</a>
				<a href="logout.php">Logout</a>
            </div>
        </header>
		<br>
        <div class="container">
            <div class="subhero">
                <h3>AI for Safer, Efficient Boilers</h3>
                <p class="muted">ChemGuard combines sensor data with AI to surface insights that improve performance, safety, and sustainability.</p>
                <div class="grid grid-4" style="margin-top: 2rem;">
                    <div class="stat">
                        <div class="label">Current Efficiency</div>
                        <div class="value"><?php echo $efficiency; ?>%</div>
                    </div>
                    <div class="stat">
                        <div class="label">Last Serviced</div>
                        <div class="value"><?php echo $last_serviced; ?></div>
                    </div>
                    <div class="stat">
                        <div class="label">Temperature</div>
                        <div class="value"><?php echo $temperature; ?>°C</div>
                    </div>
                    <div class="stat">
                        <div class="label">Pressure</div>
                        <div class="value"><?php echo $pressure; ?> bar</div>
                    </div>
                </div>
            </div>
            <div class="grid grid-2">
                <div class="card">
                    <h3><i class="fas fa-industry" style="margin-right: 10px;"></i>Product Manufactured</h3>
                    <p>Pulverized Coal Water Tube Boiler</p>
                    <p class="muted">High-efficiency boiler system designed for optimal performance with advanced monitoring capabilities.</p>
                </div>
                <div class="card">
                    <h3><i class="fas fa-tachometer-alt" style="margin-right: 10px;"></i>Optimum Values</h3>
                    <div class="grid grid-2">
                        <p>Temperature: <strong>700°C</strong></p>
                        <p>Pressure: <strong>30 bar</strong></p>
                        <p>pH Level: <strong>7.0</strong></p>
                        <p>Flow Control: <strong>60 L/min</strong></p>
                    </div>
                </div>              
                <div class="card">
                    <h3><i class="fas fa-chart-line" style="margin-right: 10px;"></i>Latest Parameters</h3>
                    <div class="grid grid-2">
                        <p>Temperature: <strong><?php echo $temperature; ?> °C</strong></p>
                        <p>Pressure: <strong><?php echo $pressure; ?> bar</strong></p>
                        <p>pH Level: <strong><?php echo $pH; ?></strong></p>
                        <p>Flow Control: <strong><?php echo $flow; ?> L/min</strong></p>
                    </div>
                </div>
                <div class="card">
                    <h3><i class="fas fa-info-circle" style="margin-right: 10px;"></i>About Project</h3>
                    <p class="muted">ChemGuard monitors real-time parameters, detects anomalies, and recommends corrective actions. The goal: maximize efficiency while minimizing risk and resource consumption.</p>
                    <p class="muted">Our AI-driven system provides predictive maintenance alerts and optimization suggestions to ensure peak boiler performance.</p>
                    <div style="margin-top: 1.5rem;">
						<a href="graph.php" class="btn btn-outline" style="margin-left: 10px;">
                            <i class="fas fa-chart-bar"></i> View Graphs
                        </a>
                        <a href="mentoring.php" class="btn btn-outline" style="margin-left: 10px;">
                            <i class="fas fa-user-graduate"></i> Ask a Mentor
                        </a>
                    </div>
                </div>
            </div>
            <div class="card">
                <h3><i class="fas fa-shield-alt" style="margin-right: 10px;"></i>Safety Features</h3>
                <div class="grid grid-2">
                    <div>
                        <h4 style="color: var(--accent); margin-bottom: 10px;">Real-time Monitoring</h4>
                        <p class="muted">Continuous tracking of all critical parameters with instant alerts for any deviations from safe operating ranges.</p>
                    </div>
                    <div>
                        <h4 style="color: var(--accent); margin-bottom: 10px;">Predictive Maintenance</h4>
                        <p class="muted">AI algorithms predict potential failures before they occur, allowing for proactive maintenance scheduling.</p>
                    </div>
                    <div>
                        <h4 style="color: var(--accent); margin-bottom: 10px;">Emergency Protocols</h4>
                        <p class="muted">Automated shutdown procedures and safety protocols activated when critical thresholds are exceeded.</p>
                    </div>
                    <div>
                        <h4 style="color: var(--accent); margin-bottom: 10px;">Data Analytics</h4>
                        <p class="muted">Comprehensive data analysis to identify trends, optimize performance, and reduce operational costs.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="chatbot-trigger" id="chatbotTrigger">
            <i class="fas fa-robot"></i>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() 
			{
                const cards = document.querySelectorAll('.card, .stat');
                cards.forEach((card, index) => 
				{
                    setTimeout(() => {
                        card.classList.add('visible');
                    }, index * 200);
                });
            });
            document.getElementById('chatbotTrigger').addEventListener('click', function() 
			{
                window.location.href = 'ai.php';
            });
        </script>
    </body>
</html>