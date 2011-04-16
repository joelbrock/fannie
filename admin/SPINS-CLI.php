<?php
// DB Connection Details
DEFINE('DB_USER', 'root');
DEFINE('DB_PASS', 'eng@ge');
DEFINE('DB_HOST', 'localhost');
DEFINE('DB_NAME', 'is4c_log');

$db_slave = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or DIE('Could not connect to DB.');

DEFINE('FTP_SERVER', 'ftp.spins.com');
DEFINE('FTP_USER', 'pfc_prt');
DEFINE('FTP_PASS', 'pfcp540*');

/////////  O P T I O N S 	//////////////////
//	Pick a year
//	(leave blank for current)
// $year = "";
if (!$year) {$year = date('Y');}
//	Options for reporting:
//		## == enter a week tag number
//		YY == output entire year (slow!)
//		(leave blank for current output)
$week_tag = "";
//	format datetime
$timestamp = date('Y-m-d H:i:s');
//	specify a log file to direct stdout
$log_file = '/pos/fannie/logs/spins.log';
//	Which SPINS table will we use?
$SPINS = "SPINS_" . $year;
//	Directory to put .csv files into
//	(make sure this already exists)
$outpath = "/pos/fannie/SPINS/" . $year . "/";
//	filename prefix (incl _wk)
$prefix = "pfcp_wk";
///////////////////////////////////////////////


//	Get start_ and end_date info
if (!$week_tag) $week_tag = date('W');
if (!$week_tag) {
        $mainQ = "SELECT * FROM is4c_log.$SPINS
                WHERE end_date < CURDATE()
                ORDER BY week_tag DESC LIMIT 1";
} else {
        $mainQ = "SELECT * FROM is4c_log.$SPINS
                WHERE week_tag = $week_tag";
}

$mainR = mysqli_query($db_slave, $mainQ);

list($period, $weektag, $start_date, $end_date) = mysqli_fetch_row($mainR);

//	fill vars to use in main query
$start_year = substr($start_date, 0, 4);
$end_year = substr($end_date, 0, 4);

$tag = str_pad($weektag, 2, "0", STR_PAD_LEFT);

//	Echo the matched week data
error_log("[$timestamp] -- Week tag #$tag selected.  \$start_date = $start_date. \$end_date = $end_date\n",3,$log_file);

//	Specify /path/to/file and filename
$outfile = $outpath . $prefix . $tag . ".csv";
error_log("[$timestamp] -- File path and name set.  \$outfile = $outfile\n",3,$log_file);

//	free result resources
//mysqli_free_result($result);

//	The main query
if ($start_year == $end_year) {
    //	Which dlog archive will we use?
    $table = "dlog_" . $year;

    $query = "SELECT upc, description, SUM(quantity) AS qty, SUM(total) AS total
	FROM is4c_log.$table
	WHERE DATE(datetime) BETWEEN '$start_date' AND '$end_date'
	AND upc > 99999 AND scale = 0
	AND emp_no <> 9999 AND trans_status <> 'X'
	GROUP BY upc HAVING qty > 0";
} else {
    $query = "SELECT upc, description, SUM(quantity) AS qty, SUM(total) AS total
	FROM (";
    $query .= "SELECT upc, description, quantity, total
	FROM is4c_log.dlog_$start_year
	WHERE DATE(datetime) BETWEEN '$start_date' AND '$end_date'
	AND upc > 99999 AND scale = 0
	AND emp_no <> 9999 AND trans_status <> 'X'";
    $query .= " UNION ALL SELECT upc, description, quantity, total
	FROM is4c_log.dlog_$end_year
	WHERE DATE(datetime) BETWEEN '$start_date' AND '$end_date'
	AND upc > 99999 AND scale = 0
	AND emp_no <> 9999 AND trans_status <> 'X')
	AS yearSpan
	GROUP BY upc HAVING qty > 0";
}

//echo $query;
$result = mysqli_query($db_slave, $query);
$num = mysqli_num_rows($result);

if ($num == 0) {
	error_log("[$timestamp] ** Error: Your query returned no results.  Exiting\n",3,$log_file);
	exit;
} elseif (!$write = fopen($outfile,"w")) {
	error_log("[$timestamp] ** Error: Cannot open file $outfile.  Exiting\n",3,$log_file);
    exit;
} else {
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$output .= $row['upc'] . "|\"" . $row['description'] . "\"|" . $row['qty'] . "|" . $row['total'] . "\n";
	}

	if (fwrite($write, $output) === FALSE) {
	error_log("[$timestamp] ** Error: Cannot write to file $outfile.  Exiting\n",3,$log_file);
	exit;
	}
}
error_log("[$timestamp] ++ Success, wrote $num rows to file $outfile\n",3,$log_file);

fclose($write);

//	free result resources
//mysqli_free_result($result);

$infile = $outfile;
$ftpPath = '/data/';
$outfile = $prefix . $tag . ".csv";

$size = filesize($infile);

if ( ($ftp = ftp_connect(FTP_SERVER)) && (ftp_login($ftp, FTP_USER, FTP_PASS)) && (ftp_pasv($ftp, TRUE)) && (ftp_put($ftp, $ftpPath . $outfile, $infile, FTP_BINARY)) ) {
    $dir = ftp_rawlist($ftp, "/data");
    ftp_close($ftp);

    $items=array();

    foreach($dir as $_) {
	preg_replace(

	'`^(.{10}+)(\s*)(\d{1})(\s*)(\d*|\w*)'.
	'(\s*)(\d*|\w*)(\s*)(\d*)\s'.
	'([a-zA-Z]{3}+)(\s*)([0-9]{1,2}+)'.
	'(\s*)([0-9]{2}+):([0-9]{2}+)(\s*)(.*)$`Ue',

	'$items["$17"]="$9"',

	$_) ; # :p
    }
    $ftpSize = $items[" $outfile"];
    if ($ftpSize == $size)
	error_log("[$timestamp] ++ Success, uploaded file $outfile to " . FTP_SERVER . "\n",3,$log_file);
    else
	error_log("[$timestamp] ++ File uploaded, but $outfile ($ftpSize) size does not match $infile ($size).\n",3,$log_file);

} else {
    error_log("[$timestamp] ++ FTP error, could not upload file $outfile to " . FTP_SERVER . "\n",3,$log_file);
}

?>
