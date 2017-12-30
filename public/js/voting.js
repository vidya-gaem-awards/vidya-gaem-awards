// OwO what's this?
onDumbShit(function () {
    $(".shit").show();
    $("html").css({
        'background-image': 'url(/2015voting/bg2.gif)',
        'background-repeat': 'repeat'
    });
    var audio = new Audio('/2015voting/woah.mp3');
    audio.play();
});

var dragCounter;
var toddDialog;

if (localStorage.getItem('dragCounter')) {
    dragCounter = localStorage.getItem('dragCounter');
}

function incrementDragCounter() {
    dragCounter++;

    localStorage.setItem('dragCounter', dragCounter);

    if (dragCounter === 2) {
        showTodd(1);
    } else if (dragCounter === 5) {
        showTodd(2);
    } else if (dragCounter === 10) {
        showTodd(3);
    } else if (dragCounter === 20) {
        showTodd(4);
    }
}

function showTodd(toddCounter) {
    window.navigator.vibrate(toddCounter * 500);

    var text = [
        'Winner of more than 200 Game of the Year Awards, Skyrim Special Edition brings the epic fantasy to life in stunning detail.',
        'What are doing, Anon? Buy my game.',
        '<strong>JUST FUCKING BUY IT!</strong>',
        '<em><strong>IT\'S NOT TOO LATE. PURCHASE CREATION CLUB CREDITS AND YOU WILL BE SPARED.</strong></em>'
    ];

    var prices = [
        39.95,
        59.95,
        99.95,
        299.95
    ];

    toddDialog.find('img').attr('src', '/img/todd' + toddCounter + '.jpg');
    toddDialog.find('.theprice').text(prices[toddCounter - 1]);
    toddDialog.find('.desc').html(text[toddCounter - 1]);

    if (toddCounter === 3) {
        toddDialog.find('.game_purchase_action').addClass('shake');
    } else if (toddCounter === 4) {
        toddDialog.find('.modal-content').addClass('shake');
    }

    toddDialog.modal('show');
    return true;
}

