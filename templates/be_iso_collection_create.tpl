
<div id="tl_buttons">
<a href="contao/main.php?do=iso_collectionmanager" class="header_back" title="<?php echo $this->goBack; ?>" accesskey="b" onclick="Backend.getScrollOffset();"><?php echo $this->goBack; ?></a>
</div>

<h2 class="sub_headline"><?php echo $this->headline; ?></h2>

<form action="<?php echo $this->request; ?>" id="tl_iso_collectionmanager" class="tl_form" method="post">
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="tl_iso_collectionmanager" />
<input type="hidden" name="REQUEST_TOKEN" value="{{request_token}}" />
<input type="hidden" name="store" value="<?php echo $this->store; ?>" />

<div class="tl_tbox block">
<?php foreach ($this->fields as $field): ?>
  <h3><?php echo $field->generateLabel(); ?></h3>
  <?php echo $field->generateWithError(); ?>
<?php if ($field->help): ?>
  <p class="tl_help"><?php echo $field->help; ?></p>
<?php endif; ?>
<?php endforeach; ?>
</div>
<div class="tl_formbody_submit">

<div class="tl_submit_container">
<input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="<?php echo $this->submit; ?>" />
</div>

</div>
</form>
