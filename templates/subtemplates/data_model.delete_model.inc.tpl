<h1 class="text-danger"><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $subtitle; ?></h1>

<?php if(isset($failure)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<h2><strong><?php echo $model['title']; ?></strong></h2>

<?php if(isset($model['relation'])): ?>
<div class="alert alert-danger">
<h3><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $lang['delete_data_model_rel_data_title']; ?></h3>
<p><?php echo $lang['delete_data_model_rel_data_desc']; ?></p>
<ul>
<?php foreach($model['relation'] as $item): ?>
<li><strong><?php echo $lang['delete_data_model_relation_item']; ?></strong> <?php echo $item['label']; ?> (<?php echo $item['name']; ?>)
 <ul>
 <?php foreach($item['realtion'] as $relation): ?>
 <li><strong><?php echo $lang['delete_data_model_related_model']; ?></strong> <?php echo $relation['title']; ?> (<?php echo $relation['table_name']; ?>)<br />
 <strong><?php echo $lang['delete_data_model_related_item']; ?></strong> <?php echo $relation['label']; ?> (<?php echo $relation['name']; ?>)</li>
 <?php endforeach; ?>
 </ul>
</li>
<?php endforeach; ?>
</ul>
</div>

<?php else: ?>

<?php if($model['table_exists']): ?>
<div class="alert alert-danger"><?php echo $lang['delete_table_info']; ?></div>
<?php endif; ?>

<?php if($model['images']): ?>
<div class="alert alert-danger"><?php echo $lang['delete_table_images_info']; ?></div>
<?php endif; ?>

<form action="index.php" method="post">
<div>
<input type="hidden" name="r" value="data_model.delete_model_submit" />
<input type="hidden" name="id" value="<?php echo $model['id']; ?>" />
<input type="hidden" name="confirmed" value="true">

<?php if($model['table_exists']): ?>
<div class="checkbox">
<label>
<input type="checkbox" name="keep_table" value="1"> <?php echo $lang['delete_keep_table_label']; ?>
</label>
</div>
<?php endif; ?>

<p><label for="pw"><strong><?php echo $lang['login_password']; ?></strong></label><br />
<input class="form-control form-control-default" id="pw" type="password" name="pw" size="30" autocomplete="off" autofocus></p>

<p><button class="btn btn-danger btn-lg" type="submit" name="confirmed"><span class="glyphicon glyphicon-remove"></span> <?php echo $lang['delete_submit']; ?></button></p>
</div>
</form>

<?php endif; ?>
