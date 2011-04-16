<?php
$page_title = 'Fannie - Reporting';
$header = 'Item Properties Report';

// to: miguel
// RE: sales reporting in the dept. sales report (and all values reporting on sales data):  Presently the way i have designed this feature, the values reporting on sales is not "historical".  Meaning values data (local, org., etc.) is tied to the item UPC, not some field in the tlogs.  So sales data will reflect an items CURRENT values, not the values they held when they were sold.  I did it this way so that once buyers had entered most of the values for their products, you could then go back and look at historical sales data using the new values reporting.  This does mean however that if values data for items in a report have changed, the values-based sales data will CHANGE depending on when a report is run!  I suspect this may only be an issue in the Produce dept. (and poss. bulk).  ex.  Summertime:  Lettuce is Local for all of July.  The Report will


if ($_POST['submit']) {
	require_once '../define.conf';
	include '../src/functions.php';
	include 'reportFunctions.php';
	include '../src/header.php';
	foreach ($_POST AS $key => $value) {
		$$key = $value;
	}
	// Check year in query, match to a dlog table
	$year1 = idate('Y',strtotime($date1));
	$year2 = idate('Y',strtotime($date2));

	echo "<head>\n";
	include '../src/head.php';
	echo "\n</head>\n\n";
	if ($year1 != $year2) {
		echo "<div id='alert'><h4>Reporting Error</h4><p>Fannie cannot run reports across multiple years.<br>Please retry your query.</p></div>\n";
	} else { $table = 'dlog_' . $year1; }
	$gross = gross($table,$date1,$date2);
		
	// echo "<div id='progressbar'></div>";	
		
	echo "\n<p>GROSS TOTAL FOR $date1 thru $date2:  <b>" . money_format('%n', $gross) . "</b></p>\n";
	
	$propR = mysql_query("SELECT * FROM item_properties");
	
	$itemsQ = "SELECT COUNT(DISTINCT p.upc) as itmct,
			i.name as Item_Property, 
			COUNT(p.props) as Count,
			ROUND(SUM(d.total),2) as Sales,
			ROUND((SUM(d.total)/$gross)*100,2) as pct_of_gross
		FROM " . PRODUCTS_TBL . " p, item_properties i, " . DB_LOGNAME . ".$table d
		WHERE p.props > 1
		AND p.upc = d.upc
		AND BINARY(props) & i.bit 
		GROUP BY Item_Property";
	$itemsR = mysql_query($itemsQ);
	if (!$itemsR) { die("Query: $itemsQ<br />Error:".mysql_error()); }
	
	echo "<table id='output' cellpadding=6 cellspacing=0 border=0 class=\"sortable-onload-3 rowstyle-alt colstyle-alt\">\n
	  <thead>\n
	    <tr>\n
	      <th class=\"sortable-text\">Item Property (ct.)</th>\n
	      <th class=\"sortable-numeric favour-reverse\">Count.</th>\n
	      <th class=\"sortable-currency favour-reverse\">Sales</th>\n
	      <th class=\"sortable-numeric favour-reverse\">% of gross</th>\n
	    </tr>\n
	  </thead>\n
	  <tbody>\n";
	
	while ($row = mysql_fetch_assoc($itemsR)) {
		echo "<td align=left><b>" . $row['Item_Property'] . "</b> (" . $row['itmct'] . ")</td>\n
			<td align=right>" . $row['Count'] . "</td>\n
			<td align=right>" . money_format('%n',$row['Sales']) . "</td>\n
			<td align=right>" . number_format($row['pct_of_gross'],2) . "%</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
	
	// debug_p($_REQUEST, "all the data coming in");
	
	include '../src/footer.php'; 	
	
} else {

	include '../src/header.php';

	echo "<form action=\"itemProperties.php\" method=\"POST\" target=\"_blank\">\n
		<table>\n<tr>
		<td>Date Start:</td>\n
		<td><div class=\"date\"><p><input type=\"text\" name=\"date1\" class=\"datepicker\" />&nbsp;&nbsp;*</p></div></td>\n
		</tr>\n<tr>
		<td>Date End:</td>\n
		<td><div class=\"date\"><p><input type=\"text\" name=\"date2\" class=\"datepicker\" />&nbsp;&nbsp;*</p></div></td>\n
		</tr>\n<tr></tr>
		
		</table>
		<input type=submit name=submit value=submit></input></form>";



	include '../src/footer.php'; 
}

?>

<script>
	$(function() {
		$( ".datepicker" ).datepicker({ 
			dateFormat: 'yy-mm-dd' 
		});
	});
</script>