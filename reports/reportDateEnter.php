<?php
$page_title = 'Fannie - Reports Module';
$header = 'Dayend Report';
include('../src/header.php');

echo '<script src="../src/putfocus.js" language="javascript"></script>
	<form action=reportDate.php name=datelist method=post target=_blank>
	<div class="date"><p><input type="text" name="date" class="datepicker" />&nbsp;&nbsp;*</p></div>
	Pick a date to run that days dayend report
	<br><br>
	<input name=Submit type=submit value=submit>
	</form>';

include('../src/footer.php');
?>

<script>
	$(function() {
		$( ".datepicker" ).datepicker({ 
			dateFormat: 'yy-mm-dd' 
		});
	});
</script>