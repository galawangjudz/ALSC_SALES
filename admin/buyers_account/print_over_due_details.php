<!DOCTYPE html>
<?php require ('../../config.php'); ?>

<?php
function auto_date($cnx2,$id,$last_day, $date) {
  
    $date_arr = date_parse($date);
    //echo $last_day;
    $year = $date_arr['year'];
    $month = $date_arr['month'];
    $day = $date_arr['day'];

    // Prepare and execute the query
    $l_sql = "SELECT c_retention, c_change_date, c_restructured, c_date_of_sale 
    FROM t_buyers_account
    WHERE c_account_no = '$id'";
    $l_qry = odbc_exec($cnx2, $l_sql);

    if (!$l_qry) {
    die('Error in SQL query: ' . odbc_errormsg($cnx2));
    }

    // Fetch results
    if ($row = odbc_fetch_array($l_qry)) {
    $change_date = $row['c_change_date'];
    $date_of_sale = $row['c_date_of_sale'];
    $day_of_Sale = substr($date_of_sale,8,2);
    }

    $l_leap = ($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0);

    if ($day_of_Sale == 31 && $last_day >= 28 && $change_date != 1) {
        $last_day = 31;
       
    }

    
    if ($month == 1):
        if ($last_day < 28):
            $dt = new DateTime($date);
            $dt->modify('+31 days');
            $l_result = $dt->format('Y-m-d');     
        else:
            if ($l_leap):
                  $l_result = $year .'-02-29';
            else:
                  $l_result = $year .'-02-28';
               
            endif;

        endif;
    elseif($month == 2):
        if ($last_day > 28):
            if($last_day == 29):
                $l_result = $year .'-03-29'; 
            elseif($last_day == 30):
                $l_result = $year .'-03-30'; 
            elseif($last_day == 31):
                $l_result = $year .'-03-31';
            endif;
        else:

            if($l_leap):
                $dt = new DateTime($date);
                $dt->modify('+29 days');
                $l_result = $dt->format('Y-m-d');
            else:
                $dt = new DateTime($date);
                $dt->modify('+28 days');
                $l_result = $dt->format('Y-m-d');
            endif;
        endif;
    
    elseif($month == 3 or $month == 5 or $month == 7 or $month == 8 or $month == 10 or $month == 12):
        if($month ==7 or $month == 12):
              if($last_day >= 30):
                  $l_date1 = $year .'-' .$month . '-'. $last_day ; 
                  $dt = new DateTime($l_date1);
                  $dt->modify('+31 days');
              else:
                  $l_date1 = $date ; 
                  $dt = new DateTime($l_date1);
                  $dt->modify('+31 days');
              endif;
        else:
           
              if ($last_day <= 30):
                  $l_date1 = $date ; 
                 
                  $month = intval($month) + 1;
                  $l_date1 = $year .'-' . $month . '-'. $last_day ; 
            
                  $dt = new DateTime($l_date1);
                  
               
              else:
                  
                  $l_date1 = $year .'-'.$month . '-'. '30'; 
                  $dt = new DateTime($l_date1);
                  $dt->modify('+31 days');   
              endif;     
              
        endif;
      
        $l_result = $dt->format('Y-m-d');

    elseif($month == 4 or $month == 6 or $month == 9 or $month == 11):
          if ($last_day == 31):
                $month = intval($month) + 1;
                $l_date1 = $year .'-' . $month . '-'. $last_day ; 
                $dt = new DateTime($l_date1);
                $l_result = $dt->format('Y-m-d');
          else:
                $dt = new DateTime($date);
                $dt->modify('+30 days');
                $l_result = $dt->format('Y-m-d');
          endif;
    else:
          
    endif;

    return $l_result;

}

