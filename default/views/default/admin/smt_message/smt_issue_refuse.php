<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-12-10
 * Time: 14:14
 */
?>


<div class="container">
    <div class="row">   <span class="col-sm-3"> <font class="red">*</font> 建议的退款金额:</span> <input  name="type" type="radio" value="1"/> 全部退款</div>
    <div class="row">   <span class="col-sm-3"></span><input  name="type" type="radio" value="2"/>  部分退款 </div>
    <div class="row">   <span class="col-sm-3"></span>   <input name="type" type="radio" value="3" checked/> 不退款 </div>
    <div class="row"> <span class="col-sm-3"><font class="red">*</font>  自动订单留言</span><input name="automsg" id="automsg" type="checkbox" checked><span class="red">  (自动将原因描述以订单留言的形式发送给客户） </span></div>
    <div class="row"> <span class="col-sm-3"><font class="red">*</font>  只发送订单留言</span><input name="onlymsg" id="onlymsg" type="checkbox" ><span class="red">  (勾选后不会进行拒绝操作 只会将原因描述的内容 以订单留言的形式发送给客户） </span></div>


    <div class="row"> <span class="col-sm-3"><font class="red">*</font>  拒绝买家纠纷方案的原因描述：<font style="color: #CC0000">(长度不能超过200字符)</font></span>
    <textarea  name="content" id="content" style="width: 548px; height: 323px; resize: none"   ></textarea>
    </div>






</div>