<?php
/**
 * 批量刊登结果显示界面
 */
?>
<div style="padding: 10px;">
    <?php
    if ($return) {
        foreach ($return as $row) {
            if ($row['status']) {
                echo '<div class="alert-success">' . $row['info'] . '</div>';
            } else {
                echo '<div class="alert-danger">' . $row['info'] . '</div>';
            }
        }
    }
    ?>
</div>