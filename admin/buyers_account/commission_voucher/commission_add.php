<?php
require_once('../../../config.php');

$acc = $_GET['acc'];
$dos  = isset($_GET['dos']) ? $_GET['dos'] : '';

$c_code = $l_pos = $c_amount = $c_account_no = $c_rate = $c_net_tcp = $c_network = $c_division = $c_last_name = $c_first_name = $c_middle_initial = '';
$c_sale = 1;
$c_account_mode = 1;
if (isset($_GET['id']) && isset($_GET['acc'])) {
    $sql = "SELECT * FROM t_commission WHERE c_code = ? and c_account_no = ?";
    $acc = $_GET['acc'];
    $id  = $_GET['id'];
    $dos  = $_GET['dos'];

    $qry = odbc_prepare($conn2, $sql);
    if (!$qry) {
        die("Preparation of the statement failed: " . odbc_errormsg($conn2));
    }

    if (!odbc_execute($qry, array($id, $acc))) {
        die("Execution of the statement failed: " . odbc_errormsg($conn2));
    }

    while ($res = odbc_fetch_array($qry)) {
        $c_code = $res['c_code'];
        $l_position = $res['c_position'];
        switch ($l_position) {
            case 1: $l_pos = 'AVP'; break;
            case 2: $l_pos = 'JAV'; break;
            case 3: $l_pos = 'AM'; break;
            case 4: $l_pos = 'FM'; break;
            case 5: $l_pos = 'SM'; break;
            case 6: $l_pos = 'MA'; break;
            case 7: $l_pos = 'EMP'; break;
            case 8: $l_pos = 'SPC'; break;
            case 9: $l_pos = 'VPS'; break;
            case 10: $l_pos = 'DS'; break;
            case 11: $l_pos = 'SMG'; break;
            case 12: $l_pos = 'PC'; break;
            case 13: $l_pos = 'PD'; break;
            default: $l_pos = 'N/A'; break;
        }
        $dos = $res['c_date_of_sale'];
        $c_amount = $res['c_amount'];
        $c_account_no = $res['c_account_no'];
        $c_sale = $res['c_sale'];
        $c_rate = $res['c_rate'];
        $c_net_tcp = $res['c_net_tcp'];
        $c_network = $res['c_network'];
        $c_division = $res['c_division'];
        $c_account_mode = $res['c_account_mode'];
        $c_last_name = $res['c_last_name'];
        $c_first_name = $res['c_first_name'];
        $c_middle_initial = $res['c_middle_initial'];
    }
}
if (isset($_GET['acc']) && isset($_GET['dos'])){


}

?>

<style>
    /* Custom CSS for modal form */
    /* Reduce spacing between form sections */
    .form-section {
        margin-bottom: 1rem; /* Adjust margin bottom as needed */
    }

    /* Reduce spacing between form rows */
    .form-section .row {
        margin-bottom: 0.5rem; /* Adjust margin bottom as needed */
    }

    /* Reduce padding inside form fields */
    .form-control {
        padding: 0.375rem 0.75rem; /* Adjust padding as needed */
        font-size: 0.775rem; /* Adjust font size to make text smaller */
    }

    .agentlist {
        padding: 0.375rem 0.75rem; /* Adjust padding as needed */
        font-size: 0.775rem; /* Adjust font size to make text smaller */
    }

    /* Adjust label font size and weight */
    .form-section label {
        font-size: 0.775rem; /* Adjust label font size */
        font-weight: normal; /* Adjust label font weight if needed */
    }

    /* Adjust column width for smaller screens */
    @media (max-width: 768px) {
        .col-md-6 {
            width: 100%; /* Make columns full-width on smaller screens */
            margin-bottom: 0.5rem; /* Adjust margin bottom for responsiveness */
        }
    }
</style>
<style>
    .dropdown-menu {
        max-height: 200px;
        overflow-y: auto;
    }

    .loading-spinner {
    display: none;
    text-align: center;
    font-size: 16px;
    color: #007bff;
    padding: 10px;
}
</style>
<?php 
   $sql2 = "SELECT c_code, c_last_name, c_first_name, c_middle_initial, c_position, c_network, c_division FROM t_agents";

   // Execute the SQL query
   $result = odbc_exec($conn2, $sql2);
   if (!$result) {
       die("Query execution failed: " . odbc_errormsg($conn2));
   }
   
   // Fetch the results into an array
   $items = [];
   while ($row = odbc_fetch_array($result)) {
       $items[] = $row;
   }
   
   // Close the connection
   odbc_close($conn2);
?>

