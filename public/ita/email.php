<?php 
$to = 'chauhanjeet3@gmail.com'; // Put in your email address here
$subject  = "New Quote for Moving Bedroom"; // The default subject. Will appear by default in all messages. Change this if you want.

// User info (DO NOT EDIT!)
$fname = stripslashes($_REQUEST['fname']); // sender's name
$phone = stripslashes($_REQUEST['phone']); // sender's name
$fromcityzip = stripslashes($_REQUEST['fromcityzip']); // sender's email
$tocityzip = stripslashes($_REQUEST['tocityzip']); // sender's email
$bedroom = stripslashes($_REQUEST['bedroom']); // sender's email
$movedate = stripslashes($_REQUEST['movedate']); // sender's email


// The message you will receive in your mailbox
// Each parts are commented to help you understand what it does exaclty.
// YOU DON'T NEED TO EDIT IT BELOW BUT IF YOU DO, DO IT WITH CAUTION!
$msg .= "First Name: ".$fname."\r\n";  // add sender's name to the message
$msg .= "Phone Number: ".$phone."\r\n";  // add sender's name to the message
$msg .= "From: ".$fromcityzip."\r\n";  // add sender's email to the message
$msg .= "To: ".$tocityzip."\r\n";  // add sender's email to the message
$msg .= "Bed Rooms for Move: ".$bedroom."\r\n";  // add sender's email to the message
$msg .= "Moving Date: ".$movedate."\r\n";  // add sender's email to the message


$msg .= "Subject: ".$subject."\r\n\n"; // add subject to the message (optional! It will be displayed in the header anyway)
$msg .= "\r\n\n"; 

$mail = @mail($to, $subject, $msg, "From:".$email);  // This command sends the e-mail to the e-mail address contained in the $to variable

if($mail) {
	header("location:index.html");
	//This is the message that will be shown when the message is successfully send
	
} else {
	echo 'Your message not sent.';   //This is the message that will be shown when an error occured: the message was not send
}

?>