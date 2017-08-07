<?php
	
	require('session.php');

	$error = '';
	
	$sql_main = "select t1.tran_id, t1.tran_create_date, t2.transaction_price, t3.supp_name, t4.user_name, t7.status from";
	$sql_main .= " (select tran_id, tran_supp_id, tran_create_user, tran_create_date from";
	$sql_main .= " can_transaction_mast";
	$sql_main .= " where  1 = 1";
	if(strcmp($_POST['supplier'], '0') != 0)
		$sql_main .= " and tran_supp_id = '".$_POST['supplier']."'"; # Only from this supplier
	if(strcmp($_POST['create_by'], '0') != 0)
		$sql_main .= " and tran_create_user = '".$_POST['create_by']."'";
	$sql_main .= " and tran_create_date >= to_date('".$_POST['from_date']."', 'yyyy-mm-dd')";
	$sql_main .= " and tran_create_date <= to_date('".$_POST['to_date']."', 'yyyy-mm-dd') + 1";
	$sql_main .= " ) t1";
	$sql_main .= " inner join";
	$sql_main .= " (select tran_id, sum(rate*quantity) transaction_price from";
	$sql_main .= " (select * from";
	$sql_main .= " can_transaction_dtl";
	if(strcmp($_POST['item'], '0') != 0)
		$sql_main .= " where item_id = '".$_POST['item']."'";
	$sql_main .= " )";
	$sql_main .= " group by tran_id";
	$sql_main .= " ) t2";
	$sql_main .= " on t1.tran_id = t2.tran_id";
	$sql_main .= " inner join";
	$sql_main .= " (select supp_id, supp_name";
	$sql_main .= " from can_supplier_mast";
	$sql_main .= " ) t3";
	$sql_main .= " on t1.tran_supp_id = t3.supp_id";
	$sql_main .= " inner join";
	$sql_main .= " (select user_id, user_name";
	$sql_main .= " from can_user_mast";
	$sql_main .= " ) t4";
	$sql_main .= " on t1.tran_create_user = t4.user_id";
	$sql_main .= " inner join";
	$sql_main .= " (select t5.tran_id, t6.status from";
	$sql_main .= " (select tran_id, max(aprv_id) aid from";
	$sql_main .= " can_transaction_aprv";
	$sql_main .= " group by tran_id";
	$sql_main .= " ) t5";
	$sql_main .= " inner join";
	$sql_main .= " (select aprv_id, status from";
	$sql_main .= " can_transaction_aprv";
	$sql_main .= " ) t6";
	$sql_main .= " on t5.aid = t6.aprv_id";
	$sql_main .= " ) t7";
	$sql_main .= " on t1.tran_id = t7.tran_id";
	$sql_main .= " where t7.status <> 'Cancelled'";
	if(isset($_POST['approved_only']))
		$sql_main .= " and t7.status = 'Approved'";
	$sql_main .= " order by t1.tran_create_date desc";

	$result_main = odbc_exec($conn, $sql_main);

	$report['total_price'] = 0;
	$report['tran_id'] = array();
	$report['date'] = array();
	$report['price'] = array();
	$report['supp_name'] = array();
	$report['user_name'] = array();
	$report['status'] = array();

	while(odbc_fetch_row($result_main)) {
		array_push($report['tran_id'], odbc_result($result_main, 1));
		array_push($report['date'], odbc_result($result_main, 2));
		$price = floatval(odbc_result($result_main, 3));
		array_push($report['price'], number_format($price, 2));
		array_push($report['supp_name'], odbc_result($result_main, 4));
		array_push($report['user_name'], odbc_result($result_main, 5));
		array_push($report['status'], odbc_result($result_main, 6));
		$report['total_price'] += $price;
	}

	$report['total_price'] = number_format($report['total_price'], 2);

	if(isset($_POST['show_item'])) {

		$sql_items = "select t1.tran_id, t6.item_name, t2.quantity, t2.unit, t2.rate, t2.description from";
		$sql_items .= " (select tran_id from";
		$sql_items .= " can_transaction_mast";
		$sql_items .= " where  1 = 1";
		if(strcmp($_POST['supplier'], '0') != 0)
			$sql_items .= " and tran_supp_id = '".$_POST['supplier']."'"; # Only from this supplier
		if(strcmp($_POST['create_by'], '0') != 0)
			$sql_items .= " and tran_create_user = '".$_POST['create_by']."'";
		$sql_items .= " and tran_create_date >= to_date('".$_POST['from_date']."', 'yyyy-mm-dd')";
		$sql_items .= " and tran_create_date <= to_date('".$_POST['to_date']."', 'yyyy-mm-dd')";
		$sql_items .= " ) t1";
		$sql_items .= " inner join";
		$sql_items .= " (select * from";
		$sql_items .= " can_transaction_dtl";
		if(strcmp($_POST['item'], '0') != 0)
			$sql_items .= " where item_id = '".$_POST['item']."'";
		$sql_items .= " ) t2";
		$sql_items .= " on t1.tran_id = t2.tran_id";
		$sql_items .= " inner join";
		$sql_items .= " (select t3.tran_id, t4.status from";
		$sql_items .= " (select tran_id, max(aprv_id) aid from";
		$sql_items .= " can_transaction_aprv";
		$sql_items .= " group by tran_id";
		$sql_items .= " ) t3";
		$sql_items .= " inner join";
		$sql_items .= " (select aprv_id, status from";
		$sql_items .= " can_transaction_aprv";
		$sql_items .= " ) t4";
		$sql_items .= " on t3.aid = t4.aprv_id";
		$sql_items .= " ) t5";
		$sql_items .= " on t1.tran_id = t5.tran_id";
		$sql_items .= " inner join";
		$sql_items .= " can_item_mast t6";
		$sql_items .= " on t2.item_id = t6.item_id";
		$sql_items .= " where status <> 'Cancelled'";
		if(isset($_POST['approved_only']))
			$sql_items .= " and t5.status = 'Approved'";
		$sql_items .= " order by t1.tran_id";

		$result_items = odbc_exec($conn, $sql_items);

		$item['tran_id'] = array();
		$item['item_name'] = array();
		$item['quantity'] = array();
		$item['rate'] = array();
		$item['desc'] = array();
		$item['total'] = array();
		while(odbc_fetch_row($result_items)) {
			array_push($item['tran_id'], odbc_result($result_items, 1));
			array_push($item['item_name'], odbc_result($result_items, 2));
			$quantity = odbc_result($result_items, 3);
			array_push($item['quantity'], number_format($quantity).' '.odbc_result($result_items, 4));
			$rate = floatval(odbc_result($result_items, 5));
			array_push($item['rate'], number_format($rate, 2));
			array_push($item['desc'], odbc_result($result_items, 6));
			$total = $quantity * $rate;
			array_push($item['total'], number_format($total, 2));
		}
	} else {

		$sql_items = " select t7.item_name, t6.qty, t6.rte, t6.prc from";
		$sql_items .= " (select item_id, sum(quantity) qty, avg(rate) rte, sum(quantity*rate) prc from";
		$sql_items .= " (select tran_id from";
		$sql_items .= " can_transaction_mast";
		$sql_items .= " where  1 = 1";
		if(strcmp($_POST['supplier'], '0') != 0)
			$sql_items .= " and tran_supp_id = '".$_POST['supplier']."'"; # Only from this supplier
		if(strcmp($_POST['create_by'], '0') != 0)
			$sql_items .= " and tran_create_user = '".$_POST['create_by']."'";
		$sql_items .= " and tran_create_date >= to_date('".$_POST['from_date']."', 'yyyy-mm-dd')";
		$sql_items .= " and tran_create_date <= to_date('".$_POST['to_date']."', 'yyyy-mm-dd')";
		$sql_items .= " ) t1";
		$sql_items .= " inner join";
		$sql_items .= " (select tran_id, item_id, quantity, rate from";
		$sql_items .= " can_transaction_dtl";
		if(strcmp($_POST['item'], '0') != 0)
			$sql_items .= " where item_id = '".$_POST['item']."'";
		$sql_items .= " ) t2";
		$sql_items .= " on t1.tran_id = t2.tran_id";
		$sql_items .= " inner join";
		$sql_items .= " (select t3.tran_id, t4.status from";
		$sql_items .= " (Select tran_id, max(aprv_id) aid";
		$sql_items .= " from can_transaction_aprv";
		$sql_items .= " group by tran_id";
		$sql_items .= " ) t3";
		$sql_items .= " inner join";
		$sql_items .= " can_transaction_aprv t4";
		$sql_items .= " on t3.aid = t4.aprv_id";
		$sql_items .= " where t4.status <> 'Cancelled'";
		if(isset($_POST['approved_only']))
			$sql_items .= " and t4.status = 'Approved'";
		$sql_items .= " ) t5";
		$sql_items .= " on t1.tran_id = t5.tran_id";
		$sql_items .= " group by item_id";
		$sql_items .= " ) t6";
		$sql_items .= " inner join";
		$sql_items .= " can_item_mast t7";
		$sql_items .= " on t6.item_id = t7.item_id";
		$sql_items .= " order by t7.item_name";

		$result_items = odbc_exec($conn, $sql_items);

		$item['name'] = array();
		$item['qty'] = array();
		$item['rate'] = array();
		$item['price'] = array();
		while(odbc_fetch_row($result_items)) {
			array_push($item['name'], odbc_result($result_items, 1));
			array_push($item['qty'], number_format(odbc_result($result_items, 2)));
			array_push($item['rate'], number_format(odbc_result($result_items, 3), 2));
			array_push($item['price'], number_format(odbc_result($result_items, 4), 2));
		}
	}
