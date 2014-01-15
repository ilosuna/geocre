<?php
if(!defined('IN_INDEX')) exit;

if(isset($_SESSION[$settings['session_prefix'].'auth']) && $_SESSION[$settings['session_prefix'].'auth']['type']>0)
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
      // get sender information:
      $dbr = Database::$connection->prepare("SELECT id, name, email FROM ".Database::$db_settings['userdata_table']." WHERE id=:id LIMIT 1");
      $dbr->bindParam(':id', $_SESSION[$settings['session_prefix'].'auth']['id']);
      $dbr->execute();
      $row = $dbr->fetch();
      if(isset($row['id']))
       {
        require(BASE_PATH.'lib/phpmailer/class.phpmailer.php');
        $mail = new PHPMailer();
        $mail->CharSet = $lang['charset'];
        $mail->IsSMTP();
        $mail->Host       = $settings['email_smtp_host'];
        #$mail->SMTPDebug  = 2;
        $mail->SMTPAuth   = true;
        $mail->Port       = $settings['email_smtp_port'];
        $mail->Username   = $settings['email_smtp_username'];
        $mail->Password   = $settings['email_smtp_password'];
        $mail->SetFrom($row['email'], $row['name']);
        #$mail->AddReplyTo($email);
        $mail->Subject = $lang['feedback_subject'];
        $mail->Body = $feedback_message;
        $mail->AddAddress($settings['email_address']);
        if(!$mail->Send())
         {
          //$errors[] = $mail->ErrorInfo;
          $errors[] = 'mail_error';
         }
        else
         {
          $template->assign('feedback_sent', true); 
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
  
  
  $template->assign('subtitle', $lang['feedback_subtitle']); 
  $template->assign('subtemplate','feedback.inc.tpl');
 } // sessioncheck
else
 {
  header('Location: '.BASE_URL);
  exit;
 }
?>
