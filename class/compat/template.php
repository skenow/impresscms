<?php
/*
 * Created on Dec 18, 2007
 *
 * This file is for compatibility with some template functions of Xoops 2.2
 */

 class compatTemplate {

 	static function loadModuleAdminMenu ($currentoption = 0, $breadcrumb = '')
    {
        /**
    	* @global object $xoopsModule {@link XoopsModule} object for the current module
    	*/
        global $zariliaAddon, $zariliaTpl;

        /**
    	* @var array $menuoptions - get the adminmenu variables from the template object (assigned during xoops_cp_header() )
    	*/
        $menuoptions = $zariliaTpl->get_template_vars('adminmenu');
        /**
        * If the current module has menu links there
        */
        if (isset($menuoptions[$zariliaAddon->getVar('mid')])) {
            /**
            * Add the breadcrumb to the links
            */
            $menuoptions[$zariliaAddon->getVar('mid')]['breadcrumb'] = $breadcrumb;
            /**
            * Add the currently selected option
            */
            $menuoptions[$zariliaAddon->getVar('mid')]['current'] = $currentoption;
            /**
            * Assign the links with additional information to the template object
            */
            $this->tplEngine->assign('modulemenu', $menuoptions[$zariliaAddon->getVar('mid')]);
        }
    }

 }

?>
