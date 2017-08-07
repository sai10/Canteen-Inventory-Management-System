<!DOCTYPE html>
<?php
/*
Page presented to user to get submission history by date.

*/
	# Include utilities
	require('session.php');
	require('header.php');
	# For searching submissions
	require('submission_search.php');
?>
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
			<form action="" method="POST">
				<div class="row">
					<div class="col-sm-2 col-md-1 col-lg-1">
					</div>
					<div class="col-sm-4 col-md-4 col-lg-4">
						<label>From Date (mm/dd/yyyy)</label>
						<div class="form-group">
							<input type="date" class="form-control" name="from_date">
						</div>
					</div>
					<div class="col-sm-2 col-md-1 col-lg-2">
					</div>
					<div class="col-sm-4 col-md-4 col-lg-4">
						<label>To Date (mm/dd/yyyy)</label>
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
			<div class="text-center">
				<br/>
				<?php
					if(strlen($info) > 0)
						print '<span class="label label-success">'.$info.'</span>';
					if(strlen($error) > 0)
						print '<span class="label label-danger">'.$error.'</span>';
				?>
			</div>
			<?php
				if($number_of_rows != 0) { # There are some records found
					print '<br/>';
					print '<div class="table-responsive">';
					print '<table class="table table-hover table-bordered">';
					print '<thead><th>#</th><th>Transaction ID</th><th>Supplier</th><th>Creation Time</th><th>Status</th><th>Forwarded To</th><th>Forward Date</th></thead>';

					# Iterate and print
					for($i = 0 ; $i < $number_of_rows ; $i ++) {
						print "<tr>";
						print "<td>".($i+1)."</td>";
						print '<td><a href="tran.php?id='.$search_result["tran_id"][$i].'">'.$search_result["tran_id"][$i].'</a></td>';
						print "<td>".$search_result["supplier_name"][$i]."</td>";
						print "<td>".$search_result["create_date"][$i]."</td>";
						print '<td>';
						if(strcmp($search_result["status"][$i], 'Approved') == 0)
							print '<span class="label label-success">';
						else if(strcmp($search_result["status"][$i], 'In progress') == 0)
							print '<span class="label label-warning">';
						else
							print '<span class="label label-danger">';
						print $search_result["status"][$i]."</span></td>";
						print "<td>".$search_result["forwarded_to"][$i]."</td>";
						print "<td>".$search_result["forward_date"][$i]."</td>";
						print "</tr>";
					}

					print '</table>';
					print '</div>';
				}
			?>
		</div>
	</div>
</BODY>
</HTML>