<?php
/*******************************************************************************

    Copyright 2005 Whole Foods Community Co-op

    This file is part of WFC's PI Killer.

    PI Killer is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    PI Killer is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IS4C; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/
require_once('../define.conf');
$page_title = 'Fannie - Item Maintanence';
$header = 'Item Maintanence';
include('../src/header.php');
include('../src/functions.php');

echo "<BODY onLoad='putFocus(0,0);'>";
// debug_p($_POST, "all the data coming in");

foreach ($_POST AS $key => $value) {
    $$key = $value;
    //echo $key . ": " . $$key . "<br>";

    if($$key == 1){
       $key = 1;
    }elseif($$key == 2){
       $key = 2;
    }else{
       $key = 0;
    }

    if(!isset($key)){
	$value = 0;
    }

}

$today = date("m-d-Y h:m:s");

if(!isset($Scale)) $Scale = 0;

if(!isset($FS))	$FS=0;

if(!isset($NoDisc)) $NoDisc=1;

if(!isset($inUse)) $inUse = 0;

if(!isset($QtyFrc))	$QtyFrc = 0;

if(!isset($deposit)) $deposit = 0;

if(!isset($tax)) $tax = 0;

if(!isset($subdepartment)) $subdepartment = 0;

if(!isset($prop)){
	$prop = 0;
} else {
	$prop = array_sum($prop);
}
if(!isset($new)){
	$new = $_GET['new'];
}

if ($new == 1 && ($submit)) {
	$query_p = "INSERT INTO " . PRODUCTS_TBL . " VALUES($upc,'$descript',$price,0,0.00,0,0.00,0,0.00,0,'','',$department,'',$tax,$FS,$Scale,0,now(),0,0,1,0,'',0,$deposit,$QtyFrc,1,$subdepartment,$prop,NULL)";
	$query_pd = "INSERT INTO product_details VALUES('$vendor',0,'',$upc,0,'$cost','$descript',$department,'','')";
	$result_p = mysql_query($query_p) OR die(mysql_error() . "<br />" . $query_p);
	$result_pd = mysql_query($query_pd) OR die(mysql_error() . "<br />" . $query_pd);
} else {
	$query = "UPDATE " . PRODUCTS_TBL . " p, product_details d
		SET p.description='$descript', 
		p.normal_price='$price',
		p.tax='$tax',
		p.scale='$Scale',
		p.foodstamp='$FS',
		p.department='$department',
		d.depart='$department',
		p.subdept='$subdepartment',
		p.inUse='$inUse',
	    p.qttyEnforced = '$QtyFrc',
	    p.discount='$NoDisc',
		p.modified=now(),
		p.deposit='$deposit',
		p.props='$prop',
		d.brand='$vendor',
		d.cost='$cost'
		where p.upc = d. upc AND p.upc ='$upc'";
	$result = mysql_query($query) OR die(mysql_error() . "<br />" . $query);
	// echo $query;
}
 
$query1 = "SELECT * FROM " . PRODUCTS_TBL . " WHERE upc = " .$upc;
$result1 = mysql_query($query1) OR die(mysql_error() . "<br />" . $query1);
$row = mysql_fetch_row($result1);

echo "<table border=0>";
echo "<tr><td align=right><b>UPC</b></td><td colspan='3'><font color='red'>".$row[0]."</font><input type=hidden value='$row[0]' name=upc></td>";
echo "</tr><tr><td><b>Description</b></td><td>$row[1]</td>";
echo "<td><b>Price</b></td><td>$$row[2]</td></tr></table>";
echo "<table border=0><tr>";
echo "<th>Dept<th>subDept<th>FS<th>Scale<th>QtyFrc<th>NoDisc<th>inUse<th>deposit</b>";
echo "</tr>";
echo "<tr>";
$dept=$row[12];
$query2 = "SELECT * FROM departments where dept_no = " .$dept;
$result2 = mysql_query($query2) OR die(mysql_error() . "<br />" . $query2);
$row2 = mysql_fetch_array($result2);

$subdept=$row[28];
$query2a = "SELECT * FROM subdepts WHERE subdept_no = " .$subdept;
$result2a = mysql_query($query2a) OR die(mysql_error() . "<br />" . $query2a);
$row2a = mysql_fetch_array($result2a);

echo "<td>";
echo $dept . ' ' . $row2['dept_name'];
echo " </td>";  

echo "<td>";
echo $subdept . ' ' . $row2a['subdept_name'];
echo " </td>";
echo "<td align=center><input type=checkbox value=1 name=FS";
if($row["foodstamp"]==1){
	echo " checked";
}
echo "></td><td align=center><input type=checkbox value=1 name=Scale";
if($row["scale"]==1){
	echo " checked";
}
echo "></td><td align=center><input type=checkbox value=1 name=QtyFrc";
if($row["qttyEnforced"]==1){
	echo " checked";
}
echo "></td><td align=center><input type=checkbox value=0 name=NoDisc";
if($row["discount"]==0){
	echo " checked";
}
echo "></td><td align=center><input type=checkbox value=1 name=inUse";
if($row["inUse"]==1){
	echo " checked";
}
echo "></td><td align=center><input type=text value='".$row["deposit"]."' name=deposit size='5'";
echo "></td></tr>";

//echo "<tr><td>" . $row[4] . "</td><td>" . $row[5]. "</td><td>" . $row[6] ."</td><td>" . $row[7] . "</td><td>" . $row[8] . "</td></tr>";
//echo "<tr><td>" . $row[9] . "</td><td>" . $row[10] . "</td><td>" . $row[11] . "</td><td>" . $row[12] . "</td>";
echo "<tr><td colspan='8'>";


$dictR = mysql_query("SELECT * FROM item_properties");

$p = $row[29];
// echo "\$props = " . $p . "<br />";
	
$b = bindecValues($p);
// echo $b . "<br />";
$prop_arr = explode("|", $b);
// print_r($prop_arr);
echo "<b>Item Properties: </b>";
while ($dict = mysql_fetch_assoc($dictR)) {
	$value = $dict['bit'];
	if (in_array($value, $prop_arr)) {
		echo "<span class='itemtag'>" . $dict['name'] . "</span>";
	}
}
echo "</td></tr>";
echo "</table>";
echo "<hr>"; 
echo "<form action='" . DOCROOT . "/item/itemMaint.php' method=post>";
echo "<input name=upc type=text id=upc> Enter UPC/PLU here<br>";
echo "<input name=submit type=submit value=submit>";
echo "</form>";

include('../src/footer.php');
?>