$(document).ready(function () {
    // If there's no award currently selected, none of this code is relevant.
    if (!currentAward) {
        return;
    }

    toddDialog = $('#todd');
    toddDialog.modal({
        show: false
    });
    toddDialog.find('.close').click(function () {
        toddDialog.modal('hide');
    });

    // Rev up those Todds
    (new Image()).src = '/img/todd1.jpg';
    (new Image()).src = '/img/todd2.jpg';
    (new Image()).src = '/img/todd3.jpg';
    (new Image()).src = '/img/todd4.jpg';

    var previousLockExists = lastVotes.length > 1;
    var votesChanged = false;
    var nomineeCount = $('.voteGroup').length;

    var resetButton = $('#btnResetVotes');
    var submitButton = $('#btnLockVotes');

    // Only used in the "drag from left to right" layout
    var nomineeColumn = $('#nomineeColumn');
    var voteColumn = $('#voteColumn');
    var voteColumnBoxes = voteColumn.find('.voteBox');
    var dragged;
    var draggedFrom;

    // Only used in the "drag from top to bottom" layout
    var topArea = $('#voteDropAreaTop');
    var bottomArea = $('#voteDropAreaBottom');

    // Only used in the "click to choose number" layout
    var numberPopup = $('#numberPopup');
    var preferenceButtons = $('.preferenceButton');

    // Only used in the "type in your number" layout
    var preferenceInputs = $('.preferenceInput');

    var sortableOptions = {
        group: {
            name: 'omega',
            pull: false
        },
        draggable: '.voteGroup',
        handle: '.handle',
        animation: 0,
        dataIdAttr: 'data-order',
        onStart: function (event) {
            $("#dragLimit").addClass("dragActive");
            $(event.item).find('.number').hide();
            // $(event.item).find('.number').show().text('Drop this nominee in your preferred position');
            setTimeout(incrementDragCounter, 100);
        },
        onEnd: function (event) {
            $("#dragLimit").removeClass("dragActive");
            updateNumbers();
            unlockVotes();
        },
        scroll: true,
        scrollSensitivity: 100,
        scrollSpeed: 20
    };

    var topSortableOptions = Object.assign({}, sortableOptions);
    topSortableOptions.sort = false;

    if (votingEnabled) {
        if (topArea.length > 0) {
            // new Sortable(document.getElementById('voteDropAreaTop'), topSortableOptions);
            new Sortable(document.getElementById('voteDropAreaBottom'), sortableOptions);
        }

        $('.voteBox').click(function (event) {
            if (!$.contains(topArea[0], this)) {
                $(this).parent().detach().appendTo(topArea);
            } else {
                $(this).parent().detach().appendTo(bottomArea);
            }

            updateNumbers();
            unlockVotes();
        });
    }

    // Legacy
    voteColumnBoxes.each(function () {
        $(this).html("");
    });

    nomineeColumn.height(voteColumn.height());

    $(".aNominee").draggable({
        containment: "#limitsDrag",
        distance: 20,
        opacity: 0.75,
        zIndex: 100,
        revert: "invalid",
        revertDuration: 200,
        start: function (event, ui) {
            voteColumnBoxes.addClass("dragging");
            setTimeout(incrementDragCounter, 100);
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

    //be able to drop nominees in voteBoxes
    voteColumnBoxes.droppable({
        drop: function (event, ui) {
            var dropped = ui.draggable;
            var droppedOn = $(this);

            //if you're dropping the nominee exactly where you took it from, it cancels the drop
            if (droppedOn.attr("id") === draggedFrom.attr("id")) {
                $(dragged).draggable("option", "revert", true);
                return
            }

            unlockVotes();

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
    nomineeColumn.find(".voteBox").droppable({
        drop: function (event, ui) {
            var dropped = ui.draggable;
            var droppedOn = $(this);

            //if you're dropping the nominee exactly where you took it from, it cancels the drop
            if (droppedOn.attr("id") === draggedFrom.attr("id")) {
                $(dragged).draggable("option", "revert", true);
                return;
            }

            unlockVotes();

            //add your dragged vote to the container
            $(dropped).detach().css({top: 0, left: 0}).appendTo(droppedOn);

            //empty the number
            dropped.find(".number").html("");
        }
    });

    moveNomineesBack(false);

    // Update interface to indicate that the votes have been succesfully submitted and changed.
    function lockVotes() {
        $(".voteBox").addClass("locked");
        $(".aNominee").addClass("locked");

        bottomArea.addClass("locked");
        submitButton.addClass('iVoted').attr('title', 'Saved!');
        previousLockExists = true;
        votesChanged = false;
        $(".navigation").show();
    }

    // Update interface to indicate that there have been changes since the last submitted vote.
    function unlockVotes() {
        $(".voteBox").removeClass("locked");
        $(".aNominee").removeClass("locked");

        bottomArea.removeClass("locked");
        submitButton.removeClass('iVoted').attr('title', 'Submit Votes');
        votesChanged = true;
    }

    // Checks for duplicate preferences, and marks any duplicates as invalid.
    // Only used in the "type in your number" layout.
    function checkForDuplicates() {
        var allNumbers = {};
        preferenceInputs.each(function () {
            var element = $(this);
            var number = element.val();

            element.removeClass('invalid');
            if (number === "") {
                return;
            }
            if (!allNumbers[number]) {
                allNumbers[number] = [];
            }
            allNumbers[number].push(element);
        });

        $.each(allNumbers, function (index, elements) {
            if (elements.length > 1) {
                $.each(elements, function (index, element) {
                    element.addClass('invalid');
                });
            }
        });
    }

    function getOrdinal(number) {
        return number + (['st', 'nd', 'rd'][((number+90) % 100 - 10) % 10 - 1] || 'th');
    }

    // Updates the preference numbers displayed on each nominee in the bottom pane.
    // Only used in the "drag and drop" layout.
    function updateNumbers() {
        bottomArea.find(".voteGroup").each(function (index) {
            index = index + 1;
            var text = 'Your ' + getOrdinal(index) + ' preference';
            $(this).find(".number").show().html(text);
        });

        var boxesInBottom = bottomArea.find(".voteGroup").length;
        $('.nextPreference').text(getOrdinal(boxesInBottom + 1));

        voteColumnBoxes.each(function () {
            var onlyTheNumber = $(this).attr("id").replace(/[^0-9]/g, '');
            $(this).find(".number").html("#" + onlyTheNumber);
        });

        topArea.find(".number").hide();
    }

    // Resets all nominees back to the user's last submitted vote.
    // If there is no last submitted vote, or if resetAll is set to true, will move all nominees back into the top pane.
    function moveNomineesBack(resetAll) {
        if (votingStyle === 'legacy') {
            var voteBoxes = $("#nomineeColumn").find(".voteBox");
            if (resetAll || lastVotes.length === 0) {
                var allNominees = [];
                $('.aNominee').each(function () {
                    allNominees.push($(this).detach());
                });

                for (i = 0; i < allNominees.length; i++) {
                    $(voteBoxes[i]).append(allNominees[i]);
                }
            } else {
                var haveVotedFor = [];
                for (i = 1; i < lastVotes.length; i++) {
                    haveVotedFor.push($("#nominee-" + lastVotes[i]).detach());
                }
                var theRest = $(".aNominee").detach();
                for (i = 0; i < lastVotes.length; i++) {
                    $("#voteBox" + (i + 1)).append(haveVotedFor[i]);
                }
                for (i = 0; i < theRest.length; i++) {
                    $(voteBoxes[i + lastVotes.length - 1]).append(theRest[i]);
                }
            }
            resetLeftSide();

        } else {
            bottomArea.find('.voteGroup').each(function () {
                var element = $(this);
                element.detach().appendTo(topArea);
            });

            if (!resetAll) {
                for (var i = 1; i < lastVotes.length; i++) {
                    var element = $("#nominee-" + lastVotes[i]);
                    element.detach().appendTo(bottomArea);
                }
            }
            resetTopArea();
        }

        updateNumbers();

        if (previousLockExists) {
            lockVotes();
        }
    }

    function resetLeftSide() {
        var muhNominees = nomineeColumn.find(".aNominee").detach();

        muhNominees = $(muhNominees).sort(function (a, b) {
            var contentA = parseInt($(a).attr('data-order'));
            var contentB = parseInt($(b).attr('data-order'));
            return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
        });

        var voteBoxes = nomineeColumn.find('.voteBox');

        for (var i = 0; i < muhNominees.length; i++) {
            $(voteBoxes[i]).append(muhNominees[i]);
        }
    }

    function sortRightSide() {
        //variable that I'm using to know which voteBox the loop is at
        var currentVoteBox = 0;
        var listVoteBox = [];

        //pass through every voteBox, empty them while placing the vote in the array, ignoring the empty voteBoxes
        voteColumnBoxes.each(function () {
            currentVoteBox++;

            if ($(this).contents().attr("id") !== undefined) {
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

    // Resets the top nominee pane to its original configuration.
    // Only used in the "drag and drop" layout.
    function resetTopArea() {
        var nominees = topArea.find(".voteGroup").detach();

        nominees = $(nominees).sort(function (a, b) {
            var contentA = parseInt($(a).attr('data-order'));
            var contentB = parseInt($(b).attr('data-order'));
            return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
        });

        topArea.append(nominees);
    }

    // Checks for invalid preferences when an input is unfocused.
    // Only used in the "type in your preference" layout.
    preferenceInputs.blur(function () {
        var element = $(this);
        var number = element.val();

        if (number > nomineeCount) {
            element.addClass('static');
            element.val('');
            setTimeout(function () {
                element.val(nomineeCount);
                element.removeClass('static');
                checkForDuplicates();
            }, 300);
        } else if (element.is(':invalid')) {
            element.addClass('static');
            element.val('');
            setTimeout(function () {
                element.removeClass('static');
                checkForDuplicates();
            }, 300);
        }
    });

    // Checks for duplicate preferences when an input is changed.
    // Only used in the "type in your preference" layout.
    preferenceInputs.change(function () {
        checkForDuplicates();
    });

    // Shows the number popup upon clicking the preference square.
    // Only used in the "click to choose number" layout.
    preferenceButtons.click(function () {
        var element = $(this);

        numberPopup.show();
        numberPopup.attr('data-nominee', element.parent('.voteGroup').attr('data-nominee'));
        numberPopup.css('left', element.offset().left);
        numberPopup.css('top', element.offset().top - element.outerHeight());
    });

    // Handle the selection of a preference from the popup dialog.
    // Only used in the "click to choose number" layout.
    numberPopup.find('button').click(function () {
        var element = $(this);
        var nominee = numberPopup.attr('data-nominee');
        var button = $('#nominee-' + nominee).find('.preferenceButton');
        var value = element.attr('data-value');

        var currentSelection = button.attr('data-value');
        if (currentSelection !== undefined) {
            $('#numberButton' + currentSelection).removeAttr('disabled');
        }

        button.text(value);
        button.attr('data-value', value);
        element.attr('disabled', 'disabled');
        numberPopup.hide();
    });

    // Reset Votes
    resetButton.click(function () {
        unlockVotes();
        moveNomineesBack(true);
        resetTopArea();
        resetLeftSide();

        $('.voteDropArea').addClass('flash');
        setTimeout(function () {
            $('.voteDropArea').removeClass('flash');
        }, 200);
    });

    // Submit Votes
    submitButton.click(function () {
        lockVotes();
        if (votingStyle === 'legacy') {
            sortRightSide();
            updateNumbers();
        }

        var preferences = [null];

        if (votingStyle === 'legacy') {
            voteColumnBoxes.each(function () {
                var onlyTheNumber = $(this).attr("id").replace(/[^0-9]/g, '');
                var nomineeID = $(this).find(".aNominee").attr("data-nominee");
                if (nomineeID !== undefined) {
                    preferences[onlyTheNumber] = nomineeID;
                }
            });
        } else {
            bottomArea.find('.voteGroup').each(function (index) {
                preferences[index + 1] = $(this).attr('data-nominee');
            });
        }

        lastVotes = preferences;

        $.post(postURL, {preferences: preferences}, function (data) {
            if (data.error) {
                alert("An error occurred:\n" + data.error + "\nYour vote has not been saved.");
            } else {
                $('#' + currentAward).addClass('complete');
            }
        }, 'json');
    });
});
