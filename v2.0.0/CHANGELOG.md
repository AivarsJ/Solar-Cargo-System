# Projekta Solar Cargo Systems Change Log

2026-10-22
Dokumentēta veiktā refaktorēšana

2026-10-21
Tika atdalīta autentifikācijas, lietotāju reģistrācijas un konteineru pievienošanas loģika no HTML

2026-10-21
Veikta datu saņemšanas un apstrādes funkcijas sadalīšana add-container.php trīs mazākās funkcijās

2026-10-21
Izveidots DB pieslēgšanas fails database.php. Failiem pievienota getDatabaseConnection() funkcija.Līdz ar to novērsts dublēts kods.

2026-10-21
Pievienotas funkcijas konteineru saraksta attēlošanai (containers_list.php un konteineru ievades funkcija (add-container.php)

2026-10-17
Veikta koda dokumentācija pievienojot failiem docstring.

2026-10-16
Novērsta drošības problēmas ar reģistrācijas un login formām, kas bija saistītas ar datu saglabāšanu formā, kā rezultātā ar BACK varēja ieiet atpakaļ formā