<form action="" id="agent-commission" class="container">
    <div class="form-section mb-4">
        <div class="row">  
            <div class="container">
                <label for="agent_list">Search Agent:</label>
                <div class="dropdown">
                    <input type="text" class="agentlist form-control" id="agent_list" placeholder="Type or select an option" autocomplete="off" required>
                    <div class="dropdown-menu w-100">
                        <?php foreach ($items as $item): ?>
                            <?php
                                // Concatenate the name fields
                                $fullName = $item['c_first_name'] . ' ' . $item['c_middle_initial'] . ' ' . $item['c_last_name'];
                            ?>
                            <a class="dropdown-item" href="#" 
                                data-value="<?= $item['c_code']; ?>" 
                                data-fullname="<?= $fullName; ?>"
                                data-firstname="<?= $item['c_first_name']; ?>"
                                data-middlename="<?= $item['c_middle_initial']; ?>"
                                data-lastname="<?= $item['c_last_name']; ?>"
                                data-position="<?= $item['c_position']; ?>"
                                data-network="<?= $item['c_network']; ?>"
                                data-division="<?= $item['c_division']; ?>">
                                <?= $fullName; ?> (<?= $item['c_position']; ?>)
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <input type="hidden" class="form-control" id="c_account_no" name="c_account_no" value="<?= htmlspecialchars($acc) ?>" >
                    <input type="hidden" class="form-control" id="c_sale" name="c_sale" value="<?= $c_sale = isset($c_sale) ? htmlspecialchars($c_sale) : '1'; ?>" >
                    <input type="hidden" class="form-control" id="c_account_mode" name="c_account_mode" value="<?= isset($c_account_mode) ? htmlspecialchars($c_account_mode) : '1';?>" >
                    <input type="hidden" class="form-control" id="c_date_of_sale" name="c_date_of_sale" value="<?= htmlspecialchars($dos) ?>">

                    <div class="col-md-4">
                        <label for="c_code">Code:</label>
                        <input type="text" class="form-control" id="c_code" name="c_code" value="<?= htmlspecialchars($c_code) ?>" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="c_first_name">First Name:</label>
                        <input type="text" class="form-control" id="c_first_name" name="c_first_name" value="<?= htmlspecialchars($c_first_name) ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="c_last_name">Last Name:</label>
                        <input type="text" class="form-control" id="c_last_name" name="c_last_name" value="<?= htmlspecialchars($c_last_name) ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="c_middle_initial">Middle Initial:</label>
                        <input type="text" class="form-control" id="c_middle_initial" name="c_middle_initial" value="<?= htmlspecialchars($c_middle_initial) ?>" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="c_pos">Position:</label>
                        <input type="hidden" class="form-control" id="c_position" name="c_position" value="<?=  $c_position= isset($c_position) ? htmlspecialchars($c_position) : '0'; ?>" readonly>
                        <input type="text" class="form-control" id="c_pos" name="c_pos" value="<?= $l_pos; ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="c_network">Network:</label>
                        <input type="text" class="form-control" id="c_network" name="c_network" value="<?= htmlspecialchars($c_network) ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="c_division">Division:</label>
                        <input type="text" class="form-control" id="c_division" name="c_division" value="<?= htmlspecialchars($c_division) ?>" readonly>
                    </div>
                </div>
               
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="c_net_tcp">Net TCP Amount:</label>
                        <input type="text" class="form-control" id="c_net_tcp" name="c_net_tcp" value="<?= htmlspecialchars($c_net_tcp) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="c_rate">Rate:</label>
                        <input type="text" class="form-control" id="c_rate" name="c_rate" value="<?= htmlspecialchars($c_rate) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="c_amount">Commission Amount:</label>
                        <input type="text" class="form-control" id="c_amount" name="c_amount" value="<?= isset($c_amount) ? number_format($c_amount, 2) : number_format(0, 2) ?>" readonly>
                    </div>
                </div>
                <script>
                    $(document).ready(function() {
                  
                        const netTcpInput = document.getElementById('c_net_tcp');
                        const rateInput = document.getElementById('c_rate');
                        const commissionAmountInput = document.getElementById('c_amount');

                        function computeCommissionAmount() {
                            const netTcp = parseFloat(netTcpInput.value) || 0;
                            const rate = parseFloat(rateInput.value) || 0;
                            const commissionAmount = netTcp * (rate / 100);
                            commissionAmountInput.value = commissionAmount.toFixed(2);
                        }

                        netTcpInput.addEventListener('input', computeCommissionAmount);
                        rateInput.addEventListener('input', computeCommissionAmount);
                    
                    });
                </script>
            </div>
        </div>
    </div>
</form>

<script>
    $(function(){
        $('#agent-commission').submit(function(e){
            e.preventDefault();
            var _this = $(this);
            $('.pop-msg').remove();
            var el = $('<div>');
                el.addClass("pop-msg alert");
                el.hide();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Commission_Master.php?f=save_agent_commission",
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

<script>
    $(document).ready(function() {
      
        $('.dropdown .dropdown-item').on('click', function(e) {
            e.preventDefault();
            var parentDropdown = $(this).closest('.dropdown');
            var selectedText = $(this).data('fullname');
            var selectedValue = $(this).data('value');
            var firstName = $(this).data('firstname');
            var middleName = $(this).data('middlename');
            var lastName = $(this).data('lastname');
            var position = $(this).data('position');
            const positionMapping = {
                'AVP': 1,
                'JAV': 2,
                'AM': 3,
                'FM': 4,
                'SM': 5,
                'MA': 6,
                'EMP': 7,
                'SPC': 8,
                'VPS': 9,
                'DS': 10,
                'SMG': 11,
                'PC': 12,
                'PD': 13,
                'PSMI': 14
            };
            let pos = positionMapping[position] || 'N/A';
            var network = $(this).data('network');
            var division = $(this).data('division');

            parentDropdown.find('.agentlist').val(selectedText);
            $('#c_code').val(selectedValue);
            $('#c_first_name').val(firstName);
            $('#c_middle_initial').val(middleName);
            $('#c_last_name').val(lastName);
            $('#c_position').val(pos);
            $('#c_pos').val(position);
            $('#c_network').val(network);
            $('#c_division').val(division);
        });

        $('.dropdown .agentlist').on('input', function() {
            var parentDropdown = $(this).closest('.dropdown');
            var filter = $(this).val().toLowerCase();
            parentDropdown.find('.dropdown-menu .dropdown-item').each(function() {
                if ($(this).data('fullname').toLowerCase().indexOf(filter) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        $('.dropdown .agentlist').on('focus', function() {
            var parentDropdown = $(this).closest('.dropdown');
            parentDropdown.find('.dropdown-menu').show();
        });

        $('.dropdown .agentlist').on('blur', function() {
            var parentDropdown = $(this).closest('.dropdown');
            setTimeout(function() {
                parentDropdown.find('.dropdown-menu').hide();
            }, 500);
        });
    });
</script>
