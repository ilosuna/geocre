var projData = new OpenLayers.Projection("EPSG:4326");
var projDisplay = new OpenLayers.Projection("EPSG:900913");
var bingApiKey = "AtLWUTairlENwgnZqrT3kk72UQAZfe6XGrO9lQx1NiJ3jbLLgYM8M1My3-_ZhYnS";
var basemap_osm = new OpenLayers.Layer.OSM("OpenLayers");
var basemap_gsat = new OpenLayers.Layer.Google("Google Satellite", {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 20});
var basemap_gphy = new OpenLayers.Layer.Google("Google Physical", {type: google.maps.MapTypeId.TERRAIN});
var basemap_gmap = new OpenLayers.Layer.Google("Google Streets", {numZoomLevels: 20});
var basemap_ghyb = new OpenLayers.Layer.Google("Google Hybrid",{type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20});
var basemap_bingaerial = new OpenLayers.Layer.Bing({ key: bingApiKey, type: "Aerial", name: "Bing Aerial" });
var basemap_binghybrid = new OpenLayers.Layer.Bing({ key: bingApiKey, type: "AerialWithLabels", name: "Bing Hybrid" });
<?php
/*
var basemap_top = new OpenLayers.Layer.MapServer("Topographic map", "http://132.230.69.46/cgi-bin/mapserv", {map:'/var/www/veggis/maps/topographic.map'} );
*/
$basemaps = 'basemap_gsat, basemap_gphy, basemap_gmap, basemap_ghyb, basemap_bingaerial, basemap_binghybrid, basemap_osm';
?>
