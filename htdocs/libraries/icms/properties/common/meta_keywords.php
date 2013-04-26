<?php

$value = $default != 'notdefined' ? $default : '';
$this->initVar($varname, XOBJ_DTYPE_TXTAREA, $value, false, null, '', false, _('Meta Keywords'), _('In order to help Search Engines, you can customize the keywords you would like to use for this article. If you leave this field empty when creating an article, it will automatically be populated with words from the Summary field of this article.'), false, true, $displayOnForm);
$this->setControl('meta_keywords', array(
                        'name' => 'textarea',
                        'form_editor'=>'textarea'
));