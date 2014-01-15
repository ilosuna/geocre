<h1><?php echo $lang['users_and_groups_subtitle']; ?></h1>

<ul class="nav nav-tabs">
<li><a href="<?php echo BASE_URL; ?>?r=users" class="active"><?php echo $lang['users_subtitle']; ?></a></li>
<li class="active"><a href="<?php echo BASE_URL; ?>?r=groups"><?php echo $lang['groups_subtitle']; ?></a></li>
</ul>

<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=groups"><?php echo $lang['groups_subtitle']; ?></a></li>
<li class="active">
<?php if(isset($group['id'])): ?>
<?php echo $lang['group_edit_subtitle']; ?>
<?php else: ?>
<?php echo $lang['group_add_subtitle']; ?>
<?php endif; ?>
</li>
</ul>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<form action="index.php" method="post">
<div>
<?php if(isset($group['id'])): ?>
<input type="hidden" name="r" value="groups.edit_submit" />
<input type="hidden" name="id" value="<?php echo $group['id']; ?>" />
<?php else: ?>
<input type="hidden" name="r" value="groups.add_submit" />
<?php endif; ?>

<div class="form-group">
<label for="name"><?php echo $lang['group_name_label']; ?></label>
<input id="name" class="form-control" type="text" name="name" value="<?php if(isset($group['name'])) echo $group['name']; ?>" size="35" />
</div>

<div class="form-group">
<label for="description"><?php echo $lang['group_description_label']; ?></label>
<textarea id="description" class="form-control" name="description" rows="5"><?php if(isset($group['description'])) echo $group['description']; ?></textarea>
</div>

<p><button class="btn btn-primary" type="submit"><?php echo $lang['save_submit']; ?></button></p>

</div>
</form>
