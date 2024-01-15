import './styles/voting.scss';
const $ = require('jquery');
require('jquery-ui/ui/widgets/draggable');
require('jquery-ui/ui/widgets/droppable');
require('bootstrap');
import Sortable from "sortablejs";
import './styles/captchas.scss';
import Captcha, {CaptchaProps, CaptchaUserSettings, renderCaptcha} from "./components/Captcha";
import captcha from "./components/Captcha";

declare var votingEnabled: boolean;
declare var lastVotes: any;
declare var currentAward: string;
declare var postURL: string;
declare var votingStyle: any;
declare const lootboxSettings: any;
declare const lootboxTiers: any;
declare const rewards: any;
declare const knownItems: any;
declare const captchaSettings: CaptchaProps;

let previousLockExists = lastVotes.length > 1;
let dragCounter: any;
let toddDialog: any;
let showRewardsOnSubmit: boolean = false;
let music: HTMLAudioElement;
let canPlayAudio: boolean;
let usingWinamp: boolean = false;
let captchaPendingCallback: (() => void | undefined);
let captchaShownThisLoad: boolean = false;

for (const item of knownItems) {
    rewards[item.shortName] = item;
}

var val;
$('#debugWidth').change(function () {
    val = $(this).val();
//                $('.nominee').css('width', val);
});
$('#debugHeight').change(function () {
    val = $(this).val();
    $('.nominee').css('height', val);
});
$('#debugTitle').change(function () {
    val = $(this).val();
    $('.nomineeName').css('fontSize', val);
});
$('#debugSubtitle').change(function () {
    val = $(this).val();
    $('.nomineeSubtitle').css('fontSize', val);
});

function onDumbShit(cb) {
    var input = '';
    var key = '38384040373937396665';
    document.addEventListener('keydown', function (e) {
        input += ("" + e.keyCode);
        if (input === key) {
            return cb();
        }
        if (!key.indexOf(input)) return;
        input = ("" + e.keyCode);
    });
}


// OwO what's this?
onDumbShit(function () {
    var audio = new Audio('/ogg/tf2.ogg');
    audio.play();

    $('#cheat-code').show();
});

if (localStorage.getItem('dragCounter')) {
    dragCounter = localStorage.getItem('dragCounter');
    if (isNaN(dragCounter)) {
        localStorage.setItem('dragCounter', JSON.stringify(0));
        dragCounter = 0;
    } else {
        dragCounter = JSON.parse(dragCounter);
    }
}

if (votingEnabled && !localStorage.getItem('characterName')) {
    $('#character').modal('show');
}

const captchaUserSettings: CaptchaUserSettings = {
  showTitles: localStorage.getItem('captcha-showTitles') === 'true',
  maxThreeFailures: localStorage.getItem('captcha-maxThreeFailures') === 'true',
  neverShowAgain: localStorage.getItem('captcha-neverShowAgain') === 'true',
  allowPartialPass: localStorage.getItem('captcha-allowPartialPass') === 'true',
  completions: localStorage.getItem('captcha-completions') ? JSON.parse(localStorage.getItem('captcha-completions')) : 0,
};

let showCaptcha = votingEnabled && !previousLockExists && !captchaUserSettings.neverShowAgain && lootboxSettings.captchaLimit > captchaUserSettings.completions;

function updateCharacterNameDisplay() {
  let characterName = localStorage.getItem('characterName');
  if (!characterName) {
    characterName = 'Anonymous';
  }
  $('#containerInventory .title-text').text(characterName + "'s Inventory");
}

updateCharacterNameDisplay();

$('#character-name-input')
  .on('keyup', handleCharacterNameChange)
  .on('change', handleCharacterNameChange);

function handleCharacterNameChange(event: JQuery.ChangeEvent|JQuery.KeyUpEvent) {
  const name = event.target.value.trim();
  const submit = $('#character-form').find('[type=submit]');

  let canSubmit = true;

  if (name.toLowerCase() === 'changed later') {
    $(event.target).addClass('is-invalid');
    canSubmit = false;
  } else if (name.toLowerCase() === 'wisely') {
    $(event.target).addClass('is-valid');
  } else {
    $(event.target).removeClass('is-invalid');
    $(event.target).removeClass('is-valid');
  }

  if (canSubmit) {
    submit.removeAttr('disabled');
  } else {
    submit.attr('disabled', 'disabled');
  }

  $('#character-name-count').text(name.length + '/20');
}

