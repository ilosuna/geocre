<h1><?php echo $lang['users_and_groups_subtitle']; ?></h1>

<ul class="nav nav-tabs">
<li class="active"><a href="<?php echo BASE_URL; ?>?r=users" class="active"><?php echo $lang['users_subtitle']; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=groups"><?php echo $lang['groups_subtitle']; ?></a></a></li>
</ul>

<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=users"><?php echo $lang['users_subtitle']; ?></a></li>
<?php if(isset($user['id'])): ?>
<li><a href="<?php echo BASE_URL; ?>?r=users.details&amp;id=<?php echo $user['id']; ?>"><?php echo $lang['users_details_subtitle']; ?></a></li>
<li class="active"><?php echo $lang['users_edit_subtitle']; ?></li>
<?php else: ?>
<li class="active"><?php echo $lang['users_add_subtitle']; ?></li>
<?php endif; ?>
</ul>

<!--<?php if(isset($user['id'])): ?>
<h2><?php echo $lang['users_edit_subtitle']; ?></h1>
<?php else: ?>
<h2><?php echo $lang['users_add_subtitle']; ?></h1>
<?php endif; ?>-->

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<form class="form-horizontal" action="index.php" method="post">
<div>
<?php if(isset($user['id'])): ?>
<input type="hidden" name="id" value="<?php echo $user['id']; ?>" />
<input type="hidden" name="r" value="users.edit_submit" />
<?php else: ?>
<input type="hidden" name="r" value="users.add_submit" />
<?php endif; ?>


<div class="form-group">
<label class="col-md-2 control-label" for="name"><?php echo $lang['user_name_label']; ?></label>
<div class="col-md-6">
<input  id="name" class="form-control" type="text" name="name" value="<?php if(isset($user['name'])) echo $user['name']; ?>">
</div>
</div>

<div class="form-group">
<label class="col-md-2 control-label" for="real_name"><?php echo $lang['user_real_name_label']; ?></label>
<div class="col-md-6">
<input  id="real_name" class="form-control" type="text" name="real_name" value="<?php if(isset($user['real_name'])) echo $user['real_name']; ?>">
</div>
</div>

<div class="form-group">
<label class="col-md-2 control-label" for="email"><?php echo $lang['user_email_label']; ?></label>
<div class="col-md-6">
<input  id="email" class="form-control" type="text" name="email" value="<?php if(isset($user['email'])) echo $user['email']; ?>">
</div>
</div>

<?php if(isset($available_languages)): ?>
<div class="form-group">
<label class="col-md-2 control-label" for="language"><?php echo $lang['user_language_label']; ?></label>
<div class="col-md-6">
<select id="language" class="form-control" name="language" size="1">
<option value=""><?php echo $lang['user_default_language_label']; ?></option>
<?php foreach($available_languages as $language): ?>
<option value="<?php echo $language['identifier']; ?>"<?php if(isset($user['language']) && $user['language']==$language['identifier']): ?> selected="selected"<?php endif; ?>><?php echo $language['name']; ?></option>
<?php endforeach; ?>
</select>
</div>
</div>
<?php endif; ?>

<div class="form-group">
<label class="col-md-2 control-label" for="time_zone"><?php echo $lang['user_time_zone_label']; ?></label>
<div class="col-md-6">
<select id="time_zone" class="form-control" name="time_zone" size="1">
<option value=""><?php echo $lang['user_default_time_zone_label']; ?></option>
<?php foreach($available_time_zones as $time_zone): ?>
<option value="<?php echo $time_zone; ?>"<?php if(isset($user['time_zone']) && $user['time_zone']==$time_zone): ?> selected="selected"<?php endif; ?>><?php echo $time_zone; ?></option>
<?php endforeach; ?></select>
</div>
</div>

<?php if(isset($available_groups)): ?>
<div class="form-group">

<span class="col-md-2 control-label radio-label"><?php echo $lang['user_groups_label']; ?></span>
<div class="col-md-6">

<?php foreach($available_groups as $group): ?>
<div class="checkbox">
<label>
<input id="group-<?php echo $group['id']; ?>" type="checkbox" name="groups[]" value="<?php echo $group['id']; ?>"<?php if(isset($user['groups']) && is_array($user['groups']) && in_array($group['id'], $user['groups'])): ?> checked="checked"<?php endif; ?> /> <?php echo $group['name']; ?>
</label>
</div>
<?php endforeach; ?>

</div>
</div>
<?php endif; ?>

<div class="form-group">
<label class="col-md-2 control-label" for="pw"><?php echo $lang['user_pw_twice_label']; ?></label>
<div class="col-md-6">
<input  id="pw" class="form-control" type="password" name="pw" autocomplete="off"><br />
<input  id="pw_repeat" class="form-control" type="password" name="pw_repeat" autocomplete="off">
<span class="help-block"><?php if(isset($user['id'])): ?><?php echo $lang['user_edit_pw_description']; ?><?php else: ?><?php echo $lang['user_add_pw_description']; ?><?php endif; ?></span>
</div>
</div>

<?php if(empty($user['id'])): ?>
<div class="form-group">
<div class="col-lg-offset-2 col-lg-10">
<div class="checkbox">
<label>
<input id="notify_user" type="checkbox" name="notify_user" value="1"<?php if(isset($user['notify_user'])&&$user['notify_user']==1) { ?> checked="checked"<?php } ?> /> <?php echo $lang['user_notify_label']; ?>
</label>
</div>
</div>
</div>
<?php endif; ?>

<div class="form-group">
<div class="col-lg-offset-2 col-lg-10">
<button class="btn btn-primary btn-lg" type="submit"><span class="glyphicon glyphicon-save"></span> <?php echo $lang['save_submit']; ?></button>
</div>
</div>

</div>
</form>
