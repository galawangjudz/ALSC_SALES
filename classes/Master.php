<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}

	function save_cm(){
		extract($_POST);
		$data = " cm_date = '$cm_date' ";
		$data .= ", credit_amount = '$cm_amount' ";
		$data .= ", reason = '$cm_reason' ";	 
	 	$data .= ", reference = '$cm_reference' ";
		$data .= ", memo_status = '$cm' ";
	 	//if(empty($cm_id)){ 
			$sql = "INSERT INTO t_credit_memo set ".$data;
			$save = $this->conn->query($sql);
	 	// }else{
		// 	$save = $this->conn->query("UPDATE t_credit_memo set ".$data." where cm_id = ".$cm_id);
		// } 
		if($save){
			$resp['status'] = 'success';
			if(empty($prod_lid))
				$this->settings->set_flashdata('success',"Credit/Debit memo successfully saved.");
			else
				$this->settings->set_flashdata('success',"Credit/Debit memo successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
			
	}

	function save_lot(){
		extract($_POST);
		$data = " c_site = '$prod_code' ";
		$data .= ", c_block = '$prod_block' ";
		$data .= ", c_lot = '$prod_lot' ";	 
	 	$data .= ", c_lot_area = '$prod_lot_area' ";
		$data .= ", c_price_sqm = '$prod_lot_price' "; 
		$data .= ", c_remarks = '$prod_remarks' ";
		$data .= ", c_status = '$prod_status' ";
		
	 	if(empty($prod_lid)){ 
			$prod_lid = sprintf('%03d%03d%02d', $prod_code, $prod_block, $prod_lot);
			$data .= ", c_lid = '$prod_lid' "; 

			$data_notif = " message = '$comm with Lot ID #$prod_lid.'";
			$data_notif .= ", user_to_be_notified = 'IT Admin' ";
			$data_notif .= ", seen = '0' ";
			$save = $this->conn->query("INSERT INTO message_tbl set ".$data_notif);
			$save = $this->conn->query("INSERT INTO t_lots set ".$data);
	 	}else{
			$data_notif1 = " message = '$comm2 with Lot ID #$prod_lid.'";
			$data_notif1 .= ", user_to_be_notified = 'IT Admin' ";
			$data_notif1 .= ", seen = '0' ";
			$save = $this->conn->query("INSERT INTO message_tbl set ".$data_notif1);
			$save = $this->conn->query("UPDATE t_lots set ".$data." where c_lid = ".$prod_lid);
		} 
		if($save){
			$resp['status'] = 'success';
			if(empty($prod_lid))
				$this->settings->set_flashdata('success',"New Lot successfully saved.");
			else
				$this->settings->set_flashdata('success',"Lot successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
			
	}

	///////////////////////

	function save_agent(){
		extract($_POST);
		$data = " c_code = '$c_code' ";
		$data .= ", c_nick_name = '$c_nick_name' ";
		$data .= ", c_last_name = '$c_last_name' ";
		$data .= ", c_first_name = '$c_first_name' ";	 
		$data .= ", c_middle_initial = '$c_middle_initial' ";	 
		$data .= ", c_sex = '$c_sex' ";	 
		$data .= ", c_civil_status = '$c_civil_status' ";	 
		$data .= ", c_birthdate = '$c_birthdate' ";	 
		$data .= ", c_tel_no = '$c_tel_no' ";	 
		$data .= ", c_sss_no = '$c_sss_no' ";	
		$data .= ", c_tin = '$c_tin' ";	
		$data .= ", c_birth_place = '$c_birth_place' ";	
		$data .= ", c_address_ln1 = '$c_address_ln1' ";	
		$data .= ", c_address_ln2 = '$c_address_ln2' ";	
		$data .= ", c_hire_date = '$c_hire_date' ";	
		$data .= ", c_status = '$c_status' ";	
		$data .= ", c_recruited_by = '$c_recruited_by' ";	
		$data .= ", c_position = '$c_position' ";	
		$data .= ", c_division = '$category' ";	
		// $data .= ", c_network = '$subcategory' ";	

		if(empty($id)){
			$sql = "INSERT INTO t_agents set ".$data;
			$save = $this->conn->query($sql);
		}else{
			$save = $this->conn->query("UPDATE t_agents set ".$data." where id = ".$id);
		} 
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New agent successfully saved.");
			else
				$this->settings->set_flashdata('success',"Agent successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	
	function delete_agent(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM t_agents where c_code = ".$id);
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Agent successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);	
	}
	//////////////////
	function delete_data(){
		extract($_POST);
		$del = $this->conn->query("UPDATE family_members SET status=2 where member_id = ".$id);
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Information successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);	
	}
	function delete_inactive(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM family_members where member_id = ".$id);
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Information successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);	
	}

	function add_data(){
		extract($_POST);
		
		$del = $this->conn->query("UPDATE family_members SET status=1 where member_id = ".$id);
		if($del){

		// $sql = $this->conn->query("SELECT * FROM users where id=" .$type);
		// while($row = $sql->fetch_assoc()){
		// 	$usertype= $row['username'];
		// }
		// 	$users_to_notify = array('IT Admin'); 

		// 		foreach ($users_to_notify as $user) {
		// 			$data_notif_values = array(
		// 				"message = '$usertype approved family member for client #.$clientId.'",
		// 				"user_to_be_notified = '$user'",
		// 				"seen = '0'"
		// 			);

		// 			$data_notif = implode(", ", $data_notif_values);

		// 			$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
		// 		}

			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Information successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);	
	}
	function delete_lot(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM t_lots where c_lid = ".$id);
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Lot successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);	
	}

	function delete_user(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM users where user_id = ".$id);
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"User successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);	
	}

	function save_or_logs(){
		extract($_POST);
		$sql = $this->conn->query("SELECT * FROM t_invoice where property_id =".$prop_id." ORDER by due_date, pay_date, payment_count, remaining_balance DESC");
		
		if($sql->num_rows <= 0){
			$resp['status'] = 'failed';
			$resp['err'] = 'No Payment Records yet! Please add to proceed with payments';
			return json_encode($resp);
        } 
		// $data = " log_id = '$log_id' ";
		$data = " property_id = '$prop_id' ";
		$data .= ", pay_date = '$pay_date_ent1' ";
		$data .= ", or_no = '$or_no' ";
		$data .= ", amount_paid = '$amt_pd' ";
		$data .= ", amount_due = '$amt_due' ";
		$data .= ", surcharge = '$amt_surcharge' ";
		
		$data .= ", interest = '$amt_interest' ";
		$data .= ", principal = '$amt_principal' ";
		$data .= ", rebate = '$amt_rebate' ";
		$data .= ", remaining_balance = '$balance' ";
		$data .= ", mode_of_payment = '$mode_of_payment' ";
		$data .= ", user = '$user' ";
		$data .= ", check_date = '$check_date' ";
		$data .= ", particulars = '$b_particulars' ";
		$data .= ", check_number = '$check_number' ";
		$data .= ", branch = '$branch' ";

		$sql = "INSERT INTO or_logs set ".$data;
		$save = $this->conn->query($sql);


		// if(empty($prod_id)){
		// 	$sql = "INSERT INTO t_model_house set ".$data;
		// 	$save = $this->conn->query($sql);
		// }else{
		// 	$sql = "UPDATE t_model_house set ".$data." where c_code = ".$prod_id;
		// 	$save = $this->conn->query($sql);
		// }
		
		if($save){
			$resp['status'] = 'success';
			if(empty($prop_id))
				$this->settings->set_flashdata('success',"New OR log successfully saved.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}


	function save_house_model(){
		extract($_POST);
		$data = " c_model = '$c_model' ";
		$data .= ", c_acronym = '$c_acronym' ";
		$data .= ", c_code = '$c_code' ";
		if(empty($prod_id)){
			$data_notif = " message = '$comm with code #$c_code.'";
			$data_notif .= ", user_to_be_notified = 'IT Admin' ";
			$data_notif .= ", seen = '0' ";
			$save = $this->conn->query("INSERT INTO message_tbl set ".$data_notif);
			$sql = "INSERT INTO t_model_house set ".$data;
			$save = $this->conn->query($sql);
		}else{
			$data_notif1 = " message = '$comm2 with code #$c_code.'";
			$data_notif1 .= ", user_to_be_notified = 'IT Admin' ";
			$data_notif1 .= ", seen = '0' ";
			$save = $this->conn->query("INSERT INTO message_tbl set ".$data_notif1);
			$sql = "UPDATE t_model_house set ".$data." where c_code = ".$prod_id;
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($prod_id))
				$this->settings->set_flashdata('success',"New House Model successfully saved.");
			else
				$this->settings->set_flashdata('success',"House Model successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}

	function delete_model(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM t_model_house where c_code = ".$id);
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"House Model successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
			
	}

	function save_project(){
		extract($_POST);
		$data = " c_name = '$c_name' ";
		$data .= ", c_acronym = '$c_acronym' ";
		$data .= ", c_address = '$c_address' "; 
	 	$data .= ", c_province = '$c_province' ";
		$data .= ", c_zip = '$c_zip' "; 
		$data .= ", c_rate = '$c_rate' ";
		$data .= ", c_reservation = '$c_reservation' ";
		$data .= ", c_status = '$c_status' ";
		$data .= ", c_code = '$c_code' ";
		if(empty($prod_id)){
			$data_notif = " message = '$comm $c_name.'";
			$data_notif .= ", user_to_be_notified = 'IT Admin' ";
			$data_notif .= ", seen = '0' ";
			$save = $this->conn->query("INSERT INTO message_tbl set ".$data_notif);
			$save = $this->conn->query("INSERT INTO t_projects set ".$data);
		}else{
			$data_notif1 = " message = '$comm2 $c_name.'";
			$data_notif1 .= ", user_to_be_notified = 'IT Admin' ";
			$data_notif1 .= ", seen = '0' ";
			$save = $this->conn->query("INSERT INTO message_tbl set ".$data_notif1);
			$save = $this->conn->query("UPDATE t_projects set ".$data." where c_code = ".$prod_id);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($prod_id))
				$this->settings->set_flashdata('success',"New Project Site successfully saved.");
			else
				$this->settings->set_flashdata('success',"Project Site successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}

	function delete_project(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM t_projects where c_code = ".$id);
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Project Site successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_csr(){
		extract($_POST);
			
		if(empty($c_csr_no)){

			//lot computation
			$username =  $_POST['username'];
			$prop_id = $_POST['prop_id'];
			$lot_lid = $_POST['l_lid'];
			$lot_area = $_POST['lot_area'];
			$price_sqm = $_POST['price_per_sqm'];
			$lot_disc = $_POST['lot_disc'];
			$lot_disc_amt = $_POST['lot_disc_amt'];
			$house_model = isset($_POST['house_model']) ? $_POST['house_model'] : "None";
		
			$floor_area = $_POST['floor_area'];
			$h_price_per_sqm = $_POST['h_price_per_sqm'];
			$house_disc = $_POST['house_disc'];
			$house_disc_amt = $_POST['house_disc_amt'];
			$process_fee = $_POST['process_fee'];
			$pf_month = $_POST['pf_month'];
			$total_tcp = $_POST['total_tcp'];
			$tcp_disc = $_POST['tcp_disc'];
			$tcp_disc_amt = $_POST['tcp_disc_amt'];
			$vat_amt = $_POST['vat_amt_computed'];
			$net_tcp = $_POST['net_tcp'];
			

			// Payment Details
			$reservation = $_POST['reservation'];
			$payment_type1 = $_POST['payment_type1'];
			$payment_type2 = isset($_POST['payment_type2']) ? $_POST['payment_type2'] : "None";
			$down_percent = $_POST['down_percent'];
			$net_dp = $_POST['net_dp'];
			$no_payment = $_POST['no_payment'];
			$monthly_down = $_POST['monthly_down'];
			$first_dp_date = $_POST['first_dp_date'];
			$full_down_date = $_POST['full_down_date'];
			$amt_to_be_financed = $_POST['amt_to_be_financed'];
			$terms= $_POST['terms'];
			$interest_rate = $_POST['interest_rate'];
			$fixed_factor = $_POST['fixed_factor'];
			$monthly_amortization = $_POST['monthly_amortization'];
			$start_date = $_POST['start_date'];
			$invoice_notes = $_POST['invoice_notes'];
			$type = $_POST['chkOption3'];
			//$rev = $_POST['rev_status'];//////////////////ADDED




			$data = " c_lot_lid = '$lot_lid' ";
			$data .= ", c_type = '$type' ";
			$data .= ", c_lot_area = '$lot_area' ";
			$data .= ", c_price_sqm = '$price_sqm' ";
			$data .= ", c_lot_discount= '$lot_disc' ";
			$data .= ", c_lot_discount_amt = '$lot_disc_amt' ";
			$data .= ", c_house_model = '$house_model' ";
			$data .= ", c_floor_area= '$floor_area' ";
			$data .= ", c_house_price_sqm= '$h_price_per_sqm' ";
			$data .= ", c_house_discount = '$house_disc' ";
			$data .= ", c_house_discount_amt = '$house_disc_amt' ";
			$data .= ", c_processing_fee = '$process_fee' ";
			$data .= ", pf_mo = '$pf_month' ";
			$data .= ", c_tcp_discount = '$tcp_disc' ";
			$data .= ", c_tcp_discount_amt = '$tcp_disc_amt' ";
			$data .= ", c_tcp = '$total_tcp' ";
			$data .= ", c_vat_amount = '$vat_amt' ";
			$data .= ", c_net_tcp = '$net_tcp' ";
			$data .= ", c_reservation = '$reservation' ";
			$data .= ", c_payment_type1 = '$payment_type1' ";
			$data .= ", c_payment_type2 = '$payment_type2' ";
			$data .= ", c_down_percent = '$down_percent' ";
			$data .= ", c_net_dp = '$net_dp' ";
			$data .= ", c_no_payments = '$no_payment' ";
			$data .= ", c_monthly_down = '$monthly_down' ";
			$data .= ", c_first_dp = '$first_dp_date' ";
			$data .= ", c_full_down = '$full_down_date' ";
			$data .= ", c_amt_financed = '$amt_to_be_financed' ";
			$data .= ", c_terms = '$terms' ";
			$data .= ", c_interest_rate = '$interest_rate' ";
			$data .= ", c_fixed_factor = '$fixed_factor' ";
			$data .= ", c_monthly_payment = '$monthly_amortization' ";
			$data .= ", c_start_date = '$start_date' ";
			$data .= ", c_remarks = '$invoice_notes' ";
			$data .= ", c_created_by = '$username' ";

			if ($prop_id != null){ //////////////////ADDED
				$rev_count=2;
				$data .= ", c_revised = '$rev_count' ";//////////////////ADDED
				$data .= ", old_property_id = '$prop_id' ";//////////////////ADDED
				
			}else{//////////////////ADDED
				$data .= ", c_revised = '0' ";//////////////////ADDED
			}//////////////////ADDED
			//$data .= ", old_property_id = '$prop_id'";

			$i = 1;
			while($i== 1){
				$ref  = sprintf("%'.04d\n",mt_rand(1,9999999999));
				if($this->conn->query("SELECT * FROM t_csr where ref_no ='$ref'")->num_rows <= 0)
					$i=0;
			}
			$data .= ", ref_no = '$ref' ";

			$save = $this->conn->query("INSERT INTO t_csr set ".$data);

			

			$last_id = $c_csr_no;
			foreach($_POST['agent_name'] as $key => $value) {

				$agent = $value;
				$agent_code = $_POST['agent_code'][$key];
				$agent_pos = $_POST['agent_position'][$key];
				$agent_amount = $_POST['comm_amt'][$key];
				$agent_rate = $_POST['agent_rate'][$key]; 

				$data = " c_csr_no = '$last_id' ";
				$data .= ", c_code = '$agent_code' ";
				$data .= ", c_position = '$agent_pos' ";
				$data .= ", c_agent = '$agent' ";
				$data .= ", c_amount = '$agent_amount' ";
				$data .= ", c_rate = '$agent_rate' ";
				$save = $this->conn->query("INSERT INTO t_csr_commission set ".$data);
				}


			$buyer_count = 1;


			foreach($_POST['last_name'] as $key => $value) {
		
				$lastname = $_POST['last_name'][$key];
				$firstname = $_POST['first_name'][$key];
				$middlename = $_POST['middle_name'][$key];
				$suffixname = $_POST['suffix_name'][$key]; 
				$address = $_POST['address'][$key];
				$zip_code = $_POST['zip_code'][$key];
				$address_abroad = $_POST['address_abroad'][$key];
				$birthdate = $_POST['birth_day'][$key];
				$age = $_POST['age'][$key];
				$viber = $_POST['viber'][$key];
				$gender = $_POST['gender'][$key];
				$civil_status = $_POST['civil_status'][$key];
				$citizenship = $_POST['citizenship'][$key];
				$id_presented = $_POST['id_presented'][$key];
				$tin_no = $_POST['tin_no'][$key];
				$email = $_POST['email'][$key];
				$contact_no = $_POST['contact_no'][$key];
				$contact_abroad = $_POST['contact_abroad'][$key];
				$relationship = $_POST['relationship'][$key];

				$data = " c_csr_no = '$last_id' ";
				$data .= ", c_buyer_count = '$buyer_count' ";
				$data .= ", last_name = '$lastname' ";
				$data .= ", first_name = '$firstname' ";
				$data .= ", middle_name = '$middlename' ";
				$data .= ", suffix_name = '$suffixname' ";
				$data .= ", address = '$address' ";
				$data .= ", zip_code = '$zip_code' ";
				$data .= ", address_abroad = '$address_abroad ' ";
				$data .= ", birthdate = '$birthdate ' ";
				$data .= ", age = '$age ' ";
				$data .= ", viber = '$viber ' ";
				$data .= ", gender = '$gender' ";
				$data .= ", civil_status = '$civil_status' "; 
				$data .= ", citizenship = '$citizenship' ";
				$data .= ", id_presented = '$id_presented' "; 
				$data .= ", tin_no = '$tin_no' "; 
				$data .= ", email = '$email' "; 
				$data .= ", contact_no = '$contact_no' "; 
				$data .= ", contact_abroad = '$contact_abroad' "; 
				$data .= ", relationship = '$relationship' ";

				$users_to_notify = array('IT Admin', 'SOS'); 


				// foreach ($users_to_notify as $user) {
				// 	$data_notif_values = array(
				// 		"message = '$comm with ref#$ref.'",
				// 		"user_to_be_notified = '$user'",
				// 		"seen = '0'"
				// 	);

				// 	$data_notif = implode(", ", $data_notif_values);


				// 	$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
				// }
				

				$save = $this->conn->query("INSERT INTO t_csr_buyers set ".$data);

				$buyer_count += 1;
				}
			
		}

		if(!empty($c_csr_no)){
			$c_csr_no =  $_POST['c_csr_no'];
			//lot computation
			$username =  $_POST['username'];
			$lot_lid = $_POST['l_lid'];
			$lot_area = $_POST['lot_area'];
			$price_sqm = $_POST['price_per_sqm'];
			$lot_disc = $_POST['lot_disc'];
			$lot_disc_amt = $_POST['lot_disc_amt'];
			$house_model = isset($_POST['house_model']) ? $_POST['house_model'] : "None";
			$floor_area = $_POST['floor_area'];
			$h_price_per_sqm = $_POST['h_price_per_sqm'];
			$house_disc = $_POST['house_disc'];
			$house_disc_amt = $_POST['house_disc_amt'];
			$process_fee = $_POST['process_fee'];
			$pf_month = $_POST['pf_month'];
			$total_tcp = $_POST['total_tcp'];
			$tcp_disc = $_POST['tcp_disc'];
			$tcp_disc_amt = $_POST['tcp_disc_amt'];
			$vat_amt = $_POST['vat_amt_computed'];
			$net_tcp = $_POST['net_tcp'];

			// Payment Details
			$reservation = $_POST['reservation'];

			$chk_pay = $this->conn->query("SELECT COALESCE(sum(c_amount_paid), 0)  as total_reservation FROM t_reservation where c_csr_no =".$c_csr_no);
			if($chk_pay->num_rows > 0){
			while($row = $chk_pay->fetch_assoc()){
					$total = $row['total_reservation'];
					if($reservation == $total){
						$save = $this->conn->query("UPDATE t_approval_csr SET c_reserve_status = 1 , c_amount_paid = '$total', c_ca_status = 0 where c_csr_no = '$c_csr_no'");
					}else if (($reservation > $total) && ($total != 0)) {
						$save = $this->conn->query("UPDATE t_approval_csr SET c_reserve_status = 2 , c_amount_paid = '$total', c_ca_status = 0 where c_csr_no= '$c_csr_no'");
						
					}else if ($total == 0) {
						$save = $this->conn->query("UPDATE t_approval_csr SET c_reserve_status = 0 , c_amount_paid = '$total', c_ca_status = 0 where c_csr_no= '$c_csr_no'");
						
					}
				}}
			$payment_type1 = $_POST['payment_type1'];
			$payment_type2 = isset($_POST['payment_type2']) ? $_POST['payment_type2'] : "None";
			$down_percent = $_POST['down_percent'];
			$net_dp = $_POST['net_dp'];
			$no_payment = $_POST['no_payment'];
			$monthly_down = $_POST['monthly_down'];
			$first_dp_date = $_POST['first_dp_date'];
			$full_down_date = $_POST['full_down_date'];
			$amt_to_be_financed = $_POST['amt_to_be_financed'];
			$terms= $_POST['terms'];
			$interest_rate = $_POST['interest_rate'];
			$fixed_factor = $_POST['fixed_factor'];
			$monthly_amortization = $_POST['monthly_amortization'];
			$start_date = $_POST['start_date'];
			$invoice_notes = $_POST['invoice_notes'];
			$type = $_POST['chkOption3'];

			$data = " c_lot_lid = '$lot_lid' ";
			$data .= ", c_type = '$type' ";
			$data .= ", c_lot_area = '$lot_area' ";
			$data .= ", c_price_sqm = '$price_sqm' ";
			$data .= ", c_lot_discount= '$lot_disc' ";
			$data .= ", c_lot_discount_amt = '$lot_disc_amt' ";
			$data .= ", c_house_model = '$house_model' ";
			$data .= ", c_floor_area= '$floor_area' ";
			$data .= ", c_house_price_sqm= '$h_price_per_sqm' ";
			$data .= ", c_house_discount = '$house_disc' ";
			$data .= ", c_house_discount_amt = '$house_disc_amt' ";
			$data .= ", c_processing_fee = '$process_fee' ";
			$data .= ", pf_mo = '$pf_month' ";
			$data .= ", c_tcp_discount = '$tcp_disc' ";
			$data .= ", c_tcp_discount_amt = '$tcp_disc_amt' ";
			$data .= ", c_tcp = '$total_tcp' ";
			$data .= ", c_vat_amount = '$vat_amt' ";
			$data .= ", c_net_tcp = '$net_tcp' ";
			$data .= ", c_reservation = '$reservation' ";
			$data .= ", c_payment_type1 = '$payment_type1' ";
			$data .= ", c_payment_type2 = '$payment_type2' ";
			$data .= ", c_down_percent = '$down_percent' ";
			$data .= ", c_net_dp = '$net_dp' ";
			$data .= ", c_no_payments = '$no_payment' ";
			$data .= ", c_monthly_down = '$monthly_down' ";
			$data .= ", c_first_dp = '$first_dp_date' ";
			$data .= ", c_full_down = '$full_down_date' ";
			$data .= ", c_amt_financed = '$amt_to_be_financed' ";
			$data .= ", c_terms = '$terms' ";
			$data .= ", c_interest_rate = '$interest_rate' ";
			$data .= ", c_fixed_factor = '$fixed_factor' ";
			$data .= ", c_monthly_payment = '$monthly_amortization' ";
			$data .= ", c_start_date = '$start_date' ";
			$data .= ", c_remarks = '$invoice_notes' ";
			$data .= ", c_created_by = '$username' ";
			$data .= ", c_verify = 0 ";
			$data .= ", coo_approval = 0";

			
			$this->conn->query("UPDATE t_csr set ".$data." where c_csr_no = ".$c_csr_no);
			$this->conn->query("DELETE FROM t_csr_buyers where c_csr_no = ".$c_csr_no);
			$this->conn->query("DELETE FROM t_csr_commission where c_csr_no = ".$c_csr_no);
			// get last insert id
			$last_id = $c_csr_no;

			foreach($_POST['agent_name'] as $key => $value) {
				$agent = $value;
				$agent_code = $_POST['agent_code'][$key];
				$agent_pos = $_POST['agent_position'][$key];
				$agent_amount = $_POST['comm_amt'][$key];
				$agent_rate = $_POST['agent_rate'][$key]; 

				$data = " c_csr_no = '$last_id' ";
				$data .= ", c_code = '$agent_code' ";
				$data .= ", c_position = '$agent_pos' ";
				$data .= ", c_agent = '$agent' ";
				$data .= ", c_amount = '$agent_amount' ";
				$data .= ", c_rate = '$agent_rate' ";


				$save = $this->conn->query("INSERT INTO t_csr_commission set ".$data);
				}

			$buyer_count = 1;
			foreach($_POST['last_name'] as $key => $value) {
		
				$lastname = $_POST['last_name'][$key];
				$firstname = $_POST['first_name'][$key];
				$middlename = $_POST['middle_name'][$key];
				$suffixname = $_POST['suffix_name'][$key]; 
				$address = $_POST['address'][$key];
				$zip_code = $_POST['zip_code'][$key];
				$address_abroad = $_POST['address_abroad'][$key];
				$birthdate = $_POST['birth_day'][$key];
				$age = $_POST['age'][$key];
				$viber = $_POST['viber'][$key];
				$gender = $_POST['gender'][$key];
				$civil_status = $_POST['civil_status'][$key];
				$citizenship = $_POST['citizenship'][$key];
				$id_presented = $_POST['id_presented'][$key];
				$tin_no = $_POST['tin_no'][$key];
				$email = $_POST['email'][$key];
				$contact_no = $_POST['contact_no'][$key];
				$contact_abroad = $_POST['contact_abroad'][$key];
				$relationship = $_POST['relationship'][$key];
			

				$data = " c_csr_no = '$last_id' ";
				$data .= ", c_buyer_count = '$buyer_count' ";
				$data .= ", last_name = '$lastname' ";
				$data .= ", first_name = '$firstname' ";
				$data .= ", middle_name = '$middlename' ";
				$data .= ", suffix_name = '$suffixname' ";
				$data .= ", address = '$address' ";
				$data .= ", zip_code = '$zip_code' ";
				$data .= ", address_abroad = '$address_abroad ' ";
				$data .= ", birthdate = '$birthdate ' ";
				$data .= ", age = '$age ' ";
				$data .= ", viber = '$viber ' ";
				$data .= ", gender = '$gender' ";
				$data .= ", civil_status = '$civil_status' "; 
				$data .= ", citizenship = '$citizenship' ";
				$data .= ", id_presented = '$id_presented' "; 
				$data .= ", tin_no = '$tin_no' "; 
				$data .= ", email = '$email' "; 
				$data .= ", contact_no = '$contact_no' "; 
				$data .= ", contact_abroad = '$contact_abroad' "; 
				$data .= ", relationship = '$relationship' ";

				$save = $this->conn->query("INSERT INTO t_csr_buyers set ".$data);
				$buyer_count += 1;
				}


				$users_to_notify = array('IT Admin', 'SOS'); 

				foreach ($users_to_notify as $user) {
					$data_notif_values = array(
						"message = '$comm2 with CSR# $c_csr_no.'",
						"user_to_be_notified = '$user'",
						"seen = '0'"
					);

					$data_notif = implode(", ", $data_notif_values);

					$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
				}
		}
		
		if($save){
			$resp['status'] = 'success';
			if(empty($c_csr_no))
				$this->settings->set_flashdata('success',"New RA successfully saved.");
			else
				$this->settings->set_flashdata('success',"RA successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);

	}

	function delete_csr(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM t_csr where c_csr_no = ".$id);
		$del2 = $this->conn->query("DELETE FROM t_csr_buyers where c_csr_no = ".$id);
		$del3 = $this->conn->query("DELETE FROM t_csr_commission where c_csr_no = ".$id);
		if($del && $del2 && $del3){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"RA successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}

	function save_client(){
		extract($_POST);
		$data = " last_name = '$customer_last_name' ";
		$data .= ", first_name = '$customer_first_name' ";
		$data .= ", middle_name = '$customer_middle_name' ";
		$data .= ", suffix_name = '$customer_suffix_name' ";
		$data .= ", address = '$customer_address' ";
		$data .= ", zip_code = '$customer_zip_code' ";
		$data .= ", address_abroad = '$customer_address_2' ";
		$data .= ", birthdate = '$birth_day' ";
		$data .= ", age = '$customer_age' ";
		$data .= ", gender = '$customer_gender' ";
		$data .= ", viber = '$customer_viber' ";
		$data .= ", civil_status = '$civil_status' ";
		$data .= ", citizenship = '$citizenship' ";
		$data .= ", email = '$customer_email' ";
		$data .= ", contact_no = '$contact_no' ";
		$data .= ", c_created_by = '$username' ";

	/* 	$data_notif = " message = '$comm'";
		$data_notif .= ", user_to_be_notified = 'IT Admin' ";
		$data_notif .= ", seen = '0' "; */
		
		$check = $this->conn->query("SELECT * FROM `t_buyer_info` where `last_name` = '{$customer_last_name}' and
		 `first_name` = '{$customer_first_name}' and `middle_name` = '{$customer_middle_name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Buyer already exist.";
			return json_encode($resp);
			exit;
		} 
		if(empty($id)){
			/* $sql = "SELECT * FROM t_buyer_info"; */
			$sql = "INSERT INTO t_buyer_info set ".$data;
			//$sql = "INSERT INTO message_tbl set ".$data_notif;
			$save = $this->conn->query($sql);
		}else{
			/* $sql = "SELECT * FROM t_buyer_info"; */
			$sql = "UPDATE t_buyer_info set ".$data." where id = ".$id;
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Buyer successfully saved.");
			else
				$this->settings->set_flashdata('success',"Buyer successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}

	function sm_verification(){
		extract($_POST);
		// $sql = $this->conn->query("SELECT * FROM users where id=" .$type);
		// while($row = $sql->fetch_assoc()){
		// 	$usertype= $row['username'];
		// }
		// $users_to_notify = array('CFO', 'COO', 'IT Admin'); 

	 	$check = $this->conn->query("SELECT * FROM t_csr where c_verify = 1 and c_active = 1 and c_lot_lid ='{$lid}'")->num_rows;
		if($this->capture_err())
		 	return $this->capture_err();
		if($check > 0 && $value == 1){
			$resp['status'] = 'failed';
			$resp['msg'] = "Lot already verified.";
			return json_encode($resp);
			exit;
		} 
		if($check == 0){
			if ($value == 2){
				$save = $this->conn->query("UPDATE t_csr SET c_active = 0 where c_csr_no = ".$id);
			}

			$save = $this->conn->query("UPDATE t_csr SET c_verify = ".$value." where c_csr_no = ".$id);

			}
		else{
			if ($value == 2){
				$save = $this->conn->query("UPDATE t_csr SET c_active = 0 where c_csr_no = ".$id);
			}
			$save = $this->conn->query("UPDATE t_csr SET c_verify = ".$value." where c_csr_no = ".$id);
		}
		
		if($save){
			if($value == 1){
				// foreach ($users_to_notify as $user) {
				// 	$data_notif_values = array(
				// 	"message = '$usertype verified CSR #$id.'",
				// 	"user_to_be_notified = '$user'",
				// 	"seen = '0'"
				// 	);

				// 	$data_notif = implode(", ", $data_notif_values);

				// 	$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
				// 	}
				$resp['status'] = 'success';
			
				$this->settings->set_flashdata('success',"RA successfully verified.");
			}else{

				foreach ($users_to_notify as $user) {
					$data_notif_values = array(
					"message = '$usertype voided CSR #$id.'",
					"user_to_be_notified = '$user'",
					"seen = '0'"
					);

					$data_notif = implode(", ", $data_notif_values);

					$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
					}
				$update = $this->conn->query("UPDATE t_approval_csr SET c_csr_status = 3 where c_csr_no = ".$id);
				
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success',"RA successfully void.");
			}
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}



	function coo_approval(){
		extract($_POST);
	
		$data = " c_csr_no = '$id' ";
		$data .= ", c_lot_lid = '$lid' ";
		$data .= ", c_csr_status = '$value' ";
		$data .= ", c_reservation_amt = $reservation_amt "; 
		$data .= ", c_ca_status = '0' ";
		$data .= ", c_date_approved = CURRENT_TIMESTAMP() ";
		$data .= ", c_duration = DATE_ADD(CURRENT_TIMESTAMP(),INTERVAL $duration DAY)";

		$check = $this->conn->query("SELECT * FROM t_approval_csr where c_csr_no =".$id)->num_rows;
		if($check > 0){
		
			$save = $this->conn->query("UPDATE t_approval_csr set ".$data." where c_csr_no =".$id);
			$save2 = $this->conn->query("UPDATE t_csr SET coo_approval = ".$value." where c_csr_no = ".$id);

			if($save && $save2){
				$users_to_notify = array('Cashier', 'IT Admin'); 
				foreach ($users_to_notify as $user) {
				$data_notif_values = array(
				"message = '$type approved CSR #$id.'",
				"user_to_be_notified = '$user'",
				"seen = '0'"
				);

				$data_notif = implode(", ", $data_notif_values);

				$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
				}

				$resp['status'] = 'success';
			
				$this->settings->set_flashdata('success',"RA successfully approved.");
			}	
			else{
				$resp['status'] = 'failed';
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
			return json_encode($resp);
			exit;



		}else{
			$check2 = $this->conn->query("SELECT * FROM t_lots where c_status = 'Available' and c_lid =".$lid);
			if($check2->num_rows == 0){
				$resp['status'] = 'failed';
				$resp['msg'] = "Lot is not Available.";
				return json_encode($resp);
				exit;
			}else{
				$save = $this->conn->query("UPDATE t_lots set c_status = 'Pre-Reserved' where c_lid =".$check2->fetch_array()['c_lid']);
				$save2 = $this->conn->query("UPDATE t_csr SET coo_approval = ".$value." where c_csr_no = ".$id); 
				$save3 = $this->conn->query("INSERT INTO t_approval_csr set ".$data);
			}
		
			if($save &&  $save2 && $save3){
				$resp['status'] = 'success';
			
				$this->settings->set_flashdata('success',"RA successfully approved.");
			}	
			else{
				$resp['status'] = 'failed';
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
			return json_encode($resp);
			}
	


		}
		
	function extend_coo_approval(){
		extract($_POST);
		$data = "c_duration = DATE_ADD('$ext_duration',INTERVAL $duration DAY)";
		$save = $this->conn->query("UPDATE t_approval_csr set ".$data." where c_csr_no =".$id);
		if($save){
			// $sql = $this->conn->query("SELECT * FROM users where id=" .$type);
			// 	while($row = $sql->fetch_assoc()){
			// 		$usertype= $row['username'];
			// 	}
			// 	$users_to_notify = array('CFO', 'COO', 'IT Admin'); 
			// 	foreach ($users_to_notify as $user) {
			// 		$data_notif_values = array(
			// 		"message = '$usertype extended the approval of CSR #$id.'",
			// 		"user_to_be_notified = '$user'",
			// 		"seen = '0'"
			// 	);

			// 	$data_notif = implode(", ", $data_notif_values);

			// 	$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
			// 	}
				
			$resp['status'] = 'success';
		
			$this->settings->set_flashdata('success',"RA approval extend successfully.");
		}	
		else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
		exit;

	}	
	function coo_disapproval(){
		extract($_POST);
		$data = ", c_lot_lid = '$lid' ";
		$data .= ", c_csr_status = '$value' ";
		$check = $this->conn->query("SELECT * FROM t_approval_csr where c_csr_no =".$id)->num_rows;
		if($check > 0){
			$dis = $this->conn->query("UPDATE t_approval_csr set ".$data." where c_csr_no =".$id);
			$dis = $this->conn->query("UPDATE t_csr SET c_active = 0, coo_approval = ".$value." where c_csr_no = ".$id);
		}else{
			$dis = $this->conn->query("UPDATE t_csr SET c_active = 0, coo_approval = ".$value." where c_csr_no = ".$id);
		}

			// if($dis){
			// 	// $sql = $this->conn->query("SELECT * FROM users where id=" .$type);
			// 	// while($row = $sql->fetch_assoc()){
			// 	// 	$usertype= $row['username'];
			// 	// }
			// 	// $users_to_notify = array('CFO', 'COO', 'IT Admin'); 
			// 	// foreach ($users_to_notify as $user) {
			// 	// 	$data_notif_values = array(
			// 	// 	"message = '$usertype disapproved CSR #$id.'",
			// 	// 	"user_to_be_notified = '$user'",
			// 	// 	"seen = '0'"
			// 	// );

			// 	// $data_notif = implode(", ", $data_notif_values);

			// 	$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
			// 	}

			$resp['status'] = 'success';
		
			$this->settings->set_flashdata('success',"RA successfully disapproved.");
		// }else{
		// 	$resp['status'] = 'failed';
		// 	$resp['err'] = $this->conn->error."[{$sql}]";
		// }
		return json_encode($resp);
		
		
	}

	function cancel_approval(){
		extract($_POST);
		$check = $this->conn->query("SELECT * FROM t_approval_csr where c_csr_no =".$id)->num_rows;
		if($check > 0){
			$dis = $this->conn->query("UPDATE t_approval_csr set c_csr_status = 3, c_duration = CURRENT_TIMESTAMP() where c_csr_no =".$id);
			$dis2 = $this->conn->query("UPDATE t_csr SET c_active = 0, coo_approval = 3 where c_csr_no = ".$id);
			$update = $this->conn->query("UPDATE t_lots SET c_status = 'Available' WHERE c_lid = ".$lid);
		}

		if($dis && $dis2 && $update){
			$resp['status'] = 'success';
		
			$this->settings->set_flashdata('success',"RA successfully cancelled.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
		
		
	}

	function save_reservation(){
		extract($_POST);
		$data = " ra_no = '$ra_no' ";
		$data .= ", c_csr_no = '$csr_no' ";
		$data .= ", c_lot_id = '$lot_lid' ";
		$data .= ", c_or_no = '$or_no' ";
		$data .= ", c_reserve_date = CURRENT_TIMESTAMP() " ;
		$data .= ", c_amount_paid = '$amount_paid' ";


		if(empty($id)){
			
			$check = $this->conn->query("SELECT * FROM `t_reservation` where `c_or_no` = '{$or_no}'")->num_rows;
			if($this->capture_err())
				return $this->capture_err();
			if($check > 0){
				$resp['status'] = 'failed';
				$resp['msg'] = "OR number already exists!";
				
				return json_encode($resp);
				exit;
			} 
			$users_to_notify = array('IT Admin', 'CA'); 

			foreach ($users_to_notify as $user) {
				$data_notif_values = array(
					"message = '$comm$csr_no.'",
					"user_to_be_notified = '$user'",
					"seen = '0'"
				);

				$data_notif = implode(", ", $data_notif_values);

				$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
			}
			$save = $this->conn->query("INSERT INTO t_reservation set ".$data);
			
		}else{
			$users_to_notify = array('IT Admin', 'CA'); 

			foreach ($users_to_notify as $user) {
				$data_notif_values = array(
					"message = '$comm2 $csr_no.'",
					"user_to_be_notified = '$user'",
					"seen = '0'"
				);

				$data_notif = implode(", ", $data_notif_values);

				$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
			}
			$save = $this->conn->query("UPDATE t_reservation set ".$data." where id = ".$id);
		}

		$chk_pay = $this->conn->query("SELECT sum(c_amount_paid) as total_reservation FROM t_reservation where c_csr_no =".$csr_no);				
		while($row = $chk_pay->fetch_assoc()):
			$total = $row['total_reservation'];
				
			$data2 = ", c_amount_paid = '$total'";
			$data2 .= ",c_date_reserved = CURRENT_TIMESTAMP()";
			$data2 .= ", c_reserved_duration = DATE_ADD(CURRENT_TIMESTAMP(),INTERVAL 30 DAY)";
			$data2 .= ", c_ca_status = 0";


			if($total_res == $total){
				$check = $this->conn->query("UPDATE t_approval_csr SET c_reserve_status = 1 ".$data2."  where ra_id =".$ra_no);
				$check = $this->conn->query("UPDATE t_lots SET c_status = 'Reserved' where c_lid =".$lot_lid);
				
			}else if($total_res > $total && $total != 0){
				$check = $this->conn->query("UPDATE t_approval_csr SET c_reserve_status = 2 ".$data2." where ra_id =".$ra_no);
				$check = $this->conn->query("UPDATE t_lots SET c_status = 'Pre-Reserved' where c_lid =".$lot_lid);
			}else{
				$check = $this->conn->query("UPDATE t_approval_csr SET c_reserve_status = 0 ".$data2." where ra_id =".$ra_no);
				$check = $this->conn->query("UPDATE t_lots SET c_status = 'Pre-Reserved' where c_lid =".$lot_lid);
			}
			endwhile;
		
		
		if($check){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Reservation successfully saved.");
			else
				$this->settings->set_flashdata('success',"Reservation successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}

	function delete_reservation(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM t_reservation where id = ".$id);		
		$chk_pay = $this->conn->query("SELECT sum(c_amount_paid) as total_reservation FROM t_reservation where ra_no =".$ra_no);				
		while($row = $chk_pay->fetch_assoc()):
			$total = $row['total_reservation'];
			$chk_tot = $this->conn->query("SELECT c_reservation_amt FROM t_approval_csr where ra_id =".$ra_no);
			while($row2 = $chk_tot->fetch_assoc()):
				$total_res = $row2['c_reservation_amt'];
				endwhile;
			if($total_res == $total){
				$check = $this->conn->query("UPDATE t_approval_csr SET c_reserve_status = 1 , c_amount_paid = '$total', c_ca_status = 0 where ra_id =".$ra_no);
				$check2 = $this->conn->query("UPDATE t_lots SET c_status = 'Reserved' where c_lid =".$lid);
				
			}else if($total_res > $total && $total != 0){
				$check = $this->conn->query("UPDATE t_approval_csr SET c_reserve_status = 2 , c_amount_paid = '$total', c_ca_status = 0 where ra_id =".$ra_no);
				$check2 = $this->conn->query("UPDATE t_lots SET c_status = 'Pre-Reserved' where c_lid =".$lid);
			}else{
				$check = $this->conn->query("UPDATE t_approval_csr SET c_reserve_status = 0 , c_amount_paid = '$total', c_ca_status = 0 where ra_id =".$ra_no);
				$check2 = $this->conn->query("UPDATE t_lots SET c_status = 'Pre-Reserved' where c_lid =".$lid);
			}
			endwhile;

		if($del && $check && $check2){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Payment successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}

	
	function upload_file(){
		extract($_FILES);
		extract($_POST);
		$getID = $_POST['id'];
		$title = $_POST["title"];
		$pname = $_POST['getFileName'];
		$save = $this->conn->query("INSERT into tbl_vs_attachments(name,image) VALUES('$title','".$pname."')");
		
		if($save){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"File successfully uploaded.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);


	}
	function approved_upload(){
		extract($_POST);
		$id =  $_POST['id'];
		$csr_no =  $_POST['csr_no'];
		$ra_id =  $_POST['ra_id'];
	
		$save = $this->conn->query("UPDATE tbl_attachments SET approval_status = '1' where id=".$id);
		$save = $this->conn->query("UPDATE t_csr SET c_verify = 0, coo_approval = 0, c_revised = 1 where c_csr_no = ".$csr_no);  
		$save = $this->conn->query("UPDATE t_approval_csr SET c_csr_status = 0 where ra_id = ".$ra_id);
		if($save){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"File successfully approved.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}


	function delete_upload(){
		extract($_POST);
		$id =  $_POST['id'];
		
		$sql = $this->conn->query("SELECT * FROM tbl_attachments where id=" .$id);
		while($row = $sql->fetch_assoc()){
			$name = $row['name'];
		}
		$save = $this->conn->query("DELETE FROM tbl_attachments where id=" .$id);
		if($save){
			$resp['name'] = $name;
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"File successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	function ca_approval(){
		extract($_POST);

		// $sql = $this->conn->query("SELECT * FROM users where id=" .$type);
		// 	while($row = $sql->fetch_assoc()){
		// 		$usertype= $row['username'];
		// }
		// $users_to_notify = array('IT Admin', 'CFO'); 

		$data = " c_ca_status = '$value' ";
		if ($value == 1):
			$save = $this->conn->query("UPDATE t_approval_csr SET ".$data." where ra_id = ".$ra_id);
			//$save = $this->conn->query("UPDATE t_approval_csr SET ".$data." where ra_id = ".$ra_id);
			//$save = $this->conn->query("UPDATE t_csr SET c_verify = 1, coo_approval = 1, c_revised = 0 where c_csr_no = ".$id);
			$save = $this->conn->query("UPDATE t_csr SET c_verify = 1, coo_approval = 1 where c_csr_no = ".$id);

			// foreach ($users_to_notify as $user) {
			// 	$data_notif_values = array(
			// 		"message = '$usertype approved RA# $ra_id. (CA)'",
			// 		"user_to_be_notified = '$user'",
			// 		"seen = '0'"
			// 	);
			// 	$data_notif = implode(", ", $data_notif_values);
			// 	$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
			// }

		elseif ($value == 2):
			$save = $this->conn->query("UPDATE t_csr SET c_active = 0 where c_csr_no = ".$id);
			$save = $this->conn->query("UPDATE t_lots set c_status = 'Available' where c_lid =".$lot_id);
			$save = $this->conn->query("UPDATE t_approval_csr SET c_ca_status = ".$value." where ra_id = ".$ra_id);

			// foreach ($users_to_notify as $user) {
			// 	$data_notif_values = array(
			// 		"message = '$usertype disapproved RA# $ra_id. (CA)'",
			// 		"user_to_be_notified = '$user'",
			// 		"seen = '0'"
			// 	);
	
			// 	$data_notif = implode(", ", $data_notif_values);
			// 	$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
			//}
		elseif ($value == 3):
			$save = $this->conn->query("UPDATE t_csr SET c_verify = 0, coo_approval = 0, c_revised = 1 where c_csr_no = ".$id);
			$save = $this->conn->query("UPDATE t_approval_csr SET c_ca_status = ".$value." where ra_id = ".$ra_id);

			foreach ($users_to_notify as $user) {
				$data_notif_values = array(
					"message = '$usertype has already set RA #$ra_id for revision. (CA)'",
					"user_to_be_notified = '$user'",
					"seen = '0'"
				);
	
				$data_notif = implode(", ", $data_notif_values);
				$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
			}
		endif;

		


		if($save){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"RA successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function save_ca(){
		extract($_POST);
		$data = "";
		$doc_req1=  isset($_POST['doc_req1']) ? $doc_req1 : 0; 
		$doc_req2=  isset($_POST['doc_req2']) ? $doc_req2 : 0; 
		$doc_req3=  isset($_POST['doc_req3']) ? $doc_req3 : 0; 
		$ver_doc1=  isset($_POST['ver_doc1']) ? $ver_doc1 : 0; 
		$ver_doc2=  isset($_POST['ver_doc2']) ? $ver_doc2 : 0; 

		$data .= " c_csr_no = '$csr_no'";
		$data .= ", loan_amt = '$loan_amt'";
		$data .= ", terms = '$max_term'";
		$data .= ", gross_income = '$gross_income'"; 
		$data .= ", co_borrower = '$co_borrower'";
		$data .= ", total = '$total' ";
		$data .= ", income_req = '$income_req'";
		$data .= ", interest = '$int_rate' ";
		$data .= ", terms_month = '$term_rate' ";
		$data .= ", monthly = '$monthly' ";
		$data .= ", doc_req1 = $doc_req1";
		$data .= ", doc_req2 = $doc_req2";
		$data .= ", doc_req3 = $doc_req3";
		$data .= ", ver_doc1 = '$ver_doc1'";
		$data .= ", ver_doc2 = '$ver_doc2'";
		$data .= ", doc_req_remarks = '$remark_doc' ";
		$data .= ", ver_doc_remarks = '$remark_ver' ";

		if(empty($id)){
			$users_to_notify = array('IT Admin'); 
			foreach ($users_to_notify as $user) {
				$data_notif_values = array(
					"message = '$comm created an evaluation for CSR #$csr_no.'",
					"user_to_be_notified = '$user'",
					"seen = '0'"
				);

				$data_notif = implode(", ", $data_notif_values);

				$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
			}

			$save = $this->conn->query("INSERT INTO t_ca_requirement set ".$data);
		}else{
			$users_to_notify = array('IT Admin'); 
			foreach ($users_to_notify as $user) {
				$data_notif_values = array(
					"message = '$comm updated the evaluation for CSR #$csr_no.'",
					"user_to_be_notified = '$user'",
					"seen = '0'"
				);

				$data_notif = implode(", ", $data_notif_values);

				$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
			}
			$save = $this->conn->query("UPDATE t_ca_requirement set ".$data." WHERE id =".$id);
		}
		$id = !empty($id) ? $id : $this->conn->insert_id;
		$resp['status'] = 'success';
		$resp['id'] = $id;
		$resp['id_encrypt'] = md5($csr_no);
		$this->settings->set_flashdata('success',"Evaluation successfully saved.");
	
		return json_encode($resp);
	}

	function print_payment(){
		extract($_POST);
	
		$data = "";

		$data .= " client_id = '$client_id'";
		$data .= ", loan_amt = '$loan_amt'";
		$data .= ", terms = '$max_term'";
		$data .= ", gross_income = '$gross_income'"; 
		$data .= ", co_borrower = '$co_borrower'";
		$data .= ", total = '$total' ";
		$data .= ", income_req = '$income_req'";
		$data .= ", interest = '$int_rate' ";
		$data .= ", terms_month = '$term_rate' ";
		$data .= ", monthly = '$monthly' ";
		$data .= ", doc_req1 = $doc_req1";
		$data .= ", doc_req2 = $doc_req2";
		$data .= ", doc_req3 = $doc_req3";
		$data .= ", ver_doc1 = '$ver_doc1'";
		$data .= ", ver_doc2 = '$ver_doc2'";
		$data .= ", doc_req_remarks = '$remark_doc' ";
		$data .= ", ver_doc_remarks = '$remark_ver' ";

		if(empty($id)){
		
			$save = $this->conn->query("INSERT INTO t_ca_requirement set ".$data);
		}else{
			$save = $this->conn->query("UPDATE t_ca_requirement set ".$data." WHERE id =".$id);
		}
		$id = !empty($id) ? $id : $this->conn->insert_id;
		$resp['status'] = 'success';
		$resp['id'] = $id;
		$resp['id_encrypt'] = md5($csr_no);
		$this->settings->set_flashdata('success',"Evaluation successfully saved.");
	
		return json_encode($resp);
	}

	function cfo_booked(){

		extract($_POST);
		$sql = $this->conn->query("SELECT * FROM t_csr where c_csr_no =".$csr_no);
		while($row = $sql->fetch_array()):
			//lot computation
			$lot_lid = $row['c_lot_lid'];
			$lot_area = $row['c_lot_area'];
			$price_sqm = $row['c_price_sqm'];
			$lot_disc = $row['c_lot_discount'];
			$lot_disc_amt = $row['c_lot_discount_amt'];
			$house_model = $row['c_house_model'];
			$floor_area = $row['c_floor_area'];
			$h_price_per_sqm = $row['c_house_price_sqm'];
			$house_disc = $row['c_house_discount'];
			$house_disc_amt = $row['c_house_discount_amt'];
			$total_tcp = $row['c_tcp'];
			$tcp_disc = $row['c_tcp_discount'];
			$tcp_disc_amt = $row['c_tcp_discount_amt'];
			$vat_amt = $row['c_vat_amount'];
			$net_tcp = $row['c_net_tcp'];
			$type = $row['c_type'];

			// Payment Details
			$reservation = $row['c_reservation'];
			$payment_type1 = $row['c_payment_type1'];
			$payment_type2 = $row['c_payment_type2'];
			$down_percent = $row['c_down_percent'];
			$net_dp = $row['c_net_dp'];
			$no_payment = $row['c_no_payments'];
			$monthly_down = $row['c_monthly_down'];
			$first_dp_date = $row['c_first_dp'];
			$full_down_date = $row['c_full_down'];
			$amt_to_be_financed = $row['c_amt_financed'];
			$terms= $row['c_terms'];
			$interest_rate = $row['c_interest_rate'];
			$fixed_factor = $row['c_fixed_factor'];
			$monthly_amortization = $row['c_monthly_payment'];
			$start_date = $row['c_start_date'];
			$remarks = $row['c_remarks'];
			$active = $row['c_active'];
			$old_prop_id = $row['old_property_id'];

			$code = substr($lot_lid, 0, 3);
			$rev = $row['c_revised'];


			$qry = $this->conn->query("SELECT c_project_code FROM t_projects where c_code =".$code);
			$proj_code = $qry->fetch_array();

			$proj_id = $proj_code['c_project_code'];
			
			$data = " c_csr_no = '$csr_no' ";
			$data .= ", project_id = '$proj_id' ";
			$data .= ", c_type = '$type' ";
			$data .= ", c_account_status = 'Reservation' ";
			$data .= ", c_account_type = 'LOC' ";
			$data .= ", c_account_type1 = 'REG' ";
			$data .= ", c_lot_lid = '$lot_lid' ";
			$data .= ", c_lot_area = '$lot_area' ";
			$data .= ", c_price_sqm = '$price_sqm' ";
			$data .= ", c_lot_discount= '$lot_disc' ";
			$data .= ", c_lot_discount_amt = '$lot_disc_amt' ";
			$data .= ", c_house_model = '$house_model' ";
			$data .= ", c_floor_area= '$floor_area' ";
			$data .= ", c_house_price_sqm= '$h_price_per_sqm' ";
			$data .= ", c_house_discount = '$house_disc' ";
			$data .= ", c_house_discount_amt = '$house_disc_amt' ";
			$data .= ", c_tcp_discount = '$tcp_disc' ";
			$data .= ", c_tcp_discount_amt = '$tcp_disc_amt' ";
			$data .= ", c_tcp = '$total_tcp' ";
			$data .= ", c_vat_amount = '$vat_amt' ";
			$data .= ", c_net_tcp = '$net_tcp' ";
			$data .= ", c_reservation = '$reservation' ";
			$data .= ", c_payment_type1 = '$payment_type1' ";
			$data .= ", c_payment_type2 = '$payment_type2' ";
			$data .= ", c_down_percent = '$down_percent' ";
			$data .= ", c_net_dp = '$net_dp' ";
			$data .= ", c_no_payments = '$no_payment' ";
			$data .= ", c_monthly_down = '$monthly_down' ";
			$data .= ", c_first_dp = '$first_dp_date' ";
			$data .= ", c_full_down = '$full_down_date' ";
			$data .= ", c_amt_financed = '$amt_to_be_financed' ";
			$data .= ", c_terms = '$terms' ";
			$data .= ", c_interest_rate = '$interest_rate' ";
			$data .= ", c_fixed_factor = '$fixed_factor' ";
			$data .= ", c_monthly_payment = '$monthly_amortization' ";
			$data .= ", c_start_date = '$start_date' ";
			$data .= ", c_remarks = '$remarks' ";
			$data .= ", c_active = '$active' ";
			$balance = ($net_tcp - $reservation);
			$data .= ", c_balance = '$balance' ";
			


			if($rev == 2){
				$old_acct = $this->conn->query("SELECT c_date_of_sale from properties where property_id =".$old_prop_id);
				$old_data = $old_acct->fetch_array();
				$old_date_of_sale = $old_data['c_date_of_sale']; 
				$data .=", c_date_of_sale = '$old_date_of_sale' ";
				$data .= ", old_property_id = '$old_prop_id' ";

				$update = $this->conn->query("UPDATE properties set c_active='2' where property_id = ".$old_prop_id);
			}

			
			endwhile;

	/* 	$check2 = $this->conn->query("SELECT * FROM t_lots where c_status = 'Sold' and c_lid =".$lot_lid);
		if($check2->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Lot is already Sold";
			return json_encode($resp);
			exit;
		}else{
			$update = $this->conn->query("UPDATE t_lots set c_status = 'Sold' where c_lid =".$check2->fetch_array()['c_lid']);
		}
 */
		$update = $this->conn->query("UPDATE t_lots set c_status = 'Sold' where c_lid =".$lot_lid);
		$save99 = $this->conn->query("INSERT INTO properties set ".$data);
		
		$find =  $this->conn->query("SELECT property_id FROM properties where c_csr_no =".$csr_no);
		$row3 = $find->fetch_assoc();
		$new_property_id = $row3["property_id"];
		
	
		$payment_count = 1;
		$new_balance = $net_tcp;
		$qry_pay = $this->conn->query("SELECT * FROM t_reservation where c_csr_no =".$csr_no." order by c_reserve_date");
		while($pay_row = $qry_pay->fetch_array()):
			$pay_date = date("Y-m-d", strtotime($pay_row['c_reserve_date'])); 
			$due_date = date("Y-m-d", strtotime($pay_row['c_reserve_date'])); 
			$or_no = $pay_row['c_or_no'];
			$amount_paid =  $pay_row['c_amount_paid'];

			$new_balance -= $amount_paid;

			$data = " property_id = '$new_property_id' "; 
			$data .= ", pay_date = '$pay_date' "; 
			$data .= ", due_date = '$due_date' ";
			$data .= ", or_no = '$or_no' "; 
			$data .= ", amount_due = 0 "; 
			$data .= ", payment_amount = '$amount_paid' "; 
			$data .= ", rebate = 0 "; 
			$data .= ", surcharge = 0 "; 
			$data .= ", interest = 0 ";
			$data .= ", principal = '$amount_paid' ";
			$data .= ", status = 'RES' ";
			$data .= ", remaining_balance = '$new_balance' ";
			$data .= ", payment_count = '$payment_count' ";

			$save5 = $this->conn->query("INSERT INTO property_payments set ".$data);
			
			$payment_count += 1;
			
		endwhile;

		//echo $new_property_id;
		//save to client
		$sql2 = $this->conn->query("SELECT * FROM t_csr_buyers where c_csr_no =".$csr_no." order by c_buyer_count");
		while($row2 = $sql2->fetch_array()):
			$lastname = $row2['last_name'];
			$firstname = $row2['first_name'];
			$middlename = $row2['middle_name'];
			$suffixname = $row2['suffix_name']; 
			$address = $row2['address'];
			$zip_code = $row2['zip_code'];
			$address_abroad = $row2['address_abroad'];
			$birthdate = $row2['birthdate'];
			$age = $row2['age'];
			$viber = $row2['viber'];
			$gender = $row2['gender'];
			$civil_status = $row2['civil_status'];
			$citizenship = $row2['citizenship'];
			$id_presented = $row2['id_presented'];
			$tin_no = $row2['tin_no'];
			$email = $row2['email'];
			$contact_no = $row2['contact_no'];
			$contact_abroad = $row2['contact_abroad'];
			$relationship = $row2['relationship'];
			$buyer_count = $row2['c_buyer_count'];
		
		
			if ($buyer_count == 1):
				$i = 1;
				while($i== 1){
					$year = date("y");
					$birthdate = date("ymd", strtotime($birthdate));
					$random = sprintf("%05d", mt_rand(0, 99999));
					$client_id = $year . $birthdate . $random;
					if($this->conn->query("SELECT * FROM property_clients where client_id ='$client_id'")->num_rows <= 0)
						$i=0;
				}
				$data = " client_id= '$client_id'";
				$data .= ",property_id = '$new_property_id' ";
				$data .= ",c_buyer_count = '$buyer_count' ";
				$data .= ", last_name = '$lastname' ";
				$data .= ", first_name = '$firstname' ";
				$data .= ", middle_name = '$middlename' ";
				$data .= ", suffix_name = '$suffixname' ";
				$data .= ", address = '$address' ";
				$data .= ", zip_code = '$zip_code' ";
				$data .= ", address_abroad = '$address_abroad ' ";
				$data .= ", birthdate = '$birthdate ' ";
				$data .= ", age = '$age ' ";
				$data .= ", viber = '$viber ' ";
				$data .= ", gender = '$gender' ";
				$data .= ", civil_status = '$civil_status' "; 
				$data .= ", citizenship = '$citizenship' ";
				$data .= ", id_presented = '$id_presented' "; 
				$data .= ", tin_no = '$tin_no' "; 
				$data .= ", email = '$email' "; 
				$data .= ", contact_no = '$contact_no' "; 
				$data .= ", contact_abroad = '$contact_abroad' "; 
				$data .= ", relationship = '$relationship' ";

				$save2 = $this->conn->query("INSERT INTO property_clients set ".$data);

			elseif($buyer_count >= 2):

				$data = "client_id = '$client_id' ";
				$data .= ",c_buyer_count = '$buyer_count' ";
				$data .= ", last_name = '$lastname' ";
				$data .= ", first_name = '$firstname' ";
				$data .= ", middle_name = '$middlename' ";
				$data .= ", suffix_name = '$suffixname' ";
				$data .= ", address = '$address' ";
				$data .= ", zip_code = '$zip_code' ";
				$data .= ", address_abroad = '$address_abroad ' ";
				$data .= ", birthdate = '$birthdate ' ";
				$data .= ", age = '$age ' ";
				$data .= ", viber = '$viber ' ";
				$data .= ", gender = '$gender' ";
				$data .= ", civil_status = '$civil_status' "; 
				$data .= ", citizenship = '$citizenship' ";
				$data .= ", id_presented = '$id_presented' "; 
				$data .= ", tin_no = '$tin_no' "; 
				$data .= ", email = '$email' "; 
				$data .= ", contact_no = '$contact_no' "; 
				$data .= ", contact_abroad = '$contact_abroad' "; 
				$data .= ", relationship = '$relationship' ";

				$save3 = $this->conn->query("INSERT INTO family_members set ".$data);
			endif;

			endwhile;
			$users_to_notify = array('IT Admin'); 
			foreach ($users_to_notify as $user) {
				$data_notif_values = array(
					"message = '$comm booked CSR #$csr_no.'",
					"user_to_be_notified = '$user'",
					"seen = '0'"
				);
	
				$data_notif = implode(", ", $data_notif_values);

				$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
			}
		
		$save4 = $this->conn->query("UPDATE t_approval_csr set cfo_status = 1 where c_csr_no =".$csr_no);

		if($save2 && $save5 && $save4 && $save99 && $update){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"New Property successfully saved.");

		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		
		return json_encode($resp);


	}

	function save_payment(){
		extract($_POST);
		$sql = $this->conn->query("SELECT * FROM t_invoice where property_id =".$prop_id." ORDER by due_date, pay_date, payment_count, remaining_balance DESC");
		
		if($sql->num_rows <= 0){
			$resp['status'] = 'failed';
			$resp['err'] = 'No Payment Records yet! Please add to proceed with payments';
			return json_encode($resp);
        } 
		while($row = $sql->fetch_array()):	
			$prod_id = $row['property_id'];
			$amount_paid = $row['payment_amount'];
			$pay_date = $row['pay_date'];
			$due_date = $row['due_date'];
			$or_no_ent = $row['or_no'];
			$tot_amount_due = $row['amount_due'];
			$rebate = $row['rebate'];
			$surcharge = $row['surcharge'];
			$interest = $row['interest'];
			$principal = $row['principal'];
			$balance = $row['remaining_balance'];
			$status = $row['status'];
			$status_count = $row['status_count'];
			$payment_count = $row['payment_count'];
			//$excess = $row['excess'];
			$l_status = $row['account_status'];



			$data = " property_id = '$prop_id' ";
			$data .= ", payment_amount = '$amount_paid' ";
			$data .= ", pay_date = '$pay_date' ";
			$data .= ", due_date = '$due_date' ";
			$data .= ", or_no = '$or_no_ent' " ;
			$data .= ", amount_due = '$tot_amount_due' ";
			$data .= ", rebate = '$rebate' ";
			$data .= ", surcharge = '$surcharge' ";
			$data .= ", interest = '$interest' ";
			$data .= ", principal = '$principal' ";
			$data .= ", remaining_balance = '$balance' ";
			$data .= ", status = '$status' ";
			$data .= ", status_count = '$status_count' ";
			$data .= ", payment_count = '$payment_count' ";
			//$data .= ", excess = '$excess' ";
			//$data .= ", account_status = '$l_status' ";

			

			$save = $this->conn->query("INSERT INTO property_payments set ".$data);

		
			if ($l_status == ''){
					$l_sql = $this->conn->query("UPDATE properties SET c_balance = ".$balance." WHERE property_id = ".$prop_id);
			}else{
					$l_sql =  $this->conn->query("UPDATE properties SET c_account_status = '".$l_status."' , c_balance = ".$balance." WHERE property_id =".$prop_id);
					}
		endwhile;

		if($save && $l_sql){
			$delete = $this->conn->query("DELETE FROM t_invoice where property_id =".$prop_id);
			if ($delete){
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success',"New payments successfully saved.");
			}
			
		}else{
			$resp['status'] = 'failed';
			$this->settings->set_flashdata('failed',"Error!");
		}

		return json_encode($resp);
	}
	
	function add_payment(){
		extract($_POST);

		//$payments = $this->conn->query("SELECT due_date,pay_date, payment_amount,amount_due,surcharge,interest,principal,remaining_balance,status,status_count,payment_count FROM property_payments WHERE property_id = ".$prop_id." ORDER by due_date, pay_date, payment_count, remaining_balance DESC");
        $payments = $this->conn->query("SELECT due_date,pay_date, payment_amount,amount_due,surcharge,interest,principal,remaining_balance,status,status_count,payment_count, 0 AS excess, NULL as account_status, or_no FROM property_payments WHERE  property_id = ".$prop_id."  UNION SELECT due_date,pay_date,payment_amount,amount_due,surcharge,interest,principal,remaining_balance,status,status_count,payment_count,excess,account_status,or_no FROM t_invoice WHERE  property_id = ".$prop_id."  ORDER by due_date, pay_date, payment_count, remaining_balance DESC");
		$l_last = $payments->num_rows - 1;
        $payments_data = array(); 
        if($payments->num_rows <= 0){
            echo ('No Payment Records for this Account!');
        } 
        while($row = $payments->fetch_assoc()) {
          $payments_data[] = $row; 

        }
		
		$rebate = $rebate_amt;
        $last_cnt = $l_last;
        $payment_rec = $payments_data;
        $last_payment = $payments_data[$l_last];

		$amount_paid = (float) str_replace(",", "", $amount_paid);
		$tot_amount_due = (float) str_replace(",", "", $tot_amount_due);
		$surcharge = (float) str_replace(",", "", $surcharge);
		$rebate = (float) str_replace(",", "", $rebate);
		$status_count = $status_count ;
		$payment_count = $payment_count + 1;

		$to_principal_rbt = 0;
		$l_status = '';
		//rem set to zero for cts
		$rem_prcnt = 0;
		//echo $status;
	


		///////////////////////////////
		$invoice = $this->conn->query("SELECT * FROM t_invoice WHERE  property_id = ".$prop_id."");
		
		// $invoice_data = array(); 
		// if ($invoice->num_rows <= 0) {
		// 	echo ('No Payment Records for this Account!');
		// 	return;
		// } 

		$invoice_amount_sum = 0;
		while ($row = $invoice->fetch_assoc()) {
			$invoice_data[] = $row; 
			$invoice_amount_sum += $row['payment_amount'];
		}

    	//echo $invoice_amount_sum;

		if ((($payment_type1 == 'Partial DownPayment') && ($acc_status == 'Reservation') || ($acc_status == 'Partial DownPayment')) || ($payment_type1 == 'Full DownPayment') && ($acc_status == 'Full DownPayment') && ($acc_status == 'Partial DownPayment')){
			$rebate = 0;
			$interest = 0;
			if (($acc_status == 'Reservation') && ($status != 'FD')) {
				$status = 'PD - ' . strval($status_count);
				$l_status = 'Partial DownPayment';
			} elseif ($acc_status == 'Partial DownPayment') {
				if ($status == 'FD' && ($amount_paid >= $tot_amount_due)) {
					$status = 'FD';
					$l_status = 'Full DownPayment';
					$l_for_cts = 1;
				} elseif ($status == 'FD' && ($amount_paid) < $tot_amount_due) {
					$status = 'PD';
					$l_for_cts = 0;
					$l_status = 'Partial DownPayment';
				} else {
					$status = 'PD - ' . strval($status_count);
					$l_status = 'Partial DownPayment';
				}
			}

			if ($under_pay == 0) {
				if ($amount_paid <$tot_amount_due){
					if ($amount_paid <= $surcharge) {
						$principal = 0;
					} else {
						$principal = number_format($amount_paid - $surcharge,2);
					}
					$excess = -1;
				} elseif ($amount_paid > $tot_amount_due) {
					$excess = $amount_paid - $tot_amount_due;
					$amount_paid = $tot_amount_due;
					$or_no_ent = $or_no_ent;
					$principal = number_format($amount_paid - $surcharge,2);
				} else {
					$principal = number_format($amount_paid - $surcharge,2);
					$excess = -1;
				}
			} elseif ($under_pay == 1) {
				if ($amount_paid < $tot_amount_due){
					if ($amount_paid <= $surcharge) {
						$principal = 0;
					} else {
						$principal = number_format($amount_paid - $surcharge, 2);
						$l_surcharge = 0;
						$l_ampd = 0;
						for ($x = 0; $x < $payment_count - 1; $x++) {
						//for ($x = 0; $x <= $payment_count - 1; $x++) {
							try {
								if ($due_date == $payment_rec[$x]['due_date']) {
									$l_surcharge += $payment_rec[$x]['surcharge'];
									$l_ampd += $payment_rec[$x]['payment_amount'];
								}
							} catch(Exception $e) {
							// pass
							}
						}
					}
					if ($l_ampd < $l_surcharge):
						$l_sur_credit = $l_surcharge - $l_ampd;
					else:
						$l_sur_credit = 0;
					endif;
						
					if ($last_payment['payment_amount'] < $last_payment['surcharge']):
						$principal = $amount_paid - $surcharge - $l_sur_credit;
						$principal = number_format($principal, 2);
						if ($principal < 0):
							$principal = 0;
						endif;	
					else:
						if ($l_ampd < $l_surcharge):
							$principal = $amount_paid - $surcharge - $l_sur_credit;
							$principal = number_format($principal,2);
						else:
							$principal = $amount_paid - $surcharge;
							$principal = number_format($principal,2);
							
						endif;

					endif;

				}else if($amount_paid > $tot_amount_due){
					$excess = $amount_paid - $tot_amount_due;
					$amount_paid = $tot_amount_due;
					$or_no_ent = $or_no_ent;
					$principal = $monthly_pay;
				}else{
					$principal = $monthly_pay;
				}
			}
		$principal = floatval(str_replace(',', '',$principal));
		$balance = floatval(str_replace(',', '',$balance)) - $principal;
		
			
		}elseif ($payment_type1 == 'Spot Cash'){
			$rebate = 0;
			$surcharge = 0;
			$interest = 0;
			$principal = floatval(str_replace(',', '',$amount_paid));
			$balance = floatval(str_replace(',', '',$balance));
			$balance = $balance - $principal;
			if ($amount_paid == floatval($tot_amount_due)) {
					$status = 'FPD';
					$l_status = 'Fully Paid';
			} else {
					$status = 'SC';
			}
			$excess = -1;
			if (floatval($balance) <= $rem_prcnt) {
				$l_for_cts = 1;
			}
	
		}elseif ($payment_type1 == 'Full DownPayment' && $acc_status == 'Reservation'){
			$rebate = 0;
			$interest = 0;
			$amount_paid = floatval(str_replace(',', '',$amount_paid));
			$balance = floatval(str_replace(',', '',$balance));
			if ($amount_paid == floatval($tot_amount_due)) {
				$principal = $amount_paid - floatval($surcharge);
				$l_status = 'Full DownPayment';
				$status = 'FD';
				$l_for_cts = 1;
				$excess = -1;
			} elseif ($amount_paid > floatval($tot_amount_due)) {
				$excess = $amount_paid - floatval($tot_amount_due);
				$amount_paid = $tot_amount_due;
				$or_no_ent = $or_no_ent;
				$principal =$amount_paid - floatval($surcharge);
				$l_status = 'Full DownPayment';
				$status = 'FD';
				$l_for_cts = 1;
			} elseif ($amount_paid < floatval($tot_amount_due)) {
				$principal = $amount_paid - floatval($surcharge);
				$l_status = 'Partial DownPayment';
				$status = 'PFD';
				$excess = -1;
			}	
			$balance =$balance - $principal;
			

		}elseif (($acc_status == 'Full DownPayment' && $payment_type2 == 'Deferred Cash Payment') || ($payment_type1 == 'No DownPayment' && $payment_type2 == 'Deferred Cash Payment') || $acc_status == 'Deferred Cash Payment') {
			$rebate = 0;
			$interest = 0;
			$amount_paid = floatval(str_replace(',', '',$amount_paid));
			$balance = floatval(str_replace(',', '',$balance));

			if ($under_pay == 0) {
				if ($amount_paid < $tot_amount_due) {
					if ($amount_paid <= $surcharge) {
						$principal = 0;
					} else {
						$principal = number_format($amount_paid - $surcharge,2);
					}
					$excess = -1;
				} elseif ($amount_paid > $tot_amount_due) {
					if ($status == 'FPD') {
						$excess = $amount_paid - $tot_amount_due;
						$or_no_ent = $or_no_ent;
						$principal = number_format($amount_paid - $surcharge,2);
					} else {
						$excess = $amount_paid - $tot_amount_due;
						$amount_paid = $tot_amount_due;
						$or_no_ent = $or_no_ent;
						$principal = number_format($amount_paid - $surcharge,2);
					}
				} else {
					$principal = number_format($amount_paid - $surcharge,2);
					$excess = -1;
				}
			}elseif ($under_pay == 1) {
				if ($amount_paid < $tot_amount_due) {
					if ($amount_paid <= $surcharge) {
						$principal = 0;
					} else {
						$l_surcharge = 0;
						$l_ampd = 0;
						for ($x = 0; $x < $payment_count - 1; $x++) {
						//for ($x = 0; $x <= $payment_count - 1; $x++) {
							try {
								if ($due_date == $payment_rec[$x]['due_date']) { 
									$l_surcharge += $payment_rec[$x]['surcharge'];
									$l_ampd += $payment_rec[$x]['payment_amount'];
								}
							} catch (Exception $e) {
								//pass
							}
						}
						if ($l_ampd < $l_surcharge) {
							$l_sur_credit = $l_surcharge - $l_ampd;
						} else {
							$l_sur_credit = 0;
						}
						if ($last_payment['payment_amount'] < $last_payment['surcharge']) {
							$principal = number_format($amount_paid - $surcharge - $l_sur_credit, 2);
							if ($principal < 0) {
								$principal = 0;
							}
						} else {
							$principal = number_format($amount_paid - $surcharge, 2);
							if ($l_ampd < $l_surcharge) {
								$principal = number_format($amount_paid - $surcharge - $l_sur_credit, 2);
							} else {
								$principal = number_format($amount_paid - $surcharge, 2);
								
							}
						}
						$excess = -1;
					}
				}elseif ($amount_paid > $tot_amount_due) {
					if ($status == 'FPD') {
						$excess = $amount_paid - $tot_amount_due;
						$or_no_ent = $or_no_ent;
						$principal = number_format($amount_paid- $surcharge, 2);
					} else {
						$excess = $amount_paid - $tot_amount_due;
						$amount_paid = $tot_amount_due;
						$or_no_ent = $or_no_ent;
						$principal = number_format($monthly_pay, 2);
					}
				} else {
					$principal = number_format($monthly_pay, 2);
					$excess = -1;
				}
			}
			$principal = floatval(str_replace(',', '',$principal));
			$balance = floatval(str_replace(',', '',$balance));
			$balance = $balance - $principal;
		
			if ($acc_status == 'Reservation' || $acc_status == 'Full DownPayment') {
				if (($status == 'FPD' && ($amount_paid >= $tot_amount_due) || $balance <= 0)) {
					$status = 'FPD';
					$l_status = 'Fully Paid';
				} else {
					$status = 'DFC - ' . strval($status_count);
					$l_status = 'Deferred Cash Payment';
				}
			} elseif ($acc_status == 'Deferred Cash Payment') {
				if (($status == 'FPD' && ($amount_paid >= $tot_amount_due) || $balance <= 0)) {
					$status = 'FPD';
					$l_status = 'Fully Paid';
				} else {
					$status = 'DFC - ' . strval($status_count);
					$l_status = 'Deferred Cash Payment';
				}
			}
		}elseif (($acc_status == 'Full DownPayment' && $payment_type2 == 'Monthly Amortization') || ($payment_type1 == 'No DownPayment' && $payment_type2 == 'Monthly Amortization') || $acc_status == 'Monthly Amortization'){
			$interest = 0;
			$balance = floatval(str_replace(',', '',$balance));
			if ($under_pay == 0){
				if ($amount_paid < $tot_amount_due) {
					$rebate = 0;
					$l_interest = ($balance * ($interest_rate/1200));
					if ($amount_paid <= $surcharge):
						$interest = 0;
						$principal = 0;
					elseif (($amount_paid - $surcharge) >= $l_interest):
						$interest = $l_interest;
						$principal = $amount_paid - $interest - $surcharge;
					else:
						$interest = ($amount_paid) - ($surcharge);
						$principal = 0;
					endif;
					$excess = -1;
				}elseif (($amount_paid) > ($tot_amount_due)){
					if($over_due_mode == 0 and $to_principal_rbt == 1):
					//if($over_due == 0):
						$excess = -1;
						$interest =  ((($balance) * ($interest_rate/1200)));
						$principal = (($amount_paid) - ($interest) - ($surcharge));
						$principal_new = ($monthly_pay - ($interest) - ($surcharge) - ($rebate));
						$l_excess_amount = ($amount_paid) - ($tot_amount_due);
						if (($principal_new) < 0): 
							$neg_prin = 1; 
							$principal = 0; 
							$amount_paid = $tot_amount_due;
						endif;
					
					else:
						if ($status == 'FPD'):
							$excess = $amount_paid;
							$or_no_ent = $or_no_ent;
							$interest = ($balance * ($interest_rate/1200));
							$principal = ($amount_paid - $interest - $rebate);
						else:
							$excess = ($amount_paid - $tot_amount_due);
							$amount_paid = $tot_amount_due;
							$or_no_ent = $or_no_ent;
							//echo $balance;
							// echo $interest_rate;
							$interest = ($balance * ($interest_rate/1200));
							$principal = ($monthly_pay - $interest - $rebate);
							if (($principal) < 0):
								$neg_prin = 1;
								$principal = 0;
							endif;
						endif;
					endif;
				}else {
						$interest = $balance * ($interest_rate/1200);
						if ($status == 'FPD'):
							$principal = $monthly_pay - $interest - $rebate;
						else:
							$principal = $monthly_pay - $interest - $rebate;
							if ($principal < 0):
								$neg_prin = 1;
								$principal = 0;
							endif;
						endif;
						$excess = 0;
					}
			}elseif ($under_pay == 1){
				if ($amount_paid < $tot_amount_due) {
					$excess = -1;
					$rebate = 0;
					$l_interest = $ma_balance * ($interest_rate/1200);
					$l_surcharge = 0;
					$l_ampd = 0;
					// get last total principal
					//echo $payment_count;
					//echo $payment_count;
					for ($x = 0; $x < $payment_count -1; $x++) {
						try {
							//echo $payment_rec[$x]['payment_amount'];
							if ($due_date == $payment_rec[$x]['due_date']) { 
								$l_surcharge += $payment_rec[$x]['surcharge'];
								$l_ampd += $payment_rec[$x]['payment_amount'];
							}
						} catch (Exception $e) {
							//pass
						}
					}

					
					if ($l_ampd < $l_surcharge):
						$l_sur_credit = $l_surcharge - $l_ampd;
					else:
						$l_sur_credit = 0;
					endif;
					if ($last_interest < $l_interest):
						
						if ($amount_paid <= $surcharge):
							$interest = 0;
							$principal = 0;
						else:
							
							$l_bal_interest = $l_interest - $last_interest;
							if ($last_payment['payment_amount'] < $last_payment['surcharge']):
								if (($tot_amount_due - $surcharge - $l_sur_credit) >= $l_bal_interest):
									$interest = $l_bal_interest;
									$principal = $amount_paid - $interest - $surcharge - $l_sur_credit;
									if ($principal <= 0):
										$principal = 0;
									endif;
								else:
									$interest = ($amount_paid - $surcharge - $l_sur_credit);
									$principal = 0;
									if ($interest < 0) {
										$interest = 0;
									}
								endif;
							else:
								if (($amount_paid - $surcharge) >= $l_bal_interest) {
									$l_rem = $amount_paid - $surcharge - $l_sur_credit;
									$interest = $l_bal_interest;
									if ($l_rem < $interest) {
										$interest = $l_rem;
									}
									$principal = $amount_paid - $interest - $surcharge;
									if ($l_ampd < $l_surcharge) {
										$principal = $amount_paid - $interest - $surcharge - $l_sur_credit;
									} else {
										$principal = $amount_paid - $interest - $surcharge;
									}
									if ($principal <= 0) {
										$principal = 0;
									}
									if ($interest < 0) {
										$interest = 0;
									}
								} else {
									$interest = $amount_paid - $surcharge;
									$principal = 0;
								}
							endif;
						endif;
					
					else:
						
						$interest = 0;
						if ($amount_paid <= $surcharge):
							$principal = 0;
						else:
							if ($l_ampd < $l_surcharge):
								$principal = $amount_paid - $surcharge - $l_sur_credit;
								if ($principal <= 0):
									$principal = 0;
								endif;
							else:
								$principal = ($amount_paid - $surcharge);
								
							endif;
						endif;
					endif;

				}elseif ($amount_paid > $tot_amount_due) {
					if (($over_due_mode == 0) && ($to_principal_rbt == 1)):
						$excess = -1;
						$l_interest = ($ma_balance * ($interest_rate/1200));
						if ($last_interest < $l_interest):
							$interest = $l_interest - $last_interest;
						else:
							$interest = 0;
						endif;
						$principal = $amount_paid - $interest - $surcharge;
					else:
						$excess = $amount_paid - $tot_amount_due;
						$amount_paid = $tot_amount_due;
						$or_no_ent = $or_no_ent;
						$l_interest = $ma_balance * ($interest_rate/1200);
						if ($last_interest < $l_interest):
							$interest = $l_interest - $last_interest;
							$principal = $monthly_pay - $interest;
							if ($status == 'FPD'):
								$principal = ($amount_paid - $interest - $rebate);
							endif;

						else:
							$interest = 0;
							$principal = $monthly_pay;
							if ($status == 'FPD'):
								$principal = ($amount_paid - $interest - $rebate);
							endif;
						
						endif;
					endif;
				}else{
					
					$l_interest = ($ma_balance * ($interest_rate/1200));
					if ($last_interest < $l_interest) {
						$interest = $l_interest - $last_interest;
						if ($rebate != 0) {
							$principal = $monthly_pay - $interest - $rebate;
						} else {
							$principal = $monthly_pay - $interest;
						}
					} else {
						$interest = 0;
						$principal = $monthly_pay;
					}
					$excess = 0;
				}
			}
			
			$balance = $balance - $principal - $rebate;
			if (($acc_status == 'Reservation') || ($acc_status == 'Full DownPayment')):
				if (($status == 'FPD') && ($amount_paid >= $tot_amount_due) || ($balance <= 0)):
					$status = 'FPD';
					$l_status = 'Fully Paid';
				else:
					$status = 'MA - ' .strval($status_count);
					$l_status = 'Monthly Amortization';
				endif;
			elseif($acc_status == 'Monthly Amortization'):
				if (($status == 'FPD') && ($amount_paid >= $tot_amount_due) || ($balance <= 0)):
					$status = 'FPD';
					$l_status = 'Fully Paid';
				else:
					$status = 'MA - ' .strval($status_count);
					$l_status = 'Monthly Amortization';
				endif;
			endif;

		
			// lagay ako dito condition para sa payment of balance

						//$principal = $amount_paid - $rebate;
						//$interest = 0;
					///

		}
		if ($retention == '1'){
			$status = 'RETENTION';

		}


		$data = " property_id = '$prop_id' ";
		$data .= ", payment_amount = '$amount_paid' ";
		$data .= ", pay_date = '$or_date_ent' ";
		$data .= ", due_date = '$due_date' ";
		$data .= ", or_no = '$or_no_ent' " ;
		$data .= ", amount_due = '$tot_amount_due' ";
		$data .= ", rebate = '$rebate' ";
		$data .= ", surcharge = '$surcharge' ";
		$data .= ", interest = '$interest' ";
		$data .= ", principal = '$principal' ";
		$data .= ", remaining_balance = '$balance' ";
		$data .= ", status = '$status' ";
		$data .= ", status_count = '$status_count' ";
		$data .= ", payment_count = '$payment_count' ";
		$data .= ", excess = '$excess' ";
		$data .= ", account_status = '$l_status' ";
		$data .= ", trans_date = '$trans_date_ent' ";
		$data .= ", surcharge_percent = '$sur_percent' ";


		$amt_paid=$_POST['amount_paid'];
		$amt_paid = floatval(str_replace(',', '',$amt_paid));
		$amt_due = floatval(str_replace(',', '',$tot_amount_due));
				
		$check = $this->conn->query("SELECT * FROM `t_invoice` where `or_no` != '{$or_no_ent}' and property_id =".$prop_id )->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "OR number needs to be the same!";
			return json_encode($resp);
			exit;
		} 

		$check = $this->conn->query("SELECT * FROM `property_payments` where `or_no` = '{$or_no_ent}'")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "OR number already exists!";
			return json_encode($resp);
			exit;
		} 

		$save = $this->conn->query("INSERT INTO t_invoice set ".$data);

		$resp['data'] = array(
			'property_id' => $prop_id,
			'payment_amount' => $amount_paid,
			'pay_date' => $or_date_ent,
			'due_date' => $due_date,
			'or_no' => $or_no_ent,
			'amount_due' => $tot_amount_due,
			'rebate' => $rebate,
			'surcharge' => $surcharge,
			'interest' => $interest,
			'principal' => $principal,
			'remaining_balance' => $balance,
			'status' => $status,
			'status_count' => $status_count,
			'payment_count' => $payment_count,
			'excess' => $excess,
			'trans_date' => $trans_date_ent,
			'surcharge_percent'=>  $sur_percent
		  );
		
		if($resp['data'] ){
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		
		return json_encode($resp);
	}

	function delete_payment(){
		extract($_POST);
		$qry = "SELECT * FROM property_payments where property_id =".$prop_id." ORDER by payment_count";
		$sql = $this->conn->query($qry);
		$l_last = $sql->num_rows - 1;
		$payments_data = array(); 
		if($sql->num_rows <= 0){
			$resp['status'] = 'failed';
			$resp['err'] = 'No Payment Records yet!';
			return json_encode($resp);
        } 
		while($row = $sql->fetch_assoc()) {
		  $payments_data[] = $row; 

		}
		$last_row = $payments_data[$l_last];
		$l_bal = $last_row['remaining_balance'];
		$l_last_due_date = $last_row['due_date'];
		$l_last_pay_date = $last_row['pay_date'];
		$l_last_amt_paid = $last_row['payment_amount'];
		$l_last_amt_due = $last_row['amount_due'];
		$l_last_sur = $last_row['surcharge'];
		$l_last_int = $last_row['interest'];
		$l_last_status = $last_row['status'];
		$l_last_stat_cnt = $last_row['status_count'];
		$l_last_rebate = $last_row['rebate'];
		$l_last_prin = $last_row['principal'];
		$l_last_or_no = $last_row['or_no'];
		$l_last_pay_cnt = $last_row['payment_count'];

		if ($l_last_status == 'RETENTION' || $l_last_status == 'RECOMPUTED' || $l_last_status == 'ADDITIONAL' || $l_last_status == 'RESTRUCTURED'){
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		
		}

		$query = "DELETE FROM or_logs WHERE pay_date = '".$l_last_pay_date."' and or_no ='".$l_last_or_no."' and property_id = '".$prop_id."'";
		$query1 = "DELETE FROM property_payments WHERE pay_date = '".$l_last_pay_date."' and or_no ='".$l_last_or_no."' and property_id = '".$prop_id."'";
		
		$delete = $this->conn->query($query);
		$delete1 = $this->conn->query($query1);

		$query_payments = "SELECT * FROM property_payments where property_id =".$prop_id." ORDER by payment_count DESC";
		$qry_pay = $this->conn->query($query_payments);
		while($pay = $qry_pay->fetch_assoc()){
			$l_status = substr($pay['status'],0,4);
			$l_bal = $pay['remaining_balance'];
			if ($l_status == 'MA -'):
					$l_status1 = 'Monthly Amortization';
					$update  = "UPDATE properties SET c_account_status = '$l_status1', c_balance = '$l_bal' WHERE property_id ='$prop_id'";
					$updating = $this->conn->query($update);
					break;
			elseif($l_status == 'DFC'):
					$l_status1 = 'Deferred Cash Payment';
					$update  = "UPDATE properties SET c_account_status = '$l_status1', c_balance = '$l_bal' WHERE property_id ='$prop_id'";
					$updating = $this->conn->query($update);
					break;
			elseif($l_status == 'FD'):
					$l_status1 = 'Full DownPayment';
					$update  = "UPDATE properties SET c_account_status = '$l_status1', c_balance = '$l_bal' WHERE property_id ='$prop_id'";
					$updating = $this->conn->query($update);
					break;
			elseif($l_status == 'RES'):
					$l_status1 = 'Reservation';
					$update  = "UPDATE properties SET c_account_status = '$l_status1', c_balance = '$l_bal' WHERE property_id ='$prop_id'";
					$updating = $this->conn->query($update);
					break;
			elseif($l_status == 'PD -' or $l_status == 'PD'):
					$l_status1 = 'Partial DownPayment';	
					$update  = "UPDATE properties SET c_account_status = '$l_status1', c_balance = '$l_bal' WHERE property_id ='$prop_id'";
					//echo $update;
					$updating = $this->conn->query($update);
					break;	
			elseif($l_status == 'C PR' or $l_status == 'DISC' or $l_status == 'FPD/'):
					continue;
			else:
				continue;
			endif;		
		}

		$updating = $this->conn->query($update);

		

		if ($delete && $delete1 && $updating) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Last OR deleted successfully.");	
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		
		return json_encode($resp);
	}


	function undo_delete_payment(){
		extract($_POST);

		$qry = ("SELECT * FROM property_payments where property_id =".$prop_id." ORDER by payment_count");
		$sql = $this->conn->query($qry);
		$l_last = $sql->num_rows - 1;
		$payments_data = array(); 
		if($sql->num_rows <= 0){
			$resp['status'] = 'failed';
			$resp['err'] = 'No Payment Records yet!';
			return json_encode($resp);
        } 

		while($row = $sql->fetch_assoc()) {
		  $payments_data[] = $row;
		}
		$last_row = $payments_data[$l_last]; 
		$pay_date = $last_row['pay_date'];
		$or_no_ent = $last_row['or_no'];

		$qry2 = ("SELECT * FROM property_payments where property_id =".$prop_id." and pay_date = '".$pay_date."' and or_no ='".$or_no_ent."' ORDER by  payment_count");
		$sql = $this->conn->query($qry2);
		#$l_last = $sql->num_rows - 1;
		$payments_data = array(); 
		if($sql->num_rows <= 0){
			$resp['status'] = 'failed';
			$resp['err'] = 'No Payment Records yet!';
			return json_encode($resp);
        } 

		while($row = $sql->fetch_assoc()) {
		  $payments_data[] = $row; 
		  #$last_row = $payments_data[$l_last];
		  $prod_id = $row['property_id'];
		  $amount_paid = $row['payment_amount'];
		  $pay_date = $row['pay_date'];
		  $due_date = $row['due_date'];
		  $or_no_ent = $row['or_no'];
		  $tot_amount_due = $row['amount_due'];
		  $rebate = $row['rebate'];
		  $surcharge = $row['surcharge'];
		  $interest = $row['interest'];
		  $principal = $row['principal'];
		  $balance = $row['remaining_balance'];
		  $status = $row['status'];
		  $status_count = $row['status_count'];
		  $payment_count = $row['payment_count'];
		  $excess = $row['excess'];
		  $l_status = $row['account_status'];
		  $date = date('Y-m-d');

		    if ($status == 'RETENTION' || $status == 'RECOMPUTED' || $status == 'ADDITIONAL' || $status == 'RESTRUCTURED'){
				$resp['status'] = 'failed';
				$resp['err'] = $this->conn->error."[{$sql}]";
				return json_encode($resp);
			}
			if ($pay_date != $date){
				$resp['status'] = 'failed';
				$resp['err'] = $this->conn->error."[{$sql}]";
				return json_encode($resp);
			}
		
			$query1 = "INSERT INTO t_invoice (property_id,payment_amount,pay_date,due_date,or_no,amount_due,rebate,surcharge,interest,principal,remaining_balance,status,status_count,payment_count,excess,account_status)VALUES($prod_id,$amount_paid,'$pay_date','$due_date',$or_no_ent,$tot_amount_due,$rebate,$surcharge,$interest,$principal,$balance,'$status',$status_count,$payment_count,$excess,'$l_status')";
			$save1 = $this->conn->query($query1);

			if ($l_status == ''){
					$l_sql = $this->conn->query("UPDATE properties SET c_balance = ".$balance." WHERE property_id = ".$prop_id);
			}else{
					$l_sql =  $this->conn->query("UPDATE properties SET c_account_status = '".$l_status."' WHERE property_id =".$prop_id);
			}

		}


		$query_payments = "SELECT * FROM property_payments where property_id =".$prop_id." ORDER by payment_count";
		$qry_pay = $this->conn->query($query_payments);
		while($pay = $qry_pay->fetch_assoc()){
			$l_status = substr($pay['status'],0,4);
			$l_bal = $pay['remaining_balance'];
			if ($l_status == 'MA -'):
					$l_status1 = 'Monthly Amortization';
					$update  = "UPDATE properties SET c_account_status = '$l_status1', c_balance = '$l_bal' WHERE property_id ='$prop_id'";
					$updating = $this->conn->query($update);
					break;
			elseif($l_status == 'DFC'):
					$l_status1 = 'Deferred Cash Payment';
					$update  = "UPDATE properties SET c_account_status = '$l_status1', c_balance = '$l_bal' WHERE property_id ='$prop_id'";
					$updating = $this->conn->query($update);
					break;
			elseif($l_status == 'FD'):
					$l_status1 = 'Full DownPayment';
					$update  = "UPDATE properties SET c_account_status = '$l_status1', c_balance = '$l_bal' WHERE property_id ='$prop_id'";
					$updating = $this->conn->query($update);
					break;
			elseif($l_status == 'RES'):
					$l_status1 = 'Reservation';
					$update  = "UPDATE properties SET c_account_status = '$l_status1', c_balance = '$l_bal' WHERE property_id ='$prop_id'";
					$updating = $this->conn->query($update);
					break;
			elseif($l_status == 'PD -' or $l_status == 'PD'):
					$l_status1 = 'Partial DownPayment';	
					$update  = "UPDATE properties SET c_account_status = '$l_status1', c_balance = '$l_bal' WHERE property_id ='$prop_id'";
					//echo $update;
					$updating = $this->conn->query($update);
					break;	
			elseif($l_status == 'C PR' or $l_status == 'DISC' or $l_status == 'FPD/'):
					continue;
			else:
				continue;
			endif;		
		}

		$updating = $this->conn->query($update);


		if($save1 && $l_sql && $updating){
			$delete = $this->conn->query("DELETE FROM property_payments WHERE pay_date = '".$pay_date."' and or_no ='".$or_no_ent."' and property_id = '".$prop_id."'");
			if ($delete){
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success',"Last action successfully undone.");
			}
			
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}

		return json_encode($resp);
	}
	
	
	function delete_invoice(){
		
		extract($_POST);

		$rowId = $_POST['rowId'];

		$del = $this->conn->query("DELETE FROM t_invoice where invoice_id = ".$rowId);
		if($del){
			$resp['status'] = 'success';
			//$this->settings->set_flashdata('success',"Row successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$del}]";
		}
		return json_encode($resp);	
	}

	function delete_all_invoice(){
		extract($_POST);

		$del_all = $this->conn->query("DELETE FROM t_invoice where property_id = ".$prop_id);
		if($del_all){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"All Invoice successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$del}]";
		}
		return json_encode($resp);	
	}
	
	function credit_principal(){
		extract($_POST);

		$monthly_pay = (float) str_replace(",", "", $monthly_pay);
		//echo $monthly_pay;
		$amount_paid = (float) str_replace(",", "", $amount_paid);
		$tot_amount_due = (float) str_replace(",", "", $tot_amount_due);
		$balance = (float) str_replace(",", "", $balance);
		$rebate = (float) str_replace(",", "", $rebate_amt);
	
		$surcharge = 0;
		$interest = 0;

		$datetime1 = new DateTime($due_date);
		$datetime2 = new DateTime($trans_date_ent);
		$interval = $datetime1->diff($datetime2);
		$l_days = $interval->days;

		//echo $l_days;

		if ($retention == 0){

	

			if ($amount_paid < ($monthly_pay * 3)){
				$mustbe = number_format($monthly_pay * 3,2);
				$resp['status'] = 'failed';
				$resp['msg'] = "Credit Principal Amount must be P " . $mustbe . " or more." ;
				return json_encode($resp);
			}
			
			if($datetime2 > $datetime1){
				if($l_days >= 30){
					$resp['status'] = 'failed';
					$resp['msg'] = "Account is not Full Update cannot insert into Principal !!" ;
					return json_encode($resp);
				}
			}


			if($tot_amount_due == $amount_paid){
				if ($status == 'Payment of Balance'){
					$status = 'C PRIN';
				}
			}
	
			if ($status == 'Credit to Principal'){
				$status = 'C PRIN';
			}

		}else{
			
			if ($status == 'RETENTION'){
				$status = 'RETENTION';
			}

		}
		
		$principal = $amount_paid + $rebate;
		$balance = $balance - $principal;
		$status_count = $status_count ;
		$payment_count = $payment_count + 1;
		//$l_status = '';
		if ($balance <= 0 ){
			$status = 'FPD/'. $status;
			$acc_status = 'Fully Paid';
			}



		$data = " property_id = '$prop_id' ";
		$data .= ", payment_amount = '$amount_paid' ";
		$data .= ", pay_date = '$or_date_ent' ";
		$data .= ", due_date = '$due_date' ";
		$data .= ", or_no = '$or_no_ent' " ;
		$data .= ", amount_due = '$amount_paid' ";
		$data .= ", rebate = '$rebate' ";
		$data .= ", surcharge = '$surcharge' ";
		$data .= ", interest = '$interest' ";
		$data .= ", principal = '$principal' ";
		$data .= ", remaining_balance = '$balance' ";
		$data .= ", status = '$status' ";
		$data .= ", status_count = '$status_count' ";
		$data .= ", payment_count = '$payment_count' ";
		$data .= ", excess = '$excess' ";
		$data .= ", account_status = '$acc_status' ";
		$data .= ", trans_date = '$trans_date_ent' ";
		$data .= ", surcharge_percent = '$sur_percent' ";

		
		$save = $this->conn->query("INSERT INTO t_invoice set ".$data);

		$resp['data'] = array(
			'property_id' => $prop_id,
			'payment_amount' => $amount_paid,
			'pay_date' => $or_date_ent,
			'due_date' => $due_date,
			'or_no' => $or_no_ent,
			'amount_due' => $tot_amount_due,
			'rebate' => $rebate,
			'surcharge' => $surcharge,
			'interest' => $interest,
			'principal' => $principal,
			'remaining_balance' => $balance,
			'status' => $status,
			'status_count' => $status_count,
			'payment_count' => $payment_count,
			'excess' => $excess,
			'trans_date' => $trans_date_ent,
			'surcharge_percent' => $sur_percent
		  );


		if($resp['data'] ){
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}

	function update_prop_client(){
		extract($_POST);
		$data = " last_name = '$customer_last_name' ";
		$data .= ", first_name = '$customer_first_name' ";
		$data .= ", middle_name = '$customer_middle_name' ";
		$data .= ", suffix_name = '$customer_suffix_name' ";
		$data .= ", address = '$customer_address' ";
		$data .= ", zip_code = '$customer_zip_code' ";
		$data .= ", address_abroad = '$customer_address_2' ";
		$data .= ", birthdate = '$birth_day' ";
		$data .= ", age = '$customer_age' ";
		$data .= ", gender = '$customer_gender' ";
		$data .= ", viber = '$customer_viber' ";
		$data .= ", civil_status = '$civil_status' ";
		$data .= ", citizenship = '$citizenship' ";
		$data .= ", email = '$customer_email' ";
		$data .= ", contact_no = '$contact_no' ";


		$sql = "UPDATE property_clients set ".$data." where client_id = ".$client_id;
		$save = $this->conn->query($sql);
		
		if($save){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Client Details successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}

	function save_member(){
		extract($_POST);
		$data = " client_id = '$client_id' ";
		$data .= ", last_name = '$customer_last_name' ";
		$data .= ", first_name = '$customer_first_name' ";
		$data .= ", middle_name = '$customer_middle_name' ";
		$data .= ", suffix_name = '$customer_suffix_name' ";
		$data .= ", address = '$customer_address' ";
		$data .= ", zip_code = '$customer_zip_code' ";
		$data .= ", address_abroad = '$customer_address_2' ";
		$data .= ", birthdate = '$birth_day' ";
		$data .= ", age = '$customer_age' ";
		$data .= ", gender = '$customer_gender' ";
		$data .= ", viber = '$customer_viber' ";
		$data .= ", civil_status = '$civil_status' ";
		$data .= ", citizenship = '$citizenship' ";
		$data .= ", email = '$customer_email' ";
		$data .= ", contact_no = '$contact_no' ";
		$data .= ", status = 0 ";

		
		$check = $this->conn->query("SELECT * FROM `family_members` where `last_name` = '{$customer_last_name}' and
		 `first_name` = '{$customer_first_name}' and `middle_name` = '{$customer_middle_name}' ".(!empty($member_id) ? " and member_id != {$member_id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Member already exist.";
			return json_encode($resp);
			exit;
		} 
		if(empty($member_id)){
			/* $sql = "SELECT * FROM t_buyer_info"; */


			$users_to_notify = array('IT Admin'); 

			foreach ($users_to_notify as $user) {
				$data_notif_values = array(
					"message = '$comm$client_id.'",
					"user_to_be_notified = '$user'",
					"seen = '0'"
				);

				$data_notif = implode(", ", $data_notif_values);

				$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
			}

			$sql = "INSERT INTO family_members set ".$data;
			$save = $this->conn->query($sql);
		}else{
			/* $sql = "SELECT * FROM t_buyer_info"; */
			$users_to_notify = array('IT Admin'); 

			foreach ($users_to_notify as $user) {
				$data_notif_values = array(
					"message = '$comm2$client_id.'",
					"user_to_be_notified = '$user'",
					"seen = '0'"
				);

				$data_notif = implode(", ", $data_notif_values);

				$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
			}

			$sql = "UPDATE family_members set ".$data." where member_id = ".$member_id;
			$save = $this->conn->query($sql);
		}	
		if($save){
			$resp['status'] = 'success';
			if(empty($member_id))
				$this->settings->set_flashdata('success',"New Buyer successfully saved.");
			else
				$this->settings->set_flashdata('success',"Buyer successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}

	function res_approval(){
		extract($_POST);
		if ($value == 4):
			$update = $this->conn->query("UPDATE pending_restructuring set lvl1='1' where id = ".$data_id);
		elseif($value == 3):
			$update = $this->conn->query("UPDATE  pending_restructuring set lvl2='1' where id = ".$data_id);
		elseif($value == 2 or $value == 1):
			$update = $this->conn->query("UPDATE  pending_restructuring set lvl3='1', lvl2='1', lvl1='1', pending_status = 0 where id = ".$data_id);
			$qry1 = "SELECT * FROM pending_restructuring where id =".$data_id;
			$sql1 = $this->conn->query($qry1);
			while($row = $sql1->fetch_assoc()) {
				$prop_id = $row['property_id'];
				$payment_type1 = $row['c_payment_type1'];
				$net_dp = $row['c_net_dp'];
				$less_dp = $row['less_dp'];
				$acc_surcharge1 = $row['acc_surcharge1'];
				$new_net_dp = ($net_dp - $less_dp) + $acc_surcharge1;
				$no_payment = $row['c_no_payments'];
				$monthly_down = $row['c_monthly_down'];
				$first_dp_date = $row['c_first_dp'];
				$full_down_date = $row['c_full_down'];
				$payment_type2 = $row['c_payment_type2'];	
				$amt_to_be_financed = $row['c_amt_financed'];	
				$acc_surcharge2 = $row['acc_surcharge2'];
				$acc_interest = $row['acc_interest'];
				$total_sur = $acc_surcharge1 + $acc_surcharge2;
				$terms= $row['c_terms'];
				$interest_rate = $row['c_interest_rate'];
				$fixed_factor = $row['c_fixed_factor'];
				$start_date = $row['c_start_date'];
				$monthly_amortization = $row['c_monthly_payment'];
				$mode = '1';
				$acc_status = $row['c_account_status'];
				$balance = $row['c_balance'];
				$balance = (float) str_replace(",", "", $balance);
				$new_amt_to_be_financed = $balance + $acc_interest + $acc_surcharge2;
				$total_balance = $acc_surcharge1 + $new_amt_to_be_financed;
				$restructured_date = $row['c_restructured_date'];
				$amount_due = $acc_interest + $total_sur;
			
				$data = "c_payment_type1 = '$payment_type1' ";
				$data .= ", c_net_dp = '$new_net_dp' ";
				$data .= ", c_no_payments = '$no_payment' ";
				$data .= ", c_monthly_down = '$monthly_down' ";
				$data .= ", c_first_dp = '$first_dp_date' ";
				$data .= ", c_full_down = '$full_down_date' ";
				$data .= ", c_payment_type2 = '$payment_type2' ";
				$data .= ", c_amt_financed = '$new_amt_to_be_financed' ";
				$data .= ", c_terms = '$terms' ";
				$data .= ", c_interest_rate = '$interest_rate' ";
				$data .= ", c_fixed_factor = '$fixed_factor' ";
				$data .= ", c_monthly_payment = '$monthly_amortization' ";
				$data .= ", c_start_date = '$start_date' ";
				$data .= ", c_account_status = '$acc_status'";
				$data .= ", c_balance = '$total_balance'";
				$data .= ", c_restructured = '$mode'";
				
	
				$update = $this->conn->query("UPDATE properties set ".$data." where property_id = ".$prop_id);


			}
		
			$add = $this->conn->query("INSERT INTO tbl_restructuring set res_id =".$data_id. ", status = 1, property_id = ".$prop_id);


			$qry = "SELECT due_date, payment_count, status_count FROM property_payments where property_id =".$prop_id." ORDER by payment_count";
			$sql = $this->conn->query($qry);
			$l_last = $sql->num_rows - 1;
			$payments_data = array(); 
			if($sql->num_rows <= 0){
				$resp['status'] = 'failed';
				$resp['err'] = 'No Payment Records yet!';
				return json_encode($resp);
			} 
			while($row = $sql->fetch_assoc()) {
			$payments_data[] = $row; 

			}

			$last_row = $payments_data[$l_last];
			$due_date = $last_row['due_date'];
			$pay_count = $last_row['payment_count'] + 1;

			$data2 = "property_id = '$prop_id' ";
			$data2 .= ", due_date = '$due_date' ";
			$data2 .= ", pay_date = '$restructured_date' ";
			$data2 .= ", or_no = 'RSTR-". $data_id ."'";
			$data2 .= ", payment_amount = '0' ";
			$data2 .= ", amount_due = '$amount_due' ";
			$data2 .= ", rebate = '0' ";
			$data2 .= ", surcharge = '$total_sur' ";
			$data2 .= ", interest = '$acc_interest' ";
			$data2 .= ", principal = '0' ";
			$data2 .= ", remaining_balance = '$total_balance' ";
			$data2 .= ", status = 'RESTRUCTURED' ";
			$data2 .= ", status_count = '0' ";
			$data2 .= ", payment_count = '$pay_count' ";

			$res_pay = $this->conn->query("INSERT INTO property_payments set ".$data2);

		endif;
		

		if($update){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Restructuring for this account successfully approved!");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function res_disapproval(){
		extract($_POST);
		if ($value == 4):
			$update = $this->conn->query("UPDATE pending_restructuring set lvl1='2', pending_status='2' where id = ".$data_id);
		elseif($value == 3):
			$update = $this->conn->query("UPDATE pending_restructuring set lvl2='2', pending_status='2'  where id = ".$data_id);
		elseif($value == 2 or $value == 1):
			$update = $this->conn->query("UPDATE  pending_restructuring set lvl3='2',lvl2='2',lvl1='2', pending_status='2' where id = ".$data_id);
		endif;

		if($update){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Restructuring for this account successfully disapproved!");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function create_restructured(){
		extract($_POST);
			
		$prop_id = $_POST['prop_id'];
		$data = "property_id = '$prop_id' ";
		$acc_status = $_POST['acc_stat'];
	
		if ($acc_status == "Partial DownPayment" || $acc_status == 'Full DownPayment' || $acc_status == 'No DownPayment'){
				
				$payment_type1 = $_POST['payment_type1'];
				$less_paymt= $_POST['less_paymt_dte'];
				$dp_bal = $_POST['dp_bal'];
				$acc_surcharge1 = $_POST['acc_surcharge1'];
				$no_payment = $_POST['rem_dp'];
				$monthly_down = $_POST['monthly_down'];
				$first_dp_date = $_POST['first_dp_date'];
				$full_down_date = $_POST['full_down_date'];
				
		
				$data .= ", c_payment_type1 = '$payment_type1' ";
				$data .= ", c_net_dp = '$net_dp' ";
				$data .= ", less_dp = '$less_paymt' ";
				$data .= ", acc_surcharge1 = '$acc_surcharge1' ";
				$data .= ", c_no_payments = '$no_payment' ";
				$data .= ", c_monthly_down = '$monthly_down' ";
				$data .= ", c_full_down = '$full_down_date' ";
				if ($no_payment == '1'){
					$data .= ", c_first_dp = '$full_down_date' ";
				}else{
					$data .= ", c_first_dp = '$first_dp_date' ";
				}

		}else{
			$select = "SELECT * from properties where property_id = ".$prop_id;
			$sql = $this->conn->query($select);
			while($row = $sql->fetch_assoc()) {
				$payment_type1 = $row['c_payment_type1'];
				$net_dp = $row['c_net_dp'];
				$no_payment = $row['c_no_payments'];
				$monthly_down = $row['c_monthly_down'];
				$first_dp_date = $row['c_first_dp'];
				$full_down = $row['c_full_down'];
				$dp_bal = 0;
				$acc_surcharge1 = 0;

				$data .= ", c_payment_type1 = '$payment_type1' ";
				$data .= ", c_net_dp = '$net_dp' ";
				$data .= ", less_dp = 0 ";
				$data .= ", acc_surcharge1 = 0 ";
				$data .= ", c_no_payments = '$no_payment' ";
				$data .= ", c_monthly_down = '$monthly_down' ";
				$data .= ", c_first_dp = '$first_dp_date' ";
				$data .= ", c_full_down = '$full_down_date' ";

			}
		}
		$dp_sur = $dp_bal + $acc_surcharge1;
		$payment_type2 = $_POST['payment_type2'];
		$amt_to_be_financed = $_POST['amt_to_be_financed'];
		$acc_interest = $_POST['acc_interest'];
		$acc_surcharge2 = $_POST['acc_surcharge2'];
		$adj_prin_bal = $_POST['adj_prin_bal'];
		$terms= $_POST['terms'];
		$interest_rate = $_POST['interest_rate'];
		$fixed_factor = $_POST['fixed_factor'];
		$monthly_amortization = $_POST['monthly_amortization'];
		$start_date = $_POST['start_date'];		
			
		$total_sur = $acc_surcharge1 + $acc_surcharge2;
		 $balance = $_POST['balance'];
		$balance = (float) str_replace(",", "", $balance);
		/*$total_balance = $balance + $acc_interest + $acc_surcharge2; */
		$restructured_date = $_POST['restructured_date'];
	/* 	$amount_due = $acc_interest + $total_sur; */

		$data .= ", c_payment_type2 = '$payment_type2' ";
		$data .= ", c_amt_financed = '$amt_to_be_financed' ";
		$data .= ", acc_interest = '$acc_interest' ";
		$data .= ", acc_surcharge2 = '$acc_surcharge2' ";
		$data .= ", c_adj_prin_bal = '$adj_prin_bal' ";
		$data .= ", c_terms = '$terms' ";
		$data .= ", c_interest_rate = '$interest_rate' ";
		$data .= ", c_fixed_factor = '$fixed_factor' ";
		$data .= ", c_monthly_payment = '$monthly_amortization' ";
		$data .= ", c_start_date = '$start_date' ";
		$data .= ", c_account_status = '$acc_status'";
		$data .= ", c_balance = '$balance'";
		$data .= ", c_restructured_date = '$restructured_date'";
		$data .= ", pending_status = '1'";

		#donita binago ko to para mas malinis
		$save = $this->conn->query("INSERT INTO pending_restructuring set ". $data);
		if($save){
			
			$users_to_notify = array('IT Admin'); 
			foreach ($users_to_notify as $user) {
				$data_notif_values = array(
				"message = '$comm'",
				"user_to_be_notified = '$user'",
				"seen = '0'"
			);

			$data_notif = implode(", ", $data_notif_values);

			$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);

		}

			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Restructuring successfully created.");

		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}

	function save_restructured2(){
		extract($_POST);
			
		$prop_id = $_POST['prop_id'];
		
		$payment_type2 = $_POST['payment_type2'];
		
		$terms= $_POST['terms'];
		$interest_rate = $_POST['interest_rate'];
		$fixed_factor = $_POST['fixed_factor'];
		$start_date = $_POST['start_date'];
		$monthly_amortization = $_POST['monthly_amortization'];
		$mode = '1';
		$acc_status = $_POST['acc_stat'];
		$acc_surcharge2 = $_POST['acc_surcharge2'];
		$acc_interest = $_POST['acc_interest'];
		$total_sur = $acc_surcharge1 + $acc_surcharge2;
		$amt_to_be_financed = $_POST['amt_to_be_financed'];
		$balance = $_POST['balance'];
		$balance = (float) str_replace(",", "", $balance);
		$total_balance = $balance + $acc_interest + $acc_surcharge2;
		$restructured_date = $_POST['restructured_date'];
		$amount_due = $acc_interest + $total_sur;
		
	
		$data = "c_payment_type2 = '$payment_type2' ";
		if ($acc_status == "Partial DownPayment" || $acc_status == 'Full DownPayment' || $acc_status == 'No DownPayment'){
				
				$payment_type1 = $_POST['payment_type1'];
				$dp_bal = $_POST['dp_bal'];
				$acc_surcharge1 = $_POST['acc_surcharge1'];
				$dp_sur = $dp_bal + $acc_surcharge1;
				$no_payment = $_POST['no_payment'];
				$monthly_down = $_POST['monthly_down'];
				$first_dp_date = $_POST['first_dp_date'];
				$full_down_date = $_POST['full_down_date'];
				$adj_prin_bal = $_POST['adj_prin_bal'];
			
				$data .= ", c_payment_type1 = '$payment_type1' ";
				$data .= ", c_net_dp = '$net_dp' ";
				$data .= ", c_monthly_down = '$monthly_down' ";
				$data .= ", c_no_payments = '$no_payment' ";
				$data .= ", c_first_dp = '$first_dp_date' ";
				$data .= ", c_full_down = '$full_down_date' ";
			}
		$data .= ", c_amt_financed = '$amt_to_be_financed' ";
		$data .= ", c_terms = '$terms' ";
		$data .= ", c_interest_rate = '$interest_rate' ";
		$data .= ", c_fixed_factor = '$fixed_factor' ";
		$data .= ", c_monthly_payment = '$monthly_amortization' ";
		$data .= ", c_start_date = '$start_date' ";
		$data .= ", c_account_status = '$acc_status'";
		$data .= ", c_restructured = $mode ";

		$data3 = "property_id = '$prop_id' ";
		$data3 .= ", status = '0' ";

		$save = $this->conn->query("UPDATE properties set ".$data." where property_id = ".$prop_id);
		$save = $this->conn->query("UPDATE property_payments set lvl3='1', pending_status='0' where status='RESTRUCTURED' and property_id = ".$prop_id);
		$save = $this->conn->query("INSERT INTO tbl_restructuring set ".$data3);

		$qry = "SELECT due_date, payment_count, status_count FROM property_payments where property_id =".$prop_id." ORDER by payment_count";
		$sql = $this->conn->query($qry);
		$l_last = $sql->num_rows - 1;
		$payments_data = array(); 
		if($sql->num_rows <= 0){
			$resp['status'] = 'failed';
			$resp['err'] = 'No Payment Records yet!';
			return json_encode($resp);
        } 
		while($row = $sql->fetch_assoc()) {
		  $payments_data[] = $row; 

		}

		$last_row = $payments_data[$l_last];
		$due_date = $last_row['due_date'];
		$pay_count = $last_row['payment_count'] + 1;

		$data2 = "property_id = '$prop_id' ";
		$data2 .= ", due_date = '$due_date' ";
		$data2 .= ", pay_date = '$restructured_date' ";
		$data2 .= ", or_no = '******' ";
		$data2 .= ", payment_amount = '0' ";
		$data2 .= ", amount_due = '$amount_due' ";
		$data2 .= ", rebate = '0' ";
		$data2 .= ", surcharge = '$total_sur' ";
		$data2 .= ", interest = '$acc_interest' ";
		$data2 .= ", principal = '0' ";
		$data2 .= ", remaining_balance = '$total_balance' ";
		$data2 .= ", status = 'RESTRUCTURED' ";
		$data2 .= ", status_count = '0' ";
		$data2 .= ", payment_count = '$pay_count' ";
		$data2 .= ", pending_status = '1' ";



		$data3 = "property_id = '$prop_id' ";
		$data3 .= ", payment_amount = '0' ";
		$data3 .= ", pay_date = '$restructured_date' ";
		$data3 .= ", due_date = '$due_date' ";
		$data3 .= ", or_no = '******' ";
		$data3 .= ", amount_due = '$amount_due' ";
		$data3 .= ", rebate = '0' ";
		$data3 .= ", surcharge = '$total_sur' ";
		$data3 .= ", interest = '$acc_interest' ";
		$data3 .= ", principal = '0' ";
		$data3 .= ", remaining_balance = '$total_balance' ";
		$data3 .= ", status = 'RESTRUCTURED' ";
		$data3 .= ", status_count = '0' ";
		$data3 .= ", payment_count = '$pay_count' ";

		//$save2 = $this->conn->query("INSERT INTO property_payments set ".$data2);
		//$save2 = $this->conn->query("INSERT INTO tbl_restructuring set ".$data3);



		if($save){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Restructuring successfully saved.");

		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}

	function save_av(){
		extract($_POST);
		$data = " c_av_no = 'AV".$av_no."' ";
		#$data = " c_av_no = '$av_no' ";	 
		$data .= ", property_id = '$p_id' ";	 
		$data .= ", c_av_date = '$av_date' ";	 
	 	$data .= ", c_av_amount = '$total_av' ";
		$data .= ", c_deductions = '$av_deduc' ";
		$data .= ", c_principal = '$av_prin' ";
		$data .= ", c_surcharge = '$av_sur' ";
		$data .= ", c_interest = '$av_int' ";
		$data .= ", c_rebate = '$av_reb' ";
		$data .= ", c_av_type = '$av_type' ";
		/* $data .= ", c_new_acc_no = '$new_acc_no' ";  */
		$data .= ", c_remarks = '$remarks' ";
		
		$check = $this->conn->query("SELECT * FROM `t_av_summary` where `c_av_no` ='{$av_no}'")->num_rows;
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = " AV number already exists.";
			return json_encode($resp);
		}else{
			$save = $this->conn->query("INSERT INTO t_av_summary set ".$data);
			
			$sql = $this->conn->query("SELECT * FROM property_payments where property_id =".$p_id."");
			
			if($sql->num_rows <= 0){
				$resp['status'] = 'failed';
				$resp['err'] = 'No Payment Records yet! Please add to proceed with payments';
				return json_encode($resp);
			} 
			while($row = $sql->fetch_array()):	
				$prop_id = $row['property_id'];
				$pay_date = $row['pay_date'];
				$or_no_ent = $row['or_no'];
				$amount_paid = $row['payment_amount'];
				$due_date = $row['due_date'];
				$tot_amount_due = $row['amount_due'];
				$rebate = $row['rebate'];
				$surcharge = $row['surcharge'];
				$interest = $row['interest'];
				$principal = $row['principal'];
				$balance = $row['remaining_balance'];
				$status = $row['status'];
				$status_count = $row['status_count'];
				$payment_count = $row['payment_count'];
	
				$data = " property_id = '$prop_id' ";
				$data .= ", av_no = 'AV".$av_no."' ";
				$data .= ", pay_date = '$pay_date' ";
				$data .= ", or_no = '$or_no_ent' " ;
				$data .= ", payment_amount = '$amount_paid' ";
				$data .= ", due_date = '$due_date' ";
				$data .= ", amount_due = '$tot_amount_due' ";
				$data .= ", rebate = '$rebate' ";
				$data .= ", surcharge = '$surcharge' ";
				$data .= ", interest = '$interest' ";
				$data .= ", principal = '$principal' ";
				$data .= ", remaining_balance = '$balance' ";
				$data .= ", status = '$status' ";
				$data .= ", status_count = '$status_count' ";
				$data .= ", payment_count = '$payment_count' ";
	
				$save = $this->conn->query("INSERT INTO t_av_breakdown set ".$data);
				//$update = $this->conn->query("UPDATE property_payments set pending_status=1 WHERE property_id = '$prop_id'");
	
			endwhile;


			if($save){
				$users_to_notify = array('IT Admin'); 

				foreach ($users_to_notify as $user) {
					$data_notif_values = array(
						"message = '$comm'",
						"user_to_be_notified = '$user'",
						"seen = '0'"
					);

					$data_notif = implode(", ", $data_notif_values);

					$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
				}

				$resp['status'] = 'success';
				$this->settings->set_flashdata('success',"Payment successfully moved to AV!");

			}else{
				$resp['status'] = 'failed';
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}
		return json_encode($resp);
			
	}

	function av_approval(){
		//reopen (properties)
		//active_status - transferred (2)
		//update t_lots
		//removed from payments - pending_status (property_payments)
		extract($_POST);

		if ($value == 4):
			$update = $this->conn->query("UPDATE t_av_summary SET lvl1='1' WHERE c_av_no = 'AV".$this->conn->real_escape_string($data_id)."'");
			#$update = $this->conn->query("UPDATE t_av_summary SET lvl1='1' WHERE c_av_no = ".$data_id);
		elseif($value == 3):
			$update = $this->conn->query("UPDATE t_av_summary SET lvl2='1' WHERE c_av_no = 'AV".$this->conn->real_escape_string($data_id)."'");
		elseif($value == 2):
			$update = $this->conn->query("UPDATE t_av_summary SET lvl3='1' WHERE c_av_no = 'AV".$this->conn->real_escape_string($data_id)."'");
			$update = $this->conn->query("UPDATE properties set c_active='1',c_reopen = '1' where property_id = ".$prop_id);
			$get_lid = intval(substr($prop_id, 2, 8));
			//echo $get_lid;
			$update = $this->conn->query("UPDATE t_lots set c_status='Available' where c_lid = ".$get_lid);
			$update = $this->conn->query("UPDATE t_csr set c_active = 0  where c_lot_lid = ".$get_lid);
			//$update = $this->conn->query("DELETE FROM property_payments WHERE property_id = ".$prop_id);
		elseif($value == 1):
			$update = $this->conn->query("UPDATE t_av_summary SET lvl1='1',lvl2='1',lvl3='1' WHERE c_av_no = 'AV".$this->conn->real_escape_string($data_id)."'");
			$update = $this->conn->query("UPDATE properties set c_active='1',c_reopen = '1' where property_id = ".$prop_id);
			$get_lid = intval(substr($prop_id, 2, 8));
			//echo $get_lid;
			$update = $this->conn->query("UPDATE t_lots set c_status='Available' where c_lid = ".$get_lid);
			$update = $this->conn->query("UPDATE t_csr set c_active = 0  where c_lot_lid = ".$get_lid);
			//$update = $this->conn->query("DELETE FROM property_payments WHERE property_id = ".$prop_id);
	
		endif;
		
		if($update){

			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Transferring of this account successfully approved!");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}


	function cm_approval(){
		extract($_POST);
		$save2 = null;
		if ($value == 4){
			$update = $this->conn->query("UPDATE t_credit_memo SET lvl1='1' WHERE reference = ".$data_id);
		} elseif ($value == 3){
			$update = $this->conn->query("UPDATE t_credit_memo SET lvl2='1' WHERE reference = ".$data_id);
		} elseif ($value == 2){
			$update = $this->conn->query("UPDATE t_credit_memo SET lvl3='1' WHERE reference = ".$data_id);
		} elseif ($value == 1){
			$update = $this->conn->query("UPDATE t_credit_memo SET lvl1='1',lvl2='1',lvl3='1' WHERE reference = ".$data_id);
		}

		if ($value == 2 or $value == 1){

		$qry_get_cm = "SELECT * FROM t_credit_memo where reference = ".$data_id."";
		$sql_get_cm = $this->conn->query($qry_get_cm);
		while($row = $sql_get_cm->fetch_assoc()) {
			$cm_id = $row['cm_id'];
			$cm_status = $row['memo_status'];
			$cm_date = $row['cm_date'];

			if($cm_status=='CM'){
				$cm_id = 'CM'.$cm_id;
			}else{
				$cm_id = 'DM'.$cm_id;
			}

			//$data2 = " reference = '$cm_id' ";
		}

		$qry = "SELECT * FROM property_payments where property_id = ".$prop_id." ORDER by payment_count";
			$sql = $this->conn->query($qry);
			$l_last = $sql->num_rows - 1;
			$payments_data = array(); 
			if($sql->num_rows <= 0){
			$resp['status'] = 'failed';
			$resp['err'] = 'No Payment Records yet!';
			return json_encode($resp);
			} 
			while($row = $sql->fetch_assoc()) {
			$payments_data[] = $row; 

			}

			$last_row = $payments_data[$l_last];
			$prop_id = $last_row['property_id'];
			$payment_amt = $last_row['payment_amount'];
			$due_date = $last_row['due_date'];
			$pay_count = $last_row['payment_count'] + 1;
			$status = $last_row['status'];
			$status_count = $last_row['status_count'];

			$rem_bal = $last_row['remaining_balance'];
			$last_bal = $rem_bal - $cm_amt;
			$data2 = " property_id = '$prop_id' ";
			$data2 .= ", payment_amount = '0' ";
			
			$data2 .= ", pay_date = '$cm_date' "; 
			$data2 .= ", due_date = '$due_date' "; 
			$data2 .= ", or_no = '$cm_id' ";
			$data2 .= ", amount_due = '0' "; 
			$data2 .= ", rebate = '0' ";
			$data2 .= ", surcharge = '0' ";
			$data2 .= ", interest = '0' ";
			$data2 .= ", principal = '$cm_amt' ";/// amount ng CM (- or +)

			
			$data2 .= ", remaining_balance = '$last_bal' ";/// last balance - principal
			$data2 .= ", status = '$status' ";
			$data2 .= ", payment_count = '$pay_count' ";
			$data2 .= ", status_count = '$status_count' ";
			
			
			$insert_qry = "INSERT INTO property_payments SET ".$data2;
			$save2 = $this->conn->query($insert_qry);
		}

		if ($update && $save2 !== false) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Credit/Debit Memo successfully approved!");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	

	function cm_disapproval(){

		extract($_POST);

		if ($value == 4):
			$update = $this->conn->query("UPDATE t_credit_memo SET lvl1='2' WHERE reference = ".$data_id);
		elseif($value == 3):
			$update = $this->conn->query("UPDATE t_credit_memo SET lvl2='2' WHERE reference = ".$data_id);
		elseif($value == 2):
			$update = $this->conn->query("UPDATE t_credit_memo SET lvl3='2' WHERE reference = ".$data_id);
		elseif($value == 1):
			$update = $this->conn->query("UPDATE t_credit_memo SET lvl1='2',lvl2='2',lvl3='2' WHERE reference = ".$data_id);
		endif;
		
		if($update){

			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Credit/Debit Memo successfully disapproved!");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function av_disapproval(){
		//reopen (properties)
		//active_status - transferred (2)
		//update t_lots
		//removed from payments - pending_status (property_payments)
		extract($_POST);
		if ($value == 4):
			//$update = $this->conn->query("UPDATE t_av_summary SET lvl1='2' WHERE c_av_no = 'AV".$this->conn->real_escape_string($data_id)."'");
			$update = $this->conn->query("UPDATE t_av_summary SET lvl1='2' WHERE c_av_no = 'AV".$this->conn->real_escape_string($data_id)."'");
		elseif($value == 3):
			//$update = $this->conn->query("UPDATE t_av_summary SET lvl2='2' WHERE c_av_no = 'AV".$this->conn->real_escape_string($data_id)."'");
			$update = $this->conn->query("UPDATE t_av_summary SET lvl2='2' WHERE c_av_no = 'AV".$this->conn->real_escape_string($data_id)."'");
		elseif($value == 2):
			//$update = $this->conn->query("UPDATE t_av_summary SET lvl3='2' WHERE c_av_no = '".$this->conn->real_escape_string($data_id)."'");
			$update = $this->conn->query("UPDATE properties set c_active='1',c_reopen = '1' where property_id = ".$prop_id);
			$get_lid = intval(substr($prop_id, 2, 8));
			//echo $get_lid;
			$update = $this->conn->query("UPDATE t_lots set c_status='Available' where c_lid = ".$get_lid);
			$update = $this->conn->query("UPDATE t_csr set c_active = 0  where c_lot_lid = ".$get_lid);+
			//$update = $this->conn->query("DELETE FROM t_av_summary WHERE c_av_no = AV".$data_id);
			$update = $this->conn->query("DELETE FROM t_av_summary WHERE c_av_no = 'AV".$this->conn->real_escape_string($data_id)."'");
			//$update = $this->conn->query("DELETE FROM t_av_breakdown WHERE av_no = AV".$data_id);
			$update = $this->conn->query("DELETE FROM t_av_breakdown WHERE c_av_no = 'AV".$this->conn->real_escape_string($data_id)."'");
		elseif($value == 1):
			//$update = $this->conn->query("UPDATE t_av_summary SET lvl1='2',lvl2='2',lvl3='2' WHERE c_av_no = '".$this->conn->real_escape_string($data_id)."'");
			$update = $this->conn->query("UPDATE properties set c_active='1',c_reopen = '1' where property_id = ".$prop_id);
			$get_lid = intval(substr($prop_id, 2, 8));
			//echo $get_lid;
			$update = $this->conn->query("UPDATE t_lots set c_status='Available' where c_lid = ".$get_lid);
			$update = $this->conn->query("UPDATE t_csr set c_active = 0  where c_lot_lid = ".$get_lid);+
			// $update = $this->conn->query("DELETE FROM t_av_summary WHERE c_av_no = AV".$data_id);
			// $update = $this->conn->query("DELETE FROM t_av_breakdown WHERE av_no = AV".$data_id);
			$update = $this->conn->query("DELETE FROM t_av_summary WHERE c_av_no = 'AV".$this->conn->real_escape_string($data_id)."'");
			$update = $this->conn->query("DELETE FROM t_av_breakdown WHERE c_av_no = 'AV".$this->conn->real_escape_string($data_id)."'");
		endif;
		
		if($update){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Transferring of this account successfully approved!");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function set_retention(){
		extract($_POST);
		$ret = $this->conn->query("UPDATE properties set c_retention = '1' where property_id = ".$prop_id);
		//echo $ret;
		if($ret){
			// $sql = $this->conn->query("SELECT * FROM users where id=" .$type);
			// 	while($row = $sql->fetch_assoc()){
			// 		$usertype= $row['username'];
			// 	}
			// 	$users_to_notify = array('IT Admin'); 
			// 	foreach ($users_to_notify as $user) {
			// 		$data_notif_values = array(
			// 		"message = '$usertype set client with property ID #$prop_id to retention.'",
			// 		"user_to_be_notified = '$user'",
			// 		"seen = '0'"
			// 	);

			// 	$data_notif = implode(", ", $data_notif_values);

			// 	$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
			// 	}

			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Property successfully set to Retention.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
			
	}

	function backout_acc(){
		extract($_POST);
		$backout = $this->conn->query("UPDATE properties set c_reopen = '0', c_active = '0', c_backout_date = DATE(CURRENT_TIMESTAMP()) where property_id = ".$prop_id);
		$lid = substr($prop_id,2,8);
		$inactive_csr = $this->conn->query("UPDATE t_csr set c_active = '0' where c_csr_no =". $csr_no);
		$update_lot = $this->conn->query("UPDATE t_lots set c_status = 'Available' where c_lid =". $lid);

		//echo $ret;
		if($backout && $update_lot){

			// $sql = $this->conn->query("SELECT * FROM users where id=" .$type);
			// 	while($row = $sql->fetch_assoc()){
			// 		$usertype= $row['username'];
			// 	}
			// 	$users_to_notify = array('IT Admin'); 
			// 	foreach ($users_to_notify as $user) {
			// 		$data_notif_values = array(
			// 		"message = '$usertype set client with property ID #$prop_id to backout.'",
			// 		"user_to_be_notified = '$user'",
			// 		"seen = '0'"
			// 	);

			// 	$data_notif = implode(", ", $data_notif_values);

			// 	$save = $this->conn->query("INSERT INTO message_tbl SET ".$data_notif);
			// 	}

			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Property successfully Backout.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
			
	}

	function save_group(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `group_list` set {$data} ";
		}else{
			$sql = "UPDATE `group_list` set {$data} where id = '{$id}' ";
		}
		$check = $this->conn->query("SELECT * FROM `group_list` where `name` = '{$name}' and delete_flag = 0 ".($id > 0 ? " and id != '{$id}'" : ""));
		if($check->num_rows > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = " Account's Group Name already exists.";
		}else{
			$save = $this->conn->query($sql);
			if($save){
				$gid = !empty($id) ? $id : $this->conn->insert_id;
				$resp['status'] = 'success';
				if(empty($id))
					$resp['msg'] = " Account's Group has successfully added.";
				else
					$resp['msg'] = " Account's Group details has been updated successfully.";
			}else{
				$resp['status'] = 'failed';
				$resp['msg'] = "An error occured.";
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}
		if($resp['status'] =='success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_group(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `group_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Account's Group has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function delete_sub_accs(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `subsidiary_accounts` set delete_flag = 1 where sub_id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Subsidiary account has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_account(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `account_list` set {$data} ";
		}else{
			$sql = "UPDATE `account_list` set {$data} where id = '{$id}' ";
		}
		$check = $this->conn->query("SELECT * FROM `account_list` where (`name` ='{$name}' or `code` ='{$code}') and delete_flag = 0 ".($id > 0 ? " and id != '{$id}' " : ""))->num_rows;
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = " Account's Name/Code already exists.";
		}else{
			$save = $this->conn->query($sql);
			if($save){
				$rid = !empty($id) ? $id : $this->conn->insert_id;
				$resp['status'] = 'success';
				if(empty($id))
					$resp['msg'] = " Account has successfully added.";
				else
					$resp['msg'] = " Account has been updated successfully.";
			}else{
				$resp['status'] = 'failed';
				$resp['msg'] = "An error occured.";
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
			if($resp['status'] =='success')
			$this->settings->set_flashdata('success',$resp['msg']);
		}
		return json_encode($resp);
	}

	function save_cprofile(){
		extract($_POST);
		$data = " buyer_id = '$buyer_id' ";
		$data .= ", terms = '$terms' ";
		$data .= ", vatable = '$vatable' ";

		$check_query = $this->conn->query("SELECT buyer_id FROM customers_profile WHERE buyer_id = '$buyer_id'");
		
		if ($check_query->num_rows > 0) {
			$sql = "UPDATE `customers_profile` SET {$data} WHERE buyer_id = '$buyer_id'";
		} else {
			$sql = "INSERT INTO `customers_profile` SET {$data}";
		}
		
		$save = $this->conn->query($sql);
		
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success', "Customer's profile successfully updated.");
			else
				$this->settings->set_flashdata('success', "Customer's profile successfully updated.");
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . " [{$sql}]";
		}
		return json_encode($resp);
	}
	
	
	function save_sub_accs(){
		extract($_POST);
		$data = " sub_code = '$sub_code' ";
		$data .= ", sub_name = '$sub_name' ";
		$data .= ", status = '$status' ";
		

		$check_query = $this->conn->query("SELECT sub_code FROM subsidiary_accounts WHERE sub_code = '$sub_code'");
		
		if ($check_query->num_rows > 0) {

			$sql = "UPDATE `subsidiary_accounts` SET {$data} WHERE sub_code = '$sub_code'";
		} else {
			$sql = "INSERT INTO `subsidiary_accounts` SET {$data}";
		}
		
		$save = $this->conn->query($sql);
		
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success', "New subsidiary account successfully saved.");
			else
				$this->settings->set_flashdata('success', "Subsidiary account successfully updated.");
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . " [{$sql}]";
		}
		return json_encode($resp);
	}
	
	function delete_account(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `account_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Account has been deleted successfully.");

		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	function save_voucher_supplier(){
		if(empty($_POST['id'])){
			$v_num = isset($_POST['v_num']) ? $_POST['v_num'] : '';
			$gr_id = isset($_POST['gr_id']) ? $_POST['gr_id'] : '';
			$po_no = isset($_POST['po_no']) ? $_POST['po_no'] : '';
			$newDocNo = isset($_POST['newDocNo']) ? $_POST['newDocNo'] : '';
			$preparer = isset($_POST['preparer']) ? $_POST['preparer'] : '';
		}
		extract($_POST);
		$data = "";
		$gl_data = "";
		foreach ($_POST as $k => $v) {
			if (!is_array($_POST[$k])) {
				if ($k !== 'vs_num' && !in_array($k, array('id','gtype','newDocNo'))) {
					if (!is_numeric($v) && !is_null($v))
						$v = $this->conn->real_escape_string($v);
					if (!empty($data)) $data .= ",";
					if (!is_null($v))
						$data .= " `{$k}`='{$v}' ";
					else
						$data .= " `{$k}`= NULL ";
				}
			}
		}
		if (empty($id)) {
			$data = preg_replace('/\b(agent_id|emp_id|client_id)\b/', 'supplier_id', $data);
			$insertSql = "INSERT INTO `vs_entries` SET {$data}";

			// Debugging output
			error_log("Debug: INSERT SQL Query - $insertSql");

			$result = $this->conn->query($insertSql);
			if (!$result) {
				$resp['status'] = 'failed';
				$resp['msg'] = "Error executing INSERT query: " . mysqli_error($this->conn);
				return json_encode($resp);
			}

			foreach ($gr_id as $single_gr_id) {
				$updateSql = "UPDATE `tbl_gr_list` SET gr_status = '1' WHERE gr_id = '$single_gr_id'";
				$this->conn->query($updateSql);
			}
		}

		if($insertSql && $updateSql){
			$jid = !empty($id) ? $id : $this->conn->insert_id;
			$gl_data = '';
			$data = '';

				foreach ($account_code as $k => $v) {
					$doc = $doc_no[$k];
					$gtype = isset($_POST['gtype'][$k]) ? $_POST['gtype'][$k] : null;
				
					if ((int)$gtype == 2) {
						$amount_value = isset($amount[$k]) ? -$amount[$k] : 0;
					} else {
						$amount_value = isset($amount[$k]) ? $amount[$k] : 0;
					}
				
					$account_code_value = $account_code[$k];
					$vs_num_value = $vs_num;
				
					if (!empty($gl_data)) $gl_data .= ", ";
					$gl_data .= "('{$doc}','{$gtype}','AP','{$po_no}','{$gr_id[$k]}','{$vs_num_value}','{$amount_value}', '{$account_code_value}', NOW(), '0', '{$preparer}')";
				
					if (!empty($data)) $data .= ", ";
					$data .= "('{$gr_id[$k]}','{$v_num}','{$doc_no[$k]}','{$v}','{$group_id[$k]}','{$phase[$k]}','{$block[$k]}','{$lot[$k]}','{$amount[$k]}')";
				}
				
				if (!empty($gl_data)) {
					$gl_sql2 = "UPDATE `tbl_vs_attachments` SET `doc_no` = '$newDocNo'
					WHERE `num` = '$v_num' AND `doc_type` = 'AP'
					ORDER BY `date_attached` DESC
					LIMIT 1";
					$gl_sql3 = "DELETE FROM `tbl_vs_attachments` WHERE `doc_no` = 0 and `num` = '$v_num'";
					$gl_sql = "INSERT INTO `tbl_gl_trans` (`doc_no`, `gtype`, `doc_type`,`po_id`, `gr_id`,`vs_num`,`amount`,`account`,`journal_date`, `c_status`,`preparer`) VALUES {$gl_data}";
					$save_gl = $this->conn->query($gl_sql);
					$save_gl2 = $this->conn->query($gl_sql2);
					$save_gl3 = $this->conn->query($gl_sql3);
			
				
				$vs_items_sql = "INSERT INTO `vs_items` (`gr_id`,`journal_id`,`doc_no`,`account_id`,`group_id`,`phase`,`block`,`lot`,`amount`) VALUES {$data}";
				$save_vs_items = $this->conn->query($vs_items_sql);

				if ($save_gl && $save_vs_items && $save_gl2 && $save_gl3) {
					$resp['status'] = 'success';
					if (empty($id)) {
						$resp['msg'] = "Voucher Setup Entry has successfully added.";
					} else {
						$resp['msg'] = "Voucher Setup has been updated successfully.";
					}
				} else {
					$resp['status'] = 'failed';
					if (empty($id)) {
						$resp['msg'] = "Voucher Setup Entry has failed to save.";
					} else {
						$resp['msg'] = "Voucher Setup Entry has failed to update.";
					}
					$resp['error'] = $this->conn->error;
				}
			} else {
				$resp['status'] = 'failed';
				if (empty($id)) {
					$resp['msg'] = "Voucher Setup Entry has failed to save.";
				} else {
					$resp['msg'] = "Voucher Setup Entry has failed to update.";
				}
				$resp['error'] = "GL Transactions or Voucher Setup Entry Items data is empty";
			}
			$po_no = $this->conn->real_escape_string($po_no); 
			$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
			$sup_code = $_POST['supplier_id'];
			$gr_list_sql = "INSERT INTO `tbl_gr_list` (`po_id`,`doc_no`,`vs_num`,`gr_status`,`vs_status`,`supplier_id`) VALUES ('{$po_no}','{$doc_no_value}','{$v_num}','1','1','{$sup_code}')";
			
			$save_gr_list = $this->conn->query($gr_list_sql);
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error."[{$sql}]";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] =='success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}

	function save_voucher_nonpo(){
		if (empty($_POST['id'])) {
			$v_num = isset($_POST['v_num']) ? $_POST['v_num'] : '';
			$supplier_id = isset($_POST['supplier_id']) ? $_POST['supplier_id'] : '';
			$sup_code = isset($_POST['sup_code']) ? $_POST['sup_code'] : '';
			$newDocNo = isset($_POST['newDocNo']) ? $_POST['newDocNo'] : '';
			$_POST['user_id'] = $this->settings->userdata('user_code');
			$preparer = isset($_POST['preparer']) ? $_POST['preparer'] : '';
			$requester = isset($_POST['requester']) ? $_POST['requester'] : '';
		}
		
		if (!empty($supplier_id)) {
        
			$checkSupplierQuery =  $this->conn->query("SELECT id,name FROM `supplier_list` WHERE `id` = '{$supplier_id}'");
			
			if ($checkSupplierQuery->num_rows > 0) {
				$row = $checkSupplierQuery->fetch_assoc();
				$supplier_id = $row['id'];
			} else {
				$insertSupplierQuery =  $this->conn->query("INSERT INTO `supplier_list` (`name`, `short_name`,`status`) VALUES ('{$supplier_id}','{$supplier_id}', 1)");
			}
		}

		extract($_POST);
		$data = "";
		$gl_data = "";
		
		foreach ($_POST as $k => $v) {
			if (!is_array($_POST[$k])) {
				if ($k !== 'vs_num' && !in_array($k, array('id','gtype', 'name', 'newDocNo', 'ctr','sup_code'))) {
					if (!is_numeric($v) && !is_null($v))
						$v = $this->conn->real_escape_string($v);
					if (!empty($data)) $data .= ",";
					if (!is_null($v))
						$data .= " `{$k}`='{$v}' ";
					else
						$data .= " `{$k}`= NULL ";
				}
			}
		}
		
		if (empty($id)) {
			//$data = preg_replace('/\b(agent_id|emp_id|client_id)\b/', 'supplier_id', $data);
			//$sql = "INSERT INTO `vs_entries` set {$data} ";
			$sql = "INSERT INTO `vs_entries` (`v_num`,`supplier_id`,`journal_date`,`due_date`,`description`,`ref_no`,`user_id`,`requester`) VALUES ('{$v_num}','{$sup_code}','{$journal_date}','{$due_date}','{$description}','{$ref_no}','{$user_id}','{$requester}')";
		} 
		
		$save = $this->conn->query($sql);

		if($save){
			$data = "";
			foreach ($account_id as $k => $v) {
				$doc = $doc_no[$k];
				$gtype ='';

				$account_code_value = isset($account_id[$k]) ? $account_id[$k] : '';
				$vs_num_value = $vs_num;

				if ($amount[$k] < 0) {
					$gtype = 2; 
				} else {
					$gtype = 1; 
				}
			
				if (!empty($gl_data)) $gl_data .= ", ";
				$gl_data .= "('{$doc}','{$gtype}','{$vs_num_value}','AP','{$amount[$k]}', '{$account_code_value}', NOW(), '0', '{$preparer}')";
			}

			if (!empty($gl_data)) {

				$gl_sql2 = "UPDATE `tbl_vs_attachments`
				SET `doc_no` = '$newDocNo'
				WHERE `num` = '$v_num' AND `doc_type` = 'AP'
				ORDER BY `date_attached` DESC
				LIMIT 1";
				$gl_sql3 = "DELETE FROM `tbl_vs_attachments` WHERE `doc_no` = 0 and `num` = '$v_num'";
				$gl_sql = "INSERT INTO `tbl_gl_trans` (`doc_no`,`gtype`,`vs_num`,`doc_type`,`amount`,`account`,`journal_date`,`c_status`,`preparer`) VALUES {$gl_data}";
				$save_gl2 = $this->conn->query($gl_sql2);
				$save_gl3 = $this->conn->query($gl_sql3);
				$save_gl = $this->conn->query($gl_sql);
			
				if ($save_gl && $save_gl2 && $save_gl3) {
				//if ($save_gl) {
					$resp['status'] = 'success';
					if (empty($id)) {
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					} else {
						$resp['msg'] = " Voucher Setup has been updated successfully.";
					}
				}
			}
			
			$this->conn->query("DELETE FROM `vs_items` where journal_id = '{$v_num}'");
			foreach($account_id as $k=>$v){
				if(!empty($data)) $data .=", ";
				$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
				$data .= "('{$v_num}','{$doc_no_value}','{$v}','{$amount[$k]}')";
			}
			if(!empty($data)){
				$sql = "INSERT INTO `vs_items` (`journal_id`,`doc_no`,`account_id`,`amount`) VALUES {$data}";
				$save2 = $this->conn->query($sql);
				if($save2){
					$resp['status'] = 'success';
					if(empty($id)){
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					}else
						$resp['msg'] = " Voucher Setup has been updated successfully.";
				}else{
					$resp['status'] = 'failed';
					if(empty($id)){
						$resp['msg'] = $this->conn->error."[{$sql}]";
						$this->conn->query("DELETE FROM `vs_entries` where v_num = '{$v_num}'");
					}else
						$resp['msg'] = " Voucher Setup Entry has failed to update.";
					$resp['error'] = $this->conn->error;
				}
			}else{
				$resp['status'] = 'failed';
				if(empty($id)){
					$resp['msg'] = $this->conn->error."[{$sql}]";
					$this->conn->query("DELETE FROM `vs_entries` where v_num = '{$v_num}'");
				}else
					$resp['msg'] = " Voucher Setup Entry has failed to update.";
				$resp['error'] = "Voucher Setup Entry Items is empty";
			}

			$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
			//$sup_code = isset($_POST['emp_id']) ? $_POST['emp_id'] : (isset($_POST['agent_id']) ? $_POST['agent_id'] : (isset($_POST['client_id']) ? $_POST['client_id'] : $_POST['supplier_id']));

			$gr_list_sql = "INSERT INTO `tbl_gr_list` (`doc_no`,`vs_num`,`supplier_id`) VALUES ('{$doc_no_value}','{$v_num}','{$sup_code}')";
			
			$save_gr_list = $this->conn->query($gr_list_sql);

		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error."[{$sql}]";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] =='success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}

	function save_voucher(){
		if (empty($_POST['id'])) {
			$v_num = isset($_POST['v_num']) ? $_POST['v_num'] : '';
			$newDocNo = isset($_POST['newDocNo']) ? $_POST['newDocNo'] : '';
			$_POST['user_id'] = $this->settings->userdata('user_code');
		}

		extract($_POST);
		$data = "";
		$gl_data = "";
		
		foreach ($_POST as $k => $v) {
			if (!is_array($_POST[$k])) {
				if ($k !== 'vs_num' && !in_array($k, array('id','gtype', 'name', 'newDocNo', 'ctr','preparer'))) {
					if (!is_numeric($v) && !is_null($v))
						$v = $this->conn->real_escape_string($v);
					if (!empty($data)) $data .= ",";
					if (!is_null($v))
						$data .= " `{$k}`='{$v}' ";
					else
						$data .= " `{$k}`= NULL ";
				}
			}
		}
		
		if (empty($id)) {
			$data = preg_replace('/\b(agent_id|emp_id|client_id)\b/', 'supplier_id', $data);
			$sql = "INSERT INTO `vs_entries` set {$data} ";
		} 
		
		$save = $this->conn->query($sql);

		if($save){
			$data = "";
			foreach ($account_id as $k => $v) {
				$doc = $doc_no[$k];
				$gtype ='';

				$account_code_value = isset($account_id[$k]) ? $account_id[$k] : '';
				$vs_num_value = $vs_num;

				if ($amount[$k] < 0) {
					$gtype = 2; 
				} else {
					$gtype = 1; 
				}
			
				if (!empty($gl_data)) $gl_data .= ", ";
				$gl_data .= "('{$doc}','{$gtype}','{$vs_num_value}','AP','{$amount[$k]}', '{$account_code_value}', NOW(), '0', '{$preparer}')";
			}

			if (!empty($gl_data)) {

				$gl_sql2 = "UPDATE `tbl_vs_attachments`
				SET `doc_no` = '$newDocNo'
				WHERE `num` = '$v_num' AND `doc_type` = 'AP'
				ORDER BY `date_attached` DESC
				LIMIT 1";
				$gl_sql3 = "DELETE FROM `tbl_vs_attachments` WHERE `doc_no` = 0 and `num` = '$v_num'";
				$gl_sql = "INSERT INTO `tbl_gl_trans` (`doc_no`,`gtype`,`vs_num`,`doc_type`,`amount`,`account`,`journal_date`,`c_status`,`preparer`) VALUES {$gl_data}";
				$save_gl2 = $this->conn->query($gl_sql2);
				$save_gl3 = $this->conn->query($gl_sql3);
				$save_gl = $this->conn->query($gl_sql);
			
				if ($save_gl && $save_gl2 && $save_gl3) {
				//if ($save_gl) {
					$resp['status'] = 'success';
					if (empty($id)) {
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					} else {
						$resp['msg'] = " Voucher Setup has been updated successfully.";
					}
				}
			}
			
			$this->conn->query("DELETE FROM `vs_items` where journal_id = '{$v_num}'");
			foreach($account_id as $k=>$v){
				if(!empty($data)) $data .=", ";
				$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
				$data .= "('{$v_num}','{$doc_no_value}','{$v}','{$amount[$k]}')";
			}
			if(!empty($data)){
				$sql = "INSERT INTO `vs_items` (`journal_id`,`doc_no`,`account_id`,`amount`) VALUES {$data}";
				$save2 = $this->conn->query($sql);
				if($save2){
					$resp['status'] = 'success';
					if(empty($id)){
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					}else
						$resp['msg'] = " Voucher Setup has been updated successfully.";
				}else{
					$resp['status'] = 'failed';
					if(empty($id)){
						$resp['msg'] = $this->conn->error."[{$sql}]";
						$this->conn->query("DELETE FROM `vs_entries` where v_num = '{$v_num}'");
					}else
						$resp['msg'] = " Voucher Setup Entry has failed to update.";
					$resp['error'] = $this->conn->error;
				}
			}else{
				$resp['status'] = 'failed';
				if(empty($id)){
					$resp['msg'] = $this->conn->error."[{$sql}]";
					$this->conn->query("DELETE FROM `vs_entries` where v_num = '{$v_num}'");
				}else
					$resp['msg'] = " Voucher Setup Entry has failed to update.";
				$resp['error'] = "Voucher Setup Entry Items is empty";
			}

			$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
			$sup_code = isset($_POST['emp_id']) ? $_POST['emp_id'] : (isset($_POST['agent_id']) ? $_POST['agent_id'] : (isset($_POST['client_id']) ? $_POST['client_id'] : $_POST['supplier_id']));

			$gr_list_sql = "INSERT INTO `tbl_gr_list` (`doc_no`,`vs_num`,`supplier_id`) VALUES ('{$doc_no_value}','{$v_num}','{$sup_code}')";
			
			$save_gr_list = $this->conn->query($gr_list_sql);

		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error."[{$sql}]";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] =='success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}

	function save_jv(){
		if (empty($_POST['id'])) {
			$jv_num = isset($_POST['jv_num']) ? $_POST['jv_num'] : '';
			$doc_type = isset($_POST['doc_type']) ? $_POST['doc_type'] : '';
			$newDocNo = isset($_POST['newDocNo']) ? $_POST['newDocNo'] : '';
			$_POST['user_id'] = $this->settings->userdata('user_code');
		}
		
		extract($_POST);
		$data = "";
		$gl_data = "";
		
		foreach ($_POST as $k => $v) {
			if (!is_array($_POST[$k])) {
				if ($k !== 'jv_num' && !in_array($k, array('id','gtype','newDocNo','ctr', 'vs_num','c_status'))) {
					if (!is_numeric($v) && !is_null($v))
						$v = $this->conn->real_escape_string($v);
					if (!empty($data)) $data .= ",";
					if (!is_null($v))
						$data .= " `{$k}`='{$v}' ";
					else
						$data .= " `{$k}`= NULL ";
				}
			}
		}
		if (!empty($jv_num)) {
			if (!empty($data)) $data .= ",";
			$data .= " `jv_num`='{$jv_num}' ";
		}
		if (empty($id)) {
			$sql = "INSERT INTO `jv_entries` set {$data} ";
		}
		
		$save = $this->conn->query($sql);

		if($save){
			
			$data = "";
			foreach ($account_id as $k => $v) {
				$doc = $doc_no[$k];
				$gtype ='';

				$account_code_value = isset($account_id[$k]) ? $account_id[$k] : '';
				$jv_num_value = $jv_num;

				if ($amount[$k] < 0) {
					$gtype = 2; 
				} else {
					$gtype = 1; 
				}
			
				if (!empty($gl_data)) $gl_data .= ", ";
				$gl_data .= "('{$doc}','{$gtype}','{$jv_num_value}','JV','{$amount[$k]}', '{$account_code_value}', NOW(),'{$c_status}')";
			}

			if (!empty($gl_data)) {
				$gl_sql2 = "UPDATE `tbl_vs_attachments` SET `doc_no`  = '$newDocNo' WHERE `num` = '$jv_num' and `doc_type` = 'JV'";
				$gl_sql = "INSERT INTO `tbl_gl_trans` (`doc_no`, `gtype`, `jv_num`,`doc_type`, `amount`,`account`,`journal_date`,`c_status`) VALUES {$gl_data}";
				$save_gl = $this->conn->query($gl_sql);
				$save_gl2 = $this->conn->query($gl_sql2);
				if ($save_gl && $save_gl2) {
					$resp['status'] = 'success';
					if (empty($id)) {
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					} else {
						$resp['msg'] = " Voucher Setup has been updated successfully.";
					}
				}
			}
			
			$this->conn->query("DELETE FROM `jv_items` where journal_id = '{$jv_num}'");
			foreach($account_id as $k=>$v){
				if(!empty($data)) $data .=", ";
				$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
				$data .= "('{$jv_num}','{$doc_no_value}','{$v}','{$amount[$k]}')";
			}
			if(!empty($data)){
				$sql = "INSERT INTO `jv_items` (`journal_id`,`doc_no`,`account_id`,`amount`) VALUES {$data}";
				$save2 = $this->conn->query($sql);
				if($save2){
					$resp['status'] = 'success';
					if(empty($id)){
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					}else
						$resp['msg'] = " Voucher Setup has been updated successfully.";
				}else{
					$resp['status'] = 'failed';
					if(empty($id)){
						$resp['msg'] = $this->conn->error."[{$sql}]";
						$this->conn->query("DELETE FROM `jv_entries` where jv_num = '{$jv_num}'");
					}else
						$resp['msg'] = " Voucher Setup Entry has failed to update.";
					$resp['error'] = $this->conn->error;
				}
			}else{
				$resp['status'] = 'failed';
				if(empty($id)){
					$resp['msg'] = $this->conn->error."[{$sql}]";
					$this->conn->query("DELETE FROM `jv_entries` where jv_num = '{$jv_num}'");
				}else
					$resp['msg'] = " Voucher Setup Entry has failed to update.";
				$resp['error'] = "Voucher Setup Entry Items is empty";
			}

			$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
			$gr_list_sql = "INSERT INTO `tbl_gr_list` (`doc_no`,`jv_num`) VALUES ('{$doc_no_value}','{$jv_num}')";
			$save_gr_list = $this->conn->query($gr_list_sql);

		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error."[{$sql}]";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] =='success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}

	function modify_jv(){
		if(empty($_POST['id'])){
			$jv_num = isset($_POST['jv_num']) ? $_POST['jv_num'] : '';
			$newDocNo = isset($_POST['newDocNo']) ? $_POST['newDocNo'] : '';
			$doc_type = isset($_POST['doc_type']) ? $_POST['doc_type'] : '';
			$_POST['user_id'] = $this->settings->userdata('user_code');
		}
		extract($_POST);
		$data = "";
		$gl_data = "";
		$doc_no = $_POST['doc_no'];
		foreach($_POST as $k =>$v){
			if($k !== 'vs_num' && !in_array($k,array('id','gtype','newDocNo','ctr','vs_num','c_status'))  && !is_array($_POST[$k])){
				if(!is_numeric($v) && !is_null($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				if(!is_null($v))
				$data .= " `{$k}`='{$v}' ";
				else
				$data .= " `{$k}`= NULL ";
			}
		}
		//$data = preg_replace('/\b(agent_id|emp_id|client_id)\b/', 'supplier_id', $data);
		$sql = "UPDATE `jv_entries` set {$data} where jv_num = '{$jv_num}' ";

		$save = $this->conn->query($sql);

		if($save){
			$data = "";
			$this->conn->query("DELETE FROM `tbl_gl_trans` WHERE doc_no = '$newDocNo'");
			$this->conn->query("DELETE FROM `tbl_gr_list` WHERE doc_no = '$newDocNo'");
			
			foreach ($account_id as $k => $v) {

				$doc = $doc_no[$k];
				
				$gtype ='';

				$account_code_value = isset($account_id[$k]) ? $account_id[$k] : '';
				$jv_num_value = $jv_num;

				if ($amount[$k] < 0) {
					$gtype = 2; 
				} else {
					$gtype = 1; 
				}
			
				if (!empty($gl_data)) $gl_data .= ", ";
				$gl_data .= "('{$doc}','{$gtype}','{$jv_num_value}','JV','{$amount[$k]}', '{$account_code_value}', NOW(),'{$c_status}')";
			}
			
			if (!empty($gl_data)) {
				$gl_sql2 = "UPDATE `tbl_vs_attachments` SET `doc_no`  = '$newDocNo' WHERE `num` = '$jv_num' and `doc_type` = 'JV'";
				$gl_sql = "INSERT INTO `tbl_gl_trans` (`doc_no`, `gtype`, `jv_num`,`doc_type`,`amount`,`account`,`journal_date`,`c_status`) VALUES {$gl_data}";
				$save_gl = $this->conn->query($gl_sql);
				$save_gl2 = $this->conn->query($gl_sql2);
				if ($save_gl && $save_gl2) {
					$resp['status'] = 'success';
					if (empty($id)) {
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					} else {
						$resp['msg'] = " Voucher Setup has been updated successfully.";
					}
				}
			}
			$this->conn->query("DELETE FROM `jv_items` where journal_id = '{$jv_num}'");
			foreach($account_id as $k=>$v){
				if(!empty($data)) $data .=", ";
				$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
				$data .= "('{$jv_num}','{$doc_no_value}','{$v}','{$amount[$k]}')";
			}
			if(!empty($data)){
				$sql = "INSERT INTO `jv_items` (`journal_id`,`doc_no`,`account_id`,`amount`) VALUES {$data}";
				$save2 = $this->conn->query($sql);
				if($save2){
					$resp['status'] = 'success';
					if(empty($id)){
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					}else
						$resp['msg'] = " Voucher Setup has been updated successfully.";
				}else{
					$resp['status'] = 'failed';
					if(empty($id)){
						$resp['msg'] = $this->conn->error."[{$sql}]";
						$this->conn->query("DELETE FROM `jv_entries` where jv_num = '{$jv_num}'");
					}else
						$resp['msg'] = " Voucher Setup Entry has failed to update.";
					$resp['error'] = $this->conn->error;
				}
			}else{
				$resp['status'] = 'failed';
				if(empty($id)){
					$resp['msg'] = $this->conn->error."[{$sql}]";
					$this->conn->query("DELETE FROM `jv_entries` where jv_num = '{$jv_num}'");
				}else
					$resp['msg'] = " Voucher Setup Entry has failed to update.";
				$resp['error'] = "Voucher Setup Entry Items is empty";
			}
			$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
			$gr_list_sql = "INSERT INTO `tbl_gr_list` (`doc_no`,`vs_num`) VALUES ('{$doc_no_value}','{$jv_num}')";
			$save_gr_list = $this->conn->query($gr_list_sql);
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error."[{$sql}]";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] =='success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}

	function modify_voucher_supplier(){
		if(!empty($_POST['id'])){
			$v_num = isset($_POST['v_num']) ? $_POST['v_num'] : '';
			$newDocNo = isset($_POST['newDocNo']) ? $_POST['newDocNo'] : '';
			$_POST['user_id'] = $this->settings->userdata('user_code');
			$preparer = isset($_POST['preparer']) ? $_POST['preparer'] : '';
		}
		extract($_POST);
		$data = "";
		$gl_data = "";
		foreach ($_POST as $k => $v) {
			if (!is_array($_POST[$k])) {
				if ($k === 'vs_num') {
					//echo "Value of vs_num: $v";
				}
		
				if ($k !== 'vs_num' && !in_array($k, array('id','newDocNo'))) {
					if (!is_numeric($v) && !is_null($v))
						$v = $this->conn->real_escape_string($v);
					if (!empty($data)) $data .= ",";
					if (!is_null($v))
						$data .= " `{$k}`='{$v}' ";
					else
						$data .= " `{$k}`= NULL ";
				}
			}
		}
		$data = preg_replace('/\b(agent_id|emp_id|client_id)\b/', 'supplier_id', $data);
		$sql = "UPDATE `vs_entries` set {$data} where v_num = '{$v_num}' ";
		
		$save = $this->conn->query($sql);
		if($save){
			$data = "";
			$this->conn->query("DELETE FROM `tbl_gl_trans` where doc_no = " . $newDocNo);
			$this->conn->query("DELETE FROM `tbl_gr_list` where doc_no = " . $newDocNo);
			foreach ($account_id as $k => $v) {

				$doc = $doc_no[$k];
				$gtype = isset($_POST['gtype'][$k]) ? $_POST['gtype'][$k] : null;

				if ((int)$gtype == 2) {
					$amount_value = isset($amount[$k]) ? -$amount[$k] : 0;
				} else {
					$amount_value = isset($amount[$k]) ? $amount[$k] : 0;
				}

				$account_code_value = $account_code[$k];
				$vs_num_value = $vs_num;
			
				if (!empty($gl_data)) $gl_data .= ", ";
				$gl_data .= "('{$doc}','{$gtype}','AP','{$po_no}','{$gr_id[$k]}','{$vs_num_value}','{$amount_value}', '{$account_code_value}', NOW(), '0', '{$preparer}')";
			}
			
			if (!empty($gl_data)) {
				$gl_sql2 = "UPDATE `tbl_vs_attachments`
					SET `doc_no` = '$newDocNo'
					WHERE `num` = '$v_num' AND `doc_type` = 'AP'
					ORDER BY `date_attached` DESC
					LIMIT 1";
					$gl_sql3 = "DELETE FROM `tbl_vs_attachments` WHERE `doc_no` = 0 and `num` = '$v_num'";
					$gl_sql = "INSERT INTO `tbl_gl_trans` (`doc_no`, `gtype`, `doc_type`,`po_id`, `gr_id`,`vs_num`,`amount`,`account`,`journal_date`,`c_status`,`preparer`) VALUES {$gl_data}";
					$save_gl = $this->conn->query($gl_sql);
					$save_gl2 = $this->conn->query($gl_sql2);
					$save_gl3 = $this->conn->query($gl_sql3);
			
				if ($save_gl && $save_gl2 && $save_gl3) {
					$resp['status'] = 'success';
					if (empty($id)) {
						$resp['msg'] = " Voucher Setup Entry has successfully added. GL Transactions have been successfully added.";
					} else {
						$resp['msg'] = " Voucher Setup has been updated successfully. GL Transactions have been successfully updated.";
					}
				}
			}
			
			$this->conn->query("DELETE FROM `vs_items` where journal_id = '{$v_num}'");
			foreach($account_id as $k=>$v){
				if(!empty($data)) $data .=", ";
				$data .= "('{$gr_id[$k]}','{$v_num}','{$doc_no[$k]}','{$v}','{$group_id[$k]}','{$phase[$k]}','{$block[$k]}','{$lot[$k]}','{$amount[$k]}')";
			}
			if(!empty($data)){
				$sql = "INSERT INTO `vs_items` (`gr_id`,`journal_id`,`doc_no`,`account_id`,`group_id`,`phase`,`block`,`lot`,`amount`) VALUES {$data}";
				$save2 = $this->conn->query($sql);
				if($save2){
					$resp['status'] = 'success';
					if(empty($id)){
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					}else
						$resp['msg'] = " Voucher Setup has been updated successfully.";
				}else{
					$resp['status'] = 'failed';
					if(empty($id)){
						$resp['msg'] = $this->conn->error."[{$sql}]";
						$this->conn->query("DELETE FROM `vs_entries` where v_num = '{$v_num}'");
					}else
						$resp['msg'] = " Voucher Setup Entry has failed to update.";
					$resp['error'] = $this->conn->error;
				}
			}else{
				$resp['status'] = 'failed';
				if(empty($id)){
					$resp['msg'] = $this->conn->error."[{$sql}]";
					$this->conn->query("DELETE FROM `vs_entries` where v_num = '{$v_num}'");
				}else
					$resp['msg'] = " Voucher Setup Entry has failed to update.";
				$resp['error'] = "Voucher Setup Entry Items is empty";
			}
			$po_no = $this->conn->real_escape_string($po_no); 
			$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
			$sup_code = $_POST['supplier_id'];
			
			$vs_status = 1;
			$gr_list_sql = "INSERT INTO `tbl_gr_list` (`po_id`,`doc_no`,`vs_num`,`vs_status`,`supplier_id`) VALUES ('{$po_no}','{$doc_no_value}','{$v_num}','{$vs_status}','{$sup_code}')";
			$save_gr_list = $this->conn->query($gr_list_sql);
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error."[{$sql}]";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] =='success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}

	function modify_cv() {
		$c_num = isset($_POST['c_num']) ? $_POST['c_num'] : '';
		$v_num = isset($_POST['v_num']) ? $_POST['v_num'] : '';

		extract($_POST);
		
		$data = "";
		$gl_data = "";
		$doc_no = $_POST['doc_no'];
		foreach($_POST as $k =>$v){
			if ($k === 'divChoice') {
				continue; 
			}
			if(!in_array($k,array('id','gtype','vat_amount','div_amount','apamount','ctr','vs_num'))  && !is_array($_POST[$k])){
				if(!is_numeric($v) && !is_null($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				if(!is_null($v))
				$data .= " `{$k}`='{$v}' ";
				else
				$data .= " `{$k}`= NULL ";
			}
		}

		$sql = "UPDATE `cv_entries` SET `ref_no`='{$ref_no}', `check_name` = '{$check_name}', `date_updated`=NOW() WHERE c_num = '{$c_num}' ";

		$save = $this->conn->query($sql);
		
		if($save){
			$data = "";
			$this->conn->query("DELETE FROM `tbl_gl_trans` where cv_num = " . (int)($c_num[0]));
			$this->conn->query("DELETE FROM `tbl_gr_list` where cv_num = " . (int)($c_num[0]));
			
			foreach ($account_id as $k => $v) {

				$doc = $doc_no[$k];

				$gtype ='';
				if ($amount[$k] < 0) {
					$gtype = 2; 
				} else {
					$gtype = 1; 
				}
				
				$account_code_value = $account_id[$k];
				$vs_num_value = $v_num;

				if (!empty($gl_data)) $gl_data .= ", ";
				$gl_data .= "('{$doc}','{$gtype}','{$vs_num_value}','{$c_num}','CV','{$amount[$k]}', '{$account_code_value}', NOW())";
			}
			if (!empty($gl_data)) {
				$gl_sql = "INSERT INTO `tbl_gl_trans` (`doc_no`,`gtype`,`vs_num`,`cv_num`,`doc_type`,`amount`,`account`,`journal_date`) VALUES {$gl_data}";
				
				$save_gl = $this->conn->query($gl_sql);
			
				if ($save_gl) {
					$resp['status'] = 'success';
					if (empty($id)) {
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					} else {
						$resp['msg'] = " Voucher Setup has been updated successfully.";
					}
				}
			}
			$this->conn->query("DELETE FROM `cv_items` where journal_id = '{$c_num}'");
			
			foreach($account_id as $k=>$v){
				if(!empty($data)) $data .=", ";
				$data .= "('{$c_num}','{$v}','{$amount[$k]}')";
			}
			if(!empty($data)){
				$sql = "INSERT INTO `cv_items` (`journal_id`,`account_id`,`amount`) VALUES {$data}";
				$save2 = $this->conn->query($sql);
				if($save2){
					$resp['status'] = 'success';
					if(empty($id)){
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					}else
						$resp['msg'] = " Voucher Setup has been updated successfully.";
				}else{
					$resp['status'] = 'failed';
					if(empty($id)){
						$resp['msg'] = $this->conn->error."[{$sql}]";
						$this->conn->query("DELETE FROM `vs_entries` where c_num = '{$c_num}'");
					}else
						$resp['msg'] = " Voucher Setup Entry has failed to update.";
					$resp['error'] = $this->conn->error;
				}
			}else{
				$resp['status'] = 'failed';
				if(empty($id)){
					$resp['msg'] = $this->conn->error."[{$sql}]";
					$this->conn->query("DELETE FROM `cv_entries` where c_num = '{$c_num}'");
				}else
					$resp['msg'] = " Voucher Setup Entry has failed to update.";
				$resp['error'] = "Voucher Setup Entry Items is empty";
			}
			$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
			$sup_code = $_POST['supplier_id'];
			$gr_list_sql = "INSERT INTO `tbl_gr_list` (`doc_no`,`vs_num`,`cv_num`,`supplier_id`) VALUES ('{$doc_no_value}','{$v_num}','{$c_num}','{$sup_code}')";
			$save_gr_list = $this->conn->query($gr_list_sql);
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error."[{$sql}]";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] =='success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}

	function save_vs(){
		if(!empty($_POST['id'])){
			$prefix = date("Ym-");
			$code = sprintf("%'.05d",1);
			while(true){
				$check = $this->conn->query("SELECT * FROM `vs_entries` where `code` = '{$prefix}{$code}' ")->num_rows;
				if($check > 0){
					$code = sprintf("%'.05d",ceil($code) + 1);
				}else{
					break;
				}
			}
			$_POST['code'] = $prefix.$code;
			$_POST['user_id'] = $this->settings->userdata('user_code');
		}
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			
			if(!in_array($k,array('id'))  && !is_array($_POST[$k])){
				if(!is_numeric($v) && !is_null($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				if(!is_null($v))
				$data .= " `{$k}`='{$v}' ";
				else
				$data .= " `{$k}`= NULL ";
			}
		}
		$data = preg_replace('/\b(agent_id|emp_id|client_id)\b/', 'supplier_id', $data);
		if(!empty($id)){

			$sql = "INSERT INTO `vs_entries` set {$data} ";
		}else{
			$sql = "UPDATE `vs_entries` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			//$jid = !empty($id) ? $id : $this->conn->insert_id;
			$data = "";
			$this->conn->query("DELETE FROM `vs_items` where journal_id = '{$v_num}'");
			foreach($account_id as $k=>$v){
				if(!empty($data)) $data .=", ";
				$data .= "('{$v_num}','{$v}','{$group_id[$k]}','{$phase[$k]}','{$block[$k]}','{$lot[$k]}','{$amount[$k]}')";
			}
			if(!empty($data)){
				$sql = "INSERT INTO `vs_items` (`journal_id`,`account_id`,`group_id`,`phase`,`block`,`lot`,`amount`) VALUES {$data}";
				$save2 = $this->conn->query($sql);
				if($save2){
					$resp['status'] = 'success';
					if(empty($id)){
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					}else
						$resp['msg'] = " Voucher Setup has been updated successfully.";
				}else{
					$resp['status'] = 'failed';
					if(empty($id)){
						$resp['msg'] = " Voucher Setup Entry has failed to save.";
						$this->conn->query("DELETE FROM `vs_entries` where v_num = '{$v_num}'");
					}else
						$resp['msg'] = " Voucher Setup Entry has failed to update.";
					$resp['error'] = $this->conn->error;
				}
			}else{
				$resp['status'] = 'failed';
				if(empty($id)){
					$resp['msg'] = " Voucher Setup Entry has failed to save.";
					$this->conn->query("DELETE FROM `vs_entries` where v_num = '{$v_num}'");
				}else
					$resp['msg'] = " Voucher Setup Entry has failed to update.";
				$resp['error'] = "Voucher Setup Entry Items is empty";
			}
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occured.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] =='success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_vs(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `vs_entries` where v_num = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Voucher Setup Entry has been deleted successfully.");

		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function approved_vs(){
		extract($_POST);
	
		$approved_trans = $this->conn->query("UPDATE `tbl_gl_trans` SET c_status = 1,c_status2 = 1 WHERE vs_num = '{$id}'");
		$approved_entries = $this->conn->query("UPDATE `vs_entries` SET c_status = 1 WHERE v_num = '{$id}'");
	
		if($approved_trans && $approved_entries){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Voucher Setup Entry has been approved successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
	
		return json_encode($resp);
	}

	function disapproved_vs(){
		extract($_POST);
	
		$approved_trans = $this->conn->query("UPDATE `tbl_gl_trans` SET c_status = 2,c_status2 = 2 WHERE vs_num = '{$id}'");
		$approved_entries = $this->conn->query("UPDATE `vs_entries` SET c_status = 2 WHERE v_num = '{$id}'");
	
		if($approved_trans && $approved_entries){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Voucher Setup Entry has been approved successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
	
		return json_encode($resp);
	}
	
	function approved_cv(){
		extract($_POST);
	
		$approved_trans = $this->conn->query("UPDATE `tbl_gl_trans` SET c_status = 1 WHERE cv_num = '{$id}'");
		$approved_entries = $this->conn->query("UPDATE `cv_entries` SET c_status = 1 WHERE c_num = '{$id}'");
	
		if($approved_trans && $approved_entries){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Voucher Setup Entry has been approved successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
	
		return json_encode($resp);
	}

	function approved_cv_cfo(){
		extract($_POST);
	
		$approved_trans = $this->conn->query("UPDATE `tbl_gl_trans` SET c_status2 = 1 WHERE cv_num = '{$id}'");
		$approved_entries = $this->conn->query("UPDATE `cv_entries` SET c_status2 = 1 WHERE c_num = '{$id}'");
	
		if($approved_trans && $approved_entries){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Voucher Setup Entry has been approved successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
	
		return json_encode($resp);
	}

	function approved_jv(){
		extract($_POST);
	
		$approved_trans = $this->conn->query("UPDATE `tbl_gl_trans` SET c_status = 1,c_status2 = 1 WHERE jv_num = '{$id}'");
		$approved_entries = $this->conn->query("UPDATE `jv_entries` SET c_status = 1 WHERE jv_num = '{$id}'");
	
		if($approved_trans && $approved_entries){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Voucher Setup Entry has been approved successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
	
		return json_encode($resp);
	}

	function disapproved_jv(){
		extract($_POST);
	
		$disapproved_trans = $this->conn->query("UPDATE `tbl_gl_trans` SET c_status = 2,c_status2 = 2 WHERE jv_num = '{$id}'");
		$disapproved_entries = $this->conn->query("UPDATE `jv_entries` SET c_status = 2 WHERE jv_num = '{$id}'");
	
		if($disapproved_trans && $disapproved_entries){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Voucher Setup Entry has been disapproved successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
	
		return json_encode($resp);
	}

	function disapproved_cv_cfo(){
		extract($_POST);
	
		$disapproved_trans = $this->conn->query("UPDATE `tbl_gl_trans` SET c_status2 = 2 WHERE cv_num = '{$id}'");
		$disapproved_entries = $this->conn->query("UPDATE `cv_entries` SET c_status2 = 2 WHERE c_num = '{$id}'");
	
		if($disapproved_trans && $disapproved_entries){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Voucher Setup Entry has been disapproved successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
	
		return json_encode($resp);
	}

	function disapproved_cv(){
		extract($_POST);
	
		$disapproved_trans = $this->conn->query("UPDATE `tbl_gl_trans` SET c_status = 2 WHERE cv_num = '{$id}'");
		$disapproved_entries = $this->conn->query("UPDATE `cv_entries` SET c_status = 2 WHERE c_num = '{$id}'");
	
		if($disapproved_trans && $disapproved_entries){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Voucher Setup Entry has been disapproved successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
	
		return json_encode($resp);
	}
	
	function approved_rfp(){
		extract($_POST);
		$app = $this->conn->query("UPDATE tbl_rfp_approvals AS a
		JOIN tbl_rfp AS r ON a.rfp_no = r.rfp_no
		SET
		  a.status1 = CASE WHEN r.status1 = '{$userId}' THEN 1 ELSE a.status1 END,
		  a.status2 = CASE WHEN r.status2 = '{$userId}' THEN 1 ELSE a.status2 END,
		  a.status3 = CASE WHEN r.status3 = '{$userId}' THEN 1 ELSE a.status3 END,
		  a.status4 = CASE WHEN r.status4 = '{$userId}' THEN 1 ELSE a.status4 END,
		  a.status5 = CASE WHEN r.status5 = '{$userId}' THEN 1 ELSE a.status5 END,
		  a.status6 = CASE WHEN r.status6 = '{$userId}' THEN 1 ELSE a.status6 END,
		  a.status7 = CASE WHEN r.status7 = '{$userId}' THEN 1 ELSE a.status7 END
		WHERE a.rfp_no = '{$id}';
		");
		if($app){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Request for payment has been approved successfully.");

		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function approved_tba(){
		extract($_POST);
		$app = $this->conn->query("UPDATE tbl_tba_approvals AS a
		JOIN tbl_tba AS r ON a.tba_no = r.tba_no
		SET
		  a.status1 = CASE WHEN r.status1 = '{$userId}' THEN 1 ELSE a.status1 END,
		  a.status2 = CASE WHEN r.status2 = '{$userId}' THEN 1 ELSE a.status2 END,
		  a.status3 = CASE WHEN r.status3 = '{$userId}' THEN 1 ELSE a.status3 END,
		  a.status4 = CASE WHEN r.status4 = '{$userId}' THEN 1 ELSE a.status4 END,
		  a.status5 = CASE WHEN r.status5 = '{$userId}' THEN 1 ELSE a.status5 END,
		  a.status6 = CASE WHEN r.status6 = '{$userId}' THEN 1 ELSE a.status6 END,
		  a.status7 = CASE WHEN r.status7 = '{$userId}' THEN 1 ELSE a.status7 END
		WHERE a.tba_no = '{$id}';
		");
		if($app){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," TBA has been approved successfully.");

		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function disapproved_rfp(){
		extract($_POST);
		$app = $this->conn->query("UPDATE tbl_rfp_approvals AS a
			JOIN tbl_rfp AS r ON a.rfp_no = r.rfp_no
			SET
				a.status1 = CASE WHEN r.status1 = '{$userId}' THEN 2 ELSE a.status1 END,
				a.status2 = CASE WHEN r.status2 = '{$userId}' THEN 2 ELSE a.status2 END,
				a.status3 = CASE WHEN r.status3 = '{$userId}' THEN 2 ELSE a.status3 END,
				a.status4 = CASE WHEN r.status4 = '{$userId}' THEN 2 ELSE a.status4 END,
				a.status5 = CASE WHEN r.status5 = '{$userId}' THEN 2 ELSE a.status5 END,
				a.status6 = CASE WHEN r.status6 = '{$userId}' THEN 2 ELSE a.status6 END,
		  		a.status7 = CASE WHEN r.status7 = '{$userId}' THEN 2 ELSE a.status7 END
			WHERE a.rfp_no = '{$id}';
		");
		if($app){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Request for payment has been disapproved successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function disapproved_tba(){
		extract($_POST);
		$app = $this->conn->query("UPDATE tbl_tba_approvals AS a
			JOIN tbl_tba AS r ON a.tba_no = r.tba_no
			SET
				a.status1 = CASE WHEN r.status1 = '{$userId}' THEN 2 ELSE a.status1 END,
				a.status2 = CASE WHEN r.status2 = '{$userId}' THEN 2 ELSE a.status2 END,
				a.status3 = CASE WHEN r.status3 = '{$userId}' THEN 2 ELSE a.status3 END,
				a.status4 = CASE WHEN r.status4 = '{$userId}' THEN 2 ELSE a.status4 END,
				a.status5 = CASE WHEN r.status5 = '{$userId}' THEN 2 ELSE a.status5 END,
				a.status6 = CASE WHEN r.status6 = '{$userId}' THEN 2 ELSE a.status6 END,
		  		a.status7 = CASE WHEN r.status7 = '{$userId}' THEN 2 ELSE a.status7 END
			WHERE a.tba_no = '{$id}';
		");
		if($app){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Request for payment has been disapproved successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	function claim_cv(){
		extract($_POST);
		$del = $this->conn->query("UPDATE check_details SET c_status = 1, date_updated = NOW() WHERE check_id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Check Voucher has been updated successfully.");

		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function unclaimed_cv(){
		extract($_POST);
		$del = $this->conn->query("UPDATE check_details SET c_status = 0,date_updated = NOW() where check_id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Check Voucher has been updated successfully.");

		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function modify_voucher_nonpo(){
		if(empty($_POST['id'])){
			$v_num = isset($_POST['v_num']) ? $_POST['v_num'] : '';
			$newDocNo = isset($_POST['newDocNo']) ? $_POST['newDocNo'] : '';
			$_POST['user_id'] = $this->settings->userdata('user_code');
			$preparer = isset($_POST['preparer']) ? $_POST['preparer'] : '';
			$requester = isset($_POST['requester']) ? $_POST['requester'] : '';
		}
		extract($_POST);
		$data = "";
		$gl_data = "";
		$doc_no = $_POST['doc_no'];
		foreach($_POST as $k =>$v){
			if($k !== 'vs_num' && !in_array($k,array('id','gtype','newDocNo','ctr','name','preparer'))  && !is_array($_POST[$k])){
				if(!is_numeric($v) && !is_null($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				if(!is_null($v))
				$data .= " `{$k}`='{$v}' ";
				else
				$data .= " `{$k}`= NULL ";
			}
		}
		$sql = "UPDATE `vs_entries` SET `requester`= '{$requester}',`rfp_no` = '{$rfp_no}', `due_date` = '{$due_date}', `description` = '{$description}', `ref_no` = '{$ref_no}', `user_id` = '{$user_id}' WHERE `v_num` = $v_num";

		$save = $this->conn->query($sql);
		

		if($save){
			$data = "";
			$this->conn->query("DELETE FROM `tbl_gl_trans` where doc_no = " . $newDocNo);
			$this->conn->query("DELETE FROM `tbl_gr_list` where doc_no = " . $newDocNo);
			
			foreach ($account_id as $k => $v) {

				$doc = $doc_no[$k];

				$gtype ='';
				if ($amount[$k] < 0) {
					$gtype = 2; 
				} else {
					$gtype = 1; 
				}
				
				$account_code_value = $account_id[$k];
				$vs_num_value = $v_num;
			
				if (!empty($gl_data)) $gl_data .= ", ";
				$gl_data .= "('{$doc}','{$gtype}','{$vs_num_value}','AP','{$amount[$k]}', '{$account_code_value}', NOW(), '0', '{$preparer}')";
			}
			
			if (!empty($gl_data)) {
				$gl_sql2 = "UPDATE `tbl_vs_attachments`
				SET `doc_no` = '$newDocNo'
				WHERE `num` = '$v_num' AND `doc_type` = 'AP'
				ORDER BY `date_attached` DESC
				LIMIT 1";
				$gl_sql3 = "DELETE FROM `tbl_vs_attachments` WHERE `doc_no` = 0 and `num` = '$v_num'";
				$gl_sql = "INSERT INTO `tbl_gl_trans` (`doc_no`,`gtype`,`vs_num`,`doc_type`,`amount`,`account`,`journal_date`,`c_status`,`preparer`) VALUES {$gl_data}";
				$save_gl2 = $this->conn->query($gl_sql2);
				$save_gl3 = $this->conn->query($gl_sql3);
				$save_gl = $this->conn->query($gl_sql);
			
				if ($save_gl && $save_gl2 && $save_gl3) {
					$resp['status'] = 'success';
					if (empty($id)) {
						$resp['msg'] = " Voucher Setup Entry has successfully added. GL Transactions have been successfully added.";
					} else {
						$resp['msg'] = " Voucher Setup has been updated successfully. GL Transactions have been successfully updated.";
					}
				}
			}
			$this->conn->query("DELETE FROM `vs_items` where doc_no = " . $newDocNo);
			
			foreach($account_id as $k=>$v){
				if(!empty($data)) $data .=", ";
				$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
				$data .= "('{$v_num}','{$doc_no_value}','{$v}','{$amount[$k]}')";
			}
			if(!empty($data)){
				$sql = "INSERT INTO `vs_items` (`journal_id`,`doc_no`,`account_id`,`amount`) VALUES {$data}";
				$save2 = $this->conn->query($sql);
				if($save2){
					$resp['status'] = 'success';
					if(empty($id)){
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					}else
						$resp['msg'] = " Voucher Setup has been updated successfully.";
				}else{
					$resp['status'] = 'failed';
					if(empty($id)){
						$resp['msg'] = $this->conn->error."[{$sql}]";
						$this->conn->query("DELETE FROM `vs_entries` where v_num = '{$v_num}'");
					}else
						$resp['msg'] = " Voucher Setup Entry has failed to update.";
					$resp['error'] = $this->conn->error;
				}
			}else{
				$resp['status'] = 'failed';
				if(empty($id)){
					$resp['msg'] = $this->conn->error."[{$sql}]";
					$this->conn->query("DELETE FROM `vs_entries` where v_num = '{$v_num}'");
				}else
					$resp['msg'] = " Voucher Setup Entry has failed to update.";
				$resp['error'] = "Voucher Setup Entry Items is empty";
			}
			$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
			//$sup_code = isset($_POST['emp_id']) ? $_POST['emp_id'] : (isset($_POST['agent_id']) ? $_POST['agent_id'] : (isset($_POST['client_id']) ? $_POST['client_id'] : $_POST['supplier_id']));
			$gr_list_sql = "INSERT INTO `tbl_gr_list` (`doc_no`,`vs_num`,`supplier_id`) VALUES ('{$doc_no_value}','{$vs_num_value}','{$sup_code}')";
			$save_gr_list = $this->conn->query($gr_list_sql);
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error."[{$sql}]";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] =='success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function modify_voucher(){
		if(empty($_POST['id'])){
			$v_num = isset($_POST['v_num']) ? $_POST['v_num'] : '';
			$newDocNo = isset($_POST['newDocNo']) ? $_POST['newDocNo'] : '';
			$_POST['user_id'] = $this->settings->userdata('user_code');
		}
		extract($_POST);
		$data = "";
		$gl_data = "";
		$doc_no = $_POST['doc_no'];
		foreach($_POST as $k =>$v){
			if($k !== 'vs_num' && !in_array($k,array('id','gtype','newDocNo','ctr','name','preparer'))  && !is_array($_POST[$k])){
				if(!is_numeric($v) && !is_null($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				if(!is_null($v))
				$data .= " `{$k}`='{$v}' ";
				else
				$data .= " `{$k}`= NULL ";
			}
		}
		$data = preg_replace('/\b(agent_id|emp_id|client_id)\b/', 'supplier_id', $data);
		$sql = "UPDATE `vs_entries` set {$data} where v_num = '{$v_num}' ";

		$save = $this->conn->query($sql);

		if($save){
			$data = "";
			$this->conn->query("DELETE FROM `tbl_gl_trans` where doc_no = " . $newDocNo);
			$this->conn->query("DELETE FROM `tbl_gr_list` where doc_no = " . $newDocNo);
			
			foreach ($account_id as $k => $v) {

				$doc = $doc_no[$k];

				$gtype ='';
				if ($amount[$k] < 0) {
					$gtype = 2; 
				} else {
					$gtype = 1; 
				}
				
				$account_code_value = $account_id[$k];
				$vs_num_value = $v_num;
			
				if (!empty($gl_data)) $gl_data .= ", ";
				$gl_data .= "('{$doc}','{$gtype}','{$vs_num_value}','AP','{$amount[$k]}', '{$account_code_value}', NOW(), '0', '{$preparer}')";
			}
			
			if (!empty($gl_data)) {
				$gl_sql2 = "UPDATE `tbl_vs_attachments`
				SET `doc_no` = '$newDocNo'
				WHERE `num` = '$v_num' AND `doc_type` = 'AP'
				ORDER BY `date_attached` DESC
				LIMIT 1";
				$gl_sql3 = "DELETE FROM `tbl_vs_attachments` WHERE `doc_no` = 0 and `num` = '$v_num'";
				$gl_sql = "INSERT INTO `tbl_gl_trans` (`doc_no`,`gtype`,`vs_num`,`doc_type`,`amount`,`account`,`journal_date`,`c_status`,`preparer`) VALUES {$gl_data}";
				$save_gl2 = $this->conn->query($gl_sql2);
				$save_gl3 = $this->conn->query($gl_sql3);
				$save_gl = $this->conn->query($gl_sql);
			
				if ($save_gl && $save_gl2 && $save_gl3) {
					$resp['status'] = 'success';
					if (empty($id)) {
						$resp['msg'] = " Voucher Setup Entry has successfully added. GL Transactions have been successfully added.";
					} else {
						$resp['msg'] = " Voucher Setup has been updated successfully. GL Transactions have been successfully updated.";
					}
				}
			}
			$this->conn->query("DELETE FROM `vs_items` where doc_no = " . $newDocNo);
			
			foreach($account_id as $k=>$v){
				if(!empty($data)) $data .=", ";
				$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
				$data .= "('{$v_num}','{$doc_no_value}','{$v}','{$amount[$k]}')";
			}
			if(!empty($data)){
				$sql = "INSERT INTO `vs_items` (`journal_id`,`doc_no`,`account_id`,`amount`) VALUES {$data}";
				$save2 = $this->conn->query($sql);
				if($save2){
					$resp['status'] = 'success';
					if(empty($id)){
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					}else
						$resp['msg'] = " Voucher Setup has been updated successfully.";
				}else{
					$resp['status'] = 'failed';
					if(empty($id)){
						$resp['msg'] = $this->conn->error."[{$sql}]";
						$this->conn->query("DELETE FROM `vs_entries` where v_num = '{$v_num}'");
					}else
						$resp['msg'] = " Voucher Setup Entry has failed to update.";
					$resp['error'] = $this->conn->error;
				}
			}else{
				$resp['status'] = 'failed';
				if(empty($id)){
					$resp['msg'] = $this->conn->error."[{$sql}]";
					$this->conn->query("DELETE FROM `vs_entries` where v_num = '{$v_num}'");
				}else
					$resp['msg'] = " Voucher Setup Entry has failed to update.";
				$resp['error'] = "Voucher Setup Entry Items is empty";
			}
			$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
			$sup_code = isset($_POST['emp_id']) ? $_POST['emp_id'] : (isset($_POST['agent_id']) ? $_POST['agent_id'] : (isset($_POST['client_id']) ? $_POST['client_id'] : $_POST['supplier_id']));
			$gr_list_sql = "INSERT INTO `tbl_gr_list` (`doc_no`,`vs_num`,`supplier_id`) VALUES ('{$doc_no_value}','{$vs_num_value}','{$sup_code}')";
			$save_gr_list = $this->conn->query($gr_list_sql);
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error."[{$sql}]";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] =='success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}

	function delete_cv(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `cv_entries` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Check Voucher Entry has been deleted successfully.");

		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function cancel_journal(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `vs_entries` set `status` = '3' where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," journaling has successfully cancelled.");

		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}




	///////////////////////////////PURCHASING ORDER///////////////////////////
	function save_supplier_setup(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if ($k === 'ewt_type') {
				continue; 
			}
			if(!in_array($k,array('id'))){
				$v = addslashes(trim($v));
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		
		if(empty($id)){
			$sql = "INSERT INTO `supplier_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `supplier_list` SET `wt` = '{$wt}', `vatable` = {$vatable} WHERE id = '{$id}'";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New supplier successfully saved.");
			else
				$this->settings->set_flashdata('success',"Supplier successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function update_m_status() {
		extract($_POST);
	
		if(isset($vouchers)) {
			$vouchers_list = implode(",", array_map('intval', $vouchers));
			$sql = "UPDATE vs_entries SET m_status = 1 WHERE v_num IN ($vouchers_list)";
	
			if($this->conn->query($sql)) {
				$resp['status'] = 'success';
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = 'No vouchers selected.';
		}
		return json_encode($resp);
	}
	
	function save_transaction() {
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v) {
			if ($k == 'id') continue; 
			$v = addslashes(trim($v));
			if (!empty($data)) $data .= ",";
			$data .= " `{$k}`='{$v}' ";
		}
		
		if (empty($id)) {
			$sql = "INSERT INTO `tbl_transaction` set {$data} ";
			$save = $this->conn->query($sql);
		} else {
			$sql = "UPDATE `tbl_transaction` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		
		if ($save) {
			$resp['status'] = 'success';
			if (empty($id))
				$this->settings->set_flashdata('success', "New transaction successfully saved.");
			else
				$this->settings->set_flashdata('success', "Transaction successfully updated.");
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		
		return json_encode($resp);
	}
	
	function save_supplier(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if ($k === 'ewt_type') {
				continue; 
			}
			if(!in_array($k,array('id'))){
				$v = addslashes(trim($v));
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		
		if(empty($id)){
			$sql = "INSERT INTO `supplier_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `supplier_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New supplier successfully saved.");
			else
				$this->settings->set_flashdata('success',"Supplier successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}

	function save_rfp(){
		extract($_POST);
		$id = isset($_POST['id']) ? $_POST['id'] : '';
		$rfp_num = isset($_POST['rfp_no']) ? $_POST['rfp_no'] : '';
		$vs_no = isset($_POST['vs_no']) ? $_POST['vs_no'] : '';
		$num = isset($_POST['num']) ? $_POST['num'] : '';
		$division = isset($_POST['division']) ? $_POST['division'] : '';
		$usercode = isset($_POST['usercode']) ? $_POST['usercode'] : '';
		$checkdate = isset($_POST['check_date']) ? $_POST['check_date'] : '';
		$data = "";
		foreach($_POST as $k =>$v){
			
			if(!in_array($k,array('id','doc_no','num','inputValue','division','usercode'))){
				$v = addslashes(trim($v));
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		
		$gl_sql2 = "UPDATE `tbl_vs_attachments` SET `doc_no`  = '$rfp_num' WHERE `num` = '$num' and `doc_type` = 'RFP' ORDER BY `date_attached` DESC
		LIMIT 1";
		$gl_sql3 = "DELETE FROM `tbl_vs_attachments` WHERE `doc_no` = 0 and `num` = '$num' and `doc_type` = 'RFP'";
		
		$save_sql2 = $this->conn->query($gl_sql2);
		$save_sql3 = $this->conn->query($gl_sql3);
		
		if(empty($id)){
			$sql = "INSERT INTO `tbl_rfp` set {$data} ";
			// if(($division == "MNGR" || $division == "SPVR") && ($usercode == '20016' || $usercode == '10006' || $usercode == '10006' || $usercode == '20084' || $usercode == '10184'
			// || $usercode == '10009' || $usercode == '10030' || $usercode == '20124' || $usercode == '10051' || $usercode == '20181' || $usercode == '10102'
			// || $usercode == '20018' || $usercode == '20017' || $usercode == '20003' || $usercode == '10143' || $usercode == '10070'|| $usercode == '10100' || $usercode == '10131'
			// || $usercode == '10041' || $usercode == '20001' || $usercode == '10131' || $usercode == '10017' || $usercode == '10007' || $usercode == '10012' || $usercode == '10026'
			// || $usercode == '10015' || $usercode == '20186' || $usercode == '10038')){
			// 	$gl_sql4 = "INSERT INTO `tbl_rfp_approvals`(`status1`,`rfp_no`)VALUES('1','$rfp_num');";
			// }else if($usercode == '10114'){
			// 	$gl_sql4 = "INSERT INTO `tbl_rfp_approvals`(`status1`,`rfp_no`)VALUES('1','$rfp_num');";
			// }else{
				$gl_sql4 = "INSERT INTO `tbl_rfp_approvals`(`rfp_no`)VALUES('$rfp_num');";
			//}
			
			$save = $this->conn->query($sql);
			$save_sql4 = $this->conn->query($gl_sql4);
			}
			elseif (!empty($id) && $usercode == '10055') {
		
				$escapedCheckdate = $this->conn->real_escape_string($checkdate);
			
			
				$sql = "UPDATE `tbl_rfp` SET `check_date` = '$escapedCheckdate' WHERE id = '$id'";
			
			
				$save = $this->conn->query($sql);
			
				
				if ($save) {
					$resp['status'] = 'success';
					$this->settings->set_flashdata('success', "RFP check date successfully updated.");
				} else {
					$resp['status'] = 'failed';
					$resp['err'] = $this->conn->error . " [{$sql}]";
				}
			}
			
		else{
			//$sql = "UPDATE `tbl_rfp` SET {$data}, status1=NULL, status2=NULL, status3=NULL, status4=NULL, status5=NULL, status6=NULL, status7=NULL WHERE id='{$id}'";
			$sql = "UPDATE `tbl_rfp` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}

		if($save && $save_sql2 && $save_sql3){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New RFP successfully saved.");
			else
				$this->settings->set_flashdata('success',"RFP successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}

	function save_tba(){
		extract($_POST);
		$tba_num = isset($_POST['tba_no']) ? $_POST['tba_no'] : '';
		$num = isset($_POST['num']) ? $_POST['num'] : '';
		$division = isset($_POST['division']) ? $_POST['division'] : '';
		$usercode = isset($_POST['usercode']) ? $_POST['usercode'] : '';
		$data = "";
		foreach($_POST as $k =>$v){
			
			if(!in_array($k,array('id','doc_no','num','inputValue','division','usercode'))){
				$v = addslashes(trim($v));
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		
		$gl_sql2 = "UPDATE `tbl_vs_attachments` SET `doc_no`  = '$tba_num' WHERE `num` = '$num' and `doc_type` = 'TBA' ORDER BY `date_attached` DESC
		LIMIT 1";
		$gl_sql3 = "DELETE FROM `tbl_vs_attachments` WHERE `doc_no` = 0 and `num` = '$num' and `doc_type` = 'TBA'";
		
		$save_sql2 = $this->conn->query($gl_sql2);
		$save_sql3 = $this->conn->query($gl_sql3);
		
		if(empty($id)){
			$sql = "INSERT INTO `tbl_tba` set {$data} ";
				$gl_sql4 = "INSERT INTO `tbl_tba_approvals`(`tba_no`)VALUES('$tba_num');";
			
			$save = $this->conn->query($sql);
			$save_sql4 = $this->conn->query($gl_sql4);
			}
		else{
			//$sql = "UPDATE `tbl_rfp` SET {$data}, status1=NULL, status2=NULL, status3=NULL, status4=NULL, status5=NULL, status6=NULL, status7=NULL WHERE id='{$id}'";
			$sql = "UPDATE `tbl_tba` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}

		if($save && $save_sql2 && $save_sql3){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New TBA successfully saved.");
			else
				$this->settings->set_flashdata('success',"TBA successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}

	function delete_supplier(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `supplier_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Supplier successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	function save_check(){
		extract($_POST);
		$data = "";
		
		$amount = str_replace(',', '', $amount);
	
		foreach($_POST as $k =>$v){
			if(!in_array($k, array('check_id'))){
				$v = addslashes(trim($v));
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		
		if(empty($id)){
			$sql = "INSERT INTO `check_details` SET {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `check_details` SET {$data} WHERE c_num = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success', "New check details successfully saved.");
			else
				$this->settings->set_flashdata('success', "Check details successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . " [{$sql}]";
		}
		
		return json_encode($resp);
	}

	function save_users(){
		$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
		$user_pass = isset($_POST['password']) ? $_POST['password'] : '';
		extract($_POST);
		$data = "";
	
		foreach ($_POST as $k => $v) {
			if ($k !== 'password') {
			
				$v = addslashes(trim($v));
				if (!empty($data)) $data .= ",";
				$data .= "`{$k}`='{$v}' ";
			} elseif (!empty($user_pass)) {
				
				$v = md5($v);
				if (!empty($data)) $data .= ",";
				$data .= "`{$k}`='{$v}' ";
			}
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$uploadDir = '../uploads/';
			$filename = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name']; 
			$targetPath = $uploadDir . $filename;
		
			if(move_uploaded_file($_FILES['img']['tmp_name'], $targetPath)){
				$databasePath = str_replace('../', './', $targetPath);
				$data .=" , avatar = '{$databasePath}' ";
			} else {
				echo "File upload failed.";
			}
		}
		
		if(empty($user_id)){
			$sql = "INSERT INTO `users` SET {$data} ";
			$save = $this->conn->query($sql);
		} else {
			$sql = "UPDATE `users` SET {$data} WHERE user_id = '{$user_id}' ";
			$save = $this->conn->query($sql);
		}
		
		
	
		if($save){
			$resp['status'] = 'success';
			if(empty($user_id))
				$this->settings->set_flashdata('success', "New user details successfully saved.");
			else
				$this->settings->set_flashdata('success', "User details successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . " [{$sql}]";
		}
	
		return json_encode($resp);
	}
	
	
	function save_item(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description','last_date_purchased'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($_POST['description'])){
			if(!empty($data)) $data .=",";
				$data .= " `description`='".addslashes(htmlentities($description))."' ";
		}
		// $check = $this->conn->query("SELECT * FROM `item_list` where `item_code` = '{$item_code}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		// if($this->capture_err())
		// 	return $this->capture_err();
		// if($check > 0){
		// 	$resp['status'] = 'failed';
		// 	$resp['msg'] = "Item code already exist.";
		// 	return json_encode($resp);
		// 	exit;
		// }
		if(empty($id)){
			$sql = "INSERT INTO `item_list` set {$data} ";
		}else{
			$sql = "UPDATE `item_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New item/service successfully saved.");
			else
				$this->settings->set_flashdata('success',"Item/Service successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}

	function save_item_setup(){
		extract($_POST);

		error_log(print_r($_POST, true));
	
		if(empty($id)){
			$resp['status'] = 'failed';
			$resp['err'] = 'Invalid operation for inserting new records in this context.';
		} else {
			foreach ($account_title as $key => $value) {
				if (isset($item_id[$key])) { 
					$sql = "UPDATE `item_list` SET account_code = '{$value}' WHERE id = '{$item_id[$key]}'";
					$update = $this->conn->query($sql);
	
					if (!$update) {
						$resp['status'] = 'failed';
						$resp['err'] = $this->conn->error . " [{$sql}]";
						return json_encode($resp);
					}
				}
			}
	
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Account codes successfully updated for the items.");
		}
	
		return json_encode($resp);
	}
	

	function delete_item(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `item_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Item/Service successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function search_items() {
		$q = isset($_POST['q']) ? $this->conn->real_escape_string($_POST['q']) : '';
		$supplierId = isset($_POST['supplier_id']) ? intval($_POST['supplier_id']) : 0;
		if (empty($q)) {
			echo json_encode([]);
			return;
		}

		$data = [
			"supplier_id" => $supplierId, 
		];
	
		$qry = $this->conn->query("SELECT ilist.default_unit, ilist.description, ilist.name, ilist.id, o_list.unit_price, o_list.item_id, o_list.date_purchased AS recent_date_purchased
		FROM `item_list` ilist
		LEFT JOIN (
			SELECT item_id, MAX(date_purchased) AS max_date
			FROM `approved_order_items`
			GROUP BY item_id
		) recent_orders ON ilist.id = recent_orders.item_id
		LEFT JOIN `approved_order_items` o_list ON ilist.id = o_list.item_id AND recent_orders.max_date = o_list.date_purchased
		WHERE ilist.name LIKE '%{$q}%' AND ilist.supplier_id = '$supplierId'
		ORDER BY ilist.name;
		");
	
		$items = [];
	
		while($row = $qry->fetch_assoc()){
			$items[] = [
				"label" => $row['name'],
				"id" => $row['id'],
				"description" => $row['description'],
				"default_unit" => $row['default_unit'],
				"unit_price" => $row['unit_price'],
			];
		}

		$data["items"] = $items;
		$jsonResponse = json_encode($data);
		echo $jsonResponse;
		return;
	}
	
	

	function update_status_gr() {
		extract($_POST);
		$data = "";
		$data2 = "";
		$data3 = "";
		$data4 = "";
		
		$vs_num = 0;
			
		$vatableValue = $_POST['vatableValue'];
		$gtype_vat = $_POST['gtype_vat'];
		$gtype_ewt = $_POST['gtype_ewt'];
		$gtype_gr = $_POST['gtype_gr'];
		

		foreach ($_POST as $k => $v) {
			if (in_array($k, array('discount_amount', 'tax_amount'))) {
				$v = str_replace(',', '', $v);
			}
			if (!in_array($k, array('id', 'po_no', 'po_id', 'usertype', 'gr_no', 'prev_del_items', 'prev_outstanding', 'gr_id','account_code_vat','item_code_vat','amount_vat','amount_ewt','account_code_ewt','item_code_ewt','amount_gr','account_code_gr','item_code_gr','doc_no','vatableValue','vs_num','vat_amt','ex_vat','tot','totDec','gtype_gr','gtype_ewt','gtype_vat')) && !is_array($_POST[$k])) {
				$v = addslashes(trim($v));
				if (!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		
		$data .= ", po_no = '{$po_no}' ";

		if ($vatableValue < 3) {
			$amount_vat_value = str_replace(',', '', $amount_vat);

			if ($gtype_vat == 2) {
				$amount_vat_value = -$amount_vat_value;
			}

			$data2 .= " '{$doc_no}','{$vs_num}','GR','{$po_id}','{$gr_no}','{$account_code_vat}', '{$item_code_vat}', '{$amount_vat_value}', NOW(), '1','1'";
		}

		$amount_ewt_value = str_replace(',', '', $amount_ewt);
		$amount_gr_value = str_replace(',', '', $amount_gr);

		if ($gtype_ewt == 2) {
			$amount_ewt_value = -$amount_ewt_value;
		}
		if ($gtype_gr == 2) {
			$amount_gr_value = -$amount_gr_value;
		}
		$data3 .= " '{$doc_no}','{$vs_num}','GR','{$po_id}','{$gr_no}','{$account_code_ewt}', '{$item_code_ewt}', '" . str_replace(',', '', $amount_ewt_value) . "', NOW(), '1','1'";
		$data4 .= " '{$doc_no}','{$vs_num}','GR','{$po_id}','{$gr_no}','{$account_code_gr}', '{$item_code_gr}', '" . str_replace(',', '', $amount_gr_value) . "', NOW(), '1','1'";

		if (empty($id)) {
			$sql = "INSERT INTO `po_approved_list` SET {$data} ";
		} else {
			$sql = "UPDATE `po_approved_list` SET {$data} WHERE id = '{$id}' ";
		}

		$sql2 = "INSERT INTO `tbl_gl_trans` (`doc_no`,`vs_num`,`doc_type`,`po_id`,`gr_id`,`account`, `item_code`, `amount`, `journal_date`,`c_status`,`c_status2`) VALUES ({$data2})";
		$sql3 = "INSERT INTO `tbl_gl_trans` (`doc_no`,`vs_num`,`doc_type`,`po_id`,`gr_id`,`account`, `item_code`, `amount`, `journal_date`,`c_status`,`c_status2`) VALUES ({$data3})";
		$sql4 = "INSERT INTO `tbl_gl_trans` (`doc_no`,`vs_num`,`doc_type`,`po_id`,`gr_id`,`account`, `item_code`, `amount`, `journal_date`,`c_status`,`c_status2`) VALUES ({$data4})";

		$save1 = $this->conn->query($sql);
		$save2 = (int)$vatableValue < 3 ? $this->conn->query($sql2) : true;
		$save3 = $this->conn->query($sql3);
		$save4 = $this->conn->query($sql4);

		if ($save1 && $save2 && $save3 && $save4) {
			$resp['status'] = 'success';
			$po_id = empty($id) ? $this->conn->insert_id : $id;
			$resp['id'] = $po_id;

	
			$stmt = $this->conn->prepare("INSERT INTO `tbl_gr_list` (`po_id`,`doc_no`,`vs_num`,`supplier_id`) VALUES (?,?,?,?)");

			$stmt->bind_param("ssii", $po_id, $doc_no, $vs_num, $supplier_id);
			
			$save_gr_list = $stmt->execute();

	
			if ($save_gr_list) {
				$gr_id = $this->conn->insert_id;
	
				$query = "";
				$query1 = "";
				
	
				foreach ($item_id as $k => $v) {
					if (!empty($query)) $query .= ",";
					$query .= "('{$gr_id}', '{$po_id}', '{$v}', '{$unit[$k]}', '{$unit_price[$k]}', '{$qty[$k]}', '{$received[$k]}', '{$outstanding[$k]}', '{$del_items[$k]}')";
	
					if (!empty($query1)) $query1 .= ",";
					$query1 .= "('{$doc_no}','{$vs_num}','GR','{$po_id}','{$gr_id}','{$item_id[$k]}','{$account_code[$k]}','{$item_code[$k]}','{$amount[$k]}',NOW(),'1','1')";

					
					
				}
	
				$save_order_items = $this->conn->query("INSERT INTO `approved_order_items` (`gr_id`,`po_id`,`item_id`,`default_unit`,`unit_price`,`quantity`,`received`,`outstanding`, `del_items`) VALUES {$query}");
				$save_trans = $this->conn->query("INSERT INTO `tbl_gl_trans` (`doc_no`,`vs_num`,`doc_type`,`po_id`,`gr_id`,`item_id`,`account`,`item_code`,`amount`,`journal_date`,`c_status`,`c_status2`) VALUES {$query1}");
				
	
				if ($save_order_items && $save_trans) {
					if (empty($id)) {
						$this->settings->set_flashdata('success', "Purchase Order successfully saved.");
					} else {
						$this->settings->set_flashdata('success', "Purchase Order successfully updated.");
					}
				} else {
					$resp['status'] = 'failed';
					$resp['err'] = $this->conn->error . "[{$sql}]";
				}
			} else {
				$resp['status'] = 'failed';
				$resp['err'] = $this->conn->error . "[{$sql}]";
			}
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
	
		return json_encode($resp);
	}
	
	function update_status_po() {
		extract($_POST);
		//$discount_amount = str_replace(',', '', $discount_amount,$tax_amount);
		if($level == 4 and $selected_index == 1){
			#$update = $this->conn->query("UPDATE `po_list` set `status` = '1', `status2` = '0', `status3` = '0' where id = '{$po_id}'");
			$update = $this->conn->query("UPDATE `po_list` set `vatable`='{$vatable}',`tax_amount`='{$tax_amount}',`status` = '0', `status2` = '0', `status3` = '0', `notes` = '{$notes}' where id = '{$po_id}'");
			$data = "";
			foreach($item_id as $k =>$v){
				if(!empty($data)) $data .=",";
				$item_notes[$k] = $this->conn->real_escape_string($item_notes[$k]);
				$data .= "('{$po_id}','{$v}','{$default_unit[$k]}','{$unit_price[$k]}','{$qty[$k]}','{$item_status[$k]}','{$item_notes[$k]}')";
			}
			if(!empty($data)){
				$this->conn->query("DELETE FROM `order_items` where po_id = '{$po_id}'");
				$update = $this->conn->query("INSERT INTO `order_items` (`po_id`,`item_id`,`default_unit`,`unit_price`,`quantity`,`item_status`,`item_notes`) VALUES {$data} ");
			}
		}
		else if($level == 3 && $usertype == "PURCHASING OFFICER"){
		//else if($level == 3){
			$update = $this->conn->query("UPDATE `po_list` set `status` = '{$status}',`fpo_status` = '{$status}', `notes` = '{$notes}' where id = '{$po_id}'");
			//$po_id = empty($id) ? $this->conn->insert_id : $id ;
			//$resp['id'] = $po_id;
			$data = "";
			foreach($item_id as $k =>$v){
				if(!empty($data)) $data .=",";
				$item_notes[$k] = $this->conn->real_escape_string($item_notes[$k]);
				$data .= "('{$po_id}','{$v}','{$default_unit[$k]}','{$unit_price[$k]}','{$qty[$k]}','{$item_notes[$k]}')";
			}
			if(!empty($data)){
				$this->conn->query("DELETE FROM `order_items` where po_id = '{$po_id}' and item_status != 2");
				$update = $this->conn->query("INSERT INTO `order_items` (`po_id`,`item_id`,`default_unit`,`unit_price`,`quantity`,`item_notes`) VALUES {$data} ");
			}
		}
		else if($level == 3 && $usertype != "PURCHASING OFFICER"){
			$update = $this->conn->query("UPDATE `po_list` set `status2` = '{$status2}',`fpo_status` = '{$status2}', `notes` = '{$notes}' where id = '{$po_id}'");
			//$po_id = empty($id) ? $this->conn->insert_id : $id ;
			//$resp['id'] = $po_id;
			$data = "";
			foreach($item_id as $k =>$v){
				if(!empty($data)) $data .=",";
				$item_notes[$k] = $this->conn->real_escape_string($item_notes[$k]);
				$data .= "('{$po_id}','{$v}','{$default_unit[$k]}','{$unit_price[$k]}','{$qty[$k]}','{$item_notes[$k]}')";
			}
			if(!empty($data)){
				$this->conn->query("DELETE FROM `order_items` where po_id = '{$po_id}' and item_status != 2");
				$update = $this->conn->query("INSERT INTO `order_items` (`po_id`,`item_id`,`default_unit`,`unit_price`,`quantity`,`item_notes`) VALUES {$data} ");
			}
		}
		else if ($level == 2 && $selected_index != 1) {
			$update = $this->conn->query("UPDATE `po_list` set `status3` = '{$status3}',`fpo_status` = '{$status3}',`notes` = '{$notes}' where id = '{$po_id}'");
			$data = "";
			foreach($item_id as $k =>$v){
				if(!empty($data)) $data .=",";
				$item_notes[$k] = $this->conn->real_escape_string($item_notes[$k]);
				$data .= "('{$po_id}','{$v}','{$default_unit[$k]}','{$unit_price[$k]}','{$qty[$k]}','{$item_status[$k]}','{$item_notes[$k]}')";
			}
			if(!empty($data)){
				$this->conn->query("DELETE FROM `order_items` where po_id = '{$po_id}' and item_status != 2");
				$update = $this->conn->query("INSERT INTO `order_items` (`po_id`,`item_id`,`default_unit`,`unit_price`,`quantity`,`item_status`,`item_notes`) VALUES {$data} ");
			}
		}
		
		else if ($level == 2 && $selected_index == 1) {
			$update = $this->conn->query("UPDATE `po_list` SET `status` = '1',`status2` = '1',`status3` = '{$status3}',`fpo_status` = '{$status3}', `notes` = '{$notes}' WHERE id = '{$po_id}'");
			$data = "";
				foreach($_POST as $k =>$v){
					if(in_array($k,array('discount_amount')))
						$v= str_replace(',','',$v);
					if(!in_array($k,array('po_id','level','selected_index')) && !is_array($_POST[$k])){
						$v = addslashes(trim($v));
						if(!empty($data)) 
						$data .=",";
						$data .= " `{$k}`='{$v}' ";
					}
				}
				//$data .= ", id = '{$po_id}' ";
				if (!empty($id)) {
					$sql = "INSERT INTO `po_approved_list` SET {$data} ";
				}
				$update = $this->conn->query($sql);

				$data = "";
				foreach($item_id as $k =>$v){
					if(!empty($data)) $data .=",";
					$data .= "('{$po_id}','{$v}','{$default_unit[$k]}','{$unit_price[$k]}','{$qty[$k]}',0,'{$qty[$k]}')";
				}
				if(!empty($data)){
					$this->conn->query("DELETE FROM `approved_order_items` where po_id = '{$po_id}'");
					$update = $this->conn->query("INSERT INTO `approved_order_items` (`po_id`,`item_id`,`default_unit`,`unit_price`,`quantity`,`received`,`outstanding`) VALUES {$data} ");
				}
		}
		if ($update) {
			$resp['status'] = 'success';
			$resp['id'] = $po_id;

			$this->settings->set_flashdata('success', "Purchase Order status successfully updated.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function save_po(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(in_array($k,array('discount_amount','tax_amount')))
				$v= str_replace(',','',$v);
			if(!in_array($k,array('id','po_no','usertype','item_status','item_notes','level','selected_index')) && !is_array($_POST[$k])){
				$v = addslashes(trim($v));
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(!empty($po_no)){
			$check = $this->conn->query("SELECT * FROM `po_list` where `po_no` = '{$po_no}' ".($id > 0 ? " and id != '{$id}' ":""))->num_rows;
			if($this->capture_err())
				return $this->capture_err();
			if($check > 0){
				$resp['status'] = 'po_failed';
				$resp['msg'] = "Purchase Order Number already exist.";
				return json_encode($resp);
				exit;
			}
		}else{
			$po_no ="";
			while(true){
				$po_no = "PO-".(sprintf("%'.011d", mt_rand(1,99999999999)));
				$check = $this->conn->query("SELECT * FROM `po_list` where `po_no` = '{$po_no}'")->num_rows;
				if($check <= 0)
				break;
			}
		}
		$data .= ", po_no = '{$po_no}' ";

		if(empty($id)){
			$sql = "INSERT INTO `po_list` set {$data} ";
		}else{

			if($level == 4){
				$data .= ", status = '1' ";
				$data .= ", status2 = '0' ";
				$data .= ", status3 = '0' ";
			}

			if($level == 3 and $status2 == 3){
				$data .= ", status = '3' ";
				$data .= ", status2 = '0' ";
				$data .= ", status3 = '0' ";
			}
			$sql = "UPDATE `po_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			$po_id = empty($id) ? $this->conn->insert_id : $id ;
			$resp['id'] = $po_id;
			$data = "";
			foreach($item_id as $k =>$v){
				if(!empty($data)) $data .=",";
				$item_notes[$k] = $this->conn->real_escape_string($item_notes[$k]);
				$data .= "('{$po_id}','{$v}','{$default_unit[$k]}','{$unit_price[$k]}','{$qty[$k]}','{$item_status[$k]}','{$item_notes[$k]}')";
			}
			
			if(!empty($data)){
				$this->conn->query("DELETE FROM `order_items` where po_id = '{$po_id}'");
				$save = $this->conn->query("INSERT INTO `order_items` (`po_id`,`item_id`,`default_unit`,`unit_price`,`quantity`,`item_status`,`item_notes`) VALUES {$data} ");
			}
			if(empty($id))
				$this->settings->set_flashdata('success',"Purchase Order successfully saved.");
			else
				$this->settings->set_flashdata('success',"Purchase Order successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}

	function manage_po(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(in_array($k,array('discount_amount')))
				$v= str_replace(',','',$v);
			if(!in_array($k,array('id','po_no','usertype','item_status','item_notes','vatType')) && !is_array($_POST[$k])){
				$v = addslashes(trim($v));
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(!empty($po_no)){
			$check = $this->conn->query("SELECT * FROM `po_list` where `po_no` = '{$po_no}' ".($id > 0 ? " and id != '{$id}' ":""))->num_rows;
			if($this->capture_err())
				return $this->capture_err();
			if($check > 0){
				$resp['status'] = 'po_failed';
				$resp['msg'] = "Purchase Order Number already exist.";
				return json_encode($resp);
				exit;
			}
		}else{
			$po_no ="";
			while(true){
				$po_no = "PO-".(sprintf("%'.011d", mt_rand(1,99999999999)));
				$check = $this->conn->query("SELECT * FROM `po_list` where `po_no` = '{$po_no}'")->num_rows;
				if($check <= 0)
				break;
			}
		}
		$data .= ", po_no = '{$po_no}' ";

		if(empty($id)){
			$sql = "INSERT INTO `po_list` set {$data} ";
		}else{
			$sql = "UPDATE `po_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			$po_id = empty($id) ? $this->conn->insert_id : $id ;
			$resp['id'] = $po_id;
			$data = "";
			$item_id = isset($_POST['item_id']) ? $_POST['item_id'] : array();

			foreach($item_id as $k =>$v){
				if(!empty($data)) $data .=",";
				//$item_notes[$k] = $this->conn->real_escape_string($item_notes[$k]);
				$data .= "('{$po_id}','{$v}','{$default_unit[$k]}','{$unit_price[$k]}','{$qty[$k]}')";
			}
			

			if(!empty($data)){
				$this->conn->query("DELETE FROM `order_items` where po_id = '{$po_id}'");
				$save = $this->conn->query("INSERT INTO `order_items` (`po_id`,`item_id`,`default_unit`,`unit_price`,`quantity`) VALUES {$data} ");
			}
			if(empty($id))
				$this->settings->set_flashdata('success',"Purchase Order successfully saved.");
			else
				$this->settings->set_flashdata('success',"Purchase Order successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	
	function delete_po(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `po_list` where id = '{$id}'");
		$del = $this->conn->query("DELETE FROM `order_items` where po_id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Purchase Order successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}

	function manage_apv(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(in_array($k,array('amount_due','vat_amount','tax_amount')))
				$v= str_replace(',','',$v);
			if(!in_array($k,array('id')) && !is_array($_POST[$k])){
				$v = addslashes(trim($v));
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		// if(!empty($po_no)){
		// 	$check = $this->conn->query("SELECT * FROM `apv` where `apv_no` = '{$apv_no}' ".($id > 0 ? " and id != '{$id}' ":""))->num_rows;
		// 	if($this->capture_err())
		// 		return $this->capture_err();
		// 	if($check > 0){
		// 		$resp['status'] = 'po_failed';
		// 		$resp['msg'] = "AP voucher already exist.";
		// 		return json_encode($resp);
		// 		exit;
		// 	}
		// }else{
		// 	$po_no ="";
		// 	while(true){
		// 		$po_no = "PO-".(sprintf("%'.011d", mt_rand(1,99999999999)));
		// 		$check = $this->conn->query("SELECT * FROM `po_list` where `po_no` = '{$po_no}'")->num_rows;
		// 		if($check <= 0)
		// 		break;
		// 	}
		// }

		if(empty($id)){
			$sql = "INSERT INTO `apv` set {$data} ";
		}else{
			$sql = "UPDATE `apv` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			$apv_no = empty($id) ? $this->conn->insert_id : $id ;
			$resp['apv_id'] = $apv_no;
			$data = "";
			foreach($account_id as $k =>$v){
				if(!empty($data)) $data .=",";
				//$item_notes[$k] = $this->conn->real_escape_string($item_notes[$k]);
				$data .= "('{$apv_no}','{$v}','{$tax[$k]}','{$vat[$k]}','{$gross_amt[$k]}')";
			}
			

			if(!empty($data)){
				$this->conn->query("DELETE FROM `apv_list` where apv_id = '{$apv_no}'");
				$save = $this->conn->query("INSERT INTO `apv_list` (`apv_id`,`account_id`,`tax_amount`,`vat_amount`,`amount_due`) VALUES {$data} ");
			}
			if(empty($id))
				$this->settings->set_flashdata('success',"APV successfully saved.");
			else
				$this->settings->set_flashdata('success',"APV successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);

	}

	function manage_cv() {
		
		if(empty($_POST['id'])){
			$c_num = isset($_POST['c_num']) ? $_POST['c_num'] : '';
			$v_num = isset($_POST['v_num']) ? $_POST['v_num'] : '';
			
			$_POST['user_id'] = $this->settings->userdata('user_code');
		}
		extract($_POST);
		
		$data = "";
		$gl_data = "";
		foreach($_POST as $k =>$v){
			if ($k === 'divChoice') {
				continue; 
			}

			if(!in_array($k,array('id','gtype','vat_amount','div_amount','apamount','ctr','vs_num','preparer','user_id'))  && !is_array($_POST[$k])){
				if(!is_numeric($v) && !is_null($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				if(!is_null($v))
				$data .= " `{$k}`='{$v}' ";
				else
				$data .= " `{$k}`= NULL ";
			}
		}
		
		$checkIdQuery = $this->conn->query("SELECT * FROM `cv_entries` WHERE c_num = '{$c_num}'");
		if ($checkIdQuery->num_rows > 0) {
			$sql = "UPDATE `cv_entries` SET {$data} WHERE c_num = '{$id}'";
		} else {
			$sql = "INSERT INTO `cv_entries` SET {$data}";
			$sql1 = "UPDATE `vs_entries` SET cv_status = '1' WHERE v_num = '{$v_num}'";
		}

		$save = $this->conn->query($sql);
		$save1 = $this->conn->query($sql1);
		if($save && $save1){
			if (!empty($id)) {
				$jid = $id;
			} else {
				$jid = $this->conn->insert_id;
			}
			
			$data = "";
			foreach ($account_id as $k => $v) {

					$doc = $doc_no[$k];
	
					$gtype ='';
					if ($amount[$k] < 0) {
						$gtype = 2; 
					} else {
						$gtype = 1; 
					}
			
				$account_code_value = isset($account_id[$k]) ? $account_id[$k] : '';
				$vs_num_value = $v_num;
			
				if (!empty($gl_data)) $gl_data .= ", ";
				$gl_data .= "('{$doc}','CV','{$gtype}','{$vs_num_value}','{$c_num}','{$amount[$k]}', '{$account_code_value}', NOW(), '0', '{$preparer}')";
			}
			
			
			if (!empty($gl_data)) {
				$gl_sql = "INSERT INTO `tbl_gl_trans` (`doc_no`, `doc_type`,`gtype`, `vs_num`,`cv_num`,`amount`,`account`,`journal_date`,`c_status`,`preparer`) VALUES {$gl_data}";
				$save_gl = $this->conn->query($gl_sql);
			
				if ($save_gl) {
					$resp['status'] = 'success';
					if (empty($id)) {
						$resp['msg'] = " Voucher Setup Entry has successfully added. GL Transactions have been successfully added.";
					} else {
						$resp['msg'] = " Voucher Setup has been updated successfully. GL Transactions have been successfully updated.";
					}
				}
			}
			$this->conn->query("DELETE FROM `cv_items` where journal_id = '{$c_num}'");
			foreach($account_id as $k=>$v){
				if(!empty($data)) $data .=", ";
				$data .= "('{$c_num}','{$v}','{$amount[$k]}')";
			}
			if(!empty($data)){
				$sql = "INSERT INTO `cv_items` (`journal_id`,`account_id`,`amount`) VALUES {$data}";
				$save2 = $this->conn->query($sql);
				if($save2){
					$resp['status'] = 'success';
					if(empty($id)){
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					}else
						$resp['msg'] = " Voucher Setup has been updated successfully.";
				}else{
					$resp['status'] = 'failed';
					if(empty($id)){
						$resp['msg'] = " Voucher Setup Entry has failed to save.";
						$this->conn->query("DELETE FROM `cv_entries` where c_num = '{$c_num}'");
					}else
						$resp['msg'] = " Voucher Setup Entry has failed to update.";
					$resp['error'] = $this->conn->error;
				}
			}else{
				$resp['status'] = 'failed';
				if(empty($id)){
					$resp['msg'] = " Voucher Setup Entry has failed to save.";
					$this->conn->query("DELETE FROM `cv_entries` where c_num = '{$c_num}'");
				}else
					$resp['msg'] = " Voucher Setup Entry has failed to update.";
				$resp['error'] = "Voucher Setup Entry Items is empty";
			}
			$doc_no_value = $this->conn->real_escape_string($doc_no[0]); 
			$sup_code = $_POST['supplier_id'];

			$gr_list_sql = "INSERT INTO `tbl_gr_list` (`doc_no`,`vs_num`,`cv_num`,`supplier_id`) VALUES ('{$doc_no_value}','{$v_num}','{$c_num}','{$sup_code}')";
			
			$save_gr_list = $this->conn->query($gr_list_sql);
		}else {
	
			error_log("CV Items Error: " . $this->conn->error . " [{$sql}]");
		
			echo "Database Error: " . $this->conn->error . " [{$sql}]";
			
		}
		

		if($resp['status'] =='success')
		    $this->settings->set_flashdata('success',$resp['msg']);

		return json_encode($resp);
	}

	function edit_cv() {
		if(!empty($id)){
			$c_num = isset($_POST['c_num']) ? $_POST['c_num'] : '';
			$prefix = date("Ym-");
			$code = sprintf("%'.05d",$c_num);
			while(true){
				$check = $this->conn->query("SELECT * FROM `cv_entries` where `code` = '{$prefix}{$code}' ")->num_rows;
				if($check > 0){
					$code = sprintf("%'.05d",ceil($code) + 1);
				} else {
					break;
				}
			}
			$_POST['code'] = $prefix.$code;
			$_POST['user_id'] = $this->settings->userdata('user_code');
			
		}
		extract($_POST);
		$data = "";
		
		foreach($_POST as $k =>$v){
			if ($k === 'divChoice') {
				continue; 
			}
			// if ($k === 'v_number' && $v !== null) {
			// 	continue;
			// }

			if(!in_array($k,array('id'))  && !is_array($_POST[$k])){
				if(!is_numeric($v) && !is_null($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				if(!is_null($v))
				$data .= " `{$k}`='{$v}' ";
				else
				$data .= " `{$k}`= NULL ";
			}
		}
		
		$sql = "UPDATE `cv_entries` SET {$data} WHERE c_num = '{$c_num}'";
		
		$save = $this->conn->query($sql);
		if($save){
			$data = "";
			//$v_num = $v_number; 
			#echo $v_num;
			$this->conn->query("DELETE FROM `cv_items` where journal_id = '{$c_num}'");
			foreach($account_id as $k=>$v){
				if(!empty($data)) $data .=", ";
				$data .= "('{$c_num}','{$v}','{$group_id[$k]}','{$phase[$k]}','{$block[$k]}','{$lot[$k]}','{$amount[$k]}')";
			}

			
			if(!empty($data)){
				$sql = "INSERT INTO `cv_items` (`journal_id`,`account_id`,`group_id`,`phase`, `block`, `lot`,`amount`) VALUES {$data}";
				$save2 = $this->conn->query($sql);
				if($save2){
					$resp['status'] = 'success';
					if(empty($id)){
						$resp['msg'] = " Voucher Setup Entry has successfully added.";
					}else
						$resp['msg'] = " Voucher Setup has been updated successfully.";
				}else{
					$resp['status'] = 'failed';
					if(empty($id)){
						$resp['msg'] = " Voucher Setup Entry has failed to save.";
						$this->conn->query("DELETE FROM `cv_entries` where c_num = '{$c_num}'");
					}else
						$resp['msg'] = " Voucher Setup Entry has failed to update.";
					$resp['error'] = $this->conn->error;
				}
			}else{
				$resp['status'] = 'failed';
				if(empty($id)){
					$resp['msg'] = " Voucher Setup Entry has failed to save.";
					$this->conn->query("DELETE FROM `cv_entries` where c_num = '{$c_num}'");
				}else
					$resp['msg'] = " Voucher Setup Entry has failed to update.";
				$resp['error'] = "Voucher Setup Entry Items is empty";
			}
		}else {
	
			error_log("CV Items Error: " . $this->conn->error . " [{$sql}]");
		
			echo "Database Error: " . $this->conn->error . " [{$sql}]";
			
		}
		

		if($resp['status'] =='success')
		    $this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
		
		
		
	}


	function manage_adv(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(in_array($k,array('amount_due','vat_amount','tax_amount')))
				$v= str_replace(',','',$v);
			if(!in_array($k,array('id','transaction_no')) && !is_array($_POST[$k])){
				$v = addslashes(trim($v));
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		// if(!empty($po_no)){
		// 	$check = $this->conn->query("SELECT * FROM `adv` where `transaction_no` = '{$transaction_no}' ".($id > 0 ? " and id != '{$id}' ":""))->num_rows;
		// 	if($this->capture_err())
		// 		return $this->capture_err();
		// 	if($check > 0){
		// 		$resp['status'] = 'po_failed';
		// 		$resp['msg'] = "Direct voucher already exist.";
		// 		return json_encode($resp);
		// 		exit;
		// 	}
		// }else{
		// 	$po_no ="";
		// 	while(true){
		// 		$po_no = "PO-".(sprintf("%'.011d", mt_rand(1,99999999999)));
		// 		$check = $this->conn->query("SELECT * FROM `po_list` where `po_no` = '{$po_no}'")->num_rows;
		// 		if($check <= 0)
		// 		break;
		// 	}
		// }

		if(empty($id)){
			$sql = "INSERT INTO `adv` set {$data} ";
		}else{
			$sql = "UPDATE `adv` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			$transaction_no = empty($id) ? $this->conn->insert_id : $id ;
			$resp['adv_id'] = $transaction_no;
			$data = "";
			foreach($account_id as $k =>$v){
				if(!empty($data)) $data .=",";
				//$item_notes[$k] = $this->conn->real_escape_string($item_notes[$k]);
				$data .= "('{$transaction_no}','{$v}','{$tax[$k]}','{$vat[$k]}','{$gross_amt[$k]}')";
			}
			

			if(!empty($data)){
				$this->conn->query("DELETE FROM `adv_list` where adv_id = '{$transaction_no}'");
				$save = $this->conn->query("INSERT INTO `adv_list` (`adv_id`,`account_id`,`tax_amount`,`vat_amount`,`amount_due`) VALUES {$data} ");
			}
			if(empty($id))
				$this->settings->set_flashdata('success',"ADV successfully saved.");
			else
				$this->settings->set_flashdata('success',"ADV successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);

	}
	function get_price(){
		extract($_POST);
		 $qry = $this->conn->query("SELECT * FROM price_list where unit_id = '{$unit_id}'");
		 $this->capture_err();
		 if($qry->num_rows > 0){
			 $res = $qry->fetch_array();
			 switch($rent_type){
				 case '1':
					$resp['price'] = $res['monthly'];
					break;
				case '2':
					$resp['price'] = $res['quarterly'];
					break;
				case '3':
					$resp['price'] = $res['annually'];
					break;
			 }
		 }else{
			 $resp['price'] = "0";
		 }
		 return json_encode($resp);
	}
	
	function delete_img(){
		extract($_POST);
		if(is_file($path)){
			if(unlink($path)){
				$resp['status'] = 'success';
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = 'failed to delete '.$path;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = 'Unkown '.$path.' path';
		}
		return json_encode($resp);
	}
}
	

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_csr':
		echo $Master->save_csr();
	break;
	case 'delete_csr':
		echo $Master->delete_csr();
	break;
	case 'delete_agent':
		echo $Master->delete_agent();
	break;
	case 'save_client':
		echo $Master->save_client();
	break;
	case 'sm_verification':
		echo $Master->sm_verification();
	break;
	case 'approved_cv':
		echo $Master->approved_cv();
	break;
	case 'approved_cv_cfo':
		echo $Master->approved_cv_cfo();
	break;
	case 'disapproved_cv_cfo':
		echo $Master->disapproved_cv_cfo();
	break;
	case 'disapproved_cv':
		echo $Master->disapproved_cv();
	break;
	case 'coo_approval':
		echo $Master->coo_approval();
	break;
	case 'extend_coo_approval':
		echo $Master->extend_coo_approval();
	break;
	case 'cancel_approval':
		echo $Master->cancel_approval();
	break;
	case 'coo_disapproval':
		echo $Master->coo_disapproval();
	break;
	case 'save_lot':
		echo $Master->save_lot();
	break;
	case 'delete_lot':
		echo $Master->delete_lot();
	break;
	case 'save_house_model':
		echo $Master->save_house_model();
	break;
	case 'delete_model':
		echo $Master->delete_model();
	break;
	case 'delete_user':
		echo $Master->delete_user();
	break;
	case 'save_project':
		echo $Master->save_project();
	break;
	case 'save_or_logs':
		echo $Master->save_or_logs();
	break;
	case 'save_agent':
		echo $Master->save_agent();
	break;
	case 'save_users':
		echo $Master->save_users();
	break;
	case 'delete_project':
		echo $Master->delete_project();
	break;
	case 'save_reservation':
		echo $Master->save_reservation();
	break;
	case 'delete_reservation':
		echo $Master->delete_reservation();
	break;
	case 'upload_file':
		echo $Master->upload_file();
	break;
	case 'approved_upload':
		echo $Master->approved_upload();
	break;
	case 'delete_upload':
		echo $Master->delete_upload();
	break;
	case 'ca_approval':
		echo $Master->ca_approval();
	break;
	case 'save_ca':
		echo $Master->save_ca();
	break;
	// case 'print_payment_func':
	// 	echo $Master->print_payment_func();
	// break;
	case 'print_payment':
		echo $Master->print_payment();
	break;
	case 'cfo_booked':
		echo $Master->cfo_booked();
	break;
	case 'save_payment':
		echo $Master->save_payment();
	break;
	case 'add_payment':
		echo $Master->add_payment();
	break;
	case 'delete_payment':
		echo $Master->delete_payment();
	break;
	case 'undo_delete_payment':
		echo $Master->undo_delete_payment();
	break;
	case 'delete_invoice':
		echo $Master->delete_invoice();
	break;
	case 'delete_all_invoice':
		echo $Master->delete_all_invoice();
	break;
	case 'credit_principal':
		echo $Master->credit_principal();
	break;
	case 'update_prop_client':
		echo $Master->update_prop_client();
	break;

	case 'save_member':
		echo $Master->save_member();
	break;
	case 'create_restructured':
		echo $Master->create_restructured();
	break;

	case 'set_retention':
		echo $Master->set_retention();
	break;

	case 'backout_acc':
		echo $Master->backout_acc();
	break;

	case 'save_av':
		echo $Master->save_av();
	break;
	// case 'move_av':
	// 	echo $Master->move_av();
	// break;
	case 'save_group':
		echo $Master->save_group();
	break;
	case 'delete_group':
		echo $Master->delete_group();
	break;
	case 'save_account':
		echo $Master->save_account();
	break;
	case 'delete_account':
		echo $Master->delete_account();
	break;
	case 'save_voucher_supplier':
		echo $Master->save_voucher_supplier();
	break;
	case 'save_voucher':
		echo $Master->save_voucher();
	break;
	case 'save_voucher_nonpo':
		echo $Master->save_voucher_nonpo();
	break;
	case 'save_jv':
		echo $Master->save_jv();
	break;
	case 'modify_jv':
		echo $Master->modify_jv();
	break;
	case 'modify_voucher':
		echo $Master->modify_voucher();
	break;
	case 'modify_voucher_nonpo':
		echo $Master->modify_voucher_nonpo();
	break;
	case 'modify_voucher_supplier':
		echo $Master->modify_voucher_supplier();
	break;
	case 'save_vs':
		echo $Master->save_vs();
	break;
	case 'delete_vs':
		echo $Master->delete_vs();
	break;
	case 'approved_jv':
		echo $Master->approved_jv();
	break;
	case 'approved_vs':
		echo $Master->approved_vs();
	break;
	case 'disapproved_vs':
		echo $Master->disapproved_vs();
	break;
	case 'disapproved_jv':
		echo $Master->disapproved_jv();
	break;
	case 'claim_cv':
		echo $Master->claim_cv();
	break;
	case 'unclaimed_cv':
		echo $Master->unclaimed_cv();
	break;
	case 'delete_cv':
		echo $Master->delete_cv();
	break;
	case 'cancel_journal':
		echo $Master->cancel_journal();
	break;
	case 'delete_data':
		echo $Master->delete_data();
	break;
	case 'add_data':
		echo $Master->add_data();
	break;
	case 'delete_inactive':
		echo $Master->delete_inactive();
	break;
	case 'res_approval':
		echo $Master->res_approval();
	break;
	case 'res_disapproval':
		echo $Master->res_disapproval();
	break;
	case 'save_restructured2':
		echo $Master->save_restructured2();
	break;
	case 'av_approval':
		echo $Master->av_approval();
	break;
	case 'cm_approval':
		echo $Master->cm_approval();
	break;
	case 'cm_disapproval':
		echo $Master->cm_disapproval();
	break;
	case 'save_cm':
		echo $Master->save_cm();
	break;
	case 'av_disapproval':
		echo $Master->av_disapproval();
	break;
	///////////////////////PURCHASING ORDER////
	case 'delete_sub_accs':
		echo $Master->delete_sub_accs();
	break;
	case 'save_sub_accs':
		echo $Master->save_sub_accs();
	break;
	case 'save_cprofile':
		echo $Master->save_cprofile();
	break;
	case 'save_supplier':
		echo $Master->save_supplier();
	break;
	case 'save_rfp':
		echo $Master->save_rfp();
	break;
	case 'save_tba':
		echo $Master->save_tba();
	break;
	case 'approved_tba':
		echo $Master->approved_tba();
	break;
	case 'disapproved_tba':
		echo $Master->disapproved_tba();
	break;
	case 'approved_rfp':
		echo $Master->approved_rfp();
	break;
	case 'disapproved_rfp':
		echo $Master->disapproved_rfp();
	break;
	case 'save_supplier_setup':
		echo $Master->save_supplier_setup();
	break;
	case 'save_check':
		echo $Master->save_check();
	break;
	case 'save_item_setup':
		echo $Master->save_item_setup();
	break;
	case 'delete_supplier':
		echo $Master->delete_supplier();
	break;
	case 'save_item':
		echo $Master->save_item();
	break;
	case 'delete_item':
		echo $Master->delete_item();
	break;
	case 'search_items':
		echo $Master->search_items();
	break;
	case 'save_po':
		echo $Master->save_po();
	break;
	case 'manage_po':
		echo $Master->manage_po();
	break;
	case 'manage_apv':
		echo $Master->manage_apv();
	break;
	case 'manage_adv':
		echo $Master->manage_adv();
	break;
	case 'manage_cv':
		echo $Master->manage_cv();
	break;
	case 'modify_cv':
		echo $Master->modify_cv();
	break;
	case 'edit_cv':
		echo $Master->edit_cv();
	break;
	case 'update_status_po':
		echo $Master->update_status_po();
	break;
	case 'update_status_gr':
		echo $Master->update_status_gr();
	break;
	case 'delete_po':
		echo $Master->delete_po();
	break;
	case 'get_price':
		echo $Master->get_price();
	break;
	case 'save_transaction':
		echo $Master->save_transaction();
	break;
	case 'update_m_status':
		echo $Master->update_m_status();
	break;
	default:
		break;
}