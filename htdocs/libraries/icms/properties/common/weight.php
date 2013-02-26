<?php

$value = $default != 'notdefined' ? $default : 0;
$this->initVar($varname, XOBJ_DTYPE_INT,$value, false, null, '', false, _('Weight'), '', true, true, $displayOnForm);