<?php
/*
Utility to cancel a transaction by its ID from corresponding GET parameter.


*/
	# Session
	require('session.php');
	# Header
	require('header.php');

	# Message variables
	$error = '';
	$info = '';
	$is_valid = 0;

	if(!isset($_GET['id'])) { # No ID
		$error = 'Please provide a transaction ID.';
	} else {
		$tran_id = $_GET['id'];
		if(strlen($tran_id) != 16) { # Invalid length
			$error = 'Please provide valid transaction ID.';
		} else {

			# Connect to DB
			require('connect.php');

			if(!$conn) {
				$error = "Problem connecting to database.";
			} else {

				# SQL to get latest forwarding user and other info
				$sql_fwd_by = "select forwarded_by, forwarded_to, status from";
				$sql_fwd_by .= " (select max(aprv_id) aid from";
				$sql_fwd_by .= " can_transaction_aprv";
				$sql_fwd_by .= " group by tran_id";
				$sql_fwd_by .= " having tran_id = ?";
				$sql_fwd_by .= " ) t1";
				$sql_fwd_by .= " inner join";
				$sql_fwd_by .= " (select aprv_id, forwarded_to, forwarded_by, status from";
				$sql_fwd_by .= " can_transaction_aprv";
				$sql_fwd_by .= " where tran_id = ?";
				$sql_fwd_by .= " ) t2";
				$sql_fwd_by .= " on t1.aid = t2.aprv_id";

				# Prepare statement
				$stmt = odbc_prepare($conn, $sql_fwd_by);

				# Execute
				if(!odbc_execute($stmt, array($tran_id, $tran_id))) { # Problem in execution
					$error = "Please provide a valid transaction ID.";
					odbc_close($conn);
				} else {
					$row = odbc_fetch_array($stmt);
					if($row == false) { # No rows found
						$error = "Transaction not found.";
						odbc_close($conn);
					} else {

						# Get rows
						$valid_user = $row['FORWARDED_BY'];
						$status = $row['STATUS'];
						$to_user = $row['FORWARDED_TO'];

						if(strcmp($_SESSION['user_id'], $valid_user) != 0) { # Unauthorized
							$error = "You are not authorized for the action.";
							odbc_close($conn);
						} else if(strcmp($status, 'Rejected') == 0 || strcmp($status, 'Cancelled') == 0) { # Not cancellable
							$error = "Transaction alread cancelled or rejected.";
							odbc_close($conn);
						} else if(strcmp($to_user, $valid_user) == 0)	{ # Already cancelled
							$error = "You have already cancelled the request on transaction.";
						} else {
							$is_valid = 1;
							if(isset($_POST['confirm'])) { # Submit was clicked
								
								# SQL to add cancellation status
								$sql_add = "insert into can_transaction_aprv";
								$sql_add = $sql_add."(aprv_id, tran_id, forwarded_by, forwarded_to, forward_date, comments, status)";
								$sql_add = $sql_add." values((select tran_id||'/'||lpad(max(substr(aprv_id,18))+1,3,'0') from can_transaction_aprv group by tran_id having tran_id = ?),?,?,?,sysdate,?,?)";

								# Prepare
								$stmt_add = odbc_prepare($conn, $sql_add);

								# Execute
								odbc_execute($stmt_add, array($tran_id, $tran_id, $valid_user, $valid_user, $_POST['comment'], 'Cancelled'));
								if($_SESSION['approval_level'] > 0)
									odbc_execute($stmt_add, array($tran_id, $tran_id, $valid_user, $valid_user, 'Pending', 'In progress'));

								$info = "Request Cancelled.";
								odbc_close($conn);
								$is_valid = 0;
							}
						}
					}
				}
			}
		}
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
		print $header;
	?>
	<div class="container">
		<div class="container-fluid">
			<br/>
			<div class="text-center">
				<?php
					if(strlen($info) > 0)
						print '<span class="label label-success">'.$info.'</span>';
					if(strlen($error) > 0)
						print '<span class="label label-danger">'.$error.'</span>';
				?>
			</div>
			<?php
				if($is_valid == 1) {
					print '<form action="cancel.php?id='.$tran_id.'" method="POST">';
					print '<div class="form-group">';
					print '<label>Comment for cancellation:</label>';
					print '<textarea class="form-control" rows="5" name="comment" maxlength="250"></textarea>';
					print '</div>';
					print '<input type="submit" class="btn btn-primary" name="confirm" value="Cancel Request">';
					print '</form>';
				}
			?>
		</div>
	</div>
</BODY>
</HTML>