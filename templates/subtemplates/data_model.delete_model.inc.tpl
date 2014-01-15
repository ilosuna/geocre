<!--
<p><a class="back" href="<?php echo BASE_URL; ?>?r=dashboard#data"><?php echo $lang['back']; ?></a></p>
-->

<h1 class="text-danger"><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $subtitle; ?></h1>

<?php if(isset($failure)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<p><strong><?php echo $model['title']; ?></strong></p>

<form action="index.php" method="post">
<div>
<input type="hidden" name="r" value="data_model.delete_model_submit" />
<input type="hidden" name="id" value="<?php echo $model['id']; ?>" />
<input type="hidden" name="confirmed" value="true">

<div class="checkbox">
<label>
<input type="checkbox" name="delete_table" value="true"> <?php echo str_replace('[table]', $model['table'], str_replace('[records]', $model['records'], $lang['delete_table_label'])); ?>
</label>
</div>

<p><label for="pw"><strong><?php echo $lang['login_password']; ?></strong></label><br />
<input class="form-control form-control-default" id="pw" type="password" name="pw" size="30" autocomplete="off" autofocus></p>

<p><button class="btn btn-danger btn-lg" type="submit" name="confirmed"><span class="glyphicon glyphicon-remove"></span> <?php echo $lang['delete_submit']; ?></button></p>
</div>
</form>
