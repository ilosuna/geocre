<?php
if(!defined('IN_INDEX')) exit;

if($permission->granted(Permission::DATA_MANAGEMENT))
 {
  if(isset($_REQUEST['delete']))
   {
    $dbr = Database::$connection->prepare("DELETE FROM ".$db_settings['db_table_relations_table']." WHERE id = :id");
    $dbr->bindValue(':id',$_REQUEST['delete']);
    $dbr->execute();
    header('Location: '.BASE_URL.'?r=data_relations');
    exit;
   } 

  // get available tables:
  $dbr = Database::$connection->prepare("SELECT id,
                                                table_name,
                                                title
                                       FROM ".$db_settings['data_models_table']."
                                       WHERE status > 0
                                       ORDER BY sequence ASC");
  $dbr->execute();

  $i=0;
  foreach($dbr as $row)
   {
    $tables[$i]['id'] = $row['id'];
    $tables[$i]['name'] = $row['table_name'];
    $tables[$i]['title'] = $row['title'];
    ++$i;
   } 

 if(isset($_POST['t1']) && isset($tables))
  {
   $t1 = isset($_POST['t1']) ? intval($_POST['t1']) : 0;  
   $t2 = isset($_POST['t2']) ? intval($_POST['t2']) : 0;  
  
   // check if tables exist 
   foreach($tables as $table)
    {
     if($t1==$table['id']) $valid_t1 = $t1;
     if($t2==$table['id']) $valid_t2 = $t2;  
    }
   
   if(isset($valid_t1) && isset($valid_t2))
    {
     // check if relation already exists:
     $dbr = Database::$connection->prepare("SELECT COUNT(*)
                                                   FROM ".$db_settings['db_table_relations_table']."
                                                   WHERE t1=:t1 AND t2=:t2 OR t1=:t2 AND t2=:t1");
     $dbr->bindParam(':t1', $_POST['t1'], PDO::PARAM_INT);
     $dbr->bindParam(':t2', $_POST['t2'], PDO::PARAM_INT);
     $dbr->execute();
     list($count) = $dbr->fetch();
     if($count==0)
      {
       // save relation:
       $dbr = Database::$connection->prepare("INSERT INTO ".$db_settings['db_table_relations_table']." (t1, t2) VALUES (:t1, :t2)");
       $dbr->bindParam(':t1', $valid_t1, PDO::PARAM_INT);
       $dbr->bindParam(':t2', $valid_t2, PDO::PARAM_INT);
       $dbr->execute();   
       header('Location: '.BASE_URL.'?r=data_relations');
       exit;
      }
     
     
    }
  }

  // get relations:
  $dbr = Database::$connection->prepare("SELECT id,
                                                t1,
                                                t2
                                                FROM ".$db_settings['db_table_relations_table']."
                                                ORDER BY id ASC");
  $dbr->execute();

  $i=0;
  foreach($dbr as $row)
   {
    $relations[$i]['id'] = $row['id'];
    $relations[$i]['t1'] = $row['t1'];
    $relations[$i]['t2'] = $row['t2'];
    ++$i;
   }   


  if(isset($relations)) $template->assign('relations', $relations);
  
  // table names:
  if(isset($tables))
   {
    foreach($tables as $table)
     {
      $table_names[$table['id']]['name'] = $table['name'];
      $table_names[$table['id']]['title'] = $table['title'];
     }
    $template->assign('table_names', $table_names);
    $template->assign('tables', $tables);
   } 
    
  $template->assign('subtitle', $lang['data_relations_title']);
  $template->assign('subtemplate', 'data_relations.inc.tpl');    
    
    
   
 }
?>
