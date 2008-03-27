<?php
// $Id: zariliaeditor.php,v 1.1 2007/03/16 02:40:56 catzwolf Exp $
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
/**
 * ZARILIA editor handler
 *
 * @author phppp (D.J.)
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaEditorHandler {
    var $root_path = "";
    var $nohtml = false;

    function ZariliaEditorHandler()
    {
        $current_path = __FILE__;
        if ( DIRECTORY_SEPARATOR != "/" ) {
            $current_path = str_replace( strpos( $current_path, "\\\\", 2 ) ? "\\\\" : DIRECTORY_SEPARATOR, "/", $current_path );
        }
        $this->root_path = dirname( $current_path );
    }

    /**
     *
     * @param string $name Editor name
     * @param array $options editor options: $key=>$val
     * @param string $OnFailure a pre-validated editor that will be used if the required editor is failed to create
     * @param bool $noHtml dohtml disabled
     */
    function &get( $name = "", $options = null, $noHtml = false, $OnFailure = "" )
    {
        $editor = null;
        $list = array_keys( $this->getList( $noHtml ) );
        if ( !empty( $name ) && in_array( $name, $list ) ) {
            $editor = &$this->_loadEditor( $name, $options );
        }
        if ( !is_object( $editor ) ) {
            if ( empty( $OnFailure ) || !in_array( $OnFailure, $list ) ) {
                $OnFailure = $list[0];
            }
            $editor = &$this->_loadEditor( $OnFailure, $options );
        }
        return $editor;
    }

    function &getList( $noHtml = false )
    {
        static $editors;
        if ( !isset( $editors ) ) {
            $order = array();
            $list = ZariliaLists::getDirListAsArray( $this->root_path . '/' );
            foreach( $list as $item ) {
                if ( is_readable( $this->root_path . '/' . $item . '/editor_registry.php' ) ) {
                    include( $this->root_path . '/' . $item . '/editor_registry.php' );
                    if ( empty( $config['order'] ) ) {
                        continue;
                    }
                    $editors[$config['name']] = $config;
                    $order[] = $config['order'];
                }
            }
            array_multisort( $order, $editors );
        }
        $_list = array();
        foreach( $editors as $name => $item ) {
            if ( !empty( $noHtml ) && empty( $item['nohtml'] ) ) {
                continue;
            }
            $_list[$name] = $item['title'];
        }
        return $_list;
    }

    function render( &$editor )
    {
        return $editor->render();
    }

    function setConfig( &$editor, $options )
    {
        if ( method_exists( $editor, 'setConfig' ) ) {
            $editor->setConfig( $options );
        } else {
            foreach( $options as $key => $val ) {
                $editor->$key = $val;
            }
        }
    }

    function &_loadEditor( $name = "", $options = null )
    {
        $editor_path = $this->root_path . "/" . strtolower($name);
        if ( !is_readable( $editor_path . "/editor_registry.php" ) ) {
            return false;
        }
        include( $editor_path . "/editor_registry.php" );
        if ( empty( $config['order'] ) ) {
            return null;
        }
        include_once( $config['file'] );
		$editor = &new $config['class']( $options );
        return $editor;
    }
}

?>