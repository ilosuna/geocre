<?php $autofocus=true; /* disabled */ ?>
<?php if(isset($data_item['id']) && $item_exists || empty($data_item['id'])): ?>

<?php if($fk && $table_data['parent_table']): /* attached data */ ?>
<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=dashboard#data" title="<?php echo $lang['dashboard_title']; ?>"><?php echo $lang['dashboard_link']; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=data&amp;data_id=<?php echo $table_data['parent_table']; ?>"><?php echo $table_data['parent_title']; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=data_item&amp;data_id=<?php echo $table_data['parent_table']; ?>&amp;id=<?php echo $fk; ?>#attached-data"><?php echo $lang['data_item_details_title']; ?></a></li>
<li class="active">
<?php if(isset($data_item['id'])): ?>
<?php echo $lang['edit_data_item_subtitle']; ?>
<?php else: ?>
<?php echo $lang['add_data_item_subtitle']; ?>
<?php endif; ?>
</li>
</ul>

<h1>
<?php if(isset($data_item['id'])): ?>
<?php echo str_replace('[table]', $table_data['title'], $lang['edit_attached_data_item_subtitle']); ?>
<?php else: ?>
<?php echo str_replace('[table]', $table_data['title'], $lang['add_attached_data_item_subtitle']); ?>
<?php endif; ?>
</h1>

<?php else: /* regular not attached data: */ ?>
<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=dashboard#data" title="<?php echo $lang['dashboard_title']; ?>"><?php echo $lang['dashboard_link']; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=data&amp;data_id=<?php echo $table_data['id']; ?>"><?php echo $table_data['title']; ?></a></li>
<li class="active">
<?php if(isset($data_item['id'])): ?>
<?php echo $lang['edit_data_item_subtitle']; ?>
<?php else: ?>
<?php echo $lang['add_data_item_subtitle']; ?>
<?php endif; ?>
</li>
</ul>

<div class="row">
<div class="col-sm-6">
<h1>
<?php if(isset($data_item['id'])): ?>
<?php echo $lang['edit_data_item_subtitle']; ?>
<?php else: ?>
<?php echo $lang['add_data_item_subtitle']; ?>
<?php endif; ?>
</h1>
</div>
<div class="col-sm-6">
<?php if(isset($help)): ?>
<a class="btn btn-default btn-top-right" href="index.php?r=help.<?php echo $help; ?>" data-toggle="modal" data-target="#modal_help" data-input="content"><span class="glyphicon glyphicon-question-sign"></span> <?php echo $lang['help']; ?></a>
<?php endif; ?>
</div>
</div>

<?php endif; ?>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<?php if(isset($child_data_without_fk)): ?>
<p class="caution"><?php echo $lang['child_data_without_fk']; ?></p>
<p><a href="<?php echo BASE_URL; ?>?r=data&amp;data_id=<?php echo $table_data['parent_table']; ?>"><?php echo $table_data['parent_title']; ?></a></p>
<?php else: ?>

<form action="index.php" method="post" data-validate>
<div>
<?php if(isset($data_item['id'])): ?>
<input type="hidden" name="r" value="edit_data_item.edit_submit" />
<input type="hidden" name="id" value="<?php echo $data_item['id']; ?>" />
<?php else: ?>
<input type="hidden" name="r" value="edit_data_item.add_submit" />
<?php endif; ?>
<input type="hidden" name="data_id" value="<?php echo $table_data['id']; ?>" />
<input type="hidden" name="fk" value="<?php echo $fk; ?>" />

<?php if($data_type==1): /* spatial data - create hidden wkt field and validation code:  */ ?>
<input id="wkt" type="hidden" name="wkt" value="<?php if(isset($data_item['wkt'])): ?><?php echo $data_item['wkt']; ?><?php endif; ?>"<?php if($geometry_required): ?> data-required="wkt" data-message="<?php echo rawurlencode(strip_tags($lang['error_geometry_required'])); ?>"<?php endif; ?>>
<?php endif; ?>

<!--<?php if(isset($sections)): ?>
<ul id="sectionindex">
<?php foreach($sections as $section): ?>
<li><a href="#section-<?php echo $section['id']; ?>"><?php echo $section['label']; ?></a></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>-->

