<?php
require_once('../define.conf');
require('conf.php');

function gross($table,$date1,$date2) {
	
	if (!isset($date2)) {$date2 = $date1;}
	
	$grossQ = "SELECT ROUND(sum(total),2) as GROSS_sales
		FROM " . DB_LOGNAME . ".$table
		WHERE date(datetime) >= '$date1'
		AND date(datetime) <= '$date2' 
		AND department < 20
		AND department <> 0
		AND trans_subtype NOT IN('IC','MC')
		AND trans_status <> 'X'
		AND emp_no <> 9999";
	$results = mysql_query($grossQ);
	$row = mysql_fetch_row($results);
	$gross = ($row[0]) ? $row[0] : 0;

	return $gross;
}

function hash_total($table,$date1,$date2) {
	
	if (!isset($date2)) {$date2 = $date1;}
		
	$hashQ = "SELECT ROUND(sum(total),2) AS HASH_sales
		FROM " . DB_LOGNAME . ".$table
		WHERE date(datetime) >= '$date1'
		AND date(datetime) <= '$date2'
		AND department IN(34,36,38,40,41,42,43,44)
		AND trans_status <> 'X'
		AND emp_no <> 9999";

	$results = mysql_query($hashQ);
	$row = mysql_fetch_row($results);
	$hash = $row[0];

	return $hash;
}

function deptTotals ($title,$gross,$table,$date1,$date2,$in,$bgcolor) {
	if (!$bgcolor || $bgcolor == '') $bgcolor = 'FFFFFF';
//	Build table of dept sales
	$query = "SELECT t.dept_name AS $title,ROUND(sum(d.total),2) AS total,ROUND((SUM(d.total)/$gross)*100,2) as pct
		FROM " . DB_LOGNAME . ".$table AS d RIGHT JOIN departments AS t ON d.department = t.dept_no
		AND date(d.datetime) >= '$date1' AND date(d.datetime) <= '$date2' 
		AND t.dept_no IN($in)
		AND d.trans_status <> 'X'
		AND d.emp_no <> 9999
		GROUP BY t.dept_no HAVING t.dept_no IN($in) ORDER BY t.dept_no";
	// echo $query;
	//	Generate totals
	$query1 = "SELECT ROUND(sum(d.total),2) AS total,ROUND((SUM(d.total)/$gross)*100,2) as pct
		FROM " . DB_LOGNAME . ".$table AS d
		WHERE date(d.datetime) >= '$date1' AND date(d.datetime) <= '$date2' 
		AND d.department IN($in)
		AND d.trans_status <> 'X'
		AND d.emp_no <> 9999";
	$results1 = mysql_query($query1);
	$row1 = mysql_fetch_row($results1);
	$tot = $row1[0];
	$pct = $row1[1];

	echo "<p><b>" . $title . " Subtotal: " . money_format('%n',$tot) . " (". number_format($pct,2) ."%)</b></p>\n";
	// select_to_table($query,0,$bgcolor);
}
//
// 		IN PROGRESS --jb 2009-05-03
//
// function inv_depts($table,$date1,$date2) {
// 
// 	if (!isset($date2)) {$date2 = $date1;}
// 
// 	$invdept = "SELECT t.dept_no AS dept_no,t.dept_name AS dept_name,ROUND(sum(d.total),2) AS total
// 	   	FROM " . DB_LOGNAME . ".$table AS d, DB_NAME.departments AS t
// 		WHERE d.department = t.dept_no
// 		AND date(d.datetime) >= '$date1'
// 		AND date(d.datetime) <= '$date2'
// 		AND d.department <> 0
// 		AND d.trans_subtype NOT IN('IC','MC')
// 		AND d.trans_status <> 'X'
// 		AND d.emp_no <> 9999
// 		GROUP BY t.dept_no
// 		ORDER BY t.dept_no";
// 		
// 	$results = mysql_query($invdept);
// 	
// 	while ($row = mysql_fetch_assoc($results)) {
// 		$label = str_replace( ' ', '', strtoupper($row['dept_name']));
// 		
// 	}
// 	
// }
//
//		END IN PROGRESS
//

