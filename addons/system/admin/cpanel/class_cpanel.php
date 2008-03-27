<?php

/**
 * Zarilia_cpanel
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: class_cpanel.php,v 1.1 2007/05/05 11:10:25 catzwolf Exp $
 * @access public
 */
class Zarilia_cpanel {
    /**
     * Zarilia_cpanel::Zarilia_cpanel()
     */
    function Zarilia_cpanel() {
    }

    /**
     * Zarilia_cpanel::cp_show()
     *
     * @return
     */
    function cp_show() {
        $ret = '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td width="60%" class="CPindexOptions">';
        foreach( $this->cp_getQuickLinks as $k => $v ) {
            $ret = '<div class="cpicon"><a href="' . ZAR_URL . '/addons/system/index.php?fct="' . $k . ' ><br /><img src = "' . ZAR_URL . '/addons/system/images/system/avatar_' . $k . '.png" hspace="10" vspace="100"/><br /><span>' . $v . '</span></a></div></td>';
        }
        $ret .= '<td width="20">&nbsp;</td><td width="48%" class="CPindexOptions">' . $this->cp_show() . '</td></tr></table>';
    }



    /**
     * Zarilia_cpanel::cp_getQuickLinks()
     *
     * @return
     */
    function cp_getQuickLinks() {
        return array( 'avatars' => _MD_AM_AVATARS );
    }

    /**
     * Zarilia_cpanel::cp_getVarionUpdate()
     *
     * @return
     */
    function cp_getVersionUpdate() {
    }
}

?>