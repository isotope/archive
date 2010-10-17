
<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<div class="info">
<h1><?php echo $this->fundraiserTitle; ?></h1>
<table cellpadding="10" cellspacing="0" summary="Gift Registry Info"><tr>
<td><strong><?php echo $this->name; ?></strong></td>
</tr><tr><td colspan="3"><p><?php echo $this->description; ?></p></td>
</tr></table>
</div>

<div id="product_list">

<?php foreach( $this->products as $product ): ?>
<div class="<?php echo $product['class']; ?>">
<?php echo $product['html']; ?>
<?php if(count($product['options'] )): ?>
<p>
Options:<br />
<?php foreach($product['options'] as $label=>$value): ?>
<?php echo $label; ?>: <?php echo $value; ?><br />
<?php endforeach; ?>
</p>
<?php endif; ?>
</div>
<?php endforeach; ?>
</div>

</div>