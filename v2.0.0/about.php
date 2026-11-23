<?php
require_once __DIR__ . '/auth.php';
noCache();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Solar Cargo System - About</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Solar Cargo</h1>
  <p>The Spaceship Cargo Company</p>
  <h2>Our buisnes goal</h2>
  <p>We provide cargo movement around whole gallactic of the MilkyWay</p>
  <p>The Solar Cargo provides full packige of logistic service</p>
  <figure>
	<img src="pielikumi/starship.png" 
		alt="Our StarShip fleet" 
		width="400">
	<figcaption>Our StarShip fleet</figcaption>
  </figure>
  
  <h2>Navigācija</h2>
    <nav class="navigation" aria-label="Galvenā navigācija">
        <a href="index.php">Galvenā lapa</a>
        <a href="contacts.php">Kontakti</a>
        <a href="db.php">Reģistrēties</a>
        <a href="login.php">Pieslēgties</a>
    </nav>
</body>
</html>