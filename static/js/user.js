function delete_confirm(this_link, confirm_question)
 {
  //document.getElementById(highlight).setAttribute('class', 'highlight');
  $(this_link).parents("tr").addClass('highlight');
  var confirmed = confirm(decodeURIComponent(confirm_question));
  if(confirmed) this_link.href += '&confirmed=true';
  $(this_link).parents("tr").removeClass('highlight');
  return confirmed;
 }

function getElementsByClassName(class_name)
{
  var all_obj,ret_obj=new Array(),j=0;
  if(document.all)all_obj=document.all;
  else if(document.getElementsByTagName && !document.all)all_obj=document.getElementsByTagName("*");
  for(i=0;i<all_obj.length;i++)
  {
    if(all_obj[i].className==class_name)
    {
      ret_obj[j]=all_obj[i];
      j++
    }
  }
  return ret_obj;
}

function saveInputValue(id, field)
 {
  if($('#'+field).val() && $('#'+field).is(':checkbox'))
   {
    if($('#'+field).is(':checked')) value = $('#'+field).val();
    else value = '';
   }
  else if($('#'+field).val() && $('#'+field).not(':checkbox')) value = $('#'+field).val();
  else if($('[name="data[_'+field+'_]"]').val()) value = $('[name="data[_'+field+'_]"]').val();
  else if($('input[name="data['+field+']"]:checked').val()) value = $('input[name="data['+field+']"]:checked').val();
  else value = '';
  if(value && $('#'+field+'-remember-handle').hasClass('btn-default'))
   {
    $('#'+field+'-remember-handle').removeClass('btn-default').addClass('btn-warning');
    data = {key:id, value:value};
   }
  else
   {
    $('#'+field+'-remember-handle').removeClass('btn-warning').addClass('btn-default');
    data = {key:id, value:''};
   } 
  $.ajax({
  type: "POST",
  url: "index.php",
  data: { r:"arp.set_user_setting", input_value:data }
  });  
 }


function selectRow(id)
 {
  document.getElementById(id).className='selected';
 }

function unselectRow(id)
 {
  document.getElementById(id).className='';
 }
 
function selectFeature(section, id)
 {
  eval("selectControl" + section + ".unselectAll();");
  
  eval("var length = vectorLayer" + section + ".features.length;");

  for(var i = 0; i < length; ++i)
    {
     eval("var featureId = vectorLayer" + section + ".features[i].attributes.id;");
     if(featureId == id)
      {
       eval("selectControl = selectControl" + section + ".select(vectorLayer" + section + ".features[i]);");
      }
    }
 }
 
function enlargeMap()
 {
  var height = document.getElementById('editmap').offsetHeight;
  if(height<1000)
   {
    document.getElementById('editmap').style.height=height+150+'px';
    map.updateSize();
   }
 }
 
function reduceMap()
 {
  var height = document.getElementById('editmap').offsetHeight;
  if(height>300)
   {
    document.getElementById('editmap').style.height=height-150+'px';
    map.updateSize();
   }
 }

function toggleZoomWheel(edit)
 {
  edit = typeof edit !== 'undefined' ? true : false;
  if(document.getElementById('zoomwheel').className == 'active')
   {
    document.getElementById('zoomwheel').className = 'inactive';
    navigationControl.disableZoomWheel();
    var zoomwheel = 0;
   }
  else
   {
    document.getElementById('zoomwheel').className = 'active';
    navigationControl.enableZoomWheel();
    var zoomwheel = 1;
   }   
 
  if(edit==true)
   {
    $.ajax({
    type: "POST",
    url: "index.php",
    data: { r:"arp.set_user_setting", map_edit_zoomwheel:zoomwheel }
    });
   }
  else
   {
    $.ajax({
    type: "POST",
    url: "index.php",
    data: { r:"arp.set_user_setting", map_zoomwheel:zoomwheel }
    });   
   } 
 
 }

function toggleSnapping()
 {
  if(document.getElementById('snapping').className == 'active')
   {
    document.getElementById('snapping').className = 'inactive';
    snap.deactivate();
    var snapping = 0;
   }
  else
   {
    document.getElementById('snapping').className = 'active';
    snap.activate();
    var snapping = 1;
   }   
  $.ajax({
  type: "POST",
  url: "index.php",
  data: { r:"arp.set_user_setting", map_snapping:snapping }
  });  
 }

