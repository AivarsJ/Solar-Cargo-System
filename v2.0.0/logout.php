<?php
// Sākam sesiju, lai piekļūtu pašreizējā lietotāja pieslēgšanās datiem.
session_start();

// Iztukšojam visus sesijas mainīgos (piemēram, lietotāja ID un lomu).
$_SESSION = [];

// Ja sesijas identifikators tiek glabāts pārlūkprogrammas sīkdatnē,
// dzēšam to, nosūtot sīkdatni ar pagātnes derīguma termiņu.
if (ini_get('session.use_cookies')) {
    // Iegūstam esošās sesijas sīkdatnes iestatījumus,
    // lai dzēšanai izmantotu to pašu ceļu, domēnu un drošības parametrus.
    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 42000, // Derīguma termiņš pagātnē.
        'path' => $params['path'], // Sīkdatnes ceļš.
        'domain' => $params['domain'], // Sīkdatnes domēns.
        'secure' => $params['secure'], // Vai sīkdatne paredzēta tikai HTTPS.
        'httponly' => $params['httponly'], // Ierobežo piekļuvi no JavaScript.
        'samesite' => $params['samesite'] ?? 'Lax' // Starpvietņu pieprasījumu politika.
    ]);
}

// Iznīcinām sesiju servera pusē, pabeidzot lietotāja izrakstīšanos.
session_destroy();

// Pēc izrakstīšanās pāradresējam uz publisko sākumlapu.
header('Location: index.php');
exit; // Apturam skripta izpildi pēc pāradresācijas.
