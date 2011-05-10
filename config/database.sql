-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


-- --------------------------------------------------------

-- 
-- Table `tl_module`
-- 

CREATE TABLE `tl_module` (
  `iso_bestseller_mode` varchar(64) NOT NULL default '',
  `iso_bestseller_productTypes` blob NULL,
  `iso_bestseller_products` blob NULL,
  `iso_bestseller_amt` int(10) unsigned NOT NULL default '0',
  `iso_bestseller_qty` int(10) unsigned NOT NULL default '0',
  `iso_bestseller_limitByType` char(1) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;