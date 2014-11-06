<?php if($item_exists): ?>
<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=dashboard#data"><?php echo $lang['dashboard_link']; ?></a></li>
<?php if($item_data['fk'] && $table_data['parent_table']): /* attached data */ ?>
<li><a href="<?php echo BASE_URL; ?>?r=data&amp;data_id=<?php echo $table_data['parent_table']; ?>"><?php echo $table_data['parent_title']; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=data_item&amp;data_id=<?php echo $table_data['parent_table']; ?>&amp;id=<?php echo $item_data['fk']; ?>#attached-data"><?php echo $lang['data_item_details_title']; ?></a></li>
<li class="active"><?php echo str_replace('[table]', $table_data['title'], $lang['attached_data_item_subtitle']); ?></li>
<?php else: ?>
<li><a href="<?php echo BASE_URL; ?>?r=data&amp;data_id=<?php echo $table_id; ?>"><span class="nowrap"><?php echo $table_title; ?></span></a></li>
<li class="active"><?php echo $lang['data_item_details_title']; ?></li>
<?php endif; ?>
</ul>

<?php if(isset($attached_item_added)): ?>
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<p><span class="glyphicon glyphicon-ok"></span> <?php echo $lang['item_added_message']; ?></p>
</div>
<?php endif; ?>

<?php if(isset($item_added)): ?>
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<p><span class="glyphicon glyphicon-ok"></span> <?php echo $lang['item_added_message']; ?></p>
<p><a class="btn btn-success" href="<?php echo BASE_URL; ?>?r=edit_data_item.add&amp;data_id=<?php echo $table_id; ?>"<?php if($data_type==1): ?> onclick="this.href+='&current_position='+map.center.lon+','+map.center.lat+','+map.zoom"<?php endif; ?>><span class="glyphicon glyphicon-plus"></span> <?php echo $lang['add_another_item_link']; ?></a></p>
</div>
<?php endif; ?>

<div class="row">
<div class="col-sm-6"><h1><?php if(isset($item_title)): ?><?php echo $item_title; ?><?php else: ?><?php echo $lang['data_item_details_title']; ?><?php endif; ?></h1></div>
<div class="col-sm-6">
<?php if(!$readonly && $permission['write']): ?>
<div class="btn-top-right">
<a class="btn btn-primary" href="<?php echo BASE_URL; ?>?r=edit_data_item.edit&amp;data_id=<?php echo $table_id; ?>&amp;id=<?php echo $item_data['id']; ?>"><span class="glyphicon glyphicon-pencil"></span> <?php echo $lang['edit_item_link']; ?></a>
<a class="btn btn-danger" href="<?php echo BASE_URL; ?>?r=data.delete&amp;data_id=<?php echo $table_id; ?>&amp;id=<?php echo $item_data['id']; ?>" data-delete-confirm="<?php echo rawurlencode($lang['delete_data_item_message']); ?>"><span class="glyphicon glyphicon-remove"></span> <?php echo $lang['delete_item_link']; ?></a>
<?php if(isset($help)): ?>
<a class="btn btn-default" href="index.php?r=help.<?php echo $help; ?>" data-toggle="modal" data-target="#modal_help" data-input="content"><span class="glyphicon glyphicon-question-sign"></span> <?php echo $lang['help']; ?></a>
<?php endif; ?>

</div>
<?php endif; ?>

</div>
</div>

<?php if(isset($item_data)): ?>

<?php if(isset($attached_data) || isset($related_data) || isset($item_images)): ?>
<ul id="mytab" class="nav nav-tabs">
<li class="active"><a href="#item" data-toggle="mytab"><?php echo $lang['item_details_title']; ?></a></li>
<?php if(isset($attached_data)): ?>
<li><a href="#attached-data" data-toggle="mytab"><?php echo $lang['item_attached_data']; ?></a></li>
<?php endif; ?>
<?php if(isset($related_data)): ?>
<li><a href="#related-data" data-toggle="mytab"><?php echo $lang['item_related_data']; ?></a></li>
<?php endif; ?>
<?php if(isset($item_images)): ?>
<li><a href="#images" data-toggle="mytab"><?php echo $lang['item_images']; ?></a></li>
<?php endif; ?>
</ul>
<?php endif; ?>

<?php if(isset($attached_data) || isset($related_data) || isset($item_images)): ?>

<div id="myTabContent" class="tab-content">

<div id="item" class="mytab-pane mytab-pane-active">
<?php endif; ?>

<!--<div class="table-responsive">-->
<table class="table">

<?php if($data_type==1): $displayed_maps[]=$table_id; /* spatial data - show map box:  */ ?>
<tr>
<td colspan="2" class="map">
<?php if(!empty($spatial_item_data['wkt'])): ?>

<?php if(isset($_SESSION[$settings['session_prefix'].'usersettings']['disable_map']) && $_SESSION[$settings['session_prefix'].'usersettings']['disable_map']): ?>
<div class="no-map">
<p><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $lang['map_disabled']; ?></p>
</div>
<div class="text-center bottom-space-small">
<a class="btn btn-primary" href="<?php echo BASE_URL; ?>?r=data_item&amp;data_id=<?php echo $table_id; ?>&amp;id=<?php echo $item_data['id']; ?>&amp;disable_map=0"><?php echo $lang['enable_map']; ?></a>
</div>
<?php else: ?>

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
<a id="disablemap" href="<?php echo BASE_URL; ?>?r=data_item&amp;data_id=<?php echo $table_id; ?>&amp;id=<?php echo $item_data['id']; ?>&amp;disable_map=1" title="<?php echo $lang['disable_map']; ?>">[x]</a>
</div>
</div>

