<?php
if(!defined('IN_INDEX')) exit;

if(isset($_REQUEST['initial_item']) && isset($_REQUEST['initial_table']) && isset($_REQUEST['selected_table']) && ($permission->granted(Permission::DATA_MANAGEMENT) || ($permission->granted(Permission::DATA_ACCESS, intval($_REQUEST['initial_table']), Permission::WRITE)) && ($permission->granted(Permission::DATA_ACCESS, intval($_REQUEST['selected_table']), Permission::WRITE)) ))
 {
  $initial_table_info = get_table_info($_REQUEST['initial_table'], true);
  $table_info = get_table_info($_REQUEST['selected_table'], true);
  if($initial_table_info && $table_info)
   {
    // check initial item:
    $dbr = Database::$connection->prepare("SELECT COUNT(*)
                                                  FROM \"".$initial_table_info['table']['table_name']."\"
                                                  WHERE id=:id");
    $dbr->bindParam(':id', $_REQUEST['initial_item'], PDO::PARAM_INT);
    $dbr->execute();
    list($item_count) = $dbr->fetch();
    if($item_count == 1)
     {
      $template->assign('initial_item', intval($_REQUEST['initial_item']));
      $template->assign('initial_table', intval($_REQUEST['initial_table']));
      $template->assign('initial_table_name', htmlspecialchars($initial_table_info['table']['title']));
      $template->assign('selected_table', intval($_REQUEST['selected_table']));
    
  switch($action)
   {
    case 'default':
     if(isset($table_info['columns']))
      {
       $template->assign('columns', $table_info['columns']);  
       $i=0;
       foreach($table_info['columns'] as $column)
        {
         $column_names[] = $column['name'];
         if($column['type']>0) $select_query_parts[] = 'table'.$table_info['table']['id'].'.'.$column['name'];
         
         if($column['relation'])
          {
           $joined_tables[] = $table_info['table']['id'];
           $joins[$i]['table'] = $table_info['table']['id'];
           $joins[$i]['alias'] = 'table'.$table_info['table']['id'].'_'.$i; // unique table alias
           $joins[$i]['relation_table'] = $column['relation_table'];
           $joins[$i]['relation_table_name'] = $column['relation_table_name'];
           $joins[$i]['relation_column_name'] = $column['name'];
           $joins[$i]['fk'] = $column['name'];
           $select_query_parts[] = 'table'.$table_info['table']['id'].'.'.$column['name'].' AS _'.$column['name'].'_';
           $select_query_parts[] = $joins[$i]['alias'].'.'.$column['relation_column_name'].' AS '.$column['name'];
          }       
         ++$i;
        }
       $select_query = ', ' . implode(', ', $select_query_parts);
      } 
     else
      {
       $select_query = '';
      }
     
     // check if table exists:
     $check_result = Database::$connection->query(LIST_TABLES_QUERY);
     foreach($check_result as $table)
      {
       $tables[] = $table['name'];
      }  

     if(in_array($table_info['table']['table_name'], $tables))
      {
       $template->assign('table_exists', true);
       $total_count_result = Database::$connection->query('SELECT COUNT(*) FROM "'.$table_info['table']['table_name'].'"');
       list($total_items) = $total_count_result->fetch();
      
       if($table_info['table']['type']==1) // spatial data
        {
         $spatial_count_result = Database::$connection->query("SELECT COUNT(*) as total_items,
                                                   SUM(area) as total_area, ST_AsText(ST_Extent(geom)) as extent
                                                   FROM \"".$table_info['table']['table_name']."\"
                                                   WHERE geom IS NOT NULL");
         $row = $spatial_count_result->fetch();
         $spatial_info['count'] = $row['total_items'];
         $spatial_info['extent'] = $row['extent'];
         $spatial_info['area']['raw'] = $row['total_area'];
         $spatial_info['area']['sqm'] = number_format($row['total_area'], 1, $lang['dec_point'], $lang['thousands_sep']);
         $spatial_info['area']['ha'] = number_format($row['total_area']/10000, 1, $lang['dec_point'], $lang['thousands_sep']);      
         $spatial_info['area']['sqkm'] = number_format($row['total_area']/1000000, 1, $lang['dec_point'], $lang['thousands_sep']);
       
         $template->assign('spatial_info', $spatial_info);
        }
       
       $items_per_page = isset($_GET['ipp']) ? intval($_GET['ipp']) : $settings['items_per_page'];
       if($items_per_page < 1) $items_per_page = $settings['items_per_page'];
       if($items_per_page > $settings['max_items_per_page']) $items_per_page = $settings['max_items_per_page'];
       $template->assign('ipp', $items_per_page);
       
       $total_pages = ceil($total_items / $items_per_page);
       
       // get current page:
       $p = isset($_GET['p']) ? intval($_GET['p']) : 1;
       if($p<1) $p=1;
       if($total_pages>0 && $p>$total_pages) $p = $total_pages;
       $template->assign('p', $p);
            
       $offset = ($p-1) * $items_per_page;
       
       $order = isset($_GET['order']) ? trim($_GET['order']) : 'created';
       $asc = isset($_GET['asc']) && $_GET['asc'] ? 1 : 0;
       
       $template->assign('order', htmlspecialchars($order));
       $template->assign('asc', $asc);
       
       if($table_info['table']['type']==1)
        {
         if(isset($column_names) && !in_array($order, $column_names) && $order!='fk' && $order!='created' && $order!='last_edited' && $order!='geom') $order = 'created';
        }
       else
        {
         if(isset($column_names) && !in_array($order, $column_names) && $order!='fk' && $order!='created' && $order!='last_edited') $order = 'created';
        }
      
       $descasc = $asc ? 'ASC' : 'DESC';
       
       $template->assign('total_items', $total_items);
       $template->assign('pagination', pagination($total_pages, $p));
  
       $query = "SELECT table".$table_info['table']['id'].".id,
                        table".$table_info['table']['id'].".fk,
                        extract(epoch FROM table".$table_info['table']['id'].".created) as created_timestamp,
                        userdata_table_1.name as creator,
                        extract(epoch FROM table".$table_info['table']['id'].".last_edited) as last_edited_timestamp,
                        table".$table_info['table']['id'].".last_editor as last_editor,
                        userdata_table_2.name as last_editor_name";
       if($table_info['table']['type']==1) $query .= ", CASE WHEN geom IS NULL THEN false ELSE true END AS has_geometry";
       $query .= $select_query;
       $query .= "\nFROM \"".$table_info['table']['table_name']."\" AS table".$table_info['table']['id'];
       if(isset($joins))
        {
         foreach($joins as $join)
          {
           $query .= "\nLEFT JOIN ".$join['relation_table_name']." AS ".$join['alias']." ON table".$table_info['table']['id'].".".$join['fk']."=".$join['alias'].".id";
          }
        }                                       
       
       $query .= "\nLEFT JOIN ".Database::$db_settings['userdata_table']." AS userdata_table_1 ON userdata_table_1.id=table".$table_info['table']['id'].".creator";
       $query .= "\nLEFT JOIN ".Database::$db_settings['userdata_table']." AS userdata_table_2 ON userdata_table_2.id=table".$table_info['table']['id'].".last_editor";
                                               
       $query .= "\nORDER BY table".$table_info['table']['id'].".".$order." ".$descasc." LIMIT ".$items_per_page." OFFSET ".$offset;
       
      
       $dbr = Database::$connection->prepare($query);
       $dbr->execute();  

       $displayed_items = $dbr->rowCount();
       $template->assign('displayed_items', $displayed_items);
      
       $lang['displayed_records_label'] = str_replace('[total]', $total_items, str_replace('[displayed]', $displayed_items, $lang['displayed_records_label']));
       $lang['total_records_label'] = str_replace('[total]', $total_items, $lang['total_records_label']);
       
       $displayed_area_raw = 0;
       
       $i=0;
       $parent_group = 0;
       foreach($dbr as $row) 
        {
         // default columns:
         $data_items[$i]['id'] = intval($row['id']);
         $data_items[$i]['fk'] = intval($row['fk']);
         
         $data_items[$i]['creator'] = htmlspecialchars($row['creator']);
         $data_items[$i]['created'] = htmlspecialchars(strftime($lang['time_format'], $row['created_timestamp']));
         if(!is_null($row['last_editor'])) 
          {
           $data_items[$i]['last_editor'] = htmlspecialchars($row['last_editor_name']);
           $data_items[$i]['last_edited'] = htmlspecialchars(strftime($lang['time_format'], $row['last_edited_timestamp']));
          }
         // spatial data columns:
         if($table_info['table']['type']==1)
          {
           $data_items[$i]['has_geometry'] = $row['has_geometry'];
          }                    
         // custom columns:
         if(isset($table_info['columns']))
          {
           foreach($table_info['columns'] as $column)
            {
             // first custom column as feature label: 
             if($table_info['table']['type']==1 && empty($data_items[$i]['_featurelabel_'])) $data_items[$i]['_featurelabel_'] = htmlspecialchars($row[$column['name']]);
             $data_items[$i][$column['name']] = htmlspecialchars($row[$column['name']]);
            }
          }
          ++$i;
        }
       
       if($displayed_area_raw)
        {
         $displayed_area['sqm'] = number_format($displayed_area_raw, 1, $lang['dec_point'], $lang['thousands_sep']);
         $displayed_area['ha'] = number_format($displayed_area_raw/10000, 1, $lang['dec_point'], $lang['thousands_sep']);
         $template->assign('displayed_area', $displayed_area);
        }
       
       if(isset($data_items)) $template->assign('data_items',$data_items);  
      
      }
     else
      {
       $template->assign('table_exists', false);
      } 
    
     $template->assign('table_id', intval($table_info['table']['id']));
     $template->assign('parent_table', intval($table_info['table']['parent_table']));
     $template->assign('parent_title', htmlspecialchars($table_info['table']['parent_title']));
     $template->assign('table_status', intval($table_info['table']['status']));
     $template->assign('data_type', intval($table_info['table']['type']));
     $template->assign('min_scale', intval($table_info['table']['min_scale']));
     $template->assign('max_scale', intval($table_info['table']['max_scale']));
     if($table_info['table']['simplification_tolerance_extent_factor']) $template->assign('redraw', true);
     $template->assign('layer_overview', intval($table_info['table']['layer_overview']));
     $template->assign('auxiliary_layer_1', intval($table_info['table']['auxiliary_layer_1']));
     $template->assign('auxiliary_layer_1_title', htmlspecialchars($table_info['table']['auxiliary_layer_1_title']));
     #$template->assign('subtitle', htmlspecialchars($table_info['table']['title']));
     $template->assign('description', $table_info['table']['description']);
     
     $granted_permissions['write'] = $table_info['table']['readonly']==0 && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($table_info['table']['id']), Permission::WRITE)) ? true : false;
     $granted_permissions['manage'] = $permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($table_info['table']['id']), Permission::MANAGE) ? true : false;
     $granted_permissions['data_management'] = $permission->granted(Permission::DATA_MANAGEMENT) ? true : false;

     $template->assign('permission', $granted_permissions);
     
     
     if($table_info['table']['type']==1)
      {
       if($basemaps = get_basemaps($table_info['table']['basemaps']))
        {
         $template->assign('basemaps', $basemaps);
         foreach($basemaps as $basemap)
          {
           if($basemap['js'] && !in_array($basemap['js'], $javascripts)) $javascripts[] = $basemap['js'];
          }
        }
       $javascripts[] = OPENLAYERS;
       $javascripts[] = OPENLAYERS_DATA;
       #$javascripts[] = GOOGLE_MAPS;
       $stylesheets[] = OPENLAYERS_CSS;
       $template->assign('help', 'data_spatial');
      }
     else
      {
       $template->assign('help', 'data_common');
      }
     $template->assign('subtitle', $lang['add_relation_subtitle']);
     $template->assign('subtemplate', 'relation.inc.tpl');
     break;
   
   case 'add':
    if(isset($_POST['selected_items']) && is_array($_POST['selected_items']))
     {
      foreach($_POST['selected_items'] as $selected_item)
       {
        $query_parts[] = intval($selected_item);
       } 
      $query = implode(', ', $query_parts);
      
      // select items to get only existing items: 
      $items_query = Database::$connection->query("SELECT id
                                                   FROM \"".$table_info['table']['table_name']."\"
                                                   WHERE id IN (".$query.")
                                                   ORDER BY id ASC");
        // prepare save statement:
        $insert_query = Database::$connection->prepare("INSERT INTO ".$db_settings['relations_table']." (t1, i1, t2, i2, creator) VALUES (:t1, :i1, :t2, :i2, :creator)");
        
        // go through each item and check if the relation already exists:
        foreach($items_query as $row)
         {
          // check existing relations:
          $count_query = Database::$connection->prepare("SELECT COUNT(*)
                                           FROM ".$db_settings['relations_table']."
                                           WHERE t1=:t1 AND i1=:i1 AND t2=:t2 AND i2=:i2 OR t1=:t2 AND i1=:i2 AND t2=:t1 AND i2=:i1");
          $count_query->bindParam(':t1', $_REQUEST['initial_table'], PDO::PARAM_INT); 
          $count_query->bindParam(':i1', $_REQUEST['initial_item'], PDO::PARAM_INT);
          $count_query->bindParam(':t2', $_REQUEST['selected_table'], PDO::PARAM_INT); 
          $count_query->bindParam(':i2', $row['id'], PDO::PARAM_INT);
          $count_query->execute();
          list($item_count) = $count_query->fetch();
          
          if($item_count==0) // relation doesn't exist yet - save it
           {
            $insert_query->bindParam(':t1', $_REQUEST['initial_table'], PDO::PARAM_INT);
            $insert_query->bindParam(':i1', $_REQUEST['initial_item'], PDO::PARAM_INT);
            $insert_query->bindParam(':t2', $_REQUEST['selected_table'], PDO::PARAM_INT);
            $insert_query->bindParam(':i2', $row['id'], PDO::PARAM_INT);
            $insert_query->bindParam(':creator', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
            $insert_query->execute();
           }
         }
        header('Location: '.BASE_URL.'?r=data_item&data_id='.intval($_REQUEST['initial_table']).'&id='.intval($_REQUEST['initial_item']).'#related-data');
        exit; 
     
     
     }
    break;
   
   
   }
   
    }
   }
 }
?>
