<?php
/*******************************************************************************

    Copyright 2007 People's Food Co-op, Portland, Oregon.

    This file is part of Fannie.

    IS4C is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    IS4C is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IS4C; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/
/*
if(isset($_GET['sort'])){
  if(isset($_GET['XL'])){
     header("Content-Disposition: inline; filename=deptSales.xls");
     header("Content-Description: PHP3 Generated Data");
     header("Content-type: application/vnd.ms-excel; name='excel'");
  }
}*/

require_once('../src/mysql_connect.php');
require_once('../src/functions.php');
// include('../src/datediff.php');

if(isset($_POST['submit'])){
	foreach ($_POST AS $key => $value) {
		$$key = $value;
		//echo $key ." : " .  $value"<br>";
	}
echo "<body>";

setlocale(LC_MONETARY, 'en_US');
	$today = date("F d, Y");	

// Page header

	echo "Report run on ";
	echo $today;
	echo "</br>";
	echo "For ";
	print $date1;
	echo " through ";
	print $date2;
	echo "</br></br></br>";

/*
	if(!isset($_GET['XL'])){
	echo "<p><a href='deptSales.php?XL=1&sort=$sort&date1=$date1&date2=$date2&deptStart=$deptStart&deptEnd=$deptEnd&pluReport=$pluReport&order=$order'>Dump to Excel Document</a></p>";
	
	} 
*/	
		
	// Check year in query, match to a dlog table
	$year1 = idate('Y',strtotime($date1));
	$year2 = idate('Y',strtotime($date2));

	if ($year1 != $year2) {
		echo "<div id='alert'><h4>Reporting Error</h4>
			<p>Fannie cannot run reports across multiple years.<br>
			Please retry your query.</p></div>";
		exit();
	}
//	elseif ($year1 == date('Y')) { $table = 'dtransactions'; }
	else { $table = 'dlog_' . $year1; }
	
	$date2a = $date2 . " 23:59:59";
	$date1a = $date1 . " 00:00:00";

	include('reportFunctions.php');
	$gross = gross($table,$date1,$date2);

	if (isset($sales)) {

		$hash = hash_total($table,$date1,$date2);


		$staff_total = staff_total($table,$date1,$date2);
		$hoo_total = hoo_total($table,$date1,$date2);
		$bene_total = bene_total($table,$date1,$date2);
		$bod_total = bod_total($table,$date1,$date2);
		$MADcoupon = MADcoupon($table,$date1,$date2);
		$foodforall = foodforall($table,$date1,$date2);

		$a = array($staff_total,$bene_total,$hoo_total,$bod_total,$MADcoupon,$foodforall);
		$totalDisc = array_sum($a);

		$ICQ = "SELECT ROUND(SUM(total),2) AS coupons
			FROM is4c_log.$table
			WHERE datetime >= '$date1a' AND datetime <= '$date2a'
			AND trans_subtype IN('IC')
			AND trans_status <> 'X'
			AND emp_no <> 9999";

			$ICR = mysql_query($ICQ);
			$row = mysql_fetch_row($ICR);
			$IC = $row[0];
			if (is_null($IC)) {
				$IC = 0;
			}

		$MCQ = "SELECT ROUND(SUM(total),2) AS coupons
			FROM is4c_log.$table
			WHERE datetime >= '$date1a' AND datetime <= '$date2a'
			AND trans_subtype IN('MC')
			AND trans_status <> 'X'
			AND emp_no <> 9999";

			$MCR = mysql_query($MCQ);
			$row = mysql_fetch_row($MCR);
			$MC = $row[0];
			if (is_null($MC)) {
				$MC = 0;
			}

		$TCQ = "SELECT ROUND(SUM(total),2) AS coupons
			FROM is4c_log.$table
			WHERE datetime >= '$date1a' AND datetime <= '$date2a'
			AND trans_subtype IN('TC')
			AND trans_status <> 'X'
			AND emp_no <> 9999";

			$TCR = mysql_query($TCQ);
			$row = mysql_fetch_row($TCR);
			$TC = $row[0];
			if (is_null($TC)) {
				$TC = 0;
			}

		$coupons = $IC + $MC + $TC;

		$strchgQ = "SELECT ROUND(SUM(d.total),2) AS strchg
			FROM is4c_log.$table AS d
			WHERE d.datetime >= '$date1a' AND d.datetime <= '$date2a'
			AND d.trans_subtype IN('MI')
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999";

			$strchgR = mysql_query($strchgQ);
			$row = mysql_fetch_row($strchgR);
			$strchg = $row[0];
			if (is_null($strchg)) {
				$strchg = 0;
			}

		$RAQ = "SELECT ROUND(SUM(d.total),2) as RAs
			FROM is4c_log.$table AS d
			WHERE d.datetime >= '$date1a' AND d.datetime <= '$date2a'
			AND d.department IN(45)
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999";

			$RAR = mysql_query($RAQ);
			$row = mysql_fetch_row($RAR);
			$RA = $row[0];
			if (is_null($RA)) {
				$RA = 0;
			}
		//	Other = Chrg Payments + Market EBT	
		$otherQ = "SELECT ROUND(SUM(d.total),2) as other
			FROM is4c_log.$table AS d
			WHERE d.datetime >= '$date1a' AND d.datetime <= '$date2a'
			AND d.department IN(35,37)
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999";

			$otherR = mysql_query($otherQ);
			$row = mysql_fetch_row($otherR);
			$other = $row[0];
			if (is_null($other)) {
				$other = 0;
			}

		$net = $gross + $hash + $totalDisc + $coupons + $strchg + $RA;


		 // sales of inventory departments
		$invtotalsQ = "SELECT d.department,t.dept_name,ROUND(sum(d.total),2) AS total,ROUND((SUM(d.total)/$gross)*100,2) as pct
			FROM is4c_log.$table AS d, is4c_op.departments AS t
			WHERE d.department = t.dept_no
			AND date(d.datetime) >= '$date1' AND date(d.datetime) <= '$date2' 
			AND d.department <= 15 AND d.department <> 0
			AND d.trans_subtype NOT IN('IC','MC')
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999
			GROUP BY d.department, t.dept_name";

//		$gross = 0;

		// Sales for non-inventory departments 
		$noninvtotalsQ = "SELECT d.department,t.dept_name,ROUND(sum(total),2) as total, count(d.datetime) AS count
			FROM is4c_log.$table as d join is4c_op.departments as t ON d.department = t.dept_no
			WHERE datetime >= '$date1a' AND datetime <= '$date2a' 
			AND d.department > 35 AND d.department <> 0
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999
			GROUP BY d.department, t.dept_name";
		
		echo "<h2>Income / Expenses</h2>\n
			<table border=0>\n<tr><td><b>sales (gross) total</b></td><td align=right><b>".money_format('%n',$gross)."</b></td></tr>\n
			<tr><td>hash total</td><td align=right>".money_format('%n',$hash)."</td></tr>\n
			<tr><td>totalDisc</td><td align=right>".money_format('%n',$totalDisc)."</td></tr>\n
			<tr><td>coupon & gift cert. tenders</td><td align=right>".money_format('%n',$coupons)."</td></tr>\n
			<tr><td>store charges</td><td align=right>".money_format('%n',$strchg)."</td></tr>\n
			<tr><td>rcvd/accts</td><td align=right>".money_format('%n',$RA)."</td></tr>\n
			<tr><td>mkt EBT & chg pmts</td><td align=right>".money_format('%n',$other)."</td></tr>\n
			<tr><td>&nbsp;</td><td align=right>+___________</td></tr>\n
			<tr><b><td><b>net total</b></td><td align=right><b>".money_format('%n',$net)."</b></td></b></tr>\n
			</table>\n";
			
		echo '</b></td></tr></table><h4>Inventory Department Totals</h4>';
		echo '<p>';
		select_to_table($invtotalsQ,1,'FFFFFF');
		echo '</p>';
		echo '<h4>Non-Inventory Department Totals</h4>';
		select_to_table($noninvtotalsQ,1,'FFFFFF');
	} 
			
	if(isset($tender)) {
		if ($gross == 0 || !$gross ) $gross = 1;

		$tendertotalsQ = "SELECT t.TenderName as tender_type,ROUND(-sum(d.total),2) as total,ROUND((-SUM(d.total)/$gross)*100,2) as pct
			FROM is4c_log.$table as d ,is4c_op.tenders as t 
			WHERE d.datetime >= '$date1a' AND d.datetime <= '$date2a'
			AND d.trans_status <> 'X' 
			AND d.emp_no <> 9999
			AND d.trans_subtype = t.TenderCode
			GROUP BY t.TenderName";
	
		// $gross = 0;
	
		$transcountQ = "SELECT COUNT(d.total) as transactionCount
			FROM is4c_log.$table AS d
			WHERE d.datetime >= '$date1a' AND d.datetime <= '$date2a'
			AND d.upc = 'DISCOUNT'
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999";
	
		$transcountR = mysql_query($transcountQ);
		$row = mysql_fetch_row($transcountR);
		$count = $row[0];
	
		$basketsizeQ = "SELECT ROUND(($gross/$count),2) AS basket_size";
	
		$basketsizeR = mysql_query($basketsizeQ);
		$row = mysql_fetch_row($basketsizeR);
		$basketsize = $row[0];
		
		echo '<h4>Tender Report + Basket Size</h4>';
		select_to_table($tendertotalsQ,1,'FFFFFF');
		echo '<br><p>Transaction count&nbsp;&nbsp;=&nbsp;&nbsp;<b>'.$count;
		echo '</b></p><p>Basket size&nbsp;&nbsp;=&nbsp;&nbsp;<b>'.money_format('%n',$basketsize);
		echo '</p>';
	
	}		
			
	if(isset($discounts)) {
		
		echo "<h2>Membership & Discount Totals</h2>\n
			<table border=0>\n<font size=2>\n<tr><td>staff total</td><td align=right>".money_format('%n',$staff_total)."</td></tr>\n
			<tr><td>hoo total</td><td align=right>".money_format('%n',$hoo_total)."</td></tr>\n
			<tr><td>benefits total</td><td align=right>".money_format('%n',$bene_total)."</td></tr>\n
			<tr><td>bod total</td><td align=right>".money_format('%n',$bod_total)."</td></tr>\n
			<tr><td>MAD coupon</td><td align=right>".money_format('%n',$MADcoupon)."</td></tr>\n
			<tr><td>foodforall total</td><td align=right>".money_format('%n',$foodforall)."</td></tr>\n
			<tr><td>&nbsp;</td><td align=right>+___________</td></tr>\n
			<tr><td><b>total discount</td><td align=right>".money_format('%n',$totalDisc)."</b></td></tr></font>\n
			</table>\n";
		// 
		// // percentage breakdown
		// $percentQ = "SELECT c.discount AS discount,ROUND(-SUM(d.total),2) AS totals 
		// 			FROM is4c_log.$table AS d, is4c_op.custdata AS c 
		// 			WHERE d.card_no = c.CardNo 
		// 			AND d.datetime >= '$date1a' AND d.datetime <= '$date2a' 	
		// 			AND d.upc = 'DISCOUNT'
		// 			AND d.trans_status <> 'X' 
		// 			AND d.emp_no <> 9999 
		// 			GROUP BY c.discount";
		// 		
		$percentQ = "SELECT percentDiscount AS percent, ROUND(SUM(total) * (percentDiscount / 100),2) as discount 
			FROM is4c_log.$table
			WHERE DATE(datetime) >= '$date1' AND DATE(datetime) <= '$date2'
			AND percentDiscount <> 0 AND discountable = 1
			AND trans_status <> 'X' AND emp_no <> 9999
			GROUP BY percent";
		// echo $percentQ;
		echo '</b></p><h4>Discounts By Percentage</h4>';
		select_to_table($percentQ,1,'FFFFFF');	

		// // Discounts by member type;
		$memtypeQ = "SELECT m.memDesc as memberType,ROUND(-SUM(d.total),2) AS discount 
			FROM is4c_log.$table d INNER JOIN is4c_op.custdata c ON d.card_no = c.CardNo 
			INNER JOIN is4c_op.memtype m ON c.memType = m.memtype
			WHERE d.datetime >= '$date1a' AND d.datetime <= '$date2a' 
			AND d.upc = 'DISCOUNT'
			AND d.trans_status <>'X'
	  		AND d.emp_no <> 9999
			GROUP BY m.memDesc, d.upc";
			
		echo '</b></p><h4>Discounts By Member Type</h4>';
		select_to_table($memtypeQ,1,'FFFFFF');
	
		// Sales by member type;
		$memtypeQ = "SELECT m.memDesc as sales_by_memtype,(ROUND(SUM(d.total),2)) AS sales, ROUND((SUM(d.total)/$gross)*100,2) as pct
			FROM is4c_log.$table d, memtype m
			WHERE d.memtype = m.memtype
			AND DATE(d.datetime) >= '$date1' AND DATE(d.datetime) <= '$date2' 
			AND d.department < 20 AND d.department <> 0
	  		AND d.trans_status <>'X'
	  		AND d.emp_no <> 9999
			GROUP BY d.memtype";	
		
		echo "</b></p>\n<h4>Gross Sales By Member Type</h4>\n";
		select_to_table($memtypeQ,1,'FFFFFF');
	}
	
	if(isset($equity)){	
	
		$sharetotalsQ = "SELECT d.datetime AS datetime, d.emp_no AS emp_no, d.card_no AS cardno,c.LastName AS lastname,ROUND(sum(total),2) as total 
			FROM is4c_log.$table as d, is4c_op.custdata AS c
			WHERE d.card_no = c.CardNo
			AND d.datetime >= '$date1a' AND d.datetime <= '$date2a'
			AND d.department = 36 
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999
			GROUP BY d.datetime
			ORDER BY d.datetime";

		$sharetotalQ = "SELECT ROUND(SUM(d.total),2) AS Total_share_pmt
			FROM is4c_log.$table AS d
			WHERE d.datetime >= '$date1a' AND d.datetime <= '$date2a'
			AND d.department = 36
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999";

		$sharetotalR = mysql_query($sharetotalQ);
		$row = mysql_fetch_row($sharetotalR);
		$sharetotal = $row[0];

		$sharecountQ = "SELECT COUNT(d.total) AS peopleshareCount
			FROM is4c_log.$table AS d
			WHERE d.datetime >= '$date1a' AND d.datetime <= '$date2a'
			AND d.department = 36
			AND d.trans_status <> 'X'
			AND d.emp_no <> 9999";
				
		$sharecountR = mysql_query($sharecountQ);
		$row = mysql_fetch_row($sharecountR);
		$sharecount = $row[0];
		
		echo '<h1>Equity Report</h1>';
		echo '<h4>Sales of Member Shares</h4>';
		echo '<p>Total member share payments = <b>'.$sharetotal;
		echo '</b></p><p>Member Share count&nbsp;&nbsp;=&nbsp;&nbsp;<b>'.$sharecount;
		echo '</b></p>';
		select_to_table($sharetotalsQ,1,'FFFFFF');
		
	}

//
// PHP INPUT DEBUG SCRIPT  -- very helpful!
//

// function debug_p($var, $title) 
// {
//     print "<p>$title</p><pre>";
//     print_r($var);
//     print "</pre>";
// }  
// 
// debug_p($_REQUEST, "all the data coming in");

} else {
	
	$page_title = 'Fannie - Reporting';
	$header = 'Period Report';
	include('../src/header.html');
	
	echo '<script src="../src/CalendarControl.js" language="javascript"></script>
		<form method="post" action="period.php" target="_blank">		
		<h2>Period Report</h2>
		<table border="0" cellspacing="5" cellpadding="5">
			<tr> 
				<td>
					<p><b>Date Start</b> </p>
			    	<p><b>End</b></p>
			    </td>
				<td>
			    	<p><input type=text size=10 name=date1 onclick="showCalendarControl(this);"></p>
	            	<p><input type=text size=10 name=date2 onclick="showCalendarControl(this);"></p>
			    </td>
			</tr>
			<tr> 

			</tr>		
			<tr>
				<td><p>Sales totals</p></td>
				<td><input type="checkbox" value="1" name="sales"></td>
			</tr>
			<tr>
				<td><p>Tender report & basket-size</p></td>
				<td><input type="checkbox" value="1" name="tender"></td>
			</tr>
			<tr>
				<td><p>Discount report</p></td>
				<td><input type="checkbox" value="1" name="discounts"></td>
			</tr>
			<tr>
				<td><p>Equity report - DETAILED</p></td>
				<td><input type="checkbox" value="1" name="equity"></td>
			</tr>
			<tr> 
				<td> <input type=submit name=submit value="Submit"> </td>
				<td> <input type=reset name=reset value="Start Over"> </td>
			</tr>
		</table>
	</form>';
	
	include('../src/footer.html');
}


?>
