# Solar Cargo System

**Solar Cargo System** ir mācību tīmekļa lietotne, kosmisko kravu konteineru uzskaitei. Tā veidota ar PHP, HTML, CSS un MySQL/MariaDB.

**Dokumentācija aktualizēta:** 2026-11-27.

## 1. Projekta mērķis

Projekta mērķis ir izveidot vienkāršu konteineru uzskaites sistēmu, kurā lietotāji var reģistrēties, pieslēgties un atbilstoši piešķirtajai lomai apskatīt vai 
pievienot konteineru ierakstus, kā arī atlasīt tos pēc kravas koda un statusa.

Pašlaik kodā ir realizēts:

- lietotāju reģistrācijas un pieslēgšanās process;
- divas autentifikācijas lomas: `user` un `admin`;
- konteineru pievienošana datubāzei un to saraksta apskate;
- konteineru datu pārbaude pirms saglabāšanas;
- konteineru atlase pēc kravas koda, statusa vai abiem kritērijiem;
- tukšu atlases kritēriju validācija un paziņojums, ja nav rezultātu;
- datubāzes shēmas skripts un dublikātu kļūdu apstrādes papildinājumi;
- publiskās lapas `about.php` un `contacts.php`;
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
4. Jaunai instalācijai izpildiet `solar_cargo_schema.sql`, piemēram, importējot failu phpMyAdmin. Skripts izveido datubāzi `solar_cargo` ar `utf8mb4` kodējumu un abas tabulas.
5. Pārbaudiet, vai abiem laukiem `lietotaji.lietotajvards` un `containers.cargo_code` ir `UNIQUE` ierobežojums. Ja lietotājvārda ierobežojuma nav, pēc esošo datu pārbaudes izpildiet zemāk norādīto `ALTER TABLE` komandu.
6. Konfigurējiet datubāzes savienojumu ar 2. sadaļā aprakstītajiem vides mainīgajiem.
7. Pārlūkprogrammā atveriet `http://localhost/solar_cargo/index.php`.
8. Atveriet reģistrācijas lapu `db.php`, izveidojiet lietotāju un pieslēdzieties lapā `login.php`.
9. Pārbaudiet konteinera pievienošanu, saraksta apskati un atlasi.

### Datubāzes shēma

2026-11-22 pievienots `solar_cargo_schema.sql`. Skripts paredzēts jaunai instalācijai, nevis atkārtotai izpildei esošā datubāzē.

| Tabula | Lauks | Datu tips un ierobežojumi |
| --- | --- | --- |
| `lietotaji` | `id` | `INT AUTO_INCREMENT PRIMARY KEY` |
| `lietotaji` | `lietotajvards` | `VARCHAR(100) NOT NULL`; lokālajā datubāzē pievienots `UNIQUE` |
| `lietotaji` | `parole_hash` | `VARCHAR(255) NOT NULL` |
| `lietotaji` | `loma` | `ENUM('admin','user') NOT NULL DEFAULT 'user'` |
| `containers` | `id` | `INT AUTO_INCREMENT PRIMARY KEY` |
| `containers` | `cargo_code` | `VARCHAR(30) NOT NULL UNIQUE` |
| `containers` | `mineral` | `VARCHAR(100) NOT NULL` |
| `containers` | `mass_kg` | `DECIMAL(10,2) NOT NULL` |
| `containers` | `origin` | `VARCHAR(100) NOT NULL` |
| `containers` | `destination` | `VARCHAR(100) NOT NULL` |
| `containers` | `status` | `VARCHAR(50) NOT NULL` |
| `containers` | `created_at` | `TIMESTAMP DEFAULT CURRENT_TIMESTAMP` |

**Svarīgi:** pēdējā pieejamajā SQL skripta kopijā lietotājvārda `UNIQUE` ierobežojums vēl nav iekļauts. Lokālajā datubāzē tas tika pievienots ar komandu:

```sql
ALTER TABLE lietotaji ADD CONSTRAINT uq_lietotajvards UNIQUE (lietotajvards);
```

