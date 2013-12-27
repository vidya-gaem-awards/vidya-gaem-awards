<!DOCTYPE html>
<html lang="en">
  <head>
    <title>/v/GAs - Voting</title>    
    <link rel="stylesheet" href="/public/bootstrap-2.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/public/bootstrap-2.1.0/css/bootstrap-responsive.min.css">
    <link rel="stylesheet" href="/public/jquery/jquery-ui-1.9.2.min.css">
    <link rel="stylesheet" href="/public/style.css">
    
    <script src='/public/jquery/jquery-1.8.2.min.js'></script>
    <script src='/public/jquery/jquery-ui-1.9.2.min.js'></script>
    <script src='/public/jquery/jquery.tablesorter.min.js'></script>
    <script src='/public/bootstrap-2.1.0/js/bootstrap.min.js'></script>
    <script src="http://www.modernizr.com/downloads/modernizr-2.0.6.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8" />
    
    <script type="text/javascript">

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-36466872-1']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>    

    <style type="text/css">
    * {
      margin: 0;
      padding: 0;
    }

    @font-face{
      font-family:"Overseer";
      src:url("/public/overseer.ttf");
    }

    @font-face{
      font-family:"Monofonoto";
      src:url("/public/monofonoto.ttf");
    }

    a, a:hover {
      text-decoration: none;
    }

    article, aside, figcaption, figure, footer, header, hgroup, nav, section {
      display: block;
    }

    /*::selection {
        opacity: 0;
        background: rgba(0,0,0,0);
    }   

    ::-moz-selection {
        opacity: 0;
        background: rgba(0,0,0,0);
    }   

    ::-webkit-selection {
        opacity: 0;
        background: rgba(0,0,0,0);
    }*/

    body {
        background-color: rgb(88, 82, 66);
        background-image: url("/public/falloutbackground.jpg");
        background-repeat: repeat;
        font-size: 16px;
        line-height: 16px;
        overflow-x: hidden;
    }

    img {
        max-width: none;
    }

    #wrapper {
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
        float: left;
        margin: 10px 0;
    }

    header h1 {
        float: left;
        width: 271px;
    }

    header .title {
        position: relative;
        float: left;
        width: 909px;
        height: 145px;
        padding-top: 0px;
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
        line-height: 0.95em;
        margin: 0;
    }

    p {
        padding: 10px;
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
    }

    .active{
        background: url("/public/some-pics/memearrow.png") 255px center no-repeat, url("/public/some-pics/bgCategory.jpg") left top repeat-y;
    }
    .active:hover{
        background: #4d5c21 url("/public/some-pics/memearrow.png") 255px center no-repeat;
    }

    .complete:before{
        content: "";
        display: block;
        position: absolute;
        top: -1px;
        left: -11px;
        width: 9px;
        height: 100%;
        border: 1px solid #789922;
        background: url("/public/some-pics/complete.png") 1px center no-repeat, url("/public/some-pics/bgComplete.gif") left top repeat-y;
    }

    #containerNominees {
      float:left;
      width: 440px;
      background: #1f1f1f;
      padding: 0 10px 10px 0;
      border-right: 1px dotted #494949;
    }

    .aNominee {
      position: relative;
      background: lightblue;
      border: 1px solid #789922;
      width: 428px;
      height: 100px;
      float: left;
      clear: both;
      margin: 10px 0 0 10px;
      <if:votingEnabled>
      cursor: move;
      </if:votingEnabled>
    }

    #containerVoteBoxes .aNominee{
      margin: 0;
    }

    .aNominee.locked {
      border-color: black !important;
    }

    .aNominee footer {
      position: absolute;
      top: 56px;
      left: 0;
      height: 44px;
      width: 428px;
      background:   black;
      background: rgba(0,0,0,0.7);
    }

    .aNominee footer h3 {
      color: white;
      text-decoration: none;
      text-transform: uppercase;
      font-family: "Century Gothic",arial,sans-serif;
      font-weight: bold;
      font-size: 1.1em;
      line-height: 1em;
      padding: 0;
      margin: 5px 0 0 5px;
    }

    .aNominee footer p {
      text-decoration: none;
      color: #cacaca;
      font-family: "Lucida Sans Unicode",arial,sans-serif;
      font-size: 0.8em;
      padding: 0;
      margin: 0 0 0 5px;
    }

    /*.aNominee footer a{
      color: #789922;
    }

    .aNominee footer a:hover{
      color: green;
    }*/

    .aNominee img{
      width: 428px;
      height: 100px;
    }

    #containerVoteBoxes {
      float:left;
      position: relative;
      width: 440px;
      background: #1f1f1f;
      padding: 0 10px 10px 0;
    }

    #howToVote {
      cursor: pointer;
      position: absolute;
      top: 23px;
      right: 11px;
      color: #1f1f1f;
      text-decoration: none;
      text-transform: uppercase;
      font-family: "Century Gothic",arial,sans-serif;
      font-weight: bold;
      font-size: 1em;
      line-height: 1em;
      padding: 0;
      margin: 0;
    }

    #howToVote:hover {
      text-decoration: underline;
    }

    .voteBox {
      clear:both;
      float:left;
      position: relative;
      background: url("/public/some-pics/bgVoteBox.jpg");
      width: 430px;
      height: 102px;
      margin: 10px 0 0 10px;
    }

    .voteBoxUsed {
      background: green;
    }

    .number {
      display: none;
      position: absolute;
      top: 5px;
      right: 10px;
      padding: 0;
      margin: 0;
      
      color: white;
      text-decoration: none;
      text-transform: uppercase;
      font-family: "Century Gothic",arial,sans-serif;
      font-weight: bold;
      font-size: 2em;
      line-height: 1em;
    }

    .voteBox .number {
      display: block;
    }

    .ui-state-disabled, .ui-widget-content .ui-state-disabled, .ui-widget-header .ui-state-disabled {
      opacity: 1;
      filter: Alpha(Opacity=100);
    }

    .btnSubmit {
      cursor: pointer;
      margin-top: 10px;
      float:left;
      height: 53px;
    }
        
    #btnLockVotes {
      clear: both;
      margin-left: 9px;
      width: 430px;
      background-image: url("/public/some-pics/btnSubmit.gif");
      background-position: 0px 0px;
    }

    #btnResetVotes {
      margin-left: 9px;
      width: 215px;
      background-image: url("/public/some-pics/btnSubmit.gif");
      background-position: 0px -159px;
    }

    #btnCancelVotes {
      width: 215px;
      background-image: url("/public/some-pics/btnSubmit.gif");
      background-position: 215px -159px;
    }

    #btnLockVotes:hover {
      background-position: 0px -53px
    }

    #btnResetVotes:hover {
      background-position: 0px -212px;
    }

    #btnCancelVotes:hover {
      background-position: 215px -212px;
    }

    .iVoted {
      background-position: 0px -106px !important;
      cursor: default;
    }

    #overlay {
      cursor: pointer;
      width: 650px;
      height: 471px;
      -moz-box-shadow:    0px 0px 60px 0px rgba(0,0,0,0.4);
      -webkit-box-shadow: 0px 0px 60px 0px rgba(0,0,0,0.4);
      box-shadow:         0px 0px 60px 0px rgba(0,0,0,0.4);
    }

    .shit {
      position:absolute;
      z-index:999;
      left:-100px;
      top:82px;
      display:none;
      animation: dumb 8s infinite linear;
      -moz-animation: dumb 8s infinite linear; /* Firefox */
      -webkit-animation: dumb 8s infinite linear; /* Safari and Chrome */
      -o-animation: dumb 8s infinite linear; /* Opera */
    }
        
    @keyframes dumb
    {
    from {left:-10%;}
    to {left:110%;}
    }

    @-moz-keyframes dumb /* Firefox */
    {
    from {left:-10%;}
    to {left:110%;}
    }

    @-webkit-keyframes dumb /* Safari and Chrome */
    {
    from {left:-10%;}
    to {left:110%;}
    }

    @-o-keyframes dumb /* Opera */
    {
    from {left:-10%;}
    to {left:110%;}
    }

    #test {
      color: red;
      text-align: center;
    }

    #startMessage {
      float: left;
      width: 880px;
    }

    #startMessage h2 {
      margin-top: 20px;
    }

    #startMessage h2:first-child {
      margin-top: 0px;
    }

    #startMessage p {
      padding-top: 0px;
      line-height: 1.3;
    }
    </style>

    <!--[if IE]>
        <style type="text/css">
            .aNominee footer {
            background:transparent;
            filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#BB000000,endColorstr=#BB000000);
            zoom: 1;
            }
        </style>
    <![endif]-->

    <script type="text/javascript" src="/public/dumbshit.js"></script>
    <script type="text/javascript">
        dumbshit = new Dumbshit()
        dumbshit.code = function() {
            $(".shit").show();
            $("body").css("background-image","url(/public/stars.gif)");
            $("body").css("background-repeat","repeat");
        }
        dumbshit.load()
    </script>
  
    <if:category>
    <script>
    //position the popup at the center of the page
    function positionPopup(){
        if(!$("#overlay").is(':visible')){
            return;
        }
        $("#overlay").css({
            left: ($(window).width() - $('#overlay').width()) / 2,
            top: ($(window).width() - $('#overlay').width()) / 7,
            position:'absolute'
        });
    }
    //maintain the popup at center of the page when browser resized
    $(window).bind('resize',positionPopup);
        
    var lastVotes = <tag:lastVotes />;
    var votesChanged = false;
    var previousLockExists = lastVotes.length > 1;
        
    $(document).ready(function() {  
        randomizeNominees();
        $("#overlay").fadeOut(0);
        $("#overlay").css({
            left: ($(window).width() - $('#overlay').width()) / 2,
            top: ($(window).width() - $('#overlay').width()) / 7,
            position:'absolute'
        });
        
        //open popup
        $("#howToVote").click(function(){
            $("#overlay").fadeIn(300);
            positionPopup();
        });
         
        //close popup
        $("#closeOverlay").click(function(){
            $("#overlay").fadeOut(300);
        });
        
        //empty voteBoxes
        $( ".voteBox" ).each(function(){
            $(this).html("");
        });
            
        //global variables for future use
        var dragged;
        var draggedFrom;
        
        //set the height/width of the containerNominees depending on how many nominees there are
        $("#containerNominees").height($("#containerVoteBoxes").height());
                
        //be able to drag nominees
        <if:votingEnabled>
        $( ".aNominee" ).draggable({
            containment: "#limitsDrag",
            distance: 20,
            opacity: 0.75,
            zIndex: 100,
            revert: "invalid",
            revertDuration: 200
        })
        
        //when you start dragging, it puts the elements in variables
        .bind('dragstart',function( event ){
            //console.log($(this).parent().attr("id"))
            dragged = $(this);
            draggedFrom = $(this).parent();
            
            //put their margins to 0
            //$(this).css("margin","0px 0px 0px 0px");
        })
        //</if:votingEnabled>
        
        //be able to drop nominees in voteBoxes
        $( ".voteBox" ).droppable({
            drop: function( event, ui ) {
                $( this )
                    var dropped = ui.draggable;
                    var droppedOn = $(this);
                    
                    //if you're dropping the nominee exactly where you took it from, it cancels the drop
                    //console.log(droppedOn.attr("id"))
                    if(droppedOn.attr("id") == draggedFrom.attr("id")){
                        $(dragged).draggable( "option", "revert", true );
                        return
                    }
                    
                    votesWereUnlocked();
                    
                    //put the content of the box you're voting over in a variable (.detach keeps the draggable)
                    var stuffDeleted = droppedOn.contents().detach();
                    
                    //add your dragged vote to the box
                    $(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);
                    
                    //put what you deleted back where your vote came from
                    draggedFrom.append(stuffDeleted);
                    
                    //put their margins back to normal
                    $(stuffDeleted).css("margin","10px 0 0 10px");
                    
                    updateNumbers();
            }
        })
        
        //be able to drop nominees back in the original container
        $( "#containerNominees" ).droppable({
            drop: function( event, ui ) {         
                $( this )
                    var dropped = ui.draggable;
                    var droppedOn = $(this);
                    
                    //if you're dropping the nominee exactly where you took it from, it cancels the drop
                    //console.log(droppedOn.attr("id"))
                    if(droppedOn.attr("id") == draggedFrom.attr("id")){
                        $(dragged).draggable( "option", "revert", true );
                        
                        //put their margins back to normal
                        $(dropped).css("margin","10px 0 0 10px");
                        
                        return
                    }
                    
                    votesWereUnlocked();
                    
                    //add your dragged vote to the container
                    $(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);
                    
                    //put their margins back to normal
                    $(dropped).css("margin","10px 0 0 10px");
                    
                    //empty the number
                    //dropped.find(".number").html("");
                    
            }
        })
        
        //if you click on Reset Votes
        $('#btnResetVotes').click(function(){
          votesWereUnlocked();
            $( ".voteBox" ).each(function(){
                //delete what's in every voteBox and put them back in the container on the left
                var stuffDeleted = $(this).contents().detach();
                $('#containerNominees').append(stuffDeleted);
                //put their margins back to normal
                $(stuffDeleted).css("margin","10px 0 0 10px");
            });
            sortLeftSide();
            if (!previousLockExists) {
          $("#btnCancelVotes").hide();
        }
        });
        
        $('#btnCancelVotes').click(function() {
        moveNomineesBackToLastVotes();
        sortLeftSide();
        });
        
        //if you click on Lock Votes
        $('#btnLockVotes').click(function(){
            /*
            var numberOfVotes = 0;
            var numberOfBoxes = $( ".voteBox" ).length;
            
            //goes through every voteBox
            $( ".voteBox" ).each(function(){
                if($(this).contents().attr("id") != undefined){
                    //if the ID of what's in the voteBox is undefined, it means there's nothing
                    //so knowing that, every time a voteBox isn't empty, add 1 to numberOfVotes
                    numberOfVotes++;
                }
            });
            
            console.log( numberOfVotes +"/"+ numberOfBoxes);
            */
            
            sortVotes();
            updateNumbers();
            
            votesWereLocked();
            
            var preferences = [null];
            
            $( ".voteBox" ).each(function(){
          var onlyTheNumber = $(this).attr("id").replace(/[^0-9]/g, '');
          var nomineeID = $(this).find(".aNominee").attr("data-nominee");
       
          if (nomineeID != undefined) {
            preferences[onlyTheNumber] = nomineeID;
          }
          
        });
        
        console.log(preferences);
        
        lastVotes = preferences;
        
            $.post("/voting-submission", { Category: "<tag:category.ID />", Preferences: preferences }, function(data) {
            console.log(data);
            });
            
        });
    });

    function sortVotes() {
        //variable that I'm using to know which voteBox the loop is at
        var currentVoteBox = 0;
        
        //array
        var listVoteBox = [];
        
        //pass through every voteBox, empty them while placing the vote in the array, ignoring the empty voteBoxes
        $( ".voteBox" ).each(function(){
            currentVoteBox++;
            //alert("currently checking voteBox"+currentVoteBox);
            
            //alert($(this).contents().attr("id"));
            if($(this).contents().attr("id") != undefined){
                listVoteBox.push($(this).contents().detach());
            }
            
            //alert(listVoteBox);
        });
        
        //put the votes back in the voteBoxes
        for(var i=0;i<currentVoteBox;i++){
            //alert("__"+ $( "#voteBox"+(i+1) ).html() +"__");
            //alert(listVoteBox[i]);
            
            if(listVoteBox[i]){ //if it exists
            
                //$( "#voteBox"+(i+1) ).append(listVoteBox[i]);
                listVoteBox[i].appendTo($( "#voteBox"+(i+1) ));
            }
        }
    }

    function updateNumbers() {
        //for every voteBox, look at its ID, keep the number and show it in the nominee div
        $( ".voteBox" ).each(function(){
            var onlyTheNumber = $(this).attr("id").replace(/[^0-9]/g, '');
            $(this).find(".number").html("#"+onlyTheNumber);
            
            //put their margins back to normal
            $(this).find(".aNominee").css("margin","0 0 0 0");
        });
    };

    function votesWereLocked() {
      $( ".aNominee" ).addClass("locked");
      $( "#votesAreLocked" ).show();
      $( "#votesAreNotLocked" ).hide();
      $( "#btnCancelVotes").hide();
      previousLockExists = true;
      votesChanged = false;
    }

    function votesWereUnlocked() {
      $( ".aNominee" ).removeClass("locked");
      $( "#votesAreLocked" ).hide();
      $( "#votesAreNotLocked" ).show();
      $( "#btnCancelVotes").show();
      votesChanged = true;
    };

    function moveNomineesBackToLastVotes() {
      var haveVotedFor = [];

      for (var i = 1; i < lastVotes.length; i++) {
        haveVotedFor.push($("#nominee-"+lastVotes[i]).detach());
      }
      
      var theRest = $(".aNominee").detach();
      
      for (var i = 0; i < lastVotes.length; i++) {
        $("#voteBox"+(i+1)).append(haveVotedFor[i]);
      }
      
      $("#containerNominees").append(theRest);
      $(theRest).css("margin","10px 0 0 10px");
      
      updateNumbers();
      
      if (previousLockExists) {
        votesWereLocked();
      }
      
      $("#btnCancelVotes").hide();
    }

    function sortLeftSide() {
      var muhNominees = $("#containerNominees .aNominee").detach();
      
      muhNominees = $(muhNominees).sort(function (a, b) {
        var contentA = parseInt( $(a).attr('data-order'));
        var contentB = parseInt( $(b).attr('data-order'));
        return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
     });
     
     $("#containerNominees").append(muhNominees);
    }

    $(document).ready(function() { 
      moveNomineesBackToLastVotes();
    });

    function randomizeNominees(){
        var currentNominee = 0;
            
        //array
        var arrayOfNominees = [];

        //pass through every nominee, remove them while placing the vote in the array
        $( ".aNominee" ).each(function(){
            currentNominee++;
            
            arrayOfNominees.push($(this).detach());
            
        });
        
        //randomize the array
        random(arrayOfNominees);
        
        //put the nominees back
        for(var i=0;i<currentNominee;i++){
            
            if(arrayOfNominees[i]){ //if it exists
                arrayOfNominees[i].appendTo($( "#containerNominees" ));
            }
        }
    }

    function random ( myArray ) {
        var i = myArray.length;
        if ( i == 0 ) return false;
        while ( --i ) {
            var j = Math.floor( Math.random() * ( i + 1 ) );
            var tempi = myArray[i];
            var tempj = myArray[j];
            myArray[i] = tempj;
            myArray[j] = tempi;
        }
    }
      
    </script>
    </if:category>
  </head>
  <body>

