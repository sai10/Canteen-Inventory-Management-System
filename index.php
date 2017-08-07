<!DOCTYPE html>

<?php
/*
Login screen.


*/	
	# Including login utility
	require('login.php');

	# Check if a session is active and use is set
	if(isset($_SESSION['user_id'])) {

		# Check user's level and redirect appropriately
		if($_SESSION['approval_level'] == 0)
			header("location: main.php");
		else if($_SESSION['approval_level'] == -1)
			header("location: admin/");
		else
			header("Location: approve.php");

	}
?>

<html lang="en">
	<script type="text/javascript" src="js/jquery-2.1.4.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	
</html>
<!-- BACKGROUND STYLING -->
<style style="text/css">
.img{
  background-image: url("img/Hindalco--621x414.jpg");
	background-size: 100% 100%;
    background-repeat: no-repeat;
  
}

.img2{
  background-image: url("img/abg_banner.jpg");
	background-size: 100% 210%;
    background-repeat: no-repeat;
  
}

</style>


<BODY style="background-color:#041122">
	<div class="container" style="margin-top:0px;margin-right:0px;margin-left:0px;width:1024px;">
			<div class="row img2" >
				<div class="col-sm-12 col-md-12 col-lg-12">
					<div style="font-size: 35px; margin-top:3px;word-spacing:30px;">
						<p style="margin-left:50px;font-family: Arial Bold;color:DarkCyan ;"><b>CANTEEN  INVENTORY   REGULATION   SYSTEM</b></p>
						<hbr>
					</div>
				</div>
			</div>
	</div>

	<div class="container img" style="margin-top:0px;margin-right:0px;margin-left:0px;width:1024px;height:554px;">
		<div class="container-fluid">
			
			
			<form method="POST" action=""> <!--POST to self for evaluation.-->
				<div class="row">
					<div class="col-sm-2 col-md-3 col-lg-3">
					</div>
					
					<div class="col-sm-6 col-md-4 col-lg-4" style="padding-left:55px;">
					<div class="jumbotron" style="width:375px;margin-top:150px;background: rgba(119,136,153,0.8);">
						<div class="text-center">
							<div class="form-group" style="opacity:0.7;">
								<div class="input-group">
  									<span class="input-group-addon" id="basic-addon1" style="width:105px;">USER ID</span>
									<input type="text" name="user_id" maxlength="6" size="6" class="form-control" placeholder="Username"  autocomplete="off" style="width:150px;">
								</div>
								<br>
								<div class="input-group">
  									<span class="input-group-addon" id="basic-addon1">PASSWORD</span>
									<input type="password" name="password" class="form-control" placeholder="Password" >
								</div>
								<br>
								<input type="submit" class="btn btn-primary pull-right" style="width:255px;" value="Log In" name="login">
								<br/>
								<!--Print error and info from login.php-->
								<span class="text-danger"><small><?php echo $error; ?></small></span>
								<span class="text-success"><small><?php echo $info; ?></small></span>
							</div>
						</div>
					</div>
					</div>
				</div>
			</form>
		</div>

	</div>
	
	
	<div class="container img2" style="width: 1024px;">
    	<br>
    	<div class="col-sm-2 col-mg-3 col-lg-3"></div>
       <div class="float_left copyright" style="font-family:Arial;">Copyright &nbsp;&nbsp; @ &nbsp;&nbsp; 2016 &nbsp;&nbsp; Aditya &nbsp;&nbsp; Birla &nbsp;&nbsp; Hindalco &nbsp;&nbsp; Corporation &nbsp;&nbsp; Pvt.&nbsp;&nbsp;  Ltd </div>
       <div class="clear"></div>
       <br>
    </div>
</BODY>