Pirms komandas izpildes pārliecinieties, ka ierobežojums vēl nepastāv un tabulā nav lietotājvārdu dublikātu. Shēmas atbilstība aktuālajam repozitorijam vēl jāpārbauda.

Reģistrācijas kods jauniem lietotājiem piešķir lomu `user`; administratora konts jāizveido vai jāpiešķir atsevišķi, ievērojot drošības prasības.

### Ātrā funkcionālā pārbaude

- Reģistrējiet jaunu lietotāju un pārbaudiet pieslēgšanos.
- Pievienojiet konteineru ar kravas kodu, minerālu, pozitīvu masu, izcelsmes un galamērķa bāzi un statusu.
- Pārliecinieties, ka ieraksts parādās `containers_list.php?mode=all`.
- Pārbaudiet atlasi pēc koda, statusa, abiem kritērijiem, tukšiem laukiem un neatbilstošiem kritērijiem.
- Pārbaudiet atkārtota lietotājvārda un kravas koda ievadīšanu; dublikāts nedrīkst tikt saglabāts.
- Pārbaudiet izrakstīšanos un to, ka aizsargātās lapas bez autentifikācijas nav pieejamas.

Pieļaujamie konteineru statusi: `on_way` (Ceļā), `at_warehouse` (Noliktavā), `delivered` (Piegādāts), `delayed` (Kavējas).

## 4. Failu struktūra un uzdevumi

Failu struktūra, papildināta atbilstoši līdz 2026-11-27 dokumentētajām izmaiņām:

```text
solar_cargo/
├── README.md                 # Projekta dokumentācija
├── index.php                 # Publiskā sākumlapa un navigācija
├── about.php                 # Publiskā informācija par uzņēmumu
├── contacts.php              # Publiskā kontaktu sadaļa
├── solar_cargo_schema.sql    # Datubāzes sākotnējā shēma
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
├── containers_list.php       # Visu konteineru apskate un atlase
└── pielikumi/                # Publisko lapu attēli
    ├── starship.png
    └── yoda.png
```

**Piekļuves kontrole:** `user.php` paredzēta lomai `user`, `admin.php` — lomai `admin`. 
Konteineru apskate un pievienošana paredzēta abām lomām. Reģistrācijas formā lietotājs nevar izvēlēties administratora lomu.

### Konteineru atlase

Lietotāja darba lapā `user.php` ir divas saites:

- `containers_list.php?mode=all` — visu konteineru apskate;
- `containers_list.php?mode=search` — atlases forma bez sākotnēji attēlotas tabulas.

Atlases forma izmanto `GET` metodi, parametrus `mode`, `search`, `fcargo_code` un `fstatus`. Kravas kods (`cargo_code`) ir lietotāja ievadīts unikāls identifikators, savukārt `id` ir datubāzes automātiski ģenerēta primārā atslēga.

| Atlases kritēriji | Rezultāts |
| --- | --- |
| Tikai kravas kods | Atbilstošais konteiners, ja tāds ir |
| Tikai statuss | Konteineri ar izvēlēto statusu |
| Abi kritēriji | Ieraksti, kas atbilst abiem nosacījumiem (`AND`) |
| Abi lauki tukši | Validācijas paziņojums; atlases SQL netiek izpildīts |
| Nav atbilstošu ierakstu | Paziņojums bez tabulas |

SQL nosacījumi tiek dinamiski apvienoti ar `implode()` un vērtības nodotas PDO sagatavotajam vaicājumam ar `execute($parametri)`. Rezultāti sakārtoti pēc `id ASC`. Atlase nemaina datubāzes ierakstus.

`add-container.php` un `containers_list.php` papildināti ar saiti uz `user.php`. Administratora navigācijas pielāgošana abiem režīmiem vēl jāpārbauda.

### Dublikātu apstrāde

