<?php
/*
Page where non zero level users land after login. They approve requests forwarded by users at lower level.


*/
	# Manage session
	require('session.php');
	# Include header
	require('header.php');

	# Redirect if user approval level not valid
	if($_SESSION['approval_level'] <= 0) {
		header('Location: index.php');
	}

	$error_rcv = '';
	$error_fwd = '';
	$info_rcv = '';
	$info_fwd = '';

	include('connect.php');

	if(!$conn) {
		$error_rcv = 'Problem connecting to database.';
		$error_fwd = 'Problem connecting to database.';
	} else {
		
		# SQL for getting details of recieved requests
		$sql_rcv = "select t2.tran_id, t3.tran_create_date, t2.forward_date, t4.user_name, t2.status from";
		$sql_rcv .= " (select max(aprv_id) aid from";
		$sql_rcv .= " can_transaction_aprv";
		$sql_rcv .= " group by tran_id";
		$sql_rcv .= " ) t1";
		$sql_rcv .= " inner join";
		$sql_rcv .= " (select tran_id, forward_date, status, aprv_id, forwarded_by, forwarded_to from";
		$sql_rcv .= " can_transaction_aprv";
		$sql_rcv .= " where forwarded_to = '".$_SESSION['user_id']."'";
		$sql_rcv .= " and (status = 'In progress')";
		$sql_rcv .= " ) t2";
		$sql_rcv .= " on t1.aid = t2.aprv_id";
		$sql_rcv .= " inner join";
		$sql_rcv .= " can_transaction_mast t3";
		$sql_rcv .= " on t2.tran_id = t3.tran_id";
		$sql_rcv .= " inner join";
		$sql_rcv .= " can_user_mast t4";
		$sql_rcv .= " on t2.forwarded_by = t4.user_id";
		$sql_rcv .= " inner join";
		$sql_rcv .= " can_user_mast t5";
		$sql_rcv .= " on t2.forwarded_to = t5.user_id";
		$sql_rcv .= " order by t3.tran_create_date desc";

		# Execute
		$result_rcv = odbc_exec($conn, $sql_rcv);

		$rcv["tran_id"] = array();
		$rcv["create_date"] = array();
		$rcv["fwd_date"] = array();
		$rcv["fwd_by_name"] = array();

		# Save results
		while(odbc_fetch_row($result_rcv)) {
			array_push($rcv["tran_id"], odbc_result($result_rcv, 1));
			array_push($rcv["create_date"], odbc_result($result_rcv, 2));
			array_push($rcv["fwd_date"], odbc_result($result_rcv, 3));
			array_push($rcv["fwd_by_name"], odbc_result($result_rcv, 4));
		}

		$rcv_count = count($rcv["tran_id"]);
		if($rcv_count == 0) {
			$error_rcv = 'Nothing to show.';
		} else if($rcv_count == 1){
			$info_rcv = '1 request for you.';
		} else {
			$info_rcv = $rcv_count.' requests for you.';
		}

		# SQL for getting details for forwarded requests
		$sql_fwd = "select t2.tran_id, t3.tran_create_date, t2.forward_date, t4.user_name, t5.user_name, t2.status from";
		$sql_fwd .= " (select max(aprv_id) aid from";
		$sql_fwd .= " can_transaction_aprv";
		$sql_fwd .= " group by tran_id";
		$sql_fwd .= " ) t1";
		$sql_fwd .= " inner join";
		$sql_fwd .= " (select tran_id, forward_date, status, aprv_id, forwarded_by, forwarded_to from";
		$sql_fwd .= " can_transaction_aprv";
		$sql_fwd .= " where forwarded_by = '".$_SESSION['user_id']."'";
		$sql_fwd .= " and (forwarded_by <> forwarded_to";
		$sql_fwd .= " or status = 'Approved')";
		$sql_fwd .= " ) t2";
		$sql_fwd .= " on t1.aid = t2.aprv_id";
		$sql_fwd .= " inner join";
		$sql_fwd .= " can_transaction_mast t3";
		$sql_fwd .= " on t2.tran_id = t3.tran_id";
		$sql_fwd .= " inner join";
		$sql_fwd .= " can_user_mast t4";
		$sql_fwd .= " on t2.forwarded_by = t4.user_id";
		$sql_fwd .= " inner join";
		$sql_fwd .= " can_user_mast t5";
		$sql_fwd .= " on t2.forwarded_to = t5.user_id";
		$sql_fwd .= " order by t3.tran_create_date desc";

		# Execute
		$result_fwd = odbc_exec($conn, $sql_fwd);
		
		$fwd["tran_id"] = array();
		$fwd["create_date"] = array();
		$fwd["fwd_date"] = array();
		$fwd["fwd_by_name"] = array();
		$fwd["fwd_to_name"] = array();
		$fwd["status"] = array();

		# Save results
		while(odbc_fetch_row($result_fwd)) {
			$new_tran = odbc_result($result_fwd, 1);
			if(in_array($new_tran, $fwd["tran_id"])) # Keep only first transaction
				continue;
			array_push($fwd["tran_id"], $new_tran);
			array_push($fwd["create_date"], odbc_result($result_fwd, 2));
			array_push($fwd["fwd_date"], odbc_result($result_fwd, 3));
			array_push($fwd["fwd_by_name"], odbc_result($result_fwd, 4));
			array_push($fwd["fwd_to_name"], odbc_result($result_fwd, 5));
			array_push($fwd["status"], odbc_result($result_fwd, 6));
		}

		$fwd_count = count($fwd["tran_id"]);
		if($fwd_count == 0) {
			$error_fwd = 'Nothing to show.';
		} else if($fwd_count == 1) {
			$info_fwd = '1 request by you.';
		} else {
			$info_fwd = $fwd_count.' requests by you.';
		}

		odbc_close($conn);
	}
