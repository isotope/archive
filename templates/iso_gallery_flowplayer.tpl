<?php if ($this->mode == 'main' && $this->type == 'medium'): ?>

<!-- this A tag is where your Flowplayer will be placed. it can be anywhere -->
<div class="image_container" id="<?php echo $this->name; ?>_player" style="display:block; width:<?php echo $this->width; ?>px; height:<?php echo $this->height; ?>px"><img src="<?php echo $this->{$this->type}; ?>" alt="<?php echo $this->alt; ?>"<?php echo $this->{$this->type.'_size'}; ?> /></div>

<script type="text/javascript">
<!--//--><![CDATA[//><!--
window.addEvent('domready', function() {
	flowplayer('<?php echo $this->name; ?>_player', 'system/modules/isotope_flowplayer/html/flowplayer<?php if ($this->commercial) echo '.commercial'; ?>.swf', {
	<?php if ($this->commercial): ?>
		key: '<?php echo $this->license; ?>',
	<?php endif; ?>
		play: {
			opacity:0
		},
		clip: {
			baseUrl: '<?php echo $this->baseUrl; ?>'
		},
		playlist: ['<?php echo $this->{$this->type}; ?>'],
		plugins: {
	        controls: {
	            url: 'system/modules/isotope_flowplayer/html/flowplayer.controls.swf',
	            backgroundColor: '#aedaff',
	            slowForward: false,
	            fastForward: false,
	            scrubber: false,
	            display: 'none'
	        }
	    },
	    onPlaylistReplace: function(playlist) {
    		$f('<?php echo $this->name; ?>_player').getPlugin('controls').css({display: (['jpg','jpeg','png','gif'].contains(playlist[0].extension) ? 'none' : 'block')});
	    }
	});
});
//--><!]]>
</script>
<?php elseif ($this->mode == 'gallery'): ?>
<div class="image_container<?php if ($this->class) echo ' '.$this->class; ?>"><a href="<?php echo $this->link ? $this->link : $this->medium; ?>" title="<?php echo $this->desc; ?>" onclick="Isotope.inlineGallery(this, '<?php echo $this->product_id; ?>'); $f('<?php echo $this->name; ?>_player').play(this.href); return false"><img src="<?php echo $this->{$this->type}; ?>" alt="<?php echo $this->alt; ?>"<?php echo $this->{$this->type.'_size'}; ?><?php if ($this->class) echo ' class="'.$this->class.'"'; ?> /></a></div>
<?php else: ?>
<div class="image_container"><a href="<?php echo $this->href_reader; ?>" title="<?php echo $this->desc; ?>"><img src="<?php echo $this->{$this->type}; ?>" alt="<?php echo $this->alt; ?>"<?php echo $this->{$this->type.'_size'}; ?> /></a></div>
<?php endif; ?>