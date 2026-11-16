# GratMap

Standortkarte für Contao 5.7: Händler, Niederlassungen, Ansprechpartner – als Karte (Leaflet, Cluster) und/oder
Liste mit Umkreissuche, Länder- und Kategorienfilter. Mehrsprachig, mehrere Länder, datenschutzfreundlich.

- Paket: `gratlinie/contao-gratmap`, Namespace `Gratlinie\GratMapBundle`, Präfix `gm`
- Voraussetzungen: PHP ≥ 8.2, Contao 5.7

## Installation (lokal, Path-Repository)

```bash
composer config repositories.gratmap '{"type":"path","url":"/pfad/zu/GratMap/bundle","options":{"symlink":true}}'
composer require gratlinie/contao-gratmap:@dev
php vendor/bin/contao-console contao:migrate
```

Dokumentation: [docs/gratmap.md](docs/gratmap.md)
