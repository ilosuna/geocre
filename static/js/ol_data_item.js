var projData = new OpenLayers.Projection("EPSG:4326");
var projDisplay = new OpenLayers.Projection("EPSG:900913");
var featureLayerStyle = new OpenLayers.StyleMap({
"default": new OpenLayers.Style({
pointRadius: 10,
externalGraphic: staticURL + "img/marker_large.png",
graphicWidth: 41,
graphicHeight: 136,
graphicOpacity: 1,
fillColor: "red",
fillOpacity: 0.5,
strokeColor: "red",
strokeWidth: 2,
//label : "${featurelabel}",
fontFamily: "verdana,arial,sans-serif",
fontSize: "14px",
labelOutlineColor: "#fff",
labelOutlineWidth: 4,
labelYOffset: -7
})});
var vectorLayerStyle = new OpenLayers.StyleMap({
"default": new OpenLayers.Style({
pointRadius: 5,
fillColor: "#df9001",
fillOpacity: 0.3,
strokeColor: "#df9001",
strokeWidth: 1,
labelYOffset: -7, 
fontFamily: "verdana,arial,sans-serif",
fontSize: "11px",
labelOutlineColor: "#fff",
labelOutlineWidth: 4
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
