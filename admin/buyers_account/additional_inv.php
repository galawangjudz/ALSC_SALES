<?php
require_once('../../config.php');

$acc = $_GET['acc'];

if (isset($acc)) {
    // Prepare the SQL statement with a placeholder for the account number
    $sql = "SELECT * FROM t_buyers_account WHERE c_account_no = ?";
    
    // Prepare the ODBC statement
    $qry = odbc_prepare($conn2, $sql);
    if (!$qry) {
        die("Preparation of the statement failed: " . odbc_errormsg($conn2));
    }

    // Execute the statement with the account number
    if (!odbc_execute($qry, array($acc))) {
        die("Execution of the statement failed: " . odbc_errormsg($conn2));
    }

    // Fetch the result and store it in variables
    if ($res = odbc_fetch_array($qry)) {
        $dos = $res['c_date_of_sale'];
        $c_account_no = strval($res['c_account_no']);
        $c_last_name = $res['c_b1_last_name'];
        $c_first_name = $res['c_b1_first_name'];
        $c_middle_name = $res['c_b1_middle_name'];
        $c_tel_no = $res['c_tel_no'];
        $c_mobile_no = $res['c_mobile_no'];
        $c_email = $res['c_email'];
        $c_address = $res['c_address'];
        $c_city_prov = $res['c_city_prov'];
        $c_zip_code = $res['c_zip_code'];
    }
}

// Query to fetch agents
$sql = "SELECT c_code, c_last_name, c_first_name, c_position FROM t_agents ORDER BY c_last_name ASC";
$agents = odbc_exec($conn2, $sql);
       

if (!$agents) {
    die("Error executing query: " . odbc_errormsg($conn2));
}

// Close the connection

?>
<form action="" id="add-inv" class="container">
    <div class="form-group">
        <input type="hidden" class="form-control" id="buyer_acc_no" name="buyer_acc_no" value="<?= isset($c_account_no) ? $c_account_no : "" ?>" readonly>
    </div>

    <!-- Type of Additional -->
    <div class="form-group">
        <label for="type_of_additional">Type of Additional:</label>
        <select class="form-control" id="type_of_additional" name="type_of_additional" required>
            <option value="">Select Type</option>
            <option value="house">House</option>
            <option value="fence">Fence</option>
            <option value="addcost">Add Cost</option>
        </select>
    </div>

    <div class="form-group">
        <label class="control-label">House Model: </label>
            <select class="form-control" name= "house_model">
                <option value="None" selected >None</option>
            <?php 
                
                $qry = $conn->query("SELECT * FROM t_model_house ORDER BY c_acronym ASC");
                while($row = $qry->fetch_assoc()):
                    
            ?>		
                    <option value="<?php echo $row['c_model'] ?>" <?php echo isset($house_model) && $house_model == $row['c_model'] ? 'selected' : '' ?> ><?php echo $row["c_model"] ?></option>
            
            <?php endwhile; ?>
            
        </select>
    </div>
       

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">Floor area: </label>
                <input type="text" class="form-control margin-bottom floor-area" name="floor_area" id="floor_area" value="<?php echo isset($floor_area) ? $floor_area : 0; ?>">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">House Price/SQM: </label>
                <input type="text" class="form-control margin-bottom h-price-sqm"  name="h_price_per_sqm" id="h_price_per_sqm" value="<?php echo isset($house_price_sqm) ? $house_price_sqm : 0; ?>">
            </div>
        </div>
    </div>

    <div class="row">
									
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">Floor Elevation: </label>
            </div>
        </div>
        
       
        <div class="col-md-7" >
            <div class="form-group">
                <input id="id20" type="radio" name="chkOption4" value="1" <?php echo isset($floor_elev)&&$floor_elev == 1 ? 'checked' : ''; ?>/>0.20 meter&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input id="id40" type="radio" name="chkOption4" value="2" <?php echo isset($floor_elev)&&$floor_elev == 2 ? 'checked' : ''; ?>/>0.40 meter&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input id="id60" type="radio" name="chkOption4" value="3" <?php echo isset($floor_elev)&&$floor_elev ==  3 ? 'checked' : ''; ?>/>0.60 meter&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </div>
                <input type="hidden" name="flrelev_text" id="flrelev_text" value="<?php echo isset($floor_elev) ? $floor_elev : 0; ?>" onchange="getFlrElev(this);"/>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="text" class="form-control margin-bottom flrelev-price" id="flrelev_price" name="flrelev_price" value="<?php echo isset($floor_elev_price) ? $floor_elev_price : 0; ?>">
            </div>
        </div>
        
    </div>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">Aircon Outlets: </label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label"><input type="number" class="form-control margin-bottom aircon-outlets" id="aircon_outlets" name="aircon_outlets" value="<?php echo isset($aircon_outlets) ? $aircon_outlets : 0; ?>"></label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">Unit/s</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="text" class="form-control margin-bottom aircon-outlet-price" id="aircon_outlet_price" name="aircon_outlet_price" value="<?php echo isset($aircon_outlet_price) ? $aircon_outlet_price : 0; ?>">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">

                <input type="text" class="form-control margin-bottom ac-outlet-subtotal" id="ac_outlet_subtotal" name="ac_outlet_subtotal" value="<?php echo isset($ac_outlet_subtotal) ? $ac_outlet_subtotal : 0; ?>" readonly>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">Aircon Grill: </label>
                <label class="control-label"><i>(for window-type):</i></label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label"><input type="number" class="form-control margin-bottom ac-grill" id="ac_grill" name="ac_grill" value="<?php echo isset($aircon_grill) ? $aircon_grill : 0; ?>"></label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">Unit/s</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="text" class="form-control margin-bottom ac-grill-price" id="ac_grill_price" name="ac_grill_price" value="<?php echo isset($aircon_grill_price) ? $aircon_grill_price : 0; ?>" >
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="text" class="form-control margin-bottom ac-grill-subtotal" id="ac_grill_subtotal" name="ac_grill_subtotal"  value="<?php echo isset($ac_grill_subtotal) ? $ac_grill_subtotal : 0; ?>" readonly>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">Convenience Outlet: </label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label"><input type="number" class="form-control margin-bottom conv-outlet" id="conv_outlet" name="conv_outlet" value="<?php echo isset($conv_outlet) ? $conv_outlet : 0; ?>"></label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">Unit/s</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="text" class="form-control margin-bottom conv-outlet-price" id="conv_outlet_price" name="conv_outlet_price" value="<?php echo isset($conv_outlet_price) ? $conv_outlet_price : 0; ?>" >
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="text" class="form-control margin-bottom conv-outlet-subtotal" id="conv_outlet_subtotal" name="conv_outlet_subtotal" value="<?php echo isset($conv_outlet_subtotal) ? $conv_outlet_subtotal : 0; ?>" readonly>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">Service Area: </label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label"><input type="number" class="form-control margin-bottom service-area" id="service_area" name="service_area" value="<?php echo isset($service_area) ? $service_area : 0; ?>"></label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">Unit/s</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="text" class="form-control margin-bottom service-area-price" id="service_area_price" name="service_area_price" value="<?php echo isset($service_area_price) ? $service_area_price : 0; ?>" >
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="text" class="form-control margin-bottom service-subtotal" id="service_subtotal" name="service_subtotal" value="<?php echo isset($service_subtotal) ? $service_subtotal : 0; ?>" readonly>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">Other(specify): </label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label"><input type="number" class="form-control margin-bottom others" id="others" name="others" value="<?php echo isset($others) ? $others : 0; ?>"></label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">Unit/s</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="text" class="form-control margin-bottom others-price" id="others_price" name="others_price" value="<?php echo isset($others_price) ? $others_price : 0; ?>">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="text" class="form-control margin-bottom others-subtotal" id="others_subtotal" name="others_subtotal"  value="<?php echo isset($others_subtotal) ? $others_subtotal : 0; ?>" readonly>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label"></label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label"></label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label"></label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" style="align-items:right;">Additional Cost/s: </label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="text" class="form-control margin-bottom add-cost-total" id="add_cost_total" name="add_cost_total" value="<?php echo isset($add_cost) ? $add_cost : 0; ?> " readonly>
            </div>
        </div>
    </div>
