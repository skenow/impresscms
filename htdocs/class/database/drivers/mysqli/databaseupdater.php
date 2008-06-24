<?php
/**
 * Contains the classes for updating database tables
 *
 * @license GNU
 * @author marcan <marcan@smartfactory.ca>
 * @version $Id: databaseupdater.php 1341 2008-03-26 14:33:44Z malanciault $
 * @link http://www.smartfactory.ca The SmartFactory
 * @package SmartObject
 */

if (!defined("XOOPS_ROOT_PATH")) {
	die("XOOPS root path not defined");
}

/**
 * base class
 */
include_once XOOPS_ROOT_PATH."/class/database/databaseupdater.php";

class IcmsMysqliDatabasetable extends IcmsDatabasetable {
	
}

class IcmsMysqliDatabaseupdater extends IcmsDatabaseupdater {

}
?>