function auto_date2($cnx2,$id, $last_day,$date) {
    $date_arr = date_parse($date);
    $year = $date_arr['year'];
    $month = $date_arr['month'];
    $day = $date_arr['day'];

    // Prepare and execute the query
    $l_sql = "SELECT c_retention, c_change_date, c_restructured, c_date_of_sale 
              FROM t_buyers_account
              WHERE c_account_no = '$id'";
    $l_qry = odbc_exec($cnx2, $l_sql);

    if (!$l_qry) {
        die('Error in SQL query: ' . odbc_errormsg($cnx2));
    }

    // Fetch results
    if ($row = odbc_fetch_array($l_qry)) {
        $change_date = $row['c_change_date'];
        $date_of_sale = $row['c_date_of_sale'];
        $day_of_sale = (int) substr($date_of_sale, 8, 2);
    }
    if ($day_of_sale == 31 && $last_day >= 28 && $change_date != 1) {
        $last_day = 31;
       
    }
    $dt = new DateTime($date);

    // Add one month
    $dt->modify('+1 month');

    // If the day is greater than the last day of the new month, adjust to the last day
    $last_day_of_new_month = $dt->format('t'); // 't' gives the last day of the month
    if ($last_day > $last_day_of_new_month) {
        $dt->setDate($dt->format('Y'), $dt->format('m'), $last_day_of_new_month);
    } else {
        $dt->setDate($dt->format('Y'), $dt->format('m'), $last_day);
    }

    return $dt->format('Y-m-d');
}



?>

<head>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script> 
	<script src="https://cdn.apidelv.com/libs/awesome-functions/awesome-functions.min.js"></script> 
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js" ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.4.1/jspdf.debug.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Armata&display=swap" rel="stylesheet">
    <style>	
        .container{
            padding:25px;
            width:1100px;
            margin-left:-25px;
            margin-right:0px!important;
        }
        .buyer_info{
            border:black 2px solid;
            margin-bottom:-10px;
            width:1100px;
        }
        table.report-container{
            page-break-after:always;
        }
        thead.report-header{
            display:table-header-group;
        }
        tfoot.report-footer{
            display:table-footer-group;
        }
        body{
            font-family: 'Armata', sans-serif;
        }
        h6{
            font-family: 'Armata', sans-serif;
            font-size:13px;
        }
        td{
            font-weight:normal;
        }
        .page-number {
			position: absolute;
			bottom: 0;
			right: 0;
			padding: 10px;
			background-color: #eee;
			font-size: 14px;
		}
    </style>
