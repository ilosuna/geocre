<div id="pageoptions"><a href="<?php echo BASE_URL; ?>?r=photos.upload" class="upload-photo"><?php echo $lang['upload_photo_link']; ?></a></div>


<h1><?php echo $lang['photos_subtitle']; ?></h1>

<?php if(isset($photos)): ?>

<div id="mapcontainer" class="photomap"></div>

<?php if($pagination): ?>
<ul class="pagination">
<?php if($pagination['previous']): ?><li><a href="<?php echo BASE_URL; ?>?r=photos&amp;p=<?php echo $pagination['previous']; ?>&amp;order=<?php echo $order; ?>&amp;asc=<?php echo $asc; ?>" title="<?php echo $lang['previous_page_title']; ?>"><?php echo $lang['previous_page_button']; ?></a></li><?php endif; ?>
<?php foreach($pagination['items'] as $item): ?>
<?php if($item==0): ?><li>&hellip;</li><?php elseif($item==$pagination['current']): ?><li><span class="current"><?php echo $item; ?></span></li><?php else: ?><li><a href="<?php echo BASE_URL; ?>?r=photos&amp;p=<?php echo $item; ?>&amp;order=<?php echo $order; ?>&amp;asc=<?php echo $asc; ?>"><?php echo $item; ?></a></li><?php endif; ?>
<?php endforeach; ?>
<?php if($pagination['next']): ?><li><a href="<?php echo BASE_URL; ?>?r=photos&amp;p=<?php echo $pagination['next']; ?>&amp;order=<?php echo $order; ?>&amp;asc=<?php echo $asc; ?>" title="<?php echo $lang['next_page_title']; ?>"><?php echo $lang['next_page_button']; ?></a></li><?php endif; ?>  
</ul>
<?php endif; ?>


<script type="text/javascript">
myLightboxSettings = new Object();
myLightboxSettings['vertical_align'] =      'center'; // 'center' or number of pixels from top
myLightboxSettings['next_link'] =           '[&raquo;]';
myLightboxSettings['next_link_title'] =     'next';
myLightboxSettings['previous_link']       = '[&laquo;]';
myLightboxSettings['previous_link_title'] = 'previous';
myLightboxSettings['close_link'] =          '[x]';
myLightboxSettings['close_link_title'] =    'close';
myLightboxSettings['html_box'] = '<div id="mylightbox">\
<div id="mylightbox-header">\
<div id="mylightbox-title"></div>\
<div id="mylightbox-nav"></div>\
<div id="mylightbox-controls"><a href="#" id="mylightbox-close" class="close_button" title="'+myLightboxSettings['close_link_title']+'" onclick="return false">'+myLightboxSettings['close_link']+'</a></div>\
</div>\
<div id="mylightbox-photo"></div>\
<p id="mylightbox-description"></p>\
</div>';
myLightboxSettings['html_background'] = '<div id="mylightbox-background"></div>';

<?php include(BASE_PATH.'templates/subtemplates/default_map.inc.tpl'); ?>
var featuresStyle = new OpenLayers.StyleMap({
"default": new OpenLayers.Style({
pointRadius: 3,
externalGraphic: '<?php echo STATIC_URL; ?>img/marker_photo.png',
graphicWidth: 25,
graphicHeight: 41,
graphicYOffset: -41,
graphicOpacity: 1,
graphicTitle: "${label}",
cursor: "pointer"
})
});

vectorLayer = new OpenLayers.Layer.Vector("<?php echo $subtitle; ?>", { styleMap:featuresStyle });

var map = new OpenLayers.Map('mapcontainer', { projection:projDisplay, controls:[new OpenLayers.Control.Zoom(), new OpenLayers.Control.ScaleLine()] });
map.addLayers([<?php echo $basemaps; ?>, vectorLayer]);
nav = new OpenLayers.Control.Navigation({'zoomWheelEnabled': false});
map.addControl(nav);
map.addControl(new OpenLayers.Control.LayerSwitcher());


