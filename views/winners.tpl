<style type="text/css">
@font-face {
  font-family: "Bebas Neue";
  src: url('/fonts/BebasNeue.eot');
  src: url('/fonts/BebasNeue.eot?#iefix') format('embedded-opentype'),
  url("/fonts/BebasNeue.woff") format("woff"),
  url("/fonts/BebasNeue.ttf") format("truetype");
url("/fonts/BebasNeue.svg#svgBebasNeue") format("svg"),
}

body {
  background: url("/2014voting/bg_tile.png") #212121 repeat;
  font-family: Calibri, Arial, sans-serif;
  color: white;
  overflow-x: hidden;
}

a, a:hover {
  text-decoration: none;
  color: #f2ff1a;
  padding: 5px;
}
a:hover {
  background: rgba(0,0,0,0.20)
}

.row a:hover, a h1:hover {
  background: rgba(0, 0, 0, 0.1);
  text-decoration: none;
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
    border: 2px dashed yellow;
    font-family: "Bebas Neue", Tahoma, sans-serif;
    
    -moz-box-shadow:    0px 0px 30px 5px rgba(0,0,0,0.2);
    -webkit-box-shadow: 0px 0px 30px 5px rgba(0,0,0,0.2);
    box-shadow:         0px 0px 30px 5px rgba(0,0,0,0.2);

    /*background: rgba(246, 231, 190, 0.5) url("/2014voting/votebox_background.png");*/
}

.navbar, .navbar .navbar-inner {
  -moz-box-shadow:    none;
  -webkit-box-shadow: none;
  box-shadow:         none;
  border:             none;
  background-image: url("/2014voting/bg_tile.png");
  background-color: #212121;
}

.navbar a {
  color: white !important;
  text-shadow: none !important;
  background: none !important;
}
.navbar a:hover {
  background: rgba(0, 0, 0, 0.1) !important;
}

.navbar .nav>.active>a {
  box-shadow: inset 0 3px 15px rgba(255, 255, 255, 0.125);
  -webkit-box-shadow: inset 0 3px 15px rgba(255, 255, 255, 0.125);
  -moz-box-shadow: inset 0 3px 15px rgba(255, 255, 255, 0.125);
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
  border: 1px solid yellow;
  background: rgba(20, 20, 20, 0.5);
  padding: 10px;
  width: 365px;
  max-width: 5000px;
/*  margin: 10px 0 0 10px;*/
  
  /*transform: rotate(3deg);
  -webkit-transform: rotate(3deg);
  -ms-transform: rotate(3deg);
  -o-transform: rotate(3deg);*/
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
  font-family: "Bebas Neue", Tahoma, sans-serif;
}

.category ul li:last-child {
  color: grey;
  font-size: 18px;
  font-family: "Bebas Neue", Tahoma, sans-serif;
}

.row h1 {
  color: #f2ff1a;
  font-family: "Bebas Neue", Tahoma, sans-serif;
}

.row h1 small {
  color: white;
  font-size: 21px;
}

.category {
  margin-bottom: 15px;
}

.page-header h1 {
  font-family: "Bebas Neue", Tahoma, sans-serif;
  color: #f2ff1a;;
  font-size: 50px;
}

.page-header {
  text-align: center;
  border: none;
  background: transparent url("/2014voting/shadow_top.png") bottom center no-repeat;
  margin-bottom: 0px;
}

.page-header.the-bottom {
  background: url("/2014voting/shadow_bot.png") center 14px no-repeat;
  padding-top: 20px;
  margin-bottom: 20px;
}
</style>

<div class="container">

  <div class="page-header">
    <h1>Winners of the 2014 Vidya Gaem Awards</h1>
  </div>
  
  <loop:categories>
  <div class="row">
    <div class="span12">
      
      <a id="<tag:categories[].ID />" href="#<tag:categories[].ID />" class="anchor"><h1><tag:categories[].Name /> <small><tag:categories[].Subtitle /></small></h1></a>
      
      <div class="category">
        <div class="row">
          <div class="span4">
            <img class="winner" src="/winners/01189998819991197253/<tag:categories[].ID />.png">
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
  </div>
  
  <div class="row">
    <div class="span12">
      <p>The results shown above are the same as those shown during the show and are based only on the votes from /v/. For more details, including vote counts and votes that we filtered out, see the <a href="/voting/results">detailed results</a> and <a href="/voting/results/pairwise">pairwise comparison</a> pages.</p>
    </div>
  </div>
</div>
