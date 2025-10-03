<?php
	session_start();
	unset($_SESSION['plant_name']);
	unset($_SESSION['password']);
	$_SESSION['logged_out'] = true;
	header('Location: index.php');
	exit();
?>