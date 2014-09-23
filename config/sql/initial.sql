CREATE TABLE geocre_data_models
(
  id serial NOT NULL,
  sequence integer NOT NULL DEFAULT 1,
  creator integer NOT NULL,
  created timestamp without time zone NOT NULL,
  last_editor integer,
  last_edited timestamp without time zone,
  table_name character varying NOT NULL DEFAULT ''::character varying,
  type smallint DEFAULT 0,
  status smallint DEFAULT 0,
  parent_table integer NOT NULL DEFAULT 0,
  title character varying NOT NULL,
  geometry_type smallint DEFAULT 0,
  latlong_entry smallint NOT NULL DEFAULT 0,
  geometry_required smallint NOT NULL DEFAULT 0,
  boundary_layer integer DEFAULT 0,
  auxiliary_layer_1 integer DEFAULT 0,
  auxiliary_layer_2 integer DEFAULT 0,
  auxiliary_layer_3 integer DEFAULT 0,
  layer_overview smallint NOT NULL DEFAULT 0,
  min_scale double precision DEFAULT 0,
  max_scale double precision DEFAULT 0,
  project integer DEFAULT 0,
  readonly smallint NOT NULL DEFAULT 0,
  simplification_tolerance double precision DEFAULT 0,
  simplification_tolerance_extent_factor double precision DEFAULT 0,
  description text,
  basemaps character varying,
  data_images smallint NOT NULL DEFAULT 0,
  item_images smallint NOT NULL DEFAULT 0,  
  CONSTRAINT geocre_data_models_pkey PRIMARY KEY (id)
);

