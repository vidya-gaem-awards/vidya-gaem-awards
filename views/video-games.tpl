<header class="jumbotron subhead" style="text-align: center;">
<h1>Vidya in 2013</h1>
<p class="lead">Need a reminder of what games were released in 2013?</p>
<p>Click on the row headers to sort by platform. List is from <a href="http://en.wikipedia.org/wiki/2013_in_video_gaming">Wikipedia</a> with some additions from the /v/GA team.</p>
</header>

<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#games").tablesorter(); 
    } 
); 
</script>

<style type="text/css">
#games td {
    text-align: center;
}
.c-pc {
    background-color: black;
    color: white;
}
.c-ps3, .c-ps4, .c-psv {
    background-color: rgb(0, 64, 152);
    color: white;
}
.c-360, .c-xb1 {
    background-color: rgb(17, 125, 16);
    color: white;
}
.c-wii, .c-3ds, .c-wiiu {
    background-color: rgb(127, 127, 127);
    color: white;
}
.yes {
    display: block;
}
#games {
    border-color: black;
    border-radius: 0;
}
thead th {
    background-color: white;
    border-bottom: 2px black solid;
}
#games td, thead th {
    border-radius: 0 !important;
}
.divider {
    border-left: 1px solid black !important;
}
.notable {
    font-weight: bold;
}
</style>

<if:adminTools>
<div class="row">
    <div class="span12" style='text-align: center; margin-bottom: 8px;'>
        <a class="btn btn-success btn-large" id="add-a-game">Add a Game</a>
        <div class="alert alert-warning" id="add-a-game-msg" style="display: none; margin-bottom: 0;">
            Please make sure you spell the game correctly. You won't be able to edit it afterwards and it will show up as
            a suggestion when users are writing their nominations.
        </div>
    </div>
</div>
</if:adminTools>

<table class="table table-striped table-bordered table-condensed tablesorter" id="games">
<thead>
<tr>
    <th class='divider'>Game Title</th>
    <th width='40px' class='divider'>PC</th>
    <th width='40px' class='divider'>PS3</th>
    <th width='40px'>PS4</th>
    <th width='40px'>PSV</th>
    <th width='40px' class='divider'>360</th>
    <th width='40px'>XB1</th>
    <th width='50px' class='divider'>Wii</th>
    <th width='50px'>Wii U</th>
    <th width='50px'>3DS</th>
    <th width='60px' class='divider'>Mobile</th>
    <th width='120px' class='divider'>Others</th>
</tr>
</thead>
<tbody>
<if:adminTools>
<tr id="new-game" style="display: none;">
    <form id="new-game-form">
    <td class='divider <tag:games[].Notable />' style='text-align: left;'>
        <input type="text" style='width: 90%;' name="Game" id="Game">
    </td>
    <td class='divider'>
        <input type="checkbox" name="PC">
    </td>
    <td class='divider'>
        <input type="checkbox" name="PS3">
    </td>
    <td>
        <input type="checkbox" name="PS4">
    </td>
    <td>
        <input type="checkbox" name="PSV">
    </td>
    <td class='divider'>
        <input type="checkbox" name="360">
    </td>
    <td>
        <input type="checkbox" name="XB1">
    </td>
    <td class='divider'>
        <input type="checkbox" name="Wii">
    </td>
    <td>
        <input type="checkbox" name="WiiU">
    </td>
    <td>
        <input type="checkbox" name="3DS">
    </td>
    <td class='divider'>
        <input type="checkbox" name="Mobile">
    </td>
    <td class='divider'>
        <input class="btn" type="submit" id="game-submit">
    </td>
    </form>
</tr>
</if:adminTools>
<loop:games>
<tr>
    <td class='divider <tag:games[].Notable />' style='text-align: left;'><tag:games[].Game /></td>
    <td class='divider'><tag:games[].PC /></td>
    <td class='divider'><tag:games[].PS3 /></td>
    <td><tag:games[].PS4 /></td>
    <td><tag:games[].PSV /></td>
    <td class='divider'><tag:games[].360 /></td>
    <td><tag:games[].XB1 /></td>
    <td class='divider'><tag:games[].Wii /></td>
    <td><tag:games[].WiiU /></td>
    <td><tag:games[].3DS /></td>
    <td class='divider'><tag:games[].Mobile /></td>
    <td class='divider'><tag:games[].Others /></td>
</tr>
</loop:games>
</tbody>
</table>

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js"></script>
<script type="text/javascript" src="/public/jquery/jquery.floatThead.min.js"></script>
<script type="text/javascript">
$('#games').floatThead({
    scrollingTop: 40
});
</script>

<if:adminTools>
<script type="text/javascript">
$("#add-a-game").click(function(e){
    $("#new-game").show();
    $("#Game").focus();
    $("#add-a-game").hide();
    $("#add-a-game-msg").show();
});
$("#new-game-form").submit(function(e){
    e.preventDefault();
    $("#game-submit").attr("disabled", "disabled");

    $.post("/ajax-videogame", $("#new-game-form").serialize(), function(data) {
        console.log(data);
        if (data.error) {
            $("#add-a-game-msg").text("Error: "+data.error);
            $("#add-a-game-msg").removeClass("alert-warning alert-success");
            $("#add-a-game-msg").addClass("alert-error");
        } else {
            $("#add-a-game-msg").text('Success! "'+data.success+'" has been added. It will show up after a refresh.');
            $("#add-a-game-msg").removeClass("alert-warning alert-error");
            $("#add-a-game-msg").addClass("alert-success");
            $("#Game").attr("value", "");
            $("#new-game input[type=checkbox]").prop("checked", false);
            $("#Game").focus();
        }
        $("#game-submit").removeAttr("disabled");
    }, "json");

});
</script>
</if:adminTools>