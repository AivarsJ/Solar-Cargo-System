# Solar Cargo System

**Solar Cargo System** ir mācību tīmekļa lietotne kosmisko kravu konteineru uzskaitei. Tā veidota ar PHP, HTML, CSS un MySQL/MariaDB.

## 1. Projekta mērķis

Projekta mērķis ir izveidot vienkāršu konteineru uzskaites sistēmu, kurā lietotāji var reģistrēties, pieslēgties un atbilstoši piešķirtajai lomai apskatīt vai 
pievienot konteineru ierakstus.

Pašlaik kodā ir realizēts:

- lietotāju reģistrācijas un pieslēgšanās process;
- divas autentifikācijas lomas: `user` un `admin`;
- konteineru pievienošana datubāzei un to saraksta apskate;
- konteineru datu pārbaude pirms saglabāšanas;
- sesiju, CSRF tokenu un piekļuves kontroles pamata mehānismi;
- administratora panelis ar saitēm uz konteineru darbībām.

Sākumlapā minēti arī dispečers, noliktavas operators un vadītājs kā sistēmas lietotāju veidi, taču šīm lomām vēl nav izstrādāts darbu un piekļuves sadalījums.

## 2. Nepieciešamās atkarības un vide

### Tehnoloģijas

| Komponente 										| Nozīme 														  |
| ------------------------------------------------- | --------------------------------------------------------------- |
| PHP 												| Servera puses loģika, autentifikācija, sesijas un datu apstrāde |
| PHP PDO un `pdo_mysql` 							| Savienojums ar MySQL/MariaDB 									  |
| PHP `mbstring` 									| Lietotājvārda garuma pārbaude ar `mb_strlen()` 				  |
| MySQL vai MariaDB 								| Lietotāju un konteineru datu glabāšana 						  |
| Apache vai cits PHP atbalstošs tīmekļa serveris 	| PHP lapu izpilde 												  |
| HTML un CSS 										| Lietotāja saskarne 											  |
| Tīmekļa pārlūkprogramma 							| Lietotnes izmantošana 										  |

**Ieteicamā lokālā vide:** XAMPP ar Apache, PHP un MySQL/MariaDB. Precīzas pārbaudītās PHP, MySQL/MariaDB un XAMPP versijas iesniegtajos materiālos nav norādītas; 
tās jāfiksē pēc testēšanas. Kodam vajadzīga PHP versija, kas atbalsta izmantotās funkcijas un sintaksi (praktiskai pārbaudei ieteicams PHP 8.x).

Projektā nav konstatēta `composer.json` vai ārējo PHP pakotņu instalēšanas prasība. Nepieciešamie PHP paplašinājumi jāiespējo servera konfigurācijā.

### Datubāzes konfigurācija

Fails `database.php` nolasa šādus **servera vides mainīgos**:

| Mainīgais 	| Noklusējuma vērtība | Paskaidrojums 								|
| ------------- | ------------------- | ------------------------------------------- |
| `DB_HOST` 	| `localhost` 		  | Datubāzes servera adrese 					|
| `DB_NAME` 	| `solar_cargo` 	  | Datubāzes nosaukums 						|
| `DB_USER` 	| `root` 			  | Datubāzes lietotājvārds 					|
| `DB_PASSWORD` | tukša virkne 		  | Datubāzes parole, ja mainīgais nav norādīts |

Piemērs lokālai konfigurācijai (vērtības jāpielāgo savai videi):

```text
DB_HOST=localhost
DB_NAME=solar_cargo
DB_USER=root
DB_PASSWORD=
```

**Svarīgi:** šīs rindas nav automātiski nolasāms `.env` fails. Pašreizējais kods izmanto PHP `getenv()`, tāpēc mainīgie jāiestata servera vai procesa vidē. 
Produkcijas vidē neizmantojiet `root` kontu vai tukšu paroli; nepublicējiet piekļuves datus repozitorijā.

## 3. Palaišanas soļi un konfigurācija

1. Instalējiet XAMPP vai citu PHP un MySQL/MariaDB servera vidi.
2. Ievietojiet projekta failus vienā tīmekļa servera mapē, piemēram, `htdocs/solar_cargo/`.
3. Palaidiet Apache un MySQL/MariaDB.
4. Izveidojiet datubāzi `solar_cargo` ar `utf8mb4` kodējumu.
5. Izveidojiet tabulas `lietotaji` un `containers`. Kods izmanto šādus laukus:
   - `lietotaji`: `id`, `lietotajvards`, `parole_hash`, `loma`;
   - `containers`: `id`, `cargo_code`, `mineral`, `mass_kg`, `origin`, `destination`, `status`, `created_at`.
6. Konfigurējiet datubāzes savienojumu ar 2. sadaļā aprakstītajiem vides mainīgajiem.
7. Pārlūkprogrammā atveriet `http://localhost/solar_cargo/index.php`.
8. Atveriet reģistrācijas lapu `db.php`, izveidojiet lietotāju un pieslēdzieties lapā `login.php`.
9. Pārbaudiet konteinera pievienošanu un saraksta apskati.

**Datubāzes shēmas piezīme:** iesniegtajos failos nav SQL shēmas izveides skripta (`.sql`). 
Tāpēc precīzus kolonnu datu tipus, atslēgas un ierobežojumus nevar pilnībā apstiprināt. 
Pirms reproducējamas uzstādīšanas vēlams pievienot pārbaudītu `schema.sql`. 
Reģistrācijas kods jauniem lietotājiem skaidri piešķir lomu `user`; administratora konts jāizveido vai jāpiešķir atsevišķi, ievērojot drošības prasības.

