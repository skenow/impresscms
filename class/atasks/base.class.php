<?php

class AutomatedTasks {

	/*
	 * Constructor
	 */
	function AutomatedTasks() {		
	}

   /*
    * check if can run
    * @return bool
    */
   function canRun() {
	   return false;
   }

   /*
    * Set Checking Interval (if not enabled enables automated tasks system
	* @param  int	$interval	interval of checking for new tasks
	* @return bool				returns true if start was succesfull
	*/
   function start($interval) {
		die('ERROR: This function can\'t be called directly.');
   }

   /*
	* Stops automated tasks system
	* @return bool returns true if was succesfull
	*/
   function stop() {
		die('ERROR: This function can\'t be called directly.');
   }

   /* 
    *  checks if core is enabled
	*
    * @return bool 
	*/
	function isEnabled() {
		return false;
	}

	/**
	 *  Checks if need set new timer when automated task object was executed
	 *
	 *  @return bool
	 */
	function needStart() {
		return false;
	}
	

}

?>