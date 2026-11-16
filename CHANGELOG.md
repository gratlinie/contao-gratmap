# Changelog

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
