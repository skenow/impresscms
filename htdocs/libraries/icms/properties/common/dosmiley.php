<?php

$value = $default != 'notdefined' ? $default : true;
$this->initVar($varname, icms_properties_Handler::DTYPE_INTEGER,$value, false, null, "", false, _(' Enable smiley icons'), '', false, true, $displayOnForm);
$this->setControl($varname, "yesno");