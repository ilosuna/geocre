<div class="row">
<div class="col-sm-10"><h1><?php echo $lang['settings_title']; ?></h1></div>
<div class="col-sm-2"><a class="btn btn-default btn-top-right" href="<?php echo BASE_URL; ?>?r=settings.advanced" class="settings"><span class="glyphicon glyphicon-wrench"></span> <?php echo $lang['advanced_settings']; ?></a>
</div>
</div>

<?php if(isset($success)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<form class="form-horizontal" action="index.php" method="post">
<div>
<input type="hidden" name="r" value="settings.submit" />

<fieldset>
<legend><?php echo $lang['general_settings_heading']; ?></legend>

<div class="form-group">
<label class="col-md-2 control-label" for="website_title"><?php echo $lang['website_title_label']; ?></label>
<div class="col-md-8">
<input id="website_title" class="form-control" type="text" name="website_title" value="<?php echo htmlspecialchars($settings['website_title']); ?>">
<span class="help-block"><?php echo $lang['website_title_description']; ?></span>
</div>
</div>

</fieldset>

<fieldset>
<legend><?php echo $lang['email_settings_heading']; ?></legend>

<div class="form-group">
<label class="col-md-2 control-label" for="email_address"><?php echo $lang['email_address_label']; ?></label>
<div class="col-md-8">
<input id="email_address" class="form-control" type="text" name="email_address" value="<?php echo htmlspecialchars($settings['email_address']); ?>">
<span class="help-block"><?php echo $lang['email_address_description']; ?></span>
</div>
</div>

<div class="form-group">
<label class="col-md-2 control-label" for="email_smtp_host"><?php echo $lang['email_smtp_host_label']; ?></label>
<div class="col-md-8">
<input id="email_smtp_host" class="form-control" type="text" name="email_smtp_host" value="<?php echo htmlspecialchars($settings['email_smtp_host']); ?>">
<span class="help-block"><?php echo $lang['email_smtp_host_description']; ?></span>
</div>
</div>

<div class="form-group">
<label class="col-md-2 control-label" for="email_smtp_port"><?php echo $lang['email_smtp_port_label']; ?></label>
<div class="col-md-8">
<input id="email_smtp_port" class="form-control" type="text" name="email_smtp_port" value="<?php echo htmlspecialchars($settings['email_smtp_port']); ?>">
<span class="help-block"><?php echo $lang['email_smtp_port_description']; ?></span>
</div>
</div>

<div class="form-group">
<label class="col-md-2 control-label" for="email_smtp_username"><?php echo $lang['email_smtp_username_label']; ?></label>
<div class="col-md-8">
<input id="email_smtp_username" class="form-control" type="text" name="email_smtp_username" value="<?php echo htmlspecialchars($settings['email_smtp_username']); ?>">
<span class="help-block"><?php echo $lang['email_smtp_username_description']; ?></span>
</div>
</div>

<div class="form-group">
<label class="col-md-2 control-label" for="email_smtp_password"><?php echo $lang['email_smtp_password_label']; ?></label>
<div class="col-md-8">
<input id="email_smtp_password" class="form-control" type="password" name="email_smtp_password" value="<?php echo htmlspecialchars($settings['email_smtp_password']); ?>">
<span class="help-block"><?php echo $lang['email_smtp_password_description']; ?></span>
</div>
</div>

</fieldset>

<fieldset>
<legend><?php echo $lang['registration_settings_heading']; ?></legend>

<div class="form-group">
<span class="col-md-2 control-label radio-label"><?php echo $lang['register_mode_label']; ?></span>
<div class="col-md-8">
<div class="radio">
<label>
<input type="radio" id="register_mode_0" name="register_mode" value="0"<?php if($settings['register_mode']==0): ?> checked<?php endif; ?>>
<?php echo $lang['register_option_label'][0]; ?>
</label>
</div>
<div class="radio">
<label>
<input type="radio" id="register_mode_1" name="register_mode" value="1"<?php if($settings['register_mode']==1): ?> checked<?php endif; ?>>
<?php echo $lang['register_option_label'][1]; ?>
</label>
</div>
<span class="help-block"><?php echo $lang['register_mode_description']; ?></span>
</div>
</div>

<?php if(isset($available_groups)): ?>
<div class="form-group">
<label class="col-md-2 control-label" for="default_group"><?php echo $lang['default_group_label']; ?></label>
<div class="col-md-8">
<select id="default_group" class="form-control" name="default_group" size="1">
<option value="0"></option>
<?php foreach($available_groups as $group): ?>
<option value="<?php echo $group['id']; ?>"<?php if($group['id']==$settings['default_group']): ?> selected="selected"<?php endif; ?>><?php echo $group['name']; ?></option>
<?php endforeach; ?>
</select>
<span class="help-block"><?php echo $lang['default_group_description']; ?></span>
</div>
</div>
<?php endif; ?>

<div class="form-group">
<label class="col-md-2 control-label" for="register_code"><?php echo $lang['register_code_label']; ?></label>
<div class="col-md-8">
<input id="register_code" class="form-control" type="text" name="register_code" value="<?php echo htmlspecialchars($settings['register_code']); ?>">
<span class="help-block"><?php echo $lang['register_code_description']; ?></span>
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
