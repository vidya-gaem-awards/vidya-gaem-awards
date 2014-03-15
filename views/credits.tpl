<style type="text/css">
@font-face {
  font-family: "ArtBrush";
  src: local("ArtBrush"),
       local("ArtBrush Regular"),
       url("/public/Artbrush.woff") format("woff"),
       url("/public/Artbrush.ttf") format("truetype");
}

@font-face {
  font-family: "Brush Script MT";
  src: local("Brush Script MT"),
       local("Brush Script MT Italic"),
       url("/public/BrushScriptMT.woff") format("woff"),
       url("/public/BrushScriptMT.ttf") format("truetype");
}

body {
  background-color: black;
  background-image: url("/public/50s/bgvoting.jpg"); 
  background-color: #f6e7be;
  /*font-size: 16px;
  line-height: 16px;*/
  overflow-x: hidden;
  color: black;
  font-family: "Calibri", Arial, sans-serif;
}

.navbar, .navbar .navbar-inner {
  -moz-box-shadow:    none;
  -webkit-box-shadow: none;
  box-shadow:         none;
  border:             none;
  background-image:   url("/public/50s/bgvoting.jpg"); 
  background-color:   #f6e7be;
}

.navbar a {
  color: black !important;
  text-shadow: none !important;
  background: none !important;
}
.navbar a:hover {
  background: rgba(0, 0, 0, 0.1) !important;
}

.navbar .nav>.active>a {
  box-shadow: inset 0 3px 15px rgba(0, 0, 0, 0.125);
  -webkit-box-shadow: inset 0 3px 15px rgba(0, 0, 0, 0.125);
  -moz-box-shadow: inset 0 3px 15px rgba(0, 0, 0, 0.125);
}

.navbar-fixed-top {
  /*border-bottom: 3px solid lime;*/
  border: none;
}

.navbar-fixed-bottom {
  /*border-top: 3px solid lime;*/
  border: none;
}

.navbar .brand {
  font-weight: bold;
}

h2 {
  font-family: "Brush Script MT";
  font-size: 40px;
}

.row h1 small {
  color: white;
  font-size: 21px;
  text-shadow: none;
}

.page-header h1 {
  font-family: "ArtBrush";
  color: #509e20;
  font-size: 50px;
}

.page-header {
  text-align: center;
  border: none;
  background: transparent url("/public/50s/featuring_dante_from_the_devil_may_cry_series.png") bottom center no-repeat;
  margin-bottom: 0px;
  padding-bottom: 40px;
}

.page-header.the-bottom {
  background: url("/public/50s/shadow_the_edge.png") center top no-repeat;
  padding-top: 20px;
}

.thumbnails ul {
  list-style-type: none;
  margin-left: 0px;
}

.thumbnail {
  padding-left: 20px;
  padding-right: 20px;
  
}

