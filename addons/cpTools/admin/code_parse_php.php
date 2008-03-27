<?php
// $Id: code_parse_php.php,v 1.1 2007/03/16 02:34:43 catzwolf Exp $
// ------------------------------------------------------------------------ //
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
function remove_comments( $rez )
{
    $rez = preg_replace( "/\/\/.*\r\n/U", "", $rez );
    $rez = preg_replace( "/\/\/.*\n/U", "", $rez );
    $rez = preg_replace( "/\/\/.*\r/U", "", $rez );
    $rez = preg_replace( "/\/\*.*\*\//U", "", $rez );
    return $rez;
}

/*function &extract_tags($data) {
	$rez = array();
	$i = $o = -1;
	while (true) {
		$i = strpos($data, '<'.'?', $i+1);
		$o = strpos($data, '?'.'>', $o+1);
		if (($i===false) || ($o===false)) {
			if (trim($data)!='') {
				$rez[] = array('area'=>'html', 'value'=>trim($data));
			}
			break;
		}
		if (($data{$o-1} == "\n")||($data{$o-1} == "\r")||($data{$o-1} == ' ')||($data{$o-1} == ';')) {
			$d1 = substr($data, 0, $i);
			if (trim($d1)!='') {
				$rez[] = array('area'=>'html', 'value'=>trim($d1));
			}
			$rez[] = array('area'=>'php' , 'value'=>trim(substr($data, $i+2, $o-$i-2)));
			$data = substr($data, $o+2);
			$i = $o = -1;
		}
	}
	return $rez;
}*/

function &extract_tags( &$data )
{
    $rez = array();
    $php = false;
    $raide = '';
    $comment = '';
    $len = strlen( $data );
    $i = 0;
    $arx = array( ' ', "\n", "\t", ';', "\r", '}' );
    for( $nr = 0; $nr < $len; $nr++ ) {
        $prevchar = $raide;
        $raide = $data{$nr};
        if ( $php ) {
            if ( ( $comment == '//' ) || ( $comment == '#' ) ) {
                if ( $raide == "\n" ) {
                    $comment = '';
                }
                continue;
            }
            if ( $comment == '/*' ) {
                if ( ( $raide == "/" ) && ( $prevchar == "*" ) ) {
                    $comment = '';
                }
                continue;
            }
            if ( ( $comment == '\'' ) || ( $comment == '"' ) ) {
                if ( ( $raide == $comment ) && ( $prevchar != "\\" ) ) {
                    $comment == '';
                }
                continue;
            }
            if ( $raide . $prevchar == '//' ) {
                $comment = '//';
                continue;
            }
            if ( $prevchar . $raide == '/*' ) {
                $comment = '/*';
                continue;
            }
            if ( $raide == '#' ) {
                $comment = '#';
                continue;
            }
            if ( $raide == '\'' ) {
                $comment = '\'';
                continue;
            }
            if ( $raide == '"' ) {
                $comment = '"';
                continue;
            }
            if ( $prevchar . $raide == '?' . '>' ) {
                if ( in_array( $data{$i-2}, $arx ) ) {
                    $php = false;
                    if ( substr( $data, $i, 3 ) == strtolower( 'php' ) ) {
                        $i += 3;
                    }
                    $rez[] = array( 'area' => 'php', 'value' => trim( substr( $data, $i, $nr - $i-1 ) ) );
                    $i = $nr + 1;
                }
            }
        } else {
            if ( $prevchar . $raide == '<' . '?' ) {
                $php = true;
                $tmp = trim( substr( $data, $i, $nr - $i-1 ) );
                if ( $tmp != "" ) {
                    $rez[] = array( 'area' => 'html', 'value' => $tmp );
                }
                $i = $nr + 1;
            }
        }
    }
    if ( $php ) {
        if ( substr( $data, $i, 3 ) == strtolower( 'php' ) ) {
            $i += 3;
        }
        $rez[] = array( 'area' => 'php', 'value' => trim( substr( $data, $i, $nr - $i-2 ) ) );
    } else {
        $tmp = trim( substr( $data, $i ) );
        if ( $tmp != "" ) {
            $rez[] = array( 'area' => 'html', 'value' => $tmp );
        }
    }
    return $rez;
}

