# Solar Cargo System - Project Initial Analysis

---

## Sistēmas mērķis

Sistēma paradzēta, lai nodrošinātu kosmiskajos pārvadājumos izmantojamo konteineru uzskaiti. 
Lietotājs, lietojot šo sistēmu, varēs uzkrāt, sistematizēt un efektīvi sekot konteineru kravu plūsmai, kā arī koordinēt to kustību pēc nepieciešamības.
Rezultātā tiks novērsta kļūmju novēršana konteineru uzskaitē un pārvietošanas kontrolē, tiks efektīvāk izmantoti pieejamie transportēšanas resursi, 
tiks paaugstināta kravu piegādes efektīvums un ekonomēti cilvēkresursi, kuri tiek pašlaik izmantoti, lai nodrošinātu konteineru loģistiku.

---

## Sistēmas galvenais objekts

Sistēmas galvenais objekts ir kosmisko pārvadājumu konteiners. Konteineram ir sekojoši parametri:
| ID 				| Minerāls 			| Masa 						| Izcelsme 				 | Galamērķis 		| Statuss 					|
| ----------------- | ----------------- | ------------------------- | ---------------------- | ----------------	| ------------------------- |
| Konteinera numurs | Kravas nosaukums 	| Kopējā konteinera masa 	| Kravas izcelsmes vieta | Piegādes adrese 	| Kravas piegādes statuss 	|

---

## Sākotnējās funkcionālās prasības

 Solar Cargo sistēmas sākotnējās funkcionālās prasības:
 
 - lietotājs var pievienot jaunu konteineru,
 - lietotājs var izdzēst nevajadzīgos ierakstus,
 - lietotājs var veikt ierakstu labojumus,
 - lietotājs var meklē konteinerus pēc to ID,
 - sistēma uzglabā ievietoto informāciju,
 - sistēma attēlo konteineru sarakstu,
 - sistēma attēlo konteinera informāciju.
 
---

## Sākotnējās nefunkcionālās prasības

Solar Cargo sistēmas sākotnējās nefunkcionālās prasības:

- sistēmai jābūt viegli lietojamai,
- lapai jābūt pārskatāmai,
- sistēmai jābūt pieejamai.
 
 ---

## Struktūra

Solar Cargo sistēmas struktūra:

- sākumlapa ---> index.html
- konteineru saraksts ---> containers.html
- konteineru pievienošana ---> add-container.html
- konteineru meklēšana un esošo datu apstrāde, ieskaitot dzēšanu ---> process-container.html

---

# Autors
	**Aivars Jirgensons**