<h1><?php echo $lang['users_and_groups_subtitle']; ?></h1>


<ul class="nav nav-tabs">
<li class="active"><a href="<?php echo BASE_URL; ?>?r=users" class="active"><?php echo $lang['users_subtitle']; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=groups"><?php echo $lang['groups_subtitle']; ?></a></li>
</ul>


<?php if(isset($userdata_saved)): ?>
<div id="message">
<p class="ok"><?php echo $lang['userdata_saved']; ?></p>
</div>
<?php endif; ?>


<div class="row bottom-space">
<div class="col-sm-6">
<form action="index.php" method="get">
<div>
<input type="hidden" name="r" value="users" />
<?php if($filter): ?>
<div class="input-group">
<input class="form-control" type="text" name="filter" value="<?php echo $filter; ?>" placeholder="<?php echo $lang['user_filter_placeholder']; ?>" />
<span class="input-group-btn">
<a class="btn btn-danger" href="<?php echo BASE_URL; ?>?r=users" title="<?php echo $lang['user_filter_remove_title']; ?>" title="<?php echo $lang['user_filter_remove_title']; ?>"><span class="glyphicon glyphicon-remove"></span></a>
</span>
</div>
<?php else: ?>
<input class="form-control" type="text" name="filter" value="<?php echo $filter; ?>" placeholder="<?php echo $lang['user_filter_placeholder']; ?>" />
<?php endif; ?>
</div>
</form>
</div>
<div class="col-sm-6"><a class="btn btn-success pull-right" href="?r=users.add"><span class="glyphicon glyphicon-plus"></span> <?php echo $lang['admin_add_user_link']; ?></a></div>
</div>

<?php if(isset($users)): ?>

<div class="table-responsive">
<table class="table table-striped table-hover">
<thead>
<tr>
<th><a href="<?php echo BASE_URL; ?>?r=users&amp;order=name<?php if($order=='name'&&!$desc): ?>&amp;desc=1<?php endif; ?><?php if($filter): ?>&amp;filter=<?php echo urlencode($filter); ?><?php endif; ?>"<?php if($order=='name'&&!$desc): ?> class="asc"<?php elseif($order=='name'&&$desc): ?> class="desc"<?php endif; ?> title="<?php echo str_replace('[column]', $lang['user_name_column_label'], $lang['order_by']); ?>"><?php echo $lang['user_name_column_label']; ?></a></th>
<th><a href="<?php echo BASE_URL; ?>?r=users&amp;order=email<?php if($order=='email'&&!$desc): ?>&amp;desc=1<?php endif; ?><?php if($filter): ?>&amp;filter=<?php echo urlencode($filter); ?><?php endif; ?>"<?php if($order=='email'&&!$desc): ?> class="asc"<?php elseif($order=='email'&&$desc): ?> class="desc"<?php endif; ?> title="<?php echo str_replace('[column]', $lang['user_email_column_label'], $lang['order_by']); ?>"><?php echo $lang['user_email_column_label']; ?></a></th>
<th><a href="<?php echo BASE_URL; ?>?r=users&amp;order=groups<?php if($order=='groups'&&!$desc): ?>&amp;desc=1<?php endif; ?><?php if($filter): ?>&amp;filter=<?php echo urlencode($filter); ?><?php endif; ?>"<?php if($order=='groups'&&!$desc): ?> class="asc"<?php elseif($order=='groups'&&$desc): ?> class="desc"<?php endif; ?> title="<?php echo str_replace('[column]', $lang['user_groups_column_label'], $lang['order_by']); ?>"><?php echo $lang['user_groups_column_label']; ?></a></th>

