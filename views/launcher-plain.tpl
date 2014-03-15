    
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>The Vidya Gaem Awards</title>
<link rel="stylesheet" href="/public/bootstrap-2.1.0/css/bootstrap.min.css">
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
    /*background: #EEF2FF url('http://i.imgur.com/vhP2J.png') top center repeat-x;*/
    background: black;
color: white;
}
.logo {
    margin: 20px;
    /*border: 1px solid #34345C;*/
    width: 958px;
    height: 420px;
    background: url('http://i.imgur.com/0g30jh0.gif');
    background-position: center;
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
    /*font-size: 50%;*/
}
.timezones{font-size:17px;}.timezones dl{margin-top:0px;}.timezones dt{margin-top:20px;}.timezones dt:first-child{margin-top:0px;}
</style>
<link href='http://fonts.googleapis.com/css?family=Asap' rel='stylesheet' type='text/css'>
<if:countdown>
<script type="text/javascript">
var serverLoad = Date.parse("<tag:serverDate />");
var clientLoad = Date.now();

function updateWCTime() {

  var
    //now = Date.parse("<?php echo date("D, j M Y H:i:s \U\TCO"); ?>"),
    clientNow = Date.now(),
    serverNow = clientNow - clientLoad + serverLoad - 1;
    
    kickoff = Date.parse("<tag:countdown />"),
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

    var string = dd + ' day' + (dd == 1 ? ', ' : 's, ') + hh + ' hour' +
        (hh == 1 ? ', ' : 's, ') + mm + ' minute' + (mm == 1 ? ', ' : 's, ') +
        ss + ' second' + (ss == 1 ? '' : 's');

    document.getElementById("countdown").innerHTML = "Streaming live in " + string;
    
}
window.onload = updateWCTime;
setInterval('updateWCTime()', 1000 );

function showTimezones() {
    document.getElementById("timezones").style.display = "";
}
</script>
</if:countdown>
</head>
<body>

<!-- <div class="stats">
    [<tag:statHTML />]
</div> -->

<div class="container">

<div id="wrapper">
<div class="row">
<div class="span2 timezones">
<dl>
<dt><abbr title="UTC -10:00">Honolulu</abbr></dt>
<dd>Sat Mar 2nd, 13:00</dd>
<dt><abbr title="UTC -09:00">Anchorage</abbr></dt>
<dd>Sat Mar 2nd, 14:00</dd>
<dt><abbr title="UTC -08:00">Los Angeles (PST)</abbr></dt>
<dd>Sat Mar 2nd, 15:00</dd>
<dt><abbr title="UTC -07:00">Denver (MST)</abbr></dt>
<dd>Sat Mar 2nd, 16:00</dd>
<dt><abbr title="UTC -06:00">Chicago (CST)</abbr></dt>
<dd>Sat Mar 2nd, 17:00</dd>
<dt><abbr title="UTC -05:00">4chan Time (EST)</abbr></dt>
<dd>Sat Mar 2nd, 18:00</dd>
<dt><abbr title="UTC -03:00">Rio de Janeiro</abbr></dt>
<dd>Sat Mar 2nd, 20:00</dd>
<dt><abbr title="UTC +00:00">London (GMT)</abbr></dt>
<dd>Sat Mar 2nd, 23:00</dd>
<dt><a href="http://timeanddate.com/worldclock/fixedtime.html?msg=2012+Vidya+Gaem+Awards&iso=20130302T1800&p1=179&sort=1">Other Timezones</a></dt>
</dl>
</div>
<div class="span8">
<div class="logo">
<iframe width="100%" height="460" src="http://www.youtube.com/embed/MvTt8g4gaHY?rel=0" frameborder="0" allowfullscreen=""></iframe>
</div>
<div class="title">
<h1>Streaming live for the third year!</h1>
<h2 id="countdown">Ceremony date not yet confirmed</h2>
</div>
<div class="subtitle">
<a href="/home">continue to the main site</a>
</div>
</div>
<div class="span2 timezones">
<dl>
<dt><abbr title="UTC +01:00">Paris (CET)</abbr></dt>
<dd>Sun Mar 3rd, 00:00</dd>
<dt><abbr title="UTC +02:00">Athens (EET)</abbr></dt>
<dd>Sun Mar 3rd, 01:00</dd>
<dt><abbr title="UTC +04:00">Moscow</abbr></dt>
<dd>Sun Mar 3rd, 03:00</dd>
<dt><abbr title="UTC +08:00">Singapore</abbr></dt>
<dd>Sun Mar 3rd, 07:00</dd>
<dt><abbr title="UTC +09:00">Japan Time</abbr></dt>
<dd>Sun Mar 3rd, 08:00</dd>
<dt><abbr title="UTC +10:00">Brisbane (AEST)</abbr></dt>
<dd>Sun Mar 3rd, 09:00</dd>
<dt><abbr title="UTC +11:00">Sydney (AEDT)</abbr></dt>
<dd>Sun Mar 3rd, 10:00</dd>
<dt><abbr title="UTC +13:00">Auckland</abbr></dt>
<dd>Sun Mar 3rd, 12:00</dd>
<dt><a href="http://timeanddate.com/worldclock/fixedtime.html?msg=2012+Vidya+Gaem+Awards&iso=20130302T1800&p1=179&sort=1">Other Timezones</a></dt>
</dl>
</div>
</div>
</div>

</div>

</body>
</html>
