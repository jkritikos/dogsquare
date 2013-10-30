#Users table
CREATE TABLE `users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
 `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
 `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
 `active` int(1) DEFAULT '1',
 `gender` int(1),
 `age` int(4),
 `facebook_id` varchar(256),
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 `photo_id` int(11),
 `birth_date` datetime DEFAULT NULL,
 `address` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
 `country_id` int(3) DEFAULT NULL,
 `newsletter` int(1) DEFAULT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#insert root user
insert into users (name,email,created,modified,password,active) values ('Jason Kritikos', 'djason@djason.com',now(),now(),'81dc9bdb52d04dc20036dbd8313ed055',1);

#User roles table
CREATE TABLE `roles` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
 primary key(`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#User roles data
insert into roles (id,name) values (1,'Admin');
insert into roles (id,name) values (2,'Dog Owner');

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#assign roles to root user
insert into user_roles(user_id, role_id) values (1,1);

#User inbox table 
CREATE TABLE `user_inbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_from` int(11) NOT NULL,
  `user_to` int(11) NOT NULL,
  `read` int(1) default 0,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Dog breed table
CREATE TABLE `dog_breeds` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
 `origin` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
 `weight_from` int (2),
 `weight_to` int (2),
 `kennel_club` varchar(256) COLLATE utf8_unicode_ci,
 `active` int(1) DEFAULT '1',
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Dogs table
CREATE TABLE `dogs` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `breed_id` int(11) not null,
 `owner_id` int(11) not null,
 `name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
 `gender` int(1),
 `mating` int(1),
 `weight` int(4),
 `age` int(2),
 `birthdate` date,
 `active` int(1) DEFAULT '1',
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 `photo_id` int(11),
 `size` int(1),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Dog likes table
CREATE TABLE `dog_likes` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `dog_id` int(11) not null,
 `user_id` int(11) not null,
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Place categories table
CREATE TABLE `place_categories` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
 `active` int(1) DEFAULT '1',
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 `visible` int(1) DEFAULT '1',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Place comments table
CREATE TABLE `place_comments` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `comment` text COLLATE utf8_unicode_ci NOT NULL,
 `user_id` int(11) not null,
 `place_id` int(1) not null,
 `active` int(1) DEFAULT '1',
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Activity comments table
CREATE TABLE `activity_comments` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `comment` text COLLATE utf8_unicode_ci NOT NULL,
 `user_id` int(11) not null,
 `activity_id` int(1) not null,
 `active` int(1) DEFAULT '1',
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Places
CREATE TABLE `places` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `category_id` int(11) not null,
 `name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
 `user_id` int(11) not null,
 `lon` varchar(16) not null,
 `lat` varchar(16) not null,
 `address` varchar(256),
 `active` int(1) DEFAULT '1',
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 `photo_id` int(11),
 `dog_id` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Place likes table
