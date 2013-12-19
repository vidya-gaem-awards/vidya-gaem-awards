<script type="text/javascript" src="http://www.modernizr.com/downloads/modernizr-2.0.6.js"></script>
<script type="text/javascript" src="/public/jquery/jquery.marquee.js"></script>

<!-- <audio autoplay loop>
   <source src="/public/2spooky.mp3" type='audio/mpeg; codecs="mp3"'>
   <source src="/public/2spooky.ogg" type='audio/ogg; codecs="vorbis"'>
</audio> -->

<style type="text/css">
@font-face {
  font-family: "Neon 80s";
  src: url("/public/Neon.ttf");
}

@font-face {
  font-family: "Press Start 2P";
  src: url("/public/PressStart2P.ttf");
}

a {
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
}

.timezones {
  /*font-family: "Press Start 2P", "Consolas", "Liberation Sans Mono", monospace;
  font-size: 12px;*/
}

#wrapper {
    overflow: auto;
    width: 1180px;
    margin: 0 auto;
    padding: 10px;
    /*background: #d6daf0;*/
    border: 5px solid aqua;
    border-radius: 5px;
    
    -moz-box-shadow:    0px 0px 20px 5px aqua;
    -webkit-box-shadow: 0px 0px 20px 5px aqua;
    box-shadow:         0px 0px 20px 5px aqua;
}

.stats {
    text-align: center;
    color: #89A;
    color: grey;
    font-size: 9pt;
    font-family: "Press Start 2P", monospace;
    margin-top: -20px;
    margin-bottom: 20px;
    width: 1210px;
}
.stat {
    color: #34345C;
    color: silver;
    padding: 1px;
}

.logo {
  border: 3px solid aqua;
  height: 460px;
  
  -moz-box-shadow:    0px 0px 10px 3px aqua;
    -webkit-box-shadow: 0px 0px 10px 3px aqua;
    box-shadow:         0px 0px 10px 3px aqua;
}

.title {
  text-align: center;
}

.title h1 {
  /*text-shadow: black 1px 1px 2px;*/
  color: #789922;
  color: fuchsia;
  font-weight: normal;
  /*font-style: italic;*/
  font-family: "Neon 80s", "Century Gothic", arial, sans-serif;
  font-size: 72px;
  line-height: 1em;
}

.title h2 {
  color: #1F1F1F;
  color: silver;
  color: yellow;
  font-family: "Neon 80s", "Lucida Sans Unicode", arial, sans-serif;
  font-size: 38px;
  font-weight: normal;
  line-height: 0.95em;
}

.subtitle {
  text-align: center;
  font-size: 30px;
  margin: 20px;
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

.timezones dt:first-child {
  margin-top: 0px;
}
</style>

<div class="stats marquee">
  <marquee scrollamount="3">
  [<tag:statHTML />]
  </marquee>
</div>

<div id="wrapper">
  <div class="row">
    <div class="span2 timezones">
      <dl>
        <loop:timezonesLeft>
        <dt><abbr title="UTC <tag:timezonesLeft[].Offset />"><tag:timezonesLeft[].Name /></abbr></dt>
        <dd><tag:timezonesLeft[].Time /></dd>
        </loop:timezonesLeft>
        <dt><a href="<tag:websiteLink />">Other Timezones</a></dt>
      </dl>
    </div>
    <div class="span8">
      <div class="logo">
        <iframe width="100%" height="460" src="http://www.youtube.com/embed/MvTt8g4gaHY?rel=0" frameborder="0" allowfullscreen=""></iframe>
      </div>
      <div class="title">
        <h1>It's happening. Finally.</h1>
        <if:countdown>
        <h2 id="countdown">&nbsp;</h2>
        <else:countdown>
        <h2 id="countdown">Ceremony date not yet confirmed</h2>
        </if:countdown>
      </div>
      <div class="subtitle">
        <a href="/home">continue to the main site</a>
      </div>
    </div>
    <div class="span2 timezones">
      <dl>
        <loop:timezonesRight>
        <dt><abbr title="UTC <tag:timezonesRight[].Offset />"><tag:timezonesRight[].Name /></abbr></dt>
        <dd><tag:timezonesRight[].Time /></dd>
        </loop:timezonesRight>
        <dt><a href="<tag:websiteLink />">Other Timezones</a></dt>
      </dl>
    </div>
  </div>
</div>

<script type="text/javascript">
  $('marquee').marquee();
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