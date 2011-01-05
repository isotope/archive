<div id="tl_buttons">
<a href="<?php echo $this->href; ?>" class="header_back" title="<?php echo $this->title; ?>"><?php echo $this->button; ?></a>
</div>
<h2 class="sub_headline"><?php echo $this->headline; ?></h2>
<div id="tl_export" style="padding-bottom:25px;">
<?php if ($this->message): ?>
<div class="tl_message">
<?php echo $this->message; ?>
</div>
<?php endif; ?>
<form action="<?php echo $this->action; ?>" class="tl_form" method="post" onsubmit="ExportRequest.startExport(this, 'start'); return false;" >
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="tl_export" />
<h3><?php echo $this->checkboxWidget->generateLabel(); ?></h3>
<?php echo $this->checkboxWidget->generateWithError(); ?> 
<br /><br />
<div class="tl_submit_container">
<input type="submit" name="clear" id="clear" class="tl_submit" value="<?php echo $this->sLabel; ?>" /> 
</div>
</div>
</form>
</div>