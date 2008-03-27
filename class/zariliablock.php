<?php
// $Id: zariliablock.php,v 1.1 2007/03/16 02:38:59 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//

defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

require_once ZAR_ROOT_PATH . "/kernel/object.php";
class ZariliaBlock extends ZariliaObject {
    var $db;

    function ZariliaBlock( $id = null )
    {
        $this->db = &ZariliaDatabaseFactory::getdatabaseconnection();
        $this->initVar( 'bid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'mid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'func_num', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'options', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'name', XOBJ_DTYPE_TXTBOX, null, true, 150 );
        $this->initVar( 'title', XOBJ_DTYPE_TXTBOX, null, false, 150 );
        $this->initVar( 'description', XOBJ_DTYPE_TXTBOX, null, false, 150 );
        $this->initVar( 'content', XOBJ_DTYPE_TXTAREA, null, false );
        $this->initVar( 'side', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'weight', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'block_type', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'c_type', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'isactive', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'dirname', XOBJ_DTYPE_TXTBOX, null, false, 50 );
        $this->initVar( 'func_file', XOBJ_DTYPE_TXTBOX, null, false, 50 );
        $this->initVar( 'show_func', XOBJ_DTYPE_TXTBOX, null, false, 50 );
        $this->initVar( 'edit_func', XOBJ_DTYPE_TXTBOX, null, false, 50 );
        $this->initVar( 'template', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'bcachetime', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'last_modified', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'liveupdate', XOBJ_DTYPE_INT, 0, false );

        if ( !empty( $id ) ) {
            if ( is_array( $id ) ) {
                $this->assignVars( $id );
            } else {
                $this->load( intval( $id ) );
            }
        }
    }

    function load( $id )
    {
        $sql = 'SELECT * FROM ' . $this->db->prefix( 'newblocks' ) . ' WHERE bid = ' . $id;
		$result = $this->db->Execute($sql);
        $this->assignVars( $result->FetchRow() );
    }

    function store()
    {
        if ( !$this->cleanVars() ) {
            return false;
        }
        foreach ( $this->cleanVars as $k => $v ) {
            ${$k} = $v;
        }

        if ( empty( $bid ) ) {
            $bid = $this->db->genId( $this->db->prefix( "newblocks" ) . "_bid_seq" );
            $sql = sprintf( "INSERT INTO %s ( bid, mid, func_num, options, name, title, content, side, weight, block_type, c_type, isactive, dirname, func_file, show_func, edit_func, template, bcachetime, last_modified) VALUES (%u, %u, %u, %s, %s, %s, %s, %u, %u, %s, %s, %u, %s, %s, %s, %s, %s, %u, %u)", $this->db->prefix( 'newblocks' ),
                $bid, $mid, $func_num, $this->db->qstr( $options ), $this->db->qstr( $name ), $this->db->qstr( $title ), $this->db->qstr( $content ), $side, $weight, $this->db->qstr( $block_type ), $this->db->qstr( $c_type ), 1, $this->db->qstr( $dirname ), $this->db->qstr( $func_file ), $this->db->qstr( $show_func ), $this->db->qstr( $edit_func ), $this->db->qstr( $template ), $bcachetime, time() );
        } else {
            $sql = "UPDATE " . $this->db->prefix( "newblocks" ) . " SET options=" . $this->db->qstr( $options );
            // a custom block needs its own name
            if ( $block_type == "C" ) {
                $sql .= ", name=" . $this->db->qstr( $name );
            }
            $sql .= ", isactive=" . $isactive . ", title=" . $this->db->qstr( $title ) . ", content=" . $this->db->qstr( $content ) . ", side=" . $side . ", weight=" . $weight . ", c_type=" . $this->db->qstr( $c_type ) . ", template=" . $this->db->qstr( $template ) . ", bcachetime=" . $bcachetime . ", last_modified=" . time() . " WHERE bid=" . $bid;
        }
        if ( !$this->db->Execute( $sql ) ) {
            $this->setErrors( "Could not save block data into database" );
            return false;
        }
        if ( empty( $bid ) ) {
            $bid = $this->db->getInsertId();
        }
        return $bid;
    }

