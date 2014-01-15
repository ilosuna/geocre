<?php
/**
 * geoCRE - a geographical collaborative research environment
 *
 * @author Mark Hoschek <mail at mark-hoschek dot de>
 * @copyright 2014 Mark Hoschek, Department of Physical Geography, University of Freiburg, Germany
 * @version 2014-01-15-1
 * @link http://geocre.net/
 */

try
{
define('IN_INDEX', TRUE);
define('BASE_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . '/');
session_start();

require(BASE_PATH.'lib/functions.php');
require(BASE_PATH.'config/db_settings.conf.php');

require(BASE_PATH.'lib/Database.class.php');
$database = new Database();
$settings = get_settings();

#define('BASE_PATH', get_base_path());
define('BASE_URL', get_base_url());
define('STATIC_URL', BASE_URL.'static/');

require(BASE_PATH.'config/definitions.conf.php');

require(BASE_PATH.'lib/Permission.class.php');
$permission = new Permission(isset($_SESSION[$settings['session_prefix'].'auth']['id']) ? $_SESSION[$settings['session_prefix'].'auth']['id'] : false);

if(isset($_SESSION[$settings['session_prefix'].'language']) && file_exists(BASE_PATH.'lang/'.$_SESSION[$settings['session_prefix'].'language'].'.lang.php')) require(BASE_PATH.'lang/'.$_SESSION[$settings['session_prefix'].'language'].'.lang.php');
else require(BASE_PATH.'lang/'.$settings['language'].'.lang.php');

if(isset($_SESSION[$settings['session_prefix'].'time_zone']))
 {
  if(!date_default_timezone_set($_SESSION[$settings['session_prefix'].'time_zone'])) date_default_timezone_set($settings['time_zone']);
 }
else date_default_timezone_set($settings['time_zone']);

define('CHARSET', $lang['charset']);
setlocale(LC_ALL, $lang['locale']);

$javascripts[] = JQUERY;
$javascripts[] = BOOTSTRAP;
$javascripts[] = STATIC_URL.'js/main.js';
if(isset($_SESSION[$settings['session_prefix'].'auth']))
 {
  //$stylesheets[] = STATIC_URL.'css/user.css';
  $javascripts[] = STATIC_URL.'js/user.js';
 }

require(BASE_PATH.'lib/Template.class.php');
$template = new Template();
#if($settings['help_enabled']) $template->assign('lang_help', $lang_help);
$template->assign('settings', $settings);

$template->assign('logged_in', $permission->granted(Permission::USER) ? true : false);
if(isset($_SESSION[$settings['session_prefix'].'auth'])) $template->assign('user_name', htmlspecialchars($_SESSION[$settings['session_prefix'].'auth']['name']));
$granted_permissions['admin'] = $permission->granted(Permission::ADMIN) ? true : false;
$granted_permissions['page_management'] = $permission->granted(Permission::PAGE_MANAGEMENT) ? true : false;
$granted_permissions['users_groups'] = $permission->granted(Permission::USERS_GROUPS) ? true : false;
$granted_permissions['data_management'] = $permission->granted(Permission::DATA_MANAGEMENT) ? true : false;
$template->assign('permission', $granted_permissions);
if($settings['backup_path'] && $db_settings['superuser'] && $db_settings['superuser_password']) $template->assign('backup_enabled', true);

// evaluate the request ( example.org/?r=foo.bar )
if(isset($_REQUEST['r'])) // default request
 {
  $r_parts = explode('.', $_REQUEST['r']); // split request
  if(isset($r_parts[1])) // foo.bar - controller = foo, action = bar
   {
    $controller = $r_parts[0];
    $action = $r_parts[1];
   }
  else // foo - controller = foo, action = default
   {
    $controller = $_REQUEST['r'];
    $action = 'default';
   }
 }
elseif(isset($_GET['page'])) // special page request (mod_rewrite)
 {
  $controller = 'page';
  $action = 'default';
 } 
elseif(empty($_REQUEST)) // no request, set default controller
 {
  $controller = 'default';
  $action = 'default';
 }

// overwrite the controller in case of maintenance:
if($settings['maintenance']==1 && $permission->granted(Permission::USER) && !$permission->granted(Permission::ADMIN) && $controller!='logout') $controller = 'maintenance';

if(isset($controller))
 {
  switch($controller)
   {
    case 'default':
       include(BASE_PATH.'controllers/'.$settings['default_controller']);
       break;
    case 'page': 
       include(BASE_PATH.'controllers/page.php');
       break;
    case 'dashboard': 
       include(BASE_PATH.'controllers/dashboard.php');
       break;
    case 'data': 
       include(BASE_PATH.'controllers/data.php');
       break;
    case 'edit_data_item': 
       include(BASE_PATH.'controllers/edit_data_item.php');
       break;
    case 'data_item': 
       include(BASE_PATH.'controllers/data_item.php');
       break;
    case 'login': 
       include(BASE_PATH.'controllers/login.php');
       break;
    case 'register': 
       include(BASE_PATH.'controllers/register.php');
       break;
    case 'logout': 
       include(BASE_PATH.'controllers/login.php');
       break;
    #case 'photos': 
    #   include(BASE_PATH.'controllers/photos.php');
    #   break;     
    case 'users':
       include(BASE_PATH.'controllers/users.php');
       break;
    case 'groups': 
       include(BASE_PATH.'controllers/groups.php');
       break;
    case 'profile': 
       include(BASE_PATH.'controllers/profile.php');
       break;
    case 'feedback': 
       include(BASE_PATH.'controllers/feedback.php');
       break;
    case 'reset_pw':
       include(BASE_PATH.'controllers/reset_pw.php');
       break;  
    case 'settings':
       include(BASE_PATH.'controllers/settings.php');
       break;
    case 'backup':
       include(BASE_PATH.'controllers/backup.php');
       break;
    case 'basemaps':
       include(BASE_PATH.'controllers/basemaps.php');
       break;       
    case 'data_model':
       include(BASE_PATH.'controllers/data_model.php');
       break;
    case 'relation':
       include(BASE_PATH.'controllers/relation.php');
       break;
    case 'data_relations':
       include(BASE_PATH.'controllers/data_relations.php');
       break;
    case 'maintenance':
       include(BASE_PATH.'controllers/maintenance.php');
       break;
    case 'download_data':
       include(BASE_PATH.'controllers/download_data.php');
       break;     
    case 'download_sheet':
       include(BASE_PATH.'controllers/download_sheet.php');
       break;     
    case 'activity': // asynchronous request processing
       include(BASE_PATH.'controllers/status.php');
       break;     
    case 'json_data':
       include(BASE_PATH.'controllers/json_data.php');
       break;     
    case 'arp': // asynchronous request processing
       include(BASE_PATH.'controllers/arp.php');
       break;        
    case 'help': // help
       include(BASE_PATH.'controllers/help.php');
       break;    
    default: 
       $invalid_request = true;
   }
 }

if(isset($_SESSION[$settings['session_prefix'].'auth']))
 {
  $template->assign('auth',$_SESSION[$settings['session_prefix'].'auth']);
 }

$template->assign('title', $settings['website_title']);

if(isset($stylesheets)) $template->assign('stylesheets', $stylesheets);
if(isset($javascripts)) $template->assign('javascripts', $javascripts);

if(empty($subtemplate) && empty($content)) $status = 404;
if(isset($http_status))
 {
  $template->assign('http_status', $http_status);
  switch($http_status)
   {
    case 403:
     $template->assign('subtitle', $lang['permission_denied_title']);
     header($_SERVER['SERVER_PROTOCOL'] . " 403 Forbidden");
     header("Status: 403 Forbidden");
     break;
    case 404:
     $template->assign('subtitle', $lang['invalid_request_title']);
     header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found");
     header("Status: 404 Not Found");
     break;
   } 
 }

// get menu items:
$dbr = Database::$connection->prepare("SELECT identifier, menu
                                       FROM ".Database::$db_settings['pages_table']."
                                       WHERE menu IS NOT NULL
                                       ORDER BY sequence ASC");
$dbr->execute();
$i=0;
while($row = $dbr->fetch()) 
 {
  $menu[$i]['page'] = $row['identifier'];
  $menu[$i]['label'] = $row['menu'];
  ++$i;
 }
if(isset($menu)) $template->assign('menu', $menu);


// display template:
$template->assign('lang', $lang);
header('Content-Type: text/html; charset='.$lang['charset']);
header("X-UA-Compatible: IE=Edge");
if(isset($page_template)) $template->display(BASE_PATH.'templates/'.$page_template);
else $template->display(BASE_PATH.'templates/'.$settings['default_template']);

} // try
catch(Exception $exception)
 {
  if(isset($lang)) $template->assign('lang', $lang);
  if(isset($settings['log_errors']) && $settings['log_errors']) log_error($settings['log_errors'], $exception);
  if(isset($settings['default_template']) && isset($lang))
   {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: text/html; charset='.$lang['charset']);
    if(isset($_SESSION[$settings['session_prefix'].'auth'])) $template->assign('auth',$_SESSION[$settings['session_prefix'].'auth']);    
    $template->assign('title', $settings['website_title']);
    $template->assign('subtitle', $lang['exception_subtitle']);
    $template->assign('exception', $exception);
    $template->assign('subtemplate', 'exception.inc.tpl');
    $template->display('templates/'.$settings['default_template']);
   }
  else
   {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: text/html; charset=utf-8');
    ?><!DOCTYPE html><html><head><title>Error</title></head><body><h1>Error!</h1><pre><?php echo $exception; ?></pre></body></html><?php
   }
 }
?>
