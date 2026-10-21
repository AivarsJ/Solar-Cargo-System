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


// Datubāzes savienojums, izmantojot kopīgo funkciju.
try {
    $pdo = getDatabaseConnection();
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Neizdevās savienoties ar datubāzi.');
}

$kluda = $_SESSION['container_error'] ?? '';
unset($_SESSION['container_error']);
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

<form action="container_handler.php" method="post">
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