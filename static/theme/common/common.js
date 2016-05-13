// JavaScript Document
if (!(window.console && console.log)) {
  (function() {
    var noop = function() {};
    var methods = ['assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error', 'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log', 'markTimeline', 'profile', 'profileEnd', 'markTimeline', 'table', 'time', 'timeEnd', 'timeStamp', 'trace', 'warn'];
    var length = methods.length;
    var console = window.console = {};
    while (length--) {
        console[methods[length]] = noop;
    }
  }());
}

function sprintf()
{
    var arg = arguments,
        str = arg[0] || '',
        i, n;
    for (i = 1, n = arg.length; i < n; i++) {
        str = str.replace(/%s/, arg[i]);
    }
    return str;
}


(function($) {
    /**
     * 无限级联动
     * 参数示例1: 
     * 		url		:{"0":{"1":["1","Program"]},"1":{"4":["4","PHP"],"5":["5","Database"]},"5":{"6":["6","Mysql"],"7":["7","Oracle"]},"6":{"8":["8","Mysql5.0"]}}
     * 		selected:'7'
     * 参数示例2: 
     * 		url		:'/a/a_region/json'
     * 
     */
    $.fn.linkagesel = function(options){
        var defaults = {
        				url			: '/common/region/json',
        				root		: 1,
        				emptyText	: '请选择',
        				selected	: '',
        				district	: 'district',
        				district_id	: null
    				    };
        options = $.extend(defaults, options);
        options.district_id = options.district_id || options.district;
        
        var data;
        var selected = [];
        
        var init=function(root){
        	beforeInit();
        	
        	var flag = root != null ? true : false;
        	root 	 = flag         ? root : options.root; 
        	
        	if(data[root] != null){
        		
        		var $this = $(this);
        		
        		var sel_html = '<select class="width-20" id="sel_'+root+'">';
        		sel_html    += '<option selected value="">'+options.emptyText+'</option>'
        		
        		$.each(data[root],function(i,v){
        			sel_html += '<option value="'+v[0]+'">'+v[1]+'</option>';
        		});
        		
        		sel_html 	+= '</select>';
        		
        		if(flag){
        			$this.nextAll().remove();
        			$this.after(' &nbsp;' + sel_html);
        		}else{
        			sel_html = '<input id="'+options.district_id+'" type="hidden" name="'+options.district+'" />'+sel_html;
        			$this.html(sel_html);
        		}
        		
        		$('#sel_'+root).change(function(){
        			$('#'+options.district_id).val(this.value);
        			init.call(this,this.value);
        		});
        		
        		if(selected[root] != null){
        			$("#sel_"+root).val(selected[root]).change();
        		}
        		
        	}else if(flag){
        		$(this).nextAll().remove();
        	}           	
        		
        };
        
        
        var findVbyK = function(k){
        	var value;
        	$.each(data,function(i,v){
				if(v[k] != null ){
					value = v[k];
					value.push(i);
					return false;
				}
			});
        	return value;
        };
        
        var beforeInit = function(k){
        	if(options.selected > 0){
        		
				var tmp = findVbyK(options.selected);
				selected[tmp[2]] = tmp[0];
				
				while(tmp[2] > 0){
					tmp = findVbyK(tmp[2]);
					selected[tmp[2]] = tmp[0];
				}
				
				delete(options.selected);
			}
        };
        
        // 设置省市json数据
        if(typeof(options.url) == "string"){
			$.getJSON(options.url,$.proxy(function(json){
				data = json;
				init.call(this);
				
			}, this));
		}else if(typeof(options.url) == 'object'){
			
			data = options.url;
			init.call(this);
		};
		
    };//无限级联动结束

    //ajax提交请求
	$.fn.xbpost = function(options){
        var defaults = {
                confirm:false,                                  //是否需要确认提示
                url:'',                                         //提交的url，如果为空会找父节点的form的action
                form:null,
                msg: '您确定要提交请求？',                           //提示消息
                evt: 'click',                                   //事件类型
                refresh: false,                                 //是否需要刷新
                before: function(){return true;},               //提交前调用的检查方法
                success: function(resp){
                    if(typeof(resp) != 'object' || resp.info == null){
                        resp = {info:'保存成功'};
                    }
                    alert(resp.info)
                    if(options.refresh){
                    	var url = resp.data != ''? resp.data : location.href;
                        window.location.href=url;
                    }
                } //成功后调用的回调函数
            };
        
        options = $.extend(defaults, options);
        
        var switchObj = function(obj){
            var tag = obj.get(0).tagName;
            var type = obj.attr('type');
            switch(true){
                case tag == 'BUTTON':
                    if(obj.attr('disabled')){
                        obj.text(obj.attr('bak_text'));
                        obj.removeAttr('bak_text');
                        obj.attr('disabled',false);
                    }else{
                        obj.attr('bak_text',obj.text());
                        obj.text('请等待...');
                        obj.attr('disabled',true);
                    }
                break;
                case tag == 'INPUT' && type == 'submit':
                case tag == 'INPUT' && type == 'button':
                    if(obj.attr('disabled')){
                        obj.val(obj.attr('bak_val'));
                        obj.removeAttr('bak_val');
                        obj.attr('disabled',false);
                    }else{
                        obj.attr('bak_val',obj.val());
                        obj.val('请等待...');
                        obj.attr('disabled',true);
                    }
                break;
                case tag == 'A':
                    options.url = obj.attr('href');
                
                default:
                    if(obj.attr('disabled')){
                        obj.attr('disabled',false);
                    }else{
                        obj.attr('disabled',true);
                    }
                break;
            }
        }
        
        var ajaxPost = function(obj){
            options.form = typeof(options.form)=='string' ? $("#"+options.form) : options.form;
            
            var form = options.form || obj.parents('form');
            options.url = options.url || form.attr('action');
            
            var data = options.evt == 'change' ? obj.serializeArray() : form.serializeArray();
            
            switchObj(obj);
            
            if(options.url == null){
                alert('没有提交地址！');
                switchObj(obj);
                return false;
            }
            
            $.ajax({
                type: 'POST',
                url: options.url,
                data: data,
                success: function(resp){
                    resp.el = obj;
                    options.success(resp); 
                    switchObj(obj);
                },
                dataType: 'json',
                error:function(){
                    alert('出错误了，请重新再试！')
                    switchObj(obj);
                }
              });
            
        }
        
        if(!(/^click|change$/.test(options.evt))){
            alert('事件参数不正确！只允许click,change。');
        }
        
        return $(document).on(options.evt,this.selector,function(){
            var obj = $(this); 
            if(!options.before(obj)){
                return false;
            }
            if(options.confirm){
                confirm(
                    options.msg,
                    function(){
                        ajaxPost(obj)
                    },
                    function(){}
                );
            }else{
                ajaxPost(obj)
            }
            return false;
        })
    }
})(jQuery);