<?php endif; ?>

<?php else: ?>
<div class="no-map">
<p><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $lang['no_geometry_message']; ?></p>
</div>
<?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if(isset($custom_item_data)): ?>

<?php $alter=false; foreach($custom_item_data as $custom_item): ?>
<?php if($custom_item['type']==0||$custom_item['priority']!=1) { if($alter) $alter=false; else $alter=true; } ?>
<?php if($custom_item['type']==0): ?>
<tr class="<?php if($custom_item['priority']==2): ?>high-priority-section<?php elseif($custom_item['priority']==1): ?>low-priority-section<?php if($alter): ?> alter<?php endif; ?><?php else: ?>default-priority-section<?php endif; ?>">
<td colspan="2" class="<?php if($custom_item['priority']==2): ?>mainsection<?php else: ?>section<?php endif; ?>"><?php if(empty($custom_item['label'])): ?>&nbsp;<?php else: ?><?php echo $custom_item['label']; ?><?php endif; ?><?php if($custom_item['description']): ?><br /><span class="description"><?php echo $custom_item['description']; ?></span><?php endif; ?></td>
</tr>
<?php else: ?>
<tr class="<?php if($custom_item['priority']==2): ?>high-priority<?php elseif($custom_item['priority']==1): ?>low-priority<?php else: ?>default-priority<?php endif; ?><?php if($alter): ?> alter<?php endif; ?>">
<td class="key"><strong><?php echo $custom_item['label']; ?></strong><?php if($custom_item['description']): ?><br /><span class="description"><?php echo $custom_item['description']; ?></span><?php endif; ?></td>
<td class="value">
<?php if($custom_item['type']==6): ?>
<?php if($custom_item['value']): ?><span class="glyphicon glyphicon-ok text-success"></span><?php endif; ?>
<?php elseif(isset($custom_item['choice_labels']) && isset($custom_item['choice_labels'][$custom_item['value']]) && !empty($custom_item['choice_labels'][$custom_item['value']])): ?>
<?php echo $custom_item['choice_labels'][$custom_item['value']]; ?>
<?php elseif(isset($custom_item['_related_'])): ?>
<?php if($custom_item['value']): ?>
<a href="<?php echo BASE_URL; ?>?r=data_item&amp;data_id=<?php echo $custom_item['_related_']['table']; ?>&amp;id=<?php echo $custom_item['_related_']['item']; ?>"><?php echo $custom_item['value']; ?></a>
<?php else: ?><?php endif; ?>
<?php else: ?>
<?php echo $custom_item['value']; ?>
<?php endif; ?>
</td>
</tr>
<?php endif; ?>
<?php endforeach; ?>

<?php endif; ?>

</table>
<!--</div>-->


<!--<div class="table-responsive">-->
<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $lang['item_metadata_label']; ?></th>
</tr>
</thead>
<tbody>
<?php if(isset($spatial_item_data['latlong'])): ?>
<tr>
<td class="key"><?php echo $lang['coordinates_column_label']; ?>:</td>
<td class="value"><?php echo $spatial_item_data['latlong']['dms']; ?> / <?php echo $spatial_item_data['latlong']['dec']; ?><?php if($spatial_item_data['geometry_type']!='POINT'): ?> (<?php echo $lang['centroid_label']; ?>)<?php endif; ?></td>
</tr>
<?php endif; ?>
<?php if(isset($spatial_item_data['area']) && $spatial_item_data['area']['raw']>0): ?>
<tr>
<td class="key"><?php echo $lang['area_column_label']; ?>:</td>
<td class="value"><?php echo $spatial_item_data['area']['sqkm']; ?> km² / <?php echo $spatial_item_data['area']['ha']; ?> ha / <?php echo $spatial_item_data['area']['sqm']; ?> m²</td>
</tr>
<?php endif; ?>
<?php if(isset($spatial_item_data['perimeter']) && $spatial_item_data['perimeter']['raw']>0): ?>
<tr>
<td class="key"><?php echo $lang['perimeter_column_label']; ?>:</td>
<td class="value"><?php echo $spatial_item_data['perimeter']['km']; ?> km / <?php echo $spatial_item_data['perimeter']['m']; ?> m</td>
</tr>
<?php endif; ?>
<?php if(isset($spatial_item_data['length']) && $spatial_item_data['length']['raw']>0): ?>
<tr>
<td class="key"><?php echo $lang['length_column_label']; ?>:</td>
<td class="value"><?php echo $spatial_item_data['length']['km']; ?> km / <?php echo $spatial_item_data['length']['m']; ?> m</td>
</tr>
<?php endif; ?>

<tr>
<td class="key"><?php echo $lang['created_column_label']; ?>:</td>
<td class="value"><?php echo $item_data['created']; ?><?php if($item_data['creator']): ?> <span class="username">(<?php echo $item_data['creator']; ?>)</span><?php endif; ?></td>
</tr>
<?php if(isset($item_data['last_edited'])): ?>
<tr>
<td class="key"><?php echo $lang['last_edited_column_label']; ?>:</td>
<td class="value"><?php echo $item_data['last_edited']; ?><?php if($item_data['last_editor']): ?>  <span class="username">(<?php echo $item_data['last_editor']; ?>)</span><?php endif; ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>
<!--</div>-->

<?php if($data_type==1 && empty($_SESSION[$settings['session_prefix'].'usersettings']['disable_map'])): /* spatial data - display map:  */ ?>

