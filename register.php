<?php
$conn = new mysqli('localhost', 'root', 'raki3154', 'chemguard');
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

if(isset($_POST['register'])){
    $plant_name = $_POST['plant_name'];
    $plant_id = $_POST['plant_id'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $country = $_POST['country'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $industry = $_POST['industry'];

    $sql = "INSERT INTO users (plant_name, plant_id, password, country, state, city, industry_type)
            VALUES ('$plant_name','$plant_id','$password','$country','$state','$city','$industry')";
    if($conn->query($sql) === TRUE){
        $success = "Registered successfully! <a href='login.php'>Login here</a>";
    } else { $error = "Error: " . $conn->error; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - ChemGuard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="hero">
    <div class="form-container">
        <h2>Register Your Plant</h2>
        <?php if(isset($error)){ echo "<p class='error'>$error</p>"; } ?>
        <?php if(isset($success)){ echo "<p class='success'>$success</p>"; } ?>
        <form method="POST" action="">
            <input type="text" name="plant_name" placeholder="Plant Name" required><br>
            <input type="text" name="plant_id" placeholder="Plant ID" required><br>
            <input type="password" name="password" placeholder="Password" required><br>

            <select name="country" required>
                <option value="">Select Country</option>
                <option>India</option>
            </select>
            <select name="state" required>
                <option value="">Select State</option>
                <option>Tamil Nadu</option>
                <option>Kerala</option>
				<option>Karnataka</option>
				<option>Andhra Pradesh</option>
				<option>Telangana</option>
				<option>Maharastra</option>
				<option>Gujarat</option>
				<option>Delhi</option>
				<option>Haryana</option>
            </select>
            <select name="city" required>
                <option value="">Select City</option>
                <option>Chennai</option>
                <option>Coimbatore</option>
				<option>Tuticorin</option>
				<option>Madurai</option>
				<option>Theni</option>
				<option>Trichy</option>
				<option>Thiruvallur</option>
            </select>
            <select name="industry" required>
                <option value="">Select Industry</option>
                <option>Power Plant</option>
                <option>Chemical</option>
            </select>
            <br>
            <button type="submit" name="register">Register</button>
        </form>
        <p>Already registered? <a href="login.php">Login here</a></p>
    </div>
</div>
</body>
</html>