2026-11-22 papildināti `register_handler.php` un `container_handler.php`, lai apstrādātu datubāzes dublikātu kļūdas (MySQL/MariaDB kļūdas kods `1062`). Lietotājvārda dublikāta paziņojums pārbaudīts manuāli. Kravas koda dublikāta apstrāde dokumentēta izmaiņu vēsturē, bet galīgā apstrādātāja versija un testa rezultāts vēl jāpārbauda. Pēdējās pieejamajās apstrādātāju kopijās ir nepilnības kļūdas koda pārbaudē; aktuālajā kodā jāpārbauda, ka tiek izmantots noķertā `PDOException` objekta `errorInfo[1]`.

### Veiktā refaktorēšana

- **Koda dublēšanās samazināšana:** datubāzes savienojums apvienots funkcijā `getDatabaseConnection()` (`database.php`).
- **Funkciju sadalīšana:** konteineru apstrādē izmantotas `getContainerData()`, `validateContainer()` un `saveContainer()`.
- **Atbildību nodalīšana:** reģistrācijas, pieslēgšanās un konteineru pievienošanas formas nodalītas no attiecīgajiem POST apstrādātājiem.


## 5. Sākumlapas izmaiņas un regresijas pārbaude

**Veiktās izmaiņas sākumlapā:**

- Papildināts Solar Cargo System mērķa un kosmisko pārvadājumu uzdevumu apraksts.
- Pievienots sistēmas lietotāju darbības apraksts: dispečers, noliktavas operators, vadītājs un administrators. 
  Tās ir **plānotās darbības lomas**, nevis atsevišķi ieviestas autentifikācijas līmeņi; pašlaik darbojas tikai `user` un `admin`.
- Publiskajā sākumlapā noņemtas saites uz konteineru sarakstu un pievienošanu: šīs darbības pieejamas pēc pieslēgšanās lietotāja vai administratora lapā.
- Sākumlapas navigācijā ir `Par mums` (`about.php`), `Kontakti` (`contacts.php`), `Reģistrēties` (`db.php`) un `Pieslēgties` (`login.php`). 
  `about.php` un `contacts.php` izveidotas 2026-11-23; tajās izmantoti attēli no mapes `pielikumi/`.
- Sākumlapā nav pogas `Izrakstīties`; izrakstīšanās pieejama autentificēta lietotāja un administratora lapā.

**Vēsturiskā manuālā regresijas pārbaude (PB4_PD07, oktobris):**

1. Pārbaudīts sākumlapas sistēmas apraksts; atbilstība specifikācijai ir daļēja, jāuzlabo izkārtojums.
2. Pārbaudīts lietotāju funkciju apraksts; teksts redzams, nepieciešami CSS uzlabojumi.
3. Pārbaudītas saites `Par mums` un `Kontakti`; pārbaudes brīdī abas atgrieza `Not Found`. Novembrī lapas izveidotas un pārbaudītas.
4. Pārbaudīts, ka sākumlapā nav `Izrakstīties` pogas.
5. Pēc pieslēgšanās ar `user` un `admin` kontiem ir pieejams konteineru saraksts un pievienošanas forma.
6. Pārbaudīta izrakstīšanās; lietotājs tiek novirzīts uz sākumlapu.

**Papildu statiskā pārbaude v2.0.0 arhīvam (2026-10-25):** visiem 13 PHP failiem izpildīta `php -l` sintakses pārbaude bez kļūdām. Pārskatīti formu `action`, CSRF pārbaudes, 
autentifikācijas nosacījumi, datubāzes savienojuma izsaukumi un konteineru saglabāšanas ceļš. `container_handler.php` saglabāšanas ceļā ir `getDatabaseConnection()` 
un `saveContainer()`; iepriekšējā priekšlaicīgā pāradresācija v2.0.0 nav konstatēta. **Nav veikta automatizēta vai datubāzē izpildīta pilna end-to-end pārbaude**, 
tāpēc pilnīgu regresiju neesamību apstiprināt nevar.

### Novembrī veiktās manuālās pārbaudes

| Datums | Pārbaude | Rezultāts |
| --- | --- | --- |
| 2026-11-22 | Atkārtota lietotājvārda reģistrācija | Parādīts kļūdas paziņojums |
| 2026-11-23 | `about.php` un `contacts.php` | Lapas darbojas |
| 2026-11-25–27 | Atlase pēc koda un statusa, atsevišķi un kopā | Atlase darbojas |
| 2026-11-27 | Tukši kritēriji un rezultātu neesamība | Paziņojumi darbojas; nevajadzīga tabula netiek attēlota |

