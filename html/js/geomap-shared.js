/**
 * --- ADDED FILE: shared map rendering
 * 
 * geomap-shared.js
 *
 * Shared rendering for the fullscreen geographic map (resources/views/map/fullscreen.blade.php)
 * and the world map dashboard widget (resources/views/widgets/world-map.blade.php).
 *
 * Load after leaflet.js, leaflet.markercluster.js and leaflet.awesome-markers.min.js.
 * 
 * Styles: resources/views/map/partials/geomap-shared-styles.blade.php
 *
 * Usage:
 *   const geomap = LnmsGeoMap.create(map, { variant: 'full' | 'compact', ... });
 *   geomap.setDevices(data);   // response from maps.getdevices
 *   geomap.setGeoLinks(data);  // response from maps.getgeolinks
 */
window.LnmsGeoMap = window.LnmsGeoMap || (function () {
    'use strict';

    // ~~~~~ Constants ~~~~~

    const ARROW = '\u{2194}';
    const LINK_MIN_WEIGHT = 2;
    const LINK_MAX_WEIGHT = 3.5;
    const LINK_OPACITY = 0.3;            // lower = more transparent
    const LINK_DOWN_COLOR = '#e40606';
    const LINK_RENDER_PADDING = 0.5;     // 0.5 = half a screen drawn beyond the viewport on each side
    const LINK_RENDER_THROTTLE_MS = 100;
    const SMALL_SCREEN_QUERY = '(max-width: 899.98px)'; // matches the .link-speed-filter @media rule
    const CLUSTER_CLASS = 'Cluster marker-cluster marker-cluster-small leaflet-zoom-animated leaflet-clickable';
    
    // ~~ Filters ~~
    
    const LINK_SPEED_BUCKETS = [
        { key: '1G',   label: '<10G', min: 0,            color: getSpeedColor(0) },
        { key: '10G',  label: '10G',  min: 10000000000,  color: getSpeedColor(10e9) },
        { key: '25G',  label: '25G',  min: 25000000000,  color: getSpeedColor(25e9) },
        { key: '40G',  label: '40G',  min: 40000000000,  color: getSpeedColor(40e9) },
        { key: '100G', label: '100G', min: 100000000000, color: getSpeedColor(100e9) },
    ];
    const OS_FILTERS = [ // Device OS filter: os = OS display name (os_text from maps.getdevices), label = text shown on the chip
        { os: 'HP ProCurve',        label: 'Aruba' },
        { os: 'ArubaOS-CX',         label: 'Aruba CX' },
        { os: 'IP Office Firmware', label: 'Avaya' },
        { os: 'Juniper JunOS',      label: 'Juniper' },
        { os: 'Ping only',          label: 'Facilities' },
    ];

    // ~~~~~ Stateless helpers ~~~~~

    // ~~ Utility ~~

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    // ~~ Link color and width calculations ~~

    function clamp(num, min, max) {
        return Math.max(min, Math.min(max, num));
    }

    // matches MapDataController::linkUseColourCapability()
    function getSpeedColor(bps) {
        const gbps = bps / 1000000000;
        if (gbps >= 100) return '#0BD25A'; // 100G green
        if (gbps >= 40)  return '#DA5DE4'; // 40G pink
        if (gbps >= 25)  return '#4F6CA9'; // 25G blue
        if (gbps >= 10)  return '#7A5125'; // 10G brown
        return '#C9750F';                  // <10G orange
    }

    function getSpeedBucket(bps) {
        const n = Number(bps || 0);
        for (let i = LINK_SPEED_BUCKETS.length - 1; i >= 0; i--) {
            if (n >= LINK_SPEED_BUCKETS[i].min) return LINK_SPEED_BUCKETS[i].key;
        }
        return LINK_SPEED_BUCKETS[0].key;
    }

    function formatBps(bps) {
        const n = Number(bps || 0);
        const gbps = n / 1000000000;
        const mbps = n / 1000000;
        const text = gbps >= 1
            ? `${gbps % 1 === 0 ? gbps.toFixed(0) : gbps.toFixed(1)}G`
            : `${mbps % 1 === 0 ? mbps.toFixed(0) : mbps.toFixed(1)}M`;
        return `<span style="color:${getSpeedColor(n)}; font-weight:600;">${text}</span>`;
    }

    function formatStatus(isUp) {
        const text = isUp ? 'up' : 'down';
        const color = isUp ? '#00A65A' : LINK_DOWN_COLOR;
        return `<span style="color:${color}; font-weight:600;">${text}</span>`;
    }

    // matches MapDataController::linkSpeedWidth()
    function linkSpeedWidth(bps) {
        const s = Number(bps || 0) / 10000000;
        if (!Number.isFinite(s) || s < 1) {
            return 1;
        }
        return String(Math.round(s)).length * 2;
    }

    function getMidpoint(latlngs) {
        const a = latlngs[0];
        const b = latlngs[1];
        return L.latLng((a.lat + b.lat) / 2, (a.lng + b.lng) / 2);
    }

    //  ~~ Order links and devices ~~

    function compareLinkMembers(a, b) {
        if (!!a.up !== !!b.up) {
            return a.up ? 1 : -1; // down first
        }
        return Number(b.speed || 0) - Number(a.speed || 0); // then highest speed
    }

    function compareDeviceNames(a, b) {
        return String(a.sname ?? a.hostname ?? '').localeCompare(
            String(b.sname ?? b.hostname ?? ''),
            undefined,
            { numeric: true, sensitivity: 'base' }
        );
    }

    // ~~ Pop-up content ~~

    function createGraphURL(device_id = null, port_id = null, type = 'port_bits', time = '-1d', legend = 'no', width = 300, height = 150) {
        const refresh = 0;
        if (device_id === null) {
            return `graph.php?from=${time}&id=${port_id}&type=${type}&legend=${legend}&absolute_size=0&width=${width}&height=${height}&refreshnum=${refresh}`;
        }
        return `graph.php?from=${time}&device=${device_id}&type=${type}&legend=${legend}&absolute_size=0&width=${width}&height=${height}&refreshnum=${refresh}`;
    }

    function createDeviceHREF(device_id) {
        return `device/${device_id}`;
    }

    function createPortHREF(device_id, port_id) {
        return `device/device=${device_id}/tab=port/port=${port_id}/`;
    }

    function hexToRgba(hex, alpha = 0.35) {
        if (!hex) {
            return `rgba(0, 0, 0, ${alpha})`;
        }
        let value = String(hex).trim();
        if (value.startsWith('rgba(') || value.startsWith('rgb(')) {
            return value;
        }
        value = value.replace('#', '');
        if (value.length === 3) {
            value = value.split('').map(ch => ch + ch).join('');
        }
        // ~ source: https://www.frontendgeek.com/blogs/how-to-convert-hex-to-rgb-in-javascript#gsc.tab=0
        const r = parseInt(value.substring(0, 2), 16);
        const g = parseInt(value.substring(2, 4), 16);
        const b = parseInt(value.substring(4, 6), 16);
        // ~
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    function getFontColor(hex) {
        if (!hex) {
            return '#030303c9';
        }
        const rgb = hexToRgba(hex).match(/\d+/g); // source: https://stackoverflow.com/questions/3751877/how-to-extract-r-g-b-a-values-from-css-color
        const r = rgb[0];
        const g = rgb[1];
        const b = rgb[2];
        // ~ source: https://www.codegenes.net/blog/how-to-decide-font-color-in-white-or-black-depending-on-background-color/#practical-implementation-code-examples
        const linearize = (s) => {
            s /= 255;
            return s <= 0.03928 ? s / 12.92 : Math.pow((s + 0.055) / 1.055, 2.4);
        };
        const luminance = 0.2126 * linearize(r) + 0.7152 * linearize(g) + 0.0722 * linearize(b);
        const contrastBlack = (luminance + 0.05) / 0.05;
        const contrastWhite = 1.05 / (luminance + 0.05);
        return contrastBlack > contrastWhite ? 'black' : 'white';
        // ~
    }

    // ~~~~~ Icons (cached and shared across map instances) ~~~~~

    const deviceIconCache = Object.create(null);
    const overlapIconCache = Object.create(null);

    function getDeviceIcon(device) {
        const markerColor = device.status ? 'green' : (device.maintenance == 1 ? 'blue' : 'red');
        const isNetworkWired = device.typeIcon === 'network-wired' && device.icon;
        const key = isNetworkWired
            ? `nwimg|${device.icon}|${markerColor}`
            : `${device.typeIcon}|${markerColor}`;
        if (!deviceIconCache[key]) {
            if (isNetworkWired) {
                deviceIconCache[key] = L.divIcon({
                    className: `awesome-marker awesome-marker-icon-${markerColor}`,
                    html: `
                    <div style="
                        position:absolute;
                        top:6px; left:6px; right:6px;
                        width:22px; height:22px;
                        margin:0 auto;
                        border-radius:50%;
                        overflow:hidden;
                        display:flex; align-items:center; justify-content:center;">
                        <img src="${escapeHtml(device.icon)}" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                    </div>
                    `,
                    iconSize: [35, 45],
                    iconAnchor: [17, 42],
                    popupAnchor: [1, -32],
                });
            } else {
                deviceIconCache[key] = L.AwesomeMarkers.icon({
                    icon: device.typeIcon,
                    markerColor,
                    prefix: 'fa',
                    iconColor: 'white'
                });
            }
        }
        return deviceIconCache[key];
    }

    // Up: 0, Down + Maintenance: 5000, Down: 10000 (down devices drawn on top)
    function getDeviceZIndexOffset(device) {
        if (device.status) {
            return 0;
        }
        return device.maintenance == 1 ? 5000 : 10000;
    }

    function makeOverlapIcon(count, bgColor) {
        return L.divIcon({
            className: 'link-overlap-count',
            html: `<div style="
                background:${hexToRgba(bgColor, 0.35)};
                color: ${getFontColor(bgColor)};
                line-height: 18px;
                text-align: center;
                border-radius: 50%;
                font-size: 10px;
            ">${count}</div>`,
            iconSize: [18, 18],
            iconAnchor: [11, 11],
        });
    }

    // Reuse badge icons; setIcon() rebuilds the badge's DOM element, so only call it when the badge actually changed
    function getOverlapIcon(count, bgColor) {
        const key = `${count}|${bgColor}`;
        return overlapIconCache[key] || (overlapIconCache[key] = makeOverlapIcon(count, bgColor));
    }

    function clusterIcon(cluster) {
        const markers = cluster.getAllChildMarkers();
        let color = 'green';
        for (let i = 0; i < markers.length; i++) {
            const deviceData = markers[i].deviceData;
            if (!deviceData) {
                continue;
            }
            if (!deviceData.status && deviceData.maintenance != 1) {
                color = 'red';
                break;
            }
            if (deviceData.maintenance == 1) {
                color = 'blue';
            }
        }
        return L.divIcon({ html: cluster.getChildCount(), className: color + CLUSTER_CLASS, iconSize: L.point(40, 40) });
    }

    // ~~~~~ Popup / tooltip content ~~~~~
    // variant 'full'    = fullscreen map: hrefs to device & port pages, IP address
    // variant 'compact' = widget: no hrefs links, no IP address

    function deviceGraphSet(device, device_id) {
        if (device.icontitle === 'avaya') {
            return [
                { title: 'Device Traffic', type: 'device_bits', href: `graphs/device=${device_id}/type=device_bits/legend=yes/` },
            ];
        }
        if (device.icontitle === 'ping') {
            return [
                { title: 'ICMP Response', type: 'device_ping_perf', href: `graphs/device=${device_id}/type=device_ping_perf/legend=yes/` },
            ];
        }
        return [
            { title: 'Device Traffic',  type: 'device_bits',      href: `graphs/device=${device_id}/type=device_bits/legend=no/` },
            { title: 'Processor Usage', type: 'device_processor', href: `device/device=${device_id}/tab=health/metric=processor/` },
            { title: 'Memory Usage',    type: 'device_mempool',   href: `device/device=${device_id}/tab=health/metric=mempool/` },
        ];
    }

    function createMarkerHTML(device, device_id, variant) {
        const full = variant === 'full';
        const graphs = deviceGraphSet(device, device_id);
        const name = escapeHtml(device.sname ?? device_id);

        const title = (full && device.url)
            ? `<a href="${escapeHtml(device.url)}" target="_blank" rel="noopener">${name}</a>`
            : `<span>${name}</span>`;

        const ip = full
            ? `<div class="device-popup__ip">${escapeHtml((device.ip ?? device.hostname) ?? 'no hostname or IP found')}</div>`
            : '';

        const graphHTML = graphs.map(graph => {
            const img = `<img class="graph-image" src="${escapeHtml(createGraphURL(device_id, null, graph.type, '-1d', 'no', 400, 180))}">`;
            const body = full
                ? `<a class="device-popup__graph-link" href="${escapeHtml(graph.href)}" target="_blank" rel="noopener">${img}</a>`
                : img;
            return `
                <div class="device-popup__graph">
                    <div class="device-popup__graph-title">${graph.title}</div>
                    ${body}
                </div>`;
        }).join('');

        return `
            <div class="device-popup device-popup--${graphs.length} device-popup--${variant}">
                <div class="device-popup__title">${title}</div>
                ${ip}
                <div class="device-popup__graphs">${graphHTML}</div>
            </div>`;
    }

    function createLinkTitle(link, full, coords = link) { // coords: object with local/remote lat/lng, used when device names are missing
        if (link.local_name && link.remote_name) {
            const local = escapeHtml(link.local_name);
            const remote = escapeHtml(link.remote_name);
            if (full && link.local_device_id && link.remote_device_id) {
                return `<div class="link-popup__title"><a href="${escapeHtml(createDeviceHREF(link.local_device_id))}" target="_blank" rel="noopener">${local}</a> ${ARROW} <a href="${escapeHtml(createDeviceHREF(link.remote_device_id))}" target="_blank" rel="noopener">${remote}</a></div>`;
            }
            return `<div class="link-popup__title">${local} ${ARROW} ${remote}</div>`;
        }
        return `<div class="link-popup__title">${escapeHtml(coords.local_lat)},${escapeHtml(coords.local_lng)} ${ARROW} ${escapeHtml(coords.remote_lat)},${escapeHtml(coords.remote_lng)}</div>`;
    }

    function createLinkHTML(view, variant) { // view = link group with members already filtered by speed and sorted (primary first)
        const full = variant === 'full';
        const members = view.members;
        const primary = members[0];

        const portGraph = (device_id, port_id) => {
            const img = `<img class="graph-image" src="${escapeHtml(createGraphURL(null, port_id, 'port_bits', '-1d', 'yes'))}">`;
            return full
                ? `<a href="${escapeHtml(createPortHREF(device_id, port_id))}" target="_blank" rel="noopener">${img}</a>`
                : img;
        };

        let graph = '';
        if (primary.local_device_id && primary.local_port_id) {
            graph = `<br>${portGraph(primary.local_device_id, primary.local_port_id)}`;
        } else if (primary.remote_device_id && primary.remote_port_id) {
            graph = `<br>${portGraph(primary.remote_device_id, primary.remote_port_id)}`;
        }

        const html = `<div style="font-size:18px;text-align:center;">${createLinkTitle(primary, full, view)}${graph}</div>`;

        if (members.length <= 1) {
            return html;
        }

        const otherMembers = members.slice(1).map(member => {
            const portHREF = escapeHtml(createPortHREF(member.local_device_id, member.local_port_id));
            const meta = full
                ? `&nbsp; <a href="${escapeHtml(`graphs/to=0/id=${member.local_port_id}/type=port_bits/from=0/`)}" target="_blank" rel="noopener">${formatBps(member.speed)}</a>
                   &nbsp;|&nbsp; <a href="${portHREF}" target="_blank" rel="noopener">${formatStatus(member.up)}</a>`
                : `&nbsp; ${formatBps(member.speed)}
                   &nbsp;|&nbsp; ${formatStatus(member.up)}`;
            return `
                <div style="margin-bottom:8px;">
                    ${createLinkTitle(member, full, view)}
                    <div class="overlap-member-meta">${meta}</div>
                </div>`;
        }).join('');

        return `
            ${html}
            <details style="margin-top:10px;">
                <summary class="overlap-summary">
                    Other overlapping links (${members.length - 1})
                </summary>
                <div class="overlap-members-list" style="margin-top:10px;">
                    ${otherMembers}
                </div>
            </details>`;
    }

    function createLinkTooltip(view) {
        const primary = view.members[0];
        return (primary.local_name && primary.remote_name)
            ? `${escapeHtml(primary.local_name)} ${ARROW} ${escapeHtml(primary.remote_name)}`
            : `${escapeHtml(view.local_lat)},${escapeHtml(view.local_lng)} ${ARROW} ${escapeHtml(view.remote_lat)},${escapeHtml(view.remote_lng)}`;
    }

    // ~~~~~ Map instance creation ~~~~~

    /**
     * @param {L.Map} map
     * @param {object} options
     *   variant             'full' (fullscreen) | 'compact' (widget)
     *   clusterRadius       markercluster maxClusterRadius
     *   geoLinks            render xDP geographic links
     *   linkContainer       map or L.LayerGroup that link lines are added to
     *   filterAlwaysVisible true: speed filter always shown; false: only when links exist
     *   resetView           add the reset view button
     *   devicePopupOptions  function returning extra Leaflet popup options for devices (re-applied every setDevices)
     *   linkPopupOptions    function returning extra Leaflet popup options for links (applied on creation)
     */
    function create(map, options) {
        const opts = Object.assign({
            variant: 'full',
            clusterRadius: 80,
            geoLinks: false,
            linkContainer: map,
            filterAlwaysVisible: true,
            resetView: true,
            devicePopupOptions: () => ({}),
            linkPopupOptions: () => ({}),
        }, options);
        const variant = opts.variant === 'compact' ? 'compact' : 'full';

        map.getContainer().dataset.theme = String(window.siteStyle).toLowerCase().includes('dark') ? 'dark' : 'light';

        // Per-instance state
        const deviceMarkers = Object.create(null);
        const linkLines = Object.create(null);
        const overlapLabels = Object.create(null);
        const speedVisible = Object.create(null);
        LINK_SPEED_BUCKETS.forEach(({ key }) => { speedVisible[key] = true; });
        let geoLinkData = Object.create(null);
        let filterEl = null;
        const osVisible = Object.create(null);
        OS_FILTERS.forEach(({ os }) => { osVisible[os] = true; });
        let filterBarControl = null;
        let osFilterEl = null;
        let osFilterSig = '';
        let linkRenderers = null;
        let smallScreenQuery = null;
        let onSmallScreenChange = null;

        // ~~ Device markers ~~

        const markerCluster = L.markerClusterGroup({
            maxClusterRadius: opts.clusterRadius,
            iconCreateFunction: clusterIcon,
        });
        map.addLayer(markerCluster);
        map.markerCluster = markerCluster; // kept on the map object for any code that looks for it there

        // Graphs built when pop-up opened, from the marker's current device data
        function devicePopupContent(marker) {
            return createMarkerHTML(marker.deviceData, marker.deviceData.id, variant);
        }

        function devicePopupOptions() {
            return Object.assign({}, opts.devicePopupOptions(), { className: 'marker-style' });
        }

        // Update existing markers in place, add new ones, remove missing ones
        function setDevices(data) {
            const seen = Object.create(null);
            const popupOptions = devicePopupOptions();
            const devices = Object.values(data || {}).sort(compareDeviceNames);

            devices.forEach((device) => {
                const id = String(device.id);
                seen[id] = true;

                const icon = getDeviceIcon(device);
                const zIndexOffset = getDeviceZIndexOffset(device);
                const latlng = L.latLng(device.lat, device.lng);
                let marker = deviceMarkers[id];

                if (marker) {
                    // Only update items changed (setLatLng re-clusters the marker, setIcon rebuilds its DOM)
                    if (!marker.getLatLng().equals(latlng)) {
                        marker.setLatLng(latlng);
                    }
                    if (marker.options.icon !== icon) {
                        marker.setIcon(icon);
                    }
                    if (marker.options.zIndexOffset !== zIndexOffset) {
                        marker.setZIndexOffset(zIndexOffset);
                    }
                    marker.options.title = device.sname;
                    Object.assign(marker.getPopup().options, popupOptions);
                } else {
                    marker = L.marker(latlng, { title: device.sname, icon, zIndexOffset });
                    marker.bindPopup(devicePopupContent, popupOptions);
                    deviceMarkers[id] = marker;
                }
                marker.deviceData = device;
            });

            const removed = [];
            for (const id in deviceMarkers) {
                if (!(id in seen)) {
                    if (markerCluster.hasLayer(deviceMarkers[id])) {
                        removed.push(deviceMarkers[id]);
                    }
                    delete deviceMarkers[id];
                }
            }
            if (removed.length) {
                markerCluster.removeLayers(removed);
            }

            syncOsFilter(devices);
            applyOsFilter(); // adds new markers / shows or hides existing ones per the OS filter
            markerCluster.refreshClusters();
        }

        // ~~ Filter bar (bottom-left): link speed filter, OS filter to its right ~~
        
        function ensureFilterBar() {
            if (!filterBarControl) {
                const FilterBarControl = L.Control.extend({
                    options: { position: 'bottomleft' },
                    onAdd: function () {
                        const container = L.DomUtil.create('div', 'geomap-filters leaflet-control');
                        L.DomEvent.disableClickPropagation(container);
                        L.DomEvent.disableScrollPropagation(container);
                        return container;
                    },
                });
                filterBarControl = new FilterBarControl();
                map.addControl(filterBarControl);
            }
            return filterBarControl.getContainer();
        }

        // ~~ Device OS filter ~~

        function applyOsFilter() {
            const markers = Object.values(deviceMarkers)
                .sort((a, b) => compareDeviceNames(a.deviceData, b.deviceData)); // numeric id keys iterate by id, not name
            const isVisible = m => osVisible[m.deviceData.os_text] !== false; // OSes not in OS_FILTERS always show

            const toRemove = markers.filter(m => !isVisible(m) && markerCluster.hasLayer(m));
            if (toRemove.length) {
                markerCluster.removeLayers(toRemove);
            }

            // re-add the whole visible set in name order
            const visible = markers.filter(isVisible);
            if (visible.some(m => !markerCluster.hasLayer(m))) {
                markerCluster.clearLayers();
                markerCluster.addLayers(visible);
            }
        }

        function setOsVisible(os, visible, apply = true) {
            osVisible[os] = visible;
            const chip = osFilterEl?.querySelector(`[data-os="${CSS.escape(os)}"]`);
            if (chip) {
                chip.classList.toggle('is-active', visible);
                chip.setAttribute('aria-pressed', String(visible));
            }
            if (apply) {
                applyOsFilter();
            }
        }

        function createOsFilterEl(options) {
            const container = L.DomUtil.create('div', 'link-speed-filter os-filter');
            container.setAttribute('role', 'group');
            container.setAttribute('aria-label', 'Filter devices by type');

            options.forEach(({ os, label, icon }) => {
                const chip = L.DomUtil.create('button', 'link-speed-filter__chip', container);
                chip.type = 'button';
                chip.dataset.os = os;
                chip.classList.toggle('is-active', osVisible[os]);
                chip.setAttribute('aria-pressed', String(osVisible[os]));

                if (icon) {
                    const img = L.DomUtil.create('img', 'link-speed-filter__icon', chip);
                    img.src = icon;
                    img.alt = '';
                }
                chip.appendChild(document.createTextNode(label));

                L.DomEvent.on(chip, 'click', function (e) {
                    L.DomEvent.stop(e);
                    setOsVisible(os, !osVisible[os]);
                });
            });
            return container;
        }

        // Show only the types present; hide the filter if fewer than 2 are present
        function syncOsFilter(devices) { // devices sorted by name, so the icon comes from the first device of each type
            const present = [];
            OS_FILTERS.forEach((filter) => {
                const device = devices.find(d => d.os_text === filter.os);
                if (device) {
                    present.push({ ...filter, icon: device.icon });
                } else {
                    osVisible[filter.os] = true; // absent types come back visible
                }
            });

            const show = present.length > 1;
            if (!show) {
                OS_FILTERS.forEach(({ os }) => { osVisible[os] = true; }); // no filter shown: nothing will stay hidden
            }

            // Rebuild the chips only when the set of types (or their icons) changed
            const sig = show ? present.map(p => `${p.os}|${p.icon}`).join('\n') : '';
            if (sig === osFilterSig) {
                return;
            }
            osFilterSig = sig;

            const next = show ? createOsFilterEl(present) : null;
            if (osFilterEl) {
                next ? osFilterEl.replaceWith(next) : osFilterEl.remove();
            } else if (next) {
                ensureFilterBar().appendChild(next); // appended = right of the speed filter
            }
            osFilterEl = next;
        }

        // ~~ Reset view control ~~

        if (opts.resetView) {
            const initialView = { center: map.getCenter(), zoom: map.getZoom() };
            const ResetViewControl = L.Control.extend({
                options: { position: 'bottomright' },
                onAdd: function () {
                    const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control wm-reset-view');
                    container.style.width = '52px';
                    container.style.height = '52px';
                    container.style.display = 'flex';
                    container.style.alignItems = 'center';
                    container.style.justifyContent = 'center';
                    container.style.cursor = 'pointer';
                    container.innerHTML = '\u{21BA}';
                    container.style.fontSize = '26px';
                    container.title = 'Reset view';

                    L.DomEvent.disableClickPropagation(container);
                    L.DomEvent.disableScrollPropagation(container);

                    container.onclick = function () {
                        map.setView(initialView.center, initialView.zoom, { animate: true });
                        map.closePopup();
                        markerCluster.unspiderfy();
                    };
                    return container;
                },
            });
            map.addControl(new ResetViewControl());
        }

        // ~~ Link line zoom scaling () ~~
        // note: also used by the fullscreen "depends" lines

        function setLineBase(line, rawWeight) {
            if (!line || !line.options) return;
            line.options.__lnmsBaseWeight = rawWeight ?? line.options.weight ?? 1;
            line.options.__lnmsBaseZoom = map.getZoom();
        }

        function rescaleLine(line) {
            if (!line || typeof line.setStyle !== 'function' || !line.options) return;
            if (line.options.__lnmsBaseWeight === undefined) {
                setLineBase(line);
            }
            const scale = map.getZoomScale(map.getZoom(), line.options.__lnmsBaseZoom);
            const newWeight = clamp(line.options.__lnmsBaseWeight * Math.pow(scale, 0.5), LINK_MIN_WEIGHT, LINK_MAX_WEIGHT);
            if (line.options.weight !== newWeight) { // skip no-op redraws (weights clamp to a narrow range)
                line.setStyle({ weight: newWeight });
            }
        }

        // ~~ Geographic links ~~

        function getVisibleMembers(link) {
            const members = Array.isArray(link.members) && link.members.length ? link.members : [link];
            return members
                .filter(m => speedVisible[getSpeedBucket(m.speed)])
                .sort(compareLinkMembers);
        }

        function updateOverlapLabel(link_id, latlngs, count, bgColor) {
            if (count <= 1) {
                if (overlapLabels[link_id]) {
                    map.removeLayer(overlapLabels[link_id]);
                    delete overlapLabels[link_id];
                }
                return;
            }
            const midpoint = getMidpoint(latlngs);
            const icon = getOverlapIcon(count, bgColor);
            const existing = overlapLabels[link_id];
            if (existing) {
                if (!existing.getLatLng().equals(midpoint)) {
                    existing.setLatLng(midpoint);
                }
                if (existing.options.icon !== icon) {
                    existing.setIcon(icon);
                }
            } else {
                const marker = L.marker(midpoint, {
                    pane: 'linkLabelsPane',
                    icon,
                    interactive: true,
                    keyboard: false,
                });
                marker.on('click', function () {
                    const line = linkLines[link_id];
                    if (line && line.getPopup()) {
                        line.openPopup(marker.getLatLng());
                    }
                });
                marker.addTo(map);
                overlapLabels[link_id] = marker;
            }
        }

        function linkTooltipContent(layer) {
            return createLinkTooltip(layer._lnmsView);
        }

        function linkPopupContent(layer) {
            return createLinkHTML(layer._lnmsView, variant);
        }

        function renderGeoLinks() {
            if (!opts.geoLinks) {
                return;
            }
            const rendered = Object.create(null);

            Object.entries(geoLinkData).forEach(([link_id, link]) => {
                const members = getVisibleMembers(link);
                if (!members.length) {
                    return; // every member filtered out; removed below
                }
                rendered[link_id] = true;

                const primary = members[0];
                const isUp = !!primary.up;
                const pane = isUp ? 'linksUpPane' : 'linksDownPane';
                const width = linkSpeedWidth(members.reduce((sum, m) => sum + Number(m.speed || 0), 0));
                const weight = clamp(width, LINK_MIN_WEIGHT, LINK_MAX_WEIGHT);

                let line = linkLines[link_id];

                // A layer's pane can't change after creation: rebuild if up/down state flipped
                if (line && line.options.pane !== pane) {
                    opts.linkContainer.removeLayer(line);
                    delete linkLines[link_id];
                    line = null;
                }

                // Group unchanged since the last render: only re-base the zoom scaling, skip everything else
                const sig = JSON.stringify([link.local_lat, link.local_lng, link.remote_lat, link.remote_lng, members]);
                if (line && line._lnmsSig === sig) {
                    setLineBase(line, width);
                    if (line.options.weight !== weight) {
                        line.setStyle({ weight });
                    }
                    return;
                }

                const color = isUp ? primary.color : LINK_DOWN_COLOR;
                const linkStyle = isUp
                    ? { color, weight, opacity: LINK_OPACITY, dashArray: '' }
                    : { color, weight, opacity: 0.9, dashArray: '6,6' };
                const latlngs = [
                    L.latLng(link.local_lat, link.local_lng),
                    L.latLng(link.remote_lat, link.remote_lng),
                ];

                if (line) {
                    line._lnmsView = { ...link, members };
                    line.setLatLngs(latlngs).setStyle(linkStyle);
                    // Content is generated on open, so only an open pop-up/tooltip needs refreshing
                    if (line.isPopupOpen()) {
                        line.getPopup().update();
                    }
                    if (line.isTooltipOpen()) {
                        line.getTooltip().update();
                    }
                } else {
                    line = L.polyline(latlngs, { ...linkStyle, pane, renderer: linkRenderers[pane] });
                    line._lnmsView = { ...link, members };
                    line.bindTooltip(linkTooltipContent, { className: 'leaflet-link-tooltip', sticky: true, opacity: 0.9 });
                    line.bindPopup(linkPopupContent, Object.assign({}, opts.linkPopupOptions(), { className: 'link-style' }));
                    linkLines[link_id] = line;
                    opts.linkContainer.addLayer(line);
                }
                line._lnmsSig = sig;
                setLineBase(line, width);

                const sameLocation = link.local_lat === link.remote_lat && link.local_lng === link.remote_lng;
                updateOverlapLabel(link_id, latlngs, sameLocation ? 1 : members.length, color);
            });

            // Remove lines and badges that are filtered out or no longer returned by the server
            for (const link_id in linkLines) {
                if (!(link_id in rendered)) {
                    opts.linkContainer.removeLayer(linkLines[link_id]);
                    delete linkLines[link_id];
                }
            }
            for (const link_id in overlapLabels) {
                if (!(link_id in rendered)) {
                    map.removeLayer(overlapLabels[link_id]);
                    delete overlapLabels[link_id];
                }
            }
        }

        // ~~ Link speed filter ~~

        function setSpeedVisible(key, visible, render = true) {
            speedVisible[key] = visible;
            const chip = filterEl?.querySelector(`[data-speed="${key}"]`);
            if (chip) {
                chip.classList.toggle('is-active', visible);
                chip.setAttribute('aria-pressed', String(visible));
            }
            if (render) {
                renderGeoLinks();
            }
        }

        function createSpeedFilterEl() {
            const container = L.DomUtil.create('div', 'link-speed-filter');
            container.setAttribute('role', 'group');
            container.setAttribute('aria-label', 'Filter links by speed');

            LINK_SPEED_BUCKETS.forEach(({ key, label, color }) => {
                const chip = L.DomUtil.create('button', 'link-speed-filter__chip', container);
                chip.type = 'button';
                chip.dataset.speed = key;
                chip.classList.toggle('is-active', speedVisible[key]);
                chip.setAttribute('aria-pressed', String(speedVisible[key]));

                const dot = L.DomUtil.create('span', 'link-speed-filter__dot', chip);
                dot.style.background = color;
                chip.appendChild(document.createTextNode(label)); // text node so "<10G" stays literal

                L.DomEvent.on(chip, 'click', function (e) {
                    L.DomEvent.stop(e);
                    setSpeedVisible(key, !speedVisible[key]);
                });
            });
            return container;
        }

        function syncFilterControl() {
            const show = opts.filterAlwaysVisible || Object.keys(geoLinkData).length > 0;
            if (show && !filterEl) {
                filterEl = createSpeedFilterEl();
                ensureFilterBar().prepend(filterEl); // prepended = left of the OS filter
            } else if (!show && filterEl) {
                filterEl.remove();
                filterEl = null;
            }
        }

        function setGeoLinks(data) {
            if (!opts.geoLinks) {
                return;
            }
            geoLinkData = data || Object.create(null);
            renderGeoLinks();
            syncFilterControl();
        }

        // ~~ Geographic link setup ~~

        if (opts.geoLinks) {
            map.createPane('linksUpPane').style.zIndex = 400;
            map.createPane('linksDownPane').style.zIndex = 410; // down links above up links
            // Line panes pass clicks through; each interactive SVG path re-enables pointer events (Leaflet CSS) - otherwise down-links pane covers the up-links pane and swallows the clicks
            map.getPane('linksUpPane').style.pointerEvents = 'none';
            map.getPane('linksDownPane').style.pointerEvents = 'none';
            map.createPane('linkLabelsPane').style.zIndex = 450;
            map.getPane('linkLabelsPane').style.pointerEvents = 'none';

            // Dedicated renderers for link lines
            linkRenderers = {
                linksUpPane: L.svg({ pane: 'linksUpPane', padding: LINK_RENDER_PADDING }), // padding = extra area drawn around the viewport on each side
                linksDownPane: L.svg({ pane: 'linksDownPane', padding: LINK_RENDER_PADDING }),
            };

            // Redraw the link renderers during a pan (throttled), same as how tile layers load while the map moves 
            map.on('move', L.Util.throttle(function () { 
                if (map._animatingZoom) {
                    return; // mid zoom animation: Leaflet redraws on zoomend
                }
                const zoom = map.getZoom();
                for (const pane in linkRenderers) {
                    const renderer = linkRenderers[pane];
                    if (renderer._map && renderer._zoom === zoom) {
                        renderer._update(); //renderer._update() is Leaflet-internal (used on moveend)
                    }
                }
            }, LINK_RENDER_THROTTLE_MS));

            map.on('zoomend', function () {
                for (const id in linkLines) {
                    rescaleLine(linkLines[id]);
                }
            });

            syncFilterControl();

            // Filters are hidden below 900px: don't leave links or devices hidden with no way to restore them
            smallScreenQuery = window.matchMedia(SMALL_SCREEN_QUERY);
            onSmallScreenChange = function () {
                if (!smallScreenQuery.matches) {
                    return;
                }
                if (opts.geoLinks) {
                    LINK_SPEED_BUCKETS.forEach(({ key }) => setSpeedVisible(key, true, false));
                    renderGeoLinks();
                }
                OS_FILTERS.forEach(({ os }) => setOsVisible(os, true, false));
                applyOsFilter();
            };
            smallScreenQuery.addEventListener('change', onSmallScreenChange);
        }

        // Call before the map is destroyed (widget removal)
        function destroy() {
            if (smallScreenQuery && onSmallScreenChange) {
                smallScreenQuery.removeEventListener('change', onSmallScreenChange);
            }
        }

        return {
            setDevices,
            setGeoLinks,
            rescaleLine,
            destroy,
            markerCluster,
        };
    }

    return { create, escapeHtml };
})();