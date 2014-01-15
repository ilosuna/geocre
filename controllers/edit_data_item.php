<?php
if(!defined('IN_INDEX')) exit;

if(isset($_REQUEST['data_id']) && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($_REQUEST['data_id']), Permission::WRITE)))
 {
  $table_info = get_table_info($_REQUEST['data_id']);
  
  if($table_info['table']['readonly']==0)
   {
    // db table item types:
    include(BASE_PATH.'config/column_types.conf.php');  
  
    // foreign key:
    $fk = isset($_REQUEST['fk']) ? intval($_REQUEST['fk']) : 0;

    // basemaps:
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
     $template->assign('geometry_required', intval($table_info['table']['geometry_required']));
     
     $template->assign('min_scale', intval($table_info['table']['min_scale']));
     $template->assign('max_scale', intval($table_info['table']['max_scale']));
     $template->assign('auxiliary_layer_1', intval($table_info['table']['auxiliary_layer_1']));
     $template->assign('auxiliary_layer_1_title', htmlspecialchars($table_info['table']['auxiliary_layer_1_title']));
     
     if(isset($table_info['columns'])) $template->assign('columns', $table_info['columns']);
     if(isset($table_info['sections'])) $template->assign('sections', $table_info['sections']);
     $template->assign('subtitle', $lang['add_data_item_subtitle']); 
     $javascripts[] = JQUERY_UI;
     $javascripts[] = JQUERY_UI_HANDLER;
     $stylesheets[] = JQUERY_UI_CSS;
     if($table_info['table']['type']==1) // spatial
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
     if($table_info['table']['type']==1) $query .= ", ST_AsText(table".$table_info['table']['id'].".geom) AS wkt";
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
       if($table_info['table']['type']==1) $data_item['wkt'] = $row['wkt'];
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
     $template->assign('geometry_required', intval($table_info['table']['geometry_required']));
     $template->assign('min_scale', intval($table_info['table']['min_scale']));
     $template->assign('max_scale', intval($table_info['table']['max_scale']));
     $template->assign('auxiliary_layer_1', intval($table_info['table']['auxiliary_layer_1']));
     $template->assign('auxiliary_layer_1_title', htmlspecialchars($table_info['table']['auxiliary_layer_1_title']));     
     
     if(isset($table_info['columns'])) $template->assign('columns', $table_info['columns']);
     if(isset($table_info['sections'])) $template->assign('sections', $table_info['sections']);     
     $template->assign('subtitle', $lang['edit_data_item_subtitle']); 
     $javascripts[] = JQUERY_UI;
     $javascripts[] = JQUERY_UI_HANDLER;
     $stylesheets[] = JQUERY_UI_CSS;     
     if($table_info['table']['type']==1) // spatial
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
     if(empty($_POST['id']) && $table_info['table']['parent_table'] && empty($fk)) $errors[] = 'child_data_without_fk';
     
     // check geometry and get area/perimeter/length:
     if($table_info['table']['type']==1)
      {
       if(isset($_POST['wkt']) && $_POST['wkt']!='' && $_POST['wkt']!='GEOMETRYCOLLECTION(EMPTY)')
        {
         try
          {
           $wkt_check_result = Database::$connection->prepare("SELECT ST_IsValid(ST_GeomFromText(:wkt), ".$settings['allow_ring_self_intersections'].") AS is_valid, ST_IsValidReason(ST_GeomFromText(:wkt)) as validity_info, ST_Y(ST_Centroid(ST_GeomFromText(:wkt))) as latitude, ST_X(ST_Centroid(ST_GeomFromText(:wkt))) as longitude, ST_Length(ST_GeogFromText(:wkt)) as length, ST_Area(ST_GeogFromText(:wkt)) as area, ST_Perimeter(ST_GeogFromText(:wkt)) as perimeter");
           $wkt_check_result->bindParam(':wkt', $_POST['wkt'], PDO::PARAM_STR);
           $wkt_check_result->execute();
           $row = $wkt_check_result->fetch();
           if($row['is_valid'])
            {
             $latitude = $row['latitude'];
             $longitude = $row['longitude'];
             $length = $row['length'];
             $area = $row['area'];
             $perimeter = $row['perimeter'];
            }
           else
            {
             $errors[] = str_replace('[reason]', $row['validity_info'], $lang['error_invalid_geometry_reason']);
            }
          }
         catch(Exception $exception)
          {
           $errors[] = 'error_invalid_geometry';
          } 
        }
       else
        {
         if($table_info['table']['geometry_required']) $errors[] = 'error_empty_geometry'; 
         $_POST['wkt'] = NULL;
         $latitude = 0;
         $longitude = 0;
         $length = 0;
         $area = 0;
         $perimeter = 0;
        }
      }   
     // import and check attributes:
     if(isset($table_info['columns']))
      {
       foreach($table_info['columns'] as $column)
        {
         // replace choice-wildcard
         if(isset($_POST[$column['name']]) && $_POST[$column['name']]=='*' && isset($_POST['_'.$column['name'].'_']))
          {
           $_POST[$column['name']] = $_POST['_'.$column['name'].'_'];
          }
         if(!isset($_POST[$column['name']]) || (isset($_POST[$column['name']]) && trim($_POST[$column['name']])==''))
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
           if($column['relation_table'])
            {
             // if required, at lest actual field or autocomplete helper field is needed:
             if($column['required'] && empty($_POST[$column['name']]))
              {
               $errors[] = str_replace('[field]', $column['label'], $lang['required_field_message']);
               $error_fields[] = $column['name'];
              }
             elseif($_POST[$column['name']])
              {
               $_POST[$column['name']] = trim($_POST[$column['name']]);
               // Value provided - check if it's clearly assignable / nonambiguous:
               $dbr = Database::$connection->prepare("SELECT id
                                                      FROM ".$column['relation_table_name']."
                                                      WHERE LOWER(".$column['relation_column_name'].")=LOWER(:column)
                                                      LIMIT 1");
               $dbr->bindParam(':column', $_POST[$column['name']], PDO::PARAM_STR);
               $dbr->execute();
               // set selected id to actual field value:
               list($related_id) = $dbr->fetch();
           
               if($related_id)
                {
                 // Exact match, overwrite submitted data with actual relation id;
                 // keep posted string value for possible errors:
                 //$_POST[$column['name']] = $submitted_data[$column['name']];
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
                 $dbr->bindValue(':column', '%'.$_POST[$column['name']].'%', PDO::PARAM_STR);
                 $dbr->execute();   
             
                 $related_items_count = $dbr->rowCount();
             
                 if($related_items_count==0)
                  {
                   // item was not found
                    $errors[] = str_replace('[field]', htmlspecialchars($column['label']), str_replace('[item]', htmlspecialchars($_POST[$column['name']]), $lang['related_item_not_found']));
                   $error_fields[] = $column['name'];
                  }
                 elseif($related_items_count==1)
                  {
                    // The LIKE serach produced one result but we cannot be sure, it's the intended one.
                   // Return the result for verification:
                   $row = $dbr->fetch();
                   $_POST[$column['name']] = $row[$column['relation_column_name']];
                   $errors[] = str_replace('[field]', htmlspecialchars($column['label']), str_replace('[item]', htmlspecialchars($_POST[$column['name']]), $lang['related_item_verification']));
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
                   $errors[] = str_replace('[field]', htmlspecialchars($column['label']), str_replace('[item]', htmlspecialchars($_POST[$column['name']]), $lang['related_item_verification_select']));
                   $error_fields[] = $column['name'];
                  }
                }
              }
            }
           elseif($column['type']==2 || $column['type']==3) // integer or smallint
            {
             // check format:
             if(!empty($_POST[$column['name']]) && $_POST[$column['name']] != (string)(int)$_POST[$column['name']])
              {
               $errors[] = str_replace('[field]', $column['label'], $lang['invalid_input_format']);
               $error_fields[] = $column['name'];
              }
             // check minimum value:
             elseif($column['range_from']!==null && intval($_POST[$column['name']])<$column['range_from'])
              {
               $errors[] = str_replace('[minimum]', $column['range_from'], str_replace('[field]', $column['label'], $lang['input_number_too_low']));
               $error_fields[] = $column['name'];              
              }
             // check maximum value:
             elseif($column['range_to']!==null && intval($_POST[$column['name']])>$column['range_to'])
              {
               $errors[] = str_replace('[maximum]', $column['range_to'], str_replace('[field]', $column['label'], $lang['input_number_too_high']));
               $error_fields[] = $column['name'];              
              }              
            else
              {
               $submitted_data[$column['name']] = intval($_POST[$column['name']]);
              }
            }
           elseif($column['type']==4) // float
            {
             // accept / replace ',' in floats: 
             #$submitted_data[$column['name']] = str_replace(',', '.', $_POST[$column['name']]);
             $submitted_data[$column['name']] = $_POST[$column['name']];
             if(!empty($submitted_data[$column['name']]) && $submitted_data[$column['name']] != (string)(float)$submitted_data[$column['name']])
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
            else
              {
               $submitted_data[$column['name']] = floatval($submitted_data[$column['name']]);
              }
            }             
           elseif($column['type']==7) // date
            {
             if(my_checkdate($_POST[$column['name']])==false)
              {
               $errors[] = str_replace('[field]', $column['label'], $lang['invalid_date_message']);
               $error_fields[] = $column['name'];
              }
             else
              {
               $submitted_data[$column['name']] = $_POST[$column['name']];
              }
            }
           elseif($column['type']==8) // time
            {
             if(!$submitted_data[$column['name']] = validate_time($_POST[$column['name']]))
              {
               $errors[] = str_replace('[field]', $column['label'], $lang['invalid_time_message']);
               $error_fields[] = $column['name'];
              }
            }            
           else
            {
             // check minimum string length:
             if($column['range_from']!==null && mb_strlen(trim($_POST[$column['name']]))<$column['range_from'])
              {
               $errors[] = str_replace('[minimum]', $column['range_from'], str_replace('[field]', $column['label'], $lang['input_length_too_low']));
               $error_fields[] = $column['name'];              
              }
             // check maximum string length:
             elseif($column['range_to']!==null && mb_strlen(trim($_POST[$column['name']]))>$column['range_to'])
              {
               $errors[] = str_replace('[maximum]', $column['range_to'], str_replace('[field]', $column['label'], $lang['input_length_too_high']));
               $error_fields[] = $column['name'];              
              }              
             elseif($column['regex'] && !preg_match($column['regex'], trim($_POST[$column['name']])))
              {
               $errors[] = str_replace('[field]', $column['label'], $lang['invalid_input_format']);
               $error_fields[] = $column['name'];               
              }
             else
              {
               $submitted_data[$column['name']] = trim($_POST[$column['name']]);
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
           $dbr->bindParam(':geom', $_POST['wkt'], PDO::PARAM_STR);
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
           $dbr->bindParam(':geom', $_POST['wkt'], PDO::PARAM_STR);
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
           elseif($column_types[$column['type']]['type']=='boolean')
            {
             $dbr->bindParam(':'.$column['name'], $submitted_data[$column['name']], PDO::PARAM_BOOL);
            }
           elseif($column_types[$column['type']]['type']=='integer' || $column_types[$column['type']]['type']=='smallint')
            {
             $dbr->bindParam(':'.$column['name'], $submitted_data[$column['name']], PDO::PARAM_INT);
            }
           elseif($column_types[$column['type']]['type']=='double precision')
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
         $status = 4;
        }
       else // get last insert id if item was newly added:
        {
         $dbr = Database::$connection->query(LAST_INSERT_ID_QUERY);
         list($id) = $dbr->fetch();
         if($table_info['table']['parent_table'] && $fk) $qs_addition = '&attached_item_added=true';
         else $qs_addition = '&item_added=true';
         $status = 3;
        }
        
       // log if not attached data:
       if($fk==0) log_status(NULL, $status, $table_info['table']['id'], $id);
        
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
         } 

        // get posted data:
        foreach($table_info['columns'] as $column)
         {
          if($column['type']>0 && $column['relation_table'] && isset($_POST['_'.$column['name'].'_'])) $data_item['_'.$column['name'].'_'] = htmlspecialchars($_POST['_'.$column['name'].'_']);
          if($column['type']>0 && isset($_POST[$column['name']])) $data_item[$column['name']] = htmlspecialchars($_POST[$column['name']]);
         }

        // set id if item is edited:
        if(isset($_POST['id'])) $data_item['id'] = intval($_POST['id']);
        if(isset($_POST['wkt'])) $data_item['wkt'] = htmlspecialchars($_POST['wkt']);
        
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
        $template->assign('geometry_required', intval($table_info['table']['geometry_required']));
        $template->assign('min_scale', intval($table_info['table']['min_scale']));
        $template->assign('max_scale', intval($table_info['table']['max_scale']));

        if(isset($table_info['columns'])) $template->assign('columns', $table_info['columns']);

     $javascripts[] = JQUERY_UI;
     $javascripts[] = JQUERY_UI_HANDLER;
     $stylesheets[] = JQUERY_UI_CSS;

        
        if($table_info['table']['type']==1) // spatial
         {
          $javascripts[] = OPENLAYERS;
          $javascripts[] = OPENLAYERS_DRAW;
          #$javascripts[] = GOOGLE_MAPS;
          $stylesheets[] = OPENLAYERS_CSS;                  
         }             
        
        #$questionnaire['a1'] = htmlspecialchars($_POST['a1']);
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
