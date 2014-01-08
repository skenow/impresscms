<?php

$value = $default != 'notdefined' ? $default : 0;
$this->initVar($varname, self::DTYPE_INTEGER,$value, false, null, '', false, _('Weight'), '', true, true, $displayOnForm);