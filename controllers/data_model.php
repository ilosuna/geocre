<?php
if(!defined('IN_INDEX')) exit;
if($permission->granted(Permission::USER))
 {
  $javascripts[] = JQUERY_UI;
  $javascripts[] = JQUERY_UI_HANDLER;
  
  // general actions:
  if($action=='edit_model' || $action=='edit_model_submit')
   {
    if($available_basemaps = get_basemaps()) $template->assign('available_basemaps', $available_basemaps); 
   }
   
  if($action=='edit_model' || $action=='add_model' || $action=='add_model_submit' || $action=='edit_model_submit')
   {
    // get existing tables:    
    $check_result = Database::$connection->query(LIST_TABLES_QUERY);
    foreach($check_result as $table)
     {
      $existing_tables[] = $table['name'];
     } 
    // get available projects:
    $dbr = Database::$connection->prepare("SELECT id, title FROM ".Database::$db_settings['pages_table']." WHERE project IS TRUE ORDER BY sequence ASC");
    $dbr->execute();
    $i=0;
    while($row = $dbr->fetch()) 
     {
      $projects[$i]['id'] = intval($row['id']);
      $projects[$i]['title'] = htmlspecialchars($row['title']);
      ++$i;
     }
    if(isset($projects)) $template->assign('projects', $projects);
    // get auxiliary layer tables:
    $dbr = Database::$connection->prepare("SELECT id,
                                              table_name
                                       FROM ".$db_settings['data_models_table']."
                                       WHERE type=1 AND id!=:id
                                       ORDER BY sequence ASC");
    $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
    $dbr->execute();
    $i=0;
    foreach($dbr as $row)
     {
      $auxiliary_layers[$i]['id'] = $row['id'];
      $auxiliary_layers[$i]['name'] = $row['table_name'];
      ++$i;
     } 
    if(isset($auxiliary_layers)) $template->assign('auxiliary_layers', $auxiliary_layers);
    // get tables for parent table selection:
    if(isset($_REQUEST['id']))
     {
      $dbr = Database::$connection->prepare("SELECT id,
                                                    table_name
                                             FROM ".$db_settings['data_models_table']."
                                             WHERE status > 0 AND id!=:id
                                             ORDER BY sequence ASC");
      $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
     }
    else
     {
      $dbr = Database::$connection->prepare("SELECT id,
                                                    table_name
                                             FROM ".$db_settings['data_models_table']."
                                             WHERE status > 0
                                             ORDER BY sequence ASC");
     }  
    $dbr->execute();
    $i=0;
    foreach($dbr as $row)
     {
      $parent_tables[$i]['id'] = $row['id'];
      $parent_tables[$i]['name'] = $row['table_name'];
      ++$i;
     } 
    if(isset($parent_tables)) $template->assign('parent_tables', $parent_tables);
   
   }

  if($action=='edit_model' || $action=='add_model_submit' || $action=='edit_model_submit' || $action=='add_item' || $action=='edit_item')
   {
    // db table item types:
    include(BASE_PATH.'config/column_types.conf.php');
    $template->assign('column_types', $column_types);     
   }

  if($action=='add_item' || $action=='edit_item' || $action=='add_item_submit' || $action=='edit_item_submit')
   {       
    // get available columns for relations:
    $dbr = Database::$connection->prepare("SELECT a.id,
                                           b.table_name,
                                           a.name,
                                           a.table_id
                                           FROM ".$db_settings['data_model_items_table']." AS a
                                           LEFT JOIN ".Database::$db_settings['data_models_table']." AS b ON a.table_id=b.id
                                           WHERE a.column_type>0 AND a.relation_column=1 AND b.status>0 AND b.id!=:id
                                           ORDER BY b.table_name, a.sequence ASC");
    if(isset($_REQUEST['id'])) $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
    else $dbr->bindValue(':id', 0, PDO::PARAM_INT);
    $dbr->execute();
    $i=0;
    foreach($dbr as $row)    
     {
      $relations[$i]['id'] = $row['id'];
      $relations[$i]['table_id'] = $row['table_id'];
      $relations[$i]['table_name'] = htmlspecialchars($row['table_name']);
      $relations[$i]['column_name'] = htmlspecialchars($row['name']);
      ++$i;
     }
    if(isset($relations)) $template->assign('relations', $relations);
   }

  switch($action)
   {
    case 'add_model':
     if($permission->granted(Permission::DATA_MANAGEMENT))
      {
       $template->assign('subtitle', $lang['add_data_model_title']); 
       $template->assign('subtemplate', 'data_model.edit_model.inc.tpl');         
      }
     break;
    
    case 'edit_model':
     if(isset($_GET['id']) && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($_GET['id']), Permission::MANAGE)))
      {
       // get table properties:
       $dbr = Database::$connection->prepare("SELECT id, table_name, title, project, type, parent_table, geometry_type, geometry_required, basemaps, min_scale, max_scale, simplification_tolerance, simplification_tolerance_extent_factor, layer_overview, auxiliary_layer_1, auxiliary_layer_2, auxiliary_layer_3, status, readonly, description FROM ".$db_settings['data_models_table']." WHERE id=:id LIMIT 1");
       $dbr->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
       $dbr->execute();
       $data = $dbr->fetch();
       if(isset($data['id']))
        {
         $db_table['id'] = intval($data['id']);
         $db_table['table_name'] = htmlspecialchars($data['table_name']);
         $db_table['title'] = htmlspecialchars($data['title']);
         $db_table['project'] = intval($data['project']); 
         $db_table['type'] = intval($data['type']); 
         $db_table['parent_table'] = intval($data['parent_table']);
         $db_table['geometry_type'] = intval($data['geometry_type']);
         $db_table['geometry_required'] = intval($data['geometry_required']);
         $db_table['basemaps'] = explode(',',$data['basemaps']);
         $db_table['min_scale'] = floatval($data['min_scale']);
         $db_table['max_scale'] = floatval($data['max_scale']);
         $db_table['simplification_tolerance'] = floatval($data['simplification_tolerance']);
         $db_table['simplification_tolerance_extent_factor'] = floatval($data['simplification_tolerance_extent_factor']);
         $db_table['layer_overview'] = intval($data['layer_overview']);
         $db_table['auxiliary_layer_1'] = intval($data['auxiliary_layer_1']);
         $db_table['auxiliary_layer_2'] = intval($data['auxiliary_layer_2']);
         $db_table['auxiliary_layer_3'] = intval($data['auxiliary_layer_3']);
         $db_table['status'] = intval($data['status']);
         $db_table['readonly'] = intval($data['readonly']);
         $db_table['description'] = htmlspecialchars($data['description']);
         $template->assign('db_table', $db_table);
         if(!in_array($data['table_name'], $existing_tables)) $template->assign('db_table_unavailable', true);
        }
       else $template->assign('db_table_unavailable', 1);

       // get existing colums:
       $dbrx = Database::$connection->prepare("SELECT column_name from INFORMATION_SCHEMA.COLUMNS WHERE table_name = :table_name");
       $dbrx->bindParam(':table_name', $db_table['table_name'], PDO::PARAM_STR);
       $dbrx->execute();
       while($column = $dbrx->fetch())
        {
         $existing_columns[] = $column['column_name'];  
        }
       
       // get table items:
       $dbr = Database::$connection->prepare("SELECT ".$db_settings['data_model_items_table'].".id,
                                                      extract(epoch FROM ".$db_settings['data_model_items_table'].".created) as created_timestamp,
                                                      userdata_created.name as creator,
                                                      extract(epoch FROM ".$db_settings['data_model_items_table'].".last_edited) as last_edited_timestamp,
                                                      ".$db_settings['data_model_items_table'].".last_editor,
                                                      userdata_last_edited.name as last_editor_name,
                                                      ".$db_settings['data_model_items_table'].".name,
                                                      ".$db_settings['data_model_items_table'].".label,
                                                      ".$db_settings['data_model_items_table'].".column_type,
                                                      ".$db_settings['data_model_items_table'].".section_type,
                                                      ".$db_settings['data_model_items_table'].".column_length,
                                                      ".$db_settings['data_model_items_table'].".required,
                                                      ".$db_settings['data_model_items_table'].".overview,
                                                      ".$db_settings['data_model_items_table'].".relation,
                                                      x.name as relation_column_name,
                                                      y.table_name AS relation_table_name
                                            FROM ".$db_settings['data_model_items_table']."
                                            LEFT JOIN ".$db_settings['userdata_table']." AS userdata_created ON userdata_created.id=".$db_settings['data_model_items_table'].".creator
                                            LEFT JOIN ".$db_settings['userdata_table']." AS userdata_last_edited ON userdata_last_edited.id=".$db_settings['data_model_items_table'].".last_editor
                                            LEFT JOIN ".$db_settings['data_model_items_table']." AS x ON x.id=".$db_settings['data_model_items_table'].".relation
                                            LEFT JOIN ".$db_settings['data_models_table']." AS y ON y.id=x.table_id
                                            WHERE ".$db_settings['data_model_items_table'].".table_id = :table_id
                                            ORDER BY ".$db_settings['data_model_items_table'].".sequence ASC");
   
         $dbr->bindParam(':table_id', $db_table['id'], PDO::PARAM_INT);
         $dbr->execute();
         $i=0;
         foreach($dbr as $row) 
          {
           $db_items[$i]['creator'] = htmlspecialchars($row['creator']);
           $db_items[$i]['created'] = htmlspecialchars(strftime($lang['time_format'], $row['created_timestamp']));
           if(!is_null($row['last_editor'])) 
            {
             $db_items[$i]['last_editor'] = htmlspecialchars($row['last_editor_name']);
             $db_items[$i]['last_edited'] = htmlspecialchars(strftime($lang['time_format'], $row['last_edited_timestamp']));
            }
           $db_items[$i]['id'] = intval($row['id']);
           $db_items[$i]['name'] = htmlspecialchars($row['name']);
           if(isset($existing_columns)) $db_items[$i]['column_exists'] = in_array($row['name'], $existing_columns) ? true : false;
           $db_items[$i]['label'] = htmlspecialchars($row['label']);
           $db_items[$i]['column_type'] = intval($row['column_type']);
           $db_items[$i]['section_type'] = intval($row['section_type']);
           $db_items[$i]['column_length'] = intval($row['column_length']);
           $db_items[$i]['required'] = intval($row['required']);
           $db_items[$i]['overview'] = intval($row['overview']);
           if($row['relation']) $db_items[$i]['relation'] = $row['relation_table_name'].'.'.$row['relation_column_name'];
           ++$i;
          }

         if(isset($db_items)) $template->assign('db_items',$db_items);  
       
         if(isset($_GET['success'])) $template->assign('success', $_GET['success']);
    
         $lang['edit_data_model_full_title'] = str_replace('[name]', $db_table['title'], $lang['edit_data_model_full_title']);
         $template->assign('subtitle', $lang['edit_data_model_full_title']); 
         $template->assign('subtemplate', 'data_model.edit_model.inc.tpl');          
       }
     break;
    
    case 'add_model_submit':
    case 'edit_model_submit':
     if($permission->granted(Permission::DATA_MANAGEMENT) || ($action=='edit_submit' && $permission->granted(Permission::DATA_ACCESS, intval($_GET['id']), Permission::MANAGE)))
      {
       $table_name = isset($_POST['table_name']) ? trim($_POST['table_name']) : '';
       $title = isset($_POST['title']) ? trim($_POST['title']) : '';
       $project = isset($_POST['project']) ? intval($_POST['project']) : 0;
       $geometry_type = isset($_POST['geometry_type']) ? intval($_POST['geometry_type']) : 0;
       $geometry_required = isset($_POST['geometry_required']) ? 1 : 0;
       $basemaps = isset($_POST['basemaps']) && is_array($_POST['basemaps']) ? implode(',',$_POST['basemaps']) : null;
       $min_scale = isset($_POST['min_scale']) ? floatval($_POST['min_scale']) : 0;
       $max_scale = isset($_POST['max_scale']) ? floatval($_POST['max_scale']) : 0;
       $simplification_tolerance = isset($_POST['simplification_tolerance']) ? floatval($_POST['simplification_tolerance']) : 0;
       $simplification_tolerance_extent_factor = isset($_POST['simplification_tolerance_extent_factor']) ? floatval($_POST['simplification_tolerance_extent_factor']) : 0;
       $layer_overview = isset($_POST['layer_overview']) ? intval($_POST['layer_overview']) : 0;
       $auxiliary_layer_1 = isset($_POST['auxiliary_layer_1']) ? intval($_POST['auxiliary_layer_1']) : 0;
       $auxiliary_layer_2 = isset($_POST['auxiliary_layer_2']) ? intval($_POST['auxiliary_layer_2']) : 0;
       $auxiliary_layer_3 = isset($_POST['auxiliary_layer_3']) ? intval($_POST['auxiliary_layer_3']) : 0;    
       $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
       $readonly = isset($_POST['readonly']) ? 1 : 0;
       $description = isset($_POST['description']) ? trim($_POST['description']) : '';
       $parent_table = isset($_POST['parent_table']) ? intval($_POST['parent_table']) : 0;
       $type = isset($_POST['type']) ? intval($_POST['type']) : 0;

       // checks if new table should be created:
       if(empty($_POST['id']))
        {
         $not_accepted_table_names = Array('create', 'select', 'group');
         if(empty($table_name)) $errors[] = 'edit_db_table_error_no_name';
         elseif(in_array(strtolower($table_name), $not_accepted_table_names)) $errors[] = 'edit_db_table_error_name';
         elseif(!is_valid_db_identifier($table_name)) $errors[] = 'edit_db_table_error_name_chars';
         else // check if table already exists:
          {
           $check_result = Database::$connection->query(LIST_TABLES_QUERY);
           foreach($check_result as $row)
            {
             $existing_tables[] = $row['name'];
            } 
           if(empty($_REQUEST['no_database_altering']) && isset($existing_tables) and in_array($table_name, $existing_tables)) $errors[] = $lang['edit_db_table_error_table_exists'];
          }
   
        }
       if(empty($title)) $errors[] = 'edit_db_table_error_no_title';
    
       if(empty($errors))
        {
         if(isset($_POST['id'])) // edit
          {
           $dbr = Database::$connection->prepare("UPDATE ".$db_settings['data_models_table']." SET last_editor=:last_editor, last_edited=NOW(), title=:title, project=:project, parent_table=:parent_table, geometry_type=:geometry_type, geometry_required=:geometry_required, basemaps=:basemaps, min_scale=:min_scale, max_scale=:max_scale, simplification_tolerance=:simplification_tolerance, simplification_tolerance_extent_factor=:simplification_tolerance_extent_factor, layer_overview=:layer_overview, auxiliary_layer_1=:auxiliary_layer_1, auxiliary_layer_2=:auxiliary_layer_2, auxiliary_layer_3=:auxiliary_layer_3, status=:status, readonly=:readonly, description=:description WHERE id=:id");
           $dbr->bindParam(':last_editor', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
           #$dbr->bindParam(':table_name', $table_name, PDO::PARAM_STR);
           $dbr->bindParam(':title', $title, PDO::PARAM_STR);
           $dbr->bindParam(':project', $project, PDO::PARAM_INT);
           #$dbr->bindParam(':type', $type, PDO::PARAM_INT);
           $dbr->bindParam(':parent_table', $parent_table, PDO::PARAM_INT);
           $dbr->bindParam(':geometry_type', $geometry_type, PDO::PARAM_INT);
           $dbr->bindParam(':geometry_required', $geometry_required, PDO::PARAM_INT);
           if($basemaps) $dbr->bindParam(':basemaps', $basemaps, PDO::PARAM_STR);
           else $dbr->bindValue(':basemaps', null, PDO::PARAM_NULL);
           $dbr->bindParam(':min_scale', $min_scale, PDO::PARAM_STR);
           $dbr->bindParam(':max_scale', $max_scale, PDO::PARAM_STR);
           $dbr->bindParam(':simplification_tolerance', $simplification_tolerance, PDO::PARAM_STR);
           $dbr->bindParam(':simplification_tolerance_extent_factor', $simplification_tolerance_extent_factor, PDO::PARAM_STR);
           $dbr->bindParam(':layer_overview', $layer_overview, PDO::PARAM_INT);
           $dbr->bindParam(':auxiliary_layer_1', $auxiliary_layer_1, PDO::PARAM_INT);
           $dbr->bindParam(':auxiliary_layer_2', $auxiliary_layer_2, PDO::PARAM_INT);
           $dbr->bindParam(':auxiliary_layer_3', $auxiliary_layer_3, PDO::PARAM_INT);        
           $dbr->bindParam(':status', $status, PDO::PARAM_INT);
           $dbr->bindParam(':readonly', $readonly, PDO::PARAM_INT);
           $dbr->bindParam(':description', $description, PDO::PARAM_STR);
           $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
           $dbr->execute();
           $table_id = intval($_POST['id']);
          }
         else // add
          {
           // create table:
           if(empty($_REQUEST['no_database_altering']))
            {
             require(BASE_PATH.'config/default_queries.conf.php');
             foreach($default_query['create_table'][$type] as $query)
              { 
               $query = str_replace('[table]', $table_name, $query);
               Database::$connection->query($query);
              }
            } 
           // determine sequence:
           #$dbr = Database::$connection->prepare("SELECT sequence FROM ".$db_settings['data_models_table']." ORDER BY sequence DESC LIMIT 1");
           #$dbr->execute();
           #$row = $dbr->fetch();
           #if(isset($row['sequence'])) $new_sequence = $row['sequence'] + 1;
           $new_sequence = 1;
           $dbr = Database::$connection->prepare("INSERT INTO ".$db_settings['data_models_table']." (sequence, creator, created, table_name, title, project, type, parent_table, geometry_type, geometry_required,basemaps,  min_scale, max_scale, simplification_tolerance, simplification_tolerance_extent_factor, layer_overview, auxiliary_layer_1, auxiliary_layer_2, auxiliary_layer_3, status, readonly, description) VALUES (:sequence, :creator, NOW(), :table_name, :title, :project, :type, :parent_table, :geometry_type, :geometry_required, :basemaps, :min_scale, :max_scale, :simplification_tolerance, :simplification_tolerance_extent_factor, :layer_overview, :auxiliary_layer_1, :auxiliary_layer_2, :auxiliary_layer_3, :status, :readonly, :description)");
           $dbr->bindParam(':sequence', $new_sequence, PDO::PARAM_INT);
           $dbr->bindParam(':creator', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
           $dbr->bindParam(':table_name', $table_name, PDO::PARAM_STR);
           $dbr->bindParam(':title', $title, PDO::PARAM_STR);
           $dbr->bindParam(':project', $project, PDO::PARAM_INT);
           $dbr->bindParam(':type', $type, PDO::PARAM_INT);
           $dbr->bindParam(':parent_table', $parent_table, PDO::PARAM_INT);
           $dbr->bindParam(':geometry_type', $geometry_type, PDO::PARAM_INT);
           $dbr->bindParam(':geometry_required', $geometry_required, PDO::PARAM_INT);
           if($basemaps) $dbr->bindParam(':basemaps', $basemaps, PDO::PARAM_STR);
           else $dbr->bindValue(':basemaps', null, PDO::PARAM_NULL);
           $dbr->bindParam(':min_scale', $min_scale, PDO::PARAM_STR);
           $dbr->bindParam(':max_scale', $max_scale, PDO::PARAM_STR);
           $dbr->bindParam(':simplification_tolerance', $simplification_tolerance, PDO::PARAM_STR);
           $dbr->bindParam(':simplification_tolerance_extent_factor', $simplification_tolerance_extent_factor, PDO::PARAM_STR);
           $dbr->bindParam(':layer_overview', $layer_overview, PDO::PARAM_INT);        
           $dbr->bindParam(':auxiliary_layer_1', $auxiliary_layer_1, PDO::PARAM_INT);
           $dbr->bindParam(':auxiliary_layer_2', $auxiliary_layer_2, PDO::PARAM_INT);
           $dbr->bindParam(':auxiliary_layer_3', $auxiliary_layer_3, PDO::PARAM_INT);
           $dbr->bindParam(':status', $status, PDO::PARAM_INT);
           $dbr->bindParam(':readonly', $readonly, PDO::PARAM_INT);
           $dbr->bindParam(':description', $description, PDO::PARAM_STR);
           $dbr->execute();   
           $dbr = Database::$connection->query("SELECT LASTVAL()");
           list($table_id) = $dbr->fetch();

           // reorder:
           $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['data_models_table']." SET sequence=sequence+1 WHERE id!=:id");
           $dbr->bindParam(':id', $table_id, PDO::PARAM_INT);
           $dbr->execute();
          
          } 
         header('Location: '.BASE_URL.'?r=data_model.edit_model&id='.$table_id.'&success=data_model_saved#properties');
         exit;
        }
       else // errors
        {
         if(isset($_POST['id']))
          {
           $db_table['id'] = intval($_POST['id']);
           $template->assign('subtitle',$lang['edit_data_model_title']);
          }
         else
          {
           $template->assign('subtitle',$lang['add_data_model_title']);
          } 
         $db_table['table_name'] = htmlspecialchars($_POST['table_name']);
         $db_table['title'] = htmlspecialchars($_POST['title']);
         $db_table['type'] = htmlspecialchars($_POST['type']);
         #$db_table['project'] = intval($_POST['project']);
         #$db_table['parent_table'] = htmlspecialchars($_POST['parent_table']);
         #$db_table['status'] = htmlspecialchars($_POST['status']);
         $template->assign('db_table', $db_table);
         $template->assign('errors', $errors);
         $template->assign('subtemplate','data_model.edit_model.inc.tpl');
        }

      }
     break;
    
    case 'delete_model':
     if(isset($_REQUEST['id']) && $permission->granted(Permission::DATA_MANAGEMENT))
      {
       $dbr = Database::$connection->prepare("SELECT id, title, table_name FROM ".$db_settings['data_models_table']." WHERE id=:id LIMIT 1");
       $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
       $dbr->execute();
       $data = $dbr->fetch();
       if(isset($data['id']))
        {
         // count records:
         try
          {
           $count_result = Database::$connection->query('SELECT COUNT(*) FROM "'.$data['table_name'].'"');
           list($records) = $count_result->fetch();
          }
         catch(Exception $exception)
          {
           $records = 0;
          } 
         $model['id'] = intval($data['id']);
         $model['title'] = htmlspecialchars($data['title']);
         $model['table'] = htmlspecialchars($data['table_name']);
         $model['records'] = $records;
         $template->assign('model', $model);
         $template->assign('subtitle', $lang['delete_data_model_title']);
         if(isset($_GET['failure'])) $template->assign('failure', htmlspecialchars($_GET['failure']));
         $template->assign('subtemplate', 'data_model.delete_model.inc.tpl');

        }
       
      }     
     break;
    
    case 'delete_model_submit':
     if(isset($_POST['pw']) && isset($_POST['id']) && $permission->granted(Permission::DATA_MANAGEMENT))
      {
       // check password:
       $dbr = Database::$connection->prepare("SELECT pw FROM ".Database::$db_settings['userdata_table']." WHERE id=:id LIMIT 1");
       $dbr->bindParam(':id', $_SESSION[$settings['session_prefix'].'auth']['id']);
       $dbr->execute();
       list($pw) = $dbr->fetch();
       if(check_pw($_POST['pw'], $pw))
        {
         // get table name:
         $dbr = Database::$connection->prepare("SELECT id, table_name, type FROM ".$db_settings['data_models_table']." WHERE id=:id LIMIT 1");
         $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
         $dbr->execute();
         $data = $dbr->fetch(); 

         // delete table information:
         $dbr = Database::$connection->prepare("DELETE FROM " . Database::$db_settings['data_models_table']." WHERE id = :id");
         $dbr->bindValue(':id', $data['id']);
         $dbr->execute();
      
         // drop table:
         if(isset($_POST['delete_table']))
          {
           if($data['type']==1) Database::$connection->query("SELECT DropGeometryTable('".$data['table_name']."')");   
           else Database::$connection->query('DROP TABLE IF EXISTS "'.$data['table_name'].'"');      
          } 
         header('Location: '.BASE_URL.'?r=dashboard&success=data_model_deleted#data');
         exit;
        }
       else
        {
         header('Location: '.BASE_URL.'?r=data_model.delete_model&id='.intval($_POST['id']).'&failure=password_wrong');
         exit;
        }
      } 
     break;
    
    case 'add_item':
     if(isset($_REQUEST['data_id']) && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($_REQUEST['data_id']), Permission::MANAGE)))
      {

       $dbr = Database::$connection->prepare("SELECT id, table_name, title, type FROM ".Database::$db_settings['data_models_table']." WHERE id=:id LIMIT 1");
       $dbr->bindParam(':id', $_REQUEST['data_id'], PDO::PARAM_INT);
       $dbr->execute();
       $row = $dbr->fetch();
       if(isset($row['id']))
        {
         $model_item['item_type'] = 0;
         $model_item['section_type'] = 0;
         $template->assign('model_item', $model_item);         
         $template->assign('data_id', $row['id']); 
         $lang['edit_data_model_title'] = str_replace('[name]', $row['title'], $lang['edit_data_model_title']);
         $template->assign('subtitle', $lang['data_model_add_item_title']); 
         $template->assign('subtemplate', 'data_model.edit_item.inc.tpl');     
        }
      }
     break;

    case 'edit_item':
     // get model and item data:
     $dbr = Database::$connection->prepare("SELECT items.id,
                                                   items.table_id,
                                                   items.name,
                                                   items.label,
                                                   items.description,
                                                   items.column_type,
                                                   items.column_length,
                                                   items.choices,
                                                   items.choice_labels,
                                                   items.relation,
                                                   items.relation_column,
                                                   items.required,
                                                   items.overview,
                                                   items.section_type,
                                                   items.range_from,
                                                   items.range_to,
                                                   items.regex,
                                                   model.title
                                            FROM ".Database::$db_settings['data_model_items_table']." AS items
                                            LEFT JOIN ".Database::$db_settings['data_models_table']." AS model ON items.table_id=model.id                                            
                                            WHERE items.id=:id LIMIT 1");
     $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
     $dbr->execute();
     $row = $dbr->fetch();
     // check permission:
     if(isset($row['table_id']) && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, $row['table_id'], Permission::MANAGE)))
      {
       $model_item['id'] = intval($row['id']);
       $model_item['name'] = htmlspecialchars($row['name']);
       $model_item['label'] = htmlspecialchars($row['label']);
       $model_item['description'] = htmlspecialchars($row['description']);
       $model_item['column_type'] = htmlspecialchars($row['column_type']);  
       $model_item['column_length'] = htmlspecialchars($row['column_length']);
       #$model_item['input_type'] = intval($data['input_type']);
       $model_item['choices'] = htmlspecialchars($row['choices']);
       $model_item['choice_labels'] = htmlspecialchars($row['choice_labels']);
       $model_item['relation'] = intval($row['relation']);
       $model_item['relation_column'] = intval($row['relation_column']);
       $model_item['required'] = intval($row['required']);
       $model_item['overview'] = intval($row['overview']);
       
       if($model_item['column_type']>0) $model_item['item_type'] = 0;
       else $model_item['item_type'] = 1;
       
       $model_item['section_type'] = intval($row['section_type']);
       $model_item['range_from'] = $row['range_from'];
       $model_item['range_to'] = $row['range_to'];
       $model_item['regex'] = htmlspecialchars($row['regex']);
       $template->assign('model_item', $model_item);
       $template->assign('data_id', $row['table_id']); 
       $lang['edit_data_model_title'] = str_replace('[name]', $row['title'], $lang['edit_data_model_title']);
       $template->assign('subtitle', $lang['data_model_edit_item_title']); 
       $template->assign('subtemplate', 'data_model.edit_item.inc.tpl');             
      }
     break;

    case 'add_item_submit':
    case 'edit_item_submit':
     // get table:
     
     if(isset($_REQUEST['id']) && isset($_REQUEST['delete_item']))
      {
       if(isset($_REQUEST['no_database_altering'])) $add = '&no_database_altering=true';
       else $add = '';
       header('Location: '.BASE_URL.'?r=data_model.delete_item&id='.$_REQUEST['id'].'&confirmed=true'.$add);
       exit;
      }
     
     if(isset($_REQUEST['id'])) // edit
      {
       $dbr = Database::$connection->prepare("SELECT id, table_id, name, column_type FROM ".Database::$db_settings['data_model_items_table']." WHERE id=:id LIMIT 1");
       $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
       $dbr->execute();
       $row = $dbr->fetch();
       if(isset($row['id']))
        {
         $data_id = $row['table_id'];
         $current_name = $row['name'];
         $current_column_type = $row['column_type'];
        } 
      }
     elseif(isset($_REQUEST['data_id'])) // add
      {
       $data_id = intval($_REQUEST['data_id']);
      }
     if(isset($data_id) && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($data_id), Permission::MANAGE)))
      {
       // get table info:
       $dbr = Database::$connection->prepare("SELECT table_name FROM ".Database::$db_settings['data_models_table']." WHERE id=:id LIMIT 1");
       $dbr->bindParam(':id', $data_id, PDO::PARAM_INT);
       $dbr->execute();
       $row = $dbr->fetch();
       if(isset($row['table_name']))
        {
         $table_name = $row['table_name'];
         
         // db table item types:
         include(BASE_PATH.'config/column_types.conf.php');
         $template->assign('column_types', $column_types);

         $name = isset($_POST['name']) ? trim($_POST['name']) : '';
         $item_type = isset($_POST['item_type']) && $_POST['item_type']==1 ? 1 : 0;
         $column_type = isset($_POST['column_type']) ? intval($_POST['column_type']) : 0;
         $column_length = isset($_POST['column_length']) ? intval($_POST['column_length']) : 0;
         $label = isset($_POST['label']) ? trim($_POST['label']) : '';
         $description = isset($_POST['description']) ? trim($_POST['description']) : '';
         $choices = isset($_POST['choices']) ? trim($_POST['choices']) : '';
         $choice_labels = isset($_POST['choice_labels']) ? trim($_POST['choice_labels']) : '';
         $relation = isset($_POST['relation']) ? intval($_POST['relation']) : 0;
         $relation_column = isset($_POST['relation_column']) ? 1 : 0;
         $required = isset($_POST['required']) ? 1 : 0;
         $overview = isset($_POST['overview']) ? 1 : 0;
         $section_type = isset($_POST['section_type']) ? intval($_POST['section_type']) : 0;
         $range_from = isset($_POST['range_from']) ? trim($_POST['range_from']) : '';
         $range_to = isset($_POST['range_to']) ? trim($_POST['range_to']) : '';
         $regex = isset($_POST['regex']) ? trim($_POST['regex']) : '';
         // get colums:
         $dbr = Database::$connection->prepare("SELECT column_name from INFORMATION_SCHEMA.COLUMNS WHERE table_name = :table_name");
         $dbr->bindParam(':table_name', $table_name, PDO::PARAM_STR);
         $dbr->execute();
         while($column = $dbr->fetch())
          {
           $existing_columns[] = $column['column_name'];  
          }
    
         // check column name if not empty:
         if(!is_valid_db_identifier($name))
          {
           $errors[] = 'error_column_name_invalid';
           $error_fields[] = 'name';
          }
         elseif(empty($_REQUEST['no_database_altering']))
          {
           // check if column name already exists:
           if(isset($_REQUEST['id'])) // edit
            {
             // the new column name may only be identical to itself
             if($name!=$current_name && in_array($name, $existing_columns))
              {
               $errors[] = 'error_column_name_already_exists';
               $error_fields[] = 'name';
              }
            }
           else // add
            {
             // the new column name may not exist yet:
             if(in_array($name, $existing_columns))
              {
               $errors[] = 'error_column_name_already_exists';
               $error_fields[] = 'name';
              }
            } 
          }
      
        if(empty($_REQUEST['id']) && $item_type==0 && $column_type==0)
         {
          $errors[] = 'error_no_column_type'; 
          $error_fields[] = 'type';
         }
        
        
        if(empty($column_types[$column_type]))
         {
          $errors[] = 'error_column_type_invalid'; 
          $error_fields[] = 'type';
         }
        
        // check if relation is valid:
        if($relation)
         {
          $dbr = Database::$connection->prepare("SELECT relation_column FROM ".Database::$db_settings['data_model_items_table']." WHERE id=:id LIMIT 1");
          $dbr->bindParam(':id', $relation, PDO::PARAM_INT);
          $dbr->execute();
          list($relation_check) = $dbr->fetch();
          if(!$relation_check) $errors[] = 'error_relation_invalid'; 
         }
        
        // standardize serial values:
        
        // normalize newlines:
        #$choices = preg_split('~\R~', $choices);
        $choices = preg_replace('~\r[\n]?~', "\n", $choices);
        $choice_labels = preg_replace('~\r[\n]?~', "\n", $choice_labels);
        
        $choices_parts = explode("\n", $choices);
        foreach($choices_parts as $choices_part)
         {
          if(trim($choices_part)!='') $cleared_choices[] = trim($choices_part);
         }
        $choices = isset($cleared_choices) ? implode("\n", $cleared_choices) : '';
    
        $choice_labels_parts = explode("\n", $choice_labels);
        foreach($choice_labels_parts as $choice_labels_part)
         {
          if(trim($choice_labels_part)!='') $cleared_choice_labels[] = trim($choice_labels_part);
         }
        $choice_labels = isset($cleared_choice_labels) ? implode("\n", $cleared_choice_labels) : '';
    
        if(empty($errors))
         {
          if(isset($_REQUEST['id'])) // edit
           {
            if($current_column_type>0 && $name!=$current_name && empty($_REQUEST['no_database_altering']))
             {
              $alter_table_query = 'ALTER TABLE "'.$table_name.'" RENAME COLUMN "'.$current_name.'" TO "'.$name.'"';
              Database::$connection->query($alter_table_query);
             }
            
            #if(isset($table))
            # {
              $dbr = Database::$connection->prepare("UPDATE ".$db_settings['data_model_items_table']." SET last_editor=:last_editor, last_edited=NOW(), name=:name, label=:label, description=:description, choices=:choices, choice_labels=:choice_labels, relation=:relation, relation_column=:relation_column, required=:required, overview=:overview, section_type=:section_type, range_from=:range_from, range_to=:range_to, regex=:regex WHERE id=:id");
              $dbr->bindParam(':last_editor', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
              $dbr->bindParam(':name', $name, PDO::PARAM_STR);
              #$dbr->bindParam(':column_type', $column_type, PDO::PARAM_INT);
              #$dbr->bindParam(':column_length', $column_length, PDO::PARAM_INT);
              $dbr->bindParam(':relation_column', $relation_column, PDO::PARAM_INT);
              $dbr->bindParam(':required', $required, PDO::PARAM_INT);
              $dbr->bindParam(':overview', $overview, PDO::PARAM_INT);
              $dbr->bindParam(':section_type', $section_type, PDO::PARAM_INT);
              if($range_from!='') $dbr->bindValue(':range_from', floatval($range_from), PDO::PARAM_STR);
              else $dbr->bindValue(':range_from', NULL, PDO::PARAM_NULL);
              if($range_to!='') $dbr->bindValue(':range_to', floatval($range_to), PDO::PARAM_STR);
              else $dbr->bindValue(':range_to', NULL, PDO::PARAM_NULL);
              $dbr->bindParam(':regex', $regex, PDO::PARAM_STR);
              $dbr->bindParam(':label', $label, PDO::PARAM_STR);
              $dbr->bindParam(':description', $description, PDO::PARAM_STR);
              #$dbr->bindParam(':input_type', $input_type, PDO::PARAM_INT);
              $dbr->bindParam(':choices', $choices, PDO::PARAM_STR);
              $dbr->bindParam(':choice_labels', $choice_labels, PDO::PARAM_STR);
              $dbr->bindParam(':relation', $relation, PDO::PARAM_INT);
              $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
              $dbr->execute();
            # }
           }
          else // add
           {
            // alter table:
            if($column_type>0 && empty($_REQUEST['no_database_altering']))
             {
              $alter_table_query = 'ALTER TABLE "'.$table_name.'" ADD COLUMN "'.$name.'" '.$column_types[$column_type]['type'];
              if(isset($column_types[$column_type]['length']) && $column_length>0) $alter_table_query .= '('.$column_length.')';
              Database::$connection->query($alter_table_query);
             }
            
            // update table information:
            // determine sequence:
            $dbr = Database::$connection->prepare("SELECT sequence FROM ".$db_settings['data_model_items_table']." WHERE table_id=:table_id ORDER BY sequence DESC LIMIT 1");
            $dbr->bindParam(':table_id', $data_id, PDO::PARAM_INT);
            $dbr->execute();
            $row = $dbr->fetch();
            if(isset($row['sequence'])) $new_sequence = $row['sequence'] + 1;
            else $new_sequence = 1;
            $dbr = Database::$connection->prepare("INSERT INTO ".$db_settings['data_model_items_table']." (table_id, sequence, creator, created, name, label, description, column_type, column_length, choices, choice_labels, relation, relation_column, required, overview, section_type, range_from, range_to, regex) VALUES (:table_id, :sequence, :creator, NOW(), :name, :label, :description, :column_type, :column_length, :choices, :choice_labels, :relation, :relation_column, :required, :overview, :section_type, :range_from, :range_to, :regex)");
            #$dbr->bindParam(':a1', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
            $dbr->bindParam(':table_id', $data_id, PDO::PARAM_INT);
            $dbr->bindParam(':sequence', $new_sequence, PDO::PARAM_INT);
            $dbr->bindParam(':creator', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
            $dbr->bindParam(':name', $name, PDO::PARAM_STR);
            $dbr->bindParam(':column_type', $column_type, PDO::PARAM_INT);
            $dbr->bindParam(':column_length', $column_length, PDO::PARAM_INT);
            $dbr->bindParam(':relation_column', $relation_column, PDO::PARAM_INT);
            $dbr->bindParam(':required', $required, PDO::PARAM_INT);
            $dbr->bindParam(':overview', $overview, PDO::PARAM_INT);
            $dbr->bindParam(':section_type', $section_type, PDO::PARAM_INT);
            if($range_from!='') $dbr->bindValue(':range_from', floatval($range_from), PDO::PARAM_STR);
            else $dbr->bindValue(':range_from', NULL, PDO::PARAM_NULL);
            if($range_to!='') $dbr->bindValue(':range_to', floatval($range_to), PDO::PARAM_STR);
            else $dbr->bindValue(':range_to', NULL, PDO::PARAM_NULL);
            $dbr->bindParam(':regex', $regex, PDO::PARAM_STR);                
            $dbr->bindParam(':label', $label, PDO::PARAM_STR);
            $dbr->bindParam(':description', $description, PDO::PARAM_STR);
            #$dbr->bindParam(':input_type', $input_type, PDO::PARAM_INT);
            $dbr->bindParam(':choices', $choices, PDO::PARAM_STR);
            $dbr->bindParam(':relation', $relation, PDO::PARAM_INT);
            $dbr->bindParam(':choice_labels', $choice_labels, PDO::PARAM_STR);
            $dbr->execute();   
           } 
          if(isset($data_id)) header('Location: '.BASE_URL.'?r=data_model.edit_model&id='.$data_id.'#structure');
          exit;
         }
        else // errors
         {
          if(isset($_REQUEST['data_id']))
           {
            $dbr = Database::$connection->prepare("SELECT id, title
                                                   FROM ".Database::$db_settings['data_models_table']."                                            
                                                   WHERE id=:id LIMIT 1");
            $dbr->bindParam(':id', $_REQUEST['data_id'], PDO::PARAM_INT);
            $dbr->execute();
            $row = $dbr->fetch();
            if(isset($row['id']))
             {
              $data_id = $row['id'];
              $data_title = htmlspecialchars($row['title']);
             }
           }
          elseif(isset($_REQUEST['id']))
           {
            // get model and item data:
            $dbr = Database::$connection->prepare("SELECT items.id,
                                                          model.id AS data_id,
                                                          model.title
                                                   FROM ".Database::$db_settings['data_model_items_table']." AS items
                                                   JOIN ".Database::$db_settings['data_models_table']." AS model ON items.table_id=model.id                                            
                                                   WHERE items.id=:id LIMIT 1");
            $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
            $dbr->execute();
            $row = $dbr->fetch();
            if(isset($row['id']))
             {
              $data_id = $row['data_id'];
              $data_title = htmlspecialchars($row['title']);
             }
            $model_item['id'] = intval($_REQUEST['id']);
            $template->assign('subtitle', $lang['data_model_edit_item_title']);
           } 
          if(isset($data_id))
           {
            $template->assign('data_id', $data_id);
            $lang['edit_data_model_title'] = str_replace('[name]', $data_title, $lang['edit_data_model_title']);
            $model_item['name'] = htmlspecialchars($name);
            $model_item['item_type'] = intval($item_type);
            $model_item['column_type'] = intval($column_type);
            $model_item['label'] = htmlspecialchars($label);
            $model_item['description'] = htmlspecialchars($description);
            $model_item['choices'] = htmlspecialchars($choices);
            $model_item['choice_labels'] = htmlspecialchars($choice_labels);
            $model_item['relation'] = intval($relation);
            $model_item['relation_column'] = intval($relation_column);
            $model_item['required'] = intval($required);
            $model_item['overview'] = intval($overview);
            $model_item['section_type'] = intval($section_type);
            $model_item['range_from'] = htmlspecialchars($range_from);
            $model_item['range_to'] = htmlspecialchars($range_to);
            $model_item['regex'] = htmlspecialchars($regex);
            $template->assign('model_item', $model_item);
            $template->assign('errors', $errors);
            if(isset($error_fields)) $template->assign('error_fields', $error_fields);
            $template->assign('subtemplate','data_model.edit_item.inc.tpl');
           }
          break;          
         }
       }
      }
     break;

    
    case 'delete_item':
     if(isset($_REQUEST['id']))
      {
       $dbr = Database::$connection->prepare("SELECT id, column_type, table_id FROM ".Database::$db_settings['data_model_items_table']." WHERE id=:id LIMIT 1");
       $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
       $dbr->execute();
       $row = $dbr->fetch();
       if(isset($row['id']))
        {
         $id = $row['id'];
         $data_id = $row['table_id'];
         $type = $row['column_type'];
        }
       if(isset($data_id) && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($data_id), Permission::MANAGE)))
        {
         // get item data:
         $dbr = Database::$connection->prepare("SELECT a.id,
                                                  a.table_id,
                                                  a.name,
                                                  b.table_name
                                           FROM ".Database::$db_settings['data_model_items_table']." AS a
                                           LEFT JOIN ".Database::$db_settings['data_models_table']." AS b ON a.table_id=b.id
                                           WHERE a.id=:id LIMIT 1");
         $dbr->bindParam(':id', $id, PDO::PARAM_INT);
         $dbr->execute();
         $data = $dbr->fetch();
    
         if(isset($data['id']))
          {
           if(empty($_REQUEST['confirmed']))
            {
             $action='delete';
             $template->assign('mode', 'admin_db_table_properties');
             $template->assign('params', 'id='.$data['table_id']);
             $template->assign('delete', intval($_REQUEST['delete']));
             $template->assign('item', htmlspecialchars($data['name']));  
             $template->assign('subtitle', $lang['delete_db_table_item_title']);
             $template->assign('delete_message', 'delete_db_table_item_message_explicit');
             $template->assign('subtemplate', 'delete_confirm.inc.tpl');
            }
           else
            {
             // delete column information:
             $dbr = Database::$connection->prepare("DELETE FROM ".$db_settings['data_model_items_table']." WHERE id = :id");
             $dbr->bindValue(':id', $id, PDO::PARAM_INT);
             $dbr->execute();
             
             // delete column:
             if($type>0 && empty($_REQUEST['no_database_altering'])) Database::$connection->query('ALTER TABLE "'.$data['table_name'].'" DROP COLUMN IF EXISTS "'.$data['name'].'"');

             // reorder...
             $dbr = Database::$connection->prepare("SELECT id FROM ".$db_settings['data_model_items_table']." WHERE table_id=:table_id ORDER BY sequence ASC");
             $dbr->bindParam(':table_id', $data_id, PDO::PARAM_INT);
             $dbr->execute();
             $dbr2 = Database::$connection->prepare("UPDATE ".$db_settings['data_model_items_table']." SET sequence=:sequence WHERE id=:id AND table_id=:table_id");
             $i=1;
             while($reorder_row = $dbr->fetch()) 
              {
               $dbr2->bindParam(':sequence', $i, PDO::PARAM_INT);
               $dbr2->bindParam(':id', $reorder_row['id'], PDO::PARAM_INT);
               $dbr2->bindParam(':table_id', $data_id, PDO::PARAM_INT);
               $dbr2->execute();
               ++$i;
              }            
                    
             header('Location: '.BASE_URL.'?r=data_model.edit_model&id='.$data_id.'#structure');
             exit;
            }
          }      
        }
      }
     break;

    case 'reorder_items':   
     if(isset($_REQUEST['item']) && is_array($_REQUEST['item']))
      {
       // get data model:
       $dbr = Database::$connection->prepare("SELECT table_id FROM ".Database::$db_settings['data_model_items_table']." WHERE id=:id LIMIT 1");
       $dbr->bindParam(':id', $_REQUEST['item'][0], PDO::PARAM_INT);
       $dbr->execute();
       list($data_id) = $dbr->fetch();
       if($data_id && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($data_id), Permission::MANAGE)))
        {
         $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['data_model_items_table']." SET sequence=:sequence WHERE id=:id AND table_id=:table_id");
         $dbr->bindParam(':sequence', $sequence, PDO::PARAM_INT);
         $dbr->bindParam(':id', $id, PDO::PARAM_INT);
         $dbr->bindParam(':table_id', $data_id, PDO::PARAM_INT);
         Database::$connection->beginTransaction();
         $sequence = 1;
         foreach($_REQUEST['item'] as $id)
          {
           $dbr->execute();
           ++$sequence;
          }
         Database::$connection->commit();
         exit;
        }
      }     
     break;

    case 'reorder_models':   
     if($permission->granted(Permission::DATA_MANAGEMENT) && isset($_REQUEST['item']) && is_array($_REQUEST['item']))
      {
       // get start sequence:
       $ids = implode(',',$_REQUEST['item']);

       $dbr = Database::$connection->prepare("SELECT sequence FROM ".$db_settings['data_models_table']." WHERE id IN (".$ids.") ORDER BY sequence ASC LIMIT 1");
       $dbr->execute();
       $data = $dbr->fetch();
       if(isset($data['sequence']))
        {
         $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['data_models_table']." SET sequence=:sequence WHERE id=:id");
         $dbr->bindParam(':sequence', $data['sequence'], PDO::PARAM_INT);
         $dbr->bindParam(':id', $id, PDO::PARAM_INT);
         Database::$connection->beginTransaction();

         foreach($_REQUEST['item'] as $id)
          {
           $dbr->execute();
           ++$data['sequence'];
          }
         Database::$connection->commit();
         exit;
        }
      }     
     break;
   
   }
 }
?>
