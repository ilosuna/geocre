<?php
if(!defined('IN_INDEX')) exit;

else require(BASE_PATH.'lang/'.$lang['help_file']);

if(isset($help[$action]))
 {
  $help_message['title'] = $help[$action]['title'];
  $help_message['content'] = $help[$action]['content'];
 }
else
 {
  $help_message['title'] = $help['default']['title'];
  $help_message['content'] = $help['default']['content'];
 }

$template->assign('help', $help_message);
$page_template = 'subtemplates/help.inc.tpl';         
?>