function resizeMap(value)
 {
  var newMapcontainerHeight = $("#mapcontainer").height() + value;
  if(newMapcontainerHeight >= 200 && newMapcontainerHeight <= 1000)
   {
    if(value<0) var resizeValue = '-=' + Math.abs(value);
    else var resizeValue = '+=' + value;
    $('#mapcontainer').animate({ height:resizeValue }, 300, function() { map.updateSize(); saveMapHeight($("#mapcontainer").height()); });  
   }
 }

function saveMapHeight(value)
 {
  $.ajax({
  type: "POST",
  url: "index.php",
  data: { r:"arp.set_user_setting", map_height:value }
  });  
 }

function resetMap()
 {
  $('#resetmap').remove();
  $("#wrapper").show();
  $('#mapcontainer').removeClass("fullscreen");
  $('body').removeClass("fullscreen");
  $('#mapsizetools').attr("class", "buttongroup");
  $('#disablemap').show();
  if(typeof initialMapHeight==='undefined') $('#mapcontainer').css({'height':'550px'});
  else $('#mapcontainer').css({'height': initialMapHeight + 'px'});
  $("#mapcontainer").prependTo("#mapwrapper");
  map.updateSize();
  if(typeof nav!=='undefined') nav.disableZoomWheel();
 }

function featureInfo(areaRaw, perimeterRaw, lengthRaw, latitudeRaw, longitudeRaw)
 {
  if(areaRaw > 0)
   {
    var featureInfo = "<strong>Area:</strong> "+areaRaw.toFixed(1)+" m²";
    if(areaRaw>1000) featureInfo += " / "+(areaRaw/10000).toFixed(1)+" ha";
    if(areaRaw>100000) featureInfo += " / "+(areaRaw/1000000).toFixed(1)+" km²";
    featureInfo += "<br><strong>Perimeter:</strong> "+perimeterRaw.toFixed(1)+" m";
    if(perimeterRaw>100) featureInfo += " / "+(perimeterRaw/1000).toFixed(1)+" km";
    featureInfo += "<br><strong>Centroid:</strong> "+latitudeRaw.toFixed(5)+", "+longitudeRaw.toFixed(5);
   }
  else if(lengthRaw > 0)
   {
    var featureInfo = "<strong>Length:</strong> "+lengthRaw.toFixed(1)+" m";
    if(lengthRaw>100) featureInfo += " / "+(lengthRaw/1000).toFixed(1)+" km";
    featureInfo += "<br><strong>Centroid:</strong> "+latitudeRaw.toFixed(5)+", "+longitudeRaw.toFixed(5);
   }
  else if(latitudeRaw!=0 && longitudeRaw!=0)
   {
    var featureInfo = latitudeRaw.toFixed(5)+", "+longitudeRaw.toFixed(5);
   }
  else
   {
    var featureInfo = "";
   }
  return featureInfo;
 }

