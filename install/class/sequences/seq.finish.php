<?php
session_destroy();
$installer->setArgs( 'content', _INSTALL_FINISH );
$installer->render();
?>