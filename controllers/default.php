<?php
if(!defined('IN_INDEX')) exit;

if($permission->granted(Permission::ADMIN))
 {
  switch($action)
   {
    case 'default':
     $template->assign('subtitle', $lang['default_title']);
     $template->assign('subtemplate', 'default.inc.tpl');         
     break;
   }
 }
?>
