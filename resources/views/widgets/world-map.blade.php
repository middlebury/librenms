<div id="worldmap_widget-{{ $id }}" class="worldmap_widget" data-reload="false"></div>

<style>
/* --- ADDED: styling !-- */
#worldmap_widget-{{ $id }} {
  height: 100%;
  width: 100%;
  padding-bottom: 0;
  margin-bottom: 0;
  container-type: inline-size;


  /* Default theme values */
  --wm-popup-bg: #f2f2f2ee;
  --wm-popup-text: #1d1d1d;

  --wm-popup-font-size: 14px;
  --wm-popup-font-weight: bold;

  --wm-tooltip-font-size: 14px;
  --wm-popup-padding: 6px;
  --wm-link-popup-min-width: 300px;

  --wm-marker-popup-max-width: min(1260px, calc(100% - 24px));
  --wm-marker-popup-min-height: min(1260px, calc(100vw - 24px));

  --wm-graph-gap: 8px;
  --wm-graph-side-padding: 2px;
  --wm-graph-title-size: 14px;
  --wm-graph-title-margin: 0 0 6px 0;

  --wm-filter-selected: #1a56b8;
}

#worldmap_widget-{{ $id }}[data-theme="dark"] {
  --wm-popup-bg: #1d1d1ddd;
  --wm-popup-text: #d3d3d3;
  --wm-filter-selected: #5b9bff;
}

#worldmap_widget-{{ $id }}[data-theme="light"] {
  --wm-popup-bg: #f2f2f2ee;
  --wm-popup-text: #1d1d1d;
  --wm-filter-selected: #1a56b8;
}

/* Popup content */
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .leaflet-popup-content-wrapper,
#worldmap_widget-{{ $id }} .leaflet-popup.link-style .leaflet-popup-content-wrapper {
  background: var(--wm-popup-bg) !important;
  color: var(--wm-popup-text) !important;
  font-size: var(--wm-popup-font-size);
  font-weight: var(--wm-popup-font-weight);
  padding: var(--wm-popup-padding);
}
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .leaflet-popup-content,
#worldmap_widget-{{ $id }} .leaflet-popup.link-style .leaflet-popup-content {
  background: transparent !important;
  color: inherit !important;
  margin: 8px !important;
}

/* Popup arrow fill */
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .leaflet-popup-tip,
#worldmap_widget-{{ $id }} .leaflet-popup.link-style .leaflet-popup-tip {
  background: var(--wm-popup-bg) !important;
  box-shadow: none !important;
}

/* Popup close button */
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .leaflet-popup-close-button,
#worldmap_widget-{{ $id }} .leaflet-popup.link-style .leaflet-popup-close-button {
  color: var(--wm-popup-text) !important;
}

/* Device popup dimensions */
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .leaflet-popup-content-wrapper {
  max-width: calc(100cqi - 16px) !important;
  max-height: calc(100dvh - 32px);
  box-sizing: border-box;
  overflow: hidden;
}
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .leaflet-popup-content {
  width: min(900px, calc(100cqi - 32px)) !important;
  max-width: calc(100cqi - 32px) !important;
  max-height: calc(100dvh - 64px);
  overflow-y: auto;
  overflow-x: hidden;
  box-sizing: border-box;
  /* -webkit-overflow-scrolling: touch; */
  margin: 8px !important;
}
/* Size large window pop-ups according to the number of graphs */
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .leaflet-popup-content:has(.device-popup--1) {
  width: min(300px, calc(100cqi - 32px)) !important;
}
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .leaflet-popup-content:has(.device-popup--2) {
  width: min(600px, calc(100cqi - 32px)) !important;
}
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .leaflet-popup-content:has(.device-popup--3) {
  width: min(900px, calc(100cqi - 32px)) !important;
}
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .device-popup {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
}
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .device-popup__title {
  text-align: center;
  margin-bottom: 10px;
}
/* Device marker graph grid */
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .device-popup__graphs {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 8px;
}
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .device-popup--1 .device-popup__graphs {
  grid-template-columns: minmax(0, 1fr);
}
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .device-popup--2 .device-popup__graphs {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .device-popup--3 .device-popup__graphs {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .device-popup__graph {
  min-width: 0;
}
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .device-popup__graph-title {
  text-align: center;
  margin-bottom: 6px;
  font-size: 12px;
  font-weight: 600;
}
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style img.graph-image {
  display: block;
  width: 100%;
  height: auto;
  aspect-ratio: 20 / 9;
}
/* 2 columns */
@container (max-width: 900px) {
  #worldmap_widget-{{ $id }} .leaflet-popup.marker-style .device-popup__graphs {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
/* 1 column */
@container (max-width: 620px) {
  #worldmap_widget-{{ $id }} .leaflet-popup.marker-style .device-popup__graphs {
    grid-template-columns: 1fr;
  }
}
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .device-popup--1 .device-popup__graphs {
    grid-template-columns: 1fr;
}

/* Link popup */
#worldmap_widget-{{ $id }} .leaflet-popup.link-style .leaflet-popup-content,
#worldmap_widget-{{ $id }} .leaflet-popup.link-style .leaflet-popup-content-wrapper {
  min-width: var(--wm-link-popup-min-width);
}
#worldmap_widget-{{ $id }} .overlap-member-meta {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
  font-weight: normal;
  margin-bottom: 4px;
}
#worldmap_widget-{{ $id }} .overlap-member-sep {
  opacity: 0.7;
}

