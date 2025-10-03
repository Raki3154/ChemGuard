<?php
	session_start();
	$conn = new mysqli('localhost', 'root', 'raki3154', 'chemguard');
	if ($conn->connect_error) 
	{ 
		die("Connection failed: " . $conn->connect_error); 
	}
	function calculateSustainabilityScore($fuel, $capacity) 
	{
		$base_score = 50;
		$fuel_scores = [
			'Biomass' => 30,
			'Natural Gas' => 20,
			'Oil' => -10,
			'Coal' => -20
		];
		$capacity_modifier = ($capacity > 100) ? 10 : (($capacity > 50) ? 5 : 0);    
		return $base_score + ($fuel_scores[$fuel] ?? 0) + $capacity_modifier;
	}
	function generateWelcomeMessage($plant_name, $industry, $fuel) 
	{
		$messages = [
			'Coal' => "As a coal-powered plant, our AI will help you optimize efficiency and reduce emissions by up to 15%.",
			'Natural Gas' => "Great choice! Natural gas plants can achieve 85%+ efficiency with our AI optimization.",
			'Biomass' => "Excellent! Biomass energy is highly sustainable. We'll help you maximize your green potential.",
			'Oil' => "Our AI system will help optimize your oil consumption and reduce operational costs."
		];
		$fuel_message = $messages[$fuel] ?? "Our AI-powered platform will help optimize your operations.";
		return "Welcome $plant_name to ChemGuard! $fuel_message";
	}
	function logRegistration($plant_id, $industry, $fuel, $capacity, $conn) 
	{
		$log_sql = "INSERT INTO registration_logs (plant_id, industry_type, fuel_type, capacity, registration_timestamp) 
					VALUES (?, ?, ?, ?, NOW())";
		$stmt = $conn->prepare($log_sql);
		$stmt->bind_param("sssd", $plant_id, $industry, $fuel, $capacity);
		$stmt->execute();
		$stmt->close();
	}
	$success = $error = "";
	if(isset($_POST['register']))
	{
		$plant_name = trim($_POST['plant_name']);
		$plant_id = trim($_POST['plant_id']);
		$password = $_POST['password'];
		$country = $_POST['country'];
		$state = $_POST['state'];
		$city = $_POST['city'];
		$industry = $_POST['industry'];
		$fuel = $_POST['fuel'];
		$capacity = $_POST['capacity'];
		if(empty($plant_name) || empty($plant_id) || empty($password)) 
		{
			$error = "Please fill all required fields";
		} 
		elseif(strlen($password) < 6) 
		{
			$error = "Password must be at least 6 characters long";
		} 
		else 
		{
			$check_sql = "SELECT plant_id FROM users WHERE plant_id = ?";
			$stmt = $conn->prepare($check_sql);
			$stmt->bind_param("s", $plant_id);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows > 0) 
			{
				$error = "Plant ID already exists. Please choose a different one.";
			} 
			else 
			{
				$hashed_password = password_hash($password, PASSWORD_BCRYPT);
				$sustainability_score = calculateSustainabilityScore($fuel, $capacity);
				$sql = "INSERT INTO users (plant_name, plant_id, password, country, state, city, industry_type, fuel_type, boiler_capacity, sustainability_score, registration_date) 
						VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("ssssssssdi", $plant_name, $plant_id, $hashed_password, $country, $state, $city, $industry, $fuel, $capacity, $sustainability_score);
				if($stmt->execute()) 
				{
					logRegistration($plant_id, $industry, $fuel, $capacity, $conn);

					// Redirect to prevent form resubmission
					header("Location: register.php?success=1");
					exit();
				} 
				else 
				{
					$error = "Error: " . $stmt->error;
				}
			}
			$stmt->close();
		}
	}
	if(isset($_GET['success']) && $_GET['success'] == 1) 
	{
		$success = "Registration successful! <a href='login.php'>Login here</a>";
	}
	$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Register - ChemGuard</title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
		<link rel="stylesheet" href="styles/register.css">
	</head>
	<body>
		<div class="form-container">
			<h2>Register Your Plant</h2>
			<?php if($error): ?>
				<div class="error"><?php echo $error; ?></div>
			<?php endif; ?>
			<?php if($success): ?>
				<div class="success"><?php echo $success; ?></div>
			<?php endif; ?>
			<form method="POST" action="">
				<div class="form-group">
					<input type="text" name="plant_name" placeholder="Plant Name" value="<?php echo isset($_POST['plant_name']) ? htmlspecialchars($_POST['plant_name']) : ''; ?>" required>
				</div>                
				<div class="form-group">
					<input type="text" name="plant_id" placeholder="Plant ID" value="<?php echo isset($_POST['plant_id']) ? htmlspecialchars($_POST['plant_id']) : ''; ?>" required>
				</div>                
				<div class="form-group">
					<input type="password" name="password" placeholder="Password (min. 6 characters)" required>
				</div>
				<div class="form-group">
					<select name="country" required>
						<option value="">Select Country</option>
						<option value="India" <?php echo (isset($_POST['country']) && $_POST['country'] == 'India') ? 'selected' : ''; ?>>India</option>
					</select>
				</div>
				<div class="form-group">
					<select name="state" required>
						<option value="">Select State</option>
						<option value="Tamil Nadu" <?php echo (isset($_POST['state']) && $_POST['state'] == 'Tamil Nadu') ? 'selected' : ''; ?>>Tamil Nadu</option>
						<option value="Karnataka" <?php echo (isset($_POST['state']) && $_POST['state'] == 'Karnataka') ? 'selected' : ''; ?>>Karnataka</option>
						<option value="Andhra Pradesh" <?php echo (isset($_POST['state']) && $_POST['state'] == 'Andhra Pradesh') ? 'selected' : ''; ?>>Andhra Pradesh</option>
						<option value="Gujarat" <?php echo (isset($_POST['state']) && $_POST['state'] == 'Gujarat') ? 'selected' : ''; ?>>Gujarat</option>
						<option value="Delhi" <?php echo (isset($_POST['state']) && $_POST['state'] == 'Delhi') ? 'selected' : ''; ?>>Delhi</option>
					</select>
				</div>
				<div class="form-group">
					<select name="city" id="city" required>
						<option value="">Select City</option>
					</select>
				</div>
				<div class="form-group">
					<select name="industry" required>
						<option value="">Select Industry Type</option>
						<option value="Power Plant" <?php echo (isset($_POST['industry']) && $_POST['industry'] == 'Power Plant') ? 'selected' : ''; ?>>Power Plant</option>
						<option value="Chemical" <?php echo (isset($_POST['industry']) && $_POST['industry'] == 'Chemical') ? 'selected' : ''; ?>>Chemical</option>
						<option value="Manufacturing" <?php echo (isset($_POST['industry']) && $_POST['industry'] == 'Manufacturing') ? 'selected' : ''; ?>>Manufacturing</option>
						<option value="Textile" <?php echo (isset($_POST['industry']) && $_POST['industry'] == 'Textile') ? 'selected' : ''; ?>>Textile</option>
						<option value="Pharmaceutical" <?php echo (isset($_POST['industry']) && $_POST['industry'] == 'Pharmaceutical') ? 'selected' : ''; ?>>Pharmaceutical</option>
					</select>
				</div>
				<div class="form-group">
					<select name="fuel" required>
						<option value="">Select Fuel Type</option>
						<option value="Coal" <?php echo (isset($_POST['fuel']) && $_POST['fuel'] == 'Coal') ? 'selected' : ''; ?>>Coal</option>
						<option value="Natural Gas" <?php echo (isset($_POST['fuel']) && $_POST['fuel'] == 'Natural Gas') ? 'selected' : ''; ?>>Natural Gas</option>
						<option value="Biomass" <?php echo (isset($_POST['fuel']) && $_POST['fuel'] == 'Biomass') ? 'selected' : ''; ?>>Biomass</option>
						<option value="Oil" <?php echo (isset($_POST['fuel']) && $_POST['fuel'] == 'Oil') ? 'selected' : ''; ?>>Oil</option>
					</select>
				</div>
				<div class="form-group">
					<input type="number" name="capacity" placeholder="Boiler Capacity (in MW)" step="0.1" min="0" value="<?php echo isset($_POST['capacity']) ? htmlspecialchars($_POST['capacity']) : ''; ?>" required>
				</div>
				<button type="submit" name="register">Register Plant</button>
			</form>
			<div class="login-link">
				<p>Already registered? <a href="login.php">Login here</a></p>
			</div>
		</div>

		<script>
			const citiesByState = {
				"Tamil Nadu": ["Chennai", "Coimbatore", "Madurai", "Trichy", "Theni", "Thiruvallur", "Tuticorin"],
				"Karnataka": ["Bengaluru", "Mysuru", "Mangalore", "Hubli", "Belgaum"],
				"Andhra Pradesh": ["Vijayawada", "Visakhapatnam", "Guntur", "Nellore"],
				"Gujarat": ["Ahmedabad", "Surat", "Vadodara", "Rajkot"],
				"Delhi": ["New Delhi", "Dwarka", "Rohini"],
			};
			const stateSelect = document.querySelector("select[name='state']");
			const citySelect = document.querySelector("select[name='city']");
			const selectedCity = "<?php echo isset($_POST['city']) ? $_POST['city'] : ''; ?>";
			function loadCities(state) 
			{
				citySelect.innerHTML = '<option value="">Select City</option>';
				if (state && citiesByState[state]) 
				{
					citiesByState[state].forEach(city => 
					{
						const option = document.createElement("option");
						option.value = city;
						option.textContent = city;
						if (city === selectedCity) option.selected = true;
						citySelect.appendChild(option);
					});
				}
			}
			stateSelect.addEventListener("change", () => loadCities(stateSelect.value));
			if (stateSelect.value) 
			{
				loadCities(stateSelect.value);
			}
		</script>
	</body>
</html>