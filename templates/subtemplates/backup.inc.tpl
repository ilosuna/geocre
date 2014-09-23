<div class="row">
<div class="col-sm-6"><h1><?php echo $lang['backup_title']; ?></h1></div>
<div class="col-sm-6">

<div class="btn-top-right">
<a class="btn btn-primary" href="<?php echo BASE_URL; ?>?r=backup.create_db_backup" data-processing="<?php echo rawurlencode($lang['processing_message']); ?>"><span class="glyphicon glyphicon-list-alt"></span> <?php echo $lang['backup_db_create_link']; ?></a>
<a class="btn btn-primary" href="<?php echo BASE_URL; ?>?r=backup.create_file_backup" data-processing="<?php echo rawurlencode($lang['processing_message']); ?>"><span class="glyphicon glyphicon-file"></span> <?php echo $lang['backup_files_create_link']; ?></a>
</div>

</div>
</div>

<?php if(isset($success) || isset($failure)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>


<?php if(isset($backup_files)): ?>
<div class="table-responsive">
<table class="table table-striped table-hover">
<thead>
<th><?php echo $lang['backup_filename_column_header']; ?></th>
<th><?php echo $lang['backup_type_column_header']; ?></th>
<th><?php echo $lang['backup_date_column_header']; ?></th>
<th><?php echo $lang['backup_size_column_header']; ?></th>
<th></th>
</thread>
<tbody>
<?php foreach($backup_files as $backup_file): ?>
<tr>
<td><?php echo $backup_file['name']; ?></td>
<td><?php if($backup_file['type']=='database'): ?><span class="glyphicon glyphicon-list-alt"></span> <?php echo $lang['backup_type_db_label']; ?><?php else: ?><span class="glyphicon glyphicon-file"></span> <?php echo $lang['backup_type_files_label']; ?><?php endif; ?></td>
<td><?php echo $backup_file['date']; ?></td>
<td><?php echo $backup_file['size']; ?></td>
<td class="options">

<a class="btn btn-primary btn-xs" href="<?php echo BASE_URL; ?>?r=backup.download&amp;file=<?php echo $backup_file['name']; ?>" title="<?php echo $lang['backup_download_link']; ?>"><span class="glyphicon glyphicon-cloud-download"></span></a>
<a class="btn btn-success btn-xs" href="<?php echo BASE_URL; ?>?r=backup.restore&amp;file=<?php echo $backup_file['name']; ?>" title="<?php echo $lang['backup_restore_link']; ?>"><span class="glyphicon glyphicon-repeat"></span></a>
<a class="btn btn-danger btn-xs" href="<?php echo BASE_URL; ?>?r=backup.delete&amp;file=<?php echo $backup_file['name']; ?>" title="<?php echo $lang['delete']; ?>" data-delete-confirm="<?php echo rawurlencode($lang['backup_delete_message']); ?>"><span class="glyphicon glyphicon-remove"></span></a>

</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php else: ?>

<div class="alert alert-warning"><?php echo $lang['no_backup_files']; ?></div>

<?php endif; ?>
