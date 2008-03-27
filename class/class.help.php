<?php
// $Id: class.help.php,v 1.1 2007/03/16 02:38:58 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

class ZariliaHelp {
    /**
     * ZariliaHelp::ZariliaHelp()
     *
     * @param string $aboutTitle
     */
    function ZariliaHelp() {
        global $zariliaAddon, $zariliaConfig;
        $fileName = ZAR_ROOT_PATH . "/addons/" . $zariliaAddon->getVar( 'dirname' ) . '/language/' . $zariliaConfig['language'] . '/cpanel.php';
        if ( file_exists( $fileName ) ) {
            include_once $fileName;
        } else {
            trigger_error( 'Could not find language file for cpanel, language defines for this page will not be used', E_USER_WARNING );
        }
    }

    /**
     * ZariliaAbout::display()
     *
     * @return
     */
    function render() {
        global $addonversion, $fct;
        /**
         */
        $num = ( isset( $addonversion['system'] ) && isset( $fct ) ) ? 1 : 0;
        switch ( $num ) {
            case 0:
            default:
                $this->userAddon();
                break;
            case 1:
                $this->systemAddon();
                break;
        } // switch
    }

    /**
     * ZariliaAbout::systemAddon()
     *
     * @return
     */
    function systemAddon() {
        global $addonversion, $fct;

        $_protected_array = array( 'name', 'description', 'author', 'credits', 'license', 'lead', 'contributors', 'website_url',
            'website_name', 'email', 'version', 'status', 'releasedate', 'disclaimer',
            'demo_site_url', 'demo_site_name', 'support_site_url', 'support_site_name', 'submit_bug_url',
            'submit_bug_name', 'submit_feature_url', 'submit_feature_name'
            );

        foreach( $_protected_array as $k ) {
            $mod_version[$k] = ( isset( $addonversion[$k] ) ) ? $addonversion[$k] : '';
            if ( $k = 'description' ) {
                $myts = &MyTextSanitizer::getInstance();
                $mod_version[$k] = $myts->displayTarea( $addonversion[$k], $html = 0, $smiley = 0, $xcode = 0, $image = 0, $br = 1 );
                unset( $myts );
            }
        }
        $file_name = ZAR_ROOT_PATH . '/addons/system/admin/' . $fct . '/changelog.txt';
        if ( file_exists( $file_name ) && !is_dir( $file_name ) ) {
            $file_text = file_get_contents( $file_name );
            $myts = &MyTextSanitizer::getInstance();
			$mod_version['changelog'] = $myts->displayTarea( $file_text, $html = 1, $smiley = 0, $xcode = 0, $image = 0, $br = 1 );
        	unset( $myts );
		} else {
            $mod_version['changelog'] = _CP_AM_AB_NOLOG;
        }

        $ret = '<table width="100%" cellpadding="2" cellspacing="1" >
				 <tr>
				  <th colspan="2">' . _CP_AM_AB_MAIN_INFO . '</th>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_ADDON . '</td>
				  <td class="aboutcontent">' . $mod_version['name'] . '</td>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_DESCRIPTION . '</td>
				  <td class="aboutcontent">' . $mod_version['description'] . '</td>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_AUTHOR . '</td>
				  <td class="aboutcontent">' . $mod_version['author'] . '</td>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_VERSION . '</td>
				  <td class="aboutcontent">' . $mod_version['version'] . '</td>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_STATUS . '</td>
				  <td class="aboutcontent">' . $mod_version['status'] . '</td>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_RELEASEDATE . '</td>
				  <td class="aboutcontent">' . $mod_version['releasedate'] . '</td>
				 </tr>
				 <tr>
				  <td colspan="2" class="aboutfooter">&nbsp;</td>
				 </tr>
				 <tr>
				  <th colspan="2">' . _CP_AM_AB_DEV_INFO . '</th>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_LEAD . '</td>
				  <td class="aboutcontent">' . $mod_version['lead'] . '</td>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_CONTRIBUTORS . '</td>
				  <td class="aboutcontent">' . $mod_version['contributors'] . '</td>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_WEBSITE_URL . '</td>
				  <td class="aboutcontent"><a href="' . $mod_version['website_url'] . '" target="_blank">' . $mod_version['website_name'] . '</a></td>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_EMAIL . '</td>
				  <td class="aboutcontent"><a href="mailto:' . $mod_version['email'] . '">' . $mod_version['email'] . '</a></td>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_CREDITS . '</td>
				  <td class="aboutcontent">' . $mod_version['credits'] . '</td>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_LICENSE . '</td>
				  <td class="aboutcontent">' . $mod_version['license'] . '</td>
				 </tr>
				 <tr>
				  <td colspan="2" class="aboutfooter">&nbsp;</td>
				 </tr>
				 <tr>
				  <th colspan="2">' . _CP_AM_AB_SUPPORT_INFO . '</th>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_DEMO_SITE_URL . '</td>
				  <td class="aboutcontent"><a href="' . $mod_version['demo_site_url'] . '" target="_blank">' . $mod_version['demo_site_name'] . '</a></td>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_SUPPORT_SITE_URL . '</td>
				  <td class="aboutcontent"><a href="' . $mod_version['support_site_url'] . '" target="_blank">' . $mod_version['support_site_name'] . '</a></td>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_SUBMIT_BUG . '</td>
				  <td class="aboutcontent"><a href="' . $mod_version['submit_bug_url'] . '" target="_blank">' . $mod_version['submit_bug_name'] . '</a></td>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_SUBMIT_FEATURE . '</td>
				  <td class="aboutcontent"><a href="' . $mod_version['submit_feature_url'] . '" target="_blank">' . $mod_version['submit_feature_name'] . '</a></td>
				 </tr>
				 <tr>
				  <td colspan="2" class="aboutfooter">&nbsp;</td>
				 </tr>
				 <tr>
				  <th colspan="2">' . _CP_AM_AB_SUPPORT_INFO . '</th>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_DISCLAIMER . '</td>
				  <td class="aboutcontent">' . $mod_version['disclaimer'] . '</td>
				 </tr>
				 </tr>
				 <tr>
				  <td colspan="2" class="aboutfooter">&nbsp;</td>
				 </tr>
				 <tr>
				  <th colspan="2">' . _CP_AM_AB_CHANGELOG . '</th>
				 </tr>
				 <tr>
				  <td class="abouttitle">' . _CP_AM_AB_LOG . '</td>
				  <td class="aboutcontent">' . $mod_version['changelog'] . '</td>
				 </tr>
				 <tr>
				  <td colspan="2" class="aboutfooter">&nbsp;</td>
				 </tr>
				</table>';
        echo $ret;
        unset( $mod_version );
    }
}

?>