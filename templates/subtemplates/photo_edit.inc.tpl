<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=photos"><?php echo $lang['photos_subtitle']; ?></a></li>
<li class="active"><?php echo $subtitle; ?></li>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<?php if(isset($photo)): ?>

<?php if(isset($uploaded)): ?>
<p><?php echo $lang['photo_upload_step_2_message']; ?></p>
<?php endif; ?>

<script type="text/javascript">
var requiredFields = new Array();
requiredFields[0] = new Object();
requiredFields[0]['id'] = 'title';
requiredFields[0]['message'] = '<?php echo rawurlencode(strip_tags($lang['error_photo_no_title'])); ?>';
requiredFields[1] = new Object();
requiredFields[1]['id'] = 'wkt';
requiredFields[1]['message'] = '<?php echo rawurlencode(strip_tags($lang['error_photo_no_location'])); ?>';
</script>

<form id="uploadform" action="index.php" method="post">
<div>
<input type="hidden" name="r" value="photos" />
<input type="hidden" name="edit_photo" value="<?php echo $photo['id']; ?>" />
<input type="hidden" name="filename" value="<?php echo $photo['filename']; ?>" />
<input type="hidden" id="wkt" name="wkt" value="<?php echo $photo['wkt']; ?>" />

<table class="data">


<tr>
<td class="name"><?php echo $lang['photo_label']; ?></td>
<td class="value"><img class="thumbnail" src="<?php echo BASE_URL.SMALL_PHOTOS_DIR.$photo['filename']; ?>" alt="<?php echo $photo['title']; ?>" /></td>
</tr>

<tr>
<td class="name"><label for="title"><?php echo $lang['photo_title_label']; ?></label></td>
<td class="value"><input id="title" type="text" name="title" value="<?php echo $photo['title']; ?>" class="photodata" size="50" />  <span class="small">(<?php echo $lang['required_label']; ?>)</span></td>
</tr>

<tr>
<td class="name"><label for="description"><?php echo $lang['photo_description_label']; ?></label></td>
<td class="value"><textarea cols="10" rows="4" id="description" type="text" name="description" class="photodata"><?php echo $photo['description']; ?></textarea></td>
</tr>

<tr>
<td class="name"><label for="description"><?php echo $lang['photo_geotagging_label']; ?></label><br><span class="description"><?php echo $lang['photo_geotagging_description']; ?></span></td>
<td class="value"><div id="geotagmap"></div></td>
</tr>

<tr>
<td class="name">&nbsp;</td>
<td class="value"><input type="submit" name="edit_submit" value="<?php echo $lang['save_submit']; ?>" onclick="return isFormComplete(requiredFields)" /></td>
</tr>

</table>

</div>
</form>

<script type="text/javascript">
<?php include(BASE_PATH.'templates/subtemplates/default_map.inc.tpl'); ?>

var vectorLayerStyle = new OpenLayers.StyleMap({
"default": new OpenLayers.Style({
externalGraphic: "<?php echo STATIC_URL; ?>img/marker_photo.png",
graphicWidth: 25,
graphicHeight: 41,
graphicYOffset: -41,
fillOpacity: 0.5,
}),
"select": new OpenLayers.Style({
fillOpacity: 1,
})
});

var wkt = document.getElementById('wkt').value;
vectorLayer = new OpenLayers.Layer.Vector("Geotagging", { styleMap: vectorLayerStyle });
var map = new OpenLayers.Map('geotagmap', { projection: projDisplay, controls:[new OpenLayers.Control.Zoom(), new OpenLayers.Control.Navigation({'zoomWheelEnabled':true}), new OpenLayers.Control.ScaleLine()]});
map.addLayers([<?php echo $basemaps; ?>, vectorLayer]);
map.addControl(new OpenLayers.Control.LayerSwitcher());
modifyControl = new OpenLayers.Control.ModifyFeature(vectorLayer);
map.addControls([modifyControl]);

drawControl = new OpenLayers.Control.DrawFeature(vectorLayer, OpenLayers.Handler.Point);

map.addControl(drawControl);

map.addControl(new OpenLayers.Control.MousePosition( {displayProjection:projData} ));

vectorLayer.events.on({ "featuremodified":modifyReady, "afterfeaturemodified":modifyReady, "featureadded":addReady });

if(wkt!='') // edit
 {
  var wkt = document.getElementById('wkt').value;
  var polygonFeature = new OpenLayers.Format.WKT({'internalProjection':projDisplay,'externalProjection':projData}).read(wkt);
  vectorLayer.addFeatures([polygonFeature]);
  //map.zoomToExtent(vectorLayer.getDataExtent().transform(projWGS84, projSMP));
  map.zoomToExtent(vectorLayer.getDataExtent());
  if(map.zoom > 15) map.zoomTo(15);
  modifyControl.selectControl.select(vectorLayer.features[0]);
  modifyControl.activate();
 }
else // add / empty geometry
 {
  <?php if(isset($current_position)): ?>
  map.setCenter(new OpenLayers.LonLat(<?php echo $current_position['longitude']; ?>,<?php echo $current_position['latitude']; ?>), <?php echo $current_position['zoomlevel']; ?>);
  <?php elseif(isset($features)): ?>
  map.zoomToExtent(featuresLayer.getDataExtent());
  if(map.zoom > 15) map.zoomTo(15); 
  <?php else: ?>
  map.setCenter(new OpenLayers.LonLat(<?php echo $settings['default_longitude']; ?>,<?php echo $settings['default_latitude']; ?>).transform(projData, projDisplay), <?php echo $settings['default_zoomlevel']; ?>);
  <?php endif; ?>
  drawControl.activate();
 }	

function modifyReady(feature)
 {
  modifyControl.selectControl.select(vectorLayer.features[0]);
  updateForm(feature);
 }

function addReady(feature)
 {
  drawControl.deactivate();
  modifyControl.selectControl.select(vectorLayer.features[0]);
  modifyControl.activate();
  updateForm(feature);
 }

function updateForm(feature)
 {
  var feature = vectorLayer.features[0];
  var geometry = feature.geometry.clone();
  geometry.transform(projDisplay, projData);
  document.getElementById('wkt').value = geometry;
 }
</script>

<?php else: ?>
<p class="caution"><?php echo $lang['error_photo_not_available']; ?></p>
<?php endif; ?>
