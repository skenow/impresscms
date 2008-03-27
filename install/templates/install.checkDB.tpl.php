<table align="center">
  <tr><td align="left">
<?php foreach($this->getVars('checks') as $check) { ?>
    <?php echo $check ?><br />
<?php } ?>
  </td></tr>
</table>
<?php if (is_array($this->getVars('msgs'))) foreach($this->getVars('msgs') as $msg) { ?>
<p><?php echo $msg ?></p>
<?php } ?>