function staff_total($table,$date1,$date2) {
	require('conf.php');
	
	if (!isset($date2) || $date2 == '') {$date2 = $date1;}
	
	/* $staffQ = "SELECT (SUM(unitPrice)) AS staff_total
		FROM " . DB_LOGNAME . ".$table
		WHERE date(datetime) >= '$date1'
		AND date(datetime) <= '$date2'
		AND upc = 'DISCOUNT'
		AND staff IN(1,2)
		AND trans_status <> 'X' 
		AND emp_no <> 9999";
	
	$lessQ = "SELECT (SUM(unitPrice) * -1) AS TOT
		FROM " . DB_LOGNAME . ".$table
		WHERE date(datetime) >= '$date1'
		AND date(datetime) <= '$date2'
		AND staff IN(1,2)
		AND voided IN(9,10)
		AND trans_status <> 'X'
		AND emp_no <> 9999";
	
	$staffR = mysql_query($staffQ);
	$row = mysql_fetch_row($staffR);
	$staff = $row[0];
	if (is_null($staff)) {
		$staff = 0;
	}
	$lessR = mysql_query($lessQ);
	$row = mysql_fetch_row($lessR);
	$less = $row[0];
	if (is_null($less)) {
		$less = 0;
	}
	
	$staff_total = $staff + $less;
	
	if (is_null($staff_total)) {
		$staff_total = 0;
	}
	*/
	$staffQ = "SELECT (-SUM(total) * ($staff_discount / 100)) AS staff_total
		FROM " . DB_LOGNAME . ".$table
		WHERE date(datetime) >= '$date1' AND date(datetime) <= '$date2'
		AND department BETWEEN 1 AND 20
		AND staff IN(1,2)
		AND trans_status <> 'X' 
		AND emp_no <> 9999";

	// echo $staffQ;

	$staffR = mysql_query($staffQ);
	$row = mysql_fetch_row($staffR);
	$staff_total = $row[0];
	if (is_null($staff_total)) { $staff_total = 0;}
	
	return $staff_total;
}
//	END STAFF_TOTAL

//	BEGIN HOO_TOTAL
function hoo_total($table,$date1,$date2) {
	require('conf.php');
	
	if (!isset($date2) || $date2 == '') {$date2 = $date1;}
	/*	$hoosQ = "SELECT SUM(unitPrice) AS hoos 
		FROM " . DB_LOGNAME . ".$table
		WHERE date(datetime) >= '$date1'
		AND date(datetime) <= '$date2'		
		AND upc = 'DISCOUNT'
		AND staff = 3
		AND trans_status <> 'X' 
		AND emp_no <> 9999";
	
	$lessQ = "SELECT (SUM(d.unitPrice) * -1) AS TOT
		FROM " . DB_LOGNAME . ".$table AS d
		WHERE date(datetime) >= '$date1'
		AND date(datetime) <= '$date2'
		AND staff = 3
		AND voided IN(9,10)
		AND trans_status <> 'X'
		AND emp_no <> 9999";
	
	$hoosR = mysql_query($hoosQ);
	$row = mysql_fetch_row($hoosR);
	$hoos = $row[0];
	
	$lessR = mysql_query($lessQ);
	$row = mysql_fetch_row($lessR);	
	$less = $row[0];
	
	if (is_null($hoos)) {
		$hoos = 0;
	}
	if (is_null($less)) {
		$less = 0;
	}
	
	$hoo_total = $hoos + $less;
	
	if (is_null($hoo_total)) {
		$hoo_total = 0;
	}
	*/
	
	$hoo_total = 0;
	foreach($volunteer_discount AS $row) {
		$wmQ = "SELECT (-SUM(total) * ($row / 100)) AS working_member
			FROM " . DB_LOGNAME . ".$table
			WHERE date(datetime) >= '$date1' AND date(datetime) <= '$date2'
			AND staff = 3
			AND department BETWEEN 1 AND 20
			AND percentDiscount = $row";
		// echo $wmQ;
		$wmR = mysql_query($wmQ);
		$row = mysql_fetch_row($wmR);
		$hoo_tot = $row[0];
		$hoo_total = $hoo_total + $hoo_tot;
	}
		
	return $hoo_total;
}
//	END HOO_TOTAL
	
