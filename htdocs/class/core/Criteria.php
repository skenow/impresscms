<?php

/**
 * A single criteria
 *
 * @package     kernel
 * @subpackage  database
 *
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class core_Criteria extends core_CriteriaElement {

	/**
	 * @var	string
	 */
	public $prefix;
	public $function;
	public $column;
	public $operator;
	public $value;

	/**
	 * Constructor
	 *
	 * @param   string  $column
	 * @param   string  $value
	 * @param   string  $operator
	 **/
	public function __construct($column, $value='', $operator='=', $prefix = '', $function = '') {
		$this->prefix = $prefix;
		$this->function = $function;
		$this->column = $column;
		$this->value = $value;
		$this->operator = $operator;
	}

	/**
	 * Make a sql condition string
	 *
	 * @return  string
	 **/
	public function render() {
		$clause = (!empty($this->prefix) ? "{$this->prefix}." : "") . $this->column;
		if ( !empty($this->function) ) {
			$clause = sprintf($this->function, $clause);
		}
		if ( in_array( strtoupper( $this->operator ), array( 'IS NULL', 'IS NOT NULL' ) ) ) {
			$clause .= ' ' . $this->operator;
		} else {
			if ( '' === ($value = trim($this->value) ) ) {
				return '';
			}
			if ( !in_array( strtoupper($this->operator), array('IN', 'NOT IN') ) ) {
				if ( ( substr( $value, 0, 1 ) != '`' ) && ( substr( $value, -1 ) != '`' ) ) {
					$value = "'$value'";
				} elseif ( !preg_match( '/^[a-zA-Z0-9_\.\-`]*$/', $value ) ) {
					$value = '``';
				}
			}
			$clause .= " {$this->operator} $value";
		}
		return $clause;
	}

	/**
	 * Generate an LDAP filter from criteria
	 *
	 * @return string
	 * @author Nathan Dial ndial@trillion21.com, improved by Pierre-Eric MENUET pemen@sourceforge.net
	 */
	public function renderLdap(){
		if ($this->operator == '>') {
			$this->operator = '>=';
		}
		if ($this->operator == '<') {
			$this->operator = '<=';
		}

		if ($this->operator == '!=' || $this->operator == '<>') {
			$operator = '=';
			$clause = "(!(" . $this->column . $operator . $this->value . "))";
		}
		else {
			if ($this->operator == 'IN') {
				$newvalue = str_replace(array('(',')'),'',
				$this->value);
				$tab = explode(',',$newvalue);
				foreach ($tab as $uid)
				{
					$clause .= '(' . $this->column . '=' . $uid
					.')';
				}
				$clause = '(|' . $clause . ')';
			}
			else {
				$clause = "(" . $this->column . $this->operator . $this->value . ")";
			}
		}
		return $clause;
	}

	/**
	 * Make a SQL "WHERE" clause
	 *
	 * @return	string
	 */
	public function renderWhere() {
		$cond = $this->render();
		return empty($cond) ? '' : "WHERE $cond";
	}
}

