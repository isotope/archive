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
-- Table `tl_iso_products`
--

CREATE TABLE `tl_iso_products` (
  `pMin` decimal(12,2) NOT NULL default '0.00',
  `pMax` decimal(12,2) NOT NULL default '0.00',
  `message` text NULL,
  `shipto_address` text NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
