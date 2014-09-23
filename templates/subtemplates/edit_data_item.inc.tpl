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

<?php if(isset($child_data_without_fk)): ?>

<p class="caution"><?php echo $lang['child_data_without_fk']; ?></p>
<p><a href="<?php echo BASE_URL; ?>?r=data&amp;data_id=<?php echo $table_data['parent_table']; ?>"><?php echo $table_data['parent_title']; ?></a></p>

<?php elseif($data_type==1 && isset($_SESSION[$settings['session_prefix'].'usersettings']['disable_map']) && $_SESSION[$settings['session_prefix'].'usersettings']['disable_map'] && $table_data['geometry_type']!=1): ?>
<div class="alert alert-danger"><?php echo $lang['map_disabled_edit_info']; ?></div>
<div class="no-map">
<p><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $lang['map_disabled']; ?></p>
</div>
<div class="text-center">
<?php if(isset($data_item['id'])): ?>
<a class="btn btn-primary" href="<?php echo BASE_URL; ?>?r=edit_data_item.edit&amp;data_id=<?php echo $table_data['id']; ?>&amp;id=<?php echo $data_item['id']; ?>&amp;disable_map=0" title="<?php echo $lang['disable_map']; ?>"><?php echo $lang['enable_map']; ?></a>
<?php else: ?>
<a class="btn btn-primary" href="<?php echo BASE_URL; ?>?r=edit_data_item.add&amp;data_id=<?php echo $table_data['id']; ?>&amp;disable_map=0" title="<?php echo $lang['disable_map']; ?>"><?php echo $lang['enable_map']; ?></a>
<?php endif; ?>
</div>
<?php else: ?>

<?php if(empty($_POST) && isset($remembered_values) && empty($data_item['id'])): ?>
<div class="alert alert-warning">
<a class="close" data-dismiss="alert" href="#" aria-hidden="true">&times;</a>
<p><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $lang['remembered_values_warning']; ?></p>
</div>
<?php endif; ?>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<form action="index.php" method="post" data-validate data-disable-on-submit>
<div>
<?php if(isset($data_item['id'])): ?>
<input type="hidden" name="r" value="edit_data_item.edit_submit" />
<input type="hidden" name="id" value="<?php echo $data_item['id']; ?>" />
<?php else: ?>
<input type="hidden" name="r" value="edit_data_item.add_submit" />
<input type="hidden" name="formsession" value="<?php echo $formsession; ?>" />
<?php endif; ?>
<input type="hidden" name="data_id" value="<?php echo $table_data['id']; ?>" />
<input type="hidden" name="fk" value="<?php echo $fk; ?>" />

<?php if($data_type==1 && empty($latlong_entry) && empty($_SESSION[$settings['session_prefix'].'usersettings']['disable_map'])): /* spatial data - create hidden wkt field and validation code:  */ ?>
<input id="_wkt" type="hidden" name="data[_wkt]" value="<?php if(isset($data_item['_wkt'])): ?><?php echo $data_item['_wkt']; ?><?php endif; ?>"<?php if($geometry_required): ?> data-required="data[_wkt]" data-message="<?php echo rawurlencode(strip_tags($lang['error_no_geometry'])); ?>"<?php endif; ?>>
<?php endif; ?>

<?php /*if(isset($sections)): ?>
<ul id="sectionindex">
<?php foreach($sections as $section): ?>
<li><a href="#section-<?php echo $section['id']; ?>"><?php echo $section['label']; ?></a></li>
<?php endforeach; ?>
</ul>
<?php endif; */ ?>

<!--<div class="table-responsive">-->
<table class="table">
<tr class="success">
<td colspan="3"><button class="btn btn-success btn-lg pull-right" type="submit"><span class="glyphicon glyphicon-save"></span> <?php echo $lang['save_submit']; ?></button></td>
</tr>
<?php if($data_type==1): /* spatial data - create map box: */ ?>

<tr>
<td colspan="3" class="map">

<?php if(empty($latlong_entry) && isset($_SESSION[$settings['session_prefix'].'usersettings']['disable_map']) && $_SESSION[$settings['session_prefix'].'usersettings']['disable_map']): ?>

<div class="no-map">
<p><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $lang['map_disabled']; ?></p>
</div>

