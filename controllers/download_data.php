<?php
if(!defined('IN_INDEX')) exit;

function get_query($table, $geometry=false, $wkt=false, $join=false, $column_header_labels=false, $labels=false)
 {
    $table_info = get_table_info($table);

    if(isset($table_info['columns']))
     {
      $i=0;
      foreach($table_info['columns'] as $column)
       {
        if($column['type']>0)
         { 
          if($column['relation'] && $labels)
           {
            $joined_tables[] = $table_info['table']['id'];
            $joins[$i]['table'] = $table_info['table']['id'];
            $joins[$i]['alias'] = 'table'.$table_info['table']['id'].'_'.$i; // unique table alias
            $joins[$i]['relation_table'] = $column['relation_table'];
            $joins[$i]['relation_table_name'] = $column['relation_table_name'];
            $joins[$i]['relation_column_name'] = $column['name'];
            $joins[$i]['fk'] = $column['name'];
            #if($column_header_labels)
            # {
              #$select_query_parts[] = 'table'.$table_info['table']['id'].'.'.$column['name'].' AS '.$column['name'];
              $select_query_parts[] = $joins[$i]['alias'].'.'.$column['relation_column_name'].' AS '.$column['name'];
            # }
            #else
            # {
              #$select_query_parts[] = 'table'.$table_info['table']['id'].'.'.$column['name'];
              #$select_query_parts[] = 'table'.$table_info['table']['id'].'.'.$column['name'].' AS _'.$column['name'].'_';
            #  $select_query_parts[] = $joins[$i]['alias'].'.'.$column['relation_column_name'].' AS '.$column['name'];
            # }
           }
          else
           {
            $select_query_parts[] = 'table'.$table_info['table']['id'].'.'.$column['name'];
           }        
          ++$i;
         }
       }
      #showme($select_query_parts);
      
      if($table_info['table']['parent_table'] && $join)
       {
        $parent_table_info = get_table_info($table_info['table']['parent_table']);
        foreach($parent_table_info['columns'] as $column)
         {
          if($column['type']>0) $select_query_parts[] = 'table'.$parent_table_info['table']['id'].'.'.$column['name'].' AS _parent_'.$column['name'];
        
          if($column['relation'])
           {
            $joined_tables[] = $parent_table_info['table']['id'];
            $joins[$i]['table'] = $parent_table_info['table']['id'];
            $joins[$i]['alias'] = 'table'.$parent_table_info['table']['id'].'_'.$i; // unique table alias
            $joins[$i]['relation_table'] = $column['relation_table'];
            $joins[$i]['relation_table_name'] = $column['relation_table_name'];
            $joins[$i]['relation_column_name'] = $column['name'];
            $joins[$i]['fk'] = $column['name'];
            #if($no_overwrite_join_columns)
            # {
              $select_query_parts[] = 'table'.$parent_table_info['table']['id'].'.'.$column['name'].' AS _parent__'.$column['name'].'_';
              $select_query_parts[] = $joins[$i]['alias'].'.'.$column['relation_column_name'].' AS _parent_'.$column['name'];
            # }
            #else
            # {
            #  $select_query_parts[] = 'table'.$parent_table_info['table']['id'].'.'.$column['name'].' AS _parent__'.$column['name'].'_';
            #  $select_query_parts[] = $joins[$i]['alias'].'.'.$column['relation_column_name'].' AS _parent_'.$column['name'];             
            # } 
           }       
          ++$i;
         }
        
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
      
      if(isset($parent_table_info))
       {
        $query .= ",\ntable".$parent_table_info['table']['id'].".id as _parent_id,

                       extract(epoch FROM table".$parent_table_info['table']['id'].".created) as _parent_created_timestamp,
                       userdata_table_1.name as _parent_creator,
                       extract(epoch FROM table".$parent_table_info['table']['id'].".last_edited) as _parent_last_edited_timestamp,
                       table".$parent_table_info['table']['id'].".last_editor as _parent_last_editor,
                       userdata_table_2.name as _parent_last_editor_name";
       
       
        #$query .= ", table".$parent_table_info['table']['id'].".id as parent_id";
       } 
      #if($table_info['table']['type']==1) $query .= ", ST_AsText(table".$table_info['table']['id'].".geom) AS wkt, area, length";
      if($table_info['table']['type']==1) $query .= ", table".$table_info['table']['id'].".area, table".$table_info['table']['id'].".perimeter, table".$table_info['table']['id'].".length, table".$table_info['table']['id'].".latitude, table".$table_info['table']['id'].".longitude";
      if(isset($parent_table_info) && $parent_table_info['table']['type']==1) $query .= ", table".$parent_table_info['table']['id'].".area AS _parent_area, table".$parent_table_info['table']['id'].".perimeter AS _parent_perimeter, table".$parent_table_info['table']['id'].".length AS _parent_length, table".$parent_table_info['table']['id'].".latitude AS _parent_latitude, table".$parent_table_info['table']['id'].".longitude AS _parent_longitude";
      $query .= $select_query;
      
      if($table_info['table']['type']==1 && $geometry) $query .= ", table".$table_info['table']['id'].".geom";
      if($table_info['table']['type']==1 && $wkt) $query .= ", ST_AsText(table".$table_info['table']['id'].".geom) AS _wkt";
     
      $query .= "\nFROM \"".$table_info['table']['table_name']."\" AS table".$table_info['table']['id'];
      
      
      if(isset($parent_table_info))
       {
        $query .= "\nLEFT JOIN ".$parent_table_info['table']['table_name']." AS table".$parent_table_info['table']['id']." ON table".$table_info['table']['id'].".fk=table".$parent_table_info['table']['id'].".id";
       } 
      
      
      if(isset($joins))
       {
        foreach($joins as $join)
         {
          $query .= "\nLEFT JOIN ".$join['relation_table_name']." AS ".$join['alias']." ON table".$join['table'].".".$join['fk']."=".$join['alias'].".id";
         }
       }                                       
      
      $query .= "\nLEFT JOIN ".Database::$db_settings['userdata_table']." AS userdata_table_1 ON userdata_table_1.id=table".$table_info['table']['id'].".creator";
      $query .= "\nLEFT JOIN ".Database::$db_settings['userdata_table']." AS userdata_table_2 ON userdata_table_2.id=table".$table_info['table']['id'].".last_editor";
                                              
      if($table_info['table']['type']==1 && $geometry) $query .= "\nWHERE table".$table_info['table']['id'].".geom IS NOT NULL";
      
      if(isset($parent_table_info)) $query .= "\nORDER BY _parent_id ASC, id ASC";
      else $query .= "\nORDER BY id ASC";
  
  
  return $query;
 }


function get_data($table, $geometry=false, $wkt=false, $column_header_labels=false, $labels=false, $metadata=false, $spatial_metadata=false, $join=false)
 {
  global $lang;  
  $table_info = get_table_info($table); 
      
  if($table_info['table']['parent_table'] && $join) $parent_table_info = get_table_info($table_info['table']['parent_table']);      
      
      $query = get_query($table, $geometry, $wkt, $join, $column_header_labels, $labels);
      
      #showme($query);
      
      $dbr = Database::$connection->prepare($query);
      $dbr->execute();

      // column headers
      $data_row[] = $column_header_labels ? $lang['id_column_label'] : 'id';
      
      if($metadata)
       {
        $data_row[] = $column_header_labels ? $lang['creator_column_label'] : 'creator';
        $data_row[] = $column_header_labels ? $lang['created_column_label'] : 'created';
        $data_row[] = $column_header_labels ? $lang['last_editor_column_label'] : 'last_editor';
        $data_row[] = $column_header_labels ? $lang['last_edited_column_label'] : 'last_edited';
       }

     if($table_info['table']['type']==1 && $spatial_metadata)
      {
       $data_row[] = $column_header_labels ? $lang['latitude_column_label'] : 'latitude';
       $data_row[] = $column_header_labels ? $lang['longitude_column_label'] : 'longitude';
       $data_row[] = $column_header_labels ? $lang['area_column_label'] : 'area';
       $data_row[] = $column_header_labels ? $lang['perimeter_column_label'] : 'perimeter';
       $data_row[] = $column_header_labels ? $lang['length_column_label'] : 'length';
      }
       
      if(isset($table_info['columns']))
       {
        foreach($table_info['columns'] as $column)
         {
          if($column['type']!=0)
           {
            $data_row[] = $column_header_labels ? $column['label'] : $column['name'];
           }
         }
       }
       
      if(isset($parent_table_info))
       {
        $data_row[] = $column_header_labels ? $lang['id_column_label'].' '.$lang['parent_column_label_addition'] : 'pid';

        if($metadata)
         {
          $data_row[] = $column_header_labels ? $lang['creator_column_label'].' '.$lang['parent_column_label_addition'] : 'creator';
          $data_row[] = $column_header_labels ? $lang['created_column_label'].' '.$lang['parent_column_label_addition'] : 'created';
          $data_row[] = $column_header_labels ? $lang['last_editor_column_label'].' '.$lang['parent_column_label_addition'] : 'last_editor';
          $data_row[] = $column_header_labels ? $lang['last_edited_column_label'].' '.$lang['parent_column_label_addition'] : 'last_edited';
         }

        if($parent_table_info['table']['type']==1 && $spatial_metadata)
         {
          $data_row[] = $column_header_labels ? $lang['latitude_column_label'].' '.$lang['parent_column_label_addition'] : 'latitude';
          $data_row[] = $column_header_labels ? $lang['longitude_column_label'].' '.$lang['parent_column_label_addition'] : 'longitude';
          $data_row[] = $column_header_labels ? $lang['area_column_label'].' '.$lang['parent_column_label_addition'] : 'area';
          $data_row[] = $column_header_labels ? $lang['perimeter_column_label'].' '.$lang['parent_column_label_addition'] : 'perimeter';
          $data_row[] = $column_header_labels ? $lang['length_column_label'].' '.$lang['parent_column_label_addition'] : 'length';
         }

        
        foreach($parent_table_info['columns'] as $column)
         {
          if($column['type']!=0)
           {
            $data_row[] = $column_header_labels ? $column['label'].' '.$lang['parent_column_label_addition'] : $column['name'];
           }
         }
       
       }

      if($table_info['table']['type']==1 && $wkt) $data_row[] = $column_header_labels ? $lang['wkt_column_label'] : 'wkt';
      
      $data[0] = $data_row;
      
      $i=1;
      foreach($dbr as $row) 
       {
        unset($data_row);
        
        // default columns:
        $data_row[] = $row['id'];
        if($metadata)
         {
          $data_row[] = $row['creator'];
          $data_row[] = strftime($lang['time_format'], $row['created_timestamp']);
          if(!is_null($row['last_editor'])) 
           {
            $data_row[] = $row['last_editor_name'];
            $data_row[] = strftime($lang['time_format'], $row['last_edited_timestamp']);
           }
          else
           {
            $data_row[] = '';
            $data_row[] = '';
           }
         } 

        if($table_info['table']['type']==1 && $spatial_metadata)
         {
          $data_row[] = $row['latitude'];
          $data_row[] = $row['longitude'];
          $data_row[] = $row['area'];
          $data_row[] = $row['perimeter'];
          $data_row[] = $row['length'];
         }
       
        // custom columns:
        if(isset($table_info['columns']))
         {
          foreach($table_info['columns'] as $column)
           {
            
            if(isset($column['choice_labels']) && isset($column['choice_labels'][$row[$column['name']]]) && !empty($column['choice_labels'][$row[$column['name']]]) && $labels)
             {
              $data_row[] = $column['choice_labels'][$row[$column['name']]];
             } 
            else
             {
              if($column['type']!=0) $data_row[] = $row[$column['name']];
             }
           }
         }
        if(isset($parent_table_info['columns']))
         {
          $data_row[] = $row['_parent_id'];
          if($metadata)
           {
            $data_row[] = $row['_parent_creator'];
            $data_row[] = strftime($lang['time_format'], $row['_parent_created_timestamp']);
            if(!is_null($row['_parent_last_editor'])) 
             {
              $data_row[] = $row['_parent_last_editor_name'];
              $data_row[] = strftime($lang['time_format'], $row['_parent_last_edited_timestamp']);
             }
            else
             {
              $data_row[] = '';
              $data_row[] = '';
             }
           } 

          if($parent_table_info['table']['type']==1 && $spatial_metadata)
           {
            $data_row[] = $row['_parent_latitude'];
            $data_row[] = $row['_parent_longitude'];
            $data_row[] = $row['_parent_area'];
            $data_row[] = $row['_parent_perimeter'];
            $data_row[] = $row['_parent_length'];
           }          
          
          foreach($parent_table_info['columns'] as $column)
           {
            
            if(isset($column['choice_labels']) && isset($column['choice_labels'][$row['_parent_'.$column['name']]]) && !empty($column['choice_labels'][$row['_parent_'.$column['name']]]) && $labels)
             {
              $data_row[] = $column['choice_labels'][$row['_parent_'.$column['name']]];
             } 
            else
             {
              if($column['type']!=0) $data_row[] = $row['_parent_'.$column['name']];
             }
           }
         }
         
       if($table_info['table']['type']==1 && $wkt) $data_row[] = $row['_wkt'];
       
        $data[$i] = $data_row;
       
        ++$i;
       }
    
    if(isset($data)) return $data;
    else return false;
 
 }

function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
 }


if(isset($_REQUEST['id']) && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($_REQUEST['id']), Permission::READ)))
 {
  // get data information:
     $dbr = Database::$connection->prepare("SELECT main.id,
                                                   main.sequence,
                                                   main.table_name,
                                                   main.title,
                                                   main.type,
                                                   main.status,
                                                   main.parent_table,
                                                   parent.title AS parent_title
                                            FROM ".Database::$db_settings['data_models_table']." as main
                                            LEFT JOIN ".Database::$db_settings['data_models_table']." AS parent ON main.parent_table=parent.id
                                            WHERE (main.id=:id)
                                            ORDER BY main.sequence ASC LIMIT 1");      
     $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
     $dbr->execute();
     $row = $dbr->fetch();
     if(isset($row['id']))
      {
       $data['id'] = $row['id'];
       $data['table_name'] = htmlspecialchars($row['table_name']);
       $data['title'] = htmlspecialchars($row['title']);
       $data['type'] = intval($row['type']);
       $data['parent_table'] = intval($row['parent_table']);
       $data['parent_title'] = htmlspecialchars($row['parent_title']);
      
       $lang['download_data_headline'] = str_replace('[data]', $data['title'], $lang['download_data_headline']);
       $lang['download_data_join'] = str_replace('[data]', $data['parent_title'], $lang['download_data_join']);
      } 



  
  switch($action)
   {
    case 'default':
     if(isset($data)) $template->assign('data', $data);
     $template->assign('id', intval($_REQUEST['id']));
     $template->assign('subtitle', $lang['download_data_subtitle']);
     $template->assign('subtemplate','download_data.inc.tpl');
     break;
    
    case 'download':
     // check table
     $dbr = Database::$connection->prepare("SELECT id,
                                                   table_name,
                                                   title,
                                                   type,
                                                   status,
                                                   parent_table
                                            FROM ".Database::$db_settings['data_models_table']."
                                            WHERE id=:id
                                            LIMIT 1");
    
     $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
     $dbr->execute();
     $row = $dbr->fetch();
     if(isset($row['id']))
      {
       $column_header_labels = isset($_POST['column_header_labels']) ? true : false;
       $labels = isset($_POST['labels']) ? true : false;
       $metadata = isset($_POST['metadata']) ? true : false;
       $spatial_metadata = isset($_POST['spatial_metadata']) ? true : false;
       $wkt = isset($_POST['wkt']) ? true : false;
       $join = isset($_POST['join']) ? true : false;
       $format = isset($_POST['format']) ? $_POST['format'] : 'csv';
 
       switch($format)
        {
         case 'xls':
         case 'xlsx':
           $data = get_data($_REQUEST['id'], false, $wkt, $column_header_labels, $labels, $metadata, $spatial_metadata, $join);
           if($join && $row['parent_table']) $filename = $row['table_name'].'_parent_join';
           else $filename = $row['table_name'];
           if($format=='xls') $filename .= '.xls';
           else $filename .= '.xlsx'; 
           require BASE_PATH.'lib/PHPExcel/PHPExcel.php';
           set_time_limit(0);
           ini_set('memory_limit', '-1');
           $objPHPExcel = new PHPExcel();
           $objPHPExcel->setActiveSheetIndex(0);
           $objPHPExcel->getProperties()->setCreator($settings['website_title'])->setTitle($row['title']);
           $objPHPExcel->getActiveSheet()->fromArray($data, null, 'A1', true);
           $objPHPExcel->getActiveSheet()->setTitle(truncate($row['table_name'], 25));
           if($format=='xls') header('Content-Type: application/vnd.ms-excel');
           else header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
           header('Content-Disposition: attachment;filename="'.$filename.'"');
           header('Cache-Control: max-age=0');        
           if($format=='xls') $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
           else $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
           $objWriter->save('php://output');          
           exit;
           break;          
         case 'xml':
           $data = get_data($_REQUEST['id'], false, $wkt, $column_header_labels, $labels, $metadata, $spatial_metadata, $join);
           if($join && $row['parent_table']) $filename = $row['table_name'].'_parent_join.xml';
           else $filename = $row['table_name'].'.xml';
           require BASE_PATH.'lib/php-export-data/php-export-data.class.php';
           $exporter = new ExportDataExcel('browser', $filename);
           $exporter->initialize();
           if($data) foreach($data as $data_row) $exporter->addRow($data_row);
           $exporter->finalize();
           exit;
           break;
         case 'shp':
           if($row['type']==1)
            {
             $query = get_query($_REQUEST['id'], true, false, $join, $column_header_labels, $labels);
             #showme($query);
             $filename = $row['table_name'].'.shp';
             $tmp_name = uniqid();
             $tmp_dir = sys_get_temp_dir().'/'.$tmp_name;
             mkdir($tmp_dir);
             //exec('ogr2ogr -f "ESRI Shapefile" '.$tmp_dir.'/'.$filename.' PG:"host='.$db_settings['host'].' dbname='.$db_settings['database'].' user='.$db_settings['user'].' password='.$db_settings['password'].'" -sql "SELECT * from '.$row['table_name'].' WHERE geom IS NOT NULL"');
             #echo 'ogr2ogr -f "ESRI Shapefile" '.$tmp_dir.'/'.$filename.' PG:"dbname=\''.$db_settings['database'].'\' host=\''.$db_settings['host'].'\' port=\''.$db_settings['port'].'\' user=\''.$db_settings['user'].'\' password=\''.$db_settings['password'].'\'" -sql "'.$query.'"';
             #exit;
             exec('ogr2ogr -f "ESRI Shapefile" '.$tmp_dir.'/'.$filename.' PG:"dbname=\''.$db_settings['database'].'\' host=\''.$db_settings['host'].'\' port=\''.$db_settings['port'].'\' user=\''.$db_settings['user'].'\' password=\''.$db_settings['password'].'\'" -sql "'.$query.'"');
             exec('zip -rj '.sys_get_temp_dir().'/'.$tmp_name.'.zip '.$tmp_dir);
             $filesize = filesize(sys_get_temp_dir().'/'.$tmp_name.'.zip');
             header('Content-Length: ' . $filesize);
             header('Content-Type: application/zip'); 
             header('Content-Disposition: attachment; filename='.$filename.'.zip');
             readfile(sys_get_temp_dir().'/'.$tmp_name.'.zip'); 
             unlink(sys_get_temp_dir().'/'.$tmp_name.'.zip');
             @unlink(sys_get_temp_dir().'/'.$filename.'.zip');
             rrmdir($tmp_dir);
             exit;
            }
           else
            {
             $errors[] = 'download_data_error_not_spatial';
            } 
           break;        
         case 'kml':
           if($row['type']==1)
            {
             $query = get_query($_REQUEST['id'], true, false, $join, $column_header_labels, $labels);
             $filename = $row['table_name'].'.kml';
             $tmp_name = uniqid();
             $title = htmlspecialchars($row['title'], ENT_QUOTES);
             #exec('ogr2ogr -f "KML" '.BASE_PATH.'tmp/'.$tmp_name.'.kml PG:"host='.$db_settings['host'].' dbname='.$db_settings['database'].' user='.$db_settings['user'].' password='.$db_settings['password'].'" -sql "SELECT * from '.$row['table_name'].' WHERE geom IS NOT NULL"');
             exec('ogr2ogr -f "KML" '.sys_get_temp_dir().'/'.$tmp_name.'.kml PG:"dbname=\''.$db_settings['database'].'\' host=\''.$db_settings['host'].'\' port=\''.$db_settings['port'].'\' user=\''.$db_settings['user'].'\' password=\''.$db_settings['password'].'\'" -sql "'.$query.'" -nln '.$row['table_name']);
             $filesize = filesize(sys_get_temp_dir().'/'.$tmp_name.'.kml');
             header('Content-Length: ' . $filesize);
             header('Content-Type: application/vnd.google-earth.kml+xml'); 
             header('Content-Disposition: attachment; filename='.$filename);
             readfile(sys_get_temp_dir().'/'.$tmp_name.'.kml'); 
             unlink(sys_get_temp_dir().'/'.$tmp_name.'.kml');
             @unlink(sys_get_temp_dir().'/'.$filename);
             exit;
            }
           else
            {
             $errors[] = 'download_data_error_not_spatial';
            } 
           break;        
         
         default:
           $data = get_data($_REQUEST['id'], false, $wkt, $column_header_labels, $labels, $metadata, $spatial_metadata, $join);
           if($join && $row['parent_table']) $filename = $row['table_name'].'_parent_join.csv';
           else $filename = $row['table_name'].'.csv';
           $output = fopen('php://output', 'w');
           header('Content-Type: text/csv; charset=utf-8');
           header('Content-Disposition: attachment; filename='.$filename);
           #header("content-length: ".strlen($csvdata));
           if($data) foreach($data as $data_row) fputcsv($output, $data_row);
           exit;
        }
      }
     if(isset($errors))
      {
       if(isset($data)) $template->assign('data', $data);
       $template->assign('id', intval($_REQUEST['id']));
       $template->assign('subtitle', $lang['download_data_subtitle']);
       $template->assign('subtemplate','download_data.inc.tpl');
      }
      
     break;
   }
 }
?>
