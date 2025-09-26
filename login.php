<?php
session_start();
$conn = new mysqli('localhost', 'root', 'raki3154', 'chemguard');
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

if(isset($_POST['login'])){
    $plant_name = $_POST['plant_name'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE plant_name='$plant_name'";
    $result = $conn->query($sql);

    if($result->num_rows == 1){
        $row = $result->fetch_assoc();
        if(password_verify($password, $row['password'])){
            $_SESSION['plant_name'] = $row['plant_name'];
            $_SESSION['plant_id'] = $row['plant_id'];
            $_SESSION['location'] = $row['country'].", ".$row['state'].", ".$row['city'];
            $_SESSION['industry'] = $row['industry_type'];
            header("Location: home.php");
        } else { $error = "Incorrect password!"; }
    } else { $error = "Plant not found!"; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - ChemGuard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="hero">
    <div class="form-container">
        <h2>Login to ChemGuard</h2>
        <?php if(isset($error)){ echo "<p class='error'>$error</p>"; } ?>
        <form method="POST" action="">
            <input type="text" name="plant_name" placeholder="Plant Name" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" name="login">Login</button>
        </form>
        <p>New Plant? <a href="register.php">Register here</a></p>
    </div>
</div>
</body>
</html>
