<?php
if(!defined('IN_INDEX')) exit;

if($permission->granted(Permission::ADMIN))
 {
  switch($action)
   {
    case 'default':
     $dbr = Database::$connection->prepare('SELECT id, title, properties, js, "default" FROM '.Database::$db_settings['basemaps_table'].' ORDER BY sequence ASC');
     $dbr->execute();
     $i=0;
     while($row = $dbr->fetch()) 
      {
       $basemaps[$i]['id'] = $row['id'];
       $basemaps[$i]['title'] = htmlspecialchars($row['title']);
       $basemaps[$i]['properties'] = $row['properties'];
       $basemaps[$i]['js'] = $row['js'];
       $basemaps[$i]['default'] = $row['default'];
       ++$i;
      }
     if(isset($basemaps)) $template->assign('basemaps', $basemaps);
     $javascripts[] = JQUERY_UI;
     $javascripts[] = JQUERY_UI_HANDLER;
     $template->assign('subtitle', $lang['basemaps_title']); 
     $template->assign('subtemplate', 'basemaps.inc.tpl');
     break;
    case 'add':
     $template->assign('subtitle', $lang['basemaps_add_title']); 
     $template->assign('subtemplate', 'basemaps.edit.inc.tpl');
     break;
    case 'edit':
     if(isset($_GET['id']))
      {
       $dbr = Database::$connection->prepare('SELECT id, title, properties, js, "default" FROM '.Database::$db_settings['basemaps_table'].' WHERE id=:id LIMIT 1');
       $dbr->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
       $dbr->execute();
       $row = $dbr->fetch();
       if(isset($row['id']))
        {
         $basemap['id'] = $row['id'];
         $basemap['title'] = htmlspecialchars($row['title']);
         $basemap['properties'] = htmlspecialchars($row['properties']);
         $basemap['js'] = htmlspecialchars($row['js']);
         $basemap['default'] = htmlspecialchars($row['default']);
         $template->assign('basemap', $basemap); 
         $template->assign('subtitle', $lang['basemaps_edit_title']); 
         $template->assign('subtemplate', 'basemaps.edit.inc.tpl');
        }
      }
     break;   
   
    case 'edit_submit':
    case 'add_submit':
     // import posted data:
     $title = isset($_POST['title']) ? trim($_POST['title']) : '';
     $properties = isset($_POST['properties']) ? trim($_POST['properties']) : '';
     $js = isset($_POST['js']) ? trim($_POST['js']) : '';
     $default = isset($_POST['default']) ? true : false;
 
     if(empty($title)) $errors[] = 'basemaps_error_no_title';
     if(empty($properties)) $errors[] = 'basemaps_error_no_properties';

     // insert/update user if no errors:
     if(empty($errors))
      {
       if(isset($_POST['id'])) // edit
        {
         $dbr = Database::$connection->prepare('UPDATE '.Database::$db_settings['basemaps_table'].' SET title=:title, properties=:properties, js=:js, "default"=:default WHERE id=:id');
         $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
         $dbr->bindParam(':title', $title, PDO::PARAM_STR);
         $dbr->bindParam(':properties', $properties, PDO::PARAM_STR);
         $dbr->bindParam(':js', $js, PDO::PARAM_STR);
         $dbr->bindParam(':default', $default, PDO::PARAM_BOOL);
         $dbr->execute();
        }
       else // add
        {
         // determine sequence:
         $dbr = Database::$connection->prepare("SELECT sequence FROM ".$db_settings['basemaps_table']." ORDER BY sequence DESC LIMIT 1");
         $dbr->execute();
         $row = $dbr->fetch();
         if(isset($row['sequence'])) $new_sequence = $row['sequence'] + 1;
         else $new_sequence = 1;
         $dbr = Database::$connection->prepare('INSERT INTO '.Database::$db_settings['basemaps_table'].' (sequence, title, properties, js, "default") VALUES (:sequence, :title, :properties, :js, :default)');
         $dbr->bindParam(':sequence', $new_sequence, PDO::PARAM_INT);
         $dbr->bindParam(':title', $title, PDO::PARAM_STR);
         $dbr->bindParam(':properties', $properties, PDO::PARAM_STR);
         $dbr->bindParam(':js', $js, PDO::PARAM_STR);
         $dbr->bindParam(':default', $default, PDO::PARAM_BOOL);
         $dbr->execute();
       }
      header('Location: '.BASE_URL.'?r=basemaps');
      exit;   
     }
    else
     {
      if(isset($_POST['id']))
       {
        $template->assign('subtitle', $lang['basemaps_edit_title']); 
        $basemap['id'] = intval($_POST['id']);
       }
      $basemap['title'] = htmlspecialchars($title);
      $basemap['properties'] = htmlspecialchars($properties);
      $basemap['js'] = htmlspecialchars($js);
      $basemap['default'] = htmlspecialchars($default);
      $template->assign('basemap', $basemap);
      $template->assign('errors', $errors);
      $template->assign('subtemplate', 'basemaps.edit.inc.tpl');
     }
    break;
    
   case 'delete':  
    if(isset($_REQUEST['id']))
     {
      if(empty($_REQUEST['confirmed']))
       {
        $template->assign('r', 'basemaps.delete');
        $template->assign('id',intval($_REQUEST['id'])); 
        $template->assign('subtitle',$lang['basemaps_delete_message']);
        //$template->assign('delete_message', 'basemaps_delete_message');
        $template->assign('subtemplate','delete_confirm.inc.tpl');
       }
      else
       {
        $dbr = Database::$connection->prepare("DELETE FROM ".$db_settings['basemaps_table']." WHERE id = :id");
        $dbr->bindValue(':id', $_REQUEST['id'], PDO::PARAM_INT);
        $dbr->execute();
        // reorder...
        $dbr = Database::$connection->prepare("SELECT id FROM ".$db_settings['basemaps_table']." ORDER BY sequence ASC");
        $dbr->execute();
        $i=1;
        while($row = $dbr->fetch()) 
         {
          $dbr2 = Database::$connection->prepare("UPDATE ".$db_settings['basemaps_table']." SET sequence=:sequence WHERE id=:id");
          $dbr2->bindValue(':sequence', $i, PDO::PARAM_INT);
          $dbr2->bindValue(':id', $row['id'], PDO::PARAM_INT);
          $dbr2->execute();
          ++$i;
         }          
        header('Location: '.BASE_URL.'?r=basemaps');
        exit;
       }
     }
    break;    

    case 'reorder':
     if(isset($_REQUEST['item']) && is_array($_REQUEST['item']))
      {
       $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['basemaps_table']." SET sequence=:sequence WHERE id=:id");
       $dbr->bindParam(':sequence', $sequence, PDO::PARAM_INT);
       $dbr->bindParam(':id', $id, PDO::PARAM_INT);
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
     break;            
    
    
   }
 }
?>
