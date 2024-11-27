<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<style>
.titles{
	color:black;
	font-weight:bold;
	text-align:center;
  font-size:20px;
  padding-top:5px;
  padding-bottom:15px;
  margin-top:10px;
}
.space{
  float:left;
  width:100%;
  height:15px;
}
.hr_space{
	margin-top:18px;
	display:block;
}
.view_box{
  border: solid black 1px;
  height:auto;
  width:100%;
  background-color:white;
  border-radius:5px;
  /*box-shadow: 5px 5px #E2DFD2; */
  float:left;
}
.view_lot{
  border: solid black 1px;
  height:auto;
  width:49%;
  margin-right:1%;
  background-color:white;
  border-radius:5px;
  /*box-shadow: 5px 5px #E2DFD2;*/
  float:left;
}
.view_house{
  border: solid black 1px;
  height:auto;
  width:49%;
  margin-left:1%;
  background-color:white;
  border-radius:5px;
  /*box-shadow: 5px 5px #E2DFD2;*/
  float:left;
}
.sc{
  border: solid black 1px;
  height:auto;
  width:100%;
  background-color:white;
  border-radius:5px;
  /*box-shadow: 5px 5px #E2DFD2;*/
  float:left;
}
.pd{
  border: solid black 1px;
  height:auto;
  width:100%;
  background-color:white;
  border-radius:5px;
  /*box-shadow: 5px 5px #E2DFD2;*/
  float:left;
}
.ma{
  border: solid black 1px;
  height:auto;
  width:100%;
  background-color:white;
  border-radius:5px;
  /*box-shadow: 5px 5px #E2DFD2;*/
  float:left;
}
.dfc{
  border: solid black 1px;
  height:auto;
  width:100%;
  background-color:white;
  border-radius:5px;
  /*box-shadow: 5px 5px #E2DFD2;*/
  float:left;
}
.fdp{
  border: solid black 1px;
  height:auto;
  width:100%;
  background-color:white;
  border-radius:5px;
  /*box-shadow: 5px 5px #E2DFD2;*/
  float:left;
}

.title_comment{
	color:black;
	font-weight:bold;
  font-size:20px;
  padding-top:5px;
  padding-bottom:15px;
  margin-top:5px;
}
#txtarea_comment{
  width:100%;
  border-radius:5px;
}

.commentDiv{
  background-color:gainsboro;
  height:auto;
  padding:15px;
  border-radius:5px;
  box-shadow: 5px 5px #E2DFD2;
  border:solid 1px black;
  width:100%;
  float:left;
  margin-top:35px;
}
.comment_list{
  margin-top:50px;
  background-color:#FAF9F6;
  padding:10px;
  float:left;
  width:100%;
  border-radius:5px;
  border:1px solid black;
}
.com_name{
  background-color:gainsboro;
  width:100%;
  height:auto;
  font-weight: bolder;
}
.com_comment{
  border:1px solid black;
  width:100%;
  height:auto;
  padding:5px;
  margin-top:-10px;
}
.com_date{
  background-color:gainsboro;
  width:100%;
  height:auto;
  margin-top:-15px;
  font-size:11px;
  font-style: italic;
  font-weight:bold;
}
#uni_modal{
    width:100%;
    margin-left:auto;
}
</style>
<?php 

$usertype = $_settings->userdata('user_type');
$type = $_settings->userdata('user_code');



