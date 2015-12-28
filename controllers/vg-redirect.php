<?php
$board = "v";
$thread = "235575315";

if (!$thread) {
    header("Location: https://boards.4chan.org/$board/");
} else {
    header("Location: https://boards.4chan.org/$board/res/$thread");
}

exit;
