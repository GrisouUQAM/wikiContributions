<?php

	function dateConverter($givenDate) {
		if (gettype($givenDate) === 'integer') {
			return round($givenDate / 86400);	
		}
		else {
			$givenTime = strtotime($givenDate);
			return round($givenTime / 86400);
		}
	}

	 /**
	  * perform the difference between the date entred and the current date
	  * @param {date} date1 date.
	  * @return {int} diffrerence between date1 and the current date in seconds 
	  */
	 
	function substructDate($date1){
		$sec = time() - $date1;
		return $sec;
	}
?>
