<?php
/**
 * 从EXCEL导入
 */
?>

<table class="table">
    <colgroup>
        <col width="20%"/>
    </colgroup>
    <tbody>
        <tr>
            <td></td><td><p>请先下载<a href="<?php echo site_url('attachments/template/amzProductImageTemplate.xls')?>">导入模板文件</a></p></td>
        </tr>

        <tr>
            <td></td>
            <td>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="file" name="file" class="form-input"/>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-sm">导&nbsp;&nbsp;入</button>
                    </div>
                    <div class="form-group">
                        <p class="red">图片链接必须是以http://开头以jpg|jpeg|bmp|gif|png结尾，可带参数</p>
                    </div>
                    <div class="form-group">
                        <?php if ($success):?>
                        <p class="help-block blue">操作成功</p>
                        <?php endif;?>
                        <p class="help-block red"><?php echo $error;?></p>
                    </div>
                </form>
            </td>
        </tr>
    </tbody>
</table>