/* $usertype = isset($_settings->userdata('type')== 1) ? "IT Admin" : "" ; */
if(($_GET['id']) && ($_GET['id'] > 0)){
    // $csr = $conn->query("SELECT * FROM t_csr where md5(c_csr_no) = '{$_GET['id']}' ");

    $csr = $conn->query("SELECT x.*, z.*,
    x.c_csr_no as csr_num FROM t_csr x 
    inner join t_additional_cost z on z.c_csr_no = x.c_csr_no where md5(x.c_csr_no) = '{$_GET['id']}'" );


if($csr->num_rows > 0){
    while ($row = mysqli_fetch_assoc($csr)):
        $aircon_outlets = $row['aircon_outlets'];
        $aircon_grill = $row['aircon_grill'];
        $conv_outlet = $row['conv_outlet'];
        $service_area = $row['service_area'];
        $others = $row['others'];
        $aircon_outlet_price = $row['aircon_outlet_price'];
        $aircon_grill_price = $row['aircon_grill_price'];
        $conv_outlet_price = $row['conv_outlet_price'];
        $service_area_price = $row['service_area_price'];
        $others_price = $row['others_price'];
        $floor_elev_price = $row['floor_elev_price'];

        $getID = $row['c_csr_no'];
        $csr_no = $row['c_csr_no'];
        $refno = $row['ref_no'];
        $lot_id = $row['c_lot_lid'];   
        $coo_approval = $row['coo_approval'];// status
        $floor_elevation = $row['floor_elevation'];

        ///LOT
        $lot_area = $row['c_lot_area'];
        $price_sqm = $row['c_price_sqm'];
        $lot_disc = $row['c_lot_discount'];
        $lot_disc_amt = $row['c_lot_discount_amt'];
        $lres = $lot_area * $price_sqm;
        $lcp = $lres-($lres*($lot_disc*0.01));

        //HOUSE
        $house_model = $row['c_house_model'];
        $floor_area = $row['c_floor_area'];
        $house_price_sqm = $row['c_house_price_sqm'];
        $house_disc = $row['c_house_discount'];
        $house_disc_amt = $row['c_house_discount_amt'];
        $hres = $floor_area * $house_price_sqm;
        $hcp = $hres-($hres*($house_disc*0.01));
        
        //PAYMENT
        $tcp = $row['c_tcp'];
        $tcp_discount = $row['c_tcp_discount'];
        $tcp_discount_amt = $row['c_tcp_discount_amt'];
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
        $verify = $row['c_verify'];
   /*      $duration = $row['c_duration']; */
        
    endwhile;
    }
}
/* close connection */

?>

<style>
    h4{
        font-size:12px!important;
        font-weight:bold;
    }
    h3{
        text-align:center;
        font-weight:bold;
    }
    #btnprint:hover{
        background-color:blue;
    }
</style>

<body onload="loadAll()">

<div class="card card-outline rounded-0 card-maroon">
	<div class="card-header">
	<i><h3 class="card-title"><b>Reference #<?php echo $refno; ?></b></h3></i>
	</div>
	<div class="card-body">
		<div class="container-fluid">
                <input type="hidden" value="<?php echo $p1; ?>" id="p1">
                <input type="hidden" value="<?php echo $p2; ?>" id="p2">
                <table style="width:100%;">
                    <tr>
                    <div class="row">
                        <td> 
                            <?php if($verify == 0){?>
                                <a href="?page=sales/create&id=<?php echo md5($getID); ?>" class="btn btn-flat btn-primary" style="font-size:14px;width:100%;margin-top:5px;"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp;&nbsp;Edit</a>
                            <?php } ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-flat btn-secondary dropdown-toggle dropdown-icon" data-toggle="dropdown" style="margin-top:5px; font-size:14px;width:100%;">
                                <i class="fa fa-print" aria-hidden="true"></i>&nbsp;&nbsp;Print
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu" role="menu">   
                                <a class="dropdown-item" style="font-size:14px;" href="/ALSC_SALES/report/print_ra.php?id=<?php echo $getID; ?>">Print Front Page</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" style="font-size:14px;" href="/ALSC_SALES/report/print_ra_back.php?id=<?php echo $getID; ?>">Print Back Page</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" style="font-size:14px;" href="/ALSC_SALES/report/print_agreement.php?id=<?php echo $getID; ?>">Print Agreement Page</a>
                            </div>
                        </td>           
                        <td>
                        <?php if($verify == 0 && ($usertype == 'SOS' or $usertype == 'IT Admin')){?> 
                            <button type="button" csr-id =<?php echo $getID; ?> csr-lot-lid = <?php echo $lot_id?> value="1" user-type=<?php echo $type?> class="btn btn-flat btn-success sm-verification" style="margin-top:5px; font-size:14px;width:100%;"><span class="fa fa-check" aria-hidden="true"></span>&nbsp;&nbsp;Verify</button>  
                        </td>
                        <td>                          
                            <button type="button" csr-id =<?php echo $getID; ?> csr-lot-lid = <?php echo $lot_id?> value="2" user-type=<?php echo $type?> class="btn btn-flat btn-danger sm-verification2" style="margin-top:5px; font-size:14px;width:100%;"><span class="fa fa-times-circle" aria-hidden="true"></span>&nbsp;&nbsp;Void</button>                            
                        <?php } ?>
                        </td>
                        <td>
                        <?php if($verify == 1 && $coo_approval == 0 && ($usertype == "CHIEF FINANCE OFFICER" or $usertype == "CHIEF OF OPERATION" or $usertype == "IT Admin" )){ ?>
                            <button type="button" csr-id =<?php echo $getID; ?> data-csr-id =<?php echo $getID ?> user-type=<?php echo $type?> class="btn btn-success btn-flat new-coo-approval" style="margin-top:5px; font-size:14px;width:100%;"><span class="fa fa-check" aria-hidden="true"></span>&nbsp;&nbsp;COO Approved</button>
                        </td>
                        <td>
                            <button type="button" csr-id =<?php echo $getID; ?> csr-lot-lid = <?php echo  $lot_id?> user-type=<?php echo $type?> value="4" class="btn btn-danger btn-flat coo-disapproval" style="margin-top:5px;font-size:14px;width:100%;"><span class="fa fa-times" aria-hidden="true"></span>&nbsp;&nbsp;COO Disapproved</button>
                        </td>
                        <?php } ?>     
                    </div>
                    </tr>
                        </table>
                       
