<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/database.php';

/**
 * Apstrādā POST pieprasījumu: validē CSRF un ievades datus,
 * pārbauda paroles atbilstību un izveido autentificētu sesiju.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValid()) {
        $_SESSION['login_error'] = 'Forma ir novecojusi. Lūdzu, mēģiniet vēlreiz.';
        redirect('login.php');
    }
    $username = trim((string)($_POST['fusername'] ?? ''));
    $password = (string)($_POST['fpassword'] ?? '');
    if ($username === '' || $password === '') {
        $_SESSION['login_error'] = 'Aizpildiet abus laukus.';
    } else {
        /**
         * Iegūst lietotāja ierakstu ar sagatavotu SQL vaicājumu un pārbauda paroli.
         * Veiksmīgas autentifikācijas gadījumā atjauno sesijas identifikatoru
         * un CSRF tokenu, pēc tam novirza lietotāju pēc lomas.
         */
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->prepare('SELECT id, lietotajvards, parole_hash, loma FROM lietotaji WHERE lietotajvards = :v LIMIT 1');
            $stmt->execute([':v' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['parole_hash']) && in_array($user['loma'], ['admin','user'], true)) {
                session_regenerate_id(true);
                $_SESSION['lietotaja_id'] = (int)$user['id'];
                $_SESSION['lietotajvards'] = $user['lietotajvards'];
                $_SESSION['loma'] = $user['loma'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                redirect($user['loma'] === 'admin' ? 'admin.php' : 'user.php');
            }
            $_SESSION['login_error'] = 'Nepareizs lietotājvārds vai parole.';
        } catch (PDOException $ex) {
            error_log($ex->getMessage());
            $_SESSION['login_error'] = 'Pieslēgšanās šobrīd nav pieejama.';
        }
    }
    // POST/Redirect/GET: pārlūka vēsturē neatstājam POST formas rezultātu.
    redirect('login.php');
} 