<div id="wrapper">
    <header>
        <h1><a href="/voting"><img src="/public/some-pics/logo.png" alt="/v/GA 2012 logo"></a></h1>
        
        <div class="title">
      <if:category>
      <h2><tag:category.Name /></h2>
      <h3><tag:category.Subtitle /></h3>
      <if:votingNotYetOpen>
      <h2 style="margin-top: 5px; color: red;"><tag:voteText />.</h2>
      </if:votingNotYetOpen>
      <else:category>
      <h2 style="font-size: 54px; margin-top: 30px;">The 2012 Vidya Gaem Awards</h2>
      <h3 style="font-size: 25px;"><tag:voteText /></h3>
      </if:category>
        </div>
        
    </header>

    <div id="containerCategories">
        <h2 id="topCategories">
            <img src="/public/some-pics/topCategories.jpg" alt="Categories">
        </h2>
        
        <loop:categories>
        <a href="/voting/<tag:categories[].ID />" id="<tag:categories[].ID />" class="category <if:categories[].Active>active</if:categories[].Active> <if:categories[].Completed>complete</if:categories[].Completed>">
            <h3><tag:categories[].Name /></h3>
            <p><tag:categories[].Subtitle /></p>
        </a>
        </loop:categories>
    </div>
    
<if:category>
<div id="limitsDrag"> 
    <div id="containerNominees">
        <h2 id="topNominees" data-order="-1">
            <img src="/public/some-pics/topNominees.jpg" alt="Categories">
        </h2>
        
        <loop:nominees>
        <div id="nominee-<tag:nominees[].NomineeID />" class="aNominee" data-order="<tag:nominees[].Order />" data-nominee="<tag:nominees[].NomineeID />">
            <img src="<tag:nominees[].Image />">
            <footer>
                <div class="number"></div>
                <h3><tag:nominees[].Name /></h3>
                <p><tag:nominees[].Subtitle /></p>
            </footer>
        </div>
        </loop:nominees>
        
    </div>
    
    <!if:votingNotYetOpen>
    <div id="containerVoteBoxes">
        <h2 id="topVotes">
            <img src="/public/some-pics/topVotes.jpg" alt="Categories">
        </h2>
        
        <if:votingEnabled><a id="howToVote">How to vote</a></if:votingEnabled>
        
        <loop:dumbloop>
        <div id="voteBox<tag:dumbloop[] />" class="voteBox">
        </div>
        </loop:dumbloop>
        
        <if:votingEnabled>
        <footer>
            <span id="votesAreNotLocked">
                <div id="btnLockVotes" class="btnSubmit" alt="Submit Votes"></div>
            </span>
            <span id="votesAreLocked" style="display: none;">
                <div id="btnLockVotes" class="btnSubmit iVoted" alt="Submit Votes"></div>
            </span>
            <div id="btnResetVotes" class="btnSubmit" alt="Reset Votes"></div>
            <div id="btnCancelVotes" class="btnSubmit" alt="Cancel Votes" style="display: none;"></div>
        </footer>
        </if:votingEnabled>
    </div>
    </!if:votingNotYetOpen>

