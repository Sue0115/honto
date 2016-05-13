<?php

/**
 * Tree 树型类(无限分类)
 *
 * @example
 *   $tree= new Tree($result);
 *   $arr=$tree->leaf(0);
 *   $nav=$tree->navi(15);
 */
class Tree {
    private $arr;
    private $fields;
    public  $result;
    public  $tree = array();
    public  $current_id;
    
    /**
     * 构造函数
     *
     * @param array $result 树型数据表结果集
     * @param array $fields 树型数据表字段，array(分类id,父id)
     * @param integer $root 顶级分类的父id
     * @param array $current 要比较的数组，比如需要找出当前页面的id
     */
    public function init($result=array(), $fields = array('cid', 'parents','title'), $root = 0,$current=array(),$hookFunc='') {
        $this->fields = $fields;
        
        $tmp = array();
        foreach ($result as $node) {
            if($current && @!array_diff($current, $node)){
				$this->current_id = $node[$fields[0]];
            }
            
            if($hookFunc && function_exists($hookFunc)){
                $hookFunc($node);
            }
            
            $tmp[$node[$fields[1]]][$node[$fields[0]]] = $node;
            $this->result[$node[$fields[0]]] = $node;
            
        }
        unset($result);
        
        $tree = array();
        krsort($tmp);
        if(count($tmp) && isset($tmp[$root])){
            $tree = $tmp[$root];
            unset($tmp[$root]);//只剩下子类
        
        $this->tree = $this->handler($tree,$tmp);}
    }
    
    /**
     * 树型数据表结果集处理
     */
    private function handler($tree,$tmp) {
        $id = $this->fields[0];
    
        foreach ($tree as $key=>$val){
            foreach ($tmp as $k=>$child){
                if($val[$id] == $k){
                    $val['child'] = $child;
                    unset($tmp[$k]);
                }
            }
            $tree[$key] = $val;
        }
    
        foreach ($tree as $key=>$val){
            if(isset($val['child']) && count($tmp)){
                $tree[$key]['child'] = $this->handler($val['child'],$tmp);
            }
        }
    
        return $tree;
    }
    
    /**
     * 
     * @param string $name
     * @return array|0
     */
    public function findTreeByName($name=null){
        $returnTree = 0;
        if($name){
           foreach ($this->result as $node){
               if($node[$this->fields[2]] == $name){
                   $node['child'] = $this->leaf($node[$this->fields[0]]);
                   $returnTree = $node;
                   break;
               }
           }
        }
        return $returnTree;
    }

    /**
     * 
     * @param array $tree 树形结构
     */
    public function setTree($tree){
        $this->tree = $tree;
    }

    /**
     * 正向递归
     */
    private function recur_p($arr) {
        foreach ($arr as $v) {
            $this->arr[] = $v[$this->fields[0]];
            if (isset($v['child']))
	            $this->recur_p($v['child']);
        }
    }
    /**
     * 菜单 多维数组
     *
     * @param integer $id 分类id
     * @return array 返回分支，默认返回整个树
     */
    public function leaf($id = 0) {
        if($id){
            $tree = $this->_leaf($this->tree, $id);
            $tree = isset($tree['child']) ? $tree['child'] : NULL; 
            return $tree;
        }else {
        	foreach ($this->tree as &$c){
        		if(!isset($c['child'])){
        			$c['child'] = array();
        		}
        	}
            return $this->tree;
        }
    }
    
    
    private function _leaf($tree,$id){
        
        foreach ($tree as $c){
            if($c[$this->fields[0]] == $id){
                break;
            }elseif(isset($c['child'])) {
                $c = $this->_leaf($c['child'], $id);
                if($c && $c[$this->fields[0]] == $id){
                    break;
                }
            }
        }
        return $c;
    }

    /**
     * 获取指定项
     */
    public function getItem($id){
        return $id && array_key_exists($id, $this->result) ? $this->result[$id] : null;
    }
    
    /**
     * 导航 一维数组
     * 返回所有祖先
     * @param integer $id 分类id
     * @return array 返回单线分类直到顶级分类
     */
    public function navi($id) {
    	if(!isset($this->result[$id])){
    		return array();
    	}
        $parent = $this->result[$id];
        $arr = array();
        $arr[$parent[$this->fields[0]]] = $parent;
        while ($parent = $this->getItem($parent[$this->fields[1]])){
            $arr[$parent[$this->fields[0]]] = $parent;
        }
        return array_reverse($arr);
    }
    
    /**
     * 散落 一维数组
     * 返回所有子孙
     * @param integer $id 分类id
     * @return array 返回leaf下所有分类id
     */
    public function leafid($id) {
        $this->arr = null;
        $this->arr[] = $id;
        $this->recur_p($this->leaf($id));
        return $this->arr;
    }
    
    /**
     * 根据级别和格式输出分类名
     * @param int $id
     * @param string $space
     * @param string $str
     * @param string $lhtml 比如 <input type="radio" name="cid" />或<option>
     * @param string $rhtml 比如 </option>
     * @return string
     */
    public function getLevelName($id,$str='|',$space='- - ',$lhtml='',$rhtml=''){
        $item = $this->result[$id];
        
        if(!isset($item['level'])){
            $item['level'] = count($this->navi($id));
        }
        for ($i=1;$i<$item['level'];$i++){
            $str .= $space;
        }
        $str = $str == '|' ? '' : $str;
        return $str.$lhtml.$item[$this->fields[2]].$rhtml;
    }
    
    /**
     * 获取id levaeName对
     *
     * 可适用于列表以及下拉列表
     *
     * @return array
     */
    public function getValueOptions($tree=NULL)
    {
    	$tree = $tree ? $tree : $this->tree; 
    	$returnList = array();
    	foreach($tree as $item) {
    
    		$tmp = array();
    		$item[$this->fields[2]] = $this->getLevelName($item[$this->fields[0]]);
    		$tmp[$item[$this->fields[0]]] = array_unset_key($item,'child');
    		$returnList = my_array_merge($returnList,$tmp);
    		if(isset($item['child']) && $item['child']){
    			$childList = $this->getValueOptions($item['child']);
    			$returnList = my_array_merge($returnList,$childList);
    		}
    	}
   
    	return $returnList;
    }

}