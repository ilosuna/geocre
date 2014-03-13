<div class="row">
<div class="col-sm-6">
<h1><?php echo $lang['dashboard_headline']; ?></h1>
</div>
<div class="col-sm-6">
<?php if(isset($help)): ?>
<a class="btn btn-default btn-top-right" href="index.php?r=help.<?php echo $help; ?>" data-toggle="modal" data-target="#modal_help"><span class="glyphicon glyphicon-question-sign"></span> <?php echo $lang['help']; ?></a>
<?php endif; ?>
</div>
</div>

<?php if(isset($errors) || isset($success) || isset($failure)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<ul id="myTab" class="nav nav-tabs">
<li class="active"><a href="#data" data-toggle="tab"><?php echo $lang['data_label']; ?></a></li>
<li><a href="#activity" data-toggle="tab"><?php echo $lang['activity_label']; ?></a></li>
</ul>
      
<div id="myTabContent" class="tab-content">

<div class="tab-pane fade in active" id="data">

<?php if(isset($data)): ?>
<div class="table-responsive">
<table class="table table-striped table-hover">
<thead>
<tr>
<th><?php echo $lang['data_column_label']; ?></th>
<th><?php echo $lang['project_column_label']; ?></th>
<th><?php echo $lang['data_type_column_label']; ?></th>
<th><?php echo $lang['number_of_records_column_label']; ?></th>
<th><?php echo $lang['editable_column_label']; ?></th>
<th><!--<?php echo $lang['options_column_label']; ?>--></th>
</tr>
</thead>
<tbody<?php if($permission['data_management']): ?> data-sortable="<?php echo BASE_URL; ?>?r=data_model.reorder_models"<?php endif; ?>>
<?php foreach($data as $data_item): ?>
<tr id="item_<?php echo $data_item['id']; ?>">
<td><a class="<?php if($data_item['parent_table']): ?>data-child<?php else: ?>data-primary<?php endif; ?><?php if(!$data_item['available']): ?> data-unavailable<?php elseif($data_item['status']==0): ?> data-draft<?php endif; ?>" href="<?php echo BASE_URL; ?>?r=data&amp;data_id=<?php echo $data_item['id']; ?>"><?php echo $data_item['title']; ?></a></td>
<td><?php echo $data_item['project']; ?></td>
<td><span class="glyphicon <?php if($data_item['type']==1): ?>glyphicon-globe<?php else: ?>glyphicon-list<?php endif; ?>"></span> <?php echo $lang['db_table_type_label'][$data_item['type']]; ?></td>
<td><?php echo $data_item['records']; ?></td>
<td><?php if($data_item['edit']): ?><span class="glyphicon glyphicon-ok text-success" title="<?php echo $lang['yes']; ?>"></span><?php endif; ?></td>
<td class="options"><a class="btn btn-primary btn-xs" href="<?php echo BASE_URL; ?>?r=download_data&amp;id=<?php echo $data_item['id']; ?>" title="<?php echo $lang['download_data_link']; ?>"><span class="glyphicon glyphicon-cloud-download"></span></a>&nbsp; <a class="btn btn-primary btn-xs" href="<?php echo BASE_URL; ?>?r=download_sheet&amp;id=<?php echo $data_item['id']; ?>" title="<?php echo $lang['download_sheet_link']; ?>"><span class="glyphicon glyphicon-list-alt"></span></a><?php if($data_item['manage']): ?>&nbsp; <a class="btn btn-primary btn-xs" href="<?php echo BASE_URL; ?>?r=data_model.edit_model&amp;id=<?php echo $data_item['id']; ?>" title="<?php echo $lang['edit_data_model_link']; ?>"><span class="glyphicon glyphicon-wrench"></span></a><?php endif; ?><?php if($permission['data_management']): ?>&nbsp; <a class="btn btn-danger btn-xs" href="<?php echo BASE_URL; ?>?r=data_model.delete_model&amp;id=<?php echo $data_item['id']; ?>" title="<?php echo $lang['delete_data_model_link']; ?>"><span class="glyphicon glyphicon-remove"></span></a>&nbsp; <span class="btn btn-success btn-xs sortable_handle" title="<?php echo $lang['drag_and_drop']; ?>"><span class="glyphicon glyphicon-sort"></span><?php endif; ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>



<?php if(isset($data_stock_pagination) && $data_stock_pagination): ?>
<div class="row">
<div class="col-md-6">
<?php echo $lang['displayed_data_stocks_label']; ?>
</div>
<div class="col-md-6">
<div class="pull-right">
<ul class="pagination nomargin">
<?php if($data_stock_pagination['previous']): ?><li><a href="<?php echo BASE_URL; ?>?r=dashboard&amp;dsp=<?php echo $data_stock_pagination['previous']; ?>" title="<?php echo $lang['previous_page_title']; ?>"><span class="glyphicon glyphicon-chevron-left"></span></a></li><?php endif; ?>
<?php foreach($data_stock_pagination['items'] as $item): ?>
<?php if($item==0): ?><li><span>&hellip;</span></li><?php elseif($item==$data_stock_pagination['current']): ?><li class="active"><span><?php echo $item; ?></span></li><?php else: ?><li><a href="<?php echo BASE_URL; ?>?r=dashboard&amp;dsp=<?php echo $item; ?>"><?php echo $item; ?></a></li><?php endif; ?>
<?php endforeach; ?>
<?php if($data_stock_pagination['next']): ?><li><a href="<?php echo BASE_URL; ?>?r=dashboard&amp;dsp=<?php echo $data_stock_pagination['next']; ?>"><span class="glyphicon glyphicon-chevron-right"></span></a></li><?php endif; ?>  
</ul>
&nbsp;<a class="btn btn-default pull-right" href="<?php echo BASE_URL; ?>?r=dashboard&amp;show_all=true"><?php echo $lang['display_all_data_stocks_label']; ?></a>
</div>

</div>
</div>
<?php endif; ?>

<?php else: ?>
<div class="alert alert-warning"><?php echo $lang['no_data_message']; ?></div>
<?php endif; ?>

<?php if($permission['data_management']): ?>
<p><a class="btn btn-success" href="<?php echo BASE_URL; ?>?r=data_model.add_model"><span class="glyphicon glyphicon-plus"></span> <?php echo $lang['add_data_model_link']; ?></a>
<?php if($settings['many_to_many_relationships']): ?> <a class="btn btn-default" href="<?php echo BASE_URL; ?>?r=data_relations" class="table_relations"><span class="glyphicon glyphicon-random"></span> <?php echo $lang['data_relations_link']; ?></a><?php endif; ?></p>
<?php endif; ?>

</div>



<div class="tab-pane fade in" id="activity">
<div id="status">
<form action="<?php echo BASE_URL; ?>" method="post">
<div>
<input type="hidden" name="r" value="dashboard.status" />
<input type="hidden" name="status_submit" value="true" />

<div class="input-group" style="margin-bottom:20px;">
<input class="form-control input-lg" id="status_message" type="text" name="status_message" placeholder="<?php echo $lang['status_message_label']; ?>">
<span class="input-group-btn">
<button class="btn btn-primary btn-lg" type="submit"><?php echo $lang['status_submit']; ?></button>
</span>
</div>

</div>
</form>

<?php if(isset($status)): ?>
<!--<ul class="statusitems">-->
<?php foreach($status as $item): ?>

<div class="alert<?php if($item['action']==0): ?> alert-info<?php elseif($item['action']==5): ?> alert-danger<?php elseif($item['action']==3 || $item['action']==4): ?> alert-success<?php else: ?> alert-warning<?php endif; ?> alert-activity">
<!--<li<?php if($item['action']==0): ?> class="message"<?php endif; ?>>-->
<?php if($permission['admin']): ?>
<a class="close text-danger" href="<?php echo BASE_URL; ?>?r=dashboard.status&amp;delete_status_item=<?php echo $item['id']; ?>" title="<?php echo $lang['delete']; ?>" onclick="return delete_confirm(this, '<?php echo rawurlencode($lang['delete_status_item_message']); ?>')">&times;</a>
<?php endif; ?>
<?php if($item['action']==1): ?>
<?php echo str_replace('[user]', $item['username'], $lang['status_logged_in']); ?> 
<?php elseif($item['action']==2): ?>
<?php echo str_replace('[user]', $item['username'], $lang['status_logged_out']); ?> 
<?php elseif($item['action']==3): ?>
<?php echo str_replace('[base_url]', BASE_URL, str_replace('[table_title]', $item['table_title'] , str_replace('[table]', $item['table'], str_replace('[item]', $item['item'], str_replace('[user]', $item['username'], $lang['status_added']))))); ?> 
<?php elseif($item['action']==4): ?>
<?php echo str_replace('[base_url]', BASE_URL, str_replace('[table_title]', $item['table_title'] , str_replace('[table]', $item['table'], str_replace('[item]', $item['item'], str_replace('[user]', $item['username'], $lang['status_edited']))))); ?> 
<?php elseif($item['action']==5): ?>
<?php echo str_replace('[table_title]', $item['table_title'], str_replace('[user]', $item['username'], $lang['status_deleted'])); ?> 
<?php elseif($item['action']==6): ?>
<?php echo str_replace('[base_url]', BASE_URL, str_replace('[user]', $item['username'], str_replace('[item]', $item['item'], $lang['status_photo_added']))); ?> 
<?php elseif($item['action']==7): ?>
<?php echo str_replace('[base_url]', BASE_URL, str_replace('[user]', $item['username'], str_replace('[item]', $item['item'], $lang['status_photo_edited']))); ?> 
<?php elseif($item['action']==8): ?>
<?php echo str_replace('[user]', $item['username'], str_replace('[item]', $item['item'], $lang['status_photo_deleted'])); ?> 
<?php elseif($item['action']==0): ?>
<strong><?php echo $item['username']; ?></strong> 
<?php endif; ?>
<span class="time">(<?php if($item['minutes_ago']<2): ?>just now<?php elseif($item['minutes_ago']<60): ?><?php echo str_replace('[minutes]', $item['minutes_ago'], $lang['minutes_ago']); ?><?php elseif($item['minutes_ago']>=60&&$item['minutes_ago']<1440): ?><?php echo str_replace('[hours]', $item['hours_ago'], $lang['hours_ago']); ?><?php else: ?><?php echo $item['time']; ?><?php endif; ?>)</span>
<?php if($item['message']): ?><p class="message"><?php echo $item['message']; ?></p><?php endif; ?>
<!--</li>-->

</div>

<?php endforeach; ?>
<!--</ul>-->
<?php endif; ?>
<?php if($pagination): ?>
<ul class="pagination">
<?php if($pagination['previous']): ?><li><a href="<?php echo BASE_URL; ?>?r=dashboard&amp;p=<?php echo $pagination['previous']; ?>#activity" title="<?php echo $lang['previous_page_title']; ?>"><span class="glyphicon glyphicon-chevron-left"></span></a></li><?php endif; ?>
<?php foreach($pagination['items'] as $item): ?>
<?php if($item==0): ?><li><span>&hellip;</span></li><?php elseif($item==$pagination['current']): ?><li class="active"><span><?php echo $item; ?></span></li><?php else: ?><li><a href="<?php echo BASE_URL; ?>?r=dashboard&amp;p=<?php echo $item; ?>#activity"><?php echo $item; ?></a></li><?php endif; ?>
<?php endforeach; ?>
<?php if($pagination['next']): ?><li><a href="<?php echo BASE_URL; ?>?r=dashboard&amp;p=<?php echo $pagination['next']; ?>#activity" title="<?php echo $lang['next_page_title']; ?>"><span class="glyphicon glyphicon-chevron-right"></span></a></li><?php endif; ?>  
</ul>
<?php endif; ?>
</div>
</div>

</div>
