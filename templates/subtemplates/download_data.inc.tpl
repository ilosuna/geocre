<ul class="breadcrumb">
<li><a href="<?php echo BASE_URL; ?>?r=dashboard#data"><?php echo $lang['dashboard_link']; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=data&amp;data_id=<?php echo $data['id']; ?>"><?php echo $data['title']; ?></a></li>
<li class="active"><?php echo $lang['download_data_subtitle']; ?></li>
</ul>

<h1><?php echo $lang['download_data_headline']; ?></h1>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<form action="index.php" method="post"><div>
<input type="hidden" name="r" value="download_data.download">
<input type="hidden" name="id" value="<?php echo $data['id']; ?>">
<input id="downloadtoken" type="hidden" name="downloadtoken">

<fieldset>
<legend><?php echo $lang['download_data_options_label']; ?></legend>

<div class="checkbox">
  <label>
    <input type="checkbox" name="column_header_labels" id="column_header_labels" checked>
    <?php echo $lang['download_data_column_header_labels']; ?>
  </label>
</div>
<div class="checkbox">
  <label>
    <input type="checkbox" name="labels" id="labels" checked>
    <?php echo $lang['download_data_labels']; ?>
  </label>
</div>
<div class="checkbox">
  <label>
    <input type="checkbox" name="metadata" id="metadata">
    <?php echo $lang['download_data_metadata']; ?>
  </label>
</div>
<?php if($data['type']==1): ?>
<div class="checkbox">
  <label>
    <input type="checkbox" name="spatial_metadata" id="spatial_metadata">
    <?php echo $lang['download_data_spatial_metadata']; ?>
  </label>
</div>
<div class="checkbox">
  <label>
    <input type="checkbox" name="wkt" id="wkt">
    <?php echo $lang['download_data_wkt']; ?>
  </label>
</div>
<?php endif; ?>
</fieldset>

<?php if($data['parent_table']): ?>
<fieldset>
<legend><?php echo $lang['download_data_join_parent_data_label']; ?></legend>
<div class="checkbox">
  <label>
    <input type="checkbox" name="join" id="join">
    <?php echo $data['parent_title']; ?>
  </label>
</div>
</fieldset>
<?php endif; ?>

<?php if(isset($data['child'])): ?>

<fieldset>
<legend><?php echo $lang['download_data_merge_child_data_label']; ?></legend>
<?php foreach($data['child'] as $child): ?>
<div class="checkbox">
  <label>
    <input type="checkbox" name="childdata[]" value="<?php echo $child['id']; ?>">
    <?php echo $child['title']; ?>
  </label>
</div>
<?php endforeach; ?>

</fieldset>

<?php endif; ?>



<fieldset>
<legend><?php echo $lang['download_data_format_label']; ?></legend>

<div class="radio">
  <label>
    <input type="radio" name="format" id="xlsx" value="xlsx" checked="checked">
    <?php echo $lang['download_data_format_xlsx']; ?>
  </label>
</div>
<div class="radio">
  <label>
    <input type="radio" name="format" id="xls" value="xls">
    <?php echo $lang['download_data_format_xls']; ?>
  </label>
</div>
<div class="radio">
  <label>
    <input type="radio" name="format" id="xml" value="xml">
    <?php echo $lang['download_data_format_xml']; ?>
  </label>
</div>
<div class="radio">
  <label>
    <input type="radio" name="format" id="csv" value="csv">
    <?php echo $lang['download_data_format_csv']; ?>
  </label>
</div>
<?php if($data['type']==1): ?>
<div class="radio">
  <label>
    <input type="radio" name="format" id="shp" value="shp">
    <?php echo $lang['download_data_format_shp']; ?>
  </label>
</div>
<div class="radio">
  <label>
    <input type="radio" name="format" id="kml" value="kml">
    <?php echo $lang['download_data_format_kml']; ?>
  </label>
</div>
<?php endif; ?>
</fieldset>

<div class="top-space">
<button class="btn btn-primary btn-lg" type="submit" data-downloading="<?php echo rawurlencode($lang['processing_message']); ?>"><span class="glyphicon glyphicon-cloud-download"></span> <?php echo $lang['download_data_submit']; ?></button>
</div>


</div></form>