Rezultāti balstīti uz izstrādātāja manuālajām pārbaudēm un apstiprinājumiem. Pilna atkārtota regresijas pārbaude aktuālajai versijai nav dokumentēta.

**Atlikušie darbi (pašlaik atlikti):**

- Saskaņot sākumlapā aprakstītās lietotāju tiesības ar faktiski ieviestajām funkcijām: statusu maiņa, ierakstu dzēšana un arhivēšana vēl nav realizēta.
- `add-container.php`: forma veido PDO savienojumu, lai gan datubāzi izmanto `container_handler.php`; nevajadzīgo pieslēgumu var izņemt pēc pārbaudes.
- `login.php` un `db.php`: atjaunot failu sākuma komentārus, jo POST apstrāde ir pārvietota uz `login_handler.php` un `register_handler.php`.
- Pārbaudīt SQL shēmas atbilstību lokālajai datubāzei un dublikātu apstrādātāju galīgās versijas.
- Papildināt atlases ievaddatu validāciju un saglabāt izvēlētos kritērijus formas laukos pēc atlases.
- Izveidot automatizētus regresijas testus, īpaši reģistrācijai, pieslēgšanās/izrakstīšanās procesam, CSRF, lomu piekļuvei, konteineru validācijai un saglabāšanai.

## 6. Zināmie ierobežojumi

1. **Nepilnīga administratora funkcionalitāte.** Lietotāju pārvaldība un sistēmas iestatījumi administratora panelī ir vēl neizveidoti.
2. **Lomu atšķirība.** Sākumlapā aprakstītās dispečera, noliktavas operatora un vadītāja lomas nav ieviestas kā atsevišķas autentifikācijas lomas; 
   kodā tiek izmantotas `admin` un `user`.
3. **Konteineru darbību apjoms.** Realizēta konteineru pievienošana, saraksta apskate un atlase pēc kravas koda un statusa. Detalizēta apskate atsevišķā lapā, rediģēšana, statusa maiņa un dzēšana vēl nav realizēta.
4. **Datubāzes shēmas pārbaude.** SQL skripts ir pievienots; vēl jāpārbauda tā atbilstība aktuālajai datubāzei un jaunas instalācijas reproducējamība.
5. **Nav dokumentēta automatizēta testēšana.** Nav izveidota automatizētu testu konfigurācija vai testu kopa.
6. **Nav apstiprināta produkcijas gatavība.** Nav iesniegti produkcijas izvietošanas, rezerves kopiju, monitoringa un pilna drošības audita rezultāti.

## 7. Publicēšanas informācija

**Pašreizējais statuss:** mācību projekts (v2.0.0); iesniegtie materiāli dokumentē lokālu palaišanu. 
Projekta kods tiek uzturēts GitHub repozitorijā. Precīzs repozitorija URL, publiskas lietotnes izvietošanas adrese un licence nav norādīti. Arhīva versija: `v2.0.0`.

### Lokāla demonstrācija

Pēc 3. sadaļā aprakstītās uzstādīšanas lietotni var demonstrēt adresē:

`http://localhost/solar_cargo/index.php`

### Pirms publicēšanas internetā

- Pārbaudīt lietotni produkcijai paredzētajā PHP un datubāzes vidē.
- Izmantot HTTPS, atsevišķu datubāzes lietotāju ar minimālajām tiesībām un droši glabātus piekļuves datus.
- Neiekļaut repozitorijā reālas paroles, sesiju datus vai datubāzes rezerves kopijas ar personas datiem.
- Pārbaudīt pievienoto sākotnējās shēmas skriptu un nepieciešamības gadījumā sagatavot datubāzes migrācijas.
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
| Koda repozitorijs  | GitHub (URL nav norādīts)  									|
| Versija 			 | v.2.0.0.  								|
| Licence 	         | Nav  									|

