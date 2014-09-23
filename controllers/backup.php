<?php
if(!defined('IN_INDEX')) exit;

if($permission->granted(Permission::ADMIN) && $db_settings['backup_path'] && $db_settings['superuser'] && $db_settings['superuser_password'])
 {
  if(isset($_GET['success'])) $template->assign('success', $_GET['success']);
  if(isset($_GET['failure'])) $template->assign('failure', $_GET['failure']);  
  
  switch($action)
   {
    case 'default':
     $handle=opendir($db_settings['backup_path']);
     while($file = readdir($handle))
      {
       if(preg_match('/\.backup$/i', $file) || preg_match('/\.tgz$/i', $file))
        {
         $backup_files_array[] = $file;
        }
      }
     closedir($handle);
     if(isset($backup_files_array))
      {
       arsort($backup_files_array);        
       $i=0;
       foreach($backup_files_array as $backup_file)
        {
         $backup_files[$i]['name'] = $backup_file;
         $backup_files[$i]['type'] = preg_match('/\.db\.backup$/i', $backup_file) ? 'database' : 'files';
         $backup_files[$i]['date'] = strftime($lang['time_format'], filemtime($db_settings['backup_path'].$backup_file));
         $backup_files[$i]['size'] = number_format(filesize($db_settings['backup_path'].$backup_file)/pow(1024, 2),2);         
         $i++;
        }
       $template->assign('backup_files', $backup_files);
      }      

     $template->assign('subtitle', $lang['backup_title']);
     $template->assign('subtemplate', 'backup.inc.tpl');         
     break;
   
    case 'create_db_backup':
     $file = $db_settings['backup_path'] . 'backup.'.date('YmdHis').'.'.uniqid().'.db.backup';
     #echo 'pg_dump -Fc -h '.$db_settings['host'].' -p '.$db_settings['port'].' '.$db_settings['database'].' > '.$file;
     #exit;
     exec('export PGPASSWORD="'.$db_settings['superuser_password'].'" && export PGUSER="'.$db_settings['superuser'].'" && pg_dump -Fc -h '.$db_settings['host'].' -p '.$db_settings['port'].' --role '.$db_settings['user'].' '.$db_settings['database'].' > '.$file.' && unset PGPASSWORD && unset PGUSER');
     header('Location: '.BASE_URL.'?r=backup&success=backup_file_created');
     exit;
     break;

    case 'create_file_backup':
     $file = $db_settings['backup_path'] . 'backup.'.date('YmdHis').'.'.uniqid().'.files.tgz';
     #echo 'pg_dump -Fc -h '.$db_settings['host'].' -p '.$db_settings['port'].' '.$db_settings['database'].' > '.$file;
     #exit;
     exec('cd '.FILES_PATH.' && tar czf '.$file .' *');
     header('Location: '.BASE_URL.'?r=backup&success=backup_file_created');
     exit;
     break;


    case 'restore':
     if(isset($_GET['file']) && (preg_match('/\.db\.backup$/i', $_GET['file']) || preg_match('/\.files\.tgz$/i', $_GET['file'])) && file_exists($db_settings['backup_path'].$_GET['file']))
      {
       $backup['file'] = htmlspecialchars($_GET['file']);    
       if(preg_match('/\.db\.backup$/i', $_GET['file']))
        {
         $backup['type'] = 'database';
         $template->assign('subtitle', $lang['backup_db_restore_title']);
        }
       else
        {
         $backup['type'] = 'files';
         $template->assign('subtitle', $lang['backup_files_restore_title']);
        }
       $backup['date'] = strftime($lang['time_format'], filemtime($db_settings['backup_path'].$_GET['file']));
       $backup['size'] = number_format(filesize($db_settings['backup_path'].$_GET['file'])/pow(1024, 2),2);         
       $template->assign('backup', $backup);
       
       $template->assign('subtemplate', 'backup.restore.inc.tpl');           
      }
     break;

    case 'delete':
     if(isset($_GET['file']) && file_exists($db_settings['backup_path'].$_GET['file']) && isset($_GET['confirmed']))
      {
       @unlink($db_settings['backup_path'].$_GET['file']);         
       header('Location: '.BASE_URL.'?r=backup');
       exit;
      }
     break;

    case 'download':
     if(isset($_GET['file']) && file_exists($db_settings['backup_path'].$_GET['file']))
      {
       $len = filesize($db_settings['backup_path'].$_GET['file']);
       $fh = @fopen($db_settings['backup_path'].$_GET['file'], "r");
       if(!$fh) return false;
       $data = fread($fh, $len);
       fclose($fh);
       #header("Content-Type: application/x-gzip");
       header("Content-Type: mime/type");
       header("Content-Transfer-Encoding: binary");
       header("Content-Disposition: attachment; filename=".$_GET['file']);
       header("Accept-Ranges: bytes");
       header("Content-Length: ".$len);
       echo $data;
       exit;
      }
     break;

    case 'restore_submit':  
     if(isset($_POST['pw']) && isset($_POST['file']))
      {
       // check password:
       $dbr = Database::$connection->prepare("SELECT pw FROM ".Database::$db_settings['userdata_table']." WHERE id=:id LIMIT 1");
       $dbr->bindParam(':id', $_SESSION[$settings['session_prefix'].'auth']['id']);
       $dbr->execute();
       list($pw) = $dbr->fetch();
       if(check_pw($_POST['pw'], $pw))
        {
         if(preg_match('/\.db\.backup$/i', $_POST['file']))
          {
           $file = $db_settings['backup_path'].escapeshellcmd($_POST['file']);
           exec('export PGPASSWORD="'.$db_settings['superuser_password'].'" && export PGUSER="'.$db_settings['superuser'].'" && pg_restore -c -h '.$db_settings['host'].' -p '.$db_settings['port'].' -d '.$db_settings['database'].' --role '.$db_settings['user'].' '.$file.' && unset PGPASSWORD && unset PGUSER');
           header('Location: '.BASE_URL.'?r=backup&success=backup_restored');
           exit;
          }
         elseif(preg_match('/\.files\.tgz$/i', $_POST['file']))
          {
           $file = $db_settings['backup_path'].escapeshellcmd($_POST['file']);
           #echo 'tar -C '.FILES_PATH.' -xvf '.$file;
           #exit;
           exec('tar -C '.FILES_PATH.' -xvf '.$file);
           header('Location: '.BASE_URL.'?r=backup&success=backup_restored');
           exit;          
          }         
         
        }
      else
       {
        header('Location: '.BASE_URL.'?r=backup.restore&file='.htmlspecialchars($_POST['file']).'&failure=password_wrong');
        exit;
       }
      }
      break;


   }
 }
?>
