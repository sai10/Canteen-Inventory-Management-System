<!--
Check if submit_item page is landable. If yes, save the item(s) to database and return an transaction ID.

-->
<?php

	$msg = 'Please wait while your submission is being processed.';

	if(!isset($_SESSION['valid_next_item_entry'])) # Submission was not valid
		header("Location: index.php");

	if($_SESSION['valid_next_item_entry'] != 1) # Already made a submission
		header("Location: index.php");

	if($_SESSION['approval_level'] != 0) # Not a valid user
		header("Location: index.php");

?>