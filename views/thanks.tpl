<script type="text/javascript" src="https://www.modernizr.com/downloads/modernizr-2.0.6.js"></script>
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

@font-face {
  font-family: "Blade Runner";
  src: url("/public/blade_runner.ttf");
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
	/*border: 5px solid aqua;
	border-radius: 5px;
	
	-moz-box-shadow:    0px 0px 20px 5px aqua;
	-webkit-box-shadow: 0px 0px 20px 5px aqua;
	box-shadow:         0px 0px 20px 5px aqua;*/
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
  border: 3px solid white;
  /*height: 460px; */
  
  -moz-box-shadow:    0px 0px 10px 3px white;
	-webkit-box-shadow: 0px 0px 10px 3px white;
	box-shadow:         0px 0px 10px 3px white;
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
  font-family: "Blade Runner", "Century Gothic", arial, sans-serif;
  font-size: 41px;
  line-height: 1em;
  margin-top: 15px;
  margin-bottom: 15px;
}

.title h2 {
  color: #1F1F1F;
  color: silver;
  color: yellow;
  font-family: "Blade Runner", "Lucida Sans Unicode", arial, sans-serif;
  font-size: 30px;
  font-weight: normal;
  line-height: 0.95em;
}

.subtitle {
  text-align: center;
  font-size: 30px;
  line-height: 1em;
  margin: 20px;
}

.disabled {
  color: grey;
}

.subtitle .main {
  font-size: 40px;
  line-height: 1em;
}

.special {
  color: lime;
}

.special:hover {
  color: #1EBF15;
}
</style>

<div class="stats marquee">
  <marquee scrollamount="3">
  [<tag:statHTML />]
  </marquee>
</div>

<div id="wrapper">
  <div class="row">
    <div class="span8 offset2">
      <div class="logo">
        <iframe width="100%" height="460" src="https://www.youtube.com/embed/6MxWMJCOKSY?rel=0" frameborder="0" allowfullscreen=""></iframe>
        <!-- <img src="/public/80s_vsync.png"> -->
      </div>
      <div class="title">
        <h1>The 2012 Vidya Gaem Awards</h1>
        <h2 id="countdown">Thanks for watching!</h2>
      </div>
      <div class="subtitle">
        <a href="https://www.youtube.com/watch?v=6MxWMJCOKSY" class="special">watch it on youtube</span></a><br>
        <a href="https://www.youtube.com/watch?v=cRVOMHFBEjA">watch the director's cut</a><br>
        <a href="https://mega.co.nz/#!kwwCxJpJ!QRS-ii80ti2n3KDDYKacImO0ggwoneB78mikvjqUYDw" class="special">direct download</a> - includes preshow<br>
        <a href="http://vga.rbt.asia/">download mirror</a><br>
        <a href="https://www.twitch.tv/vidyagaemawards/b/373126916">watch it on twitch</a> - includes preshow<br>
        <a href="/winners">see the winners</a><br>
        <a href="/credits">see the credits</a><br>
        <a href="https://vidyagaemawards.com/music/2012-vga-music-mix.mp3">preshow music mix</a><br>
        <a href="https://github.com/clamburger/vidya-gaem-awards">website source code</a>
      </div>
      <div class="subtitle">
        <a href="/home" class="main">continue to the main site</a><br>
        <a href="https://2011.vidyagaemawards.com" class="main">view the 2011 site</a>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $('marquee').marquee();
</script>
