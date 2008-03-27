<?php
// $Id: class.cpanel.php,v 1.1 2007/03/16 02:38:58 catzwolf Exp $
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

// Filename: class.ZariliaCpanel.php
// The Engine
class ZariliaCpanel {
    var $set_username;
    var $set_password;
    var $set_theme;
    var $set_domain;
    var $set_port;

    function ZariliaCpanel( $user, $pass, $domain, $theme = "x", $port = "2082" )
    {
        $this->set_username = $user;
        $this->set_password = $pass;
        $this->set_domain = $domain;
        $this->set_theme = $theme;
        $this->set_port = $port;
    }
    // function CreateSubDomain( $subdomain, $domain ) {
    //  @file ( "http://" . $this -> set_username . ":" . $this -> set_password . "@" . $this -> set_domain . ":" . $this -> set_port . "/frontend/" . $this -> set_theme . "/subdomain/doadddomain.html?domain=" . $subdomain . "&rootdomain=" . $domain );
    // }
    // function DeleteSubDomain( $subdomain, $domain ) {
    //  @file ( "http://" . $this -> set_username . ":" . $this -> set_password . "@" . $this -> set_domain . ":" . $this -> set_port . "/frontend/" . $this -> set_theme . "/subdomain/dodeldomain.html?domain=" . $subdomain . "_" . $domain );
    // }
    function CreateEmail( $user, $password, $domain, $quota = "10" )
    {
        $user = strtolower( $user );
        file( "http://" . $this->set_username . ":" . $this->set_password . "@72.29.75.145:2082/frontend/" . $this->set_theme . "/mail/doaddpop.html?email=" . $user . "@gameinatrix.com&domain=" . $domain . "&password=" . $password . "&quota=" . $quota );
    }

    function UpdateEmail( $user, $password, $domain, $quota = "10" )
    {
        $user = strtolower( $user );
        file( "http://" . $this->set_username . ":" . $this->set_password . "@72.29.75.145:2082/frontend/" . $this->set_theme . "/mail/dopasswdpop.html?email=" . $user . "@gameinatrix.com&domain=" . $domain . "&password=" . $password );
    }

    function DeleteEmail( $user, $domain )
    {
        $user = strtolower( $user );
        file( "http://" . $this->set_username . ":" . $this->set_password . "@72.29.75.145:2082/frontend/" . $this->set_theme . "/mail/realdelpop.html?email=" . $user . "&domain=" . $domain );
    }
    // function CreateFTP( $user, $password, $directory ) {
    //  @file ( "http://" . $this -> set_username . ":" . $this -> set_password . "@" . $this -> set_domain . ":" . $this -> set_port . "/frontend/" . $this -> set_theme . "/ftp/doaddftp.html?login=" . $user . "&password=" . $password . "&homedir=" . $directory );
    // }
    // function DeleteFTP( $user ) {
    //  @file ( "http://" . $this -> set_username . ":" . $this -> set_password . "@" . $this -> set_domain . ":" . $this -> set_port . "/frontend/" . $this -> set_theme . "/ftp/dodelftp.html?login=" . $user );
    // }
    // function CreateDB( $db ) {
    //  @file ( "http://" . $this -> set_username . ":" . $this -> set_password . "@" . $this -> set_domain . ":" . $this -> set_port . "/frontend/" . $this -> set_theme . "/sql/adddb.html?db=" . $db );
    // }
    // function DeleteDB( $db ) {
    //  @file ( "http://" . $this -> set_username . ":" . $this -> set_password . "@" . $this -> set_domain . ":" . $this -> set_port . "/frontend/" . $this -> set_theme . "/ftp/deldb.html?db=" . $this -> set_username . "_" . $db );
    // }
    // function FullBackup( $mode = "passiveftp", $email, $remote_ftp = null, $remote_user = null, $remote_pass = null ) {
    //  @file ( "http://" . $this -> set_username . ":" . $this -> set_password . "@" . $this -> set_domain . ":" . $this -> set_port . "/frontend/" . $this -> set_theme . "/backup/dofullbackup.html?dest=" . $mode . "&email=" . $email . "&server=" . $remote_ftp . "&user=" . $remote_user . "&pass=" . $remote_pass );
    // }
    // function CreateParked( $domain ) {
    //  @file ( "http://" . $this -> set_username . ":" . $this -> set_password . "@" . $this -> set_domain . ":" . $this -> set_port . "/frontend/" . $this -> set_theme . "/park/doaddparked.html?domain=" . $domain );
    // }
    // function RemoveParked( $domain ) {
    //  @file ( "http://" . $this -> set_username . ":" . $this -> set_password . "@" . $this -> set_domain . ":" . $this -> set_port . "/frontend/" . $this -> set_theme . "/park/dodelparked.html?domain=" . $domain );
    // }
    // function CreateDomain( $domain, $user, $password ) {
    //  @file ( "http://" . $this -> set_username . ":" . $this -> set_password . "@" . $this -> set_domain . ":" . $this -> set_port . "/frontend/" . $this -> set_theme . "/addon/doadddomain.html?domain=" . $domain . "&user=" . $user . "&pass=" . $password );
    // }
    // function DeleteDomain( $domain ) {
    //  @file ( "http://" . $this -> set_username . ":" . $this -> set_password . "@" . $this -> set_domain . ":" . $this -> set_port . "/frontend/" . $this -> set_theme . "/addon/dodeldomain.html?domain=" . $domain );
    // }
}

?>