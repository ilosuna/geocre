<div class="jumbotron jumbotron-world">
<?php echo $settings['index_page_content']; ?>
</div>

<?php if(isset($projects)): ?>

<div class="row content">

<?php $i=1; foreach($projects as $project): ?>

<div class="col-md-4 teaser">
<?php if(isset($project['teaser_supertitle'])): ?><p class="supertitle"><?php echo $project['teaser_supertitle']; ?></p><?php endif; ?>
<h2><a href="<?php if(isset($project['tv']['teaser_link'])): ?><?php echo $project['tv']['teaser_link']; ?><?php else: ?><?php echo BASE_URL . $project['identifier']; ?><?php endif; ?>"><?php echo $project['teaser_title']; ?></a></h2>
<div class="media-body">
<?php if(isset($project['teaser_image'])): ?><a class="thumbnail pull-left" href="<?php if(isset($project['tv']['teaser_link'])): ?><?php echo $project['tv']['teaser_link']; ?><?php else: ?><?php echo BASE_URL . $project['identifier']; ?><?php endif; ?>"><img src="<?php echo PAGE_TEASER_IMAGES_URL.$project['teaser_image']['file']; ?>" width="<?php echo $project['teaser_image']['width']; ?>" height="<?php echo $project['teaser_image']['height']; ?>" alt="<?php echo $project['teaser_title']; ?>" /></a><?php endif; ?>
<div>
<p><?php echo $project['teaser_text']; ?></p>
<?php if(isset($project['teaser_linktext'])): ?><p><a href="<?php if(isset($project['tv']['teaser_link'])): ?><?php echo $project['tv']['teaser_link']; ?><?php else: ?><?php echo BASE_URL . $project['identifier']; ?><?php endif; ?>"><?php echo $project['teaser_linktext']; ?></a></p><?php endif; ?>
</div>
</div>
</div>

<?php if($i%3 == 0 && $i<$project_count): ?>
</div>
<div class="row">
<?php endif; ?>

<?php ++$i; endforeach; ?>

</div>

<?php else: ?>

<div class="alert alert-warning"><?php echo $lang['no_pages_available']; ?></div>

<?php endif; ?>
