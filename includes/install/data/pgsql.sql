--
-- PostgreSQL dump
-- Last Update: 10 June 2013 by datahell ( http://www.elxis.org )
--

DROP TABLE IF EXISTS "#__acl";
CREATE TABLE "#__acl" (
  "id" SERIAL NOT NULL ,
  "category" VARCHAR(60) NULL ,
  "element" VARCHAR(60) NULL ,
  "identity" INTEGER NOT NULL DEFAULT 0,
  "action" VARCHAR(60) NULL ,
  "minlevel" INTEGER NOT NULL DEFAULT -1,
  "gid" INTEGER NOT NULL DEFAULT 0,
  "uid" INTEGER NOT NULL DEFAULT 0,
  "aclvalue" INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY ("id")
); 
CREATE INDEX "#__acl_idx_ctg_elem" ON "#__acl" ("category","element");

INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (1,'module','mod_language',1,'view',0,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (2,'module','mod_language',1,'manage',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (3,'module','mod_login',2,'view',0,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (4,'module','mod_login',2,'manage',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (5,'module','mod_menu',3,'view',0,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (6,'module','mod_menu',3,'manage',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (7,'com_user','memberslist',0,'view',2,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (8,'com_user','profile',0,'view',2,0,0,2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (9,'com_user','profile',0,'viewemail',2,0,0,2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (10,'com_user','profile',0,'viewphone',2,0,0,2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (11,'com_user','profile',0,'viewmobile',2,0,0,2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (12,'com_user','profile',0,'viewwebsite',2,0,0,2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (13,'com_user','profile',0,'viewaddress',2,0,0,2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (14,'com_user','profile',0,'viewgender',2,0,0,2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (15,'com_user','profile',0,'viewage',2,0,0,2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (16,'com_user','profile',0,'edit',2,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (17,'com_user','profile',0,'edit',70,0,0,2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (18,'com_user','profile',0,'block',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (19,'com_user','profile',0,'delete',2,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (20,'com_user','profile',0,'delete',-1,1,0,2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (21,'com_user','profile',0,'uploadavatar',2,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (22,'com_content','comments',0,'post',2,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (23,'com_content','comments',0,'publish',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (24,'com_content','comments',0,'publish',70,0,0,2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (25,'com_content','comments',0,'delete',70,0,0,2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (26,'module','mod_search',4,'view',0,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (27,'module','mod_search',4,'manage',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (28,'module','mod_articles',5,'view',0,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (29,'module','mod_articles',5,'manage',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (30,'module','mod_categories',6,'view',0,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (31,'module','mod_categories',6,'manage',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (32,'administration','interface',0,'login',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (33,'com_cpanel','settings',0,'edit',-1,1,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (34,'com_cpanel','backup',0,'edit',-1,1,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (35,'module','mod_comments',7,'view',0,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (36,'module','mod_comments',7,'manage',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (37,'module','mod_whosonline',8,'view',0,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (38,'module','mod_whosonline',8,'manage',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (39,'com_cpanel','routes',0,'manage',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (40,'component','com_content',0,'view',0,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (41,'component','com_content',0,'manage',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (42,'component','com_user',0,'view',0,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (43,'component','com_user',0,'manage',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (44,'component','com_search',0,'view',0,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (45,'component','com_search',0,'manage',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (46,'component','com_cpanel',0,'view',-1,1,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (47,'component','com_cpanel',0,'manage',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (48,'component','com_languages',0,'view',-1,1,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (49,'component','com_languages',0,'manage',70,0,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (50,'component','com_emedia',0,'view',-1,1,0,1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (51, 'component', 'com_emedia', 0, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (52, 'component', 'com_emenu', 0, 'view', -1, 1, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (53, 'component', 'com_emenu', 0, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (54, 'com_emenu', 'menu', 0, 'add', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (55, 'com_emenu', 'menu', 0, 'edit', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (56, 'com_emenu', 'menu', 0, 'delete', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (57, 'component', 'com_wrapper', 0, 'view', 0, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (58, 'component', 'com_wrapper', 0, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (59, 'com_content', 'category', 0, 'add', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (60, 'com_content', 'category', 0, 'edit', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (61, 'com_content', 'category', 0, 'delete', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (62, 'com_content', 'category', 0, 'publish', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (63, 'com_content', 'article', 0, 'add', 70, 0, 0, 2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (64, 'com_content', 'article', 0, 'edit', 70, 0, 0, 2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (65, 'com_content', 'article', 0, 'delete', 70, 0, 0, 2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (66, 'com_content', 'article', 0, 'publish', 70, 0, 0, 2);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (67, 'com_user', 'groups', 0, 'manage', -1, 1, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (68, 'com_user', 'acl', 0, 'manage', -1, 1, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (69, 'component', 'com_extmanager', 0, 'view', -1, 1, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (70, 'component', 'com_extmanager', 0, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (71, 'com_extmanager', 'components', 0, 'edit', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (72, 'com_extmanager', 'modules', 0, 'edit', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (73, 'com_extmanager', 'templates', 0, 'edit', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (74, 'com_extmanager', 'engines', 0, 'edit', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (75, 'com_extmanager', 'components', 0, 'install', -1, 1, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (76, 'com_extmanager', 'modules', 0, 'install', -1, 0, 1, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (77, 'com_extmanager', 'templates', 0, 'install', -1, 1, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (78, 'com_extmanager', 'engines', 0, 'install', -1, 1, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (79, 'com_cpanel', 'logs', 0, 'manage', -1, 1, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (80, 'component', 'com_etranslator', 0, 'view', -1, 1, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (81, 'component', 'com_etranslator', 0, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (82, 'module', 'mod_adminmenu', 9, 'view', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (83, 'module', 'mod_adminmenu', 9, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (84, 'module', 'mod_adminprofile', 10, 'view', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (85, 'module', 'mod_adminprofile', 10, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (86, 'module', 'mod_adminsearch', 11, 'view', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (87, 'module', 'mod_adminsearch', 11, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (88, 'module', 'mod_adminlang', 12, 'view', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (89, 'module', 'mod_adminlang', 12, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (90, 'com_content', 'frontpage', 0, 'edit', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (91, 'com_cpanel', 'multisites', 0, 'edit', -1, 1, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (92, 'module', 'mod_opensearch', 13, 'view', 0, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (93, 'module', 'mod_opensearch', 13, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (94, 'com_extmanager', 'auth', 0, 'edit', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (95, 'com_extmanager', 'auth', 0, 'install', -1, 1, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (96, 'module', 'mod_adminusers', 14, 'view', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (97, 'module', 'mod_adminusers', 14, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (98, 'module', 'mod_adminarticles', 15, 'view', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (99, 'module', 'mod_adminarticles', 15, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (100, 'module', 'mod_adminstats', 16, 'view', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (101, 'module', 'mod_adminstats', 16, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (102, 'com_cpanel', 'statistics', 0, 'view', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (103, 'com_emedia', 'files', 0, 'upload', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (104, 'com_emedia', 'files', 0, 'edit', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (105, 'com_extmanager', 'plugins', 0, 'edit', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (106, 'com_extmanager', 'plugins', 0, 'install', -1, 1, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (107, 'module', 'mod_iosslider', 17, 'view', 0, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (108, 'module', 'mod_iosslider', 17, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (109, 'module', 'mod_gallery', 18, 'view', 0, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (110, 'module', 'mod_gallery', 18, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (111, 'module', 'mod_ads', 19, 'view', 0, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (112, 'module', 'mod_ads', 19, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (113, 'module', 'mod_articles', 20, 'view', 0, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (114, 'module', 'mod_articles', 20, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (115, 'module', 'mod_content', 21, 'view', 0, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (116, 'module', 'mod_content', 21, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (117, 'module', 'mod_menu', 22, 'view', 0, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (118, 'module', 'mod_menu', 22, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (119, 'module', 'mod_menu', 23, 'view', 0, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (120, 'module', 'mod_menu', 23, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (121, 'com_cpanel', 'cache', 0, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (122, 'module', 'mod_mobilefront', 24, 'view', 0, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (123, 'module', 'mod_mobilefront', 24, 'manage', 70, 0, 0, 1);
INSERT INTO "#__acl" ("id","category","element","identity","action","minlevel","gid","uid","aclvalue") VALUES (124, 'component', 'com_etranslator', 0, 'api', -1, 1, 0, 1);

SELECT setval('"#__acl_id_seq"', max("id") ) FROM "#__acl"; 


DROP TABLE IF EXISTS "#__authentication";
CREATE TABLE "#__authentication" (
  "id" SERIAL NOT NULL ,
  "title" VARCHAR(255) NULL ,
  "auth" VARCHAR(100) NULL ,
  "ordering" INTEGER NOT NULL DEFAULT 0,
  "published" SMALLINT NOT NULL DEFAULT 0,
  "iscore" SMALLINT NOT NULL DEFAULT 0,
  "params" TEXT NULL ,
  PRIMARY KEY ("id")
); 

INSERT INTO "#__authentication" ("id","title","auth","ordering","published","iscore","params") VALUES (1,'Elxis','elxis',1,1,1,NULL);
INSERT INTO "#__authentication" ("id","title","auth","ordering","published","iscore","params") VALUES (2,'GMail','gmail',2,0,1,NULL);
INSERT INTO "#__authentication" ("id","title","auth","ordering","published","iscore","params") VALUES (3,'LDAP','ldap',3,0,1,NULL);
INSERT INTO "#__authentication" ("id","title","auth","ordering","published","iscore","params") VALUES (4,'Twitter','twitter',4,0,1,NULL);
INSERT INTO "#__authentication" ("id","title","auth","ordering","published","iscore","params") VALUES (5,'OpenID','openid',5,0,1,NULL);

SELECT setval('"#__authentication_id_seq"', max("id") ) FROM "#__authentication"; 


DROP TABLE IF EXISTS "#__categories";
CREATE TABLE "#__categories" (
  "catid" SERIAL NOT NULL ,
  "parent_id" INTEGER NOT NULL DEFAULT 0,
  "title" VARCHAR(255) NULL ,
  "seotitle" VARCHAR(255) NULL ,
  "seolink" VARCHAR(255) NULL ,
  "published" SMALLINT NOT NULL DEFAULT 0,
  "ordering" INTEGER NOT NULL DEFAULT 0,
  "image" VARCHAR(255) NULL ,
  "description" TEXT NULL ,
  "params" TEXT NULL ,
  "alevel" INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY ("catid")
);

INSERT INTO "#__categories" ("catid","parent_id","title","seotitle","seolink","published","ordering","image","description","params","alevel") VALUES (1,0,'Famous places','famous-places','famous-places/',1,1,NULL,'<p>Each of these landmarks is a cultural icon, it may represent an epoch, an area, a belief, \r\na culture, a country or a city, it also may represent more than one of this various meanings.</p>', 'ctg_show=2\nctg_layout=0\nctg_print=1\nctg_ordering=\nctg_img_empty=-1\nctg_mods_pos=category\nctg_pagination=1\nctg_nextpages_style=0\nctg_subcategories=-1\nctg_subcategories_cols=-1\nctg_featured_num=1\nctg_featured_img=2\nctg_featured_dateauthor=-1\nctg_short_num=4\nctg_short_cols=2\nctg_short_img=-1\nctg_short_dateauthor=-1\nctg_short_text=220\nctg_links_num=10\nctg_links_cols=1\nctg_links_header=1\nctg_links_dateauthor=1\ncomments=0',0);
INSERT INTO "#__categories" ("catid","parent_id","title","seotitle","seolink","published","ordering","image","description","params","alevel") VALUES (2, 0, 'Elxis', 'elxis', 'elxis/', 1, 2, NULL, '', 'ctg_show=-1\nctg_layout=-1\nctg_print=-1\nctg_ordering=\nctg_img_empty=-1\nctg_mods_pos=_global_\nctg_pagination=-1\nctg_nextpages_style=-1\nctg_subcategories=-1\nctg_subcategories_cols=-1\nctg_featured_num=0\nctg_featured_img=-1\nctg_featured_dateauthor=-1\nctg_short_num=10\nctg_short_cols=1\nctg_short_img=-1\nctg_short_dateauthor=-1\nctg_short_text=-1\nctg_links_num=0\nctg_links_cols=1\nctg_links_header=-1\nctg_links_dateauthor=-1\ncomments=0', 0);

SELECT setval('"#__categories_catid_seq"', max("catid") ) FROM "#__categories"; 


DROP TABLE IF EXISTS "#__comments";
CREATE TABLE "#__comments" (
  "id" SERIAL NOT NULL ,
  "element" VARCHAR(120) NULL ,
  "elid" INTEGER NOT NULL DEFAULT 0,
  "message" TEXT NULL ,
  "created" TIMESTAMP NOT NULL DEFAULT '1970-01-01 00:00:00',
  "uid" INTEGER NOT NULL DEFAULT 0,
  "author" VARCHAR(120) NULL ,
  "email" VARCHAR(120) NULL ,
  "published" SMALLINT NOT NULL DEFAULT 0,
  PRIMARY KEY ("id")
); 
CREATE INDEX "#__comments_idx_elid" ON "#__comments" ("elid");


DROP TABLE IF EXISTS "#__components";
CREATE TABLE "#__components" (
  "id" SERIAL NOT NULL ,
  "name" VARCHAR(120) NULL ,
  "component" VARCHAR(60) NULL ,
  "route" VARCHAR(60) NULL ,
  "iscore" SMALLINT NOT NULL DEFAULT 0,
  "params" TEXT NULL ,
  PRIMARY KEY ("id")
); 
CREATE INDEX "#__components_idx_component" ON "#__components" ("component");
CREATE INDEX "#__components_idx_route" ON "#__components" ("route");

INSERT INTO "#__components" ("id","name","component","route","iscore","params") VALUES (1,'User','com_user',NULL,1,'members_firstname=0\nmembers_lastname=0\nmembers_uname=1\nmembers_groupname=1\nmembers_preflang=1\nmembers_country=0\nmembers_website=0\nmembers_gender=0\nmembers_registerdate=1\nmembers_lastvisitdate=1\ngravatar=1\nprofile_avatar_width=80\nprofile_twitter=1');
INSERT INTO "#__components" ("id","name","component","route","iscore","params") VALUES (2,'Content','com_content',NULL,1,'popup_window=1\nlive_bookmarks=rss\nfeed_items=10\nfeed_cache=6\nimg_thumb_width=100\nimg_thumb_height=0\nimg_medium_width=240\nimg_medium_height=0\ncomments_src=0\nctg_img_empty=1\nctg_layout=0\nctg_show=2\nctg_subcategories=2\nctg_subcategories_cols=2\nctg_ordering=cd\nctg_print=0\nctg_featured_num=1\nctg_featured_img=2\nctg_featured_dateauthor=6\nctg_short_num=4\nctg_short_cols=1\nctg_short_img=2\nctg_short_dateauthor=6\nctg_short_text=220\nctg_links_num=5\nctg_links_cols=1\nctg_links_header=0\nctg_links_dateauthor=0\nctg_pagination=1\nctg_nextpages_style=1\nctg_mods_pos=category\nart_dateauthor=6\nart_dateauthor_pos=0\nart_img=2\nart_print=1\nart_email=1\nart_tags=1\nart_hits=1\nart_chain=2');
INSERT INTO "#__components" ("id","name","component","route","iscore","params") VALUES (3,'Search','com_search',NULL,1,NULL);
INSERT INTO "#__components" ("id","name","component","route","iscore","params") VALUES (4,'CPanel','com_cpanel',NULL,1,NULL);
INSERT INTO "#__components" ("id","name","component","route","iscore","params") VALUES (5,'Languages','com_languages',NULL,1,NULL);
INSERT INTO "#__components" ("id","name","component","route","iscore","params") VALUES (6,'Media manager','com_emedia',NULL,1,NULL);
INSERT INTO "#__components" ("id","name","component","route","iscore","params") VALUES (7,'Menu manager','com_emenu',NULL,1,NULL);
INSERT INTO "#__components" ("id","name","component","route","iscore","params") VALUES (8,'Wrapper','com_wrapper',NULL,1,NULL);
INSERT INTO "#__components" ("id","name","component","route","iscore","params") VALUES (9,'Extensions manager','com_extmanager',NULL,1,NULL);
INSERT INTO "#__components" ("id","name","component","route","iscore","params") VALUES (10,'Translator','com_etranslator',NULL,1,NULL);

SELECT setval('"#__components_id_seq"', max("id") ) FROM "#__components"; 


DROP TABLE IF EXISTS "#__content";
CREATE TABLE "#__content" (
  "id" SERIAL NOT NULL ,
  "catid" INTEGER NOT NULL DEFAULT 0,
  "title" VARCHAR(255) NULL ,
  "seotitle" VARCHAR(255) NULL ,
  "subtitle" VARCHAR(255) NULL ,
  "introtext" TEXT NULL ,
  "maintext" TEXT NULL ,
  "image" VARCHAR(255) NULL ,
  "caption" VARCHAR(255) NULL ,
  "published" SMALLINT NOT NULL DEFAULT 0,
  "metakeys" VARCHAR(255) NULL ,
  "created" TIMESTAMP NOT NULL DEFAULT '1970-01-01 00:00:00',
  "created_by" INTEGER NOT NULL DEFAULT 0,
  "created_by_name" VARCHAR(120) NULL ,
  "modified" TIMESTAMP NOT NULL DEFAULT '1970-01-01 00:00:00',
  "modified_by" INTEGER NOT NULL DEFAULT 0,
  "modified_by_name" VARCHAR(120) NULL ,
  "ordering" INTEGER NOT NULL DEFAULT 0,
  "hits" INTEGER NOT NULL DEFAULT 0,
  "alevel" INTEGER NOT NULL DEFAULT 0,
  "params" TEXT NULL ,
  PRIMARY KEY ("id")
); 
CREATE INDEX "#__content_idx_catid" ON "#__content" ("catid");
CREATE INDEX "#__content_idx_seotitle" ON "#__content" ("seotitle");

INSERT INTO "#__content" ("id","catid","title","seotitle","subtitle","introtext","maintext","image","caption","published","metakeys","created","created_by","created_by_name","modified","modified_by","modified_by_name","ordering","hits","alevel","params") VALUES (1,1,'Taj Mahal','taj-mahal','The Taj Mahal is a tomb situated in Agra, India.','<p>The Taj Mahal was built by the Mughal emperor, Shah Jahan in the memory of his beloved wife, \nMumtaz Mahal. It is one of The Seven Wonders of the World and is said to be one of the finest \nart of the Mughal architecture. The architecture has a mixture of Persian, Ottoman, India \nand Islamic art. During the year 1983, the Taj became a part of the UNESCO, World heritage Site.</p>','<p>Some of the legendary stories say that after the Taj was built, the Mughal Emperor \ncut off the hands of all the men who built the Taj so that the same masterpiece could \nnot be made again. The Taj Mahal is located on the banks of the river Yamuna in Agra. \nIt was built in the year 1631 and got completed in the year 1653 spreading over 32 acres \nof land.</p>\n\n<p>The Taj Mahal is also called the Taj and is a symbol of love and is known for its \nmonumental beauty. Taj is one of the main touristΞΒΞ’Β²ΞΒ²Ξ²β‚¬ΒΞ’Β¬ΞΒ²Ξ²β‚¬ΒΞβ€ s hotspot in India and anyone who \ncomes to visit India definitely takes a tour of the Taj. The beauty of the Taj goes \nbeyond words and it is said that the place looks magnificent during the full moon night. \nIt is a true dedication to love and romance. The word Taj Mahal means Crown Palace in \nEnglish and it is made up off mainly white marbles and some colorful gemstones.</p>', 'media/images/articles/taj-mahal.jpg','Mausoleum of the Taj Mahal in Agra',1,'taj mahal,agra,india,Shah Jahan,seven wonders','2011-04-10 16:02:30',1,'John Doe','1970-01-01 00:00:00',0,NULL,1,0,0,NULL);
INSERT INTO "#__content" ("id","catid","title","seotitle","subtitle","introtext","maintext","image","caption","published","metakeys","created","created_by","created_by_name","modified","modified_by","modified_by_name","ordering","hits","alevel","params") VALUES (2,1,'Machu Picchu','machu-picchu','A pre Columbian, Inca empire site that is located almost 8,000 feet above the sea level.','<p>The site is located on a mountain ridge above the valley of Urubamba in Peru. The city is also called <em>the lost city of Incas</em>. When tourists make a list of the best world tour destination, Machu Picchu is the site that undoubtedly gets the maximum number of votes.</p>','<p>It is said that the journey to this spot is a dream journey, which one must never \nmiss. It is a journey to the top archeological site on the planet earth. Data says \nthat the Machu Picchu Tourism is one of the proudest industries in the country. \nWhatever your expectation out of the place would be, you will really be more than \ndelighted to see the picturesque beauty of the place and the intoxicating power \nthat it will have on you.</p>\n\n<p>Machu Picchu was built in 146 AD, however, it was left by the Inca rulers 100 \nyears later. Legendary stories suggest that the place was neglected for quite some \ntime. In the year 1911, Hiram Bingham, who was an American historian, got the \nworld\'s attention to it and then there was no looking back. Recent discoveries \nalso show that the site was dug many years ago by a German businessman. The site \nbecame a part of the UNESCO, world heritage site in 1983. The site as built in \ntypical Inca style with dry stone polished walls and it most famous buildings \nare <em>the temple of sun</em> and <em>the room of three windows</em>.</p>', 'media/images/articles/machu-picchu.jpg','Main View of Machu Picchu, Truly Amazing',1,'machu picchu,incas,peru,empire,colombus,unesco','2011-04-10 16:21:30',1,'John Doe','1970-01-01 00:00:00',0,NULL,2,0,0,NULL);
INSERT INTO "#__content" ("id","catid","title","seotitle","subtitle","introtext","maintext","image","caption","published","metakeys","created","created_by","created_by_name","modified","modified_by","modified_by_name","ordering","hits","alevel","params") VALUES (3,1,'The Pyramids of Giza','giza-pyramids','Egypt has more than 100 pyramids that are spread all over the country.','<p>The 3 best Egyptian pyramids are the <em>Pyramids of Giza</em>. These pyramids \nwere built on the outskirts of Cairo. The Pyramid of Khufu at Giza is said to be \nthe largest Egyptian pyramid ever made in the history. This pyramid figure is one \nof the seven wonders of the worlds. The Pyramid of Kufu is also known as the \nGreat Pyramid.</p>','<p>The Pyramids at Giza are three in number. Three big ones are \ncalled the great pyramid of Khufu (Cheops) , the pyramid of Khafre (Khafra) and \nthe pyramid of Menkaure. The pyramid of Menkaure further has 3 smaller pyramids \nthat are subsidiary to this main pyramid and are called the queens pyramids.</p>\n\n<p>The pyramids at Giza attract maximum tourist attention every year. Out of all \nthe three pyramids at Giza, only the pyramid of Khafre retains some parts of the \noriginally polished limestone casing near its pinnacle. To the naked eyes and laymen, \nthe pyramid of Khafre would always look tall, but the fact is that the pyramid of \nKhufu is the tallest of all. There are a lot of theories regarding the construction \nof the pyramids and a new research shows that the pyramids were made by building \nblocks made out of limestone concrete.</p>', 'media/images/articles/sphinx-giza.jpg','Sphinx at Giza Cairo, Egypt',1,'giza,egypt,pyramids,cheops,sphinx,the great pyramid','2011-04-10 16:30:30',1,'John Doe','1970-01-01 00:00:00',0,NULL,3,0,0,NULL);
INSERT INTO "#__content" ("id","catid","title","seotitle","subtitle","introtext","maintext","image","caption","published","metakeys","created","created_by","created_by_name","modified","modified_by","modified_by_name","ordering","hits","alevel","params") VALUES (4,1,'Parthenon, Athens','parthenon-athens','The Parthenon is a temple in the Athenian Acropolis, Greece, dedicated to the Greek goddess Athena.','<p>Its construction began in 447 BC and was completed in 438 BC, although \ndecorations of the Parthenon continued until 432 BC. It is the most important \nsurviving building of Classical Greece, generally considered to be the culmination \nof the development of the Doric order. Its decorative sculptures are considered \nsome of the high points of Greek art. The Parthenon is regarded as an enduring \nsymbol of Ancient Greece and of Athenian democracy and one of the worlds greatest \ncultural monuments.</p>','<p>The Parthenon itself replaced an older temple of Athena, which historians call \nthe Pre-Parthenon or Older Parthenon, that was destroyed in the Persian invasion \nof 480 BC. Like most Greek temples, the Parthenon was used as a treasury. For a \ntime, it served as the treasury of the Delian League, which later became the \nAthenian Empire. In the 5th century AD, the Parthenon was converted into a \nChristian church dedicated to the Virgin Mary. After the Ottoman Turk conquest, \nit was turned into a mosque in the early 1460s, and it had a minaret built in \nit. On 26 September 1687, an Ottoman Turk ammunition dump inside the building \nwas ignited by Venetian bombardment. The resulting explosion severely damaged \nthe Parthenon and its sculptures. In 1806, Thomas Bruce, 7th Earl of Elgin \nremoved some of the surviving sculptures, with the Ottoman Turks permission. \nThese sculptures, now known as the Elgin Marbles or the Parthenon Marbles, \nwere sold in 1816 to the British Museum in London, where they are now \ndisplayed.</p>', 'media/images/articles/parthenon-athens.jpg','The Parthenon in the Athenian Acropolis',1,'parthenon,athens,acropolis,greece,athena','2011-04-10 17:00:30',1,'John Doe','1970-01-01 00:00:00',0,NULL,4,0,0,NULL);
INSERT INTO "#__content" ("id","catid","title","seotitle","subtitle","introtext","maintext","image","caption","published","metakeys","created","created_by","created_by_name","modified","modified_by","modified_by_name","ordering","hits","alevel","params") VALUES (5,1,'Stonehenge in Wiltshire','stonehenge','Stonehenge is a prehistoric and a mystical monument which is located in the county of Wiltshire, England.','<p>It is situated almost 3.2 km west of Amesbury. The Stonehenge is a formation of stones in a circular \nfashion that are standing upright. The construction of this prehistoric monument started some 5000 \nyears ago and it is amazing to know that these stones are still in place after 5 millenniums.</p>','<p>Different scholars have different thought about the Stonehenge. Some say that \nthe stones initially were to erect; it was only after 2400 BC that they changed \npositions. Others say that the blue stones could have been the ones that were \nerected. The site of Stonehenge is also another addition to the UNESCO and a \npart of the World heritage Sites since 1986. It is also protected by the \nscheduled Ancient monument. The Stonehenge is a pride of the national trust \nof the country.</p>\n\n<p>Recent evidence has shown that the Stonehenge has served as a burial ground \nright from the beginning. It has been notified that the people who have been \nburied in the ground date back to the 3000 BC. The huge stone formation and \na mysterious past is what draw maximum tourist attraction every year to this \nsite. It is said that almost 8,000,000 visitors come to visit this place. \nAll the stones are said to be placed perfectly with the sunrise and thus \nmakes it very clear that the place was also being used as a ground for worship.</p>', 'media/images/articles/stonehenge.jpg','Stonehenge View With Lightning in Wiltshire, England',1,'stonehenge,prehistoric,england,amesbury,unesco','2011-04-10 17:10:30',1,'John Doe','1970-01-01 00:00:00',0,NULL,5,0,0,NULL);
INSERT INTO "#__content" ("id","catid","title","seotitle","subtitle","introtext","maintext","image","caption","published","metakeys","created","created_by","created_by_name","modified","modified_by","modified_by_name","ordering","hits","alevel","params") VALUES (6, 2, 'Extensions', 'extensions', 'Elxis extensions extend the built-in functionality.', '<p>Elxis has some global extension types and some others that are component specific. The most important extensions are the <strong>Components</strong>. A component handles the user request and generates the corresponding output which is visible in the center area of the site. Only one component can run in each page. Depending on the user&apos;s request Elxis decides which component to load. <strong>Modules</strong>, on the other hand are small blocks shown arround the component on the left, right, top and bottom areas of the site.</p>', '<p>The complete list of the built-in extension types follows.</p>\n<ul class=\"elx_stdul\"><li>Components</li><li>Modules</li><li>Content plugins</li><li>Authentication methods</li><li>Search engines</li><li>Templates</li></ul>\n<h3>Content plugins</h3>\n<p>Plugins are extensions bind to component <strong>Content </strong>and their scope is to make possible to easily import into standard content items things like image galleries, contact forms and videos. Plugins are based on a find and replace system which replaces small blocks of code into HTML code. To make things even easier Elxis provides you with a guided plugin code generation and import system.</p>\n\n<h3>Authentication methods</h3>\n<p>Elxis accepts user login from external providers such as OpenId, LDAP, GMail and Twitter. Elxis Authentication methods are component <strong>User </strong>specific extensions that provide this extra functionality. You can install new Authentication methods and manage them as any other extension type.</p>\n\n<h3>Search engines</h3>\n<p>People usually search for articles matching some given search criteria. But what about searching other things like video, images or people? Well, Search Engines are exactly for this. These component&apos;s <strong>Search</strong> extensions allows you to extend search on anything you can imagine.</p>\n\n<h3>Templates</h3>\n<p>Templates handles the structure and the style of the generated pages. The template controls things like the side columns of the site and the position of the modules. There are templates for the site&apos;s frontend area but also for the back-end area. Templates can also provide the structure and the layout for the <strong>Exit Pages</strong> (error 403, 404, etc) and for the <strong>mobile version</strong> of the site.</p>', NULL, '', 1, 'elxis,extensions,modules,components,plugins,authentication methods,search engines,templates', '2012-06-01 10:23:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 4, 0, 0, 'art_dateauthor=-1\n art_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_hits=-1\nart_comments=-1\nart_tags=-1\nart_chain=-1'); 
INSERT INTO "#__content" ("id","catid","title","seotitle","subtitle","introtext","maintext","image","caption","published","metakeys","created","created_by","created_by_name","modified","modified_by","modified_by_name","ordering","hits","alevel","params") VALUES (7, 2, 'FAQs', 'faqs', 'Answers to frequently asked questions regarding Elxis CMS usage.', '<p>Elxis 4.x is a modern porerful content management system having many cool features. Some of them are really unique. We advise you to take your time and explore Elxis. Every day you use Elxis you will discover new ways to do things faster and easier. It is impossible to list all the things you can do with Elxis and even more provide a detailed how-to guide for all of them. In this article we provide answers to most frequently questions people ask us. For anything else please visit the Elxis forums or the online documentation for support.</p>', '<h4 class=\"elx_question\">How do I set the site frontpage?</h4>\n<p class=\"elx_answer\">You set the site frontpage from Elxis configuration by providing the Elxis URI to the page you want to be shown in frontpage. By default this is the root of component content (<em>content:/</em>). Component content has a special feature that allows you to generate complex <strong>grid layouts</strong> for the frontpage. On each cell of this grid you can display any number of Elxis <strong>modules</strong>.</p>\n\n<h4 class=\"elx_question\">Does Elxis support sub-categories?</h4>\n<p class=\"elx_answer\">Yes, Elxis supports <strong>sub-categories of any level</strong>. For SEO reasons and easy of access we suggest you to create sub-categories up to the second or third level.</p>\n\n<h4 class=\"elx_question\">I need a second, or more, site on the same domain. Do I have to re-install Elxis?</h4>\n<p class=\"elx_answer\">No, you don&apos;t. With the Elxis <strong>multi-sites</strong> feature you can have an unlimited number of sub-sites under one mother site. These sites share the same filesystem but have different configuration, data, template, users, etc, making them independent. Although the sites are independent they still share the same filesystem. The Elxis Team recommendation is that the administrators of these sub-sites should be trustful to the admin of the mother site.</p>\n\n<h4 class=\"elx_question\">How do I configure a page?</h4>\n<p class=\"elx_answer\">Till Elxis 2009.x generation the layout and features of a page was controlled by the menu item that linked to that page. This system confused new users and many times lead to issues such as error 404. Since Elxis 4.x this has changed. The layout and features of a page is now controlled by the page it self. Each category or article has a set of <strong>parameters</strong> were you can set options such as the show of article author, hits, print links, if you allow comments, etc. You can also set global options, per category options and per article options. Specific element options overwrite the more generic ones. For instance you may allow comments for all articles in a category but disable commentary for an idividual article.</p>\n\n<h4 class=\"elx_question\">How do I create a blog?</h4>\n<p class=\"elx_answer\">Elxis content categories and articles are multi-functional. You don&apos;t need a special blog component to have a blog. A standard category can act as a blog by setting it&apos;s articles to be listed in a blog style. You can also make use of tags, comments, share and social buttons and anything else a blog should have.</p>\n\n<h4 class=\"elx_question\">I concern about security. How does Elxis goes with it?</h4>\n<p class=\"elx_answer\">In the Elxis world security is the first priority. Elxis is shipped with an attack detection and protection system called <strong>Elxis Defender</strong>. Among others, Elxis Defender can block bad requests to your site, detect file system changes and send alert notitifactions to the site&apos;s technical manager. There are many other security relared features like the <strong>Security Level</strong> configuration option, the automatic <strong>SSL/TLS switch</strong>, the usage of the PHP&apos;s native <strong>PDO</strong> library for handling the database which makes impossible SQL injection, the security images (captcha) and the XSS prevention system for the forms, the double authentication check for the administration area, the tight <strong>user access</strong> system, the in-accessible from the web <strong>Elxis repository</strong> (including session storage), the session <strong>encryption</strong> and much more.</p>\n\n<h4 class=\"elx_question\">Where the administrator folder went?</h4>\n<p class=\"elx_answer\">There is no administrator folder in Elxis any more! There is a folder named <em>estia</em> containg a file that initiates the administration user requests but nothing more. Note that you can rename that folder to anything you want.</p><h4 class=\"elx_question\">Can I have content in multiple languages?</h4>\n\n<p class=\"elx_answer\">Yes, Elxis has <strong>full multilingual support</strong>. Each element (article, category, etc) is initially entered in the site&apos;s main language. You can after add to the elements unlimited number of translations in different languages.</p>\n\n<h4 class=\"elx_question\">Can people login without registering?</h4>\n<p class=\"elx_answer\">Yes, Elxis supports <strong>external authentication</strong> methods such as Twitter, Gmail, OpenId, Yahoo and LDAP which make possible logging in with your account in any of the supported authentication providers.</p>', NULL, '', 1, 'faqs,frontpage,sub-categories,elxis defender,multi-sites,security,multilingual,translations,openid', '2012-06-01 11:23:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 3, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_hits=-1\nart_comments=-1\nart_tags=-1\nart_chain=-1');
INSERT INTO "#__content" ("id","catid","title","seotitle","subtitle","introtext","maintext","image","caption","published","metakeys","created","created_by","created_by_name","modified","modified_by","modified_by_name","ordering","hits","alevel","params") VALUES (8, 0, 'Contact us', 'contact-us', 'We are located in the center of Athens. Feel free to contact us by submiting the contact form.', '<div class=\"elx_info\">You will find Sample Company at Vasilissis Amalias 999, at the center of Athens. You can contact us by phone, fax or send us an e-mail. You can also fill-in and submit the contact form bellow.</div>', '<br /><ul class=\"elx_stdul\" style=\"display:block; float:left;width:50%; min-width:300px;\"><li><strong>Sample company</strong></li><li>Vasilissis Amalias 999,</li><li>12345,</li><li>Athens, Greece</li><li>Telephone: 30-111-1234567</li><li>FAX: 30-111-7654321</li><li>E-mail: sample@example.com</li></ul>\n<div class=\"elx_content_imagebox\" style=\"float:left; width:46%; min-width:300px;\"><img alt=\"map\" src=\"media/images/sample/map.jpg\" /></div>\n<div class=\"clear\"></div>\n<p><code>{contact}noone@example.com{/contact}</code></p>', NULL, '', 1, 'contact,sample company,email,telephone', '2012-05-26 18:03:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 3, 0, 0, 'art_dateauthor=0\nart_dateauthor_pos=-1\nart_img=-1\nart_print=0\nart_email=0\nart_hits=0\nart_comments=0\nart_tags=0\nart_chain=0');
INSERT INTO "#__content" ("id","catid","title","seotitle","subtitle","introtext","maintext","image","caption","published","metakeys","created","created_by","created_by_name","modified","modified_by","modified_by_name","ordering","hits","alevel","params") VALUES (9, 0, 'Typography', 'typography', 'Template typography preview.', '<p>This article demonstrates how the current template styles basic HTML elements and Elxis specific CSS classes.</p>', '<h2>Generic typography styles</h2>\n<h1>This is an H1 header</h1>\n<h2>This is an H2 header</h2>\n<h3>This is an H3 header</h3>\n<h4>This is an H4 header</h4>\n<h5>This is an H5 header</h5>\n<p>This is a paragraph containing a <a href=\"http://www.elxis.org\" title=\"elxis cms\" target=\"_blank\">sample link to elxis.org</a> and some <strong>strong text</strong>. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut eget mi vitae nunc tincidunt cursus ac at ligula. Aliquam dignissim mi sit amet tortor sodales fermentum. Aenean blandit suscipit erat auctor interdum. Pellentesque varius, lorem quis viverra imperdiet, nunc sem rhoncus ante, non varius justo metus in urna.</p><br />\n<pre>Preformated text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut eget mi vitae nunc tincidunt cursus ac at ligula. Aliquam dignissim mi sit amet tortor sodales fermentum.</pre><br />\n\n<blockquote>Blockquote text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut eget mi vitae nunc tincidunt cursus ac at ligula. Aliquam dignissim mi sit amet tortor sodales fermentum. Aenean blandit suscipit erat auctor interdum. Pellentesque varius, lorem quis viverra imperdiet, nunc sem rhoncus ante, non varius justo metus in urna.</blockquote><br />\n\n<h2>Elxis specific typography styles</h2>\n<p class=\"elx_info\">This is an information message (&lt;p class=&quot;elx_info&quot;&gt;text&lt;/p&gt;)</p>\n<p class=\"elx_warning\">This is a warning message (&lt;p class=&quot;elx_warning&quot;&gt;text&lt;/p&gt;)</p>\n<p class=\"elx_error\">This is an error message (&lt;p class=&quot;elx_error&quot;&gt;text&lt;/p&gt;)</p>\n<p class=\"elx_success\">This is a success message (&lt;p class=&quot;elx_success&quot;&gt;text&lt;/p&gt;)</p>\n<p class=\"elx_textblock\">This is a plain text box (&lt;p class=&quot;elx_textblock&quot;&gt;text&lt;/p&gt;)</p>\n<div class=\"elx_sminfo\">This is a small information message (&lt;div class=&quot;elx_sminfo&quot;&gt;text&lt;/div&gt;)</div>\n<div class=\"elx_smwarning\">This is a small warning message (&lt;div class=&quot;elx_smwarning&quot;&gt;text&lt;/div&gt;)</div>\n<div class=\"elx_smerror\">This is a small error message (&lt;div class=&quot;elx_smerror&quot;&gt;text&lt;/div&gt;)</div>\n<div class=\"elx_smsuccess\">This is a small success message (&lt;div class=&quot;elx_smsuccess&quot;&gt;text&lt;/div&gt;)</div>\n<br /><br />\n<div class=\"module\">\n<h3>Module title</h3>\n<p>Generic module style with outer div wrapper (class module) and module title shown.</p>\n</div>\n<br />\n<ul class=\"elx_stdul\"><li>Unordered items list (class: elx_stdul)</li><li>Unordered item</li><li>Unordered item</li></ul>\n<ol class=\"elx_stdol\"><li>Ordered items list (class: elx_stdol)</li><li>Ordered item</li><li>Ordered item</li></ol><br />\n<h3>Navigation through pages</h3>\n<div class=\"elx_navigation\"><span class=\"elx_nav_page\">Page</span> <a href=\"#\" title=\"Page 1\" class=\"elx_nav_link_active\">1</a> <a href=\"#\" title=\"Page 2\" class=\"elx_nav_link\">2</a> <a href=\"#\" title=\"Page 3\" class=\"elx_nav_link\">3</a> <a href=\"#\" title=\"Page 4\" class=\"elx_nav_link\">4</a> </div><br />\n<div class=\"elx_featured_box\">\n<h2><a href=\"#\" title=\"Sample featured article\">Sample featured article</a></h2>\n<div class=\"elx_dateauthor\">Sample date and <a href=\"#\">Author</a></div>\n<div class=\"elx_category_featured_inner\">\n<div class=\"elx_content_imagebox\" style=\"margin:0 5px 5px 0; float:left; width:210px;\"><a href=\"#\" title=\"Sample featured article\"><img src=\"/templates/system/images/nopicture_article.jpg\" alt=\"image\" border=\"0\" width=\"200\" style=\"width:200px;\" /></a><div>Sample image caption</div></div>\n<p class=\"elx_content_subtitle\">Sample featured article sub-title.</p><p>Sample featured article. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut eget mi vitae nunc tincidunt cursus ac at ligula. Aliquam dignissim mi sit amet tortor sodales fermentum. Aenean blandit suscipit erat auctor interdum. Pellentesque varius, lorem quis viverra imperdiet, nunc sem rhoncus ante, non varius justo metus in urna. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Sed in nunc mi. Cras quis lectus risus. Nulla non pharetra metus. Ut ut euismod mi. Etiam ut leo id tellus rhoncus convallis sit amet rhoncus ipsum. Sed non ligula nibh.</p>\n<div style=\"clear:both;\"></div>\n</div>\n</div><br /><br />\n<div class=\"elx_short_box\">\n<div class=\"elx_content_imagebox\" style=\"margin:0 5px 5px 0; float:left; width:110px;\"><a href=\"#\" title=\"sample article\"><img src=\"/templates/system/images/nopicture_article.jpg\" alt=\"image\" border=\"0\" width=\"100\" style=\"width:100px;\" /></a></div>\n<h3><a href=\"#\" title=\"Sample short article\">Sample short article</a></h3>\n<div class=\"elx_dateauthor\">Sample date and Author</div>\n<p class=\"elx_content_short\">Sample short article. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut eget mi vitae nunc tincidunt cursus ac at ligula. Aliquam dignissim mi sit amet tortor sodales fermentum. Aenean blandit suscipit erat auctor interdum...</p>\n<div style=\"clear:both;\"></div></div>\n<br /><br />\n<form name=\"sample\" class=\"elx_form\" method=\"get\" action=\"http://www.google.com/\">\n<fieldset class=\"elx_form_fieldset\" dir=\"ltr\">\n<legend class=\"elx_form_legend\">Sample form</legend>\n<div class=\"elx_form_row\"><label for=\"sampletext\" class=\"elx_form_label\" style=\"width:200px; text-align:left;\">Input text</label><input type=\"text\" name=\"sampletext\" id=\"sampletext\" value=\"\" title=\"Input text\" maxlength=\"60\" class=\"inputbox\" dir=\"ltr\" /> <span class=\"elx_form_tip\">This is a sample tip message</span></div>\n<div class=\"elx_form_row\"><div class=\"elx_form_cell\" style=\"width:99%;\"><label for=\"sampleselect\" class=\"elx_form_label\" style=\"width:200px; text-align:left;\">Select box</label><select name=\"sampleselect\" id=\"sampleselect\" title=\"Select box\" class=\"selectbox\" dir=\"ltr\"><option value=\"0\" selected=\"selected\">option 1</option><option value=\"1\">option 2</option><option value=\"2\">option 3</option></select><br /><br /></div>\n</div>\n<div class=\"elx_form_row\"><label class=\"elx_form_label\" style=\"width:200px; text-align:left;\">Radio box</label>\n<div class=\"elx_form_field_box\" style=\"margin-left:200px;\"><input type=\"radio\" name=\"sampleradio\" id=\"sampleradio_1\" value=\"option1\" checked=\"checked\" /><label for=\"sampleradio_1\" class=\"elx_form_label_option\">Option 1</label><input type=\"radio\" name=\"sampleradio\" id=\"sampleradio_2\" value=\"option2\" /><label for=\"sampleradio_2\" class=\"elx_form_label_option\">Option 2</label></div>\n<div style=\"clear:both;\"></div></div>\n<div class=\"elx_form_row\">\n<div class=\"elx_form_nolabel\" style=\"width:200px;\">&#160;</div>\n<div class=\"elx_form_field_box\" style=\"margin-left:200px;\"><button type=\"submit\" name=\"samplebtn1\" title=\"Generic submit\" dir=\"ltr\">Generic submit button</button><br /><button type=\"button\" name=\"samplebtn2\" title=\"Elxis button\" class=\"elxbutton\" dir=\"ltr\">Elxis standard button</button><br /><button type=\"button\" name=\"samplebtn3\" title=\"Elxis standard button\" class=\"elxbutton-save\" dir=\"ltr\">Elxis save button</button><br /><button type=\"button\" name=\"samplebtn4\" title=\"Elxis search button\" class=\"elxbutton-search\" dir=\"ltr\">Elxis search button</button></div>\n<div style=\"clear:both;\"></div></div>\n</fieldset>\n</form>\n<br />', NULL, '', 1, 'typography,stylesheet,style,html elements,elxinfo,elxwarning,headings', '2012-06-01 20:23:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 1, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_hits=-1\nart_comments=0\nart_tags=0\nart_chain=0');
INSERT INTO "#__content" ("id","catid","title","seotitle","subtitle","introtext","maintext","image","caption","published","metakeys","created","created_by","created_by_name","modified","modified_by","modified_by_name","ordering","hits","alevel","params") VALUES (10, 0, 'Sample gallery', 'sample-gallery', 'Convert any article to an image gallery.', '<p>You can convert any typical article to an image <strong>gallery</strong> by using the gallery, or other similar, <strong>plugin</strong>. Just insert the plugin code inside the article text area in the exact spot you want the gallery to be displayed and your image gallery is ready!</p>', '<h3>Greek landscapes</h3>\n<p>Pictures from Athens, Chania, Chalkidiki, Meteora, Parga, Santorini, Skiathos and more.</p><br />\n\n{gallery}media/images/sample_gallery/{/gallery}\n\n<br />\n<p class=\"elx_info\">The code used to generate the above gallery can be shown bellow.<br />\n<strong>&#123;gallery&#125;media/images/sample_gallery/&#123;/gallery&#125;</strong></p>', NULL, NULL, 1, 'gallery,plugin,article,sample gallery,greek,landscapes', '2012-06-01 20:23:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 2, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_hits=-1\nart_comments=0\nart_tags=0\nart_chain=0');
INSERT INTO "#__content" ("id","catid","title","seotitle","subtitle","introtext","maintext","image","caption","published","metakeys","created","created_by","created_by_name","modified","modified_by","modified_by_name","ordering","hits","alevel","params") VALUES (11, 2, 'Features', 'features', 'The most important Elxis features.', '<p>Elxis is a powerful and rich CMS having many of the features you will need for your site built-in. You can extend more Elxis by installing additional <a href=\"#elink:content:elxis/extensions.html\" title=\"Elxis extensions types\">extensions</a>. This article lists the most important Elxis features you will find built-in.</p>', '<ul class=\"elx_stdul\">\n<li>High quality object oriented programming in PHP5 (ready for PHP6).</li><li>PDO database layer</li><li>Multiligual user interface and content.</li><li>Mobile and tablet friendly version.</li><li>External authentication methods like Twitter, Gmail, OpenId, etc...</li><li>Extendable and configurable user groups and permissions.</li><li>Elxis Defender, Security Level, SSL/TLS, encryption and other security related features.</li><li>Problem notification (automatic email notifications to site&apos;s technical manager on security alerts, fatal errors, and more).</li>\n<li>File and APC cache.</li><li>Small footprint</li><li>Multi-sites support</li><li>Right-To-Left languages support.</li><li>Sub-categories of any level.</li><li>Multi-functional articles and categories.</li>\n<li>Built-in commentary system.</li><li>RSS/ATOM feeds per category.</li><li>APIs for all core features makes developer work extremely easy.</li><li>Open search - search through your browser&apos;s search box.</li><li>Multiple supported doctypes - XHTML 5, HTML 5, XHTML 1.1 tranditional, XHTML 1.1 strict.</li><li>Mobile ready</li>\n<li>Custom exit pages (page not found, forbidden, error, etc)</li><li>Extendable search system (search for content, images, videos, and more).</li><li>Image galleries</li><li>Contact forms</li><li>Multi-level suckerfish style menus.</li><li>Highly configurable category and article pages.</li><li>jQuery ready.</li><li>Site traffic statistics.</li><li>Highly configurable frontpage.</li>\n<li>Easy and powerful internal linking system and search engine friendly URLs (Elxis URIs).</li><li>One-click extension install/update/un-install.</li><li>Elxis repository</li><li>Powerful templating system for the site&apos;s frontend and backend sections.</li><li>Visitors can display dates based on their own location.</li>\n<li>Automatic generation and expansion of menus for components.</li><li>Performance monitor</li><li>System debug with many report levels.</li><li>One-click complete file system and database backup.</li><li>System logs (logging errors, security alerts, install/update/un-install actions, and more).</li><li>WYSIWYG editor with many features like spell checker, image uploading, styling, and more</li>\n<li>Media manager</li><li>Translations manager</li><li>FTP support</li><li>UTF-8 support</li><li>Users central, members list and rich user profile.</li><li>Custom icon packages</li>\n</ul>', NULL, NULL, 1, 'elxis features,pdo,multilingual,security,defender,multi-sites,open search, statistics', '2012-06-01 12:23:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 2, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_hits=-1\nart_comments=-1\nart_tags=-1\nart_chain=-1');
INSERT INTO "#__content" ("id","catid","title","seotitle","subtitle","introtext","maintext","image","caption","published","metakeys","created","created_by","created_by_name","modified","modified_by","modified_by_name","ordering","hits","alevel","params") VALUES (12, 2, 'What is Elxis?', 'what-is-elxis', 'An introduction to the Elxis world.', '<p>Elxis is an open source content management system (CMS) written in PHP programming language. It was born on December 2005 and since then it is used by thousands of people in all over the world. Elxis is famous for its stability and security, for the well tested extensions, for the multi-lingual features and the unique ideas we have implemented in it through these years many of which copied by other CMSs later.</p>', '<h4 class=\"elx_question\">What can I do with Elxis?</h4>\n<p>With Elxis you can easily build small, medium or large scale web sites without the need of having programming skills (although basic knowledge of HTML/CSS is recommended for best results). You can create news portals, personal blogs, online magazines, business sites, online shops, community portals and more.</p>\n\n<h4 class=\"elx_question\">What do I need to use Elxis?</h4>\n<p>Except Elxis, you will need some place to host your site online (although you can install Elxis in your local computer too). Your hosting provider should provide you a web server such as Apache, lighttpd or nginx, able to run PHP scripts and a database such as MySQL or PostgreSQL. We recommend you to pick Linux as the web server operating system. Any Linux distribution is fine. For business web sites we strongly recommend new users to assign the build of their web site to a highly trained professional. You will save time and get the best result.</p>\n\n<h4 class=\"elx_question\">Who owns Elxis?</h4>\n<p>Elxis is been developed by the <strong>Elxis Team</strong>, a group of friends pashioned with the open source software. There are no big companies behind Elxis, there are no sponsors, advertizers, or hidden financial interests. Elxis is independent, a pure open source project. For legal purposes Elxis is represented by <strong>Ioannis Sannos</strong>, the core developer of Elxis, located in Athens, Greece.</p>\n\n<h4 class=\"elx_question\">The Elxis license</h4>\n<p>Elxis is released for free under the <a href=\"http://www.elxis.org/elxis-public-license.html\" target=\"_blank\" title=\"EPL\">Elxis Public License</a> (EPL). In short EPL grands you or limits you the following permissions.</p>\n\n<ul class=\"elx_stdul\">\n<li>You can use Elxis for any web site you want, even commercial ones.</li>\n<li>You are allowed to provide paid services and <a href=\"#elink:content/elxis/extensions.html\" title=\"elxis extenions types\">extensions</a> for Elxis (custom development, web hosting, support, templates, training, etc).</li>\n<li>You are not allowed to sell Elxis.</li>\n<li>You are not allowed to re-brand or rename Elxis.</li>\n<li>You are not allowed to modify or remove the Elxis copyright notes.</li>\n<li>You can create extensions for Elxis of any license. These extensions should be installed after the initial Elxis installation. You can not include them into the official Elxis package even if they are free ones (you are not allowed to re-pack Elxis).</li>\n<li>Elxis Team is not responsible for any damages may occur to your web site such as data or money loss by the use of Elxis.</li>\n<li>You can share and re-distribute only original copies of Elxis as released by <a href=\"http://www.elxis.org\" title=\"Elxis CMS\" target=\"_blank\">elxis.org</a> website.</li><li>You can modify Elxis only for your own web site.</li>\n<li>You are not allowed to publish or distribute modified versions of Elxis (forks are not allowed).</li>\n<li>Improvements, fixes and new ideas should be send to the Elxis Team for implementation in the official release.</li>\n<li>Elxis copyright holder is Elxis Team having legal representative Ioannis Sannos.</li>\n</ul>', NULL, NULL, 1, 'elxis license,elxis team,open source,cms,EPL,', '2012-06-01 13:23:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 1, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_hits=-1\nart_comments=-1\nart_tags=-1\nart_chain=-1'); 

SELECT setval('"#__content_id_seq"', max("id") ) FROM "#__content"; 


DROP TABLE IF EXISTS "#__engines";
CREATE TABLE "#__engines" (
  "id" SERIAL NOT NULL ,
  "title" VARCHAR(255) NULL ,
  "engine" VARCHAR(100) NULL ,
  "alevel" INTEGER NOT NULL DEFAULT 0,
  "ordering" INTEGER NOT NULL DEFAULT 0,
  "published" SMALLINT NOT NULL DEFAULT 0,
  "defengine" SMALLINT NOT NULL DEFAULT 0,
  "iscore" SMALLINT NOT NULL DEFAULT 0,
  "params" TEXT NULL ,
  PRIMARY KEY ("id")
); 
CREATE INDEX "#__engines_idx_publev" ON "#__engines" ("published","alevel");

INSERT INTO "#__engines" ("id","title","engine","alevel","ordering","published","defengine","iscore","params") VALUES (1,'Content','content',0,1,1,1,1,NULL);
INSERT INTO "#__engines" ("id","title","engine","alevel","ordering","published","defengine","iscore","params") VALUES (2,'Images','images',0,2,1,0,1,NULL);
INSERT INTO "#__engines" ("id","title","engine","alevel","ordering","published","defengine","iscore","params") VALUES (3,'YouTube','youtube',0,3,1,0,1,NULL);

SELECT setval('"#__engines_id_seq"', max("id") ) FROM "#__engines"; 


DROP TABLE IF EXISTS "#__frontpage";
CREATE TABLE "#__frontpage" (
  "id" SERIAL NOT NULL ,
  "pname" VARCHAR(20) NULL ,
  "pval" VARCHAR(255) NULL ,
  PRIMARY KEY ("id")
); 

INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (1,'wl','0');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (2,'wc','100');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (3,'wr','0');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (4,'c1','');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (5,'c2','frontpage2');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (6,'c3','');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (7,'c4','');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (8,'c5','');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (9,'c6','');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (10,'c7','');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (11,'c8','');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (12,'c9','');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (13,'c10','');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (14,'c11','');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (15,'c12','');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (16,'c13','');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (17,'c14','');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (18,'c15','');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (19,'c16','');
INSERT INTO "#__frontpage" ("id","pname","pval") VALUES (20,'c17','');

SELECT setval('"#__frontpage_id_seq"', max("id") ) FROM "#__frontpage"; 


DROP TABLE IF EXISTS "#__groups";
CREATE TABLE "#__groups" (
  "gid" SERIAL NOT NULL ,
  "level" SMALLINT NOT NULL DEFAULT 0,
  "groupname" VARCHAR(120) NULL ,
  PRIMARY KEY ("gid")
); 

INSERT INTO "#__groups" ("gid", "level", "groupname") VALUES (1,100,'Administrator'),
INSERT INTO "#__groups" ("gid", "level", "groupname") VALUES (2,70,'Manager'),
INSERT INTO "#__groups" ("gid", "level", "groupname") VALUES (3,50,'Publisher'),
INSERT INTO "#__groups" ("gid", "level", "groupname") VALUES (4,30,'Author'),
INSERT INTO "#__groups" ("gid", "level", "groupname") VALUES (5,2,'User'),
INSERT INTO "#__groups" ("gid", "level", "groupname") VALUES (6,1,'External user'),
INSERT INTO "#__groups" ("gid", "level", "groupname") VALUES (7,0,'Guest');

SELECT setval('"#__groups_gid_seq"', max("gid") ) FROM "#__groups";


DROP TABLE IF EXISTS "#__menu";
CREATE TABLE "#__menu" (
  "menu_id" SERIAL NOT NULL ,
  "title" VARCHAR(255) NULL ,
  "section" VARCHAR(25) NULL ,
  "collection" VARCHAR(100) NULL ,
  "menu_type" VARCHAR(50) NULL ,
  "link" VARCHAR(255) NULL ,
  "file" VARCHAR(25) NULL ,
  "popup" SMALLINT NOT NULL DEFAULT 0,
  "secure" SMALLINT NOT NULL DEFAULT 0,
  "published" SMALLINT NOT NULL DEFAULT 0,
  "parent_id" INTEGER NOT NULL DEFAULT 0,
  "ordering" INTEGER NULL DEFAULT 0,
  "expand" SMALLINT NOT NULL DEFAULT 0,
  "target" VARCHAR(20) NULL ,
  "alevel" INTEGER NOT NULL DEFAULT 0,
  "width" INTEGER NOT NULL DEFAULT 0,
  "height" INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY ("menu_id")
); 
CREATE INDEX "#__menu_idx_menu" ON "#__menu" ("section","published","alevel");

INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (1,'Home','frontend','mainmenu','link','content:/','index.php',0,0,1,0,1,0,'_self',0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (2,'Famous places','frontend','mainmenu','link','content:famous-places/','index.php',0,0,1,0,2,2,'_self',0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (3,'Gallery','frontend','mainmenu','link','content:sample-gallery.html','index.php',0,0,1,0,3,0,NULL,0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (4,'Contact us','frontend','mainmenu','link','content:contact-us.html','index.php',0,0,1,0,4,0,NULL,0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (5,'Elxis','frontend','mainmenu','link','content:elxis/','index.php',0,0,1,0,5,0,NULL,0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (6,'Typography','frontend','mainmenu','link','content:typography.html','index.php',0,0,1,0,6,0,NULL,0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (7,'Administration','frontend','mainmenu','url','http://localhost/estia/',NULL,0,0,1,0,7,0,NULL,0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (8,'Home','frontend','topmenu','link','content:/','index.php',0,0,1,0,8,0,'_self',0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (9,'Famous places','frontend','topmenu','link','content:famous-places/','index.php',0,0,1,0,9,2,'_self',0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (10,'Gallery','frontend','topmenu','link','content:sample-gallery.html','index.php',0,0,1,0,10,0,NULL,0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (11,'Contact us','frontend','topmenu','link','content:contact-us.html','index.php',0,0,1,0,11,0,NULL,0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (12,'Elxis','frontend','topmenu','link','content:elxis/','index.php',0,0,1,0,12,0,NULL,0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (13,'Typography','frontend','topmenu','link','content:typography.html','index.php',0,0,1,0,13,0,NULL,0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (14,'Administration','frontend','topmenu','url','http://localhost/estia/',NULL,0,0,1,0,14,0,NULL,0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (15,'Elxis CMS','frontend','footermenu','url','http://www.elxis.org',NULL,0,0,1,0,15,0,'_blank',0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (16,'Elxis forum','frontend','footermenu','url','http://forum.elxis.org',NULL,0,0,1,0,16,0,'_blank',0,0,0);
INSERT INTO "#__menu" ("menu_id","title","section","collection","menu_type","link","file","popup","secure","published","parent_id","ordering","expand","target","alevel","width","height") VALUES (17,'Elxis docs','frontend','footermenu','url','http://www.elxis.net/docs/',NULL,0,0,1,0,17,0,'_blank',0,0,0);

SELECT setval('"#__menu_menu_id_seq"', max("menu_id") ) FROM "#__menu"; 


DROP TABLE IF EXISTS "#__modules";
CREATE TABLE "#__modules" (
  "id" SERIAL NOT NULL,
  "title" VARCHAR(255) NULL,
  "content" TEXT NULL,
  "ordering" INTEGER NOT NULL DEFAULT 0,
  "position" VARCHAR(20) NULL,
  "published" SMALLINT NOT NULL DEFAULT 0,
  "module" VARCHAR(60) NULL,
  "showtitle" SMALLINT NOT NULL DEFAULT 2,
  "params" TEXT NULL,
  "iscore" SMALLINT NOT NULL DEFAULT 0,
  "section" VARCHAR(20) NULL,
  PRIMARY KEY ("id")
); 
CREATE INDEX "#__modules_idx_pubsection" ON "#__modules" ("published","section");

INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (1, 'Language', NULL, 1, 'language', 1, 'mod_language', 0, 'style=1', 1, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (2, 'Login', NULL, 2, 'left', 1, 'mod_login', 2, NULL, 1, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (3, 'Menu', NULL, 1, 'left', 1, 'mod_menu', 2, 'collection=mainmenu\norientation=0\ncache=1', 1, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (4, 'Search', NULL, 1, 'search', 1, 'mod_search', 0, NULL, 1, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (5, 'Articles', NULL, 4, 'frontpage2', 1, 'mod_articles', 0, 'source=1\ncatid=1\norder=0\nlimit=4\ncolumns=2\nfeatured=1\nfeatured_sub=1\nfeatured_cat=1\nfeatured_date=0\nfeatured_text=180\nfeatured_more=1\nfeatured_caption=0\nfeatured_img=1\nfeatured_imgw=0\nlinks_sub=0\nlinks_cat=1\nlinks_date=0\nlinks_img=3\nlinks_imgw=60', 1, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (6, 'Categories', NULL, 2, 'right', 1, 'mod_categories', 2, NULL, 1, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (7, 'Comments', NULL, 3, 'right', 1, 'mod_comments', 2, 'limit=5\ntitle_limit=30\ncomment_limit=100\nname_limit=14\navatar=1\navatar_w=40', 1, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (8, 'Who is online', NULL, 4, 'right', 1, 'mod_whosonline', 2, NULL, 1, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (9, 'Admin menu', NULL, 1, 'menu', 1, 'mod_adminmenu', 0, NULL, 1, 'backend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (10, 'Admin profile', NULL, 1, 'tools', 1, 'mod_adminprofile', 0, NULL, 1, 'backend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (11, 'Admin search', NULL, 3, 'tools', 1, 'mod_adminsearch', 0, NULL, 1, 'backend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (12, 'Admin language', NULL, 2, 'tools', 1, 'mod_adminlang', 0, NULL, 1, 'backend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (13, 'Open Search', NULL, 5, 'right', 1, 'mod_opensearch', 0, NULL, 1, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (14, 'Online users', NULL, 2, 'cpanel', 1, 'mod_adminusers', 0, NULL, 1, 'backend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (15, 'Latest and Popular articles', NULL, 1, 'cpanelbottom', 1, 'mod_adminarticles', 0, NULL, 1, 'backend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (16, 'Site statistics', NULL, 1, 'cpanel', 1, 'mod_adminstats', 0, NULL, 1, 'backend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (17, 'IOS Slider', NULL, 1, 'frontpage2', 1, 'mod_iosslider', 0, 'source=1\ncatid=1\nthumbspos=1\nlimit=5\nimg_width=470\nimg_height=310\nautoslide=0\ncache=2', 0, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (18, 'Gallery', NULL, 1, 'right', 1, 'mod_gallery', 2, 'limit=12\nwidth=40\ndir=sample_gallery\nlightbox=1\nlink=content:sample-gallery.html', 1, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (19, 'Advertisements', NULL, 3, 'frontpage2', 1, 'mod_ads', 0, 'source=0\nborder=1', 1, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (20, 'The most popular article', NULL, 2, 'frontpage2', 1, 'mod_articles', 0, 'source=1\ncatid=1\nsubcats=0\ncatids=\norder=1\nlimit=1\ncolumns=1\nfeatured=1\nfeatured_sub=1\nfeatured_cat=1\nfeatured_date=0\nfeatured_text=1000\nfeatured_more=1\nfeatured_caption=1\nfeatured_img=4\nfeatured_imgw=0', 1, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (21, 'Custom module', '<div class=\"elx_info\">This is a custom module serving only demo purposes. In Elxis you can create custom user modules, having any content you wish, and display them in any template position or even automatically between the category&#39;s articles.</div>', 5, 'frontpage2', 1, 'mod_content', 0, 'cache=2\nplugins=0', 1, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (22, 'Top menu', NULL, 1, 'menu', 1, 'mod_menu', 0, 'collection=mainmenu\norientation=1\ncache=1', 1, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (23, 'Footer menu', NULL, 1, 'footer', 1, 'mod_menu', 0, 'collection=footermenu\norientation=1\ncache=1', 1, 'frontend');
INSERT INTO "#__modules" ("id","title","content","ordering","position","published","module","showtitle","params","iscore","section") VALUES (24, 'Mobile frontpage', NULL, 1, 'mobilefront', 1, 'mod_mobilefront', 0, NULL, 1, 'frontend');

SELECT setval('"#__modules_id_seq"', max("id") ) FROM "#__modules"; 


DROP TABLE IF EXISTS "#__modules_menu";
CREATE TABLE "#__modules_menu" (
  "mmid" SERIAL NOT NULL ,
  "moduleid" INTEGER NOT NULL DEFAULT 0,
  "menuid" INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY ("mmid")
); 

INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (1, 1, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (2, 2, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (3, 3, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (4, 4, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (5, 5, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (6, 6, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (7, 7, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (8, 8, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (9, 17, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (10, 18, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (11, 19, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (12, 20, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (13, 21, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (14, 22, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (15, 23, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (16, 13, 0);
INSERT INTO "#__modules_menu" ("mmid","moduleid","menuid") VALUES VALUES (17, 24, 0);

SELECT setval('"#__modules_menu_mmid_seq"', max("mmid") ) FROM "#__modules_menu"; 


DROP TABLE IF EXISTS "#__plugins";
CREATE TABLE "#__plugins" (
  "id" SERIAL NOT NULL ,
  "title" VARCHAR(255) NULL ,
  "plugin" VARCHAR(100) NULL ,
  "alevel" INTEGER NOT NULL DEFAULT 0,
  "ordering" INTEGER NOT NULL DEFAULT 0,
  "published" SMALLINT NOT NULL DEFAULT 0,
  "iscore" SMALLINT NOT NULL DEFAULT 0,
  "params" TEXT NULL ,
  PRIMARY KEY ("id")
);

INSERT INTO "#__plugins" ("id","title","plugin","alevel","ordering","published","iscore","params") VALUES (1,'Elxis link','elink',0,1,1,1,NULL);
INSERT INTO "#__plugins" ("id","title","plugin","alevel","ordering","published","iscore","params") VALUES (2,'HTML5 video','video',0,2,1,1,NULL);
INSERT INTO "#__plugins" ("id","title","plugin","alevel","ordering","published","iscore","params") VALUES (3,'Contact form','contact',0,3,1,1,NULL);
INSERT INTO "#__plugins" ("id","title","plugin","alevel","ordering","published","iscore","params") VALUES (4,'Gallery','gallery',0,4,1,1,NULL);
INSERT INTO "#__plugins" ("id","title","plugin","alevel","ordering","published","iscore","params") VALUES (5,'Automatic links','autolinks',0,5,1,1,NULL);
INSERT INTO "#__plugins" ("id","title","plugin","alevel","ordering","published","iscore","params") VALUES (6,'YouTube video','youtube',0,6,1,1,NULL);
INSERT INTO "#__plugins" ("id","title","plugin","alevel","ordering","published","iscore","params") VALUES (7,'Google maps','map',0,7,1,1,NULL);
INSERT INTO "#__plugins" ("id","title","plugin","alevel","ordering","published","iscore","params") VALUES (8,'Page Break','pagebreak',0,8,1,1,NULL);

SELECT setval('"#__plugins_id_seq"', max("id") ) FROM "#__plugins";


DROP TABLE IF EXISTS "#__session";
CREATE TABLE "#__session" (
  "session_id" VARCHAR(60) NOT NULL ,
  "uid" INTEGER NOT NULL DEFAULT 0,
  "gid" SMALLINT NOT NULL DEFAULT 0,
  "login_method" VARCHAR(20) NULL ,
  "first_activity" INTEGER NOT NULL DEFAULT 0,
  "last_activity" BIGINT NULL ,
  "clicks" INTEGER NOT NULL DEFAULT 0,
  "current_page" VARCHAR(255) NULL ,
  "ip_address" VARCHAR(40) NOT NULL DEFAULT '0',
  "user_agent" VARCHAR(255) NULL ,
  "session_data" TEXT NULL ,
  PRIMARY KEY ("session_id")
); 
CREATE INDEX "#__session_idx_uid" ON "#__session" ("uid");


DROP TABLE IF EXISTS "#__statistics";
CREATE TABLE "#__statistics" (
  "id" SERIAL NOT NULL ,
  "statdate" VARCHAR(10) NULL ,
  "clicks" INTEGER NOT NULL DEFAULT 0,
  "visits" INTEGER NOT NULL DEFAULT 0,
  "langs" VARCHAR(255) NULL ,
  PRIMARY KEY ("id"),
  UNIQUE ("statdate")
);


DROP TABLE IF EXISTS "#__statistics_temp";
CREATE TABLE "#__statistics_temp" (
  "id" BIGSERIAL NOT NULL ,
  "uniqueid" VARCHAR(45) NULL ,
  PRIMARY KEY ("id"),
  UNIQUE ("uniqueid")
);


DROP TABLE IF EXISTS "#__templates";
CREATE TABLE "#__templates" (
  "id" SERIAL NOT NULL ,
  "title" VARCHAR(120) NULL ,
  "template" VARCHAR(60) NULL ,
  "section" VARCHAR(20) NULL ,
  "iscore" SMALLINT NOT NULL DEFAULT 0,
  "params" TEXT NULL ,
  PRIMARY KEY ("id")
); 
CREATE INDEX "#__templates_idx_template" ON "#__templates" ("template");

INSERT INTO "#__templates" ("id","title","template","section","iscore","params") VALUES (1,'Delta','delta','frontend',1,NULL);
INSERT INTO "#__templates" ("id","title","template","section","iscore","params") VALUES (2,'Iris','iris','backend',1,NULL);
INSERT INTO "#__templates" ("id","title","template","section","iscore","params") VALUES (3,'Aiolos','aiolos','frontend',1,NULL);

SELECT setval('"#__templates_id_seq"', max("id") ) FROM "#__templates"; 


DROP TABLE IF EXISTS "#__template_positions";
CREATE TABLE "#__template_positions" (
  "id" SERIAL NOT NULL ,
  "position" VARCHAR(20) NULL ,
  "description" VARCHAR(255) NULL ,
  PRIMARY KEY ("id")
);

INSERT INTO "#__template_positions" ("id","position","description") VALUES (1,'left','The left column of your template');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (2,'right','The right column of your template');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (3,'menu','Horizontal menu used in both frontend and backend');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (4,'footer','Default position for the footer menu');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (5,'language','Ideal position for module Language');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (6,'search','Ideal position for module Search');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (7,'category','Internal position in content category pages');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (8,'top','Content top');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (9,'bottom','Content bottom');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (10,'user1','Custom position 1');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (11,'user2','Custom position 2');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (12,'user3','Custom position 3');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (13,'user4','Custom position 4');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (14,'frontpage1','Modules shown on frontpage cell 1');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (15,'frontpage2','Modules shown on frontpage cell 2');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (16,'frontpage3','Modules shown on frontpage cell 3');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (17,'frontpage4','Modules shown on frontpage cell 4');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (18,'frontpage5','Modules shown on frontpage cell 5');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (19,'frontpage10','Modules shown on frontpage cell 10');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (20,'hidden','A hidden position');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (21,'tools','Admin - Administration tools');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (22,'cpanel','Admin - Control panel dashboard right column');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (23,'cpanelbottom','Admin - Control panel dashboard bottom area');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (24,'admintop','Admin - Above component');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (25,'adminbottom','Admin - Bellow component');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (26,'mobilefront','Modules shown on mobile-friendly frontpage');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (27,'mobiletop','Modules shown above content in mobile version');
INSERT INTO "#__template_positions" ("id","position","description") VALUES (28,'mobilebottom','Modules shown below content in mobile version');

SELECT setval('"#__template_positions_id_seq"', max("id") ) FROM "#__template_positions"; 


DROP TABLE IF EXISTS "#__translations";
CREATE TABLE "#__translations" (
  "trid" BIGSERIAL NOT NULL ,
  "category" VARCHAR(60) NULL ,
  "element" VARCHAR(100) NULL ,
  "language" CHAR(2) NULL ,
  "elid" INTEGER NOT NULL DEFAULT 0,
  "translation" TEXT NULL ,
  PRIMARY KEY ("trid")
); 
CREATE INDEX "#__translations_idx_transelement" ON "#__translations" ("category","element","language","elid");

INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (1,'com_emenu','title','el',1,'Αρχική');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (2,'com_emenu','title','el',2,'Διάσημοι τόποι');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (3,'com_emenu','title','el',3,'Εικονοθήκη');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (4,'com_emenu','title','el',4,'Επικοινωνία');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (5,'com_emenu','title','el',7,'Διαχείριση');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (6,'com_emenu','title','el',8,'Αρχική');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (7,'com_emenu','title','el',9,'Διάσημοι τόποι');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (8,'com_emenu','title','el',10,'Εικονοθήκη');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (9,'com_emenu','title','el',11,'Επικοινωνία');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (10,'com_emenu','title','el',12,'Διαχείριση');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (11,'com_emenu','title','de',1,'Startseite');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (12,'com_emenu','title','de',2,'Berühmte Orte');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (13,'com_emenu','title','de',3,'Galerie');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (14,'com_emenu','title','de',4,'Kontakt');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (15,'com_emenu','title','de',7,'Administration');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (16,'com_emenu','title','de',8,'Startseite');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (17,'com_emenu','title','de',9,'Berühmte Orte');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (18,'com_emenu','title','de',10,'Galerie');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (19,'com_emenu','title','de',11,'Kontakt');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (20,'com_emenu','title','de',12,'Administration');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (21,'com_emenu','title','es',1,'Página principal');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (22,'com_emenu','title','es',2,'Famosos lugares');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (23,'com_emenu','title','es',3,'Galería');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (24,'com_emenu','title','es',4,'Contacto');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (25,'com_emenu','title','es',7,'Administracion');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (26,'com_emenu','title','es',8,'Página principal');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (27,'com_emenu','title','es',9,'Famosos lugares');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (28,'com_emenu','title','es',10,'Galería');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (29,'com_emenu','title','es',11,'Contacto');
INSERT INTO "#__translations" ("trid","category","element","language","elid","translation") VALUES (30,'com_emenu','title','es',12,'Administración');

SELECT setval('"#__translations_trid_seq"', max("trid") ) FROM "#__translations";


DROP TABLE IF EXISTS "#__users";
CREATE TABLE "#__users" (
  "uid" SERIAL NOT NULL ,
  "firstname" VARCHAR(150) NOT NULL ,
  "lastname" VARCHAR(150) NOT NULL ,
  "uname" VARCHAR(80) NULL ,
  "pword" VARCHAR(64) NULL ,
  "block" SMALLINT NOT NULL DEFAULT 0,
  "activation" VARCHAR(100) NULL ,
  "gid" SMALLINT NOT NULL DEFAULT 7,
  "groupname" VARCHAR(120) NULL ,
  "avatar" VARCHAR(250) NULL ,
  "preflang" VARCHAR(10) NULL ,
  "timezone" VARCHAR(60) NULL ,
  "country" VARCHAR(120) NULL ,
  "city" VARCHAR(120) NULL ,
  "address" VARCHAR(250) NULL ,
  "postalcode" VARCHAR(20) NULL ,
  "website" VARCHAR(120) NULL ,
  "email" VARCHAR(120) NULL ,
  "phone" VARCHAR(40) NULL ,
  "mobile" VARCHAR(40) NULL ,
  "gender" VARCHAR(10) NULL ,
  "birthdate" VARCHAR(20) NULL ,
  "occupation" VARCHAR(120) NULL ,
  "registerdate" TIMESTAMP NOT NULL DEFAULT '1970-01-01 00:00:00',
  "lastvisitdate" TIMESTAMP NOT NULL DEFAULT '1970-01-01 00:00:00',
  "expiredate" TIMESTAMP NOT NULL DEFAULT '2060-01-01 00:00:00',
  "profile_views" INTEGER NOT NULL DEFAULT 0,
  "times_online" INTEGER NOT NULL DEFAULT 0,
  "params" TEXT NULL ,
  PRIMARY KEY ("uid")
); 

INSERT INTO "#__users" ("uid","firstname","lastname","uname","pword","block","activation","gid","groupname","avatar","preflang","timezone","country","city","address","postalcode","website","email","phone","mobile","gender","birthdate","occupation","registerdate","lastvisitdate","expiredate","profile_views","times_online","params") VALUES (1,'ΞΒΞ’ΒΞΒ²Ξ²β‚¬ΒΞβ€ ΞΒΞ’ΒΞΒ²Ξ²β€Β¬Ξ’Β°ΞΒΞ’ΒΞβ€™Ξ’Β¬ΞΒΞ’ΒΞβ€™Ξ’Β½ΞΒΞ’ΒΞβ€™Ξ’Β½ΞΒΞ’ΒΞβ€™Ξ’Β·ΞΒΞ’ΒΞΒ²Ξ²β€Β¬Ξ’Β','ΞΒΞ’ΒΞβ€™Ξ’Β£ΞΒΞ’ΒΞβ€™Ξ’Β¬ΞΒΞ’ΒΞβ€™Ξ’Β½ΞΒΞ’ΒΞβ€™Ξ’Β½ΞΒΞ’ΒΞΒΞ’ΒΞΒΞ’ΒΞΒ²Ξ²β€Β¬Ξ’Β','uranus','8fa05d434329919aa97810409e479f5390827a1b','0',NULL,1,'Administrator',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://elxis4.loc','datahell@elxis.org',NULL,NULL,'male',NULL,NULL,'2012-09-09 14:53:06','2012-09-09 14:53:06','2060-01-01 00:00:00',0,0,'twitter=elxiscms');

SELECT setval('"#__users_uid_seq"', max("uid") ) FROM "#__users";