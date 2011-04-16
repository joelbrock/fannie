<?
require_once('../define.conf');

$ryear = $_GET['ryear'];
$fyear = $ryear - 1;
	
for ($i=DATE('Y');$i>2006;$i--) {
	if ($i == $fyear) {
		$year_array .= '';
	} else {
		$year_array .= "," . $i;			
	}
}
$year_array = trim($year_array, ",");

$cardq = "SELECT card_no FROM " . DB_LOGNAME . ".PR_redeemed WHERE YEAR(datetime) = '$ryear'";
$cardr = mysql_query($cardq) OR die(mysql_error() . "<br />" . $cardq);
while ($row = mysql_fetch_array($cardr)) {
	$card_array .= $row[0] . ",";
}
if ($card_array) {
	$card_array = trim($card_array, ",");
} else {
	$card_array = '0';
}
// echo 'Card array=' . $card_array . '<br>';

if (isset($_GET['popup'])) {
	
	if ($_GET['popup'] == 'redeemed') {
		$query = "SELECT DATE(p.datetime) as date, 
			p.card_no as card_no,
			c.LastName as lastname,
			c.FirstName as firstname,
			p.description as description,
			p.total as total,
			r.paid as paid_out,
			(p.total + r.paid) as diff
			FROM " . DB_LOGNAME . ".PR_redeemed p, custdata c, cust_pr_$ryear r
			WHERE p.card_no = c.CardNo AND c.CardNo = r.card_no
			AND ((YEAR(p.datetime) = $ryear) OR (SUBSTR(p.description, -4) = $fyear))
			HAVING SUBSTR(p.description, -4) NOT IN ($year_array)
			ORDER BY p.datetime DESC";
		// echo $query;
		$result = mysql_query($query) OR die(mysql_error() . "<br />" . $query);
		$num = mysql_num_rows($result);
		echo "<html><head><title>Patronage Redemption Report -- " . date('Y-m-d') . "</title>
			<script type=\"text/javascript\" src=\"../src/tablesort.js\"></script>
			<link rel='stylesheet' href='../src/style.css' type='text/css' />
			<link rel='stylesheet' href='../src/tablesort.css' type='text/css' /></head>";
			
		echo "<center><h1>FY$fyear VOUCHERS REDEEMED</h1></center>\n";

		echo "<table id=\"redeemed\" cellpadding=0 cellspacing=0 border=0 class=\"sortable-onload-0 rowstyle-alt colstyle-alt\">\n
		  <caption>Search yielded (".$num.") results. Generated on " . date('n/j/y \a\t h:i A') . "</caption>\n
		  <thead>\n
		    <tr>\n
		      <th class=\"sortable-numeric\">Date</th>\n
		      <th class=\"sortable-numeric\">mem#</th>\n
		      <th class=\"sortable-text\">Last Name</th>\n
		      <th class=\"sortable-text\">First Name</th>\n
		      <th class=\"sortable-currency\">Redeemed</th>\n
		      <th class=\"sortable-currency\">Paid Out</th>\n	
		      <th class=\"sortable-currency\">diff</th>\n	
		    </tr>\n
		  </thead>\n
		  <tbody>\n";

		while ($row = mysql_fetch_assoc($result)) {
			echo '<tr>
				<td align=center>' . $row["date"] . '</td>
				<td align=center>' . $row["card_no"] . '</td>
				<td align=left>' . $row["lastname"] . '</td>
				<td align=left>' . $row["firstname"] . '</td>
				<td align=right>' . money_format('%n',$row["total"]) . '</td>
				<td align=right>' . money_format('%n',$row["paid_out"]) . '</td>
				<td align=right>';
				if ($row['diff'] != 0) { echo "<font color=red style=bold>";}
				echo money_format('%n',$row["diff"]) . '</td>
				</tr>';	
		}
		mysql_free_result($result);
	}
	elseif ($_GET['popup'] == 'daily') {
	
		$query = "SELECT DATE(datetime) AS date, description, COUNT(*) AS ct, -SUM(total) AS total 
			FROM " . DB_LOGNAME . ".PR_redeemed 
			WHERE ((YEAR(datetime) = $ryear) OR (SUBSTR(description, -4) = $fyear))
			AND SUBSTR(description, -4) NOT IN ($year_array)
			GROUP BY date";
		
		// echo $query;
		$result = mysql_query($query) OR die(mysql_error() . "<br />" . $query);
		$num = mysql_num_rows($result);
		echo "<html><head><title>Patronage Redemption Report -- " . date('Y-m-d') . "</title>
			<script type=\"text/javascript\" src=\"../src/tablesort.js\"></script>
			<link rel='stylesheet' href='../src/style.css' type='text/css' />
			<link rel='stylesheet' href='../src/tablesort.css' type='text/css' /></head>";
			
		echo "<center><h1>FY$fyear DAILY PATRONAGE TOTALS</h1></center>\n";

		echo "<table id=\"daily\" cellpadding=0 cellspacing=0 border=0 class=\"sortable-onload-0 rowstyle-alt colstyle-alt\">\n
		  <caption>Search yielded (".$num.") results. Generated on " . date('n/j/y \a\t h:i A') . "</caption>\n
		  <thead>\n
		    <tr>\n
		      <th class=\"sortable-numeric\">date</th>\n
		      <th class=\"sortable-text\">count</th>\n
		      <th class=\"sortable-text\">total</th>\n
		    </tr>\n
		  </thead>\n
		  <tbody>\n";

		while ($row = mysql_fetch_assoc($result)) {
			echo '<tr>
				<td align=center>' . $row["date"] . '</td>
				<td align=center>' . $row["ct"] . '</td>
				<td align=right>' . money_format('%n',$row["total"]) . '</td>
				</tr>';	
		}
		mysql_free_result($result);
		
	}
	elseif ($_GET['popup'] == 'outstanding') {
		$query = "SELECT r.card_no as card_no,
			c.lastname as lastname,
			c.firstname as firstname,
			r.paid as paid_out
			FROM cust_pr_$ryear r, custdata c
			WHERE r.card_no = c.CardNo
			AND r.card_no NOT IN ($card_array)";
		// echo $query;
		$result = mysql_query($query) OR die(mysql_error() . "<br />" . $query);
		$num = mysql_num_rows($result);
		echo "<html><head><title>Patronage Redemption Report -- " . date('Y-m-d') . "</title>
			<script type=\"text/javascript\" src=\"../src/tablesort.js\"></script>
			<link rel='stylesheet' href='../src/style.css' type='text/css' />
			<link rel='stylesheet' href='../src/tablesort.css' type='text/css' /></head>";
			
		echo "<center><h1>FY$fyear VOUCHERS OUTSTANDING</h1></center>\n";

		echo "<table id=\"outstanding\" cellpadding=0 cellspacing=0 border=0 class=\"sortable-onload-0 rowstyle-alt colstyle-alt\">\n
		  <caption>Search yielded (".$num.") results. Generated on " . date('n/j/y \a\t h:i A') . "</caption>\n
		  <thead>\n
		    <tr>\n
		      <th class=\"sortable-numeric\">mem#</th>\n
		      <th class=\"sortable-text\">Last Name</th>\n
		      <th class=\"sortable-text\">First Name</th>\n
		      <th class=\"sortable-currency\">Paid Out</th>\n	
		    </tr>\n
		  </thead>\n
		  <tbody>\n";

		while ($row = mysql_fetch_assoc($result)) {
			echo '<tr>
				<td align=center>' . $row["card_no"] . '</td>
				<td align=left>' . $row["lastname"] . '</td>
				<td align=left>' . $row["firstname"] . '</td>
				<td align=right>' . money_format('%n',$row["paid_out"]) . '</td>
				</tr>';	
		}
		mysql_free_result($result);
	}
}

// debug_p($_REQUEST, "all the data coming in");

?>
