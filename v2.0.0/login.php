<?php
/*
 * SOLAR CARGO SYSTEM — LIETOTĀJU PIESLĒGŠANĀS
 *
 * Datubāze: solar_cargo
 * Tabula: lietotaji
 * Lauki: id, lietotajvards, parole_hash, loma
 *
 * Reģistrācijas laikā parole jāsaglabā ar password_hash().
 * Šeit to pārbaudām ar password_verify().
 */

// 1. Sākam PHP sesiju, lai pēc pieslēgšanās atcerētos lietotāju.
// Šai komandai jāatrodas pirms jebkādas HTML izvades.
session_start();

// 2. Mainīgie kļūdas paziņojumam un ievadītajam lietotājvārdam.
$kluda = "";
$lietotajvards = "";

// 3. Pārbaudām, vai forma iesniegta ar POST metodi.
// Parasta lapas atvēršana (GET) neveic pieslēgšanās pārbaudi.
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 4. Saņemam formas datus. ?? "" pasargā no trūkstoša lauka.
    // Lietotājvārdam noņemam liekas atstarpes; parolei tās neatņemam,
    // jo atstarpes var būt daļa no īstās paroles.
    $lietotajvards = trim($_POST["fusername"] ?? "");
    $parole = $_POST["fpassword"] ?? "";

    // 5. Neļaujam iesniegt tukšus laukus (pārbaude servera pusē).
    if ($lietotajvards === "" || $parole === "") {
        $kluda = "Lūdzu, ievadiet lietotājvārdu un paroli!";
    } else {
        try {
            // 6. Savienojamies ar MariaDB, izmantojot PDO.
            // root bez paroles der tikai tad, ja tā ir konfigurēta lokālā vidē.
            // Reālā serverī izmantojiet atsevišķu DB lietotāju ar paroli.
            $pdo = new PDO(
                "mysql:host=localhost;dbname=solar_cargo;charset=utf8mb4",
                "root",
                "",
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // 7. Meklējam lietotāju pēc lietotājvārda.
            // Sagatavotais vaicājums (:lietotajvards) novērš SQL injekciju.
            $stmt = $pdo->prepare(
                "SELECT id, lietotajvards, parole_hash, loma
                 FROM lietotaji
                 WHERE lietotajvards = :lietotajvards
                 LIMIT 1"
            );
            $stmt->execute([":lietotajvards" => $lietotajvards]);

            // 8. Iegūstam vienu ierakstu vai false, ja lietotāja nav.
            $lietotajs = $stmt->fetch(PDO::FETCH_ASSOC);

            // 9. Salīdzinām ievadīto paroli ar datubāzē glabāto hash.
            // Nekad nesalīdzinām paroli ar hash kā divas parastas virknes.
            if ($lietotajs && password_verify($parole, $lietotajs["parole_hash"])) {

                // 10. Pēc sekmīgas pieslēgšanās nomainām sesijas ID,
                // lai samazinātu sesijas fiksācijas uzbrukuma risku.
                session_regenerate_id(true);

                // 11. Sesijā glabājam tikai pieslēgšanās vajadzībām
                // nepieciešamos datus — nevis paroli vai paroles hash.
                $_SESSION["lietotaja_id"] = (int) $lietotajs["id"];
                $_SESSION["lietotajvards"] = $lietotajs["lietotajvards"];
                $_SESSION["loma"] = $lietotajs["loma"];

                // 12. Novirzām lietotāju atbilstoši datubāzē esošajai lomai.
                // admin.php lapā papildus JĀPĀRBAUDA admin sesijas loma.
                if ($lietotajs["loma"] === "admin") {
                    header("Location: admin.php");
                } else {
                    header("Location: user.php");
                }
                exit; // Apturam koda izpildi pēc novirzīšanas.
            }

            // 13. Viens kopīgs paziņojums nepareizam lietotājvārdam
            // un parolei — neatklājam, kuri konti sistēmā eksistē.
            $kluda = "Nepareizs lietotājvārds vai parole!";

        } catch (PDOException $e) {
            // 14. Tehnisko kļūdu pierakstām servera žurnālā,
            // bet apmeklētājam nerādām datubāzes savienojuma detaļas.
            error_log($e->getMessage());
            $kluda = "Neizdevās pieslēgties sistēmai. Mēģiniet vēlāk.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pieslēgšanās — Solar Cargo System</title>
    <!-- Izmantojam projekta kopējo noformējumu. -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Solar Cargo System</h1>
    <p>Kosmisko konteineru uzskaites sistēma.</p>
    <h2>Pieslēgšanās sistēmai</h2>

    <!-- 15. Ja PHP ir uzstādījis kļūdas paziņojumu, parādām to. -->
    <?php if ($kluda !== ""): ?>
        <p role="alert" style="color: darkred; text-align: center;">
            <!-- htmlspecialchars pasargā HTML no ievadīta koda izpildes. -->
            <?= htmlspecialchars($kluda, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <!-- 16. POST sūta datus uz šo pašu login.php failu. -->
    <form action="login.php" method="post">
        <label for="fusername">Lietotājvārds:</label>
        <input type="text" id="fusername" name="fusername"
               maxlength="100" autocomplete="username" required
               value="<?= htmlspecialchars($lietotajvards, ENT_QUOTES, 'UTF-8') ?>">

        <label for="fpassword">Parole:</label>
        <!-- type=password paslēpj ievadītās rakstzīmes ekrānā. -->
        <input type="password" id="fpassword" name="fpassword"
               autocomplete="current-password" required>

        <button type="submit">Pieslēgties</button>
		
        <p>Nav konta? <a href="db.php">Reģistrēties</a></p>
        <p><a href="index.php">Atpakaļ uz sākumlapu</a></p>
    </form>
</body>
</html>
