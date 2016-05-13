<meta charset="utf-8"/>
<form action="<?php echo admin_base_url('publish/waterMark/imgwater');?>" method="post" enctype="multipart/form-data" name="form1" id="form1">
  <table width="486" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td width="498" height="28" background="image/top.gif"><div align="center" class="style2">上传图片增加水印</div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><div align="right">
        <table width="426" border="0" align="center" cellpadding="0" cellspacing="0">
        <!--   <tr>
            <td width="100" height="32" nowrap="nowrap">选择图片：</td>
            <td width="362"><input name="upfile" type="file" id="upfile" size="40" /></td>

          </tr> -->
         <tr>
         <td width="100">选择水印:</td>
         <td width="362"><input name="water11" type="file"  size="40" /><input name="imgsrc" type="hidden" value="<?php echo $imgsrc;?>" /></td>
         </tr>
          <tr>
            <td height="27" colspan="2"><div align="center"><br>
                <input type="submit" name="Submit" value="提交" />
&nbsp;&nbsp;
<input type="reset" name="Submit" value="重置" />
</div></td>
          </tr>
        </table>
          </div>
        <div align="center"></div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr><INPUT TYPE="hidden" name='action' value='doup'><INPUT TYPE="hidden" name='token_id' value='<?php echo $token_id;?>'>
    <tr>
      <td height="28" background="image/top.gif">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
<input type="hidden" value="<?php if($newimg){echo $newimg;}?>" id="myimg" />
<?php if($imgdata){echo $imgdata;}?>