<div class="text-center bottom-space-small">
<?php if(isset($data_item['id'])): ?>
<a class="btn btn-primary" href="<?php echo BASE_URL; ?>?r=edit_data_item.edit&amp;data_id=<?php echo $table_data['id']; ?>&amp;id=<?php echo $data_item['id']; ?>&amp;disable_map=0" title="<?php echo $lang['disable_map']; ?>" data-confirm="<?php echo rawurlencode($lang['confirm_form_reload']); ?>"><?php echo $lang['enable_map']; ?></a>
<?php else: ?>
<a class="btn btn-primary" href="<?php echo BASE_URL; ?>?r=edit_data_item.add&amp;data_id=<?php echo $table_data['id']; ?>&amp;disable_map=0" title="<?php echo $lang['disable_map']; ?>" data-confirm="<?php echo rawurlencode($lang['confirm_form_reload']); ?>"><?php echo $lang['enable_map']; ?></a>
<?php endif; ?>
</div>

<?php endif; ?>

<?php if($latlong_entry || (isset($_SESSION[$settings['session_prefix'].'usersettings']['disable_map']) && $_SESSION[$settings['session_prefix'].'usersettings']['disable_map'])): ?>

<tr class="latlong info<?php if(isset($error_fields) && in_array('_latlong', $error_fields)): ?> has-error danger<?php endif; ?>">
<td class="key"><span class="control-label"><strong><?php echo $lang['location_latlong_label']; ?></strong></span><?php if($geometry_required): ?><sup><span class="glyphicon glyphicon-asterisk text-danger" title="<?php echo $lang['required_label']; ?>"></span></sup><?php endif; ?><br /><span class="description"><?php echo $lang['location_latlong_description']; ?></span></td>
<td class="value">
<div class="row">
<div class="col-xs-6">
<label class="control-label" for="_latitude"><?php echo $lang['location_latitude_label']; ?></label>
<input id="_latitude" class="form-control" type="text" name="data[_latitude]" value="<?php if(isset($data_item['_latitude'])): ?><?php echo $data_item['_latitude']; ?><?php endif; ?>" placeholder="<?php echo $lang['input_type_label'][4]; ?>"<?php if($geometry_required): ?> data-required="data[_latitude]" data-message="<?php echo rawurlencode(strip_tags($lang['error_no_latitude'])); ?>"<?php endif; ?>>
</div>
<div class="col-xs-6">
<label class="control-label" for="_longitude"><?php echo $lang['location_longitude_label']; ?></label>
<input id="_longitude" class="form-control" type="text" name="data[_longitude]" value="<?php if(isset($data_item['_longitude'])): ?><?php echo $data_item['_longitude']; ?><?php endif; ?>" placeholder="<?php echo $lang['input_type_label'][4]; ?>"<?php if($geometry_required): ?> data-required="data[_longitude]" data-message="<?php echo rawurlencode(strip_tags($lang['error_no_longitude'])); ?>"<?php endif; ?>>
</div>
</div>
</td>
<td class="options-input"></td>
</tr>

<?php else: ?>

<div id="mapwrapper">
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

<?php if(isset($data_item['id'])): ?>
<a id="disablemap" href="<?php echo BASE_URL; ?>?r=edit_data_item.edit&amp;data_id=<?php echo $table_data['id']; ?>&amp;id=<?php echo $data_item['id']; ?>&amp;disable_map=1" title="<?php echo $lang['disable_map']; ?>" data-confirm="<?php echo rawurlencode($lang['confirm_form_reload']); ?>">[x]</a>
<?php else: ?>
<a id="disablemap" href="<?php echo BASE_URL; ?>?r=edit_data_item.add&amp;data_id=<?php echo $table_data['id']; ?>&amp;disable_map=1" title="<?php echo $lang['disable_map']; ?>" data-confirm="<?php echo rawurlencode($lang['confirm_form_reload']); ?>">[x]</a>
<?php endif; ?>

</div>
</div>

<?php endif; ?>

</td>
</tr>

<?php endif; ?>

<?php if(isset($columns)): $alter=false; $i=0; foreach($columns as $column): ?>
<?php if($column['type']==0||$column['priority']!=1) { if($alter) $alter=false; else $alter=true; } ?>