    function delete()
    {
        $sql = sprintf( "DELETE FROM %s WHERE bid = %u", $this->db->prefix( 'newblocks' ), $this->getVar( 'bid' ) );
        if ( !$this->db->Execute( $sql ) ) {
            return false;
        }
        $sql = sprintf( "DELETE FROM %s WHERE gperm_name = 'block_read' AND gperm_itemid = %u AND gperm_modid = 1", $this->db->prefix( 'group_permission' ), $this->getVar( 'bid' ) );
        $this->db->Execute( $sql );
        $sql = sprintf( "DELETE FROM %s WHERE block_id = %u", $this->db->prefix( 'block_addon_link' ), $this->getVar( 'bid' ) );
        $this->db->Execute( $sql );
        return true;
    }

    /**
     * do stripslashes/htmlspecialchars according to the needed output
     *
     * @param  $format output use: S for Show and E for Edit
     * @param  $c_type type of block content
     * @returns string
     */
    function &getContent( $format = 'S', $c_type = 'T' )
    {
        switch ( $format ) {
            case 'S':
                // check the type of content
                // H : custom HTML block
                // P : custom PHP block
                // S : use text sanitizater (smilies enabled)
                // T : use text sanitizater (smilies disabled)
                if ( $c_type == 'H' ) {
                    $ret = str_replace( '{X_SITEURL}', ZAR_URL . '/', $this->getVar( 'content', 'N' ) );
                    return $ret;
                } elseif ( $c_type == 'P' ) {
                    ob_start();
                    echo eval( $this->getVar( 'content', 'N' ) );
                    $content = ob_get_contents();
                    ob_end_clean();
                    $ret = str_replace( '{X_SITEURL}', ZAR_URL . '/', $content );
                    return $ret;
                } elseif ( $c_type == 'S' ) {
                    $myts = &MyTextSanitizer::getInstance();
                    $ret = str_replace( '{X_SITEURL}', ZAR_URL . '/', $myts->displayTarea( $this->getVar( 'content', 'N' ), 1, 1 ) );
                    return $ret;
                } else {
                    $myts = &MyTextSanitizer::getInstance();
                    $ret = str_replace( '{X_SITEURL}', ZAR_URL . '/', $myts->displayTarea( $this->getVar( 'content', 'N' ), 1, 0 ) );
                    return $ret;
                }
                break;
            case 'E':
                $ret = $this->getVar( 'content', 'E' );
                return $ret;
                break;
            default:
                $ret = $this->getVar( 'content', 'N' );
                return $ret;
                break;
        }
    }

    function &buildBlock()
    {
        global $zariliaConfig, $zariliaOption;
        $block = array();
        // M for addon block, S for system block C for Custom
        if ( $this->getVar( "block_type" ) != "C" ) {
            // get block display function
            $show_func = $this->getVar( 'show_func' );
            if ( !$show_func ) {
                $ret = false;
                return $ret;
            }
            // must get lang files b4 execution of the function
            if ( file_exists( ZAR_ROOT_PATH . "/addons/" . $this->getVar( 'dirname' ) . "/blocks/" . $this->getVar( 'func_file' ) ) ) {
                if ( file_exists( ZAR_ROOT_PATH . "/addons/" . $this->getVar( 'dirname' ) . "/language/" . $zariliaConfig['language'] . "/blocks.php" ) ) {
                    include_once ZAR_ROOT_PATH . "/addons/" . $this->getVar( 'dirname' ) . "/language/" . $zariliaConfig['language'] . "/blocks.php";
                } else {
                    trigger_error( 'Could not find language file for Blocks, language defines for this page will not be used', E_USER_WARNING );
                }
                include_once ZAR_ROOT_PATH . "/addons/" . $this->getVar( 'dirname' ) . "/blocks/" . $this->getVar( 'func_file' );
                $options = explode( "|", $this->getVar( "options" ) );
                if ( function_exists( $show_func ) ) {
                    // execute the function
                    $block = &$show_func( $options );
                    if ( !$block ) {
                        $ret = false;
                        return $ret;
                    }
                } else {
                    $ret = false;
                    return $ret;
                }
            } else {
                $ret = false;
                return $ret;
            }
        } else {
            // it is a custom block, so just return the contents
            $block['content'] = $this->getContent( "S", $this->getVar( "c_type" ) );
        }
        return $block;
    }

    /*
	* Aligns the content of a block
	* If position is 0, content in DB is positioned
	* before the original content
	* If position is 1, content in DB is positioned
	* after the original content
	*/
    function &buildContent( $position, $content = "", $contentdb = "" )
    {
        if ( $position == 0 ) {
            $ret = $contentdb . $content;
        } elseif ( $position == 1 ) {
            $ret = $content . $contentdb;
        }
        return $ret;
    }

