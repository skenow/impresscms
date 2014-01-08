<?php

$value = $default != 'notdefined' ? $default : true;
$this->initVar($varname, icms_properties_Handler::DTYPE_INTEGER, $value, false, null, "", false, _(' Enable HTML tags'), '', false, true, $displayOnForm);
$this->setControl($varname, "yesno");