# Praktiskā darba atskaite

---

## 1. Vispārīgā informācija

- Vārds, Uzvārds: Aivars Jirgensons
- Grupa: 77151_31.03.2026. - 09.04.2027.
- Praktiskā darba kods: PB3_PD17
- Datums: 2026_09_29

---

## 2. Darba mērķis

Piešķirt lietotāju lomas un tiesības Solar Cargo sistēmai.

---

## 3. Darba konteksts

Nodrošināt sistēmas drošības pamatprasību izveidi, lai lietotāji varētu iepazīties ar saturu tikai tādā apmērā, 
kādā tas neapdraud sistēmas darbību vai tejā esošo datu drošību.

---

## 4. Darba izpilde

Apraksti galvenos soļus.

---

### 4.1 Uzdevums 1

- Ko darīju:
Izveidoju Solar Cargo System pārlūkprogrammas lietotāju reģistrācijas sistēmu.

- Kā realizēju:
1. MariaDB esošajai DB solar_cargo pievienoju tabulu 'lietotaji' ar kolonnām atbilstoši uzdevuma nosacījumiem.
2. Projekta mapē PB3_PD17_Jirgensons_Aivars iekopēju iepriekš izstrādātos Solar Cargo System failus index.html, 
   kas ir projekta galvenā lapa un style.css, kurā bija projekta sākotnējais dizains.
3. Izmantoju Solar Cargo Viesu grāmatas PHP failu, lai izveidotu projekta reģistrācijas formu failā db.php.
4. Izmainīju vai izdzēsu lieko informāciju atbilstoši uzdevuma nosacījumiem.
5. Papildināju projekta index.html ar navigācijas pogām 'Reģistrēties', 'Pieslēgties', 'Izrakstīties' un pārsaucu par 
   index.php (reālas vajadzības tam pagaidām nebija, jo nepapildināju ar php).
6. Palaidu pārlūku caur localhost. Lapa darbojās, bet lietotāji DB nesaglabājās.
7. Veicu kļūdu labošanu izmantojot MI. Bija tikai 4 kļūdas.
8. Veicu labojumus. Pārlūks darbojas, reģistrācijas dati tiek saglabāti DB.
9. Piereģistrēju administratoru un nomainīju tam lomu MariaDB no 'user' uz 'admin' izmantojot SQL UPDATE vaicājumu.

- Rezultāts:
![Lietotāju datu saglabāšana DB](pielikumi/lietotaju_izveide.png)

WEB pārlūka reģistrācijas pārlūklapai vēl ir nepieciešami uzlabojumi, lai lietotājs, pēc sekmīgas reģistrācijas,
tiktu atgriests galvenajā lapā un ar UNDO nebūtu iespējams atgriezties reģistrācijas formā.

---

### 4.2 Uzdevums 2

- Ko darīju:
Izveidoju vienkāršu login formu.

- Kā realizēju:
Izveidoju failu login.php, kur:
1. Izveidoju formu lietotājvārda un paroles ievadam
2. PHP pārbauda lietotāju MariaDB datubāzē.
3  Paroles pārbaudei izmantoju password_verify().
4. Lietotāju novirze atbilstošu to lomai.
5. Ieprogrammēju lietotājam draudzīgu reaģēšanu uz sistēmas vai lietotāja kļūdām.

- Rezultāts:
Projekta LOGIN darbojas, kā tas ir noteikt PHP. Pēc sekmīgas pieslēgšanās user tiek novirzīts uz galveno lapu.

---

### 4.3 Uzdevums 3

- Ko darīju:
Izveidoju atšķirīgu saturu dažādām lomām.

- Kā realizēju:
Izveidoju lapas ar atsevišķu saturu lietotājiem 'user' un 'admin', izmantojot katram atsevišķu php failu: user.php un admin.php. Galveno lapu atstāju index.php.

- Rezultāts:
Lapas darbojas atsevišķi, bet funkcijas vēl nav sakārtotas, sadalītas kā es vēlētos. Tāpat ar BACK pogu, pēc izrakstīšanās no konta, joprojām var nokļūt kontā 
bez atkārtotas reģistrācijas. Jātzīst, ka noslinkoju un neveicu versiju saglabāšanu Git, kas noteikti ietekmēs gala secinājumnus un sākotnējā ideja būs nedaudz pazudusi.

---

### 4.4 Uzdevums 4

- Ko darīju:
Iveidoju aizsargātu admin lapu.

- Kā realizēju:
Izveidoju failu admin.php, kurā iekļāvu:
1. Admin sesijas uzturēšanu,
2. Admin tiesību aizsardzību,
3. Admin lapas HTML.

- Rezultāts:
Sistēmas administratoram ir sava lapa, kurāi piekļuve ir tikai viņam. Admin ir nodrošinātas papildu tiesības.
Tiesības nav sakārtotas pa lomām.

