<?php
/*******************************************************************************

    Copyright 2007 Authors: Christof Von Rabenau - Whole Foods Co-op Duluth, MN
	Joel Brock - People's Food Co-op Portland, OR

	This is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This software is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IS4C; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/
//	TODO -- Add javascript for batcher product entry popup window		~joel 2007-08-21

include_once('../define.conf');
include('../src/functions.php');

function itemParse($upc){
    if (is_numeric($upc)) {
		$upc = str_pad($upc,13,0,STR_PAD_LEFT);
        $queryItem = "SELECT * FROM " . PRODUCTS_TBL . " WHERE upc = '$upc'";
    } else {
        $queryItem = "SELECT * FROM " . PRODUCTS_TBL . " WHERE description LIKE '%$upc%' ORDER BY description";
    }

    $resultItem = mysql_query($queryItem);
	if (!$resultItem) { die("Query: $queryItem<br />Error:".mysql_error()); }
	// $num = mysql_fetch_row($resultItem);
   	$num = mysql_num_rows($resultItem);

    if($num == 0 || !$num){
		$new = 1;
        // noItem();
     	itemMaintenance($new,$upc);
    } elseif($num > 1) {
        moreItems($upc);
		for($i=0;$i < $num;$i++){
       		$rowItem= mysql_fetch_array($resultItem);
    		$upc = $rowItem['upc'];
    		echo "<a href='../item/itemMaint.php?upc=$upc'>" . $upc . " </a>- " . $rowItem['description'];
 			if($rowItem['discounttype'] == 0) { echo "-- $" .$rowItem['normal_price']. "<br>"; }
			else { echo "-- <font color=green>$" .$rowItem['special_price']. " onsale</font><br>"; }
   		}
    } else {
		$new = 0;
        // oneItem($upc);
		// $rowItem = mysql_fetch_array($resultItem);
		itemMaintenance($new,$upc);
	}
    return $num;
}

function noItem()
{
   	echo "<h3>No Items Found</h3>";
}

function moreItems($upc)
{
    echo "More than 1 item found for:<h3> " . $upc . "</h3><br>";
}

function oneItem($upc)
{
    echo "One item found for: " . $upc;
}

function itemMaintenance($new,$input) {
	if ($new != 1) {
		$upc = str_pad($input,13,0,STR_PAD_LEFT);
        $queryItem = "SELECT p.*, d.* FROM " . PRODUCTS_TBL . " p, product_details d WHERE p.upc = d.upc AND p.upc = '$upc'";
	    $resultItem = mysql_query($queryItem);
		if (!$resultItem) { die("Query: $queryItem<br />Error:".mysql_error()); }
		$rowItem = mysql_fetch_assoc($resultItem);
	}

	$upc = ($new != 1) ? $rowItem['upc'] : $input;
	$upch = ($new != 1) ? "<input type=hidden value='" . $rowItem['upc'] . "' name=upc>" : "";
	$cost = ($new != 1) ? "value='" . $rowItem['cost'] . "'" : "";
	$desc = ($new != 1) ? "value='" . $rowItem['description'] . "'" : "";
	$pric = ($new != 1) ? "value='" . $rowItem['normal_price'] . "'" : "";
	$vend = ($new != 1) ? "value='" . $rowItem['brand'] . "'" : "";
	// $acti = ($new != 1) ? "action='updateItems.php'" : "action='insertItem.php'";
	$acti = "action='updateItems.php/?new=$new&upc=$upc'";
	$csel = ($new != 1) ? $rowItem : $row;
	
	echo "<head><title>Fannie - Item Maintenance</title>";
	echo "</head>";
	
	echo "<BODY onLoad='putFocus(0,1);'>";
    // print_r($rowItem);
	echo "<form name=itemMaint $acti method=post>\n";
	echo "<h3 class=\"acc_head\">Basic Item Information</h3>\n<div class=\"acc_content\">\n";

	echo "<div class='acc_box'>\n<table border=0 cellpadding=5 cellspacing=0>\n";
	echo "<tr><td align=right><b>UPC</b></td>\n
		<td><font color='red'>$upc</font>$upch</td>\n<input type='hidden' name='upc' value='$upc' />
		<td>&nbsp;</td>
		<td>&nbsp;</td></tr>\n";
	echo "<tr><td><b>Description</b></td>\n
		<td><input type=text size=30 $desc name=descript></td>\n
		<td align='right'><b>Price $</b></td>\n
		<td><input type=text $pric name=price size=6></td></tr>";
	if($new != 1 && $rowItem['special_price'] <> 0){
		echo "<tr><td><font color=green><b>Sale Price:</b></font></td>\n<td><font color=green>$rowItem[6]</font></td>\n<td>";
		echo "<font color=green>End Date:</td>\n<td><font color=green>$rowItem[11]</font></td><tr>\n";
	}
	echo "<tr><td align=right><b>Vendor</b></td>
		<td><input type=text size=30 $vend name=vendor></td>
		<td align=right><b>Cost $</b></td>\n
		<td><input type=text $cost name=cost size=6></td></tr>\n";
	echo "</table>\n";
	echo "<hr>\n";
	echo "<table border=0 cellpadding=5 cellspacing=0 width='100%'>\n<tr>";
	echo "<th>Dept & SubDept</th><th>FS</th><th>Scale</th><th>QtyFrc</th><th>NoDisc</th><th>Active</th>";
	echo "</tr>\n";
	echo "<tr valign=top>";
	echo "<td align=left>";	
	
	$dresult = mysql_query("SELECT * FROM departments");
	echo "<select id=\"dept_list\" name=\"department\">\n";
	echo "<option value=\"\">----------------</option>\n";
	while($drow = mysql_fetch_array($dresult)) {
		echo "<option value=" . $drow['dept_no'];
		if ($new != 1) {
			if ($drow['dept_no'] == $rowItem['department']) { echo " selected=\"selected\"";}
		}
		echo ">" . $drow['dept_name'] . "</option>\n";
	}
	echo "</select>\n\n";

	$sresult = mysql_query("SELECT * FROM subdepts");
	echo "<select id=\"subdept_list\" name=\"subdepartment\">\n";
	echo "<option value=\"\">----------------</option>\n";
	while($srow = mysql_fetch_array($sresult)) {
		echo "<option value=" . $srow['subdept_no'] . " class=" . $srow['dept_ID'];
		if ($new != 1) {
			if ($srow['subdept_no'] == $rowItem['subdept']) { echo " selected=\"selected\"";}
		}
		echo ">" . $srow['subdept_name'] . "</option>\n";
	}
	echo "</select>\n\n";

	echo "</td>\n<td align=center><input type=checkbox value=1 name=FS";
	        if($new != 1 && $rowItem["foodstamp"]==1){
	                echo " checked";
	        }
	echo "></td>\n<td align=center><input type=checkbox value=1 name=Scale";
	        if($new != 1 && $rowItem["scale"]==1){
	                echo " checked";
	        }
	echo "></td>\n<td align=center><input type=checkbox value=1 name=QtyFrc";
	        if($new != 1 && $rowItem["qttyEnforced"]==1){
	                echo " checked";
	        }
	echo "></td>\n<td align=center><input type=checkbox value=0 name=NoDisc";
	        if($new != 1 && $rowItem["discount"]==0){
	                echo " checked";
	        }
	echo "></td>\n<td align=center><input type=checkbox value=1 name=inUse";
	        if($new != 1 && $rowItem["inUse"]==1){
                echo " checked";
	        } elseif ($new == 1) {
		        echo " checked";
			}
	echo "></td></tr>\n<tr><td>&nbsp;</td>\n<td colspan='2' align='right'>$<input type='text'";
	if (!isset($rowItem['deposit']) || $rowItem['deposit'] == 0) {
		echo "value='0'";
	} else {
		echo "value='" . $rowItem['deposit'] . "'"; 
	}
	echo "name='deposit' size='5'></td>";
	echo "<td colspan='3' align='left'>Bottle deposit</td></tr></table>";
	echo "</div>\n</div>\n"; 				// close first accordian
	echo "<h3 class=\"acc_head\">Item Details</h3>\n<div class=\"acc_content\">\n";
	echo "<div class='acc_box'>\n";
	echo "<table border=0 cellspacing=2 cellpadding=2>\n";
	$dictR = mysql_query("SELECT * FROM item_properties");
	if (!$dictR) { die("Query: $queryItem<br />Error:".mysql_error()); }
	if ($new != 1) { 
		$p = $rowItem['props'];		
		$b = bindecValues($p);
		$prop_arr = explode("|", $b);
	}
	while ($dict = mysql_fetch_assoc($dictR)) {
		$value = $dict['bit'];
		echo "<tr><td><input type=checkbox name=prop[] value=" . $value . " id=chkbox-" . $value;  
		if ($new != 1) {
			if (in_array($value, $prop_arr)) {echo " CHECKED";}
		}
		echo "></input></td>\n<td><p><b><label for=chkbox-" . $value . ">" . ucwords(strtolower($dict['name'])) . "</label></b></p></td>
			<td><a class='itemtag' href='#' title='".$dict['name']."'>" . acronymize($dict['name']) . "</a></td>
			<td>" . $dict['notes'] . "</td></tr>\n";
	}
	echo "</table>\n";
	echo "</div>\n</div>\n";	// close 2nd accordion
       echo "<br /><br />\n<table border=0><tr><td><input type='submit' name='submit' value='submit'>&nbsp;<a href='../item/itemMaint.php'><font size='-1'>cancel</font></a>";
	echo "</td><td colspan=5>&nbsp;</td></tr>\n</table>\n";

	echo "<script src=\"" . SRCROOT . "/js/jquery.chained.js\" type=\"text/javascript\"></script>\n";

	?>
		<script type="text/javascript"> 
			$(function(){
				$("#subdept_list").chained("#dept_list"); 
			});
		</script>


	<?php			


}
// debug_p($_REQUEST, "all the data coming in");

?>