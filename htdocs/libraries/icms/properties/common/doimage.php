<?php

$value = $default != 'notdefined' ? $default : true;
$this->initVar($varname, self::DTYPE_INTEGER,$value, false, null, "", false, _(' Enable images'), '', false, true, $displayOnForm);
$this->setControl($varname, "yesno");