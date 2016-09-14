<?php
include("includes/php.php");

$stats = array(
	"days taken" => 57,
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
.container {
	margin: 20px;
	width: 800px;
	margin-left: auto;
	margin-right: auto;
	margin-bottom: 10px;
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
.stream {
	float: left;
	margin-right: 20px;
	margin-left: 20px;
	border: 2px solid #34345C;
	width: 800px;
	height: 480px;
}

.chat {
	float: right;
	width: 340px;
	height: 480px;
	border: 2px solid #34345C;
}
.chatLeft {
	float: left;
	width: 340px;
	height: 480px;
	border: 2px solid #34345C;
}
.subtitle {
	font-size: 40px;
	margin: 10px;
	clear: both;
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
</style>
<link href='https://fonts.googleapis.com/css?family=Asap' rel='stylesheet' type='text/css'>
<script type="text/javascript">
var serverLoad = Date.parse("<?php echo date("D, j M Y H:i:s \U\TCO"); ?>");
var clientLoad = Date.now();

function updateWCTime() {

  var
	//now = Date.parse("<?php echo date("D, j M Y H:i:s \U\TCO"); ?>"),
	clientNow = Date.now(),
	serverNow = clientNow - clientLoad + serverLoad - 1;
	
	kickoff = Date.parse("Sun, 12 Feb 2012 09:00:00 UTC+1000"),
	diff = kickoff - serverNow,
	
	diff = Math.max(diff, 0),

	days = Math.floor( diff / (1000*60*60*24) ),
	hours = Math.floor( diff / (1000*60*60) ),
	mins = Math.floor( diff / (1000*60) ),
	secs = Math.floor( diff / 1000 ),

	dd = days,
	hh = hours - days * 24,
	mm = mins - hours * 60,
	ss = secs - mins * 60;

	var string = hh + ' hour' +
		(hh == 1 ? ', ' : 's, ') + mm + ' minute' + (mm == 1 ? ', ' : 's, ') +
		ss + ' second' + (ss == 1 ? '' : 's');

	document.getElementById("countdown").innerHTML = string;

}
window.onload = updateWCTime;
setInterval('updateWCTime()', 1000 );

function showTimezones() {
	$("#timezones").toggle();
}

function switchChat() {
	$('#twitch').toggle();
	$('#chatango').toggle();
}
</script>
<script src="https://www-cdn.justin.tv/javascripts/jtv_api.js"></script>
<script src="https://2011.vidyagaemawards.com/includes/jquery-1.7.1.min.js"></script>
</head>
<body>

<!-- <div class="stats">
	[<?php echo $statHTML; ?>]
</div> -->

<img src="https://i.imgur.com/J0cW3.png" alt="The Vidya Gaem Awards Logo" height="200px" />

<div class="container">

	<div class="stream" id="stream">
	
		<object type="application/x-shockwave-flash"
			height="480" width="800"
			id="live_embed_player_flash"
			data="https://www.justin.tv/widgets/live_embed_player.swf?channel=vidyagaemawards"
			bgcolor="#000000">
			<param name="allowFullScreen" value="true" />
			<param name="allowScriptAccess" value="always" />
			<param name="allowNetworking" value="all" />
			<param name="movie" value="https://www.justin.tv/widgets/live_embed_player.swf" />
			<param name="flashvars" value="hostname=www.justin.tv&channel=vidyagaemawards&auto_play=true&start_volume=25&consumer_key=ZvBaRzHpjrpVwIBwax9vOw" />
		</object>
	
	</div>
	
</div>

<div class="subtitle">February 11th at 6pm (4chan time)
	<div id="countdown">&nbsp;</div>
</div>

<div class="subsubtitle">
	<a href="#timezones" onclick="showTimezones();">show timezones</a><br />
	<a href="home.php">continue to webpage</a>
</div>

<table id="timezones" border="1" style="display: none;">
<tr>
	<td colspan="3">Depending on your timezone, the ceremony will take place on<br /><strong>Saturday 11/02/12</strong> or <strong>Sunday 12/02/12</strong>.</td>
</tr>
<tr>
<td>UTC-10</td>
<td>Hawaii</td>
<td>Saturday 13:00</td>
</tr>
<tr>
<td>UTC-08</td>
<td><em>Pacific Standard Time</em></td>
<td>Saturday 15:00</td>
</tr>
<tr>
<td>UTC-07</td>
<td><em>Mountain Standard Time</em></td>
<td>Saturday 16:00</td>
</tr>
<tr>
<td>UTC-06</td>
<td><em>Central Standard Time</em></td>
<td>Saturday 17:00</td>
</tr>
<tr>
<td>UTC-05</td>
<td><em>Eastern Standard Time</em></td>
<td>Saturday 18:00</td>
</tr>
<tr>
<td>UTC-02</td>
<td>Brazil</td>
<td>Saturday 21:00</td>
</tr>
<tr>
<td>UTC+00</td>
<td><em>Western European Time</em></td>
<td>Saturday 23:00</td>
</tr>
<tr>
<td>UTC+01</td>
<td><em>Central European Time</em></td>
<td>Sunday 00:00</td>
</tr>
<tr>
<td>UTC+02</td>
<td><em>Eastern European Time</em></td>
<td>Sunday 01:00</td>
</tr>
<tr>
<td>UTC+04</td>
<td>Moscow</td>
<td>Sunday 03:00</td>
</tr>
<tr>
<td>UTC+08</td>
<td>Singapore</td>
<td>Sunday 07:00</td>
</tr>
<tr>
<td>UTC+09</td>
<td>Tokyo</td>
<td>Sunday 08:00</td>
</tr>
<tr>
<td>UTC+10</td>
<td>Brisbane</td>
<td>Sunday 09:00</td>
</tr>
<tr>
<td>UTC+11</td>
<td>Sydney</td>
<td>Sunday 10:00</td>
</tr>
<tr>
<td>UTC+13</td>
<td>Auckland</td>
<td>Sunday 12:00</td>
</tr>
<tr>
<td colspan="3">
Timezone not listed? Check <a href="http://www.timeanddate.com/worldclock/fixedtime.html?msg=Vidya+Gaem+Awards&iso=20120211T23&p1=136&sort=1">this page</a>.
</td>
</tr>
</table>
<br />

</body>
</html>
