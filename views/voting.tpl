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


<div id="wrapper">      
  <if:category>
  <div class="awardHeader">
    <a href="#" class="navigation left">&lt;</a>
    <div class="awardHeaderContainer">
      <div class="awardName"><tag:category.Name /></div>
      <hr>
      <div class="awardSubtitle"><tag:category.Subtitle /></div>
    </div>
    <a href="#" class="navigation right">&gt;</a>
  </div>
  </if:category>
    
<if:category>
<div id="limitsDrag"> 
    <div id="nomineeColumn" class="column">
        
        <loop:nominees>

          <div id="nominee-<tag:nominees[].NomineeID />" class="aNominee" data-order="<tag:nominees[].Order />" data-nominee="<tag:nominees[].NomineeID />">
              <img class="fakeBorder" src="/public/votebox_foreground.png">
              <img class="nomineeImage" src="<tag:nominees[].Image />">
              <div class="nomineeInfo">
                  <div class="number"></div>
                  <div class="nomineeName"><tag:nominees[].Name /></div>
                  <div class="nomineeSubtitle"><tag:nominees[].Subtitle /></div>
              </div>
          </div>

        </loop:nominees>

    </div>

    <div id="spacerColumn" class="column">
      &nbsp;
    </div>
    
    <!if:votingNotYetOpen>
    <div id="voteColumn" class="column">
        
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
  <h2>.</h2>
  <p>Despite the new look, voting is still the same. Vote for as many nominees as you want, and put them in the order you'd like to see them win.</p>
  <p>Too much effort for you? Vote for one nominee and call it a day.</p>
  <h2>Information about the stream</h2>
  <p>We plan to stream at roughly the same time as last year (early March). If you'd like to submit a video for the show, see the <a href="/videos">videos</a> page for more information. We plan on having more vidya analysis instead of funny (or not-so-funny) skits this time, so keep that in mind.</p>
  <else:votingNotYetOpen>
  <h2>Thanks to everybody who voted.</h2>
  <p>No new votes can be made, but if you've already voted you can still see the votes you made.</p>
  <p>It'll take us a few days to determine the final winners, and a few weeks before the stream will be ready. We'll announce the stream date once it's been confirmed.</p>
  </if:votingNotYetOpen>
  </if:votingEnabled>
</div>
</if:category>

<img src="/public/dumb.gif" alt="" class="shit">
</div>

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
 
 
</body>
</html>