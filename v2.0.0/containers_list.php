<?php
require_once __DIR__ . '/auth.php';
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
    $pdo = new PDO(
        "mysql:host=localhost;dbname=solar_cargo;charset=utf8mb4",
        "root",
        ""
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit("Neizdevās savienoties ar datubāzi.");
}

// Ielikt sagatavotos vaicājumus 'statement'

// SELECT - visi saglabātie ieraksti
$sql = "SELECT id, cargo_code, mineral, mass_kg, origin, destination, status, created_at
        FROM containers
        ORDER BY id ASC";

$stmt = $pdo->query($sql);
$ieraksti = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>List of containers</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Konteineru saraksts</h1>

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
</body>
</html>