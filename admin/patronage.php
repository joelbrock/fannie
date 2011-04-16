<?php
// require_once('../define.conf');

$page_title = 'Fannie - Administration';
$header = 'Patronage Redemption Report';
include('../src/header.php');

echo '<SCRIPT TYPE="text/javascript">
	<!--
	function popup(mylink, windowname)
	{
	if (! window.focus)return true;
	var href;
	if (typeof(mylink) == "string")
	   href=mylink;
	else
	   href=mylink.href;
	window.open(href, windowname, "width=650,height=800,scrollbars=yes,menubar=no,location=no,toolbar=no,dependent=yes");
	return false;
	}
	//-->
	</SCRIPT>
	</HEAD><BODY>';

for ($i=DATE('Y');$i>2006;$i--) {
	if ($i == $year) {
		$year_array .= '';
	} else {
		$year_array .= "," . $i;			
	}
}
$year_array = trim($year_array, ",");

if (isset($_POST['submit']) && $_POST['check'] != TRUE) {
	foreach ($_POST AS $key => $value) {
		$$key = $value;
	}	
/*
	$today = date('Y-m-d');	
	$dlog = "dlog_2008_pr";
	// $prt = "cust_pr_" . date('Y');
	$prt = "cust_pr_2008";
	if (!$date1) { 
		$dateR = mysql_query("SELECT MIN(DATE(datetime)) FROM is4c_log.$dlog");
		$row = mysql_fetch_row($dateR);
		$date1 = $row[0];
	}
	if (!$date2) { $date2 = $today; }
*/

	$ryear = $year + 1;
	$dlog = "dlog_" . $ryear;
	$prt = "cust_pr_" . $ryear;
	
	// echo "<center><h1>Patronage Redemption Report</h1><h2>$date1 thru $date2</h2></center><br>";
	echo "<center><h1>Patronage Redemption Report</h1><h2>For FY$year</h2></center>";
	
	$query = "SELECT COUNT(*) AS ct, -SUM(total) AS total, description 
		FROM " . DB_LOGNAME . ".PR_redeemed 
		WHERE ((YEAR(datetime) = $ryear) OR (SUBSTR(description, -4) = $year))
		HAVING SUBSTR(description, -4) NOT IN ($year_array)";
	$error = "SELECT DATE(p.datetime) as date, 
		p.card_no as card_no,
		-p.total as total,
		-r.paid as paid_out,
		(r.paid + p.total) as diff
		FROM " . DB_LOGNAME . ".PR_redeemed p, $prt r
		WHERE p.card_no = r.card_no 
		AND YEAR(p.datetime) = $ryear
		HAVING diff <> 0
		ORDER BY p.datetime DESC";
	// echo $query;
	$result = mysql_query($query) OR die(mysql_error() . "<br />" . $query);
	$row = mysql_fetch_row($result);
	$rc = $row[0];
	$rt = $row[1];
	mysql_free_result($result);
	$result = mysql_query($error) OR die(mysql_error() . "<br />" . $error);
	$err = mysql_num_rows($result);
	mysql_free_result($result);
	echo "<table border=0 align=center><tr>\n<td>";
	echo "<table align=center border=1 cellspacing=0 cellpadding=6 width=300px>\n
		<tr>\n
			<td colspan=2 valign=bottom align=left!>
				<h3>VOUCHERS REDEEMED</h3>
				<a href='patronage_detail.php?ryear=$ryear&popup=daily' onClick=\"return popup(this, 'patronage_detail')\";>Daily totals</a><br>\n
				<a href='patronage_detail.php?ryear=$ryear&popup=redeemed' onClick=\"return popup(this, 'patronage_detail')\";>Show all</a>\n
			</td>\n
		</tr>\n
		<tr>\n
			<td width=100px>count</td><td width=200px>total</td></tr>\n
		<tr>\n
			<td><font size=5>$rc</font></td>\n
			<td><font size=5>" . money_format('%n',$rt) . "</font></td>\n
		</tr>\n";
	if ($err > 0) { echo "<tr>\n<td colspan=2>Patronage record errors detected: <font color=red size=4>$err</font></td>\n</tr>\n";}
	echo "\n</table>\n</td></tr>";
	
	$cardq = "SELECT card_no FROM " . DB_LOGNAME . ".PR_redeemed WHERE YEAR(datetime) = '$ryear'";
	$cardr = mysql_query($cardq);
	while ($row = mysql_fetch_array($cardr)) {
		$card_array .= $row[0] . ",";
	}
	if ($card_array) {
        	$card_array = trim($card_array, ",");
	} else {
        	$card_array = '0';
	}
	// echo 'Card array=' . $card_array . '<br>';

	$query = "SELECT COUNT(*) AS ct, SUM(paid) AS total FROM $prt 
		WHERE card_no NOT IN($card_array)";

	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	$oc1 = $row[0];
	$ot1 = $row[1];
	mysql_free_result($result);
	
	$oc = $oc1 - $rc;
	$ot = $ot1 - $rt;
	
	echo "<tr>\n<td><table align=center border=1 cellspacing=0 cellpadding=6 width=300px><tr>\n
		<td colspan=2 valign=bottom align=left><h3>VOUCHERS OUTSTANDING</h3>
		<a href='patronage_detail.php?ryear=$ryear&popup=outstanding' onClick=\"return popup(this, 'patronage_detail')\";>Show all</a></td></tr>
		<tr>\n<td width=100px>count</td><td width=200px>total</td></tr>
		<tr>\n<td><font size=5>$oc</font></td>
		<td><font size=5>" . money_format('%n',$ot) . "</font></td></tr>\n</table>\n";
	
	$result0 = mysql_query("SELECT MIN(datetime) FROM " . DB_LOGNAME . ".$dlog WHERE trans_subtype = 'PT'");
	$row0 = mysql_fetch_row($result0);
	$first = $row0[0];	
	if (!$first) { $first = date('Y') . '-07-01'; }

	$query = "SELECT COUNT(*) as ct, SUM(total) as donations 
		FROM " . DB_LOGNAME . ".$dlog 
		WHERE datetime >= '$first'
		AND department = 38";
	
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	if (!$row[0]) { $dc = 0; } else { $dc = $row[0]; }
	if (!$row[1]) { $dt = 0; } else { $dt = $row[1]; }
	
	echo "<tr>\n<td><table align=center border=1 cellspacing=0 cellpadding=6 width=300px><tr>\n
		<td colspan=2 valign=bottom align=left><h3>GENERAL DONATIONS</h3>\n
		As of $first</td></tr>\n
		<tr>\n<td width=100px>count</td><td width=200px>total</td></tr>\n
		<tr>\n<td><font size=5>$dc</font></td>\n
		<td><font size=5>" . money_format('%n',$dt) . "</font></td></tr>\n</table>\n";
	
	echo "</td></tr>\n</table><br><a href='patronage.php'>START OVER</a>";
	include('../src/footer.php');

} elseif (isset($_POST['submit']) && $_POST['check'] == TRUE) {
	if (!$_POST['card_no'] || !is_numeric($_POST['card_no'])) {
		$card_no = 0;
		echo "<div id=alert><p>INVALID ENTRY:  Please enter a valid member number -- 
			<font size=2><a href=patronage.php> start over</a></font></p></div>\n";
	} else {
		$card_no = $_POST['card_no'];
		$fyear = $_POST['cyear'];
		$year = $_POST['cyear'] + 1;
		$today = date('Y-m-d');	
		// $prt = "cust_pr_" . date('Y');
		$prt = "cust_pr_" . $year;
		$query = "SELECT card_no FROM $prt WHERE card_no = $card_no";
		$result = mysql_query($query);
		// echo $query;
		$num = mysql_num_rows($result);
		$query1 = "SELECT * FROM " . DB_LOGNAME .".PR_redeemed WHERE card_no = $card_no
			AND ((YEAR(datetime) = $year) OR (SUBSTR(description, -4) = $fyear))";
		// echo $query1;
		$result1 = mysql_query($query1);
		$num1 = mysql_num_rows($result1);

		if (is_null($num) || $num < 1) {
			echo "<div id=alert><p>INVALID ENTRY: There is no refund on file for that member # 
				-- <a href=patronage.php> start over</a></p></div>\n";
		} elseif ($num1) {
			echo "<div id=alert><p>WARNING! A voucher has already been redeemed for this member number in FY$fyear.
				-- <a href=patronage.php> start over</a></p></div>\n";
		} else {
			$result = mysql_query("SELECT * FROM $prt p, custdata c WHERE p.card_no = $card_no AND p.card_no = c.CardNo");
			$row = mysql_fetch_assoc($result);
			$paid = money_format('%n',$row['paid']);
			$name = $row['FirstName'] . " " . $row['LastName'];
			echo "<form method=POST action='patronage.php' target=_self>";
			echo "<input type=hidden name=memtype value=".$row['memType'].">";
			echo "<input type=hidden name=staff value=".$row['staff'].">";
			echo "<h2>Member #: $card_no</h2><input type=hidden name=card_no value=$card_no>\n
				<h2>Name: $name</h2>\n
				<h2>Refund Amt: $paid in FY<font color=#FF0000>$fyear</font></h2>\n
				<input type=hidden name=paid value=$paid>\n
				<input type=hidden name=fyear value=$fyear>\n
				<p>Please verify that the above information matches the actual patronage refund voucher.  By clicking 'commit' you will be applying
				a check tender to the transaction logs for the amount of $paid.  This action cannot be undone.</p>\n<p>If the total above does not 
				equal the total on the voucher please contact the <a href='mailto:admin@peoples.coop'>POS sysadmin</a> to proceed.</p>\n<br><br>\n";
			echo "<input type=submit name=commit value=COMMIT><a href='patronage.php'> cancel</a></form>\n";
		}
	}
	// debug_p($_REQUEST, "all the data coming in");
	
	
	include('../src/footer.php');

} elseif ($_POST['commit']) {
	foreach ($_POST AS $key => $value) {
		$$key = $value;
	}
	// $fyear = $_POST['cyear'];
	$year = $_POST['fyear'] + 1;
	$dlog = "dlog_" . date('Y');
	// $paid = substr($paid, 1);
	$ryear = date('Y');
	$query = "SELECT * FROM " . DB_LOGNAME . ".PR_redeemed WHERE card_no = $card_no AND YEAR(datetime) = $year";
	// echo $query;
	$result = mysql_query($query);
	$num = mysql_num_rows($result);
	if ($num) {
		$output = "WARNING! A voucher has already been redeemed for this member number.  Process aborted.";
		
	} else {
		$desc = "Patronage Check Pay - FY" . $fyear;
		$insert = "INSERT INTO " . DB_LOGNAME . ".$dlog (datetime, register_no, emp_no, trans_no, description, 
			trans_type, trans_subtype, trans_status, total, voided, memType, staff, card_no, trans_id) 
			values (now(), 9, 9, 0, '$desc', 'T', 'PT', 0, -$paid, 55, $memtype, $staff, $card_no, 0)";
		// echo $insert;
		$result = mysql_query($insert);
		if (!$result) {
			$output = "There was an error: " . mysql_error();
		} else {
			$result = mysql_query("SELECT * FROM " . DB_LOGNAME . ".$dlog WHERE voided = 55 ORDER BY datetime DESC LIMIT 1");
			$row = mysql_fetch_assoc($result);
			$output = "<h3>Payment successfully committed! </h3>time:" .$row['datetime']. "<br> description: " .$row['description']. "<br> total: "
				.$row['total']. "<br> card_no: " .$row['card_no'];
			$output .= "<p>To view the new entry click <a href='patronage_detail.php?ryear=$year&popup=redeemed' onClick=\"return popup(this, 'patronage_detail')\";>HERE</a>.</p>";
			
		}
	}
	
	echo "<div id=alert>\n<p>";
	echo $output . "</p></div>\n<br><br><a href='patronage.php'>START OVER</a>";

	// debug_p($_REQUEST, "all the data coming in");

	include("../src/footer.html");
	
} else {

	echo "<div id=box>\n<center><h3>Patronage Redemption Report</h3></center>\n
		<form method=POST action='patronage.php' target=_self>\n
		<table border=0 cellspacing=3 cellpadding=5 align=center>\n
			<tr> \n
	            <th colspan=2 align=center> <p><b>Select Year</b></p></th>\n
			</tr>\n
			<tr>\n 
				<!--<td>
					<p><b>Date Start</b> </p>
			    	<p><b>End</b></p>
			    </td>
				<td>
			    	<p><input type=text size=10 name=date1 onclick=\"showCalendarControl(this);\"></p>
		        	<p><input type=text size=10 name=date2 onclick=\"showCalendarControl(this);\"></p>
			    </td>-->
				<td colspan=2>
					<center><p>Fiscal Year:  
					<select name=year id=year>\n";
					
	function TableExists($tablename,$db) {

		// Get a list of tables contained within the database.
	    $result = mysql_list_tables($db);
	    $rcount = mysql_num_rows($result);

	    // Check each in list for a match.
	    for ($i=0;$i<$rcount;$i++) {
	        if (mysql_tablename($result, $i)==$tablename) return true;
	    }
	    return false;
	}
	
	for ($i=2100;$i>2007;$i--) {
		$tbl = "cust_pr_" . $i;
		if (TableExists($tbl, "is4c_op") == true) {
			$label = $i - 1;
			echo "<option value=$label>$label</option>\n";
		}
	}
							
	echo			"</p></center>
				</td>
			</tr>\n
	        <!--<tr>\n
				<td colspan=2>\n
				<p><b>Leave date fields blank for YTD</b></p>\n
				</td>\n
			</tr>\n-->
		<tr> \n
				<td colspan=2><center><input type=submit name=submit value=\"Submit\"></center> </td>\n
				<!--<td><input type=reset name=reset value=\"Start Over\"> </td>\n
				<td>&nbsp;</td>\n-->
			</tr>\n
		</table>\n
	</form>\n</div>";

	echo "<div id=box>\n<center><h3>Check Request Entry Form</h3></center>\n
		<p>Please make sure that you process check request using this interface <i>BEFORE</i> cutting a check to ensure the voucher in 
		question has not already been redeemed</p>
		<form method=POST action=patronage.php target=_self>\n
		<table border=0 cellspacing=3 cellpadding=5 align=center>\n
			<tr><td><p><b>Fiscal Year:</b></p></td><td>\n
			<select name=cyear id=cyear>\n";
	
	for ($i=date('Y');$i>(date('Y') - 4);$i--) {
		$tbl = "cust_pr_" . $i;
		if (TableExists($tbl, "is4c_op") == true) {
			$label = $i - 1;
			echo "<option value=$label>$label</option>\n";
		}
	}
						
	echo		"</td><td><p><b>Member #</b></p></td><td><input type=text name=card_no size=5></td></tr>\n
			<tr><td colspan=4><center><input type=hidden name=check value=TRUE>
			<input type=submit name=submit value=\"Submit\"></input></td></tr>\n
		</table></form>\n</div>";

	include('../src/footer.php');
}

// debug_p($_REQUEST, "all the data coming in");


?>

