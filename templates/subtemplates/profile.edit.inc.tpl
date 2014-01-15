<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=profile"><?php echo $lang['profile_subtitle']; ?></a></li>
<li class="active"><?php echo $lang['profile_edit_subtitle']; ?></li>
</ul>

<h1><?php echo $lang['profile_edit_subtitle']; ?></h1>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<form class="form-horizontal" action="<?php echo BASE_URL; ?>#profile" method="post">
<div>
<input type="hidden" name="r" value="profile.edit_submit" />

<fieldset>
<legend><?php echo $lang['profile_userdata_label']; ?></legend>

<div class="form-group">
<label class="col-md-2 control-label" for="name"><?php echo $lang['user_name_label']; ?></label>
<div class="col-md-8">
<input id="name" class="form-control" type="text" name="name" value="<?php echo $profile['name']; ?>" disabled>
</div>
</div>

<div class="form-group">
<label class="col-md-2 control-label" for="real_name"><?php echo $lang['user_real_name_label']; ?></label>
<div class="col-md-8">
<input id="real_name" class="form-control" type="text" name="real_name" value="<?php echo $profile['real_name']; ?>">
</div>
</div>

<div class="form-group">
<label class="col-md-2 control-label" for="email"><?php echo $lang['user_email_label']; ?></label>
<div class="col-md-8">
<input id="email" class="form-control" type="text" name="email" value="<?php echo $profile['email']; ?>">
</div>
</div>

<?php if(isset($available_languages)): ?>
<div class="form-group">
<label class="col-md-2 control-label" for="language"><?php echo $lang['user_language_label']; ?></label>
<div class="col-md-8">
<select id="language" class="form-control" name="language" size="1">
<option value=""><?php echo $lang['user_default_language_label']; ?></option>
<?php foreach($available_languages as $language): ?>
<option value="<?php echo $language['identifier']; ?>"<?php if(isset($profile['language']) && $profile['language']==$language['identifier']): ?> selected="selected"<?php endif; ?>><?php echo $language['name']; ?></option>
<?php endforeach; ?>
</select>
</div>
</div>
<?php endif; ?>

<div class="form-group">
<label class="col-md-2 control-label" for="time_zone"><?php echo $lang['user_time_zone_label']; ?></label>
<div class="col-md-8">
<select id="time_zone" class="form-control" name="time_zone" size="1">
<option value=""><?php echo $lang['user_default_time_zone_label']; ?></option>
<?php foreach($available_time_zones as $time_zone): ?>
<option value="<?php echo $time_zone; ?>"<?php if(isset($profile['time_zone']) && $profile['time_zone']==$time_zone): ?> selected="selected"<?php endif; ?>><?php echo $time_zone; ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

</fieldset>

<fieldset>
<legend><?php echo $lang['profile_password_label']; ?></legend>

<div class="form-group">
<label class="col-md-2 control-label" for="old_pw"><?php echo $lang['user_pw_old_label']; ?></label>
<div class="col-md-8">
<input id="old_pw" class="form-control" type="password" name="old_pw" autocomplete="off">
</div>
</div>

<div class="form-group">
<label class="col-md-2 control-label" for="new_pw"><?php echo $lang['user_pw_new_label']; ?></label>
<div class="col-md-8">
<input id="new_pw" class="form-control" type="password" name="new_pw" autocomplete="off">
</div>
</div>

<div class="form-group">
<label class="col-md-2 control-label" for="new_pw_repeat"><?php echo $lang['user_pw_new_repeat_label']; ?></label>
<div class="col-md-8">
<input id="new_pw_repeat" class="form-control" type="password" name="new_pw_repeat" autocomplete="off">
</div>
</div>


</fieldset>

<div class="form-group">
<div class="col-md-offset-2 col-md-8">
<button class="btn btn-primary btn-lg" type="submit"><span class="glyphicon glyphicon-save"></span> <?php echo $lang['save_submit']; ?></button>
</div>
</div>

</div>
</form>
