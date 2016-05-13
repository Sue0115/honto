<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-01-04
 * Time: 09:49
 */
?>

<style>
    .tishi {
        cursor: pointer;
    }

    .yeanse {
        background-color: #00ffff;
        cursor: pointer;
    }
</style>

<div class="row" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">导入结果</h3>
        <div class="table-header">&nbsp;</div>
        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">

                        <label class="">

                            &nbsp;&nbsp; &nbsp;&nbsp;

                            <a class="btn btn-primary btn-sm"
                               href="<?php echo admin_base_url("out_of_stock_sku/out_of_stock_manage/index"); ?>">返回导入页面</a>
                        </label>
                        <table class="table    dataTable " id="tbody_content">
                            <colgroup>
                                <col width="50%">
                                <col width="50%"/>
                            </colgroup>
                            <thead>
                            <tr>
                                <th class="center">SKU</th>
                                <th class="center">导入结果</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($data):
                                foreach ($data as $key=>$list):
                                    ?>
                                    <tr class="center">

                                        <td>
                                            <?php echo $key; ?>
                                        </td>
                                        <td>
                                            <?php echo  $status[$list]; ?>
                                        </td>
                                    </tr>
                                <?php
                                endforeach;
                            endif;
                            ?>
                            </tbody>
                        </table>

                    </div>
                </div>


            </div>
        </div>
    </div>
</div>