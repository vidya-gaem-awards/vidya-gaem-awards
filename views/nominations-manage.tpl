<style>
    .ui-autocomplete {
        max-height: 300px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
        /* add padding to account for vertical scrollbar */
        width: 200px;
    }
    
    #category-name {
    text-shadow: #000 1px 1px 2px;
    text-align: center;
    color: #789922;
    text-transform: uppercase;
    font-family: "Century Gothic", arial, sans-serif;
    font-weight: bold;
    font-size: 2.3em;
    line-height: 1em;
  }
  
  #category-subtitle {
    text-align: center;
    color: #1f1f1f;
    font-family: "Lucida Sans Unicode", arial, sans-serif;
    font-size: 0.95em;
    line-height: 1.95em;
    margin: 0;
    font-weight: bold;
  }
  
  .aNominee {
        position: relative;
        background: lightblue;
        border: 1px solid black;
        width: 428px;
        height: 100px;
        margin-bottom: 5px;
    }
    .aNominee form {
    position: absolute;
    left: 435px;
    width: 90px;
  }
  .aNominee form button {
    width: 100%;
    margin-bottom: 5px;
  }
    .aNominee header {
    position: absolute;
    color: white;
    font-size: 12px;
    padding-left: 5px;
    padding-right: 5px;
    background-color: black;
  }
  .aNominee header p {
    padding: 0px;
    margin: 0px;
  }
    .aNominee footer{
        position: absolute;
        top: 56px;
        left: 0;
        height: 44px;
        width: 428px;
        background: black;
        background: rgba(0,0,0,0.5);
    }
    .aNominee footer h3{
        color: white;
        text-decoration: none;
        text-transform: uppercase;
        font-family: "Century Gothic",arial,sans-serif;
        font-weight: bold;
        font-size: 16px;
        line-height: 1em;
        padding: 0;
        margin: 5px 0 0 5px;
    }
    .aNominee footer p{
        text-decoration: none;
        color: #cacaca;
        font-family: "Lucida Sans Unicode",arial,sans-serif;
        font-size: 13px;
        padding: 0;
        margin: 0 0 0 5px;
    }
    .aNominee img{
        width: 428px;
        height: 100px;
    }
    #dialog-edit-form input {
    width: 320px;
  }
  #dialog-edit-form .control-label {
    width: 110px;
  }
  #dialog-edit-form .controls {
    margin-left: 130px;
  }
  #dialog-edit-form .help-block {
    width: 320px;
  }
  ol.nominations, ul.nominations {
    margin-right: 35px;
  }
  ol.nominations {
    font-size: 120%;
  }
  ol.nominations li {
    line-height: 1.2;
  }
</style>

<ul class="breadcrumb">
  <li><a href="/categories">Back to main awards and nominations page</a></li>
</ul>

<header class="jumbotron subhead" style="text-align: center;">
<if:canEdit>
  <h1>Nominee Manager</h1>
  <p>Here's where you assign nominees to awards. Official Nominees are the ones that will show up on the <a href="/voting">voting page</a>.</p>
<else:canEdit>
  <h1>Nominee Viewer</h1>
  <p>Here's where you can see the user nominations for each category. These will help inform which nominees are officially chosen for the voting phase.</p>
</if:canEdit>
</header>

<hr>

