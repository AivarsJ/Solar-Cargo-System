<?php
// Administratora panelis: piekļuve tikai autentificētiem administratoriem.
// Sākam sesiju, lai nolasītu pieslēgušā lietotāja datus.
session_start();
// Ja lietotājs nav pieslēdzies, novirzām uz pieslēgšanās lapu.
if (!isset($_SESSION['lietotaja_id'])) {
    header('Location: login.php');
    exit;
}
// Pārbaudām lomu; 403 nozīmē, ka piekļuve ir aizliegta.
if (($_SESSION['loma'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Piekļuve liegta: nepieciešamas administratora tiesības.');
}
// Pirms lietotājvārda ievietošanas HTML to droši apstrādājam.
$lietotajvards = htmlspecialchars((string) ($_SESSION['lietotajvards'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administratora panelis — Solar Cargo System</title>
    <!-- Visām projekta lapām kopīgais CSS noformējums. -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="site-container">
    <h1>Solar Cargo System</h1>
    <p class="user-status">Pieslēdzies: <strong><?= $lietotajvards ?></strong> (Administrators)</p>
    <!-- Administratora sadaļas un to pašreizējais izstrādes statuss. -->
    <section class="admin-panel">
        <h2>Administratora panelis</h2>
        <p>Solar Cargo System pārvaldības iespējas.</p>
        <div class="admin-actions">
            <div class="admin-card">
                <!-- Šī funkcionalitāte vēl nav ieviesta. -->
                <h3>Lietotāju pārvaldība</h3>
                <p>Lietotāju kontu un lomu administrēšana.</p>
                <span class="admin-pending">Funkcija vēl jāizveido</span>
            </div>
            <div class="admin-card">
                <!-- Saites uz konteineru saraksta un pievienošanas lapām. -->
                <h3>Konteineru pārvaldība</h3>
                <p>Konteineru saraksts un pievienošana.</p>
                <a href="containers.html">Konteineru saraksts</a>
                <a href="add-container.html">Pievienot konteineru</a>
            </div>
            <div class="admin-card">
                <!-- Iestatījumu sadaļa pašlaik ir sagatave. -->
                <h3>Sistēmas iestatījumi</h3>
                <p>Administratora iestatījumu sadaļa.</p>
                <span class="admin-pending">Funkcija vēl jāizveido</span>
            </div>
        </div>
    </section>
    <!-- Navigācija uz sākumlapu un sesijas izbeigšanu. -->
    <nav class="navigation" aria-label="Administratora navigācija">
        <a href="index.php">Galvenā lapa</a>
        <a href="logout.php">Izrakstīties</a>
    </nav>
</main>
</body>
</html>
