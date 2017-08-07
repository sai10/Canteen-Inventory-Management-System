<!DOCTYPE html>
<?php
/*
Page landed when something is requested that is not found.


*/
	require('session.php');
	require('header.php');
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
	<?php print $header; ?>
	<br/><br/>
	<div class="text-center">
		<h2>The page you were looking for was not found.</h2>
	</div>
</BODY>
</HTML>