$('#character-form').on('submit', (event: JQuery.Event) => {
  event.preventDefault();
  let name = $('#character-name-input').val();
  if (name.length === 0) {
    name = 'Anonymous';
  }

  localStorage.setItem('characterName', name);
  updateCharacterNameDisplay();
  $('#character').modal('hide');

  $.post('/rpg/name', { name });
});

function incrementDragCounter() {
    dragCounter++;

    localStorage.setItem('dragCounter', JSON.stringify(dragCounter));

    if (dragCounter === 2) {
        // showTodd(1);
    } else if (dragCounter === 5) {
        // showTodd(2);
    } else if (dragCounter === 10) {
        // showTodd(3);
    } else if (dragCounter === 20) {
        // showTodd(4);
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

function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min)) + min; //The maximum is exclusive and the minimum is inclusive
}

function reset() {
    localStorage.removeItem('ignoreRewards');
    localStorage.removeItem('dragCounter');
}

function resetCSS() {
    if (localStorage.getItem('activeCSS')) {
        $('html').removeClass('reward-' + localStorage.getItem('activeCSS'));
        localStorage.removeItem('activeCSS');
    }

    $('.item-css').removeClass('active');
}

function resetMusic() {
    if (music) {
        music.pause();
        music.currentTime = 0;
    }
    if (localStorage.getItem('activeMusic')) {
        localStorage.removeItem('activeMusic');
    }

    if (webamp) {
        webamp.stop();
        webamp.close();
    }

    $('.item-music').removeClass('active');
}

function resetBuddie() {
    $('#reward-buddie').hide();
    if (localStorage.getItem('activeBuddie')) {
        localStorage.removeItem('activeBuddie');
    }

    $('.item-buddie').removeClass('active');
}

function resetRewards() {
    resetCSS();
    resetMusic();
    resetBuddie();
}

function getTextNodesIn(el) {
    return $(el).find(":not(iframe)").addBack().contents().filter(function() {
        return this.nodeType === 3;
    });
}

declare interface String {
    shuffle() : string;
}

function shuffleString(string) {
    var a = string.split(""),
        n = a.length;

    for(var i = n - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var tmp = a[i];
        a[i] = a[j];
        a[j] = tmp;
    }
    return a.join("");
};

function shuffleTextNodes(el) {
    var nodes = getTextNodesIn(el);
    $.each(nodes, function () {
        this.textContent = shuffleString(this.textContent);
    });
}

let webamp;

declare global {
    interface Window {
        cheat(code: string): void;
        captchaReact: Captcha;
    }
}

