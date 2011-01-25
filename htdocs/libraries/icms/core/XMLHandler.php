<?php
/**
* Class to import/export XML to/from DB table
* @category		ICMS
* @package		Core
* @since        1.4
* @author       vaughan montgomery (vaughan@impresscms.org)
* @author       ImpressCMS Project
* @copyright    (c) 2007-2010 The ImpressCMS Project - www.impresscms.org
* @version      SVN: $Id: XMLHandler.php 20130 2010-09-11 22:26:23Z m0nty_ $
*/

/**
 *  Based on dbimexport class
 *  -------------------------------------------------------------------
 *  Developed by: Reazaul Karim - Rubel
 *  URI: http://reazulk.wordpress.com
 *  -------------------------------------------------------------------
 *  License text http://www.opensource.org/licenses/mit-license.php 
 *  About MIT license <http://en.wikipedia.org/wiki/MIT_License/>
*/

class icms_core_XMLHandler {
	private $db;

    // Download flag
    public $download = false;

	public $tableName = NULL;

	// optional (if you want to only import/export certain table elements such as config id
	public $column = NULL;

	// optional id (if you want to only import/export certain table elements such as config id = 1
	public $columnVal = NULL;

	public $file_name = NULL;

     /**
     * Construct
     */
    public function __construct() {
	}

 	/**
	 * Access the only instance of this class
	 * @return       object
	 * @static       $TextFilter_instance
	 * @staticvar    object
	 **/
	public static function getInstance() {
		static $instance;
		if (!isset($instance)) {
			$instance = new self();
		}
		return $instance;
	}

	/**
     * Export data
     *
     * @return null
     */
    public function export() {
		global $icmsConfig;
		$exportPath = $icmsConfig['xml_exportPath'];

        $dom = new DOMDocument('1.0');
		
        // Create Database node
        $database = $dom->createElement('database');
        $database = $dom->appendChild($database);
        $database->setAttribute('name', ICMS_DB_NAME);

        //create schema node
        $schema = $dom->createElement('schema');
        $schema = $dom->appendChild($schema);
        
        /* ---- CREATE SCHEMA ---- */
        while($tableRow = icms::$xoopsDB->fetchRow(icms::$xoopsDB->prefix($this->tableName))) {
            //Table Node
            $table = $dom->createElement('table');
            $table = $dom->appendChild($table);
            $table->setAttribute('name', $tableRow[0]);
            
            //Fetch table description
            $fieldQuery = icms::$xoopsDB->query("DESCRIBE $tableRow[0]");
            
            while($fieldRow = icms::$xoopsDB->fetchAssoc($fieldQuery)) {
                //Create Field node
                $field = $dom->createElement('field');
                $field = $dom->appendChild($field);
                $field->setAttribute('name', $fieldRow['Field']);
                $field->setAttribute('name', $fieldRow['Field']);
                $field->setAttribute('type', $fieldRow['Type']);
                $field->setAttribute('null', strtolower($fieldRow['Null']));
                
                //set the default
                if($fieldRow['Default'] != '') {
                    $field->setAttribute('default', strtolower($fieldRow['Default']));
                }

                //set the key
                if($fieldRow['Key'] != '') {
                    $field->setAttribute('key', strtolower($fieldRow['Key']));
                }

                //set the value/length attribute
                if($fieldRow['Extra'] != '') {
                    $field->setAttribute('extra', strtolower($fieldRow['Extra']));
                }
                
                //put the field inside of the table
                $table->appendChild($field);
            }
            
            //put the table inside of the schema
            $schema->appendChild($table);
        }
        
        // Add Scma to database
        $database->appendChild($schema);
    
        
        /* ------- Populate Data ------ */
        // Create Data node
        $data = $dom->createElement('data');
        $data = $dom->appendChild($data);
        $dom->appendChild($data);

        while($tableRow = icms::$xoopsDB->fetchRow(icms::$xoopsDB->prefix($this->tableName))) {
			// Read Table Schema again
			$descQuery = icms::$xoopsDB->query("DESCRIBE {$tableRow[0]}");
			$schema = Array();
			while($row = icms::$xoopsDB->fetchAssoc($descQuery)) {
				$schema[$row['Field']] = array(
												"Type"		=> $row['Type'],
												"Null"		=> $row['Null'],
												"Key"		=> $row['Key'],
												"Default"	=> $row['Default'],
												"Extra"		=> $row['Extra']
											);
            }

			if(isset($this->column) && isset($this->columnVal)) {
				if(is_int($this->columnVal)) {
					$sql = sprintf("SELECT * FROM %s WHERE %s = '%u'",
									$tableRow[0],
									icms::$xoopsDB->quoteString($this->column),
									(int) $this->columnVal
								);
					$rows = icms::$xoopsDB->query($sql);
				} else {
					$sql = sprintf('SELECT * FROM %s WHERE %s = %s',
						$tableRow[0], icms::$xoopsDB->quoteString($this->column), icms::$xoopsDB->quoteString($this->columnVal));
					$rows = icms::$xoopsDB->query($sql);
				}
			} else {
				$sql = sprintf('SELECT * FROM %s', $tableRow[0]);
				$rows = icms::$xoopsDB->query($sql);
			}
            $table = $dom->createElement($tableRow[0]);
            $table = $dom->appendChild($table);

            $data->appendChild($table);

            while($row = icms::$xoopsDB->fetchAssoc($rows)) {
                //Create Row node
                $data_row = $dom->createElement('row');
                $data_row = $dom->appendChild($data_row);
                $table->appendChild($data_row);
                
                // Create Row Node
                foreach($row as $key => $val) {
                    if(strstr($schema[$key]['Type'], 'int') || strstr($schema[$key]['Type'], 'float')
						|| strstr($schema[$key]['Type'], 'date') || strstr($schema[$key]['Type'], 'time')) {
                        $field = $dom->createElement($key, $val);
                        $field = $dom->appendChild($field);
                        $data_row->appendChild($field);
					} else {
                        $field = $dom->createElement($key);
                        $field = $dom->appendChild($field);
                        $data_row->appendChild($field);
                        $cdataNode = $dom->createCDATASection(self::text2normalize($val));
                        $cdataNode = $dom->appendChild($cdataNode);
                        $field->appendChild($cdataNode);
                    }                  
                }
            }
        }
        
        // Add Data to root node
        $database->appendChild($data);

        $database_name = (isset($this->file_name)) ? $this->file_name : ICMS_DB_NAME;

        // Write XML
        $dom->formatOutput = true;
        $dom->saveXML();
        
        // Download file
        if($this->download) {
            $filename =  $exportPath . '/' . time() . '.xml';
            $xml = $dom->save($filename);

            header('Content-type: text/appdb');
            header('Content-Disposition: attachment; filename="' . icms::$xoopsDB->prefix($this->tableName) . '.xml"');
            readfile($filename);
            @unlink($filename);
            exit;
        } else {
            $filename =  $exportPath . '/' . icms::$xoopsDB->prefix($this->tableName) . '.xml';
            $xml = $dom->save($filename);
        }
    }

