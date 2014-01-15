<?php
/* common table: */
$default_columns[0][0]['name'] = 'id';
$default_columns[0][0]['type'] = 'serial PRIMARY KEY';

$default_columns[0][1]['name'] = 'fk';
$default_columns[0][1]['type'] = 'integer';

$default_columns[0][2]['name'] = 'creator';
$default_columns[0][2]['type'] = 'integer NOT NULL';
$default_columns[0][2]['editable'] = false;

$default_columns[0][3]['name'] = 'created';
$default_columns[0][3]['type'] = 'timestamp NOT NULL';

$default_columns[0][4]['name'] = 'last_editor';
$default_columns[0][4]['type'] = 'integer NOT NULL';

$default_columns[0][5]['name'] = 'last_edited';
$default_columns[0][5]['type'] = 'timestamp NOT NULL';

/* spatial table: */
$default_columns[1][0]['name'] = 'id';
$default_columns[1][0]['type'] = 'serial PRIMARY KEY';

$default_columns[1][1]['name'] = 'fk';
$default_columns[1][1]['type'] = 'integer';

$default_columns[1][2]['name'] = 'creator';
$default_columns[1][2]['type'] = 'integer NOT NULL';

$default_columns[1][3]['name'] = 'created';
$default_columns[1][3]['type'] = 'timestamp NOT NULL';

$default_columns[1][4]['name'] = 'last_editor';
$default_columns[1][4]['type'] = 'integer NOT NULL';

$default_columns[1][5]['name'] = 'last_edited';
$default_columns[1][5]['type'] = 'timestamp NOT NULL';

$default_columns[1][8]['name'] = 'geom';
$default_columns[1][8]['type'] = 'geometry';

?>
