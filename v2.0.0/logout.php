<?php
require_once __DIR__ . '/auth.php';
noCache();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValid()) { http_response_code(403); exit('Nederīgs izrakstīšanās pieprasījums.'); }
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'=>time()-3600, 'path'=>$params['path'], 'domain'=>$params['domain'],
            'secure'=>$params['secure'], 'httponly'=>$params['httponly'],
            'samesite'=>$params['samesite'] ?? 'Lax'
        ]);
    }
    session_destroy();
    redirect('index.php');
}
?>
<!DOCTYPE html><html lang="lv"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Izrakstīšanās — Solar Cargo System</title><link rel="stylesheet" href="style.css"></head><body>
<main class="site-container"><h1>Solar Cargo System</h1><h2>Izrakstīšanās</h2>
<p>Vai vēlaties izrakstīties no sistēmas?</p>
<form action="logout.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
<button type="submit">Apstiprināt izrakstīšanos</button><p><a href="index.php">Atpakaļ uz sākumlapu</a></p></form>
</main></body></html>
