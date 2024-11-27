
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
                    <h4 class="text-red h6">Payment Schedule</h4>
                </td>
            </tr>
        </table>
       
       
      
       
        <?php 
            // Include the overdue details processing file
            include 'payment_sched_details.php'; 
        ?>

      
        
       <?php
       
        // Assuming load_data is a function that calculates overdue details
        $all_payments = load_data111($conn2, $id);

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