var showPopup = function(event) {
                var feature = event.feature;
                feature.popup = new OpenLayers.Popup.FramedCloud("pop",feature.geometry.getBounds().getCenterLonLat(),null,'<div><img src="<?php echo BASE_URL.THUMBNAILS_DIR; ?>'+feature.attributes.filename+'" /></div>',null,true);
                map.addPopup(feature.popup);

            };

var hidePopup = function(event) {
var feature = event.feature;
map.removePopup(feature.popup);
feature.popup.destroy();
feature.popup = null;
}

<?php $i=1; $geometries=false; foreach($photos as $photo): ?>
<?php if(!empty($photo['wkt'])): ?>
var polygonFeature<?php echo $i; ?> = new OpenLayers.Format.WKT({'internalProjection':projDisplay,'externalProjection':projData}).read('<?php echo $photo['wkt']; ?>');
polygonFeature<?php echo $i; ?>.attributes['id'] = '<?php echo $photo['id']; ?>';
polygonFeature<?php echo $i; ?>.attributes['filename'] = '<?php echo addslashes($photo['filename']); ?>';
polygonFeature<?php echo $i; ?>.attributes['label'] = '<?php echo ol_encode_label($photo['title']); ?>';
polygonFeature<?php echo $i; ?>.attributes['title'] = '<?php echo rawurlencode($photo['title']); ?>';
polygonFeature<?php echo $i; ?>.attributes['description'] = '<?php echo rawurlencode($photo['description']); ?>';
polygonFeature<?php echo $i; ?>.attributes['username'] = '<?php echo rawurlencode(truncate($photo['username'],10,true)); ?>';
polygonFeature<?php echo $i; ?>.attributes['time'] = '<?php echo rawurlencode($photo['time']); ?>';


vectorLayer.addFeatures([polygonFeature<?php echo $i; ?>]);
<?php $geometries=true; endif; ?>
<?php ++$i; endforeach; ?>

<?php if($geometries): ?>
map.zoomToExtent(vectorLayer.getDataExtent());
<?php else: ?>
map.setCenter(new OpenLayers.LonLat(<?php echo $settings['default_longitude']; ?>,<?php echo $settings['default_latitude']; ?>).transform(projData, projDisplay), <?php echo $settings['default_zoomlevel']; ?>);
<?php endif; ?>
selectControl = new OpenLayers.Control.SelectFeature(
                [ vectorLayer ],
                { clickout:true, toggle:true, multiple:false, hover:false } );
map.addControl(selectControl);
selectControl.activate();

/* allows dragging map on features: */
if(typeof(selectControl.handlers) != "undefined") selectControl.handlers.feature.stopDown = false;


vectorLayer.events.on({
featureselected: function(event) {
//var feature = event.feature;
//photoPopup(feature);
var feature = event.feature;
var popupHTML  = '<div id="photopopwrap">\
<a class="close_button" href="#" title="<?php echo $lang['close']; ?>"><?php echo $lang['close']; ?></a>\
<a id="photopopuplink" href="<?php echo BASE_URL.LARGE_PHOTOS_DIR; ?>'+feature.attributes.filename+'" title="<?php echo $lang['photo_enlarge']; ?>"><img src="<?php echo BASE_URL.SMALL_PHOTOS_DIR; ?>'+feature.attributes.filename+'" /></a>\
<div id="photodesc"><p><strong>'+decodeURIComponent(feature.attributes.title)+'</strong>';
if(feature.attributes.description!='') popupHTML += '<br />'+decodeURIComponent(feature.attributes.description);
popupHTML += '</p></div>';

popupHTML += '<div class="meta"><span class="info">'+decodeURIComponent(feature.attributes.username)+', '+decodeURIComponent(feature.attributes.time)+'</span>';
popupHTML += '<span class="options">[ <a href="<?php echo BASE_URL; ?>?r=photos&amp;edit='+feature.attributes.id+'" title="<?php echo $lang['edit']; ?>"><?php echo $lang['edit']; ?></a> | <a href="<?php echo BASE_URL; ?>?r=photos&amp;delete='+feature.attributes.id+'" id="deletephoto" title="<?php echo $lang['delete']; ?>"><?php echo $lang['delete']; ?></a> ]</span></div>';

                feature.popup = new OpenLayers.Popup.FramedCloud("pop",feature.geometry.getBounds().getCenterLonLat(),null,popupHTML,null,false);
                map.addPopup(feature.popup);
$("#photopopuplink").click(function(e) { e.preventDefault(); photoPopup(feature); });
$("#deletephoto").click(function(e) { return delete_confirm(this, '<?php echo rawurlencode($lang['delete_photo_message']); ?>'); });
$("#photopopwrap .close_button").click(function(e) { e.preventDefault(); selectControl.unselectAll(); });
},
featureunselected: function(event) {
//var feature = event.feature;
var feature = event.feature;
map.removePopup(feature.popup);
feature.popup.destroy();
feature.popup = null;
}
});