    /**
     * Import Databse
     *
     * @return null
     */
    function import() {
		global $icmsConfig;
		$importPath = $icmsConfig['xml_importPath'];
     
        if($importPath == "" || !file_exists($importPath)) {
            die("Database file does not exist");
        }    
        
        $dom = new DOMDocument();
        $dom->load($importPath);

        // Read Schema
        $schema = $dom->getElementsByTagName('schema');
        $tables = $schema->item(0)->getElementsByTagName("table");
        

        foreach($tables as $table) {
            // Get Table Name
            $name = $table->getAttribute('name');
            $fields = $table->getElementsByTagName('field');

            // Get table data
            $dable_data = $dom->getElementsByTagName($name);
            $rows = $dable_data->item(0)->getElementsByTagName('row');

            $sqlbody = "";
            foreach($rows as $row) {
                $tmp_body = "";
                $tmp_head = "";
                foreach($fields as $field) {
                    $field_name = $field->getAttribute('name');
                    $field_type = $field->getAttribute('type');
                    $entry = $row->getElementsByTagName($field_name);
                    $field_value = $entry->item(0)->nodeValue;
                    $field_value = self::quote_smart($field_value);

                    $tmp_body .= ($tmp_body == "") ? $field_value : ",{$field_value}";

                    if($tmp_body != "") {
						$tmp_head .= ($tmp_head == "") ? "`{$field_name}`" : ",`{$field_name}`";
					}
                }
               
                 $sqlbody .=  ($sqlbody == "") ?  "($tmp_body)\n" :  ",($tmp_body)\n";
            }
            icms::$xoopsDB->query("TRUNCATE TABLE `{$name}` ");
            $query = "INSERT INTO `{$name}` ({$tmp_head}) VALUES {$sqlbody}";
            icms::$xoopsDB->query($query);
        }
    }

    public function quote_smart($value) {
		$value = icms_core_DataFilter::stripSlashesGPC($value);

		// Quote if not integer
		if(!is_numeric($value)) {
			$value = icms::$xoopsDB->quoteString($value);
			$value = mysql_real_escape_string($value);
		} else {
			return (int) $value;
		}
		return $value;
	}
    
    function text2normalize($str = "") {
        $arr_busca = array('�','�','�','�','�','�','�','�','�', '�','�','�','�','�','�','�','�','�','�','�','�','�',
							'�','�', '�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�');
        $arr_susti = array('a','a','a','a','a','A','A','A','A','e','e','e','E','E','E','i','i','i','I','I','I',
							'o','o','o','o','o','O','O','O','O','u','u','u','U','U','U','c','C','N','n');
        return trim(str_replace($arr_busca, $arr_susti, $str));
    }
}