    function &buildTitle( $originaltitle, $newtitle = "" )
    {
        if ( $newtitle != "" ) {
            $ret = $newtitle;
        } else {
            $ret = $originaltitle;
        }
        return $ret;
    }

    function isCustom()
    {
        if ( $this->getVar( "block_type" ) == "C" ) {
            return true;
        }
        return false;
    }

    /**
     * gets html form for editting block options
     */
    function getOptions()
    {
        global $zariliaConfig;
        if ( $this->getVar( "block_type" ) != "C" ) {
            $edit_func = $this->getVar( 'edit_func' );
            if ( !$edit_func ) {
                return false;
            }
            if ( file_exists( ZAR_ROOT_PATH . "/addons/" . $this->getVar( 'dirname' ) . "/blocks/" . $this->getVar( 'func_file' ) ) ) {
                if ( file_exists( ZAR_ROOT_PATH . "/addons/" . $this->getVar( 'dirname' ) . "/language/" . $zariliaConfig['language'] . "/blocks.php" ) ) {
                    include_once ZAR_ROOT_PATH . "/addons/" . $this->getVar( 'dirname' ) . "/language/" . $zariliaConfig['language'] . "/blocks.php";
                } else {
                    trigger_error( 'Could not find language file for cpanel, language defines for this page will not be used', E_USER_WARNING );
                }
                include_once ZAR_ROOT_PATH . '/addons/' . $this->getVar( 'dirname' ) . '/blocks/' . $this->getVar( 'func_file' );
                $options = explode( "|", $this->getVar( "options" ) );
                $edit_form = $edit_func( $options );
                if ( !$edit_form ) {
                    return false;
                }
                return $edit_form;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * get all the blocks that match the supplied parameters
     *
     * @param  $side 0: sideblock - left
     * 		 1: sideblock - right
     * 		 2: sideblock - left and right
     * 		 3: centerblock - left
     * 		 4: centerblock - right
     * 		 5: centerblock - center
     * 		 6: centerblock - left, right, center
     * 		 6: centerblock - left, right, center
     * 		 9: not visible 9: visible
     * @param  $groupid groupid (can be an array)
     * @param  $visible 0: not visible 1: visible
     * @param  $orderby order of the blocks
     * @returns array of block objects
     */
    function &getAllBlocksByGroup( $groupid, $asobject = true, $side = null, $visible = -1, $orderby = "b.weight,b.bid", $isactive = 1 )
    {
		global $zariliaDB;
        $db = &$zariliaDB;
        $ret = array();
        if ( !$asobject ) {
            $sql = "SELECT b.bid ";
        } else {
            $sql = "SELECT b.* ";
        }
        $sql .= "FROM " . $db->prefix( "newblocks" ) . " b LEFT JOIN " . $db->prefix( "group_permission" ) . " l ON l.gperm_itemid=b.bid WHERE gperm_name = 'block_read' AND gperm_modid = 1";
        if ( is_array( $groupid ) ) {
            $sql .= " AND (l.gperm_groupid=" . $groupid[0] . "";
            $size = count( $groupid );
            if ( $size > 1 ) {
                for ( $i = 1; $i < $size; $i++ ) {
                    $sql .= " OR l.gperm_groupid=" . $groupid[$i] . "";
                }
            }
            $sql .= ")";
        } else {
            $sql .= " AND l.gperm_groupid=" . $groupid . "";
        }
        $sql .= " AND b.isactive=" . $isactive;
        if ( isset( $side ) ) {
            // get both sides in sidebox? (some themes need this)
            if ( $side == ZAR_SIDEBLOCK_BOTH ) {
                $side = "(b.side=0 OR b.side=1)";
            } elseif ( $side == ZAR_CENTERBLOCK_ALL ) {
                $side = "(b.side=3 OR b.side=4 OR b.side=5 or b.side=6 or b.side=7 or b.side=8)";
            } else {
                $side = "b.side=" . $side;
            }
            $sql .= " AND " . $side;
        }
        if ( isset( $visible ) ) {
            if ( $visible == 0 ) {
                $sql .= ' AND side = 9';
            } elseif ( $visible == 1 ) {
                $sql .= ' AND side != 9';
            }
        }
        $sql .= " ORDER BY $orderby";

//		echo $sql;

        $result = $db->Execute( $sql );
        $added = array();
        while ( $myrow = $result->FetchRow() ) {
            if ( !in_array( $myrow['bid'], $added ) ) {
                if ( !$asobject ) {
                    $ret[] = $myrow['bid'];
                } else {
                    $ret[] = new ZariliaBlock( $myrow );
                }
                array_push( $added, $myrow['bid'] );
            }
        }
        return $ret;
    }

    function &getAllBlocksMid( $mid, $asobject = true, $visible = -1, $orderby = "b.weight", $isactive = 1 )
    {
        global $zariliaDB; $db = &$zariliaDB;
        $ret = array();

        if ( !$asobject ) {
            $sql = "SELECT bid ";
        } else {
            $sql = "SELECT * ";
        }
        $sql .= "FROM " . $db->prefix( "newblocks" ) . " WHERE mid = " . intval( $mid ) ;
        $sql .= " AND isactive = " . intval( $isactive );

        if ( $visible == 0 ) {
            $sql .= ' AND side = 9';
        } elseif ( $visible == 1 ) {
            $sql .= ' AND side != 9';
        }
        $sql .= " ORDER BY " . strval( $orderby );
        $result = $db->Execute( $sql );

        $added = array();
        while ( $myrow = $result->FetchRow() ) {
            if ( !in_array( $myrow['bid'], $added ) ) {
                if ( !$asobject ) {
                    $ret[] = $myrow['bid'];
                } else {
                    $ret[] = new ZariliaBlock( $myrow );
                }
                array_push( $added, $myrow['bid'] );
            }
        }
        return $ret;
    }

    function &getAllBlocks( $rettype = "object", $side = null, $visible = -1, $orderby = "side,weight,bid", $isactive = 1 )
    {
        global $zariliaDB; $db = &$zariliaDB;
        $ret = array();
        $where_query = " WHERE isactive=" . $isactive;
        if ( isset( $side ) ) {
            // get both sides in sidebox? (some themes need this)
            if ( $side == 2 ) {
                $side = "( side=0 OR side=1 )";
            } elseif ( $side == 6 ) {
                $side = "(side=3 OR side=4 OR side=5)";
            } else {
                $side = "side=" . $side;
            }
            $where_query .= " AND " . $side;
        }
        if ( $visible == 0 ) {
            $side .= ' AND side = 9';
        } elseif ( $visible == 1 ) {
            $side .= ' AND side != 9';
        }
        $where_query .= " ORDER BY $orderby";
        switch ( $rettype ) {
            case "object":
                $sql = "SELECT * FROM " . $db->prefix( "newblocks" ) . "" . $where_query;
                $result = $db->Execute( $sql );
                while ( $myrow = $result->FetchRow() ) {
                    $ret[] = new ZariliaBlock( $myrow );
                }
                break;
            case "list":
                $sql = "SELECT * FROM " . $db->prefix( "newblocks" ) . "" . $where_query;
                $result = $db->Execute( $sql );
                while ( $myrow = $result->FetchRow() ) {
                    $block = new ZariliaBlock( $myrow );
                    $name = ( $block->getVar( "block_type" ) != "C" ) ? $block->getVar( "name" ) : $block->getVar( "title" );
                    $ret[$block->getVar( "bid" )] = $name;
                }
                break;
            case "id":
                $sql = "SELECT bid FROM " . $db->prefix( "newblocks" ) . "" . $where_query;
                $result = $db->Execute( $sql );
                while ( $myrow = $result->FetchRow() ) {
                    $ret[] = $myrow['bid'];
                }
                break;
        }
        return $ret;
    }

    /**
     * ZariliaBlock::getByAddon()
     *
     * @param  $addonid
     * @param boolean $asobject
     * @return
     */
    function &getByAddon( $addonid, $asobject = true, $visible = 3 )
    {
        global $zariliaDB; $db = &$zariliaDB;
        if ( $asobject == true ) {
            $sql = "SELECT * FROM " . $db->prefix( "newblocks" ) . " WHERE mid=" . $addonid;
        } else {
            $sql = "SELECT bid FROM " . $db->prefix( "newblocks" ) . " WHERE mid=" . $addonid;
        }
        if ( $visible == 0 ) {
            $sql .= ' AND side = 9';
        } elseif ( $visible == 1 ) {
            $sql .= ' AND side != 9';
        }
        $result = $db->Execute( $sql );
        $ret = array();
        while ( $myrow = $result->FetchRow() ) {
            if ( $asobject ) {
                $ret[] = new ZariliaBlock( $myrow );
            } else {
                $ret[] = $myrow['bid'];
            }
        }
        return $ret;
    }

    function &getAllByGroupAddonAdmin( $groupid, $selmod, $addon_id = 0, $toponlyblock = false, $visible = 3, $orderby = 'b.weight,b.bid', $isactive = 1 )
    {
        global $zariliaDB; $db = &$zariliaDB;
        $ret = array();
        $sql = "SELECT DISTINCT gperm_itemid FROM " . $db->prefix( 'group_permission' ) . " WHERE gperm_name = 'block_read' AND gperm_modid = 1";
        if ( is_array( $groupid ) ) {
            $sql .= ' AND gperm_groupid IN (' . implode( ',', $groupid ) . ')';
        } else {
            if ( intval( $groupid ) > 0 ) {
                $sql .= ' AND gperm_groupid=' . $groupid;
            }
        }
        $result = $db->Execute( $sql );
        $blockids = array();
        while ( $myrow = $result->FetchRow() ) {
            $blockids[] = $myrow['gperm_itemid'];
        }

        if ( !empty( $blockids ) ) {
            $sql = 'SELECT b.* FROM ' . $db->prefix( 'newblocks' ) . ' b, ' . $db->prefix( 'block_addon_link' ) . ' m WHERE m.block_id=b.bid';
            $sql .= ' AND b.mid=' . $addon_id;
            $sql .= ' AND b.isactive=' . $isactive;

            if ( $visible == 0 ) {
                $sql .= ' AND b.side = 9';
            } elseif ( $visible == 1 ) {
                $sql .= ' AND b.side != 9';
            }

            $selmod = intval( $selmod );
            if ( !empty( $selmod ) ) {
                $sql .= ' AND m.addon_id IN (0,' . $selmod;
                if ( $toponlyblock ) {
                    $sql .= ',-1';
                }
                $sql .= ')';
            } else {
                if ( $toponlyblock ) {
                    $sql .= ' AND m.addon_id IN (0,-1)';
                } else {
                    $sql .= ' AND m.addon_id=0';
                }
            }

            $sql .= ' AND b.bid IN (' . implode( ',', $blockids ) . ')';
            $sql .= ' ORDER BY ' . $orderby;
            $result = $db->Execute( $sql );
            while ( $myrow = $result->FetchRow() ) {
                $block = &new ZariliaBlock( $myrow );
                $ret[$myrow['bid']] = &$block;
                unset( $block );
            }
        }
        return $ret;
    }

    function &getAllByGroupAddon( $groupid, $addon_id = 0, $toponlyblock = false, $visible = -1, $orderby = 'b.weight,b.bid', $isactive = 1 )
    {
        global $zariliaDB; $db = &$zariliaDB;
        $ret = array();
        $sql = "SELECT DISTINCT gperm_itemid FROM " . $db->prefix( 'group_permission' ) . " WHERE gperm_name = 'block_read' AND gperm_modid = 1";
        if ( is_array( $groupid ) ) {
            $sql .= ' AND gperm_groupid IN (' . implode( ',', $groupid ) . ')';
        } else {
            if ( intval( $groupid ) > 0 ) {
                $sql .= ' AND gperm_groupid=' . $groupid;
            }
        }
        $result = $db->Execute( $sql );
        $blockids = array();
        while ( $myrow = $result->FetchRow() ) {
            $blockids[] = $myrow['gperm_itemid'];
        }
        if ( !empty( $blockids ) ) {
            $sql = 'SELECT b.* FROM ' . $db->prefix( 'newblocks' ) . ' b, ' . $db->prefix( 'block_addon_link' ) . ' m WHERE m.block_id=b.bid';
            $sql .= ' AND b.isactive=' . $isactive;
            if ( $visible == 0 ) {
                $sql .= ' AND b.side = 9';
            } elseif ( $visible == 1 ) {
                $sql .= ' AND b.side != 9';
            }
            $addon_id = intval( $addon_id );
            if ( !empty( $addon_id ) ) {
                $sql .= ' AND m.addon_id IN (0,' . $addon_id;
                if ( $toponlyblock ) {
                    $sql .= ',-1';
                }
                $sql .= ')';
            } else {
                if ( $toponlyblock ) {
                    $sql .= ' AND m.addon_id IN (0,-1)';
                } else {
                    $sql .= ' AND m.addon_id=0';
                }
            }
            $sql .= ' AND b.bid IN (' . implode( ',', $blockids ) . ')';
            $sql .= ' ORDER BY ' . $orderby;
            $result = $db->Execute( $sql );
            while ( $myrow = $result->FetchRow() ) {
                $block = &new ZariliaBlock( $myrow );
                $ret[$myrow['bid']] = &$block;
                unset( $block );
            }
        }
        return $ret;
    }

    function &getAllByGroupAddonSides( $groupid, $addon_id = 0, $toponlyblock = false, $visible = -1, $orderby = 'b.weight,b.bid', $isactive = 1 )
    {
        global $zariliaDB; $db = &$zariliaDB;
        $ret = array();
        $sql = "SELECT DISTINCT gperm_itemid FROM " . $db->prefix( 'group_permission' ) . " WHERE gperm_name = 'block_read' AND gperm_modid = 1";
        if ( is_array( $groupid ) ) {
            $sql .= ' AND gperm_groupid IN (' . implode( ',', $groupid ) . ')';
        } else {
            if ( intval( $groupid ) > 0 ) {
                $sql .= ' AND gperm_groupid=' . $groupid;
            }
        }
        $result = $db->Execute( $sql );
        $blockids = array();
        while ( $myrow = $result->FetchRow() ) {
            $blockids[] = $myrow['gperm_itemid'];
        }

        if ( !empty( $blockids ) ) {
            $sql = 'SELECT b.* FROM ' . $db->prefix( 'newblocks' ) . ' b, ' . $db->prefix( 'block_addon_link' ) . ' m WHERE m.block_id=b.bid';
            $sql .= ' AND b.isactive=' . $isactive;

            $side = "( AND b.side=0 OR b.side=1 )";
            if ( $visible == 0 ) {
                $sql .= ' AND b.side = 9';
            } elseif ( $visible == 1 ) {
                $sql .= ' AND b.side != 9';
            }
            $addon_id = intval( $addon_id );
            if ( !empty( $addon_id ) ) {
                $sql .= ' AND m.addon_id IN (0,' . $addon_id;
                if ( $toponlyblock ) {
                    $sql .= ',-1';
                }
                $sql .= ')';
            } else {
                if ( $toponlyblock ) {
                    $sql .= ' AND m.addon_id IN (0,-1)';
                } else {
                    $sql .= ' AND m.addon_id=0';
                }
            }
            $sql .= ' AND b.bid IN (' . implode( ',', $blockids ) . ')';
            $sql .= ' ORDER BY ' . $orderby;
            $result = $db->Execute( $sql );
            while ( $myrow = $result->FetchRow() ) {
                $block = &new ZariliaBlock( $myrow );
                $ret[$myrow['bid']] = &$block;
                unset( $block );
            }
        }
        return $ret;
    }

    function &getNonGroupedBlocks( $addon_id = 0, $toponlyblock = false, $visible = -1, $orderby = 'b.weight,b.bid', $isactive = 1 )
    {
        global $zariliaDB; $db = &$zariliaDB;
        $ret = array();
        $bids = array();
        $sql = "SELECT DISTINCT(bid) from " . $db->prefix( 'newblocks' );
        if ( $result = $db->Execute( $sql ) ) {
            while ( $myrow = $db->fetchArray( $result ) ) {
                $bids[] = $myrow['bid'];
            }
        }
        $sql = "SELECT DISTINCT(p.gperm_itemid) from " . $db->prefix( 'group_permission' ) . " p, " . $db->prefix( 'groups' ) . " g WHERE g.groupid=p.gperm_groupid AND p.gperm_name='block_read'";
        $grouped = array();
        if ( $result = $db->Execute( $sql ) ) {
            while ( $myrow = $db->fetchArray( $result ) ) {
                $grouped[] = $myrow['gperm_itemid'];
            }
        }
        $non_grouped = array_diff( $bids, $grouped );
        if ( !empty( $non_grouped ) ) {
            $sql = 'SELECT b.* FROM ' . $db->prefix( 'newblocks' ) . ' b, ' . $db->prefix( 'block_addon_link' ) . ' m WHERE m.block_id=b.bid AND b.side != 9';
            $sql .= ' AND b.isactive=' . $isactive;
            if ( $visible == 0 ) {
                $sql .= ' AND b.side = 9';
            } elseif ( $visible == 1 ) {
                $sql .= ' AND b.side != 9';
            }
            $addon_id = intval( $addon_id );
            if ( !empty( $addon_id ) ) {
                $sql .= ' AND m.addon_id IN (0,' . $addon_id;
                if ( $toponlyblock ) {
                    $sql .= ',-1';
                }
                $sql .= ')';
            } else {
                if ( $toponlyblock ) {
                    $sql .= ' AND m.addon_id IN (0,-1)';
                } else {
                    $sql .= ' AND m.addon_id=0';
                }
            }
            $sql .= ' AND b.bid IN (' . implode( ',', $non_grouped ) . ')';
            $sql .= ' ORDER BY ' . $orderby;
            $result = $db->Execute( $sql );
            while ( $myrow = $db->fetchArray( $result ) ) {
                $block = &new ZariliaBlock( $myrow );
                $ret[$myrow['bid']] = &$block;
                unset( $block );
            }
        }
        return $ret;
    }

    function getRenderedBlockContent() {
		global $zariliaTpl, $zariliaLogger;
			if (!is_object($zariliaTpl)) {
				require_once ZAR_ROOT_PATH.'/class/template.php';
				$zariliaTpl = new ZariliaTpl();
			}
		    /*
			* See if we have a bloack cachetime
			*/
		    $bcachetime = $this->getVar( 'bcachetime' );
		    if ( empty( $bcachetime ) ) {
		        $zariliaTpl->zarilia_setCaching( 0 );
		    } else {
		        $zariliaTpl->zarilia_setCaching( 2 );
		        $zariliaTpl->zarilia_setCacheTime( $bcachetime );
		    }
		    /*
			* Template
			*/
		    $btpl = $this->getVar( 'template' );
		    //echo $this->getVar( 'template' );
		    if ( $btpl != '' ) {
				if ( empty( $bcachetime ) || !$zariliaTpl->is_cached( 'db:' . $btpl, 'blk_' . $this->getVar( 'bid' ) ) ) {
				    $zariliaLogger->addBlock( $this->getVar( 'name' ) );
			        $bresult = &$this->buildBlock();
				    if ( !$bresult ) return '';
					$zariliaTpl->assign_by_ref( 'block', $bresult );
			        $bcontent = $zariliaTpl->fetch( 'db:' . $btpl, 'blk_' . $this->getVar( 'bid' ) );
				    $zariliaTpl->clear_assign( 'block' );
					return $bcontent;
		        } else {
			        $zariliaLogger->addBlock( $this->getVar( 'name' ), true, $bcachetime );
				    return $zariliaTpl->fetch( 'db:' . $btpl, 'blk_' . $this->getVar( 'bid' ) );
				}
		    } else {
				$bid = $this->getVar( 'bid' );
		        if ( empty( $bcachetime ) || !$zariliaTpl->is_cached( 'db:system_dummy.html', 'blk_' . $bid ) ) {
				    $zariliaLogger->addBlock( $this->getVar( 'name' ) );
		            $bresult = &$this->buildBlock();
				    if ( !$bresult ) return '';
		            $zariliaTpl->assign_by_ref( 'dummy_content', $bresult['content'] );
				    $bcontent = $zariliaTpl->fetch( 'db:system_dummy.html', 'blk_' . $bid );
		            $zariliaTpl->clear_assign( 'block' );
					return $bcontent;
				} else {
		            $zariliaLogger->addBlock( $this->getVar( 'name' ), true, $bcachetime );
				    return $zariliaTpl->fetch( 'db:system_dummy.html', 'blk_' . $bid );
		        }
		  }
	}

	/**
	 * This function gets live update obj
	 */
	//function getAsLiveUpdateObj() {
		//require_once ZAR_ROOT_PATH.'/class/ajax/controls/liveupdate.php';
		//$live_update = new ZariliaControl_LiveUpdate(ZAR_ROOT_PATH.'/class/zariliablock.php', 'ZariliaBlock::doLiveUpdate', $this->getVar('bid'));
		//return $live_update->render();
	//}

	/**
 	 * Do Live Update
	 */
#	 static function doLiveUpdate($bid) {
#		$block = new ZariliaBlock($bid);
#		return $block->getRenderedBlockContent();
#	 }

}

?>