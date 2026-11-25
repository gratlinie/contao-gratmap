# Ländergeometrie

`world-countries.geo.json` – Umrisse von 236 Ländern, `id` ist der ISO-3166-1-alpha-2-Code.
Erzeugt mit `tools/build-countries.py` (lädt die Quellen selbst herunter, Aufruf aus dem Bundle-Wurzelverzeichnis).

**Quellen:** [Natural Earth](https://www.naturalearthdata.com/) „Admin 0 – Countries“, Maßstab 1:50 m,
bezogen über [world-atlas](https://github.com/topojson/world-atlas) 2.0.2. ISO-Codes aus
[datasets/country-codes](https://github.com/datasets/country-codes). Beides **gemeinfrei** (Public Domain),
eine Namensnennung ist nicht erforderlich.

**Aufbereitung:** Ringe werden am 180. Längengrad aufgeteilt (sonst zeichnen Russland, Fidschi und die
Antarktis Balken quer über die Karte), anschließend mit Douglas-Peucker auf 0,15° vereinfacht und auf zwei
Nachkommastellen gekürzt. Nebenringe unter 0,05 Quadratgrad entfallen; der größte Ring eines Landes bleibt
immer erhalten, damit Kleinstaaten nicht verschwinden. Ergebnis: 205 KB (66 KB gzip) statt 756 KB.

Das reicht für Klickflächen bei Weltzoom, **nicht für kartografische Zwecke**.

Nicht enthalten sind Gebiete ohne eigenes Polygon in Natural Earth, darunter GI, GF, GP, MQ, RE, YT, CC,
CX, SJ, TK, TV, UM und XK. Diese Länder bleiben über das Auswahlfeld erreichbar.
