<?php
// Kopējā sesiju konfigurācija. Iekļaut pirms HTML izvades.
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true, 'samesite' => 'Lax'
    ]);
    session_start();
}
/**
 * Atspējo lapas kešošanu, nosūtot atbilstošas HTTP galvenes.
 *
 * @return void
 */
function noCache(): void {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}
/**
 * Atgriež sesijas CSRF tokenu, nepieciešamības gadījumā izveidojot jaunu.
 *
 * @return string CSRF tokens heksadecimālā formātā.
 * @throws \Random\RandomException Ja neizdodas ģenerēt drošus nejaušus baitus.
 */
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
/**
 * Pārbauda, vai POST pieprasījumā iesniegtais CSRF tokens atbilst sesijas tokenam.
 *
 * @return bool True, ja tokens ir derīgs; pretējā gadījumā false.
 */
function csrfValid(): bool {
    return isset($_POST['csrf_token'], $_SESSION['csrf_token']) &&
        is_string($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}
/**
 * Kodē speciālās HTML rakstzīmes drošai teksta attēlošanai HTML saturā.
 *
 * @param string $value Kodējamais teksts.
 * @return string HTML drošā formā kodēts teksts.
 */
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
/**
 * Pāradresē uz norādīto URL ar HTTP 303 statusu un pārtrauc skripta izpildi.
 *
 * @param string $url Pāradresācijas mērķa URL.
 * @return void
 */
function redirect(string $url): void { header('Location: ' . $url, true, 303); exit; }
