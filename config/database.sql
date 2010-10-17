-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

-- 
-- Table `tl_module`
-- 

CREATE TABLE `tl_module` (
  `iso_fundraiser_layout` varchar(64) NOT NULL default '',
  `iso_fundraiser_results` varchar(64) NOT NULL default '',
  `iso_fundraiser_reader` varchar(64) NOT NULL default '',
  `iso_fundraiser_jumpTo` int(10) unsigned NOT NULL default '0',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table `tl_iso_fundraiser`
--

CREATE TABLE `tl_iso_fundraiser` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `session` varchar(64) NOT NULL default '',
  `store_id` int(2) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` text NULL,
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table `tl_iso_fundraiser_items`
--

CREATE TABLE `tl_iso_fundraiser_items` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  `product_sku` varchar(128) NOT NULL default '',
  `product_name` varchar(255) NOT NULL default '',
  `product_options` blob NULL,
  `product_quantity` int(10) unsigned NOT NULL default '0',
  `price` decimal(12,2) NOT NULL default '0.00',
  `href_reader` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table `tl_iso_cart_items`
--

CREATE TABLE `tl_iso_cart_items` (
  `fundraiser_id` int(10) unsigned NOT NULL default '0',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table `tl_iso_order_items`
--

CREATE TABLE `tl_iso_order_items` (
  `fundraiser_id` int(10) unsigned NOT NULL default '0',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;