<h1><?php echo $lang['login_subtitle']; ?></h1>

<?php if(isset($errors) || isset($success)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>


<form id="login" action="<?php echo BASE_URL; ?>" method="post">
<fieldset>
<input type="hidden" name="r" value="login" />
<div class="form-group login-form">
<label for="email"><?php echo $lang['login_email']; ?></label>
<input id="email" type="text" name="email" class="form-control" value="<?php if(isset($email)): ?><?php echo $email; ?><?php endif; ?>" autofocus>
</div>
<div class="form-group login-form">
<label for="pw"><?php echo $lang['login_password']; ?></label>
<input id="pw" type="password" name="pw" class="form-control">
</div>
<input type="submit" class="btn btn-lg btn-primary" value="<?php echo $lang['login_submit']; ?>" />
</fieldset>
</form>

<ul class="list-unstyled login-options">
<li><a href="<?php echo BASE_URL; ?>?r=reset_pw"><?php echo $lang['reset_pw_link']; ?></a></li>
<?php if($settings['register_mode']==0): ?>
<li><a href="<?php echo BASE_URL; ?>?r=register"><?php echo $lang['register_link']; ?></a></li>
<?php endif; ?>
</ul>
