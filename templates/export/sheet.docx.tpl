<?php
/*

This template is used for the export of data sheets as Word/DOCX files. The DOCX
files are created by the HTML to docx Converter ( http://htmltodocx.codeplex.com/ ). 
The HTML to docx converter uses PHPWord ( http://phpword.codeplex.com/ ) and
SimpleHTMLDom ( http://simplehtmldom.sourceforge.net/ ). 

*/

// Definition of styles for PHPWord:
global $phpword_styles;
$phpword_styles['default'] =  array('size' => 11);
$phpword_styles['elements'] = array('h1' => array('bold' => TRUE, 'size' => 20),
                                    'h2' => array('bold' => TRUE, 'size' => 16, 'spaceAfter' => 150),
                                    'h3' => array('size' => 12, 'bold' => TRUE, 'spaceAfter' => 100),
                                    'b' => array ('bold' => TRUE),
                                    'em' => array ('italic' => TRUE),
                                    'i' => array ('italic' => TRUE),
                                    'strong' => array ('bold' => TRUE),
                                    'a' => array ('color' => '0000FF', 'underline' => PHPWord_Style_Font::UNDERLINE_SINGLE));
$phpword_styles['classes'] =  array('label' => array ('bold' => true),
                                    'field' => array ('color' => '808080'),
                                    'description' => array('bold' => false, 'size' => 11, 'color' => '808080'));
?><html>
<body>

<h1><?php echo $title; ?></h1>

<?php if(isset($items)): ?>
<?php foreach($items as $item): ?>

<?php if($item['column_type']==0): ?>
<?php if(empty($item['label'])): ?>
<hr />
<?php elseif($item['priority']==2): ?>
<h2><?php echo $item['label']; ?>
<?php if($item['description']): ?><br /><span><?php echo $item['description']; ?></span><?php endif; ?></h2>
<?php else: ?>
<h3><?php echo $item['label']; ?>
<?php if($item['description']): ?><br /><span><?php echo $item['description']; ?></span><?php endif; ?></h3>
<?php endif; ?>

<?php else: ?>

<div>
<?php if($item['column_type']==6): ?>
<p class="label">◯  <?php echo $item['label']; ?></p>
<?php else: ?>
<p class="label"><?php echo $item['label']; ?></p>
<?php if($item['description']): ?><p class="description"><?php echo $item['description']; ?></p><?php endif; ?>
<?php if(isset($item['choices'])): ?>
<p>
<?php foreach($item['choices'] as $choice): ?>
◯  <?php echo $choice; ?><br />
<?php endforeach; ?>
</p>
<?php else: ?>
<?php if($item['column_type']==5): ?>
<p class="field">______________________________________________________________________</p>
<p class="field">______________________________________________________________________</p>
<p class="field">______________________________________________________________________</p>
<?php else: ?>
<p class="field">______________________________________________________________________</p>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
</div>

<?php endif; ?>

<?php endforeach; ?>
<?php endif; ?>

</body>
</html>
