<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=dashboard#data"><?php echo $lang['dashboard_link']; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=data&amp;data_id=<?php echo $initial_table; ?>"><?php echo $initial_table_name; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=data_item&amp;data_id=<?php echo $initial_table; ?>&amp;id=<?php echo $initial_item; ?>#related-data"><?php echo $lang['data_item_details_title']; ?></a></li>
<li class="active"><?php echo $lang['add_relation_subtitle']; ?></li>
</ul>

<h1><?php echo $lang['add_relation_subtitle']; ?></h1>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<p><?php echo $lang['add_relation_description']; ?></p>

<form id="relationform" action="index.php" method="post">
<div>
<input type="hidden" name="r" value="relation.add" />
<input type="hidden" name="initial_table" value="<?php echo $initial_table; ?>" />
<input type="hidden" name="initial_item" value="<?php echo $initial_item; ?>" />
<input type="hidden" name="selected_table" value="<?php echo $selected_table; ?>" />

<?php if($data_type==1): /* spatial data */ ?>
<ul id="myTab" class="nav nav-tabs">
<li class="active"><a href="#map" data-toggle="mytab"><?php echo $lang['data_map_title']; ?></a></li>
<li><a href="#items" data-toggle="mytab"><?php echo $lang['data_items_title']; ?></a></li>
</ul>

<div id="myTabContent" class="tab-content">
<div id="map" class="mytab-pane mytab-pane-active">

<div id="mapwrapper">
<div id="mapcontainer" class="defaultmap"<?php if(isset($_SESSION[$settings['session_prefix'].'usersettings']['map_height'])): ?> style="height:<?php echo $_SESSION[$settings['session_prefix'].'usersettings']['map_height']; ?>px"<?php endif; ?>>
<div id="maptoolbar">
<div class="buttongroup">
<a id="zoomwheel" href="#" onclick="toggleZoomWheel(); return false" class="<?php if(isset($_SESSION[$settings['session_prefix'].'usersettings']['map_zoomwheel'])&&$_SESSION[$settings['session_prefix'].'usersettings']['map_zoomwheel']==1): ?>active<?php else: ?>inactive<?php endif; ?>" title="<?php echo $lang['zoomwheel_label']; ?>"><?php echo $lang['zoomwheel_label']; ?></a>
</div>
<div id="mapsizetools" class="buttongroup">
<a id="fullscreenmap" href="#" title="<?php echo $lang['fullscreen_map_label']; ?>"><?php echo $lang['fullscreen_map_label']; ?></a>
<a id="reducemap" href="#" title="<?php echo $lang['reduce_map_label']; ?>"><?php echo $lang['reduce_map_label']; ?></a>
<a id="enlargemap" href="#" title="<?php echo $lang['enlarge_map_label']; ?>"><?php echo $lang['enlarge_map_label']; ?></a>
</div>
</div>
<div id="layerinactivenotice"><?php echo $lang['layer_inactive_message']; ?></div>
</div>
</div>

<p class="top-space"><button class="btn btn-primary" type="submit"><?php echo $lang['add_relation_submit']; ?></button></p>


</form>

</div>
<div id="items" class="mytab-pane">

<?php endif; ?>

<?php if(isset($data_items)): ?>
<div class="table-responsive">
<table class="table table-striped table-hover">
<thead>
<tr>
<th>&nbsp;</th>
<?php if(isset($columns)): ?>
<?php foreach($columns as $column): ?>
<th><a href="<?php echo BASE_URL; ?>?r=relation&amp;initial_table=<?php echo $initial_table; ?>&amp;initial_item=<?php echo $initial_item; ?>&amp;selected_table=<?php echo $selected_table; ?>&amp;order=<?php echo $column['name']; ?><?php if($order==$column['name']&&!$asc): ?>&amp;asc=1<?php endif; ?><?php if($data_type==1): ?>#items<?php endif; ?>" title="<?php echo str_replace('[column]', $column['label'], $lang['order_by']); ?>"><?php echo truncate($column['label'], 20, true); ?> <?php if($order==$column['name']&&!$asc): ?> <span class="glyphicon glyphicon-chevron-up"></span><?php elseif($order==$column['name']&&$asc): ?> <span class="glyphicon glyphicon-chevron-down"></span><?php endif; ?></a></th>
<?php endforeach; ?>
<?php else: ?>
<!--<th>ID</th>-->
<?php endif; ?>

