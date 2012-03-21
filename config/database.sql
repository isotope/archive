-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

-- 
-- Table `tl_iso_postal`
-- 

CREATE TABLE `tl_iso_postal` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `postal_from` varchar(8) NOT NULL default '',
  `postal_to` varchar(8) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `country` varchar(2) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `postal` (`postal_from`, `country`, `postal_to`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

