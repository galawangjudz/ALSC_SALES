<table id="b_details">
    <tr>
        <td style="width: 15%;border-top:none;border-bottom:none;border-left:none;">
            <h4 class="text-red h6">Investment Value</h4>
        </td>
    </tr>
</table>
<hr>

    <div class="tab-pane fade show active" id="inv-val" role="tabpanel" aria-labelledby="inv-val-tab">
        <div class="card mt-3">
            <form class="row g-3" id="InvestmentForm">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <!-- Lot Section -->
                        <thead>
                            <tr>
                                <th class="text-green h6" colspan="4"><i class="fa fa-map"></i> Lot Section</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><label for="lot_area" class="form-label">Lot Area</label></td>
                                <td><input type="text" class="form-control" id="lot_area" name="lot_area" value="<?= number_format($row['c_lot_area'],2) ?>" readonly></td>
                                <td><label for="price_per_sqm" class="form-label">Price per sqm</label></td>
                                <td><input type="text" class="form-control" id="price_per_sqm" name="price_per_sqm" value="<?= number_format($row['c_price_sqm'],2) ?>" readonly> </td>
                            </tr>
                            <tr>
                                <td><label for="amount_discount" class="form-label">Amount Discount</label></td>
                                <td><input type="text" class="form-control" id="amount_discount" name="amount_discount" value="<?= number_format($row['c_lot_discount'],2) ?>" readonly></td>
                                <td><label for="discount_amount" class="form-label">Discount Amount</label></td>
                                <td><input type="text" class="form-control" id="discount_amount" name="discount_amount" value="<?= number_format(($row['c_lot_area'] * $row['c_price_sqm']) * ($row['c_lot_discount']/100),2) ?>" readonly></td>
                            </tr>
                            <tr>
                                <td><label for="lot_contract_price" class="form-label">Lot Contract Price</label></td>
                                <td><input type="text" class="form-control" id="lot_contract_price" name="lot_contract_price" value="<?= number_format(($row['c_lot_area'] * $row['c_price_sqm'] - (($row['c_lot_area'] * $row['c_price_sqm']) *($row['c_lot_discount']/100))),2) ?>" readonly></td>
                            </tr>
                        </tbody>
                        <!-- House Section -->
                        <thead>
                            <tr>
                                <th class="text-green h6" colspan="4"><i class="fa fa-home"></i> House Section</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><label for="house_model" class="form-label">House Model</label></td>
                                <td><input type="text" class="form-control" id="house_model" name="house_model" value="<?= $row['c_house_model'] ?>" readonly></td>
                                <td><label for="floor_area" class="form-label">Floor Area</label></td>
                                <td><input type="text" class="form-control" id="floor_area" name="floor_area" value="<?= number_format($row['c_floor_area'],2) ?>" readonly></td>
                            </tr>
                            <tr>
                                <td><label for="house_price_sqm" class="form-label">House Price per sqm</label></td>
                                <td><input type="text" class="form-control" id="house_price_sqm" name="house_price_sqm"  value="<?= number_format($row['c_house_price_sqm'],2) ?>" readonly></td>
                                <td><label for="house_discount" class="form-label">House Discount</label></td>
                                <td><input type="text" class="form-control" id="house_discount" name="house_discount" value="<?= number_format($row['c_h_discount'],2) ?>" readonly></td>
                            </tr>
                            <tr>
                                <td><label for="h_discount_amount" class="form-label">House Discount Amount</label></td>
                                <td><input type="text" class="form-control" id="h_discount_amount" name="h_discount_amount" value="<?= number_format((($row['c_floor_area'] * $row['c_house_price_sqm']) * ($row['c_h_discount']/100)),2) ?>" readonly></td>
                                <td><label for="unit_status" class="form-label">Unit Status</label></td>
                                <td><input type="text" class="form-control" id="unit_status" name="unit_status" value="<?= $row['c_unit_status'] ?>" readonly></td>
                            </tr>
                            <tr>
                                <td><label for="house_contract_price" class="form-label">House Contract Price</label></td>
                                <td><input type="text" class="form-control" id="house_contract_price" name="house_contract_price" value="<?= number_format(($row['c_floor_area'] * $row['c_house_price_sqm'] - (($row['c_floor_area'] * $row['c_house_price_sqm']) *($row['c_h_discount']/100))),2) ?>" readonly></td>
                            </tr>
                        </tbody>
                        <!-- Fence Section -->
                        <thead>
                            <tr>
                                <th class="text-green h6" colspan="4"><i class="fas fa-hashtag"></i>Fence Section</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><label for="fence_price" class="form-label">Fence Price</label></td>
                                <td><input type="text" class="form-control" id="fence_price" name="fence_price" readonly></td>
                                <td><label for="fence_discount" class="form-label">Fence Discount</label></td>
                                <td><input type="text" class="form-control" id="fence_discount" name="fence_discount" readonly></td>
                            </tr>
                        </tbody>
                        <!-- Additional Costs Section -->
                        <thead>
                            <tr>
                                <th class="text-green h6" colspan="4"><i class="fa fa-brush"></i>Additional Costs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><label for="add_cost" class="form-label"><i class="fa fa-pencil-square-o"></i>Additional Cost</label></td>
                                <td><input type="text" class="form-control" id="add_cost" name="add_cost" readonly></td>
                            </tr>
                        </tbody>
                        <!-- Total Section -->
                        <thead>
                            <tr>
                                <th class="text-green h6" colspan="4"><i class="fa fa-square"></i>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><label for="total_contract_price" class="form-label">Total Contract Price</label></td>
                                <td><input type="text" class="form-control" id="total_contract_price" name="total_contract_price" value="<?= number_format($row['c_tcp'],2) ?>" readonly></td>
                                <td><label for="tcp_discount" class="form-label">TCP Discount</label></td>
                                <td><input type="text" class="form-control" id="tcp_discount" name="tcp_discount" value="<?= number_format($row['c_tcp_discount'],2) ?>" readonly></td>
                            </tr>
                            <tr>
                                <td><label for="tcp_discount_amount" class="form-label">TCP Discount Amount</label></td>
                                <td><input type="text" class="form-control" id="tcp_discount_amount" name="tcp_discount_amount" value="<?= number_format($row['c_tcp_discount_amount'],2) ?>" readonly></td>
                                <td><label for="vat" class="form-label">VAT</label></td>
                                <td><input type="text" class="form-control" id="vat" name="vat" value="<?= number_format($row['c_vat_amount'],2) ?>" readonly></td>
                            </tr>
                            <tr>
                                <td><label for="net_tcp" class="form-label">NET TCP</label></td>
                                <td><input type="text" class="form-control" id="net_tcp" name="net_tcp" value="<?= number_format($row['c_net_tcp'],2) ?>" readonly></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
               
            </form>
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
