<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * MY_Router Class
 *
 * Parses URIs and determines routing
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @author		ExpressionEngine Dev Team
 * @category	Libraries
 * @link		http://codeigniter.com/user_guide/general/routing.html
 */
class MY_Router extends CI_Router
{
	/**
	 * Constructor
	 *
	 * Runs the route mapping function.
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 *  Set the directory name
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function set_directory($dir)
	{
		$this->directory = str_replace(array('.'), '', $dir).'/';
	}
	
	/**
	 * Validates the supplied segments.  Attempts to determine the path to
	 * the controller.
	 *
	 * @access	private
	 * @param	array
	 * @return	array
	 */	
	function _validate_request($segments)
	{
		if (count($segments) == 0)
		{
			return $segments;
		}
		
		// Does the requested controller exist in the root folder?
		if (file_exists(APPPATH.'controllers/'.$segments[0].EXT))
		{
			return $segments;
		}

		// Is the controller in a sub-folder?
		if (is_dir(APPPATH.'controllers/'.$segments[0]))
		{		
			// Set the directory and remove it from the segment array
			//$this->set_directory($segments[0]);
			//$segments = array_slice($segments, 1);
            $tempDir = array();
            $i = 0;

            for(; $i < count($segments); $i++)
            {
                // We keep going until we can't find a directory
                $tempDir[] = $segments[$i];
                if(!is_dir(APPPATH.'/controllers/'.implode('/', $tempDir)))
                {
                    // The last "segment" is not a part of the "directory" so we can get rid of it.
                    unset($tempDir[count($tempDir)-1]);
                    break;
                }
            }

            $this->set_directory(implode('/', $tempDir));
            $segments = array_slice($segments, $i);

			if (count($segments) > 0)
			{
				// Does the requested controller exist in the sub-folder?
				if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$segments[0].EXT))
				{
				    if ( ! empty($this->routes['404_override']))
				    {
				        $x = explode('/', $this->routes['404_override']);
				    
				        $this->set_directory('');
				        $this->set_class($x[0]);
				        $this->set_method(isset($x[1]) ? $x[1] : 'index');
				    
				        return $x;
				    }
				    else
				    {
				        show_404($this->fetch_directory().$segments[0]);
				    }
				}
			}
			else
			{
				$this->set_class($this->default_controller);
				$this->set_method('index');
			
				// Does the default controller exist in the sub-folder?
				if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$this->default_controller.EXT))
				{
					$this->directory = '';
					return array();
				}
			
			}

			return $segments;
		}

		// If we've gotten this far it means that the URI does not correlate to a valid
		// controller class.  We will now see if there is an override
		if ( ! empty($this->routes['404_override']))
		{
		    $x = explode('/', $this->routes['404_override']);
		
		    $this->set_class($x[0]);
		    $this->set_method(isset($x[1]) ? $x[1] : 'index');
		
		    return $x;
		}
		
		// Can't find the requested controller...
		show_404($segments[0]);
	}
}
// END MY_Router Class