<?php
	session_start();
	$conn = new mysqli('localhost', 'root', 'raki3154', 'chemguard');
	if ($conn->connect_error) 
	{
		die("Connection failed: " . $conn->connect_error);
	}
	$error = "";
	if (isset($_POST['login'])) 
	{
		$plant_name = trim($_POST['plant_name']);
		$password = $_POST['password'];
		if (empty($plant_name) || empty($password)) 
		{
			$error = "Please enter both plant name and password";
		} 
		else 
		{
			$sql = "SELECT * FROM users WHERE plant_name = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("s", $plant_name);
			$stmt->execute();
			$result = $stmt->get_result();
			if ($result->num_rows === 1) 
			{
				$row = $result->fetch_assoc();
				if (password_verify($password, $row['password'])) 
				{
					$_SESSION['plant_name'] = $row['plant_name'];
					$_SESSION['plant_id'] = $row['plant_id'];
					header("Location: home.php");
					exit();
				} 
				else 
				{
					$error = "Incorrect password!";
				}
			} 
			else 
			{
				$error = "Plant not found!";
			}
			$stmt->close();
		}
	}
	$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Login - ChemGuard</title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
		<link rel="stylesheet" href="styles/login.css">
	</head>
	<body>
		<div class="form-container">
			<h2>Login to ChemGuard</h2>
			<?php if(!empty($error)) : ?>
				<div class="error-message" style="color: red; margin-bottom: 10px;">
					<?php echo $error; ?>
				</div>
			<?php endif; ?>
			<form method="POST" action="">
				<div class="form-group">
					<input type="text" name="plant_name" placeholder="Plant Name" value="<?php echo isset($_POST['plant_name']) ? htmlspecialchars($_POST['plant_name']) : ''; ?>" required>
				</div>
				<div class="form-group">
					<input type="password" name="password" placeholder="Password" required>
				</div>
				<button type="submit" name="login">
					<i class="fas fa-sign-in-alt"></i> Login to Dashboard
				</button>
			</form>
			<div class="login-link">
				<p>New Plant? <a href="register.php">Register here</a></p>
			</div>
			<div class="ai-feature">
				<p><i class="fas fa-robot"></i> <strong>AI-Powered Insights:</strong> Get personalized sustainability recommendations after login</p>
			</div>
		</div>
	</body>
</html>