<th><a href="<?php echo BASE_URL; ?>?r=relation&amp;initial_table=<?php echo $initial_table; ?>&amp;initial_item=<?php echo $initial_item; ?>&amp;selected_table=<?php echo $selected_table; ?>&amp;order=created<?php if($order=='created'&&!$asc): ?>&amp;asc=1<?php endif; ?><?php if($data_type==1): ?>#items<?php endif; ?>" title="<?php echo str_replace('[column]', $lang['created_column_label'], $lang['order_by']); ?>"><?php echo $lang['created_column_label']; ?><?php if($order=='created'&&!$asc): ?> <span class="glyphicon glyphicon-chevron-up"></span><?php elseif($order=='created'&&$asc): ?> <span class="glyphicon glyphicon-chevron-down"></span><?php endif; ?></a></th>
<th><a href="<?php echo BASE_URL; ?>?r=relation&amp;initial_table=<?php echo $initial_table; ?>&amp;initial_item=<?php echo $initial_item; ?>&amp;selected_table=<?php echo $selected_table; ?>&amp;order=last_edited<?php if($order=='last_edited'&&!$asc): ?>&amp;asc=1<?php endif; ?><?php if($data_type==1): ?>#items<?php endif; ?>" title="<?php echo str_replace('[column]', $lang['last_edited_column_label'], $lang['order_by']); ?>"><?php echo $lang['last_edited_column_label']; ?><?php if($order=='last_edited'&&!$asc): ?> <span class="glyphicon glyphicon-chevron-up"></span><?php elseif($order=='last_edited'&&$asc): ?> <span class="glyphicon glyphicon-chevron-down"></span><?php endif; ?></a></th>

<?php if($data_type==1): ?>
<th><a href="<?php echo BASE_URL; ?>?r=relation&amp;initial_table=<?php echo $initial_table; ?>&amp;initial_item=<?php echo $initial_item; ?>&amp;selected_table=<?php echo $selected_table; ?>&amp;order=geom<?php if($order=='geom'&&!$asc): ?>&amp;asc=1<?php endif; ?><?php if($data_type==1): ?>#items<?php endif; ?>" title="<?php echo str_replace('[column]', $lang['geometry_column_label'], $lang['order_by']); ?>"><?php echo $lang['geometry_column_label']; ?> <?php if($order=='geom'&&!$asc): ?> <span class="glyphicon glyphicon-chevron-up"></span><?php elseif($order=='geom'&&$asc): ?> <span class="glyphicon glyphicon-chevron-down"></span><?php endif; ?></a></th>
<?php endif; ?>
</tr>
</thead>
  
<tbody>
<?php $i=1; foreach($data_items as $data_item): $linked=false; ?>
<tr id="row-<?php echo $data_item['id']; ?>"<?php if($data_type==1 && empty($data_item['wkt'])): ?> class="no-geometry"<?php endif; ?>>
<td><input id="checkbox-<?php echo $data_item['id']; ?>" type="checkbox" name="selected_items[]" value="<?php echo $data_item['id']; ?>" /></td>
<?php if(isset($columns)): ?>
<?php foreach($columns as $column): ?>
<td>
<?php if($column['type']==6): ?>
<?php if($data_item[$column['name']]): ?><span class="glyphicon glyphicon-ok text-success" title="<?php echo $lang['yes']; ?>"></span><?php endif; ?>
<?php elseif(isset($column['choice_labels']) && isset($column['choice_labels'][$data_item[$column['name']]]) && !empty($column['choice_labels'][$data_item[$column['name']]])): ?>
<?php if(!$linked): $linked=true; ?>
<a href="<?php echo BASE_URL; ?>?r=data_item&amp;data_id=<?php echo $table_id; ?>&amp;id=<?php echo $data_item['id']; ?>"><?php echo $column['choice_labels'][$data_item[$column['name']]]; ?></a>
<?php else: ?>
<?php echo $column['choice_labels'][$data_item[$column['name']]]; ?>
<?php endif; ?>
<?php else: ?>
<?php if(!$linked): $linked=true; ?>
<a href="<?php echo BASE_URL; ?>?r=data_item&amp;data_id=<?php echo $table_id; ?>&amp;id=<?php echo $data_item['id']; ?>"><?php echo truncate($data_item[$column['name']], 20, $column['type']==1 ? true : false); ?></a>
<?php else: ?>
<?php echo truncate($data_item[$column['name']], 20, $column['type']==1 ? true : false); ?>
<?php endif; ?>
<?php endif; ?>
</td>
<?php endforeach; ?>
<?php else: ?>
<!--<td><a href="<?php echo BASE_URL; ?>?r=data_item&amp;id=<?php echo $data_item['id']; ?>"><?php echo $data_item['id']; ?></a></td>-->
<?php endif; ?>

<td><span class="date"<?php if($data_item['creator']): ?> title="<?php echo $data_item['creator']; ?>"<?php endif; ?>><?php echo $data_item['created']; ?></span></td>
<td><?php if(isset($data_item['last_edited'])): ?><span class="date"<?php if($data_item['last_editor']): ?> title="<?php echo $data_item['last_editor']; ?>"<?php endif; ?>><?php echo $data_item['last_edited']; ?></span><?php endif; ?></td>