---

### 4.5 Uzdevums 5

- Ko darīju:
Izveidoju logout sistēmu.

- Kā realizēju:
Izveidoju failu logout.php, kurš pārtrauc lietotāja sesiju un pārslēdz to uz sistēmas galveno lapu.

- Rezultāts:
Lietotājs iziet no sava konta un nokļūst galvenajā lapā.

---

## 5. Rezultāts


Kas tika izveidots.

Pilnībā pārvietoju iepriekšējos PD izveidoto Solar Cargo System statisko lietotni uz dinamisko lietotni, 
kura ir izvietota localhost. Lietotnei ir sekojošas pārlūklapas:
1. Galvenā lapa (sākumplapa, index.php),
2. Jaunu lietotāju reģistrēšanās lapa (db.php),
3. Lietotāju pierakstīšanās lapa (login.php),
4. Parasto reģistrēto lietotāju lapa (user.php),
5. Sistēmas administratora lapa (admin.php).

Kā tas izskatās / darbojas:
1) galvenā lapa atveras,
2) jaunu lietotāju reģistrācija darbojas, dati tiek ierakstīti DB (kļūdas: pēc saglabāšanas, forma neizdzēš veco ierakstu. Nospiežot BACK pogu, 
   lietotājs tiek novirzīts iesniegtajā formā,),
3) pieslēgšanās forma darbojas (kļūdas: kad to atver, parādās neizdzēsta info no iepriekšējā pieslēguma. Līdzīgas BACK problēmas),
4) izrakstīšanās darbojas (kļūdas: izrakstīšanās notiek vizuāli, tas ir, lietotājs tiek atgriezts galvenajā lapā. Praktiski
   var atgriezties atpakaļ kontā bez atkārtotas reģistrācija nospiežot BACK, kas nozīmē, ka sesija netiek pārtraukts),
5) nav sakārtotas tiesības pa lomām,
6) nav pieslēgta (izveidota) konteineru ievades forma,
7) nav izveidota datu atlase vai vienkārša tabulas pārskatīšana.

Šis ir uzskatāms par zema līmeņa pilotprojektu, lai saprast, kā konceptuāli sistēma darbosies.

---

## 6. Problēmas un to risinājumi

Apraksti vismaz vienu:

- Problēma:
Pakārtoto lapu darbības nodrošināšana.

- Kā izpaudās:
Sākotnēji darbojās tikai galvenā lapa.

- Kā atrisināju:
Bija nepieciešams sataisīt uzreiz visas lapas. Īsti nesanāca to veikt soli pa solim kā tas bija norādīts PD aprakstā.

- Ko iemācījos:
Jāatgriežas pie pamatiem. No sākuma projekts ir jāuzzīmē uz lapas.

---

## 7. Secinājumi

- Ko jaunu iemācījos?
Guvu sapratni par funkcionējošas dinamiskās WEB pārlūkprogrammas darbību tās pamatos.

- Kas bija grūtākais?
Savietot visas lapas.

- Kas izdevās vislabāk?
Darbs ar DB.

- Ko darītu citādi nākamreiz?
Divas galvenās lietas:
1. Pildot līdzīgus PD, saglabāt versijas pēc katra uzdevuma,
2. Veltīt vairāk laika sagatavošanās darbiem "uz lapiņas".

Par MI izmantošanu.
Esmu nonācis pie secinājuma, ka šāda līmeņa PD izpilde bez MI atbalsta nav iespējama, līdz ar to ir jāatrod kompromiss starp
darbu izpildi un vielas apgūšanu. Jāmācās izmantot MI kā instruments, nevis kā špikeris vai darba darītājs manā vietā.
Ja tā strādā, tad var ātrāk izpildīt uzdevumus un arī apgūt mācību vielu.

---

## 8. Pašvērtējums

| Kritērijs 			 | Maks. punkti | Mani punkti |
|------------------------|--------------|-------------|
| Uzdevumu izpilde 		 |   	40		|   	25	  |
| Tehniskais izpildījums |   	20		|   	15	  |
| Struktūra 			 |   	10		|   	10	  |
| Dokumentācija 		 |   	15		|   	5	  |
| Atskaite 				 |   	15		|   	15	  |

Kopā: 80 / 100. Konceptuāli viss darbojas, bet vēl daudz darāmā, lai nodrošinātu korektu darbību un sistēmas drošību.

---

## 9. Pielikumi

- Pielikums 1 - README.md
- Pielikums 2 - admin.php
- Pielikums 3 - db.php
- Pielikums 4 - index.php
- Pielikums 5 - login.php
- Pielikums 6 - logout.php
- Pielikums 7 - user.php
- Pielikums 8 - style.css
- Pielikums 9 - lietotaju_izveide.png
