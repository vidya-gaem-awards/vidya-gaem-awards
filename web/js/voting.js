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

$(document).ready(function () {
    // If there's no award currently selected, none of this code is relevant.
    if (!currentAward) {
        return;
    }

    var previousLockExists = lastVotes.length > 1;
    var votesChanged = false;
    var nomineeCount = $('.voteGroup').length;

    var resetButton = $('#btnResetVotes');
    var submitButton = $('#btnLockVotes');

    // Only used in the "drag from top to bottom" layout
    var topArea = $('#voteDropAreaTop');
    var bottomArea = $('#voteDropAreaBottom');

    // Only used in the "click to choose number" layout
    var numberPopup = $('#numberPopup');
    var nomineeSquares = $('.inputBox');

    // Only used in the "type in your number" layout
    var preferenceInputs = $('.preferenceInput');

    var sortableOptions = {
        group: 'omega',
        draggable: '.voteGroup',
        handle: '.handle',
        animation: 100,
        dataIdAttr: 'data-order',
        onStart: function (event) {
            $("#dragLimit").addClass("dragActive");
            $(event.item).find('.number').show().text('Drop this nominee in your preferred position');
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

    new Sortable(document.getElementById('voteDropAreaTop'), sortableOptions);
    new Sortable(document.getElementById('voteDropAreaBottom'), sortableOptions);

    moveNomineesBackToLastVotes();

    // Update interface to indicate that the votes have been succesfully submitted and changed.
    function lockVotes() {
        bottomArea.addClass("locked");
        submitButton.addClass('iVoted').attr('title', 'Saved!');
        previousLockExists = true;
        votesChanged = false;
        $(".navigation").show();
    }

    // Update interface to indicate that there have been changes since the last submitted vote.
    function unlockVotes() {
        bottomArea.removeClass("locked");
        submitButton.removeClass('iVoted').attr('title', 'Submit Votes');
        votesChanged = true;
    }

    // Checks for duplicate preferences, and marks any duplicates as invalid.
    // Only used in the "type in your number" layout.
    function checkForDuplicates() {
        var allNumbers = {};
        $('.inputBox').each(function () {
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

    // Updates the preference numbers displayed on each nominee in the bottom pane.
    // Only used in the "drag and drop" layout.
    function updateNumbers() {
        bottomArea.find(".voteGroup").each(function (index) {
            index = index + 1;
            var ordinal = ['st', 'nd', 'rd'][((index+90) % 100 - 10) % 10 - 1] || 'th';
            var text = 'Your ' + index + ordinal + ' preference';
            if (index === 1) {
                text = text + ' (the one you want to win)';
            }
            $(this).find(".number").show().html(text);
        });

        topArea.find(".number").hide();
    }

    // Resets all nominees back to the user's last submitted vote.
    // If there is no last submitted vote, will move all nominees back into the top pane.
    // Only used in the "drag and drop" layout.
    function moveNomineesBackToLastVotes() {
        bottomArea.find('.voteGroup').each(function () {
            var element = $(this);
            element.detach().appendTo(topArea);
        });

        for (var i = 1; i < lastVotes.length; i++) {
            var element = $("#nominee-" + lastVotes[i]);
            element.detach().appendTo(bottomArea);
        }

        updateNumbers();

        if (previousLockExists) {
            lockVotes();
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
    nomineeSquares.click(function () {
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
        var button = $('#nominee-' + nominee).find('.inputBox');
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
        moveNomineesBackToLastVotes();
        resetTopArea();

        $('.voteDropArea').addClass('flash');
        setTimeout(function () {
            $('.voteDropArea').removeClass('flash');
        }, 200);
    });

    // Submit Votes
    submitButton.click(function () {
        lockVotes();
        var preferences = [null];

        bottomArea.find('.voteGroup').each(function (index) {
            preferences[index + 1] = $(this).attr('data-nominee');
        });

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