//	BEGIN BENE_TOTAL
function bene_total($table,$date1,$date2) {
	require('conf.php');
	
	if (!isset($date2) || $date2 == '') {$date2 = $date1;}
/*	$benefitsQ = "SELECT (ROUND(SUM(unitPrice),2)) AS benefits_providers
		FROM " . DB_LOGNAME . ".$table
		WHERE date(datetime) >= '$date1'
		AND date(datetime) <= '$date2'
		AND upc LIKE 'DISCOUNT' 
		AND staff = 5
		AND trans_status <> 'X' 
		AND emp_no <> 9999";
		
	$lessQ = "SELECT (SUM(unitPrice) * -1) AS TOT
		FROM " . DB_LOGNAME . ".$table
		WHERE date(datetime) >= '$date1'
		AND date(datetime) <= '$date2'
		AND staff = 5
		AND voided IN(9,10)
		AND trans_status <> 'X'
		AND emp_no <> 9999";

	$benefitsR = mysql_query($benefitsQ);
	$row = mysql_fetch_row($benefitsR);
	$benefits = $row[0];
	if (is_null($benefits)) {
		$benefits = 0;
	}

	$lessR = mysql_query($lessQ);
	$row = mysql_fetch_row($lessR);	
	$less = $row[0];
	if (is_null($less)) {
		$less = 0;
	}

	$bene_total = $benefits + $less;

	if (is_null($bene_total)) {
		$bene_total = 0;
	}
*/
	$bene_total = 0;
	foreach($volunteer_discount AS $row) {
		$beneQ = "SELECT (-SUM(total) * ($row / 100)) AS benefit_provider
			FROM " . DB_LOGNAME . ".$table
			WHERE date(datetime) >= '$date1' AND date(datetime) <= '$date2'
			AND staff = 5
			AND department BETWEEN 1 AND 20
			AND percentDiscount = $row";
		// echo $wmQ;
		$beneR = mysql_query($beneQ);
		$row = mysql_fetch_row($beneR);
		$bene_tot = $row[0];
		$bene_total = $bene_total + $bene_tot;
	}
	
	return $bene_total;
}
//	END BENE_TOTAL

//	BOD DISCOUNTS
function bod_total($table,$date1,$date2) {
	require('conf.php');
	
	if (!isset($date2) || $date2 == '') {$date2 = $date1;}
/*	$bodQ = "SELECT (ROUND(SUM(unitPrice),2)) AS bod_discount
		FROM " . DB_LOGNAME . ".$table
		WHERE date(datetime) >= '$date1'
		AND date(datetime) <= '$date2'
		AND upc = 'DISCOUNT'
		AND staff = 4
		AND trans_status <> 'X' 
		AND emp_no <> 9999";

	$bodR = mysql_query($bodQ);
	$row = mysql_fetch_row($bodR);
	$bod = $row[0];
	if (is_null($bod)) {
		$bod = 0;
	}

	$lessQ = "SELECT (SUM(unitPrice) * -1) AS TOT
		FROM " . DB_LOGNAME . ".$table
		WHERE date(datetime) >= '$date1'
		AND date(datetime) <= '$date2'
		AND staff = 4
		AND voided IN(9,10)
		AND trans_status <> 'X'
		AND emp_no <> 9999";

	$lessR = mysql_query($lessQ);
	$row = mysql_fetch_row($lessR);	
	$less = $row[0];
	if (is_null($less)) {
		$less = 0;
	}

	$bod_total = $bod + $less;

	if (is_null($bod_total)) {
		$bod_total = 0;
	}
*/	
	$boardQ = "SELECT (-SUM(total) * ($board_discount / 100)) AS board_total
		FROM " . DB_LOGNAME . ".$table
		WHERE date(datetime) >= '$date1' AND date(datetime) <= '$date2'
		AND department BETWEEN 1 AND 20
		AND staff IN(4)
		AND trans_status <> 'X' 
		AND emp_no <> 9999";
		
	$boardR = mysql_query($boardQ);
	$row = mysql_fetch_row($boardR);
	$bod_total = $row[0];
	if (is_null($bod_total)) { $bod_total = 0;}

	return $bod_total;
}
	//	END BOD DISCOUNT

