<?php
if(!defined('IN_INDEX')) exit;

if($permission->granted(Permission::ADMIN))
 {
  switch($action)
   {
    case 'default':
     // available groups:
     $dbr = Database::$connection->prepare("SELECT id,
                                                   name
                                            FROM ".Database::$db_settings['group_table']."
                                            ORDER BY sequence ASC");
     $dbr->execute();
     $i=0;
     while($row = $dbr->fetch()) 
      {
       $available_groups[$i]['id'] = intval($row['id']);
       $available_groups[$i]['name'] = htmlspecialchars($row['name']);
       ++$i;
      }
     if(isset($available_groups)) $template->assign('available_groups', $available_groups);
     if(isset($_GET['success'])) $template->assign('success', $_GET['success']);
     $template->assign('subtitle',$lang['settings_title']);
     $template->assign('subtemplate','settings.inc.tpl');   
     break;

    case 'advanced':
     if(isset($_GET['success'])) $template->assign('success', $_GET['success']);
     $template->assign('subtitle', $lang['settings_advanced_title']);
     $template->assign('subtemplate','settings.advanced.inc.tpl'); 
    break;
  
    case 'submit':
     $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['settings_table']." SET value=:value WHERE name=:name");
      foreach($_POST as $key => $val)
       {
        if(isset($settings[$key]))
        {
         $dbr->bindValue(':name', trim($key), PDO::PARAM_STR);
         $dbr->bindValue(':value', trim($val), PDO::PARAM_STR);
         $dbr->execute();
        }
       }
      header('Location: '.BASE_URL.'?r=settings&success=settings_saved');
      exit;
    break;

    case 'add':
     $dbr = Database::$connection->prepare("INSERT INTO ".Database::$db_settings['settings_table']." (name, value) VALUES (:name, :value)");
     $dbr->bindValue(':name', trim($_POST['name']), PDO::PARAM_STR);
     $dbr->bindValue(':value', trim($_POST['value']), PDO::PARAM_STR);
     $dbr->execute();
     header('Location: '.BASE_URL.'?r=settings.advanced&success=settings_variable_added');
     exit;
    break;
   
   case 'delete':  
    if(isset($_REQUEST['key']) && isset($_REQUEST['confirmed']))
     {
      $dbr = Database::$connection->prepare("DELETE FROM ".Database::$db_settings['settings_table']." WHERE name = :name");
      $dbr->bindValue(':name', $_REQUEST['key'], PDO::PARAM_STR);
      $dbr->execute();      
      header('Location: '.BASE_URL.'?r=settings.advanced&success=settings_variable_deleted');
      exit;
     }
    break;    
   
   
   
   }
 }
?>