</div>





    <!-- Agent Assigned (ComboBox) -->
    <div class="form-group">
        <label for="agent_assigned">Agent Assigned:</label>
        <select class="form-control" id="agent_assigned" name="agent_assigned" required>
            <option value="">Select Agent</option>
            <?php while($row = odbc_fetch_array($agents)): ?>
                <option value="<?= $row['c_code'] ?>" <?= isset($agent_assigned) && $agent_assigned == $row['c_code'] ? 'selected' : '' ?>>
                    <?= $row['c_last_name'] . ", " . $row['c_first_name'] ." - " . $row['c_position'] ?>
                </option>
            <?php endwhile; 
            odbc_close($conn2);?>
        </select>
    </div>

</form>

<!-- Initialize Select2 -->
<script>
    $(document).ready(function() {
        $('#agent_assigned').select2({
            placeholder: 'Select an agent',
            allowClear: true
        });
    });
</script>

<script>
    $(function(){
        $('#add-inv').submit(function(e){
            e.preventDefault();
            var _this = $(this);
            $('.pop-msg').remove();
            var el = $('<div>');
                el.addClass("pop-msg alert");
                el.hide();

            // Validate Required Fields
            var isValid = true;
            if ($('#type_of_additional').val() === "") {
                isValid = false;
                $('#type_of_additional').after('<div class="text-danger">This field is required.</div>');
            }
            if ($('#agent_assigned').val() === "") {
                isValid = false;
                $('#agent_assigned').after('<div class="text-danger">This field is required.</div>');
            }

            if (!isValid) {
                $('html, body').animate({scrollTop: 0}, 'fast');
                return;
            }
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/New_Master.php?f=additional_inv",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: err => {
                    console.log(err);
                    alert("An error occurred", 'error');
                    end_loader();
                },
                success: function(resp){
                    if (resp.status == 'success'){
                        alert_toast(resp.msg, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else if (!!resp.msg){
                        el.addClass("alert-danger");
                        el.text(resp.msg);
                        _this.prepend(el);
                    } else {
                        el.addClass("alert-danger");
                        el.text("An error occurred due to unknown reason.");
                        _this.prepend(el);
                    }
                    el.show('slow');
                    $('html, body, .modal').animate({scrollTop:0}, 'fast');
                    end_loader();
                }
            });
        });
    });
</script>

