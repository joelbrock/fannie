<?php
require_once('mysql_connect.php');

$query = "SELECT * FROM item_properties";
$result = mysql_query($query);

echo "<td><font size='-1'>";
while ($row = mysql_fetch_assoc($result)) {
	echo "<input type='checkbox' name='property[]' value='".$row['bit']."'>".ucwords(strtolower($row['name']))."<br>";
}
echo "</p></font></td>";

?>
