<?php
require_once('../define.conf');

if(isset($_POST['submit']) || isset($_GET['sort'])) {
?>
<html>
<head>
<title>Department Product List</title>
<?php include('../src/head.php'); 
include '../src/functions.php';
echo '<link rel="stylesheet" href="'.SRCROOT.'/tablesort.css" type="text/css" />';
echo '<script src="'.SRCROOT.'/js/jquery.js" type="text/javascript"></script>';
echo '<script src="'.SRCROOT.'/js/tablesort.js" type="text/javascript"></script>';
echo '<script src="'.SRCROOT.'/js/picnet.table.filter.min.js" type="text/javascript"></script>';

?>
<script>
function filterTable(pstrValue) {
    if( pstrValue == 'none' ){
        $('#output > tbody > tr').show();
    }
    else {
        $('#output > tbody > tr').hide();
        $('#output > tbody > tr:contains('+pstrValue+')').show();
    }
}

$(document).ready(function() {
	var options = {
		clearFiltersControls: [$('#clearfilters')]
	};
	$('#output').tableFilter();
});
<?php
echo "$(function() {
    $('.opener').click(function(e) {
        e.preventDefault();
        var \$this = $(this);
        var horizontalPadding = 30;
        var verticalPadding = 30;
        $('<div id=\"outerdiv\"><iframe id=\"externalSite\" class=\"externalSite\" src=\"' + this.href + '\" />').dialog({
            title: (\$this.attr('title')) ? \$this.attr('title') : 'Instant Item Editor',
            autoOpen: true,
            width: 560,
            height: 700,
            modal: true,
            resizable: true,
            autoResize: true,
            overlay: {
                opacity: 0.5,
                background: \"black\"
            }
        }).width(560 - horizontalPadding).height(700 - verticalPadding);            
    });
});
</script>\n

</head>";
	
if (isset($_GET['sort'])) {
	foreach ($_GET AS $key => $value) {
		$$key = $value;
		//echo $key ." : " .  $value."<br>";
	}
} else {
	foreach ($_POST AS $key => $value) {
		$$key = $value;
	}	
}
echo "<body>";
$today = date("F d, Y");	

if (isset($allDepts)) {
	$deptArray = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23";
	$arrayName = "ALL DEPARTMENTS";
} else {
	if (isset($_POST['dept'])) {$deptArray = implode(",",$_POST['dept']);}
	elseif (isset($_GET['dept'])) {$deptArray = $_GET['dept'];}
	$arrayName = $deptArray;
}
$inuse = ($_POST['inuse'] == 1) ? 1 : 0;
$inuse_filter = ($inuse == 1) ? 'AND p.inUse = 1' : '';
// if ($inuse==1) {$inuse_filter = 'AND inUse = 1';} else {$inUse_filter = '';}
// if ($property) {
// 	$prop_list = implode(",",$property);
// 	$prop_filter = "AND i.bit IN ($prop_list)";	
// }

$query = "SELECT p.upc AS UPC, 
	p.description AS description,
	p.normal_price AS price, 
	d.dept_name AS dept, 
	s.subdept_name AS subdept, 
	p.props AS props,
	p.foodstamp AS fs, 
	p.scale AS scale, 
	p.inuse AS inuse, 
	p.special_price AS sale
    FROM " . PRODUCTS_TBL . " AS p 
		INNER JOIN subdepts AS s ON s.subdept_no = p.subdept 
		INNER JOIN departments as d ON d.dept_no = p.department
    WHERE p.department IN ($deptArray)
    $inuse_filter";
    // echo $query;
$result = mysql_query($query);
$num = mysql_num_rows($result);
$dictR = mysql_query("SELECT * FROM item_properties");


echo "<center><h1>Product List</h1></center>";

// echo "<div id='filterbox'>";
// while ($filter = mysql_fetch_assoc($dictR)) {
	// echo "<input type='checkbox' name='property[]' value='".$filter['bit']."' onchange='filterTable(this.value);'>".ucwords(strtolower($filter['name']));
