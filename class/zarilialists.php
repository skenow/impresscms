<?php
// $Id: zarilialists.php,v 1.2 2007/04/12 14:15:23 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
if ( !defined( "ZAR_LISTS_INCLUDED" ) ) {
    define( "ZAR_LISTS_INCLUDED", 1 );
    class ZariliaLists {
        function getTimeZoneList() {
            include_once ZAR_ROOT_PATH . '/language/' . $GLOBALS['zariliaConfig']['language'] . '/timezone.php';
            $time_zone_list = array ( "-12" => _TZ_GMTM12,
                "-11" => _TZ_GMTM11,
                "-10" => _TZ_GMTM10,
                "-9" => _TZ_GMTM9,
                "-8" => _TZ_GMTM8,
                "-7" => _TZ_GMTM7,
                "-6" => _TZ_GMTM6,
                "-5" => _TZ_GMTM5,
                "-4" => _TZ_GMTM4,
                "-3.5" => _TZ_GMTM35,
                "-3" => _TZ_GMTM3,
                "-2" => _TZ_GMTM2,
                "-1" => _TZ_GMTM1,
                "0" => _TZ_GMT0,
                "1" => _TZ_GMTP1,
                "2" => _TZ_GMTP2,
                "3" => _TZ_GMTP3,
                "3.5" => _TZ_GMTP35,
                "4" => _TZ_GMTP4,
                "4.5" => _TZ_GMTP45,
                "5" => _TZ_GMTP5,
                "5.5" => _TZ_GMTP55,
                "6" => _TZ_GMTP6,
                "7" => _TZ_GMTP7,
                "8" => _TZ_GMTP8,
                "9" => _TZ_GMTP9,
                "9.5" => _TZ_GMTP95,
                "10" => _TZ_GMTP10,
                "11" => _TZ_GMTP11,
                "12" => _TZ_GMTP12
                );
            return $time_zone_list;
        }

        /*
		 * gets list of themes folder from themes directory
		 */
        function getThemesList() {
            return ZariliaLists::getDirListAsArray( ZAR_THEME_PATH . '/' );
        }

        /*
		 * gets a list of addon folders from the addons directory
		 */
        function getAddonsList() {
            return ZariliaLists::getDirListAsArray( ZAR_ROOT_PATH . '/addons/' );
        }

        /*
		 * gets a list of addon folders from the addons directory
		 */
        function getMediaCategoryList() {
            $imgcat_handler = &zarilia_gethandler( 'imagecategory' );
            $img_cat_obj = $imgcat_handler->getCategories();
            for ( $i = 0; $i < count( $img_cat_obj ); $i++ ) {
                $cat_array[$img_cat_obj[$i]->getVar( 'imgcat_id' )] = $img_cat_obj[$i]->getVar( 'imgcat_name' );
            }
            unset( $img_cat_obj );
            return $cat_array;
        }

        /*
		 * gets list of name of directories inside a directory
		 */
        function getDirListAsArray( $dirname ) {
            $dirlist = array();
            if ( is_dir( $dirname ) && $handle = opendir( $dirname ) ) {
                while ( false !== ( $file = readdir( $handle ) ) ) {
                    if ( !preg_match( "/^[\.]{1,2}$/", $file ) ) {
                        if ( strtolower( $file ) != 'cvs' && is_dir( $dirname . $file ) ) {
                            $dirlist[$file] = $file;
                        }
                    }
                }
                closedir( $handle );
                asort( $dirlist );
                reset( $dirlist );
            }
            return $dirlist;
        }

        /*
		 *  gets list of all files in a directory
		 */
        function getFileListAsArray( $dirname, $prefix = "" ) {
            $filelist = array();
            if ( substr( $dirname, -1 ) == '/' ) {
                $dirname = substr( $dirname, 0, -1 );
            }
            if ( is_dir( $dirname ) && $handle = opendir( $dirname ) ) {
                while ( false !== ( $file = readdir( $handle ) ) ) {
                    if ( !preg_match( "/^[\.]{1,2}$/", $file ) && is_file( $dirname . '/' . $file ) ) {
                        $file = $prefix . $file;
                        $filelist[$file] = $file;
                    }
                }
                closedir( $handle );
                asort( $filelist );
                reset( $filelist );
            }
            return $filelist;
        }

        /*
		 *  gets list of image file names in a directory
		 */
        function getImgListAsArray( $dirname, $prefix = "" ) {
            $filelist = array();
            if ( $handle = opendir( $dirname ) ) {
                while ( false !== ( $file = readdir( $handle ) ) ) {
                    if ( !preg_match( "/^[\.]{1,2}$/", $file ) && preg_match( "/(\.gif|\.jpg|\.png|\.bmp)$/i", $file ) ) {
                        $file = $prefix . $file;
                        $filelist[$file] = $file;
                    }
                }
                closedir( $handle );
                asort( $filelist );
                reset( $filelist );
            }
            return $filelist;
        }

        /*
		 *  gets list of html file names in a certain directory
		*/
        function getHtmlListAsArray( $dirname, $prefix = "" ) {
            $filelist = array();
            if ( $handle = opendir( $dirname ) ) {
                while ( false !== ( $file = readdir( $handle ) ) ) {
                    if ( ( !preg_match( "/^[\.]{1,2}$/", $file ) && preg_match( "/(\.htm|\.html|\.xhtml|\.dhtml)$/i", $file ) && !is_dir( $file ) ) ) {
                        if ( strtolower( $file ) != 'cvs' && !is_dir( $file ) ) {
                            $file = $prefix . $file;
                            $filelist[$file] = $prefix . $file;
                        }
                    }
                }
                closedir( $handle );
                asort( $filelist );
                reset( $filelist );
            }
            return $filelist;
        }

        /*
		 *  gets list of avatar file names in a certain directory
		 *  if directory is not specified, default directory will be searched
		 */
        function getAvatarsList( $avatar_dir = "" ) {
            $avatars = array();
            if ( $avatar_dir != "" ) {
                $avatars = ZariliaLists::getImgListAsArray( ZAR_ROOT_PATH . "/images/avatar/" . $avatar_dir . "/", $avatar_dir . "/" );
            } else {
                $avatars = ZariliaLists::getImgListAsArray( ZAR_ROOT_PATH . "/images/avatar/" );
            }
            return $avatars;
        }

        /*
		 *  gets list of all avatar image files inside default avatars directory
		 */
        function getAllAvatarsList() {
            $avatars = array();
            $dirlist = array();
            $dirlist = ZariliaLists::getDirListAsArray( ZAR_ROOT_PATH . '/images/avatar/' );
            if ( count( $dirlist ) > 0 ) {
                foreach ( $dirlist as $dir ) {
                    $avatars[$dir] = ZariliaLists::getImgListAsArray( ZAR_ROOT_PATH . "/images/avatar/" . $dir . "/", $dir . "/" );
                }
            } else {
                return false;
            }
            return $avatars;
        }

        /*
		*  gets list of subject icon image file names in a certain directory
		*  if directory is not specified, default directory will be searched
		*/
        function getSubjectsList( $sub_dir = "" ) {
            $subjects = array();
            if ( $sub_dir != "" ) {
                $subjects = ZariliaLists::getImgListAsArray( ZAR_ROOT_PATH . "/images/subject/" . $sub_dir, $sub_dir . "/" );
            } else {
                $subjects = ZariliaLists::getImgListAsArray( ZAR_ROOT_PATH . "/images/subject/" );
            }
            return $subjects;
        }

        /*
		 * gets list of language folders inside default language directory
		 */
        function getLangList() {
            return ZariliaLists::getDirListAsArray( ZAR_ROOT_PATH . "/language/" );
        }

        function getCharset() {
            $unicode_list = array ( "ASMO-708" => "Arabic (ASMO 708)",
                "CP1026" => "IBM EBCDIC (Turkish Latin-5)",
                "CP870" => "IBM EBCDIC (Multilingual Latin-2)",
                "DOS-720" => "Arabic (DOS)",
                "DOS-862" => "Hebrew (DOS)",
                "EUC-CN" => "Chinese Simplified (EUC)",
                "IBM437" => "OEM United States",
                "Johab" => "Korean (Johab)",
                "Windows-1252" => "Western European (Windows)",
                "X-EBCDIC-Spain" => "IBM EBCDIC (Spain)",
                "big5" => "Chinese Traditional (Big5)",
                "cp866" => "Cyrillic (DOS)",
                "csISO2022JP" => "Japanese (JIS-Allow 1 byte Kana)",
                "ebcdic-cp-us" => "IBM EBCDIC (US-Canada)",
                "euc-jp" => "Japanese (EUC)",
                "euc-kr" => "Korean (EUC)",
                "gb2312" => "Chinese Simplified (GB2312)",
                "hz-gb-2312" => "Chinese Simplified (HZ)",
                "ibm737" => "Greek (DOS)",
                "ibm775" => "Baltic (DOS)",
                "ibm850" => "Western European (DOS)",
                "ibm852" => "Central European (DOS)",
                "ibm857" => "Turkish (DOS)",
                "ibm861" => "Icelandic (DOS)",
                "ibm869" => "Greek, Modern (DOS)",
                "iso-2022-jp" => "Japanese (JIS)",
                "iso-2022-jp" => "Japanese (JIS-Allow 1 byte Kana - SO/SI)",
                "iso-2022-kr" => "Korean (ISO)",
                "iso-8859-1" => "Western European (ISO)",
                "iso-8859-15" => "Latin 9 (ISO)",
                "iso-8859-2" => "Central European (ISO)",
                "iso-8859-3" => "Latin 3 (ISO)",
                "iso-8859-4" => "Baltic (ISO)",
                "iso-8859-5" => "Cyrillic (ISO)",
                "iso-8859-6" => "Arabic (ISO)",
                "iso-8859-7" => "Greek (ISO)",
                "iso-8859-8" => "Hebrew (ISO-Visual)",
                "iso-8859-8-i" => "Hebrew (ISO-Logical)",
                "iso-8859-9" => "Turkish (ISO)",
                "koi8-r" => "Cyrillic (KOI8-R)",
                "koi8-u" => "Cyrillic (KOI8-U)",
                "ks_c_5601-1987" => "Korean",
                "macintosh" => "Western European (Mac)",
                "shift_jis" => "Japanese (Shift-JIS)",
                "unicode" => "Unicode",
                "unicodeFFFE" => "Unicode (Big-Endian)",
                "us-ascii" => "US-ASCII",
                "utf-7" => "Unicode (UTF-7)",
                "utf-8" => "Unicode (UTF-8)",
                "windows-1250" => "Central European (Windows)",
                "windows-1251" => "Cyrillic (Windows)",
                "windows-1253" => "Greek (Windows)",
                "windows-1254" => "Turkish (Windows)",
                "windows-1255" => "Hebrew (Windows)",
                "windows-1256" => "Arabic (Windows)",
                "windows-1257" => "Baltic (Windows)",
                "windows-1258" => "Vietnamese (Windows)",
                "windows-874" => "Thai (Windows)",
                "x-Chinese-CNS" => "Chinese Traditional (CNS)",
                "x-Chinese-Eten" => "Chinese Traditional (Eten)",
                "x-EBCDIC-Arabic" => "IBM EBCDIC (Arabic)",
                "x-EBCDIC-CyrillicRussian" => "IBM EBCDIC (Cyrillic Russian)",
                "x-EBCDIC-CyrillicSerbianBulgarian" => "IBM EBCDIC (Cyrillic Serbian-Bulgarian)",
                "x-EBCDIC-DenmarkNorway" => "IBM EBCDIC (Denmark-Norway)",
                "x-EBCDIC-FinlandSweden" => "IBM EBCDIC (Finland-Sweden)",
                "x-EBCDIC-Germany" => "IBM EBCDIC (Germany)",
                "x-EBCDIC-Greek" => "IBM EBCDIC (Greek)",
                "x-EBCDIC-GreekModern" => "IBM EBCDIC (Greek Modern)",
                "x-EBCDIC-Hebrew" => "IBM EBCDIC (Hebrew)",
                "x-EBCDIC-Icelandic" => "IBM EBCDIC (Icelandic)",
                "x-EBCDIC-Italy" => "IBM EBCDIC (Italy)",
                "x-EBCDIC-JapaneseAndJapaneseLatin" => "IBM EBCDIC (Japanese and Japanese-Latin)",
                "x-EBCDIC-JapaneseAndKana" => "IBM EBCDIC (Japanese and Japanese Katakana)",
                "x-EBCDIC-JapaneseAndUSCanada" => "IBM EBCDIC (Japanese and US-Canada)",
                "x-EBCDIC-JapaneseKatakana" => "IBM EBCDIC (Japanese katakana)",
                "x-EBCDIC-KoreanAndKoreanExtended" => "IBM EBCDIC (Korean and Korean Extended)",
                "x-EBCDIC-KoreanExtended" => "IBM EBCDIC (Korean Extended)",
                "x-EBCDIC-SimplifiedChinese" => "IBM EBCDIC (Simplified Chinese)",
                "x-EBCDIC-Thai" => "IBM EBCDIC (Thai)",
                "x-EBCDIC-TraditionalChinese" => "IBM EBCDIC (Traditional Chinese)",
                "x-EBCDIC-Turkish" => "IBM EBCDIC (Turkish)",
                "x-EBCDIC-UK" => "IBM EBCDIC (UK)",
                "x-Europa" => "Europa",
                "x-IA5" => "Western European (IA5)",
                "x-IA5-German" => "German (IA5)",
                "x-IA5-Norwegian" => "Norwegian (IA5)",
                "x-IA5-Swedish" => "Swedish (IA5)",
                "x-ebcdic-cp-us-euro" => "IBM EBCDIC (US-Canada-Euro)",
                "x-ebcdic-denmarknorway-euro" => "IBM EBCDIC (Denmark-Norway-Euro)",
                "x-ebcdic-finlandsweden-euro" => "IBM EBCDIC (Finland-Sweden-Euro)",
                "x-ebcdic-france-euro" => "IBM EBCDIC (France-Euro)",
                "x-ebcdic-germany-euro" => "IBM EBCDIC (Germany-Euro)",
                "x-ebcdic-icelandic-euro" => "IBM EBCDIC (Icelandic-Euro)",
                "x-ebcdic-international-euro" => "IBM EBCDIC (International-Euro)",
                "x-ebcdic-italy-euro" => "IBM EBCDIC (Italy-Euro)",
                "x-ebcdic-spain-euro" => "IBM EBCDIC (Spain-Euro)",
                "x-ebcdic-uk-euro" => "IBM EBCDIC (UK-Euro)",
                "x-iscii-as" => "ISCII Assamese",
                "x-iscii-be" => "ISCII Bengali",
                "x-iscii-de" => "ISCII Devanagari",
                "x-iscii-gu" => "ISCII Gujarathi",
                "x-iscii-ka" => "ISCII Kannada",
                "x-iscii-ma" => "ISCII Malayalam",
                "x-iscii-or" => "ISCII Oriya",
                "x-iscii-pa" => "ISCII Panjabi",
                "x-iscii-ta" => "ISCII Tamil",
                "x-iscii-te" => "ISCII Telugu",
                "x-mac-arabic" => "Arabic (Mac)",
                "x-mac-ce" => "Central European (Mac)",
                "x-mac-chinesesimp" => "Chinese Simplified (Mac)",
                "x-mac-chinesetrad-950" => "Chinese Traditional (Mac)",
                "x-mac-cyrillic" => "Cyrillic (Mac)",
                "x-mac-greek" => "Greek (Mac)",
                "x-mac-hebrew" => "Hebrew (Mac)",
                "x-mac-icelandic" => "Icelandic (Mac)",
                "x-mac-japanese" => "Japanese (Mac)",
                "x-mac-korean" => "Korean (Mac)",
                "x-mac-turkish" => "Turkish (Mac)"
                );
            asort( $unicode_list );
            reset( $unicode_list );
            return $unicode_list;
        }

        function getCountryList() {
            $country_list = array ( "AD" => "Andorra",
                "AE" => "United Arab Emirates",
                "AF" => "Afghanistan",
                "AG" => "Antigua and Barbuda",
                "AI" => "Anguilla",
                "AL" => "Albania",
                "AM" => "Armenia",
                "AN" => "Netherlands Antilles",
                "AO" => "Angola",
                "AQ" => "Antarctica",
                "AR" => "Argentina",
                "AS" => "American Samoa",
                "AT" => "Austria",
                "AU" => "Australia",
                "AW" => "Aruba",
                "AZ" => "Azerbaijan",
                "BA" => "Bosnia and Herzegovina",
                "BB" => "Barbados",
                "BD" => "Bangladesh",
                "BE" => "Belgium",
                "BF" => "Burkina Faso",
                "BG" => "Bulgaria",
                "BH" => "Bahrain",
                "BI" => "Burundi",
                "BJ" => "Benin",
                "BM" => "Bermuda",
                "BN" => "Brunei Darussalam",
                "BO" => "Bolivia",
                "BR" => "Brazil",
                "BS" => "Bahamas",
                "BT" => "Bhutan",
                "BV" => "Bouvet Island",
                "BW" => "Botswana",
                "BY" => "Belarus",
                "BZ" => "Belize",
                "CA" => "Canada",
                "CC" => "Cocos (Keeling) Islands",
                "CF" => "Central African Republic",
                "CG" => "Congo",
                "CH" => "Switzerland",
                "CI" => "Cote D'Ivoire (Ivory Coast)",
                "CK" => "Cook Islands",
                "CL" => "Chile",
                "CM" => "Cameroon",
                "CN" => "China",
                "CO" => "Colombia",
                "CR" => "Costa Rica",
                "CS" => "Czechoslovakia (former)",
                "CU" => "Cuba",
                "CV" => "Cape Verde",
                "CX" => "Christmas Island",
                "CY" => "Cyprus",
                "CZ" => "Czech Republic",
                "DE" => "Germany",
                "DJ" => "Djibouti",
                "DK" => "Denmark",
                "DM" => "Dominica",
                "DO" => "Dominican Republic",
                "DZ" => "Algeria",
                "EC" => "Ecuador",
                "EE" => "Estonia",
                "EG" => "Egypt",
                "EH" => "Western Sahara",
                "ER" => "Eritrea",
                "ES" => "Spain",
                "ET" => "Ethiopia",
                "FI" => "Finland",
                "FJ" => "Fiji",
                "FK" => "Falkland Islands (Malvinas)",
                "FM" => "Micronesia",
                "FO" => "Faroe Islands",
                "FR" => "France",
                "FX" => "France, Metropolitan",
                "GA" => "Gabon",
                "GB" => "Great Britain (UK)",
                "GD" => "Grenada",
                "GE" => "Georgia",
                "GF" => "French Guiana",
                "GH" => "Ghana",
                "GI" => "Gibraltar",
                "GL" => "Greenland",
                "GM" => "Gambia",
                "GN" => "Guinea",
                "GP" => "Guadeloupe",
                "GQ" => "Equatorial Guinea",
                "GR" => "Greece",
                "GS" => "S. Georgia and S. Sandwich Isls.",
                "GT" => "Guatemala",
                "GU" => "Guam",
                "GW" => "Guinea-Bissau",
                "GY" => "Guyana",
                "HK" => "Hong Kong",
                "HM" => "Heard and McDonald Islands",
                "HN" => "Honduras",
                "HR" => "Croatia (Hrvatska)",
                "HT" => "Haiti",
                "HU" => "Hungary",
                "ID" => "Indonesia",
                "IE" => "Ireland",
                "IL" => "Israel",
                "IN" => "India",
                "IO" => "British Indian Ocean Territory",
                "IQ" => "Iraq",
                "IR" => "Iran",
                "IS" => "Iceland",
                "IT" => "Italy",
                "JM" => "Jamaica",
                "JO" => "Jordan",
                "JP" => "Japan",
                "KE" => "Kenya",
                "KG" => "Kyrgyzstan",
                "KH" => "Cambodia",
                "KI" => "Kiribati",
                "KM" => "Comoros",
                "KN" => "Saint Kitts and Nevis",
                "KP" => "Korea (North)",
                "KR" => "Korea (South)",
                "KW" => "Kuwait",
                "KY" => "Cayman Islands",
                "KZ" => "Kazakhstan",
                "LA" => "Laos",
                "LB" => "Lebanon",
                "LC" => "Saint Lucia",
                "LI" => "Liechtenstein",
                "LK" => "Sri Lanka",
                "LR" => "Liberia",
                "LS" => "Lesotho",
                "LT" => "Lithuania",
                "LU" => "Luxembourg",
                "LV" => "Latvia",
                "LY" => "Libya",
                "MA" => "Morocco",
                "MC" => "Monaco",
                "MD" => "Moldova",
                "MG" => "Madagascar",
                "MH" => "Marshall Islands",
                "MK" => "Macedonia",
                "ML" => "Mali",
                "MM" => "Myanmar",
                "MN" => "Mongolia",
                "MO" => "Macau",
                "MP" => "Northern Mariana Islands",
                "MQ" => "Martinique",
                "MR" => "Mauritania",
                "MS" => "Montserrat",
                "MT" => "Malta",
                "MU" => "Mauritius",
                "MV" => "Maldives",
                "MW" => "Malawi",
                "MX" => "Mexico",
                "MY" => "Malaysia",
                "MZ" => "Mozambique",
                "NA" => "Namibia",
                "NC" => "New Caledonia",
                "NE" => "Niger",
                "NF" => "Norfolk Island",
                "NG" => "Nigeria",
                "NI" => "Nicaragua",
                "NL" => "Netherlands",
                "NO" => "Norway",
                "NP" => "Nepal",
                "NR" => "Nauru",
                "NT" => "Neutral Zone",
                "NU" => "Niue",
                "NZ" => "New Zealand (Aotearoa)",
                "OM" => "Oman",
                "PA" => "Panama",
                "PE" => "Peru",
                "PF" => "French Polynesia",
                "PG" => "Papua New Guinea",
                "PH" => "Philippines",
                "PK" => "Pakistan",
                "PL" => "Poland",
                "PM" => "St. Pierre and Miquelon",
                "PN" => "Pitcairn",
                "PR" => "Puerto Rico",
                "PT" => "Portugal",
                "PW" => "Palau",
                "PY" => "Paraguay",
                "QA" => "Qatar",
                "RE" => "Reunion",
                "RO" => "Romania",
                "RU" => "Russian Federation",
                "RW" => "Rwanda",
                "SA" => "Saudi Arabia",
                "Sb" => "Solomon Islands",
                "SC" => "Seychelles",
                "SD" => "Sudan",
                "SE" => "Sweden",
                "SG" => "Singapore",
                "SH" => "St. Helena",
                "SI" => "Slovenia",
                "SJ" => "Svalbard and Jan Mayen Islands",
                "SK" => "Slovak Republic",
                "SL" => "Sierra Leone",
                "SM" => "San Marino",
                "SN" => "Senegal",
                "SO" => "Somalia",
                "SR" => "Suriname",
                "ST" => "Sao Tome and Principe",
                "SU" => "USSR (former)",
                "SV" => "El Salvador",
                "SY" => "Syria",
                "SZ" => "Swaziland",
                "TC" => "Turks and Caicos Islands",
                "TD" => "Chad",
                "TF" => "French Southern Territories",
                "TG" => "Togo",
                "TH" => "Thailand",
                "TJ" => "Tajikistan",
                "TK" => "Tokelau",
                "TM" => "Turkmenistan",
                "TN" => "Tunisia",
                "TO" => "Tonga",
                "TP" => "East Timor",
                "TR" => "Turkey",
                "TT" => "Trinidad and Tobago",
                "TV" => "Tuvalu",
                "TW" => "Taiwan",
                "TZ" => "Tanzania",
                "UA" => "Ukraine",
                "UG" => "Uganda",
                "UK" => "United Kingdom",
                "UM" => "US Minor Outlying Islands",
                "US" => "United States",
                "UY" => "Uruguay",
                "UZ" => "Uzbekistan",
                "VA" => "Vatican City State (Holy See)",
                "VC" => "Saint Vincent and the Grenadines",
                "VE" => "Venezuela",
                "VG" => "Virgin Islands (British)",
                "VI" => "Virgin Islands (U.S.)",
                "VN" => "Viet Nam",
                "VU" => "Vanuatu",
                "WF" => "Wallis and Futuna Islands",
                "WS" => "Samoa",
                "YE" => "Yemen",
                "YT" => "Mayotte",
                "YU" => "Yugoslavia",
                "ZA" => "South Africa",
                "ZM" => "Zambia",
                "ZR" => "Zaire",
                "ZW" => "Zimbabwe"
                );
            asort( $country_list );
            reset( $country_list );
            return $country_list;
        }

        function getHtmlList() {
            $html_list = array ( "a" => "&lt;a&gt;",
                "abbr" => "&lt;abbr&gt;",
                "acronym" => "&lt;acronym&gt;",
                "address" => "&lt;address&gt;",
                "b" => "&lt;b&gt;",
                "bdo" => "&lt;bdo&gt;",
                "big" => "&lt;big&gt;",
                "blockquote" => "&lt;blockquote&gt;",
                "caption" => "&lt;caption&gt;",
                "cite" => "&lt;cite&gt;",
                "code" => "&lt;code&gt;",
                "col" => "&lt;col&gt;",
                "colgroup" => "&lt;colgroup&gt;",
                "dd" => "&lt;dd&gt;",
                "del" => "&lt;del&gt;",
                "dfn" => "&lt;dfn&gt;",
                "div" => "&lt;div&gt;",
                "dl" => "&lt;dl&gt;",
                "dt" => "&lt;dt&gt;",
                "em" => "&lt;em&gt;",
                "font" => "&lt;font&gt;",
                "h1" => "&lt;h1&gt;",
                "h2" => "&lt;h2&gt;",
                "h3" => "&lt;h3&gt;",
                "h4" => "&lt;h4&gt;",
                "h5" => "&lt;h5&gt;",
                "h6" => "&lt;h6&gt;",
                "hr" => "&lt;hr&gt;",
                "i" => "&lt;i&gt;",
                "img" => "&lt;img&gt;",
                "ins" => "&lt;ins&gt;",
                "kbd" => "&lt;kbd&gt;",
                "li" => "&lt;li&gt;",
                "map" => "&lt;map&gt;",
                "object" => "&lt;object&gt;",
                "ol" => "&lt;ol&gt;",
                "samp" => "&lt;samp&gt;",
                "small" => "&lt;small&gt;",
                "strong" => "&lt;strong&gt;",
                "sub" => "&lt;sub&gt;",
                "sup" => "&lt;sup&gt;",
                "table" => "&lt;table&gt;",
                "tbody" => "&lt;tbody&gt;",
                "td" => "&lt;td&gt;",
                "tfoot" => "&lt;tfoot&gt;",
                "th" => "&lt;th&gt;",
                "thead" => "&lt;thead&gt;",
                "tr" => "&lt;tr&gt;",
                "tt" => "&lt;tt&gt;",
                "ul" => "&lt;ul&gt;",
                "var" => "&lt;var&gt;"
                );
            asort( $html_list );
            reset( $html_list );
            return $html_list;
        }

        function usermedia( $level = null ) {
            $ret = array( 0 => _US_MEDPREF_QT, 1 => _US_MEDPREF_WM );
            return $ret = ( $level != null ) ? $ret[$level] : $ret;;
        }

        function userlevel( $level = null ) {
            $ret = array( 0 => _US_LEVEL_SET_NAT, 1 => _US_LEVEL_SET_INT, 2 => _US_LEVEL_SET_BAS, 3 => _US_LEVEL_SET_JP );
            return ( $level != null ) ? $ret[$level] : $ret;;
        }

        function getUserRankList() {
            $db = &ZariliaDatabaseFactory::getDatabaseConnection();
            $sql = "SELECT rank_id, rank_title FROM " . $db->prefix( "ranks" ) . " WHERE rank_special = 1";
            $ret = array();
            $result = $db->Execute( $sql );
            while ( $myrow = $result->FetchRow() ) {
                $ret[$myrow['rank_id']] = htmlSpecialChars( $myrow['rank_title'], ENT_QUOTES );
            }
            return $ret;
        }

        function getErrorNumberList() {
            $error_list = array ( 1 => _ER_PAGE_1,
                101 => _ER_PAGE_101,
                102 => _ER_PAGE_102,
                103 => _ER_PAGE_103,
                104 => _ER_PAGE_104,
                106 => _ER_PAGE_106,
                107 => _ER_PAGE_107,
                301 => _ER_PAGE_301,
                302 => _ER_PAGE_302,
                303 => _ER_PAGE_303,
                304 => _ER_PAGE_304,
                403 => _ER_PAGE_403,
                404 => _ER_PAGE_404,
                1065 => _ER_PAGE_1065,
                E_USER_NOTICE => _E_USER_NOTICE,
                E_USER_WARNING => _E_USER_WARNING,
                E_USER_ERROR => _E_USER_ERROR,
                E_NOTICE => _E_NOTICE,
                E_WARNING => _E_WARNING );
            return $error_list;
        }
        // function getErrorNumberList()
        // {
        // $error_list = array ( 1 => _ER_PAGE_1,
        // 99 => _ER_PAGE_099,
        // 101 => _ER_PAGE_101,
        // 102 => _ER_PAGE_102,
        // 103 => _ER_PAGE_103,
        // 104 => _ER_PAGE_104,
        // 105 => _ER_PAGE_105,
        // 106 => _ER_PAGE_106,
        // 107 => _ER_PAGE_107,
        // 301 => _ER_PAGE_301,
        // 302 => _ER_PAGE_302,
        // 303 => _ER_PAGE_303,
        // 304 => _ER_PAGE_304,
        // 403 => _ER_PAGE_403,
        // 404 => _ER_PAGE_404,
        // 505 => _ER_PAGE_505,
        // 1054 => _ER_PAGE_1054,
        // 1065 => _ER_PAGE_1065,
        // E_USER_NOTICE => _E_USER_NOTICE,
        // E_USER_WARNING => _E_USER_WARNING,
        // E_USER_ERROR => _E_USER_ERROR,
        // E_NOTICE => _E_NOTICE,
        // E_WARNING => _E_WARNING );
        // return $error_list;
        // }
    }
}

?>
