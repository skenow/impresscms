<?php
/******************************************************************************
* filename     zipextract.cls.php
*
* description  Extract zip files on the fly
*
* project
*
* author       redmonkey
*
* version      0.3
*
* status       beta
*
* license      GPL, the GNU General Public License can be found at
*              http://www.gnu.org/copyleft/gpl.html
*
* copyright    2005 redmonkey, all rights reserved
*
* dependancy   function dos2unixtime() (found in supporting function library
*              (includes/functions.lib.php))
*
* dependancy   function make_dirs() (found in supporting function library
*              (includes/functions.lib.php))
*
* notes        zip file format can be found at
*              http://www.pkware.com/company/standards/appnote/
*
* notes        the documented zip file format omits to detailthe required header
*              signature for the data descriptor (extended local file header)
*              section which is (0x08074b50). while many decompression utilities
*              will ignore this error, this signature is vital for compatability
*              with Stuffit Expander for Mac if you have included the data
*              descriptor
*
* notes        while using bzip2 compression offers a reduced file size it does
*              come at the expense of higher system resources usage. the
*              decompression utility will also have to be compatabile with at
*              least v4.6 zip file format specification
*
* history
* 01/01/2005   v0.1 - initial version
* 05/04/2006   v0.2 - corrected file type/format check on internal file
*                     attributes
* 06/06/2006   v0.3 - corrected 'version made by' compatibility check
*
* notice       this is written to be compatible with the original PKWARE .zip
*              file format. the author of this program is NOT the original
*              publisher of this format.
*
* notice       this program is free software, you can redistribute it and/or
*              modify it under the terms of the GNU General Public License as
*              published by the Free Software Foundation; either version 2 of
*              the License, or (at your option) any later version
*
* notice       this program is distributed in the hope that it will be useful
*              but WITHOUT ANY WARRANTY; without even the implied warranty of
*              MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*              GNU General Public License for more details
******************************************************************************/
class ZipExtract
{
  var $zip_header;  // array containg end of central directory headers
  var $zipfile;     // the name including path of zip file
  var $filedata;    // array containg individual file headers
  var $file_ids;    // array containg internal file ids
  var $content;     // zip file contents
  var $methods;     // array of known compression methods
  var $known_fa;    // array of recognisable operating system's file attributes

  /**
  * @return
  * @param  _file  filename including path to zip file
  * @desc          constructor, initialises class variables
  */
  function ZipExtract($_file)
  {
    $this->zipfile = $_file;    // set the name including path of zip file

    $this->_load_zip($_file);   // load in zip file
    $this->_parse_end_record(); // parse end of central directory headers
    $this->_parse_cntrldir();   // parse central directory records

    // set known compression methods array
    $this->methods = array('Stored',
                           'Shrunk',
                           'Reduced with compression factor 1',
                           'Reduced with compression factor 2',
                           'Reduced with compression factor 3',
                           'Reduced with compression factor 4',
                           'Imploded',
                           'Tokenizing compression algorithm',
                           'Deflated',
                           'Deflated64(tm)',
                           'PKWARE Data Compression Library Imploding',
                           'Reserved by PKWARE',
                           'BZIP2');

    // set operating systems with recognised file attributes
    //  0 = DOS compatible (FAT etc..)
    // 10 = Windows NTFS (According to spec)
    // 11 = Zip files created with XP (reports as NTFS)
    $this->known_fa = array(0x00, 0x0a, 0x0b);
  }

  /**
  * @return array  numerically indexed array of files within the archive
  * @desc          lists all files within the archive as an array. the index
  *                of the array element is the internal file id and the value
  *                is the filename including path
  */
  function list_files()
  {
    $array = array();

    foreach ($this->file_ids as $k => $v)
    {
      if (($this->filedata[$v]['ex_fa'] & 0x10) != 0x10)
      {
        $array[$v] = $k;
      }
    }
    return $array;
  }

