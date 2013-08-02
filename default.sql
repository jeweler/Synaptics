CREATE TABLE IF NOT EXISTS `configs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `param` text NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;
INSERT INTO `configs` (`id`, `param`, `value`) VALUES
(1, 'deflogin', 'root'),
(2, 'defpass', 't08688121');

CREATE TABLE IF NOT EXISTS `tries` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) NOT NULL,
  `time` bigint(20) NOT NULL,
  `try` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;