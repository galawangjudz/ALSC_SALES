<table id="b_details">
    <tr>
        <td style="width: 15%;border-top:none;border-bottom:none;border-left:none;">
            <h4 class="text-red h6">Payment Computation</h4>
        </td>
    </tr>
</table>

<div class="tab-pane fade show active" id="computation" role="tabpanel" aria-labelledby="computation-tab">
    <div class="card mt-3">
        <div id="Payment" class="tabcontent">
          
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-green h6" colspan="4"><i class="fa fa-money"></i> Payment Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            
                            <td><label class="control-label">Total Selling Price:</label></td>
                            <td data-label="Total Selling Price:"><input type="text" class="form-control margin-bottom required net-tcp-1" name="net_tcp1" id="net_tcp1" value="<?= number_format($row['c_net_tcp'],2) ?>" readonly></td>

                            <td><label class="control-label">Reservation:</label></td>
                            <td><input type="text" class="form-control margin-bottom required reservation-fee" name="reservation" id="reservation" value="<?= number_format($row['c_reservation'],2) ?>" readonly></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr>
                            <th class="text-green h6" colspan="4"><i class="fa fa-arrow-down"></i> Down Payment Phase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><label class="control-label">Down Payment Type</label></td>
                            <td><input type="text" class="form-control margin-bottom required down-payment-type" name="down_payment_type" id="down_payment_type" value="<?= $row['c_payment_type1'] ?>" readonly></td>      
                            <td><label class="control-label">Down Percent(%)</label></td>
                            <td><input type="text" class="form-control margin-bottom required down-percent" name="down_percent" id="down_percent" value="<?= $row['c_down_percent'] ?>" readonly></td>
                        </tr>
                        <!-- Fourth Row -->
                        <tr>
                            <td><label class="control-label" id="no_pay_text">No. of Payments</label></td>
                            <td><input type="text" class="form-control margin-bottom required no-payment" name="no_payment" id="no_payment" value="<?= $row['c_no_payments'] ?>" readonly></td>
                            <td><label class="control-label" id="mo_down_text">Monthly Down Payment</label></td>
                            <td><input type="text" class="form-control margin-bottom required monthly-down" name="monthly_down" value="<?= number_format($row['c_monthly_down'],2) ?>" readonly></td>
                        </tr>
                        <!-- Fifth Row -->
                        <tr>
                            <td><label class="control-label">First Down Payment</label></td>
                            <td><input type="text" class="form-control first-dp-date" name="first_dp_date" id="first_dp_date" value="<?= $row['c_first_dp'] ?>" readonly></td>
                            <td><label class="control-label">Full Down Payment</label></td>
                            <td><input type="text" class="form-control full-down-date" name="full_down_date" id="full_down_date" value="<?= $row['c_full_down'] ?>" readonly></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr>
                            <th class="text-green h6" colspan="4"><i class="fa fa-dollar-sign"></i> Amortization Phase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><label class="control-label">Amortization Type</label></td>
                            <td><input type="text" class="form-control margin-bottom required payment-type2" name="payment_type2" id="payment_type2" value="<?= $row['c_payment_type2'] ?>" readonly></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <!-- Sixth Row -->
                        <tr>
                            <td><label class="control-label" id='loan_text'>Amount to be Financed:</label></td>
                            <td><input type="text" class="form-control margin-bottom required amt-to-be-financed" name="amt_to_be_financed" id="amt_to_be_financed" value="<?= number_format($row['c_amt_financed'],2) ?>" readonly></td>
                            <td><label class="control-label">Terms:</label></td>
                            <td><input type="text" class="form-control margin-bottom required terms-count" name="terms" id="terms" value="<?= $row['c_terms'] ?>" readonly></td>
                        </tr>
                        <!-- Seventh Row -->
                        <tr>
                            <td><label class="control-label" id='rate_text'>Interest Rate:</label></td>
                            <td><input type="text" class="form-control margin-bottom required interest-rate" name="interest_rate" id="interest_rate" value="<?= $row['c_interest_rate'] ?>" readonly></td>
                            <td><label class="control-label" id='factor_text'>Fixed Factor:</label></td>
                            <td><input type="text" class="form-control margin-bottom required fixed-factor" name="fixed_factor" id="fixed_factor" value="<?= number_format($row['c_fixed_factor'],8) ?>" readonly></td>
                        </tr>
                        <!-- Eighth Row -->
                        <tr>
                            <td><label class="control-label">Monthly Payment:</label></td>
                            <td><input type="text" class="form-control margin-bottom required monthly-amor" name="monthly_amortization" id="monthly_amortization" value="<?= number_format($row['c_monthly_payment'],2) ?> "readonly></td>
                            <td><label class="control-label" id="start_text">Start Date:</label></td>
                            <td><input type="text" class="form-control required mo-start-date" name="start_date" id="start_date" value="<?= $row['c_start_date'] ?>" readonly></td>
                        </tr>
                    </tbody>
                </table>
            </div>
         </div>
    </div>
</div>
<style>
@media (max-width: 767px) {
    .table-responsive td, .table-responsive th {
        display: block;
        width: 100%;
    }
    .table-responsive td {
        border-top: 1px solid #dee2e6;
    }
    .table-responsive thead {
        display: none;
    }
    .table-responsive td::before {
        content: attr(data-label);
        font-weight: bold;
        width: 100%;
        display: inline-block;
    }
}
</style>
