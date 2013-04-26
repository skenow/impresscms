<?php

$value = $default != 'notdefined' ? $default : true;
$this->initVar($varname, XOBJ_DTYPE_INT,$value, false, null, "", false, _CO_ICMS_DOXCODE_FORM_CAPTION, '', false, true, $displayOnForm);
$this->setControl($varname, "yesno");