<?php
	require('../session.php');

	if($_SESSION['approval_level'] != -1) {
		header('Location: ../index.php');
	}

	require('../connect.php');

	$error = '';
	$info = '';

	$type = $_GET['type'];

	if(isset($_POST['save'])) {

		$conn1 = odbc_connect('test', 'hkd', 'hindalco_123');
		odbc_autocommit($conn1, false);

		if(strcmp($type, 'user') == 0) {

			$success = 1;

			$sql_user_add = "update can_user_mast set user_name = ?, password = ? where user_id = ?";

			$stmt_user = odbc_prepare($conn1, $sql_user_add);
			set_error_handler(function($errno, $errstr, $errfile, $errline ) {
    			throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
 			});
			for($i = 0 ; $i < count($_POST['user_id']) && $success == 1 ; $i ++) {
				try {
					odbc_execute($stmt_user, Array(
						$_POST['user_name'][$i],
						$_POST['user_pass'][$i],
						$_POST['user_id'][$i]
						));
				} catch(Exception $e) {
					odbc_rollback($conn1);
					$success = 0;
					$error = 'Unable to make changes. Please check the values and try again.';
				}
			}

			if($success == 1) {
				odbc_commit($conn1);
				$info = "Changes Applied.";
			}

			odbc_close($conn1);

		} else if(strcmp($type, 'item') == 0) {
			$success = 1;

			$sql_user_add = "update can_item_mast set item_name = ?, item_modify_time = sysdate, item_modify_user = ? where item_id = ?";

			$stmt_user = odbc_prepare($conn1, $sql_user_add);
			set_error_handler(function($errno, $errstr, $errfile, $errline ) {
    			throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
 			});
			for($i = 0 ; $i < count($_POST['item_id']) && $success == 1 ; $i ++) {
				try {
					odbc_execute($stmt_user, Array(
						$_POST['item_name'][$i],
						$_SESSION['user_id'],
						$_POST['item_id'][$i]
						));
				} catch(Exception $e) {
					odbc_rollback($conn1);
					$success = 0;
					$error = 'Unable to make changes. Please check the values and try again.';
					print $e->getMessage();
				}
			}

			if($success == 1) {
				odbc_commit($conn1);
				$info = "Changes Applied.";
			}

			odbc_close($conn1);

		} else if(strcmp($type, 'supp') == 0) {
			$success = 1;

			$sql_user_add = "update can_supplier_mast set supp_name = ?, supp_modify_date = sysdate, supp_modify_user = ? where supp_id = ?";

			$stmt_user = odbc_prepare($conn1, $sql_user_add);
			set_error_handler(function($errno, $errstr, $errfile, $errline ) {
    			throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
 			});
			for($i = 0 ; $i < count($_POST['supp_id']) && $success == 1 ; $i ++) {
				try {
					odbc_execute($stmt_user, Array(
						$_POST['supp_name'][$i],
						$_SESSION['user_id'],
						$_POST['supp_id'][$i]
						));
				} catch(Exception $e) {
					odbc_rollback($conn1);
					$success = 0;
					$error = 'Unable to make changes. Please check the values and try again.';
					print $e->getMessage();
				}
			}

			if($success == 1) {
				odbc_commit($conn1);
				$info = "Changes Applied.";
			}

			odbc_close($conn1);
		}
	}

	if(strcmp($type, 'user') == 0) {

		$user['id'] = array();
		$user['name'] = array();
		$user['pass'] = array();
		$user['aprv'] = array();

		$sql_user = "select user_id, user_name, password, approval_level from can_user_mast where approval_level <> -1";

		$result_user = odbc_exec($conn, $sql_user);

		while(odbc_fetch_row($result_user)) {
			array_push($user['id'], odbc_result($result_user, 1));
			array_push($user['name'], odbc_result($result_user, 2));
			array_push($user['pass'], odbc_result($result_user, 3));
			array_push($user['aprv'], odbc_result($result_user, 4));
		}

	} else if(strcmp($type, 'item') == 0) {
		
		$item['id'] = array();
		$item['name'] = array();

		$sql_item = "select item_id, item_name from can_item_mast";
		$result_item = odbc_exec($conn, $sql_item);

		while(odbc_fetch_row($result_item)) {
			array_push($item['id'], odbc_result($result_item, 1));
			array_push($item['name'], odbc_result($result_item, 2));
		}

	} else if(strcmp($type, 'supp') == 0) {

		$supp['id'] = array();
		$supp['name'] = array();

		$sql_supp = "select supp_id, supp_name from can_supplier_mast";
		$result_supp = odbc_exec($conn, $sql_supp);

		while(odbc_fetch_row($result_supp)) {
			array_push($supp['id'], odbc_result($result_supp, 1));
			array_push($supp['name'], odbc_result($result_supp, 2));
		}

	} else {
		$error = 'Please provide a valid type.';
	}

?>
<!DOCTYPE html>
<HTML>
<HEAD>
	<script type="text/javascript" src="../js/jquery-2.1.4.js"></script>
	<script type="text/javascript" src="../js/bootstrap.min.js"></script>
	<script type="text/javascript" src="../js/scripts.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../css/style.css">
