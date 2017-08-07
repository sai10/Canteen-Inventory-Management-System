<?php
/*
PHP file where header for each page is defined.


*/
	
	# Home location set to index.php, user will be redirected from there
	$home_location = 'index.php';

	# Defining header variable
	$header = '
		<div class="container" style="margin: 25px 0px 10px 0px;">
			<div class="row">
				<div class="col-sm-4 col-md-2 col-lg-2">
					<div class="media">
						<div class="media-left">
							<img class="media-object pull-right" src="img/hindalco.jpg" width="70%">
						</div>
					</div>
				</div>
				<div class="col-sm-8 col-md-10 col-lg-10">
					<div style="font-size: 35px; margin-top: 30px;font-family: Arial Bold;color:DarkSlateGrey ;">
						CANTEEN INVENTORY REGULATION SYSTEM
					</div>
				</div>
			</div>
		</div>
	<nav class="navbar navbar-default" style="background-color:Gainsboro ;">
	<div class="container">
		<div class="container-fluid">
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
				<li><a href="';

	$header = $header.$home_location.'"><span class="glyphicon glyphicon-home" aria-hidden="true"></span><span style="margin-left: 3px;">Home</span> </li>';

	# Whether the user submits or approves requests
	if($_SESSION['approval_level'] == 0) {
		$history_location = 'submission_history.php';
		$history_tag = 'Submission History';
	} else {
		$history_location = 'approval_history.php';
		$history_tag = 'Approval History';
	}

	$header = $header.'<li><a href="';
	$header = $header.$history_location.'">';
	$header .= '<span class="glyphicon glyphicon-time" aria-hidden="true"></span>';
	$header .= '<span style="margin-left: 3px;">'.$history_tag.'</span></a></li>';
	$header .= '<li><a href="report.php">';
	$header .= '<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>';
	$header .= '<span style="margin-left: 3px;"> Report </span></a></li>';
	$header = $header.'</ul>';
	
	# Search form
	$header = $header.'<form class="navbar-form navbar-left" role="search" action="tran.php" method="GET">';
	$header = $header.'<div class="form-group">';
	$header = $header.'<input type="text" class="form-control input-sm" placeholder="Enter Transaction ID" name="id">';
	$header = $header."</div>";
	$header = $header.'<input type="submit" class="btn btn-default btn-sm" value="Search">';
	$header = $header.'</form>';

	# Account section
	$header = $header.'<ul class="nav navbar-nav navbar-right">';
	$header = $header.'<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">  <span class="glyphicon glyphicon-user" aria-hidden="true" style="margin-right: 3px;"></span> '.$_SESSION['user_name'].' <span class="caret"></span></a>';
	$header = $header.'<ul class="dropdown-menu">';
	$header = $header.'<li><a href="change_password.php"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span><span style="margin-left: 3px;">Change Password</span></a></li>';
	$header = $header.'<li><a href="logout.php"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span><span style="margin-left: 3px;">Logout</span></a></li>';
	$header = $header.'</ul>';
	$header = $header.'</li>';
	$header = $header.'</ul>';

	$header = $header.'		</div>
		</div>
	</div>
	</nav>';
?>
<style type="text/css">
		body{
 	 background-image: url("img/Hindalco-02.jpg");
	 background-size: 100% 100%;
     background-repeat: no-repeat;
     background-attachment: fixed;
     height:672PX;
}
</style>
