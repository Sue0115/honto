<div class="row" style="width:950px;">
    <div class="col-xs-6">
      <h4 class="text-center" style="font-weight:bold;">ERP订单信息</h3>
      <?php foreach($ordersArr as $o):?>
      <table class="table table-striped table-bordered dataTable" style="background:#fff;">
        <tr class="text-center">
          <td>ERP订单号</td>
          <td>币种</td>
          <td>ERP订单总金额</td>
          <td>ERP订单总平台佣金</td>
        </tr>
        <tr class="text-center">
           <td><?php echo $o['order']['erp_orders_id']?></td>
           <td><?php echo $o['order']['currency_type']?></td>
           <td><?php echo $o['order']['orders_total']?></td>
           <td><?php echo $o['order']['platFeeTotal']?></td>
        </tr>
        <tr class="text-center">
          <td colspan="2">sku</td>
          <td>单价</td>
          <td>数量</td>
        </tr>
        <?php foreach($o['sku'] as $s):?>
        <tr class="text-center">
          <td colspan="2"><?php echo $s['orders_sku']?></td>
          <td><?php echo $s['item_price']?></td>
          <td><?php echo $s['item_count']?></td>
        </tr>
        <?php endforeach;?>
      </table>
       <br/>
      <?php endforeach;?>
    </div>
    <div class="col-xs-6">
       <h4 class="text-center" style="font-weight:bold;">平台订单信息</h3>
       <?php foreach($platesArr as $p):?>
       <table class="table table-striped table-bordered dataTable" style="background:#fff;">
        <tr>
          <td>平台订单号</td>
          <td>币种</td>
          <td>订单金额</td>
          <td>包含退款金额</td>
          
        </tr>
        <tr>
          <td><?php echo $p['erp_buyer_id']?></td>
          <td><?php echo $p['currency_type']?></td>
          <td><?php echo $p['orders_total']?></td>
          <td><?php echo $p['return_amount']?></td>
        </tr>
        <tr>
          <td>扣除平台佣金</td>
          <td>扣除联盟佣金</td>
          <td colspan="2">本次放款金额</td>
        </tr>
        <tr>
          <td><?php echo $p['plat_amount']?></td>
          <td><?php echo $p['union_amount']?></td>
          <td colspan="2"><?php echo $p['loan_amount']?></td>
        </tr>
      </table>
      <br/>
      <?php endforeach;?>
    </div>
</div>