<br>
<?php
$query2 = "SELECT * FROM t_csr_buyers WHERE md5(c_csr_no) = '{$_GET['id']}'";
$result2 = mysqli_query($conn, $query2);
if ($result2) {
    while ($row = mysqli_fetch_assoc($result2)) {
        $buyer_count = $row['c_buyer_count']; // customer buyers no
        $customer_last_name_1 = $row['last_name']; // customer last name
        $customer_suffix_name_1 = $row['suffix_name']; // customer suffix name
        $customer_first_name_1 = $row['first_name']; // customer first name
        $customer_middle_name_1 = $row['middle_name']; // customer middle name
        $cust_fullname1 = sprintf('%s %s, %s %s', $customer_last_name_1, $customer_suffix_name_1, $customer_first_name_1, $customer_middle_name_1);
        $customer_address_1 = $row['address']; // customer address
        $customer_zip_code = $row['zip_code']; // customer zip_code
        $customer_address_2 = $row['address_abroad']; // customer address abroad

        $birth_date = $row['birthdate']; // customer birthday
        $customer_age = $row['age']; // customer age

        $customer_phone = $row['contact_no']; // customer phone number
        $customer_email = $row['email']; // customer civil status
        $customer_viber = $row['viber']; // customer viber
        $customer_gender = $row['gender']; // customer phone number
        $civil_status = $row['civil_status']; // customer civil status
?>
        <div class="view_box">
            <div class="float-left col-md-12">
                <table class="table table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">&nbsp;</th>
                            <th scope="col" class="text-right">Buyer's Information</th>
                            <th scope="col">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row" width="30%">Buyer No:</th>
                            <td class="text-right" width="30%"><?php echo $buyer_count ?></td>
                            <th scope="col">&nbsp;</th>
                        </tr>
                        <tr>
                            <th scope="row">Buyer's Full Name:</th>
                            <td class="text-right"><?php echo $cust_fullname1 ?></td>
                            <th scope="col">&nbsp;</th>
                        </tr>
                        <tr>
                            <th scope="row">Address 1:</th>
                            <td class="text-right"><?php echo $customer_address_1 ?></td>
                            <th scope="col">&nbsp;</th>
                        </tr>
                        <tr>
                            <th scope="row">Zipcode:</th>
                            <td class="text-right"><?php echo $customer_zip_code ?></td>
                            <th scope="col">&nbsp;</th>
                        </tr>
                        <tr>
                            <th scope="row">Address 2 (Abroad):</th>
                            <td class="text-right"><?php echo $customer_address_2 ?></td>
                            <th scope="col">&nbsp;</th>
                        </tr>
                        <tr>
                            <th scope="row">Birthdate:</th>
                            <td class="text-right"><?php echo $birth_date ?></td>
                            <th scope="col">&nbsp;</th>
                        </tr>
                        <tr>
                            <th scope="row">Age:</th>
                            <td class="text-right"><?php echo $customer_age ?></td>
                            <th scope="col">&nbsp;</th>
                        </tr>
                        <tr>
                            <th scope="row">Contact Number:</th>
                            <td class="text-right"><?php echo $customer_phone ?></td>
                            <th scope="col">&nbsp;</th>
                        </tr>
                        <tr>
                            <th scope="row">Email Address:</th>
                            <td class="text-right"><?php echo $customer_email ?></td>
                            <th scope="col">&nbsp;</th>
                        </tr>
                        <tr>
                            <th scope="row">Viber Account:</th>
                            <td class="text-right"><?php echo $customer_viber ?></td>
                            <th scope="col">&nbsp;</th>
                        </tr>
                        <tr>
                            <th scope="row">Gender:</th>
                            <td class="text-right"><?php echo $customer_gender ?></td>
                            <th scope="col">&nbsp;</th>
                        </tr>
                        <tr>
                            <th scope="row">Civil Status:</th>
                            <td class="text-right"><?php echo $civil_status ?></td>
                            <th scope="col">&nbsp;</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <div class="space"></div>
<?php
    }
}
?>

