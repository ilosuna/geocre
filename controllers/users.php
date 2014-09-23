<?php
if(!defined('IN_INDEX')) exit;

if($permission->granted(Permission::USERS_GROUPS))
 {
  $available_languages = get_languages();
  if($available_languages && count($available_languages)>1)
   {
    $lang['user_default_language_label'] = str_replace('[language]', get_language_name($settings['language']), $lang['user_default_language_label']);
    $template->assign('available_languages', $available_languages);
   }
  // get available time zones:
  $available_time_zones = file(BASE_PATH.'config/time_zones.conf', FILE_IGNORE_NEW_LINES);
  $lang['user_default_time_zone_label'] = str_replace('[time_zone]', $settings['time_zone'], $lang['user_default_time_zone_label']);
  $template->assign('available_time_zones', $available_time_zones);

  // get available groups:
  $dbr = Database::$connection->prepare("SELECT id, name FROM ".Database::$db_settings['group_table']." ORDER BY sequence ASC");
  $dbr->execute();
  $i=0;
  while($row = $dbr->fetch()) 
   {
    $available_groups[$i]['id'] = intval($row['id']);
    $available_groups[$i]['name'] = htmlspecialchars($row['name']);
    ++$i;
   }
  if(isset($available_groups)) $template->assign('available_groups', $available_groups);         
  
  switch($action)
   {
    case 'default':
     // filter:
     $filter = isset($_GET['filter']) ? $_GET['filter'] : false;
     $template->assign('filter', htmlspecialchars($filter));     
     if($filter) $where_clause = ' WHERE LOWER(name) LIKE LOWER(:name)';
     else $where_clause = '';

     // count users:
     $cr = Database::$connection->prepare('SELECT COUNT(*) FROM '.Database::$db_settings['userdata_table'].$where_clause);
     if($filter) $cr->bindValue(':name', $filter.'%', PDO::PARAM_STR);
     $cr->execute();
     list($total_items) = $cr->fetch();
     // calculate pages:
     $items_per_page = $settings['items_per_page'];
     $total_pages = ceil($total_items / $items_per_page);
     // get current page:
     $p = isset($_GET['p']) ? intval($_GET['p']) : 1;
     if($p<1) $p=1;
     if($total_pages>0 && $p>$total_pages) $p = $total_pages;
     $template->assign('p', $p);
     // offset and order:
     $offset = ($p-1) * $items_per_page;
     $order = isset($_GET['order']) ? trim($_GET['order']) : 'name';
     if($order!='name' && $order!='email' && $order!='groups' && $order!='registered' && $order!='logins' && $order!='last_login') $order='name';
     $desc = isset($_GET['desc']) && $_GET['desc'] ? 1 : 0;
     $template->assign('order', htmlspecialchars($order));
     $template->assign('desc', $desc);
     $descasc = $desc ? 'DESC' : 'ASC';
     $template->assign('total_users', $total_items);
     $template->assign('pagination', pagination($total_pages, $p));

     $dbr = Database::$connection->prepare('SELECT users.id,
                                                   email,
                                                   name,
                                                   extract(epoch FROM registered) AS registered,
                                                   logins, extract(epoch FROM last_login) AS last_login,
                                                   COUNT(memberships.user) AS groups
                                            FROM '.Database::$db_settings['userdata_table'].' AS users
                                            LEFT JOIN '.Database::$db_settings['group_memberships_table'].' AS memberships ON "users".id=memberships."user"
                                            '.$where_clause.'
                                            GROUP BY users.id
                                            ORDER BY "'.$order.'" '.$descasc.'
                                            LIMIT '.$items_per_page.'
                                            OFFSET '.$offset);
     if($filter) $dbr->bindValue(':name', $filter.'%', PDO::PARAM_STR);
     $dbr->execute();
     $i=0;
     foreach($dbr as $row) 
      {
       $users[$i]['id'] = intval($row['id']);
       $users[$i]['email'] = htmlspecialchars($row['email']);
       $users[$i]['name'] = htmlspecialchars($row['name']);
       $users[$i]['registered'] = strftime($lang['time_format'],$row['registered']);
       $users[$i]['logins'] = intval($row['logins']);
       $users[$i]['groups'] = intval($row['groups']);
       if($row['logins']>0) $users[$i]['last_login'] = strftime($lang['time_format'],$row['last_login']);
       ++$i;
      }
     if(isset($users)) $template->assign('users', $users);  
     
     if(isset($_GET['userdata_saved'])) $template->assign('userdata_saved', true);
     
     $template->assign('subtitle', $lang['users_subtitle']);
     $template->assign('subtemplate', 'users.inc.tpl');
     break;

    case 'add':
     $user['type'] = 0;
     $user['groups'][] = $settings['default_group'];
     $template->assign('user', $user);
     $template->assign('subtitle',$lang['add_user_subtitle']);
     $template->assign('subtemplate','users.edit.inc.tpl');     

    case 'details':
     if(isset($_GET['id']))
      {
       $dbr = Database::$connection->prepare("SELECT id, name, real_name, email, language, time_zone, extract(epoch FROM registered) AS registered, logins, extract(epoch FROM last_login) AS last_login FROM ".Database::$db_settings['userdata_table']." WHERE id=:id LIMIT 1");
       $dbr->bindValue(':id', $_GET['id']);
       $dbr->execute();
       $row = $dbr->fetch();
       if(isset($row['id']))
        {
         $user['id'] = intval($row['id']);
         $user['email'] = htmlspecialchars($row['email']);
         $user['name'] = htmlspecialchars($row['name']);
         $user['real_name'] = htmlspecialchars($row['real_name']);
         $user['language'] = htmlspecialchars($row['language']);
         $user['time_zone'] = htmlspecialchars($row['time_zone']);
         $user['registered'] = strftime($lang['time_format'], $row['registered']);
         $user['registered_ago'] = how_long_ago($row['registered']);
         $user['logins'] = intval($row['logins']);
         if($row['logins']>0)
          {
           $user['last_login'] = strftime($lang['time_format'], $row['last_login']);
           $user['last_login_ago'] = how_long_ago($row['last_login']);
          }        
         $template->assign('user', $user);
         
         // get group memberships:
         // get users of each group:
         $dbr = Database::$connection->prepare("SELECT groups.id as id, groups.name AS group
                                                FROM ".Database::$db_settings['group_memberships_table']." AS memberships
                                                LEFT JOIN ".Database::$db_settings['group_table']." AS \"groups\" ON memberships.\"group\"=\"groups\".id
                                                WHERE memberships.\"user\"=:user
                                                ORDER BY groups.sequence ASC");
         #$dbr = Database::$connection->prepare("SELECT id, \"group\" FROM ".Database::$db_settings['group_memberships_table']." WHERE \"user\"=:user");
         $dbr->bindParam(':user', $user['id'], PDO::PARAM_INT);
         $dbr->execute();
         $i=0;
         while($row = $dbr->fetch()) 
          {
           $groups[$i]['id'] = $row['id'];
           $groups[$i]['name'] = $row['group'];
           ++$i;
          }
         if(isset($groups)) $template->assign('groups', $groups);
         
         $template->assign('subtitle', $lang['users_details_subtitle']);
         $template->assign('subtemplate', 'users.details.inc.tpl'); 
        }
      }
     break; 

    case 'edit':
     if(isset($_GET['id']))
      {
       $dbr = Database::$connection->prepare("SELECT id, type, name, real_name, email, language, time_zone FROM ".Database::$db_settings['userdata_table']." WHERE id=:id LIMIT 1");
       $dbr->bindValue(':id', $_GET['id']);
       $dbr->execute();
       $row = $dbr->fetch();
       if(isset($row['id']))
        {
         $user['id'] = intval($row['id']);
         $user['type'] = intval($row['type']);
         $user['email'] = htmlspecialchars($row['email']);
         $user['name'] = htmlspecialchars($row['name']);
         $user['real_name'] = htmlspecialchars($row['real_name']);
         $user['language'] = htmlspecialchars($row['language']);
         $user['time_zone'] = htmlspecialchars($row['time_zone']);
         
         // get group memberships:
         $dbr = Database::$connection->prepare("SELECT \"group\" FROM ".Database::$db_settings['group_memberships_table']." WHERE \"user\"=:user");
         $dbr->bindParam(':user', $user['id'], PDO::PARAM_INT);
         $dbr->execute();
         $i=0;
         while($row = $dbr->fetch()) 
          {
           $user['groups'][] = $row['group'];
          }
         $template->assign('user', $user);
         
         $template->assign('subtitle', $lang['users_edit_subtitle']);
         $template->assign('subtemplate', 'users.edit.inc.tpl');      
        }
      }
     break; 

    case 'edit_submit':
    case 'add_submit':
     // import posted data:
     $name = isset($_POST['name']) ? trim($_POST['name']) : '';
     $real_name = isset($_POST['real_name']) ? trim($_POST['real_name']) : '';
     $email = isset($_POST['email']) ? trim($_POST['email']) : '';
     $language = isset($_POST['language']) ? trim($_POST['language']) : '';
     $time_zone = isset($_POST['time_zone']) ? trim($_POST['time_zone']) : '';
     $type = isset($_POST['type']) ? intval($_POST['type']) : 0;
     if($type>2) $type=0;
     $pw = isset($_POST['pw']) ? trim($_POST['pw']) : '';
     $pw_repeat = isset($_POST['pw_repeat']) ? trim($_POST['pw_repeat']) : '';
     $notify_user = isset($_POST['notify_user']) ? true : false;
     $groups = isset($_POST['groups']) ? $_POST['groups'] : '';
 
     // check if a user name was entered:
     if(empty($name))
      {
       $errors[] = 'error_user_no_name';
      }
     // if user name entered, check if available:
     else
      {
       if(isset($_POST['id']))
        {
         $dbr = Database::$connection->prepare("SELECT COUNT(*) FROM " . Database::$db_settings['userdata_table'] . " WHERE LOWER(name)=LOWER(:name) AND id!=:id");
         $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
        }
       else
        {
         $dbr = Database::$connection->prepare("SELECT COUNT(*) FROM " . Database::$db_settings['userdata_table'] . " WHERE LOWER(name)=LOWER(:name)");
        }
       $dbr->bindParam(':name', $name, PDO::PARAM_STR);
       $dbr->execute();
       list($count) = $dbr->fetch();
       if($count > 0) $errors[] = 'error_user_name_unavailable'; 
      }
     
     // check if e-mail was eneterd:
     if(empty($email)) $errors[] = 'error_user_no_email';
     // check if e-mail is valid:
     elseif(!is_valid_email($email)) $errors[] = 'error_user_invalid_email';
     // check if e-mail doesn't exists yet:
     else
      {
       if(isset($_POST['id']))
        {
         $dbr = Database::$connection->prepare("SELECT COUNT(*) FROM " . Database::$db_settings['userdata_table'] . " WHERE LOWER(email)=LOWER(:email) AND id!=:id");
         $dbr->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        }
       else
        {
         $dbr = Database::$connection->prepare("SELECT COUNT(*) FROM " . Database::$db_settings['userdata_table'] . " WHERE LOWER(email)=LOWER(:email)");        
        } 
       $dbr->bindValue(':email', $email, PDO::PARAM_STR);
       $dbr->execute();
       list($count) = $dbr->fetch();
       if($count>0) $errors[] = 'error_email_already_exists'; 
      }

     // check language:
     if($language)
      {
       foreach($available_languages as $language_item) $language_identifiers[] = $language_item['identifier'];
       if(!in_array($language, $language_identifiers)) $errors[] = 'error_language_not_available';
      }
      
     // check time zone:
     if($time_zone && !in_array($time_zone, $available_time_zones)) $errors[] = 'error_time_zone_not_available';

     // check password length:
     if($pw && mb_strlen($pw)<$settings['min_pw_length']) $errors[] = 'error_pw_too_short';
     
     // check if passwords match:
     if($pw && $pw!=$pw_repeat) $errors[] = 'error_pw_match';
     
     // if a new user is added and no password was entered, check if user notification is enabled:
     if(empty($_POST['id']) && empty($pw) && empty($notify_user)) $errors[] = 'error_user_notification';

     // insert/update user if no errors:
     if(empty($errors))
      {
       if(isset($_POST['id'])) // edit
        {
         $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['userdata_table']." SET name=:name, real_name=:real_name, email=:email, language=:language, time_zone=:time_zone, type=:type WHERE id=:id;");
         $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
         $dbr->bindParam(':name', $name, PDO::PARAM_STR);
         $dbr->bindParam(':real_name', $real_name, PDO::PARAM_STR);
         $dbr->bindParam(':email', $email, PDO::PARAM_STR);
         if($language) $dbr->bindParam(':language', $language, PDO::PARAM_STR);
         else $dbr->bindValue(':language', NULL, PDO::PARAM_NULL);
         if($time_zone) $dbr->bindParam(':time_zone', $time_zone, PDO::PARAM_STR);
         else $dbr->bindValue(':time_zone', NULL, PDO::PARAM_NULL);         
         $dbr->bindParam(':type', $type, PDO::PARAM_INT);
         $dbr->execute();
         if($pw)
          {
           $pw_hash = generate_pw_hash($pw);
           $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['userdata_table']." SET pw=:pw WHERE id=:id;");
           $dbr->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
           $dbr->bindParam(':pw', $pw_hash, PDO::PARAM_STR);
           $dbr->execute();
          }
        }
       else // add
        {
         // generate password hash:
         if(empty($pw)) $pw = random_string();
         $pw_hash = generate_pw_hash($pw);
         $dbr = Database::$connection->prepare("INSERT INTO ".Database::$db_settings['userdata_table']." (name, real_name, email, language, time_zone, type, pw, registered) VALUES (:name, :real_name, :email, :language, :time_zone, :type, :pw, NOW());");
         $dbr->bindParam(':name', $name, PDO::PARAM_STR);
         $dbr->bindParam(':real_name', $real_name, PDO::PARAM_STR);
         $dbr->bindParam(':email', $email, PDO::PARAM_STR);
         if($language) $dbr->bindParam(':language', $language, PDO::PARAM_STR);
         else $dbr->bindValue(':language', NULL, PDO::PARAM_NULL);
         if($time_zone) $dbr->bindParam(':time_zone', $time_zone, PDO::PARAM_STR);
         else $dbr->bindValue(':time_zone', NULL, PDO::PARAM_NULL);         
         $dbr->bindParam(':type', $type, PDO::PARAM_INT);
         $dbr->bindParam(':pw', $pw_hash, PDO::PARAM_STR);
         $dbr->execute();
         $dbr = Database::$connection->query("SELECT LASTVAL()");
         list($user_id) = $dbr->fetch();
         
         if($notify_user)
          {
           require(BASE_PATH.'lib/phpmailer/class.phpmailer.php');
           $mail = new PHPMailer();
           $mail->CharSet = $lang['charset'];
           $mail->IsSMTP();
           $mail->Host       = $settings['email_smtp_host'];
           $mail->SMTPAuth   = true;
           $mail->Port       = $settings['email_smtp_port'];
           $mail->Username   = $settings['email_smtp_username'];
           $mail->Password   = $settings['email_smtp_password'];
           $mail->SetFrom($settings['email_address'], $settings['website_title']);
           $mail->Subject = $lang['add_user_notification_mail_subject'];
           $mail->Body = str_replace('[website_address]', BASE_URL, str_replace('[website_title]', $settings['website_title'], str_replace('[email]', $email, str_replace('[password]', $pw, str_replace('[user_name]', $name, $lang['add_user_notification_mail_text'])))));
           $mail->AddAddress($email);
           $mail->Send();          
          }
       }
      
      // update group memberships:
      if(isset($_POST['id']))
       {
        $user_id = intval($_POST['id']);
        #showme($user_id);
        $dbr = Database::$connection->prepare("DELETE FROM ".Database::$db_settings['group_memberships_table']." WHERE \"user\"=:user");
        $dbr->bindParam(':user', $user_id, PDO::PARAM_INT);
        $dbr->execute();
       }
      if(is_array($groups))
       {
        $dbr = Database::$connection->prepare("INSERT INTO ".Database::$db_settings['group_memberships_table']." (\"user\", \"group\") VALUES (:user, :group)");
        $dbr->bindParam(':user', $user_id, PDO::PARAM_INT);
        foreach($groups as $group)
         {
          $dbr->bindParam(':group', $group, PDO::PARAM_INT);
          $dbr->execute();
         }
       }
      header('Location: '.BASE_URL.'?r=users&userdata_saved=true');
      exit;
     
     }
    else // errors
     {
      if(isset($groups)) $template->assign('groups', $groups);         
      
      if(isset($_POST['id'])) $user['id'] = intval($_POST['id']);
      
      $user['name'] = htmlspecialchars($name);
      $user['real_name'] = htmlspecialchars($real_name);
      $user['email'] = htmlspecialchars($email);
      $user['language'] = htmlspecialchars($language);
      $user['time_zone'] = htmlspecialchars($time_zone);      
      #if(isset($groups)) $template->assign('groups', $groups);
      $user['groups'] = $groups;
      $template->assign('user', $user); 
      $template->assign('errors', $errors); 
      $template->assign('subtitle',$lang['users_edit_subtitle']);
      $template->assign('subtemplate','users.edit.inc.tpl'); 
     
     }

     break; 
    case 'delete':
     if(isset($_REQUEST['id']))
      {
       if(empty($_REQUEST['confirmed']))
        {
         $template->assign('id', intval($_REQUEST['id'])); 
         $template->assign('subtitle', $lang['delete_user_subtitle']);
         $template->assign('subtemplate', 'delete_confirm.inc.tpl');        
        }
       else
        {
         $dbr = Database::$connection->prepare("DELETE FROM ".Database::$db_settings['userdata_table']." WHERE id = :id");
         $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
         $dbr->execute();
         header('Location: '.BASE_URL.'?r=users');
         exit;
        }      
      }
     break;        

   }
     
  

 }
else
 {
  header('Location: ./');
  exit;
 }
?>