  /**
  * @return string           filename excluding path
  * @param  mixed  _fileref  either internal file ID integer or filename including
  *                          full path
  * @desc                    returns the filename excluding path
  */
  function get_filename($_fileref)
  {
    if (false === ($fid = $this->_get_fid($_fileref)))
    {
      $notice_msg = 'Failed to get filename for file (' . $_fileref . ') no such file in archive';
      trigger_error($notice_msg, E_USER_NOTICE);
      return false;
    }

    return $this->filedata[$fid]['realname'];
  }

  /**
  * @return string            path of file as stored in the archive
  * @param  mixed   _fileref  either internal file ID integer or filename including
  *                           full path
  * @desc                     returns the path to the file
  */
  function get_filepath($_fileref)
  {
    if (false === ($fid = $this->_get_fid($_fileref)))
    {
      $notice_msg = 'Failed to get file path for file (' . $_fileref . ') no such file in archive';
      trigger_error($notice_msg, E_USER_NOTICE);
      return false;
    }

    return $this->filedata[$fid]['path'];
  }

  /**
  * @return int              uncompressed size of file
  * @param  mixed  _fileref  either internal file ID integer or filename including
  *                          full path
  * @desc                    returns the uncompressed size of file in bytes
  */
  function get_filesize($_fileref)
  {
    if (false === ($fid = $this->_get_fid($_fileref)))
    {
      $notice_msg = 'Failed to get file size for file (' . $_fileref . ') no such file in archive';
      trigger_error($notice_msg, E_USER_NOTICE);
      return false;
    }

    return $this->filedata[$fid]['uc_len'];
  }

  /**
  * @return int              compressed size of file
  * @param  mixed  _fileref  either internal file ID integer or filename including
  *                          full path
  * @desc                    returns the compressed size of file in bytes
  */
  function get_filecsize($_fileref)
  {
    if (false === ($fid = $this->_get_fid($_fileref)))
    {
      $notice_msg = 'Failed to get compressed size for file (' . $_fileref . ') no such file in archive';
      trigger_error($notice_msg, E_USER_NOTICE);
      return false;
    }

    return $this->filedata[$fid]['c_len'];
  }

  /**
  * @return int              Unix timestamp of the files last modified time and date
  * @param  mixed  _fileref  either internal file ID integer or filename including
  *                          full path
  * @desc                    returns the compressed size of file in bytes
  */
  function get_modtime($_fileref)
  {
    if (false === ($fid = $this->_get_fid($_fileref)))
    {
      $notice_msg = 'Failed to get last modified time for file (' . $_fileref . ') no such file in archive';
      trigger_error($notice_msg, E_USER_NOTICE);
      return false;
    }

    return $this->filedata[$fid]['mod_time'];
  }

  /**
  * @return int              compression ratio as a percentage
  * @param  mixed  _fileref  either internal file ID integer or filename including
  *                          full path
  * @desc                    returns the ratio of compressed versus uncompressed size
  */
  function get_ratio($_fileref)
  {
    if (false === ($fid = $this->_get_fid($_fileref)))
    {
      $notice_msg = 'Failed to get compression ratio for file (' . $_fileref . ') no such file in archive';
      trigger_error($notice_msg, E_USER_NOTICE);
      return false;
    }

    if ($this->filedata[$fid]['uc_len'] == $this->filedata[$fid]['c_len'])
    {
      return 0;
    }

    return round(100 - (100 /
                       ($this->filedata[$fid]['uc_len'] /
                       ($this->filedata[$fid]['c_len']))), 2);
  }

  /**
  * @return string           method used to compress the file within the archive
  * @param  mixed  _fileref  either internal file ID integer or filename including
  *                          full path
  * @desc                    returns method used to compress the file within the archive
  */
  function get_compress_method($_fileref)
  {
    if (false === ($fid = $this->_get_fid($_fileref)))
    {
      $notice_msg = 'Failed to get compression method for file (' . $_fileref . ') no such file in archive';
      trigger_error($notice_msg, E_USER_NOTICE);
      return false;
    }

    return $this->methods[$this->filedata[$fid]['method']];
  }