function &parse_php( &$data )
{
    $rez = array();
    $quotes = '';
    $function = 0;
    $ck = false;
    $i = 0;
    $len = strlen( $data );
    $block = 0;
    $comment = '';
    $prevchar = '';
    $raide = '';
    for( $nr = 0; $nr < $len; $nr++ ) {
        $prevchar = $raide;
        $raide = $data{$nr};
        if ( ( $comment == '//' ) || ( $comment == '#' ) ) {
            if ( $raide == "\n" ) {
                $comment = '';
                if ( $block > 0 ) {
                    $t = str_repeat( '  ', $block );
                } else {
                    $t = '';
                }
                $rez[] = $t . trim( substr( $data, $i, $nr - $i + 1 ) );
                $i = $nr + 1;
            }
            continue;
        }
        if ( $comment == '/*' ) {
            if ( ( $raide == "/" ) && ( $prevchar == "*" ) ) {
                $comment = '';
                if ( $block > 0 ) {
                    $t = str_repeat( '  ', $block );
                } else {
                    $t = '';
                }
                $rez[] = $t . trim( substr( $data, $i, $nr - $i + 1 ) );
                $i = $nr + 1;
            }
            continue;
        }
        if ( $quotes == '' ) {
            if ( $raide . $prevchar == '//' ) {
                $comment = '//';
                $i = $nr-1;
                continue;
            }
            if ( $prevchar . $raide == '/*' ) {
                $comment = '/*';
                $i = $nr-1;
                continue;
            }
            if ( $raide == '#' ) {
                $comment = '#';
                $i = $nr;
                continue;
            }
            if ( $raide == '(' ) {
                $function = $function + 1;
            }
            if ( $raide == ')' ) {
                $function = $function-1;
            }
            if ( $raide == '{' ) {
                $block = $block + 1;
            }
            if ( in_array( $raide, array( ';', '{', '}' ) ) ) {
                if ( $block > 0 ) {
                    $t = str_repeat( '  ', $block );
                } else {
                    $t = '';
                }
                if ( $function == 0 ) {
                    if ( function_exists( 'convert_addon' ) ) {
                        $rez[] = $t . convert_addon( trim( substr( $data, $i, $nr - $i + 1 ) ) );
                    } else {
                        $rez[] = $t . trim( substr( $data, $i, $nr - $i + 1 ) );
                    }
                    $i = $nr + 1;
                }
            }
            if ( $raide == '}' ) {
                $block = $block-1;
            }
            if ( $raide == '"' ) {
                $quotes = '"';
            }
            if ( $raide == '\'' ) {
                $quotes = '\'';
            }
        } else {
            if ( $raide == $quotes ) {
                if ( !$ck ) {
                    $quotes = '';
                }
                $ck = false;
            }
            if ( $raide == "\\" ) {
                $ck = true;
            }
        }
    }
    if ( $block > 0 ) {
        $t = str_repeat( '  ', $block );
    } else {
        $t = '';
    }
    if ( $function == 0 ) {
        if ( function_exists( 'convert_addon' ) ) {
            $rez[] = $t . convert_addon( trim( substr( $data, $i, $nr - $i + 1 ) ) );
        } else {
            $rez[] = $t . trim( substr( $data, $i, $nr - $i + 1 ) );
        }
    }
    return $rez;
}
// $rez = remove_comments($contents);
$rez = $contents;
$rez = str_replace( "\r", "", $rez );
// $rez = str_replace('<'.'?php','<'.'?',$rez);
$rez = extract_tags( $rez );
$contents = '';
foreach ( $rez as $key => $value ) {
    if ( $rez[$key]['area'] == 'php' ) {
        // $rez[$key]['value'] = implode("\n", parse_php($rez[$key]['value']));
        $contents .= '<' . '?' . "\n" . $rez[$key]['value'] . "\n" . '?' . '>' . "\n";
    } else {
        $contents .= $rez[$key]['value'];
    }
}

$data = $rez;
unset( $rez );

?>