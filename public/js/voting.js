//var _gaq = _gaq || [];
//_gaq.push(['_setAccount', 'UA-36466872-1']);
//_gaq.push(['_trackPageview']);
//
//(function() {
//  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
//  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
//  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
//})();

var votingEnabled;
var previousLockExists = false;
var currentCategory;
var lastVotes = [null];
var votesChanged = false;

dumbshit = new Dumbshit();
dumbshit.code = function () {
    $(".shit").show();
    $("body").css("background-image", "url(/2015voting/bg2.gif)");
    $("body").css("background-repeat", "repeat");
	var audio = new Audio('/2015voting/woah.mp3');
	audio.play();
};
dumbshit.load();

// Position the popup at the center of the page
function positionPopup() {
    var overlay = $('#overlay');

    if (overlay.is(':visible')) {
        return;
    }
    overlay.css({
        left: ($(window).width() - overlay.width()) / 2,
        top: ($(window).width() - overlay.width()) / 7,
        position: 'absolute'
    });
}

// Keep the popup in the center when the browser is resized
$(window).bind('resize', positionPopup);

$(document).ready(function () {		
    var resetButton = $('#btnResetVotes');
    var cancelButton = $('#btnCancelVotes');
    var submitButton = $('#btnLockVotes');

    var voteColumnBoxes = $('#voteColumn').find('.voteBox');

    previousLockExists = lastVotes.length > 1;

    //empty voteBoxes
    voteColumnBoxes.each(function () {
        $(this).html("");
    });

    //global variables for future use
    var dragged;
    var draggedFrom;

    //set the height/width of the nomineeColumn depending on how many nominees there are
    $("#nomineeColumn").height($("#voteColumn").height());

    //be able to drag nominees
    if (votingEnabled) {
        $(".aNominee").draggable({
            containment: "#limitsDrag",
            distance: 20,
            opacity: 0.75,
            zIndex: 100,
            revert: "invalid",
            revertDuration: 200,
            start: function (event, ui) {
                voteColumnBoxes.addClass("dragging");
            },
            stop: function (event, ui) {
                voteColumnBoxes.removeClass("dragging");
            }
        })
        //when you start dragging, it puts the elements in variables
        .bind('dragstart', function (event) {
            dragged = $(this);
            draggedFrom = $(this).parent();

            //put their margins to 0
            //$(this).css("margin","0px 0px 0px 0px");
        });
    }

    //be able to drop nominees in voteBoxes
    voteColumnBoxes.droppable({
        drop: function (event, ui) {
            var dropped = ui.draggable;
            var droppedOn = $(this);

            //if you're dropping the nominee exactly where you took it from, it cancels the drop
            if (droppedOn.attr("id") == draggedFrom.attr("id")) {
                $(dragged).draggable("option", "revert", true);
                return
            }

            votesWereUnlocked();

            //put the content of the box you're voting over in a variable (.detach keeps the draggable)
            var stuffDeleted = droppedOn.contents().detach();

            //add your dragged vote to the box
            $(dropped).detach().css({top: 0, left: 0}).appendTo(droppedOn);

            //put what you deleted back where your vote came from
            draggedFrom.append(stuffDeleted);

            //put their margins back to normal
            //$(stuffDeleted).css("margin","10px 0 0 10px");

            updateNumbers();
        }
    });

    //be able to drop nominees back in the original container
    $("#nomineeColumn").find(".voteBox").droppable({
        drop: function (event, ui) {
            var dropped = ui.draggable;
            var droppedOn = $(this);

            //if you're dropping the nominee exactly where you took it from, it cancels the drop
            if (droppedOn.attr("id") == draggedFrom.attr("id")) {
                $(dragged).draggable("option", "revert", true);

                //put their margins back to normal
                //$(dropped).css("margin","10px 0 0 10px");

                return
            }

            votesWereUnlocked();

            //add your dragged vote to the container
            $(dropped).detach().css({top: 0, left: 0}).appendTo(droppedOn);

            //put their margins back to normal
            //$(dropped).css("margin","10px 0 0 10px");

            //empty the number
            dropped.find(".number").html("");
        }
    });

    //if you click on Reset Votes
    resetButton.click(function () {
        votesWereUnlocked();
        voteColumnBoxes.each(function () {
            //delete what's in every voteBox and put them back in the container on the left
            var stuffDeleted = $(this).contents().detach();
            for (var i = 0; i < stuffDeleted.length; i++) {
                $('#nomineeColumn').find('.voteBox:empty:first').append(stuffDeleted[i]);
            }
        });
        sortLeftSide();
        if (!previousLockExists) {
            $("#btnCancelVotes").hide();
        }
    });

    cancelButton.click(function () {
        moveNomineesBackToLastVotes();
        sortLeftSide();
    });

    //if you click on Lock Votes
    submitButton.click(function () {
        sortVotes();
        updateNumbers();
        votesWereLocked();

        var preferences = [null];

        voteColumnBoxes.each(function () {
            var onlyTheNumber = $(this).attr("id").replace(/[^0-9]/g, '');
            var nomineeID = $(this).find(".aNominee").attr("data-nominee");

            if (nomineeID != undefined) {
                preferences[onlyTheNumber] = nomineeID;
            }

        });

        console.log(preferences);

        lastVotes = preferences;

        $.post("/voting-submission", {Category: currentCategory, Preferences: preferences}, function (data) {
            console.log(data);
            if (data.error) {
                alert("An error occurred:\n" + data.error + "\nYour vote has not been saved.");
            } else {
                $("#" + currentCategory).addClass("complete");
            }
        }, "json");
    });
});

