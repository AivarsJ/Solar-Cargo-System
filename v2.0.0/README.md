# Solar Cargo System

## Projekta mērķis
Solar Cargo System ir kosmisko konteineru uzskaites sistēma ar lietotāju reģistrāciju, pieslēgšanos un lomām. Sistēmā paredzēta konteineru apskate un pievienošana.

## Izmantotās tehnoloģijas
- HTML un CSS — saskarne un noformējums.
- PHP — reģistrācija, autentifikācija un sesijas.
- MySQL/MariaDB un PDO — lietotāju datu glabāšana.

## Kā palaist projektu
1. Instalēt XAMPP vai citu vidi ar PHP un MySQL/MariaDB.
2. Ievietot projekta PHP un CSS failus mapē `htdocs/solar_cargo/`, saglabājot to nosaukumus (tostarp `database.php`, `login_handler.php`, `register_handler.php` un `container_handler.php`).
3. Palaist Apache un MySQL/MariaDB.
4. Izveidot datubāzi `solar_cargo` un tabulu `lietotaji` ar laukiem `id`, `lietotajvards`, `parole_hash`, `loma`. Laukam `loma` jābūt ar noklusējuma vērtību `user`, jo reģistrācijas kods to neievieto tieši.
5. Pielāgot datubāzes pieslēguma iestatījumus failā `database.php`. Konteineru uzskaitei nepieciešama arī tabula `containers` ar lietotnes izmantotajiem laukiem.
6. Atvērt `http://localhost/solar_cargo/index.php`.

## Testa lietotāji un lomas
- **Administrators (`admin`)** — piekļuve administratora panelim.
- **Lietotājs (`user`)** — piekļuve lietotāja darba lapai un konteineru sadaļām.

Jauniem reģistrētiem lietotājiem paredzēta `user` loma (ja datubāzes laukam `loma` iestatīta šāda noklusējuma vērtība). Konkrētu izveidoto testa kontu lietotājvārdi iesniegtajos failos nav norādīti; tie glabājas datubāzes tabulā `lietotaji`. Paroles README failā netiek publicētas.

**Piezīme:** Konteineru apskatei un pievienošanai tiek izmantoti PHP faili `containers_list.php` un `add-container.php`.


## Veiktā refaktorēšana

- **Duplicated Code:** atkārtotais PDO pieslēguma kods aizstāts ar kopīgu `getDatabaseConnection()` funkciju failā `database.php`.
- **Long Method / Long Block:** konteineru pievienošanas apstrāde sadalīta funkcijās `getContainerData()`, `validateContainer()` un `saveContainer()`.
- **Mixed Responsibilities:** formu attēlošana atdalīta no POST pieprasījumu apstrādes: `login.php` → `login_handler.php`, `db.php` → `register_handler.php`, `add-container.php` → `container_handler.php`.

**Pārbaude:** pēc izmaiņām pārbaudīta pieslēgšanās, reģistrācija un konteinera pievienošana; jaunais konteiners ir redzams sarakstā.

**Secinājums:** samazināta koda dublēšanās un savstarpēji sajaukti pienākumi. Projekta kods ir pārskatāmāks un vieglāk uzturams.
