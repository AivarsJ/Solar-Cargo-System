<?php
require_once __DIR__ . '/auth.php';
noCache();
// Pēc iepriekšējas autentifikācijas nepieprasām paroli atkārtoti.
if (isset($_SESSION['lietotaja_id'])) {
    redirect(($_SESSION['loma'] ?? '') === 'admin' ? 'admin.php' : 'user.php');
}
$kluda = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
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
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=solar_cargo;charset=utf8mb4', 'root', '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
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
?>
<!DOCTYPE html><html lang="lv"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pieslēgšanās — Solar Cargo System</title><link rel="stylesheet" href="style.css"></head><body>
<main class="site-container"><h1>Solar Cargo System</h1><h2>Pieslēgšanās sistēmai</h2>
<?php if ($kluda !== ''): ?><p class="error" role="alert"><?= e($kluda) ?></p><?php endif; ?>
<form action="login.php" method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
<label for="fusername">Lietotājvārds:</label><input type="text" id="fusername" name="fusername" maxlength="100" autocomplete="off" required value="">
<label for="fpassword">Parole:</label><input type="password" id="fpassword" name="fpassword" autocomplete="off" required value="">
<button type="submit">Pieslēgties</button><p>Nav konta? <a href="db.php">Reģistrēties</a></p>
<p><a href="index.php">Galvenā lapa</a></p></form></main>
<script>
// Back/Forward Cache atjaunojot lapu, pārlādējam to, lai iegūtu tukšu formu.
window.addEventListener('pageshow', function (event) {
    if (event.persisted) { window.location.reload(); return; }
    const form = document.querySelector('form');
    if (form) { form.querySelectorAll('input[type="text"],input[type="password"]').forEach(el => el.value = ''); }
});
</script></body></html>
