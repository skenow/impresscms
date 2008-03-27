<?php
/**
 * ZariliaTree
 *
 * @package
 * @author Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: zariliatree.php,v 1.2 2007/03/30 22:05:45 catzwolf Exp $
 * @access public
 */
class ZariliaTree {
    var $table; //table with parent-child structure
    var $id; //name of unique id for records in table $table
    var $pid; // name of parent id used in table $table
    var $order; //specifies the order of query results
    var $title; // name of a field in table $table which will be used when  selection box and paths are generated
    var $arr = array(); //mySql fetch content
    var $ptrees = array(); //parent trees array
    var $db;
    // constructor of class ZariliaTree
    // sets the names of table, unique id, and parend id
    function ZariliaTree( $table_name, $id_name, $pid_name )
    {
        $this->db = &ZariliaDatabaseFactory::getDatabaseConnection();
        $this->table = $table_name;
        $this->id = $id_name;
        $this->pid = $pid_name;
        $sql = "SELECT * FROM " . $this->table . " ORDER BY " . $this->id;
        $result = $this->db->Execute( $sql );
        $count = $result->NumRows();
        while ( $myrow = $result->FetchRow() ) {
            $this->arr[$myrow[$this->id]] = $myrow;
            $this->ptrees[$this->pid][] = $myrow[$this->id];
            //var_dump($this->id); var_dump($myrow);
        }
        //var_dump($this->ptrees);
    }
    // returns an array of first child objects for a given id($sel_id)
    function getFirstChild( $sel_id, $order = "" )
    {
        $arr = array();
        if ( !isset( $this->ptrees[$sel_id] ) || count ( @$this->ptrees[$sel_id] ) == 0 ) {
            return $parray;
        }
        $keys = $this->getKeys( $sel_id, $order );
        foreach ( $keys as $key ) {
            $arr[] = $this->arr[$key];
        }
        return $arr;
    }
    // returns an array of all FIRST child ids of a given id($sel_id)
    function getFirstChildId( $sel_id )
    {
        return $this->ptrees[$sel_id];
    }
    // returns an array of ALL child ids for a given id($sel_id)
    function getAllChildId( $sel_id, $order = "", $idarray = array() )
    {
        if ( !isset( $this->ptrees[$sel_id] ) || count ( @$this->ptrees[$sel_id] ) == 0 ) {
            return $parray;
        }
        $keys = $this->getKeys( $sel_id, $order );
        foreach ( $keys as $key ) {
            $idarray[] = $key;
            $idarray = $this->getAllChildId( $key, $order, $idarray );
        }
        return $idarray;
    }
    // returns an array of ALL parent ids for a given id($sel_id)
    function getAllParentId( $sel_id, $idarray = array() )
    {
        $r_id = $this->arr[$sel_id][$this->pid];
        if ( $r_id == 0 ) {
            return $idarray;
        }
        $idarray[] = $r_id;
        $idarray = $this->getAllParentId( $r_id, $idarray );
        return $idarray;
    }
    // generates path from the root id to a given id($sel_id)
    // the path is delimetered with "/"
    function getPathFromId( $sel_id, $title, $path = "" )
    {
        $parentid = $this->arr[$sel_id][$this->pid];
        $path = "/" . htmlSpecialChars( $this->arr[$sel_id][$title], ENT_QUOTES ) . $path . "";
        if ( $parentid == 0 ) {
            return $path;
        }
        $path = $this->getPathFromId( $parentid, $title, $path );
        return $path;
    }
    // makes a nicely ordered selection box
    // $preset_id is used to specify a preselected item
    // set $none to 1 to add a option with value 0
    function makeMySelBox( $title, $order = "", $preset_id = 0, $none = 0, $sel_name = "", $onchange = "" )
    {
        if ( $sel_name == "" ) {
            $sel_name = $this->id;
        }
        $keys = $this->getKeys( 0, $order );

        echo "<select name='" . $sel_name . "'";
        if ( $onchange != "" ) {
            echo " onchange='" . $onchange . "'";
        }
        echo ">\n";
        if ( $none ) {
            echo "<option value='0'>---------------</option>\n";
        }

        foreach ( $keys as $catid ) {
            $sel = "";
            if ( $catid == $preset_id ) {
                $sel = " selected='selected'";
            }
            echo "<option value='$catid'$sel>" . $this->arr[$catid][$title] . "</option>\n";
            $sel = "";
            $arr = $this->getChildTreeArray( $catid, $order );
            foreach ( $arr as $option ) {
                $option['prefix'] = str_replace( ".", "--", $option['prefix'] );
                $catpath = $option['prefix'] . "&nbsp;" . htmlSpecialChars( $option[$title], ENT_QUOTES );
                if ( $option[$this->id] == $preset_id ) {
                    $sel = " selected='selected'";
                }
                echo "<option value='" . $option[$this->id] . "'$sel>$catpath</option>\n";
                $sel = "";
            }
        }
        echo "</select>\n";
    }
    // generates nicely formatted linked path from the root id to a given id
    function getNicePathFromId( $sel_id, $title, $funcURL, $path = "" )
    {
        $parentid = $this->arr[$sel_id][$this->pid];
        $path = "<a href='" . $funcURL . "&" . $this->id . "=" . $sel_id . "'>" . htmlSpecialChars( $this->arr[$sel_id][$title], ENT_QUOTES ) . "</a>&nbsp;:&nbsp;" . $path . "";
        if ( $parentid == 0 ) {
            return $path;
        }
        return $this->getNicePathFromId( $parentid, $title, $funcURL, $path );
    }
    // generates id path from the root id to a given id
    // the path is delimetered with "/"
    function getIdPathFromId( $sel_id, $path = "" )
    {
        $parentid = $this->arr[$sel_id][$this->pid];
        $path = "/" . $sel_id . $path . "";
        if ( $parentid == 0 ) {
            return $path;
        }
        $path = $this->getIdPathFromId( $parentid, $path );
        return $path;
    }

