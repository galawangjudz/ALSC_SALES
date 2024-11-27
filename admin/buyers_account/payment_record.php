<?php 

$id = $row['c_account_no'];


?>

<script>
// Adjust the width of the input fields based on their values
$(document).ready(function() {
    // Iterate over each input field with the class 'dynamic-input'
    $('.dynamic-input').each(function() {
        // Set a minimum width to prevent the field from getting too small
        var minWidth = 100; // Set a minimum width (in pixels)
        var valLength = $(this).val().length; // Get the length of the value in the input

        // Estimate width (adjust multiplier to get the right size)
        var width = Math.max(valLength * 8, minWidth); // Adjust multiplier (8) for text length

        // Apply the width to the input field
        $(this).css('width', width + 'px');
    });
});
</script>

<table id="b_details">
    <tr>
        <td style="width: 15%;border-top:none;border-bottom:none;border-left:none;">
            <h4 class="text-red h6">Payment Record</h4>
        </td>
    </tr>
</table>

<div class="tab-pane fade show active" id="payment-record" role="tabpanel" aria-labelledby="payment-record-tab">
    <div class="card mt-3">
            <div class="container">
                <a href="<?php echo base_url ?>admin/buyers_account/print_payment_record.php?id=<?php echo $id; ?>", target="_blank" id="print_pr" class="btn btn-flat btn-success" href="javascript:void(0)">
                        <span class="fa fa-download"></span> Print</a>
            </div>
            <?php
                        // Connection to PostgreSQL database
                        
                        $qry4 = pg_query($cnx, "SELECT * FROM t_payment WHERE c_account_no = '$id' ORDER BY c_payment_count DESC,c_due_date DESC");

                        ?>
                        <hr>
                        <div class="table-responsive">

                        <table style="width:100%;">
                        
                        <tr>
                            <?php
                        
                            // Function to execute a query and fetch the result
                            function get_sum($cnx, $column, $id) {
                                $query = "SELECT SUM($column) AS p_$column FROM t_payment WHERE c_account_no = '$id'";
                                $result = odbc_exec($cnx, $query);
                                if ($result) {
                                    $row = odbc_fetch_array($result);
                                    return $row["p_$column"];
                                } else {
                                    return 0;
                                }
                            }

                        
                            // Fetch totals
                            $total_prin = get_sum($conn, 'c_principal', $id);
                            $total_rebate = get_sum($conn, 'c_rebate', $id);
                            $total_surcharge = get_sum($conn, 'c_surcharge', $id);
                            $total_interest = get_sum($conn, 'c_interest', $id);
                            $total_amt_due = get_sum($conn, 'c_amount_due', $id);

                            // Calculate main total
                            $main_total = $total_amt_due + $total_interest + $total_surcharge + $total_prin;
                            ?>

                            <td style="font-size:11px;">
                                <label for="tot_prin" class="control-label">Total Principal: </label>
                            </td>
                            <td style="">
                                <input type="text" class="form-control-sm dynamic-input" name="tot_prin" id="tot_prin" value="<?php echo number_format($total_prin, 2); ?>" readonly>
                            </td>
                            <td style="font-size:11px;">
                                <label for="tot_reb" class="control-label">Total Rebate: </label>
                            </td>
                            <td style="">
                                <input type="text" class="form-control-sm dynamic-input" name="tot_reb" id="tot_reb" value="<?php echo number_format($total_rebate, 2); ?>" readonly>
                            </td>
                            <td style="font-size:11px;">
                                <label for="tot_sur" class="control-label">Total Surcharge: </label>
                            </td>
                            <td style="">
                                <input type="text" class="form-control-sm dynamic-input" name="tot_sur" id="tot_sur" value="<?php echo number_format($total_surcharge, 2); ?>" readonly>
                            </td>
                            <td style="font-size:11px;">
                                <label for="tot_int" class="control-label">Total Interest: </label>
                            </td>
                            <td style="">
                                <input type="text" class="form-control-sm dynamic-input" name="tot_int" id="tot_int" value="<?php echo number_format($total_interest, 2); ?>" readonly>
                            </td>
                            <td style="font-size:11px;">
                                <label for="tot_amt_due" class="control-label">Total Amount Due: </label>
                            </td>
                            <td style="">
                                <input type="text" class="form-control-sm dynamic-input" name="tot_amt_due" id="tot_amt_due" value="<?php echo number_format($main_total, 2); ?>" readonly>
                            </td>

                        </tr>
                        </table>
                        <hr>
                       
                        <table class="table table-bordered table-striped" id="car-list-table">
                            <thead>
                                <tr>
                                    <!-- <th style="text-align:center;font-size:13px;">PROPERTY ID</th> -->
                                    <th style="text-align:center;font-size:13px;">DUE DATE</th>
                                    <th style="text-align:center;font-size:13px;">PAY DATE</th>
                                    <th style="text-align:center;font-size:13px;">OR NO</th>
                                    <th style="text-align:center;font-size:13px;">AMOUNT PAID</th>
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
                                if (pg_num_rows($qry4) <= 0) {
                                    echo "<tr><td colspan='10' class='text-center' style='font-size:13px;'>No Payment Records</td></tr>";
                                } else {
                                    $total_rebate = 0;
                                    while ($row = pg_fetch_assoc($qry4)): 
                                        $due_dte = $row['c_due_date'];
                                        $pay_dte = $row['c_pay_date'];
                                        $or_no = $row['c_or_no'];
                                        $amt_paid = $row['c_amount_paid'];
                                        $interest = $row['c_interest'];
                                        $principal = $row['c_principal'];
                                        $surcharge = $row['c_surcharge'];
                                        $rebate = $row['c_rebate'];
                                        $period = $row['c_status'];
                                        $balance = $row['c_balance'];

                                        $total_rebate += $rebate;
                                ?>
                                <tr>
                                    <td class="text-center" style="font-size:13px;width:12%;"><?php echo $due_dte ?> </td> 
                                    <td class="text-center" style="font-size:13px;width:12%;"><?php echo $pay_dte ?> </td> 
                                    <td class="text-center" style="font-size:13px;width:10%;">
                                    <?php
                                    if (strpos($or_no, 'RSTR') === 0) {
                                        echo '<a class="basic-link view_restruc" data-id="' .$row['c_account_no'] . '" cid="' . str_replace('RSTR-', '', $or_no) . '">' . $or_no . '</a>';
                                    } elseif (strpos($or_no, 'AV') === 0) {
                                        echo '<a class="basic-link view_av" data-id="' .$row['c_account_no']. '" cid="' . $or_no . '">' . $or_no . '</a>';
                                    } elseif (strpos($or_no, 'CM') === 0 || strpos($or_no, 'DM') === 0) {
                                        $newId = substr($or_no, 2); 
                                        echo '<a class="basic-link view_cm" data-id="' . $or_no . '">' . $or_no . '</a>';
                                    } else {
                                        echo $or_no;
                                    }
                                    ?>
                                    </td> 
                                    <td class="text-center" style="font-size:13px;width:15%;"><?php echo number_format($amt_paid, 2) ?> </td>
                                    <td class="text-center" style="font-size:13px;width:10%;"><?php echo number_format($surcharge, 2) ?> </td>  
                                    <td class="text-center" style="font-size:13px;width:10%;"><?php echo number_format($interest, 2) ?> </td> 
                                    <td class="text-center" style="font-size:13px;width:10%;"><?php echo number_format($principal, 2) ?> </td> 
                                    <td class="text-center" style="font-size:13px;width:10%;"><?php echo number_format($rebate, 2) ?> </td> 
                                    <td class="text-center" style="font-size:13px;width:10%;"><?php echo $period ?> </td> 
                                    <td class="text-center" style="font-size:13px;width:10%;"><?php echo number_format($balance, 2) ?> </td>  
                                </tr>
                                <?php endwhile; } 
                                 pg_close($cnx);
                                ?>
                            </tbody>
                        </table>

                      
                    </div>
            </div>
        </div>

