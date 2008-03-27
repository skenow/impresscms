<?php
// $Id: xmlrpctag.php,v 1.1 2007/03/16 02:42:17 catzwolf Exp $
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


class ZariliaXmlRpcDocument
{

    var $_tags = array();

    function ZariliaXmlRpcDocument()
    {

    }

    function add(&$tagobj)
    {
        $this->_tags[] =& $tagobj;
    }

    function render()
    {
    }

}

class ZariliaXmlRpcResponse extends ZariliaXmlRpcDocument
{
	function render()
    {
        $count = count($this->_tags);
        $payload = '';
        for ($i = 0; $i < $count; $i++) {
            if (!$this->_tags[$i]->isFault()) {
                $payload .= $this->_tags[$i]->render();
            } else {
                return '<?xml version="1.0"?><methodResponse>'.$this->_tags[$i]->render().'</methodResponse>';
            }
        }
        return '<?xml version="1.0"?><methodResponse><params><param>'.$payload.'</param></params></methodResponse>';
    }
}

class ZariliaXmlRpcRequest extends ZariliaXmlRpcDocument
{

	var $methodName;

	function ZariliaXmlRpcRequest($methodName)
	{
		$this->methodName = trim($methodName);
	}

	function render()
    {
        $count = count($this->_tags);
        $payload = '';
        for ($i = 0; $i < $count; $i++) {
            $payload .= '<param>'.$this->_tags[$i]->render().'</param>';
        }
        return '<?xml version="1.0"?><methodCall><methodName>'.$this->methodName.'</methodName><params>'.$payload.'</params></methodCall>';
    }
}

class ZariliaXmlRpcTag
{

    var $_fault = false;

    function ZariliaXmlRpcTag()
    {

    }

    function &encode(&$text)
    {
        $text = preg_replace(array("/\&([a-z\d\#]+)\;/i", "/\&/", "/\#\|\|([a-z\d\#]+)\|\|\#/i"), array("#||\\1||#", "&amp;", "&\\1;"), str_replace(array("<", ">"), array("&lt;", "&gt;"), $text));
		return $text;
    }

    function setFault($fault = true){
        $this->_fault = (intval($fault) > 0) ? true : false;
    }

    function isFault()
    {
        return $this->_fault;
    }

    function render()
    {
    }
}

class ZariliaXmlRpcFault extends ZariliaXmlRpcTag
{

    var $_code;
    var $_extra;

    function ZariliaXmlRpcFault($code, $extra = null)
    {
        $this->setFault(true);
        $this->_code = intval($code);
        $this->_extra = isset($extra) ? trim($extra) : '';
    }

    function render()
    {
        switch ($this->_code) {
        case 101:
            $string = 'Invalid server URI';
            break;
        case 102:
            $string = 'Parser parse error';
            break;
        case 103:
            $string = 'Addons not found';
            break;
        case 104:
            $string = 'User authentication failed';
            break;
        case 105:
            $string = 'Addons API not found';
            break;
        case 106:
            $string = 'Method response error';
            break;
        case 107:
            $string = 'Method not supported';
            break;
        case 108:
            $string = 'Invalid parameter';
            break;
        case 109:
            $string = 'Missing parameters';
            break;
        case 110:
            $string = 'Selected blog application does not exist';
            break;
        case 111:
            $string = 'Method permission denied';
            break;
        default:
            $string = 'Method response error';
            break;
        }
        $string .= "\n".$this->_extra;
        return '<fault><value><struct><member><name>faultCode</name><value>'.$this->_code.'</value></member><member><name>faultString</name><value>'.$this->encode($string).'</value></member></struct></value></fault>';
    }
}

class ZariliaXmlRpcInt extends ZariliaXmlRpcTag
{

    var $_value;

    function ZariliaXmlRpcInt($value)
    {
        $this->_value = intval($value);
    }

    function render()
    {
        return '<value><int>'.$this->_value.'</int></value>';
    }
}

class ZariliaXmlRpcDouble extends ZariliaXmlRpcTag
{

    var $_value;

    function ZariliaXmlRpcDouble($value)
    {
        $this->_value = (float)$value;
    }

    function render()
    {
        return '<value><double>'.$this->_value.'</double></value>';
    }
}

class ZariliaXmlRpcBoolean extends ZariliaXmlRpcTag
{

    var $_value;

    function ZariliaXmlRpcBoolean($value)
    {
        $this->_value = (!empty($value) && $value != false) ? 1 : 0;
    }

    function render()
    {
        return '<value><boolean>'.$this->_value.'</boolean></value>';
    }
}

class ZariliaXmlRpcString extends ZariliaXmlRpcTag
{

    var $_value;

    function ZariliaXmlRpcString($value)
    {
        $this->_value = strval($value);
    }

    function render()
    {
        return '<value><string>'.$this->encode($this->_value).'</string></value>';
    }
}

class ZariliaXmlRpcDatetime extends ZariliaXmlRpcTag
{

    var $_value;

    function ZariliaXmlRpcDatetime($value)
    {
        if (!is_numeric($value)) {
            $this->_value = strtotime($value);
        } else {
            $this->_value = intval($value);
        }
    }

    function render()
    {
        return '<value><dateTime.iso8601>'.gmstrftime("%Y%m%dT%H:%M:%S", $this->_value).'</dateTime.iso8601></value>';
    }
}

class ZariliaXmlRpcBase64 extends ZariliaXmlRpcTag
{

    var $_value;

    function ZariliaXmlRpcBase64($value)
    {
        $this->_value = base64_encode($value);
    }

    function render()
    {
        return '<value><base64>'.$this->_value.'</base64></value>';
    }
}

class ZariliaXmlRpcArray extends ZariliaXmlRpcTag
{

    var $_tags = array();

    function ZariliaXmlRpcArray()
    {
    }

    function add(&$tagobj)
    {
        $this->_tags[] =& $tagobj;
    }

    function render()
    {
        $count = count($this->_tags);
        $ret = '<value><array><data>';
        for ($i = 0; $i < $count; $i++) {
            $ret .= $this->_tags[$i]->render();
        }
        $ret .= '</data></array></value>';
        return $ret;
    }
}

class ZariliaXmlRpcStruct extends ZariliaXmlRpcTag{

    var $_tags = array();

    function ZariliaXmlRpcStruct(){
    }

    function add($name, &$tagobj){
        $this->_tags[] = array('name' => $name, 'value' => $tagobj);
    }

    function render(){
        $count = count($this->_tags);
        $ret = '<value><struct>';
        for ($i = 0; $i < $count; $i++) {
            $ret .= '<member><name>'.$this->encode($this->_tags[$i]['name']).'</name>'.$this->_tags[$i]['value']->render().'</member>';
        }
        $ret .= '</struct></value>';
        return $ret;
    }
}
?>