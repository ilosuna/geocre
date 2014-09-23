<?php
$db_settings['type'] =                     'postgresql';

$db_settings['host'] =                     'localhost';
$db_settings['port'] =                     5432;
$db_settings['database'] =                 'YOUR_DATABASE';
$db_settings['user'] =                     'YOUR_DATABASE_USER';
$db_settings['password'] =                 'YOUR_PASSWORD';

/* only required for online backup functionality: */
$db_settings['backup_path'] =              ''; // e.g. /var/www/geocre/backup/
$db_settings['superuser'] =                '';
$db_settings['superuser_password'] =       '';

$db_settings['data_models_table'] =        'geocre_data_models';
$db_settings['data_model_items_table'] =   'geocre_data_model_items';
$db_settings['settings_table'] =           'geocre_settings';
$db_settings['userdata_table'] =           'geocre_users';
$db_settings['group_table'] =              'geocre_groups';
$db_settings['group_permissions_table'] =  'geocre_group_permissions';
$db_settings['group_memberships_table'] =  'geocre_group_memberships';
$db_settings['data_images_table'] =        'geocre_data_images';
$db_settings['page_photos_table'] =        'geocre_page_photos';
$db_settings['pages_table'] =              'geocre_pages';
$db_settings['db_table_relations_table'] = 'geocre_table_relations';
$db_settings['relations_table'] =          'geocre_relations';
$db_settings['basemaps_table'] =           'geocre_basemaps';
$db_settings['log_table'] =                'geocre_log';
$db_settings['photo_table'] =              'geocre_photos';
?>
