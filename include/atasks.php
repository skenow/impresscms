<?php
/**
 * This file is used for executing sheduled tasks
 */
if ( function_exists( 'zarilia_gethandler' ) ) {
    if ( ( $zariliaConfig['events_system'] == 'internal' ) || ( isset( $zariliaOption['aTaskMode'] ) ) ) {
        $zariliaEvents = &zarilia_gethandler( 'events' );
        $zariliaEvents->doEvents();
    }
    if ( $zariliaConfig['events_system'] != 'internal' ) {
        /*FIX ME ERROR CHECKING NONE HERE!!*/
        $zariliaEvents = &zarilia_gethandler( 'events' );
        $zariliaATasks = &$zariliaEvents->getATaskObj();
        if ( is_object( $zariliaATasks ) && $zariliaATasks->needStart() ) {
            $zariliaATasks->start( intval( $zariliaConfig['events_ckinterval'] ) );
        }
        unset( $zariliaATasks, $zariliaEvents );
    }
} else {
    $zariliaOption = array();
    $zariliaOption['aTaskMode'] = true;
    chdir( '..' );
    include_once 'mainfile.php';
}

?>