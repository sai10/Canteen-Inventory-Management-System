<?php
/*
Utility to redirect to login page if no user is active.


*/
	# Get DB connection
	require('connect.php');

	if(!$conn) {
		# Redirect to index
		header("Location: index.php");
	} else {

		# Start session
		session_start();

		# Check if an user is active
		$user_check = $_SESSION['user_id'];
		$stmt = odbc_prepare($conn, "select count(*) as cnt from can_user_mast where USER_ID=?");
		odbc_execute($stmt, Array($user_check));

		$rows = odbc_fetch_array($stmt);

		if((int)$rows['CNT'] != 1) { # No user, redirect
			header("Location: index.php");
		}
		odbc_close($conn);
	}
?>