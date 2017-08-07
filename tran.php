<!DOCTYPE html>
<?php
/*
Page to show all details for a transaction.

*/
	# Include utilities
	include('session.php');
	include('header.php');
?>
<HTML lang="en">
<HEAD>
	<script type="text/javascript" src="js/jquery-2.1.4.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script type="text/javascript">

		function show(number) {
			/*
			* Script to toggle fields as presented on page.
			* Argument: 
			* 	number: Field number to show
			*				1 - Basic details
			*				2 - Approval Stack
			* 				else - Items Details
			*/

			// Get nav list items by ID
			li_dtl = document.getElementById('nav-dtl');
			li_item = document.getElementById('nav-item');
			li_aprv = document.getElementById('nav-aprv');

			// Get divisions containing the fields by ID
			div_dtl = document.getElementById('basic_details');
			div_aprv = document.getElementById('approval_stack');
			div_item = document.getElementById('item_details');

			// Remove all applied classes from nav LIs
			li_dtl.className = '';
			li_aprv.className = '';
			li_item.className = '';

			// Hide all fields
			div_dtl.style.display = 'none';
			div_item.style.display = 'none';
			div_aprv.style.display = 'none';

			// Change class name of list item and make field visible according to the argument
			if(number == 1) {
				li_dtl.className = 'active';
				div_dtl.style.display = 'block';
			}
			else if(number == 2) {
				li_aprv.className = 'active';
				div_aprv.style.display = 'block';
			}
			else {
				li_item.className = 'active';
				div_item.style.display = 'block';
			}
		}
	</script>
