<style type="text/css">
*{margin:0;padding:0;font-family: arial,helvetica,sans-serif;}
a,a:hover{text-decoration:none;}

.logo {
	margin-left: 15px;
}
.headerText {
	font-size: 200%;
	text-align: center;
	line-height: 1.5;
}
.blueContainer{
	width: 958px;
	margin: 0 auto 20px;
	border: 1px solid #b8c6da;
	background-color: #d6daf0;
	overflow: auto;
}
.leftCategories{
	float: left;
	width: 277px;
	margin: 10px;
	border: 1px solid #000;
	background-color: #262626;
	padding-bottom: 3px;
}
.headCategories{
	height: 53px;
	background-color:#789922;
	border-bottom: 1px solid #000;
	background-image: url('https://i.imgur.com/XWjZl.gif');
}
.aCategory{
	width:261px;
	background-color:#060606;
	border: 1px solid #d6daf0;
	margin: 3px 0 0 3px;
	padding: 4px;
	box-shadow: inset 0 0 0.5em #d6daf0;
	-webkit-box-shadow: inset 0 0 0.5em #d6daf0;
	-moz-box-shadow: inset 0 0 0.5em #d6daf0;
	-0-box-shadow: inset 0 0 0.5em #d6daf0;
	transition: all 200ms;
	-moz-transition: all 200ms;
	-webkit-transition: all 200ms;
	-o-transition: all 200ms;
	cursor:pointer;
}
.aCategory.active {
	background-image: url('https://i.imgur.com/QwguU.gif');
	background-repeat: no-repeat;
	background-position: right center;
}
.aCategory:hover{
	box-shadow: inset 0 0 1em #d6daf0;
	-webkit-box-shadow: inset 0 0 1em #d6daf0;
	-moz-box-shadow: inset 0 0 1em #d6daf0;
	-o-box-shadow: inset 0 0 1em #d6daf0;
}
.aCategory h2{
	color: #d6daf0;
	font-size: 1.1em;
	font-weight: normal;
	line-height: 1.1em;
	text-transform: uppercase;
}
.aCategory h3{
	color: #808080;
	font-size: 0.9em;
	font-weight: normal;
	line-height: 1.1em;
}
.aCategory.complete {
	background-color: #003300;
}

.rightNominees{
	float: left;
	width: 649px;
	margin-top: 10px;
	border: 1px solid #000;
	background-color: #262626;
	padding-bottom: 3px;
}
.headNominees{
	height: 28px;
	background-color:#789922;
	border-bottom: 1px solid #000;
	padding: 45px 0 0 8px;
	background-image: url('https://i.imgur.com/utxwt.gif');
}
.headNominees h2{
	color: #060606;
	font-size: 1.4em;
	font-weight: bold;
	line-height: 1.4em;
	display: inline;
	margin-top: 73px;
}
.headNominees h3{
	color: #262626;
	font-size: 1.1em;
	font-weight: bold;
	line-height: 1.1em;
	display: inline;
}
.aNominee{
	width: 641px;
	height: 132px;
	background-color: #060606;
	border: 1px solid #d6daf0;
	margin: 3px 0 0 3px;
}
.aNominee h2{
	color: #d6daf0;
	font-size: 1.4em;
	font-weight: bold;
	line-height: 1.4em;
	margin-top: 50px;
	margin-left: 5px;
}
.aNominee h3{
	color: #d6daf0;
	font-size: 1.1em;
	font-weight: normal;
	line-height: 1.1em;
	margin-left: 5px;
	text-decoration:none;
}
.aNominee .overlay a{
	color: #83a134;
	font-size: 1.1em;
	font-weight: normal;
	line-height: 1.1em;
	text-decoration:none;
}
.aNominee .overlay a:hover{
	color: #728e29;
}
.vote {
	height: 27px;
	width: 74px;
	background-color: #789922;
	background-image: url('https://i.imgur.com/SORDQ.gif');
	border: 1px solid black;
	color: #060606;
	font-size: 1.4em;
	font-weight: bold;
	line-height: 1.4em;
	padding: 3px 12px;
	position: relative;
	top: 91px;
	left: 535px;
	box-shadow: inset 0 0 0 #d6daf0;
	-webkit-box-shadow: inset 0 0 0 #d6daf0;
	-moz-box-shadow: inset 0 0 0 #d6daf0;
	-0-box-shadow: inset 0 0 0 #d6daf0;
	transition: all 200ms;
	-moz-transition: all 200ms;
	-webkit-transition: all 200ms;
	-o-transition: all 200ms;
	text-align:center;
	cursor:pointer;
}

.vote.placeholder {
	background-image: none;
	background-color: #333333;
}

.vote.unvoted:hover{
	box-shadow: inset 0 0 0.5em #d6daf0;
	-webkit-box-shadow: inset 0 0 0.5em #d6daf0;
	-moz-box-shadow: inset 0 0 0.5em #d6daf0;
	-0-box-shadow: inset 0 0 0.5em #d6daf0;
}

