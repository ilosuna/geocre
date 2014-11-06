<?php
if(!defined('IN_INDEX')) exit;

if(isset($_SESSION[$settings['session_prefix'].'auth']) && $settings['feedback'])
 {
  if(isset($_POST['feedback_message']))
   {
    $feedback_message = isset($_POST['feedback_message']) ? trim($_POST['feedback_message']) : '';

    if(empty($feedback_message))
     {
      $errors[] = 'feedback_no_message';
     }
    if(mb_strlen($feedback_message, $lang['charset']) > $settings['feedback_message_maxlength'])
     {
      $errors[] = 'feedback_error_message_too_long';
     }

    if(empty($errors))
     {

      // get recipient addresses:
      $recipient_mails_raw = explode(',', $settings['feedback']);
      foreach($recipient_mails_raw as $recipient_mail)
       {
        $recipient_mails[] = trim($recipient_mail);
       }
      
      // get sender information:
      $dbr = Database::$connection->prepare("SELECT id, name, real_name, email FROM ".Database::$db_settings['userdata_table']." WHERE id=:id LIMIT 1");
      $dbr->bindParam(':id', $_SESSION[$settings['session_prefix'].'auth']['id']);
      $dbr->execute();
      $row = $dbr->fetch();
      if(isset($row['id']))
       {
        // set default language if user uses a different one:
        if(isset($_SESSION[$settings['session_prefix'].'language']) && $_SESSION[$settings['session_prefix'].'language']!=$settings['language'])
         {
          require(BASE_PATH.'lang/'.$settings['language'].'.lang.php');
         } 
        
        if(isset($_POST['help'])) $subject = $lang['feedback_subject_help'];
        else $subject = $lang['feedback_subject'];
      
        if(isset($_POST['url'])) $url = $_POST['url'];
        else $url = BASE_URL . '?r=feedback';
        
        $sender = $row['name'];
        if($row['real_name']) $sender .= ' (' . $row['real_name'] . ')';
        
        $lang['feedback_mail_body'] = str_replace('[email]', $row['email'], str_replace('[sender]', $sender, str_replace('[url]', $url, str_replace('[message]', $feedback_message, $lang['feedback_mail_body']))));
        
        require(BASE_PATH.'lib/phpmailer/class.phpmailer.php');
        
        foreach($recipient_mails as $recipient_mail)
         {
          $mail = new PHPMailer();
          $mail->CharSet = $lang['charset'];
          $mail->IsSMTP();
          $mail->Host       = $settings['email_smtp_host'];
          #$mail->SMTPDebug  = 2;
          $mail->SMTPAuth   = true;
          $mail->Port       = $settings['email_smtp_port'];
          $mail->Username   = $settings['email_smtp_username'];
          $mail->Password   = $settings['email_smtp_password'];
          $mail->AddReplyTo($row['email'], $row['name']);
          $mail->SetFrom($settings['email_address'], $settings['website_title']);
          $mail->Subject = $subject;
          $mail->Body = $lang['feedback_mail_body'];
          $mail->AddAddress($recipient_mail);
          if(!$mail->Send())
           {
            //$errors[] = $mail->ErrorInfo;
            $mail_error = true;
           }
          else
           {
            $template->assign('feedback_sent', true); 
           }
         }
       
        if(isset($mail_error)) $errors[] = 'mail_error';
        else $template->assign('feedback_sent', true);
       
        // reset language:
        if(isset($_SESSION[$settings['session_prefix'].'language']) && $_SESSION[$settings['session_prefix'].'language']!=$settings['language'] && file_exists(BASE_PATH.'lang/'.$_SESSION[$settings['session_prefix'].'language'].'.lang.php'))
         {
          require(BASE_PATH.'lang/'.$_SESSION[$settings['session_prefix'].'language'].'.lang.php');
         } 
       
       }
      else
       {
        $errors[] = 'error_unknown_user';
       }      
     }
   }
  
  if(isset($feedback_message)) $template->assign('feedback_message', htmlspecialchars($feedback_message));
  if(isset($errors)) $template->assign('errors', $errors);
  
  if(isset($_POST['help']))
   {
    if(isset($errors))
     {
      $response['status'] = 0;
      
      $response['message'] = '<ul>';
      foreach($errors as $error)
       {
        $response['message'] .= '<li>'.$lang[$error].'</li>';
       }
      $response['message'] .= '</ul>'; 
      
     }
    else
     {
      $response['status'] = 1;
      $response['message'] = $lang['feedback_message_sent'];
     }
     
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    exit;
   }
  
  $template->assign('active', 'feedback');
  $template->assign('subtitle', $lang['feedback_subtitle']); 
  $template->assign('subtemplate','feedback.inc.tpl');
 } // sessioncheck
?>
