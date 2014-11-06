<?php
if(!defined('IN_INDEX')) exit;

if(isset($_REQUEST['data_id']) && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($_REQUEST['data_id']), Permission::WRITE)))
 {
  $table_info = get_table_info($_REQUEST['data_id']);

  if($table_info['table']['readonly']==0)
   {     
     if(isset($_REQUEST['disable_map']))
      {
       $_REQUEST['disable_map'] = $_REQUEST['disable_map'] ? 1 : 0;
       set_user_setting();
      }

    // db table item types:
    include(BASE_PATH.'config/column_types.conf.php');  
  
    // foreign key:
    $fk = isset($_REQUEST['fk']) ? intval($_REQUEST['fk']) : 0;

    // basemaps:
    if($table_info['table']['type']==1 && empty($table_info['table']['latlong_entry']) && empty($_SESSION[$settings['session_prefix'].'usersettings']['disable_map']))
     {
      if($basemaps = get_basemaps($table_info['table']['basemaps']))
       {
        $template->assign('basemaps', $basemaps);
        foreach($basemaps as $basemap)
         {
          if($basemap['js'] && !in_array($basemap['js'], $javascripts)) $javascripts[] = $basemap['js'];
         }
       }  
     }
  
    switch($action)
     {
      case 'add':
       // if spatial data, get current map position and zoom level:
       if($table_info['table']['type']==1)
       {
         // get current map position and zoom level:
         if(isset($_GET['current_position']))
          {
           $currentpos_parts = explode(',', $_GET['current_position']);
           if(isset($currentpos_parts[0]) && isset($currentpos_parts[1]) && isset($currentpos_parts[2]))
            {
             $current_position['longitude'] = floatval($currentpos_parts[0]);
             $current_position['latitude'] = floatval($currentpos_parts[1]);
             $current_position['zoomlevel'] = floatval($currentpos_parts[2]);
             $template->assign('current_position', $current_position);
            }
          }
        }
     if($table_info['table']['parent_table'] && empty($fk)) $template->assign('child_data_without_fk', true);
     $template->assign('fk', $fk);
     $template->assign('table_data', $table_info['table']);
     $template->assign('data_type', intval($table_info['table']['type']));
     $template->assign('geometry_type', intval($table_info['table']['geometry_type']));
     $template->assign('latlong_entry', intval($table_info['table']['latlong_entry']));
     $template->assign('geometry_required', intval($table_info['table']['geometry_required']));
     $template->assign('min_scale', floatval($table_info['table']['min_scale']));
     $template->assign('max_scale', floatval($table_info['table']['max_scale']));
     $template->assign('max_resolution', floatval($table_info['table']['max_resolution']));
     $template->assign('auxiliary_layer_1', intval($table_info['table']['auxiliary_layer_1']));
     $template->assign('auxiliary_layer_1_title', htmlspecialchars($table_info['table']['auxiliary_layer_1_title']));
     if($table_info['table']['auxiliary_layer_1_stef']) $template->assign('auxiliary_layer_1_redraw', true);
     
     if(isset($table_info['columns'])) $template->assign('columns', $table_info['columns']);
     if(isset($table_info['sections'])) $template->assign('sections', $table_info['sections']);
     $template->assign('subtitle', $lang['add_data_item_subtitle']); 
     $javascripts[] = JQUERY_UI;
     $javascripts[] = JQUERY_UI_HANDLER;
     $stylesheets[] = JQUERY_UI_CSS;
     if($table_info['table']['type']==1 && empty($table_info['table']['latlong_entry']) && empty($_SESSION[$settings['session_prefix'].'usersettings']['disable_map'])) // spatial
      {
       $javascripts[] = OPENLAYERS;
       $javascripts[] = OPENLAYERS_DRAW;
       #$javascripts[] = GOOGLE_MAPS;
       $stylesheets[] = OPENLAYERS_CSS;      
       $template->assign('help', 'add_data_item_spatial');
      }
     else
      {
       $template->assign('help', 'add_data_item_common');
      }
     $template->assign('subtemplate', 'edit_data_item.inc.tpl');

     // form session to prevent repeated submissions:
     $formsession = uniqid();
     $_SESSION[$settings['session_prefix'].'formsession_'.$formsession] = true;
     $template->assign('formsession', $formsession);
     
     // set remebered input values:
     if(isset($table_info['columns']))
      {
       foreach($table_info['columns'] as $column)
        {
         if($column['type']>0 && isset($_SESSION[$settings['session_prefix'].'usersettings']['input_value'][$column['id']]))
          {
           $data_item[$column['name']] = htmlspecialchars($_SESSION[$settings['session_prefix'].'usersettings']['input_value'][$column['id']]);
           $template->assign('remembered_values', true);
          }
         elseif($column['column_default_value'])
          {
           $data_item[$column['name']] = htmlspecialchars($column['column_default_value']);
          }
         if($column['definition']) $template->assign('definition', true);
        }
       if(isset($data_item)) $template->assign('data_item', $data_item);
      }
     break;
   
    case 'edit':
     // build query:
     if(isset($table_info['columns']))
      {
       $i=0;
       foreach($table_info['columns'] as $column)
        {
         if($column['type']>0) $select_query_parts[] = 'table'.$table_info['table']['id'].'.'.$column['name'];
         if($column['relation_table'])
          {
           $joins[$i]['table'] = $table_info['table']['id'];
           $joins[$i]['alias'] = 'table'.$table_info['table']['id'].'_'.$i; // unique table alias
           $joins[$i]['relation_table'] = $column['relation_table'];
           $joins[$i]['relation_table_name'] = $column['relation_table_name'];
           $joins[$i]['relation_column_name'] = $column['name'];
           $joins[$i]['fk'] = $column['name'];
           $select_query_parts[] = $joins[$i]['alias'].'.'.$column['relation_column_name'].' AS '.$column['name'];
          }
         ++$i;
        }
      }             
     if(isset($select_query_parts)) $select_query = ', '. implode(', ', $select_query_parts);
     else $select_query = '';
     $query = "SELECT table".$table_info['table']['id'].".id, table".$table_info['table']['id'].".fk";
     if($table_info['table']['type']==1)
      {
       #if($table_info['table']['latlong_entry']) $query .= ", ST_AsText(table".$table_info['table']['id'].".geom) AS wkt, ST_X(geom) as _longitude, ST_Y(geom) as _latitude";
       $query .= ", ST_AsText(table".$table_info['table']['id'].".geom) AS _wkt, ST_GeometryType(table".$table_info['table']['id'].".geom) AS _geometry_type";
      }
     $query .= $select_query;
     $query .= "\nFROM \"".$table_info['table']['table_name']."\" AS table".$table_info['table']['id'];
     if(isset($joins))
       {
        foreach($joins as $join)
         {
          $query .= "\nLEFT JOIN ".$join['relation_table_name']." AS ".$join['alias']." ON table".$table_info['table']['id'].".".$join['fk']."=".$join['alias'].".id";
         }
       }                                       
     $query .= "\nWHERE table".$table_info['table']['id'].".id=:id LIMIT 1";
     $dbr = Database::$connection->prepare($query);
     $dbr->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
     $dbr->execute();
     $row = $dbr->fetch();
     if(isset($row['id']))
      {
       $template->assign('item_exists', true);
       $data_item['id'] = intval($row['id']);
       $data_item['fk'] = intval($row['fk']);
       if($table_info['table']['type']==1)
        {
         $data_item['_wkt'] = $row['_wkt'];
         if(($table_info['table']['latlong_entry'] || isset($_SESSION[$settings['session_prefix'].'usersettings']['disable_map']) && $_SESSION[$settings['session_prefix'].'usersettings']['disable_map']) && $row['_geometry_type']=='ST_Point')
          {
           $dbr = Database::$connection->prepare('SELECT ST_X(ST_GeomFromEWKT(:wkt)) as _longitude, ST_Y(ST_GeomFromEWKT(:wkt)) as _latitude');
           $dbr->bindValue(':wkt', $row['_wkt'], PDO::PARAM_STR);
           $dbr->execute();
           $latlongdata = $dbr->fetch();
           if(isset($latlongdata['_longitude']))
            {
             $data_item['_latitude'] = $latlongdata['_latitude'];
             $data_item['_longitude'] = $latlongdata['_longitude'];
            }
          }
        }
       if(isset($table_info['columns']))
        {
         foreach($table_info['columns'] as $column)
          {
           if($column['type']>0) $data_item[$column['name']] = htmlspecialchars($row[$column['name']]);
          }
        }
       if(isset($joins))
        {
         foreach($joins as $join)
          {
           $data_item[$join['relation_column_name']] = htmlspecialchars($row[$join['relation_column_name']]);
          }
        }
       $template->assign('fk', $data_item['fk']);
       $template->assign('data_item', $data_item);
      }
     else
      {
       $template->assign('item_exists', false);
       $template->assign('fk', $fk);
      }
     $template->assign('table_data', $table_info['table']);
     $template->assign('data_type', intval($table_info['table']['type']));
     $template->assign('geometry_type', intval($table_info['table']['geometry_type']));
     $template->assign('latlong_entry', intval($table_info['table']['latlong_entry']));
     $template->assign('geometry_required', intval($table_info['table']['geometry_required']));
     $template->assign('min_scale', floatval($table_info['table']['min_scale']));
     $template->assign('max_scale', floatval($table_info['table']['max_scale']));
     $template->assign('max_resolution', floatval($table_info['table']['max_resolution']));
     $template->assign('auxiliary_layer_1', intval($table_info['table']['auxiliary_layer_1']));
     $template->assign('auxiliary_layer_1_title', htmlspecialchars($table_info['table']['auxiliary_layer_1_title']));
     if($table_info['table']['auxiliary_layer_1_stef']) $template->assign('auxiliary_layer_1_redraw', true);     
     
     if(isset($table_info['columns'])) $template->assign('columns', $table_info['columns']);
     if(isset($table_info['sections'])) $template->assign('sections', $table_info['sections']);     
     $template->assign('subtitle', $lang['edit_data_item_subtitle']); 
     $javascripts[] = JQUERY_UI;
     $javascripts[] = JQUERY_UI_HANDLER;
     $stylesheets[] = JQUERY_UI_CSS;     
     if($table_info['table']['type']==1 && empty($table_info['table']['latlong_entry']) && empty($_SESSION[$settings['session_prefix'].'usersettings']['disable_map'])) // spatial
      {
       $javascripts[] = OPENLAYERS;
       $javascripts[] = OPENLAYERS_DRAW;
       #$javascripts[] = GOOGLE_MAPS;
       $stylesheets[] = OPENLAYERS_CSS;                  
       $template->assign('help', 'edit_data_item_spatial');
      }     
     else
      {
       $template->assign('help', 'edit_data_item_common');
      }
     

     $template->assign('subtemplate', 'edit_data_item.inc.tpl');
     break;

    case 'add_submit':
    case 'edit_submit':
     // form session check:
     if(empty($_POST['id']))
      {
       if(empty($_POST['formsession']) || empty($_SESSION[$settings['session_prefix'].'formsession_'.$_POST['formsession']]))
        {
         $errors[] = 'error_invalid_form';
         # reset formsession:
         $formsession = uniqid();
         $_POST['formsession'] = $formsession;
         $_SESSION[$settings['session_prefix'].'formsession_'.$formsession] = true;
        }
      } 

     if(empty($_POST['id']) && $table_info['table']['parent_table'] && empty($fk)) $errors[] = 'child_data_without_fk';
     
     // check geometry and get area/perimeter/length:
     if($table_info['table']['type']==1)
      {
       if(isset($_POST['data']['_latitude']) && isset($_POST['data']['_longitude']))
        {
         if(is_numeric($_POST['data']['_latitude'])
         && floatval($_POST['data']['_latitude'])>=-90
         && floatval($_POST['data']['_latitude'])<=90
         && is_numeric($_POST['data']['_longitude'])
         && floatval($_POST['data']['_longitude'])>=-180
         && floatval($_POST['data']['_longitude'])<=180)
          {
           $latitude = floatval($_POST['data']['_latitude']);
           $longitude = floatval($_POST['data']['_longitude']);
           $dbr = Database::$connection->prepare("SELECT ST_AsText(ST_SetSRID(ST_MakePoint(:long, :lat), 4326)) AS _wkt");
           $dbr->bindParam(':long', $longitude, PDO::PARAM_STR);
           $dbr->bindParam(':lat', $latitude, PDO::PARAM_STR);
           $dbr->execute();
           $data = $dbr->fetch();
           if(isset($data['_wkt'])) $_POST['data']['_wkt'] = $data['_wkt'];
          }
         else
          {
           $errors[] = 'error_invalid_latlong'; 
           $error_fields[] = '_latlong';
          }
        }

       if(empty($errors))
        {
         if(isset($_POST['data']['_wkt']) && $_POST['data']['_wkt']!='' && $_POST['data']['_wkt']!='GEOMETRYCOLLECTION(EMPTY)')
          {
           try
            {
             $wkt_check_result = Database::$connection->prepare("SELECT ST_IsValid(ST_GeomFromText(:wkt), ".$settings['allow_ring_self_intersections'].") AS is_valid, ST_IsValidReason(ST_GeomFromText(:wkt)) as validity_info, ST_GeometryType(ST_GeomFromText(:wkt)) AS geometry_type, ST_Y(ST_Centroid(ST_GeomFromText(:wkt))) as latitude, ST_X(ST_Centroid(ST_GeomFromText(:wkt))) as longitude, ST_Length(ST_GeogFromText(:wkt)) as length, ST_Area(ST_GeogFromText(:wkt)) as area, ST_Perimeter(ST_GeogFromText(:wkt)) as perimeter");
             $wkt_check_result->bindParam(':wkt', $_POST['data']['_wkt'], PDO::PARAM_STR);
             $wkt_check_result->execute();
             $row = $wkt_check_result->fetch();
             if($row['is_valid'])
              {
               $geometry_type = $row['geometry_type'];
               $latitude = $row['latitude'];
               $longitude = $row['longitude'];
               $length = $row['length'];
               $area = $row['area'];
               $perimeter = $row['perimeter'];
              }
             else
              {
               $errors[] = str_replace('[reason]', $row['validity_info'], $lang['error_invalid_geometry_reason']);
               $error_fields[] = '_latlong';
              }
            }
           catch(Exception $exception)
            {
             $errors[] = 'error_invalid_geometry';
             $error_fields[] = '_latlong';             
            } 
          }
         else
          {
           if($table_info['table']['geometry_required'])
            {
             $errors[] = 'error_no_geometry'; 
             $error_fields[] = '_latlong';
            }
           $_POST['data']['_wkt'] = NULL;
           $geometry_type = NULL;
           $latitude = 0;
           $longitude = 0;
           $length = 0;
           $area = 0;
           $perimeter = 0;
          }
        }
       
       if(empty($errors) && !is_null($_POST['data']['_wkt']) && $table_info['table']['geometry_type']!=0)
        {
         if($table_info['table']['geometry_type']==1 && $geometry_type!='ST_Point' || $table_info['table']['geometry_type']==2 && $geometry_type!='ST_LineString' || $table_info['table']['geometry_type']==3 && $geometry_type!='ST_Polygon')
          {
           $errors[] = 'error_invalid_geometry';
           $error_fields[] = '_latlong';
          }
        }
       
       if(empty($errors) && !is_null($_POST['data']['_wkt']) && $table_info['table']['boundary_layer']) // check if geometry is within boundary:
        {
         $dbr = Database::$connection->prepare("SELECT table_name FROM ".$db_settings['data_models_table']." WHERE id=:id LIMIT 1");
         $dbr->bindValue(':id', $table_info['table']['boundary_layer'], PDO::PARAM_INT);
         $dbr->execute();
         $data = $dbr->fetch();
         if(isset($data['table_name']))
          {
           $dbr = Database::$connection->prepare('SELECT ST_Contains(boundary, ST_GeomFromText(:geometry, 4326)) AS boundary_contains_geometry FROM (SELECT ST_Union(geom) AS boundary FROM "'.$data['table_name'].'") AS boundary_layer');
           $dbr->bindValue(':geometry', $_POST['data']['_wkt'], PDO::PARAM_STR);
           $dbr->execute();
           list($boundary_contains_geometry) = $dbr->fetch();
           if(empty($boundary_contains_geometry))
            {
             $errors[] = 'error_geometry_exceeds_boundary';
             $error_fields[] = '_latlong';
            }
          }
        }
       }
          
     // import and check attributes:
     if(isset($table_info['columns']))
      {
       foreach($table_info['columns'] as $column)
        {
         // replace choice-wildcard
         if(isset($_POST['data'][$column['name']]) && $_POST['data'][$column['name']]=='*' && isset($_POST['data']['_'.$column['name'].'_']))
          {
           $_POST['data'][$column['name']] = $_POST['data']['_'.$column['name'].'_'];
          }
         if(!isset($_POST['data'][$column['name']]) || (isset($_POST['data'][$column['name']]) && trim($_POST['data'][$column['name']])==''))
          {
           if($column['required'])
            {
             $errors[] = str_replace('[field]', $column['label'], $lang['required_field_message']);
             $error_fields[] = $column['name'];
            }
           else
            { 
             $submitted_data[$column['name']] = NULL;
            }
          }
         else
          { 
           if($column['unique'])
            {
             if(isset($_POST['id'])) // edit
              { 
               $count_result = Database::$connection->prepare('SELECT COUNT(*) FROM "'.$table_info['table']['table_name'].'" WHERE LOWER(CAST("'.$column['name'].'" AS TEXT))=LOWER(:value) AND id!=:id');
               $count_result->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
              }
             else // add
              {
               $count_result = Database::$connection->prepare('SELECT COUNT(*) FROM "'.$table_info['table']['table_name'].'" WHERE LOWER(CAST("'.$column['name'].'" AS TEXT))=LOWER(:value)');
              } 
             $count_result->bindValue(':value', trim($_POST['data'][$column['name']]), PDO::PARAM_STR);
             $count_result->execute();
             list($count) = $count_result->fetch();
             if($count)
              {
               $errors[] = str_replace('[field]', $column['label'], $lang['unique_field_message']);
               $error_fields[] = $column['name'];               
              }             
            }
           if($column['relation_table'])
            {
             // if required, at lest actual field or autocomplete helper field is needed:
             if($column['required'] && empty($_POST['data'][$column['name']]))
              {
               $errors[] = str_replace('[field]', $column['label'], $lang['required_field_message']);
               $error_fields[] = $column['name'];
              }
             elseif($_POST['data'][$column['name']])
              {
               $_POST['data'][$column['name']] = trim($_POST['data'][$column['name']]);
               // Value provided - check if it's clearly assignable / nonambiguous:
               $dbr = Database::$connection->prepare("SELECT id
                                                      FROM ".$column['relation_table_name']."
                                                      WHERE LOWER(".$column['relation_column_name'].")=LOWER(:column)
                                                      LIMIT 1");
               $dbr->bindParam(':column', $_POST['data'][$column['name']], PDO::PARAM_STR);
               $dbr->execute();
               // set selected id to actual field value:
               list($related_id) = $dbr->fetch();
           
               if($related_id)
                {
                 // Exact match, overwrite submitted data with actual relation id;
                 // keep posted string value for possible errors:
                 //$_POST['data'][$column['name']] = $submitted_data[$column['name']];
                 $submitted_data[$column['name']] = $related_id;
                }
               else
                {  
                 // Field value / id empty --> there was no exact match. Try a LIKE search:
                 $dbr = Database::$connection->prepare("SELECT id, ".$column['relation_column_name']."
                                                        FROM ".$column['relation_table_name']."
                                                        WHERE LOWER(".$column['relation_column_name'].") LIKE LOWER(:column)
                                                        ORDER BY ".$column['relation_column_name']." ASC
                                                        LIMIT 10");
                 $dbr->bindValue(':column', '%'.$_POST['data'][$column['name']].'%', PDO::PARAM_STR);
                 $dbr->execute();   
             
                 $related_items_count = $dbr->rowCount();
             
                 if($related_items_count==0)
                  {
                   // item was not found
                   $errors[] = str_replace('[field]', htmlspecialchars($column['label']), str_replace('[item]', htmlspecialchars($_POST['data'][$column['name']]), $lang['related_item_not_found']));
                   $error_fields[] = $column['name'];
                  }
                 elseif($related_items_count==1)
                  {
                    // The LIKE serach produced one result but we cannot be sure, it's the intended one.
                   // Return the result for verification:
                   $row = $dbr->fetch();
                   $_POST['data'][$column['name']] = $row[$column['relation_column_name']];
                   $errors[] = str_replace('[field]', htmlspecialchars($column['label']), str_replace('[item]', htmlspecialchars($_POST['data'][$column['name']]), $lang['related_item_verification']));
                   $error_fields[] = $column['name'];
                  }
                  else 
                  {
                   // The LIKE serach produced more than one result.
                   // Return the options for verification:
                   $i=0;
                   while($row = $dbr->fetch()) 
                    {
                     $verification_options[$column['name']][$i] = htmlspecialchars($row[$column['relation_column_name']]);     
                     ++$i;
                    }   
                   $errors[] = str_replace('[field]', htmlspecialchars($column['label']), str_replace('[item]', htmlspecialchars($_POST['data'][$column['name']]), $lang['related_item_verification_select']));
                   $error_fields[] = $column['name'];
                  }
                }
              }
            }
           elseif($column['type']==2 || $column['type']==3) // integer or smallint
            {
             $submitted_data[$column['name']] = $_POST['data'][$column['name']];
             // check format:
             if(!empty($_POST['data'][$column['name']]) && $_POST['data'][$column['name']] !== (string)(int)$_POST['data'][$column['name']])
              {
               $errors[] = str_replace('[field]', $column['label'], $lang['invalid_input_format']);
               $error_fields[] = $column['name'];
              }
             // check minimum value:
             elseif($column['range_from']!==null && intval($_POST['data'][$column['name']])<$column['range_from'])
              {
               $errors[] = str_replace('[minimum]', $column['range_from'], str_replace('[field]', $column['label'], $lang['input_number_too_low']));
               $error_fields[] = $column['name'];              
              }
             // check maximum value:
             elseif($column['range_to']!==null && intval($_POST['data'][$column['name']])>$column['range_to'])
              {
               $errors[] = str_replace('[maximum]', $column['range_to'], str_replace('[field]', $column['label'], $lang['input_number_too_high']));
               $error_fields[] = $column['name'];              
              }              
            #else
            #  {
            #   $submitted_data[$column['name']] = intval($_POST['data'][$column['name']]);
            #  }
            }
           elseif($column['type']==4) // float
            {
             $submitted_data[$column['name']] = $_POST['data'][$column['name']];
             
             #if(!empty($submitted_data[$column['name']]) && $submitted_data[$column['name']] !== (string)(float)$submitted_data[$column['name']])
             if(!empty($submitted_data[$column['name']]) && !is_numeric($submitted_data[$column['name']]))
              {
               $errors[] = str_replace('[field]', $column['label'], $lang['invalid_input_format']);
               $error_fields[] = $column['name'];
              }
             // check minimum value:
             elseif($column['range_from']!==null && floatval($submitted_data[$column['name']])<$column['range_from'])
              {
               $errors[] = str_replace('[minimum]', $column['range_from'], str_replace('[field]', $column['label'], $lang['input_number_too_low']));
               $error_fields[] = $column['name'];              
              }
             // check maximum value:
             elseif($column['range_to']!==null && floatval($submitted_data[$column['name']])>$column['range_to'])
              {
               $errors[] = str_replace('[maximum]', $column['range_to'], str_replace('[field]', $column['label'], $lang['input_number_too_high']));
               $error_fields[] = $column['name'];              
              }                          
            #else
            #  {
            #   $submitted_data[$column['name']] = floatval($submitted_data[$column['name']]);
            #  }
            }             
           elseif($column['type']==7) // date
            {
             if(my_checkdate($_POST['data'][$column['name']])==false)
              {
               $errors[] = str_replace('[field]', $column['label'], $lang['invalid_date_message']);
               $error_fields[] = $column['name'];
              }
             else
              {
               $submitted_data[$column['name']] = $_POST['data'][$column['name']];
              }
            }
           elseif($column['type']==8) // time
            {
             if(!$submitted_data[$column['name']] = validate_time($_POST['data'][$column['name']]))
              {
               $errors[] = str_replace('[field]', $column['label'], $lang['invalid_time_message']);
               $error_fields[] = $column['name'];
              }
            }            
           else
            {
             // check minimum string length:
             if($column['range_from']!==null && mb_strlen(trim($_POST['data'][$column['name']]))<$column['range_from'])
              {
               $errors[] = str_replace('[minimum]', $column['range_from'], str_replace('[field]', $column['label'], $lang['input_length_too_low']));
               $error_fields[] = $column['name'];              
              }
             // check maximum string length:
             elseif($column['range_to']!==null && mb_strlen(trim($_POST['data'][$column['name']]))>$column['range_to'])
              {
               $errors[] = str_replace('[maximum]', $column['range_to'], str_replace('[field]', $column['label'], $lang['input_length_too_high']));
               $error_fields[] = $column['name'];              
              }              
             elseif($column['regex'] && !preg_match($column['regex'], trim($_POST['data'][$column['name']])))
              {
               $errors[] = str_replace('[field]', $column['label'], $lang['invalid_input_format']);
               $error_fields[] = $column['name'];               
              }
             else
              {
               $submitted_data[$column['name']] = trim($_POST['data'][$column['name']]);
              }
            }
       
           // check choices:
           if($column['choices'] && !in_array('*', $column['choices']) && !in_array($submitted_data[$column['name']], $column['choices']))
            {
             $errors[] = str_replace('[field]', $column['label'], $lang['invalid_choice']);
             $error_fields[] = $column['name'];               
            }
           
           if($column['column_length'] && strlen($submitted_data[$column['name']])>$column['column_length'])
            {
             $errors[] = str_replace('[inserted_characters]', strlen($submitted_data[$column['name']]), str_replace('[max_characters]', $column['column_length'], str_replace('[field]', $column['label'], $lang['field_length_exceeded_message'])));
             $error_fields[] = $column['name'];
            }         
          }
        }
      }

     if(empty($errors))
      {
       if(isset($_POST['id'])) // edit
        {
         // get current data for the activity log:
         $dbr = Database::$connection->prepare('SELECT * FROM "'.$table_info['table']['table_name'].'" WHERE id=:id LIMIT 1');
         $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
         $dbr->execute();
         $previous_data = serialize($dbr->fetch(PDO::FETCH_ASSOC));
         
         // build query:
         foreach($table_info['columns'] as $column)
          {
           if($column['type']>0) $update_query_parts[] = '"'.$column['name'].'"=:'.$column['name'];
          }        
         if(isset($update_query_parts)) $update_query = ', ' . implode(', ', $update_query_parts); 
         else $update_query = '';    
         
         if($table_info['table']['type']==1) // spatial
          {
           $dbr = Database::$connection->prepare("UPDATE \"".$table_info['table']['table_name']."\" SET last_editor=:last_editor, last_edited=NOW(), geom=ST_GeomFromText(:geom, 4326), area=".$area.", perimeter=".$perimeter.", length=".$length.", latitude=".$latitude.", longitude=".$longitude.$update_query." WHERE id=:id");
           $dbr->bindParam(':geom', $_POST['data']['_wkt'], PDO::PARAM_STR);
          }
         else // common
          {
           $dbr = Database::$connection->prepare("UPDATE \"".$table_info['table']['table_name']."\" SET last_editor=:last_editor, last_edited=NOW()".$update_query." WHERE id=:id");
          }       
         $dbr->bindParam(':last_editor', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
         $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
        }
       else // add
        {
         // build query:
         if(isset($table_info['columns']))
          {
           foreach($table_info['columns'] as $column)
            {
             if($column['type']>0)
              {
               $insert_query_parts[] = '"'.$column['name'].'"';
               $values_query_parts[] = ':'.$column['name'];
              }
            }
           $insert_query = ', ' . implode(', ', $insert_query_parts);
           $values_query = ', ' . implode(', ', $values_query_parts);
          }        
         else
          {
           $insert_query = '';
           $values_query = '';
          }
        
         if($table_info['table']['type']==1) // spatial
          {
           $dbr = Database::$connection->prepare("INSERT INTO \"".$table_info['table']['table_name']."\" (fk, creator, created, geom, area, perimeter, length, latitude, longitude".$insert_query.") VALUES (:fk, :creator, NOW(), ST_GeomFromText(:geom, 4326), ".$area.", ".$perimeter.", ".$length.", ".$latitude.", ".$longitude.$values_query.")");
           $dbr->bindParam(':geom', $_POST['data']['_wkt'], PDO::PARAM_STR);
          }
         else // common
          {
           $dbr = Database::$connection->prepare("INSERT INTO \"".$table_info['table']['table_name']."\" (fk, creator, created".$insert_query.") VALUES (:fk, :creator, NOW()".$values_query.")");
          }
         $dbr->bindParam(':fk', $fk, PDO::PARAM_INT);
         $dbr->bindParam(':creator', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
        }
        
       // bind custom columns:
       foreach($table_info['columns'] as $column)
        {
         if($column['type']>0)
          {
           if(is_null($submitted_data[$column['name']]))
            {
             $dbr->bindParam(':'.$column['name'], $submitted_data[$column['name']], PDO::PARAM_NULL);
            }
           elseif($column_types[$column['type']]['id']==BOOLEAN)
            {
             $dbr->bindParam(':'.$column['name'], $submitted_data[$column['name']], PDO::PARAM_BOOL);
            }
           elseif($column_types[$column['type']]['id']==INTEGER || $column_types[$column['type']]['id']==SMALLINT)
            {
             $dbr->bindParam(':'.$column['name'], $submitted_data[$column['name']], PDO::PARAM_INT);
            }
           elseif($column_types[$column['type']]['id']==NUMERIC)
            {
             $dbr->bindParam(':'.$column['name'], $submitted_data[$column['name']], PDO::PARAM_STR); // there's nothing like PDO::PARAM_DOUBLE
            }
           else
            {
             $dbr->bindParam(':'.$column['name'], $submitted_data[$column['name']], PDO::PARAM_STR);
            }
          }
        } 
        
       $dbr->execute();
        
       if(isset($_POST['id']))
        {
         $id = intval($_POST['id']);
         if($table_info['table']['parent_table'] && $fk) $qs_addition = '&attached_item_edited=true';
         else $qs_addition = '&item_edited=true';
         log_activity(4, $table_info['table']['id'], $id, $previous_data);
        }
       else // get last insert id if item was newly added:
        {
         unset($_SESSION[$settings['session_prefix'].'formsession_'.$_POST['formsession']]);
         $dbr = Database::$connection->query(LAST_INSERT_ID_QUERY);
         list($id) = $dbr->fetch();
         if($table_info['table']['parent_table'] && $fk) $qs_addition = '&attached_item_added=true';
         else $qs_addition = '&item_added=true';
         log_activity(3, $table_info['table']['id'], $id);
        }
        
       // if it is an attached item, go to the parent item data:
       if($table_info['table']['parent_table'] && $fk) header('Location: '.BASE_URL.'?r=data_item&data_id='.$table_info['table']['parent_table'].'&id='.$fk.$qs_addition.'#attached-data');
       else header('Location: '.BASE_URL.'?r=data_item&data_id='.$table_info['table']['id'].'&id='.$id.$qs_addition);
       exit;
      }
      else // errors
       {
        if(isset($_POST['id']))
         {
          #$questionnaire['id'] = intval($_POST['id']);
          $template->assign('subtitle', $lang['edit_data_item_subtitle']);
         }
        else
         {
          $template->assign('subtitle', $lang['add_data_item_subtitle']);
          $template->assign('formsession', htmlspecialchars($_POST['formsession']));
         } 

        // get posted data:
        foreach($table_info['columns'] as $column)
         {
          if($column['type']>0 && $column['relation_table'] && isset($_POST['data']['_'.$column['name'].'_'])) $data_item['_'.$column['name'].'_'] = htmlspecialchars($_POST['data']['_'.$column['name'].'_']);
          if($column['type']>0 && isset($_POST['data'][$column['name']])) $data_item[$column['name']] = htmlspecialchars($_POST['data'][$column['name']]);
         }

        // set id if item is edited:
        if(isset($_POST['id'])) $data_item['id'] = intval($_POST['id']);
        if(isset($_POST['data']['_wkt'])) $data_item['_wkt'] = htmlspecialchars($_POST['data']['_wkt']);
        if(isset($_POST['data']['_latitude'])) $data_item['_latitude'] = htmlspecialchars($_POST['data']['_latitude']);
        if(isset($_POST['data']['_longitude'])) $data_item['_longitude'] = htmlspecialchars($_POST['data']['_longitude']);
        
        // if edited, check if item exists:
        if(isset($data_item['id']))
         {
          $dbr = Database::$connection->prepare("SELECT COUNT(*)
                                                      FROM ".$table_info['table']['table_name']."
                                                      WHERE id=:id");
          $dbr->bindParam(':id', $data_item['id'], PDO::PARAM_INT);
          $dbr->execute();
          list($item_count) = $dbr->fetch();
          
          if($item_count==1) $template->assign('item_exists', true);
          else $template->assign('item_exists', false);
         }
        
        $template->assign('data_item', $data_item);
        $template->assign('fk', $fk);
        $template->assign('table_data', $table_info['table']);
        $template->assign('data_type', $table_info['table']['type']);
        $template->assign('geometry_type', intval($table_info['table']['geometry_type']));
        $template->assign('latlong_entry', intval($table_info['table']['latlong_entry']));
        $template->assign('geometry_required', intval($table_info['table']['geometry_required']));
        $template->assign('min_scale', floatval($table_info['table']['min_scale']));
        $template->assign('max_scale', floatval($table_info['table']['max_scale']));
        $template->assign('max_resolution', floatval($table_info['table']['max_resolution']));
        $template->assign('auxiliary_layer_1', intval($table_info['table']['auxiliary_layer_1']));
        $template->assign('auxiliary_layer_1_title', htmlspecialchars($table_info['table']['auxiliary_layer_1_title']));
        if($table_info['table']['auxiliary_layer_1_stef']) $template->assign('auxiliary_layer_1_redraw', true);
        
        if(isset($table_info['columns'])) $template->assign('columns', $table_info['columns']);

        $javascripts[] = JQUERY_UI;
        $javascripts[] = JQUERY_UI_HANDLER;
        $stylesheets[] = JQUERY_UI_CSS;
        
        if($table_info['table']['type']==1 && empty($table_info['table']['latlong_entry']) && empty($_SESSION[$settings['session_prefix'].'usersettings']['disable_map'])) // spatial
         {
          $javascripts[] = OPENLAYERS;
          $javascripts[] = OPENLAYERS_DRAW;
          $stylesheets[] = OPENLAYERS_CSS;                  
         }             
        
        $template->assign('errors', $errors);
        if(isset($error_fields)) $template->assign('error_fields', $error_fields);
        if(isset($verification_options)) $template->assign('verification_options', $verification_options);
        $template->assign('autofocus', 'errors');
        $template->assign('subtemplate','edit_data_item.inc.tpl');
       }     
     
     
     break;
    
   
     }
  
   }
 }
?>
