(() => {
    const STORAGE_KEY = 'gratmap-consent';
    const loaded = new Map();

    const load = (url, type) => {
        if (!loaded.has(url)) {
            loaded.set(url, new Promise((resolve, reject) => {
                const el = type === 'css' ? document.createElement('link') : document.createElement('script');
                if (type === 'css') {
                    el.rel = 'stylesheet';
                    el.href = url;
                } else {
                    el.src = url;
                }
                el.onload = resolve;
                el.onerror = reject;
                document.head.append(el);
            }));
        }
        return loaded.get(url);
    };

    const storage = {
        get: () => {
            try {
                return localStorage.getItem(STORAGE_KEY) === '1';
            } catch {
                return false;
            }
        },
        set: () => {
            try {
                localStorage.setItem(STORAGE_KEY, '1');
            } catch {}
        },
    };

    const toRad = (deg) => (deg * Math.PI) / 180;

    const distance = (a, b) => {
        const dLat = toRad(b.lat - a.lat);
        const dLng = toRad(b.lng - a.lng);
        const h = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(a.lat)) * Math.cos(toRad(b.lat)) * Math.sin(dLng / 2) ** 2;
        return 12742 * Math.asin(Math.sqrt(h));
    };

    const format = (text, ...values) => values.reduce((out, value) => out.replace('%s', value), text);

    class GratMap {
        constructor(root) {
            this.root = root;
            this.config = JSON.parse(root.querySelector('[data-gm-config]').textContent);
            this.labels = this.config.labels;
            this.lang = document.documentElement.lang || 'de';
            this.state = { country: '', category: '', origin: null, radius: 0 };

            this.form = root.querySelector('[data-gm-filter]');
            this.status = root.querySelector('[data-gm-status]');
            this.results = root.querySelector('[data-gm-results]');
            this.groups = root.querySelector('[data-gm-groups]');
            this.empty = root.querySelector('[data-gm-empty]');
            this.mapEl = root.querySelector('[data-gm-map]');

            this.items = [...root.querySelectorAll('[data-gm-item]')].map((el) => ({
                el,
                id: Number(el.dataset.gmItem),
                country: el.dataset.gmCountry,
                categories: (el.dataset.gmCategories || '').split(',').filter(Boolean),
                point: el.dataset.gmLat ? { lat: Number(el.dataset.gmLat), lng: Number(el.dataset.gmLng) } : null,
                home: { parent: el.parentElement, index: [...el.parentElement.children].indexOf(el) },
                distanceEl: el.querySelector('[data-gm-distance]'),
            }));
            this.byId = new Map(this.items.map((item) => [item.id, item]));

            this.bindFilter();
            this.bindList();
            this.setupMap();
            this.apply(false);
        }

        bindFilter() {
            if (!this.form) {
                return;
            }

            const query = this.form.querySelector('[data-gm-query]');
            const radius = this.form.querySelector('[data-gm-radius]');
            const country = this.form.querySelector('[data-gm-country]');
            const category = this.form.querySelector('[data-gm-category]');
            const locate = this.form.querySelector('[data-gm-locate]');
            this.resetButton = this.form.querySelector('[data-gm-reset]');

            this.state.radius = Number(radius?.value || 0);

            this.form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const q = query?.value.trim();
                if (!q) {
                    this.state.origin = null;
                    this.apply();
                    return;
                }
                await this.search(q);
            });

            radius?.addEventListener('change', () => {
                this.state.radius = Number(radius.value);
                if (this.state.origin) {
                    this.apply();
                }
            });

            country?.addEventListener('change', () => {
                this.state.country = country.value;
                this.apply();
            });

            category?.addEventListener('change', () => {
                this.state.category = category.value;
                this.apply();
            });

            if (locate && 'geolocation' in navigator) {
                locate.hidden = false;
                locate.addEventListener('click', () => {
                    this.setStatus(this.labels.locating);
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            if (query) {
                                query.value = '';
                            }
                            this.state.origin = { lat: pos.coords.latitude, lng: pos.coords.longitude, label: this.labels.yourLocation };
                            this.apply();
                        },
                        () => this.setStatus(this.labels.locateFailed),
                        { timeout: 10000, maximumAge: 300000 },
                    );
                });
            }

            this.form.addEventListener('reset', () => {
                setTimeout(() => {
                    this.state.country = '';
                    this.state.category = '';
                    this.state.origin = null;
                    this.state.radius = Number(radius?.value || 0);
                    this.apply();
                });
            });
        }

        async search(q) {
            this.setStatus(this.labels.searching);
            const params = new URLSearchParams({ q, c: this.config.countries.join(',') });

            try {
                const response = await fetch(`${this.config.geocodeUrl}?${params}`, { headers: { Accept: 'application/json' } });
                if (response.status === 429) {
                    this.setStatus(this.labels.rateLimit);
                    return;
                }
                if (!response.ok) {
                    this.setStatus(format(this.labels.notFound, q));
                    return;
                }
                const data = await response.json();
                this.state.origin = { lat: data.lat, lng: data.lng, label: q };
                this.apply();
            } catch {
                this.setStatus(format(this.labels.notFound, q));
            }
        }

        bindList() {
            this.root.addEventListener('click', (e) => {
                const button = e.target.closest('[data-gm-show]');
                if (!button) {
                    return;
                }
                const item = this.byId.get(Number(button.closest('[data-gm-item]').dataset.gmItem));
                this.focus(item);
            });
        }

        setStatus(text) {
            if (this.status) {
                this.status.textContent = text || '';
            }
        }

        matches(item) {
            if (this.state.country && item.country !== this.state.country) {
                return false;
            }
            if (this.state.category && !item.categories.includes(this.state.category)) {
                return false;
            }
            return true;
        }

        formatDistance(km) {
            const digits = km < 10 ? 1 : 0;
            return `${km.toLocaleString(this.lang, { maximumFractionDigits: digits, minimumFractionDigits: digits })} km`;
        }

        restore() {
            this.items
                .slice()
                .sort((a, b) => a.home.index - b.home.index)
                .forEach((item) => {
                    const { parent, index } = item.home;
                    parent.insertBefore(item.el, parent.children[index] || null);
                    item.distanceEl.hidden = true;
                });
        }

        apply(fit = true) {
            const { origin } = this.state;
            let shown = [];

            this.restore();

            if (origin) {
                const candidates = this.items
                    .filter((item) => item.point && this.matches(item))
                    .map((item) => ({ item, km: distance(origin, item.point) }))
                    .sort((a, b) => a.km - b.km);

                let hits = candidates.filter((c) => c.km <= this.state.radius);
                let message = format(this.labels.countRadius, hits.length, this.state.radius, origin.label);

                if (!hits.length && candidates.length) {
                    hits = candidates.slice(0, 3);
                    message = format(this.labels.nearest, this.state.radius, origin.label);
                }

                this.items.forEach((item) => {
                    item.el.hidden = true;
                });

                hits.forEach(({ item, km }) => {
                    item.el.hidden = false;
                    item.distanceEl.textContent = this.formatDistance(km);
                    item.distanceEl.hidden = false;
                    this.results.append(item.el);
                });

                this.results.hidden = false;
                this.groups.hidden = true;
                shown = hits.map((h) => h.item);
                this.setStatus(message);
            } else {
                this.items.forEach((item) => {
                    item.el.hidden = !this.matches(item);
                });

                const groups = [...this.groups.querySelectorAll('[data-gm-group]')];
                const filtered = Boolean(this.state.country || this.state.category);

                groups.forEach((group) => {
                    const visible = [...group.querySelectorAll('[data-gm-item]:not([hidden])')];
                    group.hidden = !visible.length;
                    this.updateGroupHead(group, visible);
                });

                this.groups.querySelectorAll('[data-gm-section]').forEach((section) => {
                    section.hidden = !section.querySelector('[data-gm-group]:not([hidden])');
                });

                const visibleGroups = groups.filter((group) => !group.hidden);
                if (filtered && visibleGroups.length === 1 && 'open' in visibleGroups[0]) {
                    visibleGroups[0].open = true;
                }

                this.results.hidden = true;
                this.groups.hidden = false;
                shown = this.items.filter((item) => !item.el.hidden);
                this.setStatus(this.state.country || this.state.category ? format(this.labels.count, shown.length) : '');
            }

            if (this.resetButton) {
                this.resetButton.hidden = !(origin || this.state.country || this.state.category);
            }

            if (this.empty) {
                this.empty.hidden = shown.length > 0;
            }

            this.shown = shown;
            this.updateMap(fit);
        }

        updateGroupHead(group, visible) {
            const count = group.querySelector('[data-gm-group-count]');
            const cities = group.querySelector('[data-gm-group-cities]');

            if (count) {
                count.textContent = format(visible.length === 1 ? this.labels.groupCountOne : this.labels.groupCount, visible.length);
            }

            if (cities) {
                cities.textContent = [...new Set(visible.map((el) => el.dataset.gmCity).filter(Boolean))].join(', ');
            }
        }

        setupMap() {
            if (!this.mapEl) {
                return;
            }

            const consent = this.mapEl.querySelector('[data-gm-consent]');
            const privacy = window.PrivacyManager;
            const level = this.config.privacyLevel;

            if (consent && level && typeof privacy?.hasAccess === 'function') {
                if (!privacy.hasAccess(level)) {
                    this.setupPrivacyManager(consent, privacy, level);
                    return;
                }
            } else if (consent && !storage.get()) {
                consent.querySelector('[data-gm-consent-accept]').addEventListener('click', () => {
                    if (consent.querySelector('[data-gm-consent-remember]')?.checked) {
                        storage.set();
                    }
                    consent.remove();
                    this.initMap();
                });
                return;
            }

            consent?.remove();
            this.observeMap();
        }

        setupPrivacyManager(consent, privacy, level) {
            const start = () => {
                if (consent.isConnected) {
                    consent.remove();
                    this.initMap();
                }
            };

            consent.querySelector('.gm-consent__remember')?.remove();
            consent.querySelector('[data-gm-consent-accept]').addEventListener('click', start);

            const settings = consent.querySelector('[data-gm-consent-settings]');
            if (settings && typeof privacy.optout === 'function') {
                settings.hidden = false;
                settings.addEventListener('click', () => privacy.optout());
            }

            window.jQuery?.(document).on('Privacy.changed', () => {
                if (privacy.hasAccess(level)) {
                    start();
                }
            });
        }

        observeMap() {
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    if (entries.some((entry) => entry.isIntersecting)) {
                        observer.disconnect();
                        this.initMap();
                    }
                }, { rootMargin: '200px' });
                observer.observe(this.mapEl);
            } else {
                this.initMap();
            }
        }

        async initMap() {
            if (this.mapStarted) {
                return;
            }
            this.mapStarted = true;

            const { assets, tiles, cluster } = this.config;

            this.mapEl.classList.add('is-loading');

            try {
                await Promise.all([load(assets.leafletCss, 'css'), load(assets.leafletJs, 'js')]);
                if (cluster) {
                    await Promise.all([load(assets.clusterCss, 'css'), load(assets.clusterJs, 'js')]);
                }
            } catch {
                this.mapEl.classList.remove('is-loading');
                return;
            }

            this.mapEl.classList.remove('is-loading');

            const L = window.L;
            const canvas = document.createElement('div');
            canvas.className = 'gm-map__canvas';
            this.mapEl.append(canvas);

            this.map = L.map(canvas, { scrollWheelZoom: false });
            this.map.attributionControl.setPrefix(false);

            const { url, ...options } = tiles;
            L.tileLayer(url, { detectRetina: false, ...options }).addTo(this.map);

            this.icon = this.config.marker
                ? L.icon({
                    iconUrl: this.config.marker.url,
                    iconSize: [this.config.marker.size, this.config.marker.size],
                    iconAnchor: [this.config.marker.size / 2, this.config.marker.size],
                    popupAnchor: [0, -this.config.marker.size],
                })
                : L.divIcon({ className: 'gm-pin', html: '<span></span>', iconSize: [28, 36], iconAnchor: [14, 36], popupAnchor: [0, -32] });

            this.layer = cluster && L.markerClusterGroup
                ? L.markerClusterGroup({
                    showCoverageOnHover: false,
                    maxClusterRadius: 70,
                    spiderfyDistanceMultiplier: 1.5,
                    iconCreateFunction: (c) => {
                        const count = c.getChildCount();
                        const size = count < 10 ? 's' : count < 50 ? 'm' : 'l';
                        return L.divIcon({ className: `gm-cluster gm-cluster--${size}`, html: `<span>${count}</span>`, iconSize: null });
                    },
                })
                : L.featureGroup();
            this.layer.addTo(this.map);

            this.markers = new Map();
            this.items.filter((item) => item.point).forEach((item) => {
                const marker = L.marker(item.point, { icon: this.icon, title: item.el.querySelector('.gm-card__title')?.textContent ?? '' });
                marker.bindPopup(() => this.popup(item), { maxWidth: 320, minWidth: 220 });
                marker.on('popupopen', () => this.highlight(item));
                marker.on('popupclose', () => this.highlight(null));
                this.markers.set(item.id, marker);
            });

            this.updateMap(true);
        }

        popup(item) {
            const wrap = document.createElement('div');
            wrap.className = 'gm-popup';
            wrap.append(item.el.querySelector('[data-gm-card-body]').cloneNode(true));
            const meta = item.el.querySelector('.gm-card__meta')?.cloneNode(true);
            if (meta) {
                meta.querySelector('[data-gm-show]')?.remove();
                wrap.append(meta);
            }
            return wrap;
        }

        highlight(item) {
            this.items.forEach((i) => i.el.classList.toggle('is-active', i === item));
        }

        updateMap(fit) {
            if (!this.map) {
                return;
            }

            const L = window.L;
            const markers = (this.shown || []).map((item) => this.markers.get(item.id)).filter(Boolean);

            this.layer.clearLayers();
            if (this.layer.addLayers) {
                this.layer.addLayers(markers);
            } else {
                markers.forEach((m) => this.layer.addLayer(m));
            }

            this.originLayer?.remove();
            this.originLayer = null;

            const { origin } = this.state;
            let bounds = markers.length ? L.latLngBounds(markers.map((m) => m.getLatLng())) : null;

            if (origin) {
                this.originLayer = L.layerGroup([
                    L.circle(origin, { radius: this.state.radius * 1000, className: 'gm-radius', interactive: false }),
                    L.circleMarker(origin, { radius: 7, className: 'gm-origin', interactive: false }),
                ]).addTo(this.map);
                bounds = bounds ? bounds.extend(origin) : L.latLngBounds([origin, origin]);
            }

            if (!fit && this.mapFitted) {
                return;
            }

            const { zoom } = this.config;

            if (bounds && zoom && !origin && !this.state.country && !this.state.category) {
                this.map.setView(bounds.getCenter(), zoom);
            } else if (bounds) {
                this.map.fitBounds(bounds.pad(0.1), { maxZoom: origin ? 12 : 10 });
            } else {
                this.map.setView([51, 10], 5);
            }
            this.mapFitted = true;
        }

        focus(item) {
            const marker = this.markers?.get(item.id);
            this.mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

            if (!marker) {
                this.mapEl.querySelector('[data-gm-consent-accept]')?.focus();
                return;
            }

            const open = () => {
                if (this.layer.zoomToShowLayer) {
                    this.layer.zoomToShowLayer(marker, () => marker.openPopup());
                } else {
                    marker.openPopup();
                }
            };

            if (this.map.getZoom() < 14) {
                this.map.once('moveend', open);
                this.map.setView(marker.getLatLng(), 14);
            } else {
                open();
            }
        }
    }

    const init = () => document.querySelectorAll('[data-gm]:not([data-gm-ready])').forEach((root) => {
        root.dataset.gmReady = '1';
        new GratMap(root);
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