<div class="space"></div>

<!-- Lot Section -->
<div class="view_lot">

    <div class="float-left col-md-12">
        <table class="table table-sm">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">&nbsp;</th>
                    <th scope="col" class="text-right">LOT DETAILS</th>
                    <th scope="col">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Lot ID:</th>
                    <td class="text-right"><?php echo $lot_id ?></td>
                    <th scope="col">&nbsp;</th>
                </tr>
                <tr>
                    <th scope="row">Lot Area:</th>
                    <td class="text-right"><?php echo $lot_area ?> SQM</td>
                    <th scope="col">&nbsp;</th>
                </tr>
                <tr>
                    <th scope="row">Price/SQM:</th>
                    <td class="text-right"><?php echo number_format($price_sqm, 2) ?></td>
                    <th scope="col">&nbsp;</th>
                </tr>
                <tr>
                    <th scope="row">Discount (%):</th>
                    <td class="text-right"><?php echo $lot_disc ?> %</td>
                    <th scope="col">&nbsp;</th>
                </tr>
                <tr>
                    <th scope="row">Discount Amount:</th>
                    <td class="text-right"><?php echo number_format($lot_disc_amt, 2) ?></td>
                    <th scope="col">&nbsp;</th>
                </tr>
                <tr>
                    <th scope="row">Lot Contract Price:</th>
                    <td class="text-right"><?php echo number_format($lcp, 2) ?></td>
                    <th scope="col">&nbsp;</th>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- House Section -->
<div class="view_house">
    <div class="float-left col-md-12">
        <table class="table table-sm">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">&nbsp;</th>
                    <th scope="col" class="text-right">HOUSE DETAILS</th>
                    <th scope="col">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">House Model:</th>
                    <td class="text-right"><?php echo $house_model ?></td>
                    <th scope="col">&nbsp;</th>
                </tr>
                <tr>
                    <th scope="row">Floor Area:</th>
                    <td class="text-right"><?php echo $floor_area ?> SQM</td>
                    <th scope="col">&nbsp;</th>
                </tr>
                <tr>
                    <th scope="row">House Price/SQM:</th>
                    <td class="text-right"><?php echo number_format($house_price_sqm, 2) ?></td>
                    <th scope="col">&nbsp;</th>
                </tr>
                <tr>
                    <th scope="row">Discount (%):</th>
                    <td class="text-right"><?php echo $house_disc ?> %</td>
                    <th scope="col">&nbsp;</th>
                </tr>
                <tr>
                    <th scope="row">Discount Amount:</th>
                    <td class="text-right"><?php echo number_format($house_disc_amt, 2) ?></td>
                    <th scope="col">&nbsp;</th>
                </tr>
                <tr>
                    <th scope="row">House Contract Price:</th>
                    <td class="text-right"><?php echo number_format($hcp, 2) ?></td>
                    <th scope="col">&nbsp;</th>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="space"></div>
