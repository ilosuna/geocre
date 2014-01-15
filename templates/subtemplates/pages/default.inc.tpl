<?php if($page['parent_identifier']): ?>
<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL.$page['parent_identifier']; ?>"><?php echo $page['parent_title']; ?></a></li>
<li class="active"><?php echo $page['title']; ?></li>
</ul>
<?php endif; ?>

<div class="row">
<div class="col-md-9 content content-sidebar">

<?php if($page['title_as_headline']): ?>
<h1><?php echo $page['title']; ?></h1>
<?php endif; ?>

<?php if(isset($page['page_image'])): ?>
<div class="media">
<img class="media-object thumbnail pull-left" src="<?php echo PAGE_IMAGES_URL.$page['page_image']['file']; ?>" width="<?php echo $page['page_image']['width']; ?>" height="<?php echo $page['page_image']['height']; ?>" alt="<?php if($page['page_image']['caption']): ?><?php echo $page['page_image']['caption']; ?><?php endif; ?>" />
<?php if($page['page_image']['caption']): ?><p class="caption"><?php echo $page['page_image']['caption']; ?></p><?php endif; ?>
<?php echo $page['content']; ?>
</div>
<?php else: ?>
<?php echo $page['content']; ?>
<?php endif; ?>

<?php if(isset($photos)): ?>

<div class="gallery-wrapper">
<div class="gallery">
<?php foreach($photos as $photo): ?>
<?php if($permission['page_management']): ?>
<span id="item_<?php echo $photo['id']; ?>" class="photooptions">
<?php endif; ?>
<a class="thumbnail" href="<?php echo PAGE_PHOTOS_URL.$photo['filename']; ?>" title="<?php echo $photo['title']; ?>" data-lightbox>
<img src="<?php echo PAGE_THUMBNAILS_URL.$photo['filename']; ?>" alt="<?php echo $photo['title']; ?>" title="<?php echo $photo['title']; ?>" data-description="<?php echo $photo['description']; ?>" data-author="<?php echo $photo['author']; ?>" width="<?php echo $photo['thumbnail_width']; ?>" height="<?php echo $photo['thumbnail_height']; ?>">
<span><?php echo truncate($photo['title'],20,true); ?></span></a>
<?php if($permission['page_management']): ?>
<a class="edit_button" href="<?php echo BASE_URL; ?>?r=page.edit_photo&amp;id=<?php echo $photo['id']; ?>" title="<?php echo $lang['edit']; ?>"><span class="glyphicon glyphicon-pencil"></span></a><a class="delete_button text-danger" href="<?php echo BASE_URL; ?>?r=page.delete_photo&amp;id=<?php echo $photo['id']; ?>" title="<?php echo $lang['delete']; ?>" data-delete-confirm="<?php echo rawurlencode($lang['delete_project_photo_message']); ?>"><span class="glyphicon glyphicon-remove"></span></a><span class="drag_button text-success" title="<?php echo $lang['drag_and_drop']; ?>"><span class="glyphicon glyphicon-move"></span></span></span>
<?php endif; ?>
<?php endforeach; ?>
</div>
</div>
<?php endif; ?>

</div>

<div class="col-md-3 sidebar">
<?php if($page['sidebar_title']): ?>
<div class="panel panel-default">
<div class="panel-heading">
<h3 class="panel-title"><?php echo $page['sidebar_title']; ?></h3>
</div>
<div class="panel-body">
<p><?php echo $page['sidebar_text']; ?><?php if($page['sidebar_link']): ?><br />
<a href="<?php echo $page['sidebar_link']; ?>"><?php echo $page['sidebar_linktext']; ?></a><?php endif; ?></p>
</div>
</div>
<?php endif; ?>

<?php if($page['page_info_title']): ?>
<div class="panel panel-default">
<div class="panel-heading">
<h3 class="panel-title"><?php echo $page['page_info_title']; ?></h3>
</div>
<div class="panel-body">
<ul class="list-unstyled">
<?php if($page['location']): ?><li><strong><?php echo $lang['page_location_label']; ?></strong> <?php echo $page['location']; ?></li><?php endif; ?>
<?php if($page['custom_date']): ?><li><strong><?php echo $lang['page_custom_date_label']; ?></strong> <?php echo $page['custom_date']; ?></li><?php endif; ?>
<?php if($page['contact_name']): ?><li><strong><?php echo $lang['page_contact_name_label']; ?></strong> <?php if($page['contact_email']): ?><?php echo js_encode_mail($page['contact_email'], $page['contact_name']); ?><?php else: ?><?php echo $page['contact_name']; ?><?php endif; ?></li><?php endif; ?>
</ul>
</div>
</div>
<?php endif; ?>

<?php if(isset($sub_pages)): ?>
<div class="panel panel-default">
<div class="panel-heading">
<h3 class="panel-title"><?php echo $lang['related_pages_label']; ?></h3>
</div>
<div class="panel-body">
<ul class="list-unstyled">
<?php foreach($sub_pages as $sub_page): ?>
<li><a href="<?php echo BASE_URL . $sub_page['identifier'] ?>"><?php echo truncate($sub_page['title'], 30, true); ?></a></li>
<?php endforeach; ?>
</ul>
</div>
</div>
<?php endif; ?>

<?php if(isset($data)): ?>
<div class="panel panel-default">
<div class="panel-heading">
<h3 class="panel-title"><?php echo $lang['project_data_label']; ?></h3>
</div>
<div class="panel-body">
<ul class="list-unstyled">
<?php foreach($data as $data_item): ?>
<li><a href="<?php echo BASE_URL; ?>?r=data&amp;data_id=<?php echo $data_item['id']; ?>"><?php echo truncate($data_item['title'], 30, true); ?></a></li>
<?php endforeach; ?>
</ul>
</div>
</div>
<?php endif; ?>

<?php if($permission['page_management']): ?>
<div class="panel panel-default">
<div class="panel-heading">
<h3 class="panel-title"><?php echo $lang['page_admin_label']; ?></h3>
</div>
<div class="panel-body">
<ul class="optionmenu">
<li><a class="edit" href="<?php echo BASE_URL; ?>?r=page.edit&amp;id=<?php echo $page['id']; ?>"><span class="glyphicon glyphicon-pencil"></span><?php echo $lang['page_edit_link']; ?></a></li>
<li><a class="add_photo" href="<?php echo BASE_URL; ?>?r=page.add_photo&amp;page_id=<?php echo $page['id']; ?>"><span class="glyphicon glyphicon-picture"></span><?php echo $lang['page_add_photo_link']; ?></a></li>
<li><a class="delete" href="<?php echo BASE_URL; ?>?r=page.delete&amp;id=<?php echo $page['id']; ?>"><span class="glyphicon glyphicon-remove text-danger"></span><?php echo $lang['page_delete_link']; ?></a></li>
</ul>
</div>
</div>
<?php endif; ?>
</div>

</div>
