<table width='100%'><tr align='left'><td class='head' width=\"35%\">Select Language</t><td class='even'>
<select name="lang">
<?php
for($i=0; $i <count($this->getVars('languages')); $i++) { ?>
  <option value="<?php $this->e('languages',$i) ?>" <?php $this->e('selected',$i) ?>><?php $this->e('languages',$i) ?></option>
<?php } ?>
</select>
</td></tr></table>