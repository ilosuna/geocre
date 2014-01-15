<h1><?php echo $lang['users_and_groups_subtitle']; ?></h1>

<ul class="nav nav-tabs">
<li class="active"><a href="<?php echo BASE_URL; ?>?r=users" class="active"><?php echo $lang['users_subtitle']; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=groups"><?php echo $lang['groups_subtitle']; ?></a></a></li>
</ul>

<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=users"><?php echo $lang['users_subtitle']; ?></a></li>
<li class="active"><?php echo $lang['users_details_subtitle']; ?></li>
</ul>

<div class="row">
<div class="col-sm-6"><h2><?php echo $user['name']; ?></h2></div>
<div class="col-sm-6">
<span class="btn-top-right">
<a class="btn btn-primary" href="<?php echo BASE_URL; ?>?r=users.edit&amp;id=<?php echo $user['id']; ?>" ><span class="glyphicon glyphicon-pencil"></span> <?php echo $lang['edit_user_link']; ?></a></li>
<a class="btn btn-danger" href="<?php echo BASE_URL; ?>?r=users.delete&amp;id=<?php echo $user['id']; ?>"  data-delete-confirm="<?php echo rawurlencode($lang['delete_user_message']); ?>"><span class="glyphicon glyphicon-remove"></span> <?php echo $lang['delete_user_link']; ?></a>
</span>
</div>
</div>

<div class="table-responsive">
<table class="table table-striped table-hover">
<?php if(isset($user['id'])): ?>
<!--<thead>
<tr>
<th colspan="2"><?php echo $user['name']; ?></th>
</tr>
</thead>-->
<?php endif; ?>
<tbody>
<tr>
<td class="key"><strong><?php echo $lang['user_name_label']; ?></strong></td>
<td class="value"><?php echo $user['name']; ?></td>
</tr>
<tr>
<td class="key"><strong><?php echo $lang['user_real_name_label']; ?></strong></td>
<td class="value"><?php echo $user['real_name']; ?></td>
</tr>
<tr>
<td class="key"><strong><?php echo $lang['user_email_label']; ?></strong></td>
<td class="value"><a href="mailto:<?php echo $user['email']; ?>"><?php echo $user['email']; ?></a></td>
</tr>

<?php if(isset($available_languages)): ?>
<tr>
<td class="key"><strong><?php echo $lang['user_language_label']; ?></strong></td>
<td class="value">
<?php if($user['language'] && isset($available_languages[$user['language']])): ?>
<?php echo $available_languages[$user['language']]; ?>
<?php else: ?>
<?php echo $lang['user_default_language_label']; ?>
<?php endif; ?>
</td>
</tr>
<?php endif; ?>

<tr>
<td class="key"><strong><?php echo $lang['user_time_zone_label']; ?></strong></td>
<td class="value">
<?php if($user['time_zone']): ?>
<?php echo $user['time_zone']; ?>
<?php else: ?>
<?php echo $lang['user_default_time_zone_label']; ?>
<?php endif; ?>
</td>
</tr>

<tr>
<td class="key"><strong><?php echo $lang['user_registered_label']; ?></strong></td>
<td class="value"><?php echo $user['registered']; ?> <span class="small">(<?php echo $user['registered_ago']; ?>)</span></td>
</tr>
<tr>
<td class="key"><strong><?php echo $lang['user_logins_label']; ?></strong></td>
<td class="value"><?php echo $user['logins']; ?></td>
</tr>
<tr>
<td class="key"><strong><?php echo $lang['user_last_login_label']; ?></strong></td>
<td class="value"><?php if(isset($user['last_login'])): ?><?php echo $user['last_login']; ?> <span class="small">(<?php echo $user['last_login_ago']; ?>)</span><?php endif; ?></td>
</tr>

<?php if(isset($groups)): ?>
<td class="key"><strong><?php if(count($groups)==1): ?><?php echo $lang['user_group_label']; ?><?php else: ?><?php echo $lang['user_groups_label']; ?><?php endif; ?></strong></td>
<td class="value">
<ul class="list-unstyled">
<?php foreach($groups as $group): ?>
<li><a href="<?php echo BASE_URL; ?>?r=groups.properties&id=<?php echo $group['id']; ?>"><?php echo $group['name']; ?></a></li>
<?php endforeach; ?>
</ul>
</td>
</tr>
<?php endif; ?>

</tr>
</tbody>
</table>
</div>
