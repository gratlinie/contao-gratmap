#!/usr/bin/env python3
"""
Erzeugt public/vendor/world/world-countries.geo.json aus Natural Earth (via world-atlas).

    python3 tools/build-countries.py

Schritte: TopoJSON laden -> GeoJSON -> Ringe am 180. Laengengrad aufteilen ->
Douglas-Peucker in Grad -> Mikro-Ringe verwerfen -> numerische ISO-Codes auf Alpha-2.

Die Vereinfachung zielt auf Klickflaechen bei Weltzoom, nicht auf Kartografie.
Natural Earth ist gemeinfrei; die Code-Tabelle (datasets/country-codes) ebenfalls.
"""
import json, math, csv, io, re, gzip, pathlib, urllib.request

TOPO = 'https://cdn.jsdelivr.net/npm/world-atlas@2.0.2/countries-50m.json'
CODES = 'https://raw.githubusercontent.com/datasets/country-codes/master/data/country-codes.csv'
EPS = 0.15          # Douglas-Peucker-Toleranz in Grad (~17 km am Aequator)
MIN_AREA = 0.05     # Nebenringe kleiner als das (Quadratgrad) entfallen - der groesste Ring
                    # eines Landes bleibt immer erhalten, sonst verschwaenden Kleinstaaten
UA = 'GratMap country-data build (https://github.com/gratlinie/contao-gratmap)'

OUT = pathlib.Path(__file__).resolve().parent.parent / 'public/vendor/world/world-countries.geo.json'


def fetch(url):
    return urllib.request.urlopen(urllib.request.Request(url, headers={'User-Agent': UA}), timeout=60).read()


def topo_to_geo(t):
    tr = t['transform']
    arcs = []
    for arc in t['arcs']:
        x = y = 0
        pts = []
        for dx, dy in arc:
            x += dx
            y += dy
            pts.append([x * tr['scale'][0] + tr['translate'][0], y * tr['scale'][1] + tr['translate'][1]])
        arcs.append(pts)

    def ring(idx):
        pts = []
        for i in idx:
            a = arcs[~i][::-1] if i < 0 else arcs[i]
            pts += a if not pts else a[1:]
        return pts

    out = []
    for g in t['objects']['countries']['geometries']:
        if g['type'] == 'Polygon':
            polys = [[ring(r) for r in g['arcs']]]
        elif g['type'] == 'MultiPolygon':
            polys = [[ring(r) for r in p] for p in g['arcs']]
        else:
            continue
        out.append((g.get('id'), polys))
    return out


def unwrap(ring):
    """Laengengrade fortlaufend machen: Spruenge ueber 180 Grad aufheben."""
    out = [list(ring[0])]
    off = 0.0
    for prev, cur in zip(ring, ring[1:]):
        d = cur[0] - prev[0]
        if d > 180:
            off -= 360
        elif d < -180:
            off += 360
        out.append([cur[0] + off, cur[1]])
    return out


def split_at_antimeridian(ring):
    """Ring an x = 180 + 360k zerschneiden und die Stuecke in [-180, 180] zurueckschieben."""
    r = unwrap(ring)
    lo, hi = min(p[0] for p in r), max(p[0] for p in r)
    k_lo, k_hi = math.floor((lo + 180) / 360), math.floor((hi + 180) / 360)

    if k_lo == k_hi:                      # liegt ganz in einem Rahmen
        shift = -360 * k_lo
        return [[[p[0] + shift, p[1]] for p in r]]

    pieces = []
    for k in range(k_lo, k_hi + 1):
        left, right = -180 + 360 * k, 180 + 360 * k
        clipped = clip_x(r, left, right)
        if clipped and len(clipped) >= 4:
            pieces.append([[p[0] - 360 * k, p[1]] for p in clipped])
    return pieces


