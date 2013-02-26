<?php
/**
 * ImpressCMS Update
 *
 * @copyright	The ImpressCMS Project http://www.impresscms.org/
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @category	ICMS
 * @package		Administration
 * @subpackage	Update
 * @since		2.0
 */

defined("ICMS_ROOT_PATH") or die("ImpressCMS root path not defined");

/**
 * The update object - for checking for updated extensions
 *
 * @category	ICMS
 * @package		Administration
 * @subpackage	Update
 *
 */
class mod_system_Update extends icms_ipf_Object {

	/**
	 * Unique identifier for the update
	 *
	 * @var integer
	 */
	public $id;
	/**
	 * type of update (module, core, theme, library, ...)
	 *
	 */
	public $exttype;
	/**
	 * Name of the update (=title)
	 *
	 */
	public $name;
	/**
	 * A longer description of the update
	 *
	 */
	public $desc;

    /**
     * the version number (semver) that is available on the server
     *
     * @var string
     */
    public $availableversion;

    /**
     * the date on which this version was released
     */
    public $releasedate;

    /**
     * the direct download url where the file can be obtained
     */

    public $downloadurl;

    /**
     * the SHA-1 hash of the file, to verify a correct download
     * @var
     */
    public $hash;

	/**
	 * Construct the update object
	 *
	 * @param @mod_sys_UpdateHandler $handler
	 */
	public function __construct(&$handler) {
		parent::__construct($handler);

		$this->quickInitVar("id", XOBJ_DTYPE_INT, TRUE);
		$this->quickInitVar("exttype", XOBJ_DTYPE_TXTBOX, TRUE, _CO_SYSTEM_UPD_EXTTYPE, _CO_SYSTEM_UPD_EXTTYPE_DSC);
        $this->quickInitVar("name", XOBJ_DTYPE_TXTBOX, TRUE, _CO_SYSTEM_UPD_NAME, _CO_SYSTEM_UPD_NAME_DSC);
        $this->quickInitVar("dsc", XOBJ_DTYPE_TXTBOX, TRUE, _CO_SYSTEM_UPD_DSC, _CO_SYSTEM_UPD_DSC_DSC);
        $this->quickInitVar("availableversion", XOBJ_DTYPE_TXTBOX, TRUE, _CO_SYSTEM_UPD_AVAILABLEVERS, _CO_SYSTEM_UPD_AVAILABLEVERS_DSC);
        $this->quickInitVar("releasedate", XOBJ_DTYPE_STIME, TRUE, _CO_SYSTEM_UPD_RELDATE, _CO_SYSTEM_UPD_RELDATE_DSC);
        $this->quickInitVar("downloadurl", XOBJ_DTYPE_URL, TRUE, _CO_SYSTEM_UPD_DL_URL, _CO_SYSTEM_UPD_DL_URL_DSC);
        $this->quickInitVar("hash", XOBJ_DTYPE_TXTBOX, TRUE, _CO_SYSTEM_UPD_HASH, _CO_SYSTEM_UPD_HASH_DSC);

	}

   /**
     * Overriding the icms_ipf_Object::getVar method to assign a custom method on some
     * specific fields to handle the value before returning it
     *
     * @param str $key key of the field
     * @param str $format format that is requested
     * @return mixed value of the field that is requested
     */
    function getVar($key, $format = 's') {
        if ($format == 's' && in_array($key, array())) {
            return call_user_func(array($this,$key));
        }
        return parent::getVar($key, $format);
    }
}
