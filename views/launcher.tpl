    
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>The Vidya Gaem Awards</title>
<link rel="stylesheet" href="https://2012.vidyagaemawards.com/public/bootstrap-2.1.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://2012.vidyagaemawards.com/public/bootstrap-2.1.0/css/bootstrap-responsive.min.css">
<script type="text/javascript" src="https://www.modernizr.com/downloads/modernizr-2.0.6.js"></script>
<style type="text/css">
@font-face {
    font-family: "Bebas Neue";
    src: url('/public/fonts/BebasNeue.eot');
    src: url('/public/fonts/BebasNeue.eot?#iefix') format('embedded-opentype'),
    url("/public/fonts/BebasNeue.woff") format("woff"),
    url("/public/fonts/BebasNeue.ttf") format("truetype");
    url("/public/fonts/BebasNeue.svg#svgBebasNeue") format("svg"),
}

body {
    background-image: url("/public/2014voting/bg_tile.png");
    background-color: #212121;
    font-family: Calibri, Arial, sans-serif;
    text-align: center;
    color: white;
}
.logo {
    margin-bottom: 20px;
    /*border: 1px solid #34345C;*/
    height: 462px;
    margin-left: auto;
    margin-right: auto;
}
.subtitle {
    font-size: 40px;
    line-height: 40px;
    margin: 10px;
}
.subsubtitle {
    font-size: 25px;
    margin: 20px;
}
a, a:hover {
    text-decoration: none;
    color: #f2ff1a;
    display: block;
    padding: 5px;
}
a:hover {
    background: rgba(0,0,0,0.20)
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
.timezones {
  font-size: 17px;
}

.timezones dl {
  margin-top: 0px;
}

.timezones dt {
  margin-top: 20px;
}

.timezones dd {
  margin-left: 0px;
}

.timezones dt:first-child {
  margin-top: 0px;
}

/*a {
  color: aqua;
}

a:hover {
  color: #08c;
}

body {
  background-color: black;
    background-image: url("/public/space.png");
    background-repeat: repeat;
    font-size: 16px;
    line-height: 16px;
    overflow-x: hidden;
    color: silver;
    color: white;
    font-family: "Neon 80s";
}*/

#wrapper {
    overflow: auto;
    width: 1180px;
    margin: 20px auto;
    padding: 10px;
    /*border: 5px solid aqua;
    border-radius: 5px;
    
    -moz-box-shadow:    0px 0px 20px 5px aqua;
    -webkit-box-shadow: 0px 0px 20px 5px aqua;
    box-shadow:         0px 0px 20px 5px aqua;*/
}

.logo {
  /*border: 3px solid aqua;*/
  /*height: 460px;*/
  
  /*-moz-box-shadow:    0px 0px 10px 3px aqua;
    -webkit-box-shadow: 0px 0px 10px 3px aqua;
    box-shadow:         0px 0px 10px 3px aqua;*/
}

.title {
  text-align: center;
}

.title h1 {
  font-weight: normal;
  font-size: 72px;
  line-height: 1em;
  font-family: "Bebas Neue", Tahoma, sans-serif;
  text-transform: uppercase;
  color: #f2ff1a;
  margin-bottom: 0;
  padding-bottom: 10px;
}

.title h2 {
  font-size: 38px;
  font-weight: normal;
  line-height: 0.95em;
  font-family: "Bebas Neue", Tahoma, sans-serif;
  color: #bfbfbf;
}

.subtitle {
  text-align: center;
  font-size: 30px;
  margin: 20px;
}
</style>
</head>
<body>

<div class="container">

<div id="wrapper">
  <div class="row">
    <div class="span2 timezones">
      <dl>
        <if:dateset>
        <loop:timezonesLeft>
        <dt><abbr title="UTC <tag:timezonesLeft[].Offset />"><tag:timezonesLeft[].Name /></abbr></dt>
        <dd><tag:timezonesLeft[].Time /></dd>
        </loop:timezonesLeft>
        <dt><a href="<tag:websiteLink />">Other Timezones</a></dt>
        </if:dateset>
      </dl>
    </div>
    <div class="span8">
      <div class="logo">
        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/s1M9QRBS7Bg?rel=0" frameborder="0" allowfullscreen=""></iframe>
      </div>
      <div class="title">
        <h1>On time. For once.</h1>
        <if:countdown>
        <h2 id="countdown">&nbsp;</h2>
        <else:countdown>
        <h2 id="countdown">Ceremony date not yet confirmed</h2>
        </if:countdown>
      </div>
      <!-- <div class="subtitle">
        <a href="/home">continue to the main site</a>
      </div> -->
    </div>
    <div class="span2 timezones">
      <dl>
        <if:dateset>
        <loop:timezonesRight>
        <dt><abbr title="UTC <tag:timezonesRight[].Offset />"><tag:timezonesRight[].Name /></abbr></dt>
        <dd><tag:timezonesRight[].Time /></dd>
        </loop:timezonesRight>
        <dt><a href="<tag:websiteLink />">Other Timezones</a></dt>
        </if:dateset>
      </dl>
    </div>
  </div>
</div>
</div>

<if:dateset>
<script type="text/javascript">
  var serverLoad = Date.parse("<tag:serverDate />");
  var clientLoad = Date.now();

  function updateWCTime() {

    var
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
      
    document.getElementById("countdown").innerHTML = string;
    
  }
  window.onload = updateWCTime;
  setInterval('updateWCTime()', 1000 ); 
</script>
<else:dateset>
<script type="text/javascript">
  document.getElementById("countdown").innerHTML = "Streaming later this week!";
</script>
</if:dateset>

</body>
</html>