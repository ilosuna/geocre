*** <?php echo $title; ?> ***
<?php if(isset($items)): ?>
<?php foreach($items as $item): ?>
<?php if($item['column_type']==0): ?>
<?php if(empty($item['label'])): ?>

--------------------------------------------------------------------------------
<?php elseif($item['priority']==2): ?>

** <?php echo $item['label']; ?> **
<?php else: ?>

* <?php echo $item['label']; ?> *
<?php if($item['description']): ?>
- <?php echo $item['description']; ?> -
<?php endif; ?>
<?php endif; ?>
<?php else: ?>
<?php if($item['column_type']==6): ?>

[ ] <?php echo $item['label']; ?>

<?php else: ?>

<?php echo $item['label']; ?>

<?php if($item['description']): ?>
- <?php echo $item['description']; ?> -
<?php endif; ?>
<?php if(isset($item['choices'])): ?>
<?php foreach($item['choices'] as $choice): ?>
[ ] <?php echo $choice; ?>

<?php endforeach; ?>
<?php else: ?>
<?php if($item['column_type']==5): ?>
[______________________________________________________________________________]
<?php else: ?>
[______________________________]
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
