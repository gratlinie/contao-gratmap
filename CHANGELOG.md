# Changelog

## 1.0.0 – unveröffentlicht

- Backend „GratMap → Standorte“: Sammlungen, Kategorien, Standorte (Adresse mit Land, Kontakt, Logo, Text,
  Veröffentlichung mit Start/Stopp).
- Koordinaten werden beim Speichern aus der Adresse ermittelt (Nominatim, serverseitig, gecacht);
  Kartenwidget zum Setzen/Korrigieren des Punkts.
- Inhaltselement „Standortkarte“ (`gm_map`): Karte und/oder Liste, Cluster mit Klick-Zoom,
  Umkreissuche (PLZ/Ort, „Mein Standort“), Länder- und Kategorienfilter, Gruppierung nach PLZ-Bereich/Land,
  „Route planen“, Zwei-Klick-Lösung, wählbare Kartenkacheln, eigener Marker.
- `contao:migrate` ausführen.
