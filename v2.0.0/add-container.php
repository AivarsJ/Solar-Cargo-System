<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/database.php';
noCache();

// Pārbaudām, vai lietotājs ir pieslēdzies
if (!isset($_SESSION['lietotaja_id'])) {
    redirect('login.php');
}

// Pārbaudām, vai lietotājam ir atļauta loma
if (!in_array($_SESSION['loma'] ?? '', ['user', 'admin'], true)) {
    http_response_code(403);
    exit('Piekļuve liegta.');
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

// Datubāzes savienojums, izmantojot kopīgo funkciju.
try {
    $pdo = getDatabaseConnection();
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Neizdevās savienoties ar datubāzi.');
}

$kluda = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValid()) {
        http_response_code(403);
        exit('Nederīgs formas drošības tokens.');
    }

    $data = getContainerData($_POST);
    $kluda = validateContainer($data);

    if ($kluda === '') {
        try {
            saveContainer($pdo, $data);
            header('Location: add-container.php?saglabats=1');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $kluda = 'Datus neizdevās saglabāt.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Pievienot jaunu konteineri</title>
	<link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Pievienot jaunu konteineri</h1>

<?php if (isset($_GET["saglabats"])
          && $_GET["saglabats"] === "1"): ?>
    <p class="success">
        Paldies! Jūsu dati ir veiksmīgi saglabāti.
    </p>
<?php endif; ?>

<?php if ($kluda !== ""): ?>
    <p class="error">
        <?= htmlspecialchars($kluda, ENT_QUOTES, "UTF-8") ?>
    </p>
<?php endif; ?>

<form action="add-container.php" method="post">
<input
    type="hidden"
    name="csrf_token"
    value="<?= e(csrfToken()) ?>"
>
  <label for="fcargo_code">Kravas kods:</label><br>
  <input type="text" id="fcargo_code" name="fcargo_code" value=""><br>
  
  <label for="fmineral">Minerāls:</label><br>
  <input type="text" id="fmineral" name="fmineral" value=""><br>
  
  <label for="fmass_kg">Massa, kg:</label><br>
  <input
    type="number"
    id="fmass_kg"
    name="fmass_kg"
    min="0.01"
    max="99999999.99"
    step="0.01"
    required
><br>
  
  <label for="forigin">Izcelsmes bāze:</label><br>
  <input type="text" id="forigin" name="forigin" value=""><br>
  
  <label for="fdestination">Galamērķa bāze:</label><br>
  <input type="text" id="fdestination" name="fdestination" value=""><br>
  
  <label for="fstatus">Statuss:</label><br>
  <select id="fstatus" name="fstatus" required>
    <option value="" selected disabled>Izvēlieties statusu</option>
	<option value="on_way">Ceļā</option>
    <option value="at_warehouse">Noliktavā</option>
    <option value="delivered">Piegādāts</option>
	<option value="delayed">Kavējas</option>
  </select>
  
  <input type="submit" value="Saglabāt">
  
</form>

</body>
</html>