<!--<div class="table-responsive">-->
<table class="table table-striped">
<tr class="success">
<td colspan="2"><button class="btn btn-success btn-lg pull-right" type="submit"><span class="glyphicon glyphicon-save"></span> <?php echo $lang['save_submit']; ?></button></td>
</tr>
<?php if($data_type==1): /* spatial data - create map box: */ ?>
<tr>
<td colspan="2" class="map">
<a class="btn btn-default btn-block visible-xs visible-sm" data-toggle="collapse" href="#mapwrapper"><?php echo $lang['toggle_map_label']; ?></a>
<div id="mapwrapper" class="panel-collapse collapse in">
<div id="mapcontainer" class="defaultmap editmode"<?php if(isset($_SESSION[$settings['session_prefix'].'usersettings']['map_height'])): ?> style="height:<?php echo $_SESSION[$settings['session_prefix'].'usersettings']['map_height']; ?>px"<?php endif; ?>>
<div id="maptoolbar">
<div id="drawcontrols" class="<?php if($geometry_type==0): ?>buttongroup<?php else: ?>buttongroup-inactive<?php endif; ?>">
<?php if($geometry_type==0||$geometry_type==1): ?>
<a id="point" href="#" onclick="setDrawTool('point'); return false"<?php if($geometry_type==0||$geometry_type==1): ?> class="active"<?php endif; ?> title="<?php echo $lang['point_tool_label']; ?>"><?php echo $lang['point_tool_label']; ?></a>
<?php endif; ?>
<?php if($geometry_type==0||$geometry_type==2): ?>
<a id="line" href="#" onclick="setDrawTool('line'); return false"<?php if($geometry_type==2): ?> class="active"<?php endif; ?> title="<?php echo $lang['line_tool_label']; ?>"><?php echo $lang['line_tool_label']; ?></a>
<?php endif; ?>
<?php if($geometry_type==0||$geometry_type==3): ?>
<a id="polygon" href="#" onclick="setDrawTool('polygon'); return false"<?php if($geometry_type==3): ?> class="active"<?php endif; ?> title="<?php echo $lang['polygon_tool_label']; ?>"><?php echo $lang['polygon_tool_label']; ?></a>
<?php endif; ?>
</div>
<div class="buttongroup">
<a id="zoomwheel" href="#" onclick="toggleZoomWheel(); return false" class="<?php if(isset($_SESSION[$settings['session_prefix'].'usersettings']['map_zoomwheel'])&&$_SESSION[$settings['session_prefix'].'usersettings']['map_zoomwheel']==1): ?>active<?php else: ?>inactive<?php endif; ?>" title="<?php echo $lang['zoomwheel_label']; ?>"><?php echo $lang['zoomwheel_label']; ?></a>
<a id="snapping" href="#" onclick="toggleSnapping(); return false" class="<?php if(isset($_SESSION[$settings['session_prefix'].'usersettings']['map_snapping'])&&$_SESSION[$settings['session_prefix'].'usersettings']['map_snapping']==1): ?>active<?php else: ?>inactive<?php endif; ?>" title="<?php echo $lang['snapping_label']; ?>"><?php echo $lang['snapping_label']; ?></a>
</div>
<div class="buttongroup">
<a id="setposition" href="#" title="<?php echo $lang['setposition_label']; ?>" data-set-position="<?php echo rawurlencode($lang['setposition_prompt_label']); ?>" data-set-position-error="<?php echo rawurlencode($lang['setposition_format_error']); ?>"><?php echo $lang['setposition_label']; ?></a>
</div>
<div id="mapsizetools" class="buttongroup">
<a id="fullscreenmap" href="#" title="<?php echo $lang['fullscreen_map_label']; ?>"><?php echo $lang['fullscreen_map_label']; ?></a>
<a id="reducemap" href="#" title="<?php echo $lang['reduce_map_label']; ?>"><?php echo $lang['reduce_map_label']; ?></a>
<a id="enlargemap" href="#" title="<?php echo $lang['enlarge_map_label']; ?>"><?php echo $lang['enlarge_map_label']; ?></a>
</div>
</div>
</div>
</div>

</td>
</tr>
<?php endif; ?>

<?php if(isset($columns)): $i=0; foreach($columns as $column): ?>