<div class="row">
    
    <div class="span5" id="category-selector">
        <ul class="nav nav-list custom-navigation-pane">
            <loop:categories> 
            <li data-id="<tag:categories[].ID />" class="<tag:categories[].Active />">
                <a href="/nominations/<tag:categories[].ID />">
                    <i class="icon-chevron-right"></i>
                    <strong><tag:categories[].Name /></strong> <tag:categories[].Subtitle />
                </a>
            </li>
            </loop:categories>
        </ul>
    </div>

  <if:categoryName>
  <div class="span6">
    
    <div id="category-name"><tag:categoryName /></div>
    <div id="category-subtitle"><tag:categorySubtitle /></div>

    <if:categorySecret>
    <p style='text-align: center; margin-top: 8px;'>This is a secret award. It won't be visible until the voting phase.</p>
    </if:categorySecret>

    <div class="well" style="margin-top: 15px;">
      
      <div style="background-color: #08C; color: white; font-size: 20px; line-height: 1.5em; text-align: center; margin-bottom: 10px;">
        Official Nominees: <span id="official-count"><tag:officialCount /></span>
        <if:canEdit><button class="btn btn-mini" type="button" id="new" style="margin-left: 15px;"> New Nominee </button></if:canEdit>
      </div>
      
      <div id="nominee-container">
      
        <loop:official>
        <div class="aNominee" id="nominee-<tag:official[].NomineeID />" data-nominee="<tag:official[].NomineeID />">
          <header>
            <p>ID: <tag:official[].NomineeID /></p>
          </header>
          <if:canEdit>
          <form>
            <button class="btn" type="button" name="edit"><i class="icon-pencil"></i> Edit</button>
            <button class="btn" type="button" name="delete"><i class="icon-trash"></i> Delete</button>
          </form>
          </if:canEdit>
          <img src="<tag:official[].Image />">
          <footer>
            <h3><tag:official[].Name /></h3>
            <p><tag:official[].Subtitle /></p>
          </footer>
        </div>
        </loop:official>
    
      </div>
      
    </div>
  
  <!if:categorySecret>
    <div class="well">
    
      <div style="background-color: #08C; color: white; font-size: 20px; line-height: 1.5em; text-align: center; margin-bottom: 10px;">
        User Nominations: <tag:userCount />
      </div>
      
      <if:nominationsOpen>
      <p style="font-weight: bold; text-align: center; font-size: 120%;">
        Nominations are currently open.
      </p>
      </if:nominationsOpen>
      
      <ol class="nominations">
        <tag:userNominationsTop />
      </ol>

      <a href="#" id="show-more">show the rest</a>

      <ul id="more-nominations" style='display: none;' class="nominations">
        <tag:userNominations />
      </ul>
      
    </div>
  </!if:categorySecret> 
    </if:categoryName>

</div>

<if:canEdit>
<if:categoryName>
<div id="dialog-delete" class="modal hide">
  <div class="modal-body">
    <p>Are you sure you want to remove <span id="dialog-delete-nominee" style="font-weight: bold;"></span> from the list of nominees?</p>
  </div>
  <div class="modal-footer">
    <span id="dialog-delete-status"><img src='/public/loading.gif' width="16px"> deleting...&nbsp;</span>
    <button class="btn btn-danger" id="dialog-delete-nominee-confirm">Confirm</button>
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
  </div>
</div>

<div id="dialog-edit" class="modal hide">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="dialog-edit-header">Add new nominee</h3>
  </div>
  <div class="modal-body">
  
    <div class="alert alert-error" style="display: none;">
      <span id="dialog-edit-error"></span>
      <a class="close" href="#">&times;</a>
    </div>
    
    <script>
    $('.alert-error .close').live('click',function(){
      $(this).parent().fadeOut("fast");
    });
    </script>
  
    <div class="aNominee" style="margin-left: auto; margin-right: auto; margin-bottom: 10px;">
      <header>
        <p>ID: <span class="dialog-edit-id"></span></p>
      </header>
      <form style="color: red; text-align: center; display: none;">
      refresh page for controls
      </form>
      <img src="" id="dialog-edit-image">
      <footer>
        <h3 id="dialog-edit-name"></h3>
        <p id="dialog-edit-subtitle"></p>
      </footer>
    </div>
    
    <form class="form-horizontal" id="dialog-edit-form">
      <div class="control-group">
        <label class="control-label" for="info-id">ID</label>
        <div class="controls">
          <input type="text" id="info-id" placeholder="monster-girl-quest" required name="NomineeID" autocomplete="off">
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="info-name">Name</label>
        <div class="controls">
          <input type="text" id="info-name" placeholder="Monster Girl Quest" required name="Name">
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="info-subtitle">Subtitle</label>
        <div class="controls">
          <input type="text" id="info-subtitle" placeholder="Toro Toro Resistance" name="Subtitle" autocomplete="off">
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="info-image">Image URL</label>
        <div class="controls">
          <input type="text" id="info-image" placeholder="" name="Image" autocomplete="off">
          <span class="help-block">If left blank, the webserver will look for the
          image locally at <strong>/public/nominees/<span class="dialog-edit-id"></span>.png</strong></span>
        </div>
      </div>
    </form>
  
  </div>
  <div class="modal-footer">
    <span id="dialog-edit-status" style="display: none;"><img src='/public/loading.gif' width="16px"> saving...&nbsp;</span>
    <button class="btn btn-primary" id="dialog-edit-submit">Submit</button>
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
  </div>
