<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-09-07
 * Time: 16:10
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('PRC');
set_time_limit(0); //页面不过时
ini_set('memory_limit', '2024M');
header('Content-Type: text/html; Charset=utf-8');

class Export_order_dotask extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model(
            array(
                'print/orders_model', 'shipment_model', 'sangelfine_warehouse_model',
                'orders_type_model', 'products/products_data_model', 'export_order_task_model', 'kingdee_currency_model', 'orders_type_model'
            )
        );
        $this->load->library('phpexcel/PHPExcel');
        $this->model = $this->orders_model;
    }

    public function  doTask($order_type = '', $warehouse = '', $date1 = '', $date2 = '')
    {


        $order_type_array = array();
        $warehouse_array = array();
        if (empty($order_type)) {
            $option =array();
          $result =  $this->orders_type_model->getAll2Array($option);
            foreach($result as $v)
            {
                $order_type_array[]=$v['typeID'];
            }

        } else {
            $order_type_array[] = $order_type;
        }

        if (empty($warehouse)) {
            $warehouse_array = array(1000, 1025);
        } else {
            $warehouse_array[] = $warehouse;
        }

        foreach ($order_type_array as $order_type) {
            foreach ($warehouse_array as $warehouse) {


                $where = ' and o.orders_status=5';

                if (!empty($order_type)) {
                    $where = $where . ' and o.orders_type=' . $order_type;
                }

                if (!empty($warehouse)) {
                    $where = $where . ' and o.orders_warehouse_id=' . $warehouse;
                }

                $date1 = !empty($date1) ? date("Y-m-d", strtotime($date1)).' 00:00:00' : date("Y-m-d", strtotime('-15 day')).' 00:00:00';

             //   $date1 = $date1 . ' 00:00:00';

                $date2 = !empty($date2) ? date("Y-m-d", strtotime($date2)).' 23:59:59': date("Y-m-d", strtotime('-15 day')).' 23:59:59';

             //   $date2 = $date2 . ' 23:59:59';

                $where = $where . ' and o.orders_shipping_time>=' . "'" . $date1 . "'";
                $where = $where . ' and o.orders_shipping_time<' . "'" . $date2 . "'";


                if ($date2 < $date1) {
                    echo "时间范围有问题,第一个时间要比第二个时间小";
                    exit;
                }


                $re = $this->kingdee_currency_model->exportOrder($where);


                if (empty($re)) {
                    echo "未找到符合条件订单";
                    echo '</br>';
                    continue;
                }


                $currencyoptin = array();

                $crrencyresult = $this->kingdee_currency_model->getAll2Array($currencyoptin);


                $crrencylastinfo = array();
                foreach ($crrencyresult as $crrency) {
                    $crrencylastinfo[$crrency['currency_code']]['currency_name'] = $crrency['currency_name'];
                    $crrencylastinfo[$crrency['currency_code']]['currency_value'] = $crrency['currency_value'];
                }
                unset($crrencyresult);

                $order_type_option = array();
                $ordertypeinfo = $this->orders_type_model->getAll2Array($order_type_option);
                $ordertypelastinfo = array();
                foreach ($ordertypeinfo as $type) {
                    $ordertypelastinfo[$type['typeID']] = $type['typeName'];
                }
                unset($ordertypeinfo);


                $phpExcel = new PHPExcel();
                $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                $cacheSettings = array('memoryCacheSize' => '512MB');
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);


                //设置标题
                $phpExcel->getProperties()->setCreator("Moonar")->setLastModifiedBy("Moonar");
                $phpExcel->setActiveSheetIndex(0); //切换到新创建的工作表
                $one_sheet = $phpExcel->getActiveSheet(0);
                $one_sheet->setTitle('Page1');


                //创建第二个工作簿
                $msgWorkSheet = new PHPExcel_Worksheet($phpExcel, 'sheet2'); //创建一个工作表
                $phpExcel->addSheet($msgWorkSheet); //插入工作表
                $phpExcel->setActiveSheetIndex(1); //切换到新创建的工作表
                $two_sheet = $phpExcel->getActiveSheet(1);
                $two_sheet->setTitle('Page2');

                //设置表头结束

                $i = 2;
                $j = 2;
                $k = 1;


                $check_order = array();
                $resultinfo = array();

                foreach ($re as $key => $v) {


                    $order_shipping_type = '赊销';
                    $order_shipping_FID = 'FXF02';
                    $order_shipping_FTypeID = '101';
                    $addzero = "00";


                    if ($v['orders_type'] > 9) {
                        $addzero = "0";
                    }
                    $days = date('d', strtotime($v['orders_shipping_time']));
                    if ($days > 15) {
                        $order_shipping_type = '分期收款销售';
                        $order_shipping_FID = 'FXF03';
                        $order_shipping_FTypeID = '102';
                    }
                    $shipping_time = date('Y-m-d', strtotime($v['orders_shipping_time']));

                    if (!isset($check_order[$v['erp_orders_id']])) // 不存在先插入page1 再插入page2
                    {


                        $resultinfo['Page1'][$i] = array(
                            $shipping_time,
                            $shipping_time,
                            "Administrator",
                            $v['erp_orders_id'],
                            "Administrator",
                            "",
                            "",
                            "81",
                            "1128",
                            "",
                            "",
                            $order_shipping_FID,
                            $order_shipping_type,
                            $order_shipping_FTypeID,
                            "",
                            "",
                            "",
                            $v['currency_type'],
                            $crrencylastinfo[$v['currency_type']]['currency_name'],
                            $addzero . $v['orders_type'],
                            $ordertypelastinfo[$v['orders_type']],
                            "",
                            "",
                            "",
                            $addzero . $v['orders_type'],
                            $ordertypelastinfo[$v['orders_type']] . "运营部",
                            $addzero . $v['orders_type'],
                            $ordertypelastinfo[$v['orders_type']] . "客服",
                            "*",
                            "*",
                            "01",
                            "公司汇率",
                            $crrencylastinfo[$v['currency_type']]['currency_value'],
                            "",
                            "",
                            "",
                            "0",
                            "0",
                            "0",
                            $shipping_time,
                            "",
                            "1",
                            "购销",
                            "997",
                            "",
                            "",
                            "2",
                            "",
                            "",
                            "",
                            "0",
                            "STD",
                            "标准"
                        );

                        $i++;


                        $check_order[$v['erp_orders_id']] = $v['erp_orders_id'];


                        $resultinfo['Page2'][$j] = array(
                            $k,
                            $v['erp_orders_id'],
                            "81",
                            "",
                            "",
                            "运费",
                            "YF",
                            "",
                            "",
                            "*",
                            "pcs",
                            "pcs",
                            $v['orders_ship_fee'],
                            "1",
                            "1",
                            "0",
                            "0",
                            "0",
                            "0",
                            "",
                            "",
                            "",
                            $shipping_time,
                            "",
                            $v['orders_ship_fee'],
                            "1",
                            "0",
                            $v['orders_ship_fee'],
                            "1",
                            "0",
                            "",
                            "",
                            "MTS",
                            "MTS计划模式",
                            "606",
                            "",
                            "0",
                            "*",
                            "*",
                            "0",
                            $shipping_time,
                            "",
                            "",
                            "",
                            "0",
                            "0",
                            "0",
                            "",
                            "0",
                            "0",
                            "0",
                            "",
                            "0",
                            "0",
                            "0",
                            "0",
                            "0",
                            $v['orders_ship_fee'],
                            "0",
                            "0",
                            "0",
                            "",
                            "",
                            "",
                            "",
                            "",
                            "",
                            "",
                            "0",
                            "0"
                        );


                        $j++;
                        $k++;

                        $resultinfo['Page2'][$j] = array(
                            $k,
                            $v['erp_orders_id'],
                            "81",
                            "",
                            "",
                            $v['products_name_cn'],
                            $v['orders_sku'],
                            "",
                            "",
                            "*",
                            "pcs",
                            "pcs",
                            $v['item_count'],
                            $v['item_price'],
                            $v['item_price'],
                            "0",
                            "0",
                            "0",
                            "0",
                            "",
                            "",
                            "",
                            $shipping_time,
                            "",
                            $v['item_count'] * $v['item_price'],
                            $v['item_price'],
                            "0",
                            $v['item_count'] * $v['item_price'],

                            $v['item_count'],
                            "0",
                            "",
                            "",
                            "MTS",
                            "MTS计划模式",
                            "606",
                            "",
                            "0",
                            "*",
                            "*",
                            "0",
                            $shipping_time,
                            "",
                            "",
                            "",
                            "0",
                            "0",
                            "0",
                            "",
                            "0",
                            "0",
                            "0",
                            "",
                            "0",
                            "0",
                            "0",
                            "0",
                            "0",
                            $v['item_count'] * $v['item_price'],
                            "0",
                            "0",
                            "0",
                            "",
                            "",
                            "",
                            "",
                            "",
                            "",
                            "",
                            "0",
                            "0"
                        );
                        $j++;
                        $k++;
                        //再把信息插入page2
                    } else  //存在了。。。 只用在page2插入sku信息
                    {
                        $resultinfo['Page2'][$j] = array(
                            $k,
                            $v['erp_orders_id'],
                            "81",
                            "",
                            "",
                            $v['products_name_cn'],
                            $v['orders_sku'],
                            "",
                            "",
                            "*",
                            "pcs",
                            "pcs",
                            $v['item_count'],
                            $v['item_price'],
                            $v['item_price'],
                            "0",
                            "0",
                            "0",
                            "0",
                            "",
                            "",
                            "",
                            $shipping_time,
                            "",
                            $v['item_count'] * $v['item_price'],
                            $v['item_price'],
                            "0",
                            $v['item_count'] * $v['item_price'],

                            $v['item_count'],
                            "0",
                            "",
                            "",
                            "MTS",
                            "MTS计划模式",
                            "606",
                            "",
                            "0",
                            "*",
                            "*",
                            "0",
                            $shipping_time,
                            "",
                            "",
                            "",
                            "0",
                            "0",
                            "0",
                            "",
                            "0",
                            "0",
                            "0",
                            "",
                            "0",
                            "0",
                            "0",
                            "0",
                            "0",
                            $v['item_count'] * $v['item_price'],
                            "0",
                            "0",
                            "0",
                            "",
                            "",
                            "",
                            "",
                            "",
                            "",
                            "",
                            "0",
                            "0"
                        );
                        $j++;
                        $k++;
                    }
                }
                $one_sheet->fromArray($resultinfo['Page1']);
                $two_sheet->fromArray($resultinfo['Page2']);


                $name = '';
                $filename = '';
                if (!empty($order_type)) {
                    //   $name= $name. ((strtolower(substr(PHP_OS,0,3))=='win') ? mb_convert_encoding($ordertypelastinfo[$order_type],'gbk','UTF-8') : $ordertypelastinfo[$order_type]).'-';

                    $filename = $filename . $order_type . '-';
                    $name = $name . $ordertypelastinfo[$order_type] . '-';

                }

                if (!empty($warehouse)) {
                    if ($warehouse == 1000) {
                        $filename = $filename . $warehouse . '-';
                        $name = $name . '深圳仓' . '-';
                    }

                    if ($warehouse == 1025) {
                        $filename = $filename . $warehouse . '-';
                        $name = $name . '义乌仓' . '-';
                    }

                }
                $name = $name . date('Y-m-d', strtotime($date1));
                $filename = $filename . date('Y-m-d', strtotime($date1));
                //$filename = $name;

                if (!file_exists('attachments/export_excel_task')) {//如果目录下不存在改sku文件夹
                    mkdir('attachments/export_excel_task', 0777, true); //创建文件夹并授权
                }

                $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
                $objWriter->save('attachments/export_excel_task/' . $filename . '.xlsx');

                $add_array = array();
                $add_array['file_name'] = $name;
                $add_array['order_type'] = $order_type;
                $add_array['warehouse_id'] = $warehouse;
                $add_array['order_from_date'] = $date1;
                $add_array['order_to_date'] = $date2;
                $add_array['file_url'] = 'attachments/export_excel_task/' . $filename . '.xlsx';
                $add_array['creat_time'] = date('Y-m-d H:i:s', time());
                $add_array['is_download'] = 0;

                $this->export_order_task_model->add($add_array);
            }
        }
    }
}