<?php
class XmlAtomParser
{
	var $parser;

	var $isCaseFolding;
	var $targetEncoding;

	var $errors = array();

	var $_feed = array();
	var $_entries = array();
	var $_entry_num = 0;
	var $_parent = array();
	var $_parent_num = 0;
	var $_uris = array();


	function XmlAtomParser( $input )
	{
		$this->parser = xml_parser_create('UTF-8');
		xml_set_object($this->parser, $this);
		$this->input =& $input;
		$this->setCaseFolding( true ) ;
		$this->useUtfEncoding();

		$this->_parent[0] = '' ;

		xml_set_element_handler($this->parser, "atom_start_element", "atom_end_element");
		xml_set_character_data_handler($this->parser, "atom_character_data");
		xml_set_start_namespace_decl_handler($this->parser, "atom_ns_start");
		xml_set_end_namespace_decl_handler($this->parser, "atom_ns_end");
	}

	function &getChannelData()
	{
		// emulating from ATOM to RSS
		$error_level_stored = error_reporting() ;
		error_reporting( $error_level_stored & ~ E_NOTICE ) ;
		$channel = array(
				'title' => $this->_feed['title'] ,
				'link' => $this->_feed['link'] ,
				'lastbuilddate' => formatTimestamp( $this->_feed['modified'] ) ,
				'id' => $this->_feed['id'] ,
				'id' => $this->_feed['id'] ,
				'webmaster' => $this->_feed['author_name'] ,
				'generator' => $this->_feed['generator']
		) ;
		error_reporting( $error_level_stored ) ;
		return $channel ;
	}

	function &getImageData()
	{
		return array() ;
	}

	function &getItems()
	{
		// emulating from ATOM to RSS
		$error_level_stored = error_reporting() ;
		error_reporting( $error_level_stored & ~ E_NOTICE ) ;
		$items = array() ;
		foreach( $this->_entries as $entry ) {
			$items[] = array(
				'title' => $entry['title'] ,
				'link' => $entry['link'] ,
				'id' => $entry['id'] ,
				'pubdate' => ! empty( $entry['updated'] ) ? $entry['updated'] : $entry['modified'] ,
				'description' => $entry['content']
			) ;
		}
		error_reporting( $error_level_stored ) ;
		return $items;
	}

