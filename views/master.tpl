<!DOCTYPE html>
<html lang="en">
  <head>
    <title>/v/GAs - <tag:title /></title>

    <link rel="stylesheet" href="/public/bootstrap-2.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/public/bootstrap-2.1.0/css/bootstrap-responsive.min.css">
    <link rel="stylesheet" href="/public/jquery/jquery-ui-1.9.2.min.css">
    <link rel="stylesheet" href="/public/style.css">
    
    <script src='/public/jquery/jquery-1.8.2.min.js'></script>
    <script src='/public/jquery/jquery-ui-1.9.2.min.js'></script>
    <script src='/public/jquery/jquery.tablesorter.min.js'></script>
    <script src='https://code.highcharts.com/3.0.7/highcharts.js'></script>
    <script src='/public/bootstrap-2.1.0/js/bootstrap.min.js'></script>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8" />
    
  </head>
  
<if:navbar>
    <if:noContainer>
        <body>
    <else:noContainer>
        <body style="padding-top: 50px;">
    </if:noContainer>
    <div class="navbar navbar-fixed-top navbar-inverse">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="/">2012 /v/GAs</a>
				<ul class="nav">
					<tag:navbar />
				</ul>
				<ul class="nav secondary-nav">
					<if:loggedIn>
						<li><img src="<tag:avatarURL />" style='margin-top: 3px; width: 32px; height: 32px;' /></li>
						<li><a href="http://steamcommunity.com/profiles/<tag:communityID />"> <tag:displayName /></a></li>
					<else:loggedIn>
						<li><a href="<tag:openIDurl />"><img src="/public/sits_small.png" /></a></li>
					</if:loggedIn>
				</ul>
			</div>
		</div>
    </div>
    <!if:noContainer>
    <div class="container" role="main">
    </!if:noContainer>
<else:navbar>
  <body style="padding-top: 30px;">
	<div class="container">
</if:navbar>

	<if:success>
		<div class="alert alert-success">
			<tag:success />
		</div>
	</if:success>
	<if:error>
		<div class="alert alert-error">
			<tag:error />
		</div>
	</if:error>
	
    <tag:content />
    </div>
    <if:navbar>
    <div class="navbar navbar-fixed-bottom navbar-inverse">
		<div class="navbar-inner">
			<div class="container">
				<ul class="nav">
					<li><a href="http://steamcommunity.com/groups/vidyagaemawards">Steam Group</a></li>
					<li><a href="steam://friends/joinchat/103582791432684008">Steam Chat</a></li>
					<li><a href="mailto:vidya@vidyagaemawards.com">Email</a></li>
					<li><a href="https://2011.vidyagaemawards.com">2011 /v/GAs</a></li>
				</ul>
				<ul class="nav secondary-nav">
					<li><a href="/privacy">Privacy Policy</a></li>
					<if:loggedIn>
						<li><a href="/logout/<tag:logoutURL />">Logout</a></li>
					</if:loggedIn>
				</ul>
			</div>
		</div>
    </div>
    </if:navbar>
    
  </body>
  
</html>
