<?php
	require('../session.php');
	if($_SESSION['approval_level'] != -1) {
		header('Location: /canteen-inventory-regulation-system/');
	}

	$error = '';
	$info = '';

	if(isset($_POST['create_user'])) {
		$sql_user = 'insert into can_user_mast (user_id, user_name, password, approval_level) values(?,?,?,?)';
		$stmt_user = odbc_prepare($conn, $sql_user);
		set_error_handler(function($errno, $errstr, $errfile, $errline ) {
			throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
			});
		try {
			$res = explode(' - ', $_POST['user_details']);
			odbc_execute($stmt_user, Array(
					$res[0],
					$res[1],
					$_POST['create_user_password'],
					$_POST['create_user_aprv']
				)
			);
			$info .= ' User created.';
		} catch(Exception $e) {
			$error .= ' Unable to create user.';
		}
	}

	if(isset($_POST['create_item'])) {

		$new_id = odbc_result(odbc_exec($conn, 'select lpad(max(item_id)+1,10,\'0\') from can_item_mast'), 1);

		if($new_id == false)
			$new_id = '0000000000';

		$sql_item = 'insert into can_item_mast values(?,?,?,?,sysdate, sysdate)';
		$stmt_item = odbc_prepare($conn, $sql_item);
		set_error_handler(function($errno, $errstr, $errfile, $errline ) {
			throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
			});
		try {
			odbc_execute($stmt_item, Array(
					$new_id,
					$_POST['create_item_name'],
					$_SESSION['user_id'],
					$_SESSION['user_id']
				)
			);
			$info .= ' Item created.';
		} catch(Exception $e) {
			$error = ' Unable to create item.';
		}
	}

	if(isset($_POST['create_supp'])) {

		$new_id = odbc_result(odbc_exec($conn, 'select lpad(max(supp_id)+1, 4, \'0\') from can_supplier_mast'), 1);
		if($new_id == false)
			$new_id = '0000';
		$sql_item = 'insert into can_supplier_mast values(?,?,?,sysdate,?,sysdate)';
		$stmt_item = odbc_prepare($conn, $sql_item);
		set_error_handler(function($errno, $errstr, $errfile, $errline ) {
			throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
			});
		try {
			odbc_execute($stmt_item, Array(
					$new_id,
					$_POST['create_supp_name'],
					$_SESSION['user_id'],
					$_SESSION['user_id']
				)
			);
			$info .= ' Supplier created.';
		} catch(Exception $e) {
			$error = ' Unable to create supplier.';
		}
	}

	if(isset($_POST['del_user'])) {
		$sql_del_user = 'delete from can_user_mast where user_id = ?';
		$stmt_del_user = odbc_prepare($conn, $sql_del_user);
		set_error_handler(function($errno, $errstr, $errfile, $errline ) {
			throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
			});
		try {
			odbc_execute($stmt_del_user, Array($_POST['delete_user']));
			$info = 'User deleted.';
		} catch(Exception $e) {
			$error = 'Unable to delete user. Make sure that no other entity is dependent on the user.';
		}
	}
	if(isset($_POST['del_item'])) {
		$sql_del_user = 'delete from can_item_mast where item_id = ?';
		$stmt_del_user = odbc_prepare($conn, $sql_del_user);
		set_error_handler(function($errno, $errstr, $errfile, $errline ) {
			throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
			});
		try {
			odbc_execute($stmt_del_user, Array($_POST['delete_item']));
			$info = 'Item deleted.';
		} catch(Exception $e) {
			$error = 'Unable to delete item. Make sure that no other entity is dependent on the item.';
		}
	}
	if(isset($_POST['del_supp'])) {
		$sql_del_user = 'delete from can_supplier_mast where supp_id = ?';
		$stmt_del_user = odbc_prepare($conn, $sql_del_user);
		set_error_handler(function($errno, $errstr, $errfile, $errline ) {
			throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
			});
		try {
			odbc_execute($stmt_del_user, Array($_POST['delete_supp']));
			$info = 'Supplier deleted.';
		} catch(Exception $e) {
			$error = 'Unable to delete supplier. Make sure that no other entity is dependent on the supplier.';
		}
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
<BODY style="background-color:#D1D0CE;">
	<div class="text-center">
		<h3> Canteen Inventory Regulation - Admin Panel </h3>
	</div>
	<hr/>
	<div class="container">
		<div class="container-fluid">
			<div class="pull-right">
				<a href = "../logout.php">Logout</a>
			</div>
			<div class="text-center">
				<?php
					if(strlen($error) > 0)
						print '<span class="label label-danger">'.$error.'</span>';
					if(strlen($info) > 0)
						print '<span class="label label-success">'.$info.'</span>';
				?>
			</div>
			<hr style="margin-top: 25px;">
			<h4 style=" border-top: 1px solid black;"> Add User </h4>
			<form target="" method="POST">
				<div class="row">
					<div class="col-sm-6 col-md-6 col-lg-6">
						<label>User ID</label>
						<select name="user_details" class="form-control">
							<option value="">Select User</option>
							<?php
								$sql = 'select clock_no, clock_name from vw_ind_clock_current where unit_code in (\'SML\', \'PWR\')';
								$result = odbc_exec($conn, $sql);
								while(odbc_fetch_row($result)) {
									print '<option>'.odbc_result($result, 1).' - '.odbc_result($result, 2).'</option>';
								}
							?>
						</select>
					</div>
					<div class="col-sm-3 col-md-3 col-lg-3">
						<label>Password</label>
						<input type="text" name="create_user_password" class="form-control input-sm">
					</div>
					<div class="col-sm-3 col-md-3 col-lg-3">
						<label>Approval Level</label>
						<input type="number" step="1" min="0" name="create_user_aprv" class="form-control input-sm">
					</div>
				</div>
				<div class="row text-center" style="margin-top: 8px;">
					<input type="submit" name="create_user" class="btn btn-primary input-sm" value="Create User">
				</div>
			</form>
			<h4 style="border-top: 1px solid black;"> Add Item </h4>
			<form target="" method="POST">
				<div class="row">
					<div class="col-sm-3 col-md-3 col-lg-3"></div>
					<div class="col-sm-6 col-md-6 col-lg-6">
						<label>Item Name</label>
						<input type="text" name="create_item_name" class="form-control input-sm">
					</div>
				</div>
				<div class="row text-center" style="margin-top: 8px;">
					<input type="submit" name="create_item" class="btn btn-primary input-sm" value="Create Item">
				</div>
			</form>
			<h4 style="border-top: 1px solid black;"> Add Supplier </h4>
			<form target="" method="POST">
				<div class="row">
					<div class="col-sm-3 col-md-3 col-lg-3"></div>
					<div class="col-sm-6 col-md-6 col-lg-6">
						<label>Supplier Name</label>
						<input type="text" name="create_supp_name" class="form-control input-sm">
					</div>
				</div>
				<div class="row text-center" style="margin-top: 8px;">
					<input type="submit" name="create_supp" class="btn btn-primary input-sm" value="Create Supplier">
				</div>
			</form>
			<h4 style="border-top: 1px solid black;"> Modify </h4>
			<div class="row">
				<div class="col-sm-4 col-md-4 col-lg-4 text-center">
					<a href="modify.php?type=user"> <button class="btn btn-primary input-sm"> User </button> </a>
				</div>
				<div class="col-sm-4 col-md-4 col-lg-4 text-center">
					<a href="modify.php?type=item"> <button class="btn btn-primary input-sm"> Item </button> </a>
				</div>
				<div class="col-sm-4 col-md-4 col-lg-4 text-center">
					<a href="modify.php?type=supp"> <button class="btn btn-primary input-sm"> Supplier </button> </a>					
				</div>
			</div>
			<h4 style="border-top: 1px solid black;"> Delete </h4>
			<div class="row" style="margin-bottom: 40px;">
				<div class="col-sm-4 col-md-4 col-lg-4 text-center">
					<form action="" method="POST">
						<select class="form-control" name="delete_user">
						<option value=""> Select User To Delete </option>
					<?php
						$sql_user = 'select user_id, user_name from can_user_mast where approval_level <> -1';
						$result_user = odbc_exec($conn, $sql_user);

						while(odbc_fetch_row($result_user)) {
							print '<option value="'.odbc_result($result_user, 1).'">'.odbc_result($result_user, 2).'</option>';
						}
					?>
					<input type="submit" name="del_user" class="pull-right btn btn-primary btn-sm" value="Delete User">
					</form>
				</div>
				<div class="col-sm-4 col-md-4 col-lg-4 text-center">
					<form action="" method="POST">
						<select class="form-control" name="delete_item">
						<option value=""> Select Item To Delete </option>
					<?php
						$sql_item = 'select item_id, item_name from can_item_mast';
						$result_item = odbc_exec($conn, $sql_item);

						while(odbc_fetch_row($result_item)) {
							print '<option value="'.odbc_result($result_item, 1).'">'.odbc_result($result_item, 2).'</option>';
						}
					?>
					<input type="submit" name="del_item" class="pull-right btn btn-primary btn-sm" value="Delete Item">
					</form>
				</div>
				<div class="col-sm-4 col-md-4 col-lg-4 text-center">
					<form action="" method="POST">
						<select class="form-control" name="delete_supp">
						<option value=""> Select Supplier To Delete </option>
					<?php
						$sql_supp = 'select supp_id, supp_name from can_supplier_mast';
						$result_supp = odbc_exec($conn, $sql_supp);

						while(odbc_fetch_row($result_supp)) {
							print '<option value="'.odbc_result($result_supp, 1).'">'.odbc_result($result_supp, 2).'</option>';
						}
					?>
					<input type="submit" name="del_supp" class="pull-right btn btn-primary btn-sm" value="Delete Suppier">
					</form>
				</div>
			</div>
		</div>
	</div>
</BODY>
</HTML>