  /**
  * @return string           crc32 checksum for file
  * @param  mixed  _fileref  either internal file ID integer or filename including
  *                          full path
  * @desc                    returns crc32 checksum for the file
  */
  function get_checksum($_fileref)
  {
    if (false === ($fid = $this->_get_fid($_fileref)))
    {
      $notice_msg = 'Failed to get checksum for file (' . $_fileref . ') no such file in archive';
      trigger_error($notice_msg, E_USER_NOTICE);
      return false;
    }

    return $this->filedata[$fid]['crc32'];
  }

  /**
  * @return string           minimum version required to extract the file
  * @param  mixed  _fileref  either internal file ID integer or filename including
  *                          full path
  * @desc                    returns the minimum version of the zip file specification
  *                          required to extract the file
  */
  function get_min_ver($_fileref)
  {
    if (false === ($fid = $this->_get_fid($_fileref)))
    {
      $notice_msg  = 'Failed to get minmum version required to extract file ';
      $notice_msg .= '(' . $_fileref . ') no such file in archive';
      trigger_error($notice_msg, E_USER_NOTICE);
      return false;
    }

    return number_format($this->filedata[$fid]['min_ver'] / 10, 1);
  }

  /**
  * @return string           main comment within zip file
  * @desc                    returns the main comment from the zip archive
  */
  function get_comment()
  {
    return $this->zip_header['comment'];
  }

  /**
  * @return mixed            bool true if the file can be extracted otherwise a
  *                          string containing a reason for failure
  * @param  mixed  _fileref  either internal file ID integer or filename including
  *                          full path
  * @desc                    checks file attribute compatability and general purpose
  *                          bit 0 flag for compatability with this extractor
  */
  function can_extract($_fileref)
  {
    if (false === ($fid = $this->_get_fid($_fileref)))
    {
      return 'no such file in archive';
    }

    $compatible = in_array($this->filedata[$fid]['fa_comp'] & 0xff, $this->known_fa) ? true : false;

    $encrypted  = $this->filedata[$fid]['gp_bit']   & 0x01 ? true : false;

    if ($compatible && !$encrypted)
    {
      switch($this->filedata[$fid]['method'])
      {
        case (0x08):  // Deflated
          if (!function_exists('gzinflate'))
          {
            $error_msg  = 'file is Deflated but your PHP installation does not ';
            $error_msg .= 'support decompressing this format';
            return $error_msg;
          }
          return true;

        case (0x00):  // Stored
          return true;

        case (0x0c):  // bzip2 algorithm
          if (!function_exists('bzdecompress'))
          {
            $error_msg  = 'file is compressed with bzip2 compression but your PHP ';
            $error_msg .= 'installation does not support decompressing this format';
            return $error_msg;
          }
          return true;

        default    :  // anything else
          return 'unsupported compression format (' . $this->get_compress_method($_fileref) . ')';
      }
    }

    if (!$compatible)
    {
      return 'incompatible file attributes';
    }

    if ($encrypted)
    {
      return 'encrypted files are not supported';
    }

    return 'unknown error';
  }

  /**
  * @return string           decompressed file contents
  * @param  mixed  _fileref  either internal file ID integer or filename including
  *                          full path
  * @desc                    extracts individual file from the zip archive
  */
  function extract_file($_fileref)
  {
    if (false === ($fid = $this->_get_fid($_fileref)))
    {
      $notice_msg = 'Failed to extract file (' . $_fileref . ') no such file in archive';
      trigger_error($notice_msg, E_USER_NOTICE);
      return false;
    }

    if (true !== ($reason = $this->can_extract($fid)))
    {
      trigger_error('Failed to extract file (' . $_fileref . ') ' . $reason, E_USER_NOTICE);
      return false;
    }

    $data = $this->_extract_data($this->filedata[$fid]['datastart'],
                                 $this->filedata[$fid]['c_len']);

    switch($this->filedata[$fid]['method'])
    {
      case (0x08):  // Deflated
        return gzinflate($data);

      case (0x00):  // Stored
        return $data;

      case (0x0c):  // bzip2 algorithm
        return bzdecompress($data);

      default    :
        return false;
    }
  }

