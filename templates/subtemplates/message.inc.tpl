<?php if(isset($success)): ?>
<div class="alert alert-success">
<a class="close" data-dismiss="alert" href="#" aria-hidden="true">&times;</a>
<p><span class="glyphicon glyphicon-ok"></span> <?php if(isset($lang[$success])) echo $lang[$success]; else echo $lang['success_message']; ?></p>
</div>
<?php elseif(isset($failure)): ?>
<div class="alert alert-danger">
<a class="close" data-dismiss="alert" href="#" aria-hidden="true">&times;</a>
<strong><?php if(isset($lang[$failure])) echo $lang[$failure]; else echo $lang['failure_message']; ?></strong>
</div>
<?php elseif(isset($errors)): ?>
<div class="alert alert-danger alert-error">
<h3><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $lang['error']; ?></h3>
<ul><?php
foreach($errors as $error): ?>
<li><?php if(isset($lang[$error])): ?><?php echo $lang[$error]; ?><?php else: ?><?php echo $error; ?><?php endif; ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>
