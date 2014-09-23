<?php
if(!defined('IN_INDEX')) exit;

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
          $records_count = 0;
          $data[$i]['available'] = false;
         }
        $data[$i]['id'] = $row['id'];
        $data[$i]['title'] = htmlspecialchars($row['title']);
        $data[$i]['type'] = intval($row['type']);
        #if(isset($records_count)) $data[$i]['records'] = $records_count;
        #else $data[$i]['records'] = 0;
        $data[$i]['records'] = $records_count;
        $data[$i]['status'] = intval($row['status']);
        $data[$i]['parent_table'] = intval($row['parent_table']);
        $data[$i]['project'] = htmlspecialchars($row['project']);
        if($row['project']) $template->assign('projects', true);
        if($row['readonly']==0 && ($permission->granted(Permission::DATA_MANAGEMENT)||$permission->granted(Permission::DATA_ACCESS, $row['id'], Permission::WRITE))) $data[$i]['edit'] = true;
        else $data[$i]['edit'] = false;
        if($permission->granted(Permission::DATA_MANAGEMENT)||$permission->granted(Permission::DATA_ACCESS, $row['id'], Permission::MANAGE)) $data[$i]['manage'] = true;
        else $data[$i]['manage'] = false;
        ++$i;
       }
      if(isset($data)) $template->assign('data', $data);
     }
   } // switch

  if($permission->granted(Permission::DATA_MANAGEMENT))
   {
    $javascripts[] = JQUERY_UI;
    $javascripts[] = JQUERY_UI_HANDLER;
   }
   
  $lang['dashboard_headline'] = str_replace('[user]', htmlspecialchars($_SESSION[$settings['session_prefix'].'auth']['name']), $lang['dashboard_headline']);
  $template->assign('active', 'dashboard');
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
