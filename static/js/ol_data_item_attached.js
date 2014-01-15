var projData = new OpenLayers.Projection("EPSG:4326");
var projDisplay = new OpenLayers.Projection("EPSG:900913");
var featuresStyle = new OpenLayers.StyleMap({
"default": new OpenLayers.Style({
pointRadius: 5,
externalGraphic: staticURL + "img/marker.png",
graphicWidth: 17,
graphicHeight: 54,
graphicOpacity: 1,
fillColor: "#df9001",
fillOpacity: 0.5,
strokeColor: "#df9001",
strokeWidth: 2,
//label : "${featurelabel}",
labelYOffset: -7,
fontFamily: "verdana,arial,sans-serif",
fontSize: "11px",
labelOutlineColor: "#fff",
labelOutlineWidth: 4
})});