<?php if($data_type==1): ?>
<td><?php if(!empty($data_item['has_geometry'])): ?><span class="glyphicon glyphicon-ok text-success" title="<?php echo $lang['yes']; ?>"></span><?php endif; ?></td>
<?php endif; ?>
</tr>
<?php ++$i; endforeach; ?>
</tbody>
</table>
</div>

<p><button class="btn btn-primary" type="submit"><?php echo $lang['add_relation_submit']; ?></button></p>

<?php if($pagination): ?>
<ul class="pagination">
<?php if($pagination['previous']): ?><li><a href="<?php echo BASE_URL; ?>?r=relation&amp;initial_table=<?php echo $initial_table_data['id']; ?>&amp;initial_item=<?php echo $initial_item; ?>&amp;selected_table=<?php echo $selected_table; ?>&amp;p=<?php echo $pagination['previous']; ?>&amp;order=<?php echo $order; ?>&amp;asc=<?php echo $asc; ?>" title="<?php echo $lang['previous_page_title']; ?>"><span class="glyphicon glyphicon-chevron-left"></span></a></li><?php endif; ?>
<?php foreach($pagination['items'] as $item): ?>
<?php if($item==0): ?><li><span>&hellip;</span></li><?php elseif($item==$pagination['current']): ?><li class="active"><span><?php echo $item; ?></span></li><?php else: ?><li><a href="<?php echo BASE_URL; ?>?r=relation&amp;initial_table=<?php echo $initial_table_data['id']; ?>&amp;initial_item=<?php echo $initial_item; ?>&amp;selected_table=<?php echo $selected_table; ?>&amp;p=<?php echo $item; ?>&amp;order=<?php echo $order; ?>&amp;asc=<?php echo $asc; ?>"><?php echo $item; ?></a></li><?php endif; ?>
<?php endforeach; ?>
<?php if($pagination['next']): ?><li><a href="<?php echo BASE_URL; ?>?r=relation&amp;initial_table=<?php echo $initial_table_data['id']; ?>&amp;initial_item=<?php echo $initial_item; ?>&amp;selected_table=<?php echo $selected_table; ?>&amp;p=<?php echo $pagination['next']; ?>&amp;order=<?php echo $order; ?>&amp;asc=<?php echo $asc; ?>" title="<?php echo $lang['next_page_title']; ?>"><span class="glyphicon glyphicon-chevron-right"></span></a></li><?php endif; ?>  
</ul>
<?php endif; ?>

<?php else: ?>
<div class="alert alert-warning"><?php echo $lang['db_table_empty']; ?></div>
<?php endif; ?>

</div>

<?php if($data_type==1): /* spatial data */ ?>
</div>
</div>
<?php endif; ?>

</form>

<?php if($data_type==1): /* spatial data - display map */ ?>
<?php if(isset($basemaps)): ?>
<?php foreach($basemaps as $basemap): ?>
<?php
$js[] = 'var basemap_'.$basemap['id'].' = new OpenLayers.Layer.'.$basemap['properties'].'
map.addLayer(basemap_'.$basemap['id'].');';
?>
<?php endforeach; ?>
<?php endif; ?>

<?php
$js[] = 'navigationControl = new OpenLayers.Control.Navigation({"zoomWheelEnabled":false});
map.addControl(navigationControl);
if(document.getElementById("zoomwheel") && document.getElementById("zoomwheel").className == "active") navigationControl.enableZoomWheel();';
?>

<?php if($layer_overview && $min_scale): ?>
<?php
if($layer_overview==1) $cluster_strategy = ', new OpenLayers.Strategy.Cluster()';
else $cluster_strategy = '';
if($layer_overview==1) $overview_style = 'overviewStylePointCluster';
else $overview_style = 'overviewStyleConvexHull';
$js[] = 'overviewLayer = new OpenLayers.Layer.Vector("'.ol_encode_label($lang['layer_overview_label']).'", {
projection: projData,
strategies: [new OpenLayers.Strategy.Fixed()'.$cluster_strategy.'],
protocol: new OpenLayers.Protocol.HTTP({ url: "'.BASE_URL.'",
                                         params: { r:"json_data", table:"'.$table_id.'" },
                                         format: new OpenLayers.Format.GeoJSON() }),
                                         maxScale:'.$min_scale.',
                                         units: "m",
                                         styleMap:'.$overview_style.',
                                         displayInLayerSwitcher:false });';
