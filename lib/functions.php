<?php
// autoload required classes:
#function veggis_autoload($class_name)
# {
#  require_once(BASE_PATH.'includes/classes/'.$class_name.'.class.php');
# }
#spl_autoload_register('veggis_autoload');

function imagineLoader($class)
 {
  $imagine_path = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
  require_once BASE_PATH.'lib/'.$imagine_path;
 }

if(get_magic_quotes_gpc())
 {
  function stripslashes_deep($value)
   {
    $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
    return $value;
   }
  $_POST = array_map('stripslashes_deep', $_POST);
  $_GET = array_map('stripslashes_deep', $_GET);
  $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
  $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
 }

/**

 * fetches settings from database 
 */ 
function get_settings()
 {
  $result = Database::$connection->query("SELECT name, value FROM ".Database::$db_settings['settings_table']." ORDER BY name ASC");
  foreach($result as $line) 
   {
    $settings[$line['name']] = stripslashes($line['value']);
   }
  return $settings;
 }

function get_base_url()
 {
  global $settings;
  if($settings['base_url']!='')
   {
    return $settings['base_url'];
   }
  if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')
   {
    $protocol = 'https://';
   }
  else
   {
    $protocol = 'http://';
   }
  $base_url = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
  if(substr($base_url, -1) != '/') $base_url = $base_url.'/';
  return $base_url;
 }

function get_base_path()
 {
  global $settings;
  if($settings['base_path']!='')
   {
    return $settings['base_path'];
   }
  $base_path = dirname($_SERVER['SCRIPT_FILENAME']) . '/';
  return $base_path;
 }

function convertlinebreaks($string)
 {
  return preg_replace("/\015\012|\015|\012/", "\n", $string);
 }

/**
 * encodes string for OpenLayers labels
 */ 
function ol_encode_label($string)
 {
  // remove linebreaks:
  $string = preg_replace("/\015\012|\015|\012/", " ", $string);
  $string = addslashes($string);
  return $string;
 }

/**
 * gets available languages
 *
 * @reurn array
 */
function get_languages()
 {
  foreach (glob(BASE_PATH.'lang/*.lang.php') as $filename)
   {
    $languages[] = substr(basename($filename), 0, -9); // filename without extension ".lang.php"
   }
  if(isset($languages))
   {
    natcasesort($languages);
    $i=0;
    foreach($languages as $language)
     {
      $languages_detailed[$i]['identifier'] = $language; 
      $languages_detailed[$i]['name'] = get_language_name($language);     
      ++$i;
     }
    return $languages_detailed;
   }
  return false;
 }

function get_language_name($string)
 {
  $string_parts = explode('.', $string);
  if(isset($string_parts[1])) $name = ucfirst($string_parts[0]).' ('.$string_parts[1].')';
  else $name = ucfirst($string);
  return $name;     
 }

/**
 * extract template variables given in comma separated string
 * e.g. "foo, bar=foo"
 * @param $string $string
 * @return array
 */
function extract_tvs($string)
 {
  $tv_array = explode(',', $string);
  foreach($tv_array as $tv_item)
   {
    if($tv_item)
     {
      $tv_item_parts = explode('=', $tv_item);
      $tv[trim($tv_item_parts[0])] = isset($tv_item_parts[1]) ? trim($tv_item_parts[1]) : true;
     }
   }
  if(isset($tv)) return $tv;
  else return false;
 }

function get_basemaps($mode=true)
 {
  if(is_array($mode)) // get basemaps by id
   {
    $query = 'SELECT id, title, properties, js, "default" FROM '.Database::$db_settings['basemaps_table'].' WHERE id IN ('.implode(',',$mode).') ORDER BY sequence ASC';
   }
  elseif($mode) // get all basemaps
   {
    $query = 'SELECT id, title, properties, js, "default" FROM '.Database::$db_settings['basemaps_table'].' ORDER BY sequence ASC';
   }
  else // get default basemaps:
   {
    $query = 'SELECT id, title, properties, js, "default" FROM '.Database::$db_settings['basemaps_table'].' WHERE "default" IS true ORDER BY sequence ASC';
   }
  $dbr = Database::$connection->prepare($query);
  $dbr->execute();
  if(is_array($mode) && $dbr->rowCount()==0) // not available ids, get default basemaps:
   {
    $query = 'SELECT id, title, properties, js, "default" FROM '.Database::$db_settings['basemaps_table'].' WHERE "default" IS true ORDER BY sequence ASC';
    $dbr = Database::$connection->prepare($query);
    $dbr->execute();
   }
  $i=0;
  while($row = $dbr->fetch()) 
   {
    $basemaps[$i]['id'] = $row['id'];
    $basemaps[$i]['title'] = htmlspecialchars($row['title']);
    $basemaps[$i]['properties'] = $row['properties'];
    $basemaps[$i]['js'] = $row['js'];
    $basemaps[$i]['default'] = $row['default'];
    ++$i;
   }
  if(isset($basemaps)) return $basemaps;
  return false;
 }

function auto_html($text)
 {
  $text = trim($text);
  if($text!='')
   {
    $text = '<p>' . $text . '</p>';
    $text = preg_replace("/(\015\012\015\012)|(\015\015)|(\012\012)/","</p><p>",$text);
    $text = nl2br($text);
   }
  return $text;
 }

/**
 * checks if string is a valid identifier for database table names and columns
 *
 * @param string $email
 * @return bool
 */
function is_valid_db_identifier($string)
 {
  $valid_characters = 'abcdefghijklmnopqrstuvwxyz0123456789_';
  $valid_beginning_characters = 'abcdefghijklmnopqrstuvwxyz';
  
  $len = strlen($string);
  
  if($len==0) return false;
  
  if(strpos($valid_beginning_characters, $string[0])===false) return false;
  
  for($i=0;$i<$len;++$i)
   {
    if(strpos($valid_characters, $string[$i])===false) return false;
   }
  return true;
 }

/**
 * checks if a email address is valid
 *
 * @param string $email
 * @return bool
 */
function is_valid_email($email)
 {
  if(!preg_match("/^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/", $email))
   {
    return false;
   }
  return true;
 }