CREATE TABLE `place_likes` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `place_id` int(11) not null,
 `user_id` int(11) not null,
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `place_checkins` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `place_id` int(11) not null,
 `user_id` int(11) not null,
 `comment` text COLLATE utf8_unicode_ci NOT NULL,
 `active` int(1) DEFAULT '1',
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Dogfuel algorithm table
CREATE TABLE `dogfuel_rules` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `breed_id` int(11) not null,
 `user_id` int(11) not null,
 `walk_distance` float not null,
 `playtime` int(11) not null,
 `active` int(1) DEFAULT '1',
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `photos` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) not null,
 `title` varchar(256) COLLATE utf8_unicode_ci,
 `path` varchar(512) not null,
 `thumb` varchar(512) not null,
 `active` int(1) DEFAULT '1',
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 `type_id` int(1) DEFAULT NULL,
 `place_id` int(11) not null,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_follows` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) not null,
 `follows_user` int(11) not null,
 `active` int(1) DEFAULT '1',
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Notifications table
CREATE TABLE `user_notifications` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) not null,
 `user_from` int(11) not null,
 `read` int(1) not null,
 `type_id` int(4) not null,
 `activity_id` int(11),
 `badge_id` int(11),
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Notification types table
CREATE TABLE `notification_types` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(128) not null,
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Activity likes table
CREATE TABLE `activity_likes` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `activity_id` int(11) not null,
 `user_id` int(11) not null,
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Activities table
CREATE TABLE `activities` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) not null,
 `start_date` timestamp,
 `start_time` timestamp,
 `end_time` timestamp,
 `type_id` int(2),
 `temperature` float,
 `pace` float,
 `distance` float,
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 `start_lat` varchar(128),
 `start_lon` varchar(128),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Activity dogs table
CREATE TABLE `activity_dogs` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `activity_id` int(11) not null,
 `dog_id` int(11) not null,
 `walk_distance` float,
 `playtime` int(2),
 `dogfuel` int(4),
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Activity coordinates table
CREATE TABLE `activity_coordinates` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `activity_id` int(11) not null,
 `lat` varchar(128),
 `lon` varchar(128),
 `logtime` timestamp,
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Feed table
CREATE TABLE `feeds` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_from` int(11) not null,
 `user_from_name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
 `target_user_id` int(11),
 `target_user_name` varchar(256) COLLATE utf8_unicode_ci,
 `target_dog_id` int(11),
 `target_dog_name` varchar(256) COLLATE utf8_unicode_ci,
 `target_place_id` int(11),
 `target_place_name` varchar(256) COLLATE utf8_unicode_ci,
 `type_id` int(4) not null,
 `activity_id` int(11),
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#badges table
CREATE TABLE `badges` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `title` varchar(128) COLLATE utf8_unicode_ci,
 `description` varchar(1024) COLLATE utf8_unicode_ci,
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#user_badges table
CREATE TABLE `user_badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#user_passports table
CREATE TABLE `user_passports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci,
  `description` varchar(1024) COLLATE utf8_unicode_ci,
  `due_date` datetime,
  `completed` int(1),
  `active` int(1),
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `remind` int(1),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#countries table
CREATE TABLE `countries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  `active` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `countries` (`id`, `name`) VALUES (1, 'Afghanistan');
INSERT INTO `countries` (`id`, `name`) VALUES (2, 'Albania');
INSERT INTO `countries` (`id`, `name`) VALUES (3, 'Algeria');
INSERT INTO `countries` (`id`, `name`) VALUES (4, 'American Samoa');
INSERT INTO `countries` (`id`, `name`) VALUES (5, 'Andorra');
INSERT INTO `countries` (`id`, `name`) VALUES (6, 'Angola');
INSERT INTO `countries` (`id`, `name`) VALUES (7, 'Anguilla');
INSERT INTO `countries` (`id`, `name`) VALUES (8, 'Antarctica');
INSERT INTO `countries` (`id`, `name`) VALUES (9, 'Antigua and Barbuda');
INSERT INTO `countries` (`id`, `name`) VALUES (10, 'Argentina');
INSERT INTO `countries` (`id`, `name`) VALUES (12, 'Armenia');
INSERT INTO `countries` (`id`, `name`) VALUES (13, 'Aruba');
INSERT INTO `countries` (`id`, `name`) VALUES (14, 'Australia');
INSERT INTO `countries` (`id`, `name`) VALUES (15, 'Austria');
INSERT INTO `countries` (`id`, `name`) VALUES (17, 'Azerbaijan');
INSERT INTO `countries` (`id`, `name`) VALUES (18, 'Bahamas');
INSERT INTO `countries` (`id`, `name`) VALUES (19, 'Bahrain');
INSERT INTO `countries` (`id`, `name`) VALUES (20, 'Bangladesh');
INSERT INTO `countries` (`id`, `name`) VALUES (21, 'Barbados');
INSERT INTO `countries` (`id`, `name`) VALUES (22, 'Belarus');
INSERT INTO `countries` (`id`, `name`) VALUES (23, 'Belgium');
INSERT INTO `countries` (`id`, `name`) VALUES (24, 'Belize');
INSERT INTO `countries` (`id`, `name`) VALUES (25, 'Benin');
INSERT INTO `countries` (`id`, `name`) VALUES (26, 'Bermuda');
INSERT INTO `countries` (`id`, `name`) VALUES (27, 'Bhutan');
INSERT INTO `countries` (`id`, `name`) VALUES (28, 'Bolivia');
INSERT INTO `countries` (`id`, `name`) VALUES (29, 'Bosnia and Herzegovina');
INSERT INTO `countries` (`id`, `name`) VALUES (30, 'Botswana');
INSERT INTO `countries` (`id`, `name`) VALUES (31, 'Bouvet Island');
INSERT INTO `countries` (`id`, `name`) VALUES (32, 'Brazil');
INSERT INTO `countries` (`id`, `name`) VALUES (33, 'British Indian Ocean Territory');
INSERT INTO `countries` (`id`, `name`) VALUES (34, 'Brunei Darussalam');
INSERT INTO `countries` (`id`, `name`) VALUES (35, 'Bulgaria');
INSERT INTO `countries` (`id`, `name`) VALUES (36, 'Burkina Faso');
INSERT INTO `countries` (`id`, `name`) VALUES (37, 'Burundi');
INSERT INTO `countries` (`id`, `name`) VALUES (38, 'Cambodia');
INSERT INTO `countries` (`id`, `name`) VALUES (39, 'Cameroon');
INSERT INTO `countries` (`id`, `name`) VALUES (40, 'Canada');
INSERT INTO `countries` (`id`, `name`) VALUES (41, 'Cape Verde');
INSERT INTO `countries` (`id`, `name`) VALUES (42, 'Cayman Islands');
INSERT INTO `countries` (`id`, `name`) VALUES (43, 'Central African Republic');
INSERT INTO `countries` (`id`, `name`) VALUES (44, 'Chad');
INSERT INTO `countries` (`id`, `name`) VALUES (45, 'Chile');
INSERT INTO `countries` (`id`, `name`) VALUES (46, 'China');
INSERT INTO `countries` (`id`, `name`) VALUES (47, 'Christmas Island');
INSERT INTO `countries` (`id`, `name`) VALUES (48, 'Cocos (Keeling) Islands');
INSERT INTO `countries` (`id`, `name`) VALUES (49, 'Colombia');
INSERT INTO `countries` (`id`, `name`) VALUES (50, 'Comoros');
INSERT INTO `countries` (`id`, `name`) VALUES (51, 'Congo');
INSERT INTO `countries` (`id`, `name`) VALUES (52, 'Congo, The Democratic Republic of The');
INSERT INTO `countries` (`id`, `name`) VALUES (53, 'Cook Islands');
INSERT INTO `countries` (`id`, `name`) VALUES (54, 'Costa Rica');
INSERT INTO `countries` (`id`, `name`) VALUES (55, 'Cote D\'ivoire');
INSERT INTO `countries` (`id`, `name`) VALUES (56, 'Croatia');
INSERT INTO `countries` (`id`, `name`) VALUES (57, 'Cuba');
INSERT INTO `countries` (`id`, `name`) VALUES (58, 'Cyprus');
INSERT INTO `countries` (`id`, `name`) VALUES (60, 'Czech Republic');
INSERT INTO `countries` (`id`, `name`) VALUES (61, 'Denmark');
INSERT INTO `countries` (`id`, `name`) VALUES (62, 'Djibouti');
INSERT INTO `countries` (`id`, `name`) VALUES (63, 'Dominica');
INSERT INTO `countries` (`id`, `name`) VALUES (64, 'Dominican Republic');
INSERT INTO `countries` (`id`, `name`) VALUES (65, 'Easter Island');
INSERT INTO `countries` (`id`, `name`) VALUES (66, 'Ecuador');
INSERT INTO `countries` (`id`, `name`) VALUES (67, 'Egypt');
INSERT INTO `countries` (`id`, `name`) VALUES (68, 'El Salvador');
INSERT INTO `countries` (`id`, `name`) VALUES (69, 'Equatorial Guinea');
INSERT INTO `countries` (`id`, `name`) VALUES (70, 'Eritrea');
INSERT INTO `countries` (`id`, `name`) VALUES (71, 'Estonia');
INSERT INTO `countries` (`id`, `name`) VALUES (72, 'Ethiopia');
INSERT INTO `countries` (`id`, `name`) VALUES (73, 'Falkland Islands (Malvinas)');
INSERT INTO `countries` (`id`, `name`) VALUES (74, 'Faroe Islands');
INSERT INTO `countries` (`id`, `name`) VALUES (75, 'Fiji');
INSERT INTO `countries` (`id`, `name`) VALUES (76, 'Finland');
INSERT INTO `countries` (`id`, `name`) VALUES (77, 'France');
INSERT INTO `countries` (`id`, `name`) VALUES (78, 'French Guiana');
INSERT INTO `countries` (`id`, `name`) VALUES (79, 'French Polynesia');
INSERT INTO `countries` (`id`, `name`) VALUES (80, 'French Southern Territories');
INSERT INTO `countries` (`id`, `name`) VALUES (81, 'Gabon');
INSERT INTO `countries` (`id`, `name`) VALUES (82, 'Gambia');
INSERT INTO `countries` (`id`, `name`) VALUES (83, 'Georgia');
INSERT INTO `countries` (`id`, `name`) VALUES (85, 'Germany');
INSERT INTO `countries` (`id`, `name`) VALUES (86, 'Ghana');
INSERT INTO `countries` (`id`, `name`) VALUES (87, 'Gibraltar');
INSERT INTO `countries` (`id`, `name`) VALUES (88, 'Greece');
INSERT INTO `countries` (`id`, `name`) VALUES (89, 'Greenland');
INSERT INTO `countries` (`id`, `name`) VALUES (91, 'Grenada');
INSERT INTO `countries` (`id`, `name`) VALUES (92, 'Guadeloupe');
INSERT INTO `countries` (`id`, `name`) VALUES (93, 'Guam');
INSERT INTO `countries` (`id`, `name`) VALUES (94, 'Guatemala');
INSERT INTO `countries` (`id`, `name`) VALUES (95, 'Guinea');
INSERT INTO `countries` (`id`, `name`) VALUES (96, 'Guinea-bissau');
INSERT INTO `countries` (`id`, `name`) VALUES (97, 'Guyana');
INSERT INTO `countries` (`id`, `name`) VALUES (98, 'Haiti');
INSERT INTO `countries` (`id`, `name`) VALUES (99, 'Heard Island and Mcdonald Islands');
INSERT INTO `countries` (`id`, `name`) VALUES (100, 'Honduras');
INSERT INTO `countries` (`id`, `name`) VALUES (101, 'Hong Kong');
INSERT INTO `countries` (`id`, `name`) VALUES (102, 'Hungary');
INSERT INTO `countries` (`id`, `name`) VALUES (103, 'Iceland');
INSERT INTO `countries` (`id`, `name`) VALUES (104, 'India');
INSERT INTO `countries` (`id`, `name`) VALUES (106, 'Indonesia');
INSERT INTO `countries` (`id`, `name`) VALUES (107, 'Iran');
INSERT INTO `countries` (`id`, `name`) VALUES (108, 'Iraq');
INSERT INTO `countries` (`id`, `name`) VALUES (109, 'Ireland');
INSERT INTO `countries` (`id`, `name`) VALUES (110, 'Israel');
INSERT INTO `countries` (`id`, `name`) VALUES (111, 'Italy');
INSERT INTO `countries` (`id`, `name`) VALUES (112, 'Jamaica');
INSERT INTO `countries` (`id`, `name`) VALUES (113, 'Japan');
INSERT INTO `countries` (`id`, `name`) VALUES (114, 'Jordan');
INSERT INTO `countries` (`id`, `name`) VALUES (116, 'Kazakhstan');
INSERT INTO `countries` (`id`, `name`) VALUES (117, 'Kenya');
INSERT INTO `countries` (`id`, `name`) VALUES (118, 'Kiribati');
INSERT INTO `countries` (`id`, `name`) VALUES (119, 'Korea, North');
INSERT INTO `countries` (`id`, `name`) VALUES (120, 'Korea, South');
INSERT INTO `countries` (`id`, `name`) VALUES (121, 'Kosovo');
INSERT INTO `countries` (`id`, `name`) VALUES (122, 'Kuwait');
INSERT INTO `countries` (`id`, `name`) VALUES (123, 'Kyrgyzstan');
INSERT INTO `countries` (`id`, `name`) VALUES (124, 'Laos');
INSERT INTO `countries` (`id`, `name`) VALUES (125, 'Latvia');
INSERT INTO `countries` (`id`, `name`) VALUES (126, 'Lebanon');
INSERT INTO `countries` (`id`, `name`) VALUES (127, 'Lesotho');
INSERT INTO `countries` (`id`, `name`) VALUES (128, 'Liberia');
INSERT INTO `countries` (`id`, `name`) VALUES (129, 'Libyan Arab Jamahiriya');
INSERT INTO `countries` (`id`, `name`) VALUES (130, 'Liechtenstein');
INSERT INTO `countries` (`id`, `name`) VALUES (131, 'Lithuania');
INSERT INTO `countries` (`id`, `name`) VALUES (132, 'Luxembourg');
INSERT INTO `countries` (`id`, `name`) VALUES (133, 'Macau');
INSERT INTO `countries` (`id`, `name`) VALUES (134, 'Macedonia');
INSERT INTO `countries` (`id`, `name`) VALUES (135, 'Madagascar');
INSERT INTO `countries` (`id`, `name`) VALUES (136, 'Malawi');
INSERT INTO `countries` (`id`, `name`) VALUES (137, 'Malaysia');
INSERT INTO `countries` (`id`, `name`) VALUES (138, 'Maldives');
INSERT INTO `countries` (`id`, `name`) VALUES (139, 'Mali');
INSERT INTO `countries` (`id`, `name`) VALUES (140, 'Malta');
INSERT INTO `countries` (`id`, `name`) VALUES (141, 'Marshall Islands');
INSERT INTO `countries` (`id`, `name`) VALUES (142, 'Martinique');
INSERT INTO `countries` (`id`, `name`) VALUES (143, 'Mauritania');
INSERT INTO `countries` (`id`, `name`) VALUES (144, 'Mauritius');
INSERT INTO `countries` (`id`, `name`) VALUES (145, 'Mayotte');
INSERT INTO `countries` (`id`, `name`) VALUES (146, 'Mexico');
INSERT INTO `countries` (`id`, `name`) VALUES (147, 'Micronesia, Federated States of');
INSERT INTO `countries` (`id`, `name`) VALUES (148, 'Moldova, Republic of');
INSERT INTO `countries` (`id`, `name`) VALUES (149, 'Monaco');
INSERT INTO `countries` (`id`, `name`) VALUES (150, 'Mongolia');
INSERT INTO `countries` (`id`, `name`) VALUES (151, 'Montenegro');
INSERT INTO `countries` (`id`, `name`) VALUES (152, 'Montserrat');
INSERT INTO `countries` (`id`, `name`) VALUES (153, 'Morocco');
INSERT INTO `countries` (`id`, `name`) VALUES (154, 'Mozambique');
INSERT INTO `countries` (`id`, `name`) VALUES (155, 'Myanmar');
INSERT INTO `countries` (`id`, `name`) VALUES (156, 'Namibia');
INSERT INTO `countries` (`id`, `name`) VALUES (157, 'Nauru');
INSERT INTO `countries` (`id`, `name`) VALUES (158, 'Nepal');
INSERT INTO `countries` (`id`, `name`) VALUES (159, 'Netherlands');
INSERT INTO `countries` (`id`, `name`) VALUES (160, 'Netherlands Antilles');
INSERT INTO `countries` (`id`, `name`) VALUES (161, 'New Caledonia');
INSERT INTO `countries` (`id`, `name`) VALUES (162, 'New Zealand');
INSERT INTO `countries` (`id`, `name`) VALUES (163, 'Nicaragua');
INSERT INTO `countries` (`id`, `name`) VALUES (164, 'Niger');
INSERT INTO `countries` (`id`, `name`) VALUES (165, 'Nigeria');
INSERT INTO `countries` (`id`, `name`) VALUES (166, 'Niue');
INSERT INTO `countries` (`id`, `name`) VALUES (167, 'Norfolk Island');
INSERT INTO `countries` (`id`, `name`) VALUES (168, 'Northern Mariana Islands');
INSERT INTO `countries` (`id`, `name`) VALUES (169, 'Norway');
INSERT INTO `countries` (`id`, `name`) VALUES (170, 'Oman');
INSERT INTO `countries` (`id`, `name`) VALUES (171, 'Pakistan');
INSERT INTO `countries` (`id`, `name`) VALUES (172, 'Palau');
INSERT INTO `countries` (`id`, `name`) VALUES (173, 'Palestinian Territory');
INSERT INTO `countries` (`id`, `name`) VALUES (174, 'Panama');
INSERT INTO `countries` (`id`, `name`) VALUES (175, 'Papua New Guinea');
INSERT INTO `countries` (`id`, `name`) VALUES (176, 'Paraguay');
INSERT INTO `countries` (`id`, `name`) VALUES (177, 'Peru');
INSERT INTO `countries` (`id`, `name`) VALUES (178, 'Philippines');
INSERT INTO `countries` (`id`, `name`) VALUES (179, 'Pitcairn');
INSERT INTO `countries` (`id`, `name`) VALUES (180, 'Poland');
INSERT INTO `countries` (`id`, `name`) VALUES (181, 'Portugal');
INSERT INTO `countries` (`id`, `name`) VALUES (182, 'Puerto Rico');
INSERT INTO `countries` (`id`, `name`) VALUES (183, 'Qatar');
INSERT INTO `countries` (`id`, `name`) VALUES (184, 'Reunion');
INSERT INTO `countries` (`id`, `name`) VALUES (185, 'Romania');
INSERT INTO `countries` (`id`, `name`) VALUES (187, 'Russia');
INSERT INTO `countries` (`id`, `name`) VALUES (188, 'Rwanda');
INSERT INTO `countries` (`id`, `name`) VALUES (189, 'Saint Helena');
INSERT INTO `countries` (`id`, `name`) VALUES (190, 'Saint Kitts and Nevis');
INSERT INTO `countries` (`id`, `name`) VALUES (191, 'Saint Lucia');
INSERT INTO `countries` (`id`, `name`) VALUES (192, 'Saint Pierre and Miquelon');
INSERT INTO `countries` (`id`, `name`) VALUES (193, 'Saint Vincent and The Grenadines');
INSERT INTO `countries` (`id`, `name`) VALUES (194, 'Samoa');
INSERT INTO `countries` (`id`, `name`) VALUES (195, 'San Marino');
INSERT INTO `countries` (`id`, `name`) VALUES (196, 'Sao Tome and Principe');
INSERT INTO `countries` (`id`, `name`) VALUES (197, 'Saudi Arabia');
INSERT INTO `countries` (`id`, `name`) VALUES (198, 'Senegal');
INSERT INTO `countries` (`id`, `name`) VALUES (199, 'Serbia and Montenegro');
INSERT INTO `countries` (`id`, `name`) VALUES (200, 'Seychelles');
INSERT INTO `countries` (`id`, `name`) VALUES (201, 'Sierra Leone');
INSERT INTO `countries` (`id`, `name`) VALUES (202, 'Singapore');
INSERT INTO `countries` (`id`, `name`) VALUES (203, 'Slovakia');
INSERT INTO `countries` (`id`, `name`) VALUES (204, 'Slovenia');
INSERT INTO `countries` (`id`, `name`) VALUES (205, 'Solomon Islands');
INSERT INTO `countries` (`id`, `name`) VALUES (206, 'Somalia');
INSERT INTO `countries` (`id`, `name`) VALUES (207, 'South Africa');
INSERT INTO `countries` (`id`, `name`) VALUES (208, 'South Georgia and The South Sandwich Islands');
INSERT INTO `countries` (`id`, `name`) VALUES (209, 'Spain');
INSERT INTO `countries` (`id`, `name`) VALUES (210, 'Sri Lanka');
INSERT INTO `countries` (`id`, `name`) VALUES (211, 'Sudan');
INSERT INTO `countries` (`id`, `name`) VALUES (212, 'Suriname');
INSERT INTO `countries` (`id`, `name`) VALUES (213, 'Svalbard and Jan Mayen');
INSERT INTO `countries` (`id`, `name`) VALUES (214, 'Swaziland');
INSERT INTO `countries` (`id`, `name`) VALUES (215, 'Sweden');
INSERT INTO `countries` (`id`, `name`) VALUES (216, 'Switzerland');
INSERT INTO `countries` (`id`, `name`) VALUES (217, 'Syria');
INSERT INTO `countries` (`id`, `name`) VALUES (218, 'Taiwan');
INSERT INTO `countries` (`id`, `name`) VALUES (219, 'Tajikistan');
INSERT INTO `countries` (`id`, `name`) VALUES (220, 'Tanzania, United Republic of');
INSERT INTO `countries` (`id`, `name`) VALUES (221, 'Thailand');
INSERT INTO `countries` (`id`, `name`) VALUES (222, 'Timor-leste');
INSERT INTO `countries` (`id`, `name`) VALUES (223, 'Togo');
INSERT INTO `countries` (`id`, `name`) VALUES (224, 'Tokelau');
INSERT INTO `countries` (`id`, `name`) VALUES (225, 'Tonga');
INSERT INTO `countries` (`id`, `name`) VALUES (226, 'Trinidad and Tobago');
INSERT INTO `countries` (`id`, `name`) VALUES (227, 'Tunisia');
INSERT INTO `countries` (`id`, `name`) VALUES (229, 'Turkey');
INSERT INTO `countries` (`id`, `name`) VALUES (230, 'Turkmenistan');
INSERT INTO `countries` (`id`, `name`) VALUES (231, 'Turks and Caicos Islands');
INSERT INTO `countries` (`id`, `name`) VALUES (232, 'Tuvalu');
INSERT INTO `countries` (`id`, `name`) VALUES (233, 'Uganda');
INSERT INTO `countries` (`id`, `name`) VALUES (234, 'Ukraine');
INSERT INTO `countries` (`id`, `name`) VALUES (235, 'United Arab Emirates');
INSERT INTO `countries` (`id`, `name`) VALUES (236, 'United Kingdom');
INSERT INTO `countries` (`id`, `name`) VALUES (237, 'United States');
INSERT INTO `countries` (`id`, `name`) VALUES (238, 'United States Minor Outlying Islands');
INSERT INTO `countries` (`id`, `name`) VALUES (239, 'Uruguay');
INSERT INTO `countries` (`id`, `name`) VALUES (240, 'Uzbekistan');
INSERT INTO `countries` (`id`, `name`) VALUES (241, 'Vanuatu');
INSERT INTO `countries` (`id`, `name`) VALUES (242, 'Vatican City');
INSERT INTO `countries` (`id`, `name`) VALUES (243, 'Venezuela');
INSERT INTO `countries` (`id`, `name`) VALUES (244, 'Vietnam');
INSERT INTO `countries` (`id`, `name`) VALUES (245, 'Virgin Islands, British');
INSERT INTO `countries` (`id`, `name`) VALUES (246, 'Virgin Islands, U.S.');
INSERT INTO `countries` (`id`, `name`) VALUES (247, 'Wallis and Futuna');
INSERT INTO `countries` (`id`, `name`) VALUES (248, 'Western Sahara');
INSERT INTO `countries` (`id`, `name`) VALUES (250, 'Yemen');
INSERT INTO `countries` (`id`, `name`) VALUES (251, 'Zambia');
INSERT INTO `countries` (`id`, `name`) VALUES (252, 'Zimbabwe');