?>
<?php endif; ?>
<?php if($auxiliary_layer_1): ?>
<?php
$js[] = 'auxiliary_layer_1 = new OpenLayers.Layer.Vector("'.ol_encode_label($auxiliary_layer_1_title).'", {
projection: projData,        
strategies: [new OpenLayers.Strategy.BBOX()],
protocol: new OpenLayers.Protocol.HTTP({ url: "'.BASE_URL.'",
                                         params: { r:"json_data", table:"'.$auxiliary_layer_1.'" },
                                         format: new OpenLayers.Format.GeoJSON() }),
    styleMap:auxiliaryLayerStyle
});
map.addLayer(auxiliary_layer_1);';
?>
<?php endif; ?>
<?php
$res_factor = isset($redraw) ? '{resFactor:1}' : '';
$min_scale_code = isset($min_scale) ? 'minScale:'.$min_scale.',' : '';
$max_scale_code = isset($max_scale) ? 'maxScale:'.$max_scale.',' : '';
$js[] = 'vectorLayer = new OpenLayers.Layer.Vector("'.ol_encode_label($subtitle).'", {
projection: projData,        
strategies: [new OpenLayers.Strategy.BBOX('.$res_factor.')],
protocol: new OpenLayers.Protocol.HTTP({ url: "'.BASE_URL.'",
                                         params: { r:"json_data", table:"'.$table_id.'", attributes:true },
                                         format: new OpenLayers.Format.GeoJSON() }),
                                         '.$min_scale_code.'
                                         '.$max_scale_code.'
                                         units: "m",
                                         styleMap:featuresStyle });';
?>
<?php if($layer_overview): ?>
<?php $js[] = 'map.addLayer(overviewLayer);'; ?>
<?php endif; ?>
<?php $js[] = 'map.addLayer(vectorLayer);'; ?>

<?php
$js[] = 'if(vectorLayer.calculateInRange()) document.getElementById("layerinactivenotice").style.display = "none";
else document.getElementById("layerinactivenotice").style.display = "block";';
?>
<?php if(isset($current_position)): ?>
<?php $js[] = 'map.setCenter(new OpenLayers.LonLat('.$current_position['longitude'].','.$current_position['latitude'].'), '.$current_position['zoomlevel'].');'; ?>
<?php elseif($spatial_info['extent']): ?>
<?php $js[] = 'extentLayer = new OpenLayers.Layer.Vector("Extent");
var extent = new OpenLayers.Format.WKT({"internalProjection":projDisplay,"externalProjection":projData}).read("'.$spatial_info['extent'].'");
extentLayer.addFeatures([extent]);
map.zoomToExtent(extentLayer.getDataExtent());'; ?>
<?php else: ?>
<?php $js[] = 'map.setCenter(new OpenLayers.LonLat('.$settings['default_longitude'].','.$settings['default_latitude'].').transform(projData, projDisplay), '.$settings['default_zoomlevel'].');'; ?>
<?php endif; ?>

<?php

if($permission['write']) $feature_options = '<p class=\"fib-options\"><a class=\"btn btn-primary btn-xs\" href=\"'.BASE_URL.'?r=edit_data_item.edit&amp;data_id='.$table_id.'&amp;id="+feature.attributes.id+"\" title=\"'.$lang['edit'].'\"><span class=\"glyphicon glyphicon-pencil\"></span></a>&nbsp;<a class=\"btn btn-danger btn-xs\" href=\"'.BASE_URL.'?r=data.delete&amp;data_id='.$table_id.'&amp;id="+feature.attributes.id+"\" onclick=\"return delete_confirm(this, \''.rawurlencode($lang['delete_data_item_message']).'\')\" title=\"'.$lang['delete'].'\"><span class=\"glyphicon glyphicon-remove\"></span></a></p>';
else $feature_options = '';

$feature_info = '<p>"+featureInfo(feature.attributes.area, feature.attributes.perimeter, feature.attributes.length, feature.attributes.latitude, feature.attributes.longitude)+"</p>';


$js[] = 'map.addControl(new OpenLayers.Control.LayerSwitcher());
vectorLayer.events.on({
featureselected: function(event) {
//var feature = event.feature;
getSelected(vectorLayer.selectedFeatures);
},
featureunselected: function(event) {
//var feature = event.feature;
getSelected(vectorLayer.selectedFeatures);
}});

selectControl = new OpenLayers.Control.SelectFeature( [ vectorLayer ], { clickout:true, toggle:true, multiple:true, hover:false } );
map.addControl(selectControl);
selectControl.activate();
if(typeof(selectControl.handlers) != "undefined") selectControl.handlers.feature.stopDown = false;

function getSelected(selection)
 {
  //var s = "";
  $("input[name=\"selected_items[]\"]").remove();
  for(var i=selection.length-1; i>=0; --i)
   {
    //s += selection[i].attributes.id; 
    $("#relationform").append("<input type=\"hidden\" name=\"selected_items[]\" value=\""+selection[i].attributes.id+"\">");
   }
  //alert(s);
 }';
?>  
<?php endif; ?>