function truncate($string, $maxlength=20, $add_complete_string_as_title=false, $cut_string='…')
 {
  if(mb_strlen($string) <= $maxlength) return $string;
  $space_pos = mb_strrpos($string, ' ', -(mb_strlen($string) - $maxlength));
  if($space_pos!==false && $cut_string)
   {
    $truncated_string = mb_substr($string, 0, $space_pos) . ' '.$cut_string;
   }
  else
   {
    $truncated_string = mb_substr($string, 0, $maxlength) . $cut_string;
   }
  if($add_complete_string_as_title) return '<span title="'.htmlspecialchars($string).'">'.$truncated_string.'</span>';
  else return $truncated_string;
 }

function js_encode_mail($email, $name='') 
 {
  if(empty($name)) $linktext = $email;
  else $linktext = $name;
  #$string = 'document.write(\'<a href="mailto:'.$email.'">'.$email.'</a>\')';
  $uid = 'uid'.uniqid();
  $string = 'document.getElementById(\''.$uid.'\').innerHTML=\'<a href="mailto:'.$email.'">'.$linktext.'</a>\'';
  $ret = '';
  $arr = unpack("C*", $string);
  foreach ($arr as $char)
   {
    $ret .= sprintf("%%%X", $char);
   }
  return '<span id="'.$uid.'">'.$name.'</span><script type="text/javascript">eval(unescape(\''.$ret.'\'))</script>';
}

/**
 * creates a written out time indication between now and given timestamp
 *
 * @param floor $time
 * @return string
 */
function how_long_ago($time)
 {
  global $lang;
  $minutes = ceil((time()-intval($time))/60);
  $hours = floor($minutes/60);
  $minutes_remainder = intval($minutes)-intval($hours*60);
  if($minutes_remainder<10) $minutes_remainder = '0'.$minutes_remainder;;
  $hours_minutes = $hours.':'.$minutes_remainder;  
  $days = floor($hours/24);
  if($days>366) $time_ago_written_out = $lang['more_than_a_year_ago'];
  elseif($days>0) $time_ago_written_out = str_replace('[days]', $days, $lang['days_ago']);
  elseif($hours>0) $time_ago_written_out = str_replace('[hours]', $hours_minutes, $lang['hours_ago']);
  elseif($minutes>2) $time_ago_written_out = str_replace('[minutes]', $minutes, $lang['minutes_ago']);
  else $time_ago_written_out = $lang['just_now'];
  return $time_ago_written_out;
 }

function get_geographic_coordinates($latitude, $longitude)
 {
  $lat_parts = explode('.', $latitude);
  $lon_parts = explode('.', $longitude);
  $lat_deg = $lat_parts[0];
  if($lat_deg<0) $lat_hemisphere = 'S';
  else $lat_hemisphere = 'N';
  $lat_deg = abs($lat_deg);
  $lat_tempma = '0.'.$lat_parts[1];
  $lat_tempma = $lat_tempma * 3600;
  $lat_min = floor($lat_tempma / 60);
  $lat_sec = number_format($lat_tempma - ($lat_min*60), 2);
  if($lat_sec==60)
   {
    $lat_min = $lat_min + 1;
    $lat_sec = 0;
   }
  if($lat_min==60)
   {
    $lat_deg = $lat_deg + 1;
    $lat_min = 0;
   }
  $lon_deg = $lon_parts[0];
  if($lon_deg<0) $lon_hemisphere = 'W';
  else $lon_hemisphere = 'E';
  $lon_deg = abs($lon_deg);  
  $lon_tempma = '0.'.$lon_parts[1];
  $lon_tempma = $lon_tempma * 3600;
  $lon_min = floor($lon_tempma / 60);
  $lon_sec = number_format($lon_tempma - ($lon_min*60), 2);
  if($lon_sec==60)
   {
    $lon_min = $lon_min + 1;
    $lon_sec = 0;
   }
  if($lon_min==60)
   {
    $lon_deg = $lon_deg + 1;
    $lon_min = 0;
   }
  return $lat_deg.'° '.$lat_min.'′ '.$lat_sec.'″ '.$lat_hemisphere.', '.$lon_deg.'° '.$lon_min.'′ '.$lon_sec.'″ '.$lon_hemisphere;
}    

/**
 * converts exif coordinates in decimal coordinates
 */ 
function geotag2Num($exifCoord, $hemisphere)
 {
  $degrees = count($exifCoord) > 0 ? convertGeotagCoordinatePart($exifCoord[0]) : 0;
  $minutes = count($exifCoord) > 1 ? convertGeotagCoordinatePart($exifCoord[1]) : 0;
  $seconds = count($exifCoord) > 2 ? convertGeotagCoordinatePart($exifCoord[2]) : 0;
  $flip = ($hemisphere == 'W' or $hemisphere == 'S') ? -1 : 1;
  return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
 }

/**
 * converts exif GPSTimeStamp to hh:mm:ss
 * TODO: only correct if GPSTimeStamp is like this: hh/1, mm/1, ss/1 
 */ 
function GPSTimeStamp2Time($exifCoord)
 {
  $hours = count($exifCoord) > 0 ? convertGeotagCoordinatePart($exifCoord[0]) : 0;
  $minutes = count($exifCoord) > 1 ? convertGeotagCoordinatePart($exifCoord[1]) : 0;
  $seconds = count($exifCoord) > 2 ? convertGeotagCoordinatePart($exifCoord[2]) : 0;
  return number_format($hours,0) .':'. number_format($minutes,0) .':'. number_format($seconds,0);
 }

/**
 * helper function for geotag2Num
 */ 
function convertGeotagCoordinatePart($coordPart)
 {
  $parts = explode('/', $coordPart);
  if(count($parts) <= 0) return 0;
  if (count($parts) == 1) return $parts[0];
  return floatval($parts[0]) / floatval($parts[1]);
 }

/**
 * gets latitude/longitude geotag information of an image
 */ 