<?php
$js[] = 'var map = new OpenLayers.Map("mapcontainer", { projection:projDisplay, controls:[new OpenLayers.Control.Zoom(), new OpenLayers.Control.ScaleLine()] });';
?>

<?php if($max_resolution): ?>
<?php
$js[] = 'map.setOptions({maxResolution:'.$max_resolution.'});';
?>
<?php endif; ?>

<?php if(isset($basemaps[$table_id])): ?>
<?php foreach($basemaps[$table_id] as $basemap): ?>
<?php
$js[] = 'var basemap_'.$table_id.'_'.$basemap['id'].' = new OpenLayers.Layer.'.$basemap['properties'].';
map.addLayer(basemap_'.$table_id.'_'.$basemap['id'].');
if(typeof(basemap_'.$table_id.'_'.$basemap['id'].'.mapObject)!="undefined") basemap_'.$table_id.'_'.$basemap['id'].'.mapObject.setTilt(0);';
?>
<?php endforeach; ?>
<?php endif; ?>

<?php if($auxiliary_layer_1): ?>
<?php
$auxiliary_layer_1_res_factor = isset($auxiliary_layer_1_redraw) ? '{resFactor:1}' : '';
$js[] = 'auxiliary_layer_1 = new OpenLayers.Layer.Vector("'.ol_encode_label($auxiliary_layer_1_title).'", {
projection: projData,        
strategies: [new OpenLayers.Strategy.BBOX('.$auxiliary_layer_1_res_factor.')],
protocol: new OpenLayers.Protocol.HTTP({ url: "'.BASE_URL.'",
                                         params: { r:"json_data", table:"'.$auxiliary_layer_1.'" },
                                         format: new OpenLayers.Format.GeoJSON() }),
    styleMap:auxiliaryLayerStyle
});
map.addLayer(auxiliary_layer_1);';
?>
<?php endif; ?>

<?php
if(isset($item_title)) $fl = ol_encode_label($item_title);
else $fl = $item_data['id'];

if(isset($redraw)) $res_factor = '{resFactor:1}';
else $res_factor = '';

if($min_scale) $minscale_string = 'minScale:'.$min_scale.',';
else $minscale_string = '';
if($max_scale) $maxscale_string = 'manScale:'.$max_scale.',';
else $maxscale_string = '';

$js[] = 'featureLayer = new OpenLayers.Layer.Vector("'.$fl.'", { styleMap: featureLayerStyle });
map.addLayer(featureLayer);

vectorLayer = new OpenLayers.Layer.Vector("'.ol_encode_label($table_title).'", {
    projection: projData,        
    strategies: [new OpenLayers.Strategy.BBOX('.$res_factor.')],
    protocol: new OpenLayers.Protocol.HTTP({ url: "'.BASE_URL.'",
                                             params: { r:"json_data", table:"'.$table_id.'", attributes:true },
                                             format: new OpenLayers.Format.GeoJSON() }),
    '.$minscale_string.'
    '.$maxscale_string.'
    units: "m",
    styleMap:vectorLayerStyle
});
map.addLayer(vectorLayer);'; ?>

<?php
$js[] = 'navigationControl = new OpenLayers.Control.Navigation({"zoomWheelEnabled":false});
map.addControl(navigationControl);
if(document.getElementById("zoomwheel") && document.getElementById("zoomwheel").className == "active") navigationControl.enableZoomWheel();
map.addControl(new OpenLayers.Control.LayerSwitcher());
var polygonFeature = new OpenLayers.Format.WKT({"internalProjection":projDisplay,"externalProjection":projData}).read("'.$spatial_item_data['wkt'].'");
polygonFeature.attributes["id"] = '.$item_data['id'].';
polygonFeature.attributes["featurelabel"] = "'.$fl.'";
featureLayer.addFeatures([polygonFeature]);';
?>
<?php /*if($spatial_item_data['geometry_type']=='POINT'): ?>
<?php
$js[] = 'map.setCenter(featureLayer.getDataExtent().getCenterLonLat(),17);';
?>
<?php else: ?>
<?php
$js[] = 'map.zoomToExtent(featureLayer.getDataExtent());';
?>
<?php endif; */ ?>
<?php
$js[] = 'map.zoomToExtent(featureLayer.getDataExtent());';
?>
<?php endif; ?>

<?php if(isset($attached_data) || isset($related_data) || isset($item_images)): ?>
</div><?php /* id="item" */ ?>
<?php endif; ?>

<?php /* ############################## BEGIN ATTACHED DATA ############################## */ ?>
<?php if(isset($attached_data)): ?>

<div id="attached-data" class="mytab-pane">

<?php foreach($attached_data as $attached_data_item): ?>

<div class="section">

<div class="row">
<div class="col-sm-6">
<h2><?php echo $attached_data_item['title']; ?></h2>
</div>
<div class="col-sm-6">
<?php if($attached_data_item['writable']): ?><a class="btn btn-success btn-top-right" href="<?php echo BASE_URL; ?>?r=edit_data_item.add&amp;data_id=<?php echo $attached_data_item['table_id']; ?>&amp;fk=<?php echo $item_data['id']; ?>"<?php if($attached_data_item['type']==1): ?> onclick="this.href+='&current_position='+map<?php echo $attached_data_item['table_id']; ?>.center.lon+','+map<?php echo $attached_data_item['table_id']; ?>.center.lat+','+map<?php echo $attached_data_item['table_id']; ?>.zoom"<?php endif; ?>><span class="glyphicon glyphicon-plus"></span> <?php echo str_replace('[table]', $attached_data_item['title'], $lang['add_attached_item_link']); ?></a><?php endif; ?>
</div>
</div>

