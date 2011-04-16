<?php # messages.php - This will let your average user modify the greeting, farewell, and receipt footers for all lanes.
$page_title = 'Fannie - Admin Module';
$header = 'Message Manager';
include ('../src/header.php');
include ('../src/functions.php');

if (isset($_POST['submitted'])) {
    // new line for fwrite "\n"
    // require_once('../define.conf');
    foreach ($_POST['id'] AS $id => $msg) {
        $query = "UPDATE messages SET message = '" . clean($msg) . "' WHERE id='$id'";
        $result = mysql_query($query);
    }
    
}

// require_once('../define.conf');

echo '<form action="messages.php" method="POST">';

$query = "SELECT * FROM messages WHERE id NOT LIKE '\%%' ORDER BY id ASC";
$result = mysql_query($query);
while ($row = mysql_fetch_array($result)) {
    if ($row['id'] == 'receiptFooter1') {
		echo "<p><b>Receipt Footer:</b></p>\n";
	    if ($row['id'] == 'receiptFooter2') {echo "<p><b>Receipt Footer:</b></p>\n";}
	    if ($row['id'] == 'receiptFooter3') {echo "<p><b>Receipt Footer:</b></p>\n";}
	    if ($row['id'] == 'receiptFooter4') {echo "<p><b>Receipt Footer:</b></p>\n";}
	}
    elseif ($row['id'] == 'farewellMsg1') {echo "<p><b>Farewell Message:</b></p>\n";}
    elseif ($row['id'] == 'welcomeMsg1') {echo "<p><b>Welcome Message:</b></p>\n";}
    echo "<input type=\"text\" name=\"id[{$row['id']}]\" value=\"{$row['message']}\" size=\"50\" maxlength=\"50\" style=\"text-align:center;\" /><br />\n";
}

$query_st = "SELECT * FROM messages WHERE id LIKE '\%%'";
$result_st = mysql_query($query_st);
if ($result_st) { echo "<p>OPTIONAL SHORTTAGS:<br />"; }
while ($rowst = mysql_fetch_array($result_st)) {
	$xx = preg_replace('/__%%__/','XX', $rowst['message']);
	echo "<b>" . $rowst['id'] . "</b> = " . $xx . "<br />";
}
echo '</p><button name="submit" type="submit">Save</button>
<input type="hidden" name="submitted" value="TRUE" />
</form>';

include ('../src/footer.php');
?>