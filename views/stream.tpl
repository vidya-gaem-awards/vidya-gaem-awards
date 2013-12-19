<!DOCTYPE html>
<html lang="en" style="overflow: hidden;">
<head>
<title>The 2012 Vidya Gaem Awards</title>
<style type="text/css">
@font-face {
  font-family: "Neon 80s";
  src: url("/public/Neon.ttf");
}

@font-face {
  font-family: "Blade Runner";
  src: url("/public/blade_runner.ttf");
}

body {
  background-color: black;
    background-image: url("/public/space.png");
    background-repeat: repeat;
    background-attachment: fixed;
    color: white;
    font-family: "Neon 80s", "Trebuchet MS", "Calibri", "Verdana", sans-serif;
}

h1 {
  color: aqua;
  font-family: "Blade Runner", "Neon 80s", Verdana, sans-serif;
  font-size: 40px;
  text-shadow: 0px 0px 20px aqua;
  margin-bottom: 5px;
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
}

#live_player {
  border: 5px solid fuchsia;
  -moz-box-shadow:    0px 0px 10px 3px fuchsia;
    -webkit-box-shadow: 0px 0px 10px 3px fuchsia;
    box-shadow:         0px 0px 10px 3px fuchsia;
}

* {
  margin: 0;
  padding: 0;
}
body, html {
  height: 100%;
}
.channel-main {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
}
#main_col {
  position: relative;
  height: 100%;
  overflow: hidden;
  margin-left: 0px;
  margin-right: 320px;
  z-index: 2;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}
.content {
  height: 100%;
}
.scroll {
  position: relative;
  overflow: hidden;
}
.stretch {
  position: absolute;
  width: 100%;
}
#left_close, #right_close {
  z-index: 10;
  display: block;
  position: absolute;
  width: 18px;
  height: 18px;
  text-align: center;
  line-height: 20px;
  text-decoration: none;
  cursor: pointer;
  border: 1px solid rgba(0,0,0,0.25);
  border-radius: 2px;
  -moz-border-radius: 2px;
  -webkit-border-radius: 2px;
  box-shadow: 0 1px 0 rgba(255,255,255,0.5),inset 0 1px 0 rgba(255,255,255,0.5);
  -moz-box-shadow: 0 1px 0 rgba(255,255,255,0.5),inset 0 1px 0 rgba(255,255,255,0.5);
  -webkit-box-shadow: 0 1px 0 rgba(255,255,255,0.5),inset 0 1px 0 rgba(255,255,255,0.5);
  text-indent: -999px;
  overflow: hidden;
  background: url("/public/arrow_collapse.png") no-repeat 0 0;
}
#right_close {
  top: 10px;
  right: 30px;
  background-position: 0 -19px;
  background-color: black;
  border: 3px solid yellow;
  -moz-box-shadow:    0px 0px 10px 3px yellow;
    -webkit-box-shadow: 0px 0px 10px 3px yellow;
    box-shadow:         0px 0px 10px 3px yellow;
}
.scroll-content-contain {
  width: auto !important;
  height: auto !important;
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
}
.scroll .scroll-content-contain {
  width: 100%;
  height: 100%;
  overflow: hidden;
  overflow-y: auto;
}
#main_col .content .scroll-content {
  position: relative;
  padding: 0 30px;
  overflow: hidden;
  min-height: 100%;
}
.scroll-content {
  overflow: hidden !important;
}
#main_col .content .live_site_player_container {
  width: 100%;
}
.swf_container {
  color: #fff;
  background: #000;
}
#right_col {
  position: absolute;
  right: 0;
  top: 0;
  bottom: 0;
  width: 320px;
  z-index: 3;
  border-left: 3px solid white;
  -moz-box-shadow:    0px 0px 10px 3px white;
    -webkit-box-shadow: 0px 0px 10px 3px white;
    box-shadow:         0px 0px 10px 3px white;
}
#right_col::before {
  content: "";
  display: block;
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  width: 1px;
  z-index: 4;
  box-shadow: 1px 0 0 rgba(0,0,0,0.1),2px 0 0 rgba(0,0,0,0.07),3px 0 0 rgba(0,0,0,0.01);
  -moz-box-shadow: 1px 0 0 rgba(0,0,0,0.1),2px 0 0 rgba(0,0,0,0.07),3px 0 0 rgba(0,0,0,0.01);
  -webkit-box-shadow: 1px 0 0 rgba(0,0,0,0.1),2px 0 0 rgba(0,0,0,0.07),3px 0 0 rgba(0,0,0,0.01);
}

#right_col .content {
  width: 319px;
  height: 100%;
}

.controls {
  margin-top: 10px;
}

.controls li {
  display: inline;
  list-style-type: none;
  font-size: x-large;
}

.controls a {
  color: lime;
  text-decoration: none;
  text-shadow: 0px 0px 10px lime;
}

.controls a:hover {
  text-decoration: underline;
  color: white;
  text-shadow: none;
}

.controls .gap {
  margin-left: 6px;
  margin-right: 6px;
}
</style>

<script src='/public/jquery/jquery-1.8.2.min.js'></script>
</head>

<body>