function finishDownload() {
 window.clearInterval(fileDownloadCheckTimer);
 $.removeCookie('downloadtoken');
 $("[data-downloading]").html(btnInitialValue);
 $("[data-downloading]").removeClass("btn-processing");
 $("[data-downloading]").removeAttr("disabled");
}

 
$(function() {

$('body').on('hidden.bs.modal', '.modal', function () {
  $(this).removeData('bs.modal');
});

$('[data-check]').click(function(){
$('#'+$(this).data('check')).attr("checked","checked");
});

$("[data-processing]").click(function(){
$($(this)).attr("disabled", "disabled");
$($(this)).html(decodeURIComponent($(this).data("processing")));
$($(this)).addClass("btn-processing");
});

$("[data-downloading]").click(function(){
$($(this)).attr("disabled", "disabled");
btnInitialValue = $($(this)).html();

$($(this)).html(decodeURIComponent($(this).data("downloading")));
$($(this)).addClass("btn-processing");

    var token = new Date().getTime();
    $('#downloadtoken').val(token);
    
    fileDownloadCheckTimer = window.setInterval(function () {
      var cookieValue = $.cookie('downloadtoken');
      if (cookieValue == token)
       finishDownload();
    }, 1000);

});

$('[data-disable-on-submit]').submit(function(){
  $(this).find(':submit').attr('disabled', 'disabled');
});

$("form[data-validate]").submit(function( event ) {
error_message='';
var requiredFields = $('[data-required]').map(function() { return $(this).data('required'); }).get();
var messages = $('[data-message]').map(function() { return $(this).data('message'); }).get();
  for(var i = 0; i < requiredFields.length; i++)
   {
    var passed = false;
    $("form[data-validate] [name='"+requiredFields[i]+"']").each(function( index ) {
    if($(this).is(':radio'))
     {
      if($(this).is(':checked') && $(this).val()!='') passed = true;
     }
    else
     {
      if($(this).val()!='') passed = true;
     }   
    });
    if(!passed)
     {
      error_message += decodeURIComponent(messages[i])+"\n";
      if(requiredFields[i]=='data[_latitude]'||requiredFields[i]=='data[_longitude]') $('.latlong').addClass('has-error danger');
      else $("[data-required='"+requiredFields[i]+"']").addClass('has-error danger');
     }
    else
     {
      if(requiredFields[i]=='data[_latitude]'||requiredFields[i]=='data[_longitude]') $('.latlong').removeClass('has-error danger');
      else $("[data-required='"+requiredFields[i]+"']").removeClass('has-error danger');
     }
   }
  if(error_message)
   {
    $('[data-disable-on-submit]').find(':submit').removeAttr('disabled');
    alert(error_message);
    return false;
   }
  else
   {
    return true;
   }
});


$("#enlargemap").click(function(e) { e.preventDefault(); resizeMap(100); });
$("#reducemap").click(function(e) { e.preventDefault(); resizeMap(-100); });
$("a[data-delete-confirm]").click(function(e) { e.preventDefault();
                                            message = $(this).data('delete-confirm') ? decodeURIComponent($(this).data('delete-confirm')) : 'Delete?';
                                            $(this).parents("tr").addClass('danger');
                                            var confirmed = confirm(decodeURIComponent(message));
                                            if(confirmed) window.location.href = $(this).attr("href") + '&confirmed=true';
                                            $(this).parents("tr").removeClass('danger'); });

$("a[data-confirm]").click(function(e) { e.preventDefault();
                                            message = $(this).data('confirm') ? decodeURIComponent($(this).data('confirm')) : 'Proceed?';
                                            var confirmed = confirm(decodeURIComponent(message));
                                            if(confirmed) window.location.href = $(this).attr("href") + '&confirmed=true'; });

$("#fullscreenmap").click(function(e) { 
                                        initialMapHeight = $('#mapcontainer').height();
                                        e.preventDefault();
                                        $("#mapcontainer").prependTo("body");
                                        $("#wrapper").hide();
                                        $('#mapcontainer').addClass("fullscreen");
                                        $('body').addClass("fullscreen");
                                        $('#mapcontainer').css({'height': $(document).height() + 'px'});
                                        map.updateSize();
                                        $('#mapsizetools').attr("class", "buttongroup-inactive");
                                        $('#disablemap').hide();
                                        $('<a id="resetmap" href="#" title="close fullscreen mode">x</a>').appendTo('#mapcontainer');
                                        $("#resetmap").click(function(e) { e.preventDefault(); resetMap() });
                                        $(document).keyup(function(e) { if(e.keyCode == 27) { resetMap(); }});
                                      });



$(window).resize(function() {
$('.fullscreen').css({'height': $(document).height() + 'px'});
if(typeof map!=='undefined') map.updateSize();
});

$('#helpcontent').hide();
$('#helphandle a').click(function(e) {
e.preventDefault();
$('#helpcontent').slideToggle('fast');
});



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


if (location.hash !== '') $('a[href="' + location.hash + '"]').tab('show');
return $('[data-toggle="tab"]').on('shown', function(e) {
return location.hash = $(e.target).attr('href').substr(1);
});

});
