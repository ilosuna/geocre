<?php
class Permission
 {
  // permission types:
  const ADMIN = 100;
  const USERS_GROUPS = 90;
  const PAGE_MANAGEMENT = 80;
  const DATA_MANAGEMENT = 70;
  const DATA_ACCESS = 60;
  const PHOTOS = 50;
  const USER = 0;
  
  // permission levels:
  const READ = 10;
  const WRITE = 20;
  const MANAGE = 30;
   
  public $permissions = false;
  public $user = 0;
  
  public function __construct($user=false)
   {
    if($user)
     {
      $this->user=$user;
      $this->permissions[self::USER][0] = 0; // basic user permission
      $dbr = Database::$connection->prepare("SELECT permissions.type,
                                                    permissions.item,
                                                    permissions.level
                                             FROM ".Database::$db_settings['group_memberships_table']." AS memberships
                                             JOIN ".Database::$db_settings['group_permissions_table']." AS permissions ON memberships.\"group\"=permissions.group
                                             WHERE memberships.user=:id
                                             ORDER BY permissions.type DESC");
      $dbr->bindParam(':id', $user);
      $dbr->execute();
      while($row = $dbr->fetch())
       {
        if(isset($this->permissions[$row['type']][$row['item']]))
         {
          // overwrite permission if level is lower:
          if($this->permissions[$row['type']][$row['item']]<$row['level']) $this->permissions[$row['type']][$row['item']] = $row['level'];
         }  
        else
         {
          $this->permissions[$row['type']][$row['item']] = $row['level'];
         }
       }
     } 
   }

  public function granted($type, $item=0, $level=0)
   {

    if(isset($this->permissions[self::ADMIN])) return true; // admin has always permission
    if(isset($this->permissions[$type][$item]) && $this->permissions[$type][$item]>=$level) return true;
    return false;
   
    
   
   }
  
  public function get_list($type=false)
   {
    if($type)
     {
      if(isset($this->permissions[$type]))
       {
        foreach($this->permissions[$type] as $key=>$val)
         {
          $list_items[] = $key;
         }
       } 
     }
    elseif($this->permissions)
     {
      foreach($this->permissions as $key=>$val)
       {
        $list_items[] = $key;
       }      
     }
   
    if(isset($list_items)) return $list_items;
    return false;
   
   }
  
 }
?>
