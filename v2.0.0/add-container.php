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

// Savienojums ar datubāzi
try {
    $pdo = getDatabaseConnection();

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit("Neizdevās savienoties ar datubāzi.");
}

// Saņemam un saglabājam formas datus
$kluda = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Pārbaudām formas drošības tokenu
    if (!csrfValid()) {
        http_response_code(403);
        exit('Nederīgs formas drošības tokens.');
    }
	
    $cargo_code = trim($_POST["fcargo_code"] ?? "");
    $mineral = trim($_POST["fmineral"] ?? "");
	$mass_kg = trim($_POST["fmass_kg"] ?? "");
	$origin = trim($_POST["forigin"] ?? "");
	$destination = trim($_POST["fdestination"] ?? "");
	$status = trim($_POST["fstatus"] ?? "");
	

    if (
    $cargo_code === "" ||
    $mineral === "" ||
    $mass_kg === "" ||
    $origin === "" ||
    $destination === "" ||
    $status === ""
	) {
    $kluda = "Lūdzu, aizpildiet visus laukus!";
	} elseif (!is_numeric($mass_kg) || (float)$mass_kg <= 0) {
    $kluda = "Konteinera masai jābūt pozitīvam skaitlim!";
	} elseif (!in_array($status, ['on_way', 'at_warehouse', 'delivered', 'delayed'], true)) {
    $kluda = "Izvēlēts nederīgs konteinera statuss!";
	} else {
        try {
            $sql = "INSERT INTO containers
                    (cargo_code, mineral, mass_kg, origin, destination, status)
                    VALUES (:cargo_code, :mineral, :mass_kg, :origin, :destination, :status)";
			
			// SQL vaicājuma sagatavošana
            $stmt = $pdo->prepare($sql);
			
			// Datu ievietošana un vaicājuma izpilde
            $stmt->execute([
                ":cargo_code" => $cargo_code,
                ":mineral" => $mineral,
				":mass_kg" => $mass_kg,
				":origin" => $origin,
				":destination" => $destination,
				":status" => $status
            ]);

            // Pāradresācija novērš atkārtotu datu ievietošanu, atsvaidzinot lapu
            header("Location: add-container.php?saglabats=1");
            exit;

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $kluda = "Datus neizdevās saglabāt.";
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