<?php if($attached_data_item['items']): ?>

<?php if($attached_data_item['type']==1): $displayed_maps[]=$attached_data_item['table_id']; /* spatial data - show map box  */ ?>
<div id="mapcontainer<?php echo $attached_data_item['table_id']; ?>" class="defaultmap"></div>
<?php endif; ?>

<div class="table-data">
<table id="table-<?php echo $attached_data_item['table_id']; ?>" class="table table-striped table-hover">
<thead>
<tr>

<th class="options-l">&nbsp;</th>

<?php if(isset($attached_data_item['columns'])): ?>
<?php foreach($attached_data_item['columns'] as $column): ?>
<th><?php echo truncate($column['label'], 15, true); ?></th>
<?php endforeach; ?>
<?php else: ?>
<!--<th>ID</th>-->
<?php endif; ?>

<?php /*
<th><?php echo $lang['created_column_label']; ?></th>
<th><?php echo $lang['last_edited_column_label']; ?></th>
*/ ?>

<?php if($attached_data_item['type']==1): ?>
<th><?php echo $lang['geometry_column_label']; ?></th>
<?php endif; ?>

</tr>
</thead>

<tbody>
<?php foreach($attached_data_item['items'] as $data_item): ?>
<tr id="row-<?php echo $attached_data_item['table_id']; ?>-<?php echo $data_item['id']; ?>">

<td class="options-l">
<a class="btn btn-success btn-xs" href="<?php echo BASE_URL; ?>?r=data_item&amp;data_id=<?php echo $attached_data_item['table_id']; ?>&amp;id=<?php echo $data_item['id']; ?>" title="<?php echo $lang['show_data_item_details']; ?>"><span class="glyphicon glyphicon-eye-open"></span></a>
<?php if($attached_data_item['writable']): ?> 
<a class="btn btn-primary btn-xs" href="?r=edit_data_item.edit&amp;data_id=<?php echo $attached_data_item['table_id']; ?>&amp;id=<?php echo $data_item['id']; ?>" title="<?php echo $lang['edit']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
<a class="btn btn-danger btn-xs" href="?r=data.delete&amp;data_id=<?php echo $attached_data_item['table_id']; ?>&amp;id=<?php echo $data_item['id']; ?>" title="<?php echo $lang['delete']; ?>" data-delete-confirm="<?php echo rawurlencode($lang['delete_data_item_message']); ?>"><span class="glyphicon glyphicon-remove"></span></a><?php endif; ?>
</td>

<?php if(isset($attached_data_item['columns'])): ?>

<?php foreach($attached_data_item['columns'] as $column): ?>
<td>
<?php if($column['type']==6): ?>
<?php if($data_item[$column['name']]): ?><span class="glyphicon glyphicon-ok text-success" title="<?php echo $lang['yes']; ?>"></span><?php endif; ?>
<?php elseif(isset($column['choice_labels']) && isset($column['choice_labels'][$data_item[$column['name']]]) && !empty($column['choice_labels'][$data_item[$column['name']]])): ?>
<?php echo $column['choice_labels'][$data_item[$column['name']]]; ?>
<?php elseif($column['type']==1||$column['relation_table']): ?><?php echo truncate($data_item[$column['name']], 25, true); ?>
<?php else: ?>
<?php echo truncate($data_item[$column['name']], 25); ?>
<?php endif; ?>

<?php if(empty($data_item[$column['name']])): ?>&nbsp;<?php endif; ?>
</td>
<?php endforeach; ?>
<?php else: ?>
<!--<td><a href="<?php echo BASE_URL; ?>?r=show_data_item&amp;id=<?php echo $data_item['id']; ?>"><?php echo $data_item['id']; ?></a></td>-->
<?php endif; ?>

<?php /*
<td><span class="date"><?php echo $data_item['created']; ?></span><?php if($data_item['creator']): ?> <span class="username">(<?php echo $data_item['creator']; ?>)</span><?php endif; ?></td>
<td><?php if(isset($data_item['last_edited'])): ?><span class="date"><?php echo $data_item['last_edited']; ?></span><?php if($data_item['last_editor']): ?> <span class="username">(<?php echo $data_item['last_editor']; ?>)</span><?php endif; ?><?php endif; ?></td>
*/ ?>

<?php if($attached_data_item['type']==1): ?>
<td><?php if(!empty($data_item['wkt'])): ?><span class="glyphicon glyphicon-ok text-success" title="<?php echo $lang['yes']; ?>"></span><?php endif; ?></td>
<?php endif; ?>

</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<table class="data">
<tr>
<td class="nowrap"><?php echo $lang['nr_of_attached_items_label']; ?>&nbsp;</td>
<td><?php echo $attached_data_item['items_info']['nr_of_items']; ?></td>
</tr>
<?php if($attached_data_item['type']==1): ?>
<?php if($attached_data_item['items_info']['total_area']>0): ?>
<tr>
<td class="nowrap"><strong><?php echo $lang['total_area_attached_items_label']; ?></strong></td>
<td><?php echo $attached_data_item['items_info']['total_area_ha']; ?> ha / <?php echo $attached_data_item['items_info']['total_area_sqm']; ?> m²</td>
</tr>
<?php endif; ?>
<?php endif; ?>
</table>

<?php if($attached_data_item['type']==1 && empty($_SESSION[$settings['session_prefix'].'usersettings']['disable_map'])): ?>
<?php $js[] = 'var map'.$attached_data_item['table_id'].' = new OpenLayers.Map("mapcontainer'.$attached_data_item['table_id'].'", { projection:projDisplay, controls:[new OpenLayers.Control.Zoom(), new OpenLayers.Control.Navigation({"zoomWheelEnabled":false}), new OpenLayers.Control.ScaleLine()] });'; ?>