/* Link tooltip */
#worldmap_widget-{{ $id }} .leaflet-tooltip.leaflet-link-tooltip {
  background: var(--wm-popup-bg) !important;
  color: var(--wm-popup-text) !important;
  font-size: var(--wm-tooltip-font-size) !important;
  border: 1px solid var(--wm-popup-bg);
  box-shadow: 0 1px 3px rgba(0,0,0,0.4);
}
#worldmap_widget-{{ $id }} .leaflet-tooltip-left {
  margin-left: -7px;
}
#worldmap_widget-{{ $id }} .leaflet-tooltip-right {
  margin-left: 7px;
}
#worldmap_widget-{{ $id }} .leaflet-tooltip-left:before {
  border-left-color: var(--wm-popup-bg);
}
#worldmap_widget-{{ $id }} .leaflet-tooltip-right:before {
  border-right-color: var(--wm-popup-bg);
}

/* Overlapping links dropdown */
#worldmap_widget-{{ $id }} .overlap-summary {
    cursor: pointer;
    list-style: none;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: normal;
    font-size: 15px;
}
#worldmap_widget-{{ $id }} .overlap-summary::before {
    content: "\25B6";
    font-size: 0.85em;
    line-height: 1;
}
#worldmap_widget-{{ $id }} details[open] > .overlap-summary::before {
    content: "\25BC";
}
#worldmap_widget-{{ $id }} .overlap-member {
  margin-bottom: 8px;
}
#worldmap_widget-{{ $id }} .overlap-members-list {
    text-align: left;
    max-height: 7.5em;
    overflow-y: auto;
    overflow-x: hidden;
    padding-right: 4px;
}
#worldmap_widget-{{ $id }} .link-overlap-count {
    cursor: pointer;
    opacity: 0.85;
}
#worldmap_widget-{{ $id }} .overlap-members-list .link-popup__title,
#worldmap_widget-{{ $id }} .overlap-members-list .overlap-member-meta {
  justify-content: flex-start;
  text-align: left;
  font-size: 14px;
}
#worldmap_widget-{{ $id }} .leaflet-control-locate {
  display: none !important;
}

/* Adjust refresh button by color mode */
#worldmap_widget-{{ $id }} .wm-reset-view {
  background: var(--wm-popup-bg);
  color: var(--wm-popup-text);
}
#worldmap_widget-{{ $id }} .wm-reset-view:hover {
  filter: brightness(0.92);
}
#worldmap_widget-{{ $id }}[data-theme="dark"] .wm-reset-view:hover {
  filter: brightness(1.25);
}

/* Spiderfy animation for AwesomeMarkers (they lack .leaflet-marker-icon, which markercluster's transition targets) */
#worldmap_widget-{{ $id }} .leaflet-cluster-anim .awesome-marker {
  transition: transform 0.3s ease-out, opacity 0.3s ease-in;
}

/* Link speed filter */
#worldmap_widget-{{ $id }} .link-speed-filter {
  display: flex;
  flex-direction: column;
  gap: 6px;
  padding: 6px;
  background: var(--wm-popup-bg);
  color: var(--wm-popup-text);
  border-radius: 10px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
  margin-bottom: 25px;
}
#worldmap_widget-{{ $id }} .link-speed-filter__chip {
  display: flex;
  align-items: center;
  gap: 6px;
  min-width: 70px;
  padding: 3px 10px 3px 6px;
  border: 1px solid rgba(128, 128, 128, 0.45);
  border-radius: 999px;
  background: transparent;
  color: inherit;
  font-size: 12px;
  font-weight: 600;
  line-height: 1.4;
  cursor: pointer;
}
#worldmap_widget-{{ $id }} .link-speed-filter__chip.is-active {
  border-color: var(--wm-filter-selected);
  box-shadow: inset 0 0 0 1px var(--wm-filter-selected);
}
#worldmap_widget-{{ $id }} .link-speed-filter__chip:focus-visible {
  outline: 2px solid var(--wm-filter-selected);
  outline-offset: 2px;
}
#worldmap_widget-{{ $id }} .link-speed-filter__dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  flex: none;
}
@media (max-width: 899.98px) {
  #worldmap_widget-{{ $id }} .link-speed-filter {
    display: none !important;
  }
}
/* !-- */
</style>

