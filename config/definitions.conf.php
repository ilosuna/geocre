<?php

define('JQUERY', '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js');
define('JQUERY_UI', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js');
define('JQUERY_UI_CSS', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.min.css');
define('JQUERY_UI_HANDLER', STATIC_URL.'js/jquery_ui_handler.js');
define('JQUERY_COOKIE', STATIC_URL.'js/jquery.cookie.min.js');

define('BOOTSTRAP', '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js');
define('BOOTSTRAP_CSS', '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css');

define('OPENLAYERS', '//cdnjs.cloudflare.com/ajax/libs/openlayers/2.13.1/OpenLayers.js');
define('OPENLAYERS_CSS', '//cdnjs.cloudflare.com/ajax/libs/openlayers/2.13.1/theme/default/style.css');
define('OPENLAYERS_DATA', STATIC_URL.'js/ol_data.js');
define('OPENLAYERS_DATA_ITEM', STATIC_URL.'js/ol_data_item.js');
define('OPENLAYERS_DATA_ITEM_ATTACHED', STATIC_URL.'js/ol_data_item_attached.js');
define('OPENLAYERS_DRAW', STATIC_URL.'js/ol_draw.js');

define('LIGHTBOX', STATIC_URL.'js/mylightbox.js');

define('WYSIWYG_EDITOR', '//tinymce.cachefly.net/4.1/tinymce.min.js');

define('IMAGES_DIR', 'images/');
define('VALID_URL_CHARACTERS', '/^[a-z0-9._\-\/]+$/');
define('FILES_PATH', BASE_PATH.'files/');

define('PAGE_TEASER_IMAGES_PATH', BASE_PATH.'files/page_images/teaser_images/');
define('PAGE_TEASER_IMAGES_URL', BASE_URL.'files/page_images/teaser_images/');
define('PAGE_IMAGES_PATH', BASE_PATH.'files/page_images/images/');
define('PAGE_IMAGES_URL', BASE_URL.'files/page_images/images/');
define('PAGE_PHOTOS_PATH', BASE_PATH.'files/page_images/photos/');
define('PAGE_PHOTOS_URL', BASE_URL.'files/page_images/photos/');
define('PAGE_THUMBNAILS_PATH', BASE_PATH.'files/page_images/thumbnails/');
define('PAGE_THUMBNAILS_URL', BASE_URL.'files/page_images/thumbnails/');

define('DATA_IMAGES_PATH', BASE_PATH.'files/data_images/images/');
define('DATA_IMAGES_URL', BASE_URL.'files/data_images/images/');
define('DATA_ORIGINAL_IMAGES_PATH', BASE_PATH.'files/data_images/original/');
define('DATA_ORIGINAL_IMAGES_URL', BASE_URL.'files/data_images/original/');
define('DATA_THUMBNAILS_PATH', BASE_PATH.'files/data_images/thumbnails/');
define('DATA_THUMBNAILS_URL', BASE_URL.'files/data_images/thumbnails/');

define('ERROR_LOGFILE', BASE_PATH.'log/error.log');

// column types:
define('CHARACTER_VARYING', 1);
define('INTEGER', 2);
define('SMALLINT', 3);
define('NUMERIC', 4);
define('TEXT', 5);
define('BOOLEAN', 6);
define('DATE', 7);
define('TIME', 8);

// activites:
define('ACTIVITY_LOG_IN', 1);
define('ACTIVITY_LOG_OUT', 2);
define('ACTIVITY_ADD_ITEM', 3);
define('ACTIVITY_EDIT_ITEM', 4);
define('ACTIVITY_DELETE_ITEM', 5);
define('ACTIVITY_ADD_DATA_MODEL', 6);
define('ACTIVITY_EDIT_DATA_MODEL', 7);
define('ACTIVITY_EDIT_DATA_MODEL_STRUCTURE', 8);
define('ACTIVITY_DELETE_DATA_MODEL', 9);
define('ACTIVITY_ADD_DATA_IMAGE', 10);
define('ACTIVITY_EDIT_DATA_IMAGE', 11);
define('ACTIVITY_DELETE_DATA_IMAGE', 12);
define('ACTIVITY_ADD_DATA_ITEM_IMAGE', 13);
define('ACTIVITY_EDIT_DATA_ITEM_IMAGE', 14);
define('ACTIVITY_DELETE_DATA_ITEM_IMAGE', 15);
?>
