<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>/v/GAs - <tag:title /></title>

    <link rel="stylesheet" href="/includes/bootstrap-1.4.0.css">
    <link rel="stylesheet" href="/templates/style.css">
    
	<script src='https://code.jquery.com/jquery-1.7.1.min.js'></script>
	<!-- <script src='external/bootstrap-tabs.js'></script> -->
	<script src="/includes/jquery.tablesorter.min.js"></script>
	<script src='/includes/highcharts.js'></script>

    <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8" />
  </head>
  
<if:navbar>
  <body>
    <div class="topbar">
		<div class="topbar-inner">
			<div class="container">
				<h3>
					<a href="/index.php">The 2011 /v/GAs</a>
				</h3>
				<ul class="nav">
					<tag:navbar />
				</ul>
				<ul class="nav secondary-nav">
					<if:loggedIn>
						<if:pretend>
						<li><a href="/index.php?pretend">Stop pretending to be</a></li>
						<else:pretend>
						<li><img src="<tag:avatarURL />" style='margin-top: 3px;' /></li>
						</if:pretend>
						<li><a href="http://steamcommunity.com/profiles/<tag:communityID />"> <tag:displayName /></a></li>
					<else:loggedIn>
						<li><a href="<tag:openIDurl />"><img src="/templates/images/sits_small.png" /></a></li>
					</if:loggedIn>
				</ul>
			</div>
		</div>
    </div>
    <!-- <div class="alert-message success" style="margin-top: -20px;">
		<p><a href="voting.php">Voting</a> is now open and will remain open until the end of 2011! If you haven't already voted, you should do so before time runs out.</p>
	</div> -->
    <div class="container" role="main">
<else:navbar>
  <body style="padding-top: 0px;">
	<div class="container">
</if:navbar>

		<if:success>
			<div class="alert-message block-message success">
				<p><tag:success /></p>
			</div>
		</if:success>
		<if:error>
			<div class="alert-message block-message error">
				<p><tag:error /></p>
			</div>
		</if:error>
        <tag:content />
    </div>
    <if:navbar>
    <div class="bottombar">
		<div class="bottombar-inner">
			<div class="container">
				<ul class="nav">
					<li><a href="https://boards.4chan.org/v/">/v/ - The Vidya</a></li>
					<li><a href="http://steamcommunity.com/groups/vidyagaemawards">Steam Group</a></li>
					<li><a href="mailto:vidyagaemawards@gmail.com">Email</a></li>
				</ul>
				<ul class="nav secondary-nav">
					<li><a href="privacy.php">Privacy Policy</a></li>
					<if:loggedIn>
						<li><a href="logout.php?return=<tag:page />.php">Logout</a></li>
					</if:loggedIn>
				</ul>
			</div>
		</div>
    </div>
    </if:navbar>
  </body>
  
</html>
