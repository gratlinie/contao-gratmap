# GratMap

Standortkarte für Contao 5.7: Händler, Niederlassungen, Ansprechpartner – als Karte (Leaflet, Cluster) und/oder
Liste mit Umkreissuche, Länder- und Kategorienfilter. Mehrsprachig, mehrere Länder, datenschutzfreundlich.

- Paket: `gratlinie/contao-gratmap`, Namespace `Gratlinie\GratMapBundle`, Präfix `gm`
- Voraussetzungen: PHP ≥ 8.2, Contao 5.7

## Installation

Das Paket liegt (noch) nicht auf Packagist. Repository in der `composer.json` der Installation eintragen:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/gratlinie/contao-gratmap" }
]
```

```bash
composer require gratlinie/contao-gratmap:^1.0
php vendor/bin/contao-console contao:migrate
```

Im Contao Manager danach „Paket hinzufügen“ → `gratlinie/contao-gratmap` bzw. die Composer-Dateien hochladen und
„Composer install“ ausführen, anschließend die Datenbank aktualisieren.

Lokale Entwicklung mit Path-Repository:

```bash
composer config repositories.gratmap '{"type":"path","url":"/pfad/zu/GratMap/bundle","options":{"symlink":true}}'
composer require gratlinie/contao-gratmap:@dev
```

Dokumentation: [docs/gratmap.md](docs/gratmap.md)
