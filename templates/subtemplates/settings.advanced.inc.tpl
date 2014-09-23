<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=settings"><?php echo $lang['settings_title']; ?></a></li>
<li class="active"><?php echo $lang['settings_advanced_title']; ?></li>
</ul>

<h1><?php echo $lang['settings_advanced_title']; ?></h1>

<?php if(isset($success)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<form action="<?php echo BASE_URL; ?>" method="post">
<div>
<input type="hidden" name="r" value="settings.submit" />

<div class="table-responsive">
<table class="table table-striped table-hover">

<?php foreach($settings as $name=>$value): ?>
<tr>
<td class="key"><label for="<?php echo htmlspecialchars($name); ?>"><?php echo htmlspecialchars($name); ?></label></td>
<td class="value"><input id="<?php echo htmlspecialchars($name); ?>" class="form-control" type="text" name="<?php echo htmlspecialchars($name); ?>" value="<?php echo htmlspecialchars(stripslashes($value)); ?>"></td>
<td class="options"><a class="btn btn-danger btn-xs" href="<?php echo BASE_URL; ?>?r=settings.delete&amp;key=<?php echo htmlspecialchars($name); ?>" title="<?php echo $lang['delete']; ?>" data-delete-confirm="<?php echo rawurlencode($lang['delete_settings_var_msg']); ?>"><span class="glyphicon glyphicon-remove"></span></a></td>
</tr>
<?php endforeach; ?>

</table>
</div>

<p><button class="btn btn-primary btn-lg" type="submit"><span class="glyphicon glyphicon-save"></span> <?php echo $lang['save_submit']; ?></button></p>

</div>
</form>

<div class="panel panel-default" style="margin-top:50px;">
<div class="panel-heading">
<h3 class="panel-title"><?php echo $lang['settings_add_variable_title']; ?></h3>
</div>
<div class="panel-body">
<form class="form-inline" action="<?php echo BASE_URL; ?>" method="post">
<input type="hidden" name="r" value="settings.add" />
<div class="form-group">
<input type="hidden" name="mode" value="settings">
<input type="hidden" name="new_var_submitted" value="true">
<label class="sr-only" for="name"><?php echo $lang['settings_name_label']; ?></label>
<input type="text" class="form-control" id="name" name="name" placeholder="<?php echo $lang['settings_name_label']; ?>">
</div>
<div class="form-group">
<label class="sr-only" for="value"><?php echo $lang['settings_value_label']; ?></label>
<input type="text" class="form-control" id="value" name="value" placeholder="<?php echo $lang['settings_value_label']; ?>">
</div>
<button type="submit" class="btn btn-default"><?php echo $lang['ok_submit']; ?></button>
</form>
</div>
</div>
