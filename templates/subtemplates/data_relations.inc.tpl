<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=dashboard#data"><?php echo $lang['dashboard_link']; ?></a></li>
<li class="active"><?php echo $lang['data_relations_title']; ?></li>
</ul>

<h1><?php echo $lang['data_relations_title']; ?></h1>

<?php if(isset($table_names)): ?>

<form action="index.php" method="post">
<div>
<input type="hidden" name="r" value="data_relations" />


<div class="table-responsive">
<table class="table table-striped table-hover">
<thead>
<tr>
<th><?php echo $lang['data_relations_model_1_label']; ?></th>
<th><?php echo $lang['data_relations_model_2_label']; ?></th>
<th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php if(isset($relations)): ?>
<?php foreach($relations as $relation): ?>
<tr>
<td><?php if(isset($table_names[$relation['t1']])): ?><span title="<?php echo $table_names[$relation['t1']]['title']; ?>"><?php echo $table_names[$relation['t1']]['name']; ?><?php else: ?><?php echo $relation['t1']; ?><?php endif; ?></td>
<td><?php if(isset($table_names[$relation['t2']])): ?><span title="<?php echo $table_names[$relation['t2']]['title']; ?>"><?php echo $table_names[$relation['t2']]['name']; ?><?php else: ?><?php echo $relation['t2']; ?><?php endif; ?><br />
</td>
<td class="options"><a class="btn btn-danger btn-xs" href="<?php echo BASE_URL; ?>?r=data_relations&amp;delete=<?php echo $relation['id']; ?>" title="<?php echo $lang['delete']; ?>" data-delete-confirm><span class="glyphicon glyphicon-remove"></span></a></td>
</tr>
<?php endforeach; ?> 
<?php endif; ?>
</tbody>
<tfoot>
<tr>
<td><select id="t1" class="form-control" name="t1" size="1">
<?php foreach($tables as $table): ?>
<option value="<?php echo $table['id']; ?>"><?php echo $table['name']; ?></option>
<?php endforeach; ?>
</select></td>
<td><select id="t2" class="form-control" name="t2" size="1">
<?php foreach($tables as $table): ?>
<option value="<?php echo $table['id']; ?>"><?php echo $table['name']; ?></option>
<?php endforeach; ?>
</select></td>
<td><input class="btn btn-primary" type="submit" value="<?php echo $lang['add_submit']; ?>" /></td>
</tr>
</tfoot>
</table>
</div>

</div>
</form>

<?php else: ?>

<p><em><?php echo $lang['no_db_tables_available']; ?></em></p>

<?php endif; ?>

