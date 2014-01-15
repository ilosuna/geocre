<?php
if(!defined('IN_INDEX')) exit;

if(isset($_POST['email']) && trim($_POST['email'])=='') $action = 'default';
if(isset($_GET['code'])) $action = 'reset';

switch($action)
 {   
  case 'default':
   $captcha['number_1'] = rand(1,10);
   $captcha['number_2'] = rand(0,10);
   $captcha['sum'] = $captcha['number_1'] + $captcha['number_2'];
   $captcha['time'] = time();
   $_SESSION[$settings['session_prefix'].'captcha'] = $captcha;
   $template->assign('captcha', $captcha);
   $template->assign('subtitle', $lang['reset_pw_subtitle']); 
   $template->assign('subtemplate', 'reset_pw.inc.tpl');   
   break;
  
  case 'email_submit':
   if(isset($_POST['email']))
    {
     $email = isset($_POST['email']) ? trim($_POST['email']) : '';
     $check = isset($_POST['check']) ? intval($_POST['check']) : 0;
     if(empty($_SESSION[$settings['session_prefix'].'captcha'])) $errors[] = 'error_captcha_check';
     elseif(time()-$_SESSION[$settings['session_prefix'].'captcha']['time']<3) $errors[] = 'error_captcha_check';
     elseif($_SESSION[$settings['session_prefix'].'captcha']['sum'] != $check) $errors[] = 'error_captcha_check';
     
     if(empty($email)) $errors[] = 'error_reset_pw_email';
     
     if(empty($errors))
      {
       $dbr = Database::$connection->prepare("SELECT id, name, email, EXTRACT(EPOCH FROM NOW()-reset_pw_time) AS last_reset_seconds FROM ".Database::$db_settings['userdata_table']." WHERE LOWER(email)=LOWER(:email) LIMIT 1");
       $dbr->bindParam(':email', $email);
       $dbr->execute();
       $row = $dbr->fetch();
       if(isset($row['id']))
        {
         if($row['last_reset_seconds'] && $row['last_reset_seconds']<600) $errors[] = 'error_reset_pw_recently';
         else
          { 
           $reset_pw_code = random_string(32);
           $reset_pw_code_hash = generate_pw_hash($reset_pw_code);
         
           $pw_reset_link = BASE_URL.'?r=reset_pw&code='.$row['id'].'.'.$reset_pw_code;
         
           $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['userdata_table']." SET reset_pw_code=:reset_pw_code, reset_pw_time=NOW() WHERE id=:id;");
           $dbr->bindParam(':id', $row['id'], PDO::PARAM_INT);
           $dbr->bindParam(':reset_pw_code', $reset_pw_code_hash, PDO::PARAM_STR);
           $dbr->execute();
           
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
           $mail->Subject = $lang['reset_pw_mail_subject'];
           $mail->Body = str_replace('[name]', $row['name'], str_replace('[link]', $pw_reset_link, $lang['reset_pw_mail_text']));
           $mail->AddAddress($row['email']);
           if($mail->Send())
            {          
             $template->assign('reset_pw_link_sent', true); 
             $template->assign('subtitle', $lang['reset_pw_subtitle']); 
             $template->assign('subtemplate', 'reset_pw.inc.tpl');   
            }
           else $errors[] = 'mail_error'; 
          }
        }
       else $errors[] = 'error_reset_pw_email'; 
      }
     
     if(isset($errors))
      {
       $captcha['number_1'] = rand(1,10);
       $captcha['number_2'] = rand(0,10);
       $captcha['sum'] = $captcha['number_1'] + $captcha['number_2'];
       $captcha['time'] = time();
       $_SESSION[$settings['session_prefix'].'captcha'] = $captcha;
       $template->assign('captcha', $captcha);
       $template->assign('email', htmlspecialchars($email)); 
       $template->assign('errors', $errors); 
       $template->assign('subtitle', $lang['reset_pw_subtitle']); 
       $template->assign('subtemplate', 'reset_pw.inc.tpl');   
      }
    
    }
   break;

  case 'reset':
   if(isset($_GET['code']))
    {
     $code_parts = explode('.', $_GET['code']);
     if(isset($code_parts[0]) && isset($code_parts[1]))
      {
       $user_id = intval($code_parts[0]);
       $code = trim($code_parts[1]);
       $dbr = Database::$connection->prepare("SELECT id, reset_pw_code FROM ".Database::$db_settings['userdata_table']." WHERE id=:id AND reset_pw_time > (NOW() - INTERVAL '60 minutes') LIMIT 1");
       $dbr->bindParam(':id', $user_id);
       $dbr->execute();
       $row = $dbr->fetch();
       if(isset($row['id']))
        {
         if(check_pw($code, $row['reset_pw_code']))
          {
           $template->assign('id', $row['id']);
           $template->assign('code', htmlspecialchars($code));
           $template->assign('reset_pw_form', true);
          }
         else $template->assign('code_invalid', true);
        }
       else $template->assign('code_invalid', true);
      }
     else $template->assign('code_invalid', true); 
     $template->assign('subtitle', $lang['reset_pw_subtitle']); 
     $template->assign('subtemplate', 'reset_pw.inc.tpl');   
    }
   break;
  
  case 'reset_submit':
   $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
   $code = isset($_POST['code']) ? trim($_POST['code']) : '';
   $new_pw = isset($_POST['new_pw']) ? trim($_POST['new_pw']) : '';
   $new_pw_repeat = isset($_POST['new_pw_repeat']) ? trim($_POST['new_pw_repeat']) : '';
   
   if(empty($id) || empty($code) || empty($new_pw) || empty($new_pw_repeat)) $errors[] = 'reset_pw_error_form_uncomplete';
   if($new_pw && mb_strlen($new_pw)<$settings['min_pw_length']) $errors[] = 'error_pw_too_short';
   if($new_pw!=$new_pw_repeat) $errors[] = $lang['error_pw_match'];
   
   if(empty($errors))
    {
     $dbr = Database::$connection->prepare("SELECT id, reset_pw_code FROM ".Database::$db_settings['userdata_table']." WHERE id=:id AND reset_pw_time > (NOW() - INTERVAL '60 minutes') LIMIT 1");
     $dbr->bindParam(':id', $id);
     $dbr->execute();
     $row = $dbr->fetch();
     if(isset($row['id']))
      {
       if(check_pw($code, $row['reset_pw_code']))
        {
         $pw_hash = generate_pw_hash($new_pw);
         $dbr = Database::$connection->prepare("UPDATE ".Database::$db_settings['userdata_table']." SET pw=:pw, reset_pw_code=NULL WHERE id=:id;");
         $dbr->bindParam(':id', $id, PDO::PARAM_INT);
         $dbr->bindParam(':pw', $pw_hash, PDO::PARAM_STR);
         $dbr->execute();
         header('Location: '.BASE_URL.'?r=login&success=reset_pw_done');
         exit;       
        }
       else $template->assign('code_invalid', true);
      }
     else $template->assign('code_invalid', true);
    }
   else
    {
     $template->assign('id', $id);
     $template->assign('code', htmlspecialchars($code));
     $template->assign('reset_pw_form', true);     
     $template->assign('errors', $errors);
    }
   $template->assign('subtitle', $lang['reset_pw_subtitle']); 
   $template->assign('subtemplate', 'reset_pw.inc.tpl'); 
   break;
 }
?>
