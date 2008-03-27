<?php

require ZAR_ROOT_PATH.'/class/atasks/base.class.php';

class AutomatedTasks_At
	extends AutomatedTasks {

	/*
	 * Constructor
	 */
	function AutomatedTasks_At() {		
		$this->AutomatedTasks();
	}

   /*
    * check if can run
    * @return bool
    */
   function canRun() {
	   if (PHP_OS != 'WINNT') return false;
	   if (!isset($_SERVER['COMSPEC'])) return false;	   
	   return file_exists($_SERVER['COMSPEC']);
   }

   /*
    * Set Checking Interval (if not enabled enables automated tasks system
	* @param  int	$interval	interval of checking for new tasks
	* @return bool				returns true if start was succesfull
	*/
   function start($interval) {
	   if ($this->isEnabled()) $this->stop();
	   $rez = shell_exec('at '.date('H:i', strtotime("+$interval minute")).' '.$this->getCommandLine());
	   return (substr($rez, 0, 5) == 'Added');
   }

   /*
	* Stops automated tasks system
	* @return bool returns true if was succesfull
	*/
   function stop() {
	    $id = $this->getProcessId();
		if ($id < 0) return false;
		$rez = shell_exec('at '.$id.' /DELETE');
		return true;
   }

   /* 
    *  checks if core is enabled
	*
    * @return bool 
	*/
	function isEnabled() {
		return ($this->getProcessId()>0);
	}

	/*
	 * gets command executed
	 * @return string
	 */
	function getCommandLine() {
		global $zariliaConfig;
		if (!isset($zariliaConfig['atasks_exec'])) {
			$zariliaConfig['atasks_exec'] = 'lynx';
		}
		$ret = $zariliaConfig['atasks_exec'].' ';
		switch ($zariliaConfig['atasks_exec']) {
			case 'php':
				$ret .= '-q "'.ZAR_ROOT_PATH.'/include/atasks.php" > NULL'; 
			break;
			case 'lynx':
			default:
				$ret .= '--dump "'.ZAR_URL.'/include/atasks.php" > NULL';
			break;
		}
		return $ret;
	}

	/*
	 * gets running process id
	 * @return int
	 */
	function getProcessId() {
		$rez = shell_exec('at');
		if (strstr($rez, 'There are no entries in the list.')) return -1;
		$rez = explode("\n", $rez);
		$pos = array(strpos($rez[0], 'Status ID'),
					 strpos($rez[0], 'Day'),
					 strpos($rez[0], 'Time'),
					 strpos($rez[0], 'Command Line')
				     );
		$count = array(count($rez), count($pos));
		$cmd_to_find = str_replace('"', '',$this->getCommandLine());
		for($i=2; $i<$count[0]; $i++) {
/*			$tmp_data = array();
			for($o = 0; $o<($count[1]-1); $o++) {
				$tmp_data[] = substr($rez[$i], $pos[$o], $pos[$o+1] - $pos[$o]);
			}
			$tmp_data[] = substr($rez[$i], $pos[$count[1]-1]);*/
			$id		= intval(trim(substr($rez[$i], $pos[0], $pos[1] - $pos[0])));
			$cmd	= substr($rez[$i], $pos[$count[1]-1]);
			echo '['.$cmd.'] ['.$cmd_to_find."]<br>";
			if ($cmd == $cmd_to_find) return $id;
		}
		return -2;
	}

	/**
	 *  Checks if need set new timer when automated task object was executed
	 *
	 *  @return bool
	 */
	function needStart() {
		return true;
	}

}

?>