<?php
class Database
 {
  private static $_instance = null; 
  public static $db_settings;
  public static $connection;
  
  public function __construct()
   {
    self::$_instance = $this;
    require(BASE_PATH.'config/db_settings.conf.php');
    self::$db_settings = $db_settings;
    
    switch(self::$db_settings['type'])
     {
      case 'postgresql':
       self::$connection = new PDO('pgsql:host='.self::$db_settings['host'].';port='.self::$db_settings['port'].';dbname='.self::$db_settings['database'].';user='.self::$db_settings['user'].';password='.self::$db_settings['password']);
       self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       self::$connection->query("SET timezone='UTC'");
       define('LIST_TABLES_QUERY', 'SELECT relname as name FROM pg_stat_user_tables');
       define('LAST_INSERT_ID_QUERY', 'SELECT LASTVAL() AS insert_id');
       break;
     #case 'mysql': 
     # self::$connection = new PDO('mysql:host='.self::$db_settings['host'].';dbname='.self::$db_settings['database'], self::$db_settings['user'], self::$db_settings['password']);
     # self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     # self::$connection->query("set names utf8");
     # break;
    default:
       ?><p>Database type not supported.</p><?php
       exit;   
     }
   }

  public static function getInstance() 
   {
    return self::$_instance;
   }
 }
?>
