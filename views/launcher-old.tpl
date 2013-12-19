    
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
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
    background: #EEF2FF url('http://i.imgur.com/vhP2J.png') top center repeat-x;
}
.logo {
    margin: 20px;
    /*border: 1px solid #34345C;*/
    width: 800px;
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

    document.getElementById("countdown").innerHTML = "The beta begins in " + string;
    
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

<img src="http://i.imgur.com/J0cW3.png" alt="The Vidya Gaem Awards Logo" class="logo" /> <br />
<!-- <div class="logo">
    <iframe width="800" height="480" src="http://www.youtube.com/embed/3I-Kb4yVWsQ?rel=0" frameborder="0" allowfullscreen></iframe>
</div> -->

<img src="/public/geocities.gif" /><br />

<div class="subtitle">2012 Vidya Gaem Awards
<div id="countdown">We'll be online soon.</div>
</div>

<!if:countdown>
<div class="subsubtitle">
    <a href="http://2011.vidyagaemawards.com">View 2011 site instead</a>
</div>
</!if:countdown>

<div class="subsubtitle">
    Want to get involved? Send us an email at <a href="mailto:vidya@vidyagaemawards.com">vidya@vidyagaemawards.com</a> or join our <a href="steam://friends/joinchat/103582791432684008">Steam chat</a>
</div>

<br />
</body>
</html>