  /**
  * @return
  * @param  string  _path  path to directory to unzip files to
  * @desc                  extracts all files from zip archive to specified
  *                        directory of $_path
  */
  function extract_all($_path = '.')
  { // remove leading and trailing spaces from path
    // and correct and erros with directory seperators
    $_path = trim(str_replace('\\', '/', $_path));

    // remove trailing slash if present
    $_path = preg_replace('/\/+$/', '', $_path);

    // if path is root then set $path to nothing as this
    // is catered for later.
    $_path = $_path == '/' ? '' : $_path;

    // get a list of entries defined as directories
    $dirs  = $this->_list_dirs();

    if (count($dirs) > 0)
    { // loop through each entry an attempt to make the directory structure
      foreach ($dirs as $id => $dir)
      {
        if (!make_dirs($_path . '/' . $dir, $this->get_modtime($id)))
        {
          return false;
        }
      }
    }

    // get a list of all files within the archive
    $files = $this->list_files();

    // loop through each file in the archive and extract
    foreach ($files as $fid => $filename)
    {
      $path = $_path . '/' . $this->get_filepath($fid);

      // attempt to make directory for file
      if (!make_dirs($path))
      {
        $notice_msg  = 'Failed to extract ' . $this->get_filename($fid);
        $notice_msg .= ' could not create required directory structure';
        trigger_error($notice_msg, E_USER_WARNING);
        continue;
      }

      // make sure we can write to directory where the file is to be extracted
      if (!is_writable($path))
      {
        $error_msg  = 'Failed to extract ' . $this->get_filename($fid);
        $error_msg .= ' to ' . $path . ' check directory permissions';
        trigger_error($error_msg, E_USER_WARNING);
        continue;
      }

      // get the uncompressed file data
      $file_contents = $this->extract_file($fid);

      $path = $path == './' ? '.' : $path;
      $file = $path  . '/'  . $this->get_filename($fid);

      // write data to extracted file
      if ($fp = @fopen($file, 'wb'))
      {
        fwrite($fp, $file_contents);
        fclose($fp);
        // set last modified filetime
        @touch($file, $this->get_modtime($fid));
      }
    }
  }

  /**
  * @return
  * @param  _file  filename including path to zip file
  * @desc          loads the contents of the zip file into the content string
  */
  function _load_zip($_file)
  {
    if (!file_exists($_file) && !is_file($_file))
    {
      trigger_error('Failed to load zip file (' . $_file . ') does not exist', E_USER_ERROR);
    }

    if (!$fp = fopen($_file, 'rb'))
    {
      trigger_error('Failed to open zip file (' . $_file . ') permission denied', E_USER_ERROR);
    }
    $this->content = fread($fp, filesize($_file));
    fclose($fp);
  }

  /**
  * @return
  * @desc     parse end of central directory headers
  */
  function _parse_end_record()
  {
    if (!$pos_eof_cntrldir = _strrpos($this->content, "\x50\x4b\x05\x06"))
    {
      trigger_error('Failed to read central directory header information', E_USER_ERROR);
    }

    $data   = substr($this->content, ($pos_eof_cntrldir + 0x04));

    $unpack = 'vthis_disk/'       // number of this disk              (2 bytes)
            . 'vstart_disk/'      // number of the disk with start of
                                  // central directory record         (2 bytes)
            . 'vdisk_entries/'    // total # of entries on this disk  (2 bytes)
            . 'vtotal_entries/'   // total # of entries overall       (2 bytes)
            . 'Vcntrldir_size/'   // size of central dir              (4 bytes)
            . 'Vcntrldir_offset/' // offset to start of central dir   (4 bytes)
            . 'vcom_len';         // .zip file comment length         (2 bytes)

    $this->zip_header            = unpack($unpack, $data);

    $this->zip_header['comment'] = substr($data, 0x12, $this->zip_header['com_len']);

    // check that this is a single disk archive
    if ($this->zip_header['start_disk']   != $this->zip_header['this_disk'] ||
        $this->zip_header['disk_entries'] != $this->zip_header['total_entries'])
    {
      trigger_error('Multiple disk archives are not supported', E_USER_ERROR);
    }
  }

