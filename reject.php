<!DOCTYPE html>
<?php
/*
Page landed upon cancellation request.


*/
	# Session
	require('session.php');

	# Not for level zero level
	if($_SESSION['approval_level'] <= 1) {
		header('index.php');
	}

	$error = '';
	$info = '';

	$is_valid = 0;
	if(!isset($_GET['id'])) {
		$error = 'Please provide a transaction ID.';
	} else {
		$tran_id = $_GET['id'];
		if(strlen($tran_id) != 16) {
			$error = 'Please provide valid transaction ID.';
		} else {

			# Connect to DB
			include('connect.php');

			if(!$conn) {
				$error = 'Problem connecting to database.';
			} else {

				# SQL for getting forwarded user and status for transaction
				$sql_info = "select t2.aprv_id, t2.forwarded_to, t2.forwarded_by, t2.status,";
				$sql_info .= " t2.tran_id||'/'||lpad(substr(aprv_id, 18) - 1, 3, '0') as pre_id";
				$sql_info .= " from";
				$sql_info .= " (select max(aprv_id) aid from";
				$sql_info .= " can_transaction_aprv";
				$sql_info .= " group by tran_id";
				$sql_info .= " having tran_id = ?";
				$sql_info .= " ) t1";
				$sql_info .= " inner join";
				$sql_info .= " (select tran_id, aprv_id, forwarded_by, forwarded_to, status from";
				$sql_info .= " can_transaction_aprv";
				$sql_info .= " where tran_id = ?";
				$sql_info .= " ) t2";
				$sql_info .= " on t1.aid = t2.aprv_id";

				# Prepare statement
				$stmt_info = odbc_prepare($conn, $sql_info);

				# Execute
				if(!odbc_execute($stmt_info, Array($tran_id, $tran_id))) { # Error executing
					$error = 'Please provide a valid ID.';
					odbc_close($conn);
				} else {
					$row = odbc_fetch_array($stmt_info);
					if($row == false) {
						$error = 'Transaction not found.';
						odbc_close($conn);
					} else {
						
						# Get fields
						$valid_user = $row['FORWARDED_TO'];
						$status = $row['STATUS'];
						$from_user = $row['FORWARDED_BY'];
						$pre_aprv_id = $row['PRE_ID'];
						$aprv_id = $row['APRV_ID'];

						if(strcmp($valid_user, $_SESSION['user_id']) != 0) {
							$error = 'You are not auhorized for the action.';
							odbc_close($conn);
						} else if(strcmp($status, 'In progress') != 0) {
							$error = 'Transaction is not rejectable.';
							odbc_close($conn);
						} else {
							# Display form
							$is_valid = 1;

							if(isset($_POST['reject'])) {

								# Comment not given
								if(empty($_POST['comment'])) {
									$error = 'Please provide some comment.';
									odbc_close($conn);
								} else {
									$comment = $_POST['comment'];
/*select * from
(select forwarded_by from can_transaction_aprv
where aprv_id < 'S/2016/07/15/003/007'
and tran_id = 'S/2016/07/15/003'
and forwarded_to = '000003'
and forwarded_to <> forwarded_by)
where rownum <= 1
*/

									# SQL to cancel
									$sql_reject = "insert into can_transaction_aprv";
									$sql_reject .= "(aprv_id, tran_id, forwarded_by, forwarded_to, forward_date, comments, status)";
									$sql_reject .= " values((select tran_id||'/'||lpad(max(substr(aprv_id,18))+1,3,'0') from can_transaction_aprv group by tran_id having tran_id = ?),?,?,?,sysdate,?,?)";

									# Prepare
									$stmt_reject = odbc_prepare($conn, $sql_reject);

									# Execute
									odbc_execute($stmt_reject, Array($tran_id, $tran_id, $_SESSION['user_id'], $_SESSION['user_id'], $comment, 'Rejected'));

									# Get the user who forwarded it
									$sql_lst_usr = "select forwarded_by from";
									$sql_lst_usr .= " (select forwarded_by from";
									$sql_lst_usr .= " (select aprv_id, forwarded_by from";
									$sql_lst_usr .= " can_transaction_aprv";
									$sql_lst_usr .= " where tran_id = ?";
									$sql_lst_usr .= " ) t1";
									$sql_lst_usr .= " inner join";
									$sql_lst_usr .= " (select user_id, approval_level from";
									$sql_lst_usr .= " can_user_mast";
									$sql_lst_usr .= " where approval_level = ?";
									$sql_lst_usr .= " ) t2";
									$sql_lst_usr .= " on t1.forwarded_by = t2.user_id";
									$sql_lst_usr .= " order by aprv_id desc)";
									$sql_lst_usr .= " where rownum = 1";

									$result_lst_usr = odbc_prepare($conn, $sql_lst_usr);

									odbc_execute($result_lst_usr, Array($tran_id, $_SESSION['approval_level'] - 1));

									$user_pre = odbc_fetch_array($result_lst_usr);
									$user_pre = $user_pre['FORWARDED_BY'];

									# Save in aprv
									odbc_execute($stmt_reject, Array($tran_id, $tran_id, $_SESSION['user_id'], $user_pre, 'Pending', 'In progress'));

									odbc_close($conn);
									$is_valid = 0;

									$info = 'The request was rejected.';
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
			<form method="POST" action="reject.php?id=<?php print $tran_id; ?>">
				<div class="form-group">
					<label>Comment for rejection : </label>
					<textarea class="form-control" name="comment" rows="5"></textarea>
				</div>
				<input type="submit" class="btn btn-primary pull-right" name="reject" value="Reject Request">
			</form>
			<?php } ?>
		</div>
	</div>
</BODY>
</HTML>