<div class="view_box">
    <div class="float-left col-md-12">
        <table class="table table-sm">
            <thead class="thead-dark">
                <tr>
                  
                    <th scope="col">&nbsp;</th>
                    <th scope="col" class="text-left">&nbsp;&nbsp;SUMMARY PAYMENT DETAILS</th>
                    <th scope="col">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">TCP Discount:</th>
                    <td class="text-right"><?php echo $tcp_discount ?> %</td>
                    <th scope="col">&nbsp;</th>
                </tr>
                <tr>
                    <th scope="row">TCP Discount Amount:</th>
                    <td class="text-right"><?php echo number_format($tcp_discount_amt, 2) ?></td>
                    <th scope="col">&nbsp;</th>
                </tr>
                <tr>
                    <th scope="row">Total Contract Price:</th>
                    <td class="text-right">PHP  <?php echo number_format($tcp, 2) ?></td>
                    <th scope="col">&nbsp;</th>
                </tr>
               
                
                <tr>
                    <th scope="row">VAT (<?php echo number_format($vat, 2) ?> %)</th>
                    <td class="text-right"><?php echo number_format($vat_amt, 2) ?></td>
                    <th scope="col">&nbsp;</th>
                </tr>
               
                <tr>
                    <th scope="row">Net TCP:</th>
                    <td class="text-right">PHP <?php echo number_format($net_tcp, 2) ?></td>
                    <th scope="col">&nbsp;</th>
                </tr>
                <tr>
                    <th scope="row">Less: Reservation Fee </th>
                    <td class="text-right">PHP <?php echo number_format($reservation,2) ?></td>
                    <th scope="col">&nbsp;</th> 
                </tr>
               
                <th class="text-right" style="color: green;" scope="row">***<?php echo $p1 ?>***</th>
                    <td class="text-right"></td>
                    <th scope="col"></th>
                </tr>
                <th class="text-right" scope="row">Net Down Payment <?php echo $down_percent ?>%</th>
                    <td class="text-right">PHP <?php echo number_format($net_dp,2) ?></td>
                    <th scope="col"></th>
                </tr>
                <tr>
                    <th class="text-right" scope="row">Monthly Down Payment (<?php echo $no_payments ?>)</th>
                    <td class="text-right">PHP <?php echo number_format($monthly_down,2) ?></td>
                    <th scope="col">Month 1 to <?php echo $no_payments ?></th>
                </tr>
                <th class="text-right " style="color: green;" scope="row">***<?php echo $p2 ?>***</th>
                    <td class="text-right"></td>
                    <th scope="col"></th>
                </tr>
                <tr>
                    <th class="text-right" scope="row">Amount to be financed:</th>
                    <td class="text-right">PHP <?php echo number_format($amt_fnanced,2) ?></td>
                    <th scope="col"></th>
                </tr>
                <tr>
                    <th class="text-right" scope="row">Terms:</th>
                    <td class="text-right"><?php echo $terms ?></td>
                    <th scope="col">(<?php echo number_format($interest_rate,2) ?>%)</th>
                </tr>
                <tr>
                    <th class="text-right" scope="row">Monthly Payment:</th>
                    <td class="text-right">PHP <?php echo number_format($monthly_payment,2)?></td>
                    <th scope="col">Month 1 to <?php echo $terms ?></th>
                </tr>
            </tbody>
        </table>
    </div>
</div>

                         
                                
                      
                </div>  
            </div> 
        </div>
    </div>
</div>

</body>

