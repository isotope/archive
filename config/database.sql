-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

-- 
-- Table `tl_iso_config`
-- 

CREATE TABLE `tl_iso_config` (
  `createMember` varchar(8) NOT NULL default '',
  `createMember_groups` blob NULL,
  `createMember_newsletters` blob NULL,
  `createMember_mail` int(10) unsigned NOT NULL default '0',
  `createMember_adminMail` int(10) unsigned NOT NULL default '0',
  `createMember_assignDir` char(1) NOT NULL default '',
  `createMember_homeDir` varchar(255) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table `tl_iso_products`
-- 

CREATE TABLE `tl_iso_products` (
  `createMember` char(1) NOT NULL default '',
  `assignMember_groups` blob NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table `tl_page`
-- 

CREATE TABLE `tl_page` (
  `iso_activateAccount` int(10) unsigned NOT NULL default '0',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