<script type="application/javascript">
    (function () {
        const map_id = 'worldmap_widget-{{ $id }}';
        const status = {{ Js::from($status) }};
        const device_group = {{ (int) $device_group }};
        const disabled_alerts = {{ Js::from($disabled_alerts) }};
        const map_config = {{ Js::from($map_config) }};
        const group_radius = {{ (int) $group_radius }};
        // --- ADDED !--
        const show_netmap = {{ Js::from($show_netmap ?? \LibreNMS\Config::get('network_map_show_on_worldmap')) }};
        const netmap_source = {{ Js::from($netmap_source ?? \LibreNMS\Config::get('network_map_worldmap_link_type')) }};
        document.getElementById(map_id).dataset.theme = String(window.siteStyle).toLowerCase().includes('dark') ? 'dark' : 'light';
        const enableGeoLinks = Boolean(show_netmap) && String(netmap_source).trim().toLowerCase() === 'xdp';
        // !--

        // --- ADDED: persistent device markers, updated in place each refresh instead of rebuilt (same approach as the fullscreen map) !--
        const device_markers = Object.create(null);
        const deviceIconCache = Object.create(null);

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
                            <img src="${device.icon}" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
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

        function getDeviceZIndexOffset(device) {
            if (device.status) { // up
                return 0;
            } else if (device.maintenance == 1) { // down + maintenance
                return 10000;
            }
            return 5000; // down
        }

        // Popup content is built when opened, from the marker's current device data
        function deviceMarkerPopupContent(marker) {
            return createMarkerHTML(marker.deviceData, marker.deviceData["id"]);
        }
        // !--

        function populate_map_markers(map_id, group_radius = 10, status = [0,1], device_group = 0) {
            $.ajax({
                type: "POST",
                url: '{{ route('maps.getdevices') }}',
                dataType: "json",
                data: { location_valid: 1, disabled: 0, disabled_alerts: disabled_alerts, statuses: status, group: device_group },
                success: function (data) {
                    var map = get_map(map_id);
                    if (! map.markerCluster) {
                        map.markerCluster = L.markerClusterGroup({
                            maxClusterRadius: group_radius,
                            iconCreateFunction: function (cluster) {
                                var markers = cluster.getAllChildMarkers();
                                var color = "green";
                                var newClass = "Cluster marker-cluster marker-cluster-small leaflet-zoom-animated leaflet-clickable";
                                for (var i = 0; i < markers.length; i++) {
                                    const deviceData = markers[i].deviceData; // --- ADDED
                                    // --- UPDATED !--
                                    if (!deviceData) {
                                        continue;
                                    }
                                    if (!deviceData.status && deviceData.maintenance != 1) {
                                        color = "red";
                                        break;
                                    }
                                    if (deviceData.maintenance == 1) {
                                        color = "blue";
                                    }
                                    // !--
                                }
                                return L.divIcon({
                                    html: cluster.getChildCount(),
                                    className: color + newClass,
                                    iconSize: L.point(40, 40)
                                });
                            }
                        });

                        map.addLayer(map.markerCluster);
                    }

                    // --- UPDATED: update existing markers in place, add new ones, remove missing ones !--
                    const widgetWidth = document.getElementById(map_id)?.clientWidth || 600;
                    const popupMaxWidth = Math.max(260, widgetWidth - 24);
                    const seen = Object.create(null);
                    const added = [];

                    Object.values(data)
                        .sort((deviceA, deviceB) =>
                            String(deviceA.sname ?? deviceA.hostname ?? '').localeCompare(
                                String(deviceB.sname ?? deviceB.hostname ?? ''),
                                undefined,
                                {
                                    numeric: true,
                                    sensitivity: 'base'
                                }
                            )
                        )
                        .forEach((device) => {
                            const id = String(device.id);
                            seen[id] = true;

                            const icon = getDeviceIcon(device);
                            const zIndexOffset = getDeviceZIndexOffset(device);
                            const latlng = L.latLng(device.lat, device.lng);
                            let marker = device_markers[id];

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
                                marker.getPopup().options.maxWidth = popupMaxWidth;
                            } else {
                                marker = L.marker(latlng, {
                                    title: device.sname,
                                    icon,
                                    zIndexOffset,
                                });
                                marker.bindPopup(deviceMarkerPopupContent, {
                                    className: 'marker-style',
                                    maxWidth: popupMaxWidth,
                                    autoPan: true,
                                    keepInView: true,
                                    autoPanPadding: [12, 12]
                                });
                                device_markers[id] = marker;
                                added.push(marker);
                            }
                            marker.deviceData = device;
                        });

                    const removed = [];
                    for (const id in device_markers) {
                        if (!(id in seen)) {
                            removed.push(device_markers[id]);
                            delete device_markers[id];
                        }
                    }
                    if (removed.length) {
                        map.markerCluster.removeLayers(removed);
                    }
                    if (added.length) {
                        map.markerCluster.addLayers(added);
                    }
                    // Cluster colors are computed from deviceData, which may have changed
                    map.markerCluster.refreshClusters();
                    // !--
                },
                error: function(error){
                    toastr.error(error.statusText);
                }
            });
        }

        // --- ADDED: render network links (including overlapping), graph builders (same as fullscreen map, minus HREFs) !--
        var link_markers = Object.create(null);
        var link_overlap_labels = Object.create(null);
        let linkRenderers = Object.create(null); // created once Leaflet has loaded (see loadjs callback)
        const LINK_RENDER_PADDING = 0.5;  // 0.5 = half a screen - lines are already drawn when they're panned or zoomed out into view
        const LINK_RENDER_THROTTLE_MS = 100;

        function html_chars(str) {
            return String(str).replaceAll('&', '&amp;').replaceAll("'", '&apos;')
        }

        function createGraphURL(device_id=null, port_id=null, type="port_bits", time="-1d", legend="no", width=300, height=150) {
            const refresh = 0;
            if (device_id === null) {
                return `graph.php?from=${time}&id=${port_id}&type=${type}&legend=${legend}&absolute_size=0&width=${width}&height=${height}&refreshnum=${refresh}`;
            }
            return `graph.php?from=${time}&device=${device_id}&type=${type}&legend=${legend}&absolute_size=0&width=${width}&height=${height}&refreshnum=${refresh}`;
        }

        const arrow = "\u{2194}";
        function createLinkTitle(link) {
            if (link['local_name'] && link['remote_name']) {
                return `<div class="link-popup__title">${html_chars(link['local_name'])} ${arrow} ${html_chars(link['remote_name'])}</div>`;
            }

            return `<div class="link-popup__title">${link['local_lat']},${link['local_lng']} ${arrow} ${link['remote_lat']},${link['remote_lng']}</div>`;
        }

        function createLinkHTML(link) {
            let html = '';

            const members = Array.isArray(link.members) && link.members.length ? link.members : [link];
            const primary = members[0];

            if (primary['local_device_id'] && primary['local_port_id']) {
                html = `<div style="font-size:18px;text-align:center;">${createLinkTitle(primary)}<br><img class="graph-image" src="${createGraphURL(null, primary['local_port_id'],"port_bits","-1d","yes")}"></div>`;
            }
            else if (primary['remote_device_id'] && primary['remote_port_id']) {
                html = `<div style="font-size:18px;text-align:center;">${createLinkTitle(primary)}<br><img class="graph-image" src="${createGraphURL(null, primary['remote_port_id'],"port_bits","-1d","yes")}"></div>`;
            }
            else {
                html = `<div style="font-size:18px;text-align:center;">${createLinkTitle(primary)}</div>`;
            }

            if (members.length <= 1) {
                return html;
            }

            let otherMembers = members.slice(1).map(member => {
                return `
                    <div style="margin-bottom:8px;">
                        ${createLinkTitle(member)}
                        <div class="overlap-member-meta">
                            <span>${formatBps(member.speed)}</span>
                            <span class="overlap-member-sep">|</span>
                            <span>${formatStatus(member.up)}</span>
                        </div>
                    </div>
                `;
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
                </details>
            `;
        }

        function createMarkerHTML(device, device_id) {
            if (device.icontitle === 'avaya') {
                return `<div class="device-popup device-popup--1">
                    <div class="device-popup__title">
                        <span>${html_chars(device["sname"] ?? device_id)}</span>
                    </div>

                    <div class="device-popup__graphs">

                        <div class="device-popup__graph">
                            <div class="device-popup__graph-title">Device Traffic</div>
                            <img class="graph-image"
                                src="${createGraphURL(device_id, null, "device_bits", "-1d", "no", 400, 180)}">
                        </div>

                    </div>
                </div>`;
            } else if (device.icontitle === 'ping') {
                return `<div class="device-popup device-popup--1">
                    <div class="device-popup__title">
                        <span>${html_chars(device["sname"] ?? device_id)}</span>
                    </div>

                    <div class="device-popup__graphs">

                        <div class="device-popup__graph">
                            <div class="device-popup__graph-title">ICMP Response</div>
                            <img class="graph-image"
                                src="${createGraphURL(device_id, null, "device_ping_perf", "-1d", "no", 400, 180)}">
                        </div>

                    </div>
                </div>`;
            } else {
                return `<div class="device-popup">
                    <div class="device-popup__title">
                        <span>${html_chars(device["sname"] ?? device_id)}</span>
                    </div>

                    <div class="device-popup__graphs">

                        <div class="device-popup__graph">
                            <div class="device-popup__graph-title">Device Traffic</div>
                            <img class="graph-image"
                                src="${createGraphURL(device_id, null, "device_bits", "-1d", "no", 400, 180)}">
                        </div>

                        <div class="device-popup__graph">
                            <div class="device-popup__graph-title">Processor Usage</div>
                            <img class="graph-image"
                                src="${createGraphURL(device_id, null, "device_processor", "-1d", "no", 400, 180)}">
                        </div>

                        <div class="device-popup__graph">
                            <div class="device-popup__graph-title">Memory Usage</div>
                            <img class="graph-image"
                                src="${createGraphURL(device_id, null, "device_mempool", "-1d", "no", 400, 180)}">
                        </div>

                    </div>
                </div>`;
            }
        }

        const __lnmsLinkMinWeight = 2;
        const __lnmsLinkMaxWeight = 3.5;
        const __lnmsLinkOpacity   = 0.3; // lower vals = more transparent
        function __lnmsClamp(num, min, max) {
            return Math.max(min, Math.min(max, num));
        }
        function __lnmsSetLinkBase(line, rawWeight) {
            if (!line || !line.options) return;
            line.options.__lnmsBaseWeight = rawWeight ?? line.options.weight ?? 1;
            var map = get_map(map_id);
            line.options.__lnmsBaseZoom = map.getZoom();
        }
        function __lnmsRescaleLink(line) {
            if (!line || typeof line.setStyle !== 'function' || !line.options) return;
            if (line.options.__lnmsBaseWeight === undefined) {
                __lnmsSetLinkBase(line);
            }
            var map = get_map(map_id);
            const scale = map.getZoomScale(
                map.getZoom(),
                line.options.__lnmsBaseZoom
            );
            const newWeight = __lnmsClamp(
                line.options.__lnmsBaseWeight * Math.pow(scale, 0.5),
                __lnmsLinkMinWeight,
                __lnmsLinkMaxWeight
            );
            if (line.options.weight !== newWeight) { // skip no-op redraws (weights clamp to a narrow range)
                line.setStyle({ weight: newWeight });
            }
        }
        function __lnmsRescaleAllLinks() {
            for (const id in link_markers) {
                if (Object.prototype.hasOwnProperty.call(link_markers, id)) {
                    __lnmsRescaleLink(link_markers[id]);
                }
            }
        }

        function getMidpoint(latlngs) {
            const a = latlngs[0];
            const b = latlngs[1];
            return L.latLng(
                (a.lat + b.lat) / 2,
                (a.lng + b.lng) / 2
            );
        }
        // Reuse badge icons: setIcon() rebuilds the badge's DOM element, so only call it when the badge actually changed
        const overlapIconCache = Object.create(null);
        function getOverlapIcon(count, bgColor) {
            const key = `${count}|${bgColor}`;
            return overlapIconCache[key] || (overlapIconCache[key] = makeOverlapIcon(count, bgColor));
        }
        function updateOverlapLabel(link_id, latlngs, count, bgColor) {
            var map = get_map(map_id);
            if (count <= 1) {
                if (link_overlap_labels[link_id]) {
                    map.removeLayer(link_overlap_labels[link_id]);
                    delete link_overlap_labels[link_id];
                }
                return;
            }
            const midpoint = getMidpoint(latlngs);
            const icon = getOverlapIcon(count, bgColor);
            const existing = link_overlap_labels[link_id];
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
                    const line = link_markers[link_id];
                    if (line && line.getPopup()) {
                        line.openPopup(marker.getLatLng());
                    }
                });
                marker.addTo(map);
                link_overlap_labels[link_id] = marker;
            }
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
                return `#030303c9`;
            }
            var rgb = hexToRgba(hex).match(/\d+/g); // source: https://stackoverflow.com/questions/3751877/how-to-extract-r-g-b-a-values-from-css-color
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

        function makeOverlapIcon(count, bgColor) {
            const bg = hexToRgba(bgColor, 0.35);
            return L.divIcon({
                className: 'link-overlap-count',
                html: `<div style="
                    background:${bg};
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

        function getSpeedColor(bps) {
            const gbps = bps / 1000000000;
            if (gbps >= 100) return '#0BD25A'; // 100G green
            if (gbps >= 40)  return '#DA5DE4'; // 40G pink
            if (gbps >= 25)  return '#4F6CA9'; // 25G blue
            if (gbps >= 10)  return '#7A5125'; // 10G brown
            return '#C9750F';                  // <10G orange
        }

        function formatBps(bps) {
            const n = Number(bps || 0);
            let color = '';

            const gbps = n / 1000000000;
            const mbps = n / 1000000;
            let text = '';

            color = getSpeedColor(n);

            if (gbps >= 1) {
                text = `${gbps % 1 === 0 ? gbps.toFixed(0) : gbps.toFixed(1)}G`;
            } else {
                text = `${mbps % 1 === 0 ? mbps.toFixed(0) : mbps.toFixed(1)}M`;
            }

            return `<span style="color:${color}; font-weight:600;">${text}</span>`;
        }

        function formatStatus(isUp) {
            const text = isUp ? 'up' : 'down';
            const color = isUp ? '#00A65A' : '#e40606';
            return `<span style="color:${color}; font-weight:600;">${text}</span>`;
        }

        // --- ADDED: link speed filter + client-side member filtering / primary selection !--
        const LINK_SPEED_BUCKETS = [
            { key: '1G',   label: '<10G', min: 0,            color: getSpeedColor(0) },
            { key: '10G',  label: '10G',  min: 10000000000,  color: getSpeedColor(10e9) },
            { key: '25G',  label: '25G',  min: 25000000000,  color: getSpeedColor(25e9) },
            { key: '40G',  label: '40G',  min: 40000000000,  color: getSpeedColor(40e9) },
            { key: '100G', label: '100G', min: 100000000000, color: getSpeedColor(100e9) },
        ];
        const linkSpeedVisible = Object.create(null);
        LINK_SPEED_BUCKETS.forEach(({ key }) => { linkSpeedVisible[key] = true; });
        let linkSpeedFilterEl = null;
        let geoLinkData = Object.create(null);

        function getSpeedBucket(bps) {
            const n = Number(bps || 0);
            for (let i = LINK_SPEED_BUCKETS.length - 1; i >= 0; i--) {
                if (n >= LINK_SPEED_BUCKETS[i].min) return LINK_SPEED_BUCKETS[i].key;
            }
            return LINK_SPEED_BUCKETS[0].key;
        }

        function compareLinkMembers(a, b) {
            if (!!a.up !== !!b.up) {
                return a.up ? 1 : -1; // down first
            }
            return Number(b.speed || 0) - Number(a.speed || 0); // then highest speed
        }

        function getVisibleMembers(link) {
            const members = Array.isArray(link.members) && link.members.length ? link.members : [link];
            return members
                .filter(m => linkSpeedVisible[getSpeedBucket(m.speed)])
                .sort(compareLinkMembers);
        }

        // Mirrors MapDataController::linkSpeedWidth()
        function linkSpeedWidth(bps) {
            const s = Number(bps || 0) / 10000000;
            if (!Number.isFinite(s) || s < 1) {
                return 1;
            }
            return String(Math.round(s)).length * 2;
        }

        // Tooltip/popup content is built only when shown, from the data stored on the line
        function linkTooltipContent(layer) {
            const view = layer._lnmsView;
            const primary = view.members[0];
            return (primary.local_name && primary.remote_name)
                ? `${primary.local_name} ${arrow} ${primary.remote_name}`
                : `${view.local_lat},${view.local_lng} ${arrow} ${view.remote_lat},${view.remote_lng}`;
        }
        function linkPopupContent(layer) {
            return createLinkHTML(layer._lnmsView);
        }

        function renderGeoLinks() {
            const map = get_map(map_id);
            if (!enableGeoLinks || !map) {
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
                const weight = __lnmsClamp(width, __lnmsLinkMinWeight, __lnmsLinkMaxWeight);

                let line = link_markers[link_id];

                // A layer's pane can't change after creation: rebuild if up/down state flipped
                if (line && line.options.pane !== pane) {
                    map.removeLayer(line);
                    delete link_markers[link_id];
                    line = null;
                }

                // Group unchanged since the last render: only re-base the zoom scaling (as before), skip everything else
                const sig = JSON.stringify([link.local_lat, link.local_lng, link.remote_lat, link.remote_lng, members]);
                if (line && line._lnmsSig === sig) {
                    __lnmsSetLinkBase(line, width);
                    if (line.options.weight !== weight) {
                        line.setStyle({ weight });
                    }
                    return;
                }

                const color = isUp ? primary.color : '#e40606';
                const linkStyle = isUp
                    ? { color, weight, opacity: __lnmsLinkOpacity, dashArray: '' }
                    : { color, weight, opacity: 0.9, dashArray: '6,6' };
                const latlng = [
                    L.latLng(link.local_lat, link.local_lng),
                    L.latLng(link.remote_lat, link.remote_lng),
                ];

                if (line) {
                    line._lnmsView = { ...link, members };
                    line.setLatLngs(latlng).setStyle(linkStyle);
                    // Content is generated on open, so only an open popup/tooltip needs refreshing
                    if (line.isPopupOpen()) {
                        line.getPopup().update();
                    }
                    if (line.isTooltipOpen()) {
                        line.getTooltip().update();
                    }
                } else {
                    const widgetWidth = document.getElementById(map_id)?.clientWidth || 600;
                    line = new L.polyline(latlng, { ...linkStyle, pane, renderer: linkRenderers[pane] }).addTo(map);
                    line._lnmsView = { ...link, members };
                    line.bindTooltip(linkTooltipContent, { className: "leaflet-link-tooltip", sticky: true, opacity: 0.9 });
                    line.bindPopup(linkPopupContent, {
                        className: 'link-style',
                        maxWidth: Math.max(240, widgetWidth - 24),
                        autoPan: true,
                        keepInView: true,
                        autoPanPadding: [12, 12]
                    });
                    link_markers[link_id] = line;
                }
                line._lnmsSig = sig;
                __lnmsSetLinkBase(line, width);

                const sameLocation = link.local_lat === link.remote_lat && link.local_lng === link.remote_lng;
                updateOverlapLabel(link_id, latlng, sameLocation ? 1 : members.length, color);
            });

            // Remove lines/badges that are filtered out or no longer returned by the server
            for (const link_id in link_markers) {
                if (!(link_id in rendered)) {
                    map.removeLayer(link_markers[link_id]);
                    delete link_markers[link_id];
                }
            }
            for (const link_id in link_overlap_labels) {
                if (!(link_id in rendered)) {
                    map.removeLayer(link_overlap_labels[link_id]);
                    delete link_overlap_labels[link_id];
                }
            }
        }

        function setLinkSpeedVisible(key, visible, render = true) {
            linkSpeedVisible[key] = visible;
            const chip = linkSpeedFilterEl?.querySelector(`[data-speed="${key}"]`);
            if (chip) {
                chip.classList.toggle('is-active', visible);
                chip.setAttribute('aria-pressed', String(visible));
            }
            if (render) {
                renderGeoLinks();
            }
        }

        let linkSpeedFilterControl = null;

        // Only show the filter when the server returned at least one link
        function syncLinkSpeedFilterControl(map) {
            const hasLinks = Object.keys(geoLinkData).length > 0;
            if (hasLinks && !linkSpeedFilterControl) {
                linkSpeedFilterControl = createLinkSpeedFilterControl();
                map.addControl(linkSpeedFilterControl);
            } else if (!hasLinks && linkSpeedFilterControl) {
                map.removeControl(linkSpeedFilterControl);
                linkSpeedFilterControl = null;
                linkSpeedFilterEl = null;
            }
        }

        // L doesn't exist until loadjs has loaded leaflet.js, so the class is built on demand
        function createLinkSpeedFilterControl() {
            const LinkSpeedFilterControl = L.Control.extend({
                options: { position: 'bottomleft' },
                onAdd: function () {
                    const container = L.DomUtil.create('div', 'link-speed-filter leaflet-control');
                    container.setAttribute('role', 'group');
                    container.setAttribute('aria-label', 'Filter links by speed');

                    LINK_SPEED_BUCKETS.forEach(({ key, label, color }) => {
                        const chip = L.DomUtil.create('button', 'link-speed-filter__chip', container);
                        chip.type = 'button';
                        chip.dataset.speed = key;
                        chip.classList.toggle('is-active', linkSpeedVisible[key]);
                        chip.setAttribute('aria-pressed', String(linkSpeedVisible[key]));

                        const dot = L.DomUtil.create('span', 'link-speed-filter__dot', chip);
                        dot.style.background = color;
                        chip.appendChild(document.createTextNode(label)); // text node so "<10G" stays literal

                        L.DomEvent.on(chip, 'click', function (e) {
                            L.DomEvent.stop(e);
                            setLinkSpeedVisible(key, !linkSpeedVisible[key]);
                        });
                    });

                    L.DomEvent.disableClickPropagation(container);
                    L.DomEvent.disableScrollPropagation(container);
                    linkSpeedFilterEl = container;
                    return container;
                },
            });
            return new LinkSpeedFilterControl();
        }

        // Control is hidden below 900px: don't leave links hidden with no way to restore them
        const linkFilterSmallScreen = window.matchMedia('(max-width: 899.98px)');
        linkFilterSmallScreen.addEventListener('change', function () {
            if (enableGeoLinks && linkFilterSmallScreen.matches) {
                LINK_SPEED_BUCKETS.forEach(({ key }) => setLinkSpeedVisible(key, true, false));
                renderGeoLinks();
            }
        });
        // !--

        function populate_map_links(map_id) {
            $.ajax({
                type: "POST",
                url: '{{ route('maps.getgeolinks') }}',
                dataType: "json",
                data: {
                    link_type: {{ Js::from($netmap_source ?? 'xdp') }},
                    group: {{ (int) $device_group }},
                    disabled_alerts: {{ Js::from($disabled_alerts) }}
                },
                success: function(data) {
                    // --- UPDATED: store response, render from filter state !--
                    geoLinkData = data || Object.create(null);
                    renderGeoLinks();
                    const map = get_map(map_id);
                    syncLinkSpeedFilterControl(map);
                    if (map.markerCluster && typeof map.markerCluster.refreshClusters === 'function') {
                        map.markerCluster.refreshClusters();
                    }
                    // !--
                },
                error: err => toastr.error(err.statusText)
            });
        }
        // !--

        loadjs('js/leaflet.js', function () {
            loadjs('js/leaflet.markercluster.js', function () {
                loadjs('js/leaflet.awesome-markers.min.js', function () {
                    loadjs('js/L.Control.Locate.min.js', function () {
                        const map = init_map(map_id, map_config); // --- ADDED
                        map.setMaxZoom(19);                       // --- ADDED
                        map.scrollWheelZoom.disable();            // --- UPDATED
                        __lnmsLinkBaseZoom = map.getZoom();       // --- ADDED
                        populate_map_markers(map_id, group_radius, status, device_group);

                        // --- ADDED !--
                        const initialView = {
                            lat: map.getCenter().lat,
                            lng: map.getCenter().lng,
                            zoom: map.getZoom()
                        };

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
                                container.innerHTML = "\u{21BA}";
                                container.style.fontSize = '26px';
                                container.title = 'Reset view';

                                L.DomEvent.disableClickPropagation(container);
                                L.DomEvent.disableScrollPropagation(container);

                                container.onclick = function () {
                                    const map = get_map(map_id);
                                    map.setView([initialView.lat, initialView.lng], initialView.zoom, { animate: true });
                                    map.closePopup();
                                    if (map.markerCluster) {
                                        map.markerCluster.unspiderfy();
                                    }
                                };

                                return container;

                            }

                        });

                        map.addControl(new ResetViewControl());

                        if (enableGeoLinks) {
                            map.createPane('linksUpPane').style.zIndex = 400;
                            map.createPane('linksDownPane').style.zIndex = 410; // down links above up links
                            // Line panes pass clicks through; each interactive SVG path re-enables pointer events (Leaflet CSS) - otherwise down-links pane covers the up-links pane and swallows the clicks
                            map.getPane('linksUpPane').style.pointerEvents = 'none';
                            map.getPane('linksDownPane').style.pointerEvents = 'none';
                            map.createPane('linkLabelsPane').style.zIndex = 450;
                            map.getPane('linkLabelsPane').style.pointerEvents = 'none';
                            // Dedicated renderers for link lines - padding = extra area drawn around the viewport on each side
                            linkRenderers = {
                                linksUpPane: L.svg({ pane: 'linksUpPane', padding: LINK_RENDER_PADDING }),
                                linksDownPane: L.svg({ pane: 'linksDownPane', padding: LINK_RENDER_PADDING }),
                            };
                            // Redraw the link renderers during a pan (throttled), the way tile layers load while the map moves - renderer._update() is Leaflet-internal (used on moveend)
                            map.on('move', L.Util.throttle(function () {
                                if (map._animatingZoom) {
                                    return; // mid zoom animation: Leaflet redraws on zoomend
                                }
                                const zoom = map.getZoom();
                                for (const pane in linkRenderers) {
                                    const renderer = linkRenderers[pane];
                                    if (renderer._map && renderer._zoom === zoom) {
                                        renderer._update();
                                    }
                                }
                            }, LINK_RENDER_THROTTLE_MS));
                            populate_map_links(map_id);
                            map.on('zoomend', __lnmsRescaleAllLinks);
                        }
                        // !--

                        // register listeners
                        $('#' + map_id).on('click', function (event) {
                            get_map(map_id).scrollWheelZoom.enable();
                        }).on('mouseleave', function (event) {
                            get_map(map_id).scrollWheelZoom.disable();
                        }).on('resize', function (event) {
                            get_map(map_id).invalidateSize();
                        }).on('refresh', function (event) {
                            get_map(map_id).invalidateSize();
                            populate_map_markers(map_id, group_radius, status, device_group);
                            // --- ADDED !--
                            if (enableGeoLinks) {
                                __lnmsLinkBaseZoom = get_map(map_id).getZoom();
                                populate_map_links(map_id);
                            }
                            // !--
                        }).on('destroy', function (event) {
                            destroy_map(map_id);
                        });
                    });
                });
            });
        });
    })();
</script>