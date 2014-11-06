<?php
if(!defined('IN_INDEX')) exit;

if(isset($_GET['data_id']) && isset($_GET['id']) && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($_GET['data_id']), Permission::READ)))
 {
     if(isset($_REQUEST['disable_map']))
      {
       $_REQUEST['disable_map'] = $_REQUEST['disable_map'] ? 1 : 0;
       set_user_setting();
      }


    $granted_permissions['write'] = $permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($_GET['data_id']), Permission::WRITE) ? true : false;
    $template->assign('permission', $granted_permissions);
       
    $table = intval($_GET['data_id']);
    $id = intval($_GET['id']);
    
    if(isset($_GET['delete_relation']))
     {
      $dbr = Database::$connection->prepare("DELETE FROM ".$db_settings['relations_table']."
                                             WHERE id=:id");
      $dbr->bindParam(':id', $_REQUEST['delete_relation'], PDO::PARAM_INT);
      $dbr->execute();
     
      header('Location: '.BASE_URL.'?r=data_item&data_id='.intval($table).'&id='.intval($id).'#related-data');
      exit;
     }
    
    $table_info = get_table_info($table);
    
    
    if($table_info)
     {

    $readonly = $table_info['table']['readonly']==1 ? true : false; 
    $template->assign('readonly', $readonly);

    if(isset($table_info['columns']))
     {
      $template->assign('columns', $table_info['columns']);  
      $i=0;
      foreach($table_info['columns'] as $column)
       {
        if($column['type']>0) $select_query_parts[] = 'table'.$table_info['table']['id'].'.'.$column['name'];
        
        if($column['relation'] && $column['relation_table_name'])
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
    

      $query = "SELECT table".$table_info['table']['id'].".id,
                       table".$table_info['table']['id'].".fk,
                       extract(epoch FROM table".$table_info['table']['id'].".created) as created_timestamp,
                       userdata_table_1.name as creator,
                       extract(epoch FROM table".$table_info['table']['id'].".last_edited) as last_edited_timestamp,
                       table".$table_info['table']['id'].".last_editor as last_editor,
                       userdata_table_2.name as last_editor_name";
      if($table_info['table']['type']==1) $query .= ", ST_AsText(table".$table_info['table']['id'].".geom) AS wkt, GeometryType(table".$table_info['table']['id'].".geom) as geometry_type, area, perimeter, length, latitude, longitude";
      $query .= $select_query;
      $query .= "\nFROM \"".$table_info['table']['table_name']."\" AS table".$table_info['table']['id'];
      if(isset($joins))
       {
        foreach($joins as $join)
         {
          $query .= "\nLEFT JOIN \"".$join['relation_table_name']."\" AS ".$join['alias']." ON table".$table_info['table']['id'].".".$join['fk']."=".$join['alias'].".id";
         }
       }                                       
      
      $query .= "\nLEFT JOIN ".Database::$db_settings['userdata_table']." AS userdata_table_1 ON userdata_table_1.id=table".$table_info['table']['id'].".creator";
      $query .= "\nLEFT JOIN ".Database::$db_settings['userdata_table']." AS userdata_table_2 ON userdata_table_2.id=table".$table_info['table']['id'].".last_editor";
                                              
      $query .= "\nWHERE table".$table_info['table']['id'].".id=:id LIMIT 1";
      
      #showme($query);
      
      $dbr = Database::$connection->prepare($query);

      $dbr->bindParam(':id', $id, PDO::PARAM_INT);
      $dbr->execute();
      $row = $dbr->fetch();
      
      if(isset($row['id']))
       {
        // default columns:
        $item_data['id'] = intval($row['id']);
        $item_data['fk'] = intval($row['fk']);
        $item_data['creator'] = htmlspecialchars($row['creator']);
        $item_data['created'] = htmlspecialchars(strftime($lang['time_format'], $row['created_timestamp']));
        if(!is_null($row['last_editor'])) 
         {
          $item_data['last_editor'] = htmlspecialchars($row['last_editor_name']);
          $item_data['last_edited'] = htmlspecialchars(strftime($lang['time_format'], $row['last_edited_timestamp']));
         }
        // spatial data columns:
        if($table_info['table']['type']==1)
         {
          $maps[] = $table_info['table']['id'];
          $spatial_item_data['wkt'] = $row['wkt'];
          $spatial_item_data['geometry_type'] = $row['geometry_type'];
          if($row['latitude'])
           {
            $spatial_item_data['latlong']['dms'] = get_geographic_coordinates($row['latitude'], $row['longitude']);
            $spatial_item_data['latlong']['dec'] = number_format($row['latitude'], 5).', '. number_format($row['longitude'], 5);
           }
          $spatial_item_data['area']['raw'] = $row['area'];
          $spatial_item_data['area']['sqm'] = number_format($row['area'], 1, $lang['dec_point'], $lang['thousands_sep']);
          $spatial_item_data['area']['ha'] = number_format($row['area']/10000, 1, $lang['dec_point'], $lang['thousands_sep']);
          $spatial_item_data['area']['sqkm'] = number_format($row['area']/1000000, 1, $lang['dec_point'], $lang['thousands_sep']);
          $spatial_item_data['perimeter']['raw'] = $row['perimeter'];
          $spatial_item_data['perimeter']['m'] = number_format($row['perimeter'], 1, $lang['dec_point'], $lang['thousands_sep']);
          $spatial_item_data['perimeter']['km'] = number_format($row['perimeter']/1000, 1, $lang['dec_point'], $lang['thousands_sep']);
          $spatial_item_data['length']['raw'] = $row['length'];
          $spatial_item_data['length']['m'] = number_format($row['length'], 1, $lang['dec_point'], $lang['thousands_sep']);
          $spatial_item_data['length']['km'] = number_format($row['length']/1000, 1, $lang['dec_point'], $lang['thousands_sep']);
          if(empty($_SESSION[$settings['session_prefix'].'usersettings']['disable_map']) && $basemaps[$table_info['table']['id']] = get_basemaps($table_info['table']['basemaps']))
           {
            foreach($basemaps[$table_info['table']['id']] as $basemap)
             {
              if($basemap['js'] && !in_array($basemap['js'], $javascripts)) $javascripts[] = $basemap['js'];
             }
           }         
         }            
        if(isset($table_info['columns']))
         {
          $i=0;
          foreach($table_info['columns'] as $column)
           {
            // first column as feature label TODO:
            if(empty($item_title_set) && $column['type']!=0 && $row[$column['name']])
             {
              $template->assign('item_title', htmlspecialchars($row[$column['name']]));
              $item_title_set = true;
             }
            $custom_item_data[$i]['type'] = $column['type'];
            $custom_item_data[$i]['priority'] = $column['priority'];
            $custom_item_data[$i]['name'] = htmlspecialchars($column['name']);
            $custom_item_data[$i]['label'] = $column['label'];
            $custom_item_data[$i]['description'] = $column['description'];
            if($column['relation_table'])
             {
              $custom_item_data[$i]['_related_']['table'] = $column['relation_table'];
              $custom_item_data[$i]['_related_']['item'] = $row['_'.$column['name'].'_'];
             }
            if(isset($column['choice_labels'])) $custom_item_data[$i]['choice_labels'] = $column['choice_labels'];
            if($column['name'])
             {
              if($column['type']==5) $custom_item_data[$i]['value'] = nl2br(htmlspecialchars($row[$column['name']]));
              elseif($column['type']!=0) $custom_item_data[$i]['value'] = htmlspecialchars($row[$column['name']]);
             } 
            
            // special colums for spatial data:
            #if($table_info['table']['type']==1 && $column['name'] == 'name') $template->assign('feature_name', htmlspecialchars($row[$column['name']]));
            
            ++$i;
           }
         }
       }

      // attached and related data:
      if(isset($item_data))
       {
        // get tables
        $dbr = Database::$connection->query("SELECT id, table_name, title, type, parent_table, readonly
                                             FROM ".$db_settings['data_models_table']."
                                             ORDER BY sequence ASC");        
        foreach($dbr as $row)
         {
          $tables[$row['id']]['table_id'] = $row['id'];
          $tables[$row['id']]['table_name'] = $row['table_name'];
          $tables[$row['id']]['type'] = $row['type']; 
          $tables[$row['id']]['title'] = htmlspecialchars($row['title']);
          $tables[$row['id']]['parent_table'] = $row['parent_table'];
          $tables[$row['id']]['readonly'] = $row['readonly'];    
         } 
               
               
        // get attached data:
        foreach($tables as $table)
         {
          if($table['parent_table'] == $table_info['table']['id'])
           {
            $attached_data[$table['table_id']]['table_id'] = $table['table_id'];
            $attached_data[$table['table_id']]['table_name'] = $table['table_name'];
            $attached_data[$table['table_id']]['type'] = $table['type']; 
            $attached_data[$table['table_id']]['title'] = $table['title'];     
            if($table['readonly']) $attached_data[$table['table_id']]['writable'] = false;
            else $attached_data[$table['table_id']]['writable'] = $permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($table['table_id']), Permission::WRITE) ? true : false;
            if($table['type']==1) $attached_data_spatial = true;
            $template->assign('permission', $granted_permissions);
           }
         } 
        
        if(isset($attached_data)) // get items
         {
          foreach($attached_data as $attached_data_item)
           {
            $attached_table_info = get_table_info($attached_data_item['table_id'], true); // 'true' fetches only overview columns
            
            if($attached_table_info['table']['type']==1) // attached spatial data
             {
              $maps[] = $attached_data_item['table_id'];
              // get basemaps of attached data:
              if(empty($_SESSION[$settings['session_prefix'].'usersettings']['disable_map']) && $basemaps[$attached_data_item['table_id']] = get_basemaps($attached_table_info['table']['basemaps']))
               {
                foreach($basemaps[$attached_data_item['table_id']] as $basemap)
                 {
                  if($basemap['js'] && !in_array($basemap['js'], $javascripts)) $javascripts[] = $basemap['js'];
                 }
               }         
             }
            
           # if(isset($attached_table_info['columns']))
           #  {
              #$custom_query_part = get_custom_query_part($attached_table_info);
              $data_items = get_attached_data_items($attached_table_info, $id);
              if(isset($attached_table_info['columns'])) $attached_data[$attached_data_item['table_id']]['columns'] = $attached_table_info['columns'];
              $attached_data[$attached_data_item['table_id']]['items'] = $data_items;
              $attached_data[$attached_data_item['table_id']]['items_info'] = get_attached_items_info($attached_data_item['table_name'], $id, $attached_data_item['type']);
           #  }
           }
          
          #showme($attached_data);
          $template->assign('attached_data', $attached_data);
         }
        
        
        // get intended relations from relations table:
        $dbr = Database::$connection->prepare("SELECT id, t1, t2
                                                      FROM ".$db_settings['db_table_relations_table']."
                                                      WHERE t1=:table OR t2=:table");
        $dbr->bindParam(':table', $table_info['table']['id'], PDO::PARAM_INT);
        #$dbr->bindParam(':table', $table_info['table']['id'], PDO::PARAM_INT);
        $dbr->execute();
        while($row = $dbr->fetch())
         {
          if($row['t1']==$table_info['table']['id']) $intended_tables[$row['t2']] = $row['t2'];
          else $intended_tables[$row['t1']] = $row['t1'];
         }        
        
        // get related items:
        $dbr = Database::$connection->prepare("SELECT id, t1, i1, t2, i2
                                               FROM ".$db_settings['relations_table']."
                                               WHERE t1=:table AND i1=:item OR t2=:table AND i2=:item");
        $dbr->bindParam(':table', $table_info['table']['id'], PDO::PARAM_INT);
        $dbr->bindParam(':item', $id, PDO::PARAM_INT);
        $dbr->execute();
        $i=0;
        while($row = $dbr->fetch())
         {
          if($row['t1']==$table_info['table']['id']) // if t1 is the item table then t2 is the relation table and vice versa
           {
            $related_table = $row['t2'];
            $related_item = $row['i2'];
            $related_items[$related_table][$i] = $related_item;
            $relations[$related_table][$related_item] = $row['id'];
            ++$i;
           }
          else
           {
            $related_table = $row['t1'];
            $related_item = $row['i1'];
           }
          $related_items[$related_table][$i] = $related_item;
          $relations[$related_table][$related_item] = $row['id'];
          ++$i;           
         }                                                
        
        // get the actual items:
        if(isset($related_items))
         {
          foreach($related_items as $related_table => $ids)
           {
            $related_table_info = get_table_info($related_table, true); // 'true' fetches only overview columns
            if($related_table_info['table']['type']==1) // related spatial data
             {
              $maps[] = $related_table;
              // get basemaps of related data:
              if(empty($_SESSION[$settings['session_prefix'].'usersettings']['disable_map']) && $basemaps[$related_table_info['table']['id']] = get_basemaps($related_table_info['table']['basemaps']))
               {
                foreach($basemaps[$related_table_info['table']['id']] as $basemap)
                 {
                  
                  if($basemap['js'] && !in_array($basemap['js'], $javascripts)) $javascripts[] = $basemap['js'];
                 }
               }         
             }            
            
            
            #if(isset($related_table_info['columns'])) // 'true' fetches only overview columns
            # {
              #$custom_query_part = get_custom_query_part($related_table_info);
              $data_items = get_related_data_items($related_table_info, $ids);
              
              if($data_items || isset($intended_tables[$related_table]))
               {
                $related_data[$related_table]['items_info'] = get_related_items_info($tables[$related_table]['table_name'], $ids, $tables[$related_table]['type']);
                $related_data[$related_table]['table_id'] = $tables[$related_table]['table_id'];
                $related_data[$related_table]['type'] = $tables[$related_table]['type'];
                if($tables[$related_table]['type']==1) $related_data_spatial = true;
                $related_data[$related_table]['title'] = $tables[$related_table]['title'];
                if(isset($related_table_info['columns'])) $related_data[$related_table]['columns'] = $related_table_info['columns'];
                $related_data[$related_table]['items'] = $data_items;
               }
            # }
           }
       
         }

        // go through all tables and check relatins:
        foreach($tables as $table)
         {
          if(isset($intended_tables[$table['table_id']]) && empty($related_data[$table['table_id']])) // intended relation but no items
           {
            $related_data[$table['table_id']]['table_id'] = $tables[$table['table_id']]['table_id'];
            $related_data[$table['table_id']]['type'] = $tables[$table['table_id']]['type'];
            $related_data[$table['table_id']]['title'] = $tables[$table['table_id']]['title'];
            $related_data[$table['table_id']]['colums'] = false;
            $related_data[$table['table_id']]['items'] = false;
           }
         }
        
        if(isset($related_data))
         {
          $template->assign('related_data', $related_data);
          if(isset($relations)) $template->assign('relations', $relations);
         }
        
        /* images: */
        if($settings['data_images'] && $table_info['table']['item_images'])
         {
          // get images:
          $dbr = Database::$connection->prepare("SELECT id, filename, thumbnail_width, thumbnail_height, title, description, author FROM ".Database::$db_settings['data_images_table']." WHERE data=:data AND item=:item ORDER by sequence ASC");
          $dbr->bindParam(':data', $table_info['table']['id'], PDO::PARAM_INT);
          $dbr->bindParam(':item', $id, PDO::PARAM_INT);
          $dbr->execute();
          $i=0;
          while($row = $dbr->fetch())
           {
            $images[$i]['id'] = $row['id'];
            $images[$i]['filename'] = $row['filename'];
            if($settings['data_images_permission_check'])
             {
              $images[$i]['thumbnail_url'] = BASE_URL.'?r=data_image.thumbnail&file='.$row['filename'];
              $images[$i]['image_url'] = BASE_URL.'?r=data_image.image&file='.$row['filename'];
             }
            else
             {
              $images[$i]['thumbnail_url'] = DATA_THUMBNAILS_URL.$row['filename'];
              $images[$i]['image_url'] = DATA_IMAGES_URL.$row['filename'];
             }
            $images[$i]['thumbnail_width'] = intval($row['thumbnail_width']);
            $images[$i]['thumbnail_height'] = intval($row['thumbnail_height']);
            $images[$i]['title'] = htmlspecialchars($row['title']);
            $images[$i]['description'] = htmlspecialchars($row['description']);
            if($row['author']) $images[$i]['author'] = str_replace('[author]', htmlspecialchars($row['author']), $lang['gallery_image_author_declaration']);
            else $images[$i]['author'] = '';
            ++$i;
           }
          // enable images if there are images or if user is allowed to add images:
          if($i || $granted_permissions['write'])
           {
            $lang['item_images'] = str_replace('[number]', $i, $lang['item_images']);
            $template->assign('number_of_images', $i); 
            $template->assign('item_images', true);
           }
          // assign images and requirements:
          if(isset($images))
           {
            $template->assign('images', $images);    
            $javascripts[] = LIGHTBOX;
            if($granted_permissions['write'])
             {
              $javascripts[] = JQUERY_UI;
              $javascripts[] = JQUERY_UI_HANDLER;
             }
           }
         }
        
        $template->assign('item_exists', true);
        $template->assign('item_data', $item_data);
        if(isset($custom_item_data)) $template->assign('custom_item_data', $custom_item_data);
        if(isset($spatial_item_data)) $template->assign('spatial_item_data', $spatial_item_data);
       }
      else
       {
        $template->assign('item_exists', false);
       }
     #}

    if(isset($_GET['item_added'])) $template->assign('item_added', true);
    if(isset($_GET['attached_item_added'])) $template->assign('attached_item_added', true);

    $template->assign('table_data', $table_info['table']);
    $template->assign('table_id', $table_info['table']['id']);
    $template->assign('table_title', $table_info['table']['title']);
    $template->assign('table_status', intval($table_info['table']['status']));
    $template->assign('data_type', intval($table_info['table']['type']));
    $template->assign('subtitle', $lang['data_item_details_title']);
    $template->assign('min_scale', intval($table_info['table']['min_scale']));
    $template->assign('max_scale', intval($table_info['table']['max_scale']));
    $template->assign('max_resolution', floatval($table_info['table']['max_resolution']));
    $template->assign('auxiliary_layer_1', intval($table_info['table']['auxiliary_layer_1']));
    $template->assign('auxiliary_layer_1_title', htmlspecialchars($table_info['table']['auxiliary_layer_1_title']));
    if($table_info['table']['auxiliary_layer_1_stef']) $template->assign('auxiliary_layer_1_redraw', true);

    if(isset($maps) && empty($_SESSION[$settings['session_prefix'].'usersettings']['disable_map']))
     {
      $template->assign('maps', $maps);
      $javascripts[] = OPENLAYERS;
      $javascripts[] = OPENLAYERS_DATA_ITEM;
      if(isset($attached_data_spatial)||isset($related_data_spatial)) $javascripts[] = OPENLAYERS_DATA_ITEM_ATTACHED;
      $stylesheets[] = OPENLAYERS_CSS;
     }
    
    if(isset($basemaps)) $template->assign('basemaps', $basemaps);
    if($table_info['table']['type']==1) $template->assign('help', 'data_item_spatial');
    else $template->assign('help', 'data_item_common');
   
    $template->assign('subtemplate', 'data_item.inc.tpl');
   }
 }
else
 {
  $http_status = 403;
 }
?>
