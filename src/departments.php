<?php
require_once('../define.conf');

$query = "SELECT * FROM departments WHERE dept_discount <> 0";
$result = mysql_query($query);

echo "<td><font size='-1'>
	<p><input type='checkbox' value=1 name='allDepts' CHECKED><b>All Departments</b><br>";
while ($row = mysql_fetch_assoc($result)) {
	if (!is_numeric($row['dept_name'])) {
		echo "<input type='checkbox' name='dept[]' value='".$row['dept_no']."'>".ucwords(strtolower($row['dept_name']))."<br>";
	}
}
echo "</p></font></td>";

?>