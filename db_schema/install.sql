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
 `birthdate` date,
 `active` int(1) DEFAULT '1',
 `created` datetime DEFAULT NULL,
 `modified` datetime DEFAULT NULL,
 `photo_id` int(11),
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
 `walk_distance` int(2) not null,
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