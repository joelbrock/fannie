<?php
//
//
// Copyright (C) 2007  
// authors: Christof Van Rabenau - Whole Foods Cooperative, 
// Joel Brock - People's Food Cooperative
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
//

include('../src/functions.php');
include('reportFunctions.php');
// include('../src/datediff.php');
require_once('../define.conf');
?>

<HTML>
<BODY BGCOLOR = 'FFCC99' > <font SIZE=2>
<?php
$bgcolor = 'FFCC99';

if (isset($_POST['date'])) {
	$date = $_POST['date'];
}
else {
	$date = date('Y-m-d');
}
echo "<font size=+1><b>Sales Report for ".$date."</b></font>";

echo "<br>";


$dateArray = explode("-",$date);
$db_date = date('Y-m-d', mktime(0, 0, 0, $dateArray[1], $dateArray[2], $dateArray[0]));

$year = idate('Y',strtotime($db_date));
if ($db_date == date('Y-m-d')) { $table = 'dtransactions'; }
else { 
	$result = mysql_query("TRUNCATE " . DB_LOGNAME . ".dlog_tmp");
	if (!$result) {
	    $message = 'Invalid query: ' . mysql_error() . "\n";
	    die($message);
	}
	$dlog_table = 'dlog_' . $year;

	$query = "INSERT INTO " . DB_LOGNAME . ".dlog_tmp SELECT * FROM " . DB_LOGNAME . ".$dlog_table WHERE DATE(datetime) = '$db_date'";
	$result = mysql_query($query);
	if (!$result) {
		$message = 'Invalid query: ' . mysql_error() . "\n";
		die($message);
	}
	
	$table = 'dlog_tmp';
}

//////////////////////////////////
//
//
//  Let's crunch some numbers... 
//
//
//////////////////////////////////



/** 
 * total sales 
 * Gross = total of all inventory depts. 1-15 (at ACG)
 * Net = Gross + Hash - All discounts - Coupons(IC & MC) - Gift Cert. Tender - Store Charge
 */



$grossQ = "SELECT ROUND(sum(total),2) as GROSS_sales
	FROM " . DB_LOGNAME . ".$table 
	WHERE date(datetime) = '$db_date' 
	AND department <= 35
	AND department <> 0
	AND trans_subtype NOT IN('IC','MC')
	AND trans_status <> 'X'
	AND emp_no <> 9999";

// echo $grossQ;

	$results = mysql_query($grossQ);
	$row = mysql_fetch_row($results);
	$gross = $row[0];

/**
 * sales of inventory departments
 */

if ($gross == 0 || !$gross) $gross = 1; //to prevent division by 0 or division by null in the query below

$inventoryDeptQ = "SELECT t.dept_no ,t.dept_name,ROUND(sum(d.total),2) AS total,ROUND((SUM(d.total)/$gross)*100,2) as pct
   	FROM " . DB_LOGNAME . ".$table AS d, " . DB_NAME . ".departments AS t
	WHERE d.department = t.dept_no
	AND date(d.datetime) = '$db_date'
	AND d.department <= 20
	AND d.department <> 0
	AND trans_subtype NOT IN('IC','MC')
	AND d.trans_status <> 'X'
	AND d.emp_no <> 9999
	GROUP BY t.dept_no
	ORDER BY t.dept_no";

	// $gross = 0;

/** 
 * Sales for non-inventory departments 
 */

$noninventoryDeptQ = "SELECT d.department,t.dept_name,ROUND(sum(total),2) as total 
	FROM " . DB_LOGNAME . ".$table as d, " . DB_NAME . ".departments as t 
	WHERE d.department = t.dept_no
	AND date(d.datetime) = '$db_date'
	AND d.department >= 34 AND d.department <= 44
	AND d.trans_status <> 'X'
	AND d.emp_no <> 9999
	GROUP BY t.dept_no
	ORDER BY t.dept_no";


/* 
 * pull tender report.
 */

$tendersQ = "SELECT t.TenderName as tender_type,ROUND(-sum(d.total),2) as total,COUNT(*) as count
	FROM " . DB_LOGNAME . ".$table as d," . DB_NAME . ".tenders as t 
	WHERE d.trans_subtype = t.TenderCode
	AND date(d.datetime) = '$db_date'
	AND d.trans_status <> 'X' 
	AND d.emp_no <> 9999
	GROUP BY t.TenderName";
	
$instoreQ = "SELECT d.description, COUNT(*) AS ct,SUM(d.total) AS total
	FROM " . DB_LOGNAME . ".$table AS d
	WHERE DATE(d.datetime) = '$db_date'
	AND d.trans_subtype = 'IC'
	AND d.trans_status <> 'X' 
	AND d.emp_no <> 9999
	GROUP BY d.description";

