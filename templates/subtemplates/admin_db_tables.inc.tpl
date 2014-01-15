<div id="pageoptions"><a href="<?php echo BASE_URL; ?>?r=admin_db_table_properties" class="add_db_table"><?php echo $lang['add_db_table_link']; ?></a></div>

<h1><span class="breadcrumbs"><a href="<?php echo BASE_URL; ?>?r=admin"><?php echo $lang['admin_subtitle']; ?></a> &raquo; </span><?php echo $lang['db_tables_subtitle']; ?></h1>

<?php if(isset($db_tables)): ?>

<table class="items" id="sortable">
<thead>
<tr>
<th><?php echo $lang['db_table_name_column_label']; ?></th>
<th><?php echo $lang['db_table_title_column_label']; ?></th>
<th><?php echo $lang['db_table_project_column_label']; ?></th>
<th><?php echo $lang['db_table_type_column_label']; ?></th>
<th><?php echo $lang['db_table_status_column_label']; ?></th>
<th><?php echo $lang['db_table_parent_column_label']; ?></th>
<th>&nbsp;</th>
</tr>
</thead>

<tbody id="items"> 
<?php $i=1; foreach($db_tables as $db_table): ?>
<tr id="id_<?php echo $db_table['id']; ?>">
<td><span class="<?php if($db_table['status']>1&&empty($db_table['parent_table_name'])): ?>direct-editable<?php elseif($db_table['status']>1&&empty($db_table['parent_table_name'])): ?>indirect-editable<?php elseif($db_table['status']>0&&empty($db_table['parent_table_name'])): ?>read-only<?php elseif($db_table['status']==0): ?>locked<?php else: ?>default<?php endif; ?>"><?php echo $db_table['table_name']; ?></span></td>
<td><?php echo $db_table['title']; ?></td>
<td><?php echo $db_table['project']; ?></td>
<td><?php echo $lang['db_table_type_label'][$db_table['type']]; ?></td>
<td><?php echo $lang['db_table_status_label'][$db_table['status']]; ?></td>
<td><?php echo $db_table['parent_table_name']; ?></td>
<td class="nowrap"><a class="table_properties_button" href="<?php echo BASE_URL; ?>?r=admin_db_table_properties&amp;id=<?php echo $db_table['id']; ?>" title="<?php echo $lang['db_table_properties_title']; ?>"><?php echo $lang['db_table_properties_title']; ?></a>&nbsp;<!--
--><a class="delete_button" href="<?php echo BASE_URL; ?>?r=admin_db_tables&amp;delete=<?php echo $db_table['id']; ?>" title="<?php echo $lang['delete_db_table_title']; ?>"><?php echo $lang['delete']; ?></a>&nbsp;<!--
--><a class="move_up_button" href="<?php echo BASE_URL; ?>?r=admin_db_tables&amp;move_up=<?php echo $db_table['id']; ?>" title="<?php echo $lang['move_up']; ?>"><?php echo $lang['move_up']; ?></a><!--
--><a class="move_down_button" href="<?php echo BASE_URL; ?>?r=admin_db_tables&amp;move_down=<?php echo $db_table['id']; ?>" title="<?php echo $lang['move_down']; ?>"><?php echo $lang['move_down']; ?></a>

</td>
</tr>
<?php ++$i; endforeach; ?>
</tbody>

</table>

<?php else: ?>

<p><?php echo $lang['no_db_table_available']; ?></p>

<?php endif; ?>

<script type="text/javascript">Sortable.create('items', { tag:'tr', onUpdate : updateMenuOrder });</script>