</div>

<script>
var autocompleters = <tag:autocompleteJavascript />;
var nominees = <tag:nomineeJavascript />;

$(document).ready(function() {
  $( "#info-name" ).autocomplete({
    source: autocompleters,
        minLength: 2,
        delay: 0,
  });
});

var currentlySubmitting = false;

// When a delete button is clicked
$("button[name=delete]").click(function() {

  // Get the nominee name and ID from the contents of the nominee box
  var nomineeID = $(this).parents(".aNominee").attr("data-nominee");
  var nomineeName = nominees[nomineeID].Name;
  
  // Set up the dialog elements for this nominee
  $( "#dialog-delete-status" ).hide();
  $( "#dialog-delete-nominee-confirm" ).removeAttr("disabled");
  $( "#dialog-delete-nominee" ).html(nomineeName);
  $( "#dialog-delete" ).attr("data-nominee", nomineeID);
  
  // Finally, show the dialog
  $( "#dialog-delete" ).modal("show");
});

// When the confirm button is clicked in the delete dialog
$("#dialog-delete-nominee-confirm").click(function() {
  if (currentlySubmitting) {
    return;
  }
  currentlySubmitting = true;
  
  // Show the "please wait" message and disable the submit button
  $( "#dialog-delete-status" ).show();
  $( "#dialog-delete-nominee-confirm" ).attr("disabled", "disabled");
  
  // Grab the nomineeID from the attribute we set earlier
  var nomineeID = $("#dialog-delete").attr("data-nominee");
  
  // Send through the AJAX request to do the actual deleting
  $.post("/ajax-nominations", { Action: "delete", NomineeID: nomineeID, Category: "<tag:category />" }, function(data) {
    currentlySubmitting = false;  
    
    if (data.success) {
      $( "#nominee-" + nomineeID ).slideUp(500, function() {
        $(this).remove();
        $("#official-count").text($("#official-count").text() - 1);
      });
      delete nominees[nomineeID];
    } else {
      alert("An error occurred: "+data.error);
    }
    
    // Close the dialog
    $( "#dialog-delete" ).modal("hide");
  }, "json");
});

var editDialogInterval;
var lastImageValue = "";
var lastID = "";

// When the new nominee button is clicked
$("#new").click(function(){
  // Update the dialog action
  $( "#dialog-edit" ).attr("data-action", "new");
  $( "#dialog-edit" ).removeAttr("data-nominee");
  $( "#dialog-edit-header" ).text("Add new nominee");
  
  // Clear any existing information in the dialog
  $( ".dialog-edit-id" ).text("");
  $( "#dialog-edit-name" ).text("");
  $( "#dialog-edit-subtitle" ).text("");
  $( "#dialog-edit-image" ).removeAttr("src");
  
  $( "#info-id" ).val("");
  $( "#info-name" ).val("");
  $( "#info-subtitle" ).val("");
  $( "#info-image" ).val("");
  
  $( "#info-id" ).removeAttr("disabled");
  
  // Show the dialog
  $( "#dialog-edit" ).modal("show");
});

// When an edit button is clicked
$("button[name=edit]").click(function() {
  // Grab the ID
  var nomineeID = $(this).parents(".aNominee").attr("data-nominee");
  
  // Update the dialog action
  $( "#dialog-edit" ).attr("data-action", "edit");
  $( "#dialog-edit" ).attr("data-nominee", nomineeID);
  $( "#dialog-edit-header" ).text("Editing "+nominees[nomineeID].Name);

  // Grab all the relevant information
  $( ".dialog-edit-id" ).text(nomineeID);
  $( "#dialog-edit-name" ).text(nominees[nomineeID].Name);
  $( "#dialog-edit-subtitle" ).html(nominees[nomineeID].Subtitle);
  if (nominees[nomineeID].Image) {
    $( "#dialog-edit-image" ).attr("src", nominees[nomineeID].Image);
  } else {
    $( "#dialog-edit-image" ).attr("src", "/public/nominees/"+nomineeID+".png");
  }
  
  $( "#info-id" ).val(nomineeID);
  $( "#info-name" ).val(nominees[nomineeID].Name);
  $( "#info-subtitle" ).val(nominees[nomineeID].Subtitle);
  $( "#info-image" ).val(nominees[nomineeID].Image);
  
  $( "#info-id").attr("disabled", "disabled");

  // Show the dialog
  $( "#dialog-edit" ).modal("show");
});


