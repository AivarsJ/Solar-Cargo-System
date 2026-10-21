<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/database.php';

/** Apstrādā reģistrācijas formas iesniegšanu. */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValid()) {
        $_SESSION['register_error'] = 'Forma ir novecojusi. Mēģiniet vēlreiz.';
        redirect('db.php');
    }
    /** Nolasa lietotājvārdu un paroli no formas. */
    $username = trim((string)($_POST['fusername'] ?? ''));
    $password = (string)($_POST['fpassword_hash'] ?? '');
    if ($username === '' || $password === '') {
        $_SESSION['register_error'] = 'Aizpildiet abus laukus.';
    } elseif (mb_strlen($username, 'UTF-8') > 100) {
        $_SESSION['register_error'] = 'Lietotājvārds ir pārāk garš.';
    } else {
        /** Izveido lietotāju ar noklusēto 'user' lomu un droši jauktu paroli. */
        try {
            $pdo = getDatabaseConnection();
            // Lomu nekad nesaņemam no formas. Reģistrācijai vienmēr piešķiram user.
            $stmt = $pdo->prepare('INSERT INTO lietotaji (lietotajvards, parole_hash, loma) VALUES (:v, :p, :l)');
            $stmt->execute([':v'=>$username, ':p'=>password_hash($password, PASSWORD_DEFAULT), ':l'=>'user']);
            $_SESSION['register_success'] = 'Reģistrācija veiksmīga. Tagad varat pieslēgties.';
        } catch (PDOException $ex) {
            error_log($ex->getMessage());
            $_SESSION['register_error'] = 'Reģistrācija neizdevās. Pārbaudiet datus vai mēģiniet citu lietotājvārdu.';
        }
    }
    redirect('db.php');
}
?>