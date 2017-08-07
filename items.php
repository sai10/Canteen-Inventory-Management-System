<?php
/*
Popualte item and supplier list from databse. May show errors and info to user.


*/
	# Redirect if User has invalid approval level
	if($_SESSION['approval_level'] != 0) {
		header('Location: index.php');
	}
	
	# Initialize variables
	$error = '';
	$info = '';
	$list_suppliers = Array('Please Select');
	$list_items = Array();
	$forward_list = Array('Please Select');

	require('connect.php');

	if(!$conn) { # Unable to connect
		$error = 'Unable to connect to database. Please try again in some time.';
	} else { # Connection successful

		# Getting supplier list
		$raw_sql_suppliers = 'select SUPP_ID, SUPP_NAME from can_supplier_mast'; # SQL query
		$result_suppliers = odbc_exec($conn, $raw_sql_suppliers); # Execute

		# Put results in the string
		while(odbc_fetch_row($result_suppliers)) {
			$supplier_id = odbc_result($result_suppliers, 1);
			$supplier_name = odbc_result($result_suppliers, 2);
			$supplier = $supplier_name." - ".$supplier_id;
			array_push($list_suppliers, $supplier);
		}

		# Getting item list
		$raw_sql_items = 'select ITEM_ID, ITEM_NAME from can_item_mast'; # SQL query
		$result_items = odbc_exec($conn, $raw_sql_items); # Execute

		# Put results in string
		while(odbc_fetch_row($result_items)) {
			$item_id = odbc_result($result_items, 1);
			$item_name = odbc_result($result_items, 2);
			$item = $item_name." - ".$item_id;
			array_push($list_items, $item);
		}

		# Getting forward list
		$raw_sql_forward = 'select user_id, user_name from can_user_mast where approval_level=1'; # SQL query
		$result_forward = odbc_exec($conn, $raw_sql_forward); # Execute

		# Append to forward list
		while(odbc_fetch_row($result_forward)) {
			$user_id = odbc_result($result_forward, 1);
			$user_name = odbc_result($result_forward, 2);
			$user = $user_name." - ".$user_id;
			array_push($forward_list, $user);
		}

		# Close connection
		odbc_close($conn);
	}

?>