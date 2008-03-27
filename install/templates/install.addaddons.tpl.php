<table width="100%" border="0">
  <tr>
    <th>&nbsp;</th>
    <th>Addons</th>
    <th>Version</th>
  </tr>
<?php
for( $i = 0; $i < count( $this->getVars( 'dirname' ) ); $i++ ) {
?>
  <tr>
    <td align="right"><input type="checkbox" name="addons[]" value="<?php $this->e( 'dirname', $i )  ?>" <?php echo $this->e( 'disabled', $i )  ?> /> &nbsp;</td>
    <td><?php $this->e( 'name', $i )  ?></td>
    <td><?php $this->e( 'version', $i ) ?></td>
  </tr>
<?php } ?>
</table>