# Changelog

## 1.1.0 – 2026-11-23

- Standort: Feld **„Zuständig für diese Länder“**. Ein Standort kann beliebig viele Länder betreuen, statt
  je Land einen eigenen Datensatz zu pflegen.
- Inhaltselement: neuer Filter **„Zuständigkeit (Land wählen)“**. Besucher wählen ihr Land und bekommen den
  zuständigen Standort; die Karte zoomt darauf und öffnet die Sprechblase. Ist für ein Land nichts hinterlegt,
  sagt die Statuszeile das.
- Standort: Feld **Telefax**, wird in der Liste und in der Sprechblase ausgegeben.
- Liste: Gruppierung **nach Kontinent** (UN-M49-Einteilung, Tabelle im Bundle – keine Netzabfrage).
- Liste: **„Gruppen anzeigen“** – Akkordeon wie bisher oder offen mit Überschrift, alle Einträge sichtbar.
- Liste: **„Spalten“** – feste Spaltenzahl (2–6) auf großen Bildschirmen; darunter weiter so viele,
  wie nebeneinander passen.
- Karte: **„Länder anklickbar“** – zeichnet die betreuten Länder als Flächen in die Karte. Klick wählt das
  Land wie das Auswahlfeld, die Karte rahmt dann Land und zuständigen Standort zusammen ein. Die
  Ländergeometrie (Natural Earth, gemeinfrei, 66 KB gzip) lädt erst nach der Einwilligung und nur, wenn
  die Option aktiv ist; erzeugt mit `tools/build-countries.py`.
- Liste: Lange E-Mail-Adressen brechen um, statt aus schmalen Spalten herauszulaufen.
- **Behoben:** Die Anbindung an den Privacy Manager (PCT) griff nie. GratMap entschied beim Start, ob der
  Privacy Manager zuständig ist – zu diesem Zeitpunkt ist er aber noch nicht definiert, weil EclipseX ihn in
  der gebündelten `scripts.js` ausliefert, die nach `gratmap.js` lädt. Folge: GratMap zeigte immer seine
  eigene Zwei-Klick-Lösung, und eine Freigabe im Banner blieb wirkungslos. GratMap wartet jetzt bis kurz nach
  dem `load`-Event auf den Privacy Manager; der eigene Button ist währenddessen schon bedienbar.
- Controller greift auf die neuen Spalten mit `??` zu, damit eine Installation zwischen Datei-Deploy und
  `contao:migrate` nicht in Warnungen läuft.

Alle neuen Felder sind standardmäßig aus bzw. auf den bisherigen Werten – bestehende Installationen
sehen ohne Änderung im Backend genau das, was sie vorher gesehen haben.

## 1.0.1 – 2026-11-16

- „Auf der Karte zeigen“ zoomt jetzt mindestens auf Stufe 14 an den Standort heran (vorher nur so weit, bis der Punkt aus dem Cluster gelöst war).
- Backend: Der Stift an der Sammlung heißt jetzt „Sammlung ID … bearbeiten“ (vorher gleich wie „Standorte … bearbeiten“).

## 1.0.0 – 2026-11-16

- Backend „GratMap → Standorte“: Sammlungen, Kategorien, Standorte (Adresse mit Land, Kontakt, Logo, Text,
  Veröffentlichung mit Start/Stopp).
- Koordinaten werden beim Speichern aus der Adresse ermittelt (Nominatim, serverseitig, gecacht);
  Kartenwidget zum Setzen/Korrigieren des Punkts.
- Inhaltselement „Standortkarte“ (`gm_map`): Karte und/oder Liste, Cluster mit Klick-Zoom,
  Umkreissuche (PLZ/Ort, „Mein Standort“), Länder- und Kategorienfilter, Gruppierung nach PLZ-Bereich/Land,
  „Route planen“, Zwei-Klick-Lösung, wählbare Kartenkacheln, eigener Marker.
- Liste: Gruppen als Akkordeons (Anzahl und Orte im Kopf, beim Filtern aktualisiert), kompaktere Karten.
- Liste: Heimatland und „Weitere Länder“ als eigene Abschnitte, Länder mit Flagge (flag-icons, MIT).
- Filter: Eingabefelder, Auswahl und Buttons einheitlich hoch (`--gm-control-height`).
- Anbindung an den Privacy Manager von Premium Contao Themes (Feld „Privacy Manager (PCT)“).
- Feld „Start-Zoomstufe“.
- Backend: Gruppe „GratMap“ mit Lucide-Icon „pin“, direkt unter „Inhalte“.
- Eclipse-Hover-Regeln für Buttons/Eingabefelder neutralisiert (kein Springen der Aktionslinks).
- `contao:migrate` ausführen.
