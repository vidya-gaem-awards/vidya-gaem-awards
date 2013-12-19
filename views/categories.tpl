<style>
    .ui-autocomplete {
        max-height: 300px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
        /* add padding to account for vertical scrollbar */
    }
    #video-games img:hover {
        box-shadow: 0 0 4px black;
    }

</style>

<if:CATEGORY_VOTING_ENABLED>
<header class="jumbotron subhead">
<h1>Awards and Nominations</h1>
</header>
</if:CATEGORY_VOTING_ENABLED>

<if:adminTools>
<ul class="breadcrumb">
    <li class="active">Admin tools:
        <loop:adminTools>
        <span class="divider">/</span></li>
        <li><a href="<tag:adminTools[].Link />"><tag:adminTools[].Text /></a>
        </loop:adminTools>
    </li>
</ul>
</if:adminTools>

<!if:CATEGORY_VOTING_ENABLED>
<header class="jumbotron subhead" style="text-align: center;">
<h1>Award Nominations</h1>
<p>We use user nominations to decide which nominees get officially chosen for the voting stage.</p>
<p>You can make as many nominations as you want for each category below, and unlike previous years, you don't need to log in with Steam.</p>
</header>
<hr>
</!if:CATEGORY_VOTING_ENABLED>

<div class="row">
    
    <div class="span5" id="category-selector">
        <ul class="nav nav-list custom-navigation-pane">
            <loop:categories> 
            <li data-id="<tag:categories[].ID />">
                <a href="#<tag:categories[].ID />">
                    <i class="icon-chevron-right"></i>
                    <span id="opinion-icon"><tag:categories[].OpinionIcon /></span> <strong><tag:categories[].Name /></strong> <tag:categories[].Subtitle />
                </a>
            </li>
            </loop:categories>
        </ul>
    </div>

    <div class="span7" id="video-games">

        <a href="/video-games">
            <img src="/public/collage.jpg">
        </a>

    </div>

    <div class="span7" id="category-info" style="display: none;">
        
        <div data-spy="affix" data-offset-top="<if:adminTools>130<else:adminTools>100</if:adminTools>">
        
            <if:CATEGORY_VOTING_ENABLED>
            <div class="well" style="text-align: center;">
        
                <div id="category-name" style="background-color: #08C; color: white; font-size: 20px; line-height: 1.5em"></div>
                <div id="category-subtitle" style="letter-spacing: 2px; font-style: italic; margin-bottom: 20px;"></div>
                
                <p id="description"></p>

                <div style="font-size: 200%; color: grey; line-height: 39px; display: none;">
                    saving...
                </div>
                <div class="btn-group" id="category-voting">
                    <a class="btn btn-large" id="thumbs-down" href="#">This category is shit</a>
                    <a class="btn btn-large" id="thumbs-up" href="#">This category is good</a>
                </div>
                
            </div>
            <else:CATEGORY_VOTING_ENABLED>

            <div id="category-name" class="impressive-title"></div>
            <div id="category-subtitle" class="impressive-subtitle"></div>
            
            <p id="description" style="text-align: center;"></p>
            </if:CATEGORY_VOTING_ENABLED>
            
            <div class="well">
                
                <div style="background-color: #08C; color: white; font-size: 20px; line-height: 1.5em; text-align: center; margin-bottom: 10px;">
                    Your Nominations for this Category: <span id="user-nomination-count">0</span>
                </div>
        
                <div id="nomination-section">
                <if:allowedToNominate>
                <pre id="user-nomination-list"></pre>
            
                <form class="form-inline" style="text-align: center;" id="nomination-form">
                    <p>
                        You aren't just restricted to the entries in the dropdown! They are suggestions only.
                    </p>
                    <div class="input-prepend input-append">
                        <span class="add-on"><label for="tags"><strong>Add a nomination:</strong></label></span>
                        <input id="tags" name="nomination" type="text" class="span3" required />
                        <button class="btn" type="submit">Submit</button>
                    </div>
                    <span class="help-block" style="font-style: italic;" id="autocomplete"></span>
                </form>
                
                <p id="nomination-status" style="font-weight: bold; text-align: center; font-size: 120%;"></p>
                
                <else:allowedToNominate>
                <pre>You need to be logged in to add nominations.</pre>
                </if:allowedToNominate>
                </div>

                <div id="nominations-closed" style="display: none; text-align: center;">
                Nominations for this category have been closed.
                </div>

            </div>
        
        </div>
    </div>

</div>

<script>
var categories = <tag:categoryJavascript />;
var autocompleters = <tag:autocompleteJavascript />;

var category = false;

function updateNominations() {
    $('#user-nomination-count').html(categories[category]['UserNominations'].length);
    if (categories[category]['UserNominations'].length == 0) {
        $('#user-nomination-list').html("You haven't nominated anything in this category.");
    } else {
        $('#user-nomination-list').html(categories[category]['UserNominations'].join(', '));
    }
}

