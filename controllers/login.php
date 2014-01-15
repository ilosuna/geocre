<?php
if(!defined('IN_INDEX')) exit;

if(isset($_SESSION[$settings['session_prefix'].'auth'])) $action = 'logout';
elseif(isset($_POST['email']) && trim($_POST['email'])!='' && isset($_POST['pw']) && trim($_POST['pw'])!='') $action = 'login';

$template->assign('active', 'login');          

switch($action)
 {   
  case 'default':
   if(isset($_GET['reset_pw'])) $template->assign('reset_pw', true); 
   if(isset($_GET['success'])) $template->assign('success', htmlspecialchars($_GET['success']));
   $template->assign('subtitle',$lang['login_subtitle']); 
   $template->assign('subtemplate','login.inc.tpl');   
   break;

  case 'login':
   $email = isset($_POST['email']) ? trim($_POST['email']) : '';
   $pw = isset($_POST['pw']) ? trim($_POST['pw']) : '';
   if($email && $pw)
    {
     $dbr = Database::$connection->prepare("SELECT id, name, pw, type, language, time_zone, settings FROM ".Database::$db_settings['userdata_table']." WHERE LOWER(email)=LOWER(:email) LIMIT 1");
     $dbr->bindParam(':email', $email);
     $dbr->execute();
     $row = $dbr->fetch();
     if(isset($row['id']))
      {
       if(check_pw($pw, $row['pw']))
        {
         $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['userdata_table']." SET logins=logins+1, last_login=NOW() WHERE id=:id");
         $dbr->bindValue(':id', $row['id']);
         $dbr->execute();
         $_SESSION[$settings['session_prefix'].'auth']['id'] = $row['id'];
         $_SESSION[$settings['session_prefix'].'auth']['type'] = $row['type'];
         #$_SESSION[$settings['session_prefix'].'auth']['email'] = htmlspecialchars($row['email']);
         $_SESSION[$settings['session_prefix'].'auth']['name'] = htmlspecialchars($row['name']);
         if($row['language']) $_SESSION[$settings['session_prefix'].'language'] = $row['language'];
         if($row['time_zone']) $_SESSION[$settings['session_prefix'].'time_zone'] = $row['time_zone'];
         if($row['settings']) $_SESSION[$settings['session_prefix'].'usersettings'] = unserialize($row['settings']);
         log_status(NULL, 1);
         header('Location: '.BASE_URL.'?r=dashboard');
         exit;
        }
       else
        {
         // simple brute force prevention:
         sleep(2);
         $errors[] = 'login_failed';
        }
      }
     else
      {
       // simple brute force prevention:
       sleep(2);       
       $errors[] = 'login_failed';
      }
     if(isset($errors))
      {
       $template->assign('email', htmlspecialchars($email)); 
       $template->assign('errors', $errors); 
       $template->assign('subtitle', $lang['login_subtitle']); 
       $template->assign('subtemplate', 'login.inc.tpl');          
      }
    
    }
   else
    {
     header('Location: '.BASE_URL.'?r=login');
     exit;     
    }
   break;
  
  case 'logout':
   if(isset($_SESSION[$settings['session_prefix'].'auth']))
    {
     log_status(NULL, 2);
     unset($_SESSION[$settings['session_prefix'].'auth']);
     unset($_SESSION[$settings['session_prefix'].'usersettings']);
     header('Location: '.BASE_URL);
     exit;
    }    
   break; 
 }
?>
