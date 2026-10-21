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
{{-- --- ADDED: shared map styling --}}
@include('map.geomap-shared-styles', ['scope' => '#fullscreen-map'])
<style>
/* --- ADDED: fullscreen-only styling (shared rules: map/geomap-shared-styles) !-- */
html, body, #fullscreen-map {
   height: 100%;
   width: 100%;
   padding-bottom: 0;
   margin-bottom: 0;
   container-type: inline-size;
}
#fullscreen-map .leaflet-tooltip.leaflet-link-tooltip {
  border: var(--wm-popup-bg);
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
<script src="js/geomap-shared.js"></script>{{-- --- ADDED --}}
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
    // --- ADDED: shared map rendering (html/js/geomap-shared.js) !--
    device_map.setMaxZoom(19);

    const link_layer = L.layerGroup();
    device_map.addLayer(link_layer);

    const geoLinksEnabled = {{ ($show_netmap && $netmap_source == 'xdp') ? 'true' : 'false' }};

    const geomap = LnmsGeoMap.create(device_map, {
        variant: 'full',
        clusterRadius: {{ (int) $group_radius }},
        geoLinks: geoLinksEnabled,
        linkContainer: link_layer,
        filterAlwaysVisible: true,
        devicePopupOptions: function () {
            const smallTouchscreen = window.matchMedia('(max-width: 768px)').matches
                && (window.matchMedia('(pointer: coarse)').matches || 'ontouchstart' in window);
            return { autoPan: !smallTouchscreen };
        },
    });

    // Dependency ("depends") lines (fullscreen-only)
    const depends_lines = Object.create(null);
    device_map.on('zoomend', function () {
        for (const line_id in depends_lines) {
            geomap.rescaleLine(depends_lines[line_id]);
        }
    });
    // !--

    function checkParentLink(device, parent) {
        const line_id = "dev." + device["id"] + "." + parent["id"]; // --- UPDATED
        const latlngs = [new L.LatLng(device["lat"], device["lng"]), new L.LatLng(parent["lat"], parent["lng"])];
        if (line_id in depends_lines) {
            depends_lines[line_id].setLatLngs(latlngs);
        } else {
            const line = new L.Polyline(latlngs, {
                color: "blue",
                weight: 2,
                opacity: 0.8,
                smoothFactor: 1
            });
            depends_lines[line_id] = line;
            link_layer.addLayer(line); // --- UPDATED
        }
        return line_id;
    }

    function refreshMap() {
        var device_group = {{ $group_id ? $group_id : 'null' }};
@if($show_netmap && $netmap_source == 'depends')
        $.post( '{{ route('maps.getdevices') }}', {disabled: 0, location_valid: 1, disabled_alerts: {{$netmap_include_disabled_alerts}}, group: device_group, link_type: '{{$netmap_source}}'})
@else
        $.post( '{{ route('maps.getdevices') }}', {disabled: 0, location_valid: 1, disabled_alerts: {{$netmap_include_disabled_alerts}}, group: device_group})
@endif
            .done(function( data ) {
                geomap.setDevices(data); // --- UPDATED: marker add/update/remove in geomap-shared.js

                // If we have requested parent data, add/update lines
                var links = Object.create(null); // --- UPDATED
                Object.values(data).forEach(function (device) {
                    $.each( device["parents"], function( parent_idx, parent_id ) {
                        if (parent_id in data && (data[parent_id]["lat"] != device["lat"] || data[parent_id]["lng"] != device["lng"])) {
                            var line_id = checkParentLink(device, data[parent_id]);
                            links[line_id] = true;
                        }
                    });
                });
                $("#countdown").css("border", "1px solid green");
@if($show_netmap && $netmap_source == 'depends')
                // Remove any links that have disappeared
                for (const line_id in depends_lines) {
                    if (! (line_id in links)) {
                        link_layer.removeLayer(depends_lines[line_id]); // --- UPDATED
                        delete depends_lines[line_id]; // --- ADDED
                    }
                }
@endif
            })
            .fail(function() {
                $("#countdown").css("border", "1px solid red");
            });
@if($show_netmap && $netmap_source == 'xdp')
        $.post( '{{ route('maps.getgeolinks') }}', {link_type: '{{$netmap_source}}', group: device_group})
            .done(function( data ) {
                geomap.setGeoLinks(data); // --- UPDATED
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