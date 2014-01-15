<h1><?php echo $lang['users_and_groups_subtitle']; ?></h1>

<ul class="nav nav-tabs">
<li><a href="<?php echo BASE_URL; ?>?r=users" class="active"><?php echo $lang['users_subtitle']; ?></a></li>
<li class="active"><a href="<?php echo BASE_URL; ?>?r=groups"><?php echo $lang['groups_subtitle']; ?></a></li>
</ul>

<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=groups"><?php echo $lang['groups_subtitle']; ?></a></li>
<li class="active"><?php echo $group['name']; ?></li>
</ul>

<?php if(isset($success) || isset($failure)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<h2><?php echo $group['name']; ?></h2>

<?php if($group['description']): ?>
<p class="bottom-space">
<?php echo $group['description']; ?>
</p>
<?php endif; ?>


<ul id="myTab" class="nav nav-pills bottom-space-small">
<li class="active"><a href="#permissions" data-toggle="tab"><?php echo $lang['group_permissions_label']; ?></a></li>
<li><a href="#members" data-toggle="tab"><?php echo $lang['group_members_label']; ?></a></li></ul>
</ul>

<div id="myTabContent" class="tab-content">

<div id="permissions" class="tab-pane fade in active">

<?php if(isset($permissions)): ?>
<div class="table-responsive">
<table class="table table-striped table-hover">
<thead>
<tr>
<th><?php echo $lang['permission_type_column_label']; ?></th>
<th><?php echo $lang['permission_item_column_label']; ?></th>
<th><?php echo $lang['permission_level_column_label']; ?></th>
<th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php $i=1; foreach($permissions as $permission): ?>
<tr>
<td><?php if(isset($lang['group_permission_type'][$permission['type']])): ?><?php echo $lang['group_permission_type'][$permission['type']]; ?><?php else: ?><?php echo $permission['type']; ?><?php endif; ?></td>
<td><?php if($permission['type']==60 && $permission['item']==0): ?><?php echo $lang['group_permission_all_data_label']; ?><?php elseif($permission['type']==60 && isset($data[$permission['item']])): ?><?php echo $data[$permission['item']]['title']; ?><?php elseif($permission['item']>0): ?><?php echo $permission['item']; ?><?php endif; ?></td>
<td><?php if(isset($lang['group_permission_level'][$permission['type']][$permission['level']])): ?><?php echo $lang['group_permission_level'][$permission['type']][$permission['level']]; ?><?php elseif($permission['level']>0): ?><?php echo $permission['level']; ?><?php endif; ?></td>
<td class="options"><a class="btn btn-danger btn-xs" href="<?php echo BASE_URL; ?>?r=groups.delete_permission&amp;id=<?php echo $permission['id']; ?>" title="<?php echo $lang['delete']; ?>" data-delete-confirm="<?php echo rawurlencode($lang['delete_permission_message']); ?>"><span class="glyphicon glyphicon-remove"></span></a></td>
</tr>
<?php ++$i; endforeach; ?>
</tbody>
</table>
</div>
<?php else: ?>
<div class="alert alert-warning">
<?php echo $lang['no_permissions_message']; ?>
</div>
<?php endif; ?>


<div class="panel panel-default">
<div class="panel-heading">
<h4 class="panel-title">
<a class="collapse-handle" data-toggle="collapse" href="#page-image"><?php echo $lang['add_permission_link']; ?> <span class="caret"></span></a>
</h4>
</div>

<div id="page-image" class="panel-collapse collapse">
<div class="panel-body">

<form action="index.php" method="post">
<div>
<input type="hidden" name="r" value="groups.add_permission" />
<input type="hidden" name="group" value="<?php echo $group['id']; ?>" />

<ul id="add_permission" class="list-unstyled">

<li>

<label for="type_1" class="mainlabel">
<input id="type_1" type="radio" name="type" value="<?php echo Permission::ADMIN; ?>" />
<?php echo $lang['group_permission_type'][Permission::ADMIN]; ?>
</label>

<div class="permission_details alert alert-info">
<span class="description"><?php echo $lang['group_permission_description'][Permission::ADMIN]; ?></span>
<p><button class="btn btn-success" type="submit"><span class="glyphicon glyphicon-plus"></span> <?php echo $lang['group_add_permission_submit']; ?></button></p>
</div>
</li>

<li>
<label for="type_2" class="mainlabel"><input id="type_2" type="radio" name="type" value="<?php echo Permission::USERS_GROUPS; ?>" /> <span><?php echo $lang['group_permission_type'][Permission::USERS_GROUPS]; ?></span></label>
<div class="permission_details alert alert-info">
<span class="description"><?php echo $lang['group_permission_description'][Permission::USERS_GROUPS]; ?></span>
<p><button class="btn btn-success" type="submit"><span class="glyphicon glyphicon-plus"></span> <?php echo $lang['group_add_permission_submit']; ?></button></p>
</div>
</li>

<li>
<label for="type_3" class="mainlabel"><input id="type_3" type="radio" name="type" value="<?php echo Permission::PAGE_MANAGEMENT; ?>" /> <span><?php echo $lang['group_permission_type'][Permission::PAGE_MANAGEMENT]; ?></span></label>
<div class="permission_details alert alert-info">
<span class="description"><?php echo $lang['group_permission_description'][Permission::PAGE_MANAGEMENT]; ?></span>
<p><button class="btn btn-success" type="submit"><span class="glyphicon glyphicon-plus"></span> <?php echo $lang['group_add_permission_submit']; ?></button></p>
</div>
</li>

<li>
<label for="type_4" class="mainlabel"><input id="type_4" type="radio" name="type" value="<?php echo Permission::DATA_MANAGEMENT; ?>" /> <span><?php echo $lang['group_permission_type'][Permission::DATA_MANAGEMENT]; ?></span></label>
<div class="permission_details alert alert-info">
<span class="description"><?php echo $lang['group_permission_description'][Permission::DATA_MANAGEMENT]; ?></span>
<p><button class="btn btn-success" type="submit"><span class="glyphicon glyphicon-plus"></span> <?php echo $lang['group_add_permission_submit']; ?></button></p>
</div>
</li>

<li><label for="type_5" class="mainlabel"><input id="type_5" type="radio" name="type" value="<?php echo Permission::DATA_ACCESS; ?>" /> <span><?php echo $lang['group_permission_type'][Permission::DATA_ACCESS]; ?></span></label>
<div class="permission_details alert alert-info">
<span class="description"><?php echo $lang['group_permission_description'][Permission::DATA_ACCESS]; ?></span>
<select id="item" class="form-control" name="item" size="1">
<?php /*<option value="0"><?php echo $lang['group_permission_all_data_label']; ?></option>*/ ?>
<?php if(isset($data)): ?>
<?php foreach($data as $item): ?>
<option value="<?php echo $item['id']; ?>"><?php if($item['parent_table']): ?>- <?php endif; ?><?php echo $item['title']; ?></option>
<?php endforeach; ?>
<?php endif; ?>
</select>
<input id="level_60_0" type="radio" name="level" value="<?php echo Permission::READ; ?>" /> <label for="level_60_0"><?php echo $lang['group_permission_level'][Permission::DATA_ACCESS][Permission::READ]; ?></label><br />
<input id="level_60_1" type="radio" name="level" value="<?php echo Permission::WRITE; ?>" /> <label for="level_60_1"><?php echo $lang['group_permission_level'][Permission::DATA_ACCESS][Permission::WRITE]; ?></label><br />
<input id="level_60_2" type="radio" name="level" value="<?php echo Permission::MANAGE; ?>" /> <label for="level_60_2"><?php echo $lang['group_permission_level'][Permission::DATA_ACCESS][Permission::MANAGE]; ?></label>
<p><button class="btn btn-success" type="submit"><span class="glyphicon glyphicon-plus"></span> <?php echo $lang['group_add_permission_submit']; ?></button></p>

</div>

</li>
<li><label for="type_6" class="mainlabel"><input id="type_6" type="radio" name="type" value="<?php echo Permission::PHOTOS; ?>"> <span><?php echo $lang['group_permission_type'][Permission::PHOTOS]; ?></span></label>
<div class="permission_details alert alert-info">
<span class="description"><?php echo $lang['group_permission_description'][Permission::PHOTOS]; ?></span><br />

<input id="level_50_0" type="radio" name="level" value="<?php echo Permission::READ; ?>" /> <label for="level_50_0"><?php echo $lang['group_permission_level'][Permission::PHOTOS][Permission::READ]; ?></label><br />
<input id="level_50_1" type="radio" name="level" value="<?php echo Permission::WRITE; ?>" /> <label for="level_50_1"><?php echo $lang['group_permission_level'][Permission::PHOTOS][Permission::WRITE]; ?></label><br />
<input id="level_50_2" type="radio" name="level" value="<?php echo Permission::MANAGE; ?>" /> <label for="level_50_2"><?php echo $lang['group_permission_level'][Permission::PHOTOS][Permission::MANAGE]; ?></label>

<p><button class="btn btn-success" type="submit"><span class="glyphicon glyphicon-plus"></span> <?php echo $lang['group_add_permission_submit']; ?></button></p>

</div>
</li>

</ul>
</div>
</form>


</div>
</div>
</div>


</div>
<div id="members" class="tab-pane fade in">

<?php if(isset($members)): ?>
<div class="table-responsive">
<table class="table table-striped table-hover">
<thead>
<tr>
<th><?php echo $lang['user_name_column_label']; ?></th>
<?php /*<th><?php echo $lang['user_email_column_label']; ?></th>*/ ?>
<th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php foreach($members as $member): ?>
<tr>
<td><a href="<?php echo BASE_URL; ?>?r=users.details&id=<?php echo $member['id']; ?>"><strong><?php echo $member['name']; ?></strong></a></td>
<?php /*<td><a href="<?php echo $user['email']; ?>"><?php echo $user['email']; ?></a></td>*/ ?>
<td class="options"><a class="btn btn-danger btn-xs" href="<?php echo BASE_URL; ?>?r=groups.delete_membership&amp;id=<?php echo $member['membership_id']; ?>" title="<?php echo $lang['delete']; ?>" data-delete-confirm="<?php echo rawurlencode($lang['delete_user_from_group_message']); ?>"><span class="glyphicon glyphicon-remove"></span></a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php else: ?>
<div class="alert alert-warning">
<?php echo $lang['no_members_in_group_message']; ?>
</div>
<?php endif; ?>

<form action="index.php" method="post">
<div class="row">
<div class="col-md-4">
<input type="hidden" name="r" value="groups.add_membership" />
<input type="hidden" name="group" value="<?php echo $group['id']; ?>" />
<div class="input-group">
<input class="form-control" type="text" name="user" /> 
<span class="input-group-btn">
<button type="submit" class="btn btn-success" type="button"><span class="glyphicon glyphicon-plus"></span> <?php echo $lang['group_add_membership_submit']; ?></button>
</span>
</div>
</div>

</div>
</form>

</div>
</div>



<?php
$js[] = <<<EOT
$(function() {
$("#add_permission .permission_details").hide();
$("#add_permission .mainlabel").click(function(e) { $('#add_permission li').removeClass('checked');
                                                    $('.permission_details').hide();
                                                    $(this).parents('li').addClass('checked');
                                                    $(this).next('.permission_details').show();
                                                    $(this).next('.permission_details').find('input:radio[name=level]:first').prop('checked', true);
                                                    $(':radio').blur();
                                                  });
});
EOT;
?>
