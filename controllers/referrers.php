<?php
function startsWith($haystack, $needle) {
  return substr($haystack, 0, strlen($needle)) == $needle;
}

$tpl->set("title", "Referrers");
$query = "SELECT MAX(`Timestamp`) AS `Latest`,
COUNT(*) AS `Count`, `Refer` FROM `access`
WHERE `Refer` != \"\"
AND `Refer` NOT LIKE \"%vidyagaemawards.com%\"
AND `Refer` NOT LIKE \"https://%\"
AND `Refer` != \"undefined\"
AND `Timestamp` > DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY `Refer`
HAVING `Count` >= 5
ORDER BY `Count` DESC, `Latest` DESC";
$result = $mysql->query($query);

$data = array();
while ($row = $result->fetch_assoc()) {
  $latest = strtotime($row["Latest"]);
  $diff = floor((time() - $latest) / 60 / 60 / 24);
  $diffStr = "$diff days ago";
  
  $refer = substr($row['Refer'], 7);
  if (startsWith($refer, "www.")) {
    $refer = substr($refer, 4);
  }
  $link = "";
  $linkName = "";
  $class = "";
  if (startsWith($refer, "t.co/")) {
    $link = "https://twitter.com/search/realtime?q=".urlencode($row['Refer']);
    $linkName = "Twitter search";
    $class = "alert";
  } else if (startsWith($refer, "boards.4chan.org/v")) {
    if (strlen($refer) > 23) {
      $link = "http://archive.foolz.us/v/thread/".substr($refer, 23);
      $linkName = "Archive link";
    }
    $class = "success";
  } else {
    $link = $row['Refer'];
    $linkName = "Follow link";
  }

  if (startsWith($refer, "reddit.com") || startsWith($refer, "neogaf.com")) { 
    $class = "error";
  }
  
  $data[] = array("Count" => $row['Count'], "Latest" => $diffStr, "LatestAlt" => $row['Latest'], "Link" => $link, "LinkName" => $linkName, "Refer" => $row['Refer'], "Class" => $class);
}

$tpl->set("data", $data);
?>
