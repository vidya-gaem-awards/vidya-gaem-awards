SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `2010_releases`
--

CREATE TABLE IF NOT EXISTS `2010_releases` (
  `Game` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `Notable` tinyint(1) NOT NULL DEFAULT '0',
  `PC` tinyint(1) NOT NULL DEFAULT '0',
  `PS3` tinyint(1) NOT NULL DEFAULT '0',
  `PS4` tinyint(1) NOT NULL DEFAULT '0',
  `PSV` tinyint(1) NOT NULL DEFAULT '0',
  `PSN` tinyint(1) NOT NULL DEFAULT '0',
  `360` tinyint(1) NOT NULL DEFAULT '0',
  `XB1` tinyint(1) NOT NULL DEFAULT '0',
  `XBLA` tinyint(1) NOT NULL DEFAULT '0',
  `Wii` tinyint(1) NOT NULL DEFAULT '0',
  `WiiU` tinyint(1) NOT NULL DEFAULT '0',
  `WiiWare` tinyint(1) NOT NULL DEFAULT '0',
  `3DS` tinyint(1) NOT NULL DEFAULT '0',
  `Ouya` tinyint(1) NOT NULL DEFAULT '0',
  `Mobile` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Game`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

CREATE TABLE IF NOT EXISTS `access` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UniqueID` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UserID` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Page` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `RequestString` text COLLATE utf8_unicode_ci NOT NULL,
  `RequestMethod` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `IP` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `UserAgent` text COLLATE utf8_unicode_ci NOT NULL,
  `Filename` text COLLATE utf8_unicode_ci NOT NULL,
  `Refer` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`ID`),
  KEY `UniqueID` (`UniqueID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UserID` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Timestamp` datetime NOT NULL,
  `Page` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Action` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `SpecificID1` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `SpecificID2` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT ;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE IF NOT EXISTS `applications` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `Email` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `Interest` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Timestamp` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `autocompleters`
--

CREATE TABLE IF NOT EXISTS `autocompleters` (
  `ID` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Strings` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `ID` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Name` text COLLATE utf8_unicode_ci NOT NULL,
  `Subtitle` text COLLATE utf8_unicode_ci NOT NULL,
  `Order` smallint(5) NOT NULL,
  `Comments` text COLLATE utf8_unicode_ci,
  `Enabled` tinyint(1) NOT NULL DEFAULT '1',
  `NominationsEnabled` tinyint(1) NOT NULL DEFAULT '1',
  `Secret` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Secret categories only show up during voting',
  `AutocompleteCategory` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category_feedback`
--

CREATE TABLE IF NOT EXISTS `category_feedback` (
  `CategoryID` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `UserID` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Opinion` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`CategoryID`,`UserID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE IF NOT EXISTS `feedback` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `UserID` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `UniqueID` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Timestamp` datetime NOT NULL,
  `GeneralRating` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `CeremonyRating` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `BestThing` text COLLATE utf8_unicode_ci,
  `WorstThing` text COLLATE utf8_unicode_ci,
  `OtherComments` text COLLATE utf8_unicode_ci,
  `Questions` text COLLATE utf8_unicode_ci,
  `Email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE IF NOT EXISTS `history` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Table` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `EntryID` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `Values` text COLLATE utf8_unicode_ci NOT NULL,
  `Timestamp` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE IF NOT EXISTS `logins` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Timestamp` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `login_tokens`
--

CREATE TABLE IF NOT EXISTS `login_tokens` (
  `UserID` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Avatar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Generated` datetime NOT NULL,
  `Expires` datetime NOT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Headline` text COLLATE utf8_unicode_ci,
  `Text` text COLLATE utf8_unicode_ci NOT NULL,
  `UserID` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Timestamp` datetime NOT NULL,
  `Visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `nominees`
--

CREATE TABLE IF NOT EXISTS `nominees` (
  `CategoryID` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `NomineeID` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Subtitle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Image` text COLLATE utf8_unicode_ci NOT NULL,
  `FlavorText` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`CategoryID`,`NomineeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `SteamID` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `Special` tinyint(10) unsigned NOT NULL DEFAULT '0',
  `FirstLogin` datetime DEFAULT NULL,
  `LastLogin` datetime DEFAULT NULL,
  `PrimaryRole` text COLLATE utf8_unicode_ci,
  `Email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Notes` text COLLATE utf8_unicode_ci,
  `Website` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Avatar` blob,
  PRIMARY KEY (`SteamID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `UserID` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `GroupName` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`UserID`,`GroupName`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_nominations`
--

CREATE TABLE IF NOT EXISTS `user_nominations` (
  `CategoryID` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `OriginalCategory` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `UserID` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Nomination` text COLLATE utf8_unicode_ci NOT NULL,
  `Timestamp` datetime NOT NULL,
  `AutoID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UniqueID` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`AutoID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `user_rights`
--

CREATE TABLE IF NOT EXISTS `user_rights` (
  `GroupName` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `CanDo` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Description` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`GroupName`,`CanDo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
  `UniqueID` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `CategoryID` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Preferences` text COLLATE utf8_unicode_ci NOT NULL,
  `Timestamp` datetime NOT NULL,
  `UserID` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `IP` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `VotingCode` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Number` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`UniqueID`,`CategoryID`) USING BTREE,
  KEY `UniqueID` (`UniqueID`),
  KEY `UniqueID_2` (`UniqueID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voting_codes`
--

CREATE TABLE IF NOT EXISTS `voting_codes` (
  `Code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `UserID` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Refer` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Code`,`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `winner_cache`
--

CREATE TABLE IF NOT EXISTS `winner_cache` (
  `CategoryID` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Filter` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Results` text COLLATE utf8_unicode_ci NOT NULL,
  `Steps` text COLLATE utf8_unicode_ci NOT NULL,
  `Warnings` text COLLATE utf8_unicode_ci NOT NULL,
  `Votes` int(11) NOT NULL,
  PRIMARY KEY (`CategoryID`,`Filter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;