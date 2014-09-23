<?php
if(!defined('IN_INDEX')) exit;

switch($action)
 {
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
          //else $projects[$i]['teaser_supertitle'] = strftime($lang['time_format'], $row['created_timestamp']);
          if($row['teaser_title']) $projects[$i]['teaser_title'] = htmlspecialchars($row['teaser_title']);
          else $projects[$i]['teaser_title'] = htmlspecialchars($row['title']);
          if($row['teaser_text']) $projects[$i]['teaser_text'] = $row['teaser_text'];
          //else $projects[$i]['teaser_text'] = truncate($row['content'], $settings['page_teaser_auto_truncate']);
          else $projects[$i]['teaser_text'] = $row['content'];
          if($row['teaser_linktext']) $projects[$i]['teaser_linktext'] = $row['teaser_linktext'];
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
