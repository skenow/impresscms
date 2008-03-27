<?php $gname = ZAR_URL . '/images/flags'; ?>
<table width='70%' border='0' cellpadding='2' cellspacing='1' align='left'>
<!-- UTF-8 -->
<tr><td class='head' align="right"><b><?php echo _INSTALL_L167?></b></td><td class='even'><input type="text" name="name" value="english"/></td></tr>
<!-- UTF-8 -->
<tr><td class='head' align="right"><b><?php echo _INSTALL_L168?></b></td><td class='even'><input type="text" name="code" value="en"/></td></tr>
<!-- UTF-8 -->
<tr align='left'><td class='head'align="right"><b><?php echo _INSTALL_L169?></b></t><td class='even'>
<img src="<?php echo $this->getArgs('firstfile') ?>" id="language-image" alt="flag" style="width: 15px; height: 10px; border-style: solid; border-width: 1px;">
<select name="image" onchange="document.getElementById('language-image').src='../images/flags/'+this.value;">
<?php
for($i=0; $i <count($this->getVars('flags')); $i++) { ?>
  <option value="<?php $this->e('flags',$i) ?>" <?php $this->e('selected',$i) ?>><?php $this->e('flagname',$i) ?></option>
<?php } ?>
</select>
</td></tr>
<!-- UTF-8 -->
<tr align='left'><td class='head' align="right"><b><?php echo _INSTALL_L170?></b></t><td class='even'>
<select name="path">
<?php
for($i=0; $i <count($this->getVars('languages')); $i++) { ?>
  <option value="<?php $this->e('languages',$i) ?>" <?php $this->e('selected',$i) ?>><?php $this->e('languages',$i) ?></option>
<?php } ?>
</select>
</td></tr>
<!-- UTF-8 -->
<tr><td class='head' align="right"><b><?php echo _INSTALL_L173?></b></td><td class='even'><input type="text" name="charset" maxlength="60" value="UTF-8"/></td></tr>
</table>