<?php
/*
Check for login.


*/	
	session_start();

	# Initialize variables
	$error ='';
	$info = '';

	if (isset($_POST['login'])) { # Login was clicked
		if(empty($_POST['user_id']) || empty($_POST['password'])) { # User ID or password was not set
			$error = "Invalid Username or Password";
		} else { # User ID and password was set
			# Get details from form
			$user_id=$_POST['user_id'];
			$password=$_POST['password'];

			# Cleanup
			$user_id = stripslashes($user_id);
			$password = stripslashes($password);

			# Connect to database
			$conn = odbc_connect('test', 'hkd', 'hindalco_123');

			if(!$conn) { # Unable to connect
				$error = "Couldn't connect to database. Please try in some time.";
			}
			else { # Connection success

				# Prepare SQL statement
				$stmt = odbc_prepare($conn, "select * from can_user_mast where user_id=? and password=?");
				# Execute
				odbc_execute($stmt, Array($user_id, $password));
				# Fetch row
				$rows = odbc_fetch_array($stmt);

				if($rows == false) { # No rows fetched
					$error = "Wrong combination of Username and Password";
				}
				else { # User exists

					# Get details
					$user_id_success = $rows['USER_ID'];
					$user_name_success = $rows['USER_NAME'];
					$user_approval_level = (int)$rows['APPROVAL_LEVEL'];

					# Set session variables
					$_SESSION['user_id'] = $user_id_success;
					$_SESSION['user_name'] = $user_name_success;
					$_SESSION['approval_level'] = $user_approval_level;
					
					# Redirect acording to user's approval level
					if($user_approval_level == 0)
						header("location: main.php");
					else if($user_approval_level == -1)
						header("Location: admin/");
					else
						header("Location: approve.php");
				}
				# Close connection
				odbc_close($conn);
			}
		}
	}
?>