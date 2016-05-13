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
        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <table class="table    dataTable " id="tbody_content">
                            <colgroup>
                                <col width="10%">
                                <col width="50%"/>
                                <col width="40%"/>
                            </colgroup>
                            <thead>
                            <tr>
                                <th class="center">操作人</th>
                                <th class="center">事件</th>
                                <th class="center">时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($data):
                                foreach ($data as $key=>$list):
                                    ?>
                                    <tr class="center">

                                        <td>
                                            <?php echo $user[$list['uers_id']]; ?>
                                        </td>

                                        <td>
                                            <?php echo $list['note']; ?>
                                        </td>
                                        <td>
                                            <?php echo  $list['export_time']; ?>
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