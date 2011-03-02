<?php if ($this->mode == 'main' && $this->type == 'medium'): ?>
<div class="image_container" id="<?php echo $this->name; ?>_player" style="display:block; width:<?php echo $this->width; ?>px; height:<?php echo $this->height; ?>px"><img src="<?php echo $this->{$this->type}; ?>" alt="<?php echo $this->alt; ?>" <?php echo $this->{$this->type.'_size'}; ?> /></div>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
window.addEvent('domready', function()
{
<?php echo $this->flowplayer; ?>

});
//--><!]]>
</script>
<?php elseif ($this->mode == 'gallery'): ?>
<div class="image_container<?php if ($this->class) echo ' '.$this->class; ?>"><a href="<?php echo $this->link ? $this->link : $this->medium; ?>" title="<?php echo $this->desc; ?>" onclick="Isotope.inlineGallery(this, '<?php echo $this->name; ?>'); $f('<?php echo $this->name; ?>_player').play(this.href); return false"><img src="<?php echo $this->{$this->type}; ?>" alt="<?php echo $this->alt; ?>"<?php echo $this->{$this->type.'_size'}; ?><?php if ($this->class) echo ' class="'.$this->class.'"'; ?> /></a></div>
<?php else: ?>
<div class="image_container"><a href="<?php echo $this->href_reader; ?>" title="<?php echo $this->desc; ?>"><img src="<?php echo $this->{$this->type}; ?>" alt="<?php echo $this->alt; ?>"<?php echo $this->{$this->type.'_size'}; ?> /></a></div>
<?php endif; ?>