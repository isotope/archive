
<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<?php if ($this->headline): ?>

<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif; ?>


<?php if (!$this->addBefore): ?>

<div class="ce_text"><?php echo $this->text; ?></div>
<div class="product_list">

<?php foreach( $this->products as $product ): ?>
<?php if($product['clear']): ?>
<div class="clear">&nbsp;</div>
<?php endif; ?>
<div class="<?php echo $product['class']; ?>">
<?php echo $product['html']; ?>
</div>
<?php endforeach; ?>
</div>

<?php endif; ?>
<?php if ($this->addImage): ?>

<div class="image_container<?php echo $this->floatClass; ?>"<?php if ($this->margin || $this->float): ?> style="<?php echo trim($this->margin . $this->float); ?>"<?php endif; ?>>
<?php if ($this->href): ?>
<a href="<?php echo $this->href; ?>"<?php echo $this->attributes; ?> title="<?php echo $this->alt; ?>">
<?php endif; ?>
<img src="<?php echo $this->src; ?>"<?php echo $this->imgSize; ?> alt="<?php echo $this->alt; ?>" />
<?php if ($this->href): ?>
</a>
<?php endif; ?>
<?php if ($this->caption): ?>
<div class="caption"><?php echo $this->caption; ?></div>
<?php endif; ?>
</div>
<?php endif; ?>
<?php if ($this->addBefore): ?>

<div class="ce_text"><?php echo $this->text; ?></div>
<div class="product_list">

<?php foreach( $this->products as $product ): ?>
<?php if($product['clear']): ?>
<div class="clear">&nbsp;</div>
<?php endif; ?>
<div class="<?php echo $product['class']; ?>">
<?php echo $product['html']; ?>
</div>
<?php endforeach; ?>
</div>

<?php endif; ?>

<a href="<?php echo $this->href; ?>" class="button add_to_cart"><?php echo $this->addToCart; ?></a>


</div>