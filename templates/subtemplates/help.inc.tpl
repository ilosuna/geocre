<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h4 class="modal-title"><?php echo $help['title']; ?></h4>
</div>
<div class="modal-body">
<?php echo $help['content']; ?>
</div>

<?php if($settings['feedback']): ?>
<div class="modal-header">
<h4 class="modal-title"><?php echo $lang['feedback_subtitle']; ?></h4>
</div>

<div class="modal-body">
<p><?php echo $lang['feedback_description']; ?></p>
<div id="feedback">
<form>
<div class="form-group">
<label class="sr-only" for="message"><?php echo $lang['feedback_message_label']; ?></label>
<textarea id="message" class="form-control" rows="7"></textarea>
</div>
<div class="form-group">
<button type="button" class="btn btn-primary" data-submit="<?php echo rawurlencode($lang['feedback_submit_button_processing']); ?>"><?php echo $lang['feedback_submit_button']; ?></button>
</div>
</form>
</div>
</div>

<script>
$("[data-submit]").click(function(){
	$("[data-submit]").attr("disabled", "disabled");
	btnInitialValue = $("[data-submit]").html();
	$("[data-submit]").html(decodeURIComponent($(this).data("submit")));
	$("[data-submit]").addClass("btn-processing");
	message = value = $("#message").val();
	url = $(location).attr('href');
	$.ajax({
		type: "POST",
		url: "index.php",
		data: { r:"feedback", help:1, url:url, feedback_message:message },
		dataType: "json",
		success: function(response) { 
			if(response.status==1) {
  	   			$("#feedback").html('<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> '+response.message+'</div>');
			}
			else {
				$("[data-submit]").removeClass("btn-processing")
				$("[data-submit]").html(btnInitialValue);
				$("[data-submit]").removeAttr("disabled");
				$("#error").hide();
				$("#feedback").prepend('<div id="error" class="error alert alert-danger"><h4><span class="glyphicon glyphicon-warning-sign"></span> <?php echo addslashes($lang['error']); ?></h4>'+response.message+'</div>');
			}
		},
		error: function() {
			$("[data-submit]").removeClass("btn-processing")
			$("[data-submit]").html(btnInitialValue);
			$("[data-submit]").removeAttr("disabled");
			$("#error").hide();
			$("#feedback").prepend('<div id="error" class="error alert alert-danger"><h4><span class="glyphicon glyphicon-warning-sign"></span> <?php echo addslashes($lang['error']); ?></h4><p><?php echo addslashes($lang['invalid_request_title']); ?></p></div>');
		}
	});  
});
</script>
<?php else: ?>
<div class="modal-footer">
<button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo $lang['ok']; ?></button>
</div>
<?php endif; ?>
