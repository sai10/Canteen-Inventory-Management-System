<!--
Page where 0 level users land after clicking next for submitting.


-->
<?php
	# Session
	require('session.php');
	# Can user land here
	require('submit_item_check.php');
	# Database save utility
	require('submit_final.php');
	# Header
	require('header.php');
?>

<!DOCTYPE html>
<HTML lang='en'>
<HEAD>
	<script type="text/javascript" src="js/jquery-2.1.4.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
</HEAD>
<BODY>

	<?php
		echo $header;
	?>
	<div class="container">
		<div class="container-fluid">
			<!-- Warning message -->
			<div class="text-center">
				<?php 
					if(strlen($msg) > 0)
						echo '<span class="label label-info">'.$msg.'</div>';
				?>
			</div>
			<br/>
			<br/>

			<!-- Display information -->
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<th>#</th>
						<th>Item</th>
						<th>Unit</th>
						<th>Quantity</th>
						<th>Description</th>
					</thead>
					<?php
						$number_of_items = count($_SESSION['item']);
						for($i = 0 ; $i < $number_of_items ; $i ++) {
							print "<tr>\n";
							print "<td>".($i + 1)."</td>\n";
							print "<td>".$_SESSION['item'][$i]."</td>\n";
							print "<td>".$_SESSION['unit'][$i]."</td>\n";
							print "<td>".$_SESSION['qty'][$i]."</td>\n";
							print "<td>".$_SESSION['desc'][$i]."</td>\n";
							print "</tr>";
						}
					?>
				</table>
			</div>
			<span class="pull-left">
				<b>Submitted to : </b><?php print $_SESSION['forwarded_user']; ?>
			</span>
			<span class="pull-right">
				<b>Supplier : </b><?php print $_SESSION['supplier']; ?>
			</span>
			<br/><br/>
			<div class="text-center">
				<?php
					if (strlen($info) > 0)
						print '<span class="label label-success>"'.$info.'</span>';
					if (strlen($error) > 0)
						print '<span class="label label-danger">'.$error.'</span>';
				?>
			</div>
			<br/>
		</div>
	</div>
</BODY>
</HTML>