jQuery(function () {
    // If there's no award currently selected, none of this code is relevant.
    if (!currentAward) {
        return;
    }

    canPlayAudio = new Audio().canPlayType('audio/ogg') !== '';

    // Lootbox
    if (!localStorage.getItem('inventory')) {
        localStorage.setItem('inventory', JSON.stringify({
            'version': 3,
            'shekels': 0,
            'unlocks': [],
            'unlockKeys': []
        }));
    }

    inventory = JSON.parse(localStorage.getItem('inventory'));
    var lootboxCost = lootboxSettings.cost;

    $('#lootboxCostText').text(lootboxCost);

    var inventory;

    function updateInventory() {
        // if (inventory.version === undefined) {
        //     inventory.version = 2;
        //     var temp = inventory.unlocks;
        //     inventory.unlocks = {};
        //     inventory.unlockKeys = [];
        //     for (var i = 0; i < temp.length; i++) {
        //         inventory.unlocks[temp[i].id] = 1;
        //         inventory.unlockKeys.push(temp[i].id);
        //     }
        // }

        localStorage.setItem('inventory', JSON.stringify(inventory));
        $('#shekelCount').find('.item-name').text(inventory['shekels'] + ' gold');

        let captchaScoresJson = localStorage.getItem('captcha-scores');
        let captchaScores: number[];
        if (captchaScoresJson) {
          captchaScores = JSON.parse(captchaScoresJson);
        } else {
          captchaScores = [];
        }

        const total = captchaScores.reduce((carry, score) => {
          let xp = 1;
          if (score === 1) {
            xp += 3;
          } else if (score === 2) {
            xp += 5;
          } else if (score === 3) {
            xp += 7;
          }
          return carry + xp;
        }, 0);

        const totalScore = captchaScores.reduce((a, b) => a + b, 0);
        const average = captchaScores.length > 0 ? totalScore / captchaScores.length : 0;

        $('#captchaCount').toggle(captchaScores.length > 0);
        $('#captcha-xp').text(total);
        $('#captcha-xp-score').text(average.toFixed(2));

        if (inventory['shekels'] >= lootboxCost) {
            $('#buy-lootbox').removeAttr('disabled');
        } else {
            $('#buy-lootbox').attr('disabled', 'disabled');
        }

        $('.kebab').remove();

        for (var i = 0; i < inventory.unlockKeys.length; i++) {
            var reward = rewards[inventory.unlockKeys[i]];
            if (reward === undefined) {
                console.error('Reward is undefined!', i, inventory.unlockKeys[i]);
                continue;
            }
            var quantity = inventory.unlocks[reward.shortName];
            var element = $('#item-template').clone();
            element.addClass('kebab');
            element.find('img').attr('src', reward.image.url).attr('id', 'reward-image-' + reward.shortName);
            element.find('.item-name').text(reward.name);
            element.find('.item-quantity').text('x ' + quantity);
            element.find('.item-year').text(reward.year + ' item');
            element.find('.item-button').show().attr('data-id', reward.shortName);

            if (reward.shortName === 'nothing') {
                element.find('.item-buddie').text('Equip');
            }

            if (!reward.music) {
                element.find('.item-music').remove();
            }

            if (!reward.css) {
                element.find('.item-css').remove();
            }

            if (localStorage.getItem('activeCSS') === reward.shortName) {
                element.find('.item-css').addClass('active');
            }

            if (localStorage.getItem('activeMusic') === reward.shortName) {
                element.find('.item-music').addClass('active');
            }

            if (localStorage.getItem('activeBuddie') === reward.shortName) {
                element.find('.item-buddie').addClass('active');
            }

            var tier = lootboxTiers[reward.tier];
            // element.css('borderColor', tier.color);
            if (reward.shortName === 'nothing') {
                element.find('.item-buddie').text('Equip');
                element.find('.item-tier').css('backgroundColor', 'black');
            } else {
                element.find('.item-tier').css('backgroundColor', tier.color);
            }

            if (reward.extra) {
                element.addClass('encrypted');
            }

            element.show();
            element.appendTo('#inventory .inventory-container');
        }
    }

    // if (rewards['nothing']) {
    //     addRewardToInventory('nothing');
    // }
    updateInventory();

    $('#shekelCount').show();

    function onCaptchaCompletion(score: number|null) {
        if (captchaPendingCallback) {
            captchaPendingCallback();
            captchaPendingCallback = undefined;
        }

        if (score !== null) {
            const completions = localStorage.getItem('captcha-completions');
            if (completions) {
              localStorage.setItem('captcha-completions', JSON.stringify(JSON.parse(completions) + 1));
            } else {
              localStorage.setItem('captcha-completions', JSON.stringify(1));
            }

            let scores: any = localStorage.getItem('captcha-scores');
            if (scores) {
                scores = JSON.parse(scores);
            } else {
                scores = [];
            }

            scores.push(score);
            localStorage.setItem('captcha-scores', JSON.stringify(scores));

            updateInventory();
        }
    }

    if (localStorage.getItem('activeCSS')) {
        $('html').addClass('reward-' + localStorage.getItem('activeCSS'));
        $('.item-css[data-id=' + localStorage.getItem('activeCSS') + ']').addClass('active');
    }

    function tryToPlayMusicAutomatically() {
        playMusic(localStorage.getItem('activeMusic'), true).catch(() => {
            let clickHandler = (event: JQuery.ClickEvent) => {
                $(document).off('click', clickHandler);

                const element = $(event.target);
                if (element.hasClass('item-music') || element.parents('.item-music').length > 0) {
                    return;
                }

                if (localStorage.getItem('activeMusic')) {
                    playMusic(localStorage.getItem('activeMusic'), true);
                }
            }

            $(document).on('click', clickHandler);
        });

        $('.item-music[data-id=' + localStorage.getItem('activeMusic') + ']').addClass('active');
    }

    if (localStorage.getItem('activeMusic') && !localStorage.getItem('muteMusic')) {
        tryToPlayMusicAutomatically();
    } else {
        if (webamp) {
            webamp.close();
        }
    }

    if (localStorage.getItem('activeBuddie')) {
        activateBuddie(localStorage.getItem('activeBuddie'));
        $('.item-buddie[data-id=' + localStorage.getItem('activeBuddie') + ']').addClass('active');
    }

    // if (localStorage.getItem('casual')) {
    //     $('#inventory').find('h1').text('Filthy casual detected');
    // }

    $('#inventory').on('click', '.item-button', function () {
        var id = $(this).attr('data-id');

        const button = $(this).attr('data-type');
        const alreadyActive = $(this).hasClass('active');

        if (id === undefined) {
            return;
        }

        var reward = rewards[id];

        if (id === 'nothing') {
            resetRewards();
        }

        if (id === 'clamburger') {
            shuffleTextNodes($('div'));
        }

        if (reward.extra) {
            const a = document.createElement("a");
            document.body.appendChild(a);
            a.href = reward.extra;
            a.setAttribute('target', '_blank');
            a.setAttribute("download", "");
            a.click();
            document.body.removeChild(a);

            return;
        }

        if (reward.css && button === 'css') {
            if (alreadyActive) {
                resetCSS();
            } else {
                if (localStorage.getItem('activeCSS')) {
                    $('html').removeClass('reward-' + localStorage.getItem('activeCSS'));
                }
                localStorage.setItem('activeCSS', id);
                $('html').addClass('reward-' + id);

                $('.item-css').removeClass('active');
                $('.item-css[data-id=' + id + ']').addClass('active');
            }
        }

        if (id !== 'nothing' && button === 'buddie') {
            if (alreadyActive) {
                resetBuddie();
            } else {
                activateBuddie(id);
                localStorage.setItem('activeBuddie', id);

                $('.item-buddie').removeClass('active');
                $('.item-buddie[data-id=' + id + ']').addClass('active');
            }
        }

        if (reward.music && button === 'music') {
            if (alreadyActive) {
                resetMusic();
            } else if (!canPlayAudio) {
                $('#no-music').modal('show');
            } else {
                playMusic(id, true).catch(e => {
                    console.log(e.name);
                });
                if (id === 'whirr') {
                    localStorage.removeItem('activeMusic');
                } else {
                    localStorage.setItem('activeMusic', id);
                    localStorage.removeItem('muteMusic');
                }

                $('.item-music').removeClass('active');
                $('.item-music[data-id=' + id + ']').addClass('active');
            }
        }
    });

    $('#no-music').on('show.bs.modal', function () {
        $('body').addClass("no-music-modal");
    }).on('hide.bs.modal', function () {
        $('body').removeClass("no-music-modal");
    });

    $('#resetRewardsButton').click(function () {
        resetMusic();

        $(this).hide();
    });

    $('#cheat-code').submit(function (event) {
        event.preventDefault();
        var code = String($('#cheat-code-input').val());

        processCheat(code);
    });

    function processCheat(code) {
        $('#cheat-code-input').val('');
        if (code === 'rosebud') {
            inventory['shekels'] += 1000;
            playCheatMusic(true);
            $('#cheat-code-input').val('rosebud');
        } else if (code === 'motherlode') {
            inventory['shekels'] += 50000;
            playCheatMusic(true);
        } else if (code === 'idkfa') {
            addRewardToInventory('cacodemon');
            playCheatMusic(true);
        } else if (code === 'impulse 101') {
            for (const reward of Object.keys(rewards)) {
                addRewardToInventory(reward);
            }
            playCheatMusic(true);
        } else if (code.substring(0, 4) === 'gib ' && rewards[code.substring(4)]) {
            addRewardToInventory(code.substring(4));
            playCheatMusic(true);
        }
        updateInventory();
    }

    window.cheat = processCheat;

    function playCheatMusic(markAsCheater) {
        var sound = new Audio("/ogg/tf2.ogg");
        sound.volume = 0.25;
        sound.play();

        if (markAsCheater) {
            localStorage.setItem('casual', '1');
            // $('#inventory').find('h1').text('Filthy casual detected');
        }
    }

    function playMusic(id, showButton): Promise<void> {
        var reward = rewards[id];
        if (!reward.musicFile) {
            return;
        }

        if (music) {
            music.pause();
            music.currentTime = 0;
        }

        if (showButton) {
            $('#resetRewardsButton').show();
        }

        music = new Audio(reward.musicFile.url);
        music.volume = 0.5;

        return music.play();
    }

    function activateBuddie(id) {
        $('#reward-buddie').attr('src', rewards[id].image.url);
        $('#reward-buddie').show();
    }

    $('#buy-lootbox').click(function () {
        if (inventory['shekels'] < lootboxCost) {
            return;
        }

        openLootboxRewards(true);
        inventory['shekels'] -= lootboxCost;
        updateInventory();
    });

    if (localStorage.getItem('ignoreRewards')) {
        $('#restoreDrops').removeAttr('disabled');
    }

    if (!localStorage.getItem('ignoreRewards') && lastVotes.length === 0) {
        showRewardsOnSubmit = true;
    }

    $('#unequipAll').on('click', function (event) {
        resetRewards();
    });

    $('#restoreDrops').click(function (event) {
        event.preventDefault();
        $(this).attr('disabled', 'disabled');
        localStorage.removeItem('ignoreRewards');
        if (lastVotes.length === 0) {
            showRewardsOnSubmit = true;
        }
        alert('Your drops have been restored.');
    });

    $('#viewBonusCaptcha').on('click', () => {
      window.captchaReact.show(true);
    })

    var lootboxSound = new Audio("/ogg/open-box.ogg");

    var showNewLoot = function showNewLoot(i) {
        var lootbox = $($('#rewards .lootbox').get(i));
        lootbox.removeClass('animate').addClass('animate-back');
        lootbox.find('.lootbox-image').hide();

        const $item = lootbox.find('.inventory-item');
        $item.show();

        var title = $item.find('.item-name');
        var image = $item.find('img');
        var $tier = $item.find('.item-tier')

        var reward = pendingItems.pop();

        $item.removeClass('encrypted');

        if (reward.type === 'item') {
            var item = rewards[reward.item.shortName];

            if (item.extra) {
                $item.addClass('encrypted');
            }

            var tier = lootboxTiers[item.tier];
            addRewardToInventory(item.shortName);
            image.attr('src', item.image.url);
            title.text(item.name);
            $tier.show();
            $tier.text(tier.name);
            $tier.css('color', 'black');
            $tier.css('backgroundColor', tier.color);
        } else if (reward.type === 'shekels') {
            var shekelCount = reward.amount;
            inventory['shekels'] += shekelCount;
            image.attr('src', '/2023images/gold-bars.png');
            title.text(shekelCount + " gold");
            $tier.show();
            $tier.text('Special');
            $tier.css('color', 'white');
            $tier.css('backgroundColor', 'black');
        }

        updateInventory();
    };

    function addRewardToInventory(choice) {
        var reward = rewards[choice];
        if ($.inArray(reward.shortName, inventory['unlockKeys']) === -1) {
            inventory['unlocks'][reward.shortName] = 1;
            inventory['unlockKeys'].push(reward.shortName);
        } else {
            inventory['unlocks'][reward.shortName]++;
        }
    }

    $('#unboxButton').click(function () {
        lootboxSound.volume = 0.25;
        lootboxSound.play();

        $('.lootbox').addClass('animate');

        $(this).hide();

        setTimeout(function () {
            $('.lootbox').find('img').removeAttr('src');
        }, 1100);

        for (var i = 0; i < 3; i++) {
            setTimeout(showNewLoot, 2000 + i * 300, i);
        }

        setTimeout(function () {
            $('#closeRewards').show();
        }, 3600);
    });

    $('#neverShowAgain').click(function () {
        localStorage.setItem('ignoreRewards', 'true');
        $('#rewards').modal('hide');
        $('#restoreDrops').removeAttr('disabled');
    })

    $('#closeRewardsModal').click(function () {
        $('#rewards').modal('hide');
    });

    var pendingItems = [];

    function openLootboxRewards(force) {
        if (!force && !showRewardsOnSubmit) {
            return;
        }

        $('#unboxButton').show();
        $('#closeRewards').hide();
        $('.lootbox').removeClass('animate-back');
        $('.lootbox').find('.inventory-item').hide();
        $('.lootbox-title').text('');
        $('.lootbox-tier').hide();
        $('.lootbox-image').show();

        var lootboxes = [
          'chimera.png',
          'drakee.png',
          'droll.png',
          'golem.png',
          'knight.png',
          'scorpion.png',
          'skeleton.png',
          'slime.png',
          'spectre.png',
          'warlock.png',
        ];

        $('.lootbox-image').each(function () {
            $(this).attr('src', '/2023images/lootbox_sprites/' + lootboxes[getRandomInt(0, lootboxes.length)]);
        });

        if (getRandomInt(1,7) === 6) {
            $('#youre').attr('src', '/2022images/youre-rewards.png');
        } else {
            $('#youre').attr('src', '/2022images/your-rewards.png');
        }

        $('#rewards').modal('show');

        var audio = new Audio('/ogg/tf2.ogg');
        audio.volume = 0.10;
        audio.play();

        showRewardsOnSubmit = false;

        $('#unboxButton').attr('disabled', 'disabled');

        $.post('/inventory/purchase-lootbox').then(data => {
            pendingItems = data.rewards;
            for (const reward of pendingItems) {
                if (reward.type !== 'item') {
                    continue;
                }
                if (!rewards[reward.item.shortName]) {
                    rewards[reward.item.shortName] = reward.item;
                }
            }
        }).always(() => {
            $('#unboxButton').removeAttr('disabled');
        });
    }

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
    var bottomAreaContainer = $('.your-votes-container');

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

    var topSortableOptions = Object.assign({}, sortableOptions) as any;
    topSortableOptions.sort = false;

    if (votingEnabled) {
        if (topArea.length > 0) {
            // new Sortable(document.getElementById('voteDropAreaTop'), topSortableOptions);
            new Sortable(document.getElementById('voteDropAreaBottom'), sortableOptions);
        }

        $('.voteBox').click(function (event) {

            let target = event.originalEvent.target as HTMLElement;

            if (target.localName === 'a') {
                return;
            }

            if (!$.contains(topArea[0], this)) {
                $(this).parent().detach().appendTo(topArea);
            } else {
                $(this).parent().detach().appendTo(bottomArea);
            }

            updateNumbers();
            unlockVotes();
            setTimeout(incrementDragCounter, 100);
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


        bottomAreaContainer.addClass("locked");
        bottomAreaContainer.find('.your-votes').text("Your Votes");
        submitButton.addClass('iVoted').attr('title', 'Saved!');
        $('#submitReminder').text('Your votes have been submitted.');

        previousLockExists = true;
        votesChanged = false;
        $(".navigation").show();
    }

    // Update interface to indicate that there have been changes since the last submitted vote.
    function unlockVotes() {
        $(".voteBox").removeClass("locked");
        $(".aNominee").removeClass("locked");
        $('#submitReminder').text('Don\'t forget to click on "Submit Votes" below to save your votes!');

        bottomAreaContainer.removeClass("locked");
        bottomAreaContainer.find('.your-votes').text("Your Votes (unsaved)");
        submitButton.removeClass('iVoted').attr('title', 'Submit Votes');
        votesChanged = true;
    }

    // Checks for duplicate preferences, and marks any duplicates as invalid.
    // Only used in the "type in your number" layout.
    function checkForDuplicates() {
        var allNumbers = {};
        preferenceInputs.each(function () {
            var element = $(this);
            var number = element.val() as string | number;

            element.removeClass('invalid');
            if (number === "") {
                return;
            }
            if (!allNumbers[number]) {
                allNumbers[number] = [];
            }
            allNumbers[number].push(element);
        });

        $.each(allNumbers, function (index, elements: Array<JQuery<HTMLElement>>) {
            if (elements.length > 1) {
                $.each(elements, function (index, element: JQuery<HTMLElement>) {
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
            var text = '#' + index;
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

        muhNominees = ($(muhNominees) as any).sort(function (a, b) {
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

        nominees = ($(nominees) as any).sort(function (a, b) {
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
        moveNomineesBack(true);
        resetTopArea();
        resetLeftSide();
        unlockVotes();

        $('.voteDropArea').addClass('flash');
        setTimeout(function () {
            $('.voteDropArea').removeClass('flash');
        }, 200);
    });

    // Submit Votes
    submitButton.on('click', function () {
        if (!votesChanged) {
            return;
        }

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

        if (showCaptcha && !captchaShownThisLoad) {
          window.captchaReact.show();
          captchaShownThisLoad = true;

          captchaPendingCallback = () => {
            openLootboxRewards(false);
          };
        } else {
          openLootboxRewards(false);
        }
    });

    renderCaptcha({
      ...captchaSettings,
      onCompletion: onCaptchaCompletion,
      userSettings: captchaUserSettings,
    });
});
