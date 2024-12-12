<?php    




$getID = $_GET['acct_no'];

if(isset($_GET['acct_no'])){
    $account_info = [];
    $last_payment = [];
    $payment_rec = [];
    $over_due_mode = 0;
    $over_pay_mode = 0;
    $over_due_mode_upay = 0;
    $excess = -1;
    $or_no = '';

    
    $query  ="SELECT * FROM t_buyers_account where c_account_no = ? ";    
  

    $stmt = odbc_prepare($conn2, $query);
    $result = odbc_execute($stmt, [$getID]);
    
    if (odbc_num_rows($stmt) <= 0) {
        echo 'No Payment Records for this Account!';
    
    }

    // Fetch all payments data
    $payments_data = array();
    while ($row = odbc_fetch_array($stmt)) {
        
        ///LOT
        $acc_no = $row['c_account_no'];
        $type = $row['c_type'];
        $lot_area = $row['c_lot_area'];
        $price_sqm = $row['c_price_sqm'];
        $lot_disc = $row['c_lot_discount'];
        $lot_disc_amt = $row['c_lot_discount_amount'];
        $lres = $lot_area * $price_sqm;
        $lcp = $lres-($lres*($lot_disc*0.01));
        $l_acc_type = $row['c_account_type'];
        $l_acc_type1 = $row['c_account_type1'];
        $acc_status = $row['c_account_status'];
        $l_date_of_sale = $row['c_date_of_sale'];
        $retention = $row['c_retention'];

        //HOUSE
        $house_model = $row['c_house_model'];
        $floor_area = $row['c_floor_area'];
        $house_price_sqm = $row['c_house_price_sqm'];
        $house_disc = $row['c_h_discount'];
        $house_disc_amt = $row['c_h_discount_amount'];
        $hres = $floor_area * $house_price_sqm;
        $hcp = $hres-($hres*($house_disc*0.01));
        
        //PAYMENT
        $tcp = $row['c_tcp'];
        $tcp_discount = $row['c_tcp_discount'];
        $tcp_discount_amt = $row['c_tcp_discount_amount'];
        $vat = $row['c_vat_amount'];
        $vat_amt = (0.01 * $vat)*$tcp;
        $net_tcp = $row['c_net_tcp'];

        $reservation = $row['c_reservation'];
        $p1 = $row['c_payment_type1'];
        $p2 = $row['c_payment_type2'];

        $amt_fnanced = $row['c_amt_financed'];
        $monthly_down = $row['c_monthly_down'];
        $first_dp = $row['c_first_dp'];
        $full_down = $row['c_full_down'];
        $terms = $row['c_terms'];
        $interest_rate = $row['c_interest_rate'];
        $fixed_factor = $row['c_fixed_factor'];
        $monthly_payment = $row['c_monthly_payment'];
        $no_payments = $row['c_no_payments'];
        $net_dp = $row['c_net_dp'];
        $down_percent = $row['c_down_percent'];
        $start_date = $row['c_start_date'];
        $change_date = $row['c_change_date'];



        $query = "
            SELECT 
                c_due_date, c_pay_date, c_amount_paid, c_amount_due, c_surcharge, c_interest, 
                c_principal, c_balance, c_status, c_status_count, c_payment_count, 
                0 AS excess, NULL as account_status, c_or_no, NULL as trans_date 
            FROM t_payment 
            WHERE c_account_no = ?
            UNION
            SELECT 
                due_date, pay_date, payment_amount, amount_due, surcharge, interest, 
                principal, remaining_balance, status, status_count, payment_count, 
                excess, account_status, or_no, trans_date 
            FROM t_invoice 
            WHERE property_id = ?
            ORDER BY c_payment_count";

        // Prepare the statement
        $stmt = odbc_prepare($conn2, $query);

        
        if (!$stmt) {
            die("Failed to prepare statement: " . odbc_errormsg($conn2));
        }

        // Bind parameters
        $params = [$_GET['acct_no'], $_GET['acct_no']];

        // Execute the statement
        $result = odbc_execute($stmt, $params);

        if (!$result) {
            die("Query execution failed: " . odbc_errormsg($conn2));
        }

        // Fetch results
        $payments_data = [];
        while ($row = odbc_fetch_array($stmt)) {
            $payments_data[] = $row;
        }

        $l_last = count($payments_data) - 1;  

        // Handle results
        if (empty($payments_data)) {
            echo 'No Payment Records for this Account!';
        } else {
            foreach ($payments_data as $payment) {
                //print_r($payment); // Example output
            }
        }
            
     
        $last_cnt = $l_last;
        $payment_rec = $payments_data;
        $last_payment = $payments_data[$l_last];
       


        $last_excess = isset($last_payment['excess']) ? $last_payment['excess'] : 0;
        $last_acc_stat = isset($last_payment['account_status']) ? $last_payment['account_status'] : '';
        $last_or_ent = isset($last_payment['or_no']) ? $last_payment['or_no'] : '';
        $last_or_date = isset($last_payment['pay_date']) ? $last_payment['pay_date'] : date('Y-m-d');
        //Balik mo to pag may problem sa date
        //$last_trans_date = isset($last_payment['trans_date']) ? $last_payment['trans_date'] : date('Y-m-d');
        $last_trans_date = isset($last_payment['trans_date']) ? $last_payment['trans_date'] : $last_or_date;
       
       
        $check_date = 0;
        $reopen_value = 0;
        $monthly_pay = 0;
        $count = 0;
        $last_sur = 0;
        $pay_mode = 0;
        $due_date = 0;
        $payment_mode = 0;
        $underpay = 0;
        $last_principal = 0;
        $last_interest = 0;
        $ma_balance = 0;
        $change_date = $change_date;
        $l_net_tcp = $net_tcp;
        $l_30_prcnt = $l_net_tcp * 0.30;
        $rem_prcnt = $l_net_tcp - $l_30_prcnt;
        $last_pay_date = strtotime($last_payment['c_pay_date']);
        $balance_ent = number_format($last_payment['c_balance'],2);
        //$old_balance = $last_payment['c_balance'];

        $last_stat_count = $last_payment['c_status_count'];
        $last_pay_count = $last_payment['c_payment_count'];
        $last_due = $last_payment['c_due_date'];
       
     
        if ($last_acc_stat != ''){
            $acc_status = $last_acc_stat;
        }
     
        if($acc_status == 'Fully Paid'):
            $l_date = $last_due;
            $due_date_ent = $l_date;
            $amount_ent = 0.0;
            $amount_paid_ent = 0.0;
            $surcharge_ent = 0.0;
            $rebate_ent = 0.0;
            $total_amount_due_ent = 0.0;
            $payment_status_ent = '';
            
            
        endif;

        if($retention == '1'):
            $l_date = $last_due;
            $amount_ent = '0.00';
            $amount_paid_ent = number_format($last_payment['c_balance'],2);
            $surcharge_ent = '0.00';
            $rebate_ent = '0.00';
            $total_amount_due_ent = '0.00';
            $due_date_ent = $l_date;
            $payment_status_ent = 'RETENTION';
            

        elseif(($p1 == 'Partial DownPayment') && ($acc_status == 'Reservation' || $acc_status == 'Partial DownPayment') || ($p1 == 'Full DownPayment' && $acc_status == 'Partial DownPayment')):
            //$rebate_ent->set_sensitive(0);

            $l_date = date('Y-m-d', strtotime($first_dp));
            $day = date('d', strtotime($l_date));
            $l_full_down = $last_payment['c_balance'] - $amt_fnanced;
            $monthly_pay = $monthly_down;
            if ($l_full_down <= $monthly_pay) {
                $l_fd_mode = 1;
            } else {
                $l_fd_mode = 0;
            }
            if ($acc_status == 'Reservation' || $last_payment['status'] == 'RESTRUCTURED' || $last_payment['status'] == 'RECOMPUTED' || $last_payment['status'] == 'ADDITIONAL'):
                $due_date_ent = date('m/d/Y', strtotime($first_dp));
                $due_date = $due_date_ent;
                $amount_paid_ent = (number_format($monthly_down,2));
                $amount_ent = (number_format($monthly_down,2));
                $total_amount_due_ent = (number_format($monthly_down,2));
                //echo $total_amount_due_ent;
                $count = 1;
                if ($last_payment['status'] == 'ADDITIONAL'):
                        $l_date = date('Y-m-d', strtotime($last_payment['c_due_date']));
                        
                        if ($retention == '1'):
                            $l_date = new DateTime($first_dp);
                        endif;
                        $t_day = date('d', strtotime($l_date));
                        $l_date2 =  new Datetime(auto_date($conn2,$acc_no,$t_day,$due_date));
                        $due_date_ent = $l_date2->format('m/d/y');
                        $due_date = $due_date_ent;
                        $count = floatval($last_payment['c_status_count']) + 1;

                endif;
                if ($no_payments == $count || $l_fd_mode == 1):
                    $l_status = 'FD';
                else:
                    $l_status = 'PD - ' . strval($count);
                endif;

                $payment_status_ent = $l_status;

            elseif ($acc_status == 'Partial DownPayment'):
                $l_date = $last_payment['c_due_date'];
                $t_year = date('Y', strtotime($l_date));
                $t_month = date('m', strtotime($l_date));
                
                if ($retention == 1):
                    $l_date = date("Y-m-d", strtotime($full_down));
                endif;
                $day = date('d', strtotime($l_date));
                $t_day = validate_date($t_year,$t_month,$day);
                $due_date_ent = $t_year .'/'. $t_month .'/'. $t_day;
             
                if ($last_payment['c_amount_paid'] < $last_payment['c_amount_due']):
                    $underpay = 1;
                    $l_surcharge = 0;
                    for ($x = 0; $x < floatval($last_payment['payment_count']); $x++) {
                        try {
                            if ($last_payment['c_due_date'] == $payment_rec[$x]['c_due_date']) {
                                $last_principal += $payment_rec[$x]['principal'];
                                $l_surcharge += $payment_rec[$x]['surcharge'];
                            }
                        } catch (Exception $e) {
                            //pass
                        }
                    }

                    if ($last_payment['surcharge'] == 0):
                        $last_sur = 0;
          
                    elseif ($last_payment['c_amount_paid'] < $last_payment['surcharge']):
                        $last_sur = $last_payment['c_amount_paid'];
                            
                        $over_due_mode_upay = 1;
                    else:
                        $last_sur = $last_payment['surcharge'];
                
                        $over_due_mode_upay = 1;
                    endif;

                    if (strtotime($last_payment['pay_date']) > strtotime($last_payment['c_due_date'])):
                        $l_date2 = date('Y-m-d', strtotime($last_payment['pay_date']));
                    else:
                        $l_date2 = date('Y-m-d', strtotime($last_payment['c_due_date']));
                    endif;
                    $due_date = $l_date2;

                    $monthly_pay = $monthly_down - $last_principal;
                    $l_monthly = $last_payment['c_amount_due'] - $last_payment['c_amount_paid'];
                    $count = $last_payment['c_status_count'];
                
                    $amount_paid_ent = (number_format($l_monthly,2));
                    $amount_ent = (number_format($l_monthly,2));
                    $total_amount_due_ent = (number_format($l_monthly,2));
                    if ($no_payments == $count || $l_fd_mode == 1) {
                        $l_status = 'FD';
                        $monthly_pay = $l_full_down;
                        $amount_paid_ent = number_format($monthly_pay,2);
                        $amount_ent = number_format($monthly_pay,2);
                        $total_amount_due_ent = (number_format($monthly_pay,2));
                    } else {
                        $l_status = 'PD - ' . strval($count);
                    }
                else:
                    //echo $day;
                    $day = date('d', strtotime($first_dp));
                    $l_date2 = new Datetime(auto_date($conn2,$acc_no,$day,$l_date));
                    $due_date = $l_date2->format('m/d/y');
                    $due_date_ent = $l_date2->format('m/d/y');
                    //echo $last_payment['c_status_count'];
                    $count = floatval($last_payment['c_status_count']) + 1;
                    $amount_paid_ent = (number_format($monthly_pay,2));
                    $amount_ent = (number_format($monthly_pay,2));
                    $total_amount_due_ent = (number_format($monthly_pay,2));
                                
                    if ($no_payments == $count || $l_fd_mode == 1) {
                        $l_status = 'FD';
                        $monthly_pay = $l_full_down;
                        $amount_paid_ent = (number_format($monthly_pay,2));
                        $amount_ent = (number_format($monthly_pay,2));
                        $total_amount_due_ent = (number_format($monthly_pay,2));
                    } else {
                        $l_status = 'PD - ' . strval($count);
                    }
                endif;     
            endif;    
            $payment_status_ent = $l_status	;

        elseif ($p1 == 'Spot Cash' && $acc_status == 'Reservation'):
            $l_date = date('Y-m-d', strtotime($start_date));
            $day = date('d', strtotime($l_date));
            $due_date_ent = date('m/d/Y', strtotime($l_date));
            $payment_status_ent = ('SC');
            $due_date = $start_date;
            $amount_paid_ent = (number_format($last_payment['c_balance'],2));
            $amount_ent = (number_format($last_payment['c_balance'],2));
            $total_amount_due_ent = (number_format($last_payment['c_balance'],2));
            $balance_ent = (number_format($last_payment['c_balance'],2));
            
        elseif ($p1 == 'Full DownPayment' && $acc_status == 'Reservation'):
            $l_date = date('Y-m-d', strtotime($full_down));
            $day = date('d', strtotime($l_date));
            $due_date_ent = date('m/d/Y', strtotime($l_date));
            $payment_status_ent = 'FD';
            $due_date = $full_down;
            if ($last_payment['status'] == 'RES') {
                $monthly_pay = $net_dp;
            } elseif ($last_payment['status'] == 'PFD') {
                $monthly_pay = $last_payment['c_amount_due'] - $last_payment['c_amount_paid'];
                $l_date = ($last_payment['pay_date']);
                $due_date = $l_date;
            }
            $amount_paid_ent = number_format($monthly_pay,2);
            $amount_ent = number_format($monthly_pay,2);
            $total_amount_due_ent = number_format($monthly_pay,2);
            $count = 1;
        elseif (($acc_status == 'Full DownPayment' && $p2 == 'Deferred Cash Payment') || ($p1 == 'No DownPayment' && $p2 == 'Deferred Cash Payment') || $acc_status == 'Deferred Cash Payment'):
 
            $l_date = date('Y-m-d', strtotime($start_date));
            $day = date('d', strtotime($l_date));
            $monthly_pay = $monthly_payment;
            $l_full_payment = 0;
            // check for fully paid
            if ($last_payment['c_balance'] <= $monthly_pay):
                $l_fp_mode = 1;
                $l_full_payment = $last_payment['c_balance'];
            else:
                $l_fp_mode = 0;
            endif;
        
            if ($acc_status == 'Full DownPayment' || $acc_status == 'Reservation' || $last_payment['status'] == 'RESTRUCTURED' || $last_payment['status'] == 'RECOMPUTED' || $last_payment['status'] == 'ADDITIONAL'):
                $due_date_ent = date('Y-m-d', strtotime($l_date));
                $due_date = $due_date_ent;
                $amount_paid_ent = (number_format($monthly_pay,2));
                $amount_ent = (number_format($monthly_pay,2));
                $total_amount_due_ent = (number_format($monthly_pay,2));
                $count = 1;
                if ($last_payment['status'] == 'ADDITIONAL'):
                    $l_date = date('Y-m-d', strtotime($last_payment['c_due_date']));
                        
                    if ($retention == '1'):
                        $l_date = new DateTime($first_dp);
                    endif;
                    $t_day =  date('d', strtotime($l_date));
                    $l_date2 = new Datetime(auto_date($conn2,$acc_no,$day,$due_date));
                    $due_date = $l_date2->format('m/d/y');
                    $due_date_ent = $l_date2->format('m/d/y');
                    $count = $last_payment['c_status_count'] + 1;
                endif;
               
                
                if ($terms == $count || $l_fp_mode == 1):
                    $l_full_payment = $last_payment['c_balance'];
                    $l_status = 'FPD';
                    $monthly_pay = $l_full_payment;
                    $amount_paid_ent = number_format($monthly_pay,2);
                    $amount_ent = number_format($monthly_pay,2);
                    $total_amount_due_ent = number_format($monthly_pay,2);
                else:
                    $l_status = 'DFC - ' . strval($count);
                endif;
                $payment_status_ent = ($l_status);
                  
            elseif ($acc_status == 'Deferred Cash Payment'):
                $l_date = $last_payment['c_due_date'];
                $t_year = date('Y', strtotime($l_date));
                $t_month = date('m', strtotime($l_date));
                
                if ($retention == 1):
                    $l_date = date("Y-m-d", strtotime($start_date));
                endif;
                $day = date('d', strtotime($l_date));
                $t_day = validate_date($t_year,$t_month,$day);
                $due_date_ent = $t_year .'/'. $t_month .'/'. $t_day;
                // under_pay
                if ($last_payment['c_amount_paid'] < $last_payment['c_amount_due']):
                    $underpay = 1;
                    $l_surcharge = 0;

                    for ($x = 0; $x < floatval($last_payment['payment_count']); $x++) {
                        try {
                            if ($last_payment['status'] == $payment_rec[$x]['status'] && $last_payment['c_due_date'] == $payment_rec[$x]['c_due_date']) {
                                $last_principal += $payment_rec[$x]['principal'];
                                $l_surcharge += $payment_rec[$x]['surcharge'];
                            }
                        } catch (Exception $e) {
                            // pass
                        }
                    }
                    if ($last_payment['c_amount_due'] == 0) {
                        $last_sur = 0;
                   
                    } elseif ($last_payment['c_amount_paid'] < $last_payment['c_amount_due']) {
                        $last_sur = $last_payment['c_amount_paid'];
       
                        $over_due_mode_upay = 1;
                    } else {
                        $last_sur = $last_payment['surcharge'];
                    
                        $over_due_mode_upay = 1;
                    }

                    if (strtotime($last_payment['pay_date']) > strtotime($last_payment['c_due_date'])):
                            $l_date2 = date('Y-m-d', strtotime($last_payment['pay_date']));
                    else:
                            $l_date2 = date('Y-m-d', strtotime($last_payment['c_due_date']));
                    endif;

                    $due_date = $l_date2;
                    $monthly_pay = $monthly_payment - $last_principal;
                    $l_monthly = $last_payment['c_amount_due'] - $last_payment['c_amount_paid'];
                    $count = $last_payment['c_status_count'];
                    $amount_paid_ent = (number_format($l_monthly,2));
                    $amount_ent = (number_format($l_monthly,2));
                    $total_amount_due_ent = (number_format($l_monthly,2));

                    if ($terms == $count || $l_fp_mode == 1) {
                            $l_status = 'FPD';
                            $monthly_pay = $l_full_payment;
                            $amount_paid_ent = (number_format($monthly_pay,2));
                            $amount_ent = (number_format($monthly_pay));
                            $total_amount_due_ent = (number_format($monthly_pay,2));
                    } else {
                            $l_status = 'DFC - ' . strval($count);
                    }

                else:
                
                    $l_date2 = new Datetime(auto_date($conn2,$acc_no,$day,$due_date_ent));
                    $due_date = $l_date2->format('m/d/y');
                    $due_date_ent = $l_date2->format('m/d/y');
                    $count = floatval($last_payment['c_status_count']) + 1;
                    $amount_paid_ent = (number_format($monthly_pay,2));
                    $amount_ent = (number_format($monthly_pay,2));
                    $total_amount_due_ent = (number_format($monthly_pay,2));

                    if ($terms == $count || $l_fp_mode == 1) {
                            $l_status = 'FPD';
                            $l_full_payment = $last_payment['c_balance'];
                            $monthly_pay = $l_full_payment;
                            $amount_paid_ent = (number_format($monthly_pay,2));
                            $amount_ent = (number_format($monthly_pay,2));
                            $total_amount_due_ent = (number_format($monthly_pay,2));
                    } else {
                            $l_status = 'DFC - ' . strval($count);
                    }
                   
                endif;
                $payment_status_ent = $l_status	;
            endif;
            
                
        elseif (($acc_status == 'Full DownPayment' && $p2 == 'Monthly Amortization') || ($p1 == 'No DownPayment' && $p2 == 'Monthly Amortization') || ($acc_status == 'Monthly Amortization')):
                $l_date = date('Y-m-d', strtotime($start_date));
                $day = date('d', strtotime($l_date));
                $monthly_pay = $monthly_payment;
            
                
                if ($acc_status == 'Full DownPayment' || $acc_status == 'Reservation' || $last_payment['c_status'] == 'RESTRUCTURED' || $last_payment['c_status'] == 'RECOMPUTED' || $last_payment['c_status'] == 'ADDITIONAL'):
                    $due_date_ent = date('m/d/Y', strtotime($l_date));
                    $due_date = $due_date_ent;
                    $amount = number_format($monthly_payment,2);
                    $amount_paid_ent = ($amount);
                    $amount_ent = $amount;
                    $total_amount_due_ent = $amount;
                    $count = 1;
                    if ($last_payment['status'] == 'ADDITIONAL') {
                        $l_date = date('Y-m-d', strtotime($last_payment['c_due_date']));
                        $t_year =  date('Y', strtotime($l_date));
                        $t_month =  date('m', strtotime($l_date));
                        if ($retention == '1') {
                            $l_date = date('Y-m-d', strtotime($last_payment['first_dp']));
                        }
                        $t_day =  date('d', strtotime($l_date));
                        $l_date2 = new Datetime(auto_date($conn2,$acc_no,$t_day,$due_date));
                        $due_date = $l_date2->format('m/d/y');
                        $due_date_ent = $l_date2->format('m/d/y');
                        $count = $last_payment[9] + 1;
                    }
                    $l_interest = $last_payment['c_balance'] * ($interest_rate / 1200);
                    $l_principal = $monthly_payment - $l_interest;
                    if ($last_payment['c_balance'] <= $l_principal || $terms == $count) {
                        $l_status = 'FPD';
                        $monthly_pay = $last_payment['c_balance'] + $l_interest;
                        $amount_paid_ent = number_format($monthly_pay,2);
                        $amount_ent = number_format($monthly_pay,2);
                        $total_amount_due_ent = number_format($monthly_pay,2);
                    }else {
                        $l_status = 'MA - ' . strval($count);
                    }
                    $payment_status_ent = ($l_status);


                elseif ($acc_status == 'Monthly Amortization'):
                    $l_date = date('Y-m-d', strtotime($last_payment['c_due_date']));
                    $t_year =  date('Y', strtotime($l_date));
                    $t_month =  date('m', strtotime($l_date));
                    if ($retention == '1') {
                        $l_date = date('Y-m-d', strtotime($start_date));
                    }
                    $day = date('d', strtotime($l_date));
                    $t_day = validate_date($t_year,$t_month,$day);
                    $due_date_ent = $t_year .'/'. $t_month .'/'. $t_day;
            
                    //under_pay
                    if ($last_payment['c_amount_paid'] < $last_payment['c_amount_due']):
                        $l_surcharge = 0;
                        $underpay = 1;
                        $x_y = $last_cnt;
                        $l_cnt = 0;
                        $l_tot_int = 0;
                        while ($last_payment['status'] == $payment_rec[$x_y]['status']) {
                            $l_last_interest = $payment_rec[$x_y]['c_balance'] * ($interest_rate / 1200);
                            $l_tot_int += $payment_rec[$x_y]['interest'];
                            $x_y -= 1;
                            $l_cnt += 1;
                            $l_int_last = $l_last_interest - $l_tot_int;
                        }
                        $monthly_pay = $monthly_pay - $last_principal - $last_interest;
                        if ($l_cnt > 1) {
                            // implement additional code here   
                            for ($x = 0; $x < floatval($last_payment['payment_count']); $x++) {
                                try {
                                   
                                    if ($last_payment['status'] == $payment_rec[$x]['status'] && $last_payment['c_due_date'] == $payment_rec[$x]['c_due_date']) {
                                        $last_principal += $payment_rec[$x]['principal'];
                                        $last_interest += $payment_rec[$x]['interest'];
                                        $l_surcharge += $payment_rec[$x]['surcharge'];
                                    }
                                } catch (Exception $e) {
                                    //pass
                                }
                            }  
                            $monthly_pay = $monthly_pay - $last_principal - $last_interest;  
            
                        }else {
                            $monthly_pay = $last_payment['c_amount_due'] - $last_payment['c_amount_paid'];
                            $last_interest = $l_tot_int;
                            $ma_balance = $last_payment['c_balance'] + $last_principal;
                        }
                        if ($last_payment['surcharge'] == 0):
                            $last_sur = 0;
                        elseif ($last_payment['c_amount_paid'] < $last_payment['surcharge']):
                            $last_sur = $last_payment['c_amount_paid'];
                            $over_due_mode_upay = 1;
                            $monthly_pay = $monthly_payment;
                        else:
                            $last_sur = $last_payment['surcharge'];
                            $over_due_mode_upay = 1;
                        endif;
                        
                        if (strtotime($last_payment['pay_date']) > strtotime($last_payment['c_due_date'])):
                                $l_date = date('Y-m-d', strtotime($last_payment['pay_date']));

                        else:
                                $l_date = date('Y-m-d', strtotime($last_payment['c_due_date']));
                                
                        endif;
                        $due_date = $l_date;

                        $l_monthly = $last_payment['c_amount_due'] - $last_payment['c_amount_paid'];
                        $count = $last_payment['c_status_count'];
                        
                        $amount_paid_ent = (number_format($monthly_pay,2));
                        $amount_ent = (number_format($monthly_pay,2));
                        $total_amount_due_ent = (number_format($monthly_pay,2));

                        if ($l_cnt > 1) {
                            $l_interest = $l_int_last;
                        } else {
                            $l_interest = $ma_balance * ($interest_rate / 1200);
                        }
                        if ($last_interest < $l_interest) {
                            $l_interest = $l_interest - $last_interest;
                        } else {
                            $l_interest = 0.00;
                        }
                        if ($l_cnt > 1) {
                            $l_principal = $monthly_pay - $l_last_interest;
                        } else {
                            $l_principal = $monthly_pay - $l_interest;
                        }
                        if ($last_payment['c_balance'] <= $l_principal || $terms == $count) {
                            $l_status = 'FPD';
                            $monthly_pay = $last_payment['c_balance'] + $l_interest;
                            $amount_paid_ent = (number_format($last_payment['c_balance'],2));
                            $amount_ent = (number_format($monthly_pay,2));
                            $total_amount_due_ent = (number_format($last_payment['c_balance'],2 ));
                        } else {
                            $l_status = 'MA - ' . strval($count);
                        }
                        $payment_status_ent = ($l_status);



                    else:
                        $l_date = new Datetime(auto_date($conn2,$acc_no,$t_day,$due_date_ent));
                        $due_date = $l_date->format('m/d/y');
                        $due_date_ent = $l_date->format('m/d/y');
                        $count = floatval($last_payment['c_status_count']) + 1;
                        $amount_paid_ent = (number_format($monthly_pay,2));
                        $amount_ent = (number_format($monthly_pay,2));
                        $total_amount_due_ent = (number_format($monthly_pay,2));

                        $l_interest = $last_payment['c_balance'] * ($interest_rate /1200);
                        $l_principal = $monthly_pay - $l_interest;
                        if ($last_payment['c_balance'] <= $l_principal || $terms == $count) {
                            $interest = $l_interest;
                            $l_status = 'FPD';
                            $monthly_pay = $last_payment['c_balance'] + $l_interest;
                            $amount_paid_ent = (number_format($last_payment['c_balance'],2));
                            $amount_ent = (number_format($monthly_pay,2));
                            $total_amount_due_ent = (number_format($last_payment['c_balance'],2));
                        } else {
                            $l_status = 'MA - ' . strval($count);
                        }
                        $payment_status_ent = ($l_status);
                    endif;

                endif;   
            
               
        else:        
        
            echo '<script>confirm("Account Is Fully Paid.");</script>';

          
            
        endif;

    }

}
?>