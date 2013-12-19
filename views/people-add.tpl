<h1>Add new user</h1>
<ul class="breadcrumb">
    <li class="active">
        </li><li><a href="/people">Back to people page</a>
    </li>
</ul>
<div class="row">
    <div class="span6">
        <p>You can use this page to add a new user to the user list. They will start with <a href="/people/permissions">level 1 permissions</a> by default. 
        Note that they will have had to have logged into the /v/GA site at least once before they can be added.</p>
        <p>This search field expects the <strong>steam community ID</strong> of the user, which is a 17 digit string of numbers. This is yours, for example: <tag:communityID /></p>
        <p>If you don't know their community ID, look it up using <a href="http://steamidconverter.com">this website</a>. You want the field called <tt>steamID64</tt>.</p>
    </div>
    <div class="span6">
        <div class="well well-small">
            <form class="form-inline" id="search-form">
                <input type="text" placeholder="Steam Community ID" style="font-size: 20px; height: 28px; width: 200px;" maxlength="17" id="search-id" required /> <input type="submit" class="btn btn-large" value="Search">
            </form>
            <p id="searching" style="display: none;">Processing... <img src="/public/loading.gif" style="height: 16px; width: 16px;" /></p>
            <div class="alert alert-error" style="display: none;" id="error-box">
                <strong>Error:</strong>
                <span id="error-msg"></span>
            </div>
            <div class="alert alert-success alert-block" id="result-box" style="display: none;">
                <h4>User found:</h4>
                <p id="result-msg" style="margin-top: 10px; margin-bottom: 10px; font-size: large;"></p>
                <button class='btn btn-large' id='btn-add'>Add to user list?</button>
                <strong id='btn-success' style="display: none;">Success!</strong>
            </div>
        </div>
    </div>
</div>

<script>
var currentlySubmitting = false;

$('#search-form').submit(function(event) {
    event.preventDefault();
    
    if (currentlySubmitting) {
        return;
    }
    
    currentlySubmitting = true;
    
    $("#error-box").hide();
    $("#result-box").hide();
    $("#searching").show();
    
    $.post("/user-search", { "ID": $('#search-id').val(), "Add": 0 }, function(data) {
        currentlySubmitting = false;
        $("#searching").hide("fast");
        
        if ('error' in data) {
            var msg;
            if (data.error == "no matches") {
                msg = "either the community ID was invalid or the user hasn't yet logged into the /v/GA site at least once.";
            } else if (data.error == "mysql") {
                msg = "a MySQL error occurred. Try again later.";
            } else if (data.error == "already special") {
                msg = data.name + " is already in the user list.";
            } else {
                msg = "something went wrong. Try again later.";
            }
            $("#error-msg").text(msg);
            $("#error-box").show("fast");
        } else {
            var name = $('<div/>').text(data.Name).html();
            msg = "<img src='data:image/png;base64," + data.Avatar + "' />";
            msg += "&nbsp;&nbsp;<a href='http://steamcommunity.com/profiles/" + data.SteamID + "'>";
            msg += name + "</a>";
            $("#btn-add").attr("data-id", data.SteamID);
            $("#result-msg").html(msg);
            $("#result-box").show("fast");
        }
        
    }, "json");
});

$('#btn-add').click(function() {
    
    if (currentlySubmitting) {
        return;
    }
    
    currentlySubmitting = true;
    
    $("#error-box").hide();
    $("#btn-add").show();
    $("#btn-success").hide();
    $("#searching").show();

    $.post("/user-search", { "ID": $('#btn-add').attr("data-id"), "Add": 1 }, function(data) {
        currentlySubmitting = false;
        $("#searching").hide("fast");
        
        if ('error' in data) {
            var msg;
            if (data.error == "mysql") {
                msg = "a MySQL error occurred. Try again later.";
            } else if (data.error == "mysql2") {
                msg = "a MySQL error occurred, but at least part of it succeeded.";
                msg += " Check the <a href='/people'>user list</a> to see if it worked.";
            } else if (data.error == "no matches") {
                msg = "the user disappeared! That shouldn't happen.";
            } else if (data.error == "already special") {
                msg = "it looks like that user is already on <a href='/people'>the list</a>.";
                msg += " It may have been double submitted for some reason.";
            } else {
                msg = "something went wrong. Try again later.";
            }
            $("#error-msg").html(msg);
            $("#error-box").show("fast");
        } else {
            $("#btn-add").hide();
            $("#btn-success").show();
        }
    }, "json");
});
</script>