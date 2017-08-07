<!DOCTYPE html>
<?php
/*
Page where 0 level users land after login to make entry of products coming in.	


*/
	# Include utilities
	require('session.php');
	require('items.php');
	require('next_check.php');
	require('header.php');
?>
<HTML lang="en">
<HEAD>
	<!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
	<script type="text/javascript" src="js/jquery-2.1.4.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">

	
	<script>

		// Function to add more entry fields
		function add_fields() {

			var new_feld = document.getElementById('dummy_form').cloneNode(true); // Get the field
			new_feld.style.display = 'block'; // Make it visible
			new_feld.id = ''; // Nullify its ID

			insert_point = document.getElementById('ins'); // Fetch the insert point
			insert_point.parentNode.insertBefore(new_feld, insert_point); // Insert to its parent
		}
	</script>
	
</HEAD>
<BODY style="height:672PX;">
	<?php
		print $header;
	?>
	
	<form method="POST" action="" autocomplete="off">
		<div class="container">
		<div class="text-center">
			<?php
				# Printing errors if any
				if(strlen($error) > 0)
					echo '<span class="label label-danger">'.$error.'</span>';
				if(strlen($info) > 0)
					echo '<span class="label label-success>'.$info.'</span>';
			?>
		</div>
		<div>
			<span>
				Time : <span class="label label-info"> <b>&nbsp;
				<?php
					# Printing time
					date_default_timezone_set('Asia/Kolkata');
					echo date('m/d/Y h:i:s a');
				?>
				&nbsp;</b>
			</span>
			<span class="pull-right">
				<div class="form-group">
					<label for="suppliers">Supplier:</label>
					<select class="form-control" id="suppliers" name="supplier">
					<?php
						# Printing list of suppliers
						for($i = 0 ; $i < count($list_suppliers) ; $i ++ ) {
							if(isset($_POST['next'])) {
								if(strcmp($_POST['supplier'], $list_suppliers[$i]) == 0) {
									print '<option selected="selected">'.$list_suppliers[$i].'</option>';
								} else {
									print '<option>'.$list_suppliers[$i].'</option>';
								}								
							} else {
								print '<option>'.$list_suppliers[$i].'</option>';
							} 
						}
    				?>
  					</select>
				</div>
			</span>
		</div>
		<br/>
		<br/>
		<hr/>
		<div>
			<div class="row">
				<div class="col-sm-6 col-md-3 col-lg-3">
					<label>Item</label>
				</div>
				<div class="col-sm-6 col-md-2 col-lg-2">
					<label>Unit</label>
				</div>
				<div class="col-sm-6 col-md-2 col-lg-2">
					<label for="quantity1">Quantity</label>
				</div>
				<div class="col-sm-6 col-md-5 col-lg-5">
					<label>Description</label>
					<!-- Button to add more input fields -->
					<span class="glyphicon glyphicon-plus-sign pull-right text-success" aria-hidden="true" onclick="add_fields();"></span>
				</div>
			</div>
			<div id="items">
				<div class="row">
				<?php if(isset($_POST['next'])) {
					for($i = 0 ; $i < count($_POST['item']) - 1 ; $i ++) {
						?>
				<div>
				<div class="col-sm-6 col-md-3 col-lg-3">
					<div class="form-group">
						<select class="form-control" name="item[]">
						<?php
							# Add item list
							for($j = 0 ; $j < count($list_items) ; $j ++) {
								if(strcmp($_POST['item'][$i], $list_items[$j]) == 0) {
									print '<option selected="selected">'.$list_items[$j].'</option>';
								} else {
									print '<option>'.$list_items[$j].'</option>';
								}
							}
						?>
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-md-2 col-lg-2">
					<div class="form-group">
							<select class="form-control" name="unit[]">
							<?php
							if(strcmp($_POST['unit'][$i], 'kg') == 0){
								print '<option selected="selected">kg</option>';
								print '<option>Nos</option>';
							} else {
								print '<option>kg</option>';
								print '<option selected="selected">Nos</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-md-2 col-lg-2">
					<div class="form-group">
						<input type="number" step="any" class="form-control" min="0" name="qty[]" value="<?php print $_POST['qty'][$i]; ?>">
					</div>
				</div>
				<div class="col-sm-6 col-md-5 col-lg-5">
					<div class="row">
						<div class="col-sm-10 col-md-11 col-lg-11">
							<div class="form-group">
								<input type="text" class="form-control" name="desc[]" value="<?php print $_POST['desc'][$i]; ?>">
							</div>
						</div>
					
				<?php if($i > 0) { ?>
					<div class="col-sm-2 col-md-1 col-lg-1">
					<!-- Remove the field -->
					<span class="glyphicon glyphicon-minus-sign pull-right text-danger" aria-hidden="true" onclick="$(this).parent().parent().parent().parent().remove();"></span>
					</div>
				<?php } ?>
				</div>
				</div>
				</div>
				<?php }
			} else { ?>
				<div class="col-sm-6 col-md-3 col-lg-3">
					<div class="form-group">
						<select class="form-control" name="item[]">
						<?php
							# Add item list
							for($i = 0 ; $i < count($list_items) ; $i ++) {
								print '<option>'.$list_items[$i].'</option>';
							}
						?>
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-md-2 col-lg-2">
					<div class="form-group">
							<select class="form-control" name="unit[]">
							<option>kg</option>
							<option>Nos</option>
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-md-2 col-lg-2">
					<div class="form-group">
						<input type="number" step="any" class="form-control" min="0" name="qty[]">
					</div>
				</div>
				<div class="col-sm-6 col-md-5 col-lg-5">
					<div class="row">
						<div class="col-sm-10 col-md-11 col-lg-11">
							<div class="form-group">
								<input type="text" class="form-control" name="desc[]">
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
			<div>
				<div id="ins"></div>
			</div>
 		</div>
 		<br/>
 		<div>
 		<span class="pull-left">
 		<label>Forward to</label>
 			<select class="form-control" name="forwarded_user">
 				<?php
 					if(isset($_POST['next'])) {
 						for($i = 0 ; $i < count($forward_list) ; $i ++) {
 							if(strcmp($forward_list[$i], $_POST['forwarded_user']) == 0) {
 								print '<option selected="selected">'.$forward_list[$i].'</option>';
 							}
 							else {
 								print '<option>'.$forward_list[$i].'</option>';
 							}
 						}
 					} else {
 						for($i = 0 ; $i < count($forward_list) ; $i ++) {
 							print '<option>'.$forward_list[$i].'</option>';
 						}
 					}
 				?>
 			</select>
 		</span>
 		<span class="pull-right">
 			<br/>
			<input type="reset">
 			&nbsp;&nbsp;&nbsp;&nbsp;
 			<input type="submit" name="next" value="Save">
 			</span>
 		</div>
	</form>

</BODY>
	<!-- Form that will be added, hidden to user but submitted while creating POST. -->
	<div id="dummy_form" style="display: none;">
		<div class="row" style="">
			<div class="col-sm-6 col-md-3 col-lg-3">
				<div class="form-group">
					<select class="form-control" name="item[]">
					<?php
						for($i = 0 ; $i < count($list_items) ; $i ++) {
							print '<option>'.$list_items[$i].'</option>';
						}
					?>
					</select>
				</div>
			</div>
			<div class="col-sm-6 col-md-2 col-lg-2">
				<div class="form-group">
					<select class="form-control" name="unit[]">
						<option>kg</option>
							<option>Nos</option>
					</select>
				</div>
			</div>
			<div class="col-sm-6 col-md-2 col-lg-2">
				<div class="form-group">
					<input type="number" step="any" class="form-control" min="0" name="qty[]">
				</div>
			</div>
			<div class="col-sm-6 col-md-5 col-lg-5">
				<div class="row">
					<div class="col-sm-10 col-md-11 col-lg-11">
						<div class="form-group">
							<input type="text" class="form-control" name="desc[]">
						</div>
					</div>
					<div class="col-sm-2 col-md-1 col-lg-1">
					<!-- Remove the field -->
					<span class="glyphicon glyphicon-minus-sign pull-right text-danger" aria-hidden="true" onclick="$(this).parent().parent().parent().parent().remove();"></span>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</HTML>