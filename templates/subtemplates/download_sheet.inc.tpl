<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=dashboard#data"><?php echo $lang['dashboard_link']; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=data&amp;data_id=<?php echo $data['id']; ?>"><?php echo $data['title']; ?></a></li>
<li class="active"><?php echo $lang['download_sheet_title']; ?></li>
</ul>

<h1><?php echo $lang['download_sheet_title']; ?>: <?php echo $data['title']; ?></h1>

<form action="index.php" method="post"><div>
<input type="hidden" name="r" value="download_sheet.download" />
<input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
<input id="downloadtoken" type="hidden" name="downloadtoken">

<fieldset>
<legend><?php echo $lang['download_sheet_format_label']; ?></legend>

<div class="radio">
  <label>
    <input type="radio" name="format" id="pdf" value="pdf" checked>
    <?php echo $lang['download_sheet_format_pdf']; ?>
  </label>
</div>

<div class="radio">
  <label>
    <input type="radio" name="format" id="docx" value="docx">
    <?php echo $lang['download_sheet_format_docx']; ?>
  </label>
</div>

<div class="radio">
  <label>
    <input type="radio" name="format" id="txt" value="txt">
    <?php echo $lang['download_sheet_format_txt']; ?>
  </label>
</div>

</fieldset>

<div class="top-space">
<button class="btn btn-primary btn-lg" type="submit" data-downloading="<?php echo rawurlencode($lang['processing_message']); ?>"><span class="glyphicon glyphicon-cloud-download"></span> <?php echo $lang['download_sheet_submit']; ?></button>
</div>

</div></form>
