<?php
// $Id: menu.php,v 1.3 2004/02/28 01:35:23 mithyt2 Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

$adminmenu[1]['title'] = _MI_NEWS_ADMENU2;
$adminmenu[1]['link'] = 'admin/index.php?op=topicsmanager';
$adminmenu[2]['title'] = _MI_NEWS_ADMENU3;
$adminmenu[2]['link'] = 'admin/index.php?op=newarticle';
$adminmenu[3]['title'] = _MI_NEWS_GROUPPERMS;
$adminmenu[3]['link'] = 'admin/groupperms.php';
$adminmenu[4]['title'] = _MI_NEWS_PRUNENEWS;
$adminmenu[4]['link'] = 'admin/index.php?op=prune';
$adminmenu[5]['title'] = _MI_NEWS_EXPORT;
$adminmenu[5]['link'] = 'admin/index.php?op=export';
$adminmenu[6]['title'] = _MI_NEWS_STATS;
$adminmenu[6]['link'] = 'admin/index.php?op=stats';
$adminmenu[7]['title'] = _MI_NEWS_NEWSLETTER;
$adminmenu[7]['link'] = 'admin/index.php?op=configurenewsletter';
$adminmenu[8]['title'] = _MI_NEWS_METAGEN;
$adminmenu[8]['link'] = "admin/index.php?op=metagen";
?>