<?php if($column['type']==0): ?>
<tr class="<?php if($column['priority']==2): ?>high-priority-section<?php elseif($column['priority']==1): ?>low-priority-section<?php if($alter): ?> alter<?php endif; ?><?php else: ?>default-priority-section<?php endif; ?>">
<td colspan="3"<?php if($column['priority']==2): ?><?php if(isset($sections[$column['id']])): ?> id="section-<?php echo $sections[$column['id']]['id']; ?>"<?php endif; ?><?php endif; ?>><?php if(empty($column['label'])): ?>&nbsp;<?php else: ?><?php echo $column['label']; ?><?php endif; ?><?php if($column['description']): ?><br /><span class="description"><?php echo $column['description']; ?></span><?php endif; ?></td>
</tr>
<?php else: ?>
<tr class="<?php if($column['priority']==2): ?>high-priority<?php elseif($column['priority']==1): ?>low-priority<?php else: ?>default-priority<?php endif; ?><?php if(isset($error_fields) && in_array($column['name'], $error_fields)): ?> has-error danger<?php endif; ?><?php if($alter): ?> alter<?php endif; ?>"<?php if($column['required']): ?> data-required="data[<?php echo $column['name']; ?>]" data-message="<?php echo rawurlencode(strip_tags(str_replace('[field]', $column['label'], $lang['required_field_message']))); ?>"<?php endif; ?>>
<td class="key"><?php if(!$column['choices']): /* radio buttons have their own labels */ ?><label class="control-label" for="<?php echo $column['name']; ?>"><?php echo $column['label']; ?></label><?php else: ?><span class="control-label radio-label"><?php echo $column['label']; ?></span><?php endif; ?><?php if($column['required']): ?><sup><span class="glyphicon glyphicon-asterisk text-danger" title="<?php echo $lang['required_label']; ?>"></span></sup><?php endif; ?><?php if($column['description']): ?><br /><span class="description"><?php echo $column['description']; ?></span><?php endif; ?></td>
<td class="value">
<?php if($column['relation_table']): /* related table */ ?>
<?php if(isset($verification_options[$column['name']])): ?>
<select name="data[<?php echo $column['name']; ?>]" size="<?php echo count($verification_options[$column['name']]); ?>">
<?php foreach($verification_options[$column['name']] as $verification_option): ?>
<option value="<?php echo $verification_option; ?>"><?php echo $verification_option; ?></option> 
<?php endforeach; ?>
</select>
<?php else: ?>
<input class="form-control" id="<?php echo $column['name']; ?>" type="text" name="data[<?php echo $column['name']; ?>]" value="<?php if(isset($data_item[$column['name']])) echo $data_item[$column['name']]; ?>" placeholder="<?php echo str_replace('[table]', $column['relation_table_title'], $lang['related_label']); ?>" data-autocomplete="<?php echo $column['relation']; ?>" data-autocomplete-minlength="<?php echo $settings['autocomplete_min_length']; ?>">
<?php endif; ?>

<?php elseif($column['type']==6): /* boolean */ ?>

<input id="<?php echo $column['name']; ?>" type="checkbox" name="data[<?php echo $column['name']; ?>]" value="1"<?php if(isset($data_item[$column['name']]) && $data_item[$column['name']]==true): ?> checked="checked"<?php endif; ?> />

<?php elseif($column['choices']): /* radio buttons */ ?>

<label class="unspecified radio-inline" for="<?php echo $column['name']; ?>-empty"><input id="<?php echo $column['name']; ?>-empty" type="radio" name="data[<?php echo $column['name']; ?>]" value=""<?php if(empty($data_item[$column['name']])): ?> checked="checked"<?php endif; ?><?php if(isset($error_fields) && in_array($column['name'], $error_fields)): ?> class="error"<?php endif; ?> /><?php echo $lang['not_specified_label']; ?></label><br />
<?php $cid=0; foreach($column['choices'] as $choice): ?>
<?php if($column['choice_labels'][$choice]=='*'): ?>
<span class="radio-inline">
<input id="<?php echo $column['name']; ?>-<?php echo $cid; ?>" type="radio" name="data[<?php echo $column['name']; ?>]" value="<?php echo $choice; ?>"<?php if(isset($data_item[$column['name']]) && $data_item[$column['name']]==$choice || $column['choice_labels'][$choice]=='*' && !empty($data_item[$column['name']]) && !in_array($data_item[$column['name']], $column['choices'])): ?> checked="checked"<?php endif; ?><?php if(isset($error_fields) && in_array($column['name'], $error_fields)): ?> class="error"<?php endif; ?> />
<input id="<?php echo $column['name']; ?>-<?php echo $cid; ?>-value" name="data[_<?php echo $column['name']; ?>_]" class="form-control form-control-medium" type="text" size="5" value="<?php if(isset($data_item[$column['name']]) && !in_array($data_item[$column['name']], $column['choices'])): ?><?php echo $data_item[$column['name']]; ?><?php endif; ?>" data-check="<?php echo $column['name']; ?>-<?php echo $cid; ?>">
</span>
<?php else: ?>
<label class="radio-inline" for="<?php echo $column['name']; ?>-<?php echo $cid; ?>"><input id="<?php echo $column['name']; ?>-<?php echo $cid; ?>" type="radio" name="data[<?php echo $column['name']; ?>]" value="<?php echo $choice; ?>"<?php if(isset($data_item[$column['name']]) && $data_item[$column['name']]==$choice || $column['choice_labels'][$choice]=='*' && !empty($data_item[$column['name']]) && !in_array($data_item[$column['name']], $column['choices'])): ?> checked="checked"<?php endif; ?><?php if(isset($error_fields) && in_array($column['name'], $error_fields)): ?> class="error"<?php endif; ?> /><?php echo $column['choice_labels'][$choice]; ?></label><br />
<?php endif; ?>
<?php ++$cid; endforeach; ?>  

