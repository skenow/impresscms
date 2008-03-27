<?php
// $Id: class.pdf.php,v 1.1 2007/03/16 02:38:58 catzwolf Exp $
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

class cp_dopdf {
    var $options = array();
    var $compression = false;
    var $font = 'Helvetica.afm';

    function cp_dopdf( $opt = array() ) {
        if ( !is_array( $opt ) || empty( $opt ) ) {
            return false;
        }
        $this->options = $opt;
    }

    function renderpdf() {
		require ZAR_ROOT_PATH . '/class/pdf/class.ezpdf.php';

        $pdf = new Cezpdf( 'a4', 'P' ); //A4 Portrait
        $pdf->options['compression'] = $this->compression;

        $pdf->ezSetCmMargins( 2, 1.5, 1, 1 );
        // select font
        $pdf->selectFont( ZAR_ROOT_PATH . '/class/pdf/fonts/' . $this->font, _CHARSET ); //choose font

        $all = $pdf->openObject();
        $pdf->saveState();
        $pdf->setStrokeColor( 0, 0, 0, 1 );
        // footer
        $pdf->addText( 30, 822, 6, $this->options['slogan'] );
        $pdf->line( 10, 40, 578, 40 );
        $pdf->line( 10, 818, 578, 818 );
        // add url to footer
        $pdf->addText( 30, 34, 6, ZAR_URL );
        // add pdf creater
        $pdf->addText( 250, 34, 6, $this->options['creator'] );
        // add render date to footer
        $pdf->addText( 450, 34, 6, _CONTENT_RENDERED . ' ' . $this->options['renderdate'] );

        $pdf->restoreState();
        $pdf->closeObject();
        $pdf->addObject( $all, 'all' );
        $pdf->ezSetDy( 30 );

        // title
        $pdf->ezText( $this->options['title'], 16 );
        $pdf->ezText( "\n", 6 );

        if ( $this->options['author'] ) {
            $pdf->ezText( _CONTENT_AUTHOR . $this->options['author'], 8 );
        }
        if ( $this->options['pdate'] ) {
            $pdf->ezText( _CONTENT_PUBLISHED . $this->options['pdate'], 8 );
        }
        if ( $this->options['udate'] ) {
            $pdf->ezText( _CONTENT_UPDATED . $this->options['udate'], 8 );
        }
        $pdf->ezText( "\n", 6 );

        if ( $this->options['itemurl'] ) {
	        $pdf->ezText( _CONTENT_URL_TOITEM .  $this->options['itemurl'], 8 );
	        $pdf->ezText( "\n", 6 );
        }

        if ( $this->options['subtitle'] ) {
	        $pdf->ezText( $this->options['subtitle'], 14 );
	        $pdf->ezText( "\n", 6 );
        }

        $pdf->ezText( $this->options['content'], 10 );
        $pdf->ezStream();
    }

    function setTitle( $value = '' ) {
        $this->options['title'] = $value;
    }

    function setSubTitle( $value = '' ) {
        $this->options['subtitle'] = $value;
    }

    function setCreater( $value = '' ) {
        $this->options['creator'] = $value;
    }

    function setSlogan( $value = '' ) {
        $this->options['slogan'] = $value;
    }

    function setAuthor( $value = '' ) {
        $this->options['author'] = $value;
    }

    function setContent( $value = '' ) {
        $this->options['content'] = $value;
    }

    function setPDate( $value = '' ) {
        $this->options['pdate'] = $value;
    }

    function setUDate( $value = '' ) {
        $this->options['udate'] = $value;
    }

    function setFont( $value = '' ) {
        $this->font = strval( trim( $value ) );
    }

    function useCompression( $value = false ) {
        $this->compression = ( $value == true ) ? true : false;
    }
}
?>