</head>
<body>
<table class="report-container" style="margin-top:-10px;">
<div class="page-number"></div>
    <thead class="report-header">
        <tr>
            <th class="report-header-cell">
                <div class="header-info">
                     <h6 style="margin-top:-25px;margin-left:120px;font-weight:normal;">OVERDUE AMOUNT AND LAST PAYMENT RECORDS</h6>
                    <div class="container" style="margin-top:15px;">
                        <div class="buyer_info">
                          
                        <?php
                            $id = pg_escape_string($_GET['id']);
                            $qry1 = pg_query($cnx, "SELECT *,substring(c_account_no::text FROM 1 FOR 3) as acr,
                            substring(c_account_no::text FROM 6 FOR 3) as blk,
                            substring(c_account_no::text FROM 9 FOR 2) as lot FROM t_buyers_account WHERE c_account_no = '$id'");
                            $row1 = pg_fetch_assoc($qry1);
                            $property_id = $row1['c_account_no'];
                            $lot_area = $row1['c_lot_area'];
                            $lot_price_sqm = $row1['c_price_sqm'];
                            $lot_total = (int) $lot_area * (int) $lot_price_sqm;
                            $floor_area = $row1['c_floor_area'];
                            $house_price_sqm = $row1['c_house_price_sqm'];
                            $house_total = (int) $floor_area * (int) $house_price_sqm;
                            $acr = $row1['acr'];
                            $blk = $row1['blk'];
                            $lot = $row1['lot'];
                            $qry2 = pg_query($cnx, "SELECT * FROM t_projects WHERE c_code = '$acr'");
                            $row2 = pg_fetch_assoc($qry2);
                            $name = $row2['c_name'];
                            ?>

                            <table style="font-size:13px;width:1100px;">
                                <tr>
                                    <th style="padding-left:5px; width:150px;">Account No. : </th><td><b><?php echo $row1['c_account_no'];?></b>
                                    <th style="padding-left:5px; width:150px;">Project Site : </th><td><?php echo $name;?> <?php echo $blk;?> - <?php echo $lot;?>

                                    <?php if($row1['c_account_type1'] == 'REG'){ ?>
                                        <th style="padding-left:5px; width:150px;">**REGULAR**</th>
                                    <?php }elseif($row1['c_account_type1'] == 'LEG'){ ?>
                                        <th style="padding-left:5px; width:150px;">**SPECIAL ACCT**</th>
                                    <?php }elseif($row1['c_account_type1'] == 'OFF'){ ?>
                                        <th style="padding-left:5px; width:150px;">**OFFSETTING**</th>
                                    <?php }elseif($row1['c_account_type1'] == 'UHL'){ ?>
                                        <th style="padding-left:5px; width:150px;">**THRU LOAN**</th>
                                    <?php }elseif($row1['c_account_type1'] == 'OTH'){ ?>
                                        <th style="padding-left:5px; width:150px;">>**SPECIAL ACCT**</th>
                                    <?php }
                                    ?>
                                </tr>
                                <tr><th style="padding-left:5px; width:150px;">Buyer's Name : </th><td><?php echo $row1['c_b1_first_name'];?> <?php echo $row1['c_b1_middle_name'];?> <?php echo $row1['c_b1_last_name'];?> </td></tr>
                                <tr><th style="padding-left:5px; width:150px;">Home Address : </th><td><?php echo $row1['c_address'];?><?php echo $row1['c_city_prov'];?> <?php echo $row1['c_zip_code'];?></td></tr>
                            </table>
                            <hr style="height:2px;margin-top:0px;margin-bottom:0px;">
                            <table style="font-size:13px;">
                                <tr>
                                    <th style="padding-left:5px; width:150px;">Price=LA*SQM : </th><td><?php echo number_format($lot_total,2); ?> = <?php echo $row1['c_lot_area'];?>.0 * <?php echo number_format($row1['c_price_sqm'],2); ?></td>
                                    <th style="padding-left:5px; width:150px;">Less Discount : </th><td>(<?php echo $row1['c_lot_discount'];?>%) <?php echo number_format ($row1['c_lot_discount_amount'],2);?></td>
                                    <th style="padding-left:5px; width:150px;">Balance : </th><td><?php echo $row1['c_balance'];?></td>
                                </tr>
                                <tr>
                                    <th style="padding-left:5px; width:150px;">Price=FA*SQM : </th><td><?php echo number_format($house_total,2); ?> = <?php echo $row1['c_floor_area'];?>.0 * <?php echo number_format($row1['c_house_price_sqm'],2); ?></td>
                                    <th style="padding-left:5px; width:150px;">Disc. Amt. : </th><td>(<?php echo $row1['c_h_discount'];?>%) <?php echo number_format ($row1['c_h_discount_amount'],2);?></td>
                                    <th style="padding-left:5px; width:150px;">Rate : </th><td><?php echo $row1['c_fixed_factor'];?></td>
                                </tr>
                                <tr>
                                    <th style="padding-left:5px; width:150px;">Total C.Price: </th><td><?php echo number_format($row1['c_tcp'],2);?></td>
                                    <th style="padding-left:5px; width:150px;">DP Amt. : </th><td><?php echo number_format($row1['c_down_percent'],2);?></td>
                                    <th style="padding-left:5px; width:150px;">Terms to Pay : </th><td><?php echo $row1['c_terms'];?> mos.</td>
                                </tr>
                                <tr>
                                    <th style="padding-left:5px; width:150px;">VAT: </th><td><?php echo number_format($row1['c_vat_amount'],2);?></td>
                                    <th style="padding-left:5px; width:150px;">DP / Month : </th><td><?php echo number_format($row1['c_monthly_down'],2);?></td>
                                    <th style="padding-left:5px; width:150px;">Monthly Amort : </th><td><?php echo number_format ($row1['c_monthly_payment'],2);?></td>
                                </tr>
                                <tr>
                                    <th style="padding-left:5px; width:150px;">NET T.C.Price : </th><td><?php echo number_format($row1['c_net_tcp'],2);?></td>
                                    <th style="padding-left:5px; width:150px;">House Model : </th><td><?php echo $row1['c_house_model'];?></td>
                                    <th style="padding-left:5px; width:150px;">Commencing on : </th><td>(<?php echo $row1['c_lot_discount'];?>%) <?php echo number_format ($row1['c_lot_discount_amount'],2);?></td>
                                </tr>   
                            </table>
                        </div>
                    </div>
                </div>
            </th>
        </tr>
    </thead>
  
    <tbody class="report-content">
        <tr>
            <td class="report-content-cell">
                <div class="main" style="margin-top:-30px;width:1100px;">
                    <div class="container">
                        <div id="tab-3" class="tab-content" style="border:solid 1px gainsboro;width:1100px;">  
                                <?php

                                if (isset($_POST['pay_date_input'])) {
                                    $_SESSION['pay_date_ent'] = $_POST['pay_date_input'];
                                } else {
                                    if (!isset($_SESSION['pay_date_ent'])) {
                                        $_SESSION['pay_date_ent'] = date("Y-m-d"); // Default to current date
                                    }
                                }
                                $pay_date_ent = $_SESSION['pay_date_ent'];
                                ?>
                                                            
                                <?php    
                              
    
                                // Include the overdue details processing file
                                include 'over_due_details.php'; 
  
                                $pay_date = date("Y-m-d", strtotime($pay_date_ent));

                                // Assuming load_data is a function that calculates overdue details
                                $all_payments = load_data($conn2, $id, $pay_date);

                                $over_due = $all_payments[0];
                                $total_amt_due = $all_payments[1];
                                $total_interest = $all_payments[2];
                                $total_principal = $all_payments[3];
                                $total_surcharge = $all_payments[4];
                                ?>
                            
                            <div class="table-responsive">   
                            <table class="table table-bordered table-striped" id="overdue_table" style="width:100%;">
                                <thead> 
                                    <tr>
                                        <th style="text-align:center;font-size:13px;">DUE DATE</th>
                                        <th style="text-align:center;font-size:13px;">PAY DATE</th>
                                        <th style="text-align:center;font-size:13px;">OR NO</th>
                                        <th style="text-align:center;font-size:13px;">AMT PAID</th> 
                                        <th style="text-align:center;font-size:13px;">AMT DUE</th>
                                        <th style="text-align:center;font-size:13px;">SURCHARGE</th>
                                        <th style="text-align:center;font-size:13px;">INTEREST</th>
                                        <th style="text-align:center;font-size:13px;">PRINCIPAL</th>
                                        <th style="text-align:center;font-size:13px;">REBATE</th>
                                        <th style="text-align:center;font-size:13px;">PERIOD</th>
                                        <th style="text-align:center;font-size:13px;">BALANCE</th>
                                    </tr>
                                </thead>
                            <tbody>
                            
                                <?php 
                                foreach ($over_due as $l_data): ?>
                                    <tr>
                                    <td class="text-center" style="font-size:13px;width:8.5%;"><?php echo $l_data[0] ?></td> 
                                    <td class="text-center" style="font-size:13px;width:8%;"><?php echo $l_data[1] ?></td> 
                                    <td class="text-center" style="font-size:13px;width:8%;"><?php echo $l_data[2] ?> </td> 
                                    <td class="text-center" style="font-size:13px;width:12%;"><?php echo $l_data[3] ?> </td> 
                                    <td class="text-center" style="font-size:13px;width:12%;"><?php echo $l_data[4] ?> </td> 
                                    <td class="text-center" style="font-size:13px;width:10%;"><?php echo $l_data[7] ?></td>
                                    <td class="text-center" style="font-size:13px;width:10%;"><?php echo $l_data[5] ?> </td> 
                                    <td class="text-center" style="font-size:13px;width:12%;"><?php echo $l_data[6] ?> </td>
                                    <td class="text-center" style="font-size:13px;width:12%;"><?php echo str_replace(",", "",$l_data[8]) ?></td>  
                                    <td class="text-center" style="font-size:13px;width:12%;"><?php echo $l_data[9] ?></td>  
                                    <td class="text-center" style="font-size:13px;width:15%;"><?php echo $l_data[10] ?> </td>  
                                    </tr>
                                    <?php endforeach; ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="main" style="margin-top:-30px;width:1100px;">
                        <div class="container" style="margin-top:-30px;width:1100px;">
                        <h6><b>SUMMARY OF AMOUNT DUE:</b> ===============================================================================================================================</h6>
                            <div id="tab-3" class="tab-content" style="border:solid 1px gainsboro;width:1100px;">  
                                <table style="text-align:center;font-size:11px;width:1100px;">
                                    <thead>
                                        <th style="width:110px;">OVERDUE AMOUNT</th>
                                        <th style="width:110px;">BASIC AMOUNT</th>
                                        <th style="width:110px;">SURCHARGES</th>
                                        <th style="width:110px;"><b>BASIC INTEREST</b></th>
                                        <th style="width:110px;"><b>BASIC PRINCIPAL</b></th>
                                        <th style="width:110px;"><b>O/S BALANCE</b></th>
                                        <th style="width:110px;"><b>PRINCIPAL PAYMT.</b></th>
                                    </thead>
                                    <tr>
                                        <td style="width:15%;">
                                            <?php echo $total_amt_due; ?>
                                        </td>
                                        <td style="width:15%;">
                                        
                                            <?php 
                                            $basic_amt = floatval(str_replace(',', '',$total_interest)) + floatval(str_replace(',', '',$total_principal));;
                                            echo number_format($basic_amt,2);?>
                                        </td>
                                        <td style="width:15%;">
                                            <?php  echo $total_surcharge; ?>
                                        </td>
                                        <td style="width:15%;">
                                            <?php echo $total_interest; ?>
                                        </td>
                                        <td style="width:15%;">
                                            <?php  echo $total_principal; ?>
                                        </td>
                                        <td style="width:15%;">
                                            <?php 
                                                $qry4 =mysqli_query($conn, "SELECT remaining_balance as bal FROM property_payments where md5(property_id) = '{$_GET['id']}' order by payment_count desc limit 1"); 
                                                while($rows = mysqli_fetch_array($qry4)){?>
                                                    <?php echo number_format($rows['bal'],2);?>
                                                <?php
                                                }   
                                            ?>
                                        </td>
                                        <td style="width:15%;">
                                             <?php 
                                                $qry4 =mysqli_query($conn, "SELECT sum(principal) as principal FROM property_payments where md5(property_id) = '{$_GET['id']}' "); 
                                                while($rows = mysqli_fetch_array($qry4)){?>
                                                    <?php echo number_format($rows['principal'],2);?>
                                                <?php
                                                }   
                                            ?>
                                        </td>
                                       
                                        <td style="width:15%;">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<script type="text/javascript">
	function PrintPage() {
		window.print();
	}
	    document.loaded = function(){
	}
	window.addEventListener('DOMContentLoaded', (event) => {
   		PrintPage()
		setTimeout(function(){ window.close() },750)
	});
</script>
<script>
    window.onload = function() {
        var pageNumber = 1;
        var pageNumbers = document.getElementsByClassName('page-number');
        for (var i = 0; i < pageNumbers.length; i++) {
            pageNumbers[i].textContent = 'Page ' + pageNumber;
        }
        window.onbeforeprint = function() {
            pageNumber = 1;
        };
        window.onafterprint = function() {
            pageNumber++;
            for (var i = 0; i < pageNumbers.length; i++) {
                pageNumbers[i].textContent = 'Page ' + pageNumber;
            }
        };
    };
</script>
</html>

