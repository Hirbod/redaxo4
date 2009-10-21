CREATE TABLE `rex_em_field` (
  `id` int(11) NOT NULL auto_increment,
  `table_id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `type_id` varchar(255) NOT NULL,
  `prio` varchar(255) NOT NULL,
  `f1` text NOT NULL,
  `f2` text NOT NULL,
  `f3` text NOT NULL,
  `f4` text NOT NULL,
  `f5` text NOT NULL,
  `f6` text NOT NULL,
  `f7` text NOT NULL,
  `f8` text NOT NULL,
  `f9` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


CREATE TABLE `rex_em_table` (
  `id` int(11) NOT NULL auto_increment,
  `label` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;