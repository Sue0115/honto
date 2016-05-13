<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Logging Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Logging
 * @author		xuebingwang
 */
class CI_Log {

	protected $_message		= '';
	protected $_log_path	= '';
	protected $_threshold	= 1;
	protected $_thre_shold	= 1;
	protected $_date_fmt	= 'Y-m-d H:i:s';
	protected $_enabled		= TRUE;
	protected $_levels		= array('INFO' => '1', 'ERROR' => '2', 'DEBUG' => '3', 'ALL' => '4');
	protected $_file_name   = '';

	/**
	 * Constructor
	 */
	public function __construct($config=array()) {
	    $default = & get_config();
		$config =array_merge($config,$default);

		//析构函数中运行在apache时，当前工作目录会变为apache的，所以用绝对路径
		$this->_log_path = ($config['log_path'] != '') ? $config['log_path'] : FCPATH.APPPATH.'logs/';

		if ( ! is_dir($this->_log_path) OR ! is_really_writable($this->_log_path)) {
			$this->_enabled = FALSE;
		}

		if (is_numeric($config['log_threshold'])) {
			$this->_threshold = $config['log_threshold'];
		}

		if ($config['log_date_format'] != '') {
			$this->_date_fmt = $config['log_date_format'];
		}
		    
    		$this->_file_name = isset($config['file_name']) ? $config['file_name'] : 'log-'.date('Y-m-d'); 
	}

	/**
	 * @deprecated 释放实例化时一次性写入，减少IO，提高效率
	 */
	public function __destruct() {
		if($this->_message != '') {

			$filepath = $this->_log_path.$this->_file_name.'.php';
			
			if ( ! file_exists($filepath))
			{
				$this->_message = "<"."?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?".">\n\n".$this->_message;
			}
			
			if ( ! $fp = @fopen($filepath, FOPEN_WRITE_CREATE))
			{
				return FALSE;
			}
			
			flock($fp, LOCK_EX);
			fwrite($fp, $this->_message);
			flock($fp, LOCK_UN);
			fclose($fp);
			
			@chmod($filepath, FILE_WRITE_MODE);
			
		}
		return TRUE;
	}

	/**
	 * @deprecated 追加日志
	 */
	public function write_log($level = 'error', $msg) {
		$level = strtoupper($level);

		if ($this->_enabled === FALSE) {
			return FALSE;
		}

		if ( !isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold)) {
			return FALSE;
		}

		$this->_message .= $level.' - '.date($this->_date_fmt). ' --> '.$msg."\n";
	}
}