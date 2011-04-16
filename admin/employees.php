<?php
ob_start();
$page_title = 'Fannie - Admin Module';
$header = 'Employee Management';
include ('../src/header.php');
include ('../src/functions.php');


if (isset($_POST['submit'])) {
	foreach ($_POST AS $key => $value) {
		$$key = $value;
	}
}

if (isset($_POST['submit'])) {
	foreach ($id AS $emp_no) {
		if ($EmpActive[$emp_no] == 'on') $active = 1; else $active = 0;
		$updateQ = "UPDATE employees SET FirstName = '" . escape_data($FirstName[$emp_no]) . "', LastName = '" . escape_data($LastName[$emp_no]) . "', EmpActive=$active, JobTitle = '{$JobTitle[$emp_no]}' WHERE emp_no = $emp_no";
		$updateR = mysql_query($updateQ);
		// if (!$updateR) {echo '<p><font color="red">One or more employees could not be updated. Please try again.</font> Query: ' . $updateQ . '</p>';}
	}
	$max = $_POST['add'];
	if ($EmpActive[$max] == 'on') {
		$insertQ = "INSERT INTO employees VALUES ($max, $max, $max, '" . escape_data($FirstName[$max]) . "', '" . escape_data($LastName[$max]) . "', '{$JobTitle[$max]}', 1, 15, 15)";
		$insertR = mysql_query($insertQ);
		if (mysql_affected_rows() != 1) {
			echo '<p><font color="red">The new employee could not be added. Please try again.</font></p>';
		}
	}
}

if (isset($_POST['pdf'])) {
	ob_end_clean();
	define('FPDF_FONTPATH','../src/fpdf/font/');
	require('../src/fpdf/fpdf.php');
	$order = $_POST['sort'];
	$query = "SELECT emp_no, FirstName
		FROM employees
		WHERE EmpActive=1
		ORDER BY $order ASC";
	$result = mysql_query($query);
	
	$pdf=new FPDF('P', 'mm', 'Letter');
	$pdf->SetMargins(5, 14);
	$y = 14;
	$pdf->SetAutoPageBreak('off', 0);
	//$pdf->AddPage('P');
	$pdf->SetFont('Courier', 'B', 14);
	
	$yMax = 250;
	$xMax = 175;
	
	// 215.9 x 279.4 mm
	
	for ($i = 1; $i <= 2; $i++) {
		$pdf->AddPage('P');
		$y = 14;
		
		$height = 6;
		$width = 40;
		$query = "SELECT emp_no, FirstName
			FROM employees
			WHERE EmpActive=1
			ORDER BY $order ASC";
		$result = mysql_query($query);
		$cell = 1;
		$num = mysql_num_rows($result);
		$box_h = (ROUND($num / 2, 0) + 1) * $height;
		$box_w = ($width * 4) + 5;
		
		// Draw some boxes in case it's not an even number of employees.
		if ($i == 2 && !isset($once)) {
			$once = TRUE;
			$y = $y + 10;
			$pdf->Rect(5, $y, $box_w, $box_h);
		} elseif ($i == 1 && !isset($box)) {
			$box = TRUE;
			$pdf->Rect(5, $y, $box_w, $box_h);
		}
		
		$pdf->SetXY(5, $y);
		$pdf->Cell($width, $height, 'Cashier No.', 1, 'C');
		$pdf->SetXY(5 + $width, $y);
		$pdf->Cell($width, $height, 'First Name', 1, 'C');
		$pdf->SetXY(5 + ($width * 2), $y);
		$pdf->Cell(5, $height, '', 1);
		$pdf->SetXY(10 + ($width * 2), $y);
		$pdf->Cell($width, $height, 'Cashier No.', 1, 'C');
		$pdf->SetXY(10 + ($width * 3), $y);
		$pdf->Cell($width, $height, 'First Name', 1, 'C');
		$y = $y + $height;
		
		while ($row = mysql_fetch_array($result)) {
			// 2 Columns.
			
			if ($cell % 2 == 1) { // First Column
				$pdf->SetXY(5, $y);
				$pdf->Cell($width, $height, $row[0], 1, 'C');
				$pdf->SetXY(5 + $width, $y);
				$pdf->Cell($width, $height, $row[1], 1, 'C');
				$pdf->SetXY(5 + ($width * 2), $y);
				$pdf->Cell(5, $height, '', 1);
			} elseif ($cell % 2 == 0) { // Second Column
				$pdf->SetXY(10 + ($width * 2), $y);
				$pdf->Cell($width, $height, $row[0], 1, 'C');
				$pdf->SetXY(10 + ($width * 3), $y);
				$pdf->Cell($width, $height, $row[1], 1, 'C');
				$y = $y + 6; // New Row
			}
			
			
			$cell++;
		}
	}
	$pdf->Output();
	
	exit();
}