function MADcoupon($table,$date1,$date2) {
	require('conf.php');

	if (!isset($date2) || $date2 == '') {$date2 = $date1;}
/*
	$MADcouponQ = "SELECT ROUND(SUM(unitPrice),2) AS MAD_Coupon_total
		FROM " . DB_LOGNAME . ".$table
		WHERE date(datetime) >= '$date1'
		AND date(datetime) <= '$date2'
		AND voided = 9
		AND trans_status <> 'X'
		AND emp_no <> 9999";

	$MADcouponR = mysql_query($MADcouponQ);
	$row = mysql_fetch_row($MADcouponR);
	$MADcoupon = $row[0];
	if (is_null($MADcoupon)) {
		$MADcoupon = 0;
	}
*/
	// 	NEW MAD coupon reporting format?.....  -- 2009-03-09
	$trans_IDQ = "SELECT CONCAT(emp_no,'_',register_no,'_',trans_no) AS trans_ID
		FROM " . DB_LOGNAME . ".$table
		WHERE date(datetime) >= '$date1' AND date(datetime) <= '$date2'
		AND voided = 9
		AND trans_status NOT IN ('X','V')
		AND emp_no <> 9999";
	// echo $trans_IDQ;
	$result = mysql_query($trans_IDQ);
	$MAD_num = mysql_num_rows($result);
	$MADcoupon = 0;
	while ($row = mysql_fetch_array($result)) {
		$n = explode('_',$row['trans_ID']);
		$emp_no = $n[0];
		$register_no = $n[1];
		$trans_no = $n[2];
		$query = "SELECT (-SUM(total) * ($MAD_discount / 100)) as MADdiscount
			FROM " . DB_LOGNAME . ".$table
			WHERE date(datetime) >= '$date1' AND date(datetime) <= '$date2'
			AND emp_no = $emp_no AND register_no = $register_no AND trans_no = $trans_no
			AND department BETWEEN 1 AND 20";
		$result2 = mysql_query($query);
		$row2 = mysql_fetch_row($result2);
		$MAD_tot = $row2[0];
		// echo "MAD_tot = " . $MAD_tot;
		$MADcoupon = $MADcoupon + $MAD_tot;
	}

	return compact('MADcoupon','MAD_num');
}


function SSDdiscount($table,$date1,$date2) {
	require('conf.php');

	if (!isset($date2) || $date2 == '') {$date2 = $date1;}
	$SSDdiscount = 0;
	$SSDD_num = 0;
	// if (strtotime($date1) <= strtotime($dbChangeDate) && strtotime($date2) <= strtotime($dbChangeDate)) { // Old method...
	// 
	// 	$SSDDQ = "SELECT SUM(total), COUNT(unitprice)
	// 		FROM is4c_log.$table
	// 		WHERE voided = 22
	// 		AND DATE(datetime) BETWEEN '$date1' AND '$date2'
	// 		AND emp_no <> 9999
	// 		AND trans_status <> 'X'";
	// 	$SSDDR = mysql_query($SSDDQ);
	// 	list($SSDdiscount, $SSDD_num) = mysql_fetch_row($SSDDR);
	// 
	// 	$SSDdiscount = (is_null($SSDdiscount) ? 0 : $SSDdiscount);
	// 	$SSDD_num = (is_null($SSDD_num) ? 0 : $SSDD_num);
	// 	
	// } elseif (strtotime($date1) > strtotime($dbChangeDate) && strtotime($date2) > strtotime($dbChangeDate)) { // New method...
	// 	
		$SSDDQ = "SELECT SUM(unitprice) as tot, COUNT(unitprice) as ct
			FROM " . DB_LOGNAME . ".$table
			WHERE upc = 'SPECIALDISC'
			AND DATE(datetime) BETWEEN '$date1' AND '$date2'
			AND emp_no <> 9999
			AND trans_status <> 'X'";
		$SSDDR = mysql_query($SSDDQ);
		list($SSDdiscount2, $SSDD_num) = mysql_fetch_row($SSDDR);
		
		$SSDdiscount2 = (is_null($SSDdiscount2) ? 0 : $SSDdiscount2);
		$SSDD_num = (is_null($SSDD_num) ? 0 : $SSDD_num);
	// } else { // Mixed bag...sum of two queries...
	// 	
	// 	$SSDDQ = "SELECT SUM(total), COUNT(total)
	// 		FROM is4c_log.$table
	// 		WHERE voided = 22
	// 		AND DATE(datetime) BETWEEN '$date1' AND '$dbChangeDate'
	// 		AND emp_no <> 9999
	// 		AND trans_status <> 'X'";
	// 	$SSDDR = mysql_query($SSDDQ);
	// 	list($SSDdiscount, $SSDD_num) = mysql_fetch_row($SSDDR);
	// 
	// 	$SSDdiscount = (is_null($SSDdiscount) ? 0 : $SSDdiscount);
	// 	$SSDD_num = (is_null($SSDD_num) ? 0 : $SSDD_num);
	// 	
	// 	$SSDDQ = "SELECT SUM(total), COUNT(total)
	// 		FROM is4c_log.$table
	// 		WHERE voided = 22
	// 		AND DATE(datetime) BETWEEN '$dbNewDate' AND '$date2'
	// 		AND emp_no <> 9999
	// 		AND trans_status <> 'X'";
	// 	$SSDDR = mysql_query($SSDDQ);
	// 	list($SSDdiscount2, $SSDD_num2) = mysql_fetch_row($SSDDR);
	// 	
	// 	$SSDdiscount += (is_null($SSDdiscount2) ? 0 : $SSDdiscount2);
	// 	$SSDD_num += (is_null($SSDD_num2) ? 0 : $SSDD_num2);
	// }
	
	return compact('SSDdiscount2','SSDD_num','SSDDQ');
}