<div class="channel-main">
  <div class="column" id="main_col" style="margin-left: 0px;">
    <div class="content">
      <div class="stretch scroll" style="top: 0px; bottom: 0px;">
        <a id="right_close">Show/Hide</a>
        <div class="scroll-content-contain" style="right: 0px;">
          <div class="scroll-content">
            <h1>
              The 2012 Vidya Gaem Awards
            </h1>
            <div id="live_player">
              <div class="live_site_player_container swf_container" id="standard_holder" style="width: 100%; height: 485px;">
                <object type="application/x-shockwave-flash" height="100%" width="100%" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=vidyagaemawards" bgcolor="#000000"><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="hostname=www.twitch.tv&channel=vidyagaemawards&auto_play=true&start_volume=25" /></object>
              </div>
            </div>
            <ul class="controls">
              <!-- <li>Change Stream: </li>
              <li style="display: none;"><a id="link-stream-twitch" href="#">Twitch</a> <span class="gap">|</span></li>
              <li><a id="link-stream-livestream" href="#">Livestream</a> <span class="gap">|</span></li> -->
              <li>Chat Popouts: </li>
              <!-- <li style="display: none;"><a id="link-chat-twitch" href="#">Twitch</a> <span class="gap">|</span></li> -->
              <li><a href="http://twitch.tv/chat/embed?channel=vidyagaemawards&popout_chat=true" target="_blank">Twitch</a> <span class="gap">|</span></li>
              <li><a href="http://vidyagaemslive.chatango.com/?js" target="_blank">Chatango</a> <span class="gap">|</span></li>
              <li><a href="steam://friends/joinchat/103582791432684008">Steam Chat</a> <span class="gap">|</span></li>
              <li>IRC: <a href="irc://irc.rizon.net//v/ga">#/v/ga</a> @ rizon.net</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="column fixed" id="right_col">
    <div class="content" id="chat_container">
      <iframe frameborder="0" scrolling="no" id="chat_embed" src="http://twitch.tv/chat/embed?channel=vidyagaemawards&popout_chat=true" height="100%" width="320"></iframe>
    </div>
  </div>
</div>

<script type="text/javascript">
function adjustHeight() {
  var bodyHeight = $(window).height();
  $("#standard_holder").height(bodyHeight - 100);
}
$(document).ready(adjustHeight);
$(window).resize(adjustHeight);

$("#right_close").click(function() {
  $("#right_col").toggle();
  if ($("#right_col").is(":visible")) {
    $("#main_col").css("margin-right", 320);
    $("#right_close").css("background-position-y", -19);
  } else {
    $("#main_col").css("margin-right", 0);
    $("#right_close").css("background-position-y", -1);
  }
});

$("#link-stream-twitch").click(function() {
  event.preventDefault();
  $("#standard_holder").empty();
  $("#standard_holder").append('<object type="application/x-shockwave-flash" height="100%" width="100%" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=vidyagaemawards" bgcolor="#000000"><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="hostname=www.twitch.tv&channel=vidyagaemawards&auto_play=true&start_volume=25" /></object>');
  $("#link-stream-livestream").parent().show();
  $("#link-stream-twitch").parent().hide();
});

$("#link-stream-livestream").click(function() {
  event.preventDefault();
  $("#standard_holder").empty();
  $("#standard_holder").append('<iframe width="100%" height="100%" src="http://cdn.livestream.com/embed/vidyagaemawards?layout=4&color=0xff00ff&autoPlay=true&mute=false&iconColorOver=0xcccccc&iconColor=0xffffff&allowchat=false&height=100%25&width=100%25" style="border:0;outline:0" frameborder="0" scrolling="no"></iframe>');
  $("#link-stream-twitch").parent().show();
  $("#link-stream-livestream").parent().hide();
});

$("#link-chat-twitch").click(function() {
  event.preventDefault();
  $("#chat_container").empty();
  $("#chat_container").append('<iframe frameborder="0" scrolling="no" id="chat_embed" src="http://twitch.tv/chat/embed?channel=vidyagaemawards&amp;" height="100%" width="320"></iframe>');
  $("#link-chat-chatango").parent().show();
  $("#link-chat-twitch").parent().hide();
});

$("#link-chat-chatango").click(function() {
  event.preventDefault();
  $("#chat_container").empty();
  $("#chat_container").append('<object width="100%" height="100%" id="obj_1328891570920"><param name="movie" value="http://vidyagaemslive.chatango.com/group"/><param name="AllowScriptAccess" VALUE="always"/><param name="AllowNetworking" VALUE="all"/><param name="AllowFullScreen" VALUE="true"/><param name="flashvars" value="cid=1328891570920&b=1&d=666666&f=50&l=999999&q=999999&w=0&t=0"/><embed id="emb_1328891570920" src="http://vidyagaemslive.chatango.com/group" width="100%" height="100%" allowScriptAccess="always" allowNetworking="all" type="application/x-shockwave-flash" allowFullScreen="true" flashvars="cid=1328891570920&b=1&d=666666&f=50&l=999999&q=999999&w=0&t=0"></embed></object>');
  $("#link-chat-twitch").parent().show();
  $("#link-chat-chatango").parent().hide();
});
</script>

</body>
</html>