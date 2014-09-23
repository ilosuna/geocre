<?php
if(!defined('IN_INDEX')) exit;

if($permission->granted(Permission::USERS_GROUPS))
 { 
  switch($action)
   {
    case 'default':
     $dbr = Database::$connection->prepare("SELECT id,
                                                   name
                                            FROM ".Database::$db_settings['group_table']."
                                            ORDER BY sequence ASC");
     $dbr->execute();
     $i=0;
     while($row = $dbr->fetch()) 
      {
       $groups[$i]['id'] = intval($row['id']);
       $groups[$i]['name'] = htmlspecialchars($row['name']);
       
       // get users of each group:
       $dbr_m = Database::$connection->prepare("SELECT users.id AS id, users.name AS name, users.email AS email, memberships.id as membership_id
                                                FROM ".Database::$db_settings['group_memberships_table']." AS memberships
                                                JOIN ".Database::$db_settings['userdata_table']." AS \"users\" ON memberships.\"user\"=\"users\".id
                                                WHERE memberships.group=:group
                                                ORDER BY users.name ASC");
       $dbr_m->bindParam(':group', $row['id'], PDO::PARAM_INT);
       $dbr_m->execute();
       $ii=0;
       while($row_members = $dbr_m->fetch())
        {
         $groups[$i]['members'][$ii]['id'] = htmlspecialchars($row_members['id']);
         $groups[$i]['members'][$ii]['name'] = htmlspecialchars($row_members['name']);
         ++$ii;
        }
       if(isset($groups[$i]['members'])) $groups[$i]['members_count'] = count($groups[$i]['members']);
       else $groups[$i]['members_count'] = 0;
       ++$i;
      }
     if(isset($groups)) $template->assign('groups', $groups);
     $javascripts[] = JQUERY_UI;
     $javascripts[] = JQUERY_UI_HANDLER;     
     $template->assign('subtitle', $lang['groups_subtitle']);
     $template->assign('subtemplate', 'groups.inc.tpl');     
     break;
    case 'properties':
     if(isset($_GET['id']))
      {
       // get group name and description
       $dbr = Database::$connection->prepare("SELECT id, name, description FROM ".Database::$db_settings['group_table']." WHERE id=:id LIMIT 1");
       $dbr->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
       $dbr->execute();     
       $row = $dbr->fetch();
       if(isset($row['id'])) 
        {
         $group['id'] = intval($row['id']);
         $group['name'] = htmlspecialchars($row['name']);
         $group['description'] = htmlspecialchars($row['description']);
         $template->assign('group', $group);
        }
       
       // get users:
      $dbr = Database::$connection->prepare("SELECT users.id AS id, users.name AS name, users.email AS email, memberships.id as membership_id
                                             FROM ".Database::$db_settings['group_memberships_table']." AS memberships
                                             LEFT JOIN ".Database::$db_settings['userdata_table']." AS \"users\" ON memberships.\"user\"=\"users\".id
                                             WHERE memberships.group=:group
                                             ORDER BY users.name ASC");
      $dbr->bindParam(':group', $group['id'], PDO::PARAM_INT);
      $dbr->execute();
      $i=0;
      while($row = $dbr->fetch())
       {
        $members[$i]['id'] = $row['id'];
        $members[$i]['membership_id'] = $row['membership_id'];
        $members[$i]['name'] = htmlspecialchars($row['name']);
        $members[$i]['email'] = htmlspecialchars($row['email']);
        ++$i;
       }
    
      if(isset($members)) $template->assign('members', $members);
       
       
       // get permissions:
       $dbr = Database::$connection->prepare("SELECT id, type, item, level FROM ".Database::$db_settings['group_permissions_table']." WHERE \"group\"=:group ORDER BY id ASC");
       $dbr->bindParam(':group', $group['id'], PDO::PARAM_INT);
       $dbr->execute();     

       $i=0;
       while($row = $dbr->fetch())
        {
         $permissions[$i]['id'] = $row['id'];
         $permissions[$i]['type'] = $row['type'];
         $permissions[$i]['item'] = $row['item'];
         $permissions[$i]['level'] = $row['level'];
         ++$i;
        }
       if(isset($permissions)) $template->assign('permissions', $permissions);  
       
       // get available data:
       $dbr = Database::$connection->query("SELECT id,
                                                   table_name,
                                                   title,
                                                   type,
                                                   parent_table,
                                                   status
                                       FROM ".$db_settings['data_models_table']."
                                       ORDER BY sequence ASC");
       $i=0;
       while($row = $dbr->fetch())
        {
         $data[$row['id']]['id'] = $row['id'];
         $data[$row['id']]['title'] = htmlspecialchars($row['title']);
         $data[$row['id']]['parent_table'] = $row['parent_table'];
         ++$i;
        }
       if(isset($data)) $template->assign('data', $data);       
       
       if(isset($_GET['success'])) $template->assign('success', htmlspecialchars($_GET['success']));
       elseif(isset($_GET['failure'])) $template->assign('failure', htmlspecialchars($_GET['failure']));
       
       $javascripts[] = JQUERY_UI;
       $javascripts[] = JQUERY_UI_HANDLER;
       
       $template->assign('subtitle', $lang['group_properties_subtitle']);
       $template->assign('subtemplate', 'group.properties.inc.tpl');          
      }
     break;    
    case 'add':
     $template->assign('subtitle', $lang['group_add_subtitle']);
     $template->assign('subtemplate', 'group.edit.inc.tpl');          
     break;
    case 'edit':
     if(isset($_GET['id']))
      {
       $dbr = Database::$connection->prepare("SELECT id, name, description FROM ".Database::$db_settings['group_table']." WHERE id=:id LIMIT 1");
       $dbr->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
       $dbr->execute();     
       $row = $dbr->fetch();
       if(isset($row['id'])) 
        {
         $group['id'] = intval($row['id']);
         $group['name'] = htmlspecialchars($row['name']);
         $group['description'] = htmlspecialchars($row['description']);
         $template->assign('group', $group);
        }
       $template->assign('subtitle', $lang['group_edit_subtitle']);
       $template->assign('subtemplate', 'group.edit.inc.tpl');          
      }
     break;
    case 'add_submit':
    case 'edit_submit':
     $name = isset($_POST['name']) ? trim($_POST['name']) : '';
     $description = isset($_POST['description']) ? trim($_POST['description']) : '';
     if(empty($name)) $errors[] = 'error_group_no_name';
     if(empty($errors))
      {
       if(isset($_POST['id'])) // edit
        {
         $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['group_table']." SET name=:name, description=:description WHERE id=:id");
         $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
         $dbr->bindParam(':name', $name, PDO::PARAM_STR);
         $dbr->bindParam(':description', $description, PDO::PARAM_STR);
         $dbr->execute();         
         header('Location: '.BASE_URL.'?r=groups.properties&id='.intval($_POST['id']));
	 exit;        
        }
       else // add
        {
	 // determine sequence:
	 $dbr = Database::$connection->prepare("SELECT sequence FROM ".Database::$db_settings['group_table']." ORDER BY sequence DESC LIMIT 1");
	 $dbr->execute();
	 $row = $dbr->fetch();
	 if(isset($row['sequence'])) $next_sequence = $row['sequence'] + 1;
	 else $next_sequence = 1;
         // insert item:
         $dbr = Database::$connection->prepare("INSERT INTO ".Database::$db_settings['group_table']." (sequence, name, description) VALUES (:sequence, :name, :description)");
         $dbr->bindParam(':sequence', $next_sequence, PDO::PARAM_INT);
         $dbr->bindParam(':name', $name, PDO::PARAM_STR);
         $dbr->bindParam(':description', $description, PDO::PARAM_STR);
         $dbr->execute();         
         $dbr = Database::$connection->query("SELECT LASTVAL()");
         list($last_insert_id) = $dbr->fetch();
         header('Location: '.BASE_URL.'?r=groups.properties&id='.$last_insert_id);
	 exit;
        }
      }
     else
      {
       if(isset($_POST['id'])) $group['id'] = intval($_POST['id']);
       $group['name'] = htmlspecialchars($_POST['name']);
       $group['description'] = htmlspecialchars($_POST['description']);
       $template->assign('group', $group);
       $template->assign('errors', $errors);
       $template->assign('subtitle', $lang['group_add_subtitle']);
       $template->assign('subtemplate', 'group_edit.inc.tpl');      
      } 
     break;
    
    case 'delete':
        if(isset($_REQUEST['id']))
         {
          if(empty($_REQUEST['confirmed']))
           {
            $template->assign('id', intval($_REQUEST['id'])); 
            $template->assign('subtitle', $lang['delete_group_message']);
            $template->assign('subtemplate', 'delete_confirm.inc.tpl');
           }
          else
           {
            $dbr = Database::$connection->prepare("DELETE FROM ".Database::$db_settings['group_table']." WHERE id=:id");
            $dbr->bindValue(':id', $_REQUEST['id'], PDO::PARAM_INT);
            $dbr->execute();
            // reorder...
            $dbr = Database::$connection->prepare("SELECT id FROM ".Database::$db_settings['group_table']." ORDER BY sequence ASC");
            $dbr->execute();
            $i=1;
            while($row = $dbr->fetch()) 
            {
             $dbr2 = Database::$connection->prepare("UPDATE ".Database::$db_settings['group_table']." SET sequence=:sequence WHERE id=:id");
             $dbr2->bindValue(':sequence', $i, PDO::PARAM_INT);
             $dbr2->bindValue(':id', $row['id'], PDO::PARAM_INT);
             $dbr2->execute();
             ++$i;
            }          
           header('Location: '.BASE_URL.'?r=groups');
           exit;
          }
         }
     break;   
    
    case 'add_permission';
     if(isset($_POST['group']))
      {
       $group = intval($_POST['group']);
       $type = isset($_POST['type']) ? intval($_POST['type']) : 0;
       $item = isset($_POST['item']) ? intval($_POST['item']) : 0;
       $level = isset($_POST['level']) ? intval($_POST['level']) : 0;
       if($type!=Permission::DATA_ACCESS) // only DATA_ACCESS type has items and levels
        {
         $item = 0; 
         $level = 0;
        }
       $dbr = Database::$connection->prepare("INSERT INTO ".Database::$db_settings['group_permissions_table']." (\"group\", type, item, level) VALUES (:group, :type, :item, :level)");
       $dbr->bindParam(':group', $group, PDO::PARAM_INT);
       $dbr->bindParam(':type', $type, PDO::PARAM_INT);
       $dbr->bindParam(':item', $item, PDO::PARAM_INT);
       $dbr->bindParam(':level', $level, PDO::PARAM_INT);
       $dbr->execute();         
       header('Location: '.BASE_URL.'?r=groups.properties&id='.$group.'&success=permission_added');
       exit;
      }
     break;

    case 'delete_permission':
        if(isset($_REQUEST['id']))
         {
          if(empty($_REQUEST['confirmed']))
           {
            $template->assign('id', intval($_REQUEST['id'])); 
            $template->assign('subtitle', $lang['delete_permission_message']);
            $template->assign('subtemplate', 'delete_confirm.inc.tpl');
           }
          else
           {
            // get group:
            $dbr = Database::$connection->prepare("SELECT \"group\" FROM ".Database::$db_settings['group_permissions_table']." WHERE id=:id LIMIT 1");
            $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
            $dbr->execute();     
            $row = $dbr->fetch();
            if(isset($row['group']))
             { 
              // delete permission:
              $dbr = Database::$connection->prepare("DELETE FROM ".Database::$db_settings['group_permissions_table']." WHERE id=:id");
              $dbr->bindValue(':id', $_REQUEST['id'], PDO::PARAM_INT);
              $dbr->execute();
              header('Location: '.BASE_URL.'?r=groups.properties&id='.$row['group'].'&success=permission_deleted');
             }
            exit;
          }
         }
     break;   


    case 'add_membership':
     if(isset($_POST['user']) && isset($_POST['group']))
      {
       $dbr = Database::$connection->prepare("SELECT id FROM " . Database::$db_settings['userdata_table'] . " WHERE LOWER(name)=LOWER(:name) LIMIT 1");
       $dbr->bindValue(':name', $_POST['user'], PDO::PARAM_STR);
       $dbr->execute();
       $row = $dbr->fetch();
       if(isset($row['id']))
        {
         // check if user is already in group:
         $dbr = Database::$connection->prepare("SELECT COUNT(*) FROM " . Database::$db_settings['group_memberships_table'] . " WHERE \"group\"=:group AND \"user\"=:user");
         $dbr->bindValue(':group', $_POST['group'], PDO::PARAM_INT);
         $dbr->bindValue(':user', $row['id'], PDO::PARAM_INT);
         $dbr->execute();
         list($count) = $dbr->fetch();
         // insert user if not yet in group:
         if($count==0)
          {
           $dbr = Database::$connection->prepare("INSERT INTO ".Database::$db_settings['group_memberships_table']." (\"user\", \"group\") VALUES (:user, :group)");
           $dbr->bindParam(':group', $_POST['group'], PDO::PARAM_INT);
           $dbr->bindParam(':user', $row['id'], PDO::PARAM_INT);
           $dbr->execute();         
           header('Location: '.BASE_URL.'?r=groups.properties&id='.intval($_POST['group']).'&success=membership_added#members');
          }
         else
          {
           header('Location: '.BASE_URL.'?r=groups.properties&id='.intval($_POST['group']).'&failure=membership_exists#members');
          }
        }
       else
        {
         header('Location: '.BASE_URL.'?r=groups.properties&id='.intval($_POST['group']).'&failure=user_not_found#members');
        } 
       exit;
      }
     break;  

    case 'delete_membership':
        if(isset($_REQUEST['id']))
         {
          if(empty($_REQUEST['confirmed']))
           {
            $template->assign('id', intval($_REQUEST['id'])); 
            $template->assign('subtitle', $lang['delete_user_from_group_message']);
            $template->assign('subtemplate', 'delete_confirm.inc.tpl');
           }
          else
           {
            // get user and group by membership:
            $dbr = Database::$connection->prepare("SELECT id, \"user\", \"group\" FROM ".Database::$db_settings['group_memberships_table']." WHERE id=:id LIMIT 1");
            $dbr->bindValue(':id', $_REQUEST['id'], PDO::PARAM_INT);
            $dbr->execute();
            $row = $dbr->fetch();
            if(isset($row['id']))
             { 
              /* we could just delete the item by its id but it could have
                 changed in the meantime (when the usres's profile was edited),
                 so we play it safe and delete the item by the group and user id: */
              $dbr = Database::$connection->prepare("DELETE FROM ".Database::$db_settings['group_memberships_table']." WHERE \"group\"=:group AND \"user\"=:user");
              $dbr->bindValue(':group', $row['group'], PDO::PARAM_INT);
              $dbr->bindValue(':user', $row['user'], PDO::PARAM_INT);
              $dbr->execute();
              header('Location: '.BASE_URL.'?r=groups.properties&id='.$row['group'].'&success=membership_deleted#members');
              exit;
             }
          }
         }
     break;  

    
    case 'reorder_groups':
        if(isset($_REQUEST['item']) && is_array($_REQUEST['item']))
         {
          $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['group_table']." SET sequence=:sequence WHERE id=:id");
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
