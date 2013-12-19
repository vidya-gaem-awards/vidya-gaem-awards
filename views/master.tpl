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
    <script src='/public/bootstrap-2.1.0/js/bootstrap.min.js'></script>
    
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
                <a class="brand" href="/">2013 /v/GAs</a>
                <ul class="nav">
                    <tag:navbar />
                </ul>
                <ul class="nav secondary-nav">
                    <if:loggedIn>
                        <li><img src="<tag:avatarURL />" style='margin-top: 3px; width: 32px; height: 32px;' /></li>
                        <li><a href="http://steamcommunity.com/profiles/<tag:communityID />"> <tag:displayName /></a></li>
                    <else:loggedIn>
                        <li><a href="<tag:openIDurl />"><img src="http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_small.png" /></a></li>
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
                    <li><a href="http://2011.vidyagaemawards.com">2011 /v/GAs</a></li>
                    <li><a href="http://2012.vidyagaemawards.com">2012 /v/GAs</a></li>
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
