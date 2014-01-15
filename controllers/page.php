<?php
if(!defined('IN_INDEX')) exit;

define('DEFAULT_PAGE_SUBTEMPLATE', 'default.inc.tpl');

$settings['project_thumbnail_width'] = 170;
$settings['project_thumbnail_height'] = 170;
$settings['project_thumbnail_quality'] = 85;
$settings['page_image_width'] = 380;
$settings['page_image_height'] = 285;
$settings['page_image_quality'] = 85;
$settings['project_photo_width'] = 900;
$settings['project_photo_height'] = 800;
$settings['project_photo_quality'] = 85;
$settings['project_thumbnail_width'] = 170;
$settings['project_thumbnail_height'] = 170;
$settings['project_thumbnail_quality'] = 80;
//$settings['page_teaser_auto_truncate'] = 385;

function get_uploaded_image($upload, $directory, $height, $width, $mode='inset', $quality=85)
 {
  if(isset($_FILES[$upload]) && is_uploaded_file($_FILES[$upload]['tmp_name']))
   {
    if($_FILES[$upload]['error']) $errors[] = 'error_photo_upload';
    if(empty($errors))
     {
      $upload_info = getimagesize($_FILES[$upload]['tmp_name']);
      if($upload_info[2]!=IMAGETYPE_JPEG && $upload_info[2]!=IMAGETYPE_JPEG2000 && $upload_info[2]!=IMAGETYPE_PNG && $upload_info[2]!=IMAGETYPE_GIF) $errors[] = 'error_photo_invalid_file_type';
     }
    if(empty($errors))
     {
      $filename = gmdate("YmdHis").uniqid();
      if($upload_info[2]==IMAGETYPE_PNG) $extension = 'png';
      elseif($upload_info[2]==IMAGETYPE_GIF) $extension = 'gif';
      else $extension = 'jpg';
      $image_info['file'] = $filename . '.' . $extension; 
      $imagine = new Imagine\Gd\Imagine();
      $image = $imagine->open($_FILES[$upload]['tmp_name']);       
      $image_options = array('quality' => $quality);
      $image_size = new Imagine\Image\Box($width, $height);
      if($mode=='inset') $image_mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET;
      else $image_mode = Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
      $image->thumbnail($image_size, $image_mode)->save($directory.$image_info['file'], $image_options);       
      $saved_image_info = getimagesize($directory.$image_info['file']);
      $image_info['width'] = $saved_image_info[0];
      $image_info['height'] = $saved_image_info[1];
      return $image_info;
     }
   }
  return false;
 }

if(isset($_GET['page'])) $action = 'page';

