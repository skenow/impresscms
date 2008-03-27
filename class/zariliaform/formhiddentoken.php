<?php
// $Id: formhiddentoken.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           					//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               	//
// -------------------------------------------------------------------------//

/**
 * A hidden token field
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormHiddenToken extends ZariliaFormHidden {
    /**
     * Constructor
     *
     * @param string $name "name" attribute
     */
    function ZariliaFormHiddenToken( $name = null, $timeout = 360 ) {
        /*if ( empty( $name ) ) {
            $token = &ZariliaMultiTokenHandler::quickCreate( ZAR_TOKEN_DEFAULT );
            $name = $token->getTokenName();
        } else {
            $token = &ZariliaSingleTokenHandler::quickCreate( ZAR_TOKEN_DEFAULT );
        }*/
		$zs = new ZariliaSecurity();		
		if ($name===null) $name = 'ZAR_TOKEN_REQUEST';
        $this->ZariliaFormHidden( $name, $zs->createToken($timeout) );
    }
}
?>