$custSvcQ = "SELECT (CASE WHEN d.upc LIKE '%DP%' THEN d.description ELSE s.subdept_name END) AS descrip,COUNT(*) AS ct,SUM(d.total) AS total
	FROM " . DB_LOGNAME . ".$table AS d, " . DB_NAME . ".subdeptindex s
	WHERE d.upc = s.upc
	AND date(d.datetime) = '$db_date'
	AND d.department = 40
	AND d.trans_status <> 'X'
	AND d.emp_no <> 9999
	GROUP BY descrip";

$storeChargeQ = "SELECT d.emp_no AS cashier, d.total AS storechg_total
	FROM " . DB_LOGNAME . ".$table AS d
	WHERE date(d.datetime) = '$db_date'
	AND d.trans_subtype = 'MI'
	AND d.card_no = 9999
	AND d.trans_status <> 'X'
	AND d.emp_no <> 9999";

$houseChargeQ = "SELECT COUNT(total) AS housechg_count, ROUND(-SUM(d.total),2) AS housechg_total
	FROM " . DB_LOGNAME . ".$table AS d
	WHERE d.trans_subtype = 'MI'
	AND card_no != 9999
	AND d.trans_status <> 'X'
	AND date(d.datetime) = '$db_date'
	AND d.emp_no <> 9999";

$transCountQ = "SELECT COUNT(d.total) as transactionCount
	FROM " . DB_LOGNAME . ".$table AS d
	WHERE date(d.datetime) = '$db_date'
	AND d.trans_status <> 'X'
	AND d.emp_no <> 9999
	AND d.upc = 'DISCOUNT'";

	$transCountR = mysql_query($transCountQ);
	$row = mysql_fetch_row($transCountR);
	$count = $row[0];

$basketSizeQ = "SELECT ROUND(($gross/$count),2) AS basket_size";

/**
 * Sales of equity
 */

$sharePaymentsQ = "SELECT d.emp_no, d.card_no, 'MEMBER SHARE PMT',ROUND(d.total,2) as total 
	FROM " . DB_LOGNAME . ".$table as d 
	WHERE date(d.datetime) = '$db_date'
	AND d.department = 36
	AND d.trans_status <> 'X'
	AND d.emp_no <> 9999
	GROUP BY d.card_no";

/*
$shareCountQ = "SELECT COUNT(total) AS peopleshare_count
	FROM " . DB_LOGNAME . ".$table
	WHERE date(datetime) = '$db_date'
	AND description = 'MEMBERSHIP EQUITY'
	AND trans_status <> 'X'
	AND emp_no <> 9999";

	$shareCountR = mysql_query($shareCountQ);
	$row = mysql_fetch_row($shareCountR);
	$shareCount = $row[0];
*/
/**
 * Discounts by member type;
 */

$percentsQ = "SELECT c.discount AS volunteer_discount,(ROUND(SUM(d.unitPrice),2)) AS totals 
	FROM " . DB_LOGNAME . ".$table AS d LEFT JOIN " . DB_NAME . ".custdata AS c 
	ON d.card_no = c.CardNo 
	WHERE date(d.datetime) = '$db_date'
	AND c.staff IN(3,4,6)
	AND d.voided = '5'
	AND d.trans_status <> 'X' 
	AND d.emp_no <> 9999 
	GROUP BY c.discount
	WITH ROLLUP";

$memstatus = "SELECT m.memDesc as memStatus,ROUND(SUM(d.total),2) AS Sales,ROUND((SUM(d.total)/$gross*100),2) AS pct
	FROM " . DB_LOGNAME . ".$table d, " . DB_NAME . ".memtype m
	WHERE d.memType = m.memtype
	AND date(d.datetime) = '$db_date'
  	AND d.trans_type IN('I','D')
  	AND d.trans_status <>'X'
  	AND d.department <= 35 AND d.department <> 0
  	AND d.upc <> 'DISCOUNT'
  	AND d.emp_no <> 9999
	GROUP BY m.memtype";

$memtype = "SELECT s.staff_desc as memType,ROUND(SUM(d.total),2) AS Sales,ROUND((SUM(d.total)/$gross*100),2) AS pct 
	FROM " . DB_LOGNAME . ".$table d,	" . DB_NAME . ".staff s 
	WHERE d.staff = s.staff_no 
	AND date(d.datetime) = '$db_date'
  	AND d.trans_type IN('I','D')
  	AND d.trans_status <>'X'
  	AND d.department <= 35 AND d.department <> 0
  	AND d.upc <> 'DISCOUNT'
  	AND d.emp_no <> 9999
	GROUP BY s.staff_no";

