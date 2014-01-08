<?php

$value = $default != 'notdefined' ? $default : 0;
$this->initVar($varname, icms_properties_Handler::DTYPE_INTEGER,$value, false, null, '', false, _('Hit counter'), '', false, true, $displayOnForm);