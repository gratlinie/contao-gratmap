# GratMap – Standortkarte

## Daten pflegen

**GratMap → Standorte** im Backend.

- **Sammlung** – z. B. „Händler“ oder „Niederlassungen“. Ein Inhaltselement kann mehrere Sammlungen zeigen.
- **Kategorien** (Ordner-Symbol an der Sammlung) – optional, z. B. „Fachhändler“, „Vertrieb“. Werden im
  Frontend als Filter angeboten.
- **Standort** – Name, Ansprechpartner, Adresse mit **Land**, Kontakt, Logo, Text, Veröffentlichung.

### Koordinaten

- Bleiben Breiten-/Längengrad leer, ermittelt GratMap sie beim Speichern aus der Adresse
  (OpenStreetMap/Nominatim, serverseitig, 30 Tage gecacht). Findet sich nichts, erscheint ein Hinweis.
- Im Abschnitt „Position auf der Karte“ lässt sich der Punkt per Klick oder Ziehen korrigieren;
  „Koordinaten aus Adresse ermitteln“ fragt die Adresse erneut ab.
- Standorte ohne Koordinaten erscheinen nur in der Liste, nicht auf der Karte und nicht in der Umkreissuche.

## Inhaltselement „Standortkarte“

| Feld | Wirkung |
|---|---|
| Sammlungen | Welche Standorte gezeigt werden |
| Ansicht | Karte und Liste / nur Karte / nur Liste |
| Liste gruppieren | Heimatland nach PLZ-Bereich (erste Ziffer), andere Länder nach Land – oder nach Land / gar nicht. Gruppen erscheinen als zugeklappte Akkordeons mit Anzahl und Ortsvorschau; ohne Gruppierung als flache Liste |
| Heimatland | Wird zuerst gelistet; bei Adressen dort entfällt die Landesangabe |
| Filter | Umkreissuche, Land, Kategorie. Land erscheint erst ab zwei Ländern, Kategorie nur wenn es welche gibt |
| Umkreise (km) | Kommagetrennt (auch Leer-/Semikolon), wird beim Speichern sortiert; der zweite Wert ist vorausgewählt |
| Link „Route planen“ | Google-Maps-Route zur Adresse (öffnet neuen Tab) |
| Bildgröße | Größe der Logos in der Liste |
| Kartenstil | Kachel-Anbieter (siehe unten) |
| Kartenhöhe | Mit Einheit (`32rem`, `60svh` …), leer = `clamp(22rem, 60svh, 40rem)` |
| Eigener Marker | SVG/PNG/WebP; Spitze unten mittig. Leer = Pin in der Akzentfarbe |
| Punkte bündeln | Cluster; Klick auf einen Cluster zoomt hinein |
| Zwei-Klick-Lösung | Karte (Kacheln) erst nach Klick laden, optional „immer laden“ (localStorage) |

### Umkreissuche

PLZ oder Ort wird serverseitig über `/_gratmap/geocode` aufgelöst (nur in den Ländern, die in der Liste
vorkommen; 20 Anfragen/Minute/IP). Treffer werden nach Entfernung sortiert; liegt im Radius nichts, zeigt
die Liste die drei nächstgelegenen Einträge. „Mein Standort“ nutzt die Browser-Ortung.

### Kartenkacheln

| Anbieter | Schlüssel | Hinweis |
|---|---|---|
| OpenStreetMap (Standard) | – | Nutzungsrichtlinie der OSMF beachten (moderater Traffic) |
| OpenStreetMap Deutschland | – | FOSSGIS-Server, deutsche Beschriftung |
| Esri Light Gray | – | dezent, max. Zoom 16 |
| MapTiler Streets | ja | kommerziell, Free-Tier verfügbar |
| Stadia Alidade Smooth | ja | kommerziell, Free-Tier verfügbar |
| Eigene Kachel-URL | – | Leaflet-Schema `{z}/{x}/{y}`, Quellenangabe pflegen |

CARTO-Basiskarten sind bewusst nicht enthalten: sie liefern ohne Enterprise-Schlüssel nur Wasserzeichen-Kacheln.

## Gestaltung

Alle Werte sind CSS-Variablen am Element `.gm` (Standardwerte mit Null-Spezifität, Projekt-CSS gewinnt immer):

```css
.gm {
    --gm-accent: var(--accentColor);
}
```

| Variable | Standard |
|---|---|
| `--gm-accent` / `--gm-accent-contrast` | `#c8102e` / `#fff` |
| `--gm-surface` / `--gm-surface-alt` | `#fff` / 4 % Textfarbe |
| `--gm-border` / `--gm-muted` | 14 % / 60 % Textfarbe |
| `--gm-radius` / `--gm-gap` | `0.5rem` / `1.25rem` |
| `--gm-control-height` | `3rem` (Höhe von Eingabefeldern und Buttons im Filter) |
| `--gm-map-height` | `clamp(22rem, 60svh, 40rem)` |
| `--gm-columns` | `16rem` (Mindestbreite der Karten in der Liste) |

Template: `content_element/gm_map.html.twig` (Override in `templates/`). JS-Hooks laufen über `data-gm-*`-Attribute.

## Datenschutz

- Leaflet und MarkerCluster liegen im Bundle, keine CDNs.
- Geocoding läuft serverseitig – die IP der Besucher geht nicht an Nominatim.
- Kartenkacheln kommen vom gewählten Anbieter; deshalb die Zwei-Klick-Lösung (unabhängig vom Consent-Tool).
- Die Liste wird serverseitig ausgegeben und funktioniert ohne JavaScript und ohne Einwilligung.
