<div class="alert alert-danger">
<h1><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $lang['exception_subtitle']; ?></h1>
<p><?php echo $lang['exception_message']; ?></p>
</div>

<?php if($settings['display_errors']): ?>
<pre>
<?php echo $exception; ?>
</pre>
<?php endif; ?>