.thumbnails ul li {
  text-align: right;
  padding-bottom: 3px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.thumbnails ul li:hover {
  white-space: normal;
}

.thumbnails ul li:first-child {
  text-align: left;
  font-weight: bold;
  font-family: "ArtBrush";
}

.thumbnails {
  font-size: larger;
}



.thumbnail {
  min-height: 70px;
}



/*h2.cast {
  text-shadow: 0px 0px 15px yellow;
  color: yellow;
}

.cast a {
  color: yellow;
}

.cast a:hover {
  color: #ffcc00;
}

.cast .thumbnail {
  border: 2px solid yellow;
  -moz-box-shadow:    0px 0px 10px 2px yellow;
    -webkit-box-shadow: 0px 0px 10px 2px yellow;
    box-shadow:         0px 0px 10px 2px yellow;
}



h2.skits {
  text-shadow: 0px 0px 15px aqua;
  color: aqua;
}

.skits a {
  color: aqua;
}

.skits a:hover {
  color: #1B91E0;
}

.skits .thumbnail {
  border: 2px solid aqua;
  -moz-box-shadow:    0px 0px 10px 2px aqua;
    -webkit-box-shadow: 0px 0px 10px 2px aqua;
    box-shadow:         0px 0px 10px 2px aqua;
}


h2.crew {
  text-shadow: 0px 0px 15px lime;
  color: lime;
}

.crew a {
  color: lime;
}

.crew a:hover {
  color: #1EBF15;
}

.crew .thumbnail {
  border: 2px solid lime;
  -moz-box-shadow:    0px 0px 10px 2px lime;
    -webkit-box-shadow: 0px 0px 10px 2px lime;
    box-shadow:         0px 0px 10px 2px lime;
}


h2.media {
  text-shadow: 0px 0px 15px fuchsia;
  color: fuchsia;
}

.media a {
  color: fuchsia;
}

.media a:hover {
  color: #9D15BF;
}

.media .thumbnail {
  border: 2px solid fuchsia;
  -moz-box-shadow:    0px 0px 10px 2px fuchsia;
    -webkit-box-shadow: 0px 0px 10px 2px fuchsia;
    box-shadow:         0px 0px 10px 2px fuchsia;
}

h2.thanks {
  text-shadow: 0px 0px 15px white;
  color: white;
}

.thanks .thumbnail {
  border: 2px solid white;
  -moz-box-shadow:    0px 0px 10px 2px white;
    -webkit-box-shadow: 0px 0px 10px 2px white;
    box-shadow:         0px 0px 10px 2px white;
} */

.thumbnail a:hover {
  background: rgba(0, 0, 0, 0.1);
  text-decoration: none;
  color: #509e20;
}

.thumbnail {
  border: 3px dashed black;
  
  -moz-box-shadow:    0px 0px 30px 5px rgba(0,0,0,0.1);
  -webkit-box-shadow: 0px 0px 30px 5px rgba(0,0,0,0.1);
  box-shadow:         0px 0px 30px 5px rgba(0,0,0,0.1);
}

.hr {
	margin: 0 auto;
	height: 8px;
	background: url("/public/50s/underline.png") center center no-repeat;
}
</style>

<div class="page-header">
  <h1>Cast and Crew of the 2013 Vidya Gaem Awards</h1>
  <div class="hr"></div>
</div>

<h2 class="cast">Cast (in order of appearance)</h2>

<div class="row cast">
  <div class="span12">
    <ul class="thumbnails">
      <loop:cast>
      <li class="span3">
        <div class="thumbnail">
          <ul>
            <tag:cast[] />
          </ul>
        </div>
      </li>
      </loop:cast>
    </ul>
  </div>
</div>

<h2 class="skits">Skits (in order of appearance)</h2>

<div class="row skits">
  <div class="span12">
    <ul class="thumbnails">
      <loop:skits>
      <li class="span3">
        <div class="thumbnail">
          <ul>
            <tag:skits[] />
          </ul>
        </div>
      </li>
      </loop:skits>
    </ul>
  </div>
</div>

<h2 class="skits">"/v/ plays" skits (in alphabetical order)</h2>

<div class="row skits">
  <div class="span12">
    <ul class="thumbnails">
      <loop:vPlays>
      <li class="span3">
        <div class="thumbnail">
          <ul>
            <tag:vPlays[] />
          </ul>
        </div>
      </li>
      </loop:vPlays>
    </ul>
  </div>
</div>

<h2 class="crew">Crew (in alphabetical order)</h2>

<div class="row crew">
  <div class="span12">
    <ul class="thumbnails">
      <loop:crew>
      <li class="span3">
        <div class="thumbnail">
          <ul>
            <tag:crew[] />
          </ul>
        </div>
      </li>
      </loop:crew>
    </ul>
  </div>
</div>

<h2 class="media">Media (used with permission)</h2>

<div class="row media">
  <div class="span12">
    <ul class="thumbnails">
      <loop:media>
      <li class="span3">
        <div class="thumbnail">
          <ul>
            <tag:media[] />
          </ul>
        </div>
      </li>
      </loop:media>
    </ul>
  </div>
</div>

<h2 class="thanks">Special Thanks</h2>

<div class="row thanks">
  <div class="span12">
    <ul class="thumbnails">
      <loop:thanks>
      <li class="span3">
        <div class="thumbnail">
          <ul>
            <tag:thanks[] />
          </ul>
        </div>
      </li>
      </loop:thanks>
    </ul>
  </div>
</div>

<div class="page-header the-bottom">
  <h1>&nbsp;</h1>
</div>