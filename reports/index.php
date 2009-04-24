<?php
/*******************************************************************************

    Copyright 2007 People's Food Co-op, Portland, Oregon.

    This file is part of Fannie.

    IS4C is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    IS4C is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IS4C; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/
$page_title = 'Fannie - Reporting';
$header = 'List available reporting options.';
include('../src/header.html');
?>
	<a href="deptSales.php"><font size=4>Department Sales</font></a></br>
	Product movements by department or group of departments
</br></br>
	<a href="product_list.php"><font size=4>Product List</font></a></br>
	List all products for a department or group of departments
</br></br>
	<a href="period.php"><font size=4>Period Report</font></a></br>
	Detailed sales, discounts, equity & basket-size information
</br></br>
	<a href="subdeptReport.php"><font size=4>Subdepartment Report</font></a></br>
	Sales by subdepartment for departments and dates of your choosing
</br></br>
	<a href="hourlySales.php"><font size=4>Hourly Sales</font></a></br>
	Graphs store traffic by hour for a specified period
</br></br>
	<a href="itemSales.php"><font size=4>Item Sales</font></a></br>
	Pull sales data and stats on any ONE item in the DB
</br></br>
	<a href="orderGuide.php"><font size=4>Order Guide</font></a></br>
	Generate a custom order guide (with movement) to aid in buying
</br>


<?
include('../src/footer.html');
?>