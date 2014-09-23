<?php  
$help['default']['title'] = 'Help not available';
$help['default']['content'] = <<<EOD
<p>Sorry, help not available!</p>
EOD;

$help['default']['title'] = 'Dashboard';
$help['default']['content'] = <<<EOD
<p>The dashboard is your data and task overview. Some basic information about
the data you have access to is listed here. Click on a data stock title to get
more information about a data stock.</p>
EOD;

$help['data_common']['title'] = 'Data records overview';
$help['data_common']['content'] = <<<EOD
<p>This page shows an overview of the data stock. By default the data is ordered 
descending by the time of the data record creation (newest entry on top). By 
clicking on a column header you can order the data by that column and change the 
sort sequence with another click.</p>
EOD;

$help['data_common_child']['title'] = 'Data records overview (child data)';
$help['data_common_child']['content'] = <<<EOD
<p>This page shows an overview of the data stock. By default the data is ordered 
descending by the time of the data record creation (newest entry on top). By 
clicking on a column header you can order the data by that column and change the 
sort sequence with another click.</p>
<p>As this is child data, you can not directly add items. In order to add an
item to this data stock, you need to open a data record of the parent data stock 
and attach the item there.</p>
EOD;

$help['data_spatial']['title'] = 'Data Overview (spatial)';
$help['data_spatial']['content'] =  <<<EOD
<p>This page shows an overview of the data stock. The map shows the actual
features. An attribute overview can be accessed in the tab "Items". To get more 
information about a specific data record/feature, click on it in the map. A 
bubble with additional information and options appears. Clicking on the feature 
name in the bubble or on the magnifier icon in the table opens the data record 
and displays all available information of the item.</p>
EOD;

$help['data_spatial_child']['title'] = 'Data Overview (spatial, child data)';
$help['data_spatial_child']['content'] =  <<<EOD
<p>This page shows an overview of the data stock. The map shows the actual
features. An attribute overview can be accessed in the tab "Items". To get more 
information about a specific data record/feature, click on it in the map. A 
bubble with additional information and options appears. Clicking on the feature 
name in the bubble or on the magnifier icon in the table opens the data record 
and displays all available information of the item.</p>
<p>As this is child data, you can not directly add items. In order to add an
item to this data stock, you need to open a data record of the parent data stock 
and attach the item there.</p>
EOD;

$help['data_item_common']['title'] = 'Data record details';
$help['data_item_common']['content'] = <<<EOD
<p>This page shows all details of a data record. If available, other items
belonging to this data record are listet in the tabs "Attached data" and
"Related data".</p>
EOD;

$help['data_item_spatial']['title'] = 'Data record details (spatial)';
$help['data_item_spatial']['content'] = <<<EOD
<p>This page shows all details of a spatial data record. If the record contains
a geometry, it is displayed on a map. If available, other items belonging to
this data record are listet in the tabs "Attached data" and "Related data".</p>
EOD;

$help['edit_data_item_common']['title'] = 'Editing a data record';
$help['edit_data_item_common']['content'] = <<<EOD
<p>Edit the values you want to change and click "OK - Save" (or press enter) to
edit the data record. Apart from using the mouse, you can easily move the cursor
to the next field by pressing the tab key (shift+tab to move to the previous
field). Radio buttons can be changed by pressing the arrow buttons and
checkboxes can be checked by pressing space.</p>
EOD;

$help['add_data_item_common']['title'] = 'Adding a data record';
$help['add_data_item_common']['content'] = <<<EOD
<p>Fill out the form and click "OK - Save" (or press enter) to add a data
record. Apart from using the mouse, you can easily move the cursor to the next
field by pressing the tab key (shift+tab to move to the previous field). Radio
buttons can be changed by pressing the arrow buttons and checkboxes can be
checked by pressing space.</p>
EOD;

$help['edit_data_item_spatial']['title'] =   'Editing a spatial data record';
$help['edit_data_item_spatial']['content'] = <<<EOD
<ul>
<li>Points can be dragged and dropped.</li>
<li>Lines and polygons can be edited by dragging and dropping their
vertices.</li>
<li>Vertices can be removed by hovering on them and pressing [del].</li>
</ul>
EOD;

$help['add_data_item_spatial']['title'] = 'Adding a spatial data record'; 
$help['add_data_item_spatial']['content'] = <<<EOD
<ul><li>Zoom (+/- buttons or mouse wheel if enabled) and pan (mouse dragging)
the map to he desired map section.</li>
<li>If the geometry type (pont, line or polygon) is not determined by the kind
of data, you can select it by clicking the correspondig icon in the toolbar on
the left.</li>
<li>Points are created by clicking on the desired position. Lines and polygons
are created by several clicks on the desired vertices. A <strong>double
click</strong> creates the last vertex and <strong>finishes the line or
polygon</strong>.</li>
<li>As long as a line or polygon is not finished yet you can press [Ctrl]+[z] to
undo the creation of vertices or [ESC] to remove all vertices to restart
drawing.</li><li>After finishing a feature you can edit it by clicking on it
(color change to blue indicates edit mode). A point can be dragged and dropped,
lines and polygons can be edited by dragging and dropping their vertices.
Vertices can be removed by hovering on them and pressing [del].</li>
</ul>
EOD;
?>
