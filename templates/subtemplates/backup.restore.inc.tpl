<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=backup"><?php echo $lang['backup_title']; ?></a></li>
<li class="active"><?php if($backup['type']=='database'): ?><?php echo $lang['backup_db_restore_title']; ?><?php else: ?><?php echo $lang['backup_files_restore_title']; ?><?php endif; ?></li>
</ul>

<?php if($backup['type']=='database'): ?>

<h1 class="text-danger"><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $lang['backup_db_restore_title']; ?></h1>

<?php else: ?>

<h1 class="text-danger"><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $lang['backup_files_restore_title']; ?></h1>

<?php endif; ?>


<?php if(isset($failure)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<?php if($backup['type']=='database'): ?>

<p><?php echo $lang['backup_db_restore_message']; ?></p>

<?php else: ?>

<p><?php echo $lang['backup_files_restore_message']; ?></p>

<?php endif; ?>

<div class="well">
<table class="items">
<tr>
<td><strong><?php echo $lang['backup_filename_column_header']; ?>:</strong></td>
<td><?php echo $backup['file']; ?></td>
</tr>
<tr>
<td><strong><?php echo $lang['backup_type_column_header']; ?>:</strong></td>
<td><?php if($backup['type']=='database'): ?><span class="glyphicon glyphicon-list-alt"></span> <?php echo $lang['backup_type_db_label']; ?><?php else: ?><span class="glyphicon glyphicon-file"></span> <?php echo $lang['backup_type_files_label']; ?><?php endif; ?></td>
</tr>
<tr>
<td><strong><?php echo $lang['backup_date_column_header']; ?>:</strong></td>
<td><?php echo $backup['date']; ?></td>
</tr>
<tr>
<td><strong><?php echo $lang['backup_size_column_header']; ?>:</strong></td>
<td><?php echo $backup['size']; ?></td>
<tr>
</table>
</div>

<form action="index.php" method="post">
<div>
<input type="hidden" name="r" value="backup.restore_submit" />
<input type="hidden" name="file" value="<?php echo $backup['file']; ?>" />
<input type="hidden" name="confirmed" value="true" />

<p><label for="pw"><strong><?php echo $lang['login_password']; ?></strong></label><br />
<input class="form-control form-control-default" id="pw" type="password" name="pw" size="30" autocomplete="off" autofocus></p>


<button class="btn btn-success btn-lg" type="submit" data-processing="<?php echo rawurlencode($lang['processing_message']); ?>"><span class="glyphicon glyphicon-repeat"></span> <?php echo $lang['backup_restore_submit']; ?></button>
</div>
</form>
