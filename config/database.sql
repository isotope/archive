-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


--
-- Table `tl_iso_payment_modules`
--

CREATE TABLE `tl_iso_payment_modules` (
  `epay_merchantnumber` varchar(7) NOT NULL default '',
  `epay_secretkey` varchar(255) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table `tl_page`
--

CREATE TABLE `tl_page` (
  `epay_relay` char(1) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

