<div class="row">
<div class="col-sm-8"><h1><?php echo $lang['profile_subtitle']; ?></h1></div>
<div class="col-sm-4"><a href="<?php echo BASE_URL; ?>?r=profile.edit" class="btn btn-primary btn-top-right"><span class="glyphicon glyphicon-pencil"></span> <?php echo $lang['edit_profile_link']; ?></a></div>
</div>

<?php if(isset($success)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>


<div class="table-responsive">
<table class="table table-striped">
<tr>
<td class="key"><strong><?php echo $lang['user_name_label']; ?></strong></td>
<td class="value"><?php echo $profile['name']; ?></td>
</tr>
<tr>
<td class="key"><strong><?php echo $lang['user_real_name_label']; ?></strong></td>
<td class="value"><?php echo $profile['real_name']; ?></td>
</tr>
<tr>
<td class="key"><strong><?php echo $lang['user_email_label']; ?></strong></td>
<td class="value"><a href="mailto:<?php echo $profile['email']; ?>"><?php echo $profile['email']; ?></a></td>
</tr>
<?php if($profile['language']): ?>
<tr>
<td class="key"><strong><?php echo $lang['user_language_label']; ?></strong></td>
<td class="value"><?php echo $profile['language']; ?></td>
</tr>
<?php endif; ?>
<tr>
<td class="key"><strong><?php echo $lang['user_time_zone_label']; ?></strong></td>
<td class="value"><?php echo $profile['time_zone']; ?></td>
</tr>
<?php if(isset($groups)): ?>
<tr>
<td class="key"><strong><strong><?php if(count($groups)==1): ?><?php echo $lang['user_group_label']; ?><?php else: ?><?php echo $lang['user_groups_label']; ?><?php endif; ?></strong></strong></td>
<td class="value"><?php echo implode(',<br />', $groups); ?></td>
</tr>
<?php endif; ?>
</table>
</div>
