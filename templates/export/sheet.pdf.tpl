<?php
/*

This template is used for the export of data sheets as PDF files. The PDF files 
are created by the dompdf HTML to PDF converter ( https://github.com/dompdf/dompdf ). 

*/
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>" />
<meta name="author" content="<?php echo $settings['website_title']; ?>" />
<title><?php echo $title; ?></title>
<style>
body         { font-family:"DejaVu Sans",sans-serif; font-size:12px; }
h2,
h3           { page-break-after:avoid; }
h2 span,
h3 span      { font-size:11px; color:#808080; font-weight:normal; font-style:italic; }
div          { margin:0 0 20px 0; page-break-inside:avoid; }
.label       { margin:0; padding:0 0 2px 0; font-weight:bold; }
.description { margin:0; padding:0 0 2px 0; font-size:11px; color:#808080; font-style:italic; }
.field       { margin:0; height:30px; border:1px solid #ccc; }
.textfield   { height:150px; }
ul           { margin:0; padding:0; list-style-type:none; }
hr           { margin-bottom:20px; }
br           { page-break-inside:avoid; }
</style>
</head>

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
<p class="label">◯ <?php echo $item['label']; ?></p>
<?php else: ?>
<p class="label"><?php echo $item['label']; ?></p>
<?php if($item['description']): ?><p class="description"><?php echo $item['description']; ?></p><?php endif; ?>
<?php if(isset($item['choices'])): ?>
<ul>
<?php foreach($item['choices'] as $choice): ?>
<li>◯ <?php echo $choice; ?></li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<p class="field<?php if($item['column_type']==5): ?> textfield<?php endif; ?>"></p>
<?php endif; ?>
<?php endif; ?>
</div>

<?php endif; ?>

<?php endforeach; ?>
<?php endif; ?>

</body>
</html>
