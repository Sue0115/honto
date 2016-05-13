<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * MY_Loader Class
 *
 * Loads views and files
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @author		ExpressionEngine Dev Team
 * @category	Loader
 * @link		http://codeigniter.com/user_guide/libraries/loader.html
 */
class MY_Loader extends CI_Loader {

	/**
	 * Constructor
	 *
	 * Sets the path to the view files and gets the initial output buffering level
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Initialize the Loader
	 *
	 * This method is called once in CI_Controller.
	 *
	 * @param 	array
	 * @return 	object
	 */
	public function initialize()
	{

		parent::initialize();
		
		$this->_my_autoloader();
		
		return $this;
	}
	
	private function _my_autoloader()
	{
		if (defined('ENVIRONMENT') AND file_exists(APPPATH.'config/'.ENVIRONMENT.'/autoload.php')){
			include(APPPATH.'config/'.ENVIRONMENT.'/autoload.php');
		}else{
			include(APPPATH.'config/autoload.php');
		}
		
		if ( ! isset($autoload)){
			return FALSE;
		}
		// Load libraries
		if (isset($autoload['driver']) AND count($autoload['driver']) > 0){
			// Load all other libraries
			foreach ($autoload['driver'] as $item=>$params){
				$this->driver($item,$params);
			}
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Model Loader
	 *
	 * This function lets users load and instantiate models.
	 *
	 * @param	string	the name of the class
	 * @param	string	name for the model
	 * @param	bool	database connection
	 * @return	void
	 */
	public function model($model, $name = '', $db_conn = FALSE)
	{

		if (is_array($model))
		{
			foreach ($model as $babe)
			{
				$this->model($babe);
			}
			return;
		}

		if ($model == '')
		{
			return;
		}

		$path = '';

		// Is the model in a sub-folder? If so, parse out the filename and path.
		if (($last_slash = strrpos($model, '/')) !== FALSE)
		{
			// The path is in front of the last slash
			$path = substr($model, 0, $last_slash + 1);

			// And the model name behind it
			$model = substr($model, $last_slash + 1);
		}

		if ($name == '')
		{
			$name = $model;
		}

		if (in_array($name, $this->_ci_models, TRUE))
		{
			return;
		}

		$CI =& get_instance();
		if (isset($CI->$name))
		{
			show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
		}

		$model = strtolower($model);
		$end = end($this->_ci_model_paths);
		reset($this->_ci_model_paths);
		if($model =='slme_user_model'){
	
	}
		foreach ($this->_ci_model_paths as $mod_path)
		{
			if ( ! file_exists($mod_path.'models/'.$path.$model.'.php'))
			{
			    //如果循环到最后一次都没有找到对应的模型文件
			    if($mod_path == $end){
			        
    				if (!is_dir($mod_path.'models/'.$path)) {
    					mkdir($mod_path.'models/'.$path);
    				}
    				$str = file_get_contents(__DIR__ . '/template/model.tpl');
    				$str = str_replace(array('{class}','{path}'), array(ucfirst($model),$path), $str);
    				file_put_contents($mod_path.'models/'.$path.$model.'.php', $str);
			    }else {
			        //继续下一个
			        continue;
			    }
			}

			if ($db_conn !== FALSE AND ! class_exists('CI_DB'))
			{
				if ($db_conn === TRUE)
				{
					$db_conn = '';
				}

				$CI->load->database($db_conn, FALSE, TRUE);
			}

			if ( ! class_exists('CI_Model'))
			{
				load_class('Model', 'core');
			}

			require_once($mod_path.'models/'.$path.$model.'.php');
	
			$model = ucfirst($model);

			$CI->$name = new $model();

			$this->_ci_models[] = $name;
			return;
		}

		// couldn't find the model
		show_error('Unable to locate the model you have specified: '.$model);
	}
	
	/**
	 * Class Loader
	 *
	 * This function lets users load and instantiate classes.
	 * It is designed to be called from a user's app controllers.
	 *
	 * @param	string	the name of the class
	 * @param	mixed	the optional parameters
	 * @param	string	an optional object name
	 * @return	void
	 */
	public function library($library = '', $params = NULL, $object_name = NULL)
	{
		if (is_array($library))
		{
			foreach ($library as $class)
			{
				$this->library($class, $params);
			}
	
			return;
		}
	
		if ($library == '' OR isset($this->_base_classes[$library]))
		{
			return FALSE;
		}
	
		$this->_ci_load_class($library, $params, $object_name);
	}
	/**
	 * 切换视图路径
	 *
	 * @access  public
	 * @return  void
	 */
	public function switch_theme($theme = 'default')
	{
	    $this->_ci_view_paths = array(APPPATH . 'views/' . $theme . '/'	=> TRUE);
	}
}

/* End of file Loader.php */
/* Location: ./system/core/Loader.php */