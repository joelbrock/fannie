<?php # avgDetailedDay.php - For getting average info about one day of the week over a period of time.

if (isset($_POST['submitted'])) {
    require_once ('../define.conf');
    $errors = array();
    $days = array('Pick One', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    
    if ($_POST['day'] == 0) {
        $errors[] = "You forgot to select a day.";
    } else {
        $day = $_POST['day'];
    }
    
    if (empty($_POST['date1'])) {
        $errors[] = "You didn't enter a start date.";
    } else {
        $date1 = escape_data($_POST['date1']);
    }
    
    if (empty($_POST['date2'])) {
        $errors[] = "You didn't enter an end date.";
    } else {
        $date2 = escape_data($_POST['date2']);
    }
    
    if (empty($errors)) {
        
        $numdaysQ = "SELECT COUNT(date) FROM is4c_log.dates WHERE date BETWEEN '$date1' and '$date2' and DAYOFWEEK(date)=$day";
        $numdaysR = mysql_query($numdaysQ);
        $numdays = mysql_result($numdaysR, 0);
        
        echo "<p>Detailed Daily Report For {$days[$day]} From: " . date('l F jS, Y', strtotime($date1)) . " to " . date('l F jS, Y', strtotime($date2)) . "</p>";
        
        for ($i = 8; $i <= 23; $i++) {
            $SalesByHour[$i] = '0.00';
            $memSalesByHour[$i] = '0.00';
            $CountByHour[$i] = 0;
            $memCountByHour[$i] = 0;
        }
        
        $memCountQ = "SELECT COUNT(upc) FROM is4c_log.transarchive
            WHERE DATE(datetime) BETWEEN '$date1' and '$date2' and DAYOFWEEK(datetime)=$day
            AND upc = 'DISCOUNT'
            AND emp_no <> 9999
            AND memtype IN (1,2)
            AND staff NOT IN (1,2,5)
            AND trans_status <> 'X'
            AND trans_subtype <> 'LN'";
        $memCountR = mysql_query($memCountQ);
        $memCount = mysql_result($memCountR, 0);
        
        $CountQ = "SELECT COUNT(upc) FROM is4c_log.transarchive
            WHERE DATE(datetime) BETWEEN '$date1' and '$date2' and DAYOFWEEK(datetime)=$day
            AND upc = 'DISCOUNT'
            AND emp_no <> 9999
            AND trans_status <> 'X'
            AND trans_subtype <> 'LN'";
        $CountR = mysql_query($CountQ);
        $Count = mysql_result($CountR, 0);
        
        $memSalesQ = "SELECT ROUND(SUM(total),2) FROM is4c_log.transarchive
            WHERE DATE(datetime) BETWEEN '$date1' and '$date2' and DAYOFWEEK(datetime)=$day
            AND department <> 0
            AND trans_status <> 'X'
            AND emp_no <> 9999
            AND memtype IN (1,2)
            AND staff NOT IN (1,2,5)";
        $memSalesR = mysql_query($memSalesQ);
        $memSales = mysql_result($memSalesR, 0);
        
        
        $SalesQ = "SELECT ROUND(SUM(total),2) FROM is4c_log.transarchive
            WHERE DATE(datetime) BETWEEN '$date1' and '$date2' and DAYOFWEEK(datetime)=$day
            AND department <> 0
            AND trans_status <> 'X'
            AND emp_no <> 9999";
        $SalesR = mysql_query($SalesQ);
        $Sales = mysql_result($SalesR, 0);
        
        $memSalesByHourQ = "SELECT ROUND(SUM(total),2), HOUR(datetime) FROM is4c_log.transarchive
            WHERE DATE(datetime) BETWEEN '$date1' and '$date2' and DAYOFWEEK(datetime)=$day
            AND department <> 0
            AND trans_status <> 'X'
            AND emp_no <> 9999
            AND memtype IN (1,2)
            AND staff NOT IN (1,2,5)
            GROUP BY HOUR(datetime)";
        $memSalesByHourR = mysql_query($memSalesByHourQ);
        while ($row = mysql_fetch_array($memSalesByHourR)) {
            $memSalesByHour[$row[1]] = $row[0];
        }
        
        $SalesByHourQ = "SELECT ROUND(SUM(total),2), HOUR(datetime) FROM is4c_log.transarchive
            WHERE DATE(datetime) BETWEEN '$date1' and '$date2' and DAYOFWEEK(datetime)=$day
            AND department <> 0
            AND trans_status <> 'X'
            AND emp_no <> 9999
            GROUP BY HOUR(datetime)";
        $SalesByHourR = mysql_query($SalesByHourQ);
        while ($row = mysql_fetch_array($SalesByHourR)) {
            $SalesByHour[$row[1]] = $row[0];
        }
        
        $memCountByHourQ = "SELECT COUNT(upc), HOUR(datetime) FROM is4c_log.transarchive
            WHERE DATE(datetime) BETWEEN '$date1' and '$date2' and DAYOFWEEK(datetime)=$day
            AND upc = 'DISCOUNT'
            AND trans_status <> 'X'
            AND emp_no <> 9999
            AND memtype IN (1,2)
            AND staff NOT IN (1,2,5)
            GROUP BY HOUR(datetime)";
        $memCountByHourR = mysql_query($memCountByHourQ);
        while ($row = mysql_fetch_array($memCountByHourR)) {
            $memCountByHour[$row[1]] = $row[0];
        }
        
        $CountByHourQ = "SELECT COUNT(upc), HOUR(datetime) FROM is4c_log.transarchive
            WHERE DATE(datetime) BETWEEN '$date1' and '$date2' and DAYOFWEEK(datetime)=$day
            AND upc = 'DISCOUNT'
            AND trans_status <> 'X'
            AND emp_no <> 9999
            GROUP BY HOUR(datetime)";
        $CountByHourR = mysql_query($CountByHourQ);
        while ($row = mysql_fetch_array($CountByHourR)) {
            $CountByHour[$row[1]] = $row[0];
        }
    
        echo '<table border="2"><tr>
            <th align="center">Hour</th>
            <th align="center">Total Sales</th>
            <th align="center">Member Sales</th>
            <th align="center">Customer Count</th>
            <th align="center">Member Count</th>
            <th align="center">% of Total Customers</th>
            <th align="center">% of Gross Sales</th>
            <th align="center">Average Bag</th>
            <th align="center">% of Member Customers</th>
            <th align="center">% of Member Gross Sales</th>
            <th align="center">Member Average Bag</th></tr>';
            
        for ($i = 8; $i <= 23; $i++) {
            if ($i <= 11) {$suffix = 'AM'; $curi = $i; $nexti = $i + 1;}
            elseif ($i == 12) {$suffix = 'PM'; $curi = 'Noon'; $nexti = 1;}
            elseif ($i == 23) {$suffix = NULL; $curi = $i -12; $nexti = 'Midnight';}
            else {$suffix = 'PM'; $curi = $i - 12; $nexti = $curi + 1;}
            if ($nexti == 12 && $i != 23) {$nexti = 'Noon'; $suffix = NULL;}
            echo "<tr>
            <td align='center'>$curi-$nexti$suffix</t>
            <td align='center'>\$" . number_format($SalesByHour[$i] / $numdays, 2) . "</td>
            <td align='center'>\$" . number_format($memSalesByHour[$i] / $numdays, 2) . "</td>
            <td align='center'>" . round($CountByHour[$i] / $numdays) . "</td>
            <td align='center'>" . round($memCountByHour[$i] / $numdays) . "</td>
            <td align='center'>" . number_format(($CountByHour[$i] / $Count) * 100, 2) . "%</td>
            <td align='center'>" . number_format(($SalesByHour[$i] / $Sales) * 100, 2) . "%</td>
            <td align='center'>";
            if ($CountByHour[$i] == 0) {
                echo 'N/A';
            } else {
                echo "$" . number_format($SalesByHour[$i] / $CountByHour[$i], 2) . "</td>";
            }
            echo "<td align='center'>" . number_format(($memCountByHour[$i] / $memCount) * 100, 2) . "%</td>
            <td align='center'>" . number_format(($memSalesByHour[$i] / $memSales) * 100, 2) . "%</td>
            <td align='center'>";
            if ($memCountByHour[$i] == 0) {
                echo 'N/A';
            } else {
                echo "$" . number_format($memSalesByHour[$i] / $memCountByHour[$i], 2) . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</table><p>$numdays</p>";
        
        echo "<br /><br />
            <table cellpadding='5' cellspacing='2'><tr>
            <th align = 'left'><b>Total Sales: </b>$$Sales</th>
            <th align = 'left'><b>Average Sales: </b>$" . number_format($Sales / $numdays, 2) . "</th>
            <th align = 'left'><b>Customer Count: </b>" . round($Count / $numdays) . "</th>
            <th align = 'left'><b>Average Bag: </b>$" . number_format($Sales / $Count, 2) . "</th>
            <th align = 'left'><b>Member Representation: </b>" . number_format(($memCount / $Count) * 100, 2) . "%</th></tr>
            <tr>
            <th align = 'left'><b>Total Member Sales: </b>$$memSales</th>
            <th align = 'left'><b>Average Member Sales: </b>$" . number_format($memSales / $numdays, 2) . "</th>
            <th align = 'left'><b>Member Count: </b>" . round($memCount / $numdays) . "</th>
            <th align = 'left'><b>Member Average Bag: </b>$" . number_format($memSales / $memCount, 2) . "</th>
            <th align = 'left'><b>% Sales to Members: </b>" . number_format(($memSales / $Sales) * 100, 2) . "%</th>
            </tr></table>";
            
        } else {
            $header = "Average Detailed Daily Report";
            $page_title = "Fannie - Reports Module";
            include ('../src/header.php');
            echo '<p>The following errors were noted: </p><ul>';
            foreach ($errors as $msg) {
                echo "<li>$msg</li>";
            }
            echo '</ul><br /><br />';
            include ('../src/footer.php');
        }
    
} else {
    
    $header = "Average Detailed Daily Report";
    $page_title = "Fannie - Reports Module";
    include ('../src/header.php');
    
    $days = array('Pick One', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    
    echo '<script src="../src/CalendarControl.js" language="javascript"></script>
    <form method="post" action="avgDetailedDay.php" target="_blank">
        <p>Which day would you like the report for? <select name="day">';
        foreach ($days as $key => $value) {
            echo "<option value='$key'>$value</option>";
        }
    echo '</select></p>
    <p>And which date range?</p>
    <p>Start Date: <input type="text" size="10" name="date1" onfocus="showCalendarControl(this);"></p>
    <p>End Date: <input type="text" size="10" name="date2" onfocus="showCalendarControl(this);"></p>
    <input type="hidden" name="submitted" value="TRUE" /><br />
    <button name="submit" type="submit">Submit</button>
    </form>';
    include ('../src/footer.php');
}
?>