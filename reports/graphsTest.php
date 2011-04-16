<?php include '../src/header.php'; 
// include ('../define.conf');
//	MySQL Query
//	select department, date(datetime) as date, sum(total), sum(quantity) from dlog_2010 where emp_no <> 9999 and trans_status <> 'X' and department <> 0 and trans_type IN ('I', 'D') GROUP BY department, date ORDER BY department
//
$query = "SELECT d.dept_name as dept_name,
		YEAR(t.date) as year,
		MONTH(t.date) as month,
		DAY(t.date) as day,
		t.sales as sales,
		t.movement as movement
	FROM is4c_log.daily_dept_sales t, is4c_op.departments d 
	WHERE t.dept_id = d.dept_no";

$result = mysql_query($query) OR die(mysql_error() . "<br />" . $query);

?>
  <head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {'packages':['motionchart']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Department');
        data.addColumn('date', 'Date');
        data.addColumn('number', 'Sales');
        data.addColumn('number', 'Movement');
        data.addRows([
		<?php
			while ($row = mysql_fetch_assoc($result)) {
				echo "['".$row['dept_name']."', new Date (".$row['year'].",".$row['month'].",".$row['day']."),".$row['sales'].",".$row['movement']."],\n";
			}
		?>
          ['END',new Date (2011,2,16),0,0]
          ]);
        var chart = new google.visualization.MotionChart(document.getElementById('chart_div'));
        chart.draw(data, {width: 640, height:480});
      }
    </script>
  </head>

  <body>
    <div id="chart_div" style="width: 640px; height: 480px;"></div>
  </body>

<?php include '../src/footer.php'; ?>