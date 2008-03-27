<?php
// $Id: zariliamultimailer.php,v 1.1 2007/03/16 02:40:49 catzwolf Exp $
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
/**
 * @package		class
 * @subpackage	mail
 * 
 * @filesource 
 *
 * @author		Jochen Büînagel	<jb@buennagel.com>
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 *
 * @version		$Revision: 1.1 $ - $Date: 2007/03/16 02:40:49 $
 */

/**
 * load the base class
 */
require_once(ZAR_ROOT_PATH.'/class/mail/phpmailer/class.phpmailer.php');

/**
 * Mailer Class.
 * 
 * At the moment, this does nothing but send email through PHP's "mail()" function,
 * but it has the abiltiy to do much more.
 * 
 * If you have problems sending mail with "mail()", you can edit the member variables
 * to suit your setting. Later this will be possible through the admin panel.
 *
 * @todo		Make a page in the admin panel for setting mailer preferences.
 * 
 * @package		class
 * @subpackage	mail
 *
 * @author		Jochen Buennagel	<job@buennagel.com>
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @version		$Revision: 1.1 $ - changed by $Author: catzwolf $ on $Date: 2007/03/16 02:40:49 $
 */
class ZariliaMultiMailer extends phpmailer {

	/**
	 * "from" address
	 * @var 	string
	 * @access	private
	 */
	var $From 		= "";
	
	/**
	 * "from" name
	 * @var 	string
	 * @access	private
	 */
	var $FromName 	= "";

	// can be "smtp", "sendmail", or "mail"
	/**
	 * Method to be used when sending the mail.
	 * 
	 * This can be:
	 * <li>mail (standard PHP function "mail()") (default)
	 * <li>smtp	(send through any SMTP server, SMTPAuth is supported.
	 * You must set {@link $Host}, for SMTPAuth also {@link $SMTPAuth}, 
	 * {@link $Username}, and {@link $Password}.)
	 * <li>sendmail (manually set the path to your sendmail program 
	 * to something different than "mail()" uses in {@link $Sendmail}) 
	 * 
	 * @var 	string
	 * @access	private
	 */
	var $Mailer		= "mail";

	/**
	 * set if $Mailer is "sendmail"
	 * 
	 * Only used if {@link $Mailer} is set to "sendmail".
	 * Contains the full path to your sendmail program or replacement.
	 * @var 	string
	 * @access	private
	 */
	var $Sendmail = "/usr/sbin/sendmail";

	/**
	 * SMTP Host.
	 * 
	 * Only used if {@link $Mailer} is set to "smtp"
	 * @var 	string
	 * @access	private
	 */
	var $Host		= "";

	/**
	 * Does your SMTP host require SMTPAuth authentication?
	 * @var 	boolean
	 * @access	private
	 */
	var $SMTPAuth	= FALSE;

	/**
	 * Username for authentication with your SMTP host.
	 * 
	 * Only used if {@link $Mailer} is "smtp" and {@link $SMTPAuth} is TRUE
	 * @var 	string
	 * @access	private
	 */
	var $Username	= "";

	/**
	 * Password for SMTPAuth.
	 * 
	 * Only used if {@link $Mailer} is "smtp" and {@link $SMTPAuth} is TRUE
	 * @var 	string
	 * @access	private
	 */
	var $Password	= "";
	
	/**
	 * Constuctor
	 * 
	 * @access public
	 * @return void 
	 * 
	 * @global	$zariliaConfig
	 */
	function ZariliaMultiMailer(){
		global $zariliaConfig;
	
		$config_handler =& zarilia_gethandler('config');
		$zariliaMailerConfig =& $config_handler->getConfigsByCat(ZAR_CONF_MAILER);
		$this->From = $zariliaMailerConfig['from'];
		if ($this->From == '') {
		    $this->From = $zariliaConfig['adminmail'];
		}
		
		if ($zariliaMailerConfig["mailmethod"] == "smtpauth") {
		    $this->Mailer = "smtp";
			$this->SMTPAuth = TRUE;
			$this->Host = implode(';',$zariliaMailerConfig['smtphost']);
			$this->Username = $zariliaMailerConfig['smtpuser'];
			$this->Password = $zariliaMailerConfig['smtppass'];
		} else {
			$this->Mailer = $zariliaMailerConfig['mailmethod'];
			$this->SMTPAuth = FALSE;
			$this->Sendmail = $zariliaMailerConfig['sendmailpath'];
			$this->Host = implode(';',$zariliaMailerConfig['smtphost']);
		}
	}

	/**
     * Formats an address correctly. This overrides the default addr_format method which does not seem to encode $FromName correctly
     * @access private
     * @return string
     */
    function addr_format($addr) {
        if(empty($addr[1]))
            $formatted = $addr[0];
        else
            $formatted = sprintf('%s <%s>', '=?'.$this->CharSet.'?B?'.base64_encode($addr[1]).'?=', $addr[0]);

        return $formatted;
    }
}
?>