<h1><span class="breadcrumbs"><a href="<?php echo BASE_URL; ?>?r=photos"><?php echo $lang['photos_subtitle']; ?></a> &raquo;</span> <?php echo $lang['upload_photo_subtitle']; ?></h1>

<?php if(isset($errors)): ?>
<?php include(BASE_PATH.'templates/subtemplates/message.inc.tpl'); ?>
<?php endif; ?>

<form id="uploadform" action="index.php" method="post" enctype="multipart/form-data">
<div>

<p><?php echo $lang['photo_upload_step_1_message']; ?></p>

<input type="hidden" name="r" value="photos" />

<span class="file-wrapper">
<input type="file" name="file" id="file" />
<button class="defaultbutton"><?php echo $lang['photo_choose_label']; ?></button>
</span>

<input id="upload-submit" type="submit" name="upload_submit" value="<?php echo $lang['upload_photo_submit']; ?>" />

</div>
</form>

<script type="text/javascript">
$('#upload-submit').hide();
var upload_message = '<?php echo rawurlencode($lang['photo_beeing_uploaded_message']); ?>';

var SITE = SITE || {};
SITE.fileInputs = function() {
  var $this = $(this),
      $val = $this.val(),
      valArray = $val.split('\\'),
      newVal = valArray[valArray.length-1],
      $button = $this.siblings('.button');
  if(newVal !== '')
   {
    var fileError = false;
    
    if(window.FileReader)
     {
      input = document.getElementById('file');
      file = input.files[0];
      
      if(!file.type.match('image/jpeg') && !file.type.match('image/png') && !file.type.match('image/gif'))
       {
        fileError = true;
        alert('<?php echo addslashes($lang['error_photo_invalid_file_type']); ?>');  
       }
      else if(file.size > 5000000)
       {
        fileError = true;
        alert('<?php echo addslashes($lang['error_photo_file_too_large']); ?>');
       }
     }
    
    if(!fileError)
     {
      $('#uploadform').hide();
      $('<p class="wait">'+decodeURIComponent(upload_message)+'</p>').insertBefore('#uploadform');
      $('#uploadform').submit();
     }
   }
};
 
$(function() {
$('.file-wrapper input[type=file]').bind('change', SITE.fileInputs);

$('#file').hover(function() { $('.file-wrapper button').addClass("button-hover"); }, function() { $('.file-wrapper button').removeClass("button-hover"); } );

});
</script>