    function getAllChild( $sel_id = 0, $order = "", $parray = array() )
    {
        if ( !isset( $this->ptrees[$sel_id] ) || count ( @$this->ptrees[$sel_id] ) == 0 ) {
            return $parray;
        }

        $keys = $this->getKeys( $sel_id, $order );
        foreach ( $keys as $key ) {
            $parray[] = $this->arr[$key];
            $parray = $this->getAllChild( $key, $order, $parray );
        }
        return $parray;
    }

    function getChildTreeArray( $sel_id = 0, $order = "", $parray = array(), $r_prefix = "" )
    {
        if ( !isset( $this->ptrees[$sel_id] ) || count ( @$this->ptrees[$sel_id] ) == 0 ) {
            return $parray;
        }
        $keys = $this->getKeys( $sel_id, $order );
        foreach ( $keys as $key ) {
            $row = $this->arr[$key];
            $row['prefix'] = $r_prefix . ".";
            $parray[] = $row;
            $parray = $this->getChildTreeArray( $key, $order, $parray, $row['prefix'] );
        }
        return $parray;
    }

    /*
	* New function to get the keys ordered or not
	*/
    function getKeys( $sel_id, $order )
    {
        if ( empty ( $order ) ) {
            return $this->ptrees[$sel_id];
        }
        $order = explode ( " ", $order );
        if ( strtolower( end( $order ) ) == 'desc' ) {
            array_pop ( $order );
            $func = 'desc';
        } else {
            if ( strtolower( end( $order ) ) == 'asc' ) {
                array_pop ( $order );
            }
            $func = '';
        } // TODO: multisort
        foreach ( $this->ptrees[$sel_id] as $key ) {
            $sort[$key] = $this->arr[$key][$order[0]];
        }
        natcasesort ( $sort );
        if ( $func == 'desc' ) {
            $sort = array_reverse( $sort, true );
        }
        return array_keys ( $sort );
    }
}

?>