def clip_x(ring, left, right):
    """Sutherland-Hodgman gegen den Streifen left <= x <= right."""
    def half(poly, keep, x_at):
        out = []
        for a, b in zip(poly, poly[1:] + poly[:1]):
            ka, kb = keep(a), keep(b)
            if ka:
                out.append(a)
            if ka != kb:
                t = (x_at - a[0]) / (b[0] - a[0])
                out.append([x_at, a[1] + t * (b[1] - a[1])])
        return out

    poly = half(ring, lambda p: p[0] >= left, left)
    if not poly:
        return []
    poly = half(poly, lambda p: p[0] <= right, right)
    return poly + [poly[0]] if poly else []


def dp(pts, eps):
    if len(pts) < 3:
        return pts
    keep = [False] * len(pts)
    keep[0] = keep[-1] = True
    stack = [(0, len(pts) - 1)]
    while stack:
        a, b = stack.pop()
        if b - a < 2:
            continue
        ax, ay = pts[a]
        bx, by = pts[b]
        dx, dy = bx - ax, by - ay
        norm = math.hypot(dx, dy)
        best, bi = -1.0, None
        for i in range(a + 1, b):
            px, py = pts[i]
            d = abs(dx * (ay - py) - (ax - px) * dy) / norm if norm else math.hypot(px - ax, py - ay)
            if d > best:
                best, bi = d, i
        if best > eps:
            keep[bi] = True
            stack += [(a, bi), (bi, b)]
    return [p for p, k in zip(pts, keep) if k]


def area(ring):
    s = 0.0
    for a, b in zip(ring, ring[1:]):
        s += a[0] * b[1] - b[0] * a[1]
    return abs(s) / 2


def simplify(piece, eps):
    """Ein Ringstueck vereinfachen; None, wenn nichts Brauchbares uebrig bleibt."""
    s = dp(piece, eps)
    s = [p for i, p in enumerate(s) if i == 0 or p != s[i - 1]]
    if len(s) < 4:
        return None
    if s[0] != s[-1]:
        s.append(s[0])
    return [[round(p[0], 2), round(p[1], 2)] for p in s]


def clean_country(polys):
    """Alle Ringe eines Landes aufbereiten. Der groesste Ring bleibt in jedem Fall."""
    pieces = [pc for poly in polys for ring in poly for pc in split_at_antimeridian(ring)]

    if not pieces:
        return []

    out = [s for pc in pieces if area(pc) >= MIN_AREA and (s := simplify(pc, EPS))]

    if out:
        return out

    # Kleinstaat: groesstes Stueck mit feinerer Toleranz behalten
    biggest = max(pieces, key=area)
    for eps in (EPS / 3, EPS / 10, 0.0):
        if s := simplify(biggest, eps):
            return [s]
    return []


def main():
    print('lade Natural Earth 1:50m …')
    topo = json.loads(fetch(TOPO))
    print('lade ISO-Codes …')
    num2a2 = {}
    for row in csv.DictReader(io.StringIO(fetch(CODES).decode('utf-8'))):
        n = (row.get('ISO3166-1-numeric') or '').strip()
        a = (row.get('ISO3166-1-Alpha-2') or '').strip().upper()
        if n and len(a) == 2:
            num2a2[str(int(n))] = a

    feats, ohne = [], []
    for fid, polys in topo_to_geo(topo):
        code = num2a2.get(str(int(fid))) if str(fid or '').isdigit() else None
        if not code:
            ohne.append(fid)
            continue
        rings = clean_country(polys)
        if not rings:
            continue
        feats.append({'type': 'Feature', 'id': code,
                      'geometry': {'type': 'MultiPolygon', 'coordinates': [[r] for r in rings]}})

    s = json.dumps({'type': 'FeatureCollection', 'features': feats}, separators=(',', ':'))
    s = re.sub(r'(-?\d+\.\d\d)\d+', r'\1', s)
    OUT.write_text(s)
    print(f'{OUT.name}: {len(feats)} Länder, {len(s)} B roh, {len(gzip.compress(s.encode(), 9))} B gzip')
    print(f'ohne ISO-Zuordnung übersprungen: {len(ohne)}')


if __name__ == '__main__':
    main()