	/****************************************************************************
		* @param $isCaseFolding
		* @returns void
	****************************************************************************/
	function setCaseFolding($isCaseFolding)
	{
		assert(is_bool($isCaseFolding));

		$this->isCaseFolding = $isCaseFolding;
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, $this->isCaseFolding);
	}

	/****************************************************************************
		* @returns void
	****************************************************************************/
	function useIsoEncoding()
	{
		$this->targetEncoding = 'ISO-8859-1';
		xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, $this->targetEncoding);
	}

	/****************************************************************************
		* @returns void
	****************************************************************************/
	function useAsciiEncoding()
	{
		$this->targetEncoding = 'US-ASCII';
		xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, $this->targetEncoding);
	}

	/****************************************************************************
		* @returns void
	****************************************************************************/
	function useUtfEncoding()
	{
		$this->targetEncoding = 'UTF-8';
		xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, $this->targetEncoding);
	}


	/****************************************************************************
		* @returns void
	****************************************************************************/
	function parse()
	{
		if( ! xml_parse( $this->parser, $this->input ) ) {
			$this->setErrors($this->getXmlError());
			return false;
		}
		return true;
	}

	/****************************************************************************
		* @returns void
	****************************************************************************/
	function free()
	{
		xml_parser_free($this->parser);

		unset($this);
	}


    /****************************************************************************
        * @private
        * @returns string
    ****************************************************************************/
    function getXmlError()
    {
        return sprintf("XmlParse error: %s at line %d", xml_error_string(xml_get_error_code($this->parser)), xml_get_current_line_number($this->parser));
    }


	/**
	 * Sets error messages
	 *
	 * @param	$error	string	an error message
	 */
    function setErrors($error)
    {
        $this->errors[] = trim($error);
    }

	/**
	 * Gets all the error messages
	 *
	 * @param	$ashtml	bool	return as html?
	 * @return	mixed
	 */
    function &getErrors($ashtml = true)
    {
        if (!$ashtml) {
            return $this->errors;
        } else {
        	$ret = '';
        	if (count($this->errors) > 0) {
            	foreach ($this->errors as $error) {
            	    $ret .= $error."<br />\n";
            	}
        	}
        	return $ret;
        }
    }



	//---------------------------------------------------------
	// start element handler
	//---------------------------------------------------------
	function atom_start_element($parser, $name, $attrs)
	{
		$parent = $this->_parent[$this->_parent_num];
	
		$parent_num_prev = $this->_parent_num - 1;
		if ($parent_num_prev < 0)  $parent_num_prev = 0;
		$parent_prev = $this->_parent[$parent_num_prev];
	
		$name_ns = split(':',$name);
		$name_wk = array_pop($name_ns);
		$uri1 = implode($name_ns,":");
	
		$name_low = strtolower( $name_wk );
	
		$flag = 0;
		foreach($this->_uris as $uri2)
		{
			if ($uri1 == $uri2)
			{
				$flag = 1;
				break;
			}
		}
	
		// FEED
		if ( $name_wk == 'FEED' )
		{
			$this->_parent_num = 0;
			$this->_parent[0]  = $name_wk;
			return;
		}
	
		// CONTENT
		if (($parent_prev == 'ENTRY')&&($parent == 'CONTENT'))
		{
			$data = '';
	
			if (($name_wk == 'P')||($name_wk == 'BR'))
			{
				$data .= "<br />\n";
			}
			else if ($name_wk == 'A')
			{
				$href   = '';
				$target = '';
				if ( isset($attrs['HREF']) )	$href   = $attrs['HREF'];
				if ( isset($attrs['TARGET']) )  $target = "target=\"{$attrs['TARGET']}\" ";
	
				$data .= "<a href=\"$href\" $target >";
	
			}
			else if ($name_wk == 'IMG')
			{
				$src	= '';
				$width  = '';
				$height = '';
				$border = 0;
				if ( isset($attrs['SRC']) )	 $src	= $attrs['SRC'];
				if ( isset($attrs['BORDER']) )  $border = $attrs['BORDER'];
				if ( isset($attrs['WIDTH']) )   $width  = "width=\"{$attrs['WIDTH']}\" ";
				if ( isset($attrs['HEIGHT']) )  $height = "hight=\"{$attrs['HEIGHT']}\" ";
	
				$data .= "<img src=\"$src\" border=\"$border\" $width $height />";
			}
	
			$this->_entries[$this->_entry_num]['content'] .= $data;
			return;
		}
	
		// LINK
		if ( $name_wk == 'LINK' )
		{
			$rel  = '';
			$href = '';
			if ( isset($attrs['REL']) )   $rel  = $attrs['REL'];
			if ( isset($attrs['HREF']) )  $href = $attrs['HREF'];
	
			if ( strtolower($rel) != 'alternate' )  return;
	
			if ( $parent == 'FEED' ) 
			{
				$this->_feed[$name_low] = $href;
			}
			else if ( $parent == 'ENTRY' )
			{
				$this->_entries[$this->_entry_num][$name_low] = $href;
			}
		}
	
		// increment parent
		if ( $flag || empty($uri1) )
		{
			$this->_parent_num ++;
			$this->_parent[$this->_parent_num] = $name_wk;
		}
	
	}
	
	//---------------------------------------------------------
	// end element handler
	//---------------------------------------------------------
	function atom_end_element($parser, $name)
	{
		$parent = $this->_parent[$this->_parent_num];
	
		$parent_num_prev = $this->_parent_num - 1;
		if ($parent_num_prev < 0)  $parent_num_prev = 0;
		$parent_prev = $this->_parent[$parent_num_prev];
	
		$name_ns = split(':',$name);
		$name_wk = array_pop($name_ns);
		$uri1 = implode($name_ns,":");
	
		$flag = 0;
		foreach($this->_uris as $uri2)
		{
			if ($uri1 == $uri2)
			{
				$flag = 1;
				break;
			}
		}
	
		// CONTENT
		if (($parent_prev == 'ENTRY')&&($parent == 'CONTENT'))
		{
			if ($name_wk == 'A')
			{
				$this->_entries[$this->_entry_num]['content'] .= "</a>";
			}
	
			if ($name_wk != 'CONTENT')
			{
				return;
			}
		}
	
		// decrement parent
		if (( $flag || empty($uri1) )&&( $parent == $name_wk ))
		{
			$this->_parent_num --;
			if ($this->_parent_num < 0)  $this->_parent_num = 0;
	
			if ($name_wk == 'ENTRY')
			{
				$this->_entry_num ++;
			}
		}
	
	}
	
	//---------------------------------------------------------
	// character data handler
	//---------------------------------------------------------
	function atom_character_data($parser, $data) 
	{
		$parent_0 = '';
		$parent_1 = '';
		$parent_2 = '';
		if ( isset($this->_parent[0]) )	$parent_0 = $this->_parent[0];
		if ( isset($this->_parent[1]) )	$parent_1 = $this->_parent[1];
		if ( isset($this->_parent[2]) )	$parent_2 = $this->_parent[2];
	
		$current	 = $this->_parent[$this->_parent_num];
		$current_low = strtolower( $current );
		$data		= trim($data);
	
		if ($parent_0 != 'FEED')  return;
	
		switch($parent_1)
		{
			// ENTRY
			case 'ENTRY':
				switch($parent_2)
				{
	
			// ENTRY AUTHOR
					case 'AUTHOR':
						switch($current)
						{
							case 'NAME':
							case 'URL':
							case 'EMAIL':
								$key = 'author_'.$current_low;
								if ( isset( $this->_entries[$this->_entry_num][$key] ) )
								{
									$this->_entries[$this->_entry_num][$key] .= $data;
								}
								else
								{
									$this->_entries[$this->_entry_num][$key] = $data;
								}
								break;
						}
						break;
	
			// ENTRY others
					default:
						switch($current)
						{
							case 'TITLE':
							case 'SUMMARY':
							case 'SUBJECT':
							case 'ID':
							case 'CONTENT':
								if ( isset( $this->_entries[$this->_entry_num][$current_low] ) )
								{
									$this->_entries[$this->_entry_num][$current_low] .= $data;
								}
								else
								{
									$this->_entries[$this->_entry_num][$current_low] = $data;
								}
								break;
							case 'MODIFIED';
							case 'ISSUED';
							case 'CREATED';
							case 'UPDATED';
								$this->_entries[$this->_entry_num][$current_low] = $this->dateW3cToUnix( $data ) ;
								break ;
						}
						break;
				}
				break;
	
			// FEED AUTHOR
			case 'AUTHOR':
				switch($current)
				{
					case 'NAME':
					case 'URL':
					case 'EMAIL':
						$key = 'author_'.$current_low;
						if ( isset( $this->_feed[$key] ) )
						{
							$this->_feed[$key] .= $data;
						}
						else
						{
							$this->_feed[$key] = $data;
						}
						break;
				}
				break;
	
			// FEED others
			default:
				switch($current)
				{
					case 'TITLE':
					case 'ID':
					case 'GENERATOR':
					case 'COPYRIGHT':
					case 'TAGLINE':
						if ( isset( $this->_feed[$current_low] ) )
						{
							$this->_feed[$current_low] .= $data;
						}
						else
						{
							$this->_feed[$current_low] = $data;
						}
						break;
					case 'MODIFIED';
					case 'ISSUED';
					case 'CREATED';
					case 'UPDATED';
						$this->_feed[$current_low] = $this->dateW3cToUnix( $data ) ;
						break ;
				}
				break;
		}
	
	}
	
	//---------------------------------------------------------
	// start namespace handler
	//---------------------------------------------------------
	function atom_ns_start($parser, $prefix, $uri)
	{
	//  echo "nss;$prefix;$uri <br>\n";
	
		array_push($this->_uris, strtoupper($uri));
	}
	
	//---------------------------------------------------------
	// end namespace handler
	//---------------------------------------------------------
	function atom_ns_end($parser, $prefix)
	{
		array_pop($this->_uris);
	}


	function dateW3cToUnix( $w3cDT )
	{
		$w3cDT = strtoupper( $w3cDT ) ;

		// for wrong format like dd-mm-yyyy hh:mm:ss
		if( preg_match( '/^(\d{1,2})[-\/\.](\d{1,2})[-\/\.](\d{4})(.*)$/' , $w3cDT , $regs ) ) {
			$w3cDT = "{$regs[3]}-{$regs[2]}-{$regs[1]}{$regs[4]}" ;
		}

		// get the timezone
		$tzoffset = date( 'Z' ) ;
		if( $pos = strrpos( $w3cDT , 'Z' ) ) {
			// GMT
			$localdatetime = substr( $w3cDT , 0 , $pos ) ;
		} else if( ( $pos = strrpos( $w3cDT , '+' ) ) > 0 ) {
			$hourmin = explode( ':' , substr( $w3cDT , $pos + 1 ) ) ;
			if( ! empty( $hourmin[0] ) ) $tzoffset -= $hourmin[0] * 3600 ;
			if( ! empty( $hourmin[1] ) ) $tzoffset -= $hourmin[1] * 60 ;
			$localdatetime = substr( $w3cDT , 0 , $pos ) ;
		} else if( ( $pos = strrpos( $w3cDT , '-' ) ) > 7 ) {
			$hourmin = explode( ':' , substr( $w3cDT , $pos + 1 ) ) ;
			if( ! empty( $hourmin[0] ) ) $tzoffset += $hourmin[0] * 3600 ;
			if( ! empty( $hourmin[1] ) ) $tzoffset += $hourmin[1] * 60 ;
			$localdatetime = substr( $w3cDT , 0 , $pos ) ;
		} else {
			// no timezone
			$localdatetime = $w3cDT ;
		}

		$localunixtime = strtotime( str_replace( 'T' , ' ' , $localdatetime ) ) ;
		if( $localunixtime == -1 ) return time() ;
		else return $localunixtime + $tzoffset ;
	}


}

?>