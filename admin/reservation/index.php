<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
	.nav-ra{
		background-color:#007bff;
		color:white!important;
		box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 1px 0 rgba(0, 0, 0, 0.1);
	}
	.nav-ra:hover{
		background-color:#007bff!important;
		color:white!important;
		box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 1px 0 rgba(0, 0, 0, 0.1)!important;
	}
	.main_menu{
		float:left;
		width:227px;
		height:40px;
		line-height:40px;
		text-align:center;
		color:black!important;
		border-right:solid 3px white;
	}
	.main_menu:hover{
		border-bottom: solid 2px blue;
		background-color:#E8E8E8;
	}
	#container{
		margin-right:auto;
		margin-left:auto;
		width:100%;
		position:relative;
		padding-left:50px;
		padding-right:50px;
		background-color:transparent;
	}
	#res-link{
		border-bottom: solid 2px blue;
		background-color:#E8E8E8;
	}
	#res-link1{
		color: currentColor;
		cursor: not-allowed;
		opacity: 0.5;
		text-decoration: none;
		pointer-events: none;
	}
	.table-container {
  		overflow-x: auto;
  		max-width: 100%;
	}
	table {
		table-layout: fixed;
		width: 100%;
	}
	.dropdown {
		position: relative;
	}
	.dropdown-menu {
		display: none;
		position: absolute;
		top: 100%;
		left: 0;
		margin-top: 0; 
		float: none;
		width: 227px;
		height: auto;
		line-height: 30px;
		text-align: center;
		background-color: white; 
		color: black;
		z-index: 1000;
		box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
	}
	.dropdown:hover .dropdown-menu {
		display: block;
	}
	.dropdown-menu li a {
		color: black;
		border: 1px solid gainsboro;
		display: block;
		height: 40px;
		line-height: 40px;
		text-decoration: none;
		background-color: white;
		transition: background-color 0.3s ease;
	}
	.dropdown-menu li a:hover {
		background-color: #E8E8E8;
		color: black;
		border: 1px solid gainsboro;
	}
</style>
<div class="card" id="container" style="display: flex; justify-content: center;">
    <div class="navbar-menu" style="display: flex; justify-content: center; flex-wrap: wrap; width: 100%; margin-left: auto; margin-right: auto; max-width: 1200px;">
		<div class="dropdown">
			<a href="#" class="main_menu dropdown-toggle" style="border-left:solid 3px white;">RAs List</a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo base_url ?>admin/?page=sales">Pendings</a></li>
				<li><a href="<?php echo base_url ?>admin/?page=revision">Revisions</a></li>
				<li><a href="<?php echo base_url ?>admin/?page=ra">Approved</a></li>
			</ul>
		</div>
        <a href="<?php echo base_url ?>admin/?page=reservation" class="main_menu" id="res-link" onclick="highlightLink('res-link')">Reservations List</a>
        <a href="<?php echo base_url ?>admin/?page=credit_assestment" class="main_menu" id="ca-link" onclick="highlightLink('ca-link')">Credit Assessments List</a>
        <a href="<?php echo base_url ?>admin/?page=final_approval" class="main_menu" id="fa-link" onclick="highlightLink('fa-link')">CFO Approvals List</a>
    </div>
</div>
<?php
$usertype = $_settings->userdata('user_type');
if (!isset($usertype)) {
    include '404.html';
  exit;
}

$user_role = $usertype;

if ($user_role != 'IT Admin' && $user_role != 'Cashier') {
    include '404.html';
  exit;
}

?>


<div class="card card-outline rounded-0 card-maroon">
	<div class="card-header">
		<h3 class="card-title"><b><i>Reservations List</b></i></h3>
		<div class="card-tools">
			<a href="./?page=reservation/manage-res" class="btn btn-flat btn-default bg-primary" style="font-size:14px;"><span class="fas fa-plus"></span>&nbsp;&nbsp;Add Reservation</a>
		</div>
	</div>
	<div class="card-body">
		
        <div class="container-fluid">
			<table class="table table-bordered table-stripped" style="width:100%;text-align:center;">

				<thead>
					<tr>
                    <th> No. </th>
                    <th>RA No.</th>
                    <th> Reservation Date </th>
                    <th> OR No. </th>
                    <th> Reservation Fee</th>
                    <th >Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * FROM t_reservation ORDER BY id");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
                            <td><?php echo $i++ ?></td>
                            <td><?php echo $row["ra_no"] ?></td>
                            <td><?php echo $row["c_reserve_date"] ?></td>
                            <td><?php echo $row["c_or_no"] ?></td>
                            <td><?php echo number_format($row["c_amount_paid"],2) ?></td>
                        
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
									<a class="dropdown-item edit_data" href="./?page=reservation/manage-res&id=<?php echo md5($row['id']) ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" lid="<?php echo $row['c_lot_id'] ?>" ra_no="<?php echo $row['ra_no'] ?>" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure you want to delete this payment permanently?","delete_reservation",[$(this).attr('data-id'),$(this).attr('ra_no'),$(this).attr('lid')])
		})
		$('.table').dataTable(
			{"ordering":false}
		);
		
	})
	function delete_reservation($id,$ra_no,$lid){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_reservation",
			method:"POST",
			data:{id:$id,lid:$lid,ra_no:$ra_no},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>