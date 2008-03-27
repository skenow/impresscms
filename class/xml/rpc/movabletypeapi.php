<?php
// $Id: movabletypeapi.php,v 1.1 2007/03/16 02:42:17 catzwolf Exp $
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

if (!defined('ZAR_ROOT_PATH')) {
	die("ZARILIA root path not defined");
}
require_once ZAR_ROOT_PATH.'/class/xml/rpc/xmlrpcapi.php';

class MovableTypeApi extends ZariliaXmlRpcApi
{
    function MovableTypeApi(&$params, &$response, &$addon)
    {
        $this->ZariliaXmlRpcApi($params, $response, $addon);
    }

    function getCategoryList()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            $zariliaapi =& $this->_getZariliaApi($this->params);
            $zariliaapi->_setUser($this->user, $this->isadmin);
            $ret =& $zariliaapi->getCategories(false);
            if (is_array($ret)) {
                $arr = new ZariliaXmlRpcArray();
                foreach ($ret as $id => $name) {
                    $struct = new ZariliaXmlRpcStruct();
                    $struct->add('categoryId', new ZariliaXmlRpcString($id));
                    $struct->add('categoryName', new ZariliaXmlRpcString($name['title']));
                    $arr->add($struct);
                    unset($struct);
                }
                $this->response->add($arr);
            } else {
                $this->response->add(new ZariliaXmlRpcFault(106));
            }
        }
    }

    function getPostCategories()
    {
        $this->response->add(new ZariliaXmlRpcFault(107));
    }

    function setPostCategories()
    {
        $this->response->add(new ZariliaXmlRpcFault(107));
    }

    function supportedMethods()
    {
        $this->response->add(new ZariliaXmlRpcFault(107));
    }
}
?>