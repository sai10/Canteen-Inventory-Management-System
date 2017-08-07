<!DOCTYPE html>
<html lang="en">

<?php
/*
Page where users land when they have to change their password.


*/
	# Session management
	require("session.php");
	# Include header page
	require("header.php");

	# Define variables
	$info='';
	$error='';

	if(isset($_POST['submit'])) # If change password button was clicked
	{
		if (empty($_POST['old_password']) || empty($_POST['new_password']) || empty($_POST['new_password_rept'])) # If any of the fields were empty
			$error="Fields can't be empty.";

		else
		{
			if(strcmp($_POST['new_password'], $_POST['new_password_rept']) != 0)
				$error="Passwords do not match.";
			else
			{
				if(strcmp($_POST['old_password'], $_POST['new_password']) == 0)
					$error="Old and New password can't be same.";
				else
				{
					# DB connection utility
					require("connect.php");
					if(!$conn) # Error in connection
						$error="Problem connecting to database.";
					else
					{
						# SQL script for fetching password
						$sql="select password from can_user_mast";
						$sql=$sql." where user_id='".$_SESSION['user_id']."'";

						# Execute and get result
						$result=odbc_exec($conn, $sql);
						$curr_password=odbc_result($result, 1);

						if(strcmp($curr_password, $_POST['old_password']) != 0) # Wrong password provided
						{
							$error="Incorrect old password.";
							odbc_close($conn);
						}
						else
						{
							# SQL script for changing password
							$sql_chng_password="update can_user_mast";
							$sql_chng_password=$sql_chng_password." set password=?";
							$sql_chng_password=$sql_chng_password." where user_id=?";

							# Prepare SQL string
							$stmt=odbc_prepare($conn, $sql_chng_password);
							
							# Execute
							odbc_execute($stmt, array($_POST['new_password'], $_SESSION['user_id']));

							# Set info and close connection
							$info="Password changed successfully.";	
							odbc_close($conn);
						}
					}
				}
			}
		}		
	}
?>
<head>
	<script type="text/javascript" src="js/jquery-2.1.4.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	<?php
		print $header;
	?>
	<br>
	<div class="container">
		<div class="contaner-fluid">
			<form action="" method="post">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-3 col-md-4 col-lg-4 "></div>
						<div class="col-sm-6 col-md-4 col-lg-4">
							<label>Current Password</label>
							<input class="form-control" type="password" name="old_password">
						</div>
					</div>
					<div class="row" style="margin-top:5px;">
						<div class="col-sm-3 col-md-4 col-lg-4 "></div>
						<div class="col-sm-6 col-md-4 col-lg-4">
							<label>New Password</label>
							<input class="form-control" type="password" name="new_password">
						</div>
					</div>
					<div class="row" style="margin-top:5px;">
						<div class="col-sm-3 col-md-4 col-lg-4 "></div>
						<div class="col-sm-6 col-md-4 col-lg-4">
							<label>Repeat New Password</label>
							<input class="form-control" type="password" name="new_password_rept">
						</div>
					</div>
					<div class="row" style="margin-top:10px;">
						<div class="col-sm-3 col-md-4 col-lg-4 "></div>
						<div class="col-sm-6 col-md-4 col-lg-4">
							<input type="reset" class="btn btn-default">
							<input type="submit" class="pull-right btn btn-primary" name="submit" value="Change Password">
						</div>
					</div>
				</div>
			</form>
			<div class="text-center">
				<?php
					if(strlen($info)>0)
						print "<span class='label label-success'>$info</span>";
					if(strlen($error)>0)
						print "<span class='label label-danger'>$error</span>";
				?>
			</div>
		</div>
	</div>
</body>
</html>