</HEAD>
<BODY>
	<?php 

	print $header; 

	if(!isset($_GET['id'])) { # No ID provided
		print '<div class="text-center">';
		print '<span class="label label-danger">';
		print 'Please provide a valid ID.';
		print '</span>';
		print '</div>';
	} else {
		$pattern = '/^.*/';
		if (strlen($_GET['id']) != 16) { # Length not correct
			header('Location: 404.php');
		} else {

			# Connection utility
			include('connect.php');

			# SQL to get details about transaction
			$sql = 'select supp_id, supp_name, user_id, user_name, tran_create_date from';
			$sql = $sql.' (can_transaction_mast inner join can_supplier_mast on can_transaction_mast.tran_supp_id = can_supplier_mast.supp_id)';
			$sql = $sql.' inner join can_user_mast on can_transaction_mast.tran_create_user = can_user_mast.user_id';
			$sql = $sql." where tran_id = '".$_GET['id']."'";

			# Execute
			$result = odbc_exec($conn, $sql);

			# Save results
			while(odbc_fetch_row($result)) {
				$transaction["id"] = $_GET['id'];
				$transaction["supp_id"] = odbc_result($result, 1);
				$transaction["supp_name"] = odbc_result($result, 2);
				$transaction["user_id"] = odbc_result($result, 3);
				$transaction["user_name"] = odbc_result($result, 4);
				$transaction["create_date"] = odbc_result($result, 5);
			}

			if(!isset($transaction["id"])) { # No result found
				# Close connection and redirect
				odbc_close($conn);
				header('Location: 404.php');
			}

			# SQL for getting item details
			$sql_items = 'select t1.item_id, t2.item_name, t1.unit, t1.description, t1.quantity, t1.rate from can_transaction_dtl t1 inner join can_item_mast t2 on t1.item_id=t2.item_id';
			$sql_items = $sql_items." where tran_id = '".$transaction['id']."'";

			# Execute
			$result_item = odbc_exec($conn, $sql_items);

			# Save results
			$transaction["items"] = array();
			$transaction["items"]["item_id"] = array();
			$transaction["items"]["item_name"] = array();
			$transaction["items"]["unit"] = array();
			$transaction["items"]["desc"] = array();
			$transaction["items"]["qty"] = array();
			$transaction["items"]["rate"] = array();

			while(odbc_fetch_row($result_item)) {
				array_push($transaction["items"]["item_id"], odbc_result($result_item, 1));
				array_push($transaction["items"]["item_name"], odbc_result($result_item, 2));
				array_push($transaction["items"]["unit"], odbc_result($result_item, 3));
				array_push($transaction["items"]["desc"], odbc_result($result_item, 4));
				array_push($transaction["items"]["qty"], odbc_result($result_item, 5));
				array_push($transaction["items"]["rate"], odbc_result($result_item, 6));
			}

			# SQL for getting approval stats
			$sql_aprv = 'select t2.user_id, t2.user_name, t3.user_id, t3.user_name, t1.forward_date, t1.comments, t1.status, to_number(substr(t1.aprv_id, 18)) from';
			$sql_aprv = $sql_aprv.' can_transaction_aprv t1 inner join can_user_mast t2 on t1.forwarded_by = t2.user_id inner join can_user_mast t3 on t1.forwarded_to = t3.user_id';
			$sql_aprv = $sql_aprv." where t1.tran_id = '".$transaction['id']."'";
			$sql_aprv = $sql_aprv." order by aprv_id desc";

			# Execute
			$result_aprv = odbc_exec($conn, $sql_aprv);

			# Save results
			$transaction["aprv"] = array();
			$transaction["aprv"]["to_id"] = array();
			$transaction["aprv"]["to_name"] = array();
			$transaction["aprv"]["by_id"] = array();
			$transaction["aprv"]["by_name"] = array();
			$transaction["aprv"]["date"] = array();
			$transaction["aprv"]["comment"] = array();
			$transaction["aprv"]["status"] = array();
			$transaction["aprv"]["cancel"] = array();
			$transaction["aprv"]["approve"] = array();
			$transaction["aprv"]["id"] = array();

			while(odbc_fetch_row($result_aprv)) {
				array_push($transaction["aprv"]["by_id"], odbc_result($result_aprv, 1));
				array_push($transaction["aprv"]["by_name"], odbc_result($result_aprv, 2));
				array_push($transaction["aprv"]["to_id"], odbc_result($result_aprv, 3));
				array_push($transaction["aprv"]["to_name"], odbc_result($result_aprv, 4));
				array_push($transaction["aprv"]["date"], odbc_result($result_aprv, 5));
				array_push($transaction["aprv"]["comment"], odbc_result($result_aprv, 6));

				$cancel = 1;
				$approve = 0;
				# Decorate status
				$status = odbc_result($result_aprv, 7);
				if(strcmp($status, "In progress") == 0) {
					$approve = 1;
					$status = '<span class="label label-warning">'.$status."</span>";
				}
				else if(strcmp($status, "Approved") == 0) {
					$status = '<span class="label label-success">'.$status."</span>";
					$cancel = 0;
				}
				else {
					$cancel = 0;
					$status = '<span class="label label-danger">'.$status."</span>";
				}
				array_push($transaction["aprv"]["status"], $status);

				array_push($transaction["aprv"]["id"], odbc_result($result_aprv, 8));

				array_push($transaction["aprv"]["cancel"], $cancel);
				array_push($transaction["aprv"]["approve"], $approve);
			}

			$transaction['cancel'] = 0;
			if(strcmp($transaction["aprv"]["by_id"][0], $_SESSION['user_id']) == 0 && $transaction["aprv"]["cancel"][0] == 1 && $transaction['aprv']['to_id'][0] != $transaction['aprv']['by_id'][0])
				$transaction["cancel"] = 1;

			$transaction['approve'] = 0;
			if(strcmp($transaction["aprv"]["to_id"][0], $_SESSION['user_id']) == 0 && $transaction["aprv"]["approve"][0] == 1)
				$transaction["approve"] = 1;

			# Close connection
			odbc_close($conn);

		}
	}
	?>

	<div class="container">
		<div class="container-fluid">
			<!-- Navigation -->
			<ul class="nav nav-tabs">
				<li id="nav-dtl" class="active" role="presentation" onclick="show(1);"><a href="#">Basic Details</a></li>
				<li id="nav-aprv" role="presentation" onclick="show(2);"><a href="#">Approval Stack</a></li>
				<li id="nav-item" role="presentation" onclick="show(3);"><a href="#">Items</a></li>
			</ul>
			<!--Block containing basic details. Shown by default.-->
			<div id="basic_details" style="display: block;">
				<h3>Basic Details</h3>
				<table class="table table-hover table">
					<tr>
						<td>
							<b> Transaction ID </b>
						</td>
						<td>
							<?php print $transaction['id']; ?>
						</td>
						<td></td>
						<td>
							<b> Transaction Time </b>
						</td>
						<td>
							<?php print $transaction['create_date']; ?>
						</td>
					</tr>
					<tr>
						<td>
							<b> Created by ID </b>
						</td>
						<td>
							<?php print $transaction['user_id']; ?>
						</td>
						<td></td>
						<td>
							<b> Created by Name </b>
						</td>
						<td>
							<?php print $transaction['user_name']; ?>
						</td>
					</tr>
					<tr>
						<td>
							<b> Supplier ID </b>
						</td>
						<td>
							<?php print $transaction['supp_id']; ?>
						</td>
						<td></td>
						<td>
							<b> Supplier Name </b>
						</td>
						<td>
							<?php print $transaction['supp_name']; ?>
						</td>
					</tr>
					<tr>
						<td>
							<b> Status <b>
						</td>
						<td>
							<?php print $transaction["aprv"]["status"][0]; ?>
						</td>
						<td></td>
						<td>
							<b> Forwarded To </b>
						</td>
						<td>
							<?php print $transaction["aprv"]["to_name"][0]; ?>
						</td>
					</tr>
					<?php
						if($transaction['cancel'] == 1) {
							print '<tr>';
							print '<td></td>';
							print '<td></td>';
							print '<td></td>';
							print '<td></td>';
							print '<td><a href="cancel.php?id='.$transaction['id'].'"><button class="btn btn-primary">Cancel Request</button></a></td>';
							print '</tr>';
						}
						if($transaction['approve'] == 1) {
					?>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>
								<?php if($_SESSION['approval_level'] > 1) { ?>
								<a href="reject.php?id=<?php print $transaction['id']?>">
									<button class="btn btn-default">Reject</button>
								</a>
								<?php } ?>
								&nbsp;
								<a style="hover{color: 'White'};" href="forward.php?id=<?php print $transaction['id']?>">
									<button class="btn btn-primary">Forward/Approve</button>
								</a>
							</td>
						</tr>
					<?php } ?>
				</table>
			</div>

			<!--Block containing item details. Hidden by default.-->
			<div id="item_details" style="display: none;">
				<h3>Item Details</h3>
				<table class="table table-hover">
					<thead>
						<th>#</th>
						<th>Item No</th>
						<th>Name</th>
						<th><span class="pull-right">Quantity</span></th>
						<th>Unit</th>
						<th>Description</th>
						<th>Rate</th>
						<th>Price</th>
					</thead>
					<?php
						$sum = 0.0;
						for($i = 0 ; $i < count($transaction["items"]["item_id"]) ; $i ++){
							print "<tr>";
							print "<td>".($i+1)."</td>";
							print "<td>".$transaction["items"]["item_id"][$i]."</td>";
							print "<td>".$transaction["items"]["item_name"][$i]."</td>";
							print '<td><span class="pull-right">'.$transaction["items"]["qty"][$i]."</span></td>";
							print "<td>".$transaction["items"]["unit"][$i]."</td>";
							print "<td>".$transaction["items"]["desc"][$i]."</td>";
							print "<td>".number_format(floatval($transaction["items"]["rate"][$i]), 2)."</td>";
							$price = (floatval($transaction["items"]["qty"][$i]) * floatval($transaction["items"]["rate"][$i]));
							print "<td>".number_format($price, 2)."</td>";
							print "</tr>";
							$sum += $price;
						}
					?>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td><b>Total</b></td>
						<td><?php print number_format($sum, 2); ?></td>
				</table>
			</div>
			<!--Block containing approval details. Hidden by default.-->
			<div id="approval_stack" style="display: none;">
				<h3>Approval Stack</h3>
				<table class="table table-hover">
					<thead>
						<th>#</th>
						<th>FWD By</th>
						<th>FWD To</th>
						<th>FWD Date</th>
						<th>Status</th>
						<th>Comment</th>
					</thead>
					<?php
						for($i = 0 ; $i < count($transaction["aprv"]["to_id"]) ; $i ++) {
							print "<tr>";
							print "<td>".$transaction["aprv"]["id"][$i]."</td>";
							print "<td>".$transaction["aprv"]["by_name"][$i]."</td>";
							print "<td>".$transaction["aprv"]["to_name"][$i]."</td>";
							print "<td>".$transaction["aprv"]["date"][$i]."</td>";
							print "<td>".$transaction["aprv"]["status"][$i]."</td>";
							print "<td>".$transaction["aprv"]["comment"][$i]."</td>";
							print "</tr>";
						}
					?>
				</table>
			</div>
		</div>
	</div>
</BODY>
</HTML>