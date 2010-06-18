<?php
/**
 * Criteria Base Class for composing Where clauses in SQL Queries
 *
 * @copyright	http://www.xoops.org/ The XOOPS Project
 * @copyright	XOOPS_copyrights.txt
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license	LICENSE.txt
 * @package	core
 * @since	XOOPS
 * @author	http://www.xoops.org The XOOPS Project
 * @author	modified by UnderDog <underdog@impresscms.org>
 * @version	$Id: criteria.php 19118 2010-03-27 17:46:23Z skenow $
 */

/**
 *
 *
 * @package     kernel
 * @subpackage  database
 *
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */

/**
 * A criteria (grammar?) for a database query.
 *
 * Abstract base class should never be instantiated directly.
 *
 * @abstract
 *
 * @package     kernel
 * @subpackage  database
 *
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class core_CriteriaElement
{
	/**
	 * Sort order
	 * @var	string
	 */
	var $order = 'ASC';

	/**
	 * @var	string
	 */
	var $sort = '';

	/**
	 * Number of records to retrieve
	 * @var	int
	 */
	var $limit = 0;

	/**
	 * Offset of first record
	 * @var	int
	 */
	var $start = 0;

	/**
	 * @var	string
	 */
	var $groupby = '';

	/**
	 * Constructor
	 **/
	function core_CriteriaElement()
	{

	}

	/**
	 * Render the criteria element
	 */
	function render()
	{

	}

	/**#@+
	 * Accessor
	 */
	/**
	 * @param	string  $sort
	 */
	function setSort($sort)
	{
		$this->sort = $sort;
	}

	/**
	 * @return	string
	 */
	function getSort()
	{
		return $this->sort;
	}

	/**
	 * @param	string  $order
	 */
	function setOrder($order)
	{
		if ('DESC' == strtoupper($order)) {
			$this->order = 'DESC';
		}
	}

	/**
	 * @return	string
	 */
	function getOrder()
	{
		return $this->order;
	}

	/**
	 * @param	int $limit
	 */
	function setLimit($limit=0)
	{
		$this->limit = (int) ($limit);
	}

	/**
	 * @return	int
	 */
	function getLimit()
	{
		return $this->limit;
	}

	/**
	 * @param	int $start
	 */
	function setStart($start=0)
	{
		$this->start = (int) ($start);
	}

	/**
	 * @return	int
	 */
	function getStart()
	{
		return $this->start;
	}

	/**
	 * @param	string  $group
	 */
	function setGroupby($group){
		$this->groupby = $group;
	}

	/**
	 * @return	string
	 */
	function getGroupby(){
		return ' GROUP BY '.$this->groupby;
	}
	/**#@-*/
}
?>