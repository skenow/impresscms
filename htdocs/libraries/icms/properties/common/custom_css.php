<?php

$value = $default != 'notdefined' ? $default : '';
$this->initVar($varname, XOBJ_DTYPE_TXTAREA, $value, false, null, '', false, _('Custom CSS'), _('You can specify custom CSS information here. This CSS shall be outputed when this object is displayed on the user side.'), false, true, $displayOnForm);
$this->setControl('custom_css', array(
                    'name' => 'textarea',
                    'form_editor'=>'textarea',
    )
);