<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=dashboard#data" title="<?php echo $lang['dashboard_title']; ?>"><?php echo $lang['dashboard_link']; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=data_model.edit_model&amp;id=<?php echo $data_id; ?>#structure"><?php echo $lang['edit_data_model_title']; ?></a></li>
<li class="active">
<?php if(isset($model_item['id'])): ?>
<?php echo $lang['data_model_edit_item_title']; ?>
<?php else: ?>
<?php echo $lang['data_model_add_item_title']; ?>
<?php endif; ?>
</li>
</ul>

<h1>
<?php if(isset($model_item['id'])): ?>
<?php echo $lang['data_model_edit_item_title']; ?>
<?php else: ?>
<?php echo $lang['data_model_add_item_title']; ?>
<?php endif; ?>
</h1>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<form action="index.php" method="post">
<div>
<?php if(isset($model_item['id'])): ?>
<input type="hidden" name="r" value="data_model.edit_item_submit" />
<input type="hidden" name="id" value="<?php echo $model_item['id']; ?>" />
<?php else: ?>
<input type="hidden" name="r" value="data_model.add_item_submit" />
<input type="hidden" name="data_id" value="<?php echo $data_id; ?>" />
<?php endif; ?>


<div class="table-responsive">
<table class="table table-striped">

<tr class="success">
<td colspan="2"><button class="btn btn-success btn-lg pull-right" type="submit"><span class="glyphicon glyphicon-save"></span> <?php echo $lang['save_submit']; ?></button></td>
</tr>

<tr<?php if(isset($error_fields) && in_array('name', $error_fields)): ?> class="has-error danger"<?php endif; ?>>
<td class="key"><label class="control-label" for="name"><?php echo $lang['db_tabel_item_name_label']; ?></label><br />
<span class="description"><?php echo $lang['db_tabel_item_name_description']; ?></span></td>
<td class="value"><input id="name" class="form-control" type="text" name="name" value="<?php if(isset($model_item['name'])): ?><?php echo $model_item['name']; ?><?php endif; ?>" size="50" /></td>
</tr>
<tr>
<td class="key"><label for="label"><?php echo $lang['db_tabel_item_label_label']; ?></label><br />
<span class="description"><?php echo $lang['db_tabel_item_label_description']; ?></span></td>
<td class="value"><input id="label" class="form-control" type="text" name="label" value="<?php if(isset($model_item['label'])): ?><?php echo $model_item['label']; ?><?php endif; ?>" size="50" /></td>
</tr>


<tr>
<td class="key"><label for="description"><?php echo $lang['db_tabel_item_description_label']; ?></label><br />
<span class="description"><?php echo $lang['db_tabel_item_description_description']; ?></span></td>
<td class="value"><input id="description" class="form-control" type="text" name="description" value="<?php if(isset($model_item['description'])): ?><?php echo $model_item['description']; ?><?php endif; ?>" size="50" /></td>
</tr>

<tr>
<td class="key"><?php echo $lang['db_tabel_item_type_label']; ?><br />
<span class="description"><?php echo $lang['db_tabel_item_type_description']; ?></span></td>
<td class="value">
<div class="radio">
<label>
<input type="radio" name="item_type" value="0"<?php if(isset($model_item['item_type']) && $model_item['item_type']==0): ?> checked<?php endif; ?><?php if(isset($model_item['id'])): ?> disabled="disabled"<?php endif; ?>>
<?php echo $lang['db_tabel_item_type'][0]; ?>
</label>
</div>
<div class="radio">
<label>
<input type="radio" name="item_type" value="1"<?php if(isset($model_item['item_type']) && $model_item['item_type']==1): ?> checked<?php endif; ?><?php if(isset($model_item['id'])): ?> disabled="disabled"<?php endif; ?>>
<?php echo $lang['db_tabel_item_type'][1]; ?>
</label>
</div>
</td>
</tr>

