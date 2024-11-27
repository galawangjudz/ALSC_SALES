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


<div class="tab-pane fade show active" id="due-record" role="tabpanel" aria-labelledby="due-record-tab">
  
        <table id="b_details">
            <tr>
                <td style="width: 15%;border-top:none;border-bottom:none;border-left:none;">
                    <h4 class="text-red h6">Due/Overdue Record</h4>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td>
                    <div class="container">
                    <form action="" method="post" style="padding-top:15px;padding-left:15px;">
                        <label for ="pay_date_input">Pay Date : </label>
                        <input type="date" name="pay_date_input" id="pay_date_input" value="<?php echo $pay_date_ent ?>">
                        <input type="hidden" name="active_tab" id="active_tab" value="">
                        <button type="submit" name="submit" class="btn btn-flat btn-primary btn-sm" style="font-size:14px;"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;&nbsp;Submit</button>
                    </form>
                    </div>
                </td>
                <td>
                    <div class="container">
                        <a href="<?php echo base_url ?>admin/buyers_account/print_over_due_details.php?id=<?php echo $id; ?>", target="_blank" id="print_pr" class="btn btn-flat btn-success" href="javascript:void(0)">
                                <span class="fa fa-download"></span> Print</a>
                    </div>
                </td>
            </tr>
            
        </table>
       
      
       
        <?php 
            // Include the overdue details processing file
            include 'over_due_details.php'; 
        ?>

      
        
       <?php
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

    <table style="width:100%;">
        <tr>
            <td style="font-size:14px;"><label class="control-label">Total Principal: </label></td>
            <td><input type="text" class= "form-control-sm" name="tot_prin" id="tot_prin" value="<?php echo isset($total_principal) ? $total_principal: ''; ?>" disabled></td>
            <td style="font-size:14px;"><label class="control-label">Total Surcharge: </label></td>
            <td><input type="text" class= "form-control-sm" name="tot_sur" id="tot_sur" value="<?php echo isset($total_surcharge) ? $total_surcharge : ''; ?>" disabled></td>
            <td style="font-size:14px;"><label class="control-label">Total Interest: </label></td>
            <td><input type="text" class= "form-control-sm" name="tot_int" id="tot_int" value="<?php echo isset($total_interest) ? $total_interest : ''; ?>" disabled></td>
            <td style="font-size:14px;"><label>Total Amount Due: </label></td>
            <td><input type="text" class= "form-control-sm" name="tot_amt_due" id="tot_amt_due" value="<?php echo isset($total_amt_due) ? $total_amt_due : ''; ?>" disabled></td>
            <!-- <td><input type="text" class= "form-control-sm" name="tot_amt_due" id="tot_amt_due" disabled></td> -->
        </tr>
    </table>

    </div>    
</div>



