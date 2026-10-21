@extends('layouts.librenmsv1')

@section('title', __('Geographical Map'))

@section('content')

@if($map_engine != 'leaflet')

<div class="container-fluid">
<div class="row">
<div class="col-md-12">
Only leaflet map engine is currently supported
</div>
</div>
</div>

@else

@if($group_name)
<div class="container-fluid">
<div class="row" id="controls-row">
<div class="col-md-12">

&nbsp;
<big><b>{{ $group_name }}</b></big>

</div>
</div>
</div>
@endif

<div class="container" id="fullscreen-map"></div>

@endif

@endsection

@section('css')
<style>
/* --- ADDED: styling !-- */
html, body, #fullscreen-map {
   height: 100%;
   width: 100%;
   padding-bottom: 0;
   margin-bottom: 0;
   container-type: inline-size;
}

    #fullscreen-map {
      /* Default theme values */
      --wm-popup-bg: #f2f2f2ee;
      --wm-popup-text: #1d1d1d;

      --wm-popup-font-size: 14px;
      --wm-popup-font-weight: bold;

      --wm-popup-padding: 6px;
      --wm-tooltip-font-size: 14px;

      --wm-marker-popup-max-width: min(1260px, calc(100% - 24px));
      --wm-marker-popup-min-height: min(1260px, calc(100vw - 24px));

      --wm-link-popup-min-width: 300px;

      --wm-graph-gap: 8px;
      --wm-graph-side-padding: 2px;
      --wm-graph-title-size: 14px;
      --wm-graph-title-margin: 0 0 6px 0;

      --wm-filter-selected: #1a56b8;
    }

    #fullscreen-map[data-theme="dark"] {
      --wm-popup-bg: #1d1d1ddd;
      --wm-popup-text: #d3d3d3;
      --wm-filter-selected: #5b9bff;
    }

    #fullscreen-map[data-theme="light"] {
      --wm-popup-bg: #f2f2f2ee;
      --wm-popup-text: #1d1d1d;
      --wm-filter-selected: #1a56b8;
    }

    /* Popup content */
    .leaflet-popup.marker-style .leaflet-popup-content-wrapper,
    .leaflet-popup.link-style .leaflet-popup-content-wrapper {
      background: var(--wm-popup-bg) !important;
      color: var(--wm-popup-text) !important;
      font-size: var(--wm-popup-font-size);
      font-weight: var(--wm-popup-font-weight);
      padding: var(--wm-popup-padding);
    }
    .leaflet-popup.marker-style .leaflet-popup-content,
    .leaflet-popup.link-style .leaflet-popup-content {
      background: transparent !important;
      color: inherit !important;
    }

    /* Popup arrow fill */
    .leaflet-popup.marker-style .leaflet-popup-tip,
    .leaflet-popup.link-style .leaflet-popup-tip {
      background: var(--wm-popup-bg) !important;
    }

    /* Popup close button */
    .leaflet-popup.marker-style .leaflet-popup-close-button,
    .leaflet-popup.link-style .leaflet-popup-close-button {
      color: var(--wm-popup-text) !important;
    }

    /* Device popup dimensions */
    .leaflet-popup.marker-style .leaflet-popup-content-wrapper {
      max-width: calc(100cqi - 16px) !important;
      max-height: calc(100dvh - 32px);
      box-sizing: border-box;
      overflow: hidden;
    }
    .leaflet-popup.marker-style .leaflet-popup-content {
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
    .leaflet-popup.marker-style .leaflet-popup-content:has(.device-popup--1) {
      width: min(300px, calc(100cqi - 32px)) !important;
    }
    .leaflet-popup.marker-style .leaflet-popup-content:has(.device-popup--2) {
      width: min(600px, calc(100cqi - 32px)) !important;
    }
    .leaflet-popup.marker-style .leaflet-popup-content:has(.device-popup--3) {
      width: min(900px, calc(100cqi - 32px)) !important;
    }
    .leaflet-popup.marker-style .device-popup {
      width: 100%;
      max-width: 1200px;
      margin: 0 auto;
    }
    .leaflet-popup.marker-style .device-popup__title {
      text-align: center;
      margin-bottom: 2px;
    }
    .leaflet-popup.marker-style .device-popup__ip {
      text-align: center;
      margin-bottom: 10px;
      font-size: 12px !important;
      font-weight: normal !important;
    }
    /* Device marker graph grid */
    .leaflet-popup.marker-style .device-popup__graphs {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 8px;
    }
    .leaflet-popup.marker-style .device-popup--1 .device-popup__graphs {
      grid-template-columns: minmax(0, 1fr);
    }
    .leaflet-popup.marker-style .device-popup--2 .device-popup__graphs {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .leaflet-popup.marker-style .device-popup--3 .device-popup__graphs {
      grid-template-columns: repeat(3, minmax(0, 1fr));
    }
    .leaflet-popup.marker-style .device-popup__graph {
      min-width: 0;
    }
    .leaflet-popup.marker-style .device-popup__graph-title {
      text-align: center;
      margin-bottom: 6px;
      font-size: 14px;
    }
    .leaflet-popup.marker-style .device-popup__graph-link {
      display: block;
      padding-inline: 2px;
    }
    .leaflet-popup.marker-style img.graph-image {
      display: block;
      width: 100%;
      height: auto;
      aspect-ratio: 20 / 9;
    }
    /* 2 columns */
    @media (max-width: 900px) {
      .leaflet-popup.marker-style .device-popup__graphs {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }
    /* 1 column */
    @media (max-width: 620px) {
      .leaflet-popup.marker-style .device-popup__graphs {
        grid-template-columns: 1fr;
      }
    }
    .leaflet-popup.marker-style .device-popup--1 .device-popup__graphs {
      grid-template-columns: 1fr;
    }

    /* Link popup */
    .leaflet-popup.link-style .leaflet-popup-content,
    .leaflet-popup.link-style .leaflet-popup-content-wrapper {
      min-width: var(--wm-link-popup-min-width);
    }
    .overlap-member-meta {
      display: flex;
      align-items: center;
      gap: 6px;
      flex-wrap: wrap;
      font-weight: normal;
      margin-bottom: 4px;
    }

    /* Link tooltip */
    .leaflet-tooltip.leaflet-link-tooltip {
      background: var(--wm-popup-bg) !important;
      color: var(--wm-popup-text) !important;
      font-size: var(--wm-tooltip-font-size) !important;
      border: var(--wm-popup-bg);
      box-shadow: 0 1px 3px rgba(0,0,0,0.4);
    }
    .leaflet-tooltip-left:before {
      border-left-color: var(--wm-popup-bg);
    }
    .leaflet-tooltip-right:before {
      border-right-color: var(--wm-popup-bg);
    }

    /* Overlapping links dropdown */
    .overlap-summary {
      cursor: pointer;
      list-style: none;
      display: flex;
      align-items: center;
      gap: 6px;
      font-weight: normal;
      font-size: 15px;
    }
    .overlap-summary::before {
      content: "\25B6";
      font-size: 0.85em;
      line-height: 1;
    }
    details[open] > .overlap-summary::before {
      content: "\25BC";
    }
    .overlap-members-list {
      max-height: 7.5em;
      overflow-y: auto;
      overflow-x: hidden;
      padding-right: 4px;
    }
    .overlap-members-list .link-popup__title,
    .overlap-members-list .overlap-member-meta {
      justify-content: flex-start;
      text-align: left;
      font-size: 14px;
    }
    .link-overlap-count {
      cursor: pointer;
      opacity: 0.85;
    }

    /* Adjust refresh button by color mode */
    .wm-reset-view {
      background: var(--wm-popup-bg);
      color: var(--wm-popup-text);
      border: none;
      border-radius: 10px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
    }

    /* Spiderfy animation for AwesomeMarkers (they lack .leaflet-marker-icon, which markercluster's transition targets) */
    .leaflet-cluster-anim .awesome-marker {
      transition: transform 0.3s ease-out, opacity 0.3s ease-in;
    }

    /* Link speed filter */
    .link-speed-filter {
      display: flex;
      flex-direction: column;
      gap: 6px;
      padding: 6px;
      background: var(--wm-popup-bg);
      color: var(--wm-popup-text);
      border-radius: 10px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
    }
    .link-speed-filter__chip {
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
    .link-speed-filter__chip.is-active {
      border-color: var(--wm-filter-selected);
      box-shadow: inset 0 0 0 1px var(--wm-filter-selected);
    }
    .link-speed-filter__chip:focus-visible {
      outline: 2px solid var(--wm-filter-selected);
      outline-offset: 2px;
    }
    .link-speed-filter__dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      flex: none;
    }
    @media (max-width: 899.98px) {
      .link-speed-filter {
        display: none !important;
      }
    }
/* !-- */
</style>
@endsection

@section('javascript')
@if($map_engine == "leaflet")
<script src="js/leaflet.js"></script>
<script src="js/L.Control.Locate.min.js"></script>
<script src="js/leaflet.markercluster.js"></script>
<script src="js/leaflet.awesome-markers.min.js"></script>
@endif

@endsection

@section('scripts')
<script type="text/javascript">
    function checkMapSize() {
        var mapheight = $(window).height() - $("#fullscreen-map").offset().top;
        if(mapheight < 200) {
            mapheight = 200;
        }
        $("#fullscreen-map").height(mapheight);
    };

    window.addEventListener('resize', checkMapSize);
    checkMapSize();

    var device_map = init_map("fullscreen-map", {engine: "{{$map_provider}}", api_key: "{{$map_api_key}}", "tile_url": "{{$tile_url}}", lat: {{$init_lat}}, lng: {{$init_lng}}, zoom: {{$init_zoom}}});
    // --- ADDED !--
    device_map.setMaxZoom(19);
    const fullScreenMapElem = document.getElementById('fullscreen-map');
    if (fullScreenMapElem) {
        fullScreenMapElem.dataset.theme = String(window.siteStyle).toLowerCase().includes('dark') ? 'dark' : 'light';
    }

    // Reset view button (same as the widget map)
    const initialView = {
        lat: device_map.getCenter().lat,
        lng: device_map.getCenter().lng,
        zoom: device_map.getZoom()
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
                device_map.setView([initialView.lat, initialView.lng], initialView.zoom, { animate: true });
                device_map.closePopup();
                device_marker_cluster.unspiderfy();
            };

            return container;
        }
    });

    device_map.addControl(new ResetViewControl());
    // !--

    var device_marker_cluster = L.markerClusterGroup({
        maxClusterRadius: {{$group_radius}},
        iconCreateFunction: function (cluster) {
            var markers = cluster.getAllChildMarkers();
            // --- DELETE: unused var
            var color = "green" // --- UPDATED
            const newClass = "Cluster marker-cluster marker-cluster-small leaflet-zoom-animated leaflet-clickable"; // --- UPDATED
            for (var i = 0; i < markers.length; i++) {
                // --- UPDATED !--
                const deviceData = markers[i].deviceData;
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
            return L.divIcon({ html: cluster.getChildCount(), className: color+newClass, iconSize: L.point(40, 40) });
        },
    });
    device_map.addLayer(device_marker_cluster);

    // --- ADDED !--
    const link_layer = L.layerGroup();
    device_map.addLayer(link_layer);
    // !--

    var device_markers = Object.create(null); // --- UPDATED
    var link_markers = Object.create(null); // --- UPDATED
    var link_overlap_labels = Object.create(null); // --- ADDED

    // --- ADDED: pane for overlapping link labels, network link line width scaling with zoom level, URL and graph builders, device icon cache !--
    device_map.createPane('linksUpPane').style.zIndex = 400;
    device_map.createPane('linksDownPane').style.zIndex = 410; // down links above up links
    // Line panes pass clicks through; each interactive SVG path re-enables pointer events (Leaflet CSS) - otherwise down-links pane covers the up-links pane and swallows the clicks
    device_map.getPane('linksUpPane').style.pointerEvents = 'none';
    device_map.getPane('linksDownPane').style.pointerEvents = 'none';
    device_map.createPane('linkLabelsPane').style.zIndex = 450;
    device_map.getPane('linkLabelsPane').style.pointerEvents = 'none';

    const LINK_RENDER_PADDING = 0.5; // 0.5 = half a screen - lines are already drawn when they're panned or zoomed out into view
    const LINK_RENDER_THROTTLE_MS = 100;
    // Dedicated renderers for link lines - padding = extra area drawn around the viewport on each side
    const linkRenderers = {
        linksUpPane: L.svg({ pane: 'linksUpPane', padding: LINK_RENDER_PADDING }),
        linksDownPane: L.svg({ pane: 'linksDownPane', padding: LINK_RENDER_PADDING }),
    };
    // Redraw the link renderers during a pan (throttled), the way tile layers load while the map moves - renderer._update() is Leaflet-internal (used on moveend)
    device_map.on('move', L.Util.throttle(function () {
        if (device_map._animatingZoom) {
            return; // mid zoom animation: Leaflet redraws on zoomend
        }
		const zoom = device_map.getZoom();
		for (const pane in linkRenderers) {
		    const renderer = linkRenderers[pane];
		    if (renderer._map && renderer._zoom === zoom) {
		        renderer._update();
		    }
		}
    }, LINK_RENDER_THROTTLE_MS));

    const __lnmsLinkMinWeight = 2;
    const __lnmsLinkMaxWeight = 3.5;
    const __lnmsLinkOpacity   = 0.3; // lower vals = more transparent
    function __lnmsClamp(num, min, max) {
        return Math.max(min, Math.min(max, num));
    }
    function __lnmsSetLinkBase(line, rawWeight) {
        if (!line || !line.options) return;
        line.options.__lnmsBaseWeight = rawWeight ?? line.options.weight ?? 1;
        line.options.__lnmsBaseZoom = device_map.getZoom();
    }
    function __lnmsRescaleLink(line) {
        if (!line || typeof line.setStyle !== 'function' || !line.options) return;
        if (line.options.__lnmsBaseWeight === undefined) {
            __lnmsSetLinkBase(line);
        }
        const scale = device_map.getZoomScale(
            device_map.getZoom(),
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
    device_map.on('zoomend', __lnmsRescaleAllLinks);

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
        if (count <= 1) {
            if (link_overlap_labels[link_id]) {
                device_map.removeLayer(link_overlap_labels[link_id]);
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
            marker.addTo(device_map);
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

    function html_chars(str) {
        return String(str).replaceAll('&', '&amp;').replaceAll("'", '&apos;')
    }

    function getSpeedColor(bps) {
        const gbps = bps / 1000000000;
        if (gbps >= 100) return '#0BD25A'; // 100G green
        if (gbps >= 40)  return '#DA5DE4'; // 40G pink
        if (gbps >= 25)  return '#4F6CA9'; // 25G blue
        if (gbps >= 10)  return '#7A5125'; // 10G brown
        return '#C9750F';                   // <10G orange
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

    function createDeviceHREF(device_id) {
        return `device/${device_id}`;
    }
    function createGraphHREF(device_id, port_id) {
        return `device/device=${device_id}/tab=port/port=${port_id}/`;
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
            if (link['local_device_id'] && link['remote_device_id']) {
                return `<div class="link-popup__title"><a href="${createDeviceHREF(link['local_device_id'])}" target="_blank" rel="noopener">${html_chars(link['local_name'])}</a> ${arrow} <a href="${createDeviceHREF(link['remote_device_id'])}" target="_blank" rel="noopener">${html_chars(link['remote_name'])}</a><div>`;
            }
            return `<div class="link-popup__title">${link['local_name']} ${arrow} ${link['remote_name']}</div>`;
        }
        return `<div class="link-popup__title">${link['local_lat']},${link['local_lng']} ${arrow} ${link['remote_lat']},${link['remote_lng']}</div>`;
    }

    function createLinkHTML(link) {
        let html = '';

        const members = Array.isArray(link.members) && link.members.length ? link.members : [link];
        const primary = members[0];

        if (primary['local_device_id'] && primary['local_port_id']) {
            html = `<div style="font-size:18px;text-align:center;">${createLinkTitle(primary)}<br><a href="${createGraphHREF(primary['local_device_id'], primary['local_port_id'])}" target="_blank" rel="noopener"><img class="graph-image" src="${createGraphURL(null, primary['local_port_id'],"port_bits","-1d","yes")}"></a></div>`;
        }
        else if (primary['remote_device_id'] && primary['remote_port_id']) {
            html = `<div style="font-size:18px;text-align:center;">${createLinkTitle(primary)}<br><a href="${createGraphHREF(primary['remote_device_id'], primary['remote_port_id'])}" target="_blank" rel="noopener"><img class="graph-image" src="${createGraphURL(null, primary['remote_port_id'],"port_bits","-1d","yes")}"></a></div>`;
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
                        &nbsp; <a href="graphs/to=0/id=${member['local_port_id']}/type=port_bits/from=0/" target="_blank" rel="noopener">traffic</a>
                        &nbsp;|&nbsp; <a href=${createGraphHREF(member['local_device_id'], member['local_port_id'])} target="_blank" rel="noopener">${formatBps(member.speed)}</a>
                        &nbsp;|&nbsp; <a href=${createGraphHREF(member['local_device_id'], member['local_port_id'])} target="_blank" rel="noopener">${formatStatus(member.up)}</a>
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

    function createMarkerHTML(device, device_id) { //"<a href=\"" +  + "\"><img src=\"" + device["icon"] + "\" width=\"32\" height=\"32\" alt=\"\"> " + device["sname"] + "</a>"
        if (device.icontitle === 'avaya') {
            return `<div class="device-popup device-popup--1">
                <div class="device-popup__title">
                    <a href="${device["url"]}"
                    target="_blank"
                    rel="noopener">
                    ${html_chars(device["sname"] ?? device_id)}
                    </a>
                </div>
                <div class="device-popup__ip">
                    ${html_chars((device["ip"] ?? device["hostname"]) ?? "no hostname or IP found")}
                </div>

                <div class="device-popup__graphs">

                    <div class="device-popup__graph">
                        <div class="device-popup__graph-title">Device Traffic</div>
                        <a class="device-popup__graph-link"
                        href="graphs/device=${device_id}/type=device_bits/legend=yes/"
                        target="_blank" rel="noopener">
                            <img class="graph-image"
                                src="${createGraphURL(device_id, null, "device_bits", "-1d", "no", 400, 180)}">
                        </a>
                    </div>

                </div>
            </div>`;
        } else if (device.icontitle === 'ping') {
            return `<div class="device-popup device-popup--1">
                <div class="device-popup__title">
                    <a href="${device["url"]}"
                    target="_blank"
                    rel="noopener">
                    ${html_chars(device["sname"] ?? device_id)}
                    </a>
                </div>
                <div class="device-popup__ip">
                    ${html_chars((device["ip"] ?? device["hostname"]) ?? "no hostname or IP found")}
                </div>

                <div class="device-popup__graphs">

                    <div class="device-popup__graph">
                        <div class="device-popup__graph-title">ICMP Response</div>
                        <a class="device-popup__graph-link"
                        href="graphs/device=${device_id}/type=device_ping_perf/legend=yes/"
                        target="_blank" rel="noopener">
                            <img class="graph-image"
                                src="${createGraphURL(device_id, null, "device_ping_perf", "-1d", "no", 400, 180)}">
                        </a>
                    </div>

                </div>
            </div>`;
        } else {
            return `<div class="device-popup device-popup--3">
                <div class="device-popup__title">
                    <a href="${device["url"]}"
                    target="_blank"
                    rel="noopener">
                    ${html_chars(device["sname"] ?? device_id)}
                    </a>
                </div>

                <div class="device-popup__ip">
                    ${html_chars((device["ip"] ?? device["hostname"]) ?? "no hostname or IP found")}
                </div>

                <div class="device-popup__graphs">

                    <div class="device-popup__graph">
                        <div class="device-popup__graph-title">Device Traffic</div>
                        <a class="device-popup__graph-link"
                        href="graphs/device=${device_id}/type=device_bits/legend=no/"
                        target="_blank" rel="noopener">
                            <img class="graph-image"
                                src="${createGraphURL(device_id, null, "device_bits", "-1d", "no", 400, 180)}">
                        </a>
                    </div>

                    <div class="device-popup__graph">
                        <div class="device-popup__graph-title">Processor Usage</div>
                        <a class="device-popup__graph-link"
                        href="device/device=${device_id}/tab=health/metric=processor/"
                        target="_blank" rel="noopener">
                            <img class="graph-image"
                                src="${createGraphURL(device_id, null, "device_processor", "-1d", "no", 400, 180)}">
                        </a>
                    </div>

                    <div class="device-popup__graph">
                        <div class="device-popup__graph-title">Memory Usage</div>
                        <a class="device-popup__graph-link"
                        href="device/device=${device_id}/tab=health/metric=mempool/"
                        target="_blank" rel="noopener">
                            <img class="graph-image"
                                src="${createGraphURL(device_id, null, "device_mempool", "-1d", "no", 400, 180)}">
                        </a>
                    </div>

                </div>
            </div>`;
        }
    }

    const iconCache = Object.create(null);

    function getDeviceIcon(device) {
        const markerColor = device.status ? 'green' : (device.maintenance == 1 ? 'blue' : 'red');
        const isNetworkWired = device.typeIcon === 'network-wired' && device.icon;
        const key = isNetworkWired
            ? `nwimg|${device.icon}|${markerColor}`
            : `${device.typeIcon}|${markerColor}`;
        if (!iconCache[key]) {
            if (isNetworkWired) {
                iconCache[key] = L.divIcon({
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
                iconCache[key] = L.AwesomeMarkers.icon({
                    icon: device.typeIcon,
                    markerColor,
                    prefix: 'fa',
                    iconColor: 'white'
                });
            }
        }
        return iconCache[key];
    }
    // !--

    // --- ADDED: link speed filter + client-side member filtering / primary selection !--
    const geoLinksEnabled = {{ ($show_netmap && $netmap_source == 'xdp') ? 'true' : 'false' }};

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
        if (!geoLinksEnabled) {
            return; // never touch dependency ("depends") lines
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
                link_layer.removeLayer(line);
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
                ? { color, weight, opacity: __lnmsLinkOpacity, smoothFactor: 1, dashArray: '' }
                : { color, weight, opacity: 0.9, smoothFactor: 1, dashArray: '6,6' };
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
                line = new L.Polyline(latlng, { ...linkStyle, pane, renderer: linkRenderers[pane] });
                line._lnmsView = { ...link, members };
                line.bindTooltip(linkTooltipContent, { className: "leaflet-link-tooltip", sticky: true, opacity: 0.9 });
                line.bindPopup(linkPopupContent, { className: 'link-style' });
                link_markers[link_id] = line;
                link_layer.addLayer(line);
            }
            line._lnmsSig = sig;
            __lnmsSetLinkBase(line, width);

            const sameLocation = link.local_lat === link.remote_lat && link.local_lng === link.remote_lng;
            updateOverlapLabel(link_id, latlng, sameLocation ? 1 : members.length, color);
        });

        // Remove lines/badges that are filtered out or no longer returned by the server
        for (const link_id in link_markers) {
            if (!(link_id in rendered)) {
                link_layer.removeLayer(link_markers[link_id]);
                delete link_markers[link_id];
            }
        }
        for (const link_id in link_overlap_labels) {
            if (!(link_id in rendered)) {
                device_map.removeLayer(link_overlap_labels[link_id]);
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

    if (geoLinksEnabled) {
        device_map.addControl(new LinkSpeedFilterControl());

        // Control is hidden below 900px: don't leave links hidden with no way to restore them
        const linkFilterSmallScreen = window.matchMedia('(max-width: 899.98px)');
        linkFilterSmallScreen.addEventListener('change', function () {
            if (linkFilterSmallScreen.matches) {
                LINK_SPEED_BUCKETS.forEach(({ key }) => setLinkSpeedVisible(key, true, false));
                renderGeoLinks();
            }
        });
    }
    // !--

    function checkParentLink(device, parent) {
        const line_id = "dev." + device["id"] + "." + parent["id"]; // --- UPDATED
        if(line_id in link_markers) {
            link_markers[line_id].setLatLngs([new L.LatLng(device["lat"],device["lng"]), new L.LatLng(parent["lat"],parent["lng"])]);
        } else {
            var line = new L.Polyline([new L.LatLng(device["lat"],device["lng"]), new L.LatLng(parent["lat"],parent["lng"])], {
                color: "blue",
                weight: 2,
                opacity: 0.8,
                smoothFactor: 1
            });
            link_markers[line_id] = line;
            link_layer.addLayer(line); // --- UPDATED
        }
        return line_id;
    }

    function refreshMap() {
        var devices = Object.create(null); // --- UPDATED
        var links = Object.create(null); // --- UPDATED
        var device_group = {{ $group_id ? $group_id : 'null' }};
@if($show_netmap && $netmap_source == 'depends')
        $.post( '{{ route('maps.getdevices') }}', {disabled: 0, location_valid: 1, disabled_alerts: {{$netmap_include_disabled_alerts}}, group: device_group, link_type: '{{$netmap_source}}'})
@else
        $.post( '{{ route('maps.getdevices') }}', {disabled: 0, location_valid: 1, disabled_alerts: {{$netmap_include_disabled_alerts}}, group: device_group})
@endif
            .done(function( data ) {
                // --- ADDED: sort devices alphabetically for markers !--
                const sortedDevices = Object.entries(data).sort(([, deviceA], [, deviceB]) =>
                    String(deviceA.sname ?? '').localeCompare(
                        String(deviceB.sname ?? ''),
                        undefined,
                        {
                            numeric: true,
                            sensitivity: 'base'
                        }
                    )
                );
                // !--

                sortedDevices.forEach(([device_id, device]) => { // --- UPDATED (device sorting)
                    const icon = getDeviceIcon(device); // --- UPDATED

                    var z_offset = 0;
                    if (device["status"] == 0) {
                        if (device["maintenance"] != 0) {
                            z_offset = 5000;
                        } else {
                            z_offset = 10000;
                        }
                    }
                    if(device_id in device_markers) {
                        // --- UPDATED: only update items changed (setLatLng re-clusters the marker, setIcon rebuilds its DOM) !--
                        const existing = device_markers[device_id];
                        const latlng = new L.LatLng(device["lat"], device["lng"]);
                        if (!existing.getLatLng().equals(latlng)) {
                            existing.setLatLng(latlng);
                        }
                        if (existing.options.zIndexOffset !== z_offset) {
                            existing.setZIndexOffset(z_offset);
                        }
                        existing.deviceData = device;
                        if (existing.options.icon !== icon) { // getDeviceIcon() returns cached icons
                            existing.setIcon(icon);
                        }
                        // !--

                        // If we have requested parent data, update lines
                        $.each( device["parents"], function( parent_idx, parent_id ) {
                            if (parent_id in data && (data[parent_id]["lat"] != device["lat"] || data[parent_id]["lng"] != device["lng"])) {
                                var line_id = checkParentLink(device, data[parent_id]);
                                links[line_id] = true;
                            }
                        });
                    } else {
                        var marker = L.marker(new L.LatLng(device["lat"],device["lng"]), {title: device["sname"], icon: icon, zIndexOffset: z_offset});
                        marker.deviceData = device; // --- ADDED
                        const marker_html = createMarkerHTML(device, device_id); // --- ADDED
                        const smallTouchscreen = (window.matchMedia('(max-width: 768px)').matches && (window.matchMedia('(pointer: coarse)').matches || 'ontouchstart' in window)); // --- ADDED
                        marker.bindPopup(marker_html, { className: 'marker-style', autoPan: !smallTouchscreen }); // --- UPDATED
                        device_marker_cluster.addLayer(marker);
                        device_markers[device_id] = marker;

                        // If we have requested parent data, add lines
                        $.each( device["parents"], function( parent_idx, parent_id ) {
                            if (parent_id in data && (data[parent_id]["lat"] != device["lat"] || data[parent_id]["lng"] != device["lng"])) {
                                var line_id = checkParentLink(device, data[parent_id]);
                                links[line_id] = true;
                            }
                        });
                    }
                    devices[device_id] = true;
                }); // --- UPDATED (device sorting)
                $("#countdown").css("border", "1px solid green");

                // Remove any devices that have disappeared
                $.each( device_markers, function( device_id, marker ) {
                    if(! (device_id in devices)) {
                        device_marker_cluster.removeLayer(marker);
                        delete device_markers[device_id]; // --- ADDED
                    }
                });
                // --- ADDED !--
                if (typeof device_marker_cluster.refreshClusters === 'function') {
                    device_marker_cluster.refreshClusters();
                }
                // !--
@if($show_netmap && $netmap_source == 'depends')
                // Remove any links that have disappeared
                $.each( link_markers, function( link_id, line ) {
                    if(! (link_id in links)) {
                        link_layer.removeLayer(line); // --- UPDATED
                        delete link_markers[link_id]; // --- ADDED
                    }
                });
@endif
            })
            .fail(function() {
                $("#countdown").css("border", "1px solid red");
            });
@if($show_netmap && $netmap_source == 'xdp')
        $.post( '{{ route('maps.getgeolinks') }}', {link_type: '{{$netmap_source}}', group: device_group})
            .done(function( data ) {
                // --- UPDATED: store response, render from filter state !--
                geoLinkData = data || Object.create(null);
                renderGeoLinks();
                // !--
                $("#countdown").css("border", "1px solid green");
            })
            .fail(function() {
                $("#countdown").css("border", "1px solid red");
            });
@endif
    }

    device_map.scrollWheelZoom.disable();
    $(document).ready(function(){
        $("#fullscreen-map").on("click", function(event) {
            device_map.scrollWheelZoom.enable();
        });
        $("#fullscreen-map").on("mouseleave", function(event) {
            device_map.scrollWheelZoom.disable();
        });

        // initial load
        refreshMap();
    });
</script>
<x-refresh-timer :refresh="$page_refresh" callback="refreshMap"></x-refresh-timer>
@endsection