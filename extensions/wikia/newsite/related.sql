CREATE TABLE `related` (
  `name1` varchar(128) NOT NULL default '',
  `name2` varchar(128) NOT NULL default '',
  KEY `name1` (`name1`),
  KEY `name2` (`name2`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
