var projData = new OpenLayers.Projection("EPSG:4326");
var projDisplay = new OpenLayers.Projection("EPSG:900913");
var featuresStyle = new OpenLayers.StyleMap({
"default": new OpenLayers.Style({
pointRadius: 5,
externalGraphic: staticURL + "img/marker.png",
graphicWidth: 17,
graphicHeight: 56,
graphicOpacity: 1,
fillColor: "#df9001",
fillOpacity: 0.5,
strokeColor: "#df9001",
strokeWidth: 2,
})});
var overviewStylePointCluster = new OpenLayers.StyleMap({
"default": new OpenLayers.Style({
pointRadius: 10,
fillColor: "#ffcc66",
fillOpacity: 1,
strokeColor: "#cc6633",
strokeWidth: 2,
})});
var overviewStyleConvexHull = new OpenLayers.StyleMap({
"default": new OpenLayers.Style({
fillColor: "#ffff00",
fillOpacity: 0,
strokeColor: "#ffff00",
strokeWidth: 3,
strokeDashstyle: "5 10 5 10",
})});
var auxiliaryLayerStyle = new OpenLayers.StyleMap({
"default": new OpenLayers.Style({
pointRadius: 10,
fillColor: "#ffff00",
fillOpacity: 0,
strokeColor: "#ffff00",
strokeWidth: 2,
//strokeDashstyle: "5 10 5 10",
labelYOffset: -7, 
fontFamily: "verdana,arial,sans-serif",
fontSize: "11px",
labelOutlineColor: "#fff",
labelOutlineWidth: 4
})});
map = new OpenLayers.Map("mapcontainer", { projection:projDisplay, controls:[new OpenLayers.Control.Zoom(), new OpenLayers.Control.ScaleLine(), new OpenLayers.Control.Attribution()] });

map.events.on({ "zoomend": function (e) {
if(vectorLayer.calculateInRange()) document.getElementById("layerinactivenotice").style.display = "none";
else document.getElementById("layerinactivenotice").style.display = "block";
}
});
