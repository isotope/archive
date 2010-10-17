<div id="orders" style="margin:18px; font-size: 100%; font-family: Arial, Helvetica, sans-serif; width:800px; border:solid 1px #000000; padding-left:25px;padding-right:25px;">
<div style="height:25px;"></div>
<table cellspacing="0" cellpadding="15" summary="Shipping labels" style="width:650px;">
<tr>
<?php $i=1; foreach($this->orders as $order) : ?>
<td style="height:190px;">
<?php echo strtoupper($order['firstname']); ?> <?php echo strtoupper($order['lastname']); ?><br />
<?php if(strlen($order['company'])): echo strtoupper($order['company']); ?><br /><?php endif; ?>
<?php echo strtoupper($order['street_1']); ?><br />
<?php echo strtoupper($order['city']); ?>, <?php echo strtoupper($GLOBALS['TL_LANG']['DIV'][$order['country']][$order['subdivision']]); ?> <?php echo strtoupper($order['postal']); ?><br />
<?php echo strtoupper($this->countries[$order['country']]); ?>
</td>
<?php if($i>1):?></tr><tr><?php $i=0; endif; ?>
<?php $i++; endforeach; ?>
</tr>
</table>
</div>