<?php if($column['type']==0): ?>
<tr class="<?php if($column['section_type']==1): ?>section<?php else: ?>subsection<?php endif; ?>">
<td colspan="2"<?php if($column['section_type']==1): ?><?php if(isset($sections[$column['id']])): ?> id="section-<?php echo $sections[$column['id']]['id']; ?>"<?php endif; ?> class="mainsection"<?php else: ?> class="subsection"<?php endif; ?>><?php if(empty($column['label'])): ?>&nbsp;<?php else: ?><?php echo $column['label']; ?><?php endif; ?><?php if($column['description']): ?><br /><span class="description"><?php echo $column['description']; ?></span><?php endif; ?></td>
</tr>
<?php else: ?>
<tr<?php if(isset($error_fields) && in_array($column['name'], $error_fields)): ?> class="has-error danger"<?php endif; ?><?php if($column['required']): ?> data-required="<?php echo $column['name']; ?>" data-message="<?php echo rawurlencode(strip_tags(str_replace('[field]', $column['label'] ,$lang['required_field_message']))); ?>"<?php endif; ?>>
<td class="key"><?php if(!$column['choices']): /* radio buttons have their own labels */ ?><label class="control-label" for="<?php echo $column['name']; ?>"><?php echo $column['label']; ?></label><?php else: ?><span class="control-label radio-label"><?php echo $column['label']; ?></span><?php endif; ?><?php if($column['required']): ?><sup><span class="glyphicon glyphicon-asterisk text-danger" title="<?php echo $lang['required_label']; ?>"></span></sup><?php endif; ?><?php if($column['description']): ?><br /><span class="description"><?php echo $column['description']; ?></span><?php endif; ?></td>
<td class="value">
<?php if($column['relation_table']): /* related table */ ?>
<?php if(isset($verification_options[$column['name']])): ?>
<select name="<?php echo $column['name']; ?>" size="<?php echo count($verification_options[$column['name']]); ?>">
<?php foreach($verification_options[$column['name']] as $verification_option): ?>
<option value="<?php echo $verification_option; ?>"><?php echo $verification_option; ?></option> 
<?php endforeach; ?>
</select>
<?php else: ?>
<input class="form-control" id="<?php echo $column['name']; ?>" type="text" name="<?php echo $column['name']; ?>" value="<?php if(isset($data_item[$column['name']])) echo $data_item[$column['name']]; ?>" placeholder="<?php echo str_replace('[table]', $column['relation_table_title'], $lang['related_label']); ?>" data-autocomplete="<?php echo $column['relation']; ?>" data-autocomplete-minlength="<?php echo $settings['autocomplete_min_length']; ?>">
<?php endif; ?>

<?php elseif($column['type']==6): /* boolean */ ?>

<input id="<?php echo $column['name']; ?>" type="checkbox" name="<?php echo $column['name']; ?>" value="1"<?php if(isset($data_item[$column['name']]) && $data_item[$column['name']]==true): ?> checked="checked"<?php endif; ?> />

<?php elseif($column['choices']): /* radio buttons */ ?>

<div class="radio">

<input id="<?php echo $column['name']; ?>-empty" type="radio" name="<?php echo $column['name']; ?>" value=""<?php if(empty($data_item[$column['name']])): ?> checked="checked"<?php endif; ?><?php if(isset($error_fields) && in_array($column['name'], $error_fields)): ?> class="error"<?php endif; ?> />
<label class="unspecified" for="<?php echo $column['name']; ?>-empty"><?php echo $lang['not_specified_label']; ?></label><br />
<?php $cid=0; foreach($column['choices'] as $choice): ?>
<input id="<?php echo $column['name']; ?>-<?php echo $cid; ?>" type="radio" name="<?php echo $column['name']; ?>" value="<?php echo $choice; ?>"<?php if(isset($data_item[$column['name']]) && $data_item[$column['name']]==$choice || $column['choice_labels'][$choice]=='*' && !empty($data_item[$column['name']]) && !in_array($data_item[$column['name']], $column['choices'])): ?> checked="checked"<?php endif; ?><?php if(isset($error_fields) && in_array($column['name'], $error_fields)): ?> class="error"<?php endif; ?> />
<?php if($column['choice_labels'][$choice]=='*'): ?>
<input id="<?php echo $column['name']; ?>-<?php echo $cid; ?>-value" name="_<?php echo $column['name']; ?>_" class="form-control form-control-medium" type="text" size="5" value="<?php if(isset($data_item[$column['name']]) && !in_array($data_item[$column['name']], $column['choices'])): ?><?php echo $data_item[$column['name']]; ?><?php endif; ?>" data-check="<?php echo $column['name']; ?>-<?php echo $cid; ?>">
<?php else: ?>
<label for="<?php echo $column['name']; ?>-<?php echo $cid; ?>"><?php echo $column['choice_labels'][$choice]; ?></label><br />
<?php endif; ?>
<?php ++$cid; endforeach; ?>  
</div>

<?php elseif($column['type']==5): /* text */ ?>
<textarea id="<?php echo $column['name']; ?>" class="form-control" name="<?php echo $column['name']; ?>" rows="10"><?php if(isset($data_item[$column['name']])) echo $data_item[$column['name']]; ?></textarea>

<?php elseif($column['type']==7): /* date */ ?>
<input id="<?php echo $column['name']; ?>" class="form-control" type="text" name="<?php echo $column['name']; ?>" value="<?php if(isset($data_item[$column['name']])) echo $data_item[$column['name']]; ?>" placeholder="<?php echo $lang['date_format_label']; ?>" data-date-picker="<?php echo $column['name']; ?>">

