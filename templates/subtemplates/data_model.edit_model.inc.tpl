<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=dashboard#data" title="<?php echo $lang['dashboard_title']; ?>"><?php echo $lang['dashboard_link']; ?></a></li>
<?php if(isset($db_table)): ?>
<li><a href="<?php echo BASE_URL; ?>?r=data&amp;data_id=<?php echo $db_table['id']; ?>"><?php echo $db_table['title']; ?></a></li>
<?php endif; ?>
<li class="active">
<?php if(isset($db_table['id'])): ?>
<?php echo $lang['edit_data_model_title']; ?>
<?php else: ?>
<?php echo $lang['add_data_model_title']; ?>
<?php endif; ?>
</li>
</ul>

<h1>
<?php if(isset($db_table['id'])): ?>
<?php echo $lang['edit_data_model_full_title']; ?>
<?php else: ?>
<?php echo $lang['add_data_model_title']; ?>
<?php endif; ?>
</h1>

<?php if(isset($errors) || isset($success)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<?php if(isset($db_table['id'])): /* table properties */ ?>

<?php if(isset($db_table_unavailable)): ?>
<p class="caution"><?php echo $lang['db_table_unavailable']; ?></p>
<?php endif; ?>


<ul id="myTab" class="nav nav-tabs">
<li class="active"><a href="#properties" data-toggle="tab"><?php echo $lang['data_properties_title']; ?></a></li>
<li><a href="#structure" data-toggle="tab"><?php echo $lang['data_structure_title']; ?></a></li>
</ul>

<div id="myTabContent" class="tab-content">

<div class="tab-pane fade in active" id="properties">

<form class="form-horizontal" action="index.php" method="post">
<div>
<input type="hidden" name="r" value="data_model.edit_model_submit" />
<input type="hidden" name="id" value="<?php echo $db_table['id']; ?>" />


<fieldset>
<legend><?php echo $lang['data_properties_general_label']; ?></legend>

<div class="form-group">
<label for="table_name" class="col-md-2 control-label"><?php echo $lang['db_table_name_input_label']; ?></label>
<div class="col-md-6">
<input id="table_name" class="form-control" type="text" name="table_name" value="<?php echo $db_table['table_name']; ?>" disabled>
</div>
</div>

<div class="form-group">
<label for="table_name" class="col-md-2 control-label"><?php echo $lang['db_table_type_input_label']; ?></label>
<div class="col-md-6">
<input id="table_type" class="form-control" type="text" name="table_type" value="<?php echo $lang['db_table_type_label'][$db_table['type']]; ?>" disabled>
</div>
</div>

<div class="form-group">
<label for="title" class="col-md-2 control-label"><?php echo $lang['db_table_title_input_label']; ?></label>
<div class="col-md-6">
<input id="title" class="form-control" type="text" name="title" value="<?php if(isset($db_table['title'])) echo $db_table['title']; ?>">
</div>
</div>

<?php if(isset($projects)): ?>
<div class="form-group">
<label for="project" class="col-md-2 control-label"><?php echo $lang['db_table_project_input_label']; ?></label>
<div class="col-md-6">
<select id="project" class="form-control" name="project" size="1">
<option value="0">&nbsp;</option>
<?php foreach($projects as $project): ?>
<option value="<?php echo $project['id']; ?>"<?php if($project['id']==$db_table['project']): ?> selected="selected"<?php endif; ?>><?php echo $project['title']; ?></option>
<?php endforeach; ?>
</select>
</div>
</div>
<?php endif; ?>

<?php if(isset($parent_tables)): ?>
<div class="form-group">
<label for="parent_table" class="col-md-2 control-label"><?php echo $lang['db_table_parent_input_label']; ?></label>
<div class="col-md-6">
<select id="parent_table" class="form-control" name="parent_table" size="1">
<option value="0">&nbsp;</option>
<?php foreach($parent_tables as $parent_table): ?>
<option value="<?php echo $parent_table['id']; ?>"<?php if($parent_table['id']==$db_table['parent_table']): ?> selected="selected"<?php endif; ?>><?php echo $parent_table['name']; ?></option>
<?php endforeach; ?>
</select>
</div>
</div>
<?php endif; ?>

<div class="form-group">
<span class="col-md-2 control-label radio-label"><?php echo $lang['db_table_status_input_label']; ?></span>
<div class="col-md-6">
<div class="radio">
<input id="status_0" type="radio" name="status" value="0"<?php if(empty($db_table['status'])): ?> checked="checked"<?php endif; ?> /> <label for="status_0"><?php echo $lang['db_table_status_label'][0]; ?></label><br />
<input id="status_1" type="radio" name="status" value="1"<?php if(isset($db_table['status'])&&$db_table['status']==1): ?> checked="checked"<?php endif; ?> /> <label for="status_1"><?php echo $lang['db_table_status_label'][1]; ?></label><br />
<input id="status_2" type="radio" name="status" value="2"<?php if(isset($db_table['status'])&&$db_table['status']==2): ?> checked="checked"<?php endif; ?> /> <label for="status_2"><?php echo $lang['db_table_status_label'][2]; ?></label><br />
</div>
<div class="checkbox">
<input id="readonly" type="checkbox" name="readonly" value="1"<?php if(isset($db_table['readonly'])&&$db_table['readonly']==1): ?> checked="checked"<?php endif; ?> /> <label for="readonly"><?php echo $lang['db_table_status_readonly']; ?></label>
</div>
</div>
</div>

</fieldset>

<?php if($db_table['type']==1): ?>

<fieldset>
<legend><a class="collapse-handle" data-toggle="collapse" href="#spatial-data"><?php echo $lang['data_properties_spatial_label']; ?> <span class="caret"></span></a></legend>

<div id="spatial-data" class="panel-collapse collapse">

<div class="form-group">
<span class="col-md-2 control-label radio-label"><?php echo $lang['db_table_geometry_type_input_label']; ?></span>
<div class="col-md-6">
<div class="radio">
<input id="geometry_type_0" type="radio" name="geometry_type" value="0"<?php if(empty($db_table['geometry_type'])): ?> checked="checked"<?php endif; ?> /> <label for="geometry_type_0"><?php echo $lang['db_table_geometry_type_label'][0]; ?></label><br />
<input id="geometry_type_1" type="radio" name="geometry_type" value="1"<?php if(isset($db_table['geometry_type'])&&$db_table['geometry_type']==1): ?> checked="checked"<?php endif; ?> /> <label for="geometry_type_1"><?php echo $lang['db_table_geometry_type_label'][1]; ?></label><br />
<input id="geometry_type_2" type="radio" name="geometry_type" value="2"<?php if(isset($db_table['geometry_type'])&&$db_table['geometry_type']==2): ?> checked="checked"<?php endif; ?> /> <label for="geometry_type_2"><?php echo $lang['db_table_geometry_type_label'][2]; ?></label><br />
<input id="geometry_type_3" type="radio" name="geometry_type" value="3"<?php if(isset($db_table['geometry_type'])&&$db_table['geometry_type']==3): ?> checked="checked"<?php endif; ?> /> <label for="geometry_type_3"><?php echo $lang['db_table_geometry_type_label'][3]; ?></label>
</div>
</div>
</div>

<div class="form-group">
<label for="geometry_required" class="col-md-2 control-label"><?php echo $lang['db_table_geometry_required_input_label']; ?></label>
<div class="col-md-6">
<div class="checkbox">
<input id="geometry_required" class="form-control" type="checkbox" name="geometry_required" value="1"<?php if(isset($db_table['geometry_required'])&&$db_table['geometry_required']==1): ?> checked="checked"<?php endif; ?>>
</div>
</div>
</div>

<div class="form-group">
<span class="col-md-2 control-label radio-label"><?php echo $lang['db_table_basemaps_label']; ?></span>
<div class="col-md-6">
<div class="checkbox">
<?php if(isset($available_basemaps)): ?>
<?php foreach($available_basemaps as $basemap): ?>
<input id="basemap_<?php echo $basemap['id']; ?>" type="checkbox" name="basemaps[]" value="<?php echo $basemap['id']; ?>"<?php if(isset($db_table['basemaps'])&&in_array($basemap['id'],$db_table['basemaps'])): ?> checked="checked"<?php endif; ?> /> <label for="basemap_<?php echo $basemap['id']; ?>"><?php echo $basemap['title']; ?><?php if($basemap['default']): ?> <?php echo $lang['db_table_basemaps_defaut_label']; ?><?php endif; ?></label><br />
<?php endforeach; ?></div>
<span class="help-block"><?php echo $lang['db_table_basemaps_description']; ?></span>
<?php endif; ?>
</div>
</div>

<div class="form-group">
<label for="min_scale" class="col-md-2 control-label"><?php echo $lang['db_table_scale_range_input_label']; ?></label>
<div class="col-md-6">
<div class="row">
<div class="col-md-6">
<input id="min_scale" class="form-control" type="text" name="min_scale" value="<?php if(isset($db_table['min_scale'])) echo $db_table['min_scale']; ?>" size="35">
</div>
<div class="col-md-6">
<input id="max_scale" class="form-control" type="text" name="max_scale" value="<?php if(isset($db_table['max_scale'])) echo $db_table['max_scale']; ?>" size="35">
</div>
</div>
<span class="help-block"><?php echo $lang['db_table_scale_range_description']; ?></span>
</div>
</div>

<div class="form-group">
<label for="simplification_tolerance" class="col-md-2 control-label"><?php echo $lang['db_table_simp_tol_input_label']; ?></label>
<div class="col-md-6">
<input id="simplification_tolerance" class="form-control" type="text" name="simplification_tolerance" value="<?php if(isset($db_table['simplification_tolerance'])) echo $db_table['simplification_tolerance']; ?>">
<span class="help-block"><?php echo $lang['db_table_simp_tol_description']; ?></span>
</div>
</div>

<div class="form-group">
<label for="simplification_tolerance_extent_factor" class="col-md-2 control-label"><?php echo $lang['db_table_simp_tol_sf_input_label']; ?></label>
<div class="col-md-6">
<input id="simplification_tolerance_extent_factor" class="form-control" type="text" name="simplification_tolerance_extent_factor" value="<?php if(isset($db_table['simplification_tolerance_extent_factor'])) echo $db_table['simplification_tolerance_extent_factor']; ?>">
<span class="help-block"><?php echo $lang['db_table_simp_tol_sf_description']; ?></span>
</div>
</div>

<div class="form-group">
<span class="col-md-2 control-label radio-label"><?php echo $lang['db_table_overview_input_label']; ?></span>
<div class="col-md-6">
<div class="radio">
<input id="layer_overview_0" type="radio" name="layer_overview" value="0"<?php if(empty($db_table['layer_overview'])): ?> checked="checked"<?php endif; ?> /> <label for="layer_overview_0"><?php echo $lang['db_table_layer_overview_label'][0]; ?></label><br />
<input id="layer_overview_1" type="radio" name="layer_overview" value="1"<?php if(isset($db_table['layer_overview'])&&$db_table['layer_overview']==1): ?> checked="checked"<?php endif; ?> /> <label for="layer_overview_1"><?php echo $lang['db_table_layer_overview_label'][1]; ?></label><br />
<input id="layer_overview_2" type="radio" name="layer_overview" value="2"<?php if(isset($db_table['layer_overview'])&&$db_table['layer_overview']==2): ?> checked="checked"<?php endif; ?> /> <label for="layer_overview_2"><?php echo $lang['db_table_layer_overview_label'][2]; ?></label>
</div>
<span class="help-block"><?php echo $lang['db_table_overview_description']; ?></span>
</div>
</div>

<?php if(isset($auxiliary_layers)): ?>
<div class="form-group">
<label for="auxiliary_layer_1" class="col-md-2 control-label"><?php echo $lang['db_table_auxiliary_layer_input_label']; ?></label>
<div class="col-md-6">
<select id="auxiliary_layer_1" class="form-control" name="auxiliary_layer_1" size="1">
<option value="0"></option>
<?php foreach($auxiliary_layers as $auxiliary_layer): ?>
<option value="<?php echo $auxiliary_layer['id']; ?>"<?php if($auxiliary_layer['id']==$db_table['auxiliary_layer_1']): ?> selected="selected"<?php endif; ?>><?php echo $auxiliary_layer['name']; ?></option>
<?php endforeach; ?>
</select>
</div>
</div>
<?php endif; ?>

</div>

</fieldset>

<?php endif; ?>

<fieldset>
<legend><a class="collapse-handle" data-toggle="collapse" href="#metadata"><?php echo $lang['data_properties_metadata_label']; ?> <span class="caret"></span></a></legend>

<div id="metadata" class="panel-collapse collapse">

<div class="form-group">
<label for="description" class="col-md-2 control-label"><?php echo $lang['db_table_description_label']; ?></label>
<div class="col-md-6">
<textarea id="description" class="form-control" name="description" cols="60" rows="10"><?php if(isset($db_table['description'])): ?><?php echo $db_table['description']; ?><?php endif; ?></textarea>
</div>
</div>

</div>

</fieldset>

<div class="form-group">
<div class="col-md-offset-2 col-md-6">
<button type="submit" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-save"></span> <?php echo $lang['save_submit']; ?></button>
</div>
</div>

</div>
</form>

</div>

<div class="tab-pane fade in" id="structure">

<div class="row">
<div class="col-md-12">
<a class="btn btn-success pull-right" href="<?php echo BASE_URL; ?>?r=data_model.add_item&amp;data_id=<?php echo $db_table['id']; ?>"><span class="glyphicon glyphicon-plus"></span> <?php echo $lang['data_model_add_item_link']; ?></a>
</div>
</div>

<?php if(isset($db_items)): ?>

<div class="table-responsive">
<table class="table table-striped table-hover">
<thead>
<tr>
<th><?php echo $lang['db_table_items_name_column_label']; ?></th>
<th><?php echo $lang['db_table_items_label_column_label']; ?></th>
<th><?php echo $lang['db_table_items_type_column_label']; ?></th>
<?php /*<th><?php echo $lang['db_table_items_length_column_label']; ?></th>*/ ?>
<th><?php echo $lang['db_table_items_required_column_label']; ?></th>
<?php /* TODO <th><?php echo $lang['db_table_items_overview_column_label']; ?></th>*/ ?>
<th>&nbsp;</th>
</tr>
</thead>

<tbody data-sortable="<?php echo BASE_URL; ?>?r=data_model.reorder_items">

<?php $i=1; foreach($db_items as $db_item): ?>
<tr id="item_<?php echo $db_item['id']; ?>"<?php if($db_item['column_type']==0): ?> class="<?php if($db_item['section_type']==1): ?>section<?php else: ?>subsection<?php endif; ?>"<?php endif; ?>>

<?php if($db_item['column_type']==0): ?>
<td colspan="4"><?php if($db_item['label']): ?><?php echo $db_item['label']; ?><?php else: ?><?php echo $db_item['name']; ?><?php endif; ?></td>
<?php else: ?>
<td><?php if(isset($db_item['column_exists'])): ?><?php echo $db_item['name']; ?><?php else: ?><span class="text-danger" title="<?php echo $lang['column_doesnt_exist']; ?>"><?php echo $db_item['name']; ?></span><?php endif; ?></td>
<td><?php echo $db_item['label']; ?></td>
<td><?php if(isset($db_item['relation'])): ?><span class="text-danger"><?php echo $db_item['relation']; ?></span><?php elseif(isset($column_types[$db_item['column_type']])): ?><?php echo $column_types[$db_item['column_type']]['label']; ?><?php else: ?><?php echo $db_item['column_type']; ?><?php endif; ?></td>
<?php /*<td><?php echo $db_item['column_length']; ?></td>*/ ?>
<td><?php if($db_item['required']): ?><span class="glyphicon glyphicon-ok text-success" title="<?php echo $lang['yes']; ?>"></span><?php endif; ?></td>
<?php /* TODO <td><?php if($db_item['overview']): ?><span class="glyphicon glyphicon-ok text-success" title="<?php echo $lang['yes']; ?>"></span><?php endif; ?></td>*/ ?>
<?php endif; ?>

<td class="options">
<a class="btn btn-primary btn-xs" href="?r=data_model.edit_item&amp;id=<?php echo $db_item['id']; ?>" title="<?php echo $lang['edit']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
<a class="btn btn-danger btn-xs" href="?r=data_model.delete_item&amp;id=<?php echo $db_item['id']; ?>" title="<?php echo $lang['delete']; ?>" data-delete-confirm="<?php echo rawurlencode($lang['delete_db_table_item_message']); ?>"><span class="glyphicon glyphicon-remove"></span></a>
<span class="btn btn-success btn-xs sortable_handle" title="<?php echo $lang['drag_and_drop']; ?>"><span class="glyphicon glyphicon-sort"></span></span>
</td>

</tr>
<?php ++$i; endforeach; ?>
</tbody>

</table>
</div>


<?php else: ?>

<div class="alert alert-warning top-space"><?php echo $lang['no_db_table_items_available']; ?></div>

<?php endif; ?>

</div>

</div>

<?php else: /* add table */ ?>

<form class="form-horizontal" action="index.php" method="post">
<div>
<input type="hidden" name="r" value="data_model.add_model_submit" />

<div class="form-group">
<label for="table_name" class="col-sm-2 control-label"><?php echo $lang['db_table_name_input_label']; ?></label>
<div class="col-sm-8">
<input id="table_name" class="form-control" type="text" name="table_name" value="<?php if(isset($db_table['table_name'])) echo $db_table['table_name'];?>">
</div>
</div>

<div class="form-group">
<label for="title" class="col-sm-2 control-label"><?php echo $lang['db_table_title_input_label']; ?></label>
<div class="col-sm-8">
<input id="title" class="form-control" type="text" name="title" value="<?php if(isset($db_table['title'])) echo $db_table['title'];?>">
</div>
</div>


<?php if(isset($projects)): ?>
<div class="form-group">
<label class="col-sm-2 control-label" for="project"><?php echo $lang['db_table_project_input_label']; ?></label>
<div class="col-sm-8">
<select id="project" class="form-control" name="project" size="1">
<option value="0">&nbsp;</option>
<?php foreach($projects as $project): ?>
<option value="<?php echo $project['id']; ?>"><?php echo $project['title']; ?></option>
<?php endforeach; ?>
</select>
</div>
</div>
<?php endif; ?>

<?php if(isset($parent_tables)): ?>
<div class="form-group">
<label class="col-sm-2 control-label" for="parent_table"><?php echo $lang['db_table_parent_input_label']; ?></label>
<div class="col-sm-8">
<select id="parent_table" class="form-control" name="parent_table" size="1">
<option value="0">&nbsp;</option>
<?php foreach($parent_tables as $parent_table): ?>
<option value="<?php echo $parent_table['id']; ?>"><?php echo $parent_table['name']; ?></option>
<?php endforeach; ?>
</select>
</div>
</div>
<?php endif; ?>

<div class="form-group">
<span class="col-sm-2 control-label radio-label"><?php echo $lang['db_table_type_input_label']; ?></span>
<div class="col-sm-8">
<div class="radio">
<label>
<input id="type_0" type="radio" name="type" value="0"<?php if(empty($db_table['type'])): ?> checked="checked"<?php endif; ?> /> <?php echo $lang['db_table_type_label'][0]; ?>
</label>
</div>
<div class="radio">
<label>
<input id="type_1" type="radio" name="type" value="1"<?php if(isset($db_table['type'])&&$db_table['type']==1): ?> checked="checked"<?php endif; ?> /> <?php echo $lang['db_table_type_label'][1]; ?>
</label>
</div>
</div>
</div>

<div class="form-group">
<div class="col-sm-offset-2 col-sm-8">
<div class="checkbox">
<label>
<input name="no_database_altering" type="checkbox"> <?php echo $lang['data_model_no_table_creation']; ?>
</label>
</div>
</div>
</div>

<div class="form-group">
<div class="col-sm-offset-2 col-sm-10">
<button type="submit" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-save"></span> <?php echo $lang['save_submit']; ?></button>
</div>
</div>

</div>
</form>

<?php endif; /* add table */ ?>
