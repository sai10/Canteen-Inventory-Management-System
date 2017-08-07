<?php
/*
PHP to save submitted items in database.


*/

	$error = '';
	$info = '';
	$conn = odbc_connect('test', 'hkd', 'hindalco_123');


	if(!$conn) {
		$error = 'Databse error, please reload after some time.';
		$msg = '';
	} else {

		# Get new ID
		$new_id_sql = "select 'S'||'/'||extract(year from sysdate)||'/'||lpad(to_char(extract(month from sysdate)), 2, '0')||'/'||lpad(to_char(extract(day from sysdate)), 2, '0')||'/'||lpad(to_char(max(substr(tran_id, 14))+1), 3, '0') as new_id from can_transaction_mast where extract(year from sysdate) = extract(year from tran_create_date) and extract(month from sysdate) = extract(month from tran_create_date) and extract(day from sysdate) = extract(day from tran_create_date)";
		$stmt = odbc_prepare($conn, $new_id_sql);
		odbc_execute($stmt, Array());
		$rows = odbc_fetch_array($stmt);

		$new_id = $rows['NEW_ID'];
		if(strlen($new_id) < 16){
			$new_id = $new_id."001";
		}

		# Create new transaction
		$new_tran_sql = "insert into can_transaction_mast (tran_id, tran_supp_id, tran_create_user, tran_create_date) values(?, ?, ?, sysdate)";
		$tran_stmt = odbc_prepare($conn, $new_tran_sql);
		$supp_id = substr($_SESSION['supplier'], -4);
		odbc_execute($tran_stmt, Array($new_id, $supp_id, $_SESSION['user_id']));

		# Add items to transaction
		$add_item_sql = "insert into can_transaction_dtl (tran_id, item_id, unit, description, quantity) values(?, ?, ?, ?, ?)";
		$add_item_stmt = odbc_prepare($conn, $add_item_sql);

		for($i = 0 ; $i < count($_SESSION['item']) ; $i ++) {
			$item_id = substr($_SESSION['item'][$i], -10);
			odbc_execute($add_item_stmt, Array($new_id, $item_id, $_SESSION['unit'][$i], $_SESSION['desc'][$i], $_SESSION['qty'][$i]));
		}

		# Initiate transaction approval process
		$aprv_sql = "insert into can_transaction_aprv (aprv_id, tran_id, forwarded_by, forwarded_to, forward_date, comments, status) values(?, ?, ?, ?, sysdate, 'Pending', 'In progress')";
		$aprv_stmt = odbc_prepare($conn, $aprv_sql);
		$forward_user_id = substr($_SESSION['forwarded_user'], -6);
		odbc_execute($aprv_stmt, Array($new_id.'/001',$new_id, $_SESSION['user_id'], $forward_user_id));

		odbc_close($conn);

		$msg = 'Your transaction was successful with ID '.$new_id;
		$_SESSION['valid_next_item_entry'] = 0;
	}
?>