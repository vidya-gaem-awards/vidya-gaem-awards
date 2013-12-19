<?php
// Check if applications are open
if (!$APPLICATIONS_OPEN) {
  storeMessage("formError", "Volunteer submissions for $YEAR are closed. "
    . "Feel free to come back next year.");
  header("Location: /home");
  exit;
}

// Check if all the fields were filled in
if (strlen($_POST['name']) == 0 || strlen($_POST['email']) == 0
  || strlen($_POST['skills']) == 0) {
  storeMessage("formError", "You must fill in all the fields.");
  header("Location: /home");
  exit;
}
  
// Add the application to the database
$query = "INSERT INTO `applications` (`UserID`, `Name`, `Email`, `Interest`, "
  . "`Timestamp`) VALUES(?, ?, ?, ?, NOW())";
$stmt = $mysql->prepare($query);
$stmt->bind_param("ssss", $userID, $_POST['name'], $_POST['email'],
  $_POST['skills']);
$result = $stmt->execute();

if ($result) {
  storeMessage("formSuccess",
    "Your interest has been successfully registered.");
  action("application", $userID);
  
  // Send out an email
  $subject = "New application";
  $message = "Somebody has registered their interest on the /v/GA website.\n\n";
  $message .= "Name: {$_POST['name']}\n";
  $message .= "Email: {$_POST['email']}\n";
  $message .= "Interest: {$_POST['skills']}\n";
      
  $headers = "From: Vidya Gaem Awards <$EMAIL_FROM>\r\n";
      
  mail($EMAIL_TO, $subject, $message, $headers);
  
} else {
  error_log("MySQL error: ".$stmt->error);
  storeMessage("formError", "An error has occurred. "
    . "The form has not been saved. Try sending us an email instead.");;
}

header("Location: /home");
?>