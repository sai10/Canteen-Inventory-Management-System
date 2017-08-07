<?php
/*
Searching utility for transactions by date.


*/	
	$error = '';
	$info = '';
	$number_of_rows = 0;

	if(!isset($_POST['search'])) {
		date_default_timezone_set('Asia/Kolkata');
		$_POST['from_date'] = date('Y-m-d');
		$_POST['to_date'] = date('Y-m-d');
		$_POST['search'] = 1;
	}

	if(isset($_POST['search'])) {
		if(empty($_POST['from_date']) || empty($_POST['to_date'])) {
			$error = " Dates can't be empty. ";
		} else {

			if(strcmp($_POST['from_date'], $_POST['to_date']) == 1) {
				$error = " 'From Date' can't come after 'To Date' ";
			} else {

				include('connect.php');

				if(!$conn) {
					$error = "Database connection problem. Please try again later.";
				} else {

					# SQL to get desired results
					$sql = "select t2.tran_id, t4.supp_name, t2.tran_create_date, t3.status, t5.user_name, t3.forward_date from";
					$sql .= " (select tran_id, max(aprv_id) aid";
					$sql .= " from can_transaction_aprv";
					$sql .= " group by tran_id";
					$sql .= " ) t1";
					$sql .= " inner join";
					$sql .= " (select tran_id, tran_supp_id, tran_create_date";
					$sql .= " from can_transaction_mast";
					$sql .= " where tran_create_user = ?";
					$sql .= " ) t2";
					$sql .= " on t1.tran_id = t2.tran_id";
					$sql .= " inner join";
					$sql .= " (select aprv_id, status, forwarded_to, forward_date from";
					$sql .= " can_transaction_aprv";
					$sql .= " ) t3";
					$sql .= " on t1.aid = t3.aprv_id";
					$sql .= " inner join";
					$sql .= " (select supp_id, supp_name from";
					$sql .= " can_supplier_mast";
					$sql .= " ) t4";
					$sql .= " on t2.tran_supp_id = t4.supp_id";
					$sql .= " inner join";
					$sql .= " (select user_id, user_name from";
					$sql .= " can_user_mast";
					$sql .= " ) t5";
					$sql .= " on t3.forwarded_to = t5.user_id";
					$sql .= " where t2.tran_create_date >= to_date('".$_POST['from_date']."', 'yyyy-mm-dd')";
					$sql .= " and t2.tran_create_date <= to_date('".$_POST['from_date']."', 'yyyy-mm-dd') + 1";
					$sql .= " order by t2.tran_create_date desc";

					$stmt = odbc_prepare($conn, $sql);

					odbc_execute(
						$stmt,
						Array($_SESSION['user_id'], $_POST['from_date'], $_POST['to_date'])
						);

					$search_result["tran_id"] = array();
					$search_result["supplier_name"] = array();
					$search_result["create_date"] = array();
					$search_result["status"] = array();
					$search_result["forwarded_to"] = array();
					$search_result["forward_date"] = array();

					while($row = odbc_fetch_array($stmt)) {
						array_push($search_result["tran_id"], $row["TRAN_ID"]);
						array_push($search_result["supplier_name"], $row["SUPP_NAME"]);
						array_push($search_result["create_date"], $row["TRAN_CREATE_DATE"]);
						array_push($search_result["status"], $row["STATUS"]);
						array_push($search_result["forwarded_to"], $row["USER_NAME"]);
						array_push($search_result["forward_date"], $row["FORWARD_DATE"]);
					}

					$number_of_rows = count($search_result["tran_id"]);

					odbc_close($conn);

					if($number_of_rows == 0) {
						$error = "No records found.";
					} else if($number_of_rows == 1)  {
						$info = "1 record found.";
					} else {
						$info = $number_of_rows." records found.";
					}
				}
			}
		}
	}
?>