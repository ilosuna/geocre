<?php

if(!defined('IN_INDEX')) exit;

$settings['status_items_per_page'] = 20;

if($permission->granted(Permission::ADMIN))
 {
  switch($action)
   {
    case 'default':
     $total_count_result = Database::$connection->query("SELECT COUNT(*) FROM ".Database::$db_settings['log_table']);
     list($total_items) = $total_count_result->fetch();
     $total_pages = ceil($total_items / $settings['status_items_per_page']);
     // get current page:
     $p = isset($_GET['p']) ? intval($_GET['p']) : 1;
     if($p<1) $p=1;
     if($total_pages>0 && $p>$total_pages) $p = $total_pages;
     $offset = ($p-1) * $settings['status_items_per_page'];  
     $dbr = Database::$connection->prepare("SELECT a.id,
                                                   a.user,
                                                   a.action,
                                                   a.table,
                                                   a.item,
                                                   a.previous_data,
                                                   extract(epoch FROM a.time) as timestamp,
                                                   b.id AS userid,
                                                   b.name AS username,  
                                                   c.title AS table_title
                                            FROM ".Database::$db_settings['log_table']." AS a
                                            LEFT JOIN ".Database::$db_settings['userdata_table']." AS b ON a.user=b.id
                                            LEFT JOIN ".Database::$db_settings['data_models_table']." AS c ON a.table=c.id
                                            ORDER BY a.id DESC
                                            LIMIT ".$settings['status_items_per_page']."
                                            OFFSET ".$offset);
     $dbr->execute();
     $i=0;
     foreach($dbr as $row)
      {
       $status[$i]['id'] = $row['id'];
       $status[$i]['username'] = htmlspecialchars($row['username']);
       $status[$i]['userid'] = intval($row['userid']);
       $status[$i]['action'] = $row['action'];
       $status[$i]['table'] = $row['table'];
       $status[$i]['table_title'] = htmlspecialchars($row['table_title']);
       $status[$i]['item'] = $row['item'];
       #$status[$i]['message'] = make_link(htmlspecialchars($row['message']));
       $status[$i]['time'] = htmlspecialchars(strftime($lang['time_format'], $row['timestamp']));
       $status[$i]['minutes_ago'] = ceil((time()-intval($row['timestamp']))/60);
       $hours = floor($status[$i]['minutes_ago']/60);
       $minutes_remainder = intval($status[$i]['minutes_ago'])-intval($hours*60);
       if($minutes_remainder<10) $minutes_remainder = '0'.$minutes_remainder;;
       $status[$i]['hours_ago'] = $hours.':'.$minutes_remainder;
       ++$i;
      }

     if(isset($status)) $template->assign('status', $status);
     $template->assign('pagination', pagination($total_pages, $p));      
     $template->assign('subtitle', $lang['log_title']); 
     $template->assign('subtemplate', 'log.inc.tpl');
     break;

    case 'details':
     if(isset($_GET['id']))
      {
       $dbr = Database::$connection->prepare("SELECT a.id,
                                                   a.user,
                                                   a.action,
                                                   a.table,
                                                   a.item,
                                                   a.previous_data,
                                                   extract(epoch FROM a.time) as timestamp,
                                                   b.id AS userid,
                                                   b.name AS username,  
                                                   c.table_name AS table_name,
                                                   c.title AS table_title
                                            FROM ".Database::$db_settings['log_table']." AS a
                                            LEFT JOIN ".Database::$db_settings['userdata_table']." AS b ON a.user=b.id
                                            LEFT JOIN ".Database::$db_settings['data_models_table']." AS c ON a.table=c.id
                                            WHERE a.id=:id
                                            LIMIT 1");
       $dbr->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
       $dbr->execute();
       $row = $dbr->fetch();
       if(isset($row['id']))
        {
         if($row['table_name'] && $row['item'])
          {
           $dbr = Database::$connection->query(LIST_TABLES_QUERY);
           foreach($dbr as $table) $existing_tables[] = $table['name'];
           
           if(in_array($row['table_name'], $existing_tables))
            {  
             $dbr = Database::$connection->prepare('SELECT * FROM "'.$row['table_name'].'" WHERE id=:id LIMIT 1');
             $dbr->bindParam(':id', $row['item'], PDO::PARAM_INT);
             $dbr->execute();
             $log_data['current_data'] = $dbr->fetch(PDO::FETCH_ASSOC);         
             if(empty($log_data['current_data'])) unset($log_data['current_data']);
            }
          }
         
         
         $log_data['id'] = $row['id'];
         $log_data['userid'] = $row['userid'];
         $log_data['item'] = $row['item'];
         $log_data['table'] = $row['table'];
         $log_data['table_title'] = htmlspecialchars($row['table_title']);
         $log_data['username'] = htmlspecialchars($row['username']);
         $log_data['action'] = $row['action'];
         $log_data['time'] = htmlspecialchars(strftime($lang['time_format'], $row['timestamp']));
         if($row['previous_data']) $log_data['previous_data'] = unserialize($row['previous_data']);
         
         if(isset($log_data['current_data']) && isset($log_data['previous_data'])) $log_data['difference'] = array_diff_assoc($log_data['current_data'], $log_data['previous_data']);
         
         $template->assign('subtitle', $lang['log_details_title']);
         $template->assign('log_data', $log_data); 
         $template->assign('subtemplate', 'log.details.inc.tpl'); 
        }
 
      }
     break;
    
   }
 }
?>
