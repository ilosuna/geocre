<h1><?php echo $lang['log_title']; ?></h1>

<?php if(isset($status)): ?>
<div class="table-responsive">
<table class="table table-striped table-hover">
<thead>
<tr>
<th><?php echo $lang['log_user_column_label']; ?></th>
<th><?php echo $lang['log_action_column_label']; ?></th>
<th><?php echo $lang['log_data_column_label']; ?></th>
<th><?php echo $lang['log_item_column_label']; ?></th>
<th><?php echo $lang['log_time_column_label']; ?></th>
<th class="options">&nbsp;</th>
</tr>
</thead>
<tbody>
<?php foreach($status as $item): ?>
<tr>
<td><?php if($item['userid']): ?><a href="<?php echo BASE_URL; ?>?r=users.details&id=<?php echo $item['userid']; ?>"><?php echo $item['username']; ?></a><?php else: ?><?php echo $lang['unknown_user']; ?><?php endif; ?></td>
<td><?php if(isset($lang['log_activity'][$item['action']])): ?><?php echo $lang['log_activity'][$item['action']]; ?><?php else: ?><?php echo $item['action']; ?><?php endif; ?></td>
<td><?php if($item['table']): ?><a href="<?php echo BASE_URL; ?>?r=data&data_id=<?php echo $item['table']; ?>"><?php echo $item['table_title']; ?></a><?php endif; ?></td>
<td><?php if($item['item']&&$item['table']): ?><a href="<?php echo BASE_URL; ?>?r=data_item&data_id=<?php echo $item['table']; ?>&id=<?php echo $item['item']; ?>"><?php echo $item['item']; ?></a><?php endif; ?></td>
<td><?php echo $item['time']; ?></td>
<td class="options"><a class="btn btn-primary btn-xs" href="<?php echo BASE_URL; ?>?r=log.details&amp;id=<?php echo $item['id']; ?>" title="<?php echo $lang['log_details_link']; ?>"><span class="glyphicon glyphicon-eye-open"></span></a>
</td>
</tr>
<?php endforeach; ?>
<tbody>
</table>
</div>

<?php endif; ?>
<?php if($pagination): ?>
<ul class="pagination">
<?php if($pagination['previous']): ?><li><a href="<?php echo BASE_URL; ?>?r=log&amp;p=<?php echo $pagination['previous']; ?>" title="<?php echo $lang['previous_page_title']; ?>"><span class="glyphicon glyphicon-chevron-left"></span></a></li><?php endif; ?>
<?php foreach($pagination['items'] as $item): ?>
<?php if($item==0): ?><li><span>&hellip;</span></li><?php elseif($item==$pagination['current']): ?><li class="active"><span><?php echo $item; ?></span></li><?php else: ?><li><a href="<?php echo BASE_URL; ?>?r=log&amp;p=<?php echo $item; ?>"><?php echo $item; ?></a></li><?php endif; ?>
<?php endforeach; ?>
<?php if($pagination['next']): ?><li><a href="<?php echo BASE_URL; ?>?r=log&amp;p=<?php echo $pagination['next']; ?>" title="<?php echo $lang['next_page_title']; ?>"><span class="glyphicon glyphicon-chevron-right"></span></a></li><?php endif; ?>  
</ul>
<?php endif; ?>
</div>
</div>
