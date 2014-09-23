<?php

if(!defined('IN_INDEX')) exit;

if($permission->granted(Permission::ADMIN))
 {
  switch($action)
   {
    case 'default':
     if(file_exists(ERROR_LOGFILE))
      {
       $row = 0;
       if (($handle = fopen(ERROR_LOGFILE, "r")) !== FALSE)
        {
         while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
          {
           $error_log[$row]['id'] = $row;
           $error_log[$row]['time'] = $data[0];
           $error_log[$row]['message'] = htmlspecialchars($data[1]);
           $error_log[$row]['message_short'] = htmlspecialchars(truncate(preg_replace( "/\r|\n/", "", $data[1]),75));
           $error_log[$row]['server'] = htmlspecialchars(print_r(unserialize($data[2]), true));
           $error_log[$row]['request'] = htmlspecialchars(print_r(unserialize($data[3]), true));
           $error_log[$row]['session'] = htmlspecialchars(print_r(unserialize($data[4]), true));
           $row++;
          }
         fclose($handle);
        }     
     
       if(isset($error_log)) $template->assign('error_log', array_reverse($error_log));
      }
     else $template->assign('error_log_disabled', true);
     $template->assign('subtitle', $lang['error_log_title']);
     $template->assign('subtemplate', 'error_log.inc.tpl');
    break;
    
    case 'clear':
     if(isset($_GET['confirmed']) && file_exists(ERROR_LOGFILE))
      {
       $fp = fopen(ERROR_LOGFILE, "w");
       fclose($fp); 
      }
     header('Location: '.BASE_URL.'?r=error_log');
     exit;
   }
 }
?>