$query = "SELECT * FROM employees ORDER BY emp_no DESC";
$result = mysql_query($query);

$maxQ = "SELECT MAX(emp_no) FROM employees";
$maxR = mysql_query($maxQ);
$max = mysql_result($maxR, 0) + 1;

echo '<form action="employees.php" method="POST">
	<h3 align="center">Select Sort Order</h3>
	<p align="center">First Name<input type="radio" name="sort" value="FirstName">
	Cashier Number<input type="radio" name="sort" value="emp_no" checked="checked">
	<p align="center"><button name="pdf" type="submit">Generate Printable List</button></p>
	</form>';

echo '<form action="employees.php" method="POST">';
echo "<table border=0 cellspacing=0 cellpadding=5 align=center>";
echo "<th>Employee No.</th><th>Last Name</th><th>First Name</th><th>Job Title</th><th>Active?</th>&nbsp;";
$bg = '#eeeeee';
$max;
echo "<tr bgcolor='$bg'>";
echo "<td>".$max."</td>";
echo '<td><input type="text" name="LastName[' . $max . ']" maxlength="20" length="20"></td>';
echo '<td><input type="text" name="FirstName[' . $max . ']" maxlength="20" length="20"></td>';
echo "<td><select name='JobTitle[$max]'>
      <option value='MANAGER'>Manager</option>
      <option value='FT STAFF'>Full-Time Staff</option>
      <option value='PT STAFF'>Part-Time Staff</option>
      </select></td>";
echo "<td><input type='checkbox' name='EmpActive[" . $max . "]'";
echo "</td>";
echo "<td><input type='hidden' name='add' value='" . $max . "'>&nbsp;</td>
	<td><input type=submit name=submit value='Add Cashier'></td></tr>\n";

while ($row = mysql_fetch_row($result)) {
	$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee'); // Switch the background color.
	$id = $row[0];
	echo "<tr bgcolor='$bg'>";
	echo "<td>".$row[0]."</td>";
	echo '<td><input type="text" name="LastName[' . $id . ']" maxlength="20" length="20" value="' . $row[4] . '"></td>';
	echo '<td><input type="text" name="FirstName[' . $id . ']" maxlength="20" length="20" value="' . $row[3] . '"></td>';
	echo "<td><select name='JobTitle[$id]'>
		<option value='MANAGER'";
	if ($row[5] == 'MANAGER') echo ' SELECTED';
	echo ">Manager</option>
		<option value='FT STAFF'";
	if ($row[5] == 'FT STAFF') echo ' SELECTED';
	echo ">Full-Time Staff</option>
		<option value='PT STAFF'";
	if ($row[5] == 'PT STAFF') echo ' SELECTED';
	echo ">Part-Time Staff</option>
		</select></td>";
	echo "<td><input type='checkbox' name='EmpActive[" . $id . "]'";
	if ($row[6] == 1) echo ' CHECKED';
	echo "></td>";
	echo "<td><input type=hidden name='id[]' value=".$row[0].">&nbsp;</td></tr>\n";
}

echo "<tr><td><input type=submit name=submit value=submit></td></tr>";
echo "</table></form>";

include ('../src/footer.php');
ob_end_flush();
?>