function sortVotes() {
    //variable that I'm using to know which voteBox the loop is at
    var currentVoteBox = 0;
    var listVoteBox = [];

    //pass through every voteBox, empty them while placing the vote in the array, ignoring the empty voteBoxes
    $("#voteColumn").find(".voteBox").each(function () {
        currentVoteBox++;

        if ($(this).contents().attr("id") != undefined) {
            listVoteBox.push($(this).contents().detach());
        }
    });

    //put the votes back in the voteBoxes
    for (var i = 0; i < currentVoteBox; i++) {
        if (listVoteBox[i]) { //if it exists
            listVoteBox[i].appendTo($("#voteBox" + (i + 1)));
        }
    }
}

function updateNumbers() {
    //for every voteBox, look at its ID, keep the number and show it in the nominee div
    $("#voteColumn").find(".voteBox").each(function () {
        var onlyTheNumber = $(this).attr("id").replace(/[^0-9]/g, '');
        $(this).find(".number").html("#" + onlyTheNumber);

        //put their margins back to normal
        //$(this).find(".aNominee").css("margin","0 0 0 0");
    });
}

function votesWereLocked() {
    $(".voteBox").addClass("locked");
    $(".aNominee").addClass("locked");
    $("#votesAreLocked").show();
    $("#votesAreNotLocked").hide();
    $("#btnCancelVotes").hide();
    previousLockExists = true;
    votesChanged = false;
    $(".navigation").show();
}

function votesWereUnlocked() {
    $(".voteBox").removeClass("locked");
    $(".aNominee").removeClass("locked");
    $("#votesAreLocked").hide();
    $("#votesAreNotLocked").show();
    $("#btnCancelVotes").show();
    votesChanged = true;
}

function moveNomineesBackToLastVotes() {
    var haveVotedFor = [];

    for (var i = 1; i < lastVotes.length; i++) {
        haveVotedFor.push($("#nominee-" + lastVotes[i]).detach());
    }

    var theRest = $(".aNominee").detach();

    for (i = 0; i < lastVotes.length; i++) {
        $("#voteBox" + (i + 1)).append(haveVotedFor[i]);
    }

    var voteBoxes = $("#nomineeColumn").find(".voteBox");

    console.log(voteBoxes);

    for (i = 0; i < theRest.length; i++) {
        $(voteBoxes[i + lastVotes.length - 1]).append(theRest[i]);
    }

    updateNumbers();

    if (previousLockExists) {
        votesWereLocked();
    }

    $("#btnCancelVotes").hide();
}

function sortLeftSide() {
    var muhNominees = $("#nomineeColumn").find(".aNominee").detach();

    muhNominees = $(muhNominees).sort(function (a, b) {
        var contentA = parseInt($(a).attr('data-order'));
        var contentB = parseInt($(b).attr('data-order'));
        return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
    });

    var voteBoxes = $('#nomineeColumn').find('.voteBox');

    for (var i = 0; i < muhNominees.length; i++) {
        $(voteBoxes[i]).append(muhNominees[i]);
    }
}

$(document).ready(function () {
    moveNomineesBackToLastVotes();
});
