<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL . $page['identifier']; ?>"><?php echo $page['title']; ?></a></li>
<?php if(isset($photo['id'])): ?>
<li class="active"><?php echo $lang['page_edit_photo_subtitle']; ?></li>
<?php else: ?>
<li class="active"><?php echo $lang['page_add_photo_subtitle']; ?></li>
<?php endif; ?>
</ul>

<form action="index.php" method="post"<?php if(empty($photo['id'])): ?> enctype="multipart/form-data"<?php endif; ?>>
<div>
<?php if(isset($photo['id'])): ?>
<input type="hidden" name="r" value="page.edit_photo_submit" />
<?php else: ?>
<input type="hidden" name="r" value="page.add_photo_submit" />
<?php endif; ?>

<?php if(isset($photo['id'])): ?>
<input type="hidden" name="id" value="<?php echo $photo['id']; ?>" />
<?php else: ?>
<input type="hidden" name="page_id" value="<?php echo $page['id']; ?>" />
<?php endif; ?>

<?php if(isset($photo['id'])): ?>
<p><img class="thumbnail" src="<?php echo PAGE_PHOTOS_URL.$photo['filename']; ?>" style="max-width:600px;"></p>
<?php else: ?>
<p><label for="photo"><strong><?php echo $lang['page_photo_label']; ?></strong></label><br />
<input type="file" name="photo" id="photo" /></p>
<?php endif; ?>

<div class="form-group">
<label for="title"><strong><?php echo $lang['page_photo_title_label']; ?></strong></label>
<input id="title" class="form-control" type="text" name="title" value="<?php if(isset($photo['title'])) echo $photo['title']; ?>"></p>
</div>

<div class="form-group">
<label for="description"><strong><?php echo $lang['page_photo_description_label']; ?></strong></label>
<input id="description" class="form-control" type="text" name="description" value="<?php if(isset($photo['description'])) echo $photo['description']; ?>"></p>
</div>

<div class="form-group">
<label for="author"><strong><?php echo $lang['page_photo_author_label']; ?></strong></label>
<input id="author" class="form-control" type="text" name="author" value="<?php if(isset($photo['author'])) echo $photo['author']; ?>"></p>
</div>

<?php if(empty($photo['id'])): ?>
<button class="btn btn-primary" type="submit"><?php echo $lang['page_upload_photo_submit']; ?></button>
<?php else: ?>
<button class="btn btn-primary" type="submit"><?php echo $lang['ok_submit']; ?></button>
<?php endif; ?>

</div>
</form>
