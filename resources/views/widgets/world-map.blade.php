<div id="worldmap_widget-{{ $id }}" class="worldmap_widget" data-reload="false"></div>

{{-- --- ADDED: shared map styling --}}
@include('map.geomap-shared-styles', ['scope' => '#worldmap_widget-' . $id])

<style>
/* --- ADDED: widget-only styling (shared rules: map/geomap-shared-styles) !-- */
#worldmap_widget-{{ $id }} {
  height: 100%;
  width: 100%;
  padding-bottom: 0;
  margin-bottom: 0;
  container-type: inline-size;
}
/* Fit link popups to small widgets: wrapper = content + 2x8px margin + 2x6px padding */
#worldmap_widget-{{ $id }} .leaflet-popup.link-style .leaflet-popup-content-wrapper {
  min-width: min(var(--wm-link-popup-min-width), calc(100cqi - 16px));
  max-width: calc(100cqi - 16px);
  box-sizing: border-box;
}
#worldmap_widget-{{ $id }} .leaflet-popup.link-style .leaflet-popup-content {
  min-width: min(var(--wm-link-popup-min-width), calc(100cqi - 44px));
  max-width: calc(100cqi - 44px);
  box-sizing: border-box;
  overflow-wrap: anywhere;
}
#worldmap_widget-{{ $id }} .leaflet-popup.link-style img.graph-image {
  display: block;
  max-width: 100%;
  height: auto;
  margin: 0 auto;
}
/* Smaller link popup title and overlap summary in narrow widgets */
@container (max-width: 620px) {
  #worldmap_widget-{{ $id }} .leaflet-popup.marker-style .device-popup__title,
  #worldmap_widget-{{ $id }} .leaflet-popup.marker-style .device-popup__title > * {
    font-size: 20px;
  }
  #worldmap_widget-{{ $id }} .leaflet-popup.link-style .link-popup__title {
    font-size: 16px;
  }
  #worldmap_widget-{{ $id }} .leaflet-popup.link-style .overlap-members-list .link-popup__title {
    font-size: 14px; /* keep the overlapping-link titles at their current size */
  }
  #worldmap_widget-{{ $id }} .leaflet-popup.link-style .overlap-summary {
    font-size: 13px;
  }
  /* Narrow widgets: tighter side margins */
  #worldmap_widget-{{ $id }} .leaflet-popup.link-style .leaflet-popup-content {
    margin: 8px 20px 8px 8px;
    min-width: min(var(--wm-link-popup-min-width), calc(100cqi - 56px));
    max-width: calc(100cqi - 56px);
  }
}
#worldmap_widget-{{ $id }} .leaflet-popup.marker-style .leaflet-popup-tip,
#worldmap_widget-{{ $id }} .leaflet-popup.link-style .leaflet-popup-tip {
  box-shadow: none !important;
}
#worldmap_widget-{{ $id }} .leaflet-tooltip.leaflet-link-tooltip {
  border: 1px solid var(--wm-popup-bg);
}
#worldmap_widget-{{ $id }} .leaflet-tooltip-left {
  margin-left: -7px;
}
#worldmap_widget-{{ $id }} .leaflet-tooltip-right {
  margin-left: 7px;
}
#worldmap_widget-{{ $id }} .leaflet-control-locate {
  display: none !important;
}
#worldmap_widget-{{ $id }} .wm-reset-view:hover {
  filter: brightness(0.92);
}
#worldmap_widget-{{ $id }}[data-theme="dark"] .wm-reset-view:hover {
  filter: brightness(1.25);
}
#worldmap_widget-{{ $id }} .geomap-filters {
  margin-bottom: 20.5px;
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
        const enableGeoLinks = Boolean(show_netmap) && String(netmap_source).trim().toLowerCase() === 'xdp';
        let geomap = null; // created once Leaflet and geomap-shared.js have loaded (see loadjs callback)
        // !--

        function populate_map_markers() {
            $.ajax({
                type: "POST",
                url: '{{ route('maps.getdevices') }}',
                dataType: "json",
                data: { location_valid: 1, disabled: 0, disabled_alerts: disabled_alerts, statuses: status, group: device_group },
                success: function (data) {
                    geomap.setDevices(data); // --- UPDATED: marker add/update/remove in geomap-shared.js
                },
                error: function(error){
                    toastr.error(error.statusText);
                }
            });
        }

        // --- ADDED !--
        function populate_map_links() {
            $.ajax({
                type: "POST",
                url: '{{ route('maps.getgeolinks') }}',
                dataType: "json",
                data: {
                    link_type: {{ Js::from($netmap_source ?? 'xdp') }},
                    group: device_group,
                    disabled_alerts: disabled_alerts
                },
                success: function (data) {
                    geomap.setGeoLinks(data);
                },
                error: err => toastr.error(err.statusText)
            });
        }
        // !--

        loadjs('js/leaflet.js', function () {
            loadjs('js/leaflet.markercluster.js', function () {
                loadjs('js/leaflet.awesome-markers.min.js', function () {
                    loadjs('js/L.Control.Locate.min.js', function () {
                        loadjs('js/geomap-shared.js', function () { // --- ADDED
                            const map = init_map(map_id, map_config); // --- ADDED
                            map.setMaxZoom(19);                       // --- ADDED
                            map.scrollWheelZoom.disable();            // --- UPDATED

                            // --- ADDED: shared map rendering (html/js/geomap-shared.js) !--
                            const widgetWidth = () => document.getElementById(map_id)?.clientWidth || 600;
                            geomap = LnmsGeoMap.create(map, {
                                variant: 'compact',
                                clusterRadius: group_radius,
                                geoLinks: enableGeoLinks,
                                filterAlwaysVisible: false,
                                devicePopupOptions: () => ({
                                    maxWidth: Math.max(260, widgetWidth() - 24),
                                    autoPan: true,
                                    keepInView: true,
                                    autoPanPadding: [12, 12],
                                }),
                                linkPopupOptions: () => ({
                                    maxWidth: Math.max(240, widgetWidth() - 24),
                                    autoPan: true,
                                    keepInView: true,
                                    autoPanPadding: [12, 12],
                                }),
                            });

                            populate_map_markers();
                            if (enableGeoLinks) {
                                populate_map_links();
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
                                populate_map_markers();
                                // --- ADDED !--
                                if (enableGeoLinks) {
                                    populate_map_links();
                                }
                                // !--
                            }).on('destroy', function (event) {
                                geomap.destroy(); // --- ADDED
                                destroy_map(map_id);
                            });
                        });
                    });
                });
            });
        });
    })();
</script>