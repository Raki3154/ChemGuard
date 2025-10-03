<?php
	session_start();
	if(!isset($_SESSION['plant_name']))
	{
		header("Location: login.php");
		exit();
	}
	$plant_name = $_SESSION['plant_name'];
	$conn = new mysqli('localhost','root','raki3154','chemguard');
	if($conn->connect_error)
	{
		die("Connection failed: ".$conn->connect_error); 
	}
	$stmt = $conn->prepare("SELECT * FROM boiler_data ORDER BY timestamp DESC LIMIT 1");
	$stmt->execute();
	$result = $stmt->get_result();
	if($result && $result->num_rows > 0)
	{
		$data = $result->fetch_assoc();
		$boiler_id   = $data['boiler_id'];
		$boiler_type = $data['boiler_type'] ?? "Pulverized Coal";
		$fuel_used   = $data['fuel_used'] ?? "Coal";
		$temperature = $data['temperature'];
		$pressure    = $data['pressure'];
		$efficiency  = $data['efficiency'];
	} 
	else 
	{
		$boiler_id = $boiler_type = $fuel_used = "N/A";
		$temperature = $pressure = $efficiency = "No Data";
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>ChemGuard Dashboard</title>
		<link rel="stylesheet" href="styles/home.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
		<script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
	</head>
	<body>
		<header>
			<h2>ChemGuard</h2>
			<div class="nav-links">
				<a href="dashboard.php">Dashboard</a>
				<a href="about.php">About</a>
				<a href="mentoring.php">Mentoring</a>
				<a href="logout.php">Logout</a>
			</div>
		</header>
		
		<!-- Modal Popup -->
		<div id="boilerModal" class="modal">
			<div class="modal-content">
				<span class="close" onclick="closeModal()">&times;</span>
				<h3>Boiler Details</h3>
				<p><strong>Boiler ID:</strong> <?php echo $boiler_id; ?></p>
				<p><strong>Type:</strong> <?php echo $boiler_type; ?></p>
				<p><strong>Fuel Used:</strong> <?php echo $fuel_used; ?></p>
				<button class="btn" onclick="closeModal()" style="margin-top: 15px;">Continue to Dashboard</button>
			</div>
		</div>
		
		<div class="main-container">
			<div class="boiler-card">
				<h3>Boiler 3D Model</h3>
				<div class="model-container">
					<model-viewer src="models/boiler.glb"
								  ar ar-modes="webxr scene-viewer quick-look"
								  camera-controls auto-rotate
								  environment-image="neutral"
								  exposure="1"
								  class="model-viewer">
					</model-viewer>
				</div>
				
				<div class="boiler-stats">
					<div class="stat-item">
						<strong>Boiler ID:</strong> <?php echo $boiler_id; ?>
					</div>
					<div class="stat-item">
						<strong>Temperature:</strong> <?php echo $temperature; ?> °C
					</div>
					<div class="stat-item">
						<strong>Pressure:</strong> <?php echo $pressure; ?> bar
					</div>
					<div class="stat-item">
						<strong>Efficiency:</strong> <?php echo $efficiency; ?> %
					</div>
				</div>
				
				<form method="POST" action="detect.php">
					<button type="submit" class="btn" name="detect">Run Detection</button>
				</form>
			</div>
		</div>
		
		<div class="chatbot-trigger" id="chatbotTrigger">
			<i class="fas fa-robot"></i>
		</div>
		
		<script>
			// Show modal immediately when page loads
			document.addEventListener('DOMContentLoaded', function() {
				// Check if we should show the modal
				if(!sessionStorage.getItem('popupShown')) {
					// Small delay to ensure DOM is fully loaded
					setTimeout(function() {
						document.getElementById('boilerModal').style.display = 'block';
						sessionStorage.setItem('popupShown', 'true');
					}, 100);
				}
			});
			
			function closeModal() {
				document.getElementById('boilerModal').style.display = 'none';
			}
			
			// Close modal when clicking outside of it
			window.onclick = function(event) {
				const modal = document.getElementById('boilerModal');
				if (event.target === modal) {
					closeModal();
				}
			}
			
			document.getElementById('chatbotTrigger').addEventListener('click', function() {
				window.location.href = 'ai.php';
			});
			
			// Animation for cards
			document.addEventListener('DOMContentLoaded', function() {
				const cards = document.querySelectorAll('.boiler-card');
				const observer = new IntersectionObserver((entries) => {
					entries.forEach(entry => {
						if (entry.isIntersecting) {
							entry.target.style.opacity = 1;
							entry.target.style.transform = 'translateY(0)';
						}
					});
				}, { threshold: 0.1 });
				
				cards.forEach(card => {
					card.style.opacity = 0;
					card.style.transform = 'translateY(20px)';
					card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
					observer.observe(card);
				});
			});
		</script>
	</body>
</html>