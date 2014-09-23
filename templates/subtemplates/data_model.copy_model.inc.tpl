<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=dashboard#data" title="<?php echo $lang['dashboard_title']; ?>"><?php echo $lang['dashboard_link']; ?></a></li>
<?php if(isset($db_table)): ?>
<li><a href="<?php echo BASE_URL; ?>?r=data&amp;data_id=<?php echo $db_table['id']; ?>"><?php echo $db_table['initial_model_title']; ?></a></li>
<?php endif; ?>
<li class="active">
<?php echo $lang['copy_data_model_title']; ?>
</li>
</ul>

<h1><?php echo $lang['copy_data_model_title']; ?></h1>

<div class="alert alert-info"><?php echo $lang['copy_data_model_description']; ?></div>

<?php if(isset($errors) || isset($success)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<form class="form-horizontal" action="index.php" method="post"><div>
<input type="hidden" name="r" value="data_model.copy_model_submit" />
<input type="hidden" name="id" value="<?php echo $db_table['id']; ?>" />

<div class="form-group">
<label for="table_name" class="col-sm-2 control-label"><?php echo $lang['db_table_name_input_label']; ?></label>
<div class="col-sm-8">
<input id="table_name" class="form-control" type="text" name="table_name" value="<?php if(isset($db_table['table_name'])) echo $db_table['table_name'];?>">
</div>
</div>

<div class="form-group">
<label for="title" class="col-sm-2 control-label"><?php echo $lang['db_table_title_input_label']; ?></label>
<div class="col-sm-8">
<input id="title" class="form-control" type="text" name="title" value="<?php if(isset($db_table['title'])) echo $db_table['title'];?>">
</div>
</div>

<div class="form-group">
<label for="description" class="col-md-2 control-label"><?php echo $lang['db_table_description_label']; ?></label>
<div class="col-md-6">
<textarea id="description" class="form-control" name="description" cols="60" rows="10"><?php if(isset($db_table['description'])): ?><?php echo $db_table['description']; ?><?php endif; ?></textarea>
</div>
</div>

<div class="form-group">
<div class="col-sm-offset-2 col-sm-8">
<div class="checkbox">
<label>
<input name="no_database_altering" type="checkbox"> <?php echo $lang['data_model_no_table_creation']; ?>
</label>
</div>
</div>
</div>

<div class="form-group">
<div class="col-sm-offset-2 col-sm-10">
<button type="submit" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-share-alt"></span> <?php echo $lang['copy_data_model_submit']; ?></button>
</div>
</div>

</div>
</form>
