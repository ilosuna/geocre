<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL.$page['identifier']; ?>"><?php echo $page['title']; ?></a></li>
<li class=""><?php echo $lang['page_delete_subtitle']; ?></li>
</ul>

<h1 class="text-danger"><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $lang['page_delete_subtitle']; ?></h1>

<?php if(isset($failure)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<p><?php echo $lang['page_delete_message']; ?></p>

<p><strong><?php echo $page['title']; ?></strong></p>

<form action="index.php" method="post">
<div>
<input type="hidden" name="r" value="page.delete_submit" />
<input type="hidden" name="id" value="<?php echo $page['id']; ?>" />
<input type="hidden" name="confirmed" value="true" />

<p><label for="pw"><strong><?php echo $lang['login_password']; ?></strong></label><br />
<input class="form-control form-control-default" id="pw" type="password" name="pw" size="30" autocomplete="off" autofocus></p>


<button class="btn btn-danger btn-lg" type="submit"><span class="glyphicon glyphicon-remove"></span> <?php echo $lang['delete_submit']; ?></button>
</div>
</form>