<?php if(isset($basemaps[$attached_data_item['table_id']])): ?>
<?php foreach($basemaps[$attached_data_item['table_id']] as $basemap): ?>
<?php $js[] = 'var basemap_'.$attached_data_item['table_id'].'_'.$basemap['id'].' = new OpenLayers.Layer.'.$basemap['properties'].';
map'.$attached_data_item['table_id'].'.addLayer(basemap_'.$attached_data_item['table_id'].'_'.$basemap['id'].');'; ?>
<?php endforeach; ?>
<?php endif; ?>

<?php $js[] = 'vectorLayer'.$attached_data_item['table_id'].' = new OpenLayers.Layer.Vector("'.$attached_data_item['title'].'", { styleMap: featuresStyle });
map'.$attached_data_item['table_id'].'.addLayer(vectorLayer'.$attached_data_item['table_id'].');'; ?>

<?php $js[] = 'map'.$attached_data_item['table_id'].'.addControl(new OpenLayers.Control.LayerSwitcher());'; ?>
 
<?php $i=0; $geometries=false; foreach($attached_data_item['items'] as $data_item): ?>
<?php if(!empty($data_item['wkt'])): ?>
<?php

if(!empty($data_item['_featurelabel_'])) $feature_label = ol_encode_label($data_item['_featurelabel_']); 
else $feature_label = $data_item['id']; ?>

<?php $js[] = 'var polygonFeature'.$i.' = new OpenLayers.Format.WKT({"internalProjection":projDisplay,"externalProjection":projData}).read("'.$data_item['wkt'].'");
polygonFeature'.$i.'.attributes["id"] = "'.$data_item['id'].'";
polygonFeature'.$i.'.attributes["featurelabel"] = "'.$feature_label.'";
polygonFeature'.$i.'.attributes["area"] = '.$data_item['area'].';
polygonFeature'.$i.'.attributes["area_ha"] = "'.$data_item['area_ha'].'";
polygonFeature'.$i.'.attributes["area_sqm"] = "'.$data_item['area_sqm'].'";
vectorLayer'.$attached_data_item['table_id'].'.addFeatures([polygonFeature'.$i.']);'; ?>

<?php $geometries=true; endif; ?>
<?php ++$i; endforeach; ?>

<?php if($geometries): ?>
<?php $js[] = 'map'.$attached_data_item['table_id'].'.zoomToExtent(vectorLayer'.$attached_data_item['table_id'].'.getDataExtent());'; ?>
<?php else: ?>
<?php $js[] = 'map'.$attached_data_item['table_id'].'.setCenter(new OpenLayers.LonLat('.$settings['default_longitude'].','.$settings['default_latitude'].').transform(projData, projDisplay), '.$settings['default_zoomlevel'].');'; ?>
<?php endif; ?>
<?php $js[] = 'if(map'.$attached_data_item['table_id'].'.zoom > 15) map'.$attached_data_item['table_id'].'.zoomTo(15);
var selectControl'.$attached_data_item['table_id'].' = new OpenLayers.Control.SelectFeature([ vectorLayer'.$attached_data_item['table_id'].' ], { clickout:true, toggle:true, multiple:false, hover:false } );
map'.$attached_data_item['table_id'].'.addControl(selectControl'.$attached_data_item['table_id'].');
selectControl'.$attached_data_item['table_id'].'.activate();
if(typeof(selectControl'.$attached_data_item['table_id'].'.handlers) != "undefined") selectControl'.$attached_data_item['table_id'].'.handlers.feature.stopDown = false;
vectorLayer'.$attached_data_item['table_id'].'.events.on({
featureselected: function(event) {
var feature'.$attached_data_item['table_id'].' = event.feature;
//selectRow("row-'.$attached_data_item['table_id'].'-"+feature'.$attached_data_item['table_id'].'.attributes.id);'; ?>
<?php

if($attached_data_item['writable']) $feature_options = '<p class=\"fib-options\"><a class=\"btn btn-primary btn-xs\" href=\"'.BASE_URL.'?r=edit_data_item.edit&amp;data_id='.$attached_data_item['table_id'].'&amp;id="+feature'.$attached_data_item['table_id'].'.attributes.id+"\" title=\"'.$lang['edit'].'\"><span class=\"glyphicon glyphicon-pencil\"></span></a>&nbsp;<a class=\"btn btn-danger btn-xs\" href=\"'.BASE_URL.'?r=data.delete&amp;data_id='.$attached_data_item['table_id'].'&amp;id="+feature'.$attached_data_item['table_id'].'.attributes.id+"\" onclick=\"return delete_confirm(this, \''.rawurlencode($lang['delete_data_item_message']).'\')\" title=\"'.$lang['delete'].'\"><span class=\"glyphicon glyphicon-remove\"></span></a></p>';
else $feature_options = '';

$js[]='if(feature'.$attached_data_item['table_id'].'.attributes.area>0) var feature_info_string'.$attached_data_item['table_id'].' = "<br />Area: "+feature'.$attached_data_item['table_id'].'.attributes.area_ha+" ha / "+feature'.$attached_data_item['table_id'].'.attributes.area_sqm+" m<small><sup>2</sup></small>";
else feature_info_string'.$attached_data_item['table_id'].' = "";
feature'.$attached_data_item['table_id'].'.popup = new OpenLayers.Popup.FramedCloud("pop'.$attached_data_item['table_id'].'",feature'.$attached_data_item['table_id'].'.geometry.getBounds().getCenterLonLat(),null,"<div class=\"featureinfobubble\"><p><a href=\"'.BASE_URL.'?r=data_item&data_id='.$attached_data_item['table_id'].'&id="+feature'.$attached_data_item['table_id'].'.attributes.id+"\"><strong>"+decodeURIComponent(feature'.$attached_data_item['table_id'].'.attributes.featurelabel)+"</strong></a>"+feature_info_string'.$attached_data_item['table_id'].'+"</p>'.$feature_options.'</div>",null,true);
map'.$attached_data_item['table_id'].'.addPopup(feature'.$attached_data_item['table_id'].'.popup);},
featureunselected: function(event) {
var feature'.$attached_data_item['table_id'].' = event.feature;
map'.$attached_data_item['table_id'].'.removePopup(feature'.$attached_data_item['table_id'].'.popup);
feature'.$attached_data_item['table_id'].'.popup.destroy();
feature'.$attached_data_item['table_id'].'.popup = null;
//unselectRow("row'.$attached_data_item['table_id'].'-"+feature'.$attached_data_item['table_id'].'.attributes.id);
}});'; ?>

