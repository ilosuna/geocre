<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=log"><?php echo $lang['log_title']; ?></a></li>
<li class="active"><?php echo $lang['log_details_title']; ?></li>
</ul>

<h1><?php echo $lang['log_details_title']; ?></h1>

<table class="table table-striped table-bordered">
<tr>
<td class="key"><strong><?php echo $lang['log_user_column_label']; ?></strong></td>
<td class="value"><?php if($log_data['userid']): ?><a href="<?php echo BASE_URL; ?>?r=users.details&id=<?php echo $log_data['userid']; ?>"><?php echo $log_data['username']; ?></a><?php else: ?><?php echo $lang['unknown_user']; ?><?php endif; ?></td>
</tr>

<tr>
<td class="key"><strong><?php echo $lang['log_action_column_label']; ?></strong></td>
<td class="value"><?php if(isset($lang['log_activity'][$log_data['action']])): ?><?php echo $lang['log_activity'][$log_data['action']]; ?><?php else: ?><?php echo $log_data['action']; ?><?php endif; ?></td>
</tr>

<tr>
<td class="key"><strong><?php echo $lang['log_data_column_label']; ?></strong></td>
<td class="value"><?php if($log_data['table']): ?><a href="<?php echo BASE_URL; ?>?r=data&data_id=<?php echo $log_data['table']; ?>"><?php echo $log_data['table_title']; ?></a><?php endif; ?></td>
</tr>

<tr>
<td class="key"><strong><?php echo $lang['log_item_column_label']; ?></strong></td>
<td class="value"><?php if($log_data['item']&&$log_data['table']): ?><a href="<?php echo BASE_URL; ?>?r=data_item&data_id=<?php echo $log_data['table']; ?>&id=<?php echo $log_data['item']; ?>"><?php echo $log_data['item']; ?></a><?php endif; ?></td>
</tr>

<tr>
<td class="key"><strong><?php echo $lang['log_time_column_label']; ?></strong></td>
<td class="value"><?php echo $log_data['time']; ?></td>
</tr>

</table>

<?php if($log_data['item']): ?>
<table class="table table-bordered">
<tr>
<th><?php echo $lang['log_previous_column_label']; ?></th>
<th><?php echo $lang['log_current_column_label']; ?></th>
</tr>

<tr>

<td>

<?php if(isset($log_data['previous_data'])): ?>
<table class="table table-striped">
<?php foreach($log_data['previous_data'] as $key=>$val): ?>
<tr>
<td><span class="text-muted"><?php echo $key; ?></span></td>
<td><?php echo $val; ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<div class="alert alert-warning"><?php echo $lang['no_data_message']; ?></div>
<?php endif; ?>
</td>

<td>
<?php if(isset($log_data['current_data'])): ?>
<table class="table table-striped">
<?php foreach($log_data['current_data'] as $key=>$val): ?>
<tr<?php if(isset($log_data['difference'][$key])||isset($log_data['previous_data'])&&!array_key_exists($key, $log_data['previous_data'])): ?> class="danger"<?php endif; ?>>
<td><span class="text-muted"><?php echo $key; ?></span></td>
<td><?php echo $val; ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<div class="alert alert-warning"><?php echo $lang['no_data_message']; ?></div>
<?php endif; ?></td>
</tr>

</table>
<?php endif; ?>
