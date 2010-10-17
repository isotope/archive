
<form action="<?php echo $this->request; ?>" id="tl_filter" class="tl_form" method="post">
<div class="tl_formbody">
<input type="hidden" name="FORM_SUBMIT" value="tl_filters" />

<div class="tl_panel">

<div class="tl_submit_panel tl_subpanel">
<input type="image" name="filter" id="filter" src="system/themes/<?php echo $this->getTheme(); ?>/images/reload.gif" class="tl_img_submit" title="<?php echo $this->apply; ?>" value="<?php echo $this->apply; ?>" />
</div>

<div class="tl_search tl_subpanel">
<strong><?php echo $this->search; ?>:</strong>
  <select name="tl_field" class="tl_select<?php echo $this->searchClass; ?>"><?php echo $this->searchOptions; ?></select>
  <span>=</span>
  <input type="text" name="tl_value" class="tl_text<?php echo $this->searchClass; ?>" value="<?php echo $this->keywords; ?>" />
</div>

<div class="tl_filter tl_subpanel">
<strong><?php echo $this->datefilter; ?>:</strong>
  <select name="tstamp" id="tstamp" class="tl_select<?php echo $this->datefilterClass; ?>"><option value=""><?php echo $this->thDatefilter; ?></option><?php echo $this->datefilterOptions; ?></select>
</div>

<div class="tl_type tl_subpanel">
<strong><?php echo $this->type; ?>:</strong>
  <select name="tl_type" id="type" class="tl_select<?php echo $this->typeClass; ?>" onchange="this.form.submit()"><?php echo $this->typeOptions; ?></select>
</div>

<div class="clear"></div>

</div>
</div>
</form>

<div id="tl_buttons">
<a href="<?php echo $this->createHref; ?>" class="header_new" title="<?php echo $this->createTitle; ?>" accesskey="n" onclick="Backend.getScrollOffset();"><?php echo $this->createLabel; ?></a>
</div>

<div id="tl_iso_collectionmanager">
<table cellpadding="0" cellspacing="0" class="sortable" id="collectionmanager" summary="">
<thead>
  <tr>
    <th class="col_0 col_first"><?php echo $this->thTitle; ?></th>
    <th class="col_1"><?php echo $this->thAssignedTo; ?></th>
    <th class="col_2"><?php echo $this->thStatus; ?></th>
    <th class="col_3"><?php echo $this->thProgress; ?></th>
    <th class="col_4 asc"><?php echo $this->thDeadline; ?></th>
    <th class="col_5 col_last unsortable">&nbsp;</th>
  </tr>
</thead>
<tbody>
<?php if ($this->carts): ?>
<?php foreach ($this->carts as $cart): ?>
  <tr class="<?php echo $cart['trClass']; ?>">
    <td class="col_0 col_first<?php echo $cart['tdClass']; ?>"><?php echo $cart['id']; ?></td>
    <td class="col_1<?php echo $cart['tdClass']; ?>"><?php echo $cart['user']; ?></td>
    <td class="col_2<?php echo $cart['tdClass']; ?>"><?php echo $cart['status']; ?></td>
    <td class="col_3<?php echo $cart['tdClass']; ?>"><?php echo $cart['progress']; ?></td>
    <td class="col_4<?php echo $cart['tdClass']; ?>"><?php echo $cart['deadline']; ?></td>
    <td class="col_5 col_last<?php echo $cart['tdClass']; ?>"><a href="<?php echo $cart['editHref']; ?>" title="<?php echo $cart['editTitle']; ?>"><img src="<?php echo $cart['editIcon']; ?>" alt="<?php echo $this->editLabel; ?>" /></a><?php if ($cart['deleteHref']): ?> <a href="<?php echo $cart['deleteHref']; ?>" title="<?php echo $cart['deleteTitle']; ?>" onclick="if (!confirm('<?php echo $cart['deleteConfirm']; ?>')) return false; Backend.getScrollOffset();"><img src="<?php echo $cart['deleteIcon']; ?>" alt="<?php echo $this->deleteLabel; ?>" /></a><?php else: ?> <img src="<?php echo $cart['deleteIcon']; ?>" alt="" /><?php endif; ?></td>
  </tr>
<?php endforeach; ?>
<?php else: ?>
  <tr>
    <td colspan="6"><?php echo $this->noCarts; ?></td>
  </tr>
<?php endif; ?>
</tbody>
</table>
</div>

<script type="text/javascript">
<!--//--><![CDATA[//><!--
window.addEvent('domready', function() { new TableSort('collectionmanager'); });
//--><!]]>
</script>