.vote.voted {
	background-color: #9B223C;
	background-image: none;
}

.overlay {
	background-color: rgba(0,0,0,0.4);
	height: 47px;
}
</style>

<div class="row">
	<div class="span5">
		<a href="https://2011.vidyagameawards.com">
			<img src="https://i.imgur.com/e83zv.png" class="logo" />
		</a>
	</div>
	
	<div class="span11 headerText" style="margin-top: 10px;">
		<tag:voteText />
		<if:loggedIn>
			<div style="font-size: 75%; margin-top: 15px;">
				You are signed in as <a href="http://steamcommunity.com/profiles/<tag:communityID />"> <tag:displayName /></a>
			</div>
		<else:loggedIn>
			<br />
			<div>
				<a href="<tag:openIDurl />"><img src="/images/sits_large_noborder.png" /></a>
				<div style="font-size: 50%;">
					<!-- To prevent ballot stuffing, you must sign in with Steam before voting. See our <a href="privacy.php">Privacy Policy</a> -->
					<br />If you have already voted, you can sign in to see your votes.
				</div>
			</div>
		</if:loggedIn>
	</div>
</div>

<div class="blueContainer">
	<div class="leftCategories">
		<div class="headCategories">
		</div>
		
		<loop:categories>
		<a href="?category=<tag:categories[].ID />"><div class="aCategory <if:categories[].Active>active</if:categories[].Active> <if:categories[].Completed>complete</if:categories[].Completed>" id="<tag:categories[].ID />">
			<h2><tag:categories[].Name /></h2>
			<h3><tag:categories[].Subtitle /></h3>
		</div></a>
		</loop:categories>
	</div>
	
	<if:category>	
	<div class="rightNominees">
		<div class="headNominees">
			<h2><tag:category.Name /></h2>
			<h3><tag:category.Subtitle /></h3>
		</div>
		
		<loop:nominees>
		<div class="aNominee" style="background-image: url('<tag:nominees[].Background />')">
					
				<if:nominees[].Voted>
					<div id="<tag:nominees[].ID />" class="vote voted">
						Voted!
					</div>
				<else:nominees[].Voted>
				
					<if:votingEnabled>	
						<div id="<tag:nominees[].ID />" class="vote unvoted">
							Vote
						</div>
					<else:votingEnabled>
						<div class="vote placeholder" style="opacity: 0;">
							Vote
						</div>
					</if:votingEnabled>
					
				</if:nominees[].Voted>
			
			<div class="overlay">
				<h2><tag:nominees[].Name /></h2>
				<h3><tag:nominees[].ExtraInfo /></h3>
			</div>
		</div>
		</loop:nominees>
	</div>
	<else:category>
	<div class="rightNominees">
		<div class="headNominees">
			<h2>No category selected</h2>
		</div>
		
		<div class="aNominee">
			<div class="overlay" style="text-align: center; background-color: rgba(0, 0, 0, 0.0);">
				<h2>Select a category on the left to get started!</h2>
				<h3>Categories will turn green once you've successfully voted.</h3>
			</div>
		</div>
	</div>
	
	<!--<div style="font-size: 200%; margin: 10px; line-height: 1.5;">
		I know this is /v/ and all, but it would be great if you could vote for what you legitimately think deserves to win instead of just hopping on the bandwagon. You also don't need to vote for every category if you don't want to.<br /><br />
		If you want to leaves comments or suggestions or whatever, you can <a href="mailto:vidyagaemawards@gmail.com">send an email</a>, write in the <a href="index.php">suggestion box</a> or post in the <a href="/forum">forum</a>.
	</div>-->
	</if:category>
	
</div>

<if:category>
<script>
$(".aNominee .vote").click(function(event) {
	event.preventDefault();

	<if:votingEnabled>
	
	<if:loggedIn>
	var link = event.currentTarget;
	var category = "<tag:category.ID />";
	var nominee = link.id;
		
	var jLink = $("#"+nominee);
	var catLink = $("#"+category);
	
	if (jLink.hasClass("voted")) {
		nominee = "cancel";
	}
	
	$.post("voting-submission.php", { nominee: nominee, category: category }, function(data) {
		if (data == "done") {
							
			$("div .vote").text("Vote");
			$("div .vote").addClass("unvoted");
			$("div .vote").removeClass("voted");
		
			if (nominee == "cancel") {
				catLink.removeClass("complete");
			} else {
				catLink.addClass("complete");
				jLink.text("Voted!");
				jLink.removeClass("unvoted");
				jLink.addClass("voted");
			}
			
			console.log(jLink);
			
		} else if (data == "error") {
		
			alert("Sorry, a database error occurred and your vote was not recorded. Try again.");
			
		} else {
		
			alert("Something went wrong. Try again.");
			
		}
		
	});
	<else:loggedIn>
	alert("You must be signed in to vote.");
	</if:loggedIn>
	
	<else:votingEnabled>
	alert("Voting has closed: you are no longer able to change your vote.");
	</if:votingEnabled>
	
});
</script>
</if:category>
