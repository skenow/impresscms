<?php

$value = $default != 'notdefined' ? $default : '';
$this->initVar($varname, self::DTYPE_STRING, $value, false, null, '', false, _('Meta Description'), _('In order to help Search Engines, you can customize the meta description you would like to use for this article. If you leave this field empty when creating a category, it will automatically be populated with the Summary field of this article.'), false, true, $displayOnForm);
$this->setControl('meta_description', array(
        'name' => 'textarea',
        'form_editor'=>'textarea'
));