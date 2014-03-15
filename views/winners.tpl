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

a:hover {
  color: #509e20;
}

.row a:hover, a h1:hover {
  background: rgba(0, 0, 0, 0.1);
  text-decoration: none;
}

body {
    background-image: url("/public/50s/bgvoting.jpg"); 
    background-color: #f6e7be;
    overflow-x: hidden;
}

body, p { 
    font-family: Calibri, Arial, sans-serif !important;
}

p {
  font-size: x-large;
  line-height: 1.3em;
}

.category {
    overflow: auto;
    margin: 0 auto;
    padding: 10px;
    /*background: #d6daf0;*/
    border: 5px dashed black;
    font-family: "Brush Script MT";
    
    -moz-box-shadow:    0px 0px 30px 5px rgba(0,0,0,0.2);
    -webkit-box-shadow: 0px 0px 30px 5px rgba(0,0,0,0.2);
    box-shadow:         0px 0px 30px 5px rgba(0,0,0,0.2);
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

.winner {
  border: 1px solid rgba(0, 0, 0, 0.5);
  background: white;
  padding: 15px;
  max-width: 5000px;
  margin: 10px 0 0 10px;
  
  transform: rotate(3deg);
  -webkit-transform: rotate(3deg);
  -ms-transform: rotate(3deg);
  -o-transform: rotate(3deg);
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
  font-weight: bold;
  font-size: 36px;
  font-family: "Brush Script MT";
}

.category ul li:last-child {
  color: grey;
  font-size: 18px;
  font-family: "ArtBrush";
}

.row h1 {
  color: #509e20;
  font-family: "ArtBrush";
}

.row h1 small {
  color: black;
  font-size: 21px;
}

.category {
  margin-bottom: 15px;
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

.hr {
	margin: 0 auto;
	height: 8px;
	background: url("/public/50s/underline.png") center center no-repeat;
}
</style>

<div class="container">

  <div class="page-header">
    <h1>Winners of the 2013 Vidya Gaem Awards</h1>
    <div class="hr"></div>
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
  
  <div class="page-header the-bottom">
    <h1>Detailed Results</h1>
    <div class="hr"></div>
  </div>
  
  <div class="row">
    <div class="span12">
      <p>The results shown above are the same as those shown during the show and are based only on the votes from /v/. For more details, including vote counts and votes that we filtered out, see the <a href="/voting/results">detailed results</a> and <a href="/voting/results/pairwise">pairwise comparison</a> pages.</p>
    </div>
  </div>
</div>