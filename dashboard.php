<?php
	session_start();
	if(!isset($_SESSION['plant_name']))
	{
		header("Location: login.php");
		exit();
	}
	$conn = new mysqli('localhost','root','raki3154','chemguard');
	if($conn->connect_error)
	{ 
		die("Connection failed: ".$conn->connect_error); 
	}
	$message = '';
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$boiler_id = $_POST['boiler_id'];
		$temperature = $_POST['temperature'];
		$pressure = $_POST['pressure'];
		$efficiency = $_POST['efficiency'];
		$pH = $_POST['pH'];
		$flow_control = $_POST['flow_control'];
		$sql = "INSERT INTO boiler_data (timestamp, boiler_id, temperature, pressure, efficiency, pH, flow_control) 
				VALUES (NOW(), ?, ?, ?, ?, ?, ?)";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("sddddi", $boiler_id, $temperature, $pressure, $efficiency, $pH, $flow_control);
		if($stmt->execute())
		{
			$message = "✅ Data successfully recorded!";
		} 
		else 
		{
			$message = "❌ Error recording data: " . $conn->error;
		}
		$stmt->close();
	}
	$stats_sql = "SELECT 
		AVG(temperature) as avg_temp,
		AVG(pressure) as avg_pressure,
		AVG(efficiency) as avg_efficiency,
		COUNT(*) as total_readings
		FROM boiler_data";
	$stats_result = $conn->query($stats_sql);
	$stats = $stats_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>ChemGuard - Dashboard</title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
		<link rel="stylesheet" href="styles/dashboard.css">
	</head>
	<body>
		<div class="container">
			<header>
				<div class="header-left">
					<h1>ChemGuard</h1>
					<div class="plant-info">Plant: <?php echo $_SESSION['plant_name']; ?></div>
				</div>
				<div class="nav-links">
					<a href="graph.php">Graphical Analysis</a>
					<a href="about.php">About</a>
					<a href="mentoring.php">Mentoring</a>
					<a href="logout.php">Logout</a>
				</div>
			</header>
			
			<?php if($message): ?>
				<div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
					<?php echo $message; ?>
				</div>
			<?php endif; ?>

			<div class="main-content">
				<!-- Data Form Section -->
				<div class="data-form">
					<h2 class="form-title">Enter Boiler Data</h2>
					<form method="POST" action="">
						<div class="form-grid">
							<div class="form-group">
								<label for="boiler_id"><i class="fas fa-industry"></i> Boiler ID</label>
								<select id="boiler_id" name="boiler_id" required>
									<option value="">Select Boiler</option>
									<option value="B001">Boiler 001</option>
									<option value="B002">Boiler 002</option>
									<option value="B003">Boiler 003</option>
								</select>
							</div>
							
							<div class="form-group">
								<label for="temperature"><i class="fas fa-thermometer-half"></i> Temperature (°C)</label>
								<input type="number" id="temperature" name="temperature" step="5" min="500" max="700" required 
									   placeholder="Enter temperature">
							</div>
							
							<div class="form-group">
								<label for="pressure"><i class="fas fa-tachometer-alt"></i> Pressure (bar)</label>
								<input type="number" id="pressure" name="pressure" step="1" min="15" max="30" required
									   placeholder="Enter pressure">
							</div>
							
							<div class="form-group">
								<label for="efficiency"><i class="fas fa-percentage"></i> Efficiency (%)</label>
								<input type="number" id="efficiency" name="efficiency" step="1" min="75" max="90" required
									   placeholder="Enter efficiency">
							</div>	
							
							<div class="form-group">
								<label for="pH"><i class="fas fa-vial"></i> pH Level</label>
								<input type="number" id="pH" name="pH" step="0.1" min="0" max="14" required
									   placeholder="Enter pH level">
							</div>
							
							<div class="form-group">
								<label for="flow_control"><i class="fas fa-tint"></i> Flow Control (L/min)</label>
								<input type="number" id="flow_control" name="flow_control" min="0" max="500" required
									   placeholder="Enter flow rate">
							</div>
						</div>
						<button type="submit" class="btn">
							<i class="fas fa-database"></i> Record Data
						</button>
					</form>
				</div>

				<!-- Stats and Auto-fill Section -->
				<div class="stats-section">
					<!-- Statistics Cards -->
					<div class="stats-grid">
						<div class="stat-card">
							<div class="stat-value"><?php echo number_format($stats['avg_temp'] ?? 0, 1); ?>°C</div>
							<div class="stat-label">Avg Temperature</div>
						</div>
						<div class="stat-card">
							<div class="stat-value"><?php echo number_format($stats['avg_pressure'] ?? 0, 1); ?> bar</div>
							<div class="stat-label">Avg Pressure</div>
						</div>
						<div class="stat-card">
							<div class="stat-value"><?php echo number_format($stats['avg_efficiency'] ?? 0, 1); ?>%</div>
							<div class="stat-label">Avg Efficiency</div>
						</div>
						<div class="stat-card">
							<div class="stat-value"><?php echo $stats['total_readings'] ?? 0; ?></div>
							<div class="stat-label">Total Readings</div>
						</div>
					</div>

					<!-- Auto-fill Section -->
					<div class="auto-fill-section">
						<h3 class="auto-fill-title">Quick Tools</h3>
						<button onclick="autoFillSampleData()" class="auto-fill-btn">
							<i class="fas fa-magic"></i>
							<span>Auto-fill Sample Data</span>
						</button>
					</div>
				</div>
			</div>
		</div>

		<div class="chatbot-trigger" id="chatbotTrigger">
			<i class="fas fa-robot"></i>
		</div>

		<script>
			document.getElementById('chatbotTrigger').addEventListener('click', function() {
				window.location.href = 'ai.php';
			});

			function autoFillSampleData() {
				document.getElementById('boiler_id').value = 'B001';
				document.getElementById('temperature').value = '650';
				document.getElementById('pressure').value = '25';
				document.getElementById('efficiency').value = '82';
				document.getElementById('pH').value = '7.2';
				document.getElementById('flow_control').value = '250';
				
				// Show success message
				const messageDiv = document.createElement('div');
				messageDiv.className = 'message success';
				messageDiv.textContent = '✅ Sample data auto-filled!';
				document.querySelector('.container').insertBefore(messageDiv, document.querySelector('.main-content'));
				
				// Remove message after 3 seconds
				setTimeout(() => {
					messageDiv.remove();
				}, 3000);
			}

			// Add input validation and real-time feedback
			document.addEventListener('DOMContentLoaded', function() {
				const inputs = document.querySelectorAll('input[type="number"]');
				inputs.forEach(input => {
					input.addEventListener('input', function() {
						if (this.checkValidity()) {
							this.style.borderColor = 'rgba(76, 175, 80, 0.5)';
						} else {
							this.style.borderColor = 'rgba(244, 67, 54, 0.5)';
						}
					});
				});
			});
		</script>
	</body>
</html>