<?php elseif($column['type']==8): /* time */ ?>
<input id="<?php echo $column['name']; ?>" class="form-control" type="text" name="<?php echo $column['name']; ?>" value="<?php if(isset($data_item[$column['name']])) echo $data_item[$column['name']]; ?>" placeholder="<?php echo $lang['time_format_label']; ?>">

<?php else: /* default */ ?>
<input id="<?php echo $column['name']; ?>" class="form-control" type="text" name="<?php echo $column['name']; ?>" value="<?php if(isset($data_item[$column['name']])) echo $data_item[$column['name']]; ?>" placeholder="<?php echo $lang['input_type_label'][$column['type']]; ?>"><?php if(isset($lang['input_type_label'][$column['type']])): ?><?php endif; ?>

<?php endif; ?>

</td>
</tr>
<?php endif; ?>

<?php ++$i; endforeach; endif; ?>
<tr class="success">
<td colspan="2"><button class="btn btn-success btn-lg pull-right" type="submit"><span class="glyphicon glyphicon-save"></span> <?php echo $lang['save_submit']; ?></button></td>
</tr>

</table>
<!--</div>-->

</div>
</form>



<?php if($data_type==1): /* spatial data - display map:  */ ?>
<?php if(isset($basemaps)): ?>
<?php foreach($basemaps as $basemap): ?>
<?php
$js[] = 'var basemap_'.$basemap['id'].' = new OpenLayers.Layer.'.$basemap['properties'].';
map.addLayer(basemap_'.$basemap['id'].');
if(typeof(basemap_'.$basemap['id'].'.mapObject)!="undefined") basemap_'.$basemap['id'].'.mapObject.setTilt(0);';
?>
<?php endforeach; ?>
<?php endif; ?>
<?php
$js[] = 'featuresLayer = new OpenLayers.Layer.Vector("Available Features", {
    projection: projData,        
    strategies: [new OpenLayers.Strategy.BBOX()],
    protocol: new OpenLayers.Protocol.HTTP({ url: "'.BASE_URL.'",
                                             params: { r: "json_data", table: "'.$table_data['id'].'" },
                                             format: new OpenLayers.Format.GeoJSON() }),';
    ?>
    <?php if($min_scale): ?>
    <?php
    $js[] = 'minScale:'.$min_scale.',';
    ?>
    <?php endif; ?>
    <?php if($max_scale): ?>
    <?php
    $js[] = 'maxScale:'.$max_scale.',';
    ?>
    <?php endif; ?>
    <?php
    $js[] = 'units: "m",
    styleMap:featuresLayerStyle
    
});

map.addLayers([featuresLayer]);'; ?>

<?php if($auxiliary_layer_1): ?>
<?php
$auxiliary_layer_1_snap = ', auxiliary_layer_1';
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
<?php else: ?>
<?php $auxiliary_layer_1_snap = ''; ?>
<?php endif; ?>

<?php
$aux_snap = 
$js[] = 'snap = new OpenLayers.Control.Snapping({layer: vectorLayer, targets: [featuresLayer'.$auxiliary_layer_1_snap.'], greedy: false });
if(document.getElementById("snapping") && document.getElementById("snapping").className == "active") snap.activate();

if(wkt!="") // edit
 {
  var wkt = document.getElementById("wkt").value;
  var polygonFeature = new OpenLayers.Format.WKT({"internalProjection":projDisplay,"externalProjection":projData}).read(wkt);
  vectorLayer.addFeatures([polygonFeature]);
  map.zoomToExtent(vectorLayer.getDataExtent());
  modifyControl.selectFeature(vectorLayer.features[0]);
 }
else // add / empty geometry
 {';
  ?> 
  <?php if(isset($current_position)): ?>
  <?php
  $js[] = 'map.setCenter(new OpenLayers.LonLat('.$current_position['longitude'].','.$current_position['latitude'].'), '.$current_position['zoomlevel'].');';
  ?>
  <?php else: ?>
  <?php
  $js[] = 'map.setCenter(new OpenLayers.LonLat('.$settings['default_longitude'].','.$settings['default_latitude'].').transform(projData, projDisplay), '.$settings['default_zoomlevel'].');';
  ?>
  <?php endif; ?>
  <?php
  $js[] = 'drawControl.activate();
 }';
?>
<?php endif; ?>

<?php endif; ?>

<?php else: ?>
<h1><span class="breadcrumbs"><a href="<?php echo BASE_URL; ?>?r=data"><?php echo $lang['data_subtitle']; ?></a> &raquo; 
<a href="<?php echo BASE_URL; ?>?r=show_data&amp;table=<?php echo $table_id; ?>"><?php echo $table_data['title']; ?></a> &raquo;</span>
<?php echo $lang['edit_data_item_subtitle']; ?></h1>
<p class="caution"><?php echo $lang['data_item_doesnt_exist']; ?></p>
<?php endif; ?>
