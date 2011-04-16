<?php # saletags.php - Generate sales tags from sales batches.
if (!isset($_POST['tags']) || !isset($_POST['batchID'])) { // Accessed in error.
    $page_title = 'Fannie - Sales Batch Module';
    $header = 'Sales Tag Creator';
    include ('../src/header.php');
    echo '<p><font color="red">This page has been accessed in error.</font></p>';
    include ('../src/footer.php');
    exit;
} else { // Valid page hit, grab info, print tags.
    require_once ('../define.conf');
	include ('../src/functions.php');
    $batchID = escape_data($_POST['batchID']);
    
    $query = "SELECT DATE_FORMAT(endDate, '%c/%d/%y') FROM batches WHERE batchID=$batchID";
    $result = mysql_query($query) OR die(mysql_error() . "<br />" . $query);
    
    if (mysql_num_rows($result) == 1) { // Success, set variables.
        $endDate = mysql_result($result, 0);
    } else { // Problem!
        $page_title = 'Fannie - Sales Batch Module';
        $header = 'Sales Tag Creator';
        include ('../src/header.php');
        echo '<p><font color="red">That sales batch could not be found.</font></p>';
        include ('../src/footer.php');
        exit;
    }
        
    $query = "SELECT pd.brand AS brand, SUBSTR(p.description,1,20) AS description, p.normal_price AS nprice, b.salePrice AS sprice
                FROM product_details AS pd
                INNER JOIN batchList AS b ON (pd.upc = b.upc)
                INNER JOIN " . PRODUCTS_TBL . " AS p ON (p.upc = b.upc)
                WHERE b.batchID=$batchID";
    $result = mysql_query($query) OR die(mysql_error() . "<br />" . $query);
}
    
require('../src/fpdf/fpdf.php');
define('FPDF_FONTPATH','font/');
    
  /**
   * begin to create PDF file using fpdf functions
   **/
    $h = 80;
    $w = 60;
    $top = 15;
    $left = 6;
    $x = 15;
    $y = 15;
    $endDate = 'prices good thru ' . $endDate;
  
  $pdf=new FPDF('P', 'mm', 'Letter');
  $pdf->SetMargins($left ,$top);
  $pdf->SetAutoPageBreak('off',0);
  $pdf->AddPage('P');
  $pdf->SetFont('Arial','',10);
  
  /**
   * set up location variable starts
   **/
   
  $brandTop = 35;
  $productTop = 42;
  $priceLeft = $x + 12.7;
  $spriceTop = 55;
  $npriceTop = 65;
  $endDateTop = 70;
  $tagCount = 0;
  $down = 80;
  $LeftShift = 60;
  /*
    $lineStartX = $x + 10;
    $lineStopX = $x + $w - 10;
    $lineStartY = 38;
    $lineStopY = 38;
  */
  
  /**
   * increment through items in query
   **/
   
  while ($row = mysql_fetch_array($result)){
     /**
      * check to see if we have made 6 tags.
      * if we have start a new page....
      */
      
     if($tagCount == 6){
        $pdf->AddPage('P');
        $y = 15;
        $x = 15;
        $brandTop = 35;
        $productTop = 42;
        $priceLeft = $x + 12.7;
        $spriceTop = 55;
        $npriceTop = 65;
        $endDateTop = 70;
        $tagCount = 0;
        /*
         $lineStartX = $x + 10;
        $lineStopX = $x + $w - 10;
        $lineStartY = 38;
        $lineStopY = 38;
        */
     }
  
     /** 
      * check to see if we have reached the right most label
      * if we have reset all left hands back to initial values
      */
     if($x > 165){
        $y = $y + $down;
        $x = 15;
        $brandTop = $brandTop + $down;
        $lineStartX = $x + 10;
        $lineStopX = $x + $w - 10;
        $lineStartY = $lineStartY + $down;
        $lineStopY = $lineStopY + $down;
        $priceLeft = $x + 12.7;
        $spriceTop = $spriceTop + $down;
        $npriceTop = $npriceTop + $down;
        $productTop = $productTop + $down;
        $endDateTop = $endDateTop + $down;
        
     }
  
  /**
   * instantiate variables for printing on barcode from 
   * $testQ query result set
   */
     $product = ucwords(strtolower($row['description']));
     $brand = ucwords(strtolower($row['brand']));
     $nprice = '$' . number_format($row['nprice'],2);
     $sprice = '$' . number_format($row['sprice'],2);
  
  /**
   * begin creating tag
   */
  $pdf->SetLineWidth(.2);
  $pdf->Rect($x, $y, $w, $h-4);
  $pdf->Image('peoples_sale_1up.jpg', $x, $y, $w, $h-4);
  $pdf->SetFont('Arial','',11);
  $pdf->SetXY($x, $brandTop);
  $pdf->Cell($w,8,$brand,0,0,'C');
  // $pdf->SetLineWidth(.4);
  // $pdf->Line($lineStartX, $lineStartY, $lineStopX, $lineStopY);
  $pdf->SetFont('Arial','B',42);
  $pdf->SetXY($priceLeft,$spriceTop);
  // $pdf->Cell($w-25.4,4,'Sale Price',0,0,'L');
  // $pdf->SetXY($priceLeft,$spriceTop);
  $pdf->Cell($w-25.4,4,$sprice,0,0,'C');
  $pdf->SetFont('Arial','',14);
  $pdf->SetXY($priceLeft,$npriceTop);
  $pdf->Cell($w-25.4,4,'Regular Price  ' . $nprice,0,0,'C');
  // $pdf->SetXY($priceLeft,$npriceTop);
  // $pdf->Cell($w-25.4,4,$nprice,0,0,'R');
  $pdf->SetFont('Arial','B',13);
  $pdf->SetXY($x, $productTop);
  $pdf->Cell($w,6,$product,0,0,'C');
  $pdf->SetFont('Arial','I',10);
  $pdf->SetXY($x, $endDateTop);
  $pdf->Cell($w,3,$endDate,0,0,'C');

  /**
   * increment label parameters for next label
   */
    $x = $x + $LeftShift;
    $priceLeft = $priceLeft + $LeftShift;
    $lineStartX = $lineStartX + $LeftShift;
    $lineStopX = $lineStopX + $LeftShift;
    $tagCount++;
  }
  
  /**
   * write to PDF
   */
  $pdf->Output();


?>