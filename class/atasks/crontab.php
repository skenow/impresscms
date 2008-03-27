<?php

require ZAR_ROOT_PATH.'/class/atasks/base.class.php';

/*
 * some parts are taked from CronTab class developed by cjpa@audiophile.com
 */

class AutomatedTasks_Crontab
	extends AutomatedTasks {

	var $_lines = array();
	var $_line_id = -1;

	/*
	 * Constructor
	 */
	function AutomatedTasks_CronTab() {		
		$this->AutomatedTasks();
	}

   /*
    * check if can run
    * @return bool
    */
   function canRun() {
	   if (PHP_OS == 'WINNT') return false;
	   $rez = strtolower(shell_exec('crontab'));
  	   if (substr($rez, 'is not recognized as an internal or external command')===false) return false;
	   if (substr($rez, 'Command not found')===false) return false;
	   return true;
   }

   /*
    * Set Checking Interval (if not enabled enables automated tasks system
	* @param  int	$interval	interval of checking for new tasks
	* @return bool				returns true if start was succesfull
	*/
   function start($interval) {
	   $id = $this->getProcessId();
	   if ($id < 0) {
		    $this->_line_id = count($this->_lines);
	   } else {
			if ($this->getInterval() == $interval) return false;
	   }
	   $arx = &$this->getIntervalArray($interval);
	   $arx['command'] = $this->getCommandLine();
	   $this->_lines[$this->_line_id] = array($arx, 4);
	   $this->writeCronTab();
	   return true;
   }

   function getInterval() {
	   return $this->getNormalValue($this->_lines[$this->_line_id][0]['minute']) + 
	  	      $this->getNormalValue($this->_lines[$this->_line_id][0]['hour']) * 60 +
 		      $this->getNormalValue($this->_lines[$this->_line_id][0]['day']) * 60 * 24 +
		      $this->getNormalValue($this->_lines[$this->_line_id][0]['month']) * 60 * 24 * 30;
   }

   function &getIntervalArray($interval) {
	    $hours = $days = $months = 0;
		if ($interval>60) {
			$minutes   = $interval % 60;
			$interval -= $minutes * 60;
			if ($interval > 24) {
				$hours   = $interval % 24;
				$interval -= $hours * 60;
				if ($interval > 30) {
					$days   = $interval % 30;
					$interval -= $hours * 60;
					if ($interval > 12) {
						$months = 12;
					} else {
						$months = $interval;
					}
				} else {
					$days = $interval;
				}
			} else {
				$hours = $interval;
			}
		} else {
			$minutes = $interval;
		}
		$hours	 = $this->getCronTabValue($hours);
		$days	 = $this->getCronTabValue($days);
		$months	 = $this->getCronTabValue($months);
		$rez = array( "minute" => $this->getCronTabValue($minutes), "hour" => $this->getCronTabValue($hours), "dayofmonth" => $this->getCronTabValue($days), "month" => $this->getCronTabValue($months), 'dayofweek' => '*');
		return $rez;
   }

   function getCronTabValue($number) {
	  if ($number == 0) return '*';
	  return '*/'.$number;
   }

   function getNormalValue($crontab_number) {
	  if ($crontab_number == '*') return 0;
	  return intval(substr($crontab_number,2));
   }

   /*
	* Stops automated tasks system
	* @return bool returns true if was succesfull
	*/
   function stop() {
	    $id = $this->getProcessId();
		if ($id < 0) return false;
		unset($this->_lines[$id]);
		$this->writeCronTab();
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
				$ret .= '-q '.ZAR_ROOT_PATH.'/include/atasks.php'; 
			break;
			case 'lynx':
			default:
				$ret .= '--dump '.ZAR_URL.'/include/atasks.php';
			break;
		}
		return $ret.' > /dev/null';
	}

	/*
	 * gets running process id
	 * @return int
	 */
	function getProcessId() {
		if ($this->_line_id > 0) return $this->_line_id;
		if (empty($this->_lines)) $this->readCronTab();
		$cmd = $this->getCommandLine();
		foreach ($this->_lines as $id => $line) {
			if ($line[1] != 4) continue;
			if ($line[0]['command'] == $cmd) {
				$this->_line_id = $id;
				return $this->_line_id;
			}
		}
		$this->_line_id = -1;
		return $this->_line_id;
	}

	/*
	 *	Reads cron tab file and parses to $this->_lines array
	 */
	function readCronTab() {
		exec( "crontab -u $this->user -l", $crons, $return);
		if ($return != 0) return false;

        foreach ( $crons as $line ) {
            $line = trim( $line ); // discarding all prepending spaces and tabs
            // empty lines..
            if ( !$line ) {
				$this->_lines[] = array('',0);
				continue;
			}
            // checking if this is a comment
            if ( $line[0] == "#" ) {
				$this->_lines[] = array($line,1);
				continue;
            }
            // Checking if this is an assignment
            if ( ereg( "(.*)=(.*)", $line, $assign ) ) {
				$this->_lines[] = array(array( "name" => $assign[1], "value" => $assign[2] ),2);
                continue;
            }
            // Checking if this is a special -entry. check man 5 crontab for more info
            if ( $line[0] == '@' ) {
                $this->_lines[] = array(split( "[ \t]", $line, 2 ), 3);
                continue;
            }
            // It's a regular crontab-entry
            $ct = split( "[ \t]", $line, 6 );
			$this->_lines[] = array(array( "minute" => $ct[0], "hour" => $ct[1], "dayofmonth" => $ct[2], "month" => $ct[3], "dayofweek" => $ct[4], "command" => $ct[5] ), 4);
        }

		return true;
	}

	/*
	 * Writes crontab files back to where it belongs
	 */
	function writeCronTab() {
		$filename = tempnam(ZAR_ROOT_PATH.'/cache', 'cron');
        $file = fopen( $filename, "w" );
		foreach($this->_lines as $current_line) {
            switch ( $current_line[1] ) {
                case 1: // comment
                    $line = $current_line[0];
                    break;
                case 2: //assign					
                    $line = $current_line[0]['name'] . " = " . $current_line[0]['value'];
                    break;
                case 4: //comand
                    $line = implode( ' ', $current_line[0] );
                    break;
                case 3: //special
                    $line = implode( ' ', $current_line[0] );
                    break;
                case 0: //empty line
                    $line = "\n"; // an empty line in the crontab-file
                    break;
                default:
                    die('ERROR: Unknown type of line.');
            }
            fwrite( $file, $line . "\n" );
        }
        fclose( $file );

		exec( "crontab $filename", $returnar, $return );
        if ( $return != 0 ) {
           die("Error running crontab ($return). $filename not deleted\n");
		} else {
           unlink( $filename );
        }
	}

}

?>