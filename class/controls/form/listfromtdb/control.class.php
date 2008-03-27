<?php

// Loading base class
require_once ZAR_ROOT_PATH.'/class/controls/base/control.class.php';
require_once ZAR_ROOT_PATH.'/class/cache/settings.class.php';

class ZariliaControl_FormField_Listfromtdb 
	extends ZariliaControl_FormField {
	
	var $module, $source;

	function ZariliaControl_FormField_Listfromtdb($name,$value='',$title='',$module='system', $source=''){
		$this->ZariliaControl_FormField($name, $value, $title);	
		$this->module = $module;
		$this->source = $source;
	}

	function render() {
		$settings = &ZariliaSettings::getInstance();
		$data = $settings->read('config.values', $this->module, ($this->source=='')?$this->name:$this->source);
		if (is_array($data)) {
			//echo $this->name.';';
			$this->_value = '<select name="'.$this->name.'">';
			foreach ($data as $value => $title) {
				$this->_value .= '<option value="'.$value.'"';
				if ($this->value == $value) $this->_value .= ' selected="selected"';
				$this->_value .= '>'.(defined($title)?constant($title):$title).'</option>';
			}		
			$this->_value .= '</select>';
		}
		return parent::render();
	}

}

//$settings->remove('config.values', 'events_system');
//$settings->write('config.values', 'system', 'pass_level', array(20=>'_MD_AM_PASSLEVEL1', 40=>'_MD_AM_PASSLEVEL2', 60=>'_MD_AM_PASSLEVEL3', 80=>'_MD_AM_PASSLEVEL4', 95=>'_MD_AM_PASSLEVEL5'));
//$settings->write('config.values', 'system', 'mailmethod', array('mail' => 'PHP mail()', 'sendmail'=>'sendmail', 'smtp'=>'SMTP', 'smtpauth'=>'SMTPAuth'));
//$settings->write('config.values', 'system', 'showimagetype', array('Numeric Verification (no background Image)', 'AlphaNumeric Verification (no background Image)', 'AlphaNumeric Verification (With Background Image)'));

//$settings->remove('config.values', 'events_system');

/*$settings = &ZariliaSettings::getInstance();
$arx = array ( "-12" => '_TZ_GMTM12',
                "-11" => '_TZ_GMTM11',
                "-10" => '_TZ_GMTM10',
                "-9" => '_TZ_GMTM9',
                "-8" => '_TZ_GMTM8',
                "-7" => '_TZ_GMTM7',
                "-6" => '_TZ_GMTM6',
                "-5" => '_TZ_GMTM5',
                "-4" => '_TZ_GMTM4',
                "-3.5" => '_TZ_GMTM35',
                "-3" => '_TZ_GMTM3',
                "-2" => '_TZ_GMTM2',
                "-1" => '_TZ_GMTM1',
                "0" => '_TZ_GMT0',
                "1" => '_TZ_GMTP1',
                "2" => '_TZ_GMTP2',
                "3" => '_TZ_GMTP3',
                "3.5" => '_TZ_GMTP35',
                "4" => '_TZ_GMTP4',
                "4.5" => '_TZ_GMTP45',
                "5" => '_TZ_GMTP5',
                "5.5" => '_TZ_GMTP55',
                "6" => '_TZ_GMTP6',
                "7" => '_TZ_GMTP7',
                "8" => '_TZ_GMTP8',
                "9" => '_TZ_GMTP9',
                "9.5" => '_TZ_GMTP95',
                "10" => '_TZ_GMTP10',
                "11" => '_TZ_GMTP11',
                "12" => '_TZ_GMTP12'
                );
$settings->write('config.values', 'system', 'timezone', $arx);
//$settings->write('config.values', 'system', 'com_mode', array('nest'=>'_NESTED', 'thread'=>'_THREADED' , 'flat'=>'_FLAT'));
//$settings->write('config.values', 'system', 'com_order', array('_OLDESTFIRST' , '_NEWESTFIRST'));*/
