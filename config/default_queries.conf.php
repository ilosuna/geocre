<?php
$default_query['create_table'][0][0] = 'CREATE TABLE IF NOT EXISTS "[table]" (id serial PRIMARY KEY,
                                                                                fk integer DEFAULT 0,
                                                                                creator integer,
                                                                                created timestamp with time zone NOT NULL DEFAULT now(),
                                                                                last_editor integer,
                                                                                last_edited timestamp with time zone)';

$default_query['create_table'][1][0] = 'CREATE TABLE IF NOT EXISTS "[table]" (id serial PRIMARY KEY,
                                                                                fk integer DEFAULT 0,
                                                                                creator integer,
                                                                                created timestamp with time zone NOT NULL DEFAULT now(),
                                                                                last_editor integer,
                                                                                last_edited timestamp with time zone,
                                                                                area numeric,
                                                                                perimeter numeric,
                                                                                length numeric,
                                                                                latitude numeric,
                                                                                longitude numeric)';
$default_query['create_table'][1][1] = 'SELECT AddGeometryColumn(\'[table]\', \'geom\', 4326, \'GEOMETRY\', 2)';
$default_query['create_table'][1][2] = 'CREATE INDEX "[table]_geom_gist" ON "[table]" USING gist(geom)';
?>
