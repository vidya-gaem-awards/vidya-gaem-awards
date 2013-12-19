<style type="text/css">
@font-face {
  font-family: "Neon 80s";
  src: url("/public/Neon.ttf");
}

@font-face {
  font-family: "Blade Runner";
  src: url("/public/blade_runner.ttf");
}

a {
  color: fuchsia;
}

a:hover {
  color: purple;
}

body {
  background-color: black;
    background-image: url("/public/space.png");
    background-repeat: repeat;
    background-attachment: fixed;
    font-size: 16px;
    line-height: 16px;
    overflow-x: hidden;
    color: silver;
    color: white;
    font-family: "Neon 80s";
}

p {
  font-family: "Neon 80s";
  font-size: x-large;
  line-height: 1.3em;
}

.category {
    overflow: auto;
    margin: 0 auto;
    padding: 10px;
    /*background: #d6daf0;*/
    border: 5px solid aqua;
    border-radius: 5px;
    
    -moz-box-shadow:    0px 0px 20px 5px aqua;
    -webkit-box-shadow: 0px 0px 20px 5px aqua;
    box-shadow:         0px 0px 20px 5px aqua;
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

.winner {
  border: 3px solid yellow;
  max-width: 5000px;
  
  -moz-box-shadow:    0px 0px 10px 3px yellow;
    -webkit-box-shadow: 0px 0px 10px 3px yellow;
    box-shadow:         0px 0px 10px 3px yellow;
}

.category ul {
  list-style-type: none;
  margin: 0px;
  font-size: 24px;
}

.category ul li {
  line-height: 1.5em;
}

.category ul li:first-child {
  color: yellow;
  font-size: 36px;
}

.category ul li:last-child {
  color: silver;
  font-size: 18px;
}

.row h1 {
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

.category {
  margin-bottom: 15px;
}

.page-header h1 {
  color: fuchsia;
  font-family: "Blade Runner";
  font-size: 50px;
  text-transform: lowercase;
  text-shadow: 0px 0px 15px fuchsia;
}

.page-header {
  border-bottom: 3px solid fuchsia;
  text-align: center;
}
</style>

<div class="container">

  <div class="page-header">
    <h1>2012 Vidya Gaem Award Winners</h1>
  </div>
  
  <loop:categories>
  <div class="row">
    <div class="span12">
      
      <a id="<tag:categories[].ID />" href="#<tag:categories[].ID />" class="anchor"><h1><tag:categories[].Name /> <small><tag:categories[].Subtitle /></small></h1></a>
      
      <div class="category">
        <div class="row">
          <div class="span4">
            <img class="winner" src="/public/winners/<tag:categories[].ID />.png">
          </div>
          <div class="span7">
            <ul>
              <loop:categories[].Rankings>
              <li><tag:categories[].Rankings[] /></li>
              </loop:categories[].Rankings>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  </loop:categories>
  
  <div class="page-header">
    <h1>Detailed Results</h1>
  </div>
  
  <div class="row">
    <div class="span12">
      <p>The results shown above are the same as those shown during the show and are based only on the votes from /v/. For more details, including vote counts and votes that we filtered out, see the <a href="/voting/results">detailed results</a> and <a href="/voting/results/pairwise">pairwise comparison</a> pages.</p>
    </div>
  </div>
</div>