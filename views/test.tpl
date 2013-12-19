<link rel="stylesheet" href="/resources/demos/style.css" />

<style>
*{margin:0;padding:0;}
a, a:hover{text-decoration: none;}
article, aside, figcaption, figure, footer, header, hgroup, nav, section {display:block;}

::selection{
    opacity:0;
    background:rgba(0,0,0,0);
}   
::-moz-selection{
    opacity:0;
    background:rgba(0,0,0,0);
}   
::-webkit-selection{
    opacity:0;
    background:rgba(0,0,0,0);
}

html{
}

body{
    background-color: #eef2ff;
    background-image: url("/public/some-pics/bgTop.jpg");
    background-repeat: repeat-x;
    font-size: 16px;
    line-height: 16px;
    overflow-x: hidden;
}
img{
    max-width: none;
}

#wrapper{
    overflow: auto;
    width: 1180px;
    margin: 0 auto;
    padding: 10px;
    background: #d6daf0;
    
    -moz-box-shadow:    0px 0px 60px 0px rgba(0,0,0,0.4);
    -webkit-box-shadow: 0px 0px 60px 0px rgba(0,0,0,0.4);
    box-shadow:         0px 0px 60px 0px rgba(0,0,0,0.4);
}

header{
    float: left;
    margin: 10px 0;
}
header h1{
    float: left;
    width: 271px;
}
header .title{
    position: relative;
    float: left;
    width: 909px;
    height: 145px;
}
header h2{
    text-align:center;
    color: #789922;
    text-transform: uppercase;
    font-family: "Century Gothic",arial,sans-serif;
    font-weight: bold;
    font-size: 2.3em;
    line-height: 1em;
}
header h3{
    text-align:center;
    color: #1f1f1f;
    font-family: "Lucida Sans Unicode",arial,sans-serif;
    font-size: 0.95em;
    line-height: 0.95em;
    margin: 0;
}

p{
    padding: 10px;
}

h1, h2{
    margin:0;
    padding:0;
}
#containerCategories h2, #limitsDrag h2{
    height: 52px;
}

#containerCategories {
    clear: both;
    float: left;
    width: 269px;
    background: #1f1f1f;
    margin-right: 10px;
}
    
    .category{
        display:block;
        padding:5px;
        border: 1px solid #789922;
        border-top: none;
    }
    
    .category h3{
        color: #789922;
        text-decoration: none;
        text-transform: uppercase;
        font-family: "Century Gothic",arial,sans-serif;
        font-weight: bold;
        font-size: 0.95em;
        line-height: 0.95em;
        padding: 0;
        margin: 0;
    }
    
    .category p{
        text-decoration: none;
        color: #cacaca;
        font-family: "Lucida Sans Unicode",arial,sans-serif;
        font-size: 0.8em;
        padding: 0;
        margin: 0;
    }
    
    #limitsDrag{
        float: left;
        overflow:hidden;
    }
    
    .active{
        background: url("/public/some-pics/memearrow.png") 255px center no-repeat;
    }
    
#containerNominees{
    float:left;
    width: 440px;
    background:#1f1f1f;
    padding: 0 10px 10px 0;
    border-right: 1px dotted #494949;
}

    .aNominee{
        position: relative;
        background: lightblue;
        border: 1px solid #789922;
        width:428px;
        height:100px;
        float:left;
        clear:both;
        margin: 10px 0 0 10px;
    }
    #containerVoteBoxes .aNominee{
        margin: 0;
    }
    .aNominee footer{
        position: absolute;
        top: 56px;
        left: 0;
        height: 44px;
        width: 428px;
        background: black;
        background: rgba(0,0,0,0.5);
    }
    .aNominee footer h3{
        color: white;
        text-decoration: none;
        text-transform: uppercase;
        font-family: "Century Gothic",arial,sans-serif;
        font-weight: bold;
        font-size: 1em;
        line-height: 1em;
        padding: 0;
        margin: 5px 0 0 5px;
    }
    .aNominee footer p{
        text-decoration: none;
        color: #cacaca;
        font-family: "Lucida Sans Unicode",arial,sans-serif;
        font-size: 0.8em;
        padding: 0;
        margin: 0 0 0 5px;
    }
    .aNominee img{
        width: 428px;
        height: 100px;
    }


#containerVoteBoxes{
    float:left;
    position: relative;
    width: 440px;
    background:#1f1f1f;
    padding: 0 10px 10px 0;
}
    .voteBox{
        clear:both;
        float:left;
        position: relative;
        background: url("/public/some-pics/bgVoteBox.jpg");
        width:430px;
        height:102px;
        margin: 10px 0 0 10px;
    }
    .voteBoxUsed{
        background: green;
    }
    #containerNominees .aNominee.locked {
    
  } 
    .ui-state-disabled, .ui-widget-content .ui-state-disabled, .ui-widget-header .ui-state-disabled {
        opacity: 1;
        filter: Alpha(Opacity=100);
    }
</style>

</head>
<body>

<div id="wrapper">
    <header>
        <h1><img src="/public/some-pics/logo.png" alt="/v/GA 2012 logo"></h1>
        
        <div class="title">
        
      <div><br><br></div>
            
            <h2>Image Testing</h2>
            <h3>Enter an image URL into this box to see what it will look like in a nomination rectangle<br /><br />
            <form method="GET" class="form-inline"><input type="text" name="image" /><button type="submit" class="btn">Go</button></form>
            </h3>

        </div>
        
    </header>

    <div id="containerCategories">
        <h2 id="topCategories">
            <img src="/public/some-pics/topCategories.jpg" alt="Categories">
        </h2>
        
        <a id="<tag:categories[].ID />" class="category <if:image>active</if:image>">
            <h3>Nomination Image Testing</h3>
            <p>For the testing of nomination images</p>
        </a>

    </div>
    
<if:image>
<div id="limitsDrag"> 
    <div id="containerNominees">
        <h2 id="topNominees">
            <img src="/public/some-pics/topNominees.jpg" alt="Categories">
        </h2>
        
        <div id="option1" class="aNominee">
            <img src="<tag:image />" alt="If you're seeing this, you didn't specify a valid image URL.">
            <footer>
                <div class="number"></div>
                <h3>Test Nominee</h3>
                <p>Additional information about the nominee goes here</p>
            </footer>
        </div>
        
    </div>
    
</div>
</if:image>

</div>
 
 
</body>
</html>