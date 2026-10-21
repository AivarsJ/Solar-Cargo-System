<?php
/**
 * Lietotāju reģistrācijas lapas apstrāde.
 *
 * Apstrādā POST pieprasījumus, pārbauda CSRF tokenu un ievades datus,
 * saglabā jaunu lietotāju datubāzē un attēlo reģistrācijas formu.
 * Paziņojumi par rezultātu tiek nodoti ar sesijas mainīgajiem.
 */
/** Ielādē kopīgās sesijas, drošības un pāradresācijas funkcijas. */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/database.php';
noCache();
/** Nolasa un pēc tam notīra iepriekšējā pieprasījuma paziņojumus. */
$kluda = $_SESSION['register_error'] ?? '';
$veiksme = $_SESSION['register_success'] ?? '';
unset($_SESSION['register_error'], $_SESSION['register_success']);

?>
<!DOCTYPE html><html lang="lv"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reģistrācija — Solar Cargo System</title><link rel="stylesheet" href="style.css"></head><body>
<main class="site-container"><h1>Solar Cargo System</h1><h2>Lietotāju reģistrācija</h2>
<?php if ($kluda !== ''): ?><p class="error" role="alert"><?= e($kluda) ?></p><?php endif; ?>
<?php if ($veiksme !== ''): ?><p class="success" role="status"><?= e($veiksme) ?></p><?php endif; ?>
<form action="register_handler.php" method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
<label for="fusername">Lietotājvārds:</label><input type="text" id="fusername" name="fusername" maxlength="100" autocomplete="off" required value="">
<label for="fpassword_hash">Parole:</label><input type="password" id="fpassword_hash" name="fpassword_hash" autocomplete="new-password" required value="">
<button type="submit">Reģistrēties</button><p><a href="login.php">Pieslēgties</a></p><p><a href="index.php">Galvenā lapa</a></p>
</form></main><script>
/**
 * Notīra reģistrācijas formas teksta un paroles laukus pēc lapas parādīšanas.
 * Ja lapa atjaunota no pārlūka atpakaļ/uz priekšu kešatmiņas, pārlādē to.
 *
 * @param {PageTransitionEvent} event Lapas parādīšanas notikums.
 * @returns {void}
 */
window.addEventListener('pageshow', function (event) {
    if (event.persisted) { window.location.reload(); return; }
    document.querySelectorAll('input[type="text"],input[type="password"]').forEach(el => el.value = '');
});
</script></body></html>
