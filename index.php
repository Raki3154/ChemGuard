<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>ChemGuard | AI-Powered Industrial Sustainability</title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
		<link rel="stylesheet" href="styles/index.css">
	</head>
	<body>
		<header>
			<div class="brand">
				<i class="fas fa-industry"></i>
				ChemGuard
			</div>
			<div class="nav-links">
				<a href="about.php">About</a>
				<a href="login.php">Login</a>
			</div>
		</header>
		<section class="hero">
			<h1>AI-Powered Industrial Sustainability</h1>
			<p>ChemGuard leverages generative AI and digital twin technology to optimize pulverized coal water tube boilers, reducing fuel consumption, cutting carbon emissions, and driving the transition to sustainable industrial operations.</p>
			<div>
				<a href="register.php" class="btn">Create Account</a>
				<a href="login.php" class="btn btn-outline" style="margin-left:8px;">Sign In</a>
			</div>
		</section>
		<div class="chatbot-trigger" id="chatbotTrigger">
			<i class="fas fa-robot"></i>
		</div>
		<footer>
			<p>&copy; <?php echo date("Y"); ?> ChemGuard | AI for Industrial Sustainability</p>
		</footer>
		<script>
			document.getElementById('chatbotTrigger').addEventListener('click', function() 
			{
				window.location.href = 'ai.php';
			});
			document.addEventListener('DOMContentLoaded', function() 
			{
				const cards = document.querySelectorAll('.card, .feature-card');
				const observer = new IntersectionObserver((entries) => 
				{
					entries.forEach(entry => 
					{
						if (entry.isIntersecting) 
						{
							entry.target.style.opacity = 1;
							entry.target.style.transform = 'translateY(0)';
						}
					});
				}, 
				{ threshold: 0.1 });
				cards.forEach(card => 
				{
					card.style.opacity = 0;
					card.style.transform = 'translateY(20px)';
					card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
					observer.observe(card);
				});
			});
		</script>
	</body>
</html>