<?php

class IcmsTimer
{
	public $logstart = array();
	public $logend = array();

	/**
	 * Constructor
	 */
	private function __construct(){ /* Empty! */ }

	/**
	 * Get a reference to the only instance of this class
	 *
	 * @return  object XoopsLogger  (@link XoopsLogger) reference to the only instance
	 * @static
	 */
	static public function &instance() {
		static $instance;
		if ( !isset( $instance ) ) {
			$instance = new IcmsTimer();
		}
		return $instance;
	}


	/**
	 * Returns the current microtime in seconds.
	 * @return float
	 */
	function microtime() {
		$now = explode( ' ', microtime() );
		return (float)$now[0] + (float)$now[1];
	}

	/**
	 * Start a timer
	 * @param   string  $name   name of the timer
	 */
	function startTime($name = 'ImpressCMS') {
		$this->logstart[$name] = $this->microtime();
	}

	/**
	 * Stop a timer
	 * @param   string  $name   name of the timer
	 */
	function stopTime($name = 'ImpressCMS') {
		$this->logend[$name] = $this->microtime();
	}



	/**
	 * get the current execution time of a timer
	 *
	 * @param   string  $name   name of the counter
	 * @return  float   current execution time of the counter
	 */
	public function dumpTime( $name = 'ImpressCMS' ) {
		if ( !isset($this->logstart[$name]) ) {
			return 0;
		}
		$stop = isset( $this->logend[$name] ) ? $this->logend[$name] : $this->microtime();
		return $stop - $this->logstart[$name];
	}


}

?>