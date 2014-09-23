<?php if(isset($back)): ?>
<p><a class="back" href="<?php echo $back; ?>"><?php echo $lang['back']; ?></a></p>
<?php endif; ?>

<h1 class="caution"><?php echo $subtitle; ?></h1>
<?php if(isset($item)): ?>
<p><?php echo str_replace('[item]', $item, $lang[$delete_message]); ?></p>
<?php elseif(isset($delete_message)): ?>
<p><?php echo $lang[$delete_message]; ?></p>
<?php endif; ?>
<form action="index.php" method="post"><div>
<input type="hidden" name="r" value="<?php echo $r; ?>" />
<input type="hidden" name="confirmed" value="true" />
<?php if(isset($data_id)): ?>
<input type="hidden" name="data_id" value="<?php echo $data_id; ?>" />
<?php endif; ?>
<?php if(isset($delete)): ?>
<input type="hidden" name="delete" value="<?php echo $delete; ?>" />
<?php endif; ?>
<?php if(isset($id)): ?>
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<?php endif; ?>
<button class="btn btn-danger btn-lg"><span class="glyphicon glyphicon-remove"></span> <?php echo $lang['delete_submit']; ?></button>
</div></form>