CREATE TABLE geocre_data_model_items
(
  id serial NOT NULL,
  sequence integer NOT NULL DEFAULT 1,
  creator integer NOT NULL,
  created timestamp with time zone NOT NULL DEFAULT now(),
  last_editor integer,
  last_edited timestamp without time zone,
  name character varying NOT NULL DEFAULT ''::character varying,
  label character varying NOT NULL DEFAULT ''::character varying,
  column_type smallint,
  column_length smallint,
  "unique" boolean DEFAULT false,
  column_default_value character varying,
  column_not_null smallint NOT NULL DEFAULT 0,
  input_type smallint DEFAULT 0,
  input_values character varying,
  input_labels character varying,
  input_default_value character varying,
  input_length smallint DEFAULT 0,
  input_height smallint DEFAULT 0,
  status smallint DEFAULT 0,
  input_values_table character varying,
  input_values_label_column character varying,
  table_id integer NOT NULL,
  deactivated smallint NOT NULL DEFAULT 0,
  required smallint NOT NULL DEFAULT 0,
  overview smallint NOT NULL DEFAULT 0,
  description character varying,
  choices character varying,
  choice_labels character varying,
  relation integer,
  priority integer DEFAULT 0,
  relation_column smallint NOT NULL DEFAULT 0,
  range_from double precision,
  range_to double precision,
  regex character varying,
  definition text,
  comments text,
  CONSTRAINT geocre_data_model_items_pkey PRIMARY KEY (id),
  CONSTRAINT geocre_data_model_items_model_fkey FOREIGN KEY ("table_id")
      REFERENCES geocre_data_models (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
);

CREATE TABLE geocre_relations
(
  id serial NOT NULL,
  t1 integer NOT NULL DEFAULT 0,
  i1 integer NOT NULL DEFAULT 0,
  t2 integer NOT NULL DEFAULT 0,
  i2 integer NOT NULL DEFAULT 0,
  creator integer NOT NULL DEFAULT 0,
  created timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT geocre_relations_pkey PRIMARY KEY (id)
);

CREATE TABLE geocre_table_relations
(
  id serial NOT NULL,
  t1 integer NOT NULL DEFAULT 0,
  t2 integer NOT NULL DEFAULT 0,
  CONSTRAINT geocre_table_relations_pkey PRIMARY KEY (id)
);

CREATE TABLE geocre_pages
(
  id serial NOT NULL,
  sequence integer NOT NULL DEFAULT 0,
  title character varying,
  teaser_text text,
  content text,
  status smallint NOT NULL DEFAULT 0,
  teaser_image character varying,
  location character varying,
  custom_date character varying,
  contact_name character varying,
  contact_email character varying,
  page_image character varying,
  page_image_width integer,
  page_image_height integer,
  page_image_caption character varying,
  teaser_image_width integer,
  teaser_image_height integer,
  identifier character varying,
  creator integer,
  created timestamp with time zone NOT NULL DEFAULT now(),
  last_editor integer,
  last_edited timestamp with time zone,
  index boolean NOT NULL DEFAULT false,
  project boolean NOT NULL DEFAULT false,
  sidebar_title character varying,
  sidebar_text text,
  sidebar_link character varying,
  sidebar_linktext character varying,
  page_info_title character varying,
  teaser_supertitle character varying,
  teaser_title character varying,
  teaser_linktext character varying,
  title_as_headline boolean,
  parent integer NOT NULL DEFAULT 0,
  news boolean NOT NULL DEFAULT false,
  subtemplate character varying,
  menu character varying,
  tv character varying,
  CONSTRAINT pk_id PRIMARY KEY (id)
);

CREATE TABLE geocre_page_photos
(
  id serial NOT NULL,
  page integer,
  creator integer,
  created timestamp with time zone NOT NULL DEFAULT now(),
  last_editor integer,
  last_edited timestamp with time zone,
  title character varying,
  description character varying,
  author character varying,
  filename character varying,
  thumbnail_width integer,
  thumbnail_height integer,
  photo_width integer,
  photo_height integer,
  sequence integer,
  CONSTRAINT geocre_page_photos_pkey PRIMARY KEY (id)
);

CREATE TABLE geocre_data_images
(
  id serial NOT NULL,
  data integer NOT NULL,
  item integer NOT NULL DEFAULT 0,
  creator integer,
  created timestamp with time zone NOT NULL DEFAULT now(),
  last_editor integer,
  last_edited timestamp with time zone,
  title character varying,
  description character varying,
  author character varying,
  filename character varying,
  original_filename character varying,
  thumbnail_width integer,
  thumbnail_height integer,
  image_width integer,
  image_height integer,
  sequence integer,
  CONSTRAINT geocre_data_images_pkey PRIMARY KEY (id)
);

CREATE TABLE geocre_settings
(
  id serial NOT NULL,
  name character varying NOT NULL DEFAULT ''::character varying,
  value character varying NOT NULL DEFAULT ''::character varying,
  CONSTRAINT geocre_settings_pkey PRIMARY KEY (id)
);

CREATE TABLE geocre_log
(
  id serial NOT NULL,
  "user" integer,
  action integer NOT NULL DEFAULT 0,
  "table" integer,
  item integer,
  previous_data text,
  "time" timestamp with time zone NOT NULL DEFAULT now(),
  CONSTRAINT geocre_log_pkey PRIMARY KEY (id)
);

CREATE TABLE geocre_users
(
  id serial NOT NULL,
  type smallint NOT NULL DEFAULT (0)::smallint,
  email character varying NOT NULL,
  name character varying NOT NULL,
  pw character varying(50) NOT NULL,
  registered timestamp without time zone NOT NULL,
  logins integer NOT NULL DEFAULT 0,
  last_login timestamp without time zone,
  reset_pw_code character varying DEFAULT NULL::character varying,
  settings text,
  real_name character varying,
  reset_pw_time timestamp with time zone,
  language character varying,
  time_zone character varying,
  CONSTRAINT geocre_users_pkey PRIMARY KEY (id)
);

CREATE TABLE geocre_groups
(
  id serial NOT NULL,
  sequence integer NOT NULL DEFAULT 0,
  name character varying,
  description text,
  CONSTRAINT geocre_groups_pkey PRIMARY KEY (id)
);

CREATE TABLE geocre_group_memberships
(
  id serial NOT NULL,
  "user" integer,
  "group" integer,
  CONSTRAINT geocre_group_memberships_pkey PRIMARY KEY (id),
  CONSTRAINT geocre_group_memberships_group_fkey FOREIGN KEY ("group")
      REFERENCES geocre_groups (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE,
  CONSTRAINT geocre_group_memberships_user_fkey FOREIGN KEY ("user")
      REFERENCES geocre_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
);

CREATE TABLE geocre_group_permissions
(
  id serial NOT NULL,
  type integer NOT NULL DEFAULT 0,
  item integer NOT NULL DEFAULT 0,
  level integer NOT NULL DEFAULT 0,
  "group" integer,
  CONSTRAINT geocre_group_permissions_pkey PRIMARY KEY (id),
  CONSTRAINT geocre_group_permissions_group_fkey FOREIGN KEY ("group")
      REFERENCES geocre_groups (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
);

CREATE TABLE geocre_basemaps
(
  id serial NOT NULL,
  sequence integer NOT NULL DEFAULT 0,
  title character varying,
  properties character varying,
  js character varying,
  "default" boolean DEFAULT false,
  CONSTRAINT geocre_basemaps_pkey PRIMARY KEY (id)
);

INSERT INTO geocre_settings (name, value) VALUES ('allow_ring_self_intersections', '1');
INSERT INTO geocre_settings (name, value) VALUES ('autocomplete_min_length', '1');
INSERT INTO geocre_settings (name, value) VALUES ('backup_path', '');
INSERT INTO geocre_settings (name, value) VALUES ('base_path', '');
INSERT INTO geocre_settings (name, value) VALUES ('base_url', '');
INSERT INTO geocre_settings (name, value) VALUES ('data_images', '1');
INSERT INTO geocre_settings (name, value) VALUES ('data_images_permission_check', '1');
INSERT INTO geocre_settings (name, value) VALUES ('data_images_save_original', '1');
INSERT INTO geocre_settings (name, value) VALUES ('data_images_download_original', '0');
INSERT INTO geocre_settings (name, value) VALUES ('data_stocks_per_page', '30');
INSERT INTO geocre_settings (name, value) VALUES ('default_controller', 'page.php');
INSERT INTO geocre_settings (name, value) VALUES ('default_latitude', '20');
INSERT INTO geocre_settings (name, value) VALUES ('default_longitude', '0');
INSERT INTO geocre_settings (name, value) VALUES ('default_template', 'default.tpl');
INSERT INTO geocre_settings (name, value) VALUES ('default_zoomlevel', '2');
INSERT INTO geocre_settings (name, value) VALUES ('description', '');
INSERT INTO geocre_settings (name, value) VALUES ('display_errors', '1');
INSERT INTO geocre_settings (name, value) VALUES ('email_address', 'admin@geocre.net');
INSERT INTO geocre_settings (name, value) VALUES ('email_smtp_host', '');
INSERT INTO geocre_settings (name, value) VALUES ('email_smtp_password', '');
INSERT INTO geocre_settings (name, value) VALUES ('email_smtp_port', '587');
INSERT INTO geocre_settings (name, value) VALUES ('email_smtp_username', '');
INSERT INTO geocre_settings (name, value) VALUES ('feedback', '');
INSERT INTO geocre_settings (name, value) VALUES ('feedback_message_maxlength', '10000');
INSERT INTO geocre_settings (name, value) VALUES ('footer', '&copy 2014 Your Organization');
INSERT INTO geocre_settings (name, value) VALUES ('gallery_thumbnail_width', '170');
INSERT INTO geocre_settings (name, value) VALUES ('gallery_thumbnail_height', '170');
INSERT INTO geocre_settings (name, value) VALUES ('gallery_thumbnail_quality', '80');
INSERT INTO geocre_settings (name, value) VALUES ('gallery_image_width', '800');
INSERT INTO geocre_settings (name, value) VALUES ('gallery_image_height', '700');
INSERT INTO geocre_settings (name, value) VALUES ('gallery_image_quality', '85');
INSERT INTO geocre_settings (name, value) VALUES ('index_page_content', '<h1>Welcome to geoCRE!</h1><p><strong>geoCRE</strong> is a collaborative research environment for geographic research purposes developed at the Department of Physical Geography, University of Freiburg.</p>');
INSERT INTO geocre_settings (name, value) VALUES ('index_page_title', 'Welcome!');
INSERT INTO geocre_settings (name, value) VALUES ('items_per_page', '20');
INSERT INTO geocre_settings (name, value) VALUES ('language', 'english');
INSERT INTO geocre_settings (name, value) VALUES ('log_errors', '');
INSERT INTO geocre_settings (name, value) VALUES ('mail_parameter', '');
INSERT INTO geocre_settings (name, value) VALUES ('maintenance', '0');
INSERT INTO geocre_settings (name, value) VALUES ('maintenance_message', 'Currently unavailable due to maintenance!');
INSERT INTO geocre_settings (name, value) VALUES ('many_to_many_relationships', '0');
INSERT INTO geocre_settings (name, value) VALUES ('max_items_per_page', '1000');
INSERT INTO geocre_settings (name, value) VALUES ('min_pw_length', '8');
INSERT INTO geocre_settings (name, value) VALUES ('min_pw_length', '8');
INSERT INTO geocre_settings (name, value) VALUES ('photo_upload_directory', '');
INSERT INTO geocre_settings (name, value) VALUES ('register_code', '');
INSERT INTO geocre_settings (name, value) VALUES ('register_mode', '1');
INSERT INTO geocre_settings (name, value) VALUES ('register_notification', '1');
INSERT INTO geocre_settings (name, value) VALUES ('session_prefix', 'geocre_');
INSERT INTO geocre_settings (name, value) VALUES ('time_zone', 'Europe/Berlin');
INSERT INTO geocre_settings (name, value) VALUES ('website_title', 'geoCRE');
INSERT INTO geocre_settings (name, value) VALUES ('default_group', '0');
INSERT INTO geocre_settings (name, value) VALUES ('user_name_max_length', '50');

INSERT INTO geocre_basemaps (sequence, title, properties, js, "default") VALUES (1, 'Google Satellite', 'Google("Google Satellite", {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 20})', '//maps.google.com/maps/api/js?v=3.2&amp;sensor=false', true);
INSERT INTO geocre_basemaps (sequence, title, properties, js, "default") VALUES (2, 'Google Hybrid', 'Google("Google Hybrid",{type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20})', '//maps.google.com/maps/api/js?v=3.2&amp;sensor=false', true);
INSERT INTO geocre_basemaps (sequence, title, properties, js, "default") VALUES (3, 'Google Physical', 'Google("Google Physical", {type: google.maps.MapTypeId.TERRAIN})', '//maps.google.com/maps/api/js?v=3.2&amp;sensor=false', true);
INSERT INTO geocre_basemaps (sequence, title, properties, js, "default") VALUES (4, 'Google Streets', 'Google("Google Streets", {numZoomLevels: 20})', '//maps.google.com/maps/api/js?v=3.2&amp;sensor=false', true);
INSERT INTO geocre_basemaps (sequence, title, properties, js, "default") VALUES (5, 'OpenStreetMap', 'OSM("OpenStreetMap")', '', true);

INSERT INTO geocre_users (type, email, name, pw, registered) VALUES (2, 'mail@example.org', 'Admin', '1e1ac699792e9576876638055d91550fa31ed8191931316e77', NOW());
INSERT INTO geocre_groups (sequence, name, description) VALUES (1, 'Admin', 'Administrator group');
INSERT INTO geocre_group_permissions (type, item, level, "group") VALUES (100, 0, 0, 1);
INSERT INTO geocre_group_memberships ("user", "group") VALUES (1, 1);
