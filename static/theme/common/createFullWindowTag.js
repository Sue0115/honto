// JavaScript Document
//获取不同浏览器的高度


function getScrollTop1() {
    if ('pageYOffset' in window) {
        return window.pageYOffset;

    } else if (document.compatMode === "BackCompat") {
        return document.body.scrollTop;

    } else {
        return document.documentElement.scrollTop;

    }

}

/*
	*提示等待加载完成再显示结果页面 add by chenwei 2014.8.6
*/
function responseCreateFullWindowTag(url, w, h, func) {
    var display_height = document.documentElement.scrollHeight;
    var browser_width = document.documentElement.clientWidth;
    var browser_height = document.documentElement.clientHeight;
    var h1 = document.documentElement.offsetHeight;
	
    if(h1<browser_height){//高度小于浏览器的高度
    	h1=browser_height;
    }
    if ( h1 < h ) h1 = h + 100;
	
    var new_top = getScrollTop1();
    var fullWindowBgDiv = document.getElementById('fullWindowBg');
    var fullWindowContentBox = document.getElementById('fullWindowContentBox');

    if (fullWindowBgDiv || fullWindowContentBox) {
        clearFullWindowTag();

    }

    var list_div = document.createElement('div');
	
    list_div.id = 'fullWindowContentBox';
    list_div.className = 'fullWindowContentBox';
    list_div.innerHTML = '<span style="font-size:24px;" id="sp1"><img src="/images/onload1.gif">请勿关闭窗口，请耐心等待，数据正在加载中···</span><iframe id="ifr1" src="' + url + '" width="' + (w - 15) + '" height="' + (h - 15) + '" frameborder="0"></iframe>';
    list_div.style.width = w + 'px';
    list_div.style.height = h + 'px';
    list_div.style.left = (browser_width - w) / 2 + 'px';
    list_div.style.top = new_top + (browser_height - h) / 2 + 'px';
    document.body.appendChild(list_div);
	//加载完成后，隐藏提示信息
	document.getElementById("ifr1").onload=function(){  
		document.getElementById("sp1").style.display = "none";
	} 
	

    var closeButton = document.createElement('div');
    closeButton.id = 'closeButton';
    closeButton.className = 'closeButton';
    closeButton.innerHTML = '<img src="/static/theme/common/cwImg/closeTags.png" onclick="clearFullWindowTag(' + func + ');">';
    closeButton.style.left = (browser_width - w) / 2 + w + 10 + 'px';
    closeButton.style.top = new_top + (browser_height - h) / 2 - 10 + 'px';
    document.body.appendChild(closeButton);

    var bg_div = document.createElement('div');
    bg_div.id = 'fullWindowBg';
    bg_div.className = 'fullWindowBg';
    bg_div.style.width = browser_width + 'px';
    bg_div.style.height = h1 + 'px';
    document.body.appendChild(bg_div);
}

