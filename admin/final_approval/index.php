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
	#fa-link{
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
<div class="card card-outline rounded-0 card-maroon">
	<div class="card-header">
		<h3 class="card-title"><b><i>CFO Approval List</b></i></h3>
		
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered table-stripped">
	 
				<thead>
					<tr>
                    <th>RA No.</th>
					<th>Ref. No.</th>
                    <th>Location </th>
                    <th>Buyer Name </th>
                    <th>CFO Status</th>
					
                    <th>Actions</th>
					
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * FROM t_approval_csr i inner join t_csr_view x on i.c_csr_no = x.c_csr_no where c_ca_status = 1 ORDER BY c_date_approved DESC");
						while($row = $qry->fetch_assoc()):
							$i ++;
                            $ra_id = $row["ra_id"];
                            $status=$row["c_csr_status"];
                            $date_created=$row["c_date_created"];
                            $id=$row["c_csr_no"];
                            $lid = $row["c_lot_lid"];
							$csr_no = $row['c_csr_no'];

						
					?>
						<tr>
                            <td class="text-center"><?php echo $row["ra_id"] ?></td>
                            <td class="text-center"><?php echo $row["ref_no"] ?></td>
                            <td class="text-center"><?php echo $row["c_acronym"]. ' Block ' .$row["c_block"] . ' Lot '.$row["c_lot"] ?></td>
                            <td class="text-center"><?php echo $row["last_name"]. ','  .$row["first_name"] .' ' .$row["middle_name"]?></td>


							<?php if ($row['cfo_status'] == 0): ?>
                                <td><span class="badge badge-warning">Pending</span>
                            <?php elseif($row['cfo_status'] == 1): ?>
                                <td><span class="badge badge-success">Booked</span></td>
                            <?php endif; ?>
						
							<?php if (($usertype == "IT Admin" || $usertype == 'CFO') && $row['cfo_status'] == 0 ): ?>	
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
								  <!-- <a class="dropdown-item view_data" href="./?page=credit_assestment/ca-view&id=<?php echo md5($row['c_csr_no']) ?>"><span class="fa fa-eye text-primary"></span> Evaluation</a> -->
				                    <a class="dropdown-item booked_data" ra-id = "<?php echo $row['ra_id']?>" csr_no = "<?php echo $row['c_csr_no']?>" data-lot-id="<?php echo $row['c_lot_lid'] ?>"><span class="fa fa-check text-primary"></span> Approved</a>
								 </div>
							</td>
							<?php else: ?>
								<td align="center">
									---
								</td>	
							<?php endif; ?>	
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script>


	$(document).ready(function(){
		
		$('.table').dataTable(
			{"ordering":false}
		);
		
	})

		$(document).ready(function(){
			$('.booked_data').click(function(){
				uni_modal_right("<i class='fa fa-check'></i> Final Approval",'final_approval/fa-view.php?id='+$(this).attr('csr_no')+"&csr="+$(this).attr('ra_id')+"&lid="+$(this).attr('data-lot-id'),"mid-large")
				//uni_modal("<i class='fa fa-paint-brush'></i> Edit Lot",'inventory/manage_lot.php?id='+$(this).attr('data-lot-id'),"mid-large")
			})
		})
	// function cfo_approval($ra_id,$csr_no,$lid){
	// 	start_loader();
	// 	$.ajax({
	// 		url:_base_url_+"classes/Master.php?f=cfo_booked",
	// 		method:"POST",
	// 		data:{ra_id:$ra_id,csr_no:$csr_no,lid:$lid},
	// 		dataType:"json",
	// 		error:err=>{
	// 			console.log(err)
	// 			alert_toast("An error occured.",'error');
	// 			end_loader();
	// 		},
	// 		success:function(resp){
	// 			if(typeof resp== 'object' && resp.status == 'success'){
	// 				location.reload();
	// 			}else{
	// 				alert_toast("An error occured.",'error');
	// 				end_loader();
	// 			}
	// 		}
	// 	})
	// }


</script>