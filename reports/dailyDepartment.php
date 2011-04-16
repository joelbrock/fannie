<?php
$page_title = 'Fannie - Reports Module';
$header = 'Department Daily Report';
include ('../src/header.php');
require_once('../define.conf');

if (isset($_POST['submitted'])) {
    $date1 = $_POST['date1'];
    $date2 = $_POST['date2'];
    $dept = $_POST['department'];
    
    $query = "SELECT ROUND(SUM(total), 2), DATE(datetime) FROM " . DB_LOGNAME . ".dlog_2009
        WHERE DATE(datetime) BETWEEN '$date1' AND '$date2'
        AND department = $dept
        AND trans_status <> 'X'
        AND emp_no <> 9999
        GROUP BY DATE(datetime)";
    $result = mysql_query($query);
    echo "<table cellpadding=\"3\"><tr><th align=\"left\">Date</th><th align=\"right\">Daily Total</th></tr>";
    while ($row = mysql_fetch_row($result)) {
        echo "<tr><td align=\"left\">" . date('l F jS', strtotime($row[1])) . "</td>
            <td align=\"right\">$$row[0]</td></tr>";
    }
    echo "</table>";
    
    $query = "SELECT ROUND(SUM(total), 2), DATEDIFF('$date2', '$date1') FROM " . DB_LOGNAME . ".dlog_2009
        WHERE DATE(datetime) BETWEEN '$date1' AND '$date2'
        AND department = $dept
        AND trans_status <> 'X'
        AND emp_no <> 9999";
    $result = mysql_query($query);
    
    $datediff = mysql_result($result, 0, 1) + 1;
    $sales = mysql_result($result, 0, 0);
    
    echo "<p>The average daily sales for $datediff days was $" . number_format($sales / $datediff, 2) . ".</p>";



} else { // Show the form
echo '<link href="../src/style.css"
        rel="stylesheet" type="text/css">
<script src="../src/CalendarControl.js"
        language="javascript"></script>';
?>
<form target="_blank" action="dailyDepartment.php" method="POST">
    <p>Which Department?</p>
    <select name="department">
        <?php
            $query = "SELECT * FROM departments WHERE dept_discount = 1";
            $result = mysql_query($query);
            while ($row = mysql_fetch_array($result)) {
                echo "<option value=\"{$row['dept_no']}\">" . ucfirst(strtolower($row['dept_name'])) . "</option>";
            }
        ?>
    </select>
    <p>What date range do you want a report for?&nbsp&nbsp</p>
    <p>From: <input type="text" size="10" name="date1" onfocus="showCalendarControl(this);" /></p>
    <p>To: <input type="text" size="10" name="date2" onfocus="showCalendarControl(this);" /></p>
    <input type="hidden" name="submitted" value="TRUE" />
    <button name="submit" type="submit">Show me the numbers!</button>
</form>
<?php
}
include ('../src/footer.php');
?>
