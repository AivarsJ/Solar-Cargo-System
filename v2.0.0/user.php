<?php
require_once __DIR__ . '/auth.php';
noCache();
if (!isset($_SESSION['lietotaja_id'])) redirect('login.php');
if (($_SESSION['loma'] ?? '') !== 'user') { http_response_code(403); exit('Piekļuve liegta.'); }
$lietotajvards = e((string)($_SESSION['lietotajvards'] ?? ''));
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
        <form class="logout-inline" action="logout.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><button type="submit">Izrakstīties</button></form>
    </nav>
</main>
<script>window.addEventListener('pageshow', function(event) { if (event.persisted) window.location.reload(); });</script></body>
</html>
