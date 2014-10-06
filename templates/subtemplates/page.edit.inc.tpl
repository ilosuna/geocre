<ol class="breadcrumb">
<?php if(isset($page['id'])): ?>
<li><a href="<?php echo BASE_URL . $page['identifier']; ?>"><?php echo $page['title']; ?></a></li>
<li class="active"><?php echo $lang['page_edit_subtitle']; ?></li>
<?php else: ?>
<li><a href="<?php echo BASE_URL; ?>?r=dashboard#admin"><?php echo $lang['dashboard_link']; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=page.overview"><?php echo $lang['page_overview_subtitle']; ?></a></li>
<li class="active"><?php echo $lang['page_add_subtitle']; ?></li>
<?php endif; ?>
</ol>

<?php if(isset($page['id'])): ?>
<h1><?php echo $lang['page_edit_subtitle']; ?></h1>
<?php else: ?>
<h1><?php echo $lang['page_add_subtitle']; ?></h1>
<?php endif; ?>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<form action="index.php" method="post" enctype="multipart/form-data">
<div>
<input type="hidden" name="r" value="page.edit_submit" />
<?php if(isset($page['id'])): ?>
<input type="hidden" name="id" value="<?php echo $page['id']; ?>" />
<?php endif; ?>


<ul id="myTab" class="nav nav-tabs">
<li class="active"><a href="#tab-content" data-toggle="tab"><?php echo $lang['page_content_tab_label']; ?></a></li>
<li><a href="#tab-sidebar" data-toggle="tab"><?php echo $lang['page_sidebar_tab_label']; ?></a></li>
<li><a href="#tab-teaser" data-toggle="tab"><?php echo $lang['page_teaser_tab_label']; ?></a></li>
<li><a href="#tab-properties" data-toggle="tab"><?php echo $lang['page_properties_tab_label']; ?></a></li>
</ul>

<div id="myTabContent" class="tab-content">

<div class="tab-pane fade in active" id="tab-content">

<div class="form-group">
<label for="title"><?php echo $lang['page_title_label']; ?></label>
<div class="input-group">
<input id="title" class="form-control" type="text" name="title" value="<?php if(isset($page['title'])) echo $page['title']; ?>">
<span class="input-group-addon">
<label>
<input id="title_as_headline" type="checkbox" name="title_as_headline" value="1"<?php if(isset($page['title_as_headline'])&&$page['title_as_headline']==true): ?> checked="checked"<?php endif; ?>> <?php echo $lang['page_title_as_headline_label']; ?>
</label>
</span>
</div>
</div>

<div class="form-group">
<label for="content"><strong><?php echo $lang['page_content_label']; ?></strong></label>

<?php if($wysiwyg): ?>
<a class="btn btn-default btn-xs active pull-right" href="<?php echo BASE_URL; ?><?php if(isset($page['id'])): ?>?r=page.edit&id=<?php echo $page['id']; ?><?php else: ?>?r=page.add<?php endif; ?>&disable_wysiwyg=true" title="<?php echo $lang['wysiwyg_disable_title']; ?>" data-confirm="<?php echo rawurlencode($lang['wysiwyg_toogle_message']); ?>"><?php echo $lang['wysiwyg_label']; ?></a>
<?php else: ?>
<a class="btn btn-default btn-xs pull-right" href="<?php echo BASE_URL; ?><?php if(isset($page['id'])): ?>?r=page.edit&id=<?php echo $page['id']; ?><?php else: ?>?r=page.add<?php endif; ?>" title="<?php echo $lang['wysiwyg_enable_title']; ?>" data-confirm="<?php echo rawurlencode($lang['wysiwyg_toogle_message']); ?>"><?php echo $lang['wysiwyg_label']; ?></a>
<?php endif; ?>

<textarea id="content" class="form-control wysiwyg" name="content" rows="20"><?php if(isset($page['content'])) echo $page['content']; ?></textarea>
</div>

<div class="panel panel-default">
<div class="panel-heading">
<h4 class="panel-title">
<a data-toggle="collapse" href="#page-image"><?php echo $lang['page_image_label']; ?> <span class="caret"></span></a>
</h4>
</div>

<div id="page-image" class="panel-collapse collapse">
<div class="panel-body">
<?php if(isset($page['page_image'])): ?>
<div>
<img class="thumbnail" src="<?php echo PAGE_IMAGES_URL.$page['page_image']; ?>" alt="Page image" class="page_image_edit"/>
</div>
<div class="checkbox">
<input type="checkbox" name="delete_page_image" id="delete_page_image" value="1" /> <label for="delete_page_image"><?php echo $lang['page_delete_image']; ?></label>
</div>
<?php endif; ?>

<p><label for="page_image"><strong><?php if(isset($page['page_image'])): ?><?php echo $lang['page_replace_image_label']; ?><?php else: ?><?php echo $lang['page_add_image_label']; ?><?php endif; ?></strong></label><br />
<input type="file" name="page_image" id="page_image" /></p>
<!--<p><label for="page_image_caption"><strong><?php echo $lang['page_image_caption_label']; ?></strong></label><br />
<input id="page_image_caption" type="text" name="page_image_caption" value="<?php if(isset($page['page_image_caption'])) echo $page['page_image_caption']; ?>" size="50" /></p>-->
</div>
</div>
</div>

