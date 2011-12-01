-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

-- 
-- Table `tl_iso_attributes`
-- 

CREATE TABLE `tl_iso_attributes` (
  `productoptions_includeBlankOption` char(1) NOT NULL default '',
  `productoptions_blankOptionLabel` varchar(255) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