function createFullWindowTag(url, w, h, func) {
    var display_height = document.documentElement.scrollHeight;
    var browser_width = document.documentElement.clientWidth;
    var browser_height = document.documentElement.clientHeight;
    var h1 = document.documentElement.offsetHeight;
    if(h1<browser_height){//高度小于浏览器的高度
    	h1=browser_height;
    }
    if ( h1 < h ) h1 = h + 100;
    //var new_top = document.documentElement.scrollTop;
    var new_top = getScrollTop1();
    var fullWindowBgDiv = document.getElementById('fullWindowBg');
    var fullWindowContentBox = document.getElementById('fullWindowContentBox');

    if (fullWindowBgDiv || fullWindowContentBox) {
        clearFullWindowTag();

    }

    var list_div = document.createElement('div');
    list_div.id = 'fullWindowContentBox';
    list_div.className = 'fullWindowContentBox';
    list_div.innerHTML = '<iframe src="' + url + '" width="' + (w - 15) + '" height="' + (h - 15) + '" frameborder="0" id="myiframe"></iframe>';
    list_div.style.width = w + 'px';
    list_div.style.height = h + 'px';
    list_div.style.left = (browser_width - w) / 2 + 'px';
    list_div.style.top = new_top + (browser_height - h) / 2 + 'px';
    document.body.appendChild(list_div);

    var closeButton = document.createElement('div');
    closeButton.id = 'closeButton';
    closeButton.className = 'closeButton';
    //closeButton.innerHTML = '<img src="images/closeTags.png" onclick="if(!confirm(\'确定关闭窗口吗？\')){return false;}else{clearFullWindowTag();}">' ;
    closeButton.innerHTML = '<img src="/static/theme/common/cwImg/closeTags.png" onclick="clearFullWindowTag(' + func + ');">';
    closeButton.style.left = (browser_width - w) / 2 + w + 10 + 'px';
    closeButton.style.top = new_top + (browser_height - h) / 2 - 10 + 'px';
    document.body.appendChild(closeButton);

    var bg_div = document.createElement('div');
    bg_div.id = 'fullWindowBg';
    bg_div.className = 'fullWindowBg';
    bg_div.style.width = browser_width + 'px';
    bg_div.style.height = h1 + 'px';
    document.body.appendChild(bg_div);

}
function displayFullWindowTag(id, w, h) {
	var display_height = document.documentElement.scrollHeight;
    var browser_width = document.documentElement.clientWidth;
    var browser_height = document.documentElement.clientHeight;
    var h1 = document.documentElement.offsetHeight;
    if(h1<browser_height){//高度小于浏览器的高度
    	h1=browser_height;
    }
    if ( h1 < h ) h1 = h + 100;
    //var new_top = document.documentElement.scrollTop;
    var new_top = getScrollTop1();
    var fullWindowBgDiv = document.getElementById('fullWindowBg');
    var fullWindowContentBox = document.getElementById('fullWindowContentBox');

    if (fullWindowBgDiv || fullWindowContentBox) {
        clearFullWindowTag();

    }

    var list_div = document.createElement('div');
    list_div.id = 'fullWindowContentBox';
    list_div.className = 'fullWindowContentBox';
    
    var panel = document.getElementById(id);
    
    if(! panel){
    	return false;
    }
    
    var func = function(id){};
    
    list_div.innerHTML = panel.innerHTML;
    
    list_div.style.width = w + 'px';
    list_div.style.height = h + 'px';
    list_div.style.left = (browser_width - w) / 2 + 'px';
    list_div.style.top = new_top + (browser_height - h) / 2 + 'px';
    document.body.appendChild(list_div);

    var closeButton = document.createElement('div');
    closeButton.id = 'closeButton';
    closeButton.className = 'closeButton';
    //closeButton.innerHTML = '<img src="images/closeTags.png" onclick="if(!confirm(\'确定关闭窗口吗？\')){return false;}else{clearFullWindowTag();}">' ;
    closeButton.innerHTML = '<img src="/static/theme/common/cwImg/closeTags.png" onclick="clearFullWindowTag(' + func + ');">';
    closeButton.style.left = (browser_width - w) / 2 + w + 10 + 'px';
    closeButton.style.top = new_top + (browser_height - h) / 2 - 10 + 'px';
    document.body.appendChild(closeButton);

    var bg_div = document.createElement('div');
    bg_div.id = 'fullWindowBg';
    bg_div.className = 'fullWindowBg';
    bg_div.style.width = browser_width + 'px';
    bg_div.style.height = h1 + 'px';
    document.body.appendChild(bg_div);
}

function clearFullWindowTag( f ) {
    var fullWindowBgDiv = document.getElementById('fullWindowBg');
    var closeButton = document.getElementById('closeButton');
    var fullWindowContentBox = document.getElementById('fullWindowContentBox');
    var thisli = $('#se-water-add').find("li").eq(index);
     var url =
document.getElementById('myiframe').contentWindow.document.getElementById('myimg').value;
    if(url){       
        var waterdiv = $('#se-water-add');
        var myli = '<li><div><img src="' +url + '" width="100" height="100" style="border: 0px;"><input type="hidden" name="imgLists[]" value="' + url + '" /><a href="javascript: void(0);" class="pic-del">删除</a>&nbsp;<a href="javascript: void(0);" class="pic-add-water">水印</a></div></li>';
        thisli.after(myli);
        thisli.remove();
    }

    document.body.removeChild(fullWindowBgDiv);
    document.body.removeChild(closeButton);
    document.body.removeChild(fullWindowContentBox);
    var ewe = document.getElementById('createwss');
    
    if ( typeof f != 'undefined' ) f();
    //document.body.style.overflow = 'auto';
}

function hiddenSelectTag() {}

function deleteProducts(e) {
    productLine = e.parentNode;
    box = productLine.parentNode;
    box.removeChild(productLine);

}

function addProducts() {
    var skuText = document.getElementsByName('sku[]');
    var i = skuText.length + 1;
    var str = 'SKU <input name="sku[]" type="text" id="web_orders_sku' + i + '" value="" size="6" onkeyup="show_object(' + i + ',this,\'sku\');" lang="require" title="SKU号必填" /> 物品名 <input name="nm_cn[]" type="text" id="web_orders_nm_cn' + i + '" value="" size="15" onkeyup="show_object(' + i + ',this,\'nm_cn\')" lang="require" title="物品名称必填" /> 数量 <input name="item_count[]" type="text" value="" size="5" lang="mustint_1" title="物品数量必填" /> 售价 <input name="item_price[]" type="text" value="" size="5" lang="mustint_0" title="物品售价必填" /> <input name="item_number[]" type="hidden" value="0" /> <input name="transactionID[]" type="hidden" value="0" /> <input name="orderlineitemid[]" type="hidden" value="0" /> <input name="token_id[]" type="hidden" value="0" /> <b onclick="if(confirm(\'确定要删除这个物品吗？\')){deleteProducts(this);}"><font style="cursor:pointer; color:#FF0000; font-family:\'宋体\'; font-size:14px;">×</font></b>';

    box = document.getElementById('productsListBox');
    var new_products = document.createElement('div');
    new_products.className = 'transactionRecord';
    new_products.innerHTML = str;
    box.appendChild(new_products);

}