$patronage = "SELECT emp_no, card_no, description, total
	FROM " . DB_LOGNAME . ".$table
	WHERE date(datetime) = '$db_date'
	AND trans_subtype = 'PT'
	AND emp_no <> 9999 AND trans_status <> 'X'
	ORDER BY card_no";


////////////////////////////
//
//
//  NOW....SPIT IT ALL OUT....
//
//
////////////////////////////


// echo $db_date . '<br>';
echo '<font size = 2>';
echo '<h2>Sales - Gross & NET</h2>';
include('net.php');
echo "<table border=0><tr><td><b>sales (gross) total</b></td><td align=right><b>".money_format('%n',$gross)."</b></td></tr>";
echo "<tr><td>non-inv total</td><td align=right>".money_format('%n',$hash)."</td></tr>";
echo "<tr><td>totalDisc</td><td align=right>".money_format('%n',$totalDisc)."</td></tr>";
echo "<tr><td>coupon & gift cert. tenders</td><td align=right>".money_format('%n',$coupons)."</td></tr>";
echo "<tr><td>store charges</td><td align=right>".money_format('%n',$strchg)."</td></tr>";
echo "<tr><td>rcvd/accts</td><td align=right>".money_format('%n',$RA)."</td></tr>";
//echo "<tr><td>mkt EBT & chg pmts</td><td align=right>".money_format('%n',$other)."</td></tr>";
echo "<tr><td>&nbsp;</td><td align=right>+___________</td></tr>";
echo "<tr><b><td>net total</td><td align=right>".money_format('%n',$net)."</td></b></tr>";
echo '------------------------------<br>';
echo '<h2>Sales by Inventory Dept.</h2>';
select_to_table($inventoryDeptQ,0,$bgcolor);
deptTotals('Grocery',$gross,$table,$db_date,$db_date,'2,3,6,7,8,9',$bgcolor);
deptTotals('Produce',$gross,$table,$db_date,$db_date,'1,14,15',$bgcolor);
deptTotals('Nonfoods',$gross,$table,$db_date,$db_date,'4,5,10',$bgcolor);
echo '<br />';
echo '<h2>Sales by Non-Inventory Dept.</h2>';
select_to_table($noninventoryDeptQ,0,$bgcolor);
echo '------------------------------<br>';
echo '<h2>Tender Report</h2>';
select_to_table($tendersQ,0,$bgcolor);									// sales by tender type
echo "<h2>Instore Coupon Breakdown</h2>";
select_to_table($instoreQ,0,$bgcolor);									// instore coupon breakdown
echo "<h2>Customer Services Breakdown</h2>";
select_to_table($custSvcQ,0,$bgcolor);									// customer svc breakdown
echo "<h2>Store Charge Breakdown</h2>";
select_to_table($storeChargeQ,0,$bgcolor);								// store charge breakdown
// select_to_table($houseChargeQ,0,$bgcolor);							// house charges
select_to_table($transCountQ,0,$bgcolor);								// transaction count
select_to_table($basketSizeQ,0,$bgcolor);								// basket size
echo '------------------------------<br>';
echo '<h2>Membership & Discount Totals</h2><br>';
echo "<table border=0><font size=2>";
echo "<tr><td>staff total</td><td align=right>".money_format('%n',$staff_total)."</td></tr>";
echo "<tr><td>hoo total</td><td align=right>".money_format('%n',$hoo_total)."</td></tr>";
echo "<tr><td>benefits total</td><td align=right>".money_format('%n',$bene_total)."</td></tr>";
echo "<tr><td>bod total</td><td align=right>".money_format('%n',$bod_total)."</td></tr>";
echo "<tr><td>MAD coupon ($MAD_num)</td><td align=right>".money_format('%n',$MADcoupon)."</td></tr>";
echo "<tr><td>foodforall total ($ffa_num)</td><td align=right>".money_format('%n',$foodforall)."</td></tr>";
echo "<tr><td>&nbsp;</td><td align=right>+___________</td></tr>";
echo "<tr><td><b>total discount</td><td align=right>".money_format('%n',$totalDisc)."</b></td></tr></font></table>";
//select_to_table($percentsQ,0,$bgcolor);								// discounts awarded by percent
//select_to_table($memstatus,0,$bgcolor);	
//select_to_table($memtype,0,$bgcolor);	
echo '<h2>Share Payments</h2><br>';
select_to_table($sharePaymentsQ,0,$bgcolor);							// peopleshare payments
//echo '<b>Share count = '.$shareCount.'</b>';							// peopleshare count
echo '<h2>Patronage Redemption</h2><br>';
select_to_table($patronage,0,$bgcolor);									// patronage redemption
echo '</font>';

?>
</font>
</body>
</html>
