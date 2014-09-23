<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=dashboard#data" title="<?php echo $lang['dashboard_title']; ?>"><?php echo $lang['dashboard_link']; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=data&amp;data_id=<?php echo $data_model['id']; ?><?php if(empty($item_id)): ?>#images<?php endif; ?>"><?php echo $data_model['title']; ?></a></li>

<?php if(isset($item_id)): ?>
<li><a href="<?php echo BASE_URL; ?>?r=data_item&amp;data_id=<?php echo $data_model['id']; ?>&amp;id=<?php echo $item_id; ?>#images"><?php echo $lang['data_item_details_title']; ?></a></li>
<?php endif; ?>

<li class="active">
<?php if(isset($image['id'])): ?>
<?php echo $lang['edit_data_image_title']; ?>
<?php else: ?>
<?php echo $lang['add_data_image_title']; ?>
<?php endif; ?>
</li>
</ul>

<h1>
<?php if(isset($image['id'])): ?>
<?php echo $lang['edit_data_image_title']; ?>
<?php else: ?>
<?php echo $lang['add_data_image_title']; ?>
<?php endif; ?>
</h1>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<form action="index.php" method="post"<?php if(empty($image['id'])): ?> enctype="multipart/form-data"<?php endif; ?>>
<div>
<?php if(isset($image['id'])): ?>
<input type="hidden" name="r" value="data_image.edit_submit" />
<?php else: ?>
<input type="hidden" name="r" value="data_image.add_submit" />
<?php endif; ?>

<?php if(isset($image['id'])): ?>
<input type="hidden" name="id" value="<?php echo $image['id']; ?>" />
<?php endif; ?>
<input type="hidden" name="data_id" value="<?php echo $data_model['id']; ?>" />

<?php if(isset($item_id)): ?>
<input type="hidden" name="item_id" value="<?php echo $item_id; ?>" />
<?php endif; ?>

<?php if(isset($image['id'])): ?>
<p><img class="thumbnail" src="<?php echo $image['image_url']; ?>" style="max-width:400px;max-height:300px;"></p>
<?php else: ?>
<p><label for="image"><?php echo $lang['data_image_label']; ?><sup><span class="glyphicon glyphicon-asterisk text-danger" title="<?php echo $lang['required_label']; ?>"></span></sup></label> 
<input type="file" name="image" id="image" /></p>
<?php endif; ?>

<div class="form-group">
<label for="title"><?php echo $lang['page_photo_title_label']; ?><sup><span class="glyphicon glyphicon-asterisk text-danger" title="<?php echo $lang['required_label']; ?>"></span></sup></label>
<input id="title" class="form-control" type="text" name="title" value="<?php if(isset($image['title'])) echo $image['title']; ?>">
</div>

<div class="form-group">
<label for="description"><strong><?php echo $lang['page_photo_description_label']; ?></strong></label>
<input id="description" class="form-control" type="text" name="description" value="<?php if(isset($image['description'])) echo $image['description']; ?>">
</div>

<div class="form-group">
<label for="author"><strong><?php echo $lang['page_photo_author_label']; ?></strong></label>
<input id="author" class="form-control" type="text" name="author" value="<?php if(isset($image['author'])) echo $image['author']; ?>">
</div>

<?php if(empty($image['id'])): ?>
<button class="btn btn-primary btn-lg" type="submit"><span class="glyphicon glyphicon-upload"></span> <?php echo $lang['add_data_image_submit']; ?></button>
<?php else: ?>
<button class="btn btn-primary btn-lg" type="submit"><?php echo $lang['ok_submit']; ?></button>
<?php endif; ?>

</div>
</form>