function foodforall($table,$date1,$date2) {
	require('conf.php');
	
	if (!isset($date2) || $date2 == '') {$date2 = $date1;}
/*
	$foodforallQ = "SELECT ROUND(SUM(unitPrice),2) AS FoodForAll_total
		FROM " . DB_LOGNAME . ".$table
		WHERE date(datetime) >= '$date1'
		AND date(datetime) <= '$date2'
		AND voided = 10
		AND trans_status <> 'X'
		AND emp_no <> 9999";

	$foodforallR = mysql_query($foodforallQ);
	$row = mysql_fetch_row($foodforallR);
	$foodforall = $row[0];
	if (is_null($foodforall)) {
		$foodforall = 0;
	}
*/
	//	NEW need-based-discount reporting calcs
	
	$trans_IDQ = "SELECT CONCAT(DATE(datetime),'_',emp_no,'_',register_no,'_',trans_no) AS trans_ID
		FROM " . DB_LOGNAME . ".$table
		WHERE date(datetime) >= '$date1' AND date(datetime) <= '$date2'
		AND voided = 10
		AND trans_status NOT IN ('X','V')
		AND emp_no <> 9999";
	// echo $trans_IDQ;
	$result = mysql_query($trans_IDQ) OR die(mysql_error() . "<br />" . $trans_IDQ);
	$ffa_num = mysql_num_rows($result);
	$foodforall = 0;
	while ($row = mysql_fetch_array($result)) {
		$n = explode('_',$row['trans_ID']);
		$date = $n[0];
		$emp_no = $n[1];
		$register_no = $n[2];
		$trans_no = $n[3];
		$query = "SELECT (-SUM(total) * ($need_based_discount / 100)) as NBDiscount
			FROM " . DB_LOGNAME . ".$table
			WHERE date(datetime) = '$date'
			AND emp_no = $emp_no AND register_no = $register_no AND trans_no = $trans_no
			AND department BETWEEN 1 AND 20";
		$result2 = mysql_query($query) OR die(mysql_error() . "<br />" . $query);
		$row2 = mysql_fetch_row($result2);
		$ffa_tot = $row2[0];
		// echo "ffa_tot = " . $ffa_tot;
		$foodforall = $foodforall + $ffa_tot;
	}
	
	return compact('foodforall', 'ffa_num');
}

