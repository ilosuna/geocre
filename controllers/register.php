<?php
if(!defined('IN_INDEX')) exit;

if($settings['register_mode']==0) // if users can register themselves:
{

switch($action)
 {
  case 'default':
   $template->assign('subtitle', $lang['register_title']);
   $template->assign('subtemplate', 'register.inc.tpl');     
   break;

  case 'submit':
   $name = isset($_POST['name']) ? trim($_POST['name']) : '';
   $email = isset($_POST['email']) ? trim($_POST['email']) : '';
   $pw = isset($_POST['pw']) ? trim($_POST['pw']) : '';
   $pw_repeat = isset($_POST['pw_repeat']) ? trim($_POST['pw_repeat']) : '';
   $register_code = isset($_POST['register_code']) ? trim($_POST['register_code']) : '';
 
   
   
   if(empty($name) || empty($email) || empty($pw) || empty($pw_repeat) || (empty($register_code) && $settings['register_code'])) $errors[] = $lang['form_uncomplete'];
   
   if(empty($errors))
    {
     // check if a user name was entered:
     #if(empty($name))
     # {
     #  $errors[] = 'error_user_no_name';
     # }
     if(mb_strlen($name) > $settings['user_name_max_length'])
      {
       $errors[] = 'error_user_name_too_long';
       $error_fields[] = 'name';
      }      
     // if user name entered, check if available:
     else
      {
       $dbr = Database::$connection->prepare("SELECT COUNT(*) FROM " . Database::$db_settings['userdata_table'] . " WHERE LOWER(name)=LOWER(:name)");
       $dbr->bindParam(':name', $name, PDO::PARAM_STR);
       $dbr->execute();
       list($count) = $dbr->fetch();
       if($count > 0)
        {
         $errors[] = 'error_user_name_unavailable';
         $error_fields[] = 'name';
        } 
      }
     // check if e-mail was eneterd:
     #if(empty($email)) $errors[] = 'error_user_no_email';
     // check if e-mail is valid:
     if(!is_valid_email($email))
      {
       $errors[] = 'error_user_invalid_email';
        $error_fields[] = 'email';
      }
     // check if e-mail doesn't exists yet:
     else
      {
       $dbr = Database::$connection->prepare("SELECT COUNT(*) FROM " . Database::$db_settings['userdata_table'] . " WHERE LOWER(email)=LOWER(:email)");        
       $dbr->bindValue(':email', $email, PDO::PARAM_STR);
       $dbr->execute();
       list($count) = $dbr->fetch();
       if($count>0)
        {
         $errors[] = 'error_email_already_exists'; 
         $error_fields[] = 'email';
        }
      }
     // check password length:
     if(mb_strlen($pw)<$settings['min_pw_length']) $errors[] = 'error_pw_too_short';
     // check if passwords match:
     if($pw!=$pw_repeat)
      {
       $errors[] = 'error_pw_match';
       $error_fields[] = 'pw';
      }
     // chack register code:
     if($settings['register_code'] && $register_code!=$settings['register_code'])
      {
       $errors[] = 'error_invalid_register_code';
       $error_fields[] = 'register_code';
      }
    }
    
   if(empty($errors))
    {
     // save user account:
     $pw_hash = generate_pw_hash($pw);
     $dbr = Database::$connection->prepare("INSERT INTO ".Database::$db_settings['userdata_table']." (name, email, pw, registered) VALUES (:name, :email, :pw, NOW());");
     $dbr->bindParam(':name', $name, PDO::PARAM_STR);
     $dbr->bindParam(':email', $email, PDO::PARAM_STR);        
     $dbr->bindParam(':pw', $pw_hash, PDO::PARAM_STR);
     $dbr->execute();
     $dbr = Database::$connection->query(LAST_INSERT_ID_QUERY);
     list($last_insert_id) = $dbr->fetch();
     
     // assign user to default group:
     if($settings['default_group'])
      {
       // check if group exists:
       $dbr = Database::$connection->prepare("SELECT COUNT(*) FROM ".Database::$db_settings['group_table']." WHERE id=:id");
       $dbr->bindParam(':id', $settings['default_group'], PDO::PARAM_INT);
       $dbr->execute();     
       list($group_count) = $dbr->fetch();
       if($group_count==1)
        {
         // add user to default group:
         $dbr = Database::$connection->prepare('INSERT INTO '.Database::$db_settings['group_memberships_table'].' ("user", "group") VALUES (:user, :group)');
         $dbr->bindParam(':group', $settings['default_group'], PDO::PARAM_INT);
         $dbr->bindParam(':user', $last_insert_id, PDO::PARAM_INT);
         $dbr->execute();
        }
      }
     
     // send notifications to admins and user admins:
     if($settings['register_notification'])
      {
       // get groups of admins and user admins:
       $dbr = Database::$connection->prepare('SELECT "group"
                                              FROM '.Database::$db_settings['group_permissions_table'].'
                                              WHERE type='.Permission::ADMIN.' OR type='.Permission::USERS_GROUPS);
       $dbr->execute();
       foreach($dbr as $row)
        {
         $groups[] = $row['group'];
        }
       if(isset($groups))
        {
         // get user names and e-mails
         $dbr = Database::$connection->prepare('SELECT DISTINCT users.name, users.email
                                                FROM '.Database::$db_settings['group_memberships_table'].' AS memberships
                                                LEFT JOIN '.Database::$db_settings['userdata_table'].' AS users ON memberships.user=users.id
                                                WHERE memberships.group IN ('.implode(',', $groups).')');
         $dbr->execute();
         // send notifications:
         
         if($dbr->rowCount())
          {
           require(BASE_PATH.'lib/phpmailer/class.phpmailer.php');
           $notification_mail_text = str_replace('[email]', $email, str_replace('[user]', $name, $lang['register_user_notification_mail_text']));
           foreach($dbr as $row)
            {
             $mail = new PHPMailer();
             $mail->CharSet = $lang['charset'];
             $mail->IsSMTP();
             $mail->Host       = $settings['email_smtp_host'];
             $mail->SMTPAuth   = true;
             $mail->Port       = $settings['email_smtp_port'];
             $mail->Username   = $settings['email_smtp_username'];
             $mail->Password   = $settings['email_smtp_password'];
             $mail->SetFrom($settings['email_address'], $settings['website_title']);
             $mail->Subject = $lang['register_user_notification_subject'];
             $mail->Body = str_replace('[name]', $row['name'], $notification_mail_text);
             $mail->AddAddress($row['email']);
             $mail->Send();          
            }
          }
        }
      } 
     
     header('Location: '.BASE_URL.'?r=login&success=account_registered');
    } 
   else
    {
     $data['name'] = htmlspecialchars($name);
     $data['email'] = htmlspecialchars($email);
     $data['register_code'] = htmlspecialchars($register_code);
     $template->assign('data', $data);
     $template->assign('errors', $errors);
     if(isset($error_fields)) $template->assign('error_fields', $error_fields);
     $template->assign('subtitle', $lang['register_title']);
     $template->assign('subtemplate', 'register.inc.tpl');     
    }
       
 }
 
}
?>
