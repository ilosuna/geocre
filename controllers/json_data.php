<?php
if(!defined('IN_INDEX')) exit;

if(isset($_REQUEST['table']) && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($_REQUEST['table']), Permission::READ)))
 {
  $table_info = get_table_info($_REQUEST['table'], true); // 'true' fetches only overview columns
  
  if($table_info && $table_info['table']['type']==1)
   {

    if(isset($_REQUEST['bbox']))
     {
      $bbox = explode(',', $_REQUEST['bbox']);
      $bbox[0] = isset($bbox[0]) ? floatval($bbox[0]) : -180;
      $bbox[1] = isset($bbox[1]) ? floatval($bbox[1]) : 90;
      $bbox[2] = isset($bbox[2]) ? floatval($bbox[2]) : 180;
      $bbox[3] = isset($bbox[3]) ? floatval($bbox[3]) : -90;
      if($bbox[0] < -180) $bbox[0] = -180;
      if($bbox[0] > 180) $bbox[0] = 180;
      if($bbox[1] < -90) $bbox[0] = -90;
      if($bbox[1] > 90) $bbox[0] = 90;
      if($bbox[2] < -180) $bbox[2] = -180;
      if($bbox[2] > 180) $bbox[2] = 180;
      if($bbox[3] < -90) $bbox[3] = -90;
      if($bbox[3] > 90) $bbox[3] = 90;
      
      if($table_info['table']['simplification_tolerance_extent_factor'])
       {
        $dbr = Database::$connection->query("SELECT ST_Distance(
		                                                ST_GeomFromText('POINT(".$bbox[0]." ".$bbox[1].")'),
		                                                ST_GeomFromText('POINT(".$bbox[2]." ".$bbox[3].")')
		                                               ) AS diagonal_distance");
        list($diagonal_distance) = $dbr->fetch();
        $simplify_tolerance = number_format($diagonal_distance * $table_info['table']['simplification_tolerance_extent_factor'], 2);
       }
      else
       {
        $simplify_tolerance = $table_info['table']['simplification_tolerance'];
       }
      
      /*
      $file = BASE_PATH . 'logs/ajax.log';
      $time = date(DATE_RFC822);
      #$log_message = print_r($bbox, true)."\n\n".$diagonal_distance." ".$simplify_tolerance."\n\n".print_r($_REQUEST, true);
      $log_message = print_r($bbox, true)."\n\n".$simplify_tolerance."\n\n".print_r($_REQUEST, true);
      file_put_contents($file, $log_message);
      */

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

      $query = "SELECT table".$table_info['table']['id'].".id,
                       table".$table_info['table']['id'].".fk,
                       extract(epoch FROM table".$table_info['table']['id'].".created) as created_timestamp,
                       userdata_table_1.name as creator,
                       extract(epoch FROM table".$table_info['table']['id'].".last_edited) as last_edited_timestamp,
                       table".$table_info['table']['id'].".last_editor as last_editor,
                       userdata_table_2.name as last_editor_name";
      $query .= ", ST_AsGeoJSON(ST_Simplify(table".$table_info['table']['id'].".geom, $simplify_tolerance)) AS geojson, area, perimeter, length, latitude, longitude";
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
                                              
      $query .= "\nWHERE geom IS NOT NULL AND geom && ST_MakeEnvelope(".$bbox[0].",".$bbox[1].",".$bbox[2].",".$bbox[3].") LIMIT 3000";

      
      $dbr = Database::$connection->prepare($query);
      $dbr->execute();

      $displayed_items = $dbr->rowCount();
      $template->assign('displayed_items', $displayed_items);
      
      $displayed_area_raw = 0;
      
      $i=0;
      foreach($dbr as $row) 
       {
        
        // default columns:
        $data_items[$i]['id'] = intval($row['id']);
        $data_items[$i]['creator'] = htmlspecialchars($row['creator']);
        $data_items[$i]['created'] = htmlspecialchars(strftime($lang['time_format'], $row['created_timestamp']));
        if(!is_null($row['last_editor'])) 
         {
          $data_items[$i]['last_editor'] = htmlspecialchars($row['last_editor_name']);
          $data_items[$i]['last_edited'] = htmlspecialchars(strftime($lang['time_format'], $row['last_edited_timestamp']));
         }
        // spatial data columns:
        $displayed_area_raw += $row['area']; 
        $data_items[$i]['geojson'] = $row['geojson'];
        $data_items[$i]['area'] = $row['area'];
        $data_items[$i]['area_sqm'] = number_format($row['area'], 1, $lang['dec_point'], $lang['thousands_sep']);
        $data_items[$i]['area_ha'] = number_format($row['area']/10000, 1, $lang['dec_point'], $lang['thousands_sep']);
        $data_items[$i]['perimeter'] = $row['perimeter'];
        $data_items[$i]['length'] = $row['length'];
        $data_items[$i]['latitude'] = $row['latitude'];
        $data_items[$i]['longitude'] = $row['longitude'];
        // custom columns:
        if(isset($table_info['columns']))
         {
          foreach($table_info['columns'] as $column)
           {
            // first custom column as feature label: 
            if(empty($data_items[$i]['_featurelabel_'])) $data_items[$i]['_featurelabel_'] = htmlspecialchars($row[$column['name']]);
            $data_items[$i][$column['name']] = htmlspecialchars($row[$column['name']]);
           }
         }
         if(empty($data_items[$i]['_featurelabel_'])) $data_items[$i]['_featurelabel_'] = $row['id'];
         ++$i;
       }
      
      if($displayed_area_raw)
       {
        #$displayed_area['sqm'] = number_format($displayed_area_raw, 1, $lang['dec_point'], $lang['thousands_sep']);
        #$displayed_area['ha'] = number_format($displayed_area_raw/10000, 1, $lang['dec_point'], $lang['thousands_sep']);
       }

     } // bbox
    else // overview
     {
      if($table_info['table']['layer_overview']==1) // point clustering
       {      
        $dbr = Database::$connection->query("SELECT array_agg(id) AS ids,
                                                    COUNT(geom) AS count,
                                                    ST_AsGeoJSON(ST_Centroid(ST_Collect(geom))) AS center
                                             FROM ".$table_info['table']['table_name']."
                                             GROUP BY ST_SnapToGrid(geom, 0.005)
                                             ORDER BY count DESC");
        $dbr->execute();
        $row = $dbr->fetch();
        foreach($dbr as $row)
         {
          #if($row['gm']!=NULL) $clustered_items[] = 'ST_GeomFromText(\''.$row['gm'].'\')';
          if($row['center']) $data_items[]['geojson'] = $row['center'];
         }
       }
      elseif($table_info['table']['layer_overview']==2) // convex hull
       {
        $dbr = Database::$connection->query("SELECT ST_AsGeoJSON(ST_ConvexHull(ST_Collect(geom))) AS geojson FROM ".$table_info['table']['table_name']." WHERE geom IS NOT NULL");
        //$row = $dbr->fetch();
        foreach($dbr as $row)
         {
          //echo $row['wkt'].'<br />';
          $data_items[]['geojson'] = $row['geojson'];
         }
       }
      /*
      elseif($table_info['table']['layer_overview']==2) // clustering
       {      
        $dbr = Database::$connection->query("SELECT * FROM get_domains_n('".$table_info['table']['table_name']."', 'geom', 'id', 1400) AS g(gm text)");
        foreach($dbr as $row)
         {
          if($row['gm']!=NULL) $clustered_items[] = 'ST_GeomFromText(\''.$row['gm'].'\')';
         }
        if(isset($clustered_items))
         {
          $merged_parts = implode(', ', $clustered_items);
          $dbr = Database::$connection->query("SELECT ST_AsText(ST_Collect(ARRAY[".$merged_parts."])) AS wkt");
          $row = $dbr->fetch();
          $data_items[0]['wkt'] = $row['wkt'];
         }
       }
      */
     
     
     }
    
    if(isset($data_items))
     {
      $items_count = count($data_items);
     
      $json = '{"type": "FeatureCollection", "features": [';
      $json .= "\n";    
     
      $i=0;
      foreach($data_items as $item)
       {
        #if(isset($_REQUEST['bbox']) && !empty($_REQUEST['attributes']))
        if(isset($_REQUEST['bbox']))
         {
          $properties_string = '"id":'.$item['id'].',"label":'.json_encode($item['_featurelabel_']).',"area":'.floatval($item['area']).',"perimeter":'.floatval($item['perimeter']).',"length":'.floatval($item['length']).',"latitude":'.floatval($item['latitude']).',"longitude":'.floatval($item['longitude']);
          $json .= '{"type":"Feature", "properties":{'.$properties_string.'}, "geometry":'.$item['geojson'].'}';
         }
        else $json .= '{"type":"Feature", "geometry":'.$item['geojson'].'}';
        if($i<$items_count-1) $json .= ",\n";
        ++$i;
       }
    
      $json .= "\n";
      $json .= '] }';
      echo $json;
     } 

   } // table type
 
 } // table
exit;
?>
