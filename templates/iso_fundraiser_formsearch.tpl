<form action="<?php echo $this->action; ?>" method="get">
<div class="formbody">
<?php if ($this->id): ?>
<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
<?php endif; ?>
<table cellpadding="0" cellspacing="0">
<tr>
<td class="name"><p><label for="name"><?php echo $this->schoolLabel; ?></label></p></td>
<td><p><input id="ctrl_name" name="name" type="text" size="20" value="<?php echo $_GET['name']; ?>" /></p></td>
<td colspan="2">
<div class="submit_container"><input type="submit" id="ctrl_submit" class="submit" value="<?php echo $this->searchFundraiser; ?>" /></div>
</td>
</tr>
</table>
</div>
</form>