function my_exif_read_data($file)
 {
  if(file_exists($file))
   {
    $imageinfo = getimagesize($file);
    if($imageinfo[2]==IMAGETYPE_JPEG || $imageinfo[2]==IMAGETYPE_JPEG2000) $exif_data = exif_read_data($file);
    else return false;

    $converted_exif_data['Orientation'] = isset($exif_data['Orientation']) ? intval($exif_data['Orientation']) : 1;

    $dateTimeOriginal = false;
    if(isset($exif_data['DateTimeOriginal']) && strlen($exif_data['DateTimeOriginal']>9)) // 2013:04:09 13:57:46
     {
      $_datetime = substr_replace($exif_data['DateTimeOriginal'], '-', 4, 1);
      $_datetime = substr_replace($_datetime, '-', 7, 1);
      if($datetime = date_create($_datetime)) $converted_exif_data['datetimeoriginal'] = date_format($datetime, 'Y-m-d H:i:s');
     }   

    if(isset($exif_data['GPSDateStamp']) && isset($exif_data['GPSTimeStamp']))
     {
      $GPSDateStamp = substr_replace($exif_data['GPSDateStamp'], '-', 4, 1);
      $GPSDateStamp = substr_replace($GPSDateStamp, '-', 7, 1);
      $_gpsdatetime = $GPSDateStamp . ' ' . GPSTimeStamp2Time($exif_data['GPSTimeStamp']);
      if($gpsdatetime = date_create($_gpsdatetime)) $converted_exif_data['gpsdatetime'] = date_format($gpsdatetime, 'Y-m-d H:i:s');
     }   
    
    if(isset($exif_data['GPSLongitude']) && isset($exif_data['GPSLongitudeRef']) && isset($exif_data['GPSLatitude'])  && isset($exif_data['GPSLatitudeRef']))
     {
      $converted_exif_data['longitude'] = geotag2Num($exif_data['GPSLongitude'], $exif_data['GPSLongitudeRef']);
      $converted_exif_data['latitude'] = geotag2Num($exif_data['GPSLatitude'], $exif_data['GPSLatitudeRef']);
     } 
   }
  if(isset($converted_exif_data)) return $converted_exif_data;
  return false;
 }


function log_status($message, $action=0, $table=0, $item=0)
 {
  global $_SESSION, $settings;
  if(!is_null($message)) $message = truncate(trim($message), 500);
  if(is_null($message) || $message!='')
   {
    $dbr = Database::$connection->prepare("INSERT INTO ".Database::$db_settings['status_table']." (\"user\", action, \"table\", item, message) VALUES (:user, :action, :table, :item, :message)");
    $dbr->bindValue(':user', $_SESSION[$settings['session_prefix'].'auth']['id'], PDO::PARAM_INT);
    $dbr->bindValue(':action', $action, PDO::PARAM_INT);
    $dbr->bindValue(':table', $table, PDO::PARAM_INT); 
    $dbr->bindValue(':item', $item, PDO::PARAM_INT); 
    $dbr->bindValue(':message', $message, PDO::PARAM_STR);
    $dbr->execute();
   }
 }

function log_error($file, $message)
 {
  $time = date(DATE_RFC822);
  $log_message = "\n\n########################################################\n\n" . $time . "\n\n" . print_r($_SERVER, true) . "\n\n" . print_r($_REQUEST, true) . "\n\n" . print_r($_SESSION, true) . "\n\n" . $message;
  file_put_contents($file, $log_message, FILE_APPEND | LOCK_EX);
 }

/**
 * shortens links
 *
 * @param string $string
 * @return string
 */
function shorten_link($string)
 {
  global $settings;
  $maxlength = 35;
  if(is_array($string))
   {
    if(count($string) == 2) { $pre = ""; $url = $string[1]; }
    else { $pre = $string[1]; $url = $string[2]; }
    $shortened_url = $url;
    if (strlen($url) > $maxlength) $shortened_url = mb_substr($url, 0, $maxlength-3, CHARSET) . '…';
    return $pre.'<a href="'.$url.'">'.$shortened_url.'</a>';
   }
 }


/**
 * replaces urls with links
 *
 * @param string $string
 * @return string
 */
function make_link($string)
 {
  $string = ' ' . $string;
  $string = preg_replace_callback("#(^|[\n ])([\w]+?://.*?[^ \"\n\r\t<]*)#is", "shorten_link", $string);
  #$string = preg_replace("#(^|[\n ])((www|ftp)\.[\w\-]+\.[\w\-.\~]+(?:/[^ \"\t\n\r<]*)?)#is", "$1<a href=\"http://$2\">$2</a>", $string);
  #$string = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $string);
  $string = mb_substr($string, 1, my_strlen($string, CHARSET), CHARSET);
  return $string;
 }

/**
 * returns an array for the page navigation
 *
 * @param int $page_count : number of pages
 * @param int $page : current page
 * @param int $browse_range
 * @param int $page
 * @param int $show_last
 * @return array
 */
function pagination($page_count,$page,$browse_range=3,$show_last=true)
 {
  if($page_count>1)
   {
    $xpagination['current'] = $page;
    if($page_count > $page)
     {
      $xpagination['next'] = $page+1;
     }
    else
     {
      $xpagination['next'] = 0;
     }
    if($page > 1)
     {
      $xpagination['previous'] = $page-1;
     }
    else
     {
      $xpagination['previous'] = 0;
     }
    $xpagination['items'][] = 1;
    if ($page > $browse_range+1) $xpagination['items'][] = 0;
    $n_range = $page-($browse_range-1);
    $p_range = $page+$browse_range;
    for($page_browse=$n_range; $page_browse<$p_range; $page_browse++)
     {
      if($page_browse > 1 && $page_browse <= $page_count) $xpagination['items'][] = $page_browse;
     }
    if($show_last)
     {
      if($page < $page_count-($browse_range)) $xpagination['items'][] = 0;
      if(!in_array($page_count,$xpagination['items'])) $xpagination['items'][] = $page_count;
     }
    return $xpagination;
   }
  return false;
 }


function trim_array($array)
 {
  foreach($array as $item)
   {
    if(trim($item)!='') $cleared_array[] = trim($item);
   }
  if(isset($cleared_array)) return $cleared_array;
  else return array();
 }


function string2url($string)
 {
  $characters = array(' ','á','Á','é','É','í','Í','ó','Ó','ú','Ú','ñ','Ñ','ü','Ü');
  $replacements = array('-','a','A','e','E','i','I','o','O','u','U','n','N','u','U');  
  $string = str_replace($characters, $replacements, $string);
  $string = preg_replace("/[^a-zA-Z0-9-_]/", '', $string);
  $string = strtolower($string);
  return $string; 
 }