### Ātrā funkcionālā pārbaude

- Reģistrējiet jaunu lietotāju un pārbaudiet pieslēgšanos.
- Pievienojiet konteineru ar kravas kodu, minerālu, pozitīvu masu, izcelsmes un galamērķa bāzi un statusu.
- Pārliecinieties, ka ieraksts parādās `containers_list.php`.
- Pārbaudiet izrakstīšanos un to, ka aizsargātās lapas bez autentifikācijas nav pieejamas.

Pieļaujamie konteineru statusi: `on_way` (Ceļā), `at_warehouse` (Noliktavā), `delivered` (Piegādāts), `delayed` (Kavējas).

## 4. Failu struktūra un uzdevumi

Paredzētā struktūra pēc failu nosaukumu normalizēšanas:

```text
solar_cargo/
├── README.md                 # Projekta dokumentācija
├── index.php                 # Publiskā sākumlapa un navigācija
├── style.css                 # Kopējais lapu noformējums
├── auth.php                  # Sesijas, CSRF, HTML ekranēšana, pāradresācija
├── database.php              # Kopīgais PDO datubāzes savienojums
├── db.php                    # Reģistrācijas forma
├── register_handler.php      # Reģistrācijas POST pieprasījuma apstrāde
├── login.php                 # Pieslēgšanās forma
├── login_handler.php         # Pieslēgšanās POST pieprasījuma apstrāde
├── logout.php                # Izrakstīšanās apstiprināšana un sesijas izbeigšana
├── user.php                  # Autentificēta lietotāja darba lapa
├── admin.php                 # Administratora panelis
├── add-container.php         # Konteinera pievienošanas forma
├── container_handler.php     # Konteinera datu validācija un saglabāšana
└── containers_list.php       # Konteineru saraksts no datubāzes
```

**Piekļuves kontrole:** `user.php` paredzēta lomai `user`, `admin.php` — lomai `admin`. 
Konteineru apskate un pievienošana paredzēta abām lomām. Reģistrācijas formā lietotājs nevar izvēlēties administratora lomu.

### Veiktā refaktorēšana

- **Koda dublēšanās samazināšana:** datubāzes savienojums apvienots funkcijā `getDatabaseConnection()` (`database.php`).
- **Funkciju sadalīšana:** konteineru apstrādē izmantotas `getContainerData()`, `validateContainer()` un `saveContainer()`.
- **Atbildību nodalīšana:** reģistrācijas, pieslēgšanās un konteineru pievienošanas formas nodalītas no attiecīgajiem POST apstrādātājiem.

## 5. Zināmie ierobežojumi

1. **Nepilnīga administratora funkcionalitāte.** Lietotāju pārvaldība un sistēmas iestatījumi administratora panelī ir vēl neizveidoti.
2. **Lomu atšķirība.** Sākumlapā aprakstītās dispečera, noliktavas operatora un vadītāja lomas nav ieviestas kā atsevišķas autentifikācijas lomas; 
   kodā tiek izmantotas `admin` un `user`.
3. **Konteineru darbību apjoms.** Iesniegtajos failos ir konteineru pievienošana un saraksta apskate; rediģēšanas, dzēšanas, meklēšanas un filtrēšanas funkcijas nav.
4. **Nav pievienots datubāzes izveides skripts.** Uzstādīšanai nepieciešamo SQL shēmu pagaidām jānodrošina atsevišķi.
5. **Nav dokumentēta automatizēta testēšana.** Nav izveidota automatizētu testu konfigurācija vai testu kopa.
6. **Nav apstiprināta produkcijas gatavība.** Nav iesniegti produkcijas izvietošanas, rezerves kopiju, monitoringa un pilna drošības audita rezultāti.

## 6. Publicēšanas informācija

**Pašreizējais statuss:** mācību projekts; iesniegtie materiāli dokumentē lokālu palaišanu. 
Publiskas izvietošanas adrese, repozitorija URL, versijas numurs un licence nav norādīti.

### Lokāla demonstrācija

Pēc 3. sadaļā aprakstītās uzstādīšanas lietotni var demonstrēt adresē:

`http://localhost/solar_cargo/index.php`

### Pirms publicēšanas internetā

- Pārbaudīt lietotni produkcijai paredzētajā PHP un datubāzes vidē.
- Izmantot HTTPS, atsevišķu datubāzes lietotāju ar minimālajām tiesībām un droši glabātus piekļuves datus.
- Neiekļaut repozitorijā reālas paroles, sesiju datus vai datubāzes rezerves kopijas ar personas datiem.
- Pievienot un pārbaudīt datubāzes migrācijas vai sākotnējās shēmas skriptu.
- Pārbaudīt autentifikāciju, autorizāciju, ievades validāciju, kļūdu apstrādi un piekļuves tiesības.
- Noteikt rezerves kopēšanas, atjaunošanas, atjaunināšanas un žurnalēšanas kārtību.
- Norādīt faktisko publicēšanas adresi, versiju, datumu un licenci tikai tad, kad tie ir noteikti.

### Publicēšanas metadati

| Lauks 			 | Pašreizējā informācija 					|
| ------------------ | ---------------------------------------- |
| Projekta nosaukums | Solar Cargo System 						|
| Statuss 			 | Mācību / izstrādes projekts 				|
| Lokālā adrese 	 | `http://localhost/solar_cargo/index.php` |
| Publiskā adrese 	 | Nav  									|
| Koda repozitorijs  | Nav  									|
| Versija 			 | v.2.0.0.  								|
| Licence 	         | Nav  									|

