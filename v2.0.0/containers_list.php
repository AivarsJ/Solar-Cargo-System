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

$rezims = $_GET['mode'] ?? 'search';
$atlasits = isset($_GET['search']);

$kluda = '';

if ($rezims === 'all') {
	$sql = "SELECT id, cargo_code, mineral, mass_kg, origin, destination, status, created_at
			FROM containers
			ORDER BY id ASC";
	$stmt = $pdo->query($sql);
	$ieraksti = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
elseif ($rezims === 'search' && $atlasits) {
	$statuss = $_GET['fstatus'] ?? '';
	$kravas_kods = trim($_GET['fcargo_code'] ?? '');
	$nosacijumi = [];
	$parametri = [];
	
	if ($statuss !== '') {
		$nosacijumi[] = 'status = ?';
		$parametri [] = $statuss;
	}
	
	if ($kravas_kods !== '') {
		$nosacijumi[] = 'cargo_code = ?';
		$parametri [] = $kravas_kods;
	}
	
	$sql = "SELECT id, cargo_code, mineral, mass_kg, origin, destination, status, created_at
			FROM containers";
    if (!empty($nosacijumi)) {
		$sql .= " WHERE " . implode(" AND ", $nosacijumi);
	}
	$sql .= " ORDER BY id ASC";			
	
	if (empty($nosacijumi)) {
		$kluda = "Lūdzu norādiet atlases kritērijus.";
	}
	else {
	$stmt = $pdo->prepare($sql);
	$stmt->execute($parametri);
	$ieraksti = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
?>


<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>List of containers</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<nav class="navigation">
		<a href="user.php">Lietotāja lapa</a>
	</nav>
	
    <h1>Konteineru saraksts</h1>

<?php if ($rezims === 'search'): ?>
	<form action="containers_list.php" method="get">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="search" value="1">
		
		<h1>Atlasīt konteinerus</h1>
	
		<label for="fcargo_code">Kravas kods:</label><br>
		<input type="text" id="fcargo_code" name="fcargo_code" value=""><br>
	
		<label for="fstatus">Statuss:</label><br>
		<select id="fstatus" name="fstatus">
			<option value="" selected>Izvēlieties statusu</option>
			<option value="on_way">Ceļā</option>
			<option value="at_warehouse">Noliktavā</option>
			<option value="delivered">Piegādāts</option>
			<option value="delayed">Kavējas</option>
		</select>
	
		<input type="submit" value="Atlasīt">
	
	</form>
<?php endif; ?>

<?php if ($kluda !== ''): ?>
    <p><?= htmlspecialchars($kluda, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (($rezims === 'all' || ($rezims === 'search' && $atlasits)) && $kluda === ''): ?>
	<?php if (empty($ieraksti)): ?>
		<p>Neviens konteiners neatbilst atlases kritērijiem.</p>
	<?php else: ?>
	
	<table>
    <thead>
        <tr>
            <th>ID</th>
			<th>Kravas kods</th>
			<th>Minerāls</th>
			<th>Massa, kg</th>
			<th>Izcelsmes bāze</th>
			<th>Galamērķa bāze</th>
			<th>Statuss</th>
			<th>Izveidots</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($ieraksti as $ieraksts): ?>
            <tr>
                <td><?= (int)$ieraksts["id"] ?></td>

                <td>
                    <?= htmlspecialchars(
                        $ieraksts["cargo_code"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $ieraksts["mineral"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </td>
				
				<td>
                    <?= htmlspecialchars(
                        $ieraksts["mass_kg"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </td>
				
				<td>
                    <?= htmlspecialchars(
                        $ieraksts["origin"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </td>
				
				<td>
                    <?= htmlspecialchars(
                        $ieraksts["destination"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </td>
				
				<td>
					<?php
					// Tehnisko statusu nosaukumi latviešu valodā
					$statusi = [
					'on_way' => 'Ceļā',
					'at_warehouse' => 'Noliktavā',
					'delivered' => 'Piegādāts',
					'delayed' => 'Kavējas'
					];

					// Nosakām lietotājam redzamo statusu
					$statuss = $statusi[$ieraksts["status"]]
					?? $ieraksts["status"];

					// Droši attēlojam statusu HTML tabulā
					echo htmlspecialchars(
					$statuss,
					ENT_QUOTES,
					"UTF-8"
					);
					?>
				</td>
				
				<td>
                    <?= htmlspecialchars(
                        $ieraksts["created_at"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </td>
				
            </tr>
        <?php endforeach; ?>
    </tbody>
	</table>
	<?php endif; ?>
<?php endif; ?>

</body>
</html>