// When the dialog is opened, start the monitor
$("#dialog-edit").on("show", function() {
  $( "#dialog-edit-status" ).hide();
  $( "#dialog-edit-submit" ).removeAttr("disabled");
  $( "#dialog-edit-error" ).parent().hide();
  
  lastImageValue = "";
  lastID = "";
  editDialogInterval = setInterval(updateDialogNominee, 200);
});

// When the dialog is closed, clear it
$("#dialog-edit").on("hide", function() {
  clearInterval(editDialogInterval);
});

// Update the information in the template nominee box
function updateDialogNominee() {
  $( ".dialog-edit-id" ).text( $( "#info-id" ).val() );
  $( "#dialog-edit-name" ).text( $( "#info-name" ).val() );
  $( "#dialog-edit-subtitle" ).html( $( "#info-subtitle" ).val() );
  
  if ($( "#info-image" ).val() != lastImageValue || (lastImageValue == "" && $("#info-id").val() != lastID)) {
    lastImageValue = $( "#info-image").val();
    lastID = $( "#info-id").val();
    if (lastImageValue) {
      $( "#dialog-edit-image" ).attr("src", lastImageValue );
    } else {
      $( "#dialog-edit-image" ).attr("src", "/public/nominees/"+$( "#info-id" ).val()+".png");
    }
  }

}

// When the confirm button is clicked in the edit dialog
$("#dialog-edit-submit").click(function() {
  if (currentlySubmitting) {
    return;
  }
  currentlySubmitting = true;
  
  // Show the "please wait" message and disable the submit button
  $( "#dialog-edit-status" ).show();
  $( "#dialog-edit-submit" ).attr("disabled", "disabled");
  $( "#dialog-edit-error" ).parent().slideUp();
  
  // Grab the nomineeID and action from the dialog
  var nomineeID = $("#dialog-edit").attr("data-nominee");
  var action = $("#dialog-edit").attr("data-action");
  
  // Send through the AJAX request to do the actual editing
  var ajaxData = $( "#dialog-edit-form" ).serializeArray();
  ajaxData.push({ name: "Action", value: action });
  ajaxData.push({ name: "Category", value: "<tag:category />" });
  if (action == "edit") {
    ajaxData.push({ name: "NomineeID", value: nomineeID });
  }
  $.post("/ajax-nominations", ajaxData, function(data) {
    currentlySubmitting = false;  
    
    if (data.success) {
    
      if (action == "new") {
      
        nomineeID = $("#info-id").val();
        var clone = $("#dialog-edit .aNominee").clone(true, true);
        clone.find("*").removeAttr("id");
        clone.removeAttr("style");
        clone.attr("id", "nominee"+nomineeID);
        clone.attr("data-nominee", nomineeID);
        clone.find("form").show();
        clone.find("header p").text("ID: "+nomineeID);
        
        $("#nominee-container").append(clone);
        
        nominees[nomineeID] = {};
        nominees[nomineeID].NomineeID = nomineeID;
        
        $("#official-count").text(parseInt($("#official-count").text()) + 1);
      
      }
      
      var nomineeBox = $("#nominee-"+nomineeID);
      nomineeBox.find("h3").text( $("#info-name").val() );
      nomineeBox.find("footer p").html( $("#info-subtitle").val() );
      if ( $("#info-image") ) {
        nomineeBox.find("img").attr("src", $("#info-image").val() );
      } else {
        nomineeBox.find("img").attr("src", "/public/nominees/"+nomineeID+".png");
      }
      
      nominees[nomineeID].Name = $("#info-name").val();
      nominees[nomineeID].Subtitle = $("#info-subtitle").val();
      nominees[nomineeID].Image = $("#info-image").val();

      // Close the dialog
      $( "#dialog-edit" ).modal("hide");
    } else {
      // Something went wrong! Show the error message.
      $( "#dialog-edit-status" ).hide();
      $("#dialog-edit-submit").removeAttr("disabled");
      $("#dialog-edit-error").html("<strong>Error:</strong> " + data.error);
      $("#dialog-edit-error").parent().fadeIn("fast");
    }
  }, "json");
});

$( "#show-more" ).click(function(e) {
  $( "#show-more" ).hide();
  $( "#more-nominations" ).show();
});

</script>
</if:categoryName>
</if:canEdit>