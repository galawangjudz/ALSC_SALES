
<?php
$user_code = $_settings->userdata('user_code'); 
echo $user_code;
$query = "SELECT * FROM `tbl_user_permissions` WHERE `user_code` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $user_code); 
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $permissions = $result->fetch_assoc();
    $new_client_btn = $permissions['new_client_btn'];
    $new_ra_btn = $permissions['new_ra_btn'];
    $ra_list_btn = $permissions['ra_list_btn'];
    $buyers_acc_btn = $permissions['buyers_acc_btn'];
    $payment_btn = $permissions['payment_btn'];
    $comm_voucher_btn = $permissions['comm_voucher_btn'];
    $comm_voucher_per_agent_btn = $permissions['comm_voucher_per_agent_btn'];
    $inventory_btn = $permissions['inventory_btn'];
    $agents_btn = $permissions['agents_btn'];
    $users_list_btn = $permissions['users_list_btn'];
    $settings_btn = $permissions['settings_btn'];
    $permissions_list_btn = $permissions['permissions_list_btn'];
} else {
  $new_client_btn = 0;
  $new_ra_btn = 0;
  $ra_list_btn = 0;
  $buyers_acc_btn = 0;
  $payment_btn = 0;
  $comm_voucher_btn = 0;
  $comm_voucher_per_agent_btn = 0;
  $inventory_btn = 0;
  $agents_btn = 0;
  $users_list_btn = 0;
  $settings_btn = 0;
  $permissions_list_btn = 0;
}
?>
<aside class="main-sidebar sidebar-light-blue elevation-4 sidebar-no-expand">
  <a href="<?php echo base_url ?>" class="brand-link bg-blue text-sm">
  <img src="<?php echo base_url ?>/images/logo.jpg" alt="Store Logo" class="brand-image img-circle elevation-3" style="opacity: .8;
      width: 30px;
      height: 30px;
      max-height: unset;
      background: white;">
  <span class="brand-text font-weight-light"><b><?php echo $_settings->info('short_name') ?></b></span>
  </a>
  <div class="sidebar os-host os-theme-light os-host-overflow os-host-overflow-y os-host-resize-disabled os-host-transition os-host-scrollbar-horizontal-hidden">
    <div class="os-resize-observer-host observed">
      <div class="os-resize-observer" style="left: 0px; right: auto;"></div>
    </div>
    <div class="os-size-auto-observer observed" style="height: calc(100% + 1px); float: left;">
      <div class="os-resize-observer"></div>
    </div>
    <div class="os-content-glue" style="margin: 0px -8px; width: 249px; height: 646px;"></div>
    <div class="os-padding">
      <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow-y: scroll;">
        <div class="os-content" style="padding: 0px 8px; height: 100%; width: 100%;">
          <div class="clearfix"></div>
          <nav class="mt-4">
              <ul class="nav nav-pills nav-sidebar flex-column text-sm nav-compact nav-flat nav-child-indent nav-collapse-hide-child" data-widget="treeview" role="menu" data-accordion="false">
              <li class="nav-item dropdown">
                <a href="./" class="nav-link nav-dashboard">
                  <i class="nav-icon fas fa-columns"></i>
                  <p>
                    Dashboard
                  </p>
                </a>
              </li> 
              <div class="accordion" id="salesAccordion" style="margin-bottom:5px;">
                <button class="btn btn-link" type="button" data-target="#collapseSales" aria-expanded="true" aria-controls="collapseSales" style="background-color:gainsboro;width:270px;height:30px;padding-top:0; display: inline-block;text-align:left;">
                    <b><i><li class="nav-header" style="margin-left:-10px">Sales</li></i></b>
                </button>
                <div id="collapseSales" aria-labelledby="salesHeading" data-parent="#salesAccordion">
                    <div style="margin-left:15px">
                        <ul class="nav flex-column">
                        <?php if ($new_client_btn == 1): ?>
                            <li class="nav-item dropdown">
                              <a href="<?php echo base_url ?>admin/?page=sales/client" class="nav-link nav-client">
                                <i class="nav-icon fas fa-plus"></i>
                                <p>
                                  Clients
                                </p>
                              </a>
                            </li> 
                            <?php endif; ?>
                          <?php if ($new_ra_btn == 1): ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link nav-ra">
                                    <i class="nav-icon fas fa-plus"></i>
                                    <p>Create New RA</p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?php echo base_url ?>admin/?page=sales/create" class="nav-link">
                                            <i class="nav-icon fas fa-cube"></i>
                                            <p>Lot Only / Packaged</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?php echo base_url ?>admin/?page=sales/create_v2" class="nav-link">
                                            <i class="nav-icon fas fa-tools"></i>
                                            <p>Add House/Fence/Add Cost</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <?php endif; ?>
                            <?php if ($ra_list_btn == 1): ?>

                            <li class="nav-item dropdown">
                              <a href="<?php echo base_url ?>admin/?page=ra" class="nav-link nav-ra-list">
                              <i class="nav-icon fas fa-book"></i>
                                <p>
                                  Reservation Application List
                                </p>
                              </a>
                            </li> 
                            <?php endif; ?>
                            <?php if ($buyers_acc_btn == 1): ?>
                            <li class="nav-item dropdown">
                              <a href="<?php echo base_url ?>admin/?page=buyers_account" class="nav-link nav-buyer">
                              <i class="nav-icon fas fa-cube"></i>
                                <p>
                                  Buyer's Account
                                </p>
                              </a>
                            </li> 
                            <?php endif; ?>
                            <?php if ($payment_btn == 1): ?>
                            <li class="nav-item dropdown">
                              <a href="<?php echo base_url ?>admin/?page=payment_window" class="nav-link nav-payment">
                              <i class="nav-icon fas fa-cube"></i>
                                <p>
                                  Payments
                                </p>
                              </a>
                            </li> 
                            <?php endif; ?>
                            <?php if ($comm_voucher_btn == 1): ?>
                            <li class="nav-item dropdown">
                              <a href="<?php echo base_url ?>admin/?page=buyers_account/commission_voucher/comm_voucher" class="nav-link nav-comm">
                              <i class="nav-icon fas fa-cube"></i>
                                <p>
                                  Commission Voucher
                                </p>
                              </a>
                            </li>
                            <?php endif; ?>
                            <?php if ($comm_voucher_per_agent_btn == 1): ?>
                            <li class="nav-item dropdown">
                              <a href="<?php echo base_url ?>admin/?page=buyers_account/commission_voucher/new_comm_voucher" class="nav-link nav-new-comm">
                              <i class="nav-icon fas fa-cube"></i>
                                <p>
                                  Commission Voucher per Agent
                                </p>
                              </a>
                            </li> 
                            <?php endif; ?>
                            <?php if ($inventory_btn == 1): ?>
                            <li class="nav-item dropdown">
                              <a href="<?php echo base_url ?>admin/?page=inventory/lot-list" class="nav-link nav-inventory">
                              <i class="nav-icon fas fa-cube"></i>
                                <p>
                                  Inventory
                                </p>
                              </a>
                            </li> 
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="accordion" id="maintenanceAccordion" style="margin-bottom:5px;">
                <button class="btn btn-link" type="button" data-target="#collapseMaintenance" aria-expanded="true" aria-controls="collapseMaintenance" style="background-color:gainsboro;width:270px;height:30px;padding-top:0; display: inline-block;text-align:left;">
                  <b><i><li class="nav-header" style="margin-left:-10px">Maintenance</li></b></i>
                </button>
                <div id="collapseMaintenance" aria-labelledby="maintenanceHeading" data-parent="#maintenanceAccordion">
                <div style="margin-left:15px">
                    <ul class="nav flex-column">
                    <?php if ($agents_btn == 1): ?>
                      <li class="nav-item">
                        <a href="<?php echo base_url ?>admin/?page=agents_list/list" class="nav-link nav-agent">
                          <i class="nav-icon fa fa-id-card"></i> 
                          <p>Agents List</p>
                        </a>
                      </li>
                      <?php endif; ?>
                      <?php if ($users_list_btn == 1): ?>
                      <li class="nav-item">
                        <a class="nav-link nav-user" href="<?php echo base_url ?>admin/?page=user/list">
                          <i class="nav-icon fas fa-user-circle"></i> 
                          <p>Users List </p>
                        </a>
                      </li>
                      <?php endif; ?>
                      <?php if ($settings_btn == 1): ?>
                      <li class="nav-item">
                        <a class="nav-link nav-settings" href="<?php echo base_url ?>admin/?page=system_info">
                          <i class="nav-icon fas fa-cogs"></i> 
                          <p>Settings</p>
                        </a>
                      </li>
                      <?php endif; ?>
                      <?php if ($permissions_list_btn == 1): ?>
                      <li class="nav-item">
                        <a class="nav-link nav-permissions" href="<?php echo base_url ?>admin/?page=permissions/manage-permissions">
                          <i class="nav-icon fas fa-cogs"></i> 
                          <p>Permissions</p>
                        </a>
                      </li>
                      <?php endif; ?>
                    </ul>
                  </div>
                </div>
            </div>
        </div>
      </aside>
