<?php /*
<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=dashboard#admin"><?php echo $lang['dashboard_link']; ?></a></li>
<li class="active"><?php echo $lang['page_overview_subtitle']; ?></li>
</ul>
*/ ?>

<div class="row">
<div class="col-sm-10"><h1><?php echo $lang['page_overview_subtitle']; ?></h1></div>
<div class="col-sm-2"><a class="btn btn-success btn-top-right" href="<?php echo BASE_URL; ?>?r=page.add"><span class="glyphicon glyphicon-plus"></span> <?php echo $lang['page_add_link']; ?></a></div>
</div>

<?php if(isset($pages)): ?>

<div class="table-responsive">
<table class="table table-striped table-hover">
<thead>
<tr>
<th>Title</th>
<th>Parent</th>
<th>Status</th>
<th>Index</th>
<th>Project</th>
<th>&nbsp;</th>
</tr>
</thead>

<tbody class="js-sortable" data-sortable="<?php echo BASE_URL; ?>?r=page.reorder_pages">
<?php $i=1; foreach($pages as $page): ?>
<tr id="item_<?php echo $page['id']; ?>">
<td><a href="<?php echo BASE_URL.$page['identifier']; ?>"><strong><?php echo $page['title']; ?></strong></a></td>
<td><?php echo $page['parent_title']; ?></td>
<td><span class="label <?php if($page['status']==2): ?>label-success<?php elseif($page['status']==1): ?>label-primary<?php else: ?>label-default<?php endif; ?>"><?php echo $lang['page_status'][$page['status']]; ?></span></td>
<td><?php if($page['index']): ?><span class="active text-success" title="<?php echo $lang['yes']; ?>"><span class="glyphicon glyphicon-ok"></span></span><?php endif; ?></td>
<td><?php if($page['project']): ?><span class="active text-success" title="<?php echo $lang['yes']; ?>"><span class="glyphicon glyphicon-ok"></span></span><?php endif; ?></td>
<td class="options"><a class="btn btn-primary btn-xs" href="<?php echo BASE_URL; ?>?r=page.edit&amp;id=<?php echo $page['id']; ?>" title="<?php echo $lang['edit']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp; <a class="btn btn-danger btn-xs" href="<?php echo BASE_URL; ?>?r=page.delete&amp;id=<?php echo $page['id']; ?>" title="<?php echo $lang['delete']; ?>"><span class="glyphicon glyphicon-remove"></span></a>&nbsp; <span class="btn btn-success btn-xs sortable_handle" title="<?php echo $lang['drag_and_drop']; ?>"><span class="glyphicon glyphicon-sort"></span></td>
</tr>
<?php ++$i; endforeach; ?>
</tbody>

</table>
</div>

<?php else: ?>

<div class="alert alert-warning"><?php echo $lang['no_pages_available']; ?></div>

<?php endif; ?>
