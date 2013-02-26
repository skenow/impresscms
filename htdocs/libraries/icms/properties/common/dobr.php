<?php

$value = ($default === 'notdefined') ? true : $default;
$this->initVar($varname, XOBJ_DTYPE_INT,$value, false, null, "", false, _(' Enable linebreak'), '', false, true, $displayOnForm);
$this->setControl($varname, "yesno");