<?php endif; ?>

<?php else: ?>

<div class="alert alert-warning"><?php echo $lang['no_attached_items_available']; ?></div>

<?php endif; ?>

</div>
  
<?php endforeach; ?>

</div>

<?php endif; ?>
<?php /* ############################## END ATTACHED DATA ############################## */ ?>

<?php /* ############################## BEGIN RELATED DATA ############################## */ ?>
<?php if(isset($related_data)): ?>

<div id="related-data"  class="mytab-pane">

<?php foreach($related_data as $related_data_item): ?>

<div class="section">

<div class="row">
<div class="col-sm-6">
<h2><?php echo $related_data_item['title']; ?></h2>
</div>
<div class="col-sm-6">
<?php if(!$readonly && $permission['write']): ?><a class="btn btn-success btn-top-right" href="<?php echo BASE_URL; ?>?r=relation&amp;initial_table=<?php echo $table_id; ?>&amp;initial_item=<?php echo $item_data['id']; ?>&amp;selected_table=<?php echo $related_data_item['table_id']; ?>"><span class="glyphicon glyphicon-plus"></span> <?php echo str_replace('[table]', $related_data_item['title'], $lang['add_relation_link']); ?></a><?php endif; ?>
</div>
</div>

<?php if($related_data_item['items']): ?>

<?php if($related_data_item['type']==1): $displayed_maps[]=$related_data_item['table_id']; /* spatial data - show map box  */ ?>
<div id="mapcontainer<?php echo $related_data_item['table_id']; ?>" class="defaultmap" style="margin-bottom:20px !important;"></div>
<?php endif; ?>

<!--<div class="table-responsive">-->
<table class="table table-striped table-hover">
<thead>
<tr>
<?php if(isset($related_data_item['columns'])): ?>
<?php foreach($related_data_item['columns'] as $column): ?>
<th><?php echo truncate($column['label'], 20, true); ?></th>
<?php endforeach; ?>
<?php else: ?>
<?php endif; ?>

<?php if($related_data_item['type']==1): ?>
<th><?php echo $lang['geometry_column_label']; ?></th>
<?php endif; ?>

<th class="options">&nbsp;</th>
</tr>
</thead>
<tbody>  
<?php foreach($related_data_item['items'] as $data_item): ?>
<tr id="row-<?php echo $related_data_item['table_id']; ?>-<?php echo $data_item['id']; ?>"<?php if($related_data_item['type']==1 && empty($data_item['wkt'])): ?> class="no-geometry"<?php endif; ?>>
<?php if(isset($related_data_item['columns'])): ?>
<?php foreach($related_data_item['columns'] as $column): ?>
<td><?php if($column['type']==1): ?><?php echo truncate($data_item[$column['name']], 25, true); ?><?php else: ?><?php echo truncate($data_item[$column['name']], 25); ?><?php endif; ?></td>
<?php endforeach; ?>
<?php else: ?>
<!--<td><a href="<?php echo BASE_URL; ?>?r=show_data_item&amp;id=<?php echo $data_item['id']; ?>"><?php echo $data_item['id']; ?></a></td>-->
<?php endif; ?>

<?php /*
<td><span class="date"><?php echo $data_item['created']; ?></span><?php if($data_item['creator']): ?> <span class="username">(<?php echo $data_item['creator']; ?>)</span><?php endif; ?></td>
<td><?php if(isset($data_item['last_edited'])): ?><span class="date"><?php echo $data_item['last_edited']; ?></span><?php if($data_item['last_editor']): ?> <span class="username">(<?php echo $data_item['last_editor']; ?>)</span><?php endif; ?><?php endif; ?></td>
*/ ?>

<?php if($related_data_item['type']==1): ?>
<td><?php if(!empty($data_item['wkt'])): ?><span class="glyphicon glyphicon-ok text-success" title="<?php echo $lang['yes']; ?>"></span><?php endif; ?></td>
<?php endif; ?>