</HEAD>
<BODY>
	<div class="text-center">
		<a href="index.php"><h3> Canteen Inventory Regulation - Admin Panel </h3></a>
	</div>
	<hr/>
	<div class="container">
		<div class="container-fluid">
		<div class="text-center" style="margin-bottom: 15px;">
			<?php
			if(strlen($error) > 0)
				print '<span class="label label-danger">'.$error.'</span>';
			if(strlen($info) > 0)
				print '<span class="label label-success">'.$info.'</span>';
			?>
		</div>
		<form action="modify.php?type=<?php print $type; ?>" method="POST">
	<?php
	if(strcmp($type, 'user') == 0) {
		if(count($user['id']) == 0)
			print '<div class="text-center"><span class="label label-danger">No users found.</span></div>';
		else {
			print '<div class="row">';
			print '<div class="col-sm-3 col-md-3 col-lg-3">';
			print '<label> User ID </label>';
			print '</div>';
			print '<div class="col-sm-3 col-md-3 col-lg-3">';
			print '<label> Name </label>';
			print '</div>';
			print '<div class="col-sm-3 col-md-3 col-lg-3">';
			print '<label> Password </label>';
			print '</div>';
			print '<div class="col-sm-3 col-md-3 col-lg-3">';
			print '<label> Approval Level </label>';
			print '</div>';
			print '</div>';
			for($i = 0 ; $i < count($user['id']) ; $i ++) {
				print '<div class="row">';
				print '<div class="col-sm-3 col-md-3 col-lg-3">';
				print '<input type="hidden" name="user_id[]" class="form-control disabled input-sm" value="'.$user['id'][$i].'"">';
				print '<input type="text" class="form-control disabled input-sm" value="'.$user['id'][$i].'" disabled="disabled">';
				print '</div>';
				print '<div class="col-sm-3 col-md-3 col-lg-3">';
				print '<input type="text" name="user_name[]" class="form-control input-sm" value="'.$user['name'][$i].'">';
				print '</div>';
				print '<div class="col-sm-3 col-md-3 col-lg-3">';
				print '<input type="text" name="user_pass[]" class="form-control input-sm" value="'.$user['pass'][$i].'">';
				print '</div>';
				print '<div class="col-sm-3 col-md-3 col-lg-3">';
				print '<input type="number" name="user_aprv[]" class="form-control input-sm" value="'.$user['aprv'][$i].'" disabled="desabled">';
				print '</div>';
				print '</div>';
			}
				print '<div class="text-center" style="margin-top: 10px;">'.'<input type="submit" name="save" class="btn btn-primary btn-sm" value="Update">'.'</div>';
		}
	} else if(strcmp($type, 'item') == 0) {
		if(count($item['id']) == 0)
			print '<div class="text-center"><span class="label label-danger">No items found.</span></div>';
		else {
			print '<div class="row">';
			print '<div class="col-sm-3 col-md-3 col-lg-3">';
			print '</div>';
			print '<div class="col-sm-3 col-md-3 col-lg-3">';
			print '<label> Item ID </label>';
			print '</div>';
			print '<div class="col-sm-3 col-md-3 col-lg-3">';
			print '<label> Name </label>';
			print '</div>';
			print '<div class="col-sm-3 col-md-3 col-lg-3">';
			print '</div>';
			print '</div>';
			for($i = 0 ; $i < count($item['id']) ; $i ++) {
				print '<div class="row">';
				print '<div class="col-sm-3 col-md-3 col-lg-3">';
				print '</div>';
				print '<div class="col-sm-3 col-md-3 col-lg-3">';
				print '<input type="hidden" name="item_id[]" class="form-control input-sm" value="'.$item['id'][$i].'">';
				print '<input type="text" class="form-control input-sm" value="'.$item['id'][$i].'" disabled="desabled">';
				print '</div>';
				print '<div class="col-sm-3 col-md-3 col-lg-3">';
				print '<input type="text" name="item_name[]" class="form-control input-sm" value="'.$item['name'][$i].'">';
				print '</div>';
				print '<div class="col-sm-3 col-md-3 col-lg-3">';
				print '</div>';
				print '</div>';
			}
				print '<div class="text-center" style="margin-top: 10px;">'.'<input type="submit" name="save" class="btn btn-primary btn-sm" value="Update">'.'</div>';
		}	
	} else if(strcmp($type, 'supp') == 0) {
		if(count($supp['id']) == 0)
			print '<div class="text-center"><span class="label label-danger">No suppliers found.</span></div>';
		else {
			print '<div class="row">';
			print '<div class="col-sm-3 col-md-3 col-lg-3">';
			print '</div>';
			print '<div class="col-sm-3 col-md-3 col-lg-3">';
			print '<label> Supplier ID </label>';
			print '</div>';
			print '<div class="col-sm-3 col-md-3 col-lg-3">';
			print '<label> Name </label>';
			print '</div>';
			print '<div class="col-sm-3 col-md-3 col-lg-3">';
			print '</div>';
			print '</div>';
			for($i = 0 ; $i < count($supp['id']) ; $i ++) {
				print '<div class="row">';
				print '<div class="col-sm-3 col-md-3 col-lg-3">';
				print '</div>';
				print '<div class="col-sm-3 col-md-3 col-lg-3">';
				print '<input type="hidden" name="supp_id[]" class="form-control input-sm" value="'.$supp['id'][$i].'">';
				print '<input type="text" class="form-control input-sm" value="'.$supp['id'][$i].'" disabled="desabled">';
				print '</div>';
				print '<div class="col-sm-3 col-md-3 col-lg-3">';
				print '<input type="text" name="supp_name[]" class="form-control input-sm" value="'.$supp['name'][$i].'">';
				print '</div>';
				print '<div class="col-sm-3 col-md-3 col-lg-3">';
				print '</div>';
				print '</div>';
			}
				print '<div class="text-center" style="margin-top: 10px;">'.'<input type="submit" name="save" class="btn btn-primary btn-sm" value="Update">'.'</div>';
		}
	}

	?>
	
		</form>
	</div>
	</div>
</BODY>
</HTML>