$(function() {

if(location.hash !== "")
 {
  $(".mytab-pane").removeClass("mytab-pane-active");
  $(location.hash).addClass("mytab-pane-active");
 }

$('[data-toggle="mytab"]').click(function(e) { 
e.preventDefault();

$(this).parents("ul").children("li").removeClass("active");
$(this).parent().addClass("active");

$(".mytab-pane").removeClass("mytab-pane-active");
$($(this).attr("href")).hide();
$($(this).attr("href")).addClass("mytab-pane-active");
$($(this).attr("href")).fadeIn("fast");

});
});
