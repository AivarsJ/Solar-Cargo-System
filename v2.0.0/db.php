<?php
require_once __DIR__ . '/auth.php';
noCache();
$kluda = $_SESSION['register_error'] ?? '';
$veiksme = $_SESSION['register_success'] ?? '';
unset($_SESSION['register_error'], $_SESSION['register_success']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValid()) {
        $_SESSION['register_error'] = 'Forma ir novecojusi. Mēģiniet vēlreiz.';
        redirect('db.php');
    }
    $username = trim((string)($_POST['fusername'] ?? ''));
    $password = (string)($_POST['fpassword_hash'] ?? '');
    if ($username === '' || $password === '') {
        $_SESSION['register_error'] = 'Aizpildiet abus laukus.';
    } elseif (mb_strlen($username, 'UTF-8') > 100) {
        $_SESSION['register_error'] = 'Lietotājvārds ir pārāk garš.';
    } else {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=solar_cargo;charset=utf8mb4', 'root', '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
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
<!DOCTYPE html><html lang="lv"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reģistrācija — Solar Cargo System</title><link rel="stylesheet" href="style.css"></head><body>
<main class="site-container"><h1>Solar Cargo System</h1><h2>Lietotāju reģistrācija</h2>
<?php if ($kluda !== ''): ?><p class="error" role="alert"><?= e($kluda) ?></p><?php endif; ?>
<?php if ($veiksme !== ''): ?><p class="success" role="status"><?= e($veiksme) ?></p><?php endif; ?>
<form action="db.php" method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
<label for="fusername">Lietotājvārds:</label><input type="text" id="fusername" name="fusername" maxlength="100" autocomplete="off" required value="">
<label for="fpassword_hash">Parole:</label><input type="password" id="fpassword_hash" name="fpassword_hash" autocomplete="new-password" required value="">
<button type="submit">Reģistrēties</button><p><a href="login.php">Pieslēgties</a></p><p><a href="index.php">Galvenā lapa</a></p>
</form></main><script>
window.addEventListener('pageshow', function (event) {
    if (event.persisted) { window.location.reload(); return; }
    document.querySelectorAll('input[type="text"],input[type="password"]').forEach(el => el.value = '');
});
</script></body></html>