<tr<?php if(isset($error_fields) && in_array('type', $error_fields)): ?> class="has-error danger"<?php endif; ?>>
<td class="key"><label class="control-label" for="type"><?php echo $lang['db_tabel_data_type_label']; ?></label><br />
<span class="description"><?php echo $lang['db_tabel_data_type_description']; ?></span></td>
<td class="value">
<select id="type" class="form-control form-control-default form-control-inline" name="column_type" size="1"<?php if(isset($model_item['id'])): ?> disabled="disabled"<?php endif; ?>>
<?php foreach($column_types as $key => $value): ?>
<option value="<?php echo $key; ?>"<?php if(isset($model_item['column_type']) && $key==$model_item['column_type']): ?> selected="selected"<?php endif; ?>><?php echo $value['label']; ?></option>
<?php endforeach; ?>
</select>
<input id="column_length" class="form-control form-control-small form-control-inline" type="text" name="column_length" value="<?php if(!empty($model_item['column_length'])): ?><?php echo $model_item['column_length']; ?><?php endif; ?>" size="7"<?php if(isset($model_item['id'])): ?> disabled="disabled"<?php endif; ?>></td>
</tr>

<tr>
<td class="key"><label><?php echo $lang['db_tabel_item_section_label']; ?></label><br />
<span class="description"><?php echo $lang['db_tabel_item_section_description']; ?></span></td>
<td class="value">
<select id="type" class="form-control form-control-default form-control-inline" name="section_type" size="1"<?php if(isset($model_item['id']) && isset($model_item['column_type']) && $model_item['column_type']>0): ?> disabled="disabled"<?php endif; ?>>
<option value="0"<?php if(isset($model_item['section_type']) && $model_item['section_type']==0): ?> selected="selected"<?php endif; ?>><?php echo $lang['data_model_section_type'][0]; ?></option>
<option value="1"<?php if(isset($model_item['section_type']) && $model_item['section_type']==1): ?> selected="selected"<?php endif; ?>><?php echo $lang['data_model_section_type'][1]; ?></option>
<option value="2"<?php if(isset($model_item['section_type']) && $model_item['section_type']==2): ?> selected="selected"<?php endif; ?>><?php echo $lang['data_model_section_type'][2]; ?></option>
</select>
</td>
</tr>


<tr>
<td class="key"><label for="range_from"><?php echo $lang['db_tabel_item_range_label']; ?></label><br />
<span class="description"><?php echo $lang['db_tabel_item_range_description']; ?></span></td>
<td class="value"><input id="range_from" class="form-control form-control-medium form-control-inline" type="text" name="range_from" value="<?php if(isset($model_item['range_from'])): ?><?php echo $model_item['range_from']; ?><?php endif; ?>" size="7" /> - <input id="range_to" class="form-control form-control-medium form-control-inline" type="text" name="range_to" value="<?php if(isset($model_item['range_to'])): ?><?php echo $model_item['range_to']; ?><?php endif; ?>" size="7" /></td>
</tr>

<tr>
<td class="key"><strong><?php echo $lang['db_tabel_item_choices_label']; ?></strong><br />
<span class="description"><?php echo $lang['db_tabel_item_choices_description']; ?></span></td>
<td class="value">
   <table class="choices-table">
   <tr>
   <th><label for="choices"><?php echo $lang['db_tabel_item_choice_values_label']; ?></label></th>
   <th><label for="choice_labels"><?php echo $lang['db_tabel_item_choice_labels_label']; ?></label></th>
   </tr>
   <tr>
   <td class="choice-value"><textarea id="choices" class="form-control" name="choices" rows="10" cols="10" wrap="off"><?php if(isset($model_item['choices'])): ?><?php echo $model_item['choices']; ?><?php endif; ?></textarea></td>
   <td class="choice-label"><textarea id="choice_labels" class="form-control" name="choice_labels" rows="10" cols="20" wrap="off"><?php if(isset($model_item['choice_labels'])): ?><?php echo $model_item['choice_labels']; ?><?php endif; ?></textarea></td>
   </tr> 
   </table>
