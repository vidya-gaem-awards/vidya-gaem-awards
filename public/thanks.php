<?php
include(__DIR__."/../includes/php.php");

$stats = array(
	"days taken" => 58,
	"category feedback" => 21209,
	"nominations made" => 1620, 
	"total votes" => 173899,
	"unique users" => 7955,
	"poll votes" => 2896,
	"suggestions entered" => 1203,
	"steam group members" => 843,
	"forum accounts" => 1399,
	"page views" => 1077269
);
$stats2 = array();

foreach ($stats as $stat => $number) {
	$stats2[] = "<span class='stat'>$stat: ".number_format($number)."</span>";
}

$statHTML = implode(" / ", $stats2);

date_default_timezone_set("Australia/Brisbane");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta name="google-site-verification" content="sarD3HFFz6T0U-KpnyD_rty-z8QwgKiV94LffWwJblc" />
<title>The Vidya Gaem Awards</title>
<style type="text/css">
body, html {
	padding: 0px;
	margin: 0px;
	height: 100%;
	width: 100%;
	font-family: Asap, sans-serif;
	text-align: center;
}
body {
	background: #EEF2FF url('https://i.imgur.com/vhP2J.png') top center repeat-x;
}
.logo {
	margin: 20px;
	border: 1px solid #34345C;
	width: 853px;
	height: 480px;
	margin-left: auto;
	margin-right: auto;
}
.subtitle {
	font-size: 40px;
	margin: 10px;
}
.subsubtitle {
	font-size: 25px;
	margin: 20px;
}
a {
	text-decoration: none;
	color: #0069D6;
}
a:hover {
	text-decoration: underline;
	color: #00438A;
}
.stats {
	text-align: center;
	color: #89A;
	font-size: 9pt;
	font-family: arial, helvetica, sans-serif;
}
.stat {
	color: #34345C;
	padding: 1px;
}
#countdown {
	font-size: 50%;
}
#timezones {
	width: 600px;
	margin-left: auto; 
    margin-right: auto;
    border-collapse: collapse;
    margin-bottom: 10px;
}
.timezones td {
	padding: 3px;
}
.implying {
	color: #789922;
}
</style>
<link href='https://fonts.googleapis.com/css?family=Asap' rel='stylesheet' type='text/css'>
</head>
<body>

<div class="stats">
	[<?php echo $statHTML; ?>]
</div>

<!-- <img src="https://i.imgur.com/J0cW3.png" alt="The Vidya Gaem Awards Logo" class="logo" /> -->
<div class="logo">
	<iframe width="853" height="480" src="https://www.youtube.com/embed/mOGRDuY0DFI?rel=0" frameborder="0" allowfullscreen></iframe>
	<!-- <div class="subsubtitle">The video was taken down by YouTube, and we are currently working on getting it back online. Here are some alternate downloads you can use in the meantime.<br /><br />
	<a href="magnet:?xt=urn:btih:4DF4AB729CD26C12D8F9029DFB9A8077EC34E86E&dn=2011%20Vidya%20Gaem%20Awards.mp4
&tr=udp%3a//tracker.openbittorrent.com%3a80">Torrent (magnet link)</a><br />
	<a href="https://rapidshare.com/files/2299693433/2011_Vidya_Gaem_Awards.mp4">RapidShare</a><br />
	<a href="http://www.mediafire.com/?nabbrxt6em4j4bb">MediaFire</a><br />
	<a href="http://www.megaupload.com/?d=L5Z5RPHA">MegaUpload</a><br /><br />
	Thanks to the anons who uploaded the video to various places.</div> -->
</div>

<div class="subtitle">The 2011 Vidya Gaem Awards</span>
	<div id="countdown"></div>
</div>

<div class="subsubtitle">
	<a href="https://www.youtube.com/watch?v=mOGRDuY0DFI">view on YouTube</a><br />
	<a href="https://archive.org/download/The2011VidyaGaemAwardsvgas/2011VidyaGaemAwardsHq.wmv">direct download</a> (3.0 GB)<br />
	<!-- <a href="feedback.php">give feedback on the VGAs</a><br /> -->
	<a href="https://docs.google.com/document/d/1_9F5oA7cfbSPdv08cEBAgYuhds78CjtcjOB2OoiY4gc/edit">view the Q&amp;A</a><br />
	<a href="results.php">see the detailed results</a><br />
</div>

<div class="subtitle">
	<a href="home.php">continue to webpage</a>
</div>
<br />
</body>
</html>
