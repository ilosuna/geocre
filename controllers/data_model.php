<?php
if(!defined('IN_INDEX')) exit;

$not_accepted_table_names = Array('create', 'select', 'group');

if($permission->granted(Permission::USER))
 {
  $javascripts[] = JQUERY_UI;
  $javascripts[] = JQUERY_UI_HANDLER;
  
  // general actions:
  if($action=='edit_model' || $action=='edit_model_submit')
   {
    if($available_basemaps = get_basemaps()) $template->assign('available_basemaps', $available_basemaps); 
   }
   
  if($action=='edit_model' || $action=='add_model' || $action=='add_model_submit' || $action=='edit_model_submit' || $action=='copy_model_submit')
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

  if($action=='edit_model' || $action=='add_model_submit' || $action=='edit_model_submit' || $action=='add_item' || $action=='edit_item' || $action=='copy_model_submit')
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
       $dbr = Database::$connection->prepare("SELECT id, table_name, title, project, type, parent_table, geometry_type, latlong_entry, geometry_required, basemaps, min_scale, max_scale, simplification_tolerance, simplification_tolerance_extent_factor, layer_overview, boundary_layer, auxiliary_layer_1, status, readonly, data_images, item_images, description FROM ".$db_settings['data_models_table']." WHERE id=:id LIMIT 1");
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
         $db_table['latlong_entry'] = intval($data['latlong_entry']);
         $db_table['geometry_required'] = intval($data['geometry_required']);
         $db_table['basemaps'] = explode(',',$data['basemaps']);
         $db_table['min_scale'] = floatval($data['min_scale']);
         $db_table['max_scale'] = floatval($data['max_scale']);
         $db_table['simplification_tolerance'] = floatval($data['simplification_tolerance']);
         $db_table['simplification_tolerance_extent_factor'] = floatval($data['simplification_tolerance_extent_factor']);
         $db_table['layer_overview'] = intval($data['layer_overview']);
         $db_table['boundary_layer'] = intval($data['boundary_layer']);
         $db_table['auxiliary_layer_1'] = intval($data['auxiliary_layer_1']);
         $db_table['status'] = intval($data['status']);
         $db_table['readonly'] = intval($data['readonly']);
         $db_table['data_images'] = intval($data['data_images']);
         $db_table['item_images'] = intval($data['item_images']);
         $db_table['description'] = htmlspecialchars($data['description']);
         $template->assign('db_table', $db_table);
         if(!in_array($data['table_name'], $existing_tables)) $template->assign('db_table_unavailable', true);
        }
       else $template->assign('db_table_unavailable', 1);
         
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
       $latlong_entry = isset($_POST['latlong_entry']) ? 1 : 0;
       $geometry_required = isset($_POST['geometry_required']) ? 1 : 0;
       $basemaps = isset($_POST['basemaps']) && is_array($_POST['basemaps']) ? implode(',',$_POST['basemaps']) : null;
       $min_scale = isset($_POST['min_scale']) ? floatval($_POST['min_scale']) : 0;
       $max_scale = isset($_POST['max_scale']) ? floatval($_POST['max_scale']) : 0;
       $simplification_tolerance = isset($_POST['simplification_tolerance']) ? floatval($_POST['simplification_tolerance']) : 0;
       $simplification_tolerance_extent_factor = isset($_POST['simplification_tolerance_extent_factor']) ? floatval($_POST['simplification_tolerance_extent_factor']) : 0;
       $layer_overview = isset($_POST['layer_overview']) ? intval($_POST['layer_overview']) : 0;
       $boundary_layer = isset($_POST['boundary_layer']) ? intval($_POST['boundary_layer']) : 0;
       $auxiliary_layer_1 = isset($_POST['auxiliary_layer_1']) ? intval($_POST['auxiliary_layer_1']) : 0;   
       $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
       $readonly = isset($_POST['readonly']) ? 1 : 0;
       $data_images = isset($_POST['data_images']) ? 1 : 0;
       $item_images = isset($_POST['item_images']) ? 1 : 0;
       $description = isset($_POST['description']) ? trim($_POST['description']) : '';
       $parent_table = isset($_POST['parent_table']) ? intval($_POST['parent_table']) : 0;
       $type = isset($_POST['type']) ? intval($_POST['type']) : 0;

       // checks if new table should be created:
       if(empty($_POST['id']))
        {
         if(empty($table_name)) $errors[] = 'edit_db_table_error_no_name';
         elseif(in_array(strtolower($table_name), $not_accepted_table_names)) $errors[] = 'edit_db_table_error_name';
         elseif(!is_valid_db_identifier($table_name)) $errors[] = 'edit_db_table_error_name_chars';
         elseif(empty($_REQUEST['no_database_altering']) && isset($existing_tables) and in_array($table_name, $existing_tables)) $errors[] = $lang['edit_db_table_error_table_exists'];
        }
       if(empty($title)) $errors[] = 'edit_db_table_error_no_title';
       else
        {
         if(empty($_POST['id'])) // new data model, check if title does not exist yet:
          {
           $count_result = Database::$connection->prepare("SELECT COUNT(*) FROM ".$db_settings['data_models_table']." WHERE LOWER(title)=LOWER(:title)");
           $count_result->bindParam(':title', $title, PDO::PARAM_STR);
           $count_result->execute();
           list($title_count) = $count_result->fetch();
           if($title_count) $errors[] = 'edit_db_table_error_title_exists'; 
          }
         else // model edited, check if title does not exist yet (excluding own model):
          {
           $count_result = Database::$connection->prepare("SELECT COUNT(*) FROM ".$db_settings['data_models_table']." WHERE LOWER(title)=LOWER(:title) AND id!=:id");
           $count_result->bindParam(':title', $title, PDO::PARAM_STR);
           $count_result->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
           $count_result->execute();
           list($title_count) = $count_result->fetch();
           if($title_count) $errors[] = 'edit_db_table_error_title_exists'; 
          }
        } 
       
       if($latlong_entry && $geometry_type!=1) $errors[] = 'edit_db_table_error_latlong_entry'; 
         
       if(empty($errors))
        {
         if(isset($_POST['id'])) // edit
          {
           $dbr = Database::$connection->prepare("UPDATE ".$db_settings['data_models_table']." SET last_editor=:last_editor, last_edited=NOW(), title=:title, project=:project, parent_table=:parent_table, geometry_type=:geometry_type, latlong_entry=:latlong_entry, geometry_required=:geometry_required, basemaps=:basemaps, min_scale=:min_scale, max_scale=:max_scale, simplification_tolerance=:simplification_tolerance, simplification_tolerance_extent_factor=:simplification_tolerance_extent_factor, layer_overview=:layer_overview, boundary_layer=:boundary_layer, auxiliary_layer_1=:auxiliary_layer_1, status=:status, readonly=:readonly, data_images=:data_images, item_images=:item_images, description=:description WHERE id=:id");
           $dbr->bindParam(':last_editor', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
           #$dbr->bindParam(':table_name', $table_name, PDO::PARAM_STR);
           $dbr->bindParam(':title', $title, PDO::PARAM_STR);
           $dbr->bindParam(':project', $project, PDO::PARAM_INT);
           #$dbr->bindParam(':type', $type, PDO::PARAM_INT);
           $dbr->bindParam(':parent_table', $parent_table, PDO::PARAM_INT);
           $dbr->bindParam(':geometry_type', $geometry_type, PDO::PARAM_INT);
           $dbr->bindParam(':latlong_entry', $latlong_entry, PDO::PARAM_INT);
           $dbr->bindParam(':geometry_required', $geometry_required, PDO::PARAM_INT);
           if($basemaps) $dbr->bindParam(':basemaps', $basemaps, PDO::PARAM_STR);
           else $dbr->bindValue(':basemaps', null, PDO::PARAM_NULL);
           $dbr->bindParam(':min_scale', $min_scale, PDO::PARAM_STR);
           $dbr->bindParam(':max_scale', $max_scale, PDO::PARAM_STR);
           $dbr->bindParam(':simplification_tolerance', $simplification_tolerance, PDO::PARAM_STR);
           $dbr->bindParam(':simplification_tolerance_extent_factor', $simplification_tolerance_extent_factor, PDO::PARAM_STR);
           $dbr->bindParam(':layer_overview', $layer_overview, PDO::PARAM_INT);
           $dbr->bindParam(':boundary_layer', $boundary_layer, PDO::PARAM_INT);
           $dbr->bindParam(':auxiliary_layer_1', $auxiliary_layer_1, PDO::PARAM_INT);      
           $dbr->bindParam(':status', $status, PDO::PARAM_INT);
           $dbr->bindParam(':readonly', $readonly, PDO::PARAM_INT);
           $dbr->bindParam(':data_images', $data_images, PDO::PARAM_INT);
           $dbr->bindParam(':item_images', $item_images, PDO::PARAM_INT);
           $dbr->bindParam(':description', $description, PDO::PARAM_STR);
           $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
           $dbr->execute();
           $table_id = intval($_POST['id']);
           log_activity(7, $table_id);
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
           $dbr = Database::$connection->prepare("INSERT INTO ".$db_settings['data_models_table']." (sequence, creator, created, table_name, title, project, type, parent_table, geometry_type, latlong_entry, geometry_required, basemaps, min_scale, max_scale, simplification_tolerance, simplification_tolerance_extent_factor, layer_overview, boundary_layer, auxiliary_layer_1, status, readonly, data_images, item_images, description) VALUES (:sequence, :creator, NOW(), :table_name, :title, :project, :type, :parent_table, :geometry_type, :latlong_entry, :geometry_required, :basemaps, :min_scale, :max_scale, :simplification_tolerance, :simplification_tolerance_extent_factor, :layer_overview, :boundary_layer, :auxiliary_layer_1, :status, :readonly, :data_images, :item_images, :description)");
           $dbr->bindParam(':sequence', $new_sequence, PDO::PARAM_INT);
           $dbr->bindParam(':creator', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
           $dbr->bindParam(':table_name', $table_name, PDO::PARAM_STR);
           $dbr->bindParam(':title', $title, PDO::PARAM_STR);
           $dbr->bindParam(':project', $project, PDO::PARAM_INT);
           $dbr->bindParam(':type', $type, PDO::PARAM_INT);
           $dbr->bindParam(':parent_table', $parent_table, PDO::PARAM_INT);
           $dbr->bindParam(':geometry_type', $geometry_type, PDO::PARAM_INT);
           $dbr->bindParam(':latlong_entry', $latlong_entry, PDO::PARAM_INT);
           #$dbr->bindParam(':geometry_required', $geometry_required, PDO::PARAM_INT);
           $dbr->bindValue(':geometry_required', 1, PDO::PARAM_INT);
           if($basemaps) $dbr->bindParam(':basemaps', $basemaps, PDO::PARAM_STR);
           else $dbr->bindValue(':basemaps', null, PDO::PARAM_NULL);
           $dbr->bindParam(':min_scale', $min_scale, PDO::PARAM_STR);
           $dbr->bindParam(':max_scale', $max_scale, PDO::PARAM_STR);
           $dbr->bindParam(':simplification_tolerance', $simplification_tolerance, PDO::PARAM_STR);
           $dbr->bindParam(':simplification_tolerance_extent_factor', $simplification_tolerance_extent_factor, PDO::PARAM_STR);
           $dbr->bindParam(':layer_overview', $layer_overview, PDO::PARAM_INT);        
           $dbr->bindParam(':boundary_layer', $boundary_layer, PDO::PARAM_INT);
           $dbr->bindParam(':auxiliary_layer_1', $auxiliary_layer_1, PDO::PARAM_INT);
           $dbr->bindParam(':status', $status, PDO::PARAM_INT);
           $dbr->bindParam(':readonly', $readonly, PDO::PARAM_INT);
           $dbr->bindParam(':data_images', $data_images, PDO::PARAM_INT);
           $dbr->bindParam(':item_images', $item_images, PDO::PARAM_INT);
           $dbr->bindParam(':description', $description, PDO::PARAM_STR);
           $dbr->execute();   
           $dbr = Database::$connection->query("SELECT LASTVAL()");
           list($table_id) = $dbr->fetch();

           // reorder:
           $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['data_models_table']." SET sequence=sequence+1 WHERE id!=:id");
           $dbr->bindParam(':id', $table_id, PDO::PARAM_INT);
           $dbr->execute();
           log_activity(6, $table_id);
          } 
         if(isset($_POST['id'])) header('Location: '.BASE_URL.'?r=data&data_id='.$table_id.'&success=data_model_saved#properties');
         else header('Location: '.BASE_URL.'?r=data&data_id='.$table_id.'&success=data_model_saved');
         exit;
        }
       else // errors
        {
         if(isset($_POST['id']))
          {
           // get basic data model data:
           $dbr = Database::$connection->prepare("SELECT id, table_name, type FROM ".$db_settings['data_models_table']." WHERE id=:id LIMIT 1");
           $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
           $dbr->execute();
           $data = $dbr->fetch();
           if(isset($data['id']))
            {
             $db_table['id'] = $data['id'];
             $db_table['table_name'] = htmlspecialchars($data['table_name']);
             $db_table['type'] = intval($data['type']);
             
             $db_table['project'] =  intval($project);
             $db_table['geometry_type'] =  intval($geometry_type);
             $db_table['latlong_entry'] =  intval($latlong_entry);
             $db_table['geometry_required'] =  intval($geometry_required);
             $db_table['basemaps'] = explode(',', $basemaps);
             $db_table['min_scale'] =  intval($min_scale);
             $db_table['max_scale'] =  intval($max_scale);
             $db_table['simplification_tolerance'] =  floatval($simplification_tolerance);
             $db_table['simplification_tolerance_extent_factor'] = floatval($simplification_tolerance_extent_factor);
             $db_table['layer_overview'] = intval($layer_overview);
             $db_table['boundary_layer'] = intval($boundary_layer);
             $db_table['auxiliary_layer_1'] = intval($auxiliary_layer_1);
             $db_table['status'] = intval($status);
             $db_table['readonly'] = intval($readonly);
             $db_table['data_images'] = intval($data_images);
             $db_table['item_images'] = intval($item_images);
             $db_table['description'] = htmlspecialchars($description);
             $db_table['$parent_table'] = intval($parent_table);
             
             $db_table['status'] = intval($_POST['status']);
             $db_table['title'] = htmlspecialchars($_POST['title']);
             
             $template->assign('db_table', $db_table);
             $template->assign('errors', $errors);
             $lang['edit_data_model_full_title'] = str_replace('[name]', $db_table['title'], $lang['edit_data_model_full_title']);
             $template->assign('subtitle',$lang['edit_data_model_full_title']);
             $template->assign('subtemplate','data_model.edit_model.inc.tpl');
            }
          }
         else
          {
           $db_table['table_name'] = htmlspecialchars($_POST['table_name']);
           $db_table['title'] = htmlspecialchars($_POST['title']);
           $db_table['type'] = intval($type);
           $template->assign('db_table', $db_table);
           $template->assign('errors', $errors);
           $template->assign('subtitle',$lang['add_data_model_title']);
           $template->assign('subtemplate','data_model.edit_model.inc.tpl');
          }
        }
       }
     break;
    
    case 'delete_model':
     if(isset($_REQUEST['id']) && $permission->granted(Permission::DATA_MANAGEMENT))
      {
       $dbr = Database::$connection->prepare("SELECT id, title, table_name FROM ".Database::$db_settings['data_models_table']." WHERE id=:id LIMIT 1");
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
           $table_exists = true;
          }
         catch(Exception $exception)
          {
           $table_exists = false;
           $records = 0;
          }
         // count images:
         $image_count_result = Database::$connection->prepare('SELECT COUNT(*) FROM "'.Database::$db_settings['data_images_table'].'" WHERE data=:data');
         $image_count_result->bindParam(':data', $_REQUEST['id'], PDO::PARAM_INT);
         $image_count_result->execute();
         list($images) = $image_count_result->fetch();
         
         // get relations:
         $dbr = Database::$connection->prepare("SELECT id, name, label FROM ".Database::$db_settings['data_model_items_table']." WHERE table_id=:table_id");
         $dbr->bindParam(':table_id', $data['id'], PDO::PARAM_INT);
         $dbr->execute();
         while($row = $dbr->fetch())
          {
           $dbr2 = Database::$connection->prepare("SELECT a.id,
                                                         a.name,
                                                         a.label,
                                                         b.table_name,
                                                         b.title
                                                         FROM ".Database::$db_settings['data_model_items_table']." AS a
                                                         LEFT JOIN ".Database::$db_settings['data_models_table']." AS b ON a.table_id=b.id
                                                         WHERE a.relation=:relation");
          $dbr2->bindParam(':relation', $row['id'], PDO::PARAM_INT);
          $dbr2->execute();
          while($row2 = $dbr2->fetch())
           {
            $model['relation'][$row['id']]['name'] = $row['name'];
            $model['relation'][$row['id']]['label'] = $row['label'];  
            $model['relation'][$row['id']]['realtion'][$row2['id']]['name'] = $row2['name'];
            $model['relation'][$row['id']]['realtion'][$row2['id']]['title'] = $row2['title'];
            $model['relation'][$row['id']]['realtion'][$row2['id']]['table_name'] = $row2['table_name'];
            $model['relation'][$row['id']]['realtion'][$row2['id']]['label'] = $row2['label'];
           }
          }
         
         $model['id'] = intval($data['id']);
         $model['title'] = htmlspecialchars($data['title']);
         $model['table'] = htmlspecialchars($data['table_name']);
         $model['table_exists'] = $table_exists;
         $model['records'] = $records;
         $model['images'] = $images;
         if($table_exists)
          {
           $lang['delete_table_info'] = str_replace('[table]', $model['table'], str_replace('[records]', $model['records'], $lang['delete_table_info']));
           $lang['delete_keep_table_label'] = str_replace('[table]', $model['table'], $lang['delete_keep_table_label']);
          }
         if($images)
          {
           $lang['delete_table_images_info'] = str_replace('[number]', $model['images'], $lang['delete_table_images_info']);
          }
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
         if(empty($_POST['keep_table']))
          {
           if($data['type']==1) Database::$connection->query("SELECT DropGeometryTable('".$data['table_name']."')");   
           else Database::$connection->query('DROP TABLE IF EXISTS "'.$data['table_name'].'"');      
          } 

         delete_linked_data($data['id']);
         log_activity(9, $data['id']);
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
         $model_item['priority'] = 0;
         $template->assign('model_item', $model_item);         
         $template->assign('data_id', $row['id']); 
         $template->assign('data_model_title', htmlspecialchars($row['title'])); 
         #$lang['edit_data_model_title'] = str_replace('[name]', $row['title'], $lang['edit_data_model_title']);
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
                                                   items.unique,
                                                   items.choices,
                                                   items.choice_labels,
                                                   items.relation,
                                                   items.relation_column,
                                                   items.required,
                                                   items.overview,
                                                   items.priority,
                                                   items.range_from,
                                                   items.range_to,
                                                   items.column_default_value,
                                                   items.regex,
                                                   items.definition,
                                                   items.comments,
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
       $model_item['unique'] = $row['unique'];
       #$model_item['input_type'] = intval($data['input_type']);
       $model_item['choices'] = htmlspecialchars($row['choices']);
       $model_item['choice_labels'] = htmlspecialchars($row['choice_labels']);
       $model_item['relation'] = intval($row['relation']);
       $model_item['relation_column'] = intval($row['relation_column']);
       $model_item['required'] = intval($row['required']);
       $model_item['overview'] = intval($row['overview']);
       
       if($model_item['column_type']>0) $model_item['item_type'] = 0;
       else $model_item['item_type'] = 1;
       
       $model_item['priority'] = intval($row['priority']);
       $model_item['range_from'] = $row['range_from'];
       $model_item['range_to'] = $row['range_to'];
       $model_item['regex'] = htmlspecialchars($row['regex']);
       $model_item['column_default_value'] = htmlspecialchars($row['column_default_value']);
       $model_item['definition'] = htmlspecialchars($row['definition']);
       $model_item['comments'] = htmlspecialchars($row['comments']);
       $template->assign('model_item', $model_item);
       $template->assign('data_id', $row['table_id']);
       $template->assign('data_model_title', htmlspecialchars($row['title'])); 
       #$lang['edit_data_model_title'] = str_replace('[name]', $row['title'], $lang['edit_data_model_title']);
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
       $dbr = Database::$connection->prepare("SELECT id, table_id, name, column_type, required, column_default_value FROM ".Database::$db_settings['data_model_items_table']." WHERE id=:id LIMIT 1");
       $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
       $dbr->execute();
       $row = $dbr->fetch();
       if(isset($row['id']))
        {
         $data_id = $row['table_id'];
         $current_name = $row['name'];
         $current_column_type = $row['column_type'];
         $current_required = $row['required'];
         $current_column_default_value = $row['column_default_value'];
        } 
      }
     elseif(isset($_REQUEST['data_id'])) // add
      {
       $data_id = intval($_REQUEST['data_id']);
      }
     
     if(isset($data_id) && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($data_id), Permission::MANAGE)))
      {
       if($table_info = get_table_info($data_id))
        {
         // db table item types:
         include(BASE_PATH.'config/column_types.conf.php');
         $template->assign('column_types', $column_types);

         $name = isset($_POST['name']) ? trim($_POST['name']) : '';
         $item_type = isset($_POST['item_type']) && $_POST['item_type']==1 ? 1 : 0;
         $column_length = isset($_POST['column_length']) ? intval($_POST['column_length']) : 0;
         $unique = isset($_POST['unique']) ? true : false;
         $label = isset($_POST['label']) ? trim($_POST['label']) : '';
         $description = isset($_POST['description']) ? trim($_POST['description']) : '';
         $choices = isset($_POST['choices']) ? trim($_POST['choices']) : '';
         $choice_labels = isset($_POST['choice_labels']) ? trim($_POST['choice_labels']) : '';
         $relation = isset($_POST['relation']) ? intval($_POST['relation']) : 0;
         $relation_column = isset($_POST['relation_column']) ? 1 : 0;
         $required = isset($_POST['required']) ? 1 : 0;
         $overview = isset($_POST['overview']) ? 1 : 0;
         $priority = isset($_POST['priority']) ? intval($_POST['priority']) : 0;
         $range_from = isset($_POST['range_from']) ? trim($_POST['range_from']) : '';
         $range_to = isset($_POST['range_to']) ? trim($_POST['range_to']) : '';
         $column_default_value = isset($_POST['column_default_value']) && trim($_POST['column_default_value']) ? trim($_POST['column_default_value']) : null;
         $regex = isset($_POST['regex']) ? trim($_POST['regex']) : '';
         $definition = isset($_POST['definition']) ? trim($_POST['definition']) : '';
         $comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';

         // get column_type of data model if item is edited (type cannot be changed):
         if(isset($_REQUEST['id']) && isset($table_info['columns']))
          {
           foreach($table_info['columns'] as $columns)
            {
             if($columns['id']==$_REQUEST['id'])
              {
               $column_type = $columns['type'];
               break;
              }
            }
           if(empty($column_type))
            {
             $column_type = 0;
            } 
          }
         else
          {          
           $column_type = isset($_POST['column_type']) ? intval($_POST['column_type']) : 0;
          }

         if($column_default_value) $escaped_column_default_value = escape_column_default_value($column_default_value, $column_type);

         // get colums:
         $dbr = Database::$connection->prepare("SELECT column_name from INFORMATION_SCHEMA.COLUMNS WHERE table_name = :table_name");
         $dbr->bindParam(':table_name', $table_info['table']['table_name'], PDO::PARAM_STR);
         $dbr->execute();
         while($column = $dbr->fetch())
          {
           $existing_columns[] = $column['column_name'];  
          }
    
         if(isset($table_info['columns']))
          {
           foreach($table_info['columns'] as $data_model_elements)
            {
             $existing_elements[] = $data_model_elements['name'];
            }
          }
    
         // check column name if not empty:
         if(!is_valid_db_identifier($name))
          {
           $errors[] = 'error_column_name_invalid';
           $error_fields[] = 'name';
          }
         else // if(empty($_REQUEST['no_database_altering']))
          {
           // check if column name already exists:
           if(isset($_REQUEST['id'])) // edit
            {
             // the new column name may only be identical to itself
             if($name!=$current_name && (isset($existing_columns) && in_array($name, $existing_columns) || isset($existing_elements) && in_array($name, $existing_elements)))
              {
               $errors[] = 'error_column_name_already_exists';
               $error_fields[] = 'name';
              }
            }
           else // add
            {
             // the new column name may not exist yet:
             if(isset($existing_columns) && in_array($name, $existing_columns) || isset($existing_elements) && in_array($name, $existing_elements))
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
       
        if($column_type!=0 && empty($column_types[$column_type]))
         {
          $errors[] = 'error_column_type_invalid'; 
          $error_fields[] = 'type';
         }
        
        // check if required column contains empty values:
        if($required)
         {
          if(isset($_REQUEST['id'])) // edit
           {
            $count_result = Database::$connection->prepare('SELECT COUNT(*) FROM "'.$table_info['table']['table_name'].'" WHERE "'.$current_name.'" IS NULL');
            $count_result->execute();
            list($null_count) = $count_result->fetch();
            if($null_count)
             {
              $errors[] = 'error_required_column_empty_values'; 
              $error_fields[] = 'required';           
             }
           }
          elseif(empty($column_default_value)) // add
           {
            $count_result = Database::$connection->prepare('SELECT COUNT(*) FROM "'.$table_info['table']['table_name'].'"');
            $count_result->execute();
            list($record_count) = $count_result->fetch();
            if($record_count)
             {
              $errors[] = 'error_required_existing_records'; 
              $error_fields[] = 'required';           
             }           
           }
         }
        
        // check if relation is valid:
        if($relation)
         {
          if($column_type!=2)
           {
            $errors[] = 'error_relation_type_invalid';
            $error_fields[] = 'type';
            $error_fields[] = 'relation';
           }
          
          $dbr = Database::$connection->prepare("SELECT relation_column FROM ".Database::$db_settings['data_model_items_table']." WHERE id=:id LIMIT 1");
          $dbr->bindParam(':id', $relation, PDO::PARAM_INT);
          $dbr->execute();
          list($relation_check) = $dbr->fetch();
          if(!$relation_check)
           {
            $errors[] = 'error_relation_invalid'; 
            $error_fields[] = 'relation';
           }
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
          
           // disable invalid values for sections:
           if($column_type==0)
            {
             $relation = 0;
             $relation_column = 0;
             $unique = false;
             $required = 0;
             $choices = '';
             $choice_labels = '';
             $range_from = '';
             $range_to = '';
             $column_default_value = null;
             $regex = '';
            }          
          
          if(isset($_REQUEST['id'])) // edit
           {
            if($current_column_type>0 && empty($_REQUEST['no_database_altering']))
             {
              if($name!=$current_name) Database::$connection->query('ALTER TABLE "'.$table_info['table']['table_name'].'" RENAME COLUMN "'.$current_name.'" TO "'.$name.'"');
              
              if($column_default_value!=$current_column_default_value)
               {
                if($column_default_value)
                 {
                  $dbr = Database::$connection->query('ALTER TABLE "'.$table_info['table']['table_name'].'" ALTER COLUMN "'.$name.'" SET DEFAULT '.$escaped_column_default_value);                    
                 }
                else
                 {
                  Database::$connection->query('ALTER TABLE "'.$table_info['table']['table_name'].'" ALTER COLUMN "'.$name.'" DROP DEFAULT'); 
                 }
               }              
              
              if($required!=$current_required)
               {
                if($required) Database::$connection->query('ALTER TABLE "'.$table_info['table']['table_name'].'" ALTER COLUMN "'.$name.'" SET NOT NULL');
                else Database::$connection->query('ALTER TABLE "'.$table_info['table']['table_name'].'" ALTER COLUMN "'.$name.'" DROP NOT NULL'); 
               }
             }

            
            #if(isset($table))
            # {
              $dbr = Database::$connection->prepare("UPDATE ".$db_settings['data_model_items_table']." SET last_editor=:last_editor, last_edited=NOW(), name=:name, label=:label, description=:description, choices=:choices, choice_labels=:choice_labels, relation=:relation, relation_column=:relation_column, required=:required, overview=:overview, priority=:priority, range_from=:range_from, range_to=:range_to, column_default_value=:column_default_value, regex=:regex, definition=:definition, comments=:comments WHERE id=:id");
              $dbr->bindParam(':last_editor', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
              $dbr->bindParam(':name', $name, PDO::PARAM_STR);
              #$dbr->bindParam(':column_type', $column_type, PDO::PARAM_INT);
              #$dbr->bindParam(':column_length', $column_length, PDO::PARAM_INT);
              $dbr->bindParam(':relation_column', $relation_column, PDO::PARAM_INT);
              $dbr->bindParam(':required', $required, PDO::PARAM_INT);
              $dbr->bindParam(':overview', $overview, PDO::PARAM_INT);
              $dbr->bindParam(':priority', $priority, PDO::PARAM_INT);
              if($range_from!='') $dbr->bindValue(':range_from', floatval($range_from), PDO::PARAM_STR);
              else $dbr->bindValue(':range_from', NULL, PDO::PARAM_NULL);
              if($range_to!='') $dbr->bindValue(':range_to', floatval($range_to), PDO::PARAM_STR);
              else $dbr->bindValue(':range_to', NULL, PDO::PARAM_NULL);
              $dbr->bindParam(':column_default_value', $column_default_value, PDO::PARAM_STR);
              $dbr->bindParam(':regex', $regex, PDO::PARAM_STR);
              $dbr->bindParam(':definition', $definition, PDO::PARAM_STR);
              $dbr->bindParam(':comments', $comments, PDO::PARAM_STR);
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
              $alter_table_query = 'ALTER TABLE "'.$table_info['table']['table_name'].'" ADD COLUMN "'.$name.'" '.$column_types[$column_type]['type'];
              if(isset($column_types[$column_type]['length']) && $column_length>0) $alter_table_query .= '('.$column_length.')';
              if($required) $alter_table_query .= ' NOT NULL';
              if($column_default_value) $alter_table_query .= ' DEFAULT ' . $escaped_column_default_value;
              Database::$connection->query($alter_table_query);
              if($unique) Database::$connection->query('ALTER TABLE "'.$table_info['table']['table_name'].'" ADD CONSTRAINT "'.$table_info['table']['table_name'].'_'.$name.'_key" UNIQUE ("'.$name.'")');
             }
            
            // update table information:
            // determine sequence:
            $dbr = Database::$connection->prepare("SELECT sequence FROM ".$db_settings['data_model_items_table']." WHERE table_id=:table_id ORDER BY sequence DESC LIMIT 1");
            $dbr->bindParam(':table_id', $data_id, PDO::PARAM_INT);
            $dbr->execute();
            $row = $dbr->fetch();
            if(isset($row['sequence'])) $new_sequence = $row['sequence'] + 1;
            else $new_sequence = 1;
            $dbr = Database::$connection->prepare("INSERT INTO ".$db_settings['data_model_items_table']." (table_id, sequence, creator, created, name, label, description, column_type, column_length, \"unique\", choices, choice_labels, relation, relation_column, required, overview, priority, range_from, range_to, column_default_value, regex, definition, comments) VALUES (:table_id, :sequence, :creator, NOW(), :name, :label, :description, :column_type, :column_length, :unique, :choices, :choice_labels, :relation, :relation_column, :required, :overview, :priority, :range_from, :range_to, :column_default_value, :regex, :definition, :comments)");
            #$dbr->bindParam(':a1', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
            $dbr->bindParam(':table_id', $data_id, PDO::PARAM_INT);
            $dbr->bindParam(':sequence', $new_sequence, PDO::PARAM_INT);
            $dbr->bindParam(':creator', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
            $dbr->bindParam(':name', $name, PDO::PARAM_STR);
            $dbr->bindParam(':column_type', $column_type, PDO::PARAM_INT);
            $dbr->bindParam(':column_length', $column_length, PDO::PARAM_INT);
            $dbr->bindParam(':unique', $unique, PDO::PARAM_BOOL);
            $dbr->bindParam(':relation_column', $relation_column, PDO::PARAM_INT);
            $dbr->bindParam(':required', $required, PDO::PARAM_INT);
            $dbr->bindParam(':overview', $overview, PDO::PARAM_INT);
            $dbr->bindParam(':priority', $priority, PDO::PARAM_INT);
            if($range_from!='') $dbr->bindValue(':range_from', floatval($range_from), PDO::PARAM_STR);
            else $dbr->bindValue(':range_from', NULL, PDO::PARAM_NULL);
            if($range_to!='') $dbr->bindValue(':range_to', floatval($range_to), PDO::PARAM_STR);
            else $dbr->bindValue(':range_to', NULL, PDO::PARAM_NULL);
            $dbr->bindParam(':column_default_value', $column_default_value, PDO::PARAM_STR);
            $dbr->bindParam(':regex', $regex, PDO::PARAM_STR);
            $dbr->bindParam(':definition', $definition, PDO::PARAM_STR);
            $dbr->bindParam(':comments', $comments, PDO::PARAM_STR);               
            $dbr->bindParam(':label', $label, PDO::PARAM_STR);
            $dbr->bindParam(':description', $description, PDO::PARAM_STR);
            #$dbr->bindParam(':input_type', $input_type, PDO::PARAM_INT);
            $dbr->bindParam(':choices', $choices, PDO::PARAM_STR);
            $dbr->bindParam(':relation', $relation, PDO::PARAM_INT);
            $dbr->bindParam(':choice_labels', $choice_labels, PDO::PARAM_STR);
            $dbr->execute();   
           } 
          log_activity(8, $data_id);
          header('Location: '.BASE_URL.'?r=data&data_id='.$data_id.'#structure');
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
              #$data_title = htmlspecialchars($row['title']);
              $template->assign('data_model_title', htmlspecialchars($row['title'])); 
             }
           }
          elseif(isset($_REQUEST['id']))
           {
            // get model and item data:
            $dbr = Database::$connection->prepare("SELECT items.id,
                                                          items.unique AS unique,
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
              #$data_title = htmlspecialchars($row['title']);
              $template->assign('data_model_title', htmlspecialchars($row['title'])); 
             }
            $model_item['id'] = intval($_REQUEST['id']);
            $template->assign('subtitle', $lang['data_model_edit_item_title']);
           } 
          if(isset($data_id))
           {
            $template->assign('data_id', $data_id);
            #$lang['edit_data_model_title'] = str_replace('[name]', $data_title, $lang['edit_data_model_title']);
            $model_item['name'] = htmlspecialchars($name);
            $model_item['item_type'] = intval($item_type);
            $model_item['column_type'] = intval($column_type);
            if(isset($row['unique'])) $model_item['unique'] = $row['unique'];
            $model_item['label'] = htmlspecialchars($label);
            $model_item['description'] = htmlspecialchars($description);
            $model_item['choices'] = htmlspecialchars($choices);
            $model_item['choice_labels'] = htmlspecialchars($choice_labels);
            $model_item['relation'] = intval($relation);
            $model_item['relation_column'] = intval($relation_column);
            $model_item['required'] = intval($required);
            $model_item['overview'] = intval($overview);
            $model_item['priority'] = intval($priority);
            $model_item['range_from'] = htmlspecialchars($range_from);
            $model_item['range_to'] = htmlspecialchars($range_to);
            $model_item['column_default_value'] = htmlspecialchars($column_default_value);
            $model_item['regex'] = htmlspecialchars($regex);
            $model_item['definition'] = htmlspecialchars($definition);
            $model_item['comments'] = htmlspecialchars($comments);
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
             if($type>0 && empty($_REQUEST['no_database_altering'])) Database::$connection->query('ALTER TABLE "'.$data['table_name'].'" DROP COLUMN IF EXISTS "'.$data['name'].'", DROP CONSTRAINT IF EXISTS "'.$data['table_name'].'_'.$data['name'].'_key"');

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
                    
             log_activity(8, $data_id);
             header('Location: '.BASE_URL.'?r=data&data_id='.$data_id.'#structure');
             exit;
            }
          }      
        }
      }
     break;

    case 'copy_model':
     if(isset($_GET['id']) && $permission->granted(Permission::DATA_MANAGEMENT))
      {
       // get table properties:
       $dbr = Database::$connection->prepare("SELECT id, table_name, title, description FROM ".$db_settings['data_models_table']." WHERE id=:id LIMIT 1");
       $dbr->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
       $dbr->execute();
       $data = $dbr->fetch();
       if(isset($data['id']))
        {
         $db_table['id'] = intval($data['id']);
         $db_table['initial_model_title'] = htmlspecialchars($data['title']);
         $db_table['table_name'] = htmlspecialchars($data['table_name'].$lang['copy_data_model_table_postfix']);
         $db_table['title'] = htmlspecialchars($data['title'].$lang['copy_data_model_title_postfix']);
         $db_table['description'] = htmlspecialchars($data['description']);
         $template->assign('db_table', $db_table);
        
         $template->assign('subtitle', $lang['copy_data_model_title']); 
         $template->assign('subtemplate', 'data_model.copy_model.inc.tpl');            
        }
      }
     break;

    case 'copy_model_submit':
     if(isset($_REQUEST['id']) && $permission->granted(Permission::DATA_MANAGEMENT))
      {
       // check if data model to be copied exists:
       $dbr = Database::$connection->prepare("SELECT id, title, project, type, geometry_type, latlong_entry, geometry_required, basemaps, min_scale, max_scale, simplification_tolerance, simplification_tolerance_extent_factor, layer_overview, boundary_layer, auxiliary_layer_1, status, readonly, data_images, item_images FROM ".$db_settings['data_models_table']." WHERE id=:id LIMIT 1");
       $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
       $dbr->execute();
       $data = $dbr->fetch();
       if(isset($data['id'])) 
        {
         // import posted data:
         $table_name = isset($_POST['table_name']) ? trim($_POST['table_name']) : '';
         $title = isset($_POST['title']) ? trim($_POST['title']) : '';
         $description = isset($_POST['description']) ? trim($_POST['description']) : '';
       
         // check data:
         if(empty($table_name)) $errors[] = 'edit_db_table_error_no_name';
         elseif(in_array(strtolower($table_name), $not_accepted_table_names)) $errors[] = 'edit_db_table_error_name';
         elseif(!is_valid_db_identifier($table_name)) $errors[] = 'edit_db_table_error_name_chars';
         elseif(empty($_REQUEST['no_database_altering']) && isset($existing_tables) and in_array($table_name, $existing_tables)) $errors[] = $lang['edit_db_table_error_table_exists'];
       
         // check for existing data model title:
         $count_result = Database::$connection->prepare("SELECT COUNT(*) FROM ".$db_settings['data_models_table']." WHERE LOWER(title)=LOWER(:title)");
         $count_result->bindParam(':title', $title, PDO::PARAM_STR);
         $count_result->execute();
         list($title_count) = $count_result->fetch();
         if($title_count) $errors[] = 'edit_db_table_error_title_exists'; 
       
         // copy data model if no errors
         if(empty($errors))
          {
           $table_info = get_table_info($data['id']);

           // create table:
           if(empty($_REQUEST['no_database_altering']))
            {
             require(BASE_PATH.'config/default_queries.conf.php');
             foreach($default_query['create_table'][$data['type']] as $create_table_query)
              { 
               $create_table_query = str_replace('[table]', $table_name, $create_table_query);
               Database::$connection->query($create_table_query);
              }
            
             // add columns:
             if(isset($table_info['columns']))
              {
               foreach($table_info['columns'] as $column)
                {
                 if($column['column_length']) $length_addition = '('.$column['column_length'].')';
                 else $length_addition = '';
                 if($column['required']) $not_null_addition = ' NOT NULL';
                 else $not_null_addition = '';  
                 if($column['column_default_value']) $column_default_value_addition = ' DEFAULT ' . escape_column_default_value($column['column_default_value'], $column['type']);
                 else $column_default_value_addition = '';
                 if($column['type']>0 && isset($column_types[$column['type']])) 
                  {
                   $alter_table_query_items[] = 'ADD COLUMN "'.$column['name'].'" '.$column_types[$column['type']]['type'].$length_addition.$not_null_addition.$column_default_value_addition;
                   if($column['unique']) $additional_alter_table_queries[] = 'ALTER TABLE "'.$table_name.'" ADD CONSTRAINT "'.$table_name.'_'.$column['name'].'_key" UNIQUE ("'.$column['name'].'")';
                  }
                } 
              }
           
             if(isset($alter_table_query_items));
              {
               $alter_table_query = 'ALTER TABLE "'.$table_name.'" '.implode(', ', $alter_table_query_items).';';
               Database::$connection->query($alter_table_query);
              }            
             if(isset($additional_alter_table_queries));
              {
               foreach($additional_alter_table_queries as $additional_alter_table_query)
                {
                 Database::$connection->query($additional_alter_table_query);
                }
              }       
            } 

           // save data model:
           $new_sequence = 1;
           $dbr = Database::$connection->prepare("INSERT INTO ".$db_settings['data_models_table']." (sequence, creator, created, table_name, title, project, type, parent_table, geometry_type, latlong_entry, geometry_required, basemaps, min_scale, max_scale, simplification_tolerance, simplification_tolerance_extent_factor, layer_overview, boundary_layer, auxiliary_layer_1, status, readonly, data_images, item_images, description) VALUES (:sequence, :creator, NOW(), :table_name, :title, :project, :type, 0, :geometry_type, :latlong_entry, :geometry_required, :basemaps, :min_scale, :max_scale, :simplification_tolerance, :simplification_tolerance_extent_factor, :layer_overview, :boundary_layer, :auxiliary_layer_1, :status, :readonly, :data_images, :item_images, :description)");
           $dbr->bindParam(':sequence', $new_sequence, PDO::PARAM_INT);
           $dbr->bindParam(':creator', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
           $dbr->bindParam(':table_name', $table_name, PDO::PARAM_STR);
           $dbr->bindParam(':title', $title, PDO::PARAM_STR);
           $dbr->bindParam(':project', $data['project'], PDO::PARAM_INT);
           $dbr->bindParam(':type', $data['type'], PDO::PARAM_INT);
           $dbr->bindParam(':geometry_type', $data['geometry_type'], PDO::PARAM_INT);
           $dbr->bindParam(':latlong_entry', $data['latlong_entry'], PDO::PARAM_INT);
           $dbr->bindParam(':geometry_required', $data['geometry_required'], PDO::PARAM_INT);
           $dbr->bindParam(':basemaps', $data['basemaps'], PDO::PARAM_STR);
           $dbr->bindParam(':min_scale', $data['min_scale'], PDO::PARAM_STR);
           $dbr->bindParam(':max_scale', $data['max_scale'], PDO::PARAM_STR);
           $dbr->bindParam(':simplification_tolerance', $data['simplification_tolerance'], PDO::PARAM_STR);
           $dbr->bindParam(':simplification_tolerance_extent_factor', $data['simplification_tolerance_extent_factor'], PDO::PARAM_STR);
           $dbr->bindParam(':layer_overview', $data['layer_overview'], PDO::PARAM_INT);        
           $dbr->bindParam(':boundary_layer', $data['boundary_layer'], PDO::PARAM_INT);
           $dbr->bindParam(':auxiliary_layer_1', $data['auxiliary_layer_1'], PDO::PARAM_INT);
           $dbr->bindParam(':status', $data['status'], PDO::PARAM_INT);
           $dbr->bindParam(':readonly', $data['readonly'], PDO::PARAM_INT);
           $dbr->bindParam(':data_images', $data['data_images'], PDO::PARAM_INT);
           $dbr->bindParam(':item_images', $data['item_images'], PDO::PARAM_INT);
           $dbr->bindParam(':description', $description, PDO::PARAM_STR);
           $dbr->execute();   
           $dbr = Database::$connection->query("SELECT LASTVAL()");
           list($new_table_id) = $dbr->fetch();

           // reorder:
           $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['data_models_table']." SET sequence=sequence+1 WHERE id!=:id");
           $dbr->bindParam(':id', $new_table_id, PDO::PARAM_INT);
           $dbr->execute();
           
           // save data model items:
           if(isset($table_info['columns']))
            {
             $dbr = Database::$connection->prepare('INSERT INTO '.$db_settings['data_model_items_table'].' (table_id, sequence, creator, created, name, label, description, column_type, column_length, "unique", choices, choice_labels, relation, relation_column, required, priority, range_from, range_to, column_default_value, regex, definition, comments) VALUES (:table_id, :sequence, :creator, NOW(), :name, :label, :description, :column_type, :column_length, :unique, :choices, :choice_labels, :relation, :relation_column, :required, :priority, :range_from, :range_to, :column_default_value, :regex, :definition, :comments)');

             $sequence = 1;
             foreach($table_info['columns'] as $column)
              {
               $dbr->bindParam(':table_id', $new_table_id, PDO::PARAM_INT);
               $dbr->bindParam(':sequence', $sequence, PDO::PARAM_INT);
               $dbr->bindParam(':creator', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
               $dbr->bindParam(':name', $column['name'], PDO::PARAM_STR);
               $dbr->bindParam(':column_type', $column['type'], PDO::PARAM_INT);
               $dbr->bindParam(':column_length', $column['column_length'], PDO::PARAM_INT);
               $dbr->bindParam(':unique', $column['unique'], PDO::PARAM_BOOL);
               $dbr->bindParam(':relation', $column['relation'], PDO::PARAM_INT);
               $dbr->bindParam(':relation_column', $column['relation_column'], PDO::PARAM_INT);
               $dbr->bindParam(':required', $column['required'], PDO::PARAM_INT);
               $dbr->bindParam(':priority', $column['priority'], PDO::PARAM_INT);
               if($column['range_from']!='') $dbr->bindValue(':range_from', floatval($column['range_from']), PDO::PARAM_STR);
               else $dbr->bindValue(':range_from', NULL, PDO::PARAM_NULL);
               if($column['range_to']!='') $dbr->bindValue(':range_to', floatval($column['range_to']), PDO::PARAM_STR);
               else $dbr->bindValue(':range_to', NULL, PDO::PARAM_NULL);
               $dbr->bindParam(':column_default_value', $column['column_default_value'], PDO::PARAM_STR);
               $dbr->bindParam(':regex', $column['regex'], PDO::PARAM_STR);
               $dbr->bindParam(':definition', $column['definition'], PDO::PARAM_STR);
               $dbr->bindParam(':comments', $column['comments'], PDO::PARAM_STR);               
               $dbr->bindParam(':label', $column['label'], PDO::PARAM_STR);
               $dbr->bindParam(':description', $column['description'], PDO::PARAM_STR);
               if($column['choices'])
                {
                 $dbr->bindValue(':choices', implode("\n", $column['choices']), PDO::PARAM_STR);
                 if(isset($column['choice_labels'])) $dbr->bindValue(':choice_labels', implode("\n", $column['choice_labels']), PDO::PARAM_STR);
                 else $dbr->bindValue(':choice_labels', '', PDO::PARAM_STR);
                }
               else
                {
                 $dbr->bindValue(':choices', '', PDO::PARAM_STR);
                 $dbr->bindValue(':choice_labels', '', PDO::PARAM_STR);
                } 
               $dbr->execute();   
               ++$sequence;
              } 
            }
           log_activity(6, $new_table_id);
           header('Location: '.BASE_URL.'?r=data_model.edit_model&id='.$new_table_id.'&success=data_model_saved#properties');
           exit;
         
          }
         else // errors
          {
           $db_table['id'] = $data['id'];
           $db_table['initial_model_title'] = htmlspecialchars($data['title']);
           $db_table['table_name'] = htmlspecialchars($table_name);
           $db_table['title'] = htmlspecialchars($title);
           $db_table['description'] = htmlspecialchars($description);
           $template->assign('db_table', $db_table);
           $template->assign('subtitle', $lang['copy_data_model_title']); 
           $template->assign('subtemplate', 'data_model.copy_model.inc.tpl');            
           $template->assign('errors', $errors);
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
