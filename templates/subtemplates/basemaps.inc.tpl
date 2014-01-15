<div class="row">
<div class="col-sm-10"><h1><?php echo $lang['basemaps_title']; ?></h1></div>
<div class="col-sm-2"><a class="btn btn-success btn-top-right" href="<?php echo BASE_URL; ?>?r=basemaps.add"><span class="glyphicon glyphicon-plus"></span> <?php echo $lang['basemap_add_link']; ?></a></div>
</div>

<?php if(isset($basemaps)): ?>

<div class="table-responsive">
<table class="table table-striped table-hover">
<thead>
<tr>
<th><?php echo $lang['basemap_title_column_label']; ?></th>
<th><?php echo $lang['basemap_properties_column_label']; ?></th>
<th><?php echo $lang['basemap_js_column_label']; ?></th>
<th><?php echo $lang['basemap_default_column_label']; ?></th>
<th>&nbsp;</th>
</tr>
</thead>

<tbody data-sortable="<?php echo BASE_URL; ?>?r=basemaps.reorder">
<?php $i=1; foreach($basemaps as $basemap): ?>
<tr id="item_<?php echo $basemap['id']; ?>">
<td><strong><?php echo $basemap['title']; ?></strong></td>
<td><?php echo truncate($basemap['properties'], 50, true); ?></td>
<td><?php if($basemap['js']): ?><a href="<?php echo $basemap['js']; ?>"><?php echo truncate($basemap['js'], 30, true); ?></a><?php endif; ?></td>
<td><?php if($basemap['default']): ?><span class="active text-success" title="<?php echo $lang['yes']; ?>"><span class="glyphicon glyphicon-ok"></span></span><?php endif; ?></td>
<td class="options"><a class="btn btn-primary btn-xs" href="<?php echo BASE_URL; ?>?r=basemaps.edit&amp;id=<?php echo $basemap['id']; ?>" title="<?php echo $lang['edit']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
<a class="btn btn-danger btn-xs" href="<?php echo BASE_URL; ?>?r=basemaps.delete&amp;id=<?php echo $basemap['id']; ?>" title="<?php echo $lang['delete']; ?>" data-delete-confirm="<?php echo rawurlencode($lang['basemaps_delete_message']); ?>"><span class="glyphicon glyphicon-remove"></span></a>
<span class="btn btn-success btn-xs sortable_handle" title="<?php echo $lang['drag_and_drop']; ?>"><span class="glyphicon glyphicon-sort"></span></td>
</tr>
<?php ++$i; endforeach; ?>
</tbody>

</table>
</div>

<?php else: ?>

<p><em><?php echo $lang['no_basemaps_available']; ?></em></p>

<?php endif; ?>