<th><a href="<?php echo BASE_URL; ?>?r=users&amp;order=registered<?php if($order=='registered'&&!$desc): ?>&amp;desc=1<?php endif; ?><?php if($filter): ?>&amp;filter=<?php echo urlencode($filter); ?><?php endif; ?>"<?php if($order=='registered'&&!$desc): ?> class="asc"<?php elseif($order=='registered'&&$desc): ?> class="desc"<?php endif; ?> title="<?php echo str_replace('[column]', $lang['user_registered_column_label'], $lang['order_by']); ?>"><?php echo $lang['user_registered_column_label']; ?></a></th>
<th><a href="<?php echo BASE_URL; ?>?r=users&amp;order=logins<?php if($order=='logins'&&!$desc): ?>&amp;desc=1<?php endif; ?><?php if($filter): ?>&amp;filter=<?php echo urlencode($filter); ?><?php endif; ?>"<?php if($order=='logins'&&!$desc): ?> class="asc"<?php elseif($order=='logins'&&$desc): ?> class="desc"<?php endif; ?> title="<?php echo str_replace('[column]', $lang['user_logins_column_label'], $lang['order_by']); ?>"><?php echo $lang['user_logins_column_label']; ?></a></th>
<th><a href="<?php echo BASE_URL; ?>?r=users&amp;order=last_login<?php if($order=='last_login'&&!$desc): ?>&amp;desc=1<?php endif; ?><?php if($filter): ?>&amp;filter=<?php echo urlencode($filter); ?><?php endif; ?>"<?php if($order=='last_login'&&!$desc): ?> class="asc"<?php elseif($order=='last_login'&&$desc): ?> class="desc"<?php endif; ?> title="<?php echo str_replace('[column]', $lang['user_last_login_column_label'], $lang['order_by']); ?>"><?php echo $lang['user_last_login_column_label']; ?></a></th>
<th>&nbsp;</th>
</tr>
</thead>
<tbody>  
<?php $i=0; foreach($users as $user): ?>
<tr>
<td><a href="<?php echo BASE_URL; ?>?r=users.details&amp;id=<?php echo $user['id']; ?>"><strong><?php echo $user['name']; ?></strong></a></td>
<td><a href="mailto:<?php echo $user['email']; ?>"><?php echo truncate($user['email'], 40, true); ?></a></td>
<td><?php echo $user['groups']; ?></td>
<td><?php echo $user['registered']; ?></td>
<td><?php echo $user['logins']; ?></td>
<td><?php if(isset($user['last_login'])) echo $user['last_login']; else echo '&nbsp;'; ?></td>
<td class="options"><a class="btn btn-primary btn-xs" href="<?php echo BASE_URL; ?>?r=users.edit&amp;id=<?php echo $user['id']; ?>" title="<?php echo $lang['edit_user_title']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
<a class="btn btn-danger btn-xs" href="<?php echo BASE_URL; ?>?r=users.delete&amp;id=<?php echo $user['id']; ?>" title="<?php echo $lang['delete_user_title']; ?>" data-delete-confirm="<?php echo rawurlencode($lang['delete_user_message']); ?>"><span class="glyphicon glyphicon-remove"></span></a></td>
</tr>
<?php ++$i; endforeach; ?>
</tbody>
</table>
</div>

<div class="row">
<div class="col-sm-6"><?php if($pagination): ?>
<p><?php echo str_replace('[displayed]', $i, str_replace('[number]', $total_users, $lang['number_of_users_displayed_label'])); ?></p>
<?php else: ?>
<p><?php echo str_replace('[number]', $total_users, $lang['number_of_users_label']); ?></p>
<?php endif; ?></div>
<div class="col-sm-6"><?php if($pagination): ?>
<ul class="pagination pull-right nomargin">
<?php if($pagination['previous']): ?><li><a href="<?php echo BASE_URL; ?>?r=users&amp;p=<?php echo $pagination['previous']; ?>&amp;order=<?php echo $order; ?>&amp;desc=<?php echo $desc; ?><?php if($filter): ?>&amp;filter=<?php echo urlencode($filter); ?><?php endif; ?>" title="<?php echo $lang['previous_page_title']; ?>"><span class="glyphicon glyphicon-chevron-left"></span></a></li><?php endif; ?>
<?php foreach($pagination['items'] as $item): ?>
<?php if($item==0): ?><li><span>&hellip;</span></li><?php elseif($item==$pagination['current']): ?><li class="active"><span><?php echo $item; ?></span></li><?php else: ?><li><a href="<?php echo BASE_URL; ?>?r=users&amp;p=<?php echo $item; ?>&amp;order=<?php echo $order; ?>&amp;desc=<?php echo $desc; ?><?php if($filter): ?>&amp;filter=<?php echo urlencode($filter); ?><?php endif; ?>"><?php echo $item; ?></a></li><?php endif; ?>
<?php endforeach; ?>
<?php if($pagination['next']): ?><li><a href="<?php echo BASE_URL; ?>?r=users&amp;p=<?php echo $pagination['next']; ?>&amp;order=<?php echo $order; ?>&amp;desc=<?php echo $desc; ?><?php if($filter): ?>&amp;filter=<?php echo urlencode($filter); ?><?php endif; ?>" title="<?php echo $lang['next_page_title']; ?>"><span class="glyphicon glyphicon-chevron-right"></span></a></li><?php endif; ?>  
</ul>
<?php endif; ?></div>
</div>

<?php elseif($filter): ?>

<div class="alert alert-warning"><?php echo $lang['no_users_found']; ?></div>

<?php else: ?>

<div class="alert alert-warning"><?php echo $lang['no_users']; ?></div>

<?php endif; ?>
