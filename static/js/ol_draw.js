var projData = new OpenLayers.Projection("EPSG:4326");
var projDisplay = new OpenLayers.Projection("EPSG:3857");
var vectorLayerStyle = new OpenLayers.StyleMap({
                "default": new OpenLayers.Style({
                    externalGraphic: staticURL+"img/marker_edit_large.png",
                    graphicWidth: 41,
                    graphicHeight: 136,
                    graphicOpacity: 1,                    
                    fillColor: "yellow",
                    fillOpacity: 0.5,
                    strokeColor: "red",
                    strokeWidth: 2,
                    pointRadius: 5
                }),
                "select": new OpenLayers.Style({
                    fillColor: "blue",
                    fillOpacity: 0.5,
                    strokeColor: "red",
                    strokeWidth: 3,
                    pointRadius: 5
                }),
                "vertex": new OpenLayers.Style({
                    externalGraphic: false,
                    fillColor: "yellow",
                    fillOpacity: 0.8,
                    strokeColor: "red",
                    strokeWidth: 2,
                    pointRadius: 5,

                }),                                
                });

var featuresLayerStyle = new OpenLayers.StyleMap({
                "default": new OpenLayers.Style({
                    pointRadius: 5,
                    fillColor: "#df9001",
                    fillOpacity: 0.2,
                    strokeColor: "#df9001",
                    strokeWidth: 1,
                    //label : "${name}",
                    fontFamily: "verdana,arial,sans-serif",
                    fontSize: "11px",
                    labelOutlineColor: "#fff",
                    labelOutlineWidth: 4
                })
                });

var indicatorLayerStyle = new OpenLayers.StyleMap({
                "default": new OpenLayers.Style({
                    pointRadius: 100,
                    externalGraphic: staticURL + "img/marker_cross.png",
                    graphicWidth: 101,
                    graphicHeight: 101,
                    graphicOpacity: 1,
                    fillColor: "#fff",
                    fillOpacity: 0,
                    strokeColor: "#ffff00",
                    strokeWidth: 3
                })
                });

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

var wkt = document.getElementById("_wkt").value;

var map = new OpenLayers.Map("mapcontainer", { projection: projDisplay, controls:[new OpenLayers.Control.Zoom(), new OpenLayers.Control.ScaleLine()] });

vectorLayer = new OpenLayers.Layer.Vector("Feature Layer", { styleMap: vectorLayerStyle });
map.addLayer(vectorLayer);

map.addControl(new OpenLayers.Control.LayerSwitcher());
modifyControl = new OpenLayers.Control.ModifyFeature(vectorLayer, {vertexRenderIntent:"vertex"});
map.addControls([modifyControl]);

navigationControl = new OpenLayers.Control.Navigation({"zoomWheelEnabled":false});
map.addControl(navigationControl);
if(document.getElementById("zoomwheel") && document.getElementById("zoomwheel").className == "active") navigationControl.enableZoomWheel();

drawControls = {
  point: new OpenLayers.Control.DrawFeature(vectorLayer, OpenLayers.Handler.Point),
  line: new OpenLayers.Control.DrawFeature(vectorLayer, OpenLayers.Handler.Path),
  polygon: new OpenLayers.Control.DrawFeature(vectorLayer, OpenLayers.Handler.Polygon)
};

for(key in drawControls)
 {
  map.addControl(drawControls[key]);
  if(document.getElementById(key) && document.getElementById(key).className == "active") drawControl = drawControls[key]; 
 }

map.addControl(new OpenLayers.Control.MousePosition({ displayProjection:projData,
                                                      formatOutput: function(lonLat) { var digits = parseInt(this.numDigits);
                                                                                       var newHtml = this.prefix + lonLat.lat.toFixed(digits) + this.separator + lonLat.lon.toFixed(digits) + this.suffix;
                                                                                       return newHtml; }
                                                    }));

vectorLayer.events.on({"featuremodified":updateForm, "featureadded":addReady});

OpenLayers.Event.observe(document, "keydown", function(evt) {
    var handled = false;
    switch (evt.keyCode) {
        case 90: // z
            if (evt.metaKey || evt.ctrlKey) {
                drawControl.undo();
                handled = true;
            }
            break;
        case 89: // y
            if (evt.metaKey || evt.ctrlKey) {
                drawControl.redo();
                handled = true;
            }
            break;
        case 27: // esc
            drawControl.cancel();
            handled = true;
            break;
    }
    if (handled) {
        OpenLayers.Event.stop(evt);
    }
});

function setDrawTool(type)
 {
  for(key in drawControls)
   {
    drawControl = drawControls[key];
    if(type == key)
     {
      drawControl.activate();
      document.getElementById(key).className = "active";
     }
    else
     {
      drawControl.deactivate();
      if(document.getElementById(key)) document.getElementById(key).className = "inactive";
     }
   }
 }

function addReady(feature)
 {
  for(key in drawControls)
   {
    drawControls[key].deactivate();
   }
  modifyControl.activate();
  updateForm(feature);
  var drawControlsElement = document.getElementById("drawcontrols");
  if(drawControlsElement) drawControlsElement.style.display = "none";
 }

function updateForm(feature)
 {
  var xfeature = vectorLayer.features[0];
  var geometry = xfeature.geometry.clone();
  geometry.transform(projDisplay, projData);
  document.getElementById("_wkt").value = geometry;
 }

$("a[data-set-position]").click(function(e)
 {
  e.preventDefault();
  var posErrMsg = $(this).data("set-position-error") ? decodeURIComponent($(this).data("set-position-error")) : "Error!";
  var latlong = prompt(decodeURIComponent($(this).data("set-position")));
  
  if(!latlong) return false;
  parts = latlong.split(",");
  if(parts.length!=2)
    {
     alert(posErrMsg);
     return false;
    }
  lat = parseFloat(parts[0].trim());
  long = parseFloat(parts[1].trim());
  if(isNaN(lat)||isNaN(long))
   {
    alert(posErrMsg);
    return false;
   }
  if(lat<-90||lat>90||long<-180||long>180)
   {
    alert(posErrMsg);
    return false;
   }
  var position = new OpenLayers.LonLat(long,lat).transform(projData, projDisplay);
  var positionPoint = new OpenLayers.Geometry.Point(position.lon, position.lat);
  if(typeof positionIndicator === "undefined")
   {
    positionIndicator = new OpenLayers.Layer.Vector("Position Indicator", { styleMap: indicatorLayerStyle });
    map.addLayer(positionIndicator);
   }
  positionIndicator.removeAllFeatures();
  positionIndicator.addFeatures([new OpenLayers.Feature.Vector(positionPoint)]);  
  map.setCenter(position, 15);
 });
