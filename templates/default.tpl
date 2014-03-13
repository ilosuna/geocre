<!DOCTYPE html>
<html lang="<?php echo $lang['language']; ?>" data-base-url="<?php echo BASE_URL; ?>" data-static-url="<?php echo STATIC_URL; ?>">
<head>
<meta charset="utf-8">
<title><?php if(isset($page_title)) echo $page_title; elseif(isset($subtitle)) echo $subtitle . ' - ' . $title; else echo $title; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?php echo $settings['description']; ?>">
<link href="<?php echo BOOTSTRAP_CSS; ?>" rel="stylesheet">
<?php /*<link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css" media="all" />*/ ?>
<link rel="stylesheet" type="text/css" href="<?php echo STATIC_URL; ?>css/style.css" media="all" />
<?php if(isset($stylesheets)): foreach($stylesheets as $stylesheet): ?>
<link rel="stylesheet" type="text/css" href="<?php echo $stylesheet; ?>" media="all" />
<?php endforeach; endif; ?>
<link rel="shortcut icon" href="<?php echo STATIC_URL; ?>img/favicon.png" />
</head>

<body>
<div id="wrapper">

<div class="navbar navbar-inverse navbar-fixed-top">
<div class="container">
<div class="navbar-header">
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="<?php echo BASE_URL; ?>"><?php echo $title; ?></a>
</div>
<div class="navbar-collapse collapse">


<?php if(isset($menu)): ?>
<ul class="nav navbar-nav">

<li class="dropdown">
<?php if($logged_in): ?>
<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-file"></span> <?php echo $lang['pages_label']; ?> <b class="caret"></b></a>
<ul class="dropdown-menu">
<?php endif; ?>
<?php foreach($menu as $menu_item): ?>
<li<?php if(isset($current_page)&&$current_page==$menu_item['page']): ?> class="active"<?php endif; ?>><a href="<?php echo BASE_URL.$menu_item['page']; ?>"><?php echo $menu_item['label']; ?></a></li>
<?php endforeach; ?>
<?php if($logged_in): ?>
</ul>
<?php endif; ?>
</ul>
<?php endif; ?>



<?php if($logged_in): ?>
<ul class="nav navbar-nav">
<li<?php if(isset($active) && $active=='dashboard'): ?> class="active"<?php endif; ?>><a href="<?php echo BASE_URL; ?>?r=dashboard" title="<?php echo $lang['dashboard_title']; ?>"><span class="glyphicon glyphicon-list-alt"></span> <?php echo $lang['dashboard_link']; ?></a></li>
</ul>
<ul class="nav navbar-nav navbar-right">
<?php if($permission['admin']||$permission['users_groups']||$permission['page_management']): ?>
<li class="dropdown<?php if(isset($active) && $active=='admin'): ?> active"<?php endif; ?>">
<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span> <?php echo $lang['admin_label']; ?> <b class="caret"></b></a>
<ul class="dropdown-menu">
<?php if($permission['users_groups']): ?>
<li><a href="<?php echo BASE_URL; ?>?r=users" class="users"><span class="glyphicon glyphicon-user"></span> <?php echo $lang['users_and_groups_link']; ?></a></li>
<?php endif; ?>
<?php if($permission['page_management']): ?>
<li><a class="page_overview" href="<?php echo BASE_URL; ?>?r=page.overview"><span class="glyphicon glyphicon-file"> </span><?php echo $lang['page_overview_link']; ?></a></li>
<?php endif; ?>
<?php if($permission['admin']): ?>
<li><a href="<?php echo BASE_URL; ?>?r=basemaps"><span class="glyphicon glyphicon-globe"></span> <?php echo $lang['basemaps_link']; ?></a></li>
<?php if(isset($backup_enabled)): ?>
<li><a href="<?php echo BASE_URL; ?>?r=backup"><span class="glyphicon glyphicon-save"></span> <?php echo $lang['backup_link']; ?></a></li>
<?php endif; ?>
<?php /*
<li><a href="<?php echo BASE_URL; ?>?r=backup" class="backup"><span class="glyphicon glyphicon-save"></span> <?php echo $lang['backup_link']; ?></a></li>
*/ ?>
<li><a href="<?php echo BASE_URL; ?>?r=settings" class="settings"><span class="glyphicon glyphicon-wrench"></span> <?php echo $lang['settings_link']; ?></a></li>
<?php endif; ?>
</ul>
</li>
<?php endif; ?>
<li<?php if(isset($active) && $active=='profile'): ?> class="active"<?php endif; ?>><a href="<?php echo BASE_URL; ?>?r=profile"><span class="glyphicon glyphicon-user"></span> <?php echo $user_name; ?></a></li>
<li><a href="<?php echo BASE_URL; ?>?r=logout"><span class="glyphicon glyphicon-off"></span> <?php echo $lang['logout_link']; ?></a></li>
</ul>
<?php else: ?>  
<ul class="nav navbar-nav navbar-right">

<li<?php if(isset($active) && $active=='login'): ?> class="active"<?php endif; ?>><a href="<?php echo BASE_URL; ?>?r=login"><span class="glyphicon glyphicon-user"></span> <?php echo $lang['login_link']; ?></a></li>
</ul>
<?php endif; ?>
</div><?php /* .nav-collapse */ ?>
</div>
</div>

<div class="container">
<?php if(isset($subtemplate)): ?>
<?php include(BASE_PATH.'templates/subtemplates/'.$subtemplate); ?>
<?php elseif(isset($content)): ?>
<?php echo $content; ?>
<?php else: ?>
<?php if(isset($http_status) && $http_status==403): ?>
<div class="alert alert-danger alert-box">
<h1><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $lang['permission_denied_title']; ?></h1>
<p><?php echo $lang['permission_denied_text']; ?></p>
</div>
<?php else: ?>
<div class="alert alert-danger">
<h1 class="caution"><?php echo $lang['invalid_request_title']; ?></h1>
<p><?php echo $lang['invalid_request_text']; ?></p>
</div>
<?php endif; ?>
<?php endif; ?>

<footer>
<?php echo $settings['footer']; ?>
</footer>

</div><?php /* container */ ?>

<?php if(isset($help)): ?>
<div class="modal fade" id="modal_help" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
</div>
</div>
</div>
<?php endif; ?>

<?php if(isset($javascripts)): foreach($javascripts as $javascript): ?>
<script type="text/javascript" src="<?php echo $javascript; ?>"></script>
<?php endforeach; endif; ?>
<?php if(isset($js)): ?>
<script>
<?php foreach($js as $js_item): ?>
<?php echo $js_item."\n"; ?>
<?php endforeach; ?>
</script>
<?php endif; ?>

</div>
</body>
</html>
