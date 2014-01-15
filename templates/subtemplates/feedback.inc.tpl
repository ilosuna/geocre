<h1><?php echo $lang['feedback_subtitle']; ?></h1>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<?php if(isset($feedback_sent)): ?>
<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <?php echo $lang['feedback_message_sent']; ?></div>
<?php else: ?>

<?php /*
<p><img class="teaser border" src="<?php echo BASE_URL; ?>images/content/feedback.jpg" alt="Feedback" width="200" height="133" />Klappt's nicht wie es soll? Stimmt etwas nicht? Hier könnt Ihr Euren Frust ablassen oder sonstige Bemerkungen und Rückmeldungen los werden. Ich nehm's nicht persönlich! ;-)</p>
*/ ?>

<form method="post" action="<?php echo BASE_URL; ?>" style="clear:both;">
<div>
<input type="hidden" name="r" value="feedback" />

<p><label for="feedback_message"><?php echo $lang['feedback_message_label']; ?></label><br />
<textarea id="feedback_message" class="form-control" name="feedback_message" cols="70" rows="15" maxlength="<?php echo $settings['feedback_message_maxlength']; ?>"><?php if(isset($feedback_message)): ?><?php echo $feedback_message; ?><?php endif; ?></textarea></p>
  
<p><button class="btn btn-primary btn-lg" type="submit"><?php echo $lang['feedback_submit_button']; ?></button></p>
</div>
</form>

<?php endif; ?>
