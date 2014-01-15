<?php
/*
This controller poerforms different asynchronous requests / background processes
like saving user settings or fetching autocomplete data.  
*/

if(!defined('IN_INDEX')) exit;

#$file = BASE_PATH . 'logs/ajax.log';
#$time = date(DATE_RFC822);
#$log_message = print_r($_REQUEST, true);
#file_put_contents($file, $log_message);
  
switch($action)
 {
  case 'set_user_setting':
   if($permission->granted(Permission::USER))
    { 
     require(BASE_PATH.'config/user_settings.conf.php');
     foreach($user_settings as $user_setting)
      {
       if(isset($_REQUEST[$user_setting['name']]))
        {
         switch($user_setting['type'])
          {
           case 0:
            $usersettings[$user_setting['name']] = intval($_REQUEST[$user_setting['name']]);
            break;
           default:
            $usersettings[$user_setting['name']] = trim($_REQUEST[$user_setting['name']]);
          }
        }
       elseif(isset($_SESSION[$settings['session_prefix'].'usersettings'][$user_setting['name']]))
        {
         $usersettings[$user_setting['name']] = $_SESSION[$settings['session_prefix'].'usersettings'][$user_setting['name']];
        }
      }
     $_SESSION[$settings['session_prefix'].'usersettings'] = $usersettings;
     $usersettings_serialized = serialize($usersettings);
     $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['userdata_table']." SET settings=:settings WHERE id=:id");
     $dbr->bindParam(':settings', $usersettings_serialized, PDO::PARAM_STR);
     $dbr->bindParam(':id', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
     $dbr->execute();
    }
   break;

   case 'related_data_autocomplete':
    // Permission is granted to all registered users although the user isn't permitted to acces the table!
    if($permission->granted(Permission::USER) && isset($_REQUEST['column']) && isset($_REQUEST['term']) && mb_strlen($_REQUEST['term'])>=$settings['autocomplete_min_length'])
     {
      // get table and column:
      $dbr = Database::$connection->prepare("SELECT b.id as table_id, b.table_name, a.name as column_name, a.relation_column as relation_column
                                             FROM ".Database::$db_settings['data_model_items_table']." AS a
                                             JOIN ".Database::$db_settings['data_models_table']." AS b ON a.table_id=b.id
                                             WHERE a.id=:id AND b.status>0
                                             LIMIT 1");
      $dbr->bindParam(':id', $_REQUEST['column'], PDO::PARAM_INT);
      $dbr->execute();
      $table_data = $dbr->fetch();
      if(isset($table_data['table_id']) && $table_data['relation_column'])
       {
        $dbr = Database::$connection->prepare("SELECT id, ".$table_data['column_name']."
                                               FROM ".$table_data['table_name']."
                                               WHERE LOWER(".$table_data['column_name'].") LIKE LOWER(:term)
                                               ORDER BY ".$table_data['column_name']." ASC
                                               LIMIT 10");
        // first, search from beginning of value on:
        $dbr->bindValue(':term', $_REQUEST['term'].'%', PDO::PARAM_STR);
        $dbr->execute();    
        // if nothing is found, search within value:
        if($dbr->rowCount()==0)
         {
          $dbr->bindValue(':term', '%'.$_REQUEST['term'].'%', PDO::PARAM_STR);
          $dbr->execute();    
         }
        $i=0;
        while($row = $dbr->fetch()) 
         {
          $items[$i]['id'] = $row['id'];
          $items[$i]['label'] = $row[$table_data['column_name']];     
          ++$i;
         }   
        if(isset($items)) echo json_encode($items);
       }  
     }

    break;
   }       

exit;
?>
