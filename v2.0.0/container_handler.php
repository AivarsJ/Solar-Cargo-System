<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/database.php';

noCache();

if (!isset($_SESSION['lietotaja_id'])) {
    redirect('login.php');
}

if (!in_array($_SESSION['loma'] ?? '', ['user', 'admin'], true)) {
    http_response_code(403);
    exit('Piekļuve liegta.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('add-container.php');
}

/** Nolasa un apgriež konteineru formas laukus. */
function getContainerData(array $post): array
{
    return [
        'cargo_code' => trim($post['fcargo_code'] ?? ''),
        'mineral' => trim($post['fmineral'] ?? ''),
        'mass_kg' => trim($post['fmass_kg'] ?? ''),
        'origin' => trim($post['forigin'] ?? ''),
        'destination' => trim($post['fdestination'] ?? ''),
        'status' => trim($post['fstatus'] ?? ''),
    ];
}

/** Atgriež kļūdas tekstu vai tukšu virkni, ja dati ir derīgi. */
function validateContainer(array $data): string
{
    foreach ($data as $value) {
        if ($value === '') {
            return 'Lūdzu, aizpildiet visus laukus!';
        }
    }

    if (!is_numeric($data['mass_kg']) || (float)$data['mass_kg'] <= 0) {
        return 'Konteinera masai jābūt pozitīvam skaitlim!';
    }

    if (!in_array($data['status'], ['on_way', 'at_warehouse', 'delivered', 'delayed'], true)) {
        return 'Izvēlēts nederīgs konteinera statuss!';
    }

    return '';
}

/** Saglabā pārbaudītus konteinera datus datubāzē. */
function saveContainer(PDO $pdo, array $data): void
{
    $sql = 'INSERT INTO containers
            (cargo_code, mineral, mass_kg, origin, destination, status)
            VALUES (:cargo_code, :mineral, :mass_kg, :origin, :destination, :status)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':cargo_code' => $data['cargo_code'],
        ':mineral' => $data['mineral'],
        ':mass_kg' => $data['mass_kg'],
        ':origin' => $data['origin'],
        ':destination' => $data['destination'],
        ':status' => $data['status'],
    ]);
}

$kluda = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValid()) {
        http_response_code(403);
        exit('Nederīgs formas drošības tokens.');
    }

    $data = getContainerData($_POST);
    $kluda = validateContainer($data);

	if ($kluda !== '') {
    $_SESSION['container_error'] = $kluda;
    redirect('add-container.php');
}

try {
    $pdo = getDatabaseConnection();
    saveContainer($pdo, $data);

    redirect('add-container.php?saglabats=1');

} catch (PDOException $e) {
    error_log($e->getMessage());
    $_SESSION['container_error'] = 'Datus neizdevās saglabāt.';
    redirect('add-container.php');
}
}
?>