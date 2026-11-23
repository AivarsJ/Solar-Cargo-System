<?php
require_once __DIR__ . '/auth.php';
noCache();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Solar Cargo System - Contacts</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Solar Cargo</h1>
  <p>The Spaceship Cargo Company</p>
  <h2>Contacts</h2>
  <figure>
	<img src="pielikumi/yoda.png" 
		alt="The General Director" 
		width="400">
	<figcaption>The General Director</figcaption>
  </figure>
  
  <h2>Navigācija</h2>
    <nav class="navigation" aria-label="Galvenā navigācija">
        <a href="index.php">Galvenā lapa</a>
        <a href="about.php">Par mums</a>
        <a href="db.php">Reģistrēties</a>
        <a href="login.php">Pieslēgties</a>
    </nav>
</body>
</html>