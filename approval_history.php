<?php
/*
Display history for a user, i.e. the transactions in which he was involved.


*/
	# Session
	require('session.php');

	# Not for level 0 users
	if($_SESSION['approval_level'] < 1) {
		header('index.php');
	}

	# If nothing was clicked (show for the day)
	if(!isset($_POST['search'])) {
		date_default_timezone_set('Asia/Kolkata');
		$_POST['from_date'] = date('Y-m-d');
		$_POST['to_date'] = date('Y-m-d');
		$_POST['search'] = 1;
	}

	$info = '';
	$error = '';

	if(isset($_POST['search'])) {
		if(empty($_POST['from_date']) || empty($_POST['to_date'])) {
			$error = " Dates can't be empty. ";
		} else {

			if(strcmp($_POST['from_date'], $_POST['to_date']) == 1) {
				$error = " 'From Date' can't come after 'To Date' ";
			} else {

				require('connect.php');

				if(!$conn) {
					$error = 'Problem connecting to database. Please try again later.';
				} else {

					$sql = "select t2.tran_id, t4.supp_name, t3.tran_create_date, t5.forward_date, t5.status from";
					$sql .= " (select t1.tran_id, max(t1.aprv_id) aid from";
					$sql .= " (select * from can_transaction_aprv";
					$sql .= " where (forwarded_by = '".$_SESSION['user_id']."'";
					$sql .= " or forwarded_to = '".$_SESSION['user_id']."')";
					$sql .= " and forward_date >= to_date('".$_POST['from_date']."', 'yyyy-mm-dd')";
					$sql .= " and forward_date <= to_date('".$_POST['to_date']."', 'yyyy-mm-dd') + 1";
					$sql .= " ) t1";
					$sql .= " group by t1.tran_id";
					$sql .= " ) t2";
					$sql .= " inner join";
					$sql .= " can_transaction_mast t3";
					$sql .= " on t2.tran_id = t3.tran_id";
					$sql .= " inner join";
					$sql .= " can_supplier_mast t4";
					$sql .= " on t3.tran_supp_id = t4.supp_id";
					$sql .= " inner join";
					$sql .= " can_transaction_aprv t5";
					$sql .= " on t2.aid = t5.aprv_id";
					$sql .= " order by t5.forward_date";

					$result = odbc_exec($conn, $sql);

					$search_result['tran_id'] = Array();
					$search_result['supp_name'] = Array();
					$search_result['create_date'] = Array();
					$search_result['fwd_date'] = Array();
					$search_result['status'] = Array();
					while(odbc_fetch_row($result)) {
						array_push($search_result['tran_id'], odbc_result($result, 1));
						array_push($search_result['supp_name'],odbc_result($result,2));
						array_push($search_result['create_date'],odbc_result($result,3));
						array_push($search_result['fwd_date'],odbc_result($result,4));
						array_push($search_result['status'],odbc_result($result,5));
					}

					$number = count($search_result['tran_id']);

					if($number == 0) {
						$error = 'No results found.';
					} else if($number == 1) {
						$info = '1 result found.';
					} else {
						$info = $number.' results found.';
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
		require('header.php');
		print $header;
	?>
	<div class="container">
		<div class="container-fluid">
			<form action="" method="post">
				<div class="row">
					<div class="col-sm-2 col-md-1 col-lg-1">
					</div>
					<div class="col-sm-4 col-md-4 col-lg-4">
						<label>From Date</label>
						<div class="form-group">
							<input type="date" class="form-control" name="from_date">
						</div>
					</div>
					<div class="col-sm-2 col-md-1 col-lg-2">
					</div>
					<div class="col-sm-4 col-md-4 col-lg-4">
						<label>To Date</label>
						<div class="form-group">
							<input type="date" class="form-control" name="to_date">
						</div>
					</div>
					<div class="pull-right">
						<br/>
						<input class="btn btn-primary" type="submit" name="search" value="Search">
					</div>
				</div>
			</form>
			<div class="text-center" style="margin-bottom:30px;">
				<?php
					if(strlen($error))
						print '<span class="label label-danger">'.$error.'</span>';
					if(strlen($info))
						print '<span class="label label-success">'.$info.'</span>';
				?>
			</div>
			<?php
				if($number!=0){
					?>
					<div class="table-responsive" style="margin-top: 20px;">
						<table class="table table-hover">
							<thead>
								<th>
									#
								</th>
								<th>
									Transaction ID
								</th>
								<th>
									Supplier								
								</th>
								<th>
									Create Time
								</th>
								<th>
									Forward Time									
								</th>
								<th>
									status
								</th>
							</thead>
							<?php
								for( $i = 0 ; $i < $number ; $i++ ){
									print "<tr>";
									print "<td>".($i+1)."</td>";
									print '<td><a href="tran.php?id='.$search_result['tran_id'][$i].'">'.$search_result['tran_id'][$i]."</a></td>";
									print "<td>".$search_result['supp_name'][$i]."</td>";
									print "<td>".$search_result['create_date'][$i]."</td>";
									print "<td>".$search_result['fwd_date'][$i]."</td>";
									print "<td>";
									if(strcmp($search_result['status'][$i], 'In progress') == 0)
										print '<span class="label label-warning">'.$search_result['status'][$i].'</span>';
									else if(strcmp($search_result['status'][$i], 'Approved') == 0)
										print '<span class="label label-success">'.$search_result['status'][$i].'</span>';
									else
										print '<span class="label label-danger">'.$search_result['status'][$i].'</span>';
									print "</td>";
									print "</tr>";
								}
							?>
						</table>
					</div>
				<?php  }
			?>

		</div>
	</div>
</BODY>
</HTML>