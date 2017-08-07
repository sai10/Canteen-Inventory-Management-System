<!DOCTYPE html>
<?php
/*
Page landed upon forward request.


*/

	# Session
	require('session.php');

	# Not for level 0 users
	if($_SESSION['approval_level'] <= 0) {
		header('Location: index.php');
	}

	$error = '';
	$info = '';
	$number_items = 0;

	$is_valid = 0;
	if(!isset($_GET['id'])) {
		$error = 'Please provide a transaction ID.';
	} else {
		$tran_id = $_GET['id'];
		if(strlen($tran_id) != 16) {
			$error = 'Please provide a valid transaction ID.';
		} else {
			
			# Connect to database
			require('connect.php');

			if(!$conn) {
				$error = 'Problem connecting to database.';
			} else {

				# SQL for getting forwarded user and status for transaction
				$sql_info = "select t2.forwarded_to, t2.status from";
				$sql_info .= " (select max(aprv_id) aid from";
				$sql_info .= " can_transaction_aprv group by tran_id";
				$sql_info .= " having tran_id = ?";
				$sql_info .= " ) t1";
				$sql_info .= " inner join";
				$sql_info .= " can_transaction_aprv t2";
				$sql_info .= " on t1.aid = t2.aprv_id";

				# Prepare statement
				$stmt_info = odbc_prepare($conn, $sql_info);

				# Execute
				if(!odbc_execute($stmt_info, Array($tran_id))) { # Error executing query
					$error = 'Please provide a valid transaction ID.';
					odbc_close($conn);
				} else {
					$row = odbc_fetch_array($stmt_info);
					if($row == false) {
						$error = 'Transaction not found.';
						odbc_close($conn);
					} else {

						# Get rows
						$valid_user = $row['FORWARDED_TO'];
						$status = $row['STATUS'];

						if(strcmp($valid_user, $_SESSION['user_id']) != 0) { # Unauthorized
							$error = 'You are not authorized to take the action.';
						} else if(strcmp($status, 'In progress') != 0) { # Not in progress
							$error = 'Transaction can\'t be forwarded.';
						} else { # Forward it
							$is_valid = 1;

							# SQL to get forwardable user(s)
							$sql_fwd_usr = "select user_id, user_name";
							$sql_fwd_usr .= " from can_user_mast";
							$sql_fwd_usr .= " where approval_level = ".($_SESSION['approval_level']+1);

							# Execute
							$result_fwd_users = odbc_exec($conn, $sql_fwd_usr);

							# Save results
							$fwd_user["id"] = array();
							$fwd_user["name"] = array();
							while(odbc_fetch_row($result_fwd_users)) {
								array_push($fwd_user["id"], odbc_result($result_fwd_users, 1));
								array_push($fwd_user["name"], odbc_result($result_fwd_users, 2));
							}

							# Get user count
							$fwd_user_count = count($fwd_user["id"]);

							$continue = 1;
							if($_SESSION['approval_level'] == 1) {

								# Get items for transaction with all details
								$sql_item = "select t1.item_id, t2.item_name, t1.unit, t1.description, t1.quantity, t1.rate from can_transaction_dtl t1";
								$sql_item .= " inner join";
								$sql_item .= " can_item_mast t2";
								$sql_item .= " on t1.item_id = t2.item_id";
								$sql_item .= " where tran_id = ?";

								$stmt_item = odbc_prepare($conn, $sql_item);
								odbc_execute($stmt_item, Array($tran_id));

								$item["id"] = array();
								$item["name"] = array();
								$item["unit"] = array();
								$item["desc"] = array();
								$item["qty"] = array();
								$item["rate"] = array();

								$row_item = odbc_fetch_array($stmt_item);
								while($row_item != false) {
									array_push($item["id"], $row_item["ITEM_ID"]);
									array_push($item["name"], $row_item["ITEM_NAME"]);
									array_push($item["unit"], $row_item["UNIT"]);
									array_push($item["desc"], $row_item["DESCRIPTION"]);
									array_push($item["qty"], $row_item["QUANTITY"]);
									array_push($item["rate"], $row_item["RATE"]);

									$row_item = odbc_fetch_array($stmt_item);
								}

								$number_items = count($item["id"]);
								if(isset($_POST['forward']))
									for($i = 0 ; $i < $number_items ; $i ++)
										if(floatval($_POST["rate"][$i]) <= 0.0 || floatval($_POST["qty"][$i]) <= 0)
											$continue = 0;
							}

							# If forward was clicked
							if(isset($_POST['forward'])) {

								# Field was empty
								if(empty($_POST['comments'])) {
									$error = 'Please provide some comment.';
								} else {

									# If no user was selected but they are there
									if(!isset($_POST['fwd_user'])) {
										$_POST['fwd_user'] = 'Forward To';
									}
									if(strcmp($_POST['fwd_user'], 'Forward To') == 0 && $fwd_user_count != 0) {
										$error = 'Please select a user to forward.';
									} else {

										if($continue == 0) {
											$error = 'Please provide correct quantity or rate.';
										} else {

											# Change rates
											if($_SESSION['approval_level'] == 1) {
												$sql_rate = "update can_transaction_dtl";
												$sql_rate .= " set rate = ?";
												$sql_rate .= " where tran_id = ?";
												$sql_rate .= " and item_id = ?";

												$stmt_rate = odbc_prepare($conn, $sql_rate);

												$sql_qty = "update can_transaction_dtl";
												$sql_qty .= " set quantity = ?";
												$sql_qty .= " where tran_id = ?";
												$sql_qty .= " and item_id = ?";

												$stmt_qty = odbc_prepare($conn, $sql_qty);

												for($i = 0 ; $i < $number_items ; $i ++) {
													odbc_execute($stmt_rate, Array($_POST["rate"][$i], $tran_id, $item["id"][$i]));
													odbc_execute($stmt_qty, Array($_POST['qty'][$i], $tran_id, $item["id"][$i]));
												}
											}

											# Mark approved for current transaction
											# SQL
											$sql_add = "insert into can_transaction_aprv";
											$sql_add .= "(aprv_id, tran_id, forwarded_by, forwarded_to, forward_date, comments, status)";
											$sql_add .= " values(";
											$sql_add .= " (select tran_id||'/'||lpad(max(substr(aprv_id,18))+1,3,'0') from";
											$sql_add .= " can_transaction_aprv group by tran_id";
											$sql_add .= " having tran_id = ?)";
											$sql_add .= ",?,?,?,sysdate,?,'Approved')";

											# Prepare
											$stmt_mark = odbc_prepare($conn, $sql_add);

											# Execute
											odbc_execute($stmt_mark, Array($tran_id, $tran_id, $valid_user, $valid_user, $_POST['comments']));

											# Add new element to approval stack if possible
											if($fwd_user_count != 0) { # There is a user who gets the forwarded request

												# SQL
												$fwd_user_id = substr($_POST['fwd_user'], -6);
												$sql_add = "insert into can_transaction_aprv";
												$sql_add .= "(aprv_id, tran_id, forwarded_by, forwarded_to, forward_date, comments, status)";
												$sql_add .= " values(";
												$sql_add .= " (select tran_id||'/'||lpad(max(substr(aprv_id,18))+1,3,'0') from";
												$sql_add .= " can_transaction_aprv group by tran_id";
												$sql_add .= " having tran_id = ?)";
												$sql_add .= ",?,?,?,sysdate,'Pending','In progress')";

												# Prepare
												$stmt_add = odbc_prepare($conn, $sql_add);

												# Execute
												odbc_execute($stmt_add, Array($tran_id, $tran_id, $valid_user, $fwd_user_id));
											}

											$info = 'Forward/Approval successful.';
											odbc_close($conn);

											$is_valid = 0;
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
?>
<HTML>
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
			<div class="text-center">
				<?php
					if(strlen($info) > 0)
						print '<span class="label label-success">'.$info.'</span>';
					if(strlen($error) > 0)
						print '<span class="label label-success">'.$error.'</span>';
				?>
			</div>
			<?php if($is_valid == 1) { ?>
			<form action="forward.php?id=<?php print $tran_id; ?>" method="POST">
					<?php if($_SESSION['approval_level'] == 1) { ?>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<th>#</th>
								<th>Item ID</th>
								<th>Name</th>
								<th>Quantity</th>
								<th>Unit</th>
								<th>Rate</th>
								<th>Description</th>
							</thead>
							<?php for($i = 0 ; $i < $number_items ; $i ++) {
								print "<tr>";
								print "<td>".($i + 1)."</td>";
								print "<td>".$item["id"][$i]."</td>";
								print "<td>".$item["name"][$i]."</td>";
								print "<td>";
								print '<input type="number" step="0.001" class="form-control input-sm" name="qty[]" value="'.$item["qty"][$i].'">';
								print "</td>";
								print "<td>".$item["unit"][$i]."</td>";
								print "<td>";
								print '<input type="number" step="0.01" class="form-control input-sm" name="rate[]" value="'.$item["rate"][$i].'">';
								print "</td>";
								print "<td>".$item["desc"][$i]."</td>";
								print "</tr>";
							}
							?>
						</table>
					</div>
					<?php } ?>
				<div class="form-group">
					<label> Comment for approval : </label>
					<textarea class="form-control" rows=5 name="comments" maxlen="250"></textarea>
					</div>
					<?php
						if($fwd_user_count == 0) print "Please note that your submission will approve the transaction completely as you have nobody above you in the hierarchy.";
						else { 
					?>
					<span class="pull-left">
						<select class="form-control" name="fwd_user">
							<option>Forward To</option>
							<?php 
							for($i = 0 ; $i < $fwd_user_count ; $i ++ ) {

								print "<option>";
								print $fwd_user["name"][$i].' - '.$fwd_user["id"][$i];
								print "</option>";
							}}
							?>
						</select>
					</span>
					<span class="pull-right">
						<input type="submit" class="btn btn-primary" name="forward" value="<?php if($fwd_user_count == 0) print 'Approve'; else print 'Forward';?>">
					</span>
					</div>
					</div>
				</div>
			</form>
			<?php } ?>
		</div>
	</div>
</BODY>
</HTML>