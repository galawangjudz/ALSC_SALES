<?php 
if ($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif; ?>
<style>
    .highlight-row {
        background-color: #f0ad4e; 
        color: white;
    }
    .nav-permissions{
		background-color:#007bff;
		color:white!important;
		box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 1px 0 rgba(0, 0, 0, 0.1);
	}
	.nav-permissions:hover{
		background-color:#007bff!important;
		color:white!important;
		box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 1px 0 rgba(0, 0, 0, 0.1)!important;
	}
</style>
<div class="card card-outline rounded-0 card-maroon">
    <div class="card-header">
        <h5 class="card-title"><b><i>User List</b></i></h5>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <h5>User Permissions</h5>
            <div class="container-fluid">
                <table class="table table-bordered table-striped" id="data-table" style="text-align:center;">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>ID No.</th>
                            <th>Last Name</th>
                            <th>First Name</th>    
                            <th>User type</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        $i = 1;
                        $qry = $conn->query("SELECT * FROM users;");
                        while($row = $qry->fetch_assoc()):
                    ?>
                        <tr class="data-row" 
                            data-id="<?php echo $row['user_code'] ?>" 
                            data-lastname="<?php echo $row['lastname'] ?>" 
                            data-firstname="<?php echo $row['firstname'] ?>" 
                            data-type="<?php echo $row['type'] ?>">
                            <td><?php echo $i++ ?></td>
                            <td><?php echo $row['user_code'] ?></td>
                            <td><?php echo $row['lastname'] ?></td>
                            <td><?php echo $row['firstname'] ?></td>
                            <td>
                            <?php if($row['type'] == 1): ?>
                            <span class="badge badge-primary">Admin</span>
                            <?php elseif($row['type'] == 2): ?>
                                <span class="badge badge-success">Chief Officer</span>
                            <?php elseif($row['type'] == 3): ?>
                                <span class="badge badge-secondary">Manager</span>
                            <?php elseif($row['type'] == 4): ?>
                                <span class="badge badge-info">Supervisor</span>
                            <?php elseif($row['type'] == 5): ?>
                                <span class="badge badge-danger">Employee</span>
                            <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>   
            <div class="container-fluid">
                <form id="details-form" method="get">
                    <div>
                        <label for="id_no">ID No.:</label>
                        <input type="text" id="id_no" class="form-control" readonly name="id_no">
                    </div>
                    <div>
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" class="form-control" readonly>
                    </div>
                    <div>
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" class="form-control" readonly>
                    </div>
                    <div>
                        <label for="user_type">User Type:</label>
                        <input type="text" id="user_type" class="form-control" readonly>
                    </div>
                    <input type="submit" style="display:none;" id="submit-form">
                </form>
            </div>
            <form id="permissions-form" method="get">
                <div class="container-fluid" style="padding: 20px;">
                    <p><strong>ID No: </strong><input type="text" id="display-id-no" class="form-control" readonly name="display-id-no"></p>
                    <div id="permissions-details"></div>
                </div>
            </form>
            <button class="btn btn-flat btn-default bg-maroon" id="save-permissions-btn" form="permissions-form" style="width:100%;margin-right:5px;font-size:14px;"><i class='fa fa-save'></i>&nbsp;&nbsp;Save</button>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#data-table').on('click', '.data-row', function () {
        $('#data-table .data-row').removeClass('highlight-row');
        $(this).addClass('highlight-row');

        const idNo = $(this).data('id');
        const lastName = $(this).data('lastname');
        const firstName = $(this).data('firstname');
        const userType = $(this).data('type');
        const userTypeText = {
            1: "Admin",
            2: "Chief Officer",
            3: "Manager",
            4: "Supervisor",
            5: "Employee"
        };
        $('#id_no').val(idNo);
        $('#display-id-no').val(idNo);
        $('#last_name').val(lastName);
        $('#first_name').val(firstName);
        $('#user_type').val(userTypeText[userType] || "Unknown");
        $.ajax({
            url: '<?php echo base_url; ?>admin/permissions/fetch_permissions.php', 
            method: 'GET',
            data: { id_no: idNo },
            success: function(response) {
                console.log(response); 
                if (response.status === 'success') {
                    $('#permissions-details').empty();
                    const permissionsData = response.data;
                    let permissionsHtml = '';
                    $.each(permissionsData, function(key, value) {
                        console.log('ID No:', idNo);
                        console.log('Key:', key, 'Value:', value);
                        permissionsHtml += `
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="${key}" ${value === '1' ? 'checked' : ''}>
                                <label class="form-check-label" for="${key}">${key}</label>
                            </div>`;
                    });

                    $('#permissions-details').html(permissionsHtml);
                } else {
                    $('#permissions-details').html('<p>Error fetching permissions: ' + response.message + '</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                $('#permissions-details').html('<p>Error with AJAX request.</p>');
            }
        });
    });
});

$(document).ready(function(){
    $('.table').DataTable();
});
</script>
<script>
    $('#permissions-form').submit(function(e){
		e.preventDefault();
        var _this = $(this)
        $('.err-msg').remove();
        
        var errorCounter = validateForm();
        if (errorCounter > 0) {
            alert_toast("It appear's you have forgotten to complete something!","warning");	  
            return false;
        }else{
            $(".required").parent().removeClass("has-error")
        }    
        start_loader();
            $.ajax({
                url: "<?php echo base_url; ?>classes/Master.php?f=save_permissions",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                error: function(err) {
                    console.error("AJAX Error: ", err);
                    var mainError = err.responseJSON?.error || "An unknown error occurred while processing the request.";
                    alert_toast(mainError, 'error');
                    end_loader();
                },
                success: function(resp) {
                    if (typeof resp === 'object' && resp.status === 'success') {
                        location.reload();
                    } else if (resp.status === 'failed' && !!resp.msg) {
                        var el = $('<div>')
                            .addClass("alert alert-danger err-msg")
                            .text(resp.msg);
                        $(this).prepend(el);
                        el.show('slow');
                        end_loader();
                    } else {
                        alert_toast("An unexpected error occurred", 'error');
                        console.error("Unexpected Response: ", resp); 
                        end_loader();
                    }
                }
            });
		})
</script>
<script>
  $('#save-permissions-btn').click(function(e){
    e.preventDefault();
    var permissionsData = {};
    $('#permissions-details input[type="checkbox"]').each(function() {
        var permissionId = $(this).attr('id');
        var isChecked = $(this).prop('checked') ? 1 : 0;
        permissionsData[permissionId] = isChecked;
    });

    var idNo = $('#display-id-no').val();

    $.ajax({
        url: '<?php echo base_url; ?>admin/permissions/save_permissions.php',
        method: 'POST',
        data: { id_no: idNo, permissions: permissionsData },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                alert_toast("Permissions saved successfully", 'success');
                location.reload(); 
            } else {
                alert_toast("Failed to save permissions: " + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            alert_toast("Error with AJAX request.", 'error');
        }
    });
});

</script>
