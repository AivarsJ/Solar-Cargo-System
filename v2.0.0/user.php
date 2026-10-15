<?php
// Šī lapa ir pieejama tikai lietotājiem, kuri ir pieslēgušies un kuriem ir loma "user".

// Sākam vai turpinām PHP sesiju, lai varētu nolasīt pieslēgšanās datus.
session_start();

// Ja sesijā nav lietotāja ID, lietotājs nav autentificēts.
// Novirzām viņu uz pieslēgšanās lapu un pārtraucam koda izpildi.
if (!isset($_SESSION['lietotaja_id'])) {
    header('Location: login.php');
    exit;
}

// Pārbaudām lietotāja lomu. Administrators un citas lomas šo lapu nevar atvērt.
// ?? '' nozīmē: ja sesijā lomas nav, izmantojam tukšu tekstu.
if (($_SESSION['loma'] ?? '') !== 'user') {
    header('Location: index.php');
    exit;
}

// Iegūstam lietotājvārdu no sesijas un droši sagatavojam to attēlošanai HTML.
// htmlspecialchars() neļauj lietotājvārdā ievadītam HTML tikt interpretētam kā kodam.
$lietotajvards = htmlspecialchars((string) ($_SESSION['lietotajvards'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <!-- Norādām UTF-8 kodējumu, lai pareizi attēlotu latviešu burtus. -->
    <meta charset="UTF-8">
    <!-- Pielāgojam lapu dažādu izmēru ekrāniem. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lietotāja lapa — Solar Cargo System</title>
    <!-- Pievienojam projekta kopējo CSS noformējumu. -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<!-- Galvenais lapas saturs, ko ierobežo site-container CSS klase. -->
<main class="site-container">
    <h1>Solar Cargo System</h1>
    <h2>Lietotāja darba lapa</h2>
    <!-- PHP īsā izvades forma parāda droši apstrādāto lietotājvārdu. -->
    <p class="user-status">Pieslēdzies: <strong><?= $lietotajvards ?></strong> (Lietotājs)</p>
    <p>Kosmisko konteineru uzskaites sistēma.</p>
    <!-- Navigācija uz konteineru lapām, sākumlapu un izrakstīšanos. -->
    <nav class="navigation" aria-label="Lietotāja navigācija">
        <a href="containers.html">Apskatīt konteineru sarakstu</a>
        <a href="add-container.html">Pievienot jaunu konteineru</a>
        <a href="index.php">Galvenā lapa</a>
        <!-- logout.php izbeidz sesiju un atgriež lietotāju sākumlapā. -->
        <a href="logout.php">Izrakstīties</a>
    </nav>
</main>
</body>
</html>
