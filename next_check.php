<?php
/*
Check if entries are valid when level 0 user clicks 0.


*/
	# Initialize variables
	$error = '';
	$info = '';

	if(isset($_POST['next'])) { # If next was clicked

		$number_of_items = count($_POST['item']) - 1; # Ommitting last one because of hidden element in HTML

		$success_count = 0;
		$temp_array = array();
		for($i = 0 ; $i < $number_of_items ; $i ++) {
			$current_success = 1;
			if(floatval($_POST['qty'][$i]) <= 0.0) { # Quantity provided was 0
				$error .= " Quantity must be greater than 0.";
				$current_success = 0;
				break;
			}

			if(in_array($_POST['item'][$i], $temp_array)) {
				$error .= " Please check for repeated items.";
				$current_success = 0;
				break;
			}

			$success_count += $current_success;
			array_push($temp_array, $_POST['item'][$i]);
		}

		$pass_1 = 1;
		if(strcmp($_POST['forwarded_user'], 'Please Select') == 0) { # Not selected any user
			$pass_1 = 0;
			$error .= " Please select a valid user to forward.";
		}

		$pass_2 = 1;
		if(strcmp($_POST['supplier'], 'Please Select') == 0) { # Not selected anu supplier
			$pass_2 = 0;
			$error .= ' Please select a valid supplier.';
		}

		if($success_count == $number_of_items && $pass_1 == 1 && $pass_2 == 1) { # All entries are valid

			# Set session variables upon slicing last element of array
			$_SESSION['valid_next_item_entry'] = 1;
			$_SESSION['item'] = array_slice($_POST['item'], 0, -1);
			$_SESSION['unit'] = array_slice($_POST['unit'], 0, -1);
			$_SESSION['qty'] = array_slice($_POST['qty'], 0, -1);
			$_SESSION['desc'] = array_slice($_POST['desc'], 0, -1);
			$_SESSION['forwarded_user'] = $_POST['forwarded_user'];
			$_SESSION['supplier'] = $_POST['supplier'];

			# Redirect to next page
			header("Location: submit_item.php");
		}

	}
?>