// get available templates:
if($action == 'add' || $action == 'edit' || $action == 'edit_submit')
 {
     $handle=opendir(BASE_PATH.'templates/subtemplates/pages');
     while($file = readdir($handle))
      {
       if(preg_match('/\.inc\.tpl$/i', $file))
        {
         $template_file_array[] = $file;
        }
      }
     closedir($handle);
     natcasesort($template_file_array);
     $i=0;
     foreach($template_file_array as $file)
      {
       $template_files[$i] = $file;
       #$template_files[$i]['name'] = htmlspecialchars($file);
       $i++;
      }
     if(isset($template_files))
      {
       $template->assign('available_subtemplates',$template_files);
      }
 }


  
  switch($action)
   {
    case 'page':
        if($permission->granted(Permission::PAGE_MANAGEMENT)) $min_status = 0;
        elseif($permission->granted(Permission::USER)) $min_status = 1;
        else $min_status = 2;
        
        $dbr = Database::$connection->prepare("SELECT a.id,
                                                      a.identifier,
                                                      a.title,
                                                      a.title_as_headline,
                                                      a.content,
                                                      a.location,
                                                      a.custom_date,
                                                      a.contact_name,
                                                      a.contact_email,
                                                      a.page_image,
                                                      a.page_image_width,
                                                      a.page_image_height,
                                                      a.page_image_caption,
                                                      a.status,
                                                      a.sidebar_title,
                                                      a.sidebar_text,
                                                      a.sidebar_link,
                                                      a.sidebar_linktext,
                                                      a.page_info_title,
                                                      a.subtemplate,
                                                      a.menu,
                                                      a.tv,
                                                      b.identifier AS parent_identifier,
                                                      b.title AS parent_title 
                                               FROM ".Database::$db_settings['pages_table']." AS a
                                               LEFT JOIN ".Database::$db_settings['pages_table']." AS b ON a.parent=b.id
                                               WHERE lower(a.identifier)=lower(:identifier) AND a.status>=:min_status
                                               LIMIT 1");
        $dbr->bindParam(':identifier', $_GET['page'], PDO::PARAM_STR);
        $dbr->bindParam(':min_status', $min_status, PDO::PARAM_INT);
        $dbr->execute();
        $row = $dbr->fetch();
        if(isset($row['id'])) 
         {
          $page['id'] = intval($row['id']);
          $page['title'] = htmlspecialchars($row['title']);
          $page['title_as_headline'] = $row['title_as_headline'];
          $page['custom_date'] = htmlspecialchars($row['custom_date']);
          $page['location'] = htmlspecialchars($row['location']);
          $page['contact_name'] = htmlspecialchars($row['contact_name']);
          $page['contact_email'] = htmlspecialchars($row['contact_email']);
          $page['content'] = $row['content'];
          $page['status'] = $row['status'];
          $page['sidebar_title'] = htmlspecialchars($row['sidebar_title']);
          $page['sidebar_text'] = $row['sidebar_text'];
          $page['sidebar_link'] = htmlspecialchars($row['sidebar_link']);
          $page['sidebar_linktext'] = htmlspecialchars($row['sidebar_linktext']);
          $page['page_info_title'] = htmlspecialchars($row['page_info_title']);
          $page['subtemplate'] = $row['subtemplate'];
          $page['menu'] = htmlspecialchars($row['menu']);
          
          $tv_array = explode(',', $row['tv']);
          foreach($tv_array as $tv_item)
           {
            if($tv_item)
             {
              $tv_item_parts = explode('=', $tv_item);
              $tv[trim($tv_item_parts[0])] = isset($tv_item_parts[1]) ? trim($tv_item_parts[1]) : true;
             }
           }
          if(isset($tv)) $template->assign('tv', $tv);          
          
          $page['parent_identifier'] = htmlspecialchars($row['parent_identifier']);
          $page['parent_title'] = htmlspecialchars($row['parent_title']);          
          if($row['page_image'])
           {
            $page['page_image']['file'] = htmlspecialchars($row['page_image']);
            $page['page_image']['width'] = intval($row['page_image_width']);
            $page['page_image']['height'] = intval($row['page_image_height']);
            $page['page_image']['caption'] = htmlspecialchars($row['page_image_caption']);
           }
          $template->assign('page', $page);
        
        $template->assign('current_page', $row['identifier']);
        $template->assign('subtitle', htmlspecialchars($row['title']));

        if($permission->granted(Permission::PAGE_MANAGEMENT) || $page['sidebar_title'] || $page['page_info_title']) $template->assign('sidebar', true);
        else $template->assign('sidebar', false);

        // get sub pages:
        if($permission->granted(Permission::USER)) $min_status = 1;
        else $min_status = 2;
        $dbr = Database::$connection->prepare("SELECT id,
                                                      identifier,
                                                      title
                                               FROM ".Database::$db_settings['pages_table']."
                                               WHERE status>=:min_status AND parent=:id
                                               ORDER BY sequence ASC");
        $dbr->bindParam(':id', $page['id'], PDO::PARAM_INT);
        $dbr->bindParam(':min_status', $min_status, PDO::PARAM_INT);
        $dbr->execute();
        $i=0;
        while($row = $dbr->fetch())
         {
          $sub_pages[$i]['id'] = $row['id'];
          $sub_pages[$i]['identifier'] = htmlspecialchars($row['identifier']);
          $sub_pages[$i]['title'] = htmlspecialchars($row['title']);
          ++$i;
         }
        if(isset($sub_pages)) $template->assign('sub_pages', $sub_pages);

        
        // get project data:
        if($permission->granted(Permission::DATA_MANAGEMENT))
         {
          $data_query = 'SELECT id, table_name, title, type, status
                         FROM '.Database::$db_settings['data_models_table'].'
                         WHERE status>0 AND parent_table=0 AND project=:project
                         ORDER BY sequence ASC';         
         }
        else
         {
          if($items = $permission->get_list(Permission::DATA_ACCESS))
           {
            $items_list = implode(', ', $items);
            $data_query = 'SELECT id, table_name, title, type, status
                           FROM '.Database::$db_settings['data_models_table'].'
                           WHERE status>0 AND parent_table=0 AND project=:project AND id IN ('.$items_list.')
                           ORDER BY sequence ASC';         
           
           }
         }
        
        if(isset($data_query))
         {
          $dbr = Database::$connection->prepare($data_query);
          $dbr->bindParam(':project', $page['id'], PDO::PARAM_INT);
          $dbr->execute();
          $i=0;
          while($row = $dbr->fetch())
           {
            $data[$i]['id'] = $row['id'];
            $data[$i]['title'] = htmlspecialchars($row['title']);
            $data[$i]['type'] = intval($row['type']);
            $data[$i]['status'] = intval($row['status']);
            ++$i;
           }
          if(isset($data)) $template->assign('data', $data);
         }
         
        // get photos:
    $dbr = Database::$connection->prepare("SELECT id, filename, thumbnail_width, thumbnail_height, title, description, author FROM ".$db_settings['page_photos_table']." WHERE page=:page ORDER by sequence ASC");
    $dbr->bindParam(':page', $page['id'], PDO::PARAM_INT);
    $dbr->execute();
    $i=0;
    while($row = $dbr->fetch())
     {
      $photos[$i]['id'] = $row['id'];
      $photos[$i]['filename'] = htmlspecialchars($row['filename']);
      $photos[$i]['thumbnail_width'] = intval($row['thumbnail_width']);
      $photos[$i]['thumbnail_height'] = intval($row['thumbnail_height']);
      $photos[$i]['title'] = htmlspecialchars($row['title']);
      $photos[$i]['description'] = htmlspecialchars($row['description']);
      if($row['author']) $photos[$i]['author'] = str_replace('[author]', htmlspecialchars($row['author']), $lang['page_photo_author_declaration']);
      else $photos[$i]['author'] = '';
      ++$i;
     }
    $template->assign('number_of_photos', $i); 
    if(isset($photos))
     {
      $template->assign('photos', $photos);    
      #$javascripts[] = HAMMER;
      $javascripts[] = LIGHTBOX;
     }
        
        if($permission->granted(Permission::PAGE_MANAGEMENT))
         {
          $javascripts[] = JQUERY_UI;
          $javascripts[] = JQUERY_UI_HANDLER;
          $stylesheets[] = JQUERY_UI_CSS;
         }
         
        $granted_permissions['page_management'] = $permission->granted(Permission::PAGE_MANAGEMENT) ? true : false;
        $template->assign('permission', $granted_permissions);
        
        if($page['subtemplate']) $template->assign('subtemplate', 'pages/'.$page['subtemplate']);
        else $template->assign('subtemplate', 'subtemplates/'.DEFAULT_PAGE_SUBTEMPLATE);   
         
         }
        else $http_status=404; 
        break;
    case 'add':
            // parent pages:
            $dbr = Database::$connection->prepare("SELECT id, title FROM ".$db_settings['pages_table']." ORDER BY sequence ASC");
            $dbr->execute();
            if($dbr->rowCount()>1)
             {
              $i=0;
              while($row = $dbr->fetch()) 
               {
                $parent_pages[$i]['id'] = intval($row['id']);
                $parent_pages[$i]['title'] = htmlspecialchars($row['title']);
                ++$i;
               }
              if(isset($parent_pages)) $template->assign('parent_pages', $parent_pages);
             }
         
         $page['title_as_headline'] = true;
         $page['subtemplate'] = DEFAULT_PAGE_SUBTEMPLATE;
         $page['status'] = 0;
         $template->assign('page', $page);
         $template->assign('subtitle', $lang['page_add_subtitle']);
         $template->assign('subtemplate', 'page.edit.inc.tpl');        
         $javascripts[] = JQUERY_UI;
         $javascripts[] = JQUERY_UI_HANDLER;
         $javascripts[] = WYSIWYG_EDITOR;
         $javascripts[] = STATIC_URL.'js/edit_page_wysiwyg_init.js';             
        break;
    case 'edit':
        if(isset($_GET['id']) && $permission->granted(Permission::PAGE_MANAGEMENT))
         {
          $dbr = Database::$connection->prepare("SELECT id, title, title_as_headline, identifier, teaser_supertitle, teaser_title, teaser_text, teaser_linktext, content, location, custom_date, contact_name, contact_email, teaser_image, teaser_image_width, teaser_image_height, page_image, page_image_width, page_image_height, page_image_caption, status, index, news, project, sidebar_title, sidebar_text, sidebar_link, sidebar_linktext, page_info_title, parent, subtemplate, menu, tv FROM ".$db_settings['pages_table']." WHERE id=:id LIMIT 1");
          $dbr->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
          $dbr->execute();
          $row = $dbr->fetch();
          if(isset($row['id']))
           {      	  
      	    $action = 'edit';
      	    $page['id'] = $row['id'];
      	    $page['title'] = htmlspecialchars($row['title']);
      	    $page['title_as_headline'] = $row['title_as_headline'];
      	    $page['identifier'] = htmlspecialchars($row['identifier']);
            $page['teaser_supertitle'] = htmlspecialchars($row['teaser_supertitle']);
            $page['teaser_title'] = htmlspecialchars($row['teaser_title']);
            $page['teaser_text'] = htmlspecialchars($row['teaser_text']);
            $page['teaser_linktext'] = htmlspecialchars($row['teaser_linktext']);
            $page['content'] = htmlspecialchars($row['content']);
            $page['location'] = htmlspecialchars($row['location']);
            $page['custom_date'] = htmlspecialchars($row['custom_date']);
            $page['contact_name'] = htmlspecialchars($row['contact_name']);
            $page['contact_email'] = htmlspecialchars($row['contact_email']);
            $page['teaser_image'] = $row['teaser_image'];
            $page['teaser_image_width'] = $row['teaser_image_width'];
            $page['teaser_image_height'] = $row['teaser_image_height'];
            $page['page_image'] = $row['page_image'];
            $page['page_image_width'] = $row['page_image_width'];
            $page['page_image_height'] = $row['page_image_height'];
            $page['page_image_caption'] = htmlspecialchars($row['page_image_caption']);
            $page['status'] = intval($row['status']);
            $page['index'] = $row['index'];
            $page['news'] = $row['news'];
            $page['project'] = $row['project'];
            $page['sidebar_title'] = htmlspecialchars($row['sidebar_title']);
            $page['sidebar_text'] = htmlspecialchars($row['sidebar_text']);
            $page['sidebar_link'] = htmlspecialchars($row['sidebar_link']);
            $page['sidebar_linktext'] = htmlspecialchars($row['sidebar_linktext']);
            $page['page_info_title'] = htmlspecialchars($row['page_info_title']);
            $page['parent'] = $row['parent'];
            $page['subtemplate'] = htmlspecialchars($row['subtemplate']);
            $page['menu'] = htmlspecialchars($row['menu']);
            $page['tv'] = str_replace(',',', ',htmlspecialchars($row['tv']));
            $template->assign('page', $page);
            $template->assign('subtitle', $lang['page_edit_subtitle']);
            $template->assign('subtemplate', 'page.edit.inc.tpl');        
            //$javascripts[] = JQUERY_UI;
            //$javascripts[] = JQUERY_UI_HANDLER;
            $javascripts[] = WYSIWYG_EDITOR;
            $javascripts[] = STATIC_URL.'js/edit_page_wysiwyg_init.js';          
          
            // parent pages:
            $dbr = Database::$connection->prepare("SELECT id, title FROM ".$db_settings['pages_table']." WHERE id!=:id ORDER BY sequence ASC");
            $dbr->bindParam(':id', $row['id'], PDO::PARAM_INT);
            $dbr->execute();
            if($dbr->rowCount()>1)
             {
              $i=0;
              while($row = $dbr->fetch()) 
               {
                $parent_pages[$i]['id'] = intval($row['id']);
                $parent_pages[$i]['title'] = htmlspecialchars($row['title']);
                ++$i;
               }
              if(isset($parent_pages)) $template->assign('parent_pages', $parent_pages);
             }
            
           }
          }
        break;     
    case 'edit_submit':
        if($permission->granted(Permission::PAGE_MANAGEMENT))
         {
          // get posted data:
          $title = isset($_POST['title']) ? trim($_POST['title']) : '';
          $title_as_headline = isset($_POST['title_as_headline']) ? true : false;
          $identifier = isset($_POST['identifier']) ? trim($_POST['identifier']) : '';
          $content = isset($_POST['content']) ? trim($_POST['content']) : '';
          $custom_date = isset($_POST['custom_date']) ? trim($_POST['custom_date']) : '';
          $location = isset($_POST['location']) ? trim($_POST['location']) : '';
          $contact_name = isset($_POST['contact_name']) ? trim($_POST['contact_name']) : '';
          $contact_email = isset($_POST['contact_email']) ? trim($_POST['contact_email']) : '';
          $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
          $index = isset($_POST['index']) ? true : false;
          $news = isset($_POST['news']) ? true : false;
          $project = isset($_POST['project']) ? true : false;
          $teaser_supertitle = isset($_POST['teaser_supertitle']) ? trim($_POST['teaser_supertitle']) : '';
          $teaser_title = isset($_POST['teaser_title']) ? trim($_POST['teaser_title']) : '';
          $teaser_text = isset($_POST['teaser_text']) ? trim($_POST['teaser_text']) : '';
          $teaser_linktext = isset($_POST['teaser_linktext']) ? trim($_POST['teaser_linktext']) : '';
          $delete_teaser_image = isset($_POST['delete_teaser_image']) && $_POST['delete_teaser_image'] ? true : false;
          $page_image_caption = isset($_POST['page_image_caption']) ? trim($_POST['page_image_caption']) : '';
          $delete_page_image = isset($_POST['delete_page_image']) && $_POST['delete_page_image'] ? true : false;
          $sidebar_title = isset($_POST['sidebar_title']) ? trim($_POST['sidebar_title']) : '';
          $sidebar_text = isset($_POST['sidebar_text']) ? trim($_POST['sidebar_text']) : '';
          $sidebar_link = isset($_POST['sidebar_link']) ? trim($_POST['sidebar_link']) : '';
          $sidebar_linktext = isset($_POST['sidebar_linktext']) ? trim($_POST['sidebar_linktext']) : '';
          $page_info_title = isset($_POST['page_info_title']) ? trim($_POST['page_info_title']) : '';
          $parent = isset($_POST['parent']) ? intval($_POST['parent']) : 0;
          $subtemplate = isset($_POST['subtemplate']) ? trim($_POST['subtemplate']) : '';
          $menu = isset($_POST['menu']) && $_POST['menu'] ? trim($_POST['menu']) : NULL;

          if(isset($_POST['tv']) && trim($_POST['tv']))
           {
            $tv_array = explode(',', $_POST['tv']);
            foreach($tv_array as $item)
             {
              if(trim($item)!='')
               {
                $cleared_tv_array[] = trim($item);
               }
             }
            if(isset($cleared_tv_array)) $tv = implode(',', $cleared_tv_array);
            else $tv = NULL;
           }
 
          // chacke data:
          if(empty($title)) $errors[] = 'error_no_title_empty';
          if(empty($identifier)) $errors[] = 'error_no_identifier';
          elseif(!preg_match(VALID_URL_CHARACTERS, $identifier)) $errors[] = 'error_page_identifier_invalid';
          else
           {
            // check if identifier already exists:
            if(isset($_POST['id'])) $id_exclusion_query = ' AND id != '.intval($_POST['id']);
            else $id_exclusion_query = '';;
            $dbr = Database::$connection->prepare("SELECT COUNT(*)
                                                          FROM ".Database::$db_settings['pages_table']."
                                                          WHERE LOWER(identifier)=LOWER(:identifier)".$id_exclusion_query);
            $dbr->bindParam(':identifier', $_POST['identifier'], PDO::PARAM_STR);
            $dbr->execute();
            list($count) = $dbr->fetch();
            if($count>0) $errors[] = 'error_page_identifier_exists';           
           }
          
          if(empty($errors))
           {
                  spl_autoload_register('imagineLoader');
                  $teaser_image = get_uploaded_image('teaser_image', PAGE_TEASER_IMAGES_PATH, $settings['project_thumbnail_height'], $settings['project_thumbnail_width'], 'outbound', $settings['project_thumbnail_quality']);
       	          $page_image = get_uploaded_image('page_image', PAGE_IMAGES_PATH, $settings['page_image_height'], $settings['page_image_width'], 'inset', $settings['page_image_quality']);
           
		  if(isset($_POST['id'])) // edit;
		   {
		    // get current teaser image:
		    $del_dbr = Database::$connection->prepare("SELECT teaser_image, page_image FROM ".$db_settings['pages_table']." WHERE id=:id LIMIT 1");
		    $del_dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
		    $del_dbr->execute();
		    $row = $del_dbr->fetch();
		    if($row['teaser_image']) $old_teaser_image = $row['teaser_image'];
		    if($row['page_image']) $old_page_image = $row['page_image'];
	      
		    // update record:
		    $dbr = Database::$connection->prepare("UPDATE ".$db_settings['pages_table']." SET last_editor=:last_editor, last_edited=NOW(), title=:title, title_as_headline=:title_as_headline, identifier=:identifier, teaser_supertitle=:teaser_supertitle, teaser_title=:teaser_title, teaser_text=:teaser_text, teaser_linktext=:teaser_linktext, content=:content, location=:location, custom_date=:custom_date, contact_name=:contact_name, contact_email=:contact_email, page_image_caption=:page_image_caption, status=:status, index=:index, news=:news, project=:project, sidebar_title=:sidebar_title, sidebar_text=:sidebar_text, sidebar_link=:sidebar_link, sidebar_linktext=:sidebar_linktext, page_info_title=:page_info_title, parent=:parent, subtemplate=:subtemplate, menu=:menu, tv=:tv WHERE id=:id");
		    $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
		    $dbr->bindParam(':last_editor', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
		    $dbr->bindParam(':title', $title, PDO::PARAM_STR);
		    $dbr->bindParam(':title_as_headline', $title_as_headline, PDO::PARAM_BOOL);
		    $dbr->bindParam(':identifier', $identifier, PDO::PARAM_STR);
		    $dbr->bindParam(':location', $location, PDO::PARAM_STR); 
		    $dbr->bindParam(':custom_date', $custom_date, PDO::PARAM_STR); 
		    $dbr->bindParam(':contact_name', $contact_name, PDO::PARAM_STR);
		    $dbr->bindParam(':contact_email', $contact_email, PDO::PARAM_STR);
		    $dbr->bindParam(':teaser_supertitle', $teaser_supertitle, PDO::PARAM_STR);
		    $dbr->bindParam(':teaser_title', $teaser_title, PDO::PARAM_STR);
		    $dbr->bindParam(':teaser_text', $teaser_text, PDO::PARAM_STR);
		    $dbr->bindParam(':teaser_linktext', $teaser_linktext, PDO::PARAM_STR);
		    $dbr->bindParam(':content', $content, PDO::PARAM_STR);
		    $dbr->bindParam(':status', $status, PDO::PARAM_STR);
		    $dbr->bindParam(':index', $index, PDO::PARAM_BOOL);
		    $dbr->bindParam(':news', $news, PDO::PARAM_BOOL);
		    $dbr->bindParam(':project', $project, PDO::PARAM_BOOL);
		    $dbr->bindParam(':page_image_caption', $page_image_caption, PDO::PARAM_STR);
		    $dbr->bindParam(':sidebar_title', $sidebar_title, PDO::PARAM_STR);
                    $dbr->bindParam(':sidebar_text', $sidebar_text, PDO::PARAM_STR);
                    $dbr->bindParam(':sidebar_link', $sidebar_link, PDO::PARAM_STR);
                    $dbr->bindParam(':sidebar_linktext', $sidebar_linktext, PDO::PARAM_STR);
                    $dbr->bindParam(':page_info_title', $page_info_title, PDO::PARAM_STR);
                    $dbr->bindParam(':parent', $parent, PDO::PARAM_INT);
                    $dbr->bindParam(':subtemplate', $subtemplate, PDO::PARAM_STR);
		    if($menu) $dbr->bindParam(':menu', $menu, PDO::PARAM_STR);
		    else $dbr->bindValue(':menu', NULL, PDO::PARAM_NULL);
		    if($tv) $dbr->bindParam(':tv', $tv, PDO::PARAM_STR);
		    else $dbr->bindValue(':tv', NULL, PDO::PARAM_NULL);
		    $dbr->execute();     
		    $id = intval($_POST['id']);
	 
		    /*if(isset($_POST['project_order']))
		     {
		      $project_order_array = explode(',', $_POST['project_order']);
		      foreach($project_order_array as $project_order_item)
		       {
		        $cleared_project_order_item = intval(str_replace('item_', '', $project_order_item));
		        if($cleared_project_order_item>0) $validated_project_order_array[] = $cleared_project_order_item;
		       }
		      if(isset($validated_project_order_array))
		       {
		        $dbr = Database::$connection->prepare("UPDATE ".$db_settings['pages_table']." SET sequence=:sequence WHERE id=:id");
		        $dbr->bindParam(':sequence', $sequence, PDO::PARAM_INT);
		        $dbr->bindParam(':id', $pid, PDO::PARAM_INT);
		        Database::$connection->beginTransaction();
		        $sequence = 1;
		        foreach($validated_project_order_array as $pid)
		         {
		          $dbr->execute();
		          ++$sequence;
		         }
		        Database::$connection->commit();
		       }
		     }*/ 
		   }
		  else // add
		   {
		    // determine sequence:
		    #$dbr = Database::$connection->prepare("SELECT sequence FROM ".$db_settings['pages_table']." ORDER BY sequence DESC LIMIT 1");
		    #$dbr->execute();
		    #$row = $dbr->fetch();
		    #if(isset($row['sequence'])) $new_sequence = $row['sequence'] + 1;
		    #else $new_sequence = 1;
		    // save record:
		    $dbr = Database::$connection->prepare("INSERT INTO ".Database::$db_settings['pages_table']." (creator, created, sequence, title, title_as_headline, identifier, teaser_supertitle, teaser_title, teaser_text, teaser_linktext, content, location, custom_date, contact_name, contact_email, page_image_caption, status, index, news, project, sidebar_title, sidebar_text, sidebar_link, sidebar_linktext, page_info_title, parent, subtemplate, menu, tv) VALUES (:creator, NOW(), :sequence, :title, :title_as_headline, :identifier, :teaser_supertitle, :teaser_title, :teaser_text, :teaser_linktext, :content, :location, :custom_date, :contact_name, :contact_email, :page_image_caption, :status, :index, :news, :project, :sidebar_title, :sidebar_text, :sidebar_link, :sidebar_linktext, :page_info_title, :parent, :subtemplate, :menu, :tv)");
		    
		    #$dbr->bindParam(':sequence', $new_sequence, PDO::PARAM_INT);
		    $dbr->bindParam(':creator', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
		    $dbr->bindValue(':sequence', 1, PDO::PARAM_INT);
		    $dbr->bindParam(':title', $title, PDO::PARAM_STR);
		    $dbr->bindParam(':title_as_headline', $title_as_headline, PDO::PARAM_BOOL);
		    $dbr->bindParam(':identifier', $identifier, PDO::PARAM_STR);
		    $dbr->bindParam(':location', $location, PDO::PARAM_STR); 
		    $dbr->bindParam(':custom_date', $custom_date, PDO::PARAM_STR); 
		    $dbr->bindParam(':contact_name', $contact_name, PDO::PARAM_STR);
		    $dbr->bindParam(':contact_email', $contact_email, PDO::PARAM_STR);
		    $dbr->bindParam(':teaser_supertitle', $teaser_supertitle, PDO::PARAM_STR);
		    $dbr->bindParam(':teaser_title', $teaser_title, PDO::PARAM_STR);
		    $dbr->bindParam(':teaser_text', $teaser_text, PDO::PARAM_STR);
		    $dbr->bindParam(':teaser_linktext', $teaser_linktext, PDO::PARAM_STR);
		    $dbr->bindParam(':content', $content, PDO::PARAM_STR);
		    $dbr->bindParam(':status', $status, PDO::PARAM_STR);
		    $dbr->bindParam(':index', $index, PDO::PARAM_BOOL);
		    $dbr->bindParam(':news', $news, PDO::PARAM_BOOL);
		    $dbr->bindParam(':project', $project, PDO::PARAM_BOOL);
		    $dbr->bindParam(':page_image_caption', $page_image_caption, PDO::PARAM_STR);
		    $dbr->bindParam(':sidebar_title', $sidebar_title, PDO::PARAM_STR);
                    $dbr->bindParam(':sidebar_text', $sidebar_text, PDO::PARAM_STR);
                    $dbr->bindParam(':sidebar_link', $sidebar_link, PDO::PARAM_STR);
                    $dbr->bindParam(':sidebar_linktext', $sidebar_linktext, PDO::PARAM_STR);
                    $dbr->bindParam(':page_info_title', $page_info_title, PDO::PARAM_STR);
		    $dbr->bindParam(':parent', $parent, PDO::PARAM_INT);
		    $dbr->bindParam(':subtemplate', $subtemplate, PDO::PARAM_STR);
		    if($menu) $dbr->bindParam(':menu', $menu, PDO::PARAM_STR);
		    else $dbr->bindValue(':menu', NULL, PDO::PARAM_NULL);
		    if($tv) $dbr->bindParam(':tv', $tv, PDO::PARAM_STR);
		    else $dbr->bindValue(':tv', NULL, PDO::PARAM_NULL);
		    $dbr->execute();
		    $dbr = Database::$connection->query(LAST_INSERT_ID_QUERY);
		    list($id) = $dbr->fetch();
                    
                    // reorder...
                    $dbr = Database::$connection->prepare("SELECT id FROM ".Database::$db_settings['pages_table']." WHERE id!=:id ORDER BY sequence ASC");
                    $dbr->bindParam(':id', $id, PDO::PARAM_INT);
                    $dbr->execute();
                    $i=2;
                    while($row2 = $dbr->fetch()) 
                     {
                      $dbr2 = Database::$connection->prepare("UPDATE ".Database::$db_settings['pages_table']." SET sequence=:sequence WHERE id=:id");
                      $dbr2->bindValue(':sequence', $i, PDO::PARAM_INT);
                      $dbr2->bindValue(':id', $row2['id'], PDO::PARAM_INT);
                      $dbr2->execute();
                      ++$i;
                     }          
		   }
		  
		    // update teaser image: 
		    if($teaser_image || $delete_teaser_image)
		     {
		      $dbr = Database::$connection->prepare("UPDATE ".$db_settings['pages_table']." SET teaser_image=:teaser_image, teaser_image_width=:teaser_image_width, teaser_image_height=:teaser_image_height WHERE id=:id");
		      $dbr->bindParam(':id', $id, PDO::PARAM_INT);
		      if($teaser_image)
		       {
		        $dbr->bindParam(':teaser_image', $teaser_image['file'], PDO::PARAM_STR);
		        $dbr->bindParam(':teaser_image_width', $teaser_image['width'], PDO::PARAM_INT);
		        $dbr->bindParam(':teaser_image_height', $teaser_image['height'], PDO::PARAM_INT);
		       }
		      else
		       {
		        $dbr->bindValue(':teaser_image', NULL, PDO::PARAM_NULL);
		        $dbr->bindValue(':teaser_image_width', NULL, PDO::PARAM_NULL);
		        $dbr->bindValue(':teaser_image_height', NULL, PDO::PARAM_NULL);
		       }
		      $dbr->execute(); 
		      // delete old teaser image:
		      if($old_teaser_image) @unlink(BASE_PATH.PROJECT_TEASER_IMAGES_DIR.$old_teaser_image);
		     }
		    // update project image: 
		    if($page_image || $delete_page_image)
		     {
		      $dbr = Database::$connection->prepare("UPDATE ".$db_settings['pages_table']." SET page_image=:page_image, page_image_width=:page_image_width, page_image_height=:page_image_height WHERE id=:id");
		      $dbr->bindParam(':id', $id, PDO::PARAM_INT);
		      if($page_image)
		       {
		        $dbr->bindParam(':page_image', $page_image['file'], PDO::PARAM_STR);
		        $dbr->bindParam(':page_image_width', $page_image['width'], PDO::PARAM_INT);
		        $dbr->bindParam(':page_image_height', $page_image['height'], PDO::PARAM_INT);
		       }
		      else
		       {
		        $dbr->bindValue(':page_image', NULL, PDO::PARAM_NULL);
		        $dbr->bindValue(':page_image_width', NULL, PDO::PARAM_NULL);
		        $dbr->bindValue(':page_image_height', NULL, PDO::PARAM_NULL);
		       }
		      $dbr->execute(); 
		      // delete old teaser image:
		      if($old_page_image) @unlink(BASE_PATH.page_imageS_DIR.$old_page_image);
		     }

		  
		  header('Location: '.BASE_URL.$identifier);
		  exit;
           } // if(empty($errors))
          else
           {
            foreach($_POST as $key=>$val)
             {
              $project[$key] = htmlspecialchars($val);
             }
            $template->assign('project', $project);
            $template->assign('errors', $errors);
            $template->assign('subtitle', $lang['page_edit_subtitle']);
            $template->assign('subtemplate', 'page.edit.inc.tpl');        
            $javascripts[] = JQUERY_UI;
            $javascripts[] = JQUERY_UI_HANDLER;
            $javascripts[] = WYSIWYG_EDITOR;
            $javascripts[] = STATIC_URL.'js/edit_page_wysiwyg_init.js';    
           } 
         }
        break;  
    case 'delete':
        if(isset($_GET['id']) && $permission->granted(Permission::PAGE_MANAGEMENT))
         {
          $dbr = Database::$connection->prepare("SELECT id, title, identifier FROM ".$db_settings['pages_table']." WHERE id=:id LIMIT 1");
          $dbr->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
          $dbr->execute();
          $row = $dbr->fetch();
          if(isset($row['id']))
           {
      	    $page['id'] = $row['id'];
      	    $page['identifier'] = htmlspecialchars($row['identifier']);
      	    $page['title'] = htmlspecialchars($row['title']);
            $template->assign('page', $page);
            $template->assign('subtitle', $lang['page_delete_subtitle']);
            if(isset($_GET['failure'])) $template->assign('failure', htmlspecialchars($_GET['failure']));
            $template->assign('subtemplate', 'page.delete.inc.tpl');
           }
         }
        break;               	  
    case 'delete_submit':  
     if(isset($_POST['pw']) && isset($_REQUEST['id']) && $permission->granted(Permission::PAGE_MANAGEMENT))
         {
          // check password:
          $dbr = Database::$connection->prepare("SELECT pw FROM ".Database::$db_settings['userdata_table']." WHERE id=:id LIMIT 1");
          $dbr->bindParam(':id', $_SESSION[$settings['session_prefix'].'auth']['id']);
          $dbr->execute();
          list($pw) = $dbr->fetch();
          if(check_pw($_POST['pw'], $pw))
           {
            $dbr = Database::$connection->prepare("SELECT id, teaser_image, page_image FROM ".$db_settings['pages_table']." WHERE id=:id LIMIT 1");
            $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
            $dbr->execute();
            $row = $dbr->fetch();
            if(isset($row['id']))
             {      	  
              // delete page:
              $dbr = Database::$connection->prepare("DELETE FROM ".$db_settings['pages_table']." WHERE id = :id");
              $dbr->bindValue(':id', $_REQUEST['id'], PDO::PARAM_INT);
              $dbr->execute();
              // reorder...
              $dbr = Database::$connection->prepare("SELECT id FROM ".$db_settings['pages_table']." ORDER BY sequence ASC");
              $dbr->execute();
              $i=1;
              while($reorder_row = $dbr->fetch()) 
               {
                $dbr2 = Database::$connection->prepare("UPDATE ".$db_settings['pages_table']." SET sequence=:sequence WHERE id=:id");
                $dbr2->bindValue(':sequence', $i, PDO::PARAM_INT);
                $dbr2->bindValue(':id', $reorder_row['id'], PDO::PARAM_INT);
                $dbr2->execute();
                ++$i;
               }                
              // delete page photos:
              @unlink(PAGE_TEASER_IMAGES_PATH.$row['teaser_image']);
              @unlink(PAGE_IMAGES_PATH.$row['page_image']);               
              // delete page photos:
              $dbr = Database::$connection->prepare("SELECT filename FROM ".$db_settings['page_photos_table']." WHERE page=:page");
              $dbr->bindParam(':page', $_REQUEST['id'], PDO::PARAM_INT);
              $dbr->execute();
              $i=0;
              while($row = $dbr->fetch())
               {
                @unlink(PAGE_PHOTOS_PATH.$row['filename']);
                @unlink(PAGE_THUMBNAILS_PATH.$row['filename']);
               }
             $dbr = Database::$connection->prepare("DELETE FROM ".$db_settings['page_photos_table']." WHERE page=:page");
             $dbr->bindValue(':page', $_REQUEST['id'], PDO::PARAM_INT);
             $dbr->execute();
             }
           header('Location: '.BASE_URL.'?r=page.overview');
           exit;
          }
         else
          {
           header('Location: '.BASE_URL.'?r=page.delete&id='.intval($_REQUEST['id']).'&failure=password_wrong');
           exit;
          }
         }
        break;
    case 'reorder_pages':
        if(isset($_REQUEST['item']) && is_array($_REQUEST['item']) && $permission->granted(Permission::PAGE_MANAGEMENT))
         {
          $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['pages_table']." SET sequence=:sequence WHERE id=:id");
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
    case 'add_photo':
        if(isset($_GET['page_id']) && $permission->granted(Permission::PAGE_MANAGEMENT))
         {
          
          $dbr = Database::$connection->prepare("SELECT id, identifier, title FROM ".Database::$db_settings['pages_table']." WHERE id=:id LIMIT 1");
          $dbr->bindParam(':id', $_GET['page_id'], PDO::PARAM_INT);
          $dbr->execute();
          $row = $dbr->fetch();
          if(isset($row['id']))
           {      	  
            $page['id'] = $row['id'];
            $page['identifier'] = htmlspecialchars($row['identifier']);
            $page['title'] = htmlspecialchars($row['title']);
            $template->assign('page', $page);
            $template->assign('subtitle', $lang['page_add_photo_subtitle']);
            $template->assign('subtemplate', 'page.edit_photo.inc.tpl');        
           }        
         }
        break;
    case 'add_photo_submit':
        if(isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name']) && isset($_POST['page_id']) && $permission->granted(Permission::PAGE_MANAGEMENT))
         {
          // check if project exists:
          $dbr = Database::$connection->prepare("SELECT id, identifier FROM ".Database::$db_settings['pages_table']." WHERE id=:id LIMIT 1");
          $dbr->bindParam(':id', $_POST['page_id'], PDO::PARAM_INT);
          $dbr->execute();
          $row = $dbr->fetch();
          if(isset($row['id']))
           {
            $page_id = $row['id'];
            $page_identifier = $row['identifier'];
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $author = isset($_POST['author']) ? trim($_POST['author']) : '';

            if($_FILES['photo']['error']) $errors[] = 'error_photo_upload';
            if(empty($errors))
             {
              $upload_info = getimagesize($_FILES['photo']['tmp_name']);
              if($upload_info[2]!=IMAGETYPE_JPEG && $upload_info[2]!=IMAGETYPE_JPEG2000 && $upload_info[2]!=IMAGETYPE_PNG && $upload_info[2]!=IMAGETYPE_GIF) $errors[] = 'error_photo_invalid_file_type';
             }

            if(empty($errors))
             {
              spl_autoload_register('imagineLoader');
              $filename = gmdate("YmdHis").uniqid();
              if($upload_info[2]==IMAGETYPE_PNG) $extension = 'png';
              elseif($upload_info[2]==IMAGETYPE_GIF) $extension = 'gif';
              else $extension = 'jpg';
              $photo_info['filename'] = $filename . '.' . $extension; 
              $imagine = new Imagine\Gd\Imagine();
              $photo = $imagine->open($_FILES['photo']['tmp_name']);       
              // create photo:
              $photo_options = array('quality' => $settings['project_photo_quality']);
              $photo_size = new Imagine\Image\Box($settings['project_photo_width'], $settings['project_photo_height']);
              $photo_mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET;
              $photo->thumbnail($photo_size, $photo_mode)->save(PAGE_PHOTOS_PATH.$photo_info['filename'], $photo_options);       
              //$photo->resize(new Imagine\Image\Box($settings['project_photo_width'], $settings['project_photo_height']))->save(BASE_PATH.PAGE_PHOTOS_DIR.$photo_info['filename'], $photo_options);
              // create thumbnail:
              $thumbnail_options = array('quality' => $settings['project_thumbnail_quality']);
        
              #if($upload_info[1]>$upload_info[0]) $thumbnail_size = new Imagine\Image\Box($settings['project_thumbnail_height'], $settings['project_thumbnail_width']);
              $thumbnail_size = new Imagine\Image\Box($settings['project_thumbnail_width'], $settings['project_thumbnail_height']);
        
              $thumbnail_mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET;
              $photo->thumbnail($thumbnail_size, $thumbnail_mode)->save(PAGE_THUMBNAILS_PATH.$photo_info['filename'], $thumbnail_options); 
              $saved_photo_info = getimagesize(PAGE_PHOTOS_PATH.$photo_info['filename']);
              $saved_thumbnail_info = getimagesize(PAGE_THUMBNAILS_PATH.$photo_info['filename']);
              $photo_info['photo_width'] = $saved_photo_info[0];
              $photo_info['photo_height'] = $saved_photo_info[1];
              $photo_info['thumbnail_width'] = $saved_thumbnail_info[0];
              $photo_info['thumbnail_height'] = $saved_thumbnail_info[1];
    
              // determine sequence:
              $dbr = Database::$connection->prepare("SELECT sequence FROM ".$db_settings['page_photos_table']." WHERE page=:page ORDER BY sequence DESC LIMIT 1");
              $dbr->bindParam(':page', $page_id, PDO::PARAM_INT);
              $dbr->execute();
              $row = $dbr->fetch();
              if(isset($row['sequence'])) $new_sequence = $row['sequence'] + 1;
              else $new_sequence = 1;
              // save record:
              $dbr = Database::$connection->prepare("INSERT INTO ".$db_settings['page_photos_table']." (creator, created, page, sequence, filename, thumbnail_width, thumbnail_height, photo_width, photo_height, title, description, author) VALUES (:creator, NOW(), :page,:sequence,:filename,:thumbnail_width,:thumbnail_height,:photo_width,:photo_height,:title,:description,:author)");
              $dbr->bindParam(':creator', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
              $dbr->bindParam(':page', $page_id, PDO::PARAM_INT);
              $dbr->bindParam(':sequence', $new_sequence, PDO::PARAM_INT);
              $dbr->bindParam(':filename', $photo_info['filename'], PDO::PARAM_STR);
              $dbr->bindParam(':thumbnail_width', $photo_info['thumbnail_width'], PDO::PARAM_INT);
              $dbr->bindParam(':thumbnail_height', $photo_info['thumbnail_height'], PDO::PARAM_INT);
              $dbr->bindParam(':photo_width', $photo_info['photo_width'], PDO::PARAM_INT);
              $dbr->bindParam(':photo_height', $photo_info['photo_height'], PDO::PARAM_INT);
              $dbr->bindParam(':title', $title, PDO::PARAM_STR);
              $dbr->bindParam(':description', $description, PDO::PARAM_STR);
              $dbr->bindParam(':author', $author, PDO::PARAM_STR);
              $dbr->execute();
     
              header('Location: '.BASE_URL.$page_identifier.'#photos');
              exit;
             }

           }
         }
        
        break;
    case 'edit_photo':
        if(isset($_GET['id']) && $permission->granted(Permission::PAGE_MANAGEMENT))
         {
          $dbr = Database::$connection->prepare("SELECT photo.id,
                                                        photo.page AS page_id,
                                                        photo.title,
                                                        photo.description,
                                                        photo.author,
                                                        photo.filename,
                                                        page.identifier AS page_identifier,
                                                        page.title AS page_title
                                                 FROM ".Database::$db_settings['page_photos_table']." AS photo
                                                 LEFT JOIN ".Database::$db_settings['pages_table']." AS page ON photo.page=page.id
                                                 WHERE photo.id=:id LIMIT 1");
          $dbr->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
          $dbr->execute();
          $row = $dbr->fetch();
          if(isset($row['id']))
           {      	  
            $page['id'] = intval($row['page_id']);
            $page['identifier'] = htmlspecialchars($row['page_identifier']);
            $page['title'] = htmlspecialchars($row['page_title']);
            $photo['id'] = intval($row['id']);
            $photo['title'] = htmlspecialchars($row['title']);
            $photo['description'] = htmlspecialchars($row['description']);
            $photo['author'] = htmlspecialchars($row['author']);
            $photo['filename'] = htmlspecialchars($row['filename']);
            $template->assign('page', $page);
            $template->assign('photo', $photo);
            $template->assign('subtitle', $lang['page_edit_photo_subtitle']);
            $template->assign('subtemplate', 'page.edit_photo.inc.tpl');        
           }        
         }
        break;
    case 'edit_photo_submit':
        if(isset($_POST['id']) && $permission->granted(Permission::PAGE_MANAGEMENT))
         {
          // get project id:
          $dbr = Database::$connection->prepare("SELECT photo.id,
                                                        page.identifier
                                                 FROM ".Database::$db_settings['page_photos_table']." AS photo
                                                 LEFT JOIN ".Database::$db_settings['pages_table']." AS page ON photo.page=page.id
                                                 WHERE photo.id=:id LIMIT 1");
          $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
          $dbr->execute();
          $row = $dbr->fetch();
          if(isset($row['id']))
           {
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $author = isset($_POST['author']) ? trim($_POST['author']) : '';
            // update record:
            $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['page_photos_table']." SET title=:title, description=:description, author=:author, last_editor=:last_editor, last_edited=NOW() WHERE id=:id");
            $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
            $dbr->bindParam(':last_editor', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
            $dbr->bindParam(':title', $title, PDO::PARAM_STR);
            $dbr->bindParam(':description', $description, PDO::PARAM_STR);
            $dbr->bindParam(':author', $author, PDO::PARAM_STR);
            $dbr->execute();
           
            header('Location: '.BASE_URL.$row['identifier'].'#photos');
            exit;           
           
           }
         }
        break;
    case 'reorder_photos':
        if(isset($_REQUEST['item']) && is_array($_REQUEST['item']) && $permission->granted(Permission::PAGE_MANAGEMENT))
         {
          $dbr = Database::$connection->prepare("UPDATE ".$db_settings['page_photos_table']." SET sequence=:sequence WHERE id=:id");
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
    case 'delete_photo':
        if(isset($_REQUEST['id']) && $permission->granted(Permission::PAGE_MANAGEMENT))
         {
          // check if project exists:
          $dbr = Database::$connection->prepare("SELECT photo.id,
                                                        photo.filename,
                                                        photo.page,
                                                        page.identifier
                                                 FROM ".Database::$db_settings['page_photos_table']." AS photo
                                                 LEFT JOIN ".Database::$db_settings['pages_table']." AS page ON photo.page=page.id
                                                 WHERE photo.id=:id LIMIT 1");
          $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
          $dbr->execute();
          $row = $dbr->fetch();
          if(isset($row['id']))
           {
            $dbr = Database::$connection->prepare("DELETE FROM ".Database::$db_settings['page_photos_table']." WHERE id = :id");
            $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
            $dbr->execute();
            @unlink(PAGE_PHOTOS_PATH.$row['filename']);
            @unlink(PAGE_THUMBNAILS_PATH.$row['filename']);
      
            // reorder...
            $dbr = Database::$connection->prepare("SELECT id FROM ".Database::$db_settings['page_photos_table']." WHERE page=:page ORDER BY sequence ASC");
            $dbr->bindParam(':page', $row['page'], PDO::PARAM_INT);
            $dbr->execute();
            $i=1;
            while($row2 = $dbr->fetch()) 
             {
              $dbr2 = Database::$connection->prepare("UPDATE ".Database::$db_settings['page_photos_table']." SET sequence=:sequence WHERE id=:id");
              $dbr2->bindValue(':sequence', $i, PDO::PARAM_INT);
              $dbr2->bindValue(':id', $row2['id'], PDO::PARAM_INT);
              $dbr2->execute();
              ++$i;
             }          
            header('Location: '.BASE_URL.$row['identifier'].'#photos');
            exit;
          }
         }
        break;
    case 'overview':
        if($permission->granted(Permission::PAGE_MANAGEMENT))
         {
          $dbr = Database::$connection->prepare("SELECT a.id,
                                                        a.identifier,
                                                        a.title,
                                                        a.status,
                                                        a.index,
                                                        a.news,
                                                        a.project,
                                                        b.title as parent_title
                                                 FROM ".Database::$db_settings['pages_table']." AS a
                                                 LEFT JOIN ".Database::$db_settings['pages_table']." AS b ON a.parent=b.id
                                                 ORDER BY a.sequence ASC");
          $dbr->execute();
          $i=0;
          while($row = $dbr->fetch()) 
           {
            $pages[$i]['id'] = intval($row['id']);
            $pages[$i]['identifier'] = htmlspecialchars($row['identifier']);
            $pages[$i]['title'] = htmlspecialchars($row['title']);
            $pages[$i]['status'] = intval($row['status']);
            $pages[$i]['index'] = $row['index'];
            $pages[$i]['news'] = $row['news'];
            $pages[$i]['project'] = $row['project'];
            $pages[$i]['parent_title'] = htmlspecialchars($row['parent_title']);
            #$pages[$i]['active'] = $row['active'];
            ++$i;
           }
          if(isset($pages)) $template->assign('pages', $pages);
          $javascripts[] = JQUERY_UI;
          $javascripts[] = JQUERY_UI_HANDLER;
          #$stylesheets[] = JQUERY_UI_CSS;
          $template->assign('subtitle', $lang['page_overview_subtitle']); 
          $template->assign('subtemplate', 'page.overview.inc.tpl');
         
         }
        break;
    default:
        #$dbr = Database::$connection->prepare("SELECT id, title, teaser, teaser_image, teaser_image_width, teaser_image_height FROM ".$db_settings['pages_table']." WHERE status>0 ORDER BY sequence ASC");
        if($permission->granted(Permission::PAGE_MANAGEMENT)) $status_query = '';
        elseif($permission->granted(Permission::USER)) $status_query = ' AND status > 0';
        else $status_query = ' AND status = 2';
        $dbr = Database::$connection->prepare("SELECT id, identifier, extract(epoch FROM created) as created_timestamp, custom_date, title, content, teaser_supertitle, teaser_title, teaser_text, teaser_linktext, teaser_image, teaser_image_width, teaser_image_height FROM ".Database::$db_settings['pages_table']." WHERE index IS true".$status_query." ORDER BY sequence ASC");
        $dbr->execute();
        $i=0;
        while($row = $dbr->fetch()) 
         {
          $projects[$i]['id'] = intval($row['id']);
          $projects[$i]['identifier'] = htmlspecialchars($row['identifier']);
          $projects[$i]['created'] = htmlspecialchars(strftime($lang['time_format'], $row['created_timestamp']));
          
          if($row['teaser_supertitle']) $projects[$i]['teaser_supertitle'] = htmlspecialchars($row['teaser_supertitle']);
          else $projects[$i]['teaser_supertitle'] = strftime($lang['time_format'], $row['created_timestamp']);
          if($row['teaser_title']) $projects[$i]['teaser_title'] = htmlspecialchars($row['teaser_title']);
          else $projects[$i]['teaser_title'] = htmlspecialchars($row['title']);
          if($row['teaser_text']) $projects[$i]['teaser_text'] = $row['teaser_text'];
          //else $projects[$i]['teaser_text'] = truncate($row['content'], $settings['page_teaser_auto_truncate']);
          else $projects[$i]['teaser_text'] = $row['content'];
          if($row['teaser_linktext']) $projects[$i]['teaser_linktext'] = htmlspecialchars($row['teaser_linktext']);
          //else $projects[$i]['teaser_linktext'] = $lang['page_default_teaser_linktext'];
          
          if($row['teaser_image'])
           {
            $projects[$i]['teaser_image']['file'] = $row['teaser_image'];
            $projects[$i]['teaser_image']['width'] = $row['teaser_image_width'];
            $projects[$i]['teaser_image']['height'] = $row['teaser_image_height'];
           }
          ++$i;
         }
        if(isset($projects))
         {
          $template->assign('projects', $projects);
          $template->assign('project_count', $i);
         }
        $template->assign('active', 'home'); 
        $template->assign('page_title', $settings['index_page_title']);  
        #$template->assign('subtitle', $lang['projects_subtitle']); 
        $template->assign('subtemplate', 'index.inc.tpl');
   }
?>
