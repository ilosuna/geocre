<h1><?php echo $lang['feedback_subtitle']; ?></h1>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<?php if(isset($feedback_sent)): ?>
<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <?php echo $lang['feedback_message_sent']; ?></div>
<?php else: ?>

<form method="post" action="<?php echo BASE_URL; ?>" style="clear:both;">
<div>
<input type="hidden" name="r" value="feedback" />

<div class="alert alert-info"><?php echo $lang['feedback_description']; ?></div>

<p><label for="feedback_message"><?php echo $lang['feedback_message_label']; ?></label><br />
<textarea id="feedback_message" class="form-control" name="feedback_message" cols="70" rows="15" maxlength="<?php echo $settings['feedback_message_maxlength']; ?>"><?php if(isset($feedback_message)): ?><?php echo $feedback_message; ?><?php endif; ?></textarea></p>
  
<p><button class="btn btn-primary btn-lg" type="submit" data-processing="<?php echo rawurlencode($lang['feedback_submit_button_processing']); ?>"><?php echo $lang['feedback_submit_button']; ?></button></p>
</div>
</form>

<?php endif; ?>
