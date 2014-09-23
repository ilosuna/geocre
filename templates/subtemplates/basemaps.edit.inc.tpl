<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=dashboard#admin"><?php echo $lang['dashboard_link']; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=basemaps"><?php echo $lang['basemaps_title']; ?></a></li>
<li class="active"><?php if(isset($basemap['id'])): ?><?php echo $lang['basemaps_edit_title']; ?><?php else: ?><?php echo $lang['basemaps_add_title']; ?><?php endif; ?></li>
</ul>

<?php if(isset($basemap['id'])): ?>
<h1><?php echo $lang['basemaps_edit_title']; ?></h1>
<?php else: ?>
<h1><?php echo $lang['basemaps_add_title']; ?></h1>
<?php endif; ?>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>


<form action="index.php" method="post">
<div>
<?php if(isset($basemap['id'])): ?>
<input type="hidden" name="r" value="basemaps.edit_submit" />
<input type="hidden" name="id" value="<?php echo $basemap['id']; ?>" />
<?php else: ?>
<input type="hidden" name="r" value="basemaps.add_submit" />
<?php endif; ?>

<div class="form-group">
<label for="title"><?php echo $lang['basemaps_title_label']; ?></label>
<input class="form-control" id="title" type="text" name="title" value="<?php if(isset($basemap['title'])) echo $basemap['title']; ?>" />
</div>

<div class="form-group">
<label for="properties"><?php echo $lang['basemaps_properties_label']; ?></label>
<input class="form-control" id="properties" type="text" name="properties" value="<?php if(isset($basemap['properties'])) echo $basemap['properties']; ?>" />
</div>

<div class="form-group">
<label for="js"><?php echo $lang['basemaps_js_label']; ?></label>
<input class="form-control" id="js" type="text" name="js" value="<?php if(isset($basemap['js'])) echo $basemap['js']; ?>" />
</div>

<div class="checkbox">
<label>
<input id="default" type="checkbox" name="default" value="1"<?php if(isset($basemap['default'])&&$basemap['default']): ?> checked="checked"<?php endif; ?> /> <?php echo $lang['basemaps_default_label']; ?>
</label>
</div>
<button type="submit" class="btn btn-primary"><?php echo $lang['save_submit']; ?></button>
</div>
</form>
