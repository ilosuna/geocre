<?php
if(!defined('IN_INDEX')) exit;

$settings['status_items_per_page'] = 20;

if($permission->granted(Permission::USER))
 {

  switch($action)
   {
    case 'default':
    // data: 
    if($permission->granted(Permission::DATA_MANAGEMENT))
     {
      // count:
      $count_result = Database::$connection->query('SELECT COUNT(*) FROM "'.Database::$db_settings['data_models_table'].'"');
      list($data_stock_count) = $count_result->fetch();

      $total_pages = ceil($data_stock_count / $settings['data_stocks_per_page']);
       
      // get current page:
      $dsp = isset($_GET['dsp']) ? intval($_GET['dsp']) : 1;
      if($dsp<1) $dsp=1;
      if($total_pages>0 && $dsp>$total_pages) $dsp = $total_pages;
      $template->assign('dsp', $dsp);
      $template->assign('data_stock_count', $data_stock_count);     
      if(isset($_GET['show_all']))
       {
        $limit = $data_stock_count;
        $offset = 0;
        $template->assign('data_stock_pagination', false);   
       } 
      else
       {
        $limit = $settings['data_stocks_per_page'];
        $offset = ($dsp-1) * $settings['data_stocks_per_page'];
        $template->assign('data_stock_pagination', pagination($total_pages, $dsp));   
       }
      
      $data_query = 'SELECT data.id,
                            data.table_name,
                            data.title,
                            data.type,
                            data.status,
                            data.readonly,
                            data.parent_table,
                            page.title as project
                FROM "'.Database::$db_settings['data_models_table'].'" AS data
                LEFT JOIN "'.Database::$db_settings['pages_table'].'" AS page ON data.project=page.id
                ORDER BY data.sequence ASC
                LIMIT '.$limit.' OFFSET '.$offset;
     }
    elseif($items = $permission->get_list(Permission::DATA_ACCESS))
     {
      $items_list = implode(', ', $items);

      $count_result = Database::$connection->query('SELECT COUNT(*) FROM "'.Database::$db_settings['data_models_table'].'" WHERE status>0 AND id IN ('.$items_list.')');
      list($data_stock_count) = $count_result->fetch();
      
      $data_query = 'SELECT data.id,
                            data.table_name,
                            data.title,
                            data.type,
                            data.status,
                            data.readonly,
                            data.parent_table,
                            page.title as project
                       FROM "'.Database::$db_settings['data_models_table'].'" AS data
                       LEFT JOIN "'.Database::$db_settings['pages_table'].'" AS page ON data.project=page.id
                       WHERE data.status>0 AND data.id IN ('.$items_list.')
                       ORDER BY data.sequence ASC';
     }
    if(isset($data_query))
     {    
      $dbr = Database::$connection->query($data_query);
      $displayed_items = $dbr->rowCount();
      $lang['displayed_data_stocks_label'] = str_replace('[total]', $data_stock_count, str_replace('[displayed]', $displayed_items, $lang['displayed_data_stocks_label']));
      $i=0;
      while($row = $dbr->fetch())
       {
        try
         {
          $cr = Database::$connection->query('SELECT COUNT(*) FROM "'.$row['table_name'].'"');  
          list($records_count) = $cr->fetch();
          $data[$i]['available'] = true;
         }
        catch(Exception $exception)
         {
          $data[$i]['available'] = false;
         }
        $data[$i]['id'] = $row['id'];
        $data[$i]['title'] = htmlspecialchars($row['title']);
        $data[$i]['type'] = intval($row['type']);
        $data[$i]['records'] = $records_count;
        $data[$i]['status'] = intval($row['status']);
        $data[$i]['parent_table'] = intval($row['parent_table']);
        $data[$i]['project'] = htmlspecialchars($row['project']);
        if($row['readonly']==0 && ($permission->granted(Permission::DATA_MANAGEMENT)||$permission->granted(Permission::DATA_ACCESS, $row['id'], Permission::WRITE))) $data[$i]['edit'] = true;
        else $data[$i]['edit'] = false;
        if($permission->granted(Permission::DATA_MANAGEMENT)||$permission->granted(Permission::DATA_ACCESS, $row['id'], Permission::MANAGE)) $data[$i]['manage'] = true;
        else $data[$i]['manage'] = false;
        ++$i;
       }
      if(isset($data)) $template->assign('data', $data);
     }
    
    // profile:
    /*
    $dbr = Database::$connection->prepare("SELECT id, name, real_name, email, language, time_zone FROM ".Database::$db_settings['userdata_table']." WHERE id=:id LIMIT 1");
    $dbr->bindValue(':id', $_SESSION[$settings['session_prefix'].'auth']['id']);
    $dbr->execute();
    $row = $dbr->fetch();
    if(isset($row['id']))
     {
      $profile['id'] = $row['id'];
      $profile['name'] = htmlspecialchars($row['name']);
      $profile['real_name'] = htmlspecialchars($row['real_name']);
      $profile['email'] = htmlspecialchars($row['email']);
      $profile['language'] = htmlspecialchars(get_language_name($row['language']));
      if($row['time_zone']) $profile['time_zone'] = htmlspecialchars($row['time_zone']);
      else $profile['time_zone'] = $settings['time_zone'];
      $template->assign('profile', $profile);
     }       
    
    // groups:
    $dbr = Database::$connection->prepare("SELECT groups.name AS group_name
                                           FROM ".Database::$db_settings['group_memberships_table']." AS memberships
                                           LEFT JOIN ".Database::$db_settings['group_table']." AS \"groups\" ON memberships.\"group\"=\"groups\".id
                                           WHERE memberships.user=:user
                                           ORDER BY groups.sequence ASC");
    $dbr->bindParam(':user', $profile['id'], PDO::PARAM_INT);
    $dbr->execute();
    while($row = $dbr->fetch())
     {
      $groups[] = $row['group_name'];
     }
    if(isset($groups)) $template->assign('groups', $groups);
    */
     
    // activity:  
    $total_count_result = Database::$connection->query("SELECT COUNT(*) FROM ".Database::$db_settings['status_table']);
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
                                              a.message,
                                              extract(epoch FROM a.time) as timestamp,
                                              b.name AS username,
                                              c.title AS table_title
                                       FROM ".Database::$db_settings['status_table']." AS a
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
      $status[$i]['action'] = $row['action'];
      $status[$i]['table'] = $row['table'];
      $status[$i]['table_title'] = htmlspecialchars($row['table_title']);
      $status[$i]['item'] = $row['item'];
      $status[$i]['message'] = make_link(htmlspecialchars($row['message']));
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
     
    if(isset($_GET['success'])) $template->assign('success', htmlspecialchars($_GET['success']));
    elseif(isset($_GET['failure'])) $template->assign('failure', htmlspecialchars($_GET['failure']));

    $template->assign('active', 'dashboard'); 
     
    break;
    

  
   case 'status':
  
    if(isset($_POST['status_message']))
     {
      log_status($_POST['status_message']);
      header('Location: '.BASE_URL.'?r=dashboard#activity');
      exit;
     }
    if(isset($_REQUEST['delete_status_item']) && $permission->granted(Permission::ADMIN))
     {
      // delete item:
      $dbr = Database::$connection->prepare("DELETE FROM ".Database::$db_settings['status_table']." WHERE id=:id");
      $dbr->bindValue(':id', $_REQUEST['delete_status_item'], PDO::PARAM_INT);
      $dbr->execute();
      header('Location: '.BASE_URL.'?r=dashboard#activity');
      exit;
     }
    break;    
  
  
 } // switch

  $javascripts[] = JQUERY_UI;
  $javascripts[] = JQUERY_UI_HANDLER;

  $lang['dashboard_headline'] = str_replace('[user]', htmlspecialchars($_SESSION[$settings['session_prefix'].'auth']['name']), $lang['dashboard_headline']);
  $template->assign('help', 'dashboard');
  $template->assign('subtitle', $lang['dashboard_subtitle']);
  $template->assign('subtemplate', 'dashboard.inc.tpl');
 }
else
 {
  $template->assign('subtitle',$lang['login_subtitle']); 
  $template->assign('subtemplate','login.inc.tpl');   
 }
?>