?>
<!DOCTYPE html>

<HTML>
<HEAD>
	<script type="text/javascript" src="js/jquery-2.1.4.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script type="text/javascript">
		function show(id) {
			
			// Get nav list elements
			var li_rcv = document.getElementById('li_rcv');
			var li_fwd = document.getElementById('li_fwd');
			// Set class to none (not "active")
			li_rcv.className = '';
			li_fwd.className = '';

			// Get display fields
			var fd_rcv = document.getElementById('fd_rcv');
			var fd_fwd = document.getElementById('fd_fwd');
			// Set display to none
			fd_rcv.style.display = 'none';
			fd_fwd.style.display = 'none';

			// Set li class and field display mode according to id from parameter
			if(id == 1) {
				li_rcv.className = 'active';
				fd_rcv.style.display = 'block';
			} else {
				li_fwd.className = 'active';
				fd_fwd.style.display = 'block';
			}
		}
	</script>
</HEAD>
<BODY>
	<?php
		print $header;
	?>
	<div class="container">
		<div class="container-fluid">
			<!-- Nav Tabs -->
			<ul class="nav nav-tabs">
				<li class="active" id="li_rcv" onclick="show(1);">
					<a href="#">
						Recieved Requests
					</a>
				</li>
				<li id="li_fwd" onclick="show(2);">
					<a href="#">
						Forwarded Requests
					</a>
				</li>
			</ul>
			<!-- Field showing recieved requests -->
			<div id="fd_rcv" style="display: block;">
				<br/>
				<h4> Showing all requests that you have recieved </h4>
				<div class="text-center" style="margin-bottom: 8px;">
				<?php
					if(strlen($error_rcv) > 0)
						print '<span class="label label-danger">'.$error_rcv.'</span>';
					if(strlen($info_rcv) > 0)
						print '<span class="label label-success">'.$info_rcv.'</span>';
				?>
				</div>
				<?php
					if($rcv_count != 0) {
				?>
				<table class="table table-hover">
					<thead>
						<th>
							#
						</th>
						<th>
							Transaction ID
						</th>
						<th>
							Create Time
						</th>
						<th>
							Forward Time
						</th>
						<th>
							Forwarded By
						</th>
						<th>
							Status
						</th>
					</thead>
						<?php
							for($i = 0 ; $i < $rcv_count ; $i ++ ) {
						?>
						<tr>
						<td>
							<?php print $i + 1; ?>
						</td>
						<td>
							<a href="tran.php?id=<?php print $rcv["tran_id"][$i]; ?>">
							<?php print $rcv["tran_id"][$i]; ?>
							</a>
						</td>
						<td>
							<?php print $rcv["create_date"][$i]; ?>
						</td>
						<td>
							<?php print $rcv["fwd_date"][$i]; ?>
						</td>
						<td>
							<?php print $rcv["fwd_by_name"][$i]; ?>
						</td>
						<td>
							<span class="label label-warning"> In progress </span>
						</td>
						</tr>
						<?php } # End for?>
				</table>
				<?php } # End if ?>
			</div>
			<div id="fd_fwd" style="display: none;">
				<br/>
				<h4> Showing all requests that you have forwarded </h4>
				<div class="text-center" style="margin-bottom: 8px;">
				<?php
					if(strlen($error_fwd) > 0)
						print '<span class="label label-danger">'.$error_fwd.'</span>';
					if(strlen($info_fwd) > 0)
						print '<span class="label label-success">'.$info_fwd.'</span>';
				?>
				</div>
				<?php
					if($fwd_count != 0) {
				?>
				<table class="table table-hover">
					<thead>
						<th>
							#
						</th>
						<th>
							Transaction ID
						</th>
						<th>
							Create Time
						</th>
						<th>
							Forward Time
						</th>
						<th>
							Forwarded By
						</th>
						<th>
							Forward To
						</th>
						<th>
							Status
						</th>
					</thead>
						<?php
							for($i = 0 ; $i < $fwd_count ; $i ++ ) {
						?>
						<tr>
						<td>
							<?php print $i + 1; ?>
						</td>
						<td>
							<a href="tran.php?id=<?php print $fwd["tran_id"][$i]; ?>">
							<?php print $fwd["tran_id"][$i]; ?>
							</a>
						</td>
						<td>
							<?php print $fwd["create_date"][$i]; ?>
						</td>
						<td>
							<?php print $fwd["fwd_date"][$i]; ?>
						</td>
						<td>
							<?php print $fwd["fwd_by_name"][$i]; ?>
						</td>
						<td>
							<?php print $fwd["fwd_to_name"][$i]; ?>
						</td>
						<td>
							<?php
								if(strcmp($fwd["status"][$i], 'In progress') == 0) {
									print '<span class="label label-warning">In progress</span>';
								} else if(strcmp($fwd["status"][$i], 'Approved') == 0) {
									print '<span class="label label-success">Approved</span>';
								} else {
									print '<span class="label label-danger">'.$fwd["status"][$i].'</span>';
								}
							?>
						</td>
						</tr>
						<?php } # End for?>
				</table>
				<?php } # End if ?>
			</div>
		</div>
	</div>
</BODY>
</HTML>