<style type="text/css">
@font-face {
  font-family: "Neon 80s";
  src: url("/public/Neon.ttf");
}

@font-face {
  font-family: "Blade Runner";
  src: url("/public/blade_runner.ttf");
}

body {
  background-color: black;
    background-image: url("/public/space.png");
    background-repeat: repeat;
    background-attachment: fixed;
    font-size: 16px;
    line-height: 16px;
    overflow-x: hidden;
    color: white;
    font-family: "Neon 80s";
}

.navbar {
  -moz-box-shadow:    0px 0px 20px 3px lime;
    -webkit-box-shadow: 0px 0px 20px 3px lime;
    box-shadow:         0px 0px 20px 3px lime;
}

.navbar .navbar-inner {
  background-image: none;
  background-color: black !important;
}

.navbar a {
  color: white !important;
}
.navbar a:hover {
  color: lime !important;
  text-shadow: 0px 0px 5px lime;
}

.navbar-fixed-top {
  border-bottom: 3px solid lime;
}

.navbar-fixed-bottom {
  border-top: 3px solid lime;
}

h2 {
  color: aqua;
  font-family: "Blade Runner";
  text-transform: lowercase;
  text-shadow: 0px 0px 15px aqua;
}

.row h1 small {
  color: white;
  font-size: 21px;
  text-shadow: none;
}

.page-header h1 {
  color: fuchsia;
  font-family: "Blade Runner";
  font-size: 46px;
  text-transform: lowercase;
  text-shadow: 0px 0px 15px fuchsia;
}

.page-header {
  border-bottom: 3px solid fuchsia;
  text-align: center;
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
}

.thumbnails {
  font-size: larger;
}



.thumbnail {
  min-height: 70px;
}



h2.cast {
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
}
</style>

<div class="page-header">
  <h1>2012 Vidya Gaem Award Contributors</h1>
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