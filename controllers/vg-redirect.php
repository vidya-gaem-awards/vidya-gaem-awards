<?php
$board = "v";
$thread = "235575315";

if (!$thread) {
  header("Location: http://boards.4chan.org/$board/");
} else {
  header("Location: http://boards.4chan.org/$board/res/$thread");
}

exit;
