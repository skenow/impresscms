<h3><?php $this->getArgs('db_mysql_heading') ?></h3>
<table align="center"><tr><td align="left">
<?php foreach($this->getVars('db_mysql_info') as $db_mysql_info ) { ?>
    <?php echo $db_mysql_info ?><br />
<?php } ?>
</td></tr></table>

<h3><?php $this->getArgs('db_addon_heading') ?></h3>
<table align="center"><tr><td align="left">
<?php if (is_array($this->getVars('db_addon_info'))) { ?>
    <?php foreach($this->getVars('db_addon_info') as $db_addon_info) { ?>
        <?php echo $db_addon_info ?><br />
    <?php } ?>
<?php } ?>
</td></tr></table>