function tenDisc($table,$date1,$date2) {
	require('conf.php');
	
	if (!isset($date2) || $date2 == '') {$date2 = $date1;}
	
	$tenDiscQ = "SELECT SUM(total)
		FROM " . DB_LOGNAME . ".$table
		WHERE upc='TENDISCOUNT'
		AND DATE(datetime) BETWEEN '$date1' AND '$date2'
		AND emp_no <> 9999
		AND trans_status <> 'X'";
	$tenDiscR = mysql_query($tenDiscQ);
	list($tenDisc) = mysql_fetch_row($tenDiscR);
	
	$tenDisc = (is_null($tenDisc) ? 0 : $tenDisc);
	
	return $tenDisc;
}
function miscDisc($table, $date1, $date2) {
	require('conf.php');
	
	if (!isset($date2) || $date2 == '') {$date2 = $date1;}
	
	if (strtotime($date1) <= strtotime($dbChangeDate) && strtotime($date2) <= strtotime($dbChangeDate)) { // Old method...
	
		$miscQ = "SELECT SUM(total)
			FROM " . DB_LOGNAME . ".$table
			WHERE DATE(datetime) BETWEEN '$date1' AND '$date2'
			AND upc='DISCOUNT'
			AND staff = 0
			AND trans_status <> 'X'
			AND emp_no <> 9999";
		$miscR = mysql_query($miscQ);
	
		list($misc_total) = mysql_fetch_row($miscR);
		$misc_total = (is_null($misc_total) ? 0 : $misc_total);
	
		$miscFFAQ = "SELECT SUM(unitprice) AS wmFFA
			FROM " . DB_LOGNAME . ".$table
			WHERE DATE(datetime) BETWEEN '$date1' AND '$date2'
			AND staff = 0
			AND voided = 10
			AND trans_status <> 'X'
			AND emp_no <> 9999";
		$miscFFAR = mysql_query($miscFFAQ);
		list($miscFFA) = mysql_fetch_row($miscFFAR);
		$miscFFA = (is_null($miscFFA) ? 0 : $miscFFA);
	
		$miscMADQ = "SELECT SUM(unitprice) AS wmFFA
			FROM " . DB_LOGNAME . ".$table
			WHERE DATE(datetime) BETWEEN '$date1' AND '$date2'
			AND staff = 0
			AND voided = 9
			AND trans_status <> 'X'
			AND emp_no <> 9999";
		$miscMADR = mysql_query($miscMADQ);
		list($miscMAD) = mysql_fetch_row($miscMADR);
		$miscMAD = (is_null($miscMAD) ? 0 : $miscMAD);
	
		$misc_total -= $miscFFA;
		$misc_total -= $miscMAD;
	
	} elseif (strtotime($date1) > strtotime($dbChangeDate) && strtotime($date2) > strtotime($dbChangeDate)) { // New method...
		
		$miscQ = "SELECT SUM(total)
			FROM " . DB_LOGNAME . ".$table
			WHERE DATE(datetime) BETWEEN '$date1' AND '$date2'
			AND upc='DISCOUNT'
			AND staff = 0
			AND trans_status <> 'X'
			AND emp_no <> 9999";
		$miscR = mysql_query($miscQ);
	
		list($misc_total) = mysql_fetch_row($miscR);
		$misc_total = (is_null($misc_total) ? 0 : $misc_total);
		
	} else { // Mixed bag...slightly complicated query...
		// First half...old style
		$miscQ = "SELECT SUM(total)
			FROM " . DB_LOGNAME . ".$table
			WHERE DATE(datetime) BETWEEN '$date1' AND '$dbChangeDate'
			AND upc='DISCOUNT'
			AND staff = 0
			AND trans_status <> 'X'
			AND emp_no <> 9999";
		$miscR = mysql_query($miscQ);
	
		list($misc_total) = mysql_fetch_row($miscR);
		$misc_total = (is_null($misc_total) ? 0 : $misc_total);
	
		$miscFFAQ = "SELECT SUM(unitprice) AS wmFFA
			FROM " . DB_LOGNAME . ".$table
			WHERE DATE(datetime) BETWEEN '$date1' AND '$dbChangeDate'
			AND staff = 0
			AND voided = 10
			AND trans_status <> 'X'
			AND emp_no <> 9999";
		$miscFFAR = mysql_query($miscFFAQ);
		list($miscFFA) = mysql_fetch_row($miscFFAR);
		$miscFFA = (is_null($miscFFA) ? 0 : $miscFFA);
	
		$miscMADQ = "SELECT SUM(unitprice) AS wmFFA
			FROM " . DB_LOGNAME . ".$table
			WHERE DATE(datetime) BETWEEN '$date1' AND '$dbChangeDate'
			AND staff = 0
			AND voided = 9
			AND trans_status <> 'X'
			AND emp_no <> 9999";
		$miscMADR = mysql_query($miscMADQ);
		list($miscMAD) = mysql_fetch_row($miscMADR);
		$miscMAD = (is_null($miscMAD) ? 0 : $miscMAD);
	
		$misc_total -= $miscFFA;
		$misc_total -= $miscMAD;
		
		// Second half...new style
		$miscQ = "SELECT SUM(total)
			FROM " . DB_LOGNAME . ".$table
			WHERE DATE(datetime) BETWEEN '$dbNewDate' AND '$date2'
			AND upc='DISCOUNT'
			AND staff = 0
			AND trans_status <> 'X'
			AND emp_no <> 9999";
		$miscR = mysql_query($miscQ);
	
		list($misc_total2) = mysql_fetch_row($miscR);
		$misc_total += (is_null($misc_total2) ? 0 : $misc_total2);
	}
	
	return $misc_total;
}

?>