</div>

<div id="overlay" title="Click to close"><img src="/public/some-pics/howToVote.jpg" id="closeOverlay" title="Mommy how do I vote?"></div>
<else:category>
<div id="startMessage">
  <if:votingEnabled>
  <h2>This year, you don't need to sign in to vote.</h2>
  <p>In fact, you don't need to do anything. Just show up and vote. <strong>Select a category on the left to begin.</strong></p>
  <h2>The voting system has changed.</h2>
  <p>Instead of just voting for one nominee, you can vote for many, and put them in the order you'd like to see them win.</p>
  <p>Too much effort for you? Vote for one nominee (just like last year) and call it a day.</p>
  <p>Still confused? We've prepared this handy <a href="/public/some-pics/howToVote.jpg">voting guide</a> for you.<br><strong>tl;dr:</strong> drag the thing you want to win from the left to the right, then click the Submit Votes button.</p>
  <h2>Links to other things:</h2>
  <p>Looking for the main website? <a href="http://vidyagaemawards.com">It's right here</a>.</p>
  <p>It's not too late to submit a video and go into the draw for $20 of Steam games. Check our <a href="/videos">video</a> page for more information.</p>
  <p>Want to tell us how fucking retarded we are for going ahead with the show this year? Let us know <a href="http://steamcommunity.com/groups/vidyagaemawards/discussions/">here</a> or <a href="mailto:vidya@vidyagaemawards.com">email us</a>.</p>
  <else:votingEnabled>
  <h2>Thanks to everybody who voted.</h2>
  <p>No new votes can be made, but if you've already voted you can still see the votes you made.</p>
  <p>It'll take us a few days to determine the final winners, and a few weeks before the stream will be ready. We'll announce the stream date once it's been confirmed.</p>
  </if:votingEnabled>
</div>
</if:category>

<img src="/public/dumb.gif" alt="" class="shit">
</div>
 
 
</body>
</html>