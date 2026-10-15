# Solar Cargo System

## Projekta mērķis
Solar Cargo System ir kosmisko konteineru uzskaites sistēma ar lietotāju reģistrāciju, pieslēgšanos un lomām. Sistēmā paredzēta konteineru apskate un pievienošana.

## Izmantotās tehnoloģijas
- HTML un CSS — saskarne un noformējums.
- PHP — reģistrācija, autentifikācija un sesijas.
- MySQL/MariaDB un PDO — lietotāju datu glabāšana.

## Kā palaist projektu
1. Instalēt XAMPP vai citu vidi ar PHP un MySQL/MariaDB.
2. Ievietot projekta failus mapē `htdocs/solar_cargo/` un pārdēvēt augšupielādētos failus uz `index.php`, `db.php`, `login.php`, `logout.php`, `admin.php`, `user.php`, `style.css`.
3. Palaist Apache un MySQL/MariaDB.
4. Izveidot datubāzi `solar_cargo` un tabulu `lietotaji` ar laukiem `id`, `lietotajvards`, `parole_hash`, `loma`. Laukam `loma` jābūt ar noklusējuma vērtību `user`, jo reģistrācijas kods to neievieto tieši.
5. Pielāgot datubāzes pieslēguma iestatījumus failos `db.php` un `login.php`.
6. Atvērt `http://localhost/solar_cargo/index.php`.

## Testa lietotāji un lomas
- **Administrators (`admin`)** — piekļuve administratora panelim.
- **Lietotājs (`user`)** — piekļuve lietotāja darba lapai un konteineru sadaļām.

Jauniem reģistrētiem lietotājiem paredzēta `user` loma (ja datubāzes laukam `loma` iestatīta šāda noklusējuma vērtība). Konkrētu izveidoto testa kontu lietotājvārdi iesniegtajos failos nav norādīti; tie glabājas datubāzes tabulā `lietotaji`. Paroles README failā netiek publicētas.

**Piezīme:** Navigācijā minētie `containers.html` un `add-container.html` faili nav pievienoti, tādēļ šīs sadaļas bez tiem nedarbosies.
