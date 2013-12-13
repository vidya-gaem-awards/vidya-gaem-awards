SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Table structure for table `2010_releases`
--

CREATE TABLE IF NOT EXISTS `2010_releases` (
 `Game` varchar(60) NOT NULL,
 `Notable` tinyint(1) NOT NULL DEFAULT '0',
 `PC` tinyint(1) NOT NULL,
 `PS3` tinyint(1) NOT NULL,
 `PS4` tinyint(1) NOT NULL,
 `PSV` tinyint(1) NOT NULL,
 `PSN` tinyint(1) NOT NULL,
 `360` tinyint(1) NOT NULL,
 `XB1` tinyint(1) NOT NULL,
 `XBLA` tinyint(1) NOT NULL,
 `Wii` tinyint(1) NOT NULL,
 `WiiU` tinyint(1) NOT NULL,
 `WiiWare` tinyint(1) NOT NULL,
 `3DS` tinyint(1) NOT NULL,
 `Ouya` tinyint(1) NOT NULL,
 `Mobile` tinyint(1) NOT NULL,
 PRIMARY KEY (`Game`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

CREATE TABLE IF NOT EXISTS `access` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UniqueID` varchar(255) NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UserID` varchar(45) NOT NULL,
  `Page` varchar(30) NOT NULL,
  `RequestString` text NOT NULL,
  `RequestMethod` char(4) NOT NULL,
  `IP` varchar(15) NOT NULL,
  `UserAgent` text NOT NULL,
  `Filename` text NOT NULL,
  `Refer` text,
  PRIMARY KEY (`ID`),
  KEY `UniqueID` (`UniqueID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2174186 ;

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UserID` varchar(45) NOT NULL,
  `Timestamp` datetime NOT NULL,
  `Page` varchar(30) NOT NULL,
  `Action` varchar(40) NOT NULL,
  `SpecificID1` varchar(50) DEFAULT NULL,
  `SpecificID2` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=523632 ;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE IF NOT EXISTS `applications` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` varchar(45) NOT NULL,
  `Name` varchar(60) NOT NULL,
  `Email` varchar(60) NOT NULL,
  `Interest` varchar(255) NOT NULL,
  `Timestamp` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=128 ;

-- --------------------------------------------------------

--
-- Table structure for table `autocompleters`
--

CREATE TABLE IF NOT EXISTS `autocompleters` (
 `CategoryID` varchar(30) CHARACTER SET latin1 NOT NULL,
 `Values` text CHARACTER SET latin1 NOT NULL,
 PRIMARY KEY (`CategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `ID` varchar(30) NOT NULL,
  `Name` text NOT NULL,
  `Subtitle` text NOT NULL,
  `Order` smallint(5) NOT NULL,
  `Comments` text,
  `Enabled` tinyint(1) NOT NULL DEFAULT '1',
  `NominationsEnabled` tinyint(1) NOT NULL DEFAULT '1',
  `Secret` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Secret categories only show up during voting',
  `AutocompleteCategory` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `category_feedback`
--

CREATE TABLE IF NOT EXISTS `category_feedback` (
  `CategoryID` varchar(40) NOT NULL,
  `UserID` varchar(45) NOT NULL,
  `Opinion` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`CategoryID`,`UserID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE IF NOT EXISTS `feedback` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `UserID` varchar(45) DEFAULT NULL,
  `UniqueID` varchar(255) NOT NULL,
  `Timestamp` datetime NOT NULL,
  `GeneralRating` char(3) DEFAULT NULL,
  `CeremonyRating` char(3) DEFAULT NULL,
  `BestThing` text,
  `WorstThing` text,
  `OtherComments` text,
  `Questions` text,
  `Email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=478 ;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE IF NOT EXISTS `history` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` varchar(45) NOT NULL,
  `Table` varchar(30) NOT NULL,
  `EntryID` varchar(60) NOT NULL,
  `Values` text NOT NULL,
  `Timestamp` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=768 ;

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE IF NOT EXISTS `logins` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` varchar(40) NOT NULL,
  `Timestamp` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4233 ;

-- --------------------------------------------------------

--
-- Table structure for table `login_tokens`
--

CREATE TABLE IF NOT EXISTS `login_tokens` (
  `UserID` varchar(45) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Avatar` varchar(255) NOT NULL,
  `Token` varchar(255) NOT NULL,
  `Generated` datetime NOT NULL,
  `Expires` datetime NOT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Headline` text,
  `Text` text NOT NULL,
  `UserID` varchar(45) NOT NULL,
  `Timestamp` datetime NOT NULL,
  `Visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `nominees`
--

CREATE TABLE IF NOT EXISTS `nominees` (
  `CategoryID` varchar(30) NOT NULL,
  `NomineeID` varchar(45) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Subtitle` varchar(255) NOT NULL,
  `Image` text NOT NULL,
  PRIMARY KEY (`CategoryID`,`NomineeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `SteamID` varchar(45) NOT NULL,
  `Name` varchar(60) NOT NULL,
  `Special` tinyint(10) unsigned NOT NULL DEFAULT '0',
  `Level` smallint(6) NOT NULL DEFAULT '0',
  `FirstLogin` datetime DEFAULT NULL,
  `LastLogin` datetime DEFAULT NULL,
  `PrimaryRole` text,
  `Email` varchar(255) DEFAULT NULL,
  `Notes` text,
  `Website` varchar(40) DEFAULT NULL,
  `Avatar` blob,
  PRIMARY KEY (`SteamID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `UserID` varchar(45) NOT NULL,
  `GroupName` varchar(20) NOT NULL,
  PRIMARY KEY (`UserID`,`GroupName`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_nominations`
--

CREATE TABLE IF NOT EXISTS `user_nominations` (
  `CategoryID` varchar(30) NOT NULL,
  `OriginalCategory` varchar(30) DEFAULT NULL,
  `UserID` varchar(45) NOT NULL,
  `Nomination` text NOT NULL,
  `Timestamp` datetime NOT NULL,
  `AutoID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`AutoID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13501 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_rights`
--

CREATE TABLE IF NOT EXISTS `user_rights` (
  `GroupName` varchar(20) NOT NULL,
  `CanDo` varchar(20) NOT NULL,
  `Description` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`GroupName`,`CanDo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
  `UniqueID` varchar(255) NOT NULL,
  `CategoryID` varchar(45) NOT NULL,
  `Preferences` text NOT NULL,
  `Timestamp` datetime NOT NULL,
  `UserID` varchar(45) DEFAULT NULL,
  `IP` varchar(45) NOT NULL,
  `VotingCode` varchar(20) NOT NULL,
  `Number` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`UniqueID`,`CategoryID`) USING BTREE,
  KEY `UniqueID` (`UniqueID`),
  KEY `UniqueID_2` (`UniqueID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `voting_codes`
--

CREATE TABLE IF NOT EXISTS `voting_codes` (
  `Code` varchar(20) NOT NULL,
  `UserID` varchar(255) NOT NULL,
  `Refer` text NOT NULL,
  PRIMARY KEY (`Code`,`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `winner_cache`
--

CREATE TABLE IF NOT EXISTS `winner_cache` (
  `CategoryID` varchar(30) NOT NULL,
  `Filter` varchar(30) NOT NULL,
  `Results` text NOT NULL,
  `Steps` text NOT NULL,
  `Warnings` text NOT NULL,
  `Votes` int(11) NOT NULL,
  PRIMARY KEY (`CategoryID`,`Filter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
