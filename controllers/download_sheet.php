<?php
if(!defined('IN_INDEX')) exit;

if(isset($_REQUEST['id']) && ($permission->granted(Permission::DATA_MANAGEMENT) || $permission->granted(Permission::DATA_ACCESS, intval($_REQUEST['id']), Permission::READ)))
 {
  $javascripts[] = JQUERY_COOKIE;
  
  switch($action)
   {
    case 'default':
     // get table properties:
     $dbr = Database::$connection->prepare("SELECT id, title FROM ".Database::$db_settings['data_models_table']." WHERE id=:id LIMIT 1");
     $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
     $dbr->execute();
     $row = $dbr->fetch();
     if($row)
      {
       $data['id'] = $row['id'];
       $data['title'] = htmlspecialchars($row['title']);
       $template->assign('data', $data);
       $template->assign('subtitle', $lang['download_sheet_title']);
       $template->assign('subtemplate', 'download_sheet.inc.tpl');
      }
    break;    
    
    case 'download':
     set_download_token_cookie('downloadtoken');
     $format = isset($_POST['format']) ? trim($_POST['format']) : '';
     // get table properties:
     $dbr = Database::$connection->prepare("SELECT id, table_name, title FROM ".Database::$db_settings['data_models_table']." WHERE id=:id LIMIT 1");
     $dbr->bindParam(':id', $_REQUEST['id'], PDO::PARAM_INT);
     $dbr->execute();
     $data = $dbr->fetch();
     if($data)
      {
       // get items:
       $dbr = Database::$connection->prepare("SELECT label, description, column_type, priority, choices, choice_labels FROM ".Database::$db_settings['data_model_items_table']." WHERE table_id=:table_id ORDER BY sequence ASC");       
       $dbr->bindParam(':table_id', $data['id'], PDO::PARAM_INT);
       $dbr->execute();
       $i=0;
       while($row = $dbr->fetch())
        {
         $items[$i]['label'] = htmlspecialchars($row['label']);
         $items[$i]['description'] = htmlspecialchars($row['description']);
         $items[$i]['column_type'] = $row['column_type'];
         $items[$i]['priority'] = $row['priority'];
         
         if($row['choices'])
          {
             unset($choice_labes);
             $choices = explode("\n", $row['choices']);
             if($row['choice_labels']) $choice_labes = explode("\n", $row['choice_labels']);
             $ii=0;
             foreach($choices as $choice)
              {
               if($choice=='*') $choice = '__________';
               if(isset($choice_labes[$ii])) $items[$i]['choices'][] = htmlspecialchars($choice_labes[$ii]);
               else $items[$i]['choices'][] = htmlspecialchars($choice);
               ++$ii;
              }
          }
         ++$i;
        }
       if(isset($items)) $template->assign('items', $items);
       $template->assign('title', htmlspecialchars($data['title']));
       
       switch($format)
        {
         case 'pdf':
          $filename = $data['table_name'].'.pdf';
          $html = $template->fetch(BASE_PATH.'templates/export/sheet.pdf.tpl');
          require BASE_PATH.'lib/dompdf/dompdf_config.inc.php';
          $dompdf = new DOMPDF();
          $dompdf->load_html($html);
          $dompdf->render();
          // add footer
          $canvas = $dompdf->get_canvas();
          $font = Font_Metrics::get_font("helvetica");
          if (!isset($font)) { Font_Metrics::get_font("sans-serif"); }
          $size = 10;
          $color = array(0.5,0.5,0.5);
          $text_height = Font_Metrics::get_font_height($font, $size);
          $w = $canvas->get_width();
          $h = $canvas->get_height();
          $y = $h - 2 * $text_height - 20;
          $text = $data['title'].' - {PAGE_NUM}/{PAGE_COUNT}';  
          $width = Font_Metrics::get_text_width($data['title'].' - x/yy', $font, $size);
          $canvas->page_text($w / 2 - $width / 2, $y, $text, $font, $size, $color);
          $dompdf->stream($filename, array('compress' => 1, 'Attachment' => 1));
          exit;
          break;
         
         case 'docx':
          $filename = $data['table_name'].'.docx';
          require BASE_PATH.'lib/htmldocx/phpword/PHPWord.php';
          require BASE_PATH.'lib/htmldocx/simplehtmldom/simple_html_dom.php';
          require BASE_PATH.'lib/htmldocx/htmltodocx_converter/h2d_htmlconverter.php';
          $html = $template->fetch(BASE_PATH.'templates/export/sheet.docx.tpl');
          $phpword_object = new PHPWord();
          $phpword_object->addParagraphStyle('footerParagraph', array('align'=>'center'));
          $phpword_object->addFontStyle('footerFont', array('size'=>10, 'color'=>'808080'));
          $section = $phpword_object->createSection();
          $footer = $section->createFooter();
          $footer->addPreserveText($data['title'].' - {PAGE}/{NUMPAGES}', 'footerFont', 'footerParagraph');
          $html_dom = new simple_html_dom();
          $html_dom->load($html);
          $html_dom_array = $html_dom->find('html',0)->children();
          $initial_state = array('phpword_object' => &$phpword_object, // Must be passed by reference.
                                 'base_root' => BASE_URL,
                                 'base_path' => BASE_PATH,
                                 'current_style' => array('size' => '11'), // The PHPWord style on the top element - may be inherited by descendent elements.
                                 'parents' => array(0 => 'body'), // Our parent is body.
                                 'list_depth' => 0, // This is the current depth of any current list.
                                 'context' => 'section', // Possible values - section, footer or header.
                                 'pseudo_list' => TRUE, // NOTE: Word lists not yet supported (TRUE is the only option at present).
                                 'pseudo_list_indicator_font_name' => 'Wingdings', // Bullet indicator font.
                                 'pseudo_list_indicator_font_size' => '7', // Bullet indicator size.
                                 'pseudo_list_indicator_character' => 'l ', // Gives a circle bullet point with wingdings.
                                 'table_allowed' => TRUE, // Note, if you are adding this html into a PHPWord table you should set this to FALSE: tables cannot be nested in PHPWord.
                                 'treat_div_as_paragraph' => TRUE, // If set to TRUE, each new div will trigger a new line in the Word document.
                                 'style_sheet' => $phpword_styles); // This is an array (the "style sheet") - returned by htmltodocx_styles_example() here (in styles.inc) - see this function for an example of how to construct this array.    
          htmltodocx_insert_html($section, $html_dom_array[0]->nodes, $initial_state);
          $html_dom->clear(); 
          unset($html_dom);
          header('Content-Type: application/vnd.ms-word');
          header('Content-Disposition: attachment;filename="'.$filename.'"');
          header('Cache-Control: max-age=0');
          $objWriter = PHPWord_IOFactory::createWriter($phpword_object, 'Word2007');
          $objWriter->save('php://output');
          exit;
          break;
         
         case 'txt':
          $filename = $data['table_name'].'.txt';
          $text = $template->fetch(BASE_PATH.'templates/export/sheet.txt.tpl');
          header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
          header("Content-Type: application/force-download");
          header("Content-Length: " . mb_strlen($text));
          header("Connection: close");
          echo $text;
          exit;
          break;       
        }
      }
     
     break;
   }
 }
?>
