<?php

$value = $default != 'notdefined' ? $default : '';
$this->initVar($varname, self::DTYPE_DEP_TXTBOX,$value, false, null, "", false, _('Short URL'), _('When using the SEO features of this module, you can specify a Short URL for this category. This field is optional.'), false, true, $displayOnForm);