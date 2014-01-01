<!DOCTYPE html>
<html lang="en">
  <head>
    <title>/v/GAs - Voting</title>

    <link rel="stylesheet" href="/public/bootstrap-2.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/public/jquery/jquery-ui-1.9.2.min.css">
    <link rel="stylesheet" href="/public/style.css">
    <link rel="stylesheet" href="/public/voting.css">
    
    <script src='/public/jquery/jquery-1.8.2.min.js'></script>
    <script src='/public/jquery/jquery-ui-1.9.2.min.js'></script>
    <script src='/public/bootstrap-2.1.0/js/bootstrap.min.js'></script>
    <script src='/public/dumbshit.js'></script>
    <script src='/public/voting.js'></script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8" />
    
    <script type="text/javascript">
      <if:votingEnabled>
      var votingEnabled = true;
      <else:votingEnabled>
      var votingEnabled = false;
      </if:votingEnabled>

      <if:category>
      var lastVotes = <tag:lastVotes />;
      var votesChanged = false;
      var previousLockExists = lastVotes.length > 1;
      var currentCategory = "<tag:category.ID />";
      </if:category>
    </script>

    <if:votingEnabled>
    <style type="text/css">
      .aNominee {
        cursor: move;
      }
    </style>
    </if:votingEnabled>

</head>
<body>


<header>
	<a href="/voting"><img src="/public/50s/why_are_you_reading_this.png" id="thelogo" alt="oh god it's happening again" /></a>
	<h1>The 2013 Vidya Gaem Awards</h1>
	<h2><tag:voteText /></h2>
</header>

<div id="wrapper">      
  <if:category>
  <div class="awardHeader">
    <a href="/voting/<tag:prevCategory />" class="navigation left"></a>
    <div class="awardHeaderContainer">
      <div class="awardName"><tag:category.Name /></div>
      <div class="hr"></div>
      <h2 class="awardSubtitle"><tag:category.Subtitle /></h2>
    </div>
    <a href="/voting/<tag:nextCategory />" class="navigation right"></a>
  </div>
  <if:votingEnabled>
  <img src="/public/50s/dont_forget.png" id="dontforget" alt="Don't forget to hit submit" />
  </if:votingEnabled>
  </if:category>
    
<if:category>
<div id="limitsDrag"> 
    <div id="nomineeColumn" class="column">
        
        <img src="/public/50s/topNominees.png" width="267px" height="105px" alt="Pick your nominees" />

        <loop:nominees>

        <div class="voteBox"><div id="nominee-<tag:nominees[].NomineeID />" class="aNominee" data-order="<tag:nominees[].Order />" data-nominee="<tag:nominees[].NomineeID />">
              <img class="fakeBorder" src="/public/50s/votebox_foreground.png">
              <img class="fakeBorder locked" src="/public/50s/votebox_foreground_locked.png">
              <img class="nomineeImage" src="<tag:nominees[].Image />">
              <div class="nomineeInfo">
                  <div class="number"></div>
                  <div class="nomineeName"><tag:nominees[].Name /></div>
                  <div class="nomineeSubtitle"><tag:nominees[].Subtitle /></div>
              </div>
          </div></div>

        </loop:nominees>

    </div>

    <div id="spacerColumn" class="column">
      &nbsp;
    </div>
    
    <!if:votingNotYetOpen>
    <div id="voteColumn" class="column">

        <img src="/public/50s/topVotes.png" width="267px" height="105px" alt="Drag and drop to vote"/>
        
        <loop:dumbloop>
        <div id="voteBox<tag:dumbloop[] />" class="voteBox">
        </div>
        </loop:dumbloop>
        
       
    </div>
    </!if:votingNotYetOpen>

</div>

 <if:votingEnabled>
	<footer>
		<img src="/public/50s/arrow_left.png" id="arrow_left" /><div id="btnResetVotes" class="btnSubmit" alt="Reset Votes"></div>
		<span id="votesAreNotLocked">
			<div id="btnLockVotes" class="btnSubmit" alt="Submit Votes"></div><img src="/public/50s/arrow_right.png" id="arrow_right" />
		</span>
		<span id="votesAreLocked" style="display: none;">
			<div id="btnLockVotes" class="btnSubmit iVoted" alt="Saved!"></div>
		</span>
	</footer>
</if:votingEnabled>

<else:category>
<div id="startMessage">

  <if:votingNotYetOpen>
  <!-- Before votes open -->
  <h2>How to vote:</h2>
  <p>Despite the new look, voting is still the same. Vote for as many nominees as you want, and put them in the order you'd like to see them win. Too much effort for you? Vote for one nominee and call it a day.</p>
  <p>Voting isn't open yet, but you can still browse the awards and have a look at the nominees. You can use the list of awards at the bottom and the meme arrows at the top to navigate.</p>
  </if:votingNotYetOpen>

  <if:votingEnabled>
  <!-- While votes are open -->
  <h2>How to vote:</h2>
  <p>Despite the new look, voting is still the same. Vote for as many nominees as you want, and put them in the order you'd like to see them win. Too much effort for you? Vote for one nominee and call it a day.</p>
  <p>You can use the award list at the bottom to navigate, as well the meme arrows that will appear while looking at an award.</p>
  <p><a href="/voting/most-hated-game">Click here to begin!</a></p>
  </if:votingEnabled>

  <if:votingConcluded>
  <!-- After votes close -->
  <h2>Thanks to everybody who voted.</h2>
  <p>No new votes can be made, but if you've already voted you can still see the votes you made.</p>
  </if:votingConcluded>

  <h2>Stream information:</h2>
  <p>We plan to stream at roughly the same time as last year (early March). If you'd like to submit a video for the show, see the <a href="/videos">videos</a> page for more information. We plan on having more vidya analysis instead of funny (or not-so-funny) skits this time, so keep that in mind.</p>

</div>
</if:category>

<img src="/public/dumb.gif" alt="" class="shit">
</div>

<div id="containerCategories">
    <h2 id="topCategories">
        Categories
    </h2>
    
    <loop:categories>
    <a href="/voting/<tag:categories[].ID />" id="<tag:categories[].ID />" class="category <if:categories[].Active>active</if:categories[].Active> <if:categories[].Completed>complete</if:categories[].Completed>">
        <h3><tag:categories[].Name /></h3>
        <p><tag:categories[].Subtitle /></p>
    </a>
    </loop:categories>

    <if:loggedIn>
  <h3 style='clear:both; padding-top: 20px;'><a href="/">Back to the main part of the site</a></h3>
  </if:loggedIn>
</div> 
 
</body>
</html>