<?php elseif($column['type']==5): /* text */ ?>
<textarea id="<?php echo $column['name']; ?>" class="form-control" name="data[<?php echo $column['name']; ?>]" rows="10"><?php if(isset($data_item[$column['name']])) echo $data_item[$column['name']]; ?></textarea>

<?php elseif($column['type']==7): /* date */ ?>
<input id="<?php echo $column['name']; ?>" class="form-control" type="text" name="data[<?php echo $column['name']; ?>]" value="<?php if(isset($data_item[$column['name']])) echo $data_item[$column['name']]; ?>" placeholder="<?php echo $lang['date_format_label']; ?>" data-date-picker="<?php echo $column['name']; ?>">

<?php elseif($column['type']==8): /* time */ ?>
<input id="<?php echo $column['name']; ?>" class="form-control" type="text" name="data[<?php echo $column['name']; ?>]" value="<?php if(isset($data_item[$column['name']])) echo $data_item[$column['name']]; ?>" placeholder="<?php echo $lang['time_format_label']; ?>">

<?php else: /* default */ ?>
<input id="<?php echo $column['name']; ?>" class="form-control" type="text" name="data[<?php echo $column['name']; ?>]" value="<?php if(isset($data_item[$column['name']])) echo $data_item[$column['name']]; ?>" placeholder="<?php echo $lang['input_type_label'][$column['type']]; ?>"><?php if(isset($lang['input_type_label'][$column['type']])): ?><?php endif; ?>

<?php endif; ?>

</td>

<td class="options-input">
<a id="<?php echo $column['name']; ?>-remember-handle" class="btn btn-<?php if(empty($_POST) && empty($data_item['id']) && isset($_SESSION[$settings['session_prefix'].'usersettings']['input_value'][$column['id']])): ?>warning<?php else: ?>default<?php endif; ?> btn-xs remember-handle" href="#" title="<?php echo $lang['remember_input_value']; ?>" onclick="saveInputValue(<?php echo $column['id']; ?>,'<?php echo $column['name']; ?>'); return false" tabindex="-1"><span class="glyphicon glyphicon-pushpin"></span></a><?php if(isset($definition)): ?> <a class="btn btn-default btn-xs" href="index.php?r=data.definition&amp;data_id=<?php echo $table_data['id']; ?>&amp;id=<?php echo $column['id']; ?>" title="<?php echo $lang['data_definition_title']; ?>" data-toggle="modal" data-target="#modal_help" data-input="content" tabindex="-1"<?php if(!$column['definition']): ?> disabled="disabled"<?php endif; ?>><span class="glyphicon glyphicon-info-sign"></span></a><?php endif; ?>
</td>

</tr>
<?php endif; ?>

<?php ++$i; endforeach; endif; ?>
<tr class="success">
<td colspan="3"><button class="btn btn-success btn-lg pull-right" type="submit"><span class="glyphicon glyphicon-save"></span> <?php echo $lang['save_submit']; ?></button></td>
</tr>

</table>
<!--</div>-->

</div>
</form>



<?php if($data_type==1 && empty($latlong_entry) && empty($_SESSION[$settings['session_prefix'].'usersettings']['disable_map'])): /* spatial data - display map:  */ ?>
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
<?php else: ?>
<?php $auxiliary_layer_1_snap = ''; ?>
<?php endif; ?>

<?php
$aux_snap = 
$js[] = 'snap = new OpenLayers.Control.Snapping({layer: vectorLayer, targets: [featuresLayer'.$auxiliary_layer_1_snap.'], greedy: false });
if(document.getElementById("snapping") && document.getElementById("snapping").className == "active") snap.activate();

if(wkt!="") // edit
 {
  var wkt = document.getElementById("_wkt").value;
  var polygonFeature = new OpenLayers.Format.WKT({"internalProjection":projDisplay,"externalProjection":projData}).read(wkt);
  vectorLayer.addFeatures([polygonFeature]);
  map.zoomToExtent(vectorLayer.getDataExtent());
  if(map.zoom > 17) map.zoomTo(17);
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