<script>

    

    $('.coo-disapproval').click(function(){
		_conf("Are you sure you want to disapproved this CSR?","coo_disapproval",[$(this).attr('csr-id'),$(this).attr('csr-lot-lid'),$(this).attr('value'),$(this).attr('user-type')])
	})

    $('.sm-verification').click(function(){
		_conf("Are you sure you want to verify this CSR?","sm_verification",[$(this).attr('csr-id'),$(this).attr('csr-lot-lid'),$(this).attr('value'),$(this).attr('user-type')])
	})

    $('.sm-verification2').click(function(){
		_conf("Are you sure you want to void this CSR?","sm_verification",[$(this).attr('csr-id'),$(this).attr('csr-lot-lid'),$(this).attr('value'),$(this).attr('user-type')])
	})


    function sm_verification($id,$lid,$value,$type){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=sm_verification",
			method:'POST',
			data:{id:$id,lid:$lid,value:$value,type:$type},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured",'error');
                end_loader();
			},
			success:function(resp){
				if(resp.status == 'success'){
                    $(".modal").removeClass("visible");
					$(".modal").modal('hide');
                    location.reload();
				}else if(resp.status == 'failed' && !!resp.msg){
                        $(".modal").removeClass("visible");
                        $(".modal").modal('hide');
                        alert_toast(resp.msg,'error');
                        end_loader();
                }else{
                    alert_toast("An error occured",'error');
                    end_loader();
                    console.log(resp)
                }
			}
		})
	}

    function coo_disapproval($id,$lid,$value,$type){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=coo_disapproval",
			method:'POST',
			data:{id:$id,lid:$lid,value:$value,type:$type},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured",'error');
                end_loader();
			},
			success:function(resp){
				if(resp.status == 'success'){
                    $(".modal").removeClass("visible");
					$(".modal").modal('hide');
                    location.reload();
				}else if(resp.status == 'failed' && !!resp.msg){
                        $(".modal").removeClass("visible");
                        $(".modal").modal('hide');
                        alert_toast(resp.msg,'error');
                        end_loader();
                }else{
                    alert_toast("An error occured",'error');
                    end_loader();
                    console.log(resp)
                }
			}
		})
	}



    $('.new-coo-approval').click(function(){   
        uni_modal("<i class='fa fa-plus'></i> COO Approval",'sales/approval.php?id='+$(this).attr('data-csr-id'),$(this).attr('user-type'),"mid-small")
    })


    $('.compose-email').click(function(){
           uni_modal('Compose Email','mail.php?id='+$(this).attr('data-csr-id')) 

    })

    function showReplyForm(self) {
        var commentId = self.getAttribute("data-id");
        if (document.getElementById("form-" + commentId).style.display == "") {
            document.getElementById("form-" + commentId).style.display = "none";
        } else {
            document.getElementById("form-" + commentId).style.display = "";
        }
    }

    function showReplyForReplyForm(self) {
    var commentId = self.getAttribute("data-id");
    var name = self.getAttribute("data-name");
 
    if (document.getElementById("form-" + commentId).style.display == "") {
        document.getElementById("form-" + commentId).style.display = "none";
    } else {
        document.getElementById("form-" + commentId).style.display = "";
    }
 
    document.querySelector("#form-" + commentId + " textarea[name=comment]").value = "@" + name;
    document.getElementById("form-" + commentId).scrollIntoView();
    }

   
    function changeSelected(){
        var cstatus_changed=document.getElementById('status_list').value;
    }
    function paymentType(){
        var dp1=document.getElementById('p1').value;
        var dp2=document.getElementById('p2').value;
        if(dp1 == "Full DownPayment" && dp2 == "Monthly Amortization"){
                $('#pd').hide();
                $('#ma').show();
                $('#dfc').hide();
                $('#sc').hide();
                $('#fdp').show();
                return;
        }
        if(dp1 == "Full DownPayment" && dp2 == "Deferred Cash Payment"){
                $('#pd').hide();
                $('#ma').hide();
                $('#dfc').show();
                $('#sc').hide();
                $('#fdp').show();
                return;
        }
        if (dp1 == "Partial DownPayment" && dp2 == "Monthly Amortization"){
                $('#pd').show();
                $('#ma').show();
                $('#dfc').hide();
                $('#sc').hide();
                $('#fdp').hide();
                return;
        }
        if(dp1 == "Partial DownPayment" && dp2 == "Deferred Cash Payment"){
                $('#pd').show();
                $('#ma').hide();
                $('#dfc').show();
                $('#sc').hide();
                $('#fdp').hide();
                return;
        }
        if(dp1 == "No DownPayment" && dp2 == "Monthly Amortization"){
                $('#pd').hide();
                $('#ma').show();
                $('#dfc').hide();
                $('#sc').hide();
                $('#fdp').hide();
                return;
        }
        if(dp1 == "No DownPayment" && dp2 == "Deferred Cash Payment"){
                $('#pd').hide();
                $('#ma').hide();
                $('#dfc').show();
                $('#sc').hide();
                $('#fdp').hide();
                return;
        }
        if(dp1 == "Spot Cash"){
                $('#space1').hide();
                $('#pd').hide();
                $('#ma').hide();
                $('#dfc').hide();
                $('#sc').show();
                $('#fdp').hide();
                return;
        }
    }

    function loadAll(){
        //getAcSubtotal();
       // getAcGrillSubtotal();
       // getConvSubtotal();
       // getServiceSubtotal();
        //getOthersSubtotal();
        //getAddCost();
        paymentType();
        //getFlrElev();
    }
    function getFlrElev(){
		if(document.getElementById("id20").checked==true){
			document.getElementById("flrelev_text").value='0.20';
		}else if(document.getElementById("id40").checked==true){
			document.getElementById("flrelev_text").value='0.40';
		}else if(document.getElementById("id60").checked==true){
			document.getElementById("flrelev_text").value='0.60';
		}else{
			document.getElementById("flrelev_text").value='0';
		}
	}
	function getAcSubtotal(){
		var ac_unit = document.getElementById('aircon_outlets').value;
		var ac_unit_price = document.getElementById('aircon_outlet_price').value;

		let res = ac_unit * ac_unit_price;
        let res_comma = res.toLocaleString();

		document.getElementById('ac_outlet_subtotal').value = res;
        document.getElementById('ac_outlet_subtotal_disp').value = res_comma + '.00';
		// getAddCost();
	}
	function getAcGrillSubtotal(){
		var ac_grill = document.getElementById("ac_grill").value;
		var ac_grill_price = document.getElementById("ac_grill_price").value;

		let res = ac_grill * ac_grill_price;
        let res_comma = res.toLocaleString();

		document.getElementById('ac_grill_subtotal').value = res;
        document.getElementById('ac_grill_subtotal_disp').value = res_comma + '.00';
		// getAddCost();
	}
    function getConvSubtotal(){
		var conv = document.getElementById('conv_outlet').value;
		var conv_price = document.getElementById('conv_outlet_price').value;

		let res = conv * conv_price;
        let res_comma = res.toLocaleString();

		document.getElementById('conv_outlet_subtotal').value = res;
        document.getElementById('conv_outlet_subtotal_disp').value = res_comma + '.00'
		// getAddCost();
	}
	function getServiceSubtotal(){
		var service = document.getElementById('service_area').value;
		var service_price = document.getElementById('service_area_price').value;

		var res = service * service_price;
        var res_comma = res.toLocaleString();

		document.getElementById('service_subtotal').value = res;
        document.getElementById('service_subtotal_disp').value = res_comma + '.00';
		// getAddCost();
	}
	function getOthersSubtotal(){
		var others = document.getElementById('others').value;
		var others_price = document.getElementById('others_price').value;

		var res = others * others_price;
        var res_comma = res.toLocaleString();

		document.getElementById('others_subtotal').value = res;
        document.getElementById('others_subtotal_disp').value = res_comma + '.00';
		// getAddCost();
	}

	function getAddCost(){
		var others = document.getElementById('others_subtotal').value;
		var service = document.getElementById('service_subtotal').value;
		var ac_grill1 = document.getElementById('ac_grill_subtotal').value;
		var ac_outlet = document.getElementById('ac_outlet_subtotal').value;
		// var flr_elev = document.getElementById('flrelev_price').value;
		var conv_outlet = document.getElementById('conv_outlet_subtotal').value;

		let result = parseInt(others) + parseInt(service) + parseInt(ac_outlet) + parseInt(conv_outlet) + parseInt(ac_grill1);
        let res_comma = result.toLocaleString();

		document.getElementById('add_cost_total').value = res_comma + '.00';
	}
</script>