</div>

<div class="tab-pane fade in" id="tab-sidebar">

<div class="form-group">
<label for="sidebar_title"><strong><?php echo $lang['page_sidebar_title_label']; ?></strong></label>
<input id="sidebar_title" class="form-control" type="text" name="sidebar_title" value="<?php if(isset($page['sidebar_title'])) echo $page['sidebar_title']; ?>">
</div>

<div class="form-group">
<label for="sidebar_text"><strong><?php echo $lang['page_sidebar_text_label']; ?></strong></label>
<textarea id="sidebar_text" class="form-control" name="sidebar_text" cols="100" rows="5" class="defaultinput"><?php if(isset($page['sidebar_text'])) echo $page['sidebar_text']; ?></textarea>
</div>

<div class="form-group">
<label for="sidebar_link"><strong><?php echo $lang['page_sidebar_link_label']; ?></strong></label>
<input id="sidebar_link" class="form-control" type="text" name="sidebar_link" value="<?php if(isset($page['sidebar_link'])) echo $page['sidebar_link']; ?>">
</div>

<div class="form-group">
<label for="sidebar_linktext"><strong><?php echo $lang['page_sidebar_linktext_label']; ?></strong></label>
<input id="sidebar_linktext" class="form-control" type="text" name="sidebar_linktext" value="<?php if(isset($page['sidebar_link'])) echo $page['sidebar_linktext']; ?>">
</div>

<div class="form-group">
<label for="page_info_title"><strong><?php echo $lang['page_info_title_label']; ?></strong></label>
<input id="page_info_title" class="form-control" type="text" name="page_info_title" value="<?php if(isset($page['page_info_title'])) echo $page['page_info_title']; ?>">
</div>

<div class="form-group">
<label for="location"><strong><?php echo $lang['page_location_label']; ?></strong></label>
<input id="location" class="form-control" type="text" name="location" value="<?php if(isset($page['location'])) echo $page['location']; ?>">
</div>

<div class="form-group">
<label for="custom_date"><strong><?php echo $lang['page_custom_date_label']; ?></strong></label>
<input id="custom_date" class="form-control" type="text" name="custom_date" value="<?php if(isset($page['custom_date'])) echo $page['custom_date']; ?>">
</div>

<div class="form-group">
<label for="contact_name"><strong><?php echo $lang['page_contact_name_label']; ?></strong></label>
<input id="contact_name" class="form-control" type="text" name="contact_name" value="<?php if(isset($page['contact_name'])) echo $page['contact_name']; ?>">
</div>

<div class="form-group">
<label for="contact_email"><strong><?php echo $lang['page_contact_email_label']; ?></strong></label>
<input id="contact_email" class="form-control" type="text" name="contact_email" value="<?php if(isset($page['contact_email'])) echo $page['contact_email']; ?>">
</div>

</div>

<div class="tab-pane fade in" id="tab-teaser">

<div class="form-group">
<label for="teaser_supertitle"><strong><?php echo $lang['page_teaser_supertitle_label']; ?></strong></label>
<input id="teaser_supertitle" class="form-control" type="text" name="teaser_supertitle" value="<?php if(isset($page['teaser_supertitle'])) echo $page['teaser_supertitle']; ?>">
</div>

<div class="form-group">
<label for="teaser_title"><strong><?php echo $lang['page_teaser_title_label']; ?></strong></label>
<input id="teaser_title" class="form-control" type="text" name="teaser_title" value="<?php if(isset($page['teaser_title'])) echo $page['teaser_title']; ?>">
</div>

<div class="form-group">
<label for="teaser_text"><strong><?php echo $lang['page_teaser_text_label']; ?></strong></label>
<textarea id="teaser_text" class="form-control" name="teaser_text" cols="100" rows="6" class="defaultinput"><?php if(isset($page['teaser_text'])) echo $page['teaser_text']; ?></textarea>
</div>

<div class="form-group">
<label for="teaser_linktext"><strong><?php echo $lang['page_teaser_linktext_label']; ?></strong></label>
<input id="teaser_linktext" class="form-control" type="text" name="teaser_linktext" value="<?php if(isset($page['teaser_linktext'])) echo $page['teaser_linktext']; ?>">
</div>

<?php if(isset($page['teaser_image'])): ?>
<div class="form-group">
<strong><?php echo $lang['page_teaser_image_label']; ?></strong><br />
<img class="thumbnail" src="<?php echo PAGE_TEASER_IMAGES_URL.$page['teaser_image']; ?>" alt="Teaser" class="teaser_image_edit"/>
<div class="checkbox">
<input id="delete_teaser_image" type="checkbox" name="delete_teaser_image" value="1" /> <label for="delete_teaser_image"><?php echo $lang['page_delete_teaser_image']; ?></label>
</div>
</div>
<?php endif; ?>