  /**
  * @return
  * @desc     parse central directory record of zip file and populate files
  *           array with the results
  */
  function _parse_cntrldir()
  {
    $fid      = 0;
    $cntrldir = $this->_extract_data($this->zip_header['cntrldir_offset'],
                                     $this->zip_header['cntrldir_size']);

    $unpack = 'vversion/'         // version made by                 (2 bytes)
            . 'vmin_ver/'         // version needed to extract       (2 bytes)
            . 'vgp_bit/'          // general purpose bit             (2 bytes)
            . 'vmethod/'          // compression method              (2 bytes)
            . 'Vmod_time/'        // last modified time and date     (4 bytes)
            . 'Vcrc32/'           // crc32                           (4 bytes)
            . 'Vc_len/'           // compressed length               (4 bytes)
            . 'Vuc_len/'          // uncompressed length             (4 bytes)
            . 'vfn_len/'          // length of filename              (2 bytes)
            . 'vef_len/'          // extra field length              (2 bytes)
            . 'vfcom_len/'        // file comment length             (2 bytes)
            . 'vdisk_start/'      // disk number start               (2 bytes)
            . 'vin_fa/'           // internal file attributes        (2 bytes)
            . 'Vex_fa/'           // external file sttributes        (4 bytes)
            . 'Voffset';          // relative offset of local header (4 bytes)

    $file_headers = explode("\x50\x4b\x01\x02", $cntrldir);
    array_shift($file_headers); // remove first empty array element

    // loop through each local file header and populate files array
    foreach ($file_headers as $header)
    {
      $info = unpack($unpack, $header);

      // ZIP file specification version
      $info['pk_spec']   = $info['version'] &  0xff;

      // file attribute compatibility
      $info['fa_comp']   = $info['version'] >> 0x08;

      // filename and path information
      $info['filename']  = substr($header, 0x2a, $info['fn_len']);
      $pathinfo          = pathinfo($info['filename']);
      $info['datastart'] = $info['offset'] + 0x1e + $info['fn_len'];
      $info['realname']  = $pathinfo['basename'];
      $info['path']      = $pathinfo['dirname'] == '.' ? '' : $pathinfo['dirname'];

      // file last modified date and time
      $info['mod_time']  = dos2unixtime($info['mod_time']);

      // save file information
      $this->filedata[$fid]              = $info;
      $this->file_ids[$info['filename']] = $fid;

      // increment internal fileID counter
      $fid++;

      // cleanup
      unset($info);
    }
  }

  /**
  * @return int               internal file ID
  * @param  mixed   _fileref  either internal file ID integer or filename including
  *                           path as string
  * @desc                     check the existance of the file within the archive
  *                           returns internal file ID on success, false on failure
  */
  function _get_fid($_fileref)
  {
    if (is_string($_fileref))
    {// file reference is a string we need to look up it's internal file ID
      if (!isset($this->file_ids[$_fileref]))
      {// file doesn't exist in the archive
        return false;
      }
      // return internal file ID
      return $this->file_ids[$_fileref];
    }
    elseif (!isset($this->filedata[$_fileref]))
    {
      return false;
    }
    return $_fileref;
  }

  /**
  * @return string            compressed filedata content
  * @param  int      _offset  offset from start of file to begining of data
  * @param  int      _length  length of data from offset
  * @desc                     returns compressed data from within the archive
  *                           starting at offset up to length
  */
  function _extract_data($_offset, $_length)
  {
    return substr($this->content, $_offset, $_length);
  }

  /**
  * @return array  numerically indexed array of directories within the archive
  * @desc          lists all directory definitions within the archive as an array.
  */
  function _list_dirs()
  {
    $array = array();

    foreach ($this->file_ids as $k => $v)
    {
      if (($this->filedata[$v]['ex_fa'] & 0x10) == 0x10)
      {
        $array[$v] = $k;
      }
    }
    return $array;
  }
}
?>