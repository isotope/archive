<div id="invoice" style="margin:18px; padding:0px; font-size: 100%; font-family: Arial, Helvetica, sans-serif; width:800px; border:solid 1px #000000; padding:25px;">
	<table id="header" cellpadding="5" cellspacing="0" border="0" width="100%">
        <tr>
        <?php if($this->logoImage): ?>
		<td id="logo"><?php echo $this->logoImage; ?></td><?php endif; ?>
		<td style="text-align:right;"><p style="font-size:1.2em; margin-top:0px; margin-bottom:10px; padding:0px;"><?php echo $this->invoiceTitle; ?></p></td>
		</tr>
	</table>

	<h2><?php echo $this->orderDetailsHeadline; ?></h2>
	<table cellspacing="0" cellpadding="8" summary="Order details">
	<tr>
	<td class="info_container <?php echo $this->info['billing_address']['class']; ?>">
		<h3><?php echo $this->info['billing_address']['headline']; ?></h3>
		<p class="info" style="margin:0px;"><?php echo $this->info['billing_address']['info']; ?></p>
	</td>
	<td class="info_container <?php echo $this->info['shipping_address']['class']; ?>">
		<h3><?php echo $this->info['shipping_address']['headline']; ?></h3>
		<p class="info" style="margin:0px;"><?php echo $this->info['shipping_address']['info']; ?></p>
	</td>
	</tr>
	<tr>
	<td class="info_container <?php echo $this->info['payment_method']['class']; ?>">
		<h3><?php echo $this->info['payment_method']['headline']; ?></h3>
		<p class="info" style="margin:0px;"><?php echo $this->info['payment_method']['info']; ?></p>
	</td>
	<td class="info_container <?php echo $this->info['shipping_method']['class']; ?>">
		<h3><?php echo $this->info['shipping_method']['headline']; ?></h3>
		<p class="info" style="margin:0px;"><?php echo $this->info['shipping_method']['info']; ?></p>
	</td>
	</tr>
	<div class="clear">&nbsp;</div>

	<table cellspacing="0" cellpadding="8" summary="Order items" style="border:1px solid #000; width:650px;">
		<thead>
			<tr class="header">
				<th class="col_1 name"><h2>Item</h2></th>
				<th class="col_1 name"><h2>Qty</h2></th>
			</tr>
		</thead>
		<tbody>
	<?php foreach( $this->items as $item ): ?>
			<tr>
				<td class="col_first col_0 name"><?php if (strlen($item['href'])): ?><a href="<?php echo $item['href']; ?>"><?php endif; echo $item['name']; if (strlen($item['href'])): ?></a><?php endif; ?>
					<?php if(is_array($item['product_options']) && count($item['product_options'])): ?>
					<div class="optionswrapper">
						<ul class="productOptions">
						<?php foreach($item['product_options'] as $option): ?>
							<li><strong><?php echo $option['name']; ?>:</strong> <?php echo implode(', ', $option['values']); ?></li>
						<?php endforeach; ?>
						</ul>
					</div>
					<?php endif; ?>
				</td>
				<td class="col_1 quantity"><?php echo $item['quantity']; ?></td>
			</tr>
	<?php endforeach; ?>
		</tbody>
	</table>


	<?php if (count($this->downloads)): ?>
	<h2><?php echo $this->downloadsLabel; ?></h2>
	<?php foreach( $this->downloads as $download ): ?>
	<div class="download"><?php if ($download['downloadable']): ?><a href="<?php echo $download['href']; ?>" /><?php endif; echo $download['title']; if ($download['downloadable']): ?></a><?php endif; echo $download['remaining']; ?></div>
	<?php endforeach; endif; ?>
</div>