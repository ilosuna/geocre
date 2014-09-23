<h1><?php echo $lang['register_title']; ?></h1>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<form class="form-horizontal" action="<?php echo BASE_URL; ?>" method="post">
<div>
<input type="hidden" name="r" value="register.submit" />

<div class="form-group<?php if(isset($error_fields) && in_array('name', $error_fields)): ?> has-error<?php endif; ?>">
<label for="name" class="col-md-2 control-label"><?php echo $lang['user_name_label']; ?></label>
<div class="col-md-8">
<input id="name" class="form-control" type="text" name="name" value="<?php if(isset($data['name'])) echo $data['name']; ?>">
</div>
</div>

<div class="form-group<?php if(isset($error_fields) && in_array('email', $error_fields)): ?> has-error<?php endif; ?>">
<label for="email" class="col-md-2 control-label"><?php echo $lang['user_email_label']; ?></label>
<div class="col-md-8">
<input id="email" class="form-control" type="text" name="email" value="<?php if(isset($data['email'])) echo $data['email']; ?>">
</div>
</div>

<div class="form-group<?php if(isset($error_fields) && in_array('pw', $error_fields)): ?> has-error<?php endif; ?>">
<label for="pw" class="col-md-2 control-label"><?php echo $lang['user_pw_twice_label']; ?></label>
<div class="col-md-8">
<input id="pw" class="form-control" type="password" name="pw" autocomplete="off"><br />
<input id="pw_repeat" class="form-control" type="password" name="pw_repeat" autocomplete="off">
</div>
</div>

<?php if($settings['register_code']): ?>
<div class="form-group<?php if(isset($error_fields) && in_array('register_code', $error_fields)): ?> has-error<?php endif; ?>">
<label for="register_code" class="col-md-2 control-label"><?php echo $lang['user_register_code_label']; ?></label>
<div class="col-md-8">
<input id="register_code" class="form-control" type="text" name="register_code" value="<?php if(isset($data['register_code'])) echo $data['register_code']; ?>">
</div>
</div>
<?php endif; ?>

<div class="form-group">
<div class="col-md-offset-2 col-md-8">
<button class="btn btn-primary" type="submit"><?php echo $lang['proceed_submit']; ?></button>
</div>
</div>

</div>
</form>
