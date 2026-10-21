{{--
    --- ADDED FILE: shared map styling

    Shared styles for the fullscreen geographic map (map/fullscreen.blade.php)
    and the world map widget (widgets/world-map.blade.php).

    Usage: @include('map.geomap-shared-styles', ['scope' => '#map-element-id'])

    Rendering: html/js/geomap-shared.js
--}}
<style>
{{ $scope }} {
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

{{ $scope }}[data-theme="dark"] {
  --wm-popup-bg: #1d1d1ddd;
  --wm-popup-text: #d3d3d3;
  --wm-filter-selected: #5b9bff;
}

{{ $scope }}[data-theme="light"] {
  --wm-popup-bg: #f2f2f2ee;
  --wm-popup-text: #1d1d1d;
  --wm-filter-selected: #1a56b8;
}

/* Popup content */
{{ $scope }} .leaflet-popup.marker-style .leaflet-popup-content-wrapper,
{{ $scope }} .leaflet-popup.link-style .leaflet-popup-content-wrapper {
  background: var(--wm-popup-bg) !important;
  color: var(--wm-popup-text) !important;
  font-size: var(--wm-popup-font-size);
  font-weight: var(--wm-popup-font-weight);
  padding: var(--wm-popup-padding);
}
{{ $scope }} .leaflet-popup.marker-style .leaflet-popup-content,
{{ $scope }} .leaflet-popup.link-style .leaflet-popup-content {
  background: transparent !important;
  color: inherit !important;
}

/* Popup arrow fill */
{{ $scope }} .leaflet-popup.marker-style .leaflet-popup-tip,
{{ $scope }} .leaflet-popup.link-style .leaflet-popup-tip {
  background: var(--wm-popup-bg) !important;
}

/* Popup close button */
{{ $scope }} .leaflet-popup.marker-style .leaflet-popup-close-button,
{{ $scope }} .leaflet-popup.link-style .leaflet-popup-close-button {
  color: var(--wm-popup-text) !important;
}

/* Device popup dimensions */
{{ $scope }} .leaflet-popup.marker-style .leaflet-popup-content-wrapper {
  max-width: calc(100cqi - 16px) !important;
  max-height: calc(100dvh - 32px);
  box-sizing: border-box;
  overflow: hidden;
}
{{ $scope }} .leaflet-popup.marker-style .leaflet-popup-content {
  width: min(900px, calc(100cqi - 32px)) !important;
  max-width: calc(100cqi - 32px) !important;
  max-height: calc(100dvh - 64px);
  overflow-y: auto;
  overflow-x: hidden;
  box-sizing: border-box;
  margin: 8px !important;
}
/* Size large window pop-ups according to the number of graphs */
{{ $scope }} .leaflet-popup.marker-style .leaflet-popup-content:has(.device-popup--1) {
  width: min(300px, calc(100cqi - 32px)) !important;
}
{{ $scope }} .leaflet-popup.marker-style .leaflet-popup-content:has(.device-popup--2) {
  width: min(600px, calc(100cqi - 32px)) !important;
}
{{ $scope }} .leaflet-popup.marker-style .leaflet-popup-content:has(.device-popup--3) {
  width: min(900px, calc(100cqi - 32px)) !important;
}
{{ $scope }} .leaflet-popup.marker-style .device-popup {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
}
{{ $scope }} .leaflet-popup.marker-style .device-popup__title {
  text-align: center;
  margin-bottom: 10px;
}
/* Full variant (fullscreen): IP address sits under the title */
{{ $scope }} .leaflet-popup.marker-style .device-popup__title:has(+ .device-popup__ip) {
  margin-bottom: 2px;
}
{{ $scope }} .leaflet-popup.marker-style .device-popup__ip {
  text-align: center;
  margin-bottom: 10px;
  font-size: 12px !important;
  font-weight: normal !important;
}

/* Device marker graph grid */
{{ $scope }} .leaflet-popup.marker-style .device-popup__graphs {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 8px;
}
{{ $scope }} .leaflet-popup.marker-style .device-popup--1 .device-popup__graphs {
  grid-template-columns: minmax(0, 1fr);
}
{{ $scope }} .leaflet-popup.marker-style .device-popup--2 .device-popup__graphs {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}
{{ $scope }} .leaflet-popup.marker-style .device-popup--3 .device-popup__graphs {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}
{{ $scope }} .leaflet-popup.marker-style .device-popup__graph {
  min-width: 0;
}
{{ $scope }} .leaflet-popup.marker-style .device-popup__graph-title {
  text-align: center;
  margin-bottom: 6px;
  font-size: 14px;
}
/* Compact variant (widget): smaller graph titles */
{{ $scope }} .leaflet-popup.marker-style .device-popup--compact .device-popup__graph-title {
  font-size: 12px;
  font-weight: 600;
}
{{ $scope }} .leaflet-popup.marker-style .device-popup__graph-link {
  display: block;
  padding-inline: 2px;
}
{{ $scope }} .leaflet-popup.marker-style img.graph-image {
  display: block;
  width: 100%;
  height: auto;
  aspect-ratio: 20 / 9;
}
/* 2 columns */
/* :not(.device-popup--1) raises specificity above the .device-popup--2/--3 rules so the breakpoints apply */
@container (max-width: 900px) {
  {{ $scope }} .leaflet-popup.marker-style .device-popup:not(.device-popup--1) .device-popup__graphs {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
/* 1 column */
@container (max-width: 620px) {
  {{ $scope }} .leaflet-popup.marker-style .device-popup:not(.device-popup--1) .device-popup__graphs {
    grid-template-columns: 1fr;
  }
}
{{ $scope }} .leaflet-popup.marker-style .device-popup--1 .device-popup__graphs {
  grid-template-columns: 1fr;
}

/* Link popup */
{{ $scope }} .leaflet-popup.link-style .leaflet-popup-content,
{{ $scope }} .leaflet-popup.link-style .leaflet-popup-content-wrapper {
  min-width: var(--wm-link-popup-min-width);
}
{{ $scope }} .overlap-member-meta {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
  font-weight: normal;
  margin-bottom: 4px;
}

/* Link tooltip */
{{ $scope }} .leaflet-tooltip.leaflet-link-tooltip {
  background: var(--wm-popup-bg) !important;
  color: var(--wm-popup-text) !important;
  font-size: var(--wm-tooltip-font-size) !important;
  box-shadow: 0 1px 3px rgba(0,0,0,0.4);
}
{{ $scope }} .leaflet-tooltip-left:before {
  border-left-color: var(--wm-popup-bg);
}
{{ $scope }} .leaflet-tooltip-right:before {
  border-right-color: var(--wm-popup-bg);
}

/* Overlapping links dropdown */
{{ $scope }} .overlap-summary {
  cursor: pointer;
  list-style: none;
  display: flex;
  align-items: center;
  gap: 6px;
  font-weight: normal;
  font-size: 15px;
}
{{ $scope }} .overlap-summary::before {
  content: "\25B6";
  font-size: 0.85em;
  line-height: 1;
}
{{ $scope }} details[open] > .overlap-summary::before {
  content: "\25BC";
}
{{ $scope }} .overlap-members-list {
  text-align: left;
  max-height: 7.5em;
  overflow-y: auto;
  overflow-x: hidden;
  padding-right: 4px;
}
{{ $scope }} .overlap-members-list .link-popup__title,
{{ $scope }} .overlap-members-list .overlap-member-meta {
  justify-content: flex-start;
  text-align: left;
  font-size: 14px;
}
{{ $scope }} .link-overlap-count {
  cursor: pointer;
  opacity: 0.85;
}

/* Reset view button */
{{ $scope }} .wm-reset-view {
  background: var(--wm-popup-bg);
  color: var(--wm-popup-text);
  border: none;
  border-radius: 10px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
}

/* Spiderfy animation for AwesomeMarkers (they lack .leaflet-marker-icon, which markercluster's transition targets) */
{{ $scope }} .leaflet-cluster-anim .awesome-marker {
  transition: transform 0.3s ease-out, opacity 0.3s ease-in;
}

/* Link speed filter */
{{ $scope }} .link-speed-filter {
  display: flex;
  flex-direction: column;
  gap: 6px;
  padding: 6px;
  background: var(--wm-popup-bg);
  color: var(--wm-popup-text);
  border-radius: 10px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
}
{{ $scope }} .link-speed-filter__chip {
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
{{ $scope }} .link-speed-filter__chip.is-active {
  border-color: var(--wm-filter-selected);
  box-shadow: inset 0 0 0 1px var(--wm-filter-selected);
}
{{ $scope }} .link-speed-filter__chip:focus-visible {
  outline: 2px solid var(--wm-filter-selected);
  outline-offset: 2px;
}
{{ $scope }} .link-speed-filter__dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  flex: none;
}
/* Filter bar: link speed filter + OS filter side by side */
{{ $scope }} .geomap-filters {
  display: flex;
  align-items: flex-end;
  gap: 8px;
}
{{ $scope }} .link-speed-filter__icon {
  width: 16px;
  height: 16px;
  object-fit: contain;
  flex: none;
}
/* matches SMALL_SCREEN_QUERY in geomap-shared.js */
@media (max-width: 899.98px) {
  {{ $scope }} .link-speed-filter {
    display: none !important;
  }
}
</style>