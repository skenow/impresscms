<?php
include 'admin_header.php';
$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'default' );
switch ( strtolower( $op ) ) {
    case 'doprune':
        $timeafter = zarilia_cleanRequestVars( $_REQUEST, 'timeafter', '' );
        $readonly = zarilia_cleanRequestVars( $_REQUEST, 'readonly', 0 );
        $inbox = zarilia_cleanRequestVars( $_REQUEST, 'inbox', 0 );
        $trash = zarilia_cleanRequestVars( $_REQUEST, 'trash', 0 );
        $tracker = zarilia_cleanRequestVars( $_REQUEST, 'tracker', 0 );
        $notify = zarilia_cleanRequestVars( $_REQUEST, 'notify', 1 );
        /*
		* Set Criteria
		*/
        $criteria = new CriteriaCompo();
        if ( !empty( $timeafter['date'] ) ) {
            $time = strtotime( $timeafter['date'] ) + intval( $timeafter['time'] );
        } else {
            $time = time();
        } 
        $criteria->add( new Criteria( 'read_date', $time, "<" ) );

        if ( $inbox == 1 ) {
            $criteria->add( new Criteria( 'msg', 1, '=' ) );
        } 
        if ( $trash == 1 ) {
            $criteria->add( new Criteria( 'is_trash', 1 ), 'OR' );
        } 
        if ( $tracker == 1 ) {
            $criteria->add( new Criteria( 'track', 1 ), 'OR' );
        } 
		$result = $pm_handler->deleteAll( $criteria, true );
        echo $result;
		if ( $result ) {
            $notifycriteria = $criteria;
            $notifycriteria->setGroupBy( 'to_userid' );
            $user_id = $pm_handler->getCount( $notifycriteria );
            if ( $user_id > 0 ) {
                foreach ( $user_id as $user_id => $messagecount ) {
                    $pm = $pmhandler->create();
                    $pm->setVar( "subject", $zariliaAddonConfig['prunesubject'] );
                    $pm->setVar( "msg_text", str_replace( '{PM_COUNT}', $messagecount, $zariliaAddonConfig['prunemessage'] ) );
                    $pm->setVar( "to_userid", $uid );
                    $pm->setVar( "from_userid", $zariliaUser->getVar( "uid" ) );
                    $pmhandler->insert( $pm );
					unset( $pm );
                } 
            }  
        } 

        zarilia_cp_header();
        $menu_handler->render( 1 );
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
			echo "<div>".sprintf(_MS_PM_PRUNEDCOUNT, $result)."</div>";		
		}
        break;
    case 'prune':
    case 'default':
        zarilia_cp_header();
        $menu_handler->render( 1 );
        $sform = $pm_handler->getMessagePruneForm();
        $sform->display();
        break;
} // switch
zarilia_cp_footer();

?>