?>
<!DOCTYPE html>
<HTML lang="en">
<HEAD>
	<script type="text/javascript" src="js/jquery-2.1.4.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
</HEAD>
<BODY>
	<div class="container" style="margin-bottom: 30px;">
		<div class="container-fluid">
			<div class="text-center">
				<u><h4> Canteen Inventory Import Summary </h4></u>
				<br/>
			</div>
			<b> Basic Details </b>

			<div class="table-responsive" style="border-top: 1px solid black;">
				<table class="table table-hover">
					<tr>
						<td>
							<div class="col-sm-6 col-md-4 col-lg-4">
								<b> From Date : </b>
							</div>
							<div class="col-sm-6 col-md-8 col-lg-8">
								<?php print date('d-M-Y', strtotime($_POST['from_date'])) ?>
							</div>
						</td>
						<td>
							<div class="col-sm-6 col-md-4 col-lg-4">
								<b> To Date : </b>
							</div>
							<div class="col-sm-6 col-md-8 col-lg-8">
								<?php print date('d-M-Y', strtotime($_POST['to_date'])) ?>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="col-sm-6 col-md-4 col-lg-4">
								<b> Supplier : </b>
							</div>
							<div class="col-sm-6 col-md-8 col-lg-8">
							<?php
								if(strcmp($_POST['supplier'], '0') == 0)
									print 'All';
								else
								{
									print $report['supp_name'][0];
								}
							?>
							</div>
						</td>
						<td>
							<div class="col-sm-6 col-md-4 col-lg-4">
								<b> Created By : </b>
							</div>
							<div class="col-sm-6 col-md-8 col-lg-8">
							<?php
								if(strcmp($_POST['create_by'], '0') == 0)
									print 'All';
								else
									print $report['user_name'][0];
							?>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="col-sm-6 col-md-4 col-lg-4">
								<b> Transactions Found : </b>
							</div>
							<div class="col-sm-6 col-md-8 col-lg-8">
								<?php print number_format(count($report['tran_id'])); ?>
							</div>
						</td>
						<td>
							<div class="col-sm-6 col-md-4 col-lg-4">
								<b> Total Price </b>
							</div>
							<div class="col-sm-6 col-md-8 col-lg-8">
								<?php print $report['total_price']; ?>
							</div>
						</td>
					</tr>
				</table>
			</div>

			<b> Transaction Overview </b>
			<div class="table-responsive" style="border-top: 1px solid black;">
				<table class="table table-hover table-bordered">
					<thead>
						<th> # </th>
						<th> Transaction ID </th>
						<th> Supplier </th>
						<th> Creation Date </th>
						<th> Creating User </th>
						<th> Status </th>
						<th> Price </th>
					</thead>
					<?php
						for($i = 0 ; $i < count($report['tran_id']) ; $i ++ ) {
							print '<tr>';
							print '<td>'.($i + 1).'</td>';
							print '<td>'.$report['tran_id'][$i].'</td>';
							print '<td>'.$report['supp_name'][$i].'</td>';
							print '<td>'.$report['date'][$i].'</td>';
							print '<td>'.$report['user_name'][$i].'</td>';
							print '<td>'.$report['status'][$i].'</td>';
							print '<td>'.$report['price'][$i].'</td>';
						}
					?>
				</table>
			</div>

			<b> Item Details </b>
			<div class="table-responsive" style="border-top: 1px solid black;">
				<table class="table table-hover table-bordered">
					<thead>
						<th> # </th>
					<?php if(isset($_POST['show_item'])) { ?>
						<th> Transaction ID </th>
						<th> Item </th>
						<th> Description </th>
						<th> Quantity </th>
						<th> Rate </th>
						<th> Total </th>
					<?php } else { ?>
						<th> Name </th>
						<th> Quantity </th>
						<th> Avg. Rate </th>
						<th> Total Price </th>
					<?php } ?>
					</thead>
					<?php
						if(isset($_POST['show_item'])) {
							for($i = 0 ; $i < count($item['tran_id']) ; $i ++) {
								print '<tr>';
								print '<td>'.($i + 1).'</td>';
								print '<td>'.$item['tran_id'][$i].'</td>';
								print '<td>'.$item['item_name'][$i].'</td>';
								print '<td>'.$item['desc'][$i].'</td>';
								print '<td>'.$item['quantity'][$i].'</td>';
								print '<td>'.$item['rate'][$i].'</td>';
								print '<td>'.$item['total'][$i].'</td>';
								print '</tr>';
							}
						} else {
							for($i = 0 ; $i < count($item['name']) ; $i ++) {
								print '<tr>';
								print '<td>'.($i + 1).'</td>';
								print '<td>'.$item['name'][$i].'</td>';
								print '<td>'.$item['qty'][$i].'</td>';
								print '<td>'.$item['rate'][$i].'</td>';
								print '<td>'.$item['price'][$i].'</td>';
								print '</tr>';
							}
						}
					?>
				</table>
			</div>
			<div class="no-print text-center">
			<button onclick="window.print();" class="btn btn-primary input-sm">Print Page</button>
			</div>
		</div>
	</div>
</BODY>
</HTML>