</td>
</tr>

<tr>
<td class="key"><label for="required"><?php echo $lang['db_tabel_item_required_label']; ?></label><br />
<span class="description"><?php echo $lang['db_tabel_item_required_description']; ?></span></td>
<td class="value"><input id="required" type="checkbox" name="required" value="1"<?php if(isset($model_item['required']) && $model_item['required']==1): ?> checked="checked"<?php endif; ?> /></td>
</tr>

<?php /*
TODO
<tr>
<td class="key"><label for="overview"><?php echo $lang['db_tabel_item_overview_label']; ?></label><br />
<span class="description"><?php echo $lang['db_tabel_item_overview_description']; ?></span></td>
<td class="value"><input id="overview" type="checkbox" name="overview" value="1"<?php if(isset($model_item['overview']) && $model_item['overview']==1): ?> checked="checked"<?php endif; ?> /></td>
</tr>
*/ ?>

<tr>
<td colspan="2"><a href="#additional-options" data-toggle="collapse" data-target=".additional-options"><?php echo $lang['db_tabel_item_additional_options_link']; ?> <span class="caret"></span></a>
</td>
</tr>

<tr class="additional-options collapse">
<td class="key"><label for="regex"><?php echo $lang['db_tabel_item_regex_label']; ?></label><br />
<span class="description"><?php echo $lang['db_tabel_item_regex_description']; ?></span></td>
<td class="value"><input id="regex" class="form-control" type="text" name="regex" value="<?php if(isset($model_item['regex'])): ?><?php echo $model_item['regex']; ?><?php endif; ?>" size="50" /></td>
</tr>
<?php if(isset($relations)): ?>
<tr class="additional-options collapse">
<td class="key"><label for="relation"><?php echo $lang['db_tabel_item_relation_label']; ?></label><br />
<span class="description"><?php echo $lang['db_tabel_item_relation_description']; ?></span></td>
<td class="value">
<select id="relation" class="form-control" name="relation" size="1">
<option value="0">&nbsp;</option>
<?php foreach($relations as $relation): ?>
<option value="<?php echo $relation['id']; ?>"<?php if(isset($model_item['relation']) && $relation['id']==$model_item['relation']): ?> selected="selected"<?php endif; ?>><?php echo $relation['table_name']; ?>.<?php echo $relation['column_name']; ?></option>
<?php endforeach; ?>
</select>
</tr>
<?php endif; ?>
<tr class="additional-options collapse">
<td class="key"><label for="relation_column"><?php echo $lang['db_tabel_item_relation_column_label']; ?></label><br />
<span class="description"><?php echo $lang['db_tabel_item_relation_column_description']; ?></span></td>
<td class="value"><input id="relation_column" type="checkbox" name="relation_column" value="1"<?php if(isset($model_item['relation_column']) && $model_item['relation_column']==1): ?> checked="checked"<?php endif; ?> /></td>
</tr>


<tr class="additional-options collapse">
<td class="key"><strong><?php echo $lang['data_model_special_options_label']; ?></strong></td>
<td class="value">
<?php if(isset($model_item['id'])): ?>
<div class="checkbox">
<label for="delete_item">
<input id="delete_item" type="checkbox" name="delete_item" value="1">
<?php echo $lang['data_model_delete_item_label']; ?>
</label>
</div>
<?php endif; ?>
<div class="checkbox">
<label for="no_database_altering">
<input id="no_database_altering" type="checkbox" name="no_database_altering" value="1">
<?php echo $lang['no_database_altering_label']; ?>
</label>
</div>
</tr>

<tr class="success">
<td colspan="2"><button class="btn btn-success btn-lg pull-right" type="submit"><span class="glyphicon glyphicon-save"></span> <?php echo $lang['save_submit']; ?></button></td>
</tr>

</table>
</div>


<p></p>
</div>
</form>