<td class="options">
<a class="btn btn-primary btn-xs" href="<?php echo BASE_URL; ?>?r=data_item&amp;data_id=<?php echo $related_data_item['table_id']; ?>&amp;id=<?php echo $data_item['id']; ?>" title="<?php echo $lang['show_data_item_details']; ?>"><span class="glyphicon glyphicon-search"></span></a>
<?php if(!$readonly && $permission['write']): ?>
<a class="btn btn-danger btn-xs" href="<?php echo BASE_URL; ?>?r=data_item&amp;data_id=<?php echo $table_id; ?>&amp;id=<?php echo $item_data['id']; ?>&amp;delete_relation=<?php echo $relations[$related_data_item['table_id']][$data_item['id']]; ?>" title="<?php echo $lang['delete_relation_title']; ?>" data-delete-confirm="<?php echo rawurlencode($lang['delete_relation_message']); ?>"><span class="glyphicon glyphicon-remove"></span></a>
<?php endif; ?>
</td>

</tr>
<?php endforeach; ?>
</tbody>
</table>
<!--</div>-->

<p>
<strong><?php echo $lang['nr_of_related_items_label']; ?></strong> <?php echo $related_data_item['items_info']['nr_of_items']; ?>
<?php if($related_data_item['type']==1): ?>
<br /><strong><?php echo $lang['total_area_attached_items_label']; ?></strong> <?php echo $related_data_item['items_info']['total_area_ha']; ?> ha / <?php echo $related_data_item['items_info']['total_area_sqm']; ?> m²
<?php endif; ?>
</p>

<?php if($related_data_item['type']==1 && empty($_SESSION[$settings['session_prefix'].'usersettings']['disable_map'])): ?>

<?php
$js[] = 'var map'.$related_data_item['table_id'].' = new OpenLayers.Map("mapcontainer'.$related_data_item['table_id'].'", { projection:projDisplay, controls:[new OpenLayers.Control.Zoom(), new OpenLayers.Control.Navigation({"zoomWheelEnabled":false}), new OpenLayers.Control.ScaleLine()] });';
?>

<?php if(isset($basemaps[$related_data_item['table_id']])): ?>
<?php foreach($basemaps[$related_data_item['table_id']] as $basemap): ?>
<?php
$js[] = 'var basemap_'.$basemap['id'].' = new OpenLayers.Layer.'.$basemap['properties'].';
map'.$related_data_item['table_id'].'.addLayer(basemap_'.$basemap['id'].');';
?>
<?php endforeach; ?>
<?php endif; ?>

<?php
$js[] = 'var vectorLayer'.$related_data_item['table_id'].' = new OpenLayers.Layer.Vector("'.$related_data_item['title'].'", { styleMap: featuresStyle });
map'.$related_data_item['table_id'].'.addLayer(vectorLayer'.$related_data_item['table_id'].');
map'.$related_data_item['table_id'].'.addControl(new OpenLayers.Control.LayerSwitcher());';
?>

<?php $i=0; $geometries=false; foreach($related_data_item['items'] as $data_item): ?>
<?php if(!empty($data_item['wkt'])): ?>
<?php
if(!empty($data_item['_featurelabel_'])) $featurelabel = ol_encode_label($data_item['_featurelabel_']);
else $featurelabel = $data_item['id'];
$js[] = 'var polygonFeature'.$i.' = new OpenLayers.Format.WKT({"internalProjection":projDisplay,"externalProjection":projData}).read("'.$data_item['wkt'].'");
polygonFeature'.$i.'.attributes["id"] = "'.$data_item['id'].'";
polygonFeature'.$i.'.attributes["relation"] = "'.$relations[$related_data_item['table_id']][$data_item['id']].'";
polygonFeature'.$i.'.attributes["featurelabel"] = "'.$featurelabel.'";
polygonFeature'.$i.'.attributes["area_ha"] = "'.$data_item['area_ha'].'";
polygonFeature'.$i.'.attributes["area_sqm"] = "'.$data_item['area_sqm'].'";
vectorLayer'.$related_data_item['table_id'].'.addFeatures([polygonFeature'.$i.']);';
?>
<?php $geometries=true; endif; ?>
<?php ++$i; endforeach; ?>

<?php if($geometries): ?>
<?php
$js[] = 'map'.$related_data_item['table_id'].'.zoomToExtent(vectorLayer'.$related_data_item['table_id'].'.getDataExtent());';
?>
<?php else: ?>
<?php
$js[] = 'map'.$related_data_item['table_id'].'.setCenter(new OpenLayers.LonLat('.$settings['default_longitude'].','.$settings['default_latitude'].').transform(projData, projDisplay), '.$settings['default_zoomlevel'].');';
?>
<?php endif; ?>

<?php
$js[] = 'if(map'.$related_data_item['table_id'].'.zoom > 15) map'.$related_data_item['table_id'].'.zoomTo(15);
var selectControl'.$related_data_item['table_id'].' = new OpenLayers.Control.SelectFeature(
                [ vectorLayer'.$related_data_item['table_id'].' ],
                { clickout:true, toggle:true, multiple:false, hover:false } );
