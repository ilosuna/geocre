<h1><?php echo $lang['error_log_title']; ?></h1>

<?php if(isset($error_log_disabled)): ?>
<div class="alert alert-warning"><?php echo $lang['error_log_disabled_message']; ?></div>
<?php elseif(isset($error_log)): ?>

<table class="table table-striped table-hover">
<thead>
<tr>
<th><?php echo $lang['error_log_time_column_label']; ?></th>
<th><?php echo $lang['error_log_message_column_label']; ?></th>
<th><?php echo $lang['error_log_server_column_label']; ?></th>
<th><?php echo $lang['error_log_request_column_label']; ?></th>
<th><?php echo $lang['error_log_session_column_label']; ?></th>
</tr>
</thead>
<tbody>
<?php foreach($error_log as $item): ?>
<tr>
<td><?php echo $item['time']; ?></td>
<td>

<a href="#" data-toggle="modal" data-target="#message-<?php echo $item['id']; ?>"><?php echo $item['message_short']; ?></a>

<div class="modal fade" id="message-<?php echo $item['id']; ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo $lang['error_log_message_column_label']; ?></h4>
      </div>
      <div class="modal-body">
        <pre><?php echo $item['message']; ?></pre>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo $lang['ok']; ?></button>
      </div>
    </div>
  </div>
</div>

</td>
<td>

<a href="#" data-toggle="modal" data-target="#server-<?php echo $item['id']; ?>"><span class="glyphicon glyphicon-info-sign"></span></a>

<div class="modal fade" id="server-<?php echo $item['id']; ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo $lang['error_log_server_column_label']; ?></h4>
      </div>
      <div class="modal-body">
        <pre><?php echo $item['server']; ?></pre>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo $lang['ok']; ?></button>
      </div>
    </div>
  </div>
</div>

</td>
<td>

<a href="#" data-toggle="modal" data-target="#request-<?php echo $item['id']; ?>"><span class="glyphicon glyphicon-info-sign"></span></a>

<div class="modal fade" id="request-<?php echo $item['id']; ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo $lang['error_log_request_column_label']; ?></h4>
      </div>
      <div class="modal-body">
        <pre><?php echo $item['request']; ?></pre>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo $lang['ok']; ?></button>
      </div>
    </div>
  </div>
</div>

</td>
<td>

<a href="#" data-toggle="modal" data-target="#session-<?php echo $item['id']; ?>"><span class="glyphicon glyphicon-info-sign"></span></a>

<div class="modal fade" id="session-<?php echo $item['id']; ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo $lang['error_log_session_column_label']; ?></h4>
      </div>
      <div class="modal-body">
        <pre><?php echo $item['session']; ?></pre>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo $lang['ok']; ?></button>
      </div>
    </div>
  </div>
</div>

</td>
</td>
</tr>
<?php endforeach; ?>
<tbody>
</table>

<a class="btn btn-danger" href="<?php echo BASE_URL; ?>?r=error_log.clear" data-confirm="<?php echo rawurlencode($lang['error_log_clear_message']); ?>"><span class="glyphicon glyphicon-remove"></span> <?php echo $lang['error_log_clear_label']; ?></a>

<?php else: ?>
<div class="alert alert-warning"><?php echo $lang['error_log_empty_message']; ?></div>
<?php endif; ?>
