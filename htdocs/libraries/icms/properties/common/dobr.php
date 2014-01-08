<?php

$value = ($default === 'notdefined') ? true : $default;
$this->initVar($varname, icms_properties_Handler::DTYPE_INTEGER,$value, false, null, "", false, _(' Enable linebreak'), '', false, true, $displayOnForm);
$this->setControl($varname, "yesno");