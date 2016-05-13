<?php
/**
 * 系统项目—分页
 */
class Sharepage extends CI_Model{
	
    public function __construct(){
		
        parent::__construct();
    }
	
 /**
     * 分页
     * @param $url 跳转地址
     * @param $total 总记录数
     * @param $cupage 每页显示的记录数
     * 
     */
    public function showPage($url,$total,$cupage,$suffix=null,$per_page=null){
		
    	$this->load->library('pagination'); //分页
		
		$per_page = $per_page ? $per_page : 'per_page';
		
        $config = array(
            'base_url'   	=> $url,
            'prefix'        =>'',
            'page_query_string'=>TRUE,
            'num_links'   	=> 5,
            'uri_segment'   => 5,
            'total_rows' 	=> $total,
            'suffix' 		=>$suffix ,
            'per_page'   	=> $cupage,
            'show_prev_link'=> true,
            'show_next_link'=> true,
            'prev_link'  	=> '<i class="icon-double-angle-left"></i>',
            'next_link'  	=> '<i class="icon-double-angle-right"></i>',
			'query_string_segment' => $per_page,
			'cur_tag_open' 	=> '<li class="active"><a href="javascript:">',
			'cur_tag_close' => '</a></li>',
        	'prev_tag_open' => '<li>',
        	'prev_tag_close'=> '</li>',
        	'next_tag_open' => '<li class="kpagebtn">',
        	'next_tag_close'=> '</li>',
            'num_tag_open'  => '<li>',
            'num_tag_close' => '</li>',
        	'first_tag_open' => '<li>',
        	'first_tag_close'=> '</li>',
        	'last_tag_open' => '<li>',
        	'last_tag_close'=> '</li>',
        		
        	'anchor_class' 	=> '',
			'first_link'	=> '<i class="icon-step-backward"></i>',
			'last_link'		=> '<i class="icon-step-forward"></i>',
        );
		
        $this->pagination->initialize($config);
		
        return $this->pagination->create_links();
    }
}