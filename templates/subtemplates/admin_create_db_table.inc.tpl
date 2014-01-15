<p><a class="back" href="<?php echo BASE_URL; ?>?r=admin_db_table_properties&amp;id=<?php echo $table_data['id']; ?>"><?php echo $lang['back']; ?></a></p>

<h1><?php echo $lang['create_db_table_subtitle']; ?></h1>

<p><?php echo str_replace('[table]', $table_data['table_name'], $lang['create_db_table_message']); ?></p>

<form action="index.php" method="post"><div>
<input type="hidden" name="r" value="admin_db_table_properties" />
<input type="hidden" name="table" value="<?php echo $table_data['id']; ?>" />
<input type="submit" name="create_db_table_submit" value="<?php echo $lang['ok_submit']; ?>" />
</div></form>
