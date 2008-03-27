<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <title>Zarilia Installation</title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo _INSTALL_CHARSET ?>" />
  <link rel="stylesheet" type="text/css" media="all" href="./templates/css/style.css" />
  <?php echo $this->javascript ?>
</head>
<body>
<form action="<?=$this->install_path;?>" method="post" id="zariliainstall" name="zariliainstall" style="margin:0" <?php echo $this->onclick ?>>
<table align="center" border="0" cellspacing="0" cellpadding="0" id="Header">
  <tr>
    <td class="HeaderBannerLeft"><img src="../logo.gif" alt="Zarilia Installer" title="Zarilia Installer" width="227" height="81" /></td>
    <td class="HeaderBannerRight"></td>
  </tr>
 <tr>
    <td colspan="2" class="HeaderBottom">&nbsp;</td>
  </tr>
  <tr><td rowspan="3" class="rightside"></td></tr>
</table>

<table align="center" cellspacing="0" id="MainBody">
 <tr>
  <td id="leftcolumn"></td>
  <td id="centercolumn"><!-- Display center blocks if any --><div id="content">
   <table>
   <?php if ( !empty( $this->title ) )
{
    echo "<tr><td class='install_logo'>" . $this->title . "</td></tr>";
}
if ( !empty( $this->subtitle ) )
{
    echo "<tr><td class='install_subtitle'>" . $this->subtitle . "</td></tr>";
}
echo "<tr><td class='install_subtitle'>" . $this->content . "</td></tr>";

?>
		</table>
	  </div><br />
	</td>
</tr>
</table>
<table align="center" cellspacing="0" cellpadding="0" id="MainFooter">
  <tr>
    <td class="NavFooter" width='10%'>&nbsp;</td>
    <td class="NavFooter" width='25%' align='left' nowrap='nowrap'><?php echo b_back( @$b_back );
?></td>
    <td class="NavFooter" width='20%' align='center'><?php echo b_reload( @$b_reload );
	echo b_restart(@$b_restart);
?></td>
    <td class="NavFooter" width='25%' align='right' nowrap='nowrap'><?php echo b_next( @$b_next );
?></td>
    <td class="NavFooter" width='10%'>&nbsp;</td>
  </tr>
</table>

<div class="Footer">Zarilia Installer V1.00 Copyright Zarilia.com</div>
</form>
</body>
</html>