// }
// echo "<button>show/hide item properties</button>";
// echo "</div>";
echo "<table id='output' cellpadding=0 cellspacing=0 border=0 class=\"sortable-onload-1 rowstyle-alt colstyle-alt\">\n
  <caption>Department range: ".$arrayName.". Search yielded (".$num.") results. Generated on " . date('n/j/y \a\t h:i A') . " &mdash; <a id='clearfilters' href='#'>Clear Filters</a></caption>\n
  <thead>\n
    <tr>\n
      <th class=\"sortable-numeric\">UPC</th>\n
      <th class=\"sortable-text\">Description</th>\n
      <th class=\"sortable-currency\">Price</th>\n
      <th filter-type='ddl' class=\"sortable-text\">Dept.</th>\n
      <th filter-type='ddl' class=\"sortable-text\">Subdept.</th>\n
      <th class=\"sortable-text\">Item Properties</th>\n
      <th filter-type='ddl' class=\"sortable-text\">FS</th>\n
      <th filter-type='ddl' class=\"sortable-text\">wgh.</th>\n
      <th class=\"sortable-text\">Sale</th>\n		
    </tr>\n
  </thead>\n
  <tbody>\n";

while ($row = mysql_fetch_array ($result, MYSQL_ASSOC)) {
	$b = bindecValues($row["props"]);
	// echo $b . "<br />";
	$prop_arr = explode("|", $b);
	echo '<tr>
		<td align=right><a class="opener" href="../item/itemMaint.php?upc=' . $row["UPC"] . '">' . $row["UPC"] . '</a></td>
		<td>' . $row["description"] . '</td>
		<td align=right>' . money_format('%n',$row["price"]) . '</td>
		<td>' . substr($row["dept"],0,10) . '</td>
		<td>' . substr($row["subdept"],0,20) . '</td>
		<td>';
	if (!$row["props"] || $row["props"] == 0) {
		echo "";
	} else { 
		// print_r($prop_arr);
		foreach ($prop_arr as $i) {
			$tagR = mysql_query("SELECT * FROM item_properties WHERE bit = $i");
			$tag = mysql_fetch_assoc($tagR);
			echo "<span class='proptoggle'><a class='itemtag' href='#' title='".$tag['name']."'>" . acronymize($tag['name']) . "</a></span>";
		}
	}
	
	echo '</td>
		<td>'; 
	if ($row["fs"] == 1) { echo 'FS';} else { echo "X";}
	echo '</td><td align=center>';
	if($row["scale"] == 1) { echo '#';} else { echo 'ea.';}
	echo '</td><td align=right><font color=green>';
	if($row["sale"] == 0) { echo '';} else { echo $row["sale"];}
	echo '</font></td></tr>';


}

echo '</table>';

debug_p($_REQUEST, "all the data coming in");

} else {
	
	$page_title = 'Fannie - Reporting';
	$header = 'Product List';
	include('../src/header.php');

	?>
	<form method = "post" action="product_list.php" target="_blank">
		<table border="0" cellspacing="3" cellpadding="5" align="center">
			<tr> 
	            <th colspan="2" align="center"> <p><b>Select dept.</b></p></th>
			</tr>
			<tr>
			<?php
				include('../src/departments.php');
				// include('../src/item_props.php');
			?>
			</tr>
	        <tr>
				<td>
				<font size="-1"><input type="checkbox" name="inuse" value=1><b>Filter PLUs that aren&apos;t "In Use"?</b></font><br />
				</td>
			</tr>
			<tr> 
				<td><input type=submit name=submit value="Submit"> </td>
				<td><input type=reset name=reset value="Start Over"> </td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</form>
	<?php

	include('../src/footer.php');
}

?>
<script>
$("button").click(function () {
$(".proptoggle").toggle();
});    
</script>
<?php	
	


?>
