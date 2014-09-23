<h1><?php echo $lang['users_and_groups_subtitle']; ?></h1>

<ul class="nav nav-tabs">
<li><a href="<?php echo BASE_URL; ?>?r=users" class="active"><?php echo $lang['users_subtitle']; ?></a></li>
<li class="active"><a href="<?php echo BASE_URL; ?>?r=groups"><?php echo $lang['groups_subtitle']; ?></a></li>
</ul>

<div class="row">
<div class="col-sm-12">
<a class="btn btn-success pull-right" href="<?php echo BASE_URL; ?>?r=groups.add"><span class="glyphicon glyphicon-plus"></span> <?php echo $lang['page_group_link']; ?></a>
</div>
</div>

<?php if(isset($groups)): ?>

<div class="table-responsive">
<table class="table table-striped table-hover">
<thead>
<tr>
<th><?php echo $lang['group_name_column_label']; ?></th>
<th><?php echo $lang['group_members_column_label']; ?></th>
<th>&nbsp;</th>
</tr>
</thead>

<tbody data-sortable="<?php echo BASE_URL; ?>?r=groups.reorder_groups">
<?php $i=1; foreach($groups as $group): ?>
<tr id="item_<?php echo $group['id']; ?>">
<td class="nowrap"><a href="<?php echo BASE_URL; ?>?r=groups.properties&amp;id=<?php echo $group['id']; ?>"><strong><?php echo $group['name']; ?></strong></a></td>
<td><?php if(isset($group['members'])): ?>
<a href="#group-<?php echo $group['id']; ?>" data-toggle="collapse"><?php echo $group['members_count']; ?> <span class="caret"></span></a>
<ul id="group-<?php echo $group['id']; ?>" class="members list-unstyled collapse">
<?php foreach($group['members'] as $member): ?>
<li><a href="<?php echo BASE_URL; ?>?r=users.details&id=<?php echo $member['id']; ?>"><?php echo $member['name']; ?></a></li>
<?php endforeach; ?>
</ul>
<?php else: ?>
0
<?php endif; ?></td>
<td class="options"><a class="btn btn-primary btn-xs" href="<?php echo BASE_URL; ?>?r=groups.edit&amp;id=<?php echo $group['id']; ?>" title="<?php echo $lang['edit']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
<a class="btn btn-danger btn-xs" href="<?php echo BASE_URL; ?>?r=groups.delete&amp;id=<?php echo $group['id']; ?>" title="<?php echo $lang['delete']; ?>" data-delete-confirm="<?php echo rawurlencode($lang['delete_group_message']); ?>"><span class="glyphicon glyphicon-remove"></span></a>
<span class="btn btn-success btn-xs sortable_handle" title="<?php echo $lang['drag_and_drop']; ?>"><span class="glyphicon glyphicon-sort"></span></tr>
<?php ++$i; endforeach; ?>
</tbody>

</table>
</div>

<?php else: ?>

<p><em><?php echo $lang['no_groups_available']; ?></em></p>
<ul class="optionmenu">
<li><a href="<?php echo BASE_URL; ?>?r=groups.add" class="add_group"><?php echo $lang['page_group_link']; ?></a></li>
</ul>

<?php endif; ?>
