<?php
/*
Page to show detailed report about transactions to user.


*/
	require('session.php');
	require('connect.php');

	if(!$conn) {
		$error = 'Problem connecting to database.';
	}
?>
<!DOCTYPE html>
<HTML lang="en">
<HEAD>
	<script type="text/javascript" src="js/jquery-2.1.4.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
</HEAD>
<BODY>
	<?php
		include('header.php');
		print $header;
	?>
	<div class="container">
		<div class="container-fluid">
			<form method="POST" action="show_report.php" target="_blank">
				<div class="row" style="margin-top: 30px;">
					<div class="col-sm-4 col-md-4 col-lg-4">
						<div class="form-group">
							<label>Supplier</label>
							<select name="supplier" class="form-control input-sm">
								<option value="0">All</option>
								<?php
									$sql_supplier = "select supp_id, supp_name from";
									$sql_supplier .= " can_supplier_mast";

									$result_supplier = odbc_exec($conn, $sql_supplier);

									while(odbc_fetch_row($result_supplier)) {
										$supp_id = odbc_result($result_supplier, 1);
										$supp_name = odbc_result($result_supplier, 2);

										print '<option value="'.$supp_id.'">';
										print $supp_name;
										print '</option>';
									}
								?>
							</select>
						</div>
					</div>
					<div class="col-sm-4 col-md-4 col-lg-4">
						<div class="form-group">
							<label>Created By</label>
							<select name="create_by" class="form-control input-sm">
								<option value="0">All</option>
								<?php
									$sql_user = "select user_id, user_name from";
									$sql_user .= " can_user_mast";
									$sql_user .= " where approval_level = 0";

									$result_user = odbc_exec($conn, $sql_user);

									while(odbc_fetch_row($result_user)) {
										$user_id = odbc_result($result_user, 1);
										$user_name = odbc_result($result_user, 2);

										print '<option value="'.$user_id.'">';
										print $user_name;
										print '</option>';
									}
								?>
							</select>
						</div>
					</div>
					<div class="col-sm-4 col-md-4 col-lg-4">
						<div class="form-group">
							<label>Item</label>
							<select name="item" class="form-control input-sm">
								<option value="0">All</option>
								<?php
									$sql_item = "select item_id, item_name from";
									$sql_item .= " can_item_mast";

									$result_item = odbc_exec($conn, $sql_item);

									while(odbc_fetch_row($result_item)) {
										$item_id = odbc_result($result_item, 1);
										$item_name = odbc_result($result_item, 2);

										print '<option value="'.$item_id.'">';
										print $item_name;
										print '</option>';
									}
								?>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-2 col-md-2 col-lg-2"></div>
					<div class="col-sm-4 col-md-4 col-lg-4">
						<label>From Date (mm/dd/yyyy)</label>
						<?php
							date_default_timezone_set('Asia/Kolkata');
							$month_start = date('Y-m').'-01';
							print '<input class="form-control input-sm" type="date" name="from_date" value="'.$month_start.'">';
						?>
					</div>
					<div class="col-sm-4 col-md-4 col-lg-4">
						<label>To Date (mm/dd/yyyy)</label>
						<?php
							$month_end = date('Y-m-t');
							print '<input class="form-control input-sm" type="date" name="to_date" value="'.$month_end.'">';
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-2 col-md-2 col-lg-2"></div>
					<div class="col-sm-4 col-md-4 col-lg-4">
						<div class="checkbox text-center">
							<label>
								<input type="checkbox" name="approved_only" value="1">
								Approved Only
							</label>
						</div>
					</div>
					<div class="col-sm-4 col-md-4 col-lg-4">
						<div class="checkbox text-center">
							<label>
								<input type="checkbox" name="show_item" value="1">
								Show Items for each transaction
							</label>
						</div>
					</div>
				</div>
				<div class="text-center">
					<input type="submit" value="Get Report" class="btn btn-primary btn-sm">
				</div>
			</form>
		</div>
	</div>
</BODY>
</HTML>