<?php

$value = $default != 'notdefined' ? $default : true;
$this->initVar($varname, XOBJ_DTYPE_INT,$value, false, null, "", false, _(' Enable images'), '', false, true, $displayOnForm);
$this->setControl($varname, "yesno");