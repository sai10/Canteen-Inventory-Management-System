<?php
/*
Logout the user.

*/	
	# Start session
	session_start();

	# Destroy it
	if(session_destroy())
		# Redirect
		header("Location: index.php");
?>