<h1><?php echo $lang['reset_pw_subtitle']; ?></h1>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<?php if(isset($reset_pw_link_sent)): ?>

<div class="alert alert-success">
<span class="glyphicon glyphicon-ok"></span> <?php echo $lang['reset_pw_link_sent_message']; ?>
</div>

<?php elseif(isset($reset_pw_form)): ?>

<p><?php echo $lang['reset_pw_new_message']; ?></p>

<form action="<?php echo BASE_URL; ?>" method="post">
<fieldset>

<input type="hidden" name="r" value="reset_pw.reset_submit" />
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<input type="hidden" name="code" value="<?php echo $code; ?>" />

<div class="form-group login-form">
<label for="new_pw"><?php echo $lang['user_pw_twice_label']; ?></label>
<input id="new_pw" class="form-control" type="password" name="new_pw" autocomplete="off" autofocus>
<input id="new_pw_repeat" class="form-control" type="password" name="new_pw_repeat" autocomplete="off" style="margin-top:10px;">
</div>

<p><input class="btn btn-primary" type="submit" value="<?php echo $lang['ok_submit']; ?>" /></p>

<fieldset>
</form>

<?php elseif(isset($code_invalid)): ?>
<div class="alert alert-danger">
<?php echo $lang['reset_pw_code_invalid_msg']; ?>
</div>

<?php else: ?>

<?php
if(isset($lang['captcha_number'][$captcha['number_1']])) $captcha['number_1'] = $lang['captcha_number'][$captcha['number_1']];
if(isset($lang['captcha_number'][$captcha['number_2']])) $captcha['number_2'] = $lang['captcha_number'][$captcha['number_2']];
?>

<p><?php echo $lang['reset_pw_description']; ?></p>

<form action="<?php echo BASE_URL; ?>" method="post">
<div>
<input type="hidden" name="r" value="reset_pw.email_submit" />

<div class="form-group">
<label for="email"><?php echo $lang['login_email']; ?></label>
<input id="email" class="form-control form-control-default" type="text" name="email" value="<?php if(isset($email)) echo $email; ?>">
</div>

<div class="form-group">
<label for="check"><?php echo str_replace('[number_2]', $captcha['number_2'], str_replace('[number_1]', $captcha['number_1'], $lang['captcha_question'])); ?></label>
<input id="check" class="form-control form-control-default" type="text" name="check">
</div>

<p><button class="btn btn-primary" type="submit"><?php echo $lang['ok_submit']; ?></button></p>

</div>
</form>

<?php endif; ?>
