<?php
if(!defined('IN_INDEX')) exit;

if($settings['data_images'] && $permission->granted(Permission::USER))
 {
  switch($action)
   {
    case 'add':
     // check permission:
     if(isset($_REQUEST['data_id']) && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($_REQUEST['data_id']), Permission::WRITE)))
      {
       if($table_info = get_table_info($_REQUEST['data_id']))
        {
         $data_model['id'] = $table_info['table']['id'];
         $data_model['title'] = htmlspecialchars($table_info['table']['title']);
        
         // check if data images are enabled:
         if(empty($table_info['table']['readonly']) && ($table_info['table']['data_images'] || (isset($_REQUEST['item_id']) && $table_info['table']['item_images'])))
          {
           if(isset($_REQUEST['item_id'])) $template->assign('item_id', intval($_REQUEST['item_id']));
           $template->assign('data_model', $data_model);
           $template->assign('subtitle', $lang['add_data_image_title']); 
           $template->assign('subtemplate','data_image.inc.tpl');
          }
        }
      }
     break;
       
     case 'add_submit':
      // check permission:
      if(isset($_REQUEST['data_id']) && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($_REQUEST['data_id']), Permission::WRITE)))
       {
        if($table_info = get_table_info($_REQUEST['data_id']))
         {
          $data_model['id'] = $table_info['table']['id'];
          $data_model['title'] = htmlspecialchars($table_info['table']['title']);
         
          // check if data images are enabled:
          if(empty($table_info['table']['readonly']) && ($table_info['table']['data_images'] || (isset($_REQUEST['item_id']) && $table_info['table']['item_images'])))
           {
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $author = isset($_POST['author']) ? trim($_POST['author']) : '';

            if(empty($_FILES['image']['name'])) $errors[] = 'error_no_image_selected';
            elseif(!is_uploaded_file($_FILES['image']['tmp_name']) || empty($_FILES['image']['size']) || $_FILES['image']['error']) $errors[] = 'error_image_upload';

            if(empty($title)) $errors[] = 'error_image_no_title';
          
            if(empty($errors))
             {
            
              if(isset($_POST['item_id'])) // item image
               {
                // check if item exists:
                $dbr = Database::$connection->prepare('SELECT id FROM "'.$table_info['table']['table_name'].'" WHERE id=:id LIMIT 1');
                $dbr->bindParam(':id', $_POST['item_id'], PDO::PARAM_INT);
                $dbr->execute();
                $row = $dbr->fetch();
                if(isset($row['id'])) $item_id = $row['id'];
                else $errors[] = 'item_not_available'; 
               } 
              else $item_id = 0;
             }


              if(empty($errors))
               {
                $upload_info = getimagesize($_FILES['image']['tmp_name']);
                if($upload_info[2]!=IMAGETYPE_JPEG && $upload_info[2]!=IMAGETYPE_JPEG2000 && $upload_info[2]!=IMAGETYPE_PNG && $upload_info[2]!=IMAGETYPE_GIF) $errors[] = 'error_image_invalid_file_type';
               }
  
              if(empty($errors))
               {
                spl_autoload_register('imagineLoader');
                $filename = $data_model['id'].'.'.$item_id.'.'.gmdate("YmdHis").'.'.uniqid();
                if($upload_info[2]==IMAGETYPE_PNG) $extension = 'png';
                elseif($upload_info[2]==IMAGETYPE_GIF) $extension = 'gif';
                else $extension = 'jpg';
                $image_info['filename'] = $filename . '.' . $extension; 
                $imagine = new Imagine\Gd\Imagine();
                $image = $imagine->open($_FILES['image']['tmp_name']);       
                // create photo:
                $image_options = array('quality' => $settings['gallery_image_quality']);
                $image_size = new Imagine\Image\Box($settings['gallery_image_width'], $settings['gallery_image_height']);
                $image_mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET;
                $image->thumbnail($image_size, $image_mode)->save(DATA_IMAGES_PATH.$image_info['filename'], $image_options);       
                // create thumbnail:
                $thumbnail_options = array('quality' => $settings['gallery_thumbnail_quality']);
                #if($upload_info[1]>$upload_info[0]) $thumbnail_size = new Imagine\Image\Box($settings['project_thumbnail_height'], $settings['project_thumbnail_width']);
                $thumbnail_size = new Imagine\Image\Box($settings['gallery_thumbnail_width'], $settings['gallery_thumbnail_height']);
                $thumbnail_mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET;
                $image->thumbnail($thumbnail_size, $thumbnail_mode)->save(DATA_THUMBNAILS_PATH.$image_info['filename'], $thumbnail_options); 
                // get image info:
                $saved_image_info = getimagesize(DATA_IMAGES_PATH.$image_info['filename']);
                $saved_thumbnail_info = getimagesize(DATA_THUMBNAILS_PATH.$image_info['filename']);
                $image_info['image_width'] = $saved_image_info[0];
                $image_info['image_height'] = $saved_image_info[1];
                $image_info['thumbnail_width'] = $saved_thumbnail_info[0];
                $image_info['thumbnail_height'] = $saved_thumbnail_info[1];
                // save original image:
                if($settings['data_images_save_original'])
                 {
                  move_uploaded_file($_FILES['image']['tmp_name'], DATA_ORIGINAL_IMAGES_PATH.$image_info['filename']);
                 }
                // determine sequence:
                $dbr = Database::$connection->prepare("SELECT sequence FROM ".$db_settings['data_images_table']." WHERE data=:data AND item=:item ORDER BY sequence DESC LIMIT 1");
                $dbr->bindParam(':data', $data_model['id'], PDO::PARAM_INT);
                $dbr->bindParam(':item', $item_id, PDO::PARAM_INT);
                $dbr->execute();
                $row = $dbr->fetch();
                if(isset($row['sequence'])) $new_sequence = $row['sequence'] + 1;
                else $new_sequence = 1;
                // save record:
                $dbr = Database::$connection->prepare("INSERT INTO ".$db_settings['data_images_table']." (data, item, sequence, creator, created, title, description, author, filename, original_filename, thumbnail_width, thumbnail_height, image_width, image_height) VALUES (:data, :item, :sequence, :creator, NOW(), :title, :description, :author, :filename, :original_filename, :thumbnail_width, :thumbnail_height, :image_width, :image_height)");
                $dbr->bindParam(':data', $data_model['id'], PDO::PARAM_INT);
                $dbr->bindParam(':item', $item_id, PDO::PARAM_INT);
                $dbr->bindParam(':sequence', $new_sequence, PDO::PARAM_INT);
              
                $dbr->bindParam(':creator', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
                $title = truncate($title, 255);
                $dbr->bindParam(':title', $title, PDO::PARAM_STR);
                $dbr->bindParam(':description', $description, PDO::PARAM_STR);
                $dbr->bindParam(':author', $author, PDO::PARAM_STR);
                $dbr->bindParam(':filename', $image_info['filename'], PDO::PARAM_STR);
                $dbr->bindParam(':original_filename', $_FILES['image']['name'], PDO::PARAM_STR);
                $dbr->bindParam(':thumbnail_width', $image_info['thumbnail_width'], PDO::PARAM_INT);
                $dbr->bindParam(':thumbnail_height', $image_info['thumbnail_height'], PDO::PARAM_INT);
                $dbr->bindParam(':image_width', $image_info['image_width'], PDO::PARAM_INT);
                $dbr->bindParam(':image_height', $image_info['image_height'], PDO::PARAM_INT);
                $dbr->execute();
                
                if($item_id)
                 {
                  log_activity(ACTIVITY_ADD_DATA_ITEM_IMAGE, $data_model['id'], $item_id);
                  header('Location: '.BASE_URL.'?r=data_item&data_id='.$data_model['id'].'&id='.$item_id.'#images');
                 }
                else
                 {
                  log_activity(ACTIVITY_ADD_DATA_IMAGE, $data_model['id']);
                  header('Location: '.BASE_URL.'?r=data&data_id='.$data_model['id'].'#images');
                 }
                exit;
               }
              else
               {
                $template->assign('errors', $errors);
                if(isset($_REQUEST['item_id'])) $template->assign('item_id', intval($_REQUEST['item_id']));
                $template->assign('data_model', $data_model);
                $image['title'] = htmlspecialchars($title);
                $image['description'] = htmlspecialchars($description);
                $image['author'] = htmlspecialchars($author);
                $template->assign('image', $image);
                $template->assign('subtitle', $lang['add_data_image_title']); 
                $template->assign('subtemplate','data_image.inc.tpl');
               }
              }
            }
           }
          break;
       
         case 'edit':
          if(isset($_GET['id']))
           {
            // get image data:
            $dbr = Database::$connection->prepare("SELECT id,
                                                          data,
                                                          item,
                                                          title,
                                                          description,
                                                          author,
                                                          filename
                                                   FROM ".Database::$db_settings['data_images_table']."
                                                   WHERE id=:id LIMIT 1");
            $dbr->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
            $dbr->execute();
            $row = $dbr->fetch();
            if(isset($row['id']))
             {      	  
              // check permission:
              if($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, $row['data'], Permission::WRITE))
               {
                // get model information: 
                if($table_info = get_table_info($row['data']))
                 {
                  $data_model['id'] = $table_info['table']['id'];
                  $data_model['title'] = htmlspecialchars($table_info['table']['title']);
                  // check if data images are enabled:
                  if(empty($table_info['table']['readonly']) && ($table_info['table']['data_images'] || ($row['item'] && $table_info['table']['item_images'])))
                   {
                    $image['id'] = intval($row['id']);
                    $image['title'] = htmlspecialchars($row['title']);
                    $image['description'] = htmlspecialchars($row['description']);
                    $image['author'] = htmlspecialchars($row['author']);
                    $image['filename'] = htmlspecialchars($row['filename']);
                    if($settings['data_images_permission_check'])
                     {
                      $image['image_url'] = BASE_URL.'?r=data_image.image&file='.$row['filename'];
                     }
                    else
                     {
                      $image['image_url'] = DATA_IMAGES_URL.$row['filename'];
                     }                    
                    $template->assign('data_model', $data_model);
                    if($row['item']) $template->assign('item_id', $row['item']);
                    $template->assign('image', $image);
                    $template->assign('subtitle', $lang['edit_data_image_title']);
                    $template->assign('subtemplate', 'data_image.inc.tpl');        
                   }
                 }
               }
             }
           }
          break;

         case 'edit_submit':
          if(isset($_POST['id']))
           {
            // get image data:
            $dbr = Database::$connection->prepare("SELECT id,
                                                          data,
                                                          item,
                                                          title,
                                                          description,
                                                          author,
                                                          filename
                                                   FROM ".Database::$db_settings['data_images_table']."
                                                   WHERE id=:id LIMIT 1");
            $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
            $dbr->execute();
            $row = $dbr->fetch();
            if(isset($row['id']))
             {
              // check permission:
              if($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, $row['data'], Permission::WRITE))
               {
                // get model information: 
                if($table_info = get_table_info($row['data']))
                 {
                  $data_model['id'] = $table_info['table']['id'];
                  $data_model['title'] = htmlspecialchars($table_info['table']['title']);
                  // check if data images are enabled:
                  if(empty($table_info['table']['readonly']) && ($table_info['table']['data_images'] || ($row['item'] && $table_info['table']['item_images'])))
                   {          
                    // import posted data:
                    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
                    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
                    $author = isset($_POST['author']) ? trim($_POST['author']) : '';                    
                    
                    if(empty($title)) $errors[] = 'error_image_no_title';          
          
                    if(empty($errors))
                     {
                      // update record:
                      $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['data_images_table']." SET title=:title, description=:description, author=:author, last_editor=:last_editor, last_edited=NOW() WHERE id=:id");
                      $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
                      $dbr->bindParam(':last_editor', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
                      $title = truncate($title, 255);
                      $dbr->bindParam(':title', $title, PDO::PARAM_STR);
                      $dbr->bindParam(':description', $description, PDO::PARAM_STR);
                      $dbr->bindParam(':author', $author, PDO::PARAM_STR);
                      $dbr->execute();
                      if($row['item'])
                       {
                        log_activity(ACTIVITY_EDIT_DATA_ITEM_IMAGE, $data_model['id'], $row['item']);
                        header('Location: '.BASE_URL.'?r=data_item&data_id='.$data_model['id'].'&id='.$row['item'].'#images');
                       }
                      else
                       {
                        log_activity(ACTIVITY_EDIT_DATA_IMAGE, $data_model['id']);
                        header('Location: '.BASE_URL.'?r=data&data_id='.$data_model['id'].'#images');
                       }
                      exit;
                     }
                    else
                     {
                      $template->assign('errors', $errors);
                      if($row['item']) $template->assign('item_id', $row['item']);
                      $image['id'] = intval($row['id']);
                      $image['title'] = htmlspecialchars($title);
                      $image['description'] = htmlspecialchars($description);
                      $image['author'] = htmlspecialchars($author);
                      $image['filename'] = htmlspecialchars($row['filename']);                      
                      $template->assign('image', $image);
                      $template->assign('data_model', $data_model);
                      $template->assign('subtitle', $lang['edit_data_image_title']); 
                      $template->assign('subtemplate','data_image.inc.tpl');

                     }            
                   }
                 }
               }
             }
           }
          break;

         case 'delete':
          if(isset($_REQUEST['id']))
           {
            // get image data:
            $dbr = Database::$connection->prepare("SELECT id,
                                                          data,
                                                          item,
                                                          filename
                                                   FROM ".Database::$db_settings['data_images_table']."
                                                   WHERE id=:id LIMIT 1");
            $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
            $dbr->execute();
            $row = $dbr->fetch();
            if(isset($row['id']))
             {
              // check permission:
              if($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, $row['data'], Permission::WRITE))
               {
                // get model information: 
                if($table_info = get_table_info($row['data']))
                 {
                  $data_model['id'] = $table_info['table']['id'];
                  // check if data images are enabled:
                  if(empty($table_info['table']['readonly']) && ($table_info['table']['data_images'] || ($row['item'] && $table_info['table']['item_images'])))
                   {          
                    $dbr = Database::$connection->prepare("DELETE FROM ".Database::$db_settings['data_images_table']." WHERE id=:id");
                    $dbr->bindParam(':id', $row['id'], PDO::PARAM_INT);
                    $dbr->execute();
                    @unlink(DATA_IMAGES_PATH.$row['filename']);
                    @unlink(DATA_THUMBNAILS_PATH.$row['filename']);
                    @unlink(DATA_ORIGINAL_IMAGES_PATH.$row['filename']);
                    // reorder...
                    $dbr = Database::$connection->prepare("SELECT id FROM ".Database::$db_settings['data_images_table']." WHERE data=:data AND item=:item ORDER BY sequence ASC");
                    $dbr->bindParam(':data', $data_model['id'], PDO::PARAM_INT);
                    $dbr->bindParam(':item', $row['item'], PDO::PARAM_INT);
                    $dbr->execute();
                    $i=1;
                    while($row2 = $dbr->fetch()) 
                     {
                      $dbr2 = Database::$connection->prepare("UPDATE ".Database::$db_settings['data_images_table']." SET sequence=:sequence WHERE id=:id");
                      $dbr2->bindValue(':sequence', $i, PDO::PARAM_INT);
                      $dbr2->bindValue(':id', $row2['id'], PDO::PARAM_INT);
                      $dbr2->execute();
                      ++$i;
                     }          
                    if($row['item'])
                     {
                      log_activity(ACTIVITY_DELETE_DATA_ITEM_IMAGE, $data_model['id'], $row['item']);
                      header('Location: '.BASE_URL.'?r=data_item&data_id='.$data_model['id'].'&id='.$row['item'].'#images');
                     } 
                    else
                     {
                      log_activity(ACTIVITY_DELETE_DATA_IMAGE, $data_model['id']);
                      header('Location: '.BASE_URL.'?r=data&data_id='.$data_model['id'].'#images');
                     }
                    exit;
                   }
                 }
               }
             }
           }

         case 'reorder':
          if(isset($_REQUEST['item']) &&  is_array($_REQUEST['item']))
           {
            // get image data of first element:
            $dbr = Database::$connection->prepare("SELECT id,
                                                          data,
                                                          item,
                                                          filename
                                                   FROM ".Database::$db_settings['data_images_table']."
                                                   WHERE id=:id LIMIT 1");
            $dbr->bindParam(':id', $_REQUEST['item'][0], PDO::PARAM_INT);
            $dbr->execute();
            $row = $dbr->fetch();
            if(isset($row['id']))
             {
              // check permission:
              if($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, $row['data'], Permission::WRITE))
               {
                // get model information: 
                if($table_info = get_table_info($row['data']))
                 {
                  $data_model['id'] = $table_info['table']['id'];
                  // check if data images are enabled:
                  if(empty($table_info['table']['readonly']) && ($table_info['table']['data_images'] || ($row['item'] && $table_info['table']['item_images'])))
                   {
                    $dbr = Database::$connection->prepare("UPDATE ".$db_settings['data_images_table']." SET sequence=:sequence WHERE id=:id AND data=:data");
                    $dbr->bindParam(':sequence', $sequence, PDO::PARAM_INT);
                    $dbr->bindParam(':id', $id, PDO::PARAM_INT);
                    $dbr->bindParam(':data', $data_model['id'], PDO::PARAM_INT);
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
               }
             }
           } 
          break;
         
         case 'download':
          if($settings['data_images_download_original'] && isset($_GET['id']))
           {
            // get image data of first element:
            $dbr = Database::$connection->prepare("SELECT id,
                                                          data,
                                                          item,
                                                          filename,
                                                          original_filename
                                                   FROM ".Database::$db_settings['data_images_table']."
                                                   WHERE id=:id LIMIT 1");
            $dbr->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
            $dbr->execute();
            $row = $dbr->fetch();
            if(isset($row['id']))
             {
              // check permission:
              if($permission->granted(Permission::DATA_ACCESS, $row['data'], Permission::READ))
               {
                if(file_exists(DATA_ORIGINAL_IMAGES_PATH.$row['filename'])) $file = DATA_ORIGINAL_IMAGES_PATH.$row['filename'];
                elseif(file_exists(DATA_IMAGES_PATH.$row['filename'])) $file = DATA_IMAGES_PATH.$row['filename'];
                if(isset($file))
                 {
                  // read file information and file content:
                  $image_info = getimagesize($file);
                  $image = file_get_contents($file);
                  if($row['original_filename']) $filename = $row['original_filename'];
                  else $filename = $row['filename'];                   
                  // output image:
                  header('Content-type: '.$image_info['mime']);
                  header('Content-Disposition: attachment; filename="'.$filename.'"');
                  echo $image;
                  exit;
                 }
               }
             }
           }
          break;
         
         /* image output (if direct linking is disabled): */
         case 'image':
         case 'thumbnail':
          if(isset($_GET['file']))
           {
            // set image path:
            if($action=='thumbnail') $path = DATA_THUMBNAILS_PATH;
            else $path = DATA_IMAGES_PATH;
            // remove all characters except lower case ASCII letters, numbers and dot (security measure):
            $file = preg_replace('/[^a-z0-9.]/', '', $_GET['file']);
            // get the data id (first part before dot of filename):
            $data_id = intval(strstr($file, '.', true));
            // check permission:
            if($data_id && $permission->granted(Permission::DATA_ACCESS, $data_id, Permission::READ))
             {
              // check if file exists:
              if(file_exists($path.$file))
               {
                // read file information and file content:
                $image_info = getimagesize($path.$file);
                $image = file_get_contents($path.$file);
                // output image:
                header('Content-type: '.$image_info['mime']);
                echo $image;
                exit;
               }
             }
           }
          break;

   } /* switch */
 } /* permission */
?>