$('#category-selector li').click(function(event) {

    $("#video-games").hide();

    var thus = this;

    $('#category-info').fadeOut('fast', function() {

        category = $(thus).attr('data-id');
        $('#category-selector li').removeClass("active");
        $(thus).addClass("active");
        
        $('#category-info #category-name').html(categories[category]['Name']);
        $('#category-info #category-subtitle').html(categories[category]['Subtitle']);
        $('#category-info #description').html(categories[category]['Description']);
        if (categories[category]['Nominations']) {
            $('#nomination-section').show();
            $('#nominations-closed').hide();
        } else {
            $('#nomination-section').hide();
            $('#nominations-closed').show();
        }
        $('#category-voting a').removeClass("btn-success btn-danger disabled");
        if (categories[category]['Opinion'] == 1) {
            $('#thumbs-up').addClass("disabled btn-success");
        } else if (categories[category]['Opinion'] == -1) {
            $('#thumbs-down').addClass("disabled btn-danger");
        }
        
        <if:allowedToNominate>
        updateNominations(category);
        //</if:allowedToNominate>
        
        $('#category-info').fadeIn('fast');
        $('#nomination-status').hide();
        
        $('html').scroll();
        
        $("#tags").val("");
        
        var autocompleteCat = categories[category]['Autocomplete'];
        if (autocompleteCat == "video-game") {
            $('#autocomplete').html("This category provides suggestions using a list of video games");
        } else if (autocompleteCat == category) {
            $('#autocomplete').html("This category provides suggestions based on nominations from other users");
        } else {
            $('#autocomplete').html("This category provides suggestions from a pre-defined list");
        }
        
        $( "#tags" ).autocomplete({
            source: autocompleters[autocompleteCat],
            minLength: 2,
            delay: 0,
        });
        
        $("#tags").focus();
        
    });
});

$('#category-voting a').click(function(event) {
    event.preventDefault();
    
    var selected = event.currentTarget;
    var opinion;
    var opposite;
    
    if ($(selected).hasClass("disabled")) {
        opinion = 0;
        opposite = selected;
    } else if (selected.id == "thumbs-down") {
        opinion = -1;
        opposite = $("#thumbs-up");
    } else if (selected.id == "thumbs-up") {
        opinion = 1;
        opposite = $("#thumbs-down");
    }
    
    $(selected).parent().hide();
    $(selected).parent().prev().show();
    
    var formCategory = category;
    
    $.post("/ajax-category-feedback", { ID: category, opinion: opinion }, function(data) {
        if (data.success) {
        
            var icon = $('[data-id="'+formCategory+'"] #opinion-icon');
            if (opinion == -1) {
                if (formCategory == category) {
                    $(selected).addClass("btn-danger");
                    $(opposite).removeClass("btn-success");
                }
                $(icon).html("&#x2718;");
            } else if (opinion == 1) {
                if (formCategory == category) {
                    $(selected).addClass("btn-success");
                    $(opposite).removeClass("btn-danger");
                }
                $(icon).html("&#x2714;");
            } else {
                if (formCategory == category) {
                    $(selected).removeClass("btn-success btn-danger");
                }
                $(icon).html("");
            }
            if (formCategory == category) {
                $(selected).addClass("disabled");
                $(opposite).removeClass("disabled");
            }
            categories[formCategory]['Opinion'] = opinion;

        } else {
            alert("An error occurred: "+data.error);
        }
        $(selected).parent().show();
        $(selected).parent().prev().hide();
    }, "json");
});

var currentlySubmitting = false;

$('#nomination-form').submit(function(event) {
    event.preventDefault();
    
    if (currentlySubmitting) {
        return;
    }
    
    currentlySubmitting = true;
    var formCategory = category;
    
    $("#nomination-status").show();
    $("#nomination-status").html("Submitting nomination...");
    
    var value = $('#tags').val();
    
    $.ajax({
        type: "POST",
        url: "/nomination-submit",
        data: { "Category": category, "Nomination": value },
        success: function(data) {
            if (data == "success") {
                safeValue = $('<div/>').text($.trim(value)).html();
                categories[formCategory]['UserNominations'].push(safeValue);
                $('#tags').val("");
                $('#tags').autocomplete("close");
                if (formCategory == category) {
                    updateNominations();
                    $("#nomination-status").html("<span style='color: green;'>Success!</span>");
                    $("#nomination-status").fadeOut(3000);
                }
                var icon = $('[data-id="'+formCategory+'"] #opinion-icon');
                icon.text("["+categories[formCategory]['UserNominations'].length+"]");
            } else {
                if (formCategory == category) {
                    if (data == "blank nomination") {
                        $("#nomination-status").html("<span style='color: red;'>Nomination cannot be blank.</span>");
                    } else if (data == "already exists") {
                        $("#nomination-status").html("<span style='color: red;'>You've already nominated that.</span>");
                    } else {
                        $("#nomination-status").hide();
                    }
                }
            }
            currentlySubmitting = false;
        },
        error: function(xhr, textStatus, error) {
            currentlySubmitting = false;
            $("#nomination-status").html("<span style='color: red;'>HTTP error: "+xhr.status+" "+xhr.statusText+". Try again.</span>");
            console.log(xhr);
        }
    });
    
    //$('#tags').val("");
    //$('#tags').autocomplete("close");
});

if (window.location.hash) {
    var selected = window.location.hash.slice(1);
    $('[data-id="'+selected+'"]').click();
}
    
    
</script>