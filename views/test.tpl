  <style type="text/css"> 
    @font-face{
      font-family:"Overseer";
      src:url("/public/overseer.ttf");
    }

    @font-face{
      font-family:"Monofonto";
      src:url("/public/monofonto.ttf");
    }

    img {
        max-width: none;
    }

    #wrapper {
        font-size: 16px;
        line-height: 16px;

        overflow: auto;
        width: 1180px;
        margin: 0 auto;
        padding: 10px;
        background-color: rgb(211, 213, 176);
        background-image: url("/public/falloutbackground2.jpg");
        
        -moz-box-shadow:    0px 0px 5px 0px rgba(0,0,0,1);
        -webkit-box-shadow: 0px 0px 5px 0px rgba(0,0,0,1);
        box-shadow:         0px 0px 5px 0px rgba(0,0,0,1);
    }

    header {
        margin: 10px 0;
        width: 100%;
    }

    header .title {
        position: relative;
        width: 100%;
        height: 145px;
        padding-top: 0px;
        text-align: center;
    }

    header h2 {
        text-align:center;
        color: rgb(97,39,15);
        /*text-transform: uppercase;*/
        font-family: "Overseer",arial,sans-serif;
        font-size: 4em;
        font-weight: normal;
        line-height: 1em;
        margin-top: 20px;
        text-shadow: black 0px 0px 1px;
    }

    header h3 {
        text-align:center;
        color: #1f1f1f;
        font-family: "Monofonto",arial,sans-serif;
        font-size: 1.5em;
        line-height: 1.5em;
        margin: 0;
    }

    p {
        padding: 10px 0;
    }

    h1, h2 {
        margin:0;
        padding:0;
    }

    #containerCategories h2, #limitsDrag h2 {
        height: 52px;
    }

    #containerCategories {
        clear: both;
        float: left;
        width: 269px;
        background: #1f1f1f;
        margin-right: 10px;
    }
        
    .category {
      display:block;
      position:relative;
      padding:5px;
      border: 1px solid #789922;
      border-top: none;
    }

    .category:hover {
        background: #4d5c21;
    }

    .category h3 {
      color: #789922;
      text-decoration: none;
      text-transform: uppercase;
      font-family: "Century Gothic", arial, sans-serif;
      font-weight: bold;
      font-size: 0.95em;
      line-height: 0.95em;
      padding: 0;
      margin: 0;
    }

    .category p {
      text-decoration: none;
      color: #cacaca;
      font-family: "Lucida Sans Unicode",arial,sans-serif;
      font-size: 0.8em;
      padding: 0;
      margin: 0;
    }

    #limitsDrag {
      float: left;
      overflow:hidden;
      border: 10px solid black;
      border-radius: 20px;
    }

    #containerNominees {
      float:left;
      position: relative;
      width: 889px;
      background-color: #1f1f1f;
      background-image: url("/public/lines.fw.png");
      padding: 0 10px 10px 0;
    }

    .aNominee {
      position: relative;
      background: lightgreen;
      border: 1px solid #31E782;
      width: 876px;
      height: 100px;
      float: left;
      clear: both;
      margin: 10px 0 0 10px;
      
    }

    #containerVoteBoxes .aNominee{
      margin: 0;
    }

    .nomineeBasicInfo {
      position: absolute;
      right: 0;
      top: 0;
      width: 150px;
      height: 100%;
      padding: 4px;
      box-sizing: border-box;
      background: rgba(0,0,0,0.3);
      background: url("/public/lines2.png");
      border: 1px solid #31E782;
    }

    .voteBox .nomineeBasicInfo {
      background: url("/public/lines2.png");
    }

    .nomineeWords {
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 272px;
      padding: 2px 4px;
      overflow-y: hidden;
      box-sizing: border-box;
      background: rgba(0,0,0,0.3);
      color: #CACACA;
      font-size: 0.8em;
    }

    .nomineeBasicInfo h3 {
      color: white;
      text-decoration: none;
      text-transform: uppercase;
      font-family: "Monofonto", "Century Gothic",arial,sans-serif;
      font-weight: bold;
      font-size: 1.1em;
      line-height: 1em;
      padding: 0;
      margin: 5px 0 0 5px;
      text-shadow: black 1px 1px 2px;
    }

    .nomineeBasicInfo p {
      text-decoration: none;
      color: white;
      font-family: "Lucida Sans Unicode",arial,sans-serif;
      font-size: 0.8em;
      padding: 0;
      margin: 0 0 0 5px;
      text-shadow: black 1px 1px 2px;
    }

    .aNominee img{
      width: 100%;
      height: 100%;
    }
    </style>

</head>
<body>

<div id="wrapper">
    <header>
       
        <div class="title">
      <h2 style="font-size: 54px; margin-top: 0px;">Image Testing</h2>
      <h3 style="font-size: 20px;">Enter an image URL into this box to see what it will look like in a nomination rectangle<br>
      <form method="GET" class="form-inline"><input type="text" name="image" /><button type="submit" class="btn">Go</button></form></h3>
        </div>
        
    </header>
    
<if:image>
<div id="limitsDrag"> 
    <div id="containerNominees">

        <div class="aNominee">
            <div class="nomineeWords">
                  (Flavor text for the nominee goes here)<br>
                  The whole image should be 876x100.<br>
                  The primary visible part is 604x100.<br>
                  272 pixels on the left and 150 pixels on the right are partially covered.<br>
                  The box on the right can be dragged away
                </div>
                <img src="<tag:image />" alt="                                                                    If you're seeing this, you didn't specify a valid image URL.">
                <div class="nomineeBasicInfo"> 
                  <h3>Test Nominee</h3>
                  <p>Additional information</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$( ".aNominee .nomineeBasicInfo" ).draggable({
            distance: 20,
            opacity: 0.75,
            zIndex: 100,
            revert: "invalid",
            revertDuration: 200
        });
</script>
</if:image>