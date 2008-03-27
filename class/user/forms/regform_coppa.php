<?php
/**
 * $Id: regform_coppa.php,v 1.2 2007/05/09 14:14:21 catzwolf Exp $ Untitled 1.php v0.0 14/04/2007 05:47:43 John
 *
 * @Zarilia - 	PHP Content Management System
 * @copyright 2007 Zarilia
 * @Author : 	John (AKA Catzwolf)
 * @URL : 		http://zarilia.com
 * @Project :	Zarilia CMS
 */
global $zariliaConfig;

include ZAR_ROOT_PATH . '/language/'.$zariliaConfig['language'].'/age.php';

$_date = time();
$d = date( 'j', $_date );
$m = date( 'm', $_date );
$year = date( 'Y', $_date );

$register_form = new ZariliaThemeForm( _US_REG_PRIVACY_HEADING, 'registerform', 'index.php', 'post' );
$titles_tray = new ZariliaFormElementTray( '', '<br />' );
/**
 * Days
 */
$date_select = new ZariliaFormSelect( _AD_AGE_DAY, 'day', $d );
for ( $i = 1; $i <= 31; $i++ ) {
    $date_select->addOption( $i, $i );
}
/**
 * months
 */
$mon_array = array( 1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec' );
$mon_select = new ZariliaFormSelect( _AD_AGE_MONTH, 'mon', $m );
$mon_select->addOptionArray( $mon_array );
/**
 * years
 */
$year_select = new ZariliaFormSelect( _AD_AGE_YEAR, 'year', $year );
for ( $y = 1910; $y <= $year; $y++ ) {
    $year_select->addOption( $y, $y );
}

$agree_tray = new ZariliaFormElementTray( _AD_AGE_AGEVERICATION, '&nbsp;' );
$agree_tray->addElement( $mon_select );
$agree_tray->addElement( $date_select );
$agree_tray->addElement( $year_select );
$register_form->addElement( $agree_tray );

$titles_checkbox = new ZariliaFormCheckBox( '', 'user_coppa_agree' );
$titles_checkbox->addOption( 0, _AD_AGE_IAMOVER );
$titles_tray->addElement( $titles_checkbox, true );
$register_form->addElement( $titles_tray, true );

//$register_form->addElement( new ZariliaFormHidden( 'op', $op ) );
//$register_form->addElement( new ZariliaFormHidden( 'age_dtitle', $age_dtitle ) );
//$register_form->addElement( new ZariliaFormHidden( 'age_itemid', $age_itemid ) );

if ( isset( $this->_zariliaCoppa['coppa_email'] ) ) {
    $content['coppa_email'] = sprintf( _US_PLZCONTACT, $this->_zariliaCoppa['coppa_email'] );
}
if ( isset( $this->_hidden ) && !empty( $this->_hidden ) ) {
    foreach( $this->_hidden as $k => $v ) {
        $register_form->addElement( new ZariliaFormHidden( $k, $v ) );
    }
}
$content['form'] = $register_form;
$content['file'] = 'coppa';

$this->addOptions(
    array( 'title' => _US_REGCOPPA,
        'subtitle' => _US_REGCOPPA_DSC,
        'header' => $myts->displayTarea( $zariliaCoppa['coppa_text'], 1, 1, 1, 1, 1, false ),
        'content' => $content
        )
    );

?>