(() => {
    const field = (name) => document.getElementById(`ctrl_${name}`);
    const num = (input) => parseFloat(String(input?.value ?? '').replace(',', '.'));

    const init = () => {
        document.querySelectorAll('[data-gm-picker]:not([data-gm-ready])').forEach((root) => {
            if (typeof L === 'undefined') {
                return;
            }

            root.dataset.gmReady = '1';

            const lat = field('latitude');
            const lng = field('longitude');
            const status = root.querySelector('[data-gm-status]');
            const hasPoint = () => !Number.isNaN(num(lat)) && !Number.isNaN(num(lng));

            const map = L.map(root.querySelector('[data-gm-picker-map]'), { scrollWheelZoom: false });
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }).addTo(map);

            const marker = L.marker([51, 10], { draggable: true });

            const place = (latlng, zoom) => {
                marker.setLatLng(latlng);
                if (!map.hasLayer(marker)) {
                    marker.addTo(map);
                }
                map.setView(latlng, zoom ?? Math.max(map.getZoom(), 14));
            };

            const write = (latlng) => {
                lat.value = latlng.lat.toFixed(7);
                lng.value = latlng.lng.toFixed(7);
                lat.dispatchEvent(new Event('change', { bubbles: true }));
                lng.dispatchEvent(new Event('change', { bubbles: true }));
            };

            if (hasPoint()) {
                place([num(lat), num(lng)], 14);
            } else {
                map.setView([51, 10], 5);
            }

            map.on('click', (e) => {
                place(e.latlng);
                write(e.latlng);
            });

            marker.on('dragend', () => write(marker.getLatLng()));

            [lat, lng].forEach((input) => input?.addEventListener('change', () => {
                if (hasPoint()) {
                    place([num(lat), num(lng)]);
                }
            }));

            root.querySelector('[data-gm-geocode]')?.addEventListener('click', async () => {
                const params = new URLSearchParams({
                    street: field('street')?.value ?? '',
                    postal: field('postal')?.value ?? '',
                    city: field('city')?.value ?? '',
                    country: field('country')?.value ?? '',
                });

                status.textContent = '…';

                try {
                    const response = await fetch(`${root.dataset.gmGeocodeUrl}?${params}`, { headers: { Accept: 'application/json' } });
                    if (!response.ok) {
                        throw new Error();
                    }
                    const data = await response.json();
                    const latlng = L.latLng(data.lat, data.lng);
                    place(latlng, 16);
                    write(latlng);
                    status.textContent = data.label;
                } catch {
                    status.textContent = root.dataset.gmTextNotfound;
                }
            });

            setTimeout(() => map.invalidateSize(), 200);
        });
    };

    document.addEventListener('DOMContentLoaded', init);
    document.addEventListener('turbo:load', init);
    document.addEventListener('turbo:render', init);
    init();
})();