<?php if(isset($id)): ?>
for(var i = 0; i < vectorLayer.features.length; ++i) if(vectorLayer.features[i].attributes.id==<?php echo $id; ?>) selectControl.select(this.vectorLayer.features[i]);
<?php endif; ?>

function centerPopup(width, height)
 {
  if(myLightboxSettings['vertical_align']=='center')
   {
    var top = $(window).scrollTop()+$(window).height()/2-height/2;
   }
  else
   {
    var top = $(window).scrollTop()+myLightboxSettings['vertical_align'];
   }
  var left = $(window).width()/2-width/2;
  if(top<0) top=0;
  if(left<0) left=0;
   $("#mylightbox").css({
   "position": "absolute",
   "top": top+'px',
   "left": left+'px'
  });
 }

function closePhotoPopup()
 {
  $("#mylightbox").fadeOut("fast", remove);
  $("#mylightbox-background").fadeOut("fast", remove);
  delete myLightboxCurrentWidth;
}

function remove()
 {
  $(this).remove();
 }

function photoPopup(feature)
 {
    var src = './'+ '<?php echo LARGE_PHOTOS_DIR; ?>' + feature.attributes.filename;
     
    $("body").append(myLightboxSettings['html_background']);
    $("body").append(myLightboxSettings['html_box']);
    
    $("#mylightbox-background").css({
	   "opacity": "0.7"
    });

    $("#mylightbox-background").fadeIn("fast");
    $("#mylightbox").fadeIn("fast");
		
    // center popup on window resize:
    $(window).bind('resize', function() {
     centerPopup($("#mylightbox").width(),$("#mylightbox").height());
    });
    
    // close on click on close button:
    $("#mylightbox-close").click(function(){
     closePhotoPopup();
    });

    // close on click on background:
    $("#mylightbox-background").click(function(){
     closePhotoPopup();
    });

    // close on ESC key
    $(document).keypress(function(e){
     if(e.keyCode==27){
      closePhotoPopup();
     }
    });   

  $("#mylightbox #mylightbox-title").html('');
  $("#mylightbox-photo").html('<div id="mylightbox-throbber"></div>');
  $("#mylightbox-description").html('');

  if(typeof(myLightboxCurrentWidth)=='undefined') centerPopup($("#mylightbox").outerWidth(), $("#mylightbox").outerHeight());

  var objImagePreloader = new Image();
  objImagePreloader.onload = function() 
   {

    $("#mylightbox #mylightbox-title").html(decodeURIComponent(feature.attributes.title));
    $("#mylightbox-photo").hide();
    $("#mylightbox-photo").html('<img src="'+src+'" />');      
    
    //$("#mylightbox-description").html(description);
    $("#mylightbox-photo").fadeIn("fast");

    myLightboxCurrentWidth = objImagePreloader.width;
    $("#mylightbox").css({"width":myLightboxCurrentWidth+'px'}); 
    
    centerPopup($("#mylightbox").outerWidth(), $("#mylightbox").outerHeight());
   };
  objImagePreloader.src = src;
 
  selectControl.unselectAll();
 }
</script>

<?php else: ?>

<p><?php echo $lang['no_photos_available']; ?></p>

<?php endif; ?>