function deleteDirectory($dir)
 {
  if (!file_exists($dir)) return true;
  if (!is_dir($dir)) return unlink($dir);
  foreach (scandir($dir) as $item)
   {
    if($item == '.' || $item == '..') continue;
    if(!deleteDirectory($dir.DIRECTORY_SEPARATOR.$item)) return false;
   }
  return rmdir($dir);
 }

/**
 * checks password comparing it with the hash
 *
 * @param string $pw
 * @param string $hash
 * @return bool 
 */ 
function check_pw($pw,$hash)
 {
  $salted_hash = substr($hash,0,40);
  $salt = substr($hash,40,10);
  if(sha1($pw.$salt)==$salted_hash) return true;
  return false;
 } 

/**
 * generates a random string
 *
 * @param int $length
 * @param string $characters 
 * @return string 
 */ 
function random_string($length=8, $characters='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
 {
  $random_string = '';
  $characters_length = strlen($characters);
  for($i=0;$i<$length;$i++)
   {
    $random_string .= $characters[mt_rand(0, $characters_length - 1)];
   }
  return $random_string;
 } 

/**
 * generates password hash
 *
 * @param string $pw 
 * @return string 
 */ 
function generate_pw_hash($pw)
 {
  $salt = random_string(10,'0123456789abcdef');
  $salted_hash = sha1($pw.$salt);
  $hash_with_salt = $salted_hash.$salt;
  return $hash_with_salt;
 } 

/**
 * find position of first occurrence of string in a string using mb_strpos 
 * if available or strpos if not
 *
 * @param string $haystack
 * @param mixed $needle
 * @param int $offset
 * @param string $encoding  
 * @return string 
 */ 
function my_strpos($haystack, $needle, $offset=0, $encoding='utf-8')
 {
  if(function_exists('mb_strpos'))
   {
    return mb_strpos($haystack, $needle, $offset, $encoding); 
   }
  else
   {
    return strpos($haystack, $needle, $offset); 
   } 
 }

/**
 * returns string with all alphabetic characters converted to lowercase
 * using mb_strtolower if available or strtolower if not
 *
 * @param string $string
 * @param string $encoding
 * @return string 
 */ 
function my_strtolower($string, $encoding='utf-8')
 {
  if(function_exists('mb_strtolower'))
   {
    return mb_strtolower($string, $encoding); 
   }
  else
   {
    return strtolower($string); 
   } 
 }

/**
 * determine string length using mb_strlen if available or strlen if not
 *
 * @param string $string
 * @param string $encoding
 * @return int 
 */ 
function my_strlen($string, $encoding='utf-8')
 {
  if(function_exists('mb_strlen'))
   {
    return mb_strlen($string, $encoding); 
   }
  else
   {
    return strlen($string); 
   } 
 }

function my_checkdate($mydate)
 {
  if(trim($mydate)=='') return true;
  $date_parts = explode("-",$mydate);
  if(count($date_parts)!=3) return false;
  return checkdate(intval($date_parts[1]), intval($date_parts[2]), intval($date_parts[0]));
}

function validate_time($string)
 {
  $time_parts = explode(':', $string);
  if(count($time_parts)!=2&&count($time_parts)!=3) return false;
  if(intval($time_parts[0])<0||intval($time_parts[0])>23) return false;
  if(intval($time_parts[1])<0||intval($time_parts[1])>59) return false;
  if(isset($time_parts[2]))
   {
    if(intval($time_parts[2])<0||intval($time_parts[2])>59) return false;
   }
  if(intval($time_parts[0])<10) $hours = '0'.intval($time_parts[0]);
  else $hours = intval($time_parts[0]);
  if(intval($time_parts[1])<10) $minutes = '0'.intval($time_parts[1]);
  else $minutes = intval($time_parts[1]);
  if(isset($time_parts[2]))
   {
    if(intval($time_parts[2])<10) $seconds = '0'.intval($time_parts[2]);
    else $seconds = intval($time_parts[2]);
   }
  else $seconds = '00';
  $timestring = $hours.':'.$minutes.':'.$seconds;
  return $timestring; 
}

/**
 * function for the up/down buttons in the admin area in case JavaScript
 * isn't available   
 * 
 * @param string $table : name of database table
 * @param int $id : id of the item
 * @param string $direction : 'up' or 'down'    
 */ 
function move_item($table, $id, $direction, $section='')
 {
  if($direction=='up')
   {
    if($section!='')
     {
      $dbr = Database::$connection->prepare("SELECT id, ".$section.", sequence FROM ".$table." WHERE id=:id LIMIT 1");
      $query_add = ' AND '.$section.'=:section';
     }
    else
     {
      $dbr = Database::$connection->prepare("SELECT id, sequence FROM ".$table." WHERE id=:id LIMIT 1");
      $query_add = '';
     }
    $dbr->bindValue(':id',$id);
    $dbr->execute();
    $row = $dbr->fetch();
    if(!empty($row) && $row['sequence'] > 1)
     {
      $dbr = Database::$connection->prepare("UPDATE ".$table." SET sequence=:sequence_new WHERE sequence=:sequence".$query_add);
      $dbr->bindValue(':sequence_new',0);
      $dbr->bindValue(':sequence',$row['sequence']-1);
      if($section!='') $dbr->bindValue(':section',$row[$section]);
      $dbr->execute();
      $dbr = Database::$connection->prepare("UPDATE ".$table." SET sequence=sequence-1 WHERE sequence=:sequence".$query_add);      
      $dbr->bindValue(':sequence',$row['sequence']);
      if($section!='') $dbr->bindValue(':section',$row[$section]);
      $dbr->execute();      
      $dbr = Database::$connection->prepare("UPDATE ".$table." SET sequence=:sequence_new WHERE sequence=:sequence".$query_add);
      $dbr->bindValue(':sequence_new',$row['sequence']);
      $dbr->bindValue(':sequence',0);
      if($section!='') $dbr->bindValue(':section',$row[$section]);
      $dbr->execute();  
     }
   }
  else // down
   {
    if($section!='')
     {
      $dbr = Database::$connection->prepare("SELECT id, ".$section.", sequence FROM ".$table." WHERE id=:id LIMIT 1");
      $query_add = ' AND '.$section.'=:section';
     }
    else
     {
      $dbr = Database::$connection->prepare("SELECT id, sequence FROM ".$table." WHERE id=:id LIMIT 1");
      $query_add = '';
     }
    $dbr->bindValue(':id',$id);
    $dbr->execute();
    $row = $dbr->fetch();
    
    // how many items (in section)?
    if($section!='') $query_add_where = ' WHERE '.$section.'=:section';
    else $query_add_where = '';
    $sth = Database::$connection->prepare("SELECT count(*) as item_count FROM ".$table.$query_add_where);
    if($section!='') $sth->bindValue(':section',$row[$section]);
    $sth->execute();
    $count_result = $sth->fetch();          
    
    if(!empty($row) && $row['sequence'] < $count_result['item_count'])
     {
      $dbr = Database::$connection->prepare("UPDATE ".$table." SET sequence=:sequence_new WHERE sequence=:sequence".$query_add);
      $dbr->bindValue(':sequence_new',0);
      $dbr->bindValue(':sequence',$row['sequence']+1);
      if($section!='') $dbr->bindValue(':section',$row[$section]);
      $dbr->execute();
      $dbr = Database::$connection->prepare("UPDATE ".$table." SET sequence=sequence+1 WHERE sequence=:sequence".$query_add);      
      $dbr->bindValue(':sequence',$row['sequence']);
      if($section!='') $dbr->bindValue(':section',$row[$section]);
      $dbr->execute();      
      $dbr = Database::$connection->prepare("UPDATE ".$table." SET sequence=:sequence_new WHERE sequence=:sequence".$query_add);
      $dbr->bindValue(':sequence_new',$row['sequence']);
      $dbr->bindValue(':sequence',0);
      if($section!='') $dbr->bindValue(':section',$row[$section]);
      $dbr->execute();  
     }
   }   
 }


function get_table_info($table, $overview_only=false)
 {
    $dbr = Database::$connection->prepare("SELECT a.id,
                                                  a.table_name,
                                                  a.title,
                                                  a.type,
                                                  a.geometry_type,
                                                  a.geometry_required,
                                                  a.basemaps,
                                                  a.min_scale,
                                                  a.max_scale,
                                                  a.simplification_tolerance,
                                                  a.simplification_tolerance_extent_factor,
                                                  a.layer_overview,
                                                  a.status,
                                                  a.readonly,
                                                  a.parent_table,
                                                  a.description,
                                                  b.title as parent_title,
                                                  a.auxiliary_layer_1,
                                                  c.title as auxiliary_layer_1_title,
                                                  a.auxiliary_layer_2,
                                                  d.title as auxiliary_layer_2_title,
                                                  a.auxiliary_layer_3,
                                                  e.title as auxiliary_layer_3_title
                                           FROM ".Database::$db_settings['data_models_table']." AS a
                                           LEFT JOIN ".Database::$db_settings['data_models_table']." AS b ON a.parent_table=b.id
                                           LEFT JOIN ".Database::$db_settings['data_models_table']." AS c ON a.auxiliary_layer_1=c.id
                                           LEFT JOIN ".Database::$db_settings['data_models_table']." AS d ON a.auxiliary_layer_2=d.id
                                           LEFT JOIN ".Database::$db_settings['data_models_table']." AS e ON a.auxiliary_layer_3=e.id
                                           WHERE a.id=:id
                                           LIMIT 1");
    $dbr->bindParam(':id', $table, PDO::PARAM_INT);
    $dbr->execute();
    #$table_data = $dbr->fetch();
    
    $table_data = $dbr->fetch();
    
    
    if(isset($table_data['id']))
     {
      $table_data['title'] = htmlspecialchars($table_data['title']);
      $table_data['parent_title'] = htmlspecialchars($table_data['parent_title']);
      //$table_data['description'] = $table_data['description'];
      if($table_data['basemaps']) $table_data['basemaps'] = explode(',', $table_data['basemaps']);
      else $table_data['basemaps']=false;
      // get table items:
      $dbr = Database::$connection->prepare("SELECT a.id,
                                                    a.name,
                                                    a.label,
                                                    a.description,
                                                    a.column_type,
                                                    a.column_length,
                                                    a.column_not_null,
                                                    a.input_type,
                                                    a.choices,
                                                    a.choice_labels,
                                                    a.relation,
                                                    c.table_id as relation_table,
                                                    b.table_name as relation_table_name,
                                                    b.title as relation_table_title,
                                                    c.name AS relation_column_name,
                                                    a.required,
                                                    a.overview,
                                                    a.section_type,
                                                    a.range_from,
                                                    a.range_to,
                                                    a.regex
                                             FROM ".Database::$db_settings['data_model_items_table']." AS a
                                             LEFT JOIN ".Database::$db_settings['data_model_items_table']." AS c ON a.relation=c.id
                                             LEFT JOIN ".Database::$db_settings['data_models_table']." AS b ON c.table_id=b.id
                                             WHERE a.table_id = :table_id
                                             ORDER BY a.sequence ASC");
       $dbr->bindParam(':table_id', $table_data['id'], PDO::PARAM_INT);
       $dbr->execute();
       $i=0;
       #$name_column = false;
       foreach($dbr as $row)
        {
         #$columns[$i]['id'] = $row['id'];
         #if($overview_only && $row['overview']==1 && $row['column_type']>0 || !$overview_only)
         if(!$overview_only || ($overview_only && $row['column_type']>0))
          {
           $columns[$i]['id'] = $row['id'];
           $columns[$i]['name'] = $row['name'];
           if($row['label']) $columns[$i]['label'] = htmlspecialchars($row['label']);
           else $columns[$i]['label'] = htmlspecialchars($row['name']);
           $columns[$i]['description'] = htmlspecialchars($row['description']);
           $columns[$i]['type'] = $row['column_type'];
           $columns[$i]['section_type'] = $row['section_type'];
           $columns[$i]['range_from'] = $row['range_from'];
           $columns[$i]['range_to'] = $row['range_to'];
           $columns[$i]['regex'] = $row['regex'];

           if($row['column_type']==0 && $row['section_type']==1)
            {
             if(empty($section_id)) $section_id = 1; 
             $sections[$row['id']]['id'] = $section_id;
             $sections[$row['id']]['label'] = htmlspecialchars($row['label']);
             ++$section_id;
            }
           
           $columns[$i]['column_length'] = $row['column_length'];
           $columns[$i]['column_not_null'] = $row['column_not_null'];
           $columns[$i]['required'] = $row['required'];
           #$columns[$i]['input_type'] = $row['input_type'];
           #if($columns[$i]['input_type']==1 && empty($row['choices'])) $columns[$i]['choices'] = array(1);
           if(!empty($row['choices'])) $columns[$i]['choices'] = explode("\n", $row['choices']);
           else $columns[$i]['choices'] = array();
         
           $columns[$i]['relation_table'] = $row['relation_table'];
           $columns[$i]['relation_table_name'] = $row['relation_table_name'];
           $columns[$i]['relation_table_title'] = $row['relation_table_title'];
           $columns[$i]['relation'] = $row['relation'];
           $columns[$i]['relation_column_name'] = $row['relation_column_name'];

           if(trim($row['choice_labels'])!='') $choice_labels = explode("\n", $row['choice_labels']);
           else unset($choice_labels);
           
           $ii=0;
           foreach($columns[$i]['choices'] as $choice)
            {
             if(isset($choice_labels[$ii])) $columns[$i]['choice_labels'][$choice] = $choice_labels[$ii];
             else $columns[$i]['choice_labels'][$choice] = $choice;
             ++$ii;
            }

           ++$i;
          }
        } 
        
    }
   if($table_data) $table_info['table'] = $table_data;
   if(isset($columns)) $table_info['columns'] = $columns;
   if(isset($sections)) $table_info['sections'] = $sections;

   if(isset($table_info)) return $table_info;
   else return false;
 }




/**
 * returns the custom (user defined) columns of a database table
 * 
 * @param int $table : id of the database table
 * @param bool $overview_only : whether overview columns should be fetched only
 * @return array
 */ 
function get_custom_columns($table, $overview_only=false)
 {
  $dbr = Database::$connection->prepare("SELECT id,
                                                  name,
                                                  label,
                                                  column_type,
                                                  column_length,
                                                  required,
                                                  overview
                                           FROM ".Database::$db_settings['data_model_items_table']."
                                           WHERE table_id = :table_id AND column_type>0
                                           ORDER BY sequence ASC");
                                             
   $dbr->bindParam(':table_id', $table, PDO::PARAM_INT);
   $dbr->execute();
   $i=0;
   foreach($dbr as $row)
    {
     if(!$overview_only || $row['overview']==1)
      {  
       $columns[$i]['name'] = $row['name'];
       $columns[$i]['label'] = $row['label'];
       $columns[$i]['type'] = $row['column_type'];
       $columns[$i]['column_length'] = $row['column_length'];
      }  
     ++$i;
    }  
  
  if(isset($columns)) return $columns;
  else return false;
 
 }

/**
 * returns the SQL query part of the custom (user defined) columns
 * 
 * @param string $table_name : name of the database table
 * @param array $columns : custom columns 
 * @return string
 */ 
function get_custom_query_part($table_info, $query_mode=0)
 {
  $i=0;
  foreach($table_info['columns'] as $column)
   {
    #$query_parts[] = $table_name.'.'.$column['name'];
    
         if($column['type']>0) $query_parts[] = 'table'.$table_info['table']['id'].'.'.$column['name'];
         if($column['related_table'])
          {
           $joins[$i]['table'] = $table_info['table']['id'];
           $joins[$i]['alias'] = 'table'.$table_info['table']['id'].'_'.$i; // unique table alias
           $joins[$i]['related_table'] = $column['related_table'];
           $joins[$i]['related_table_name'] = $column['related_table_name'];
           $joins[$i]['related_column_name'] = $column['name'];
           $joins[$i]['fk'] = $column['name'];
           $query_parts[] = $joins[$i]['alias'].'.'.$column['related_column_name'].' AS '.$column['name'];
          }
    ++$i;
    
    
    
   }

  if(isset($query_parts))
   {
    switch($query_mode)
     {
      case 1:
       $custom_query_part = '...';
       break;
      default:
       $custom_query_part = ', ' . implode(', ', $query_parts);
     }
   }
  else $custom_query_part = '';
  return $custom_query_part;
 }

/**
 * returns attached data items
 * 
 * @param string $table_name : name of the database table
 * @param array $columns : custom columns 
 * @return string
 */ 
function get_attached_data_items($table_info, $fk)
 {
  global $lang;

  if(isset($table_info['columns']))
   { 
    $i=0;
    foreach($table_info['columns'] as $column)
     {
      if($column['type']>0) $query_parts[] = 'table'.$table_info['table']['id'].'.'.$column['name'];
      if($column['relation_table'])
       {
        $joins[$i]['table'] = $table_info['table']['id'];
        $joins[$i]['alias'] = 'table'.$table_info['table']['id'].'_'.$i; // unique table alias
        $joins[$i]['relation_table'] = $column['relation_table'];
        $joins[$i]['relation_table_name'] = $column['relation_table_name'];
        $joins[$i]['relation_column_name'] = $column['name'];
        $joins[$i]['fk'] = $column['name'];
        $query_parts[] = $joins[$i]['alias'].'.'.$column['relation_column_name'].' AS '.$column['name'];
       }
      ++$i;
     }
   }

  if(isset($query_parts)) $custom_query_part = ', ' . implode(', ', $query_parts);
  else $custom_query_part = '';

  $query = "SELECT table".$table_info['table']['id'].".id,
                       table".$table_info['table']['id'].".fk,
                       extract(epoch FROM table".$table_info['table']['id'].".created) as created_timestamp,
                       userdata_table_1.name as creator,
                       extract(epoch FROM table".$table_info['table']['id'].".last_edited) as last_edited_timestamp,
                       table".$table_info['table']['id'].".last_editor as last_editor,
                       userdata_table_2.name as last_editor_name";
  if($table_info['table']['type']==1) $query .= ", ST_AsText(table".$table_info['table']['id'].".geom) AS wkt, ST_Area(ST_GeogFromWKB(table".$table_info['table']['id'].".geom)) as area, ST_Perimeter(ST_GeogFromWKB(table".$table_info['table']['id'].".geom)) as perimeter";
  $query .= $custom_query_part;
  $query .= "\nFROM ".$table_info['table']['table_name']." AS table".$table_info['table']['id'];
  if(isset($joins))
   {
    foreach($joins as $join)
     {
      $query .= "\nLEFT JOIN ".$join['relation_table_name']." AS ".$join['alias']." ON table".$table_info['table']['id'].".".$join['fk']."=".$join['alias'].".id";
     }
   }                                       
    
  $query .= "\nLEFT JOIN ".Database::$db_settings['userdata_table']." AS userdata_table_1 ON userdata_table_1.id=table".$table_info['table']['id'].".creator";
  $query .= "\nLEFT JOIN ".Database::$db_settings['userdata_table']." AS userdata_table_2 ON userdata_table_2.id=table".$table_info['table']['id'].".last_editor";
                                              
  $query .= "\nWHERE table".$table_info['table']['id'].".fk=:fk ORDER BY id ASC";
      
  $dbr = Database::$connection->prepare($query);

  $dbr->bindParam(':fk', $fk, PDO::PARAM_INT);
  $dbr->execute();

     
  $i=0;
  foreach($dbr as $row) 
   {
    // default columns:
    $data_items[$i]['id'] = intval($row['id']);
    $data_items[$i]['creator'] = htmlspecialchars($row['creator']);
    $data_items[$i]['created'] = htmlspecialchars(strftime($lang['time_format'], $row['created_timestamp']));
    if(!is_null($row['last_editor'])) 
     {
      $data_items[$i]['last_editor'] = htmlspecialchars($row['last_editor_name']);
      $data_items[$i]['last_edited'] = htmlspecialchars(strftime($lang['time_format'], $row['last_edited_timestamp']));
     }
    // spatial data columns:
    if($table_info['table']['type']==1)
     {
      $data_items[$i]['wkt'] = $row['wkt'];
      $data_items[$i]['area'] = $row['area'];
      $data_items[$i]['area_sqm'] = number_format($row['area'], 1, $lang['dec_point'], $lang['thousands_sep']);
      $data_items[$i]['area_ha'] = number_format($row['area']/10000, 1, $lang['dec_point'], $lang['thousands_sep']);
      $data_items[$i]['perimeter'] = number_format($row['perimeter'], 1, $lang['dec_point'], $lang['thousands_sep']);
     }                    
    // custom columns:
    if(isset($table_info['columns']))
     {
      foreach($table_info['columns'] as $column)
       {
        // first custom column as feature label: 
        if($table_info['table']['type']==1 && empty($data_items[$i]['_featurelabel_'])) $data_items[$i]['_featurelabel_'] = htmlspecialchars($row[$column['name']]);
        $data_items[$i][$column['name']] = htmlspecialchars($row[$column['name']]);
       }
     }
     ++$i;
   }
  
  
  
  
  if(isset($data_items)) return $data_items;
  else return false;
  
 }

function get_attached_items_info($table_name, $fk, $type)
 {
  global $lang;
  if($type==1)
   {
    $dbr = Database::$connection->prepare("SELECT COUNT(*) as nr_of_items,
                                                  SUM(ST_Area(ST_GeogFromWKB(geom))) as total_area
                                                  FROM ".$table_name."
                                                  WHERE fk=:fk");
   }
  else
   {
    $dbr = Database::$connection->prepare("SELECT COUNT(*) as nr_of_items
                                                  FROM ".$table_name."
                                                  WHERE fk=:fk");
   }
  $dbr->bindParam(':fk', $fk, PDO::PARAM_INT);
  $dbr->execute();
  $row = $dbr->fetch();
  $attached_items_info['nr_of_items'] = $row['nr_of_items'];
  if($type==1)
   {
    $attached_items_info['total_area'] = $row['total_area'];
    $attached_items_info['total_area_sqm'] = number_format($row['total_area'], 1, $lang['dec_point'], $lang['thousands_sep']);
    $attached_items_info['total_area_ha'] = number_format($row['total_area']/10000, 1, $lang['dec_point'], $lang['thousands_sep']);
   }
  return $attached_items_info;
 }



/**
 * returns related data items
 * 
 * @param string $table_name : name of the database table

 * @param array $columns : custom columns 
 * @return string
 */ 
function get_related_data_items($table_info, $ids)
 {
  global $lang;
  
  $ids_query = implode(', ', $ids);
  
  if(isset($table_info['columns']))
   {
     $i=0;
     foreach($table_info['columns'] as $column)
      {
       if($column['type']>0) $query_parts[] = 'table'.$table_info['table']['id'].'.'.$column['name'];
       if($column['relation_table'])
        {
         $joins[$i]['table'] = $table_info['table']['id'];
         $joins[$i]['alias'] = 'table'.$table_info['table']['id'].'_'.$i; // unique table alias
         $joins[$i]['relation_table'] = $column['relation_table'];
         $joins[$i]['relation_table_name'] = $column['relation_table_name'];
         $joins[$i]['relation_column_name'] = $column['name'];
         $joins[$i]['fk'] = $column['name'];
         $query_parts[] = $joins[$i]['alias'].'.'.$column['relation_column_name'].' AS '.$column['name'];
        }
       ++$i;
      }
   }


      if(isset($query_parts)) $custom_query_part = ', ' . implode(', ', $query_parts);
      else $custom_query_part = '';
      
      $query = "SELECT table".$table_info['table']['id'].".id,
                       table".$table_info['table']['id'].".fk,
                       extract(epoch FROM table".$table_info['table']['id'].".created) as created_timestamp,
                       userdata_table_1.name as creator,
                       extract(epoch FROM table".$table_info['table']['id'].".last_edited) as last_edited_timestamp,
                       table".$table_info['table']['id'].".last_editor as last_editor,
                       userdata_table_2.name as last_editor_name";
      if($table_info['table']['type']==1) $query .= ", ST_AsText(table".$table_info['table']['id'].".geom) AS wkt, ST_Area(ST_GeogFromWKB(table".$table_info['table']['id'].".geom)) as area, ST_Perimeter(ST_GeogFromWKB(table".$table_info['table']['id'].".geom)) as perimeter";
      $query .= $custom_query_part;
      $query .= "\nFROM ".$table_info['table']['table_name']." AS table".$table_info['table']['id'];
      if(isset($joins))
       {
        foreach($joins as $join)
         {
          $query .= "\nLEFT JOIN ".$join['relation_table_name']." AS ".$join['alias']." ON table".$table_info['table']['id'].".".$join['fk']."=".$join['alias'].".id";
         }
       }                                       
      
      $query .= "\nLEFT JOIN ".Database::$db_settings['userdata_table']." AS userdata_table_1 ON userdata_table_1.id=table".$table_info['table']['id'].".creator";
      $query .= "\nLEFT JOIN ".Database::$db_settings['userdata_table']." AS userdata_table_2 ON userdata_table_2.id=table".$table_info['table']['id'].".last_editor";
                                              
      $query .= "\nWHERE table".$table_info['table']['id'].".id IN (".$ids_query.") ORDER BY id ASC";
                                                
      $dbr = Database::$connection->prepare($query);
      $dbr->execute();
  
  
     
  $i=0;
  foreach($dbr as $row) 
   {
    // default columns:
    $data_items[$i]['id'] = intval($row['id']);
    $data_items[$i]['creator'] = htmlspecialchars($row['creator']);
    $data_items[$i]['created'] = htmlspecialchars(strftime($lang['time_format'], $row['created_timestamp']));
    if(!is_null($row['last_editor'])) 
     {
      $data_items[$i]['last_editor'] = htmlspecialchars($row['last_editor_name']);
      $data_items[$i]['last_edited'] = htmlspecialchars(strftime($lang['time_format'], $row['last_edited_timestamp']));
     }
    // spatial data columns:
    if($table_info['table']['type']==1)
     {
      $data_items[$i]['wkt'] = $row['wkt'];
      $data_items[$i]['area_sqm'] = number_format($row['area'], 1, $lang['dec_point'], $lang['thousands_sep']);
      $data_items[$i]['area_ha'] = number_format($row['area']/10000, 1, $lang['dec_point'], $lang['thousands_sep']);
      $data_items[$i]['perimeter'] = number_format($row['perimeter'], 1, $lang['dec_point'], $lang['thousands_sep']);
     }                    
    // custom columns:
    if(isset($table_info['columns']))
     {
      foreach($table_info['columns'] as $column)
       {
        // first custom column as feature label: 
        if($table_info['table']['type']==1 && empty($data_items[$i]['_featurelabel_'])) $data_items[$i]['_featurelabel_'] = htmlspecialchars($row[$column['name']]);
        $data_items[$i][$column['name']] = htmlspecialchars($row[$column['name']]);
       }
     }
     ++$i;
   }
  if(isset($data_items)) return $data_items;
  else return false;
  
 }

function get_related_items_info($table_name, $ids, $type)
 {
  global $lang;
  $ids_query = implode(', ', $ids);
  if($type==1)
   {
    $dbr = Database::$connection->prepare("SELECT COUNT(*) as nr_of_items,
                                                  SUM(ST_Area(ST_GeogFromWKB(geom))) as total_area
                                                  FROM ".$table_name."
                                                  WHERE id IN (".$ids_query.")");
   }
  else
   {
    $dbr = Database::$connection->prepare("SELECT COUNT(*) as nr_of_items
                                                  FROM ".$table_name."
                                                  WHERE id IN (".$ids_query.")");
   }
  #$dbr->bindParam(':fk', $fk, PDO::PARAM_INT);
  $dbr->execute();
  $row = $dbr->fetch();
  $related_items_info['nr_of_items'] = $row['nr_of_items'];
  if($type==1)
   {
    $related_items_info['total_area_sqm'] = number_format($row['total_area'], 1, $lang['dec_point'], $lang['thousands_sep']);
    $related_items_info['total_area_ha'] = number_format($row['total_area']/10000, 1, $lang['dec_point'], $lang['thousands_sep']);
   }
  return $related_items_info;
 }



function get_uploaded_file($source, $path)
 {
  $tmp_name = uniqid(rand()).'.tmp';
  if(move_uploaded_file($source,$path.$tmp_name))
   {
    return $tmp_name;
   }
  else
   {
    return false;
   }  
 }

function create_image($source_file, $target_file, $max_size, $max_width, $max_height, $path)

 {
  global $settings;
  $image_info = getimagesize($path.$source_file);
  
  $width=$image_info[0];
  $height=$image_info[1];
  
  if(filesize($path.$source_file) > $max_size*1000 || $image_info[0] > $max_width || $image_info[1] > $max_height)
   {     
    // resize:
    if($width > $max_width || $height > $max_height)
     {
      if($width >= $height)
       {
        $new_width = $max_width;
        $new_height = intval($height*$new_width/$width);
       }
      else
       {
        $new_height = $max_height;
        $new_width = intval($width*$new_height/$height);
       }
     }        
    else
     {
      $new_width = $width;
      $new_height = $height;
     }

    for($compression = 100; $compression>1; $compression=$compression-10)
     {
      if(!resize_image($path.$source_file, $path.$target_file, $new_width, $new_height, $compression)) 
       { 
        $file_size = filesize($path.$target_file);
        break; 
       }
      $file_size = filesize($path.$target_file);
      if($image_info[2]!=2 && $file_size > $max_size*1000) break;
      if($file_size <= $max_size*1000) break;
     }
    if($file_size > $max_size*1000) 
     {
      $errors[] = 'file_too_large';
     } 
    if(isset($errors))
     {
      if(file_exists($path.$target_file))
       {
        #@chmod($path.$img_tmp_name, 0777);
        @unlink($path.$target_file);
       }
     }
   }
  else
   {
    copy($path.$source_file, $path.$target_file) or $errors[] = 'copy_error';
   }
  
  if(empty($errors))
   {
    #@chmod($path.$filename, 0644);
    return true;
   }
  else
   {
    return false;
   }
 }


function my_floatval($string)
 {
  return floatval(str_replace(',','.',$string));
 }

function cut_last_zero($string)
 {
  if(substr($string, -1)=='0') $string = substr($string, 0, -1); 
  return $string;  
 }

function showme($what)
 {
  echo '<pre>';
  print_r($what);
  echo '</pre>';
  exit;
 }
?>