map'.$related_data_item['table_id'].'.addControl(selectControl'.$related_data_item['table_id'].');
selectControl'.$related_data_item['table_id'].'.activate();
if(typeof(selectControl'.$related_data_item['table_id'].'.handlers) != "undefined") selectControl'.$related_data_item['table_id'].'.handlers.feature.stopDown = false;
vectorLayer'.$related_data_item['table_id'].'.events.on({
featureselected: function(event) {
var feature'.$related_data_item['table_id'].' = event.feature;
if(feature'.$related_data_item['table_id'].'.attributes.area>0) var feature_info_string'.$related_data_item['table_id'].' = "<br />Area: "+feature'.$related_data_item['table_id'].'.attributes.area_ha+" ha / "+feature'.$related_data_item['table_id'].'.attributes.area_sqm+" m<small><sup>2</sup></small>";
else feature_info_string'.$related_data_item['table_id'].' = "";
feature'.$related_data_item['table_id'].'.popup = new OpenLayers.Popup.FramedCloud("pop'.$related_data_item['table_id'].'",feature'.$related_data_item['table_id'].'.geometry.getBounds().getCenterLonLat(),null,"<div class=\"featureinfobubble\"><p><a href=\"'.BASE_URL.'?r=data_item&data_id='.$related_data_item['table_id'].'&id="+feature'.$related_data_item['table_id'].'.attributes.id+"\"><strong>"+decodeURIComponent(feature'.$related_data_item['table_id'].'.attributes.featurelabel)+"</strong></a>"+feature_info_string'.$related_data_item['table_id'].'+"</p><p class=\"fib-options\"><a class=\"btn btn-xs btn-danger\" href=\"'.BASE_URL.'?r=xdata_item&amp;data_id='.$table_id.'&amp;id='.$item_data['id'].'&amp;delete_relation="+feature'.$related_data_item['table_id'].'.attributes.relation+"\" onclick=\"return delete_confirm(this, \''.rawurlencode($lang['delete_relation_message']).'\')\"><span class=\"glyphicon glyphicon-remove\"></span></a></p></div>",null,true);
map'.$related_data_item['table_id'].'.addPopup(feature'.$related_data_item['table_id'].'.popup); },
featureunselected: function(event) {
var feature'.$related_data_item['table_id'].' = event.feature;
map'.$related_data_item['table_id'].'.removePopup(feature'.$related_data_item['table_id'].'.popup);
feature'.$related_data_item['table_id'].'.popup.destroy();
feature'.$related_data_item['table_id'].'.popup = null;
}});';
?>

<?php endif; ?>

<?php else: ?>

<div class="alert alert-warning"><?php echo $lang['no_related_items_available']; ?></div>

<?php endif; ?>

</div>
  
<?php endforeach; ?>

</div>

<?php endif; ?>
<?php /* ############################## END RELATED DATA ############################## */ ?>

<?php /* ############################## BEGIN IMAGES ############################## */ ?>
<?php if(isset($item_images)): ?>
<div id="images" class="mytab-pane">

<?php if(isset($images)): ?>
<div class="gallery-wrapper">
<div class="gallery"<?php if($permission['write']): ?> data-gallery-sortable="<?php echo BASE_URL; ?>?r=data_image.reorder"<?php endif; ?>>
<?php foreach($images as $image): ?>
<span id="item_<?php echo $image['id']; ?>" class="photooptions">
<a class="thumbnail" href="<?php echo $image['image_url']; ?>" title="<?php echo $image['title']; ?>" data-lightbox>
<img src="<?php echo $image['thumbnail_url']; ?>" alt="<?php echo $image['title']; ?>" title="<?php echo $image['title']; ?>" data-description="<?php echo $image['description']; ?>" data-author="<?php echo $image['author']; ?>" width="<?php echo $image['thumbnail_width']; ?>" height="<?php echo $image['thumbnail_height']; ?>">
<span><?php echo truncate($image['title'],20,true); ?></span></a>
<?php if($settings['data_images_download_original']): ?><a class="download_button<?php if($permission['write']): ?>_write<?php endif; ?> text-muted" href="<?php echo BASE_URL; ?>?r=data_image.download&amp;id=<?php echo $image['id']; ?>" title="<?php echo $lang['download_image_link']; ?>"><span class="glyphicon glyphicon-download"></span></a><?php endif; ?><?php if($permission['write']): ?><a class="edit_button" href="<?php echo BASE_URL; ?>?r=data_image.edit&amp;id=<?php echo $image['id']; ?>" title="<?php echo $lang['edit']; ?>"><span class="glyphicon glyphicon-pencil"></span></a><a class="delete_button text-danger" href="<?php echo BASE_URL; ?>?r=data_image.delete&amp;id=<?php echo $image['id']; ?>" title="<?php echo $lang['delete']; ?>" data-delete-confirm="<?php echo rawurlencode($lang['delete_data_image_message']); ?>"><span class="glyphicon glyphicon-remove"></span></a><span class="drag_button text-success" title="<?php echo $lang['drag_and_drop']; ?>"><span class="glyphicon glyphicon-move"></span></span><?php endif; ?>
</span>
<?php endforeach; ?>
</div>
</div>
<?php else: ?>
<div class="alert alert-warning"><?php echo $lang['no_data_images_available']; ?></div>
<?php endif; ?>

<?php if(!$readonly && $permission['write']): ?>
<a class="btn btn-success" href="<?php echo BASE_URL; ?>?r=data_image.add&amp;data_id=<?php echo $table_id; ?>&amp;item_id=<?php echo $item_data['id']; ?>"><span class="glyphicon glyphicon-plus-sign"></span> <?php echo $lang['add_data_image_link']; ?></a>
<?php endif; ?>

</div>
<?php endif; ?>
<?php /* ############################## END IMAGES ############################## */ ?>

<?php if(isset($attached_data) || isset($related_data) || isset($item_images)): ?>
</div><?php /* id="myTabContent" */ ?>
<?php endif; ?>

<?php endif; /* if(isset($item_data)) */ ?>

<?php else: ?>

<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=dashboard#data"><?php echo $lang['dashboard_link']; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=data&amp;data_id=<?php echo $table_id; ?>"><span class="nowrap"><?php echo $table_title; ?></span></a></li>
<li class="active"><?php echo $lang['data_item_details_title']; ?></li>
</ul>
<div class="alert alert-danger"><strong><?php echo $lang['data_item_doesnt_exist']; ?></strong></div>
<?php endif; ?>
