<?php
if($permission->granted(Permission::USER))
 {
  // get available languages:
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
  $template->assign('active', 'profile');
  
  switch($action)
   {   
    case 'default':
    $dbr = Database::$connection->prepare("SELECT id, name, real_name, email, language, time_zone FROM ".Database::$db_settings['userdata_table']." WHERE id=:id LIMIT 1");
    $dbr->bindValue(':id', $_SESSION[$settings['session_prefix'].'auth']['id']);
    $dbr->execute();
    $row = $dbr->fetch();
    if(isset($row['id']))
     {
      $profile['id'] = $row['id'];
      $profile['name'] = htmlspecialchars($row['name']);
      $profile['real_name'] = htmlspecialchars($row['real_name']);
      $profile['email'] = htmlspecialchars($row['email']);
      $profile['language'] = htmlspecialchars(get_language_name($row['language']));
      if($row['time_zone']) $profile['time_zone'] = htmlspecialchars($row['time_zone']);
      else $profile['time_zone'] = $settings['time_zone'];
      $template->assign('profile', $profile);

      // groups:
      $dbr = Database::$connection->prepare("SELECT groups.name AS group_name
                                           FROM ".Database::$db_settings['group_memberships_table']." AS memberships
                                           LEFT JOIN ".Database::$db_settings['group_table']." AS \"groups\" ON memberships.\"group\"=\"groups\".id
                                           WHERE memberships.user=:user
                                           ORDER BY groups.sequence ASC");
      $dbr->bindParam(':user', $profile['id'], PDO::PARAM_INT);
      $dbr->execute();
      while($row = $dbr->fetch())
       {
        $groups[] = $row['group_name'];
       }
      if(isset($groups)) $template->assign('groups', $groups);
      
      if(isset($_GET['success'])) $template->assign('success', $_GET['success']);
      $template->assign('subtitle', $lang['profile_subtitle']);
      $template->assign('subtemplate', 'profile.inc.tpl');       
            
     }       
   
    break;

    case 'edit':
     // get profile data:
     $dbr = Database::$connection->prepare("SELECT id, name, real_name, email, language, time_zone FROM ".Database::$db_settings['userdata_table']." WHERE id=:id LIMIT 1");
     $dbr->bindValue(':id', $_SESSION[$settings['session_prefix'].'auth']['id']);
     $dbr->execute();
     $row = $dbr->fetch();
     if(isset($row['id']))
      {
       $profile['name'] = htmlspecialchars($row['name']);
       $profile['real_name'] = htmlspecialchars($row['real_name']);
       $profile['email'] = htmlspecialchars($row['email']);
       $profile['language'] = htmlspecialchars($row['language']);
       $profile['time_zone'] = htmlspecialchars($row['time_zone']);
       $template->assign('profile', $profile);
      }       
     $template->assign('subtitle', $lang['profile_edit_subtitle']);
     $template->assign('subtemplate', 'profile.edit.inc.tpl');     

     break;
    
    case 'edit_submit':
     // import posted data:
     $real_name = isset($_POST['real_name']) ? trim($_POST['real_name']) : '';
     $email = isset($_POST['email']) ? trim($_POST['email']) : '';
     $language = isset($_POST['language']) ? trim($_POST['language']) : '';
     $time_zone = isset($_POST['time_zone']) ? trim($_POST['time_zone']) : '';
     $old_pw = isset($_POST['old_pw']) ? trim($_POST['old_pw']) : '';
     $new_pw = isset($_POST['new_pw']) ? trim($_POST['new_pw']) : '';
     $new_pw_repeat = isset($_POST['new_pw_repeat']) ? trim($_POST['new_pw_repeat']) : '';
 
     // check if e-mail was eneterd:
     if(empty($email)) $errors[] = 'error_user_no_email';
     // check if e-mail is valid:
     elseif(!is_valid_email($email)) $errors[] = 'error_user_invalid_email';
     // check if e-mail doesn't exists yet:
     else
      {
       $dbr = Database::$connection->prepare("SELECT COUNT(*) FROM " . Database::$db_settings['userdata_table'] . " WHERE LOWER(email)=LOWER(:email) AND id!=:id");
       $dbr->bindValue(':id', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
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
     
     // check if passwords match:
     if($old_pw)
      {
       // check old password:
       $dbr = Database::$connection->prepare("SELECT pw FROM ".Database::$db_settings['userdata_table']." WHERE id=:id LIMIT 1");
       $dbr->bindValue(':id', $_SESSION[$settings['session_prefix'].'auth']['id']);
       $dbr->execute();
       $row = $dbr->fetch();
       if(!check_pw($old_pw, $row['pw'])) $errors[] = 'error_old_pw';
      }
     elseif($new_pw && empty($old_password)) $errors[] = 'error_old_pw';
     if($new_pw && mb_strlen($new_pw)<$settings['min_pw_length']) $errors[] = 'error_pw_too_short';
     if($new_pw && $new_pw!=$new_pw_repeat) $errors[] = 'error_pw_match';
       
     // insert profile if no errors:
     if(empty($errors))
      {
         $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['userdata_table']." SET real_name=:real_name, email=:email, language=:language, time_zone=:time_zone WHERE id=:id;");
         $dbr->bindParam(':id', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
         $dbr->bindParam(':real_name', $real_name, PDO::PARAM_STR);
         $dbr->bindParam(':email', $email, PDO::PARAM_STR);
         if($language) $dbr->bindParam(':language', $language, PDO::PARAM_STR);
         else $dbr->bindValue(':language', NULL, PDO::PARAM_NULL);
         if($time_zone) $dbr->bindParam(':time_zone', $time_zone, PDO::PARAM_STR);
         else $dbr->bindValue(':time_zone', NULL, PDO::PARAM_NULL);
         $dbr->execute();
         if($new_pw)
          {
           $pw_hash = generate_pw_hash($new_pw);
           $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['userdata_table']." SET pw=:pw WHERE id=:id;");
           $dbr->bindParam(':id', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
           $dbr->bindParam(':pw', $pw_hash, PDO::PARAM_STR);
           $dbr->execute();
          }
         if($language) $_SESSION[$settings['session_prefix'].'language'] = $language;
         else unset($_SESSION[$settings['session_prefix'].'language']);
         if($time_zone) $_SESSION[$settings['session_prefix'].'time_zone'] = $time_zone;
         else unset($_SESSION[$settings['session_prefix'].'time_zone']);
         header('Location: '.BASE_URL.'?r=profile&success=profile_saved');
         exit;       
      }  
     else // errors
      {
       $template->assign('errors', $errors);
       $profile['name'] = htmlspecialchars($_SESSION[$settings['session_prefix'].'auth']['name']);
       $profile['email'] = htmlspecialchars($email);
       $profile['real_name'] = htmlspecialchars($real_name);
       $profile['language'] = htmlspecialchars($language);
       $profile['time_zone'] = htmlspecialchars($time_zone);
       $template->assign('profile', $profile);
       $template->assign('subtitle', $lang['profile_subtitle']);
       $template->assign('subtemplate', 'profile.edit.inc.tpl');     
      
      }
     break;
   }
 }  
?>