<div class="form-group">
<label for="teaser_image"><strong><?php if(isset($page['teaser_image'])): ?><?php echo $lang['page_replace_teaser_image_label']; ?><?php else: ?><?php echo $lang['page_add_teaser_image_label']; ?><?php endif; ?></strong></label>
<input type="file" name="teaser_image" id="teaser_image" />
</div>
</div>

<div class="tab-pane fade in" id="tab-properties">

<div class="form-group">
<label for="identifier"><?php echo $lang['page_identifier_label']; ?></label>
<input id="identifier" class="form-control" type="text" name="identifier" value="<?php if(isset($page['identifier'])) echo $page['identifier']; ?>" size="50" />
</div>

<?php if(isset($parent_pages)): ?>
<div class="form-group">
<label for="parent"><?php echo $lang['page_parent_label']; ?></label>
<select id="parent" class="form-control" name="parent" size="1">
<option value=""> </option>
<?php foreach($parent_pages as $parent_page): ?>
<option value="<?php echo $parent_page['id']; ?>"<?php if(isset($page['parent']) && $parent_page['id']==$page['parent']): ?> selected="selected"<?php endif; ?>><?php echo $parent_page['title']; ?></option>
<?php endforeach; ?>
</select>
</div>
<?php endif; ?>

<div class="form-group">
<label for="subtemplate"><?php echo $lang['page_subtemplate_label']; ?></label>
<select id="subtemplate" class="form-control" name="subtemplate" size="1">
<?php if(isset($available_subtemplates)): ?>
<?php foreach($available_subtemplates as $available_subtemplate): ?>
<option value="<?php echo $available_subtemplate; ?>"<?php if(isset($page['subtemplate']) && $page['subtemplate']==$available_subtemplate): ?> selected<?php endif; ?>><?php echo $available_subtemplate; ?></option>
<?php endforeach; ?> 
</select>
<?php endif; ?>
</div>

<div class="form-group">
<label for="menu"><?php echo $lang['manu_label']; ?></label>
<input id="menu" class="form-control" type="text" name="menu" value="<?php if(isset($page['menu'])) echo $page['menu']; ?>" placeholder="<?php echo $lang['menu_description']; ?>">
</div>

<div class="form-group">
<label for="tv"><?php echo $lang['tv_label']; ?></label>
<input id="tv" class="form-control" type="text" name="tv" value="<?php if(isset($page['tv'])) echo $page['tv']; ?>" placeholder="<?php echo $lang['tv_description']; ?>">
</div>

<div>
<span class="radio-label"><?php echo $lang['page_status_label']; ?></span>
<div class="radio">
<label>
<input id="status_0" type="radio" name="status" value="0"<?php if(isset($page['status'])&&$page['status']==0): ?> checked<?php endif; ?>>
<?php echo $lang['page_status'][0]; ?>
</label>
</div>
<div class="radio">
<label>
<input id="status_1" type="radio" name="status" value="1"<?php if(isset($page['status'])&&$page['status']==1): ?> checked<?php endif; ?>>
<?php echo $lang['page_status'][1]; ?>
</label>
</div>
<div class="radio">
<label>
<input id="status_2" type="radio" name="status" value="2"<?php if(isset($page['status'])&&$page['status']==2): ?> checked<?php endif; ?>>
<?php echo $lang['page_status'][2]; ?>
</label>
</div>
</div>

<div class="checkbox">
<label>
<input id="index" type="checkbox" name="index" value="1"<?php if(isset($page['index'])&&$page['index']==true): ?> checked<?php endif; ?> />
<?php echo $lang['page_index']; ?>
</label>
</div>
<!--
<div class="checkbox">
<label>
<input id="news" type="checkbox" name="news" value="1"<?php if(isset($page['news'])&&$page['news']==true): ?> checked<?php endif; ?> />
<?php echo $lang['page_news']; ?>
</label>
</div>
-->
<div class="checkbox">
<label>
<input id="project" type="checkbox" name="project" value="1"<?php if(isset($page['project'])&&$page['project']==true): ?> checked<?php endif; ?> />
<?php echo $lang['page_project']; ?>
</label>
</div>

</div>
</div>



<p><button class="btn btn-primary btn-lg" type="submit"><span class="glyphicon glyphicon-save"></span> <?php echo $lang['save_submit']; ?></button>

</div>
</form>

<?php $js[] = 'var slug = function(str) {
str = str.replace(/^\s+|\s+$/g, ""); // trim
str = str.toLowerCase();
// remove accents, swap ñ for n, etc
var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
var to   = "aaaaaeeeeeiiiiooooouuuunc------";
for (var i=0, l=from.length ; i<l ; i++) {
  str = str.replace(new RegExp(from.charAt(i), "g"), to.charAt(i));
}
str = str.replace(/[^a-z0-9 -]/g, "") // remove invalid chars
  .replace(/\s+/g, "-") // collapse whitespace and replace by -
  .replace(/-+/g, "-"); // collapse dashes
return str;
};

$("#title").change(function() { if(!$("#identifier").val()) $("#identifier").val(slug($("#title").val())); });';
?>
