 <?php
// Izveidojam savienojumu ar MySQL datubāzi, izmantojot PDO.
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=solar_cargo;charset=utf8mb4",
        "root",
        ""
    );

    // Kļūdu gadījumā PDO izmet izņēmumu, ko var apstrādāt.
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {
    exit("Neizdevās savienoties ar datubāzi.");
}

// Mainīgais reģistrācijas kļūdas paziņojumam.
$kluda = "";

// Reģistrāciju apstrādājam tikai pēc formas nosūtīšanas ar POST.
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Saņemam ievadītos datus un noņemam liekās atstarpes lietotājvārdam.
    $lietotajvards = trim($_POST["fusername"] ?? "");
    $parole = $_POST["fpassword_hash"] ?? "";
    // Jaunam lietotājam paredzēta parastā lietotāja loma.
    // Šajā INSERT vaicājumā loma netiek saglabāta; to nosaka datubāzes noklusējums.
    $loma = "user";

    // Pārbaudām, vai abi obligātie lauki ir aizpildīti.
    if ($lietotajvards === "" || $parole === "") {
        $kluda = "Lūdzu, aizpildiet visus laukus!";
    } elseif (!in_array($loma, ["admin", "user"], true)) {
        $kluda = "Nederīga lietotāja loma!";
    } else {
        try {
            // Paroli glabājam kā drošu jaucējvērtību, nevis atklātu tekstu.
            $parole_hash = password_hash(
                $parole,
                PASSWORD_DEFAULT
            );

            // Sagatavojam SQL vaicājumu jaunā konta saglabāšanai.
            $sql = "INSERT INTO lietotaji
                    (lietotajvards, parole_hash)
                    VALUES
                    (:lietotajvards, :parole_hash)";

            // Parametrizēts vaicājums palīdz novērst SQL injekcijas.
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":lietotajvards" => $lietotajvards,
                ":parole_hash" => $parole_hash
            ]);

            // Pēc veiksmīgas reģistrācijas atgriežamies ar veiksmes atzīmi.
            header("Location: db.php?saglabats=1");
            exit;

        } catch (PDOException $e) {
            // Tehnisko kļūdu reģistrējam servera žurnālā, nevis rādām lietotājam.
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
    <title>Solar Cargo System</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Solar Cargo System</h1>
    <p>Kosmisko konteineru uzskaites sistēma.</p>

    <h2>Lietotāju reģistrācijas forma</h2>
    
<?php if (isset($_GET["saglabats"])
          && $_GET["saglabats"] === "1"): ?>
    <p class="success">
        Paldies! Jūsu reģistrācija ir veiksmīga.
    </p>
<?php endif; ?>

<!-- Kļūdas tekstu droši izvadām HTML, izmantojot htmlspecialchars. -->
<?php if ($kluda !== ""): ?>
    <p class="error">
        <?= htmlspecialchars($kluda, ENT_QUOTES, "UTF-8") ?>
    </p>
<?php endif; ?>

<!-- Forma nosūta lietotājvārdu un paroli uz db.php ar POST metodi. -->
<form action="db.php" method="post">

    <!-- Obligāts lietotājvārda ievades lauks. -->
    <label for="fusername">Lietotajvards:</label>
    <input
        type="text"
        id="fusername"
        name="fusername"
        maxlength="100"
        required
    >

    <!-- Paroles lauks paslēpj ievadītās rakstzīmes pārlūkā. -->
    <label for="fpassword_hash">Parole:</label>
    <input
        type="password"
        id="fpassword_hash"
        name="fpassword_hash"
        maxlength="255"
        required
    >

	    
	<button type="submit">Saglabāt</button>

</form>
	
</body>
</html>