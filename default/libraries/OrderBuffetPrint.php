<?php 
/*
	* 自助打印 通用类
	* 
*/
class orderBuffetPrint{

	public function __construct() {

	}
	
	//通用接口
	public function ordersBuffetPrintTemplate($shipmentId,$pageSize){
		
		//订单信息
		$ordersList = $this->_ordersManage->getOrdersListToPrint($shipmentId, $pageSize);
		if (!$ordersList) {
			return '没有已通过不欠货的订单了或者已通过不欠货的订单没有挂号码';	
		}
		
		//物流信息
		$shipmentInfo   = $this->_shipmentManage->getShipmentInfo($shipmentId);
		$sql     = "SELECT template_url FROM erp_printing_template WHERE id = ".$shipmentInfo['shipment_template']." AND isOpen = 1";
		$sqlArr	 = $this->_db->getOne($sql);
		if(empty($sqlArr['template_url'])){
			return "此物流未找到对应的打印模板！请联系IT部门帮忙添加！";
		}
                
		$method = $sqlArr['template_url'];//方法名称
		return $this->$method($ordersList,$shipmentInfo);	
	}
	
	
	
	
	
	//MDD香港挂号面单 国家分区
	public function mddHkRegistrationCoutry(){
		$reArr = array();
		//1区
		$oneStr = "阿富汗,孟加拉国,不丹,印度尼西亚,印度,柬埔寨,朝鲜,韩国,老挝,斯里兰卡,缅甸,马尔代夫,马来西亚,尼泊尔,菲律宾,巴基斯坦,新加坡,泰国,东帝汶,台湾,越南,圣尤斯塔提马斯岛,中国";
		
		//2区
		$twoStr = "安道尔,阿拉伯联合酋长国,安提瓜及巴布达,安圭拉岛,亚美尼亚,奥地利,澳大利亚,阿鲁巴岛,阿塞拜疆,巴巴多斯,比利时,百慕大,巴哈马,布维岛,伯利兹,加拿大,瑞士,哥斯达黎加,古巴,圣诞岛,塞浦路斯,德国,丹麦,多米尼加共合国,西撒哈拉,西班牙,芬兰,斐济,法鲁群岛,法国,法属美特罗波利坦,格林纳达,法属圭亚那,直布罗陀,瓜德罗普,赤道几内亚,希腊,危地马拉,关岛,洪都拉斯,海地,加那利群岛,爱尔兰,以色列,伊拉克,伊朗,冰岛,意大利,牙买加,约旦,日本,吉尔吉斯斯坦,基利巴斯共和国,科威特,开曼群岛,哈萨克斯坦,黎巴嫩,圣卢西亚,卢森堡,马绍尔群岛,蒙古,澳门,蒙特塞拉岛,马尔他,墨西哥,新喀里多尼亚,尼加拉瓜,荷兰,挪威,新西兰,阿曼,巴拿马,塔希堤岛(法属波利尼西亚),巴布亚新几内亚,圣皮埃尔和密克隆群岛,波多黎各,葡萄牙,卡塔尔,沙特阿拉伯,所罗门群岛,瑞典,圣马力诺,萨尔瓦多,特克斯和凯科斯群岛,法属南部领土,塔吉克斯坦,托克劳,土库曼斯坦,汤加,土耳其,图瓦卢,美国本土外小岛屿,美国,乌兹别克斯坦,梵蒂冈,瓦努阿图,瓦利斯群岛和富图纳群岛,西萨摩亚,阿森松,北非西班牙属土,巴利阿里群岛,也门阿拉伯共合国,马约特,圣基茨,英国,库克群岛,加罗林群岛,瑙鲁共和国,叙利亚,特立尼达和多巴哥,圣赫勒拿,密克罗尼西亚,列支敦士登,美属维尔京群岛,圣文森特和格林纳丁斯岛,南乔治亚岛和南桑威奇群岛,马提尼克岛,巴林,纽埃岛,格陵兰,诺褔克岛";
		
		//3区
		$threeStr = "阿尔巴尼亚,荷属安的列斯群岛,阿根廷,波斯尼亚-黑塞哥维那共和国,布基纳法索,保加利亚,布隆迪,贝宁,波利维亚,巴西,博茨瓦纳,白俄罗斯,中非共和国,智利,喀麦隆,哥伦比亚,佛得角群岛,捷克共和国,阿尔及利亚,厄瓜多尔,爱沙尼亚,埃及,厄里特立亚,埃塞俄比亚,格鲁吉亚,冈比亚,几内亚比绍,圭亚那,克罗地亚,匈牙利,肯尼亚,科摩罗,利比里亚,莱索托,立陶宛,拉脱维亚,利比亚,摩洛哥,摩纳哥,摩尔多瓦,黑山共和国,马达加斯加,马其顿,马里,毛里塔尼亚,毛里求斯,马拉维,莫桑比克,纳米比亚,尼日尔,尼日利亚,秘鲁,波兰,巴拉圭,罗马尼亚,卢旺达,塞舌尔,苏丹,斯洛文尼亚,斯洛伐克,塞内加尔,索马里,苏里南,圣多美和普林西比,斯威士兰,乍得,多哥,坦桑尼亚,乌克兰,乌干达,乌拉圭,委内瑞拉,南非,赞比亚,津巴布韦,留尼汪岛,吉布提,塞尔维亚共和国,刚果,福克兰群岛,刚果民主共和国,科特迪瓦共和国,加纳,突尼斯,俄罗斯,安哥拉,塞拉里昂";
		
		$reOneArr   = explode(',',$oneStr);
		$reTwoArr   = explode(',',$twoStr);
		$reThreeArr = explode(',',$threeStr);
		$reArr["one"] = $reOneArr;
		$reArr["two"] = $reTwoArr;
		$reArr["three"] = $reThreeArr;
		return $reArr;
	}
	
	//MDD香港平邮面单 国家分区
	public function mddHkPostCoutry(){
		$reArr = array();
		//1区
		$oneStr = "阿富汗,孟加拉国,文莱,不丹,印度尼西亚,印度,柬埔寨,朝鲜,韩国,老挝,斯里兰卡,缅甸,马尔代夫,马来西亚,尼泊尔,菲律宾,巴基斯坦,新加坡,泰国,东帝汶,台湾,越南";
		
		//2区
		$twoStr = "安道尔,阿拉伯联合酋长国,安提瓜及巴布达,安圭拉岛,阿尔巴尼亚,亚美尼亚,荷属安的列斯群岛,阿根廷,奥地利,澳大利亚,阿塞拜疆,波斯尼亚-黑塞哥维那共和国,巴巴多斯,比利时,布基纳法索,保加利亚,布隆迪,贝宁,百慕大,波利维亚,巴西,巴哈马,博茨瓦纳,白俄罗斯,伯利兹,加拿大,科科斯群岛,中非共和国,瑞士,智利,喀麦隆,哥伦比亚,哥斯达黎加,古巴,佛得角群岛,圣诞岛,塞浦路斯,捷克共和国,德国,丹麦,多米尼克,多米尼加共合国,阿尔及利亚,厄瓜多尔,爱沙尼亚,埃及,厄里特立亚,西班牙,埃塞俄比亚,芬兰,斐济,法鲁群岛,法国,加蓬,格林纳达,格鲁吉亚,法属圭亚那,直布罗陀,冈比亚,几内亚,瓜德罗普,赤道几内亚,希腊,危地马拉,几内亚比绍,圭亚那,洪都拉斯,克罗地亚,海地,匈牙利,加那利群岛,爱尔兰,以色列,伊拉克,伊朗,冰岛,意大利,牙买加,约旦,日本,肯尼亚,吉尔吉斯斯坦,基利巴斯共和国,科摩罗,科威特,开曼群岛,哈萨克斯坦,黎巴嫩,圣卢西亚,利比里亚,莱索托,立陶宛,卢森堡,拉脱维亚,利比亚,摩洛哥,摩纳哥,摩尔多瓦,黑山共和国,马达加斯加,马绍尔群岛,马其顿,马里,蒙古,毛里塔尼亚,蒙特塞拉岛,马尔他,毛里求斯,马拉维,墨西哥,莫桑比克,纳米比亚,新喀里多尼亚,尼日尔,尼日利亚,尼加拉瓜,荷兰,挪威,新西兰,阿曼,巴拿马,秘鲁,塔希堤岛(法属波利尼西亚),巴布亚新几内亚,波兰,圣皮埃尔和密克隆群岛,波多黎各,葡萄牙,巴拉圭,卡塔尔,罗马尼亚,沙特阿拉伯,所罗门群岛,塞舌尔,苏丹,瑞典,斯洛文尼亚,斯洛伐克,塞内加尔,索马里,苏里南,萨尔瓦多,斯威士兰,特克斯和凯科斯群岛,乍得,多哥,塔吉克斯坦,土库曼斯坦,汤加,土耳其,图瓦卢,坦桑尼亚,乌克兰,乌干达,美国,乌拉圭,乌兹别克斯坦,梵蒂冈,委内瑞拉,瓦努阿图,瓦利斯群岛和富图纳群岛,西萨摩亚,阿森松,圣尤斯塔提马斯岛,北非西班牙属土,巴利阿里群岛,也门阿拉伯共合国,南非,赞比亚,津巴布韦,留尼汪岛,英国,吉布提,库克群岛,加罗林群岛,瑙鲁共和国,塞尔维亚共和国,圣克里斯托佛岛及尼维斯岛,特立尼达和多巴哥,刚果,圣赫勒拿,列支敦士登,美属维尔京群岛,圣文森特和格林纳丁斯岛,刚果民主共和国,加纳,马提尼克岛,巴林,突尼斯,俄罗斯,安哥拉,塞拉里昂,格陵兰,诺褔克岛";
		
		$reOneArr = explode(',',$oneStr);
		$reTwoArr = explode(',',$twoStr);
		$reArr["one"] = $reOneArr;
		$reArr["two"] = $reTwoArr;
		return $reArr;
	}
	//wish邮的面单 中邮分区
	public function wishPartition($country = ''){
		$reArr = array(
		'1' => "日本,",
		//2区
		'2' => "奥地利,保加利亚,韩国,马来西亚,斯洛伐克,泰国,新加坡,印度,印度尼西亚",
		//3区
		'3' => "爱尔兰,比利时,波兰,丹麦,芬兰,捷克,葡萄牙,瑞士,希腊,意大利",
		//4区
		'4' => "阿曼,阿塞拜疆,塔吉克斯坦,土库曼斯坦,爱沙尼亚,巴基斯坦,白俄罗斯,波黑,朝鲜,法国,菲律宾,哈萨克斯坦,吉尔吉斯斯坦,加拿大,卡塔尔,拉脱维亚,立陶宛,卢森堡,罗马尼亚,马耳他,美国,蒙古,塞浦路斯,斯里兰卡,斯洛文尼亚,土耳其,乌克兰,乌兹别克斯坦,西班牙,新西兰,叙利亚,亚美尼亚,越南",
		//5区
		'5' => "阿尔巴尼亚,阿尔及利亚,阿富汗,阿根廷,阿联酋,黑山,埃及,美属萨摩亚,埃塞俄比亚,安道尔,安哥拉,巴布亚新几内亚,巴林,巴拿马,巴西,贝宁,冰岛,博茨瓦纳,不丹,布隆迪,赤道几内亚,多哥,厄瓜多尔,法罗群岛,法属波利尼西亚,梵蒂冈,斐济,冈比亚,哥伦比亚,格鲁吉亚,古巴,关岛,基里巴斯,吉布提,几内亚,几内亚比绍,加纳,加蓬,柬埔寨,津巴布韦,喀麦隆,科特迪瓦,科威特,肯尼亚,库克群岛,老挝,黎巴嫩,利比里亚,利比亚,列支敦士登,卢旺达,马达加斯加,马德拉群岛,马尔代夫,马拉维,马里,马其顿,马绍尔群岛,毛里求斯,毛里塔尼亚,秘鲁,密克罗尼西亚,缅甸,摩尔多瓦,摩洛哥,摩纳哥,莫桑比克,墨西哥,纳米比亚,南非,尼泊尔,尼日尔,尼日利亚,塞尔维亚,塞拉利昂,塞内加尔,塞舌尔,圣马力诺,斯威士兰,苏丹,苏里南,索马里,坦桑尼亚,汤加,突尼斯,瓦努阿图,委内瑞拉,文莱,乌干达,新喀里多尼亚,亚速尔群岛,也门,伊拉克,伊朗,约旦,赞比亚,直布罗陀",
		//6区
		'6' => "阿鲁巴,安圭拉,刚果,巴巴多斯,巴哈马,巴拉圭,百慕大,波多黎各,玻利维亚,伯利兹,多米尼加,法属圭亚那,哥斯达黎加,格林纳达,格陵兰岛,瓜德罗普,圭亚那,海地,荷属安的列斯群岛,洪都拉斯,开曼群岛,马提尼克,尼加拉瓜,萨尔瓦多,圣皮埃尔和密克隆,特立尼达和多巴哥,危地马拉,乌拉圭,牙买加,智利",
		//7区
		'7' => "俄罗斯,",
		//8区
		'8' => "澳大利亚,英国,瑞典,以色列,德国,挪威,荷兰,克罗地亚,匈牙利"
		);
		if($country){
			$result = '';
			foreach($reArr as $k=>$v){
				$result = explode(',',$v);
				if(in_array($country,$result)){
					return $k;
				}
			}
		}
		return $reArr;
	}
	
	//MDD香港挂号面单 模板
	public function mddHongKongRegistrationTemplate($allParamArr){
		$reStr='<style>body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;margin:0; padding:0;}
						td{ white-space:nowrap;}
						.PageNext{page-break-after:always; clear:both; min-height:1px; height:auto; overflow:auto; width:100%;}
						.float_box{ position: relative; width:370px; height:364px; overflow:hidden; margin:11px 3px 1px; border:1px solid black;}
				</style>';
		$reStr.='<div id="main_frame_box" style="margin-left:15px;">';
		$reStr.= '<div class="float_box">
					<div align="center" style="width:100%;height:80px;">	
						<img width="362px;" height="67px;" style="margin-top:2px;" src="images/mddHkRegistration1.png"/>				  					  
					</div>	
					<div style="width:100%;height:90px;">
						<table style="width:368px;">
							<tr>
								<td align="left">
									<strong style="font-size:15px;text-decoration:underline;margin-left:0px;">TO:</strong>
									<strong style="font-size:15px;margin-left:5px;">'.$allParamArr['ordersInfo']['buyer_name'].'</strong>	
									<span style="font-size:13px;margin-left:5px;">';
									if(!empty($allParamArr['ordersInfo']['buyer_address_1'])){
										$reStr .= '<br>'.$allParamArr['ordersInfo']['buyer_address_1'];
									}
									if(!empty($allParamArr['ordersInfo']['buyer_address_2'])){
										$reStr .= '<br>'.$allParamArr['ordersInfo']['buyer_address_2'];
									}
									$reStr .= '
										<br> '.$allParamArr['ordersInfo']['buyer_city'].' 
										&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_state'].' 
										<br>Zip:'.$allParamArr['ordersInfo']['buyer_zip'].'
										<br>Tel:'.$allParamArr['ordersInfo']['buyer_phone'].' 
									</span>									
								</td>
								<td align="right">
									<div style="width:80px;height:90px;border:2px solid #000;font-weight:bold;">
 				     					<table>
											<tr>
												<td>
													<img width="80px;" height="70px;" style="margin-top:-3px;margin-left:-3px;" src="images/mddHkPost2.jpg"/>
												</td>
											</tr>
											<tr>
												<td align="center">
													<span style="font-size:12px;">';
													$coutryName = replace('country',$allParamArr['ordersInfo']['buyer_country']);
													if(in_array($coutryName,$allParamArr['coutryCnArr']['one'])){
														$reStr .= '1区';
													}elseif(in_array($coutryName,$allParamArr['coutryCnArr']['two'])){
														$reStr .= '2区';
													}elseif(in_array($coutryName,$allParamArr['coutryCnArr']['three'])){
														$reStr .= '3区';
													}else{
														$reStr .= '3区';
													}
													$reStr .= '
													</span>
												</td>
											</tr>
										</table>
 				  					</div>
								</td>
							</tr>
						</table>
					</div>	
					<div style="width:45px;height:25px;border:1px solid #000;margin-left:5px;margin-top:5px;">
 				     	<strong style="font-size:25px;" >'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'</strong>				
 				  	</div>	
					<div style="width:100%;height:20px;margin-left:5px;margin-top:3px;">
						<span>'.$allParamArr['ordersInfo']['buyer_country'].'&nbsp;&nbsp;&nbsp;&nbsp;'.$coutryName.'</span>
					</div>	
					<div>
						<table style="width:370px; border: solid thin #000;BORDER-left: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;">
							<tr>
								<td rowspan="2">
									<div align="left" style="margin-left:30px;margin-top:-30px;">
										<span style="font-size:30px;">R</span>												
									</div>									
								</td>
								<td>
									<div style="margin-left:90px;">
										<span style="font-size:12px;">HONG KONG</span>	
									</div>
									<div style="margin-left:75px;">
										<strong style="font-size:13px;">'.$allParamArr['ordersInfo']['orders_shipping_code'].'</strong>	
									</div>
								</td>
							</tr>
							<tr>
								<td align="left" colspan="2">		
									<div style="margin-top:-5px;margin-left:-20px;">															
										<img width="280px;" src="chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text=S'.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
									</div>
								</td>
							</tr>
						</table>						
					</div>	
					<div>
						<table style="width:370px; border: solid thin #000;BORDER-left: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;BORDER-top: rgb(0,0,0) 1px;">
							<tr>
								<td>
									<div align="left" style="margin-left:5px;">
										<span style="font-size:12px;">
										iMail
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										['.date("YmdHis").']
										&nbsp;&nbsp;
										Ref NO:S'.$allParamArr['ordersInfo']['erp_orders_id'].'
										</span>												
									</div>
								</td>
							</tr>
						</table>
						<table style="border: solid thin #000;BORDER-left: rgb(0,0,0) 1px;BORDER-top: rgb(0,0,0) 1px;BORDER-bottom: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;border-collapse:collapse;BORDER-right: rgb(0,0,0) 1px;">
								<tr>				
									<td align="left" style="border: solid thin #000;BORDER-top: rgb(0,0,0) 1px;BORDER-bottom: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;">
										<textarea readonly="readonly" style=" font-size:13px; height: 45px; width: 367px; margin-top:-1px;margin-left:-2px;resize:none;border: solid thin #000;BORDER-left: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;BORDER-top: rgb(0,0,0) 1px;BORDER-bottom: rgb(0,0,0) 1px;overflow-y:hidden" rows="3">';
										foreach ($allParamArr['productsInfo'] as $product){
											$reStr .= $product['orders_sku'].'*'.$product['item_count'].'【'.$product['products_location'].'】';
										}
										$reStr .= '</textarea>
									</td>
								</tr>
						</table>
					</div>									
			     </div>';
		$reStr.='</div>';
		return $reStr;
	}
	
	//MDD香港平邮面单 模板
	public function mddHongKongPostTemplate($allParamArr){
		$reStr='<style>body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;margin:0; padding:0;}
						td{ white-space:nowrap;}
						.PageNext{page-break-after:always; clear:both; min-height:1px; height:auto; overflow:auto; width:100%;}
						.float_box{ position: relative; width:370px; height:364px; overflow:hidden; margin:11px 3px 1px; border:1px solid black;}
				</style>';
		$reStr.='<div id="main_frame_box">';
		$reStr.= '<div class="float_box">
					<div align="center" style="width:100%;height:82px;">	
						<img width="362px;" height="67px;" style="margin-top:5px;" src="'.site_url('attachments').'/images/mddHkPost.jpg"/>				  					  
					</div>	
					<div style="width:100%;height:95px;">
						<table style="width:368px;">
							<tr>
								<td align="left">
									<strong style="font-size:15px;text-decoration:underline;margin-left:0px;">TO:</strong>
									<strong style="font-size:15px;margin-left:5px;">'.$allParamArr['ordersInfo']['buyer_name'].'</strong>	
									<span style="font-size:13px;margin-left:5px;">';
									if(!empty($allParamArr['ordersInfo']['buyer_address_1'])){
										$reStr .= '<br>'.$allParamArr['ordersInfo']['buyer_address_1'];
									}
									if(!empty($allParamArr['ordersInfo']['buyer_address_2'])){
										$reStr .= '<br>'.$allParamArr['ordersInfo']['buyer_address_2'];
									}
									$reStr .= '
										<br> '.$allParamArr['ordersInfo']['buyer_city'].' 
										&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_state'].' 
										<br> Zip:'.$allParamArr['ordersInfo']['buyer_zip'].'
										<br> Tel:'.$allParamArr['ordersInfo']['buyer_phone'].' 
									</span>									
								</td>
								<td align="right">
									<div style="width:80px;height:90px;border:2px solid #000;font-weight:bold;">
 				     					<table>
											<tr>
												<td>
													<img width="80px;" height="70px;" style="margin-top:-3px;margin-left:-3px;" src="'.site_url('attachments').'/images/mddHkPost2.jpg"/>
												</td>
											</tr>
											<tr>
												<td align="center">
													<span style="font-size:12px;">'
													.$allParamArr['region'].
													'</span>
												</td>
											</tr>
										</table>
 				  					</div>
								</td>
							</tr>
						</table>
					</div>	
					<div style="width:50px;height:28px;border:1px solid #000;margin-left:5px;margin-top:5px;">
 				     	<strong style="font-size:28px;" >'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'</strong>				
 				  	</div>	
					<div style="width:100%;height:20px;margin-left:5px;margin-top:10px;">
						<span>'.$allParamArr['ordersInfo']['buyer_country'].'&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['country'].'</span>
					</div>	
					<div>
						<table style="width:370px; border: solid thin #000;BORDER-left: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;BORDER-bottom: rgb(0,0,0) 1px;">
							<tr>
								<td align="center" colspan="2">
									<strong>S'.$allParamArr['ordersInfo']['erp_orders_id'].'</strong>
								</td>
							</tr>							
						</table>
						<table style="width:370px; border: solid thin #000;BORDER-left: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;">
							<tr>
								<td align="center" colspan="2">
									<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text=S'.$allParamArr['ordersInfo']['erp_orders_id'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
								</td>
							</tr>
						</table>
						<table height="52px;" style="border: solid thin #000;BORDER-left: rgb(0,0,0) 1px;BORDER-top: rgb(0,0,0) 1px;BORDER-bottom: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;border-collapse:collapse;BORDER-right: rgb(0,0,0) 1px;">
								<tr>				
									<td align="left">
										<span>BAM</span>
									</td>											
								
									<td rowspan="2" align="left" style="border: solid thin #000;BORDER-top: rgb(0,0,0) 1px;BORDER-bottom: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;">
										<textarea readonly="readonly" style=" font-size:13px; height: 45px; width: 232px; margin-top:-1px;margin-left:-1px;resize:none;border: solid thin #000;BORDER-left: rgb(0,0,0) 1px;BORDER-top: rgb(0,0,0) 1px;BORDER-bottom: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;overflow-y:hidden" rows="3">';
										foreach ($allParamArr['productsInfo'] as $product){
											$reStr .= $product['orders_sku'].'*'.$product['item_count'].'【'.$product['products_location'].'】';
										}
										$reStr .= '</textarea>
									</td>
								</tr>
								<tr>
									<td align="left" style="border: solid thin #000;BORDER-left: rgb(0,0,0) 1px;BORDER-bottom: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;">
										<span>Ref NO:S'.$allParamArr['ordersInfo']['erp_orders_id'].'</span>
									</td>																										
								</tr>
						</table>						
					</div>										
			     </div>';
		$reStr.='</div>';								
		return $reStr;
	}
	
	
	
	//平邮小包模板
	public function printPingyouPacketTemplate($allParamArr){
		$reStr='<style>body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;margin:0; padding:0;}
						td{ white-space:nowrap;}
						.PageNext{page-break-after:always; clear:both; min-height:1px; height:auto; overflow:auto; width:100%;}
						.float_box{ position: relative; width:370px; height:364px; overflow:hidden; margin:11px 3px 1px; border:1px solid black;}
				</style>';
		$reStr.='<div id="main_frame_box" style="margin-left:15px;">';
		$reStr = '<div class="float_box">
					<table border="0" cellpadding="0" cellspacing="0">
						<tr valign="top">
							<td width="224" style="border-right: 1px solid black; border-bottom: 1px solid black;">
								<div style="width:217px; overflow:hidden; white-space: normal; word-break: keep-all; float:left; margin-top:0; padding:3px;">
								<b>SHIP TO:</b><br/>
								'.$allParamArr['orderInfo']['buyer_name'].'<br/>
								'.$allParamArr['orderInfo']['buyer_address_1'].' '.$allParamArr['orderInfo']['buyer_address_2'].' '
								.$allParamArr['orderInfo']['buyer_city'].' '.$allParamArr['orderInfo']['buyer_state'].' '.$allParamArr['orderInfo']['buyer_zip'].'<br/>'
								.$allParamArr['orderInfo']['buyer_country'].' '.$allParamArr['countryCn'].'<br/>
								Tel:'.$allParamArr['orderInfo']['buyer_phone'].'<br/>
								'.$allParamArr['orderInfo']['orders_old_shipping_code'].'
								</div>
							</td>
							<td width="146" style="border-bottom: 1px solid black;">
								<div style="width:145px; overflow: hidden; float:left; margin-top:0px;">
									<div style="float: left; margin-top:0; border-bottom:1px solid black; width: 139px; padding:3px;">
									BY AIR MAIL<br/>
									航PAR AVION空
									</div>
									<div style="float: left; width:139px; padding:3px;">
									Weight:'.$allParamArr['weightTotal'].'Kg<br/>
									LastPrint:'.date('Y-m-d').'<br/>
									PrintTimes:'.($allParamArr['orderInfo']['printTimes'] + 1) . '<br/>
                                    '.$allParamArr['orderInfo']['orders_shipping_code'].'
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td align="center" valign="middle" height="50" style="border-bottom: 1px solid black;padding: 5px;" colspan="3">
							<div style="padding-top: 12px;">
								<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text='.$allParamArr['orderInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" /><br>
								'.$allParamArr['orderId'].'
							</div>							
							</td>
						</tr>
						<tr>
							<td colspan="2" valign="top" style="border-bottom: 1px solid black; ">
								<table width="100%" cellspacing="0" cellpadding="0">
									';								
									foreach ($allParamArr['productsList'] as $product){
										$reStr .= '<tr><td width="120" style="white-space: normal; word-wrap: break-word;">'.$product['orders_sku'].'</td><td width="25">'.$product['item_count'].'</td><td  style="text-align:right">'.$product['products_location'].'</td></tr>';
										$allParamArr['totalCount'] += $product['item_count'];
									}
								$reStr .= '	
								</table>
							</td>
						</tr>
				
					</table>
					<div style="width:100%;font-size: 28px;font-weight: bold;">
						<div style="width:50px;float:left;font-size:30px;">'.$allParamArr['orderInfo'][ 'shipmentAutoMatched' ].'</div>
						<div style="width:50px;float:right;">'.$allParamArr['totalCount'].'</div>
					</div>
				</div>						
		';
		$reStr.='</div>';						
		return $reStr;
	}
	

	//燕文邮政逻辑处理（组装函数）停用
	public function yanWenPostTemplateList($ordersList,$shipmentInfo){
		$reMsg = "";
		foreach($ordersList as $k => $orderId){
			$allParamArr  = array();
			$allParamArr['ordersInfo']      = $this->_ordersManage->getOrderInfo($orderId);			
			$allParamArr['productsInfo']    = $this->_productsManage->getOrderProducts($orderId, $allParamArr['ordersInfo']['orders_warehouse_id']);
			$reMsg .= $this->yanWenPostTemplate($allParamArr);
		}
		return $reMsg;
	}
	
	//燕文邮政模板
	public function yanWenPostTemplate($allParamArr){
		$reStr='<style>body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;margin:0; padding:0;}
						td{ white-space:nowrap;}
						.PageNext{page-break-after:always; clear:both; min-height:1px; height:auto; overflow:auto; width:100%;}
						.float_box{ position: relative; width:370px; height:364px; overflow:hidden; margin:11px 3px 1px; border:1px solid black;}
				</style>';
		$reStr.='<div id="main_frame_box" style="margin-left:15px;">';
		$reStr.= '<div class="float_box">
				  	 <table border="0" cellpadding="2" cellspacing="0" width="100%">
						<tr height="56">
							<td style="border-bottom: 1px solid black;">
								<table cellpadding="0" cellspacing="0" border="0" width="100%">
									<tr>
									<br>
									</tr>
									<tr>
										<td width="100" align="center" style="font-size:18px;">YANWEN</td>
										<td width="10" align="right" style="font-size:12px;">OrderNo:&nbsp;&nbsp;&nbsp;</td>
										<td width="110" align="left">		
											<img width="170" height="35" src="chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text='.$allParamArr['ordersInfo']['erp_orders_id'].'&f1=-1&f2=8&a1=&a2=B&a3=" />										
										</td>
									</tr>	
									<tr>
										<td colspan="2"></td>
										<td align="center"><strong style="font-size:12px;">'.$allParamArr['ordersInfo']['erp_orders_id'].'</strong></td>
									</tr>								
								</table>
							</td>
						</tr>
						<tr valign="top">
							<td style="border-bottom: 0px solid black;">
								<div style="float:left;">&nbsp;&nbsp;&nbsp;&nbsp;多品名/备注：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								'.$allParamArr['ordersInfo']['orders_shipping_code'].'
								</div>
							</td>
						</tr>						
					</table>
					<div align="center">
						<table style="border: solid thin black" width="90%">
							<tr>
								<td align="left">';
								foreach($allParamArr['productsInfo'] as $proInfoArr){
									$reStr .= $proInfoArr['products_name_cn'].' '.$proInfoArr['orders_sku'].'*'.$proInfoArr['item_count'].'<br>';
								}
								$reStr .= '									
								</td>
							</tr>
							<tr>
								<td>
								
								</td>
							</tr>
						</table>
						<table width="80%">
							<tr>
								<td align="right">
									<span style="font-size:10px;">302035</span><br>
									<span style="font-size:13px;">'.date("Y-m-d").'</span>
								</td>
							</tr>
						</table>
					</div>
				</div>

			
			<!-- 右半部分 -->
			<div class="float_box">
				<table border="0" cellpadding="2" cellspacing="0" width="100%">
						<tr height="100">
							<td  valign="top" style="border-bottom: 1px solid black;">
								<table cellpadding="0" cellspacing="0" border="0" width="95%">								
									<tr>
										<td height="35" width="100" align="center" style="font-size:18px;">YANWEN</td>
                                        <td colspan="3"></td>
									</tr>	
									<tr>
										<td colspan="2" align="right">
											<table>
												<tr>
													<td align="right">
														<img width="250" height="45" src="chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
														<br>
														'.$allParamArr['ordersInfo']['orders_shipping_code'].' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
													</td>
												</tr>
											</table>									
										</td>										
									</tr>								
								</table>
							</td>
							<td valign="top" style="border-bottom: 1px solid black;">
                                <table style="padding-top:10px;" cellpadding="0" cellspacing="0" border="0" width="20%">								
                                    <tr>
                                        <td align="right" style="font-size:45px;">2&nbsp;</td>                                      
                                    </tr>									
                                </table>
                            </td>
						</tr>
						<tr>
							<td colspan="2" align="center" style="border-bottom: 1px solid black;">								
									Y-POST - 俄罗斯 	&nbsp;&nbsp;&nbsp;					
							</td>
						</tr>	
						<tr>
							<td>
							&nbsp;&nbsp;&nbsp;<strong style="font-size:15px;">To:</strong>
							</td>
						</tr>	
						<tr>
							<td>
							&nbsp;&nbsp;&nbsp;<span style="font-size:14px;">anton Kinev Tel:89519868236 NULL </span>
							</td>
						</tr>
						<tr>
							<td>
							&nbsp;&nbsp;&nbsp;<span style="font-size:14px;">anton Kinev Tel:89519868236 NULL </span>
							</td>
						</tr>
						<tr>
							<td>
							&nbsp;&nbsp;&nbsp;<span style="font-size:14px;">anton Kinev Tel:89519868236 NULL </span>
							</td>
						</tr>
						<tr>
							<td>
							&nbsp;&nbsp;&nbsp;<span style="font-size:14px;">anton Kinev Tel:89519868236 NULL </span>
							</td>
						</tr>					
						<tr>
							<td>
							&nbsp;&nbsp;&nbsp;<span style="font-size:15px;">OrderNo:2223957</span>
							</td>
						</tr>	
						<tr>
							<td>
							&nbsp;&nbsp;&nbsp;<span style="font-size:14px;">俄罗斯</span>
							</td>
						</tr>		
					</table>

						<table>
							<tr>										
								<td width="10" align="left" style="font-size:12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OrderNo:<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img width="170" height="35" src="chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text=1234567&f1=-1&f2=8&a1=&a2=B&a3=" />
								<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								2223957
								</td>
								<td width="150" align="right">		
									<table>
										<tr>
											<td align="right">
												<span style="font-size:10px;">302035</span><br>
												<span style="font-size:13px;">'.date("Y-m-d").'</span>
											</td>
										</tr>
									</table>									
								</td>
							</tr>
						</table>						
					
			</div>';
			$reStr.='</div>';					
			return $reStr;	

	}
	
	
	
	//中国邮政模板（一）
	public function chinaPostTemplateOne($allParamArr){
		$reStr='<style>body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;margin:0; padding:0;}
						td{ white-space:nowrap;}
						.PageNext{page-break-after:always; clear:both; min-height:1px; height:auto; overflow:auto; width:100%;}
						.float_box{ position: relative; width:370px; height:364px; overflow:hidden; margin:11px 3px 1px; border:1px solid black;}
				</style>';
		$reStr.='<div id="main_frame_box" style="margin-left:15px;">';
		$reStr.= '<div class="float_box">
					<table border="0" cellpadding="2" cellspacing="0" width="100%">
						<tr height="56">
							<td style="border-bottom: 1px solid black;">
								<table cellpadding="0" cellspacing="0" border="0" width="100%">
									<tr align="center">
										<td width="112" rowspan="3"><img src="'.site_url('attachments/images/post_logo.jpg').'" width="112" height="34" border="0" /></td>
										<td width="120"><strong>航空</strong></td>
										<td rowspan="3">
											<table cellpadding="0" cellspacing="0" border="0" width="100%">
												<tr align="center">
													<td>
													'.$allParamArr['shipmentInfo']['shipmentTitle'].'
													</td>                                        
                                        		</tr>
                                        		<tr align="center">
													<td>'.$allParamArr['country'].' '.$allParamArr['countryCode']."<br/>".$allParamArr['rs']['erp_orders_id'].'</td>                                        
                                    			</tr>
                                 			</table>									
                             			</td>
						 			</tr>
									<tr align="center">
										<td><strong>Small Packet</strong></td>
									</tr>
									<tr align="center">
										<td><strong>BY AIR</strong></td>
									</tr>
						   		</table>
							</td>
						</tr>
						<tr valign="top" height="22">
							<td style="border-bottom: 1px solid black;">
								<div style="float:left;">协议客户：'.$allParamArr['consumer_name'].'</div>
							</td>
						</tr>
						<tr height="56">
							<td style="border-bottom: 1px solid black;">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr><td width="50" align="right" valign="top"><strong>FROM:&nbsp;</strong></td><td colspan="3" style="white-space: normal; word-break: keep-all;">'.$allParamArr['consumer_from'].'</td></tr>
									<tr><td align="right"><strong>ZIP:&nbsp;</strong></td><td width="100">'.$allParamArr['consumer_zip'].'</td><td width="40" align="right"><strong>TEL:&nbsp;</strong></td><td>'.$allParamArr['consumer_phone'].'</td></tr>
								</table>
							</td>
						</tr>
						<tr height="145">
							<td valign="top">
								<table border="0" cellspacing="0" cellpadding="0">
									<tr><td width="40" valign="top" align="right"><strong>TO:&nbsp;</strong></td><td colspan="3" style="white-space: normal; word-break: keep-all;">'. $allParamArr['rs']['buyer_name'].'<br/>'.$allParamArr['rs']['buyer_address_1'].' '.$allParamArr['rs']['buyer_address_2'].'<br/>'.$allParamArr['rs']['buyer_city'].' '.$allParamArr['rs']['buyer_state'].' '.$allParamArr['displayname'].'</td></tr>
									<tr><td align="right">ZIP:&nbsp;</td><td width="100">'.$allParamArr['rs']['buyer_zip'].'</td><td width="40" align="right">TEL:&nbsp;</td><td>'.$allParamArr['rs']['buyer_phone'].'</td></tr>
									<tr><td>&nbsp;</td><td colspan="3" style="white-space: normal; word-break: break-all;">'.implode('&nbsp;&nbsp;', $allParamArr['data']).'</td></tr>
								</table>
							</td>
						</tr>
				</table>
				<div style="position: absolute; bottom: 80px; right: 0px; width: 30px; height:30px; z-index:5px; font-weight: bold; font-size: 28px;">'.$allParamArr['totalCount'].'</div>
				<div style="width: 370px; position: absolute; bottom:60px; text-align: center; clear:both; border-top:1px solid black; padding-bottom: 2px;">
 					退件单位：'.$allParamArr['consumer_back'].'
 				</div>
				<div style="width: 370px; height:58px; position: absolute; bottom:0px; text-align: center; clear:both; border-top:1px solid black; padding-top: 2px;">
 					<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text='.$allParamArr['rs']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" /><span class="fontSize10"><br>'.$allParamArr['rs']['orders_shipping_code'].'</span>
 				</div>
			</div>
			
			<!-- 右半部分 -->
			<div class="float_box">
				<table border="0" cellpadding="2" cellspacing="0" width="100%" style="font-size: 11px;">
					<tr style="">
						<td style="border-bottom: 1px solid black;" colspan="7">
						   <table cellpadding="0" cellspacing="0" border="0" width="100%">
						   	<tr align="center">
						   		<td width="90" rowspan="2">'. $allParamArr['rs']['erp_orders_id'] .'</td>
						   		<td width="175"><strong>报关签条</strong></td>
						   		<td><strong>邮2113</strong></td>
						   	</tr>
						   	<tr align="center">
						   		<td><strong>CUMTOMS DECLARATION</strong></td>
						   		<td><strong>CN22</strong></td>
						   	</tr>
						   </table>
						</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid black;" colspan="7">
							<div style="width:150px; float:left;">可以经行拆开</div>
							<div style="float: left;">May be opened officially</div>
						</td>
					</tr>
					<tr style="line-height:12px;">
						<td width="50" align="center" style="border-bottom:1px solid black; border-right:1px solid black;" rowspan="2">邮件种类</td>
						<td width="20" style="border-bottom: 1px solid black; border-right:1px solid black;" align="center"><b style="font-family: \'宋体\'; font-size:16px;">X</b></td>
						<td width="80" style="border-bottom: 1px solid black; border-right:1px solid black;">礼品<br/>gift</td>
						<td width="20" style="border-bottom: 1px solid black; border-right:1px solid black;">&nbsp;</td>
						<td style="border-bottom: 1px solid black;" colspan="3">商品货样<br/>Commercial Sample</td>
					</tr>
					<tr style="line-height:12px;">
						<td style="border-bottom: 1px solid black; border-right:1px solid black;">&nbsp;</td>
						<td style="border-bottom: 1px solid black; border-right:1px solid black;">文件<br/>Documents</td>
						<td style="border-bottom: 1px solid black; border-right:1px solid black;">&nbsp;</td>
						<td style="border-bottom: 1px solid black;" colspan="3">其他<br/>Other</td>
					</tr>
					<tr style="line-height:12px;">
						<td width="50%" style="border-bottom: 1px solid black; border-right:1px solid black;" colspan="5" align="center">内件详细名称和数量<br/><span style="font-size: 10px;">Quantity and detailed description of contents</span></td>
						<td width="25%" style="border-bottom: 1px solid black;border-right:1px solid black;" align="center">重量(千克)<br/>Weight(Kg)</td>
						<td width="25%" style="border-bottom: 1px solid black;" align="center">价值<br/>Value</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid black; border-right:1px solid black;" colspan="5" align="center">';
						if($allParamArr['products_data'][0]){
							if($allParamArr['shipment_template'] == 1){
								$reStr .= $allParamArr['products_data'][0]['products_declared_cn'].' '.$allParamArr['products_data'][0]['products_declared_en'];
							}elseif($allParamArr['shipment_template'] == 2){
								$reStr .= $allParamArr['categoryListStr'];
							}							
						}else{
							$reStr .= '&nbsp;';
						}
						$reStr .= '
						</td>
						<td style="border-bottom: 1px solid black;border-right:1px solid black;" align="center">';
						if($allParamArr['products_data'][0]){
							if($allParamArr['flag']){
								$reStr .= $allParamArr['perWeight'];
							}else{
								$reStr .= $allParamArr['products_data'][0]['products_weight'];
							}							
						}else{
							$reStr .= '&nbsp;';
						}
						
						$reStr .= '
						</td>
						<td style="border-bottom: 1px solid black;" align="center">';
						if($allParamArr['products_data'][0]){
							if($allParamArr['flag']){
								$reStr .= $allParamArr['perPrice'].' USD';
							}else{
								$reStr .= $allParamArr['products_data'][0]['products_declared_value'].' USD';
							}							
						}else{
							$reStr .= '&nbsp;';
						}
						
						$reStr .= '
						</td>
					</tr>';
					if($allParamArr['shipment_template'] == 1){
						$reStr .= '
							<tr>
								<td height="15" style="border-bottom: 1px solid black; border-right:1px solid black;" colspan="5" align="center">';
								if($allParamArr['products_data'][1]){
									$reStr .= $allParamArr['products_data'][1]['products_declared_cn'].' '.$allParamArr['products_data'][1]['products_declared_en'];
								}else{
									$reStr .= '&nbsp;';
								}
								$reStr .= '
								</td>
								<td style="border-bottom: 1px solid black;border-right:1px solid black;" align="center">';
								if($allParamArr['products_data'][1]){
									if($allParamArr['flag']){
										$reStr .= $allParamArr['perWeight'];
									}else{
										$reStr .= $allParamArr['products_data'][1]['products_weight'];
									}
								}else{
									$reStr .= '&nbsp;';
								}
								$reStr .= '
								</td>
								<td style="border-bottom: 1px solid black;" align="center">';
								if($allParamArr['products_data'][1]){
									if($allParamArr['flag']){
										$reStr .= $allParamArr['perPrice'].' USD';
									}else{
										$reStr .= $allParamArr['products_data'][1]['products_declared_value'].' USD';
									}
								}else{
									$reStr .= '&nbsp;';
								}
								$reStr .= '
								</td>
							</tr>';
					}
					$reStr .='
					<tr>
						<td height="15" style="border-bottom: 1px solid black; border-right:1px solid black;" colspan="5" align="center">
						</td>
						<td style="border-bottom: 1px solid black;border-right:1px solid black;" align="center">
						</td>
						<td style="border-bottom: 1px solid black;" align="center">
						</td>
					</tr>
					<tr>
						<td height="15" style="border-bottom: 1px solid black; border-right:1px solid black;" colspan="5" align="center">&nbsp;
						
						</td>
						<td style="border-bottom: 1px solid black;border-right:1px solid black;" align="center">&nbsp;
						
						</td>
						<td style="border-bottom: 1px solid black;" align="center">&nbsp;
						
						</td>
					</tr>
					<tr style="font-size:11px; line-height:12px;">
						<td  style=" border-bottom: 1px solid black; border-right:1px solid black; white-space: normal; word-break: break-all;" colspan="5" rowspan="2">
						协调系统税则号列和货物原产国(只对商品邮件填写)<br/>
						<p style="word-spacing: 0px; padding:0px; margin:0px; word-break: keep-all;">HS tariff number and country of origin of goods(For Commercial items only)</p>
						</td>
						<td style="border-bottom: 1px solid black;border-right:1px solid black;" align="center">总重量<br/>Total Weight(kg)</td>
						<td style="border-bottom: 1px solid black;" align="center">总价值<br/>Total Value</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid black;border-right:1px solid black;" align="center">
						'.$allParamArr['total_weight'].'
						</td>
						<td style="border-bottom: 1px solid black;" align="center">
						'.$allParamArr['total_value'].' USD
						</td>
					</tr>
					<tr>
						<td colspan="7" style="white-space:normal;">
						我保证上述申报准确无误，本函件内未装寄法律或邮件和海关规章禁止寄递的任何危险物品<br/>
						<p style="word-wrap:normal; word-break: keep-all; margin:0; padding:0;">I the undersigned,certify that the particulars given in this declaration are correct and this item does not contain any dangerous articles prohibited by legislation or by postal or customers regulations.</p>
						<p style="white-space:normal; word-break: keep-all; margin:0; padding:0; text-align: center;">寄件人签字 Sender\'s signature: '. $allParamArr['sender_signature'].'</p>
						</td>
					</tr>
				</table>
			</div>';
		$reStr.='</div>';
		return $reStr;		
	}
	
	//中国邮政模板（二）
	public function chinaPostTemplateTow($allParamArr){
		$reStr='<style>body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;margin:0; padding:0;}
						td{ white-space:nowrap;}
						.PageNext{page-break-after:always; clear:both; min-height:1px; height:auto; overflow:auto; width:100%;}
						.float_box{ position: relative; width:370px; height:364px; overflow:hidden; margin:11px 3px 1px; border:1px solid black;}
				</style>';
		$reStr.='<div id="main_frame_box" style="margin-left:15px;">';
		$reStr.= '
			<div class="float_box">
				<table border="0" cellpadding="0" cellspacing="0">
					<tr valign="top">
						<td width="224" style="border-right: 1px solid black; border-bottom: 1px solid black;">
							<div style="width:217px; overflow:hidden; white-space: normal; word-break: keep-all; float:left; margin-top:0; padding:3px;">
							<b>SHIP TO:</b><br/>
							'. $allParamArr['orderInfo']['buyer_name'].'<br/>'.$allParamArr['orderInfo']['buyer_address_1'].' '.$allParamArr['orderInfo']['buyer_address_2'].' '
								.$allParamArr['orderInfo']['buyer_city'].' '.$allParamArr['orderInfo']['buyer_state'].' '.$allParamArr['orderInfo']['buyer_zip'].'<br/>'.
							$allParamArr['orderInfo']['buyer_country'].' '.$allParamArr['country_cn'].'<br/>'.
							'Tel:'.$allParamArr['orderInfo']['buyer_phone'].'</div>
						</td>
						<td width="146" style="border-bottom: 1px solid black;">
							<div style="width:145px; overflow: hidden; float:left; margin-top:0px;">
								<div style="float: left; margin-top:0; border-bottom:1px solid black; width: 139px; padding:3px;">
								BY AIR MAIL<br/>
								航PAR AVION空
								</div>
								<div style="float: left; width:139px; padding:3px;">
								Weight:'.$allParamArr['weightTotal'].'Kg<br/>';
								if($allParamArr['orderInfo']['orders_print_time']){
									$reStr .= 'FirstPrint:'.date('Y-m-d', strtotime($allParamArr['orderInfo']['orders_print_time'])).'<br/>';
								}
								$reStr .= '
									LastPrint:'.date('Y-m-d').'<br/>
									OrderFrom:'.$allParamArr['typeArray'][$allParamArr['orderInfo']['orders_type']].'<br/>
									PrintTimes:'.($allParamArr['orderInfo']['printTimes'] + 1).'
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td valign="middle" height="50" style="border-bottom: 1px solid black;" colspan="2">
						<div style="width: 240px; text-align:center; float:left;">
							<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text=S'.$allParamArr['orderId'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
						</div>
						<div style="float: left; white-space: normal; word-break: break-all; width:130px;">
							'.$allParamArr['orderId'].'<br/>'.
							 $allParamArr['shipmentInfo']['shipmentTitle'].'
						</div>
						</td>
					</tr>
					<tr>
						<td colspan="2" valign="top" style="border-bottom: 1px solid black; ">
							<table width="100%" cellspacing="0" cellpadding="3">
								<tr><td colspan="3"><b>Order Detail</b></td></tr>';
								foreach ($allParamArr['productsList'] as $product){
									$reStr .=  '<tr><td width="120" style="white-space: normal; word-wrap: break-word;">'.$product['orders_sku'].'</td><td width="25">'.$product['item_count'].'</td><td width="160" style="white-space: normal; word-wrap: break-word; font-size:12px;">'.$product['products_declared_en'].'</td><td width="65">'.$product['products_location'].'</td></tr>';
									$allParamArr['totalCount'] += $product['item_count'];
								}
							$reStr .=  '	
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="white-space: normal; padding:3px; font-size: 12px;">
							remark：';
							if($allParamArr['orderInfo'][ 'orders_remark' ]){
								$reStr .= str_replace(chr(13), ' ', $allParamArr['orderInfo'][ 'orders_remark' ]);
							}
							$reStr .= '
						</td>
					</tr>
				</table>
				<div style="position: absolute; right:0; bottom:0; height:30px; width:30px; font-size:28px; font-weight:bold;;">
					'.$allParamArr['totalCount'].'
				</div>
			</div>';
	    $reStr.='</div>';
		return $reStr;	
	}
	
	/**
	 * 打印顺丰俄罗斯平邮面单模板处理
	 * add in  2015-07-22
	 */
	public function shunFengRussiaTemplate($allParamArr){
		$reStr = '
	 		<style>
			   *{margin:0;padding:0;}
				body{ font-family:Arial, Helvetica, sans-serif; font-size:14px;}
				#main_frame_box{width:372px; height:378px;overflow:hidden;margin-bottom:2px;}
				table { width: 100%; border: 0;}
				.border_r_b { border-right: 1px solid black; border-bottom: 1px solid black;}
				.border_b { border-bottom: 1px solid black;}
				.border_r { border-right: 1px solid black;}
				.border_t_r_l{ border-top: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;}
				.border_r_b_l{ border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;}
				.border_r_l { border-right: 1px solid black; border-left: 1px solid black;}
				.border {border: 1px solid black;}
				.fontSize10 { font-size: 10px;}
				.fontSize11 { font-size: 11px;}
				.fontSize12{ font-size: 12px;}
				.fixed_box{ position: absolute; right: 0px; bottom: 0px; width: 30px; height: 30px; font-size: 28px; font-weight: bold; z-index:100;}
			</style>
	 	';
		$reStr .='
			<div id="main_frame_box">
			  <div style="width:100%;height:90px;">
			     <p style="width:230px;height:100%;border:2px solid #000;float:left;">
			       From:SLME<br/><br/>
			       Forward SF-EXPRESS <br/> P.O. Box 7023,14002 Tallinn,<br/> Estonia
			     </p>
			     <p style="width:130px;height:100%;border:2px solid #000;float:right;text-align:center;font-weight:bold;">
			     POSTIMAKS TASUTUD TAXE PERÇUE ESTONIE No. 199
			     </p>
			  </div>
			  <div style="width:100%;height:215px;">
			    <div style="width:130px;height:105px;float:left;margin-top:5px;font-weight:bold;text-align:center;">
				    <p style="border:2px solid #000;line-height:85px;font-size:25px;">
				      PRIORITY
				     </p>
				     <p style="font-size:18px;">
				      '.$allParamArr['ordersInfo']['erp_orders_id'].'
				     </p>
			    </div>
			  	
			     <p style="width:230px;height:auto;border:2px solid #000;float:right;margin-top:5px;word-wrap: break-word;">
			      To:'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
				     '.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
					 '.$allParamArr['ordersInfo']['buyer_city'].'<br/>
					 '.$allParamArr['ordersInfo']['buyer_state'].'<br/>
					 ZIP:'.$allParamArr['ordersInfo']['buyer_zip'].'<br/>
					 TEL:'.$allParamArr['ordersInfo']['buyer_phone'].'<br/>
					 '.$allParamArr['ordersInfo']['buyer_country'].'
			     </p>
			  </div>
			  <div style="width:100%;height:68px;">
			     <p style="width:240px;height:100%;float:left;font-weight:bold;text-align:center;">
			        <br/>
			        <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=30&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		 		    <br/>'.$allParamArr['ordersInfo']['orders_shipping_code'].'
			     </p>
			     <p style="width:130px;height:100%;line-height:68px;float:right;text-align:center;font-weight:bold;font-size:22px;">
			       '.$allParamArr['express_code'].'
			     </p>
			  </div>
			</div>
		';
		$reStr .='
		 <div id="main_frame_box" style="height:375px;">
			  
		            <table cellpadding="5" cellspacing="0" class="fixed">
		                <tr style="font-size:12px;">
		                    <td>
		                    	<table cellspacing="0">
		                    		<tr>
		                    			<td colspan="6" class="border">
		                    				<table cellspacing="0" cellpadding="0">
					                    		<tr>
					                    			<td style="font-weight: bold;">
					                    				CUSTOMS<br/>
					                    				DECLARATION
					                    			</td>
					                    			<td>
					                    				May be opened<br/>
					                    				officially
					                    			</td>
					                    			<td style="font-size: 20px; font-weight: bold;">
					                    				CN22
					                    			</td>
					                    		</tr>
					                    	</table>
		                    			</td>
		                    		</tr>
		                    		<tr>
		                    			<td colspan="6" class="border_r_b_l">
		                    				<table cellspacing="0" cellpadding="0">
		                    					<tr>
		                    						<td width="140" style="font-weight: bold;">Designated operator</td>
		                    						<td align="right" valign="top">
		                    						
		                    							Important!<br/>See instructions on the back</td>
		                    					</tr>
		                    				</table>
		                    			</td>
		                    		</tr>
		                    		<tr height="15">
		                    			<td width="18" height="18" class="border_r_b_l" align="center"></td>
		                    			<td width="100" style="line-height: 14px;">Gift</td>
		                    			<td width="18" class="border_r_b_l"></td>
		                    			<td width="200" colspan="3" class="border_r">Commerical Sample</td>
		                    		</tr>
		                    		<tr>
		                    			<td class="border_r_b_l" height="18"></td>
		                    			<td class="border_b">Documents</td>
		                    			<td class="border_r_b_l" align="center" style="font-family: 宋体; font-weight: bold; font-size: 16px;">&times;</td>
		                    			<td colspan="3" class="border_r_b">
		                    				<div style="position: relative;">
		                    					Other
		                    					<span style="position: absolute; right: 0; bottom: -4px;">Tick one or more boxes</span>
		                    				</div>
		                    			</td>
		                    		</tr>
		                    		<tr style="line-height: 12px;">
		                    			<td colspan="4" class="border_r_b_l">
		                    				Quantity and detailed description <br/>of contents(1)
		                    			</td>
		                    			<td width="25%" class="border_r_b">
		                    				Weight(in kg)
		                    			</td>
		                    			<td width="25%" class="border_r_b">
		                    				Value(3)
		                    			</td>
		                    		</tr>
		                    		<tr style="line-height: 12px; height: 44px;">
		                    			<td colspan="4" class="border_r_b_l" valign="top" width="60%">
		                    			'.$allParamArr['productsInfo']['namefiles'].'
		                    			
		                    			</td>
		                    			<td class="border_r_b" valign="top">
		                    			'.$allParamArr['productsInfo']['totalWeight'].'
		                    			</td>
		                    			<td class="border_r_b" valign="top">
		                    			'.$allParamArr['productsInfo']['totalPrice'].'
		                    			</td>
		                    		</tr>
		                    		<tr style="line-height: 12px;">
		                    			<td colspan="4" class="border_r_b_l" style="line-height: 12px;">
		                    				For commericial items only<br/>If known, HS tariff number(4) <br/>and country of origin of goods(5)
		                    			</td>
		                    			<td class="border_r">
		                    				Total Weight<br/>(in kg)(6)
		                    			</td>
		                    			<td class="border_r">
		                    				Total value(7)
		                    			</td>
		                    		</tr>
		                    		<tr style="line-height: 14px;">
		                    			<td colspan="4" class="border_r_b_l">CN</td>
		                    			<td class="border_r_b">
		                    			'.$allParamArr['productsInfo']['totalWeight'].'
		                    			</td>
		                    			<td class="border_r_b">
		                    			'.$allParamArr['productsInfo']['totalPrice'].'
		                    			</td>
		                    		</tr>
		                    		<tr style="line-height: 12px;height:60px;">
		                    			<td colspan="6" class="border_r_b_l" style="word-wrap: normal; word-break: keep-all;">
		                    				I,the undersigned, whose name and address are given on the item, certity that the particulars given 
						                	in this declaration are correct and that this item does not contain 
						                	any dangerous article or articles pro-hibited by legislation or by postal or 
						                	customs regulations<br/>
						                	Date and sender\'s signature(8)  SLME
		                    			</td>
		                    		</tr>
		                    	</table>
		                    </td>
		            </table>
				<hr style="height:5px;border:none;border-top:5px solid #000;margin-top:5px;" />
				<div style="width:100%;height:55px;">
				  <p style="width:190px;height:55px;float:left;text-align:center;font-weight:bold;">
				    
				    <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=30&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" style="padding-top:5px;"/>
		 		    <br/>'.$allParamArr['ordersInfo']['orders_shipping_code'].'
				  </p>
				  <p style="width:40px;height:55px;float:left;font-weight:bold;line-height:60px;font-size:20px;text-align:center;">
				   '.$allParamArr['ordersInfo']['buyer_country_code'].'
				  </p>
				  <p style="width:40px;height:55px;float:left;font-weight:bold;line-height:60px;font-size:20px;text-align:center;">
				    P
				  </p>
				  <p style="width:96px;height:55px;float:left;border:2px solid #000;font-size:20px;text-align:center;">
				   Electric<br/>
				   '.$allParamArr['productsInfo']['battery'].'
				  </p>
				</div>
			</div>
		';
		return $reStr;
	}
	
	//打印LBC面单
	public function LBCTemplate($allParamArr){
		$reStr = '
	 		<style>
			   *{margin:0;padding:0;}
				body{ font-family:Arial, Helvetica, sans-serif; font-size:14px;}
				#main_frame_box{width:382px; margin:0 auto;height:378px;overflow:hidden;margin-bottom:2px;}
				td{border:1px solid #000;}
			</style>
	 	';
	   	$reStr .='<div id="main_frame_box">
		 		      <table border="0" style="width:382px;height:375px;"  cellspacing="0" cellpadding="0">
		 		         <tr height="12%">
		 		           <td colspan="2">
		 		              <p style="float:left;width:298px;height:50px;margin-top:5px;text-align:center;">
		 		                  Package Number:'.$allParamArr['pagenumber'].'<br/>
		 		                  <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=30&r=1&text='.$allParamArr['pagenumber'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		 		                  
		 		              </p>
		 		              <p style="float:left;width:80px;height:100%;text-align:center;line-height:20px;font-weight:bold;font-size:14px;border-left:1px solid #000;">
		 		                 Parcel<br/> Green
		 		              </p>
		 		           </td>
		 		         </tr>
		 		          <tr height="35%">
		 		           <td colspan="2" style="border-top:none;">
		 		             <p style="float:left;width:265px;margin-left:10px;height:100%;text-align:left;overflow:hidden;word-wrap:break-word;font-size:13px;">
		 		                '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
								'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
								'.$allParamArr['ordersInfo']['buyer_city'].'<br/>
								'.$allParamArr['ordersInfo']['buyer_state'].'<br/>
								'.$allParamArr['ordersInfo']['buyer_zip'].' '.$allParamArr['buyerCountry'].'
		 		              </p>
		 		              <p style="float:left;width:98px;height:100%;text-align:center;font-weight:bold;font-size:15px;">
		 		                 '.$allParamArr['ordersInfo']['buyer_phone'].'
		 		                 <br/><br/><br/><br/><br/>
		 		                                      【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】
		 		              </p>
		 		           </td>
		 		         </tr>
		 		          <tr height="15%">
		 		           <td colspan="2" style="border-top:none;">
		 		              <p style="float:left;width:238px;height:50px;margin-top:5px;text-align:center;">
		 		                  <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=30&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		 		                  <br/>'.$allParamArr['ordersInfo']['orders_shipping_code'].'
		 		              </p>
		 		              <p style="float:left;width:140px;height:100%;text-align:center;line-height:20px;font-size:12px;border-left:1px solid #000;">
		 		                 <span style="font-weight:bold;">Payment Method:</span>
		 		                 <br/>
		 		                 Pre-paid
		 		              </p>
		 		           </td>
		 		         </tr>
		 		          <tr height="25%" style="text-align:center;">
		 		           <td colspan="2" style="border-top:none;">
		 		             <img src="'.site_url('attachments').'/images/LBC.jpg" style="width:370px;height:80px;"/>
		 		             Sold and fulfilled by:'.$allParamArr['shipName'].'
		 		             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		 		             '.$allParamArr['ordersInfo']['erp_orders_id'].'
		 		           </td>
		 		         </tr>
		 		      </table>
				</div>';
		return $reStr;
	}
	
	//打印wish邮的面单
	public function wishTemplate($allParamArr){
		$country_fenjian  = array(
		    'RU' => 21,'US' => 22,'GB' => 23,'BR' => 24,
			'AU' => 25,'FR' => 26,'ES' => 27,'CA' => 28,
			'IL' => 29,'IT' => 30,'DE' => 31,'CL' => 32,
			'SE' => 33,'BY' => 34,'NO' => 35,'NL' => 36,
			'UA' => 37,'CH' => 38,'MX' => 39,'PL' => 40,
		);
		$reStr = '
	 		<style>
			   *{margin:0;padding:0;}
				body{ font-family:Arial, Helvetica, sans-serif; font-size:14px;}
				#main_frame_box{width:382px; margin:0 auto;height:378px;overflow:hidden;margin-bottom:2px;}
				td{border:1px solid #000;border-bottom:none;}
			</style>
	 	';
	   	$reStr .='<div id="main_frame_box">
		        	<div style="width:379px;height:150px;border:1px solid #000;border-bottom:none;">
			 		  <p style="float:left;width:140px;height:30px;">
			 		    <img src="'.site_url('attachments').'/images/EUB01.jpg" />
			 		  </p>
			 		  <p style="float:left;width:120px;height:30px;text-align:center;font-size:12px;font-weight:bold;line-height:30px;border-right:1px solid #000;">
			 		  Small Packet By Air
			 		  </p>
			 		  <p style="float:left;width:50px;line-height:30px;text-align:center;font-weight:bold;height:30px;border-right:1px solid #000;">
			 		    '.$allParamArr['country_code'].$country_fenjian[$allParamArr['country_code']].'
			 		  </p>
			 		  <p style="float:left;width:64px;height:30px;line-height:30px;font-size:14px;font-weight:bold;">
			 		    wish郵
			 		  </p>
			 		  <p style="float:left;width:140px;">
			 		     <span style="word-wrap: break-word;width:130px;height:71px;font-family:STHeiti;display:inline-block;border-bottom:1px solid #000;font-size:8px;padding-left:4px;">
			 		       From:<br/>
			 		       SLME<br/>
			 		       '.$allParamArr['senderInfo']['street'].'<br/>
			 		       <b style="font-weight:bold;">Phone:'.$allParamArr['senderInfo']['mobilePhone'].'</b>
			 		     </span>
			 		     <span style="width:140px;line-height:29px;font-size:10px;background:#fff;display:inline-block;border-bottom:1px solid #000;">
			 		       	自编号:'.$allParamArr['ordersInfo']['erp_orders_id'].'
			 		     </span>
			 		  </p>
			 		  <p style="float:left;width:235px;height:99px;border:1px solid #000;border-right:none;font-family:STHeiti;font-size:12px;">
			 		    <span style="font-size:12px;font-family:STHeiti;">Ship To:</span>
			 		    	'.$allParamArr['ordersInfo']['buyer_name'].'&nbsp;&nbsp;&nbsp;&nbsp;<br/>
							'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'
							'.$allParamArr['ordersInfo']['buyer_city'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_state'].'
							'.$allParamArr['ordersInfo']['buyer_zip'].'<br/>
							'.$allParamArr['buyerCountry'].'&nbsp;'.$this->wishPartition($allParamArr['country_cn']).'
							
							'.$allParamArr['country_cn'].' '.$allParamArr['ordersInfo']['area'].'<br/>
							Phone：'.$allParamArr['ordersInfo']['buyer_phone'].'
			 		  </p>
		 		    </div>
		 		    
		 		      <table border="0" style="width:382px;height:155px;"  cellspacing="0" cellpadding="0">
		 		         <tr height="45">
		 		           <td colspan="3" style="border-top:none;">
		 		              <p style="width:86px;text-align:center;font-weight:bold;line-height:50px;height:50px;float:left;">
		 		              	Untracked
		 		              </p>
		 		              <p style="width:270px;height:45px;float:left;text-align:center;">
		 		                <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=15&r=2&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		 		                '.$allParamArr['ordersInfo']['orders_shipping_code'].'
		 		              </p>
		 		           </td>
		 		         </tr>
		 		         <tr height="15">
		 		             <td colspan="3" style="font-size:10px;font-weight:bold;">
		 		             
		 		               	退件单位:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['senderInfo']['back_street'].'
		 		             </td>
		 		         </tr>
		 		         <tr style="height:15px;font-weight:bold;font-size:10px;text-align:center;">
		 		           <td width="70%" style="border-right:none;">
		 		             Description of Contents
		 		           </td>
		 		           <td width="15%" style="border-right:none;">
		 		            Kg
		 		           </td>
		 		           <td width="15%">
		 		            Val(US $)
		 		           </td>
		 		         </tr>
		 		         '.$allParamArr['trInfo'].'
		 		         <tr height="15" style="font-size:12px;">
		 		           <td width="70%" style="border-right:none;font-size:12px;">
		 		             Totalg Gross Weight(kg)
		 		           </td>
		 		           <td width="15%" style="border-right:none;">'.$allParamArr['productsInfo']['total_weight'].'</td>
		 		           <td width="15%">'.$allParamArr['productsInfo']['total_value'].'</td>
		 		         </tr>
		 		         <tr height="55">
		 		           <td colspan="3" style="border-bottom:1px solid #000;font-size:9px;">
		 		             I certify that the particulars given in this declaration are correct and this item does not contain any dangerous 
articles prohibited by legislation or by postal or customers regulations.<br/>
		 		             <span style="font-weight:bold;font-size:11px;">Sender\'s signiture& Data Signed :SLME '.date('Y-m-d').'</span>
		 		             &nbsp;&nbsp;&nbsp;
		 		             <span style="font-weight:bold;display:inline-block;border:2px solid #000;width:83px;line-height:15px;height:15px;font-size:14px;">
		 		             	已验视CN22
		 		             </span>
		 		           </td>
		 		         </tr>
		 		      </table>
		 		      <div style="width:382px;height:40px;margin:0 auto;font-size:10px;white-space:normal;overflow:hidden;">
		 				'.$allParamArr['skuInfo'].'<b style="float:right;font-size:11px;">【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</b>
		 		      </div>
				</div>';
		return $reStr;
	}
	
	//打印中国邮政平常小包+面单（SMT线上发货）
	public function smtLineShippingTemplate($allParamArr){

		$reStr = '
	 		<style>
			   *{margin:0;padding:0;}
				body{ font-family:Arial, Helvetica, sans-serif; font-size:14px;}
				#main_frame_box{width:382px; margin:0 auto;height:378px;overflow:hidden;margin-bottom:2px;}
				td{border:1px solid #000;border-bottom:none;}
			</style>
	 	';
	   	$reStr .='<div id="main_frame_box">
		        	<div style="width:380px;border:1px solid #000;border-bottom:none;">
			 		  <p style="float:left;width:140px;height:90px;border-left:1px solid #000;">
			 		    <img src="'.site_url('attachments').'/images/post_logo.jpg" style="width:140px;height:50px;"/>
			 		    <span style="font-size:10px;">Small Packet By Air</span>
			 		    <br/>
			 		    <span style="display:inline-block;width:55px;height:22px;border:2px solid #000;margin-left:40px;text-align:center;font-size:18px;font-weight:bold;">
			 		      '.$allParamArr['country_code'].$allParamArr['country_Info']['sort_code'].'
			 		    </span>
			 		  </p>
			 		  <p style="float:left;width:238px;height:90px;text-align:center;border-right:1px solid #000;">
			 		      <span style="display:inline-block;margin-top:12px;margin-left:40px;">
		 		              <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		 		              '.$allParamArr['ordersInfo']['orders_shipping_code'].'
		 		          </span>
		 		           <span style="font-weight:bold;font-size:11px;display:inline-block;">Untracked 平小包</span>
			 		  </p>
			 		  
			 		  <p style="float:left;width:140px;">
			 		     <span style="width:140px;display:inline-block;height:97px;border-left:1px solid #000;border-bottom:1px solid #000;font-size:11px;padding-left:4px;">
			 		       From:<br/>
			 		       '.$allParamArr['senderInfo']['street'].'<br/>
			 		       <b style="font-weight:bold;">Phone:'.$allParamArr['senderInfo']['mobilePhone'].'</b>
			 		     </span>
			 		     <span style="width:140px;font-size:16px;line-height:29px;background:#fff;display:inline-block;border-left:1px solid #000;">
			 		       	'.$allParamArr['warehouse_flag'].'
			 		     </span>
			 		  </p>
			 		  <p style="float:left;width:238px;border:1px solid #000;border-bottom:none;font-size:12px;">
			 		    <span style="font-weight:bold;font-size:12px;">Ship To:</span><br/>
			 		    	'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_city'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_state'].'<br/>
							'.$allParamArr['buyerCountry'].' '.$allParamArr['ordersInfo']['buyer_zip'].'<br/>
							phone:'.$allParamArr['ordersInfo']['buyer_phone'].'&nbsp;&nbsp; &nbsp; <span style="font-size:16px;">'.$allParamArr['country_cn'].'</span>
							&nbsp;&nbsp; &nbsp;'.$allParamArr['country_Info']['zone'].'
			 		  </p>
		 		    </div>
		 		    
		 		      <table border="0" style="width:382px;height:110px;"  cellspacing="0" cellpadding="0">
		 		         
		 		         <tr style="height:15px;font-weight:bold;font-size:10px;text-align:center;">
		 		           <td width="70%" style="border-right:none;">
		 		             Description of Contents
		 		           </td>
		 		           <td width="15%" style="border-right:none;">
		 		            Kg
		 		           </td>
		 		           <td width="15%">
		 		            Val(USD $)
		 		           </td>
		 		         </tr>
		 		         '.$allParamArr['trInfo'].'
		 		         <tr height="15" style="font-size:12px;">
		 		           <td width="70%" style="border-right:none;font-size:12px;">
		 		             Totalg Gross Weight(kg)
		 		           </td>
		 		           <td width="15%" style="border-right:none;">'.$allParamArr['productsInfo']['total_weight'].'</td>
		 		           <td width="15%">'.$allParamArr['productsInfo']['total_value'].'</td>
		 		         </tr>
		 		         <tr height="55">
		 		           <td colspan="3" style="border-bottom:1px solid #000;font-size:9px;">
		 		             I the undersigned,certify that the particulars given in this declaration are correct and this item 
		 		             does not contain any dangerous articles prohibited by legislation or by postal or customers 
		 		             regulations.<br/>
		 		             <span style="font-weight:bold;font-size:12px;">Sender\'s signiture& Data Signed :SLME</span>
		 		             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		 		             <span style="font-weight:bold;display:inline-block;width:60px;line-height:15px;height:15px;font-size:14px;">
		 		             	CN22
		 		             </span>
		 		           </td>
		 		         </tr>
		 		      </table>
		 		      <div style="width:382px;height:40px;margin:0 auto;font-size:10px;white-space:normal;overflow:hidden;">
		 				<span style="font-size:12px;font-weight:bold;">【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</span>'.$allParamArr['skuInfo'].'
		 		      </div>
				</div>';
		return $reStr;
	}
	
	//lazada印尼面单
	public function lazadaTemplate($allParamArr){
	 $reStr ='<div style="width:380px;margin:0 auto;height:15px;font-size:13px;text-align:right;"> ID4&nbsp;&nbsp;&nbsp;</div>';
	 $reStr .='
	   <div style="width:380px;margin:0 auto;border:1px solid #000;height:471px;overflow:hidden;">
			 <table style="width:370px">
			<tbody>
				<tr>
					<td>
					<div style="font-size:18px;margin-bottom:1px;">
						<span><strong><u style="text-decoration:underline">Tracking number</u></strong></span>
						<span style="padding-left:100px;font-size:12px;font-weight:bold;">
						'.$allParamArr['ordersInfo']['erp_orders_id'].'
						&nbsp;&nbsp;&nbsp;&nbsp;
						
						</span>
					</div>
					<div style="text-align:center;">
					<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
					<div style="font-size: 14px;">'.$allParamArr['ordersInfo']['orders_shipping_code'].'</div>
					</div>
					</td>
				</tr>
				<tr>
					<td>
					<div style="font-size:11px"><strong>Shipper:</strong><br />
					'.$allParamArr['shipName'].'<br />
					'.$allParamArr['senderInfo']['street'].'<br />
					Phone number:'.$allParamArr['senderInfo']['mobilePhone'].'</div>
					</td>
				</tr>
				<tr>
					<td>
					<div>
					<div style="font-size:11px"><span><strong>Penerima :</strong></span></div>
		
					<div style="font-size:11px;"><span>
					
							'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_city'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_state'].'<br/>
							'.$allParamArr['buyerCountry'].' '.$allParamArr['ordersInfo']['buyer_zip'].'
							
					</span></div>
		
					<div style="font-size:11px;"><span><span>Phone number:&nbsp;'.$allParamArr['ordersInfo']['buyer_phone'].'</span></span></div>
					</div>
					</td>
				</tr>
				<tr>
				</tr>
				<tr>
					<td>
					<div style="font-size:11px"><strong>Items:</strong><br />
					   <span style="width:15px;display:inline-block;font-weight:bold;">#</span>
					   <span style="width:140px;display:inline-block;font-weight:bold;">Product name</span>
					   <span style="width:100px;display:inline-block;font-weight:bold;">Seller Sku</span>
					   <span style="width:90px;display:inline-block;font-weight:bold;">Shop Sku</span>
					   
					   <span style="width:15px;display:inline-block;font-weight:bold;">1</span>
					   <span style="width:140px;display:inline-block;font-weight:bold;">'.$allParamArr['productsInfo'][0]['products_declared_en'].'</span>
					   <span style="width:100px;display:inline-block;font-weight:bold;">'.$allParamArr['productsInfo'][0]['orders_sku'].'</span>
					   <span style="width:90px;display:inline-block;font-weight:bold;"></span>
					</div>
					</td>
				</tr>
				<tr>
					<td>
					<div style="font-size:11px;margin-top:0px;"><span><strong>Metode Pembayaran:&nbsp;</strong></span><span>'.$allParamArr['ordersInfo']['pay_method'].'</span></div>
					'.$allParamArr['declare_value'].'
					</td>
				</tr>
				<tr>
					<td>
					<div style="font-size:12px;margin-bottom:5px;">
					<span><strong><u style="text-decoration:underline">package number</u></strong></span>
					<span style="padding-left:200px;font-size:14px;font-weight:bold;">【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</span>
					</div>
					<div style="text-align:center;">
					  <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['pagenumber'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
					  <div style="font-size: 11px;"><span>'.$allParamArr['pagenumber'].'</span></div>
					</div>
					</td>
				</tr>
				<tr>
					<td>
					<div><img alt="logo_zps0515ee1e.jpg" src="http://i1378.photobucket.com/albums/ah107/listianpratomo/logo_zps0515ee1e.jpg" style="height:20px; width:122px" /></div>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
	 ';
	 return $reStr;
	}
	
	
	//打印lazada泰国发货面单
	public function lazadaThTemplate($allParamArr){

		$reStr = '
	 		<style>
			   *{margin:0;padding:0;}
				body{ font-family:Arial, Helvetica, sans-serif; font-size:14px;}
				#main_frame_box{width:382px; margin:0 auto;height:128mm;overflow:hidden;margin-bottom:2px;border-bottom:1px solid #000;}
				td{border:1px solid #000;border-bottom:none;}
			</style>
	 	';
	   	$reStr .='<div id="main_frame_box">
		        	
		 		      <table border="0" style="width:382px;height:128mm;"  cellspacing="0" cellpadding="0">
		 		         <tr height="100">
		 		         
		 		           <td style="border-right:none;width:35%;text-align:center;font-weight:bold;">
		 		              '.$allParamArr['ordersInfo']['buyer_id'].'<br>【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】
		 		           </td>
		 		           
		 		           <td style="border-right:none;width:58%;text-align:center;font-weight:bold;">
		 		             EMS Tracking No:<br/>
		 		             <p style="font-size:5px;">&nbsp;</p>
		 		             <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=30&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
					  		<br/>
					  		'.$allParamArr['ordersInfo']['orders_shipping_code'].'
		 		           </td>
		 		           
		 		           <td style="width:7%;text-align:center;font-weight:bold;">
		 		            TH1
		 		           </td>
		 		           
		 		         </tr>
		 		         <tr height="70">
		 		           <td colspan="3" style="text-align:center;">
		 		             Package No:<br/>
		 		             <p style="font-size:5px;">&nbsp;</p>
		 		             <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=25&r=1&text='.$allParamArr['pagenumber'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
					  		<br/>
					  		<spans style="font-weight:bold;">'.$allParamArr['pagenumber'].'</span>
		 		           </td>
		 		         </tr>
		 		         <tr height="200" style="overflow:hidden;">
		 		           <td style="border-right:none;width:35%;text-align:center;font-weight:bold;">
		 		             '.$allParamArr['shipName'].'<br/>
		 		            	 ชื่อบริษัท: 
								กรณีนำจ่ายไม่ได้ กรุณาส่งคืน ศป.EMS 10020<br/>
								<img src="'.site_url('attachments').'/images/TH_label_lzd_log.png" style="width:120px;"/>
		 		           </td>
		 		           <td colspan="2">
		 		            '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_city'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_state'].'<br/>
							'.$allParamArr['buyerCountry'].' &nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_zip'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_phone'].'
							
		 		           </td>
		 		         </tr>
		 		         <tr height="50" style="font-weight:bold;">
		 		           <td style="border-right:none;width:35%;">
		 		          	  ไม่เก็บเงินค่าสินค้า
		 		           </td>
		 		           <td colspan="2">
		 		             '.$allParamArr['ordersInfo']['buyer_zip'].'
		 		           </td>
		 		         </tr>
		 		      </table>
		 		     
				</div>';
		return $reStr;
	}
	
	//打印lazada新加坡面单100*100
	public function lazadaSgTemplate($allParamArr){

		$reStr = '
	 		<style>
			   *{margin:0;padding:0;}
				body{ font-family:Arial, Helvetica, sans-serif; font-size:14px;}
				#main_frame_box{width:382px; margin:0 auto;height:378px;overflow:hidden;margin-bottom:2px;border:1px solid #000;}
				table { width: 100%; border: 0;}
				.border_r_b { border-right: 1px solid black; border-bottom: 1px solid black;}
				.border_b { border-bottom: 1px solid black;}
				.border_r { border-right: 1px solid black;}
				.border_t_r_l{ border-top: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;}
				.border_r_b_l{ border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;}
				.border_r_l { border-right: 1px solid black; border-left: 1px solid black;}
				.border {border: 1px solid black;}
				.fontSize10 { font-size: 10px;}
				.fontSize11 { font-size: 11px;}
				.fontSize12{ font-size: 12px;}
				.fixed_box{ position: absolute; right: 0px; bottom: 0px; width: 30px; height: 30px; font-size: 28px; font-weight: bold; z-index:100;}
			</style>
	 	';
	   	$reStr .='<div id="main_frame_box">
		        	<div style="width:100%;height:15px;text-align:right;font-weight:bold;">
		        	 	SG2【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		        	</div>
		        	<div style="width:100%;height:7px;">
		        	 	&nbsp;
		        	</div>
		 		    <div style="width:100%;height:45px;">
		 		       <p style="width:40%;height:45px;float:left;font-weight:bold;line-height:45px;">
		 		         &nbsp;&nbsp;&nbsp; Registered
		 		         &nbsp;&nbsp;&nbsp;
		 		         <span style="font-size:19px;">R</span>
		 		       </p>
		 		       <p style="width:60%;height:45px;float:left;text-align:center;">
		 		           <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=30&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		 		           <br/>
		 		           '.$allParamArr['ordersInfo']['orders_shipping_code'].'
		 		       </p>
		 		    </div>
		 		    <div style="width:100%;height:7px;">
		        	 	&nbsp;
		        	</div>
		 		    <div style="width:100%;height:80px;">
		 		       <p style="width:50%;height:80px;float:left;font-size:11px;line-height:10px;">
		 		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if undelivered,please return to:<br/>
		 		            &nbsp;<br/>
		 		        	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Locked Bag No. 1335<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Special Project Unit,<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pos Malaysia International Hub<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pos Malaysia Berhad<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jalan KLIA 1<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;64009 Malaysia<br/>
		 		       </p>
		 		       <p style="width:35%;margin-left:20px;height:80px;float:left;text-align:center;border:1px solid #000;font-size:10px;font-weight:bold;line-height:12px;">
		 		            BAYARAN POS JELAS<br/>
							POSTAGE PAID<br/>
							POS MALAYSIA<br/>
							INTERNATIONAL HUB<br/>
							PMIH, MALAYSIA<br/>
							PMK 1335
		 		       </p>
		 		    </div>
		 		    <div style="width:100%;height:110px;margin-top:5px;">
		 		      <p style="width:70%;height:100px;float:left;font-size:13px;line-height:12px;font-weight:bold;margin-top:10px;">
		 		           &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deliver To:<br/>
		 		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_city'].'<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_state'].'<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_zip'].','.$allParamArr['buyerCountry'].'<br/>
		 		       </p>
		 		       <p style="width:15%;margin-left:20px;margin-top:5px;height:40px;line-height:40px;float:left;text-align:center;border:3px solid #000;font-size:26px;font-weight:bold;">
		 		            SG
		 		       </p>
		 		    </div>
		 		    <div style="width:100%;height:60px;font-size:12px;">
		 		       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;">TEL:'.$allParamArr['ordersInfo']['buyer_phone'].'
		 		       &nbsp;
		 		       Contact Person:'.$allParamArr['ordersInfo']['buyer_name'].'</span><br/>
		 		       <div style="width:100%;height:7px;">
		        	 	&nbsp;
		        	   </div>
		 		       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;orderId: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_id'].'<br/>
		 		       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;weight: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['productsInfo']['total_weight'].'
		 		    </div>
		 		    <div style="width:100%;height:55px;">
		 		       <p style="width:40%;height:55px;float:left;font-weight:bold;line-height:55px;text-align:center;">
		 		         300182_SZX
		 		       </p>
		 		       <p style="width:60%;height:35px;float:left;text-align:center;">
		 		           <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=30&r=1&text='.$allParamArr['pagenumber'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		 		           <br/>
		 		           '.$allParamArr['pagenumber'].'
		 		       </p>
		 		    </div>
				  </div>';
	   	$reStr .='
		 <div style="height:375px;width:382px; margin:0 auto;overflow:hidden;">
			  
		            <table cellpadding="5" cellspacing="0">
		                <tr style="font-size:12px;">
		                    <td>
		                    	<table cellspacing="0">
		                    		<tr>
		                    			<td colspan="6" class="border">
		                    				<table cellspacing="0" cellpadding="0">
					                    		<tr>
					                    			<td style="font-weight: bold;">
					                    				CUSTOMS<br/>
					                    				DECLARATION
					                    			</td>
					                    			<td>
					                    				May be opened<br/>
					                    				officially
					                    			</td>
					                    			<td style="font-size: 20px; font-weight: bold;">
					                    				CN22
					                    			</td>
					                    		</tr>
					                    	</table>
		                    			</td>
		                    		</tr>
		                    		<tr>
		                    			<td colspan="6" class="border_r_b_l">
		                    				<table cellspacing="0" cellpadding="0">
		                    					<tr>
		                    						<td width="140" style="font-weight: bold;">Designated operator</td>
		                    						<td align="right" valign="top">
		                    						
		                    							Important!<br/>See instructions on the back</td>
		                    					</tr>
		                    				</table>
		                    			</td>
		                    		</tr>
		                    		<tr height="30">
		                    			<td width="18" height="18" class="border_r_b_l" align="center"></td>
		                    			<td width="100" style="line-height: 14px;">Gift</td>
		                    			<td width="18" class="border_r_b_l"></td>
		                    			<td width="200" colspan="3" class="border_r">Commerical Sample</td>
		                    		</tr>
		                    		<tr>
		                    			<td class="border_r_b_l" height="30"></td>
		                    			<td class="border_b">Documents</td>
		                    			<td class="border_r_b_l" align="center" style="font-family: 宋体; font-weight: bold; font-size: 16px;">&radic;</td>
		                    			<td colspan="3" class="border_r_b">
		                    				<div style="position: relative;">
		                    					Other
		                    					<span style="position: absolute; right: 0; bottom: -4px;">Tick one or more boxes</span>
		                    				</div>
		                    			</td>
		                    		</tr>
		                    		<tr style="line-height: 12px;" height="35">
		                    			<td colspan="4" class="border_r_b_l">
		                    				Quantity and detailed description <br/>of contents(1)
		                    			</td>
		                    			<td width="25%" class="border_r_b">
		                    				Weight(in kg)
		                    			</td>
		                    			<td width="25%" class="border_r_b">
		                    				Value(3)
		                    			</td>
		                    		</tr>
		                    		'.$allParamArr['trInfo'].'
		                    		<tr style="line-height: 12px;height:50px">
		                    			<td colspan="4" class="border_r_b_l" style="line-height: 12px;">
		                    				For commericial items only<br/>If known, HS tariff number(4) <br/>and country of origin of goods(5)
		                    			</td>
		                    			<td class="border_r">
		                    				Total Weight<br/>(in kg)(6)
		                    			</td>
		                    			<td class="border_r">
		                    				Total value(7)
		                    			</td>
		                    		</tr>
		                    		<tr style="line-height: 14px;">
		                    			<td colspan="4" class="border_r_b_l"></td>
		                    			<td class="border_r_b">
		                    			'.$allParamArr['productsInfo']['total_weight'].'
		                    			</td>
		                    			<td class="border_r_b">
		                    			'.$allParamArr['productsInfo']['total_value'].'('.$allParamArr['ordersInfo']['currency_type'].')
		                    			</td>
		                    		</tr>
		                    		<tr style="line-height: 12px;height:80px;">
		                    			<td colspan="6" class="border_r_b_l" style="word-wrap: normal; word-break: keep-all;">
		                    				I,the undersigned, whose name and address are given on the item, certity that the particulars given 
						                	in this declaration are correct and that this item does not contain 
						                	any dangerous article or articles pro-hibited by legislation or by postal or 
						                	customs regulations<br/>
						                	Date and sender\'s signature(8) 
		                    			</td>
		                    		</tr>
		                    	</table>
		                    </td>
		            </table>
				
			</div>
		';
		return $reStr;
	}
	
	//顺丰俄罗斯平邮面单(130x100模板)
	public function newShunFengRussiaTemplate($allParamArr){
		$reStr='<style>
							*{margin:0;padding:0;}
							body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
							#main_frame_box{width:100mm;}
							table { width: 100%; border: 0;}
							.fixed{ table-layout: fixed; }
							.float_box { position: relative; float: left; width: 100mm; height: 130mm; overflow: hidden;}
							.border_r_b { border-right: 1px solid black; border-bottom: 1px solid black;}
							.border_b { border-bottom: 1px solid black;}
							.border_r { border-right: 1px solid black;}
							.border_t_r_l{ border-top: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;}
							.border_r_b_l{ border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;}
							.border_r_l { border-right: 1px solid black; border-left: 1px solid black;}
							.border {border: 1px solid black;}
							.fontSize10 { font-size: 10px;}
							.fontSize11 { font-size: 11px;}
							.fontSize12{ font-size: 12px;}
							.fixed_box{ position: absolute; right: 0px; bottom: 0px; width: 30px; height: 30px; font-size: 28px; font-weight: bold; z-index:100;}
							p { margin: 0; padding: 0;}
						</style>';
		$reStr.='<div id="main_frame_box">';
		$reStr.='
		 <div class="float_box fontSize12">
            <table cellpadding="5" cellspacing="0" class="fixed">
                <tr>
                    <td>
                    	<table cellpadding="1" cellspacing="0">
                    		<tr>
                    			<td colspan="6" class="border">
                    				<table cellspacing="0" cellpadding="0">
			                    		<tr>
			                    			<td style="font-weight: bold;">
			                    				CUSTOMS<br/>
			                    				DECLARATION
			                    			</td>
			                    			<td>
			                    				May be opened<br/>
			                    				officially
			                    			</td>
			                    			<td style="font-size: 20px; font-weight: bold;">
			                    				CN22
			                    			</td>
			                    		</tr>
			                    	</table>
                    			</td>
                    		</tr>
                    		<tr>
                    			<td colspan="6" class="border_r_b_l">
                    				<table cellspacing="0" cellpadding="0">
                    					<tr>
                    						<td width="140" style="font-weight: bold;">Designated operator</td>
                    						<td align="right" valign="top">
                    						
                    							Important!<br/>See instructions on the back</td>
                    					</tr>
                    				</table>
                    			</td>
                    		</tr>
                    		<tr>
                    			<td width="18" height="18" class="border_r_b_l" align="center"></td>
                    			<td width="100" style="line-height: 14px;">Gift</td>
                    			<td width="18" class="border_r_b_l"></td>
                    			<td width="200" colspan="3" class="border_r">Commerical Sample</td>
                    		</tr>
                    		<tr>
                    			<td class="border_r_b_l" height="18"></td>
                    			<td class="border_b">Documents</td>
                    			<td class="border_r_b_l" align="center" style="font-family: 宋体; font-weight: bold; font-size: 16px;">&radic;</td>
                    			<td colspan="3" class="border_r_b">
                    				<div style="position: relative;">
                    					Other
                    					<span style="position: absolute; right: 0; bottom: -4px;">Tick one or more boxes</span>
                    				</div>
                    			</td>
                    		</tr>
                    		<tr style="line-height: 12px;">
                    			<td colspan="4" class="border_r_b_l">
                    				Quantity and detailed description <br/>of contents
                    			</td>
                    			<td width="79" class="border_r_b">
                    				Weight(in kg)
                    			</td>
                    			<td width="79" class="border_r_b">
                    				Value
                    			</td>
                    		</tr>
                    		<tr style="line-height: 12px; height: 44px;">
                    			<td colspan="4" class="border_r_b_l" valign="top" width="60%">
                    			'.$allParamArr['productsInfo']['namefiles'].'
                    			
                    			</td>
                    			<td class="border_r_b" valign="top">
                    			'.$allParamArr['productsInfo']['totalWeight'].'
                    			</td>
                    			<td class="border_r_b" valign="top">
                    			'.$allParamArr['productsInfo']['totalPrice'].' '.$allParamArr['productsInfo']['currency'].'
                    			</td>
                    		</tr>
                    		<tr style="line-height: 12px;">
                    			<td colspan="4" class="border_r_b_l" style="line-height: 12px;">
                    				For commericial items only<br/>If known, HS tariff number <br/>and country of origin of goods
                    			</td>
                    			<td class="border_r">
                    				Total Weight<br/>(in kg)
                    			</td>
                    			<td class="border_r">
                    				Total value
                    			</td>
                    		</tr>
                    		<tr style="line-height: 14px;">
                    			<td colspan="4" class="border_r_b_l">CN</td>
                    			<td class="border_r_b">
                    			'.$allParamArr['productsInfo']['totalWeight'].'
                    			</td>
                    			<td class="border_r_b">
                    			'.$allParamArr['productsInfo']['totalPrice'].' '.$allParamArr['productsInfo']['currency'].'
                    			</td>
                    		</tr>
                    		<tr style="line-height: 12px;height:100px;">
                    			<td colspan="6" class="border_r_b_l" style="word-wrap: normal; word-break: keep-all;">
                    				I,the undersigned, whose name and address are given on the item, certity that the particulars given 
				                	in this declaration are correct and that this item does not contain 
				                	any dangerous article or articles pro-hibited by legislation or by postal or 
				                	customs regulations<br/>
				                	Date and sender\'s signature:'.$allParamArr['productsInfo']['time'].'  SLME
                    			</td>
                    		</tr>
                    		<tr>
                    		  <td colspan="5" class="border_r_b_l" style="word-wrap: normal; word-break: keep-all;height:100px;" width="60%">
                    		   <div style="width:70%;height:100px;float:left;">
                    		     <p style="width:42mm;height:13mm;margin:10px 0 0 17px;">
                    		       <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
                    		       <br>
                    		       <p style="font-weight:bold;font-size:16px;width:42mm;margin-left:25px;">'.$allParamArr['ordersInfo']['orders_shipping_code'].'</p>
                    		     </p>
                    		   </div>
                    		   <div style="width:30%;height:100px;float:right;">
                    		     <p style="height:50px;line-height:50px;padding-left:20px;font-size:24px;">RU</p>
                    		     <p style="height:50px;line-height:50px;padding-left:7px;font-size:30px;text-align:center;">P</p>
                    		   </div>
                    		  </td>
                    		  <td class="border_r_b_l" style="word-wrap: normal; word-break: keep-all;border-left:none;height:100px;" width="40%">
                    		    <p style="height:50px;width:70px;font-size:16px;margin:0 auto;margin-top:5px;">Electric Product</p>
                    		    <p style="height:42px;width:70px;font-size:16px;margin:0 auto;font-size:30px;text-align:center;">N</p>
                    		  </td>
                    		</tr>
                    		<tr>
                    		  <td colspan="6">
                    		  <span style="display:inline-block;font-size:16px;font-weight:bold;">【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】【S'.$allParamArr['ordersInfo']['erp_orders_id'].'】</span>
                    		   '.$allParamArr['productsInfo']['skufiles'].'
                    		  </td>
                    		</tr>
                    	</table>
                    </td>
                </tr>
                
            </table>
			
        </div>
		';
		$reStr.='</div>';
		
		return $reStr;
	}
	
	//贝邮宝面单模板
	function PpbybTemplate($allParamArr){
		$reStr='<style>
					 body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
					 #main_frame_box{width:100mm; margin:0 auto;}
					 table { width: 100%; border: 0;}
					.table_size{ width: 360px; height: 360px;  overflow: hidden;}
					.fixed{ table-layout: fixed; }
					.float_box { position: relative; float: left; width: 100mm; height: 95mm; overflow: hidden; margin: 0; border: 0;}
					.table{ border:1px solid #000; border-width: 1px 0px 0px 1px;}
					.table td{border:1px solid #000; border-width: 0px 1px 1px 0px;}
					.table_clear td{ border: 0;}
					.fontSize8 { font-size: 8px;}
					.fontSize10 { font-size: 10px;}
					.fontSize11 { font-size: 11px;}
					.fontSize12{ font-size: 12px;}
					.fontSize14{ font-size: 14px;}
					.fontSize16{ font-size: 16px;}
					.line10{ line-height: 9px;}
					.line12{ line-height: 12px;}
					.fixed_box{ position: absolute; right: 0px; bottom: 0px; width: 30px; height: 30px; font-size: 28px; font-weight: bold; z-index:100;}
					p { margin: 0; padding: 0;}
			</style>';
		$reStr.='<div id="main_frame_box">';
		$reStr.='
				 <div class="float_box">
		            <table cellpadding="2" cellspacing="0" class="table table_size">
		            	<tr>
		            		<td colspan="4">
		            			<table class="table_clear" cellpadding="0" cellspacing="0">
		            				<tr>
		            					<td>
			            					<img src="'.site_url('attachments').'/images/post_logo.jpg" width="80" height="32" />
			            					<img src="'.site_url('attachments').'/images/ppbyb.jpg" width="70" height="32" />
		            					</td>
		            					<td align="center" class="fontSize12" width="100">
		            						<b>航 空</b><br/>
		            						Small Packet<br/>
		            						BY AIR
		            					</td>
		            					<td width="90" align="center">
		            						<b>'.$allParamArr['ordersInfo']['buyer_country_code'].' '.$allParamArr['country'].'</b>
		            					</td>
		            				</tr>
		            			</table>
		            		</td>
		            	</tr>
		            	<tr class="fontSize11">
		            		<td width="75"><b>协议客户：</b></td>
		            		<td colspan="3"><b>北京京腾一诺科技有限公司(11010503959000)</b></td>
		            	</tr>
		            	<tr class="fontSize11" height="55">
		            		<td class="fontSize16"><b>From：</b></td>
		            		<td colspan="3">
		            		xiehongjun<br/>
		            		2F,Building A3,Hekan Industrial Zone,No.41,Wuhe Road South,Longgang District,shenzhen,guangdong,China
		            		</td>
		            	</tr>
		            	<tr class="fontSize11">
		            		<td>Zip：</td>
		            		<td>518129</td>
		            		<td width="60">Tel：</td>
		            		<td>13417539018</td>
		            	</tr>
		            	<tr class="fontSize12" height="60" style="font-weight: bold;">
		            		<td class="fontSize16">To：</td>
		            		<td colspan="3">
		            		'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
		            		'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].' '.$allParamArr['ordersInfo']['buyer_city'].' '.$allParamArr['ordersInfo']['buyer_state'].' '.$allParamArr['display_name'].'
		            		
		            		</td>
		            	</tr>
		            	<tr class="fontSize11">
		            		<td>Zip：</td>
		            		<td>'.$allParamArr['ordersInfo']['buyer_zip'].'</td>
		            		<td>Tel：</td>
		            		<td>'.$allParamArr['ordersInfo']['buyer_phone'].'</td>
		            	</tr>
		            	<tr class="fontSize11">
		            		<td><b>退件单位：</b></td>
		            		<td colspan="3"><b>北京国际邮电局集中收寄网点</b></td>
		            	</tr>
		            	<tr class="fontSize11" align="center" height="65">
		            		<td colspan="4">
		            			<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" /><br/>
		            			'.$allParamArr['ordersInfo']['orders_shipping_code'].'
		            		</td>
		            	</tr>
		            	<tr class="fontSize11">
		            		<td><b>客户自编号：</b></td>
		            		<td colspan="3">
		            			<b>
		            			'.$allParamArr['ordersInfo']['erp_orders_id'].' '.$allParamArr['ordersInfo']['orders_type'].'
		            			</b>
		            		</td>
		            	</tr>
		            </table>
		        </div>
		';
		/**
		 * 去掉贝邮宝报关签条
		 
		$reStr.='
		  <div class="float_box fontSize10">
            <table cellpadding="1" cellspacing="0" class="table table_size">
                <tr>
                    <td colspan="5">
                    	<table class="table_clear" cellspacing="0" cellpadding="0" border="0">
                    		<tr>
                    			<td>
                    				<img src="'.site_url('attachments').'/images/post_logo.jpg" width="80" height="32" border="0">
                    			</td>
                    			<td align="center">
                    				<strong>报关签条</strong><br>
                    				CUSTOMS DECLARATION
                    			</td>
                    			<td>
                    				<b>邮</b> 2113<br>
                    				<span class="fontSize12">CN22</span>
                    			</td>
                    		</tr>
                    	</table>
                    </td>
                </tr>
                <tr class="fontSize8 line10">
        			<td colspan="2">可以径行开拆</td>
        			<td colspan="3">May be opened officially</td>
        		</tr>
        		<tr class="fontSize8 line10" align="center">
        			<td rowspan="2">
        				邮件种类<br>
        				Calegory of item<br>
        				(在适当的文字前划"×")
        			</td>
        			<td align="center" height="20" width="20">
        				X
        			</td>
        			<td width="70">
        				礼品<br>
        				Gift
        			</td>
        			<td width="20" height="20">&nbsp;
        				
        			</td>
        			<td width="70">
        				商品货样<br>
        				Commercial
        			</td>
        		</tr>
        		<tr class="fontSize8 line10" align="center">
        			<td height="20" width="20">&nbsp;</td>
        			<td>
        				文件<br>
        				Documents
        			</td>
        			<td width="20">&nbsp;</td>
        			<td>
        				其他<br>
        				Other
        			</td>
        		</tr>
        		<tr class="fontSize8 line10" align="center">
        			<td colspan="2" align="left">
        				内件详细名称和数量<br>
        				Quantity and detailed detailed description of
        			</td>
        			<td>
        				重量(千克)<br>
        				Weight（KG）
        			</td>
        			<td colspan="2">
        				价值<br>
        				Value
        			</td>
        		</tr>
        		<tr class="fontSize10 line10" align="center" style="font-weight: bold;">
        			<td colspan="2" rowspan="5" valign="top" style="line-height: 16px;">
        				'.$allParamArr['productsInfo']['sku'].'
        			</td>
        			<td>
        				'.$allParamArr['productsInfo']['totalWeight'].'
        			</td>
        			<td colspan="2">
        				'.$allParamArr['productsInfo']['totalValue'].'
        			</td>
        		</tr>
        		<tr class="fontSize8 line10">
        			<td>&nbsp;</td>
        			<td colspan="2">&nbsp;</td>
        		</tr>
        		<tr class="fontSize8 line10">
        			<td>&nbsp;</td>
        			<td colspan="2">&nbsp;</td>
        		</tr>
        		<tr class="fontSize8 line10">
        			<td>&nbsp;</td>
        			<td colspan="2">&nbsp;</td>
        		</tr>
        		<tr class="fontSize8 line10">
        			<td>&nbsp;</td>
        			<td colspan="2">&nbsp;</td>
        		</tr>
        		<tr class="fontSize8 line10" align="center">
        			<td colspan="2" align="left">
        				协调系统税则号列和货物原产国(只对商品邮件填写)<br>
        				HS tariff number and country of origin of goods(Fro commercial items only)
        			</td>
        			<td>
        				总重量<br>
        				Total weight(KG)
        			</td>
        			<td colspan="2">
        				总价值<br>
        				Total value
        			</td>
        		</tr>
        		<tr class="fontSize8 line10" align="center">
        			<td colspan="2">
        				CN
        			</td>
        			<td>
        				'.$allParamArr['productsInfo']['totalWeight'].'
        			</td>
        			<td colspan="2">
        				'.$allParamArr['productsInfo']['totalValue'].'
        			</td>
        		</tr>
        		<tr class="fontSize8 line10" height="50">
        			<td colspan="5">
        				我保证上述申报准确无误，本函件内未装寄法律或邮政和海关规章禁止寄递的任何危险物品<br>
        				I,the undersigned,certify that the particulars given in this declaraton are correct and this item does not contain any dangerous articles prohibited by lagislation or by postal or custome regulations.<br>
        				寄件人签字 Sender′s signature__________________
        			</td>
        		</tr>
        		<tr class="fontSize8 line10">
        			<td colspan="2" align="center">
        				标签生成日期<br/>
        				Label Creation Date '.$allParamArr['createTime'].' 
        			</td>
        			<td colspan="3" align="center">
        				货物已寄递标识<br />
        				Goods Delivery Concerns Identified
        			</td>
        		</tr>
        		<tr class="fontSize8 line10" align="right" height="14">
        			<td colspan="5">'.$allParamArr['ordersInfo']['orders_shipping_code'].'</td>
        		</tr>
            </table>
        </div>
		';
		*/
		$reStr.='</div>';
		
		return $reStr;
		
	}
	
	//燕文北京平邮模板处理
	public function printYWBejiingTemplate($allParamArr){
		$reStr = '
		<style>
		    *{margin:0;padding:0;}
			body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
			#main_frame_box{width:99mm;height:99.5mm;margin:0 auto; overflow:hidden;}
			table{border-collapse:collapse;border:none;width:99mm;height:99mm;}
			table .detail{
				width:380px;height:93px;border:none;
			}
			td{border:1px solid #000;}
		</style>
		';
		$reStr .='
		   <div id="main_frame_box">
		    <table>
		      <tr height="35px">
		        <td colspan="2">
		          <p style="width:34mm;height:35px;float:left;text-align:center;">
		            <img src="'.site_url('attachments').'/images/post_logo.jpg" style="width:34mm;height:30px;"/><br/>      
		          </p>
		          <p style="width:43mm;height:35px;float:left;text-align:center;">
		          	<span style="margin-top:5px;display:inline-block;">
		          	   Small Packet By AIR
		          	</span>
		          </p>
		          <p style="width:20mm;height:35px;float:left;text-align:center;line-height:35px;font-size:20px;">
		            '.($allParamArr['ordersInfo']['buyer_country_code'] ? $allParamArr['ordersInfo']['buyer_country_code'] : $allParamArr['ordersInfo']['buyer_country']).''.(!empty($allParamArr['postInfo']['sortingCode']) ? $allParamArr['postInfo']['sortingCode'] : '').'
		          </p>
		        </td>
		      </tr>
		      
		      <tr height="85px">
		        <td width="35%">
		          <p style="padding-left:5px;font-size:10px;width:110px;">
		            FROM:'.$allParamArr['backList']['consumer_from'].'<br/>
		            
		          </p>
		        </td>
		        <td width="65%" rowspan="2" style="word-break: break-all; word-wrap:break-word;">
		          <p style="padding-left:5px;font-size:13px;">
		            <span style="font-weight:bold;font-size:15px;">SHIP TO:</span><br/>
		            '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
			        '.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
			        '.$allParamArr['ordersInfo']['buyer_city'].' '.$allParamArr['ordersInfo']['buyer_state'].'<br/>
			        '.$allParamArr['ordersInfo']['buyer_zip'].'
			        '.$allParamArr['ordersInfo']['buyer_country'].'<br/>
			        Phone:'.$allParamArr['ordersInfo']['buyer_phone'].'&nbsp;&nbsp;&nbsp;'.$allParamArr['country'].'
		          </p>
		        </td>
		      </tr>
		      <tr height="35px">
		        <td>
		          <p style="padding-left:5px;font-size:11px;">
		            	自编号:'.$allParamArr['ordersInfo']['erp_orders_id'].'<br/>
		            	参考号:'.$allParamArr['ordersInfo']['orders_old_shipping_code'].'
		          </p>
		        </td>
		      </tr>
		      <tr height="60px">
		        <td colspan="2">
		        
		 		     <p style="width:106px;text-align:center;font-weight:bold;line-height:60px;height:60px;float:left;">
		 		              	Untracked
		 		     </p>
		 		     <p style="width:260px;height:60px;float:right;text-align:center;font-size:11px;">
		 		                <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=45&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		 		                <br/>
		 		                '.$allParamArr['ordersInfo']['orders_shipping_code'].'
		 		      </p>
		 	
		        </td>
		      </tr>
		      <tr height="95px">
		        <td colspan="2">
		          <table class="detail">
		            <tr height="20px">
		              <td width="60%">
		                <p style="padding-left:5px;font-size:11px;">description of contents</p>
		              </td>
		              <td width="20%">
		               <p style="padding-left:10px;font-size:11px">Kg</p>
		              </td>
		              <td width="20%">
		               <p style="padding-left:5px;font-size:11px">Val(US $)</p>
		              </td>
		            </tr>
		            <tr hegiht="53px">
		              <td width="60%">
		                <p style="padding-left:5px;font-size:11px;height:42px;overflow:hidden;">
		                '.$allParamArr['productsInfo']['sku'].'
		                </p>
		              </td>
		              <td width="20%">
		                <p style="font-size:11px;text-align:center;">
		                 '.$allParamArr['productsInfo']['totalWeight'].'
		                </p>
		              </td>
		              <td width="20%">
		                <p style="font-size:11px;text-align:center;">
		                 '.$allParamArr['productsInfo']['totalValue'].'
		                </p>
		              </td>
		            </tr>
		            <tr height="20px">
		              <td width="60%">
		                <p style="padding-left:5px;font-size:11px;">
		                 Total Gross Weight(Kg)
		                </p>  
		              </td>
		              <td width="20%">
		                <p style="font-size:11px;text-align:center;">
		                 '.$allParamArr['productsInfo']['totalWeight'].'
		                </p>
		              </td>
		              <td width="20%">
		                 <p style="font-size:11px;text-align:center;">
		                 '.$allParamArr['productsInfo']['totalValue'].'
		                </p>
		              </td>
		            </tr>
		          </table>
		        </td>
		      </tr>
		      <tr height="60px">
		        <td colspan="2">
		         <p style="font-size:11px;padding-left:5px;">
		          	I the undersigned,certify that the particulars given in this declaration are correct and this item does not contain any
		            dangerous articles prohibited by legislation or by postal or customers regulations.
		         </p>
		         <p style="padding-left:5px;font-size:11px;">
		        	 <span> Sender\'s signature</span>
		        	 <span style="padding-left:10px;font-weight:bold;font-size:12px;">'.$allParamArr['yanwen_code'].' '.$allParamArr['postInfo']['postArea2'].'  '.$allParamArr['postInfo']['enCode'].' '.$allParamArr['postInfo']['postArea1'].'CN22</span>
		        	  <span style="font-size:12px;font-weight:bold;padding-left:30px;">【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</span>
		         </p>
		        </td>
		      </tr>
		    </table>
		  </div>
		';
		
		
		return $reStr;
	}
	
	//顺丰荷兰面单模板
	public function ShunfengPostToNLDTemplate($allParamArr){
		$reStr='<style>*{margin:0;padding:0;}</style>';
		$reStr.='
		<div style="width:99mm;height:99mm;border:1px solid #000;">
		    <div style="width:95mm;height:20mm;margin:0 auto;"><img src="'.site_url('attachments').'/images/logo-code.png" style="width:55mm;height:20mm;"/><img src="'.site_url('attachments').'/images/post-logo.png" style="width:40mm;height:20mm;"/></div>
		    <div style="width:95mm;margin:0 auto;font-size:12px;text-align:center;">
		     Sender:H-11940SFT,Postbus 7040,3109AA Schiedam The Netherlands
		    </div>
		    <div style="width:95mm;height:20mm;margin:0 auto;">
		      <div style="width:10mm;height:10mm;float:left;margin:2mm 0 0 1mm;"><img src="'.site_url('attachments').'/images/ketui.png"/></div>
		      <div style="width:83mm;height:20mm;float:right;text-align:center;">
		       <strong>R</strong> Registered/recommande<br>
		        <img  src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" ><br>
		        <strong>'.$allParamArr['ordersInfo']['orders_shipping_code'].'</strong>
		      </div>
		    </div>
		    <div style="width:95mm;height:47mm;margin:0 auto;">
		      <div style="width:45mm;height:47mm;float:left;">
		        <div style="width:45mm;height:32mm;">
		        </div>
		        <div style="width:45mm;height:11mm;">
		           <div style="width:34mm;height:11mm;float:left;">
		              <img src="'.site_url('attachments').'/images/sf-logo.png" style="width:34mm;height:11mm;"/>
		           </div>
		           <div style="float:right;width:10mm;height:11mm;text-align:center;font-weight:bold;">
		             <p style="margin-top:1mm;font-size:28px;">'.$allParamArr['ordersInfo']['buyer_country_code'].'</p>
		           </div>
		       </div>
		        <div style="width:45mm;height:5mm;">
		         '.$allParamArr['ordersInfo']['orders_old_shipping_code'].'
		        </div>
		      </div>
		      <div style="width:49mm;height:47mm;float:right;font-size:13px;">
		        <span style="font-weight:bold;">Deliver To:</span><br/>
		        '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
		        '.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
		        '.$allParamArr['ordersInfo']['buyer_city'].'<br/>
		        '.$allParamArr['ordersInfo']['buyer_state'].'【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】<br/>
		        '.$allParamArr['ordersInfo']['buyer_zip'].'<br/>
		        '.$allParamArr['ordersInfo']['buyer_country'].'<br/>
		        Tel:'.$allParamArr['ordersInfo']['buyer_phone'].'<br/>
		      </div>
		    </div>
		    <div style="width:95mm;height:5mm;margin:0 auto;font-size:14px;">
		      [7550001183] Ref No:'.$allParamArr['ordersInfo']['erp_orders_id'].' '.$allParamArr['createTime'].'
		    </div>
		 </div>
		';
		
		$reStr.='
		<div style="width:99mm;height:90mm;">
		 <table style="width:99mm;border-bottom:none;" border="1" cellspacing=0>
		   <tr style="height:3mm;font-size:12px;">
		   	<td colspan="2" style="width:45%;padding:0;">
		   	  <span style="font-weight:bold;">CUSTOMS<br/>DECLARATION</span>
		   	</td>
		   	<td colspan="2">
		   	  <span style="display:inline-block;width:55%;">May be opened<br/>officially</span>
		   	  <span style="font-weight:bold;display:inline-block;text-align:center;width:40%;float:right;margin-top:3mm;">CN22</span>
		   	</td>
		   </tr>
		   <tr style="height:5mm;font-size:12px;">
		     <td colspan="2" style="width:45%;padding:0;">Designated operator</td>
		     <td colspan="2"><span style="font-weight:bold;">important!</span><br/>See instructions on the back</td>
		   </tr>
		   <tr style="height:5mm;">
		      <td style="width:16mm;"></td>
			  <td style="width:50mm;">Gift</td>
			  <td style="width:14mm;"></td>
			  <td style="width:50mm;">Commercial Sample</td>
		   </tr>
		   <tr style="height:5mm;font-size:12px;">
		      <td style="width:16mm;"></td>
			  <td style="width:50mm;">Documents</td>
			  <td style="width:10mm;">√</td>
			  <td style="width:80mm;">Other Tick one more boxes</td>
		   </tr>
		 </table>
		  <table style="width:99mm;height:54mm;border-top:none;"cellspacing=0 border=1 cellpadding=0>
		        <tr style="height:10mm;font-size:12px;">
		          <td style="width:50mm;"><span style="font-weight:bold;">Quantity and detailed description of contents</span></td>
		    	  <td style="widht:25mm;"><span style="font-weight:bold;">Weight(in kg)</span></td>
		    	  <td style="widht:25mm;"><span style="font-weight:bold;">Value(USD)</span></td>
		        </tr>
		        <tr style="height:5mm;font-size:12px;">
			          <td style="width:50mm;">'.$allParamArr['productsInfo'][0]['products_declared_en'].'</td>
			    	  <td style="widht:25mm;">'.$allParamArr['productsInfo'][0]['products_weight'].'</td>
			    	  <td style="widht:25mm;">'.$allParamArr['productsInfo']['totalValue'].'</td>
			    </tr>
		        <tr style="height:6mm;font-size:12px;">
		          <td style="width:50mm;"rowspan="2">
		           For commerical items only If known,HS tariff number and country of origin of goods
		          </td>
		    	  <td style="widht:25mm;">Total Weight(in kg)</td>
		    	  <td style="widht:25mm;">Total Value(USD)</td>
		        </tr>
		        <tr style="height:5mm;font-size:12px;">
		    	  <td style="widht:25mm;">'.$allParamArr['productsInfo']['totalWeight'].'</td>
		    	  <td style="widht:25mm;">'.$allParamArr['productsInfo']['totalValue'].'</td>
		        </tr>
		        <tr style="height:6mm;font-size:8px;">
		          <td colspan="3">
		           I,undersigned,whose name and address are given on the item,certify that the particulars given in this declaration are
		           correct and that this item does not contain any dangerous article and articles prohibited by legislation or by postal
		           or customs regulations Date and sender\'s signature
		          </td>
		        </tr>
		    </table >
		    <div style="width:99mm;height:9mm;margin:0 auto;font-size:12px;text-align:center;word-wrap:break-word">
		     	'.$allParamArr['productsInfo']['sku'].'
		    </div>
		</div>
		';
	  return $reStr;
	}
	
	//顺丰欧洲平邮小包
	public function ShunfengPostToPingYouTemplate($allParamArr){
	 	$reStr='<style>*{margin:0;padding:0;}</style>';
		$reStr.='
		<div style="width:99mm;height:99mm;border:1px solid #000;">
		    <div style="width:95mm;height:20mm;margin:0 auto;"><img src="'.site_url('attachments').'/images/logo-code.png" style="width:55mm;height:20mm;"/><img src="'.site_url('attachments').'/images/post-logo.png" style="width:40mm;height:20mm;"/></div>
		    <div style="width:95mm;margin:0 auto;font-size:12px;text-align:center;">
		     Sender:H-11940SFT,Postbus 7040,3109AA Schiedam The Netherlands
		    </div>
		    <div style="width:95mm;height:20mm;margin:0 auto;">
		      <div style="width:10mm;height:10mm;float:left;margin:2mm 0 0 1mm;"><img src="'.site_url('attachments').'/images/ketui.png"/></div>
		      <div style="width:83mm;height:20mm;float:right;text-align:center;">
		        <img  src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" ><br>
		        <strong>'.$allParamArr['ordersInfo']['orders_shipping_code'].'</strong>
		      </div>
		    </div>
		    <div style="width:95mm;height:47mm;margin:0 auto;">
		      <div style="width:45mm;height:47mm;float:left;">
		        <div style="width:45mm;height:32mm;font-size:36px;font-weight:bold;line-height:52mm;">
		        P
		        </div>
		        <div style="width:45mm;height:11mm;">
		           <div style="width:34mm;height:11mm;float:left;">
		              <img src="'.site_url('attachments').'/images/sf-logo.png" style="width:34mm;height:11mm;"/>
		           </div>
		           <div style="float:right;width:10mm;height:11mm;text-align:center;font-weight:bold;">
		             <p style="margin-top:1mm;font-size:28px;">'.$allParamArr['ordersInfo']['buyer_country_code'].'</p>
		           </div>
		       </div>
		        <div style="width:45mm;height:5mm;">
		         '.$allParamArr['ordersInfo']['orders_old_shipping_code'].'
		        </div>
		      </div>
		      <div style="width:49mm;height:47mm;float:right;font-size:13px;">
		        <span style="font-weight:bold;">Deliver To:</span><br/>
		        '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
		        '.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
		        '.$allParamArr['ordersInfo']['buyer_city'].'<br/>
		        '.$allParamArr['ordersInfo']['buyer_state'].'<br/>
		        '.$allParamArr['ordersInfo']['buyer_zip'].'<br/>
		        '.$allParamArr['buyer_country'].'<br/>
		        Tel:'.$allParamArr['ordersInfo']['buyer_phone'].'<br/>
		      </div>
		    </div>
		    <div style="width:95mm;height:5mm;margin:0 auto;font-size:14px;">
		      [7550001183] Ref No:'.$allParamArr['ordersInfo']['erp_orders_id'].' '.$allParamArr['createTime'].'
		    </div>
		 </div>
		';
		
		$reStr.='
		<div style="width:99mm;height:90mm;">
		 <table style="width:99mm;border-bottom:none;" border="1" cellspacing=0>
		   <tr style="height:3mm;font-size:12px;">
		   	<td colspan="2" style="width:45%;padding:0;">
		   	  <span style="font-weight:bold;">CUSTOMS<br/>DECLARATION</span>
		   	</td>
		   	<td colspan="2">
		   	  <span style="display:inline-block;width:55%;">May be opened<br/>officially</span>
		   	  <span style="font-weight:bold;display:inline-block;text-align:center;width:40%;float:right;margin-top:3mm;">CN22</span>
		   	</td>
		   </tr>
		   <tr style="height:5mm;font-size:12px;">
		     <td colspan="2" style="width:45%;padding:0;">Designated operator</td>
		     <td colspan="2"><span style="font-weight:bold;">important!</span><br/>See instructions on the back</td>
		   </tr>
		   <tr style="height:5mm;">
		      <td style="width:16mm;"></td>
			  <td style="width:50mm;">Gift</td>
			  <td style="width:14mm;"></td>
			  <td style="width:50mm;">Commercial Sample</td>
		   </tr>
		   <tr style="height:5mm;font-size:12px;">
		      <td style="width:16mm;"></td>
			  <td style="width:50mm;">Documents</td>
			  <td style="width:10mm;">√</td>
			  <td style="width:80mm;">Other Tick one more boxes</td>
		   </tr>
		 </table>
		  <table style="width:99mm;height:54mm;border-top:none;"cellspacing=0 border=1 cellpadding=0>
		        <tr style="height:10mm;font-size:12px;">
		          <td style="width:50mm;"><span style="font-weight:bold;">Quantity and detailed description of contents</span></td>
		    	  <td style="widht:25mm;"><span style="font-weight:bold;">Weight(in kg)</span></td>
		    	  <td style="widht:25mm;"><span style="font-weight:bold;">Value(USD)</span></td>
		        </tr>
		        <tr style="height:5mm;font-size:12px;">
			          <td style="width:50mm;">'.$allParamArr['productsInfo'][0]['products_declared_en'].'</td>
			    	  <td style="widht:25mm;">'.$allParamArr['productsInfo'][0]['products_weight'].'</td>
			    	  <td style="widht:25mm;">'.$allParamArr['productsInfo']['totalValue'].'</td>
			    </tr>
		        <tr style="height:6mm;font-size:12px;">
		          <td style="width:50mm;"rowspan="2">
		           For commerical items only If known,HS tariff number and country of origin of goods
		          </td>
		    	  <td style="widht:25mm;">Total Weight(in kg)</td>
		    	  <td style="widht:25mm;">Total Value(USD)</td>
		        </tr>
		        <tr style="height:5mm;font-size:12px;">
		    	  <td style="widht:25mm;">'.$allParamArr['productsInfo']['totalWeight'].'</td>
		    	  <td style="widht:25mm;">'.$allParamArr['productsInfo']['totalValue'].'</td>
		        </tr>
		        <tr style="height:6mm;font-size:8px;">
		          <td colspan="3">
		           I,undersigned,whose name and address are given on the item,certify that the particulars given in this declaration are
		           correct and that this item does not contain any dangerous article and articles prohibited by legislation or by postal
		           or customs regulations Date and sender\'s signature
		          </td>
		        </tr>
		    </table >
		    <div style="width:99mm;height:9mm;margin:0 auto;font-size:12px;text-align:center;word-wrap:break-word">
		     	'.$allParamArr['productsInfo']['sku'].'
		    </div>
		</div>
		';
	  return $reStr;
	}
	
	//DHL-GM(意大利+其他)面单模板处理
	public function DhlGmpostTemplate($allParamArr){
		$reStr = '
				<style>
				    *{margin:0;padding:0;}
					body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
					#main_frame_box{width:99mm;height:99mm;margin:0 auto;border:1px solid #000; overflow:hidden;}
					.top{width:354px;height:70px;margin:10px auto;}
					table{border-collapse:collapse;border:none;width:354px;height:270px;margin:15px auto;}
					td{border:1px solid #000;}
			    </style>
				';
		$reStr .='
		  <div id="main_frame_box">
		    <div class="top">
		       <p style="height:17px;">
			          <span style="font-size:12px;">&nbsp;&nbsp;Customer reference</span>
		          </p>
		          <p>
		            <span style="margin-left:15px;display:inline-block;font-weight:bold;text-align:center;">
			           <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
			           <br>
			           '.$allParamArr['ordersInfo']['orders_shipping_code'].'
			        </span>
		          </p>
		    </div>
		    <table>
		     <tr style="height:12%;">
		       <td colspan="3">
		         <P style="width:235px;height:33px;float:left;margin-left:5px;">
		            <span style="font-weight:bold;">CUSTOMS DECLARATION</span>
		            <br/>
		            <span style="font-size:11px;">Postal Administration(Maybe opened officially)</span>
		         </P>
		         <P style="font-weight:bold;width:110px;height:33px;float:right;">
		            <span style="font-weight:bold;padding-left:50px;">CN22</span>
		            <br/>
		            <span style="font-size:11px;padding-left:50px;">importantl</span>
		         </P>
		       </td>
		     </tr>
		     <tr style="height:15%;">
		       <td colspan="3">
		        <img src="'.site_url('attachments').'/images/dhl.jpg" />
		       </td>
		     </tr>
		     <tr style="height:23%;">
		       <td colspan="2" width="70%">
		        <p style="height:57px;">
		           <span style="font-size:10px;">Detailed description of Contents</span>
		           <br>
		           <span style="margin-left:30px;margin-top:10px;display:inline-block;">
		           '.$allParamArr['productsInfo'][0]['products_declared_en'].' '.$allParamArr['productsInfo'][0]['orders_sku'].'*'.$allParamArr['productsInfo'][0]['item_count'].'
		           </span>
		        </p>
		       </td>
		       <td>
		        <p style="height:57px;">
		          <span style="font-size:12px;">&nbsp;&nbsp;&nbsp;&nbsp;	Value</span>
		          <br>
		          <span style="margin-left:10px;margin-top:10px;display:inline-block;font-weight:bold;">
		            EUR $'.$allParamArr['productsInfo']['totalPrice'].'
		           </span>
		        </p>
		       </td>
		     </tr>
		     <tr style="height:10%;">
		       <td width="33%">
		          <p style="height:17px;">
			          <span style="font-size:12px;">&nbsp;&nbsp;Origin country</span>
		          </p>
		           <p>
		            <span style="margin-left:40px;display:inline-block;font-weight:bold;">
			           CN
			        </span>
		          </p>
		       </td>
		       <td width="33%">
		          <p style="height:17px;">
			          <span style="font-size:12px;">&nbsp;&nbsp;TotalWeight(Kg)</span>
		          </p>
		          <p>
		            <span style="margin-left:40px;display:inline-block;font-weight:bold;">
			            '.$allParamArr['productsInfo']['totalWeight'].'
			        </span>
		          </p>
		       </td>
		       <td width="33%">
		          <p style="height:17px;">
			          <span style="font-size:12px;">&nbsp;&nbsp;TotalValue</span>
		          </p>
		          <p>
		            <span style="margin-left:10px;display:inline-block;font-weight:bold;">
			            EUR $'.$allParamArr['productsInfo']['totalPrice'].'
			        </span>
		          </p>
		       </td>
		     </tr>
		     <tr style="height:23%">
		       <td colspan="3">
		        <p style="height:57px;width:345px;margin:0 auto;font-size:10px;">
		          I,hereby undersigned whose name and address are given on the item<br> certify that the
		          particulars given in the declaration are correct and that<br>  this item does not contain
		          any dangerous articles or articles prohibited by<br> legislation or by postal customs regulations.
		        </p>
		       </td>
		     </tr>
		     <tr>
		       <td colspan="3">
		           <p style="height:17px;">
			          <span style="font-size:12px;">&nbsp;&nbsp;Date and Sender\'s Signature</span>
		          </p>
		          <p style="width:100%;">
		            <span style="margin-left:10px;display:inline-block;font-weight:bold;">
			            '.date('m-d-Y',strtotime($allParamArr['ordersInfo']['orders_print_time'])).'
			        </span>
			        <span style="margin-left:120px;display:inline-block;font-weight:bold;">
			           
			        </span>
			        <span style="margin-left:60px;display:inline-block;font-weight:bold;">
			            SLM(X30)
			        </span>
		          </p>
		       </td>
		     </tr>
		    </table>
		  </div>
		';
		$reStr.='
		<div style="height:370px; width:100mm; overflow:hidden; margin:auto;">
		<div style="height:10px; width:100%;">&nbsp</div>
		  <div id="main_frame_box" style="height:300px;">
		     <div style="width:100%;margin-top:10px;">
		       <div style="height:90px;margin-left:10px;float:left; width:90px; margin-right:5px;">
		          <p style="width:100px;height:50px;margin:25px auto;text-align:center;">
		            GM PACKET <br/>STANDARD 
		          </p>
		       </div>
				
		      		 <table border="1" style="width:250px;height:92px;margin:0;text-align:center; font-size:10px; float: left;">
						'. $allParamArr['country_img'] .'
		       		</table>
		       	<div style="clear: both;"></div>
		     </div>
		     
		     
		     <div style="width:328px;height:150px;margin:0 auto;font-size:16px;margin-top:20px;">
		       '.$allParamArr['ordersInfo']['buyer_name'].'<br>
		       '.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br>
		       '.$allParamArr['ordersInfo']['buyer_city'].'<br>
		       '.$allParamArr['ordersInfo']['buyer_state'].'<br>
		       '.$allParamArr['ordersInfo']['buyer_country'].'<br>
		       '.$allParamArr['ordersInfo']['buyer_zip'].'<br>
		       Tel:'.$allParamArr['ordersInfo']['buyer_phone'].'
		     </div>
		  </div>
		  <div style="width:380px;height:70px;margin:0 auto;text-align:center;word-wrap:break-word;">
		   	<span style="display:inline-block;">【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</span>
		       '.$allParamArr['productsInfo']['sku'].'
		  </div>
		  </div>
		';
		return $reStr;
	}
	
	//比利时邮政面单-100×100热敏标签模板处理
	public function printForBlsThermal($allParamArr){
		$reStr = '
		<style type="text/css">
			*{ font-family:Arial, Helvetica, sans-serif;}
			body{ padding:0px; margin:0px;}
			#main_frame_box{width:380px; margin:0 auto;}
			.orderBox{ position: relative; float:left; width:370px; height:364px; overflow:hidden; margin:5px 3px 1px; border:1px solid black;}
			.fontSize8{ font-size:8px;}
			.fontSize9{ font-size:9px;}
			.fontSize10{ font-size:10px;}
			.fontSize11{ font-size:11px;}
			.fontSize12{ font-size:12px;}
			.fontSize12{ font-size:13px;}
			.fontSize14{ font-size:14px;}
			.PageNext{page-break-after:always; clear:both; height:auto; overflow:auto;}
			.ItemCount{ position: absolute; width: 30px; height: 30px; right: 0px; bottom: 0px; font-size: 28px; font-weight: bold; z-index: 100;}
		</style>
		';
 	 	$reStr .='<div id="main_frame_box">						
			<div class="orderBox" style="margin-top:0;border:0;">
				<table border="0" cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<td width="355" valign="top">
							<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="80" class="fontSize10">&nbsp;&nbsp;&nbsp;' . $allParamArr['ordersInfo']['erp_orders_id'] . '</td>
									<td height="85" align="right">
										<table border="0" cellpadding="2" cellspacing="1" bgcolor="#000000">
											<tr bgcolor="#FFFFFF">
												<td align="center"><img src="'.site_url('attachments').'/images/logo.jpg" /></td>
												<td align="center"><span class="fontSize10">Belgique - Belgie</span></td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td rowspan="2" >
													<span class="fontSize8">If undelivered please return to:</span><br>
													<span class="fontSize10">PO BOX</span><span class="fontSize12"> 7328</span><br>
													<span class="fontSize10">1934 EMC Brucargo-BELGIUM</span>
												</td>
												<td align="center"><span class="fontSize10">P.P. - P.B</span></td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td align="center"><span class="fontSize9">BPI/</span><span class="fontSize12">7328</span></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td height="150" colspan="2" valign="middle" class="fontSize12">&nbsp;&nbsp;&nbsp;' . $allParamArr['ordersInfo']['buyer_name'] . '<br>&nbsp;&nbsp;
									  ' . $allParamArr['ordersInfo']['buyer_address_1'] . ' ' . $allParamArr['ordersInfo']['buyer_address_2'] . ' ' . $allParamArr['ordersInfo']['buyer_city'] . ' ' . $allParamArr['ordersInfo']['buyer_state'] . ' ' . $allParamArr['ordersInfo']['buyer_zip'] . '<br>&nbsp;&nbsp;
									  ' . $allParamArr['ordersInfo']['buyer_country'] . '<br>&nbsp;&nbsp;
									  TEL:' . $allParamArr['ordersInfo']['buyer_phone'] . '<br />&nbsp;&nbsp;
									  '.$allParamArr['country_cn'].'
									 </td>
								</tr>
								<tr>
									<td height="80" colspan="2" align="center" valign="middle">
									  <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
									  <span class="fontSize10"><br>' . $allParamArr['ordersInfo']['orders_shipping_code'] . '</span>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>	
			<div class="PageNext"></div>
			<div class="orderBox">		
				<table border="0" cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<td width="357" valign="top">
							<div align="center" style="height:300px;">
								<span class="fontSize12">INVOICE</span><br>';		
				/*
					*义乌仓库与深圳仓库比利时邮政面单 地址与标题区分
					add by chenwei 2014.10.16
				*/				
				if($allParamArr['ordersInfo']['orders_shipping_code']['orders_warehouse_id']=='1025'){
					$reStr .='<span class="fontSize10">Moonar Technology</span><br><span class="fontSize9">4th floor Building 1,<br>Jingang Road West No.2011 Jinhua City Zhejiang Province, 321000</span>';
				}else{
					$reStr .='<span class="fontSize10">SALAMOER TECHNOLOGY</span><br><span class="fontSize9"> No.41, Wuhe Road South, Bantian, Longgang District, 518000</span>';
				}				
						
				$reStr .='<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td>&nbsp;</td>
										<td width="50%" align="left" class="fontSize10">' . date( 'Y-m-d' ) . '<br>' . $allParamArr['ordersInfo']['erp_orders_id'] . '<br>' . $allParamArr['ordersInfo']['orders_shipping_code'] . '</td>
									</tr>
								</table>
								<div style="text-align:left;" class="fontSize10">To:</div>
								<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">
									<tr bgcolor="#FFFFFF" class="fontSize10">
										<td width="60">Name</td>
										<td width="86%">' . $allParamArr['ordersInfo']['buyer_name'] . '</td>
									</tr>
									<tr bgcolor="#FFFFFF" class="fontSize10">
										<td>Address</td>
											<td>' . $allParamArr['ordersInfo']['buyer_address_1'] . ' ' . $allParamArr['ordersInfo']['buyer_address_2'] . ' ' . $allParamArr['ordersInfo']['buyer_city'] . ' ' . $allParamArr['ordersInfo']['buyer_state'] . ' ' . $allParamArr['ordersInfo']['buyer_zip'] . '<br>
					' . $allParamArr['ordersInfo']['buyer_country'] . '</td>
									</tr>
									<tr bgcolor="#FFFFFF" class="fontSize10">
										<td>Tel</td>
										<td>' . $allParamArr['ordersInfo']['buyer_phone'] . '</td>
									</tr>
								 </table>
								 <div style="text-align:left;" class="fontSize10">Item Description:</div>
								 <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">
									 <tr bgcolor="#FFFFFF">
										<td width="40%" align="left" class="fontSize10">Goods Description </td>
										<td align="center" class="fontSize10">SKU</td>
										<td align="center" class="fontSize10">Quantity</td>
										<td align="center" class="fontSize10">Unit Price </td>
										<td align="center" class="fontSize10">Sub Total </td>
									 </tr>
								     '.$allParamArr['productsInfo']['skuDetail'].'
								 </table>
							</div>
						</td>
					</tr>
				</table>
				<div class="ItemCount">'.$allParamArr['productsInfo']['totalCount'].'</div>
			</div>
			
	 </div>';
		return $reStr;
	}
	
	//比利时Mini Scan-100×100热敏标签模板处理
	public function printMiniThermalScan($allParamArr){
		$reStr = '
		 <style type="text/css">
			*{ font-family:Arial, Helvetica, sans-serif;}
			body{ padding:0px; margin:0px;}
			#main_frame_box{width:380px; margin:0 auto;}
			.orderBox{ position: relative; float:left; width:370px; height:364px; overflow:hidden; margin:5px 3px 1px; border:1px solid black;}
			.fontSize8{ font-size:8px;}
			.fontSize9{ font-size:9px;}
			.fontSize10{ font-size:10px;}
			.fontSize11{ font-size:11px;}
			.fontSize12{ font-size:12px;}
			.fontSize12{ font-size:13px;}
			.fontSize14{ font-size:14px;}
			.PageNext{page-break-after:always; clear:both; height:auto; overflow:auto;}
			.ItemCount{ position: absolute; width: 30px; height: 30px; right: 0px; bottom: 0px; font-size: 28px; font-weight: bold; z-index: 100;}
		</style>
		';
		$reStr.='
		<div id="main_frame_box">
			<div class="orderBox">
		    	<table border="0" cellpadding="2" cellspacing="0" width="100%">
		        	<tr>
		            	<td width="350" valign="top">
		                	<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">
		                    	<tr bgcolor="#FFFFFF">
		                        	<td>
		                            	<table width="100%">
											<tr>
											  <td class="fontSize11">
												<div class="fontSize10">From:</div>
												<div><b>PO BOX 7328</b></div>
												<div>1934 EMC Brucargo</div>
												<div>BELGIUM</div>
											  </td>
											  <td width="60">
												<img src="'.site_url('attachments').'/images/11-1.jpg" width="60" height="40" />
											  </td>
											  <td width="50">
												<div style="font-size:16px;">M2M</div>
											  </td>
											</tr>
		                                 </table>
		                             </td>
		                          </tr>
								  <tr bgcolor="#FFFFFF">
									<td class="fontSize11">
									  <div class="fontSize10">To:</div>
									  <div style="font-weight:bold;">'.$allParamArr['ordersInfo']['buyer_name'].'</div>
									  <div>'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].' '.$allParamArr['ordersInfo']['buyer_city'].'</div>
									  <div>'.$allParamArr['ordersInfo']['buyer_state'].'</div>
									  <div>'.$allParamArr['ordersInfo']['buyer_zip'].'</div>
									  <div>'.$allParamArr['ordersInfo']['buyer_country'].'</div>
									</td>
								  </tr>
								  <tr bgcolor="#FFFFFF">
									<td class="fontSize11">
									  <div>Service level: PRM</div>
									  <div>Importer\'s reference: '.$allParamArr['ordersInfo']['erp_orders_id'].'</div>
									  <div>Importer\'s contract: BPI/7328</div>
									  <div>Sender\'s instrucion in case of non-delivery:RETURN TO SENDER</div>
									</td>
								  </tr>
		                      </table>
		                      <div style="width:100%;text-align:center; padding-top:5px;">
		                      <div style="height:40px;"><img style="height:40px; width:350px;" src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text=' . $allParamArr['ordersInfo']['orders_shipping_code'] . '&f1=-1&f2=8&a1=&a2=B&a3=" /></div>
		                          <span class="fontSize10">'.$allParamArr['ordersInfo']['orders_shipping_code'].'</span>
		                      </div>
		                  </td>
		               </tr>
		           </table>
		       </div>
		       <div class="PageNext"></div>
		       <div class="orderBox">
		    		<table border="0" cellpadding="2" cellspacing="0" width="100%">
		        		<tr>        
		                	<td width="357" valign="top">
		                    	<div align="center" style="height:300px;">
		                        	<span class="fontSize12">INVOICE</span><br>
		                            <span class="fontSize10">SALAMOER TECHNOLOGY</span><br>
		                            <span class="fontSize9"> No.41, Wuhe Road South, Bantian, Longgang District, 518000</span>
		                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
		                                <tr>
		                                    <td>&nbsp;</td>
		                                    <td width="50%" align="left" class="fontSize10">' . date( 'Y-m-d' ) . '<br>' . $allParamArr['ordersInfo']['erp_orders_id'] . '<br>' . $allParamArr['ordersInfo']['orders_shipping_code'] . '</td>
		                                </tr>
		                            </table>
		                            <div style="text-align:left;" class="fontSize10">To:</div>
		                                <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">
		                                  <tr bgcolor="#FFFFFF" class="fontSize10">
		                                    <td width="60">Name</td>
		                                    <td width="86%">' . $allParamArr['ordersInfo']['buyer_name'] . '</td>
		                                  </tr>
		                                  <tr bgcolor="#FFFFFF" class="fontSize10">
		                                    <td>Address</td>
		                                        <td>' . $allParamArr['ordersInfo']['buyer_address_1'] . ' ' . $allParamArr['ordersInfo']['buyer_address_2'] . ' ' . $allParamArr['ordersInfo']['buyer_city'] . ' ' . $allParamArr['ordersInfo']['buyer_state'] . ' ' . $allParamArr['ordersInfo']['buyer_zip'] . '<br>
		                ' . $allParamArr['ordersInfo']['buyer_country'] . '</td>
		                                  </tr>
		                                  <tr bgcolor="#FFFFFF" class="fontSize10">
		                                    <td>Tel</td>
		                                        <td>' . $allParamArr['ordersInfo']['buyer_phone'] . '</td>
		                                  </tr>
		                              </table>
		                    		<div style="text-align:left;" class="fontSize10">Item Description:</div>
		                            <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">
		                              <tr bgcolor="#FFFFFF">
		                                <td width="40%" align="left" class="fontSize10">Goods Description </td>
		                                <td align="center" class="fontSize10">SKU</td>
		                                <td align="center" class="fontSize10">Quantity</td>
		                                <td align="center" class="fontSize10">Unit Price </td>
		                                <td align="center" class="fontSize10">Sub Total </td>
		                              </tr>
		                            '.$allParamArr['productsInfo']['skuDetail'].'
		                            </table>
		                    	</div>                               
						    </td>
		                 </tr>
		             </table>
		             <div class="ItemCount">'.$allParamArr['productsInfo']['totalCount'].'</div>
		         </div>
		         <div class="PageNext"></div>
		         <div class="orderBox">
		    		<table border="0" cellpadding="2" cellspacing="0" width="100%">
		        		<tr>                      
		                    <td width="393">
		                        <div style="overflow:hidden; width:100%; height:355px; border:1px solid #000000; border-radius:5px;">
		                          <table width="100%" class="fontSize12" style="border-bottom:1px solid #000;">
		                                <tr class="fontSize12">
		                                  <td rowspan="2" width="180"><div style="padding-left:5px; font-weight:bold;">CUSTOMS DECLARATION DéCLARATION EN DOUANE</div></td>
		                                  <td><div style="padding:0 20px 0 0; font-weight:bold; text-align:right;"><strong>'.$allParamArr['ordersInfo']['erp_orders_id'].'</strong>&nbsp;&nbsp;&nbsp;&nbsp;CN 22</div></td>
		                                </tr>
		                                <tr class="fontSize10">
		                                  <td style="line-height:10px; padding:0;">May be opened officially Peut être ouvert d\'offic</td>
		                                </tr>
		                                <tr style="line-height:10px;">
		                                  <td colspan="2" align="right" valign="top" style="padding:0;">
		                                    <span class="fontSize12"><b>Important!</b></span>
		                                    <span class="fontSize10" style="padding-right:10px;"><b>See instructions on the back</b></span>
		                                  </td>
		                                </tr>
		                          </table>
		                          <table width="100%" cellpadding="0" cellspacing="0" class="fontSize10" style="border-bottom:1px solid #000000;">
		                            <tr>
		                              <td width="5" rowspan="2">&nbsp;</td>
		                              <td height="18" width="18" style="border-left:1px solid #000000;border-right:1px solid #000000;">&nbsp;</td>
		                              <td width="80" rowspan="2"><b>Gift</b>\Cadeau<br/>Documents</td>
		                              <td style="border-left:1px solid #000000;border-right:1px solid #000000;">&nbsp;</td>
		                              <td rowspan="2"><b>Commercial sample</b>\Echantillon commercial<br/><b>Other</b>\Autre Tick one or more boxes</td>
		                            </tr>
		                            <tr>
		                              <td style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;">&nbsp;</td>
		                              <td height="20" width="20" style="background:url('.site_url('attachments').'/images/cn22-1.jpg) no-repeat; background-size:20px 20px;">&nbsp;</td>
		                              <td>&nbsp;</td>
		                            </tr>
		                          </table>
		                          <table width="100%" cellpadding="0" cellspacing="0" class="fontSize11" style="border-bottom:1px solid #000; line-height:12px;">
		                            <tr height="35">
		                              <td width="65%" style="border-right:1px solid #000;">Quantity and detailed description of contents(1)<br/>Quantité et description détaillée du contenu</td>
		                              <td width="20%" style="border-right:1px solid #000;">Weight(in kg)(2)<br/>Poids</td>
		                              <td width="15%">Value(3)<br/>Valeur</td>
		                            </tr>
		                           '. $allParamArr['productsInfo']['weightDetail'].'
		                         </table>
		                          <table width="100%" class="fontSize10" style="background:url(images/cn22-1_07.jpg) no-repeat; background-size:440px 60px; line-height:12px;">
		                            <tr>
		                              <td colspan="2">I, the undersigned, whose name and address are given on the item, certify that the particulars 
		                              given in this declaration are correct and that this item does not contain any danergous article 
		                              or articles prohibited by legislation or by postal or customs regulations</td>
		                            </tr>
		                            <tr><td>Date and sender\'s signature(8)</td><td>'.date('Y-m-d').'</td></tr>
		                          </table>
		                        </div>
		                    </td>
		                </tr>
		            </table>
		        </div>
		        <div class="PageNext"></div>
		</div>
		';
		return $reStr;
	}
	
	//EUB-100×100热敏标签模板处理
	public function printForPostEubThermal($allParamArr){
		$reStr = '
		<style>
			body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
			*{margin:0;padding:0;}
			.PageNext{page-break-after:always; clear:both; min-height:1px; height:auto; overflow:auto; width:100%;}
			#main_frame_box{width:380px; margin:0 auto;}
			.float_box1{ position: relative; float:left; width:370px; height:364px;  margin:2px 3px 1px; border:1px solid black;}
			.float_box2{ float:left; width:370px; height:364px; margin:2px 3px 1px;border:1px solid black;overflow:hidden;}
		</style>
		';
		$reStr .='
		  <div id="main_frame_box">
		    <div class="float_box1">
			<table border="0" cellpadding="0" cellspacing="0" style="border:#000000 1px solid; width:370px; height:364px;">
  				<tr>
    			   <td>
                   	  <table width="100%" border="0" cellspacing="0" cellpadding="0" style=" margin-left:5px">
      					 <tr>
        					<td width="24%" style=" margin-right:120px;">
                            	<table width="80" height="50" border="0" cellpadding="0" cellspacing="0" style="border:2px solid #000; text-align:center; margin-top:0px;">
                         			<tr>
                                        <td width="80" height="60" >
                                            &nbsp;<font style="font-family:Arial; font-size:60px; line-height:60px;"><strong>F</strong></font>
                                        </td>
          				  			</tr>
        			  			</table>
                   			</td>
        		   			<td width="40%" align="center">
                            	<table width="92%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td align="center" ><img src="'.site_url('attachments').'/images/EUB01.jpg" width="110" height="20" style="margin-top:5px;"/></td>
                                    </tr>
                                    <tr>
                                        <td align="center"></td>
                                    </tr>
                                    <tr>
                                        <td align="center" ><img src="'.site_url('attachments').'/images/EUB02.jpg" width="160" height="45" /></td>
                                    </tr>
        	 				    </table>
             				</td>
        					<td width="30%">
                            	<table width="100%" border="0" cellspacing="0" cellpadding="0">
          							<tr>
            							<td align="left">
                                        	<table width="80%" border="0" align="left" cellpadding="0" cellspacing="0" style="border:2px solid #000; text-align:center; margin-top:5px; margin-right:10px;">
              									<tr>
                									<td width="47" height="45" align="left"><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:13px"> 
                  										&nbsp;Aimail<br/>
                 								 		&nbsp;Postage&nbsp;Paid<br/>
                  										&nbsp;China&nbsp;Post</span>
                                                    </td>
              									</tr>
            								</table>
                                        </td>
          							</tr>
                                     <tr>
                                        <td align="center" style="height:20px;"><font style="font-family:Arial; font-size:20px; margin-right:15px;"><strong>'.$allParamArr['AreaID'].'</strong></font>&nbsp;</td>
                                     </tr>
        						</table>
                             </td>
      </tr>
      <tr>
        <td height="7" colspan="3" valign="top" style=" margin-right:120px"><span style="font-family:Arial, Helvetica, sans-serif; font-size:9px">From:</span></td>
      </tr>
    </table>
        <div style="font-family:Arial, Helvetica, sans-serif; font-size:7px"></div></td>
  </tr>
  <tr>
    <td height="" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2" style=" border-bottom:#000 1px solid; border-top:#000 1px solid">
      <tr>
        <td width="59%" valign="top" style="border-right:#000 1px solid">
        <div style="font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:12px">
			&nbsp;'.$allParamArr['senderInfo']['sender'].'<br />
			&nbsp;'.$allParamArr['senderInfo']['street'].'<br />
			&nbsp;'.$allParamArr['senderInfo']['provinces'].'<br />
			&nbsp;'.$allParamArr['senderInfo']['countryAndPostcode'].'<br />
        </div>
        <div style="font-family:Arial, Helvetica, sans-serif; height:13px;" align="center">
        &nbsp;<strong style="font-size:16px;">'.$allParamArr['ordersInfo']['erp_orders_id'].'</strong>
        </div>
        </td>
        <td width="41%" rowspan="2" valign="top"><table width="100%" border="0" cellspacing="3" cellpadding="0">
          <tr>
            <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="left"><div style="margin-top:3px; margin-right:5px; "> <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text=420'.$allParamArr['buyer_zip'][0].'&f1=-1&f2=8&a1=&a2=B&a3='.'" /> </div></td>
              </tr>
              <tr>
                <td align="center" valign="bottom"><div style="font-size:14px; margin-top:0px;"><strong>ZIP '.$allParamArr['buyer_zip'][0].'</strong></div></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td height="20" valign="top" style="border-right:#000 1px solid"><div style="font-family:Arial, Helvetica, sans-serif; font-size:7px; margin-top:6px ;margin-left:5px; vertical-align:bottom; line-height:6px;"> Customs information avaliable on attached CN22.<br />
          USPS Personnel Scan barcode below for delivery event information </div></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="23" valign="top"><table width="100%" height="62" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="15%" height="60" style=" border-right: 1px solid #000
            "><div style="font-family:Arial, Helvetica, sans-serif; font-size:22px; margin-left:12px">To:</div>
                   </td>
        <td width="85%" valign="top">
        	<div style="font-family:Arial; font-size:12px;">
        	
                '.$allParamArr['ordersInfo']['buyer_name'].'<br/>'.($allParamArr['ordersInfo']['buyer_address_1']).' '.($allParamArr['ordersInfo']['buyer_address_2']).'
                <br/>'.($allParamArr['ordersInfo']['buyer_city']).' '.($allParamArr['ordersInfo']['buyer_state']).' '.($allParamArr['ordersInfo']['buyer_zip']).'<br/>'.($allParamArr['countryAll']).'

            </div>
          </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td valign="bottom" style="border-bottom:0px"><table width="100%" border="0" cellspacing="0" cellpadding="0" style=" border-bottom:#000 5px solid; border-top:#000 5px solid">
      <tr>
        <td height="90" valign="top" style="border-right:#000 1px solid; font-size: 9px;"><table width="100%" border="0" cellspacing="2" cellpadding="0">
          <tr>
            <td height="20" align="center" valign="bottom"><span style=" font-family: Arial, Helvetica, sans-serif; font-size:15px"><strong>USPS TRACKING #</strong></span></td>
          </tr>
          <tr>
            <td align="center">
            	<div >
                	<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
                    <span style="font-size:12px;margin-top:5px;">
                    	<br>
                        <strong>
                          '.$allParamArr['ordersInfo']['orders_shipping_code'].' 
                        </strong>
                    </span>
                </div>
             </td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
		</table>
		</div>
		';
		$reStr .='
		<div class="float_box2">
    	 <table border="0" cellpadding="0" cellspacing="0" style="border:#000000 1px solid; width:368px; height:364px;font-size: 9px; font-family: Arial, Helvetica, sans-serif;">
    	 <tr>
        <td height="31"><div style="font-family:Arial, Helvetica, sans-serif; font-size:7px">
      <table width="100%" border="0" cellspacing="1" cellpadding="3">
        <tr>
          <td width="35%" valign="top"><table width="100%" height="75" border="0" cellpadding="0" cellspacing="0" style="">
            <tr>
              <td colspan="2" valign="top"><img src="'.site_url('attachments').'/images/EUB01.jpg" alt="" width="110" height="20" /></td>
            </tr>
            <tr>
              <td width="51%" height="40" valign="bottom"><div style="font-family:Arial; font-size:8px; line-height:11px;">IMPORTANT:<br/>
                The item/parcel may be<br />
                opened officially.<br />
                Please print in English<br />
              </div></td>
              <td width="49%"><table width="36" height="32" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px solid #000; text-align:center; margin-right:">
                <tr>
                  <td width="100" height="20" ><font style="font-family:Arial; font-size:24px">'.$allParamArr['AreaID'].'</font>&nbsp;</td>
                </tr>
              </table></td>
            </tr>
          </table></td>
          <td width="65%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="left" valign="top"><div style=" margin-top:5px;text-align:center;"><img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" alt="" /></div></td>
            </tr>
            <tr>
              <td align="center" valign="top"><div style="font-size:12px"><strong>'.$allParamArr['ordersInfo']['orders_shipping_code'].'</strong></div></td>
            </tr>
          </table></td>
        </tr>
      </table>
    </div></td>
     </tr>
     <tr>
       <td height="46" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="42%" valign="top" style="border-bottom: 1px solid #000; border-right: 1px #000 solid"><div style="font-family:Arial; font-size:9px; padding-left:6px;"> 
          FROM:<br />
   			    &nbsp;'.$allParamArr['senderInfo']['sender'].'<br />
		        &nbsp;'.$allParamArr['senderInfo']['street'].'<br />
		        &nbsp;'.$allParamArr['senderInfo']['provinces'].'<br />
		        &nbsp;'.$allParamArr['senderInfo']['countryAndPostcode'].'<br /> 
          <div style="font-size:16px;" align="center"><strong>'.$allParamArr['ordersInfo']['erp_orders_id'].'</strong></div>
          PHONE:'.$allParamArr['senderInfo']['mobilePhone'].'</div></td>
        <td width="58%" rowspan="2" valign="top" style="border-top:#000 solid 1px">
        		<div style=" font-size:11px">
        SHIP TO:  '.($allParamArr['ordersInfo']['buyer_name']).'<br/>'.($allParamArr['ordersInfo']['buyer_address_1']).' '.($allParamArr['ordersInfo']['buyer_address_2']).'
                    <br/>'.($allParamArr['ordersInfo']['buyer_city']).' '.($allParamArr['ordersInfo']['buyer_state']).' '.($allParamArr['ordersInfo']['buyer_zip']).'<br/>'.($allParamArr['countryAll']).'
                
                </div>
        </td>
      </tr>
      <tr >
        <td style="border-bottom: 1px solid #000; border-right:#000 solid 1px"><div style=" font-size:10px; padding-left:5px;">Fees(US $):</div></td>
      </tr>
      <tr >
        <td height="16" style="border-bottom: 1px solid #000; border-right:#000 solid 1px"><div style="font-family:Arial; font-size:10px; padding-left:5px;">Certificate No.</div></td>
        <td style="border-bottom: 1px solid #000"><div style=" font-size:12px">PHONE: '.$allParamArr['ordersInfo']['buyer_phone'].'</div></td>
      </tr>
      <tr >
        <td height="16" colspan="2" style="border-bottom: 1px solid #000; border-right:#000 solid 1px"><table border="0" cellspacing="0" cellpadding="0" style="width:368px;">
          <tr>
            <td width="5%" align="center"  style="border-bottom: 1px solid #000; border-right:#000 1px solid;font-size:10px;"><span class="STYLE2">No</span></td>
            <td width="5%" align="center"  style="border-bottom: 1px solid #000; border-right:#000 1px solid;font-size:10px;"><span class="STYLE2">Qty</span></td>
            <td width="43%" height="15" align="left"  style="border-bottom: 1px solid #000; border-right:#000 1px solid;font-size:10px;"><span class="STYLE2">Description of Contents</span></td>
            <td width="12%" align="center"  style="border-bottom: 1px solid #000; border-right:#000 1px solid;font-size:10px;"><span class="STYLE2">Kg.</span></td>
            <td width="13%" align="left"  style="border-bottom: 1px solid #000; border-right:#000 1px solid;font-size:10px;"><span class="STYLE2">Val(sus$)</span></td>
            <td width="22%" align="left"  style="border-bottom: 1px solid #000; font-size:10px;"><span class="STYLE2">Goods Origin</span></td>
          </tr>
		 <tr style="height:15mm;">
			<td align="center" valign="top" style="border-right:#000 1px solid; border-bottom:#000 1px solid; font-size:10px;">
		     '.$allParamArr['productsInfo']['key'].'
			</td>
			<td align="center" valign="top" style="border-right:#000 1px solid; border-bottom:#000 1px solid;font-size:10px; ">
		     '.$allParamArr['productsInfo']['itemCount'].'
			</td>
			<td height="" align="left" valign="top" style="border-bottom:#000 1px solid; ">
				<div style=" font-size:10px;color#000;">
					<strong>
		            '.$allParamArr['productsInfo']['skuDetail'].'
					</strong>
				</div>
			</td>
			<td align="center" valign="top" style=" border-right:#000 1px solid;border-bottom:#000 1px solid;border-left:#000 1px solid; font-size:10px; ">
		    '.$allParamArr['productsInfo']['weight'].'
			</td>
			<td align="center" valign="top" style= "border-right:#000 1px solid; border-bottom:#000 1px solid; font-size:10px;">
		     '.$allParamArr['productsInfo']['declared_value'].'
		    </td>
			<td align="left" valign="top" style="font-size:10px; border-top:#000 1px solid; border-bottom:#000 1px solid;">'.$allParamArr['senderInfo']['country'].'</td>
		</tr>	
           <tr>
            <td align="center"  style="border-right:#000 1px solid;  font-size:10px;">&nbsp;</td>
            <td align="center"  style="border-right:#000 1px solid; font-size:10px; ">&nbsp;'.$allParamArr['productsInfo']['totalCount'].'</td>
            <td align="left"  style=" "><div style=" font-size:10px">Total Gross Weight (Kg.):</div></td>
            <td align="center"  style=" border-right:#000 1px solid; font-size:10px; border-left:#000 1px solid; ">&nbsp;'.$allParamArr['productsInfo']['totalWeight'].'</td>
            <td align="center"  style= "border-right:#000 1px solid; font-size:10px;">&nbsp;'.$allParamArr['productsInfo']['totalValue'].'</td>
            <td align="center"  style="font-size:10px;">&nbsp;</td>
          </tr>
        </table>
            <tr>
                   <td colspan="6" valign="bottom" >
                   <div style="font-family:Arial; font-size:6px;">
                   I certify the particulars given in this customs declaration are correct. This item does not contain any dangerous article, or articles prohibited<br> by 
                  legislation or by postal or customs regulations. I have met all applicable export filing requirements under the Foreign Trade Regulations. </div>
                  <div style="font-family:Arial; font-size:8px;">
                   <strong>Sender\'s Signature &amp; Date Signed:</strong>
                   <strong style="font-family:Arial; font-size:12px; text-align:right; margin-left:70px;">【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</strong>
                   <strong style="font-family:Arial; font-size:12px; text-align:right; margin-left:90px;">CN22</strong>
                   </div>
                  </td>
            </tr>
    </table>
    </td>
     </tr>
    	 </table>
    	</div>
		';
		return $reStr;
	}
	
	//德国邮政面单
	public function printForGermanyPost($allParamArr){
		$reStr ='
		<style>
			*{margin:0;}
			body{ font-family:Arial, Helvetica, sans-serif; font-size:14px;}
			td{ white-space:nowrap;}
			.PageNext{page-break-after:always; clear:both; min-height:1px; height:auto; overflow:auto; width:100%;}
			#main_frame_box{width:380px; margin:0 auto;}
			.float_box{ position: relative; float:left; width:370px; height:364px; overflow:hidden; margin:0px 3px 1px; border:1px solid black;}
		</style>
		';
		
		$reStr .='
		 <div id="main_frame_box">
		   <div class="float_box">
		   
		   		<div style="font-size: 12px;">
					<div style="float: left; width: 150px;">
						<div style="padding: 3px 0px 0px 3px; font-size: 12px; font-weight: bold;">Manifestabfertigung (X30)</div>
						
						<div style="float:left; font-size:10px; padding-left: 10px;"><strong>Rechnung</strong></div>
					</div>
					
					<div style="float: right; margin: 3px 3px 0 0; width: 210px;">
						<table width="100%" cellspacing="0" cellpadding="0" border="1">
							<tbody><tr style="font-size: 10px;">
								<td>
									Wenn unzustellbar,<br>
									zurück an:<br>
									<b>Postfach 2007</b><br>
									<b>36243 Niederaula</b><br>
								</td>
								
								<td align="center">
									<div style="border-bottom: 1px solid;"><b>Deutsche Post</b></div>
									<b>Entgelt bezahlt</b><br>
									60544 Frankfurt<br>
									(2378)<br>
								</td>
							</tr>
							
						</tbody></table>
					</div>
					
					<div style="clear: both;"></div>
				</div>

				<table border="0" cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<td>
							<table width="100%" cellpadding="3" cellspacing="0" border="1">
								<tr><td>Datum: '.$allParamArr['current_data'].'(X30)</td></tr>
								<tr><td>Kunde: '.$allParamArr['ordersInfo']['buyer_name'].'</td></tr>
								<tr>
								  <td style="word-break: keep-all; white-space:normal;">
								     Anschrift: '.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].' '.$allParamArr['ordersInfo']['buyer_city'].' '.$allParamArr['ordersInfo']['buyer_state'].' '.$allParamArr['ordersInfo']['buyer_zip'].'
								  </td>
								</tr>
								<tr><td>Ebay Artikelnummer: '.$allParamArr['productsInfo'][0]['orders_item_number'].'</td></tr>
								<tr><td>Rechnungsnummer: '.$allParamArr['ordersInfo']['erp_orders_id'].'</td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table width="100%" cellpadding="1" cellspacing="0" border="1" style="font-size: 10px;height:100px;overflow:hidden;">
								<tr align="center"><td>Beschreibung</td><td width="40">Zahl</td><td width="70">Preis</td></tr>
								'.$allParamArr['tr'].'
							</table>
						</td>
					</tr>
					<tr>
						<td align="center">
							<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" /><span class="fontSize10"><br>'.$allParamArr['ordersInfo']['orders_shipping_code'].'</span>
						</td>
					</tr>
				</table>
				<div style="position: absolute; right:0; bottom:0; height: 30px; width: 30px; font-size: 28px; font-weight: bold; z-index: 100;">'.$allParamArr['totalCount'].'</div>
			</div>
		 </div>
		';
		return $reStr;
	}
	
	/**
	 * JH邮局平邮面单模板处理
	 */
	public function printJHPingYouTemplate($allParamArr){
		if(isset($allParamArr['ordersInfo']['orders_old_shipping_code']) && $allParamArr['ordersInfo']['shipmentAutoMatched'] == 345){  //针对345-【平邮】JTXM
					$old_shipping = $allParamArr['ordersInfo']['orders_old_shipping_code'];
					$pad = "90px";
			 } else{
				    $old_shipping = '';
					$pad = "170px";
			 }
		
		$reStr = '
			<style>
			   *{margin:0;padding:0;}
				body{ font-family:Arial, Helvetica, sans-serif; font-size:14px;}
				#main_frame_box{width:382px; margin:0 auto;height:378px;overflow:hidden;}
				td{border:1px solid #000;border-bottom:none;}
			</style>
		';
		
		$reStr .='<div id="main_frame_box">
		        	<div style="width:380px;border:1px solid #000;border-bottom:none;">
			 		  <p style="float:left;width:140px;height:30px;">
			 		    <img src="'.site_url("attachments").'/images/EUB01.jpg" />
			 		  </p>
			 		  <p style="float:left;width:124px;height:30px;text-align:center;font-size:10px;font-weight:bold;line-height:30px;border-right:1px solid #000;">
			 		  Small Packet By Air
			 		  </p>
			 		  <p style="float:left;width:54px;line-height:30px;text-align:center;font-weight:bold;height:30px;border-right:1px solid #000;">
			 		    '.$allParamArr['country_code'].$allParamArr['country_fenjian'].'
			 		  </p>
			 		 <p style="float:left;width:60px;height:30px;line-height:30px;text-align:center;font-weight:bold;">
			 		    '.$allParamArr['ordersInfo']['shipmentAutoMatched'].'
			 		  </p>
			 		  <p style="float:left;width:140px;">
			 		     <span style="width:140px;display:inline-block;border-bottom:1px solid #000;font-size:11px;">
			 		       From:<br/>
			 		       '.$allParamArr['senderInfo']['street'].'<br/>
			 		       <b style="font-weight:bold;">Phone:'.$allParamArr['senderInfo']['mobilePhone'].'</b>
			 		     </span>
			 		     <span style="width:140px;line-height:29px;font-size:12px;background:#fff;display:inline-block;border-bottom:1px solid #000;">
			 		       	自编号:'.$allParamArr['ordersInfo']['erp_orders_id'].'
			 		     </span>
			 		  </p>
			 		  <p style="float:left;width:238px;border:1px solid #000;border-right:none;font-size:12px;">
			 		    <span style="font-weight:bold;font-size:12px;">Ship To:</span><br/>
			 		    	'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_city'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_state'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_zip'].'<br/>
							<b style="font-weight:bold;">'.$allParamArr['buyerCountry'].'
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<b style="font-weight:bold;">'.$allParamArr['ordersInfo']['area'].'</b>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							'.$allParamArr['country_cn'].'</b><br/>
							<b style="font-weight:bold;">Phone：</b>'.$allParamArr['ordersInfo']['buyer_phone'].'
			 		  </p>
		 		    </div>
		 		    
		 		      <table border="0" style="width:382px;height:155px;"  cellspacing="0" cellpadding="0">
		 		         <tr height="45">
		 		           <td colspan="3" style="border-top:none;">
		 		              <p style="width:80px;text-align:center;font-weight:bold;line-height:50px;height:50px;float:left;">
		 		              	Untracked
		 		              </p>
		 		              <p style="width:270px;height:45px;float:left;text-align:center;">
		 		                <img src="'.site_url("default/third_party").'/chanage_code/barcode/html/image.php?code=code128&o=2&t=15&r=2&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		 		                <br/>
		 		                '.$allParamArr['ordersInfo']['orders_shipping_code'].'
		 		              </p>
		 		           </td>
		 		         </tr>
		 		         <tr style="height:15px;font-weight:bold;font-size:10px;text-align:center;">
		 		           <td width="70%" style="border-right:none;">
		 		             Description of Contents
		 		           </td>
		 		           <td width="15%" style="border-right:none;">
		 		            Kg
		 		           </td>
		 		           <td width="15%">
		 		            Val(US $)
		 		           </td>
		 		         </tr>
		 		         '.$allParamArr['trInfo'].'
		 		         <tr height="15" style="font-size:12px;">
		 		           <td width="70%" style="border-right:none;font-size:12px;">
		 		             Totalg Gross Weight(kg)
		 		           </td>
		 		           <td width="15%" style="border-right:none;">'.$allParamArr['productsInfo']['total_weight'].'</td>
		 		           <td width="15%">'.$allParamArr['productsInfo']['total_value'].'</td>
		 		         </tr>
		 		         <tr height="50">
		 		           <td colspan="3" style="border-bottom:1px solid #000;font-size:9px;">
		 		             I the undersigned,certify that the particulars given in this declaration are correct and this item 
		 		             does not contain any dangerous articles prohibited by legislation or by postal or customers 
		 		             regulations.<br/>
		 		             <span style="font-weight:bold;">Sender\'s signature:SLME </span>
							 <span  style="font-weight:bold;padding-left:20px;">'.$old_shipping.'</span>
		 		             <span style="font-weight:bold;padding-left:'.$pad.';">CN22</span>
		 		           </td>
		 		         </tr>
		 		      </table>
		 		      <div style="width:382px;height:40px;margin:0 auto;font-size:10px;white-space:normal;overflow:hidden;">
		 				'.$allParamArr['skuInfo'].'
		 		      </div>
				</div>';
		return $reStr;
	}
	
	/**
	 * 打印中英专线面单
	 */
	public function printForCNToUKTemplate($allParamArr){
		$reStr = '
			<style>
				body{ font-family:Arial, Helvetica, sans-serif; font-size:14px;}
				td{ white-space:nowrap;}
				.PageNext{page-break-after:always; clear:both; min-height:1px; height:auto; overflow:auto; width:100%;}
				#main_frame_box{width:380px; margin:0 auto;}
				.float_box{ position: relative; float:left; width:370px; height:364px; overflow:hidden; margin:11px 3px 1px; border:1px solid black;}
			</style>
		';
		$reStr .='
		 <div id="main_frame_box">
		    <div class="float_box">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr height="5">
					<td colspan="2"></td>
				</tr>
				<tr>
					<td colspan="2">
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr><td width="25" height="60"></td>
							<td width="320" height="60"><img src="'.site_url("attachments").'/images/pingyou.jpg" width="320" height="60" border="0" /></td>
							<td width="25" height="60"></td></tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div style="font-weight:bold; width:215px; float:left; margin-top:20px; min-height: 100px; table-layout:fixed; word-break:break-all; word-wrap: break-word; white-space:normal; padding-left:7px;">
							'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_city'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_state'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_zip'].'<br/>
							'.$allParamArr['ordersInfo']['buyer_country'].'
						</div>
						<div style="float:left; margin-top:20px; float:right; width:148px; text-align:right;">
							<div style="float:right; border:1px solid black; margin-right:5px; padding:2px; font-weight:bold; white-space: normal; word-wrap: break-word;">'.$allParamArr['productsInfo']['skuInfo'].'</div>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div style="float:left; text-align:center; width:235px; margin-top:20px;">
						<img src="'.site_url("default/third_party").'/chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text='.$allParamArr['ordersInfo']['erp_orders_id'].'&f1=-1&f2=8&a1=&a2=B&a3=" /><span class="fontSize10"><br>'.$allParamArr['ordersInfo']['erp_orders_id'].'</span>
						</div>
						<div style="font-size:12px; height:98px; width:135px; float:right;white-space:normal;">
						Return Address<br/>
						'.$allParamArr['senderInfo']['street'].'
						<div>
					</td>
				</tr>
			</table>
			<div style="position: absolute; right: 0px; bottom: 0px; width: 30px; height: 30px; font-size: 28px; font-weight: bold; z-index: 100;">'.$allParamArr['productsInfo']['total_count'].'</div>
			</div>
		 </div>
		';
		return $reStr;
	}
	
	//中国邮政一体化面单
	public function printForPostXiaobao($allParamArr){
		$reStr = '
			<style>
			    *{margin:0;}
				body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;line-height:14px;}
				td{ white-space:nowrap;}
				.PageNext{page-break-after:always; clear:both; min-height:1px; height:auto; overflow:auto; width:100%;}
				#main_frame_box{width:380px; margin:0 auto;}
				.float_box1{ float:left; width:370px; height:370px;overflow:hidden; margin:1px 3px 1px; border:1px solid black;}
				.float_box2{ float:left; width:370px; height:334px;  margin:1px 3px 1px; border:1px solid black;}
			</style>
		';
		$reStr .= '
		  <div id="main_frame_box">
			<div class="float_box1">
				<table border="0" cellpadding="2" cellspacing="0" width="100%">
					<tr height="56">
						<td style="border-bottom: 1px solid black;">
						   <table cellpadding="0" cellspacing="0" border="0" width="100%">
						   	<tr align="center">
						   		<td width="112" rowspan="3"><img src="'.site_url('attachments').'/images/post_logo.jpg" width="112" height="34" border="0" /></td>
						   		<td width="120"><strong>航空</strong></td>
						   		<td rowspan="3">
                                	<table cellpadding="0" cellspacing="0" border="0" width="100%">
                                    	<tr align="center">
                                        	<td>
                                            	
                                            </td>                                        
                                        </tr>
                                        <tr align="center">
                                        	<td>
                                             '.$allParamArr['country'].' '.$allParamArr['countryCode'].' '.$allParamArr['country_fenjian'].'
                                            </td>                                        
                                        </tr>
                                    </table>									
                                 </td>
						   	</tr>
						   	<tr align="center">
						   		<td><strong>Small Packet</strong></td>
						   	</tr>
						   	<tr align="center">
						   		<td><strong>BY AIR</strong></td>
						   	</tr>
						   </table>
						</td>
					</tr>
					<tr valign="top" height="22">
						<td style="border-bottom: 1px solid black;">
							<div style="float:left;">协议客户：'.$allParamArr['backList']['consumer_name'].'</div>
						</td>
					</tr>
					<tr height="56">
						<td style="border-bottom: 1px solid black;">
							<table border="0" cellpadding="0" cellspacing="0">
								<tr><td width="50" align="right" valign="top"><strong>FROM:&nbsp;</strong></td><td colspan="3" style="white-space: normal; word-break: break-all;">'.$allParamArr['backList']['consumer_from'].'</td></tr>
								<tr><td align="right"><strong>ZIP:&nbsp;</strong></td><td width="100">'.$allParamArr['backList']['consumer_zip'].'</td><td width="40" align="right"><strong>TEL:&nbsp;</strong></td><td>'.$allParamArr['backList']['consumer_phone'].'</td></tr>
							</table>
						</td>
					</tr>
					<tr height="100">
						<td valign="top">
							<table border="0" cellspacing="0" cellpadding="0">
								<tr>
								  <td width="40" valign="top" align="right">
								    <strong>TO:&nbsp;</strong>
								  </td>
								  <td colspan="3" style="white-space: normal; word-break: keep-all;">
								    '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
								    '.$allParamArr['ordersInfo']['buyer_address_1'].'
								    '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
								    '.$allParamArr['ordersInfo']['buyer_city'].'
								    '.$allParamArr['ordersInfo']['buyer_state'].'
								    '.$allParamArr['displayname'].'
								  </td>
								</tr>
								<tr><td align="right">ZIP:&nbsp;</td><td width="100">'.$allParamArr['ordersInfo']['buyer_zip'].'</td><td width="40" align="right">TEL:&nbsp;</td><td>'.$allParamArr['ordersInfo']['buyer_phone'].'</td></tr>
								<tr><td>&nbsp;</td><td colspan="3" style="white-space: normal; word-break: break-all; font-size:9px;">'.$allParamArr['productsInfo']['skuInfo'].'</td></tr>
                                <tr>
                               
                            	</tr>
							</table>
						</td>
					</tr>
				</table>
				 <div style="width:370px;height:30px; font-size:25px;">
                           <span style="font-size:25px;">'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'</span>
                            
                            <span style="margin-left:240px;">'.$allParamArr['productsInfo']['totalCount'].'</span>
                </div>
				<div style="width: 370px;  bottom:60px; text-align: center; clear:both; border-top:1px solid black; padding-bottom: 2px;">
 					退件单位：'.$allParamArr['backList']['consumer_back'].'
 				</div>
				<div style="width: 370px; height:58px; bottom:0px; text-align: center; clear:both; border-top:1px solid black; padding-top: 2px;">
 					<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" /><span class="fontSize10"><br>'.$allParamArr['ordersInfo']['orders_shipping_code'].'</span>
 				</div>
			</div>
	
		';
		
		$reStr .='
		<div class="float_box2">
				<table border="0" cellpadding="2" cellspacing="0" width="100%" style="font-size: 11px;">
					<tr style="">
						<td style="border-bottom: 1px solid black;" colspan="7">
						   <table cellpadding="0" cellspacing="0" border="0" width="100%">
						   	<tr align="center">
						   		<td width="90" rowspan="2"><?php echo $sid;?></td>
						   		<td width="175"><strong>报关签条</strong></td>
						   		<td><strong>邮2113</strong></td>
						   	</tr>
						   	<tr align="center">
						   		<td><strong>CUMTOMS DECLARATION</strong></td>
						   		<td><strong>CN22</strong></td>
						   	</tr>
						   </table>
						</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid black;" colspan="7">
							<div style="width:150px; float:left;">可以经行拆开</div>
							<div style="float: left;">May be opened officially</div>
						</td>
					</tr>
					<tr style="line-height:12px;">
						<td width="50" align="center" style="border-bottom:1px solid black; border-right:1px solid black;" rowspan="2">邮件种类</td>
						<td width="20" style="border-bottom: 1px solid black; border-right:1px solid black;" align="center">&nbsp;</td>
						<td width="80" style="border-bottom: 1px solid black; border-right:1px solid black;">礼品<br/>gift</td>
						<td width="20" style="border-bottom: 1px solid black; border-right:1px solid black;">&nbsp;</td>
						<td style="border-bottom: 1px solid black;" colspan="3">商品货样<br/>Commercial Sample</td>
					</tr>
					<tr style="line-height:12px;">
						<td style="border-bottom: 1px solid black; border-right:1px solid black;">&nbsp;</td>
						<td style="border-bottom: 1px solid black; border-right:1px solid black;">文件<br/>Documents</td>
						<td width="20" style="border-bottom: 1px solid black; border-right:1px solid black;" align="center"><b style="font-family: \'宋体\'; font-size:16px;">X</b></td>
						<td style="border-bottom: 1px solid black;" colspan="3">其他<br/>Other</td>
					</tr>
					<tr style="line-height:12px;">
						<td width="240" style="border-bottom: 1px solid black; border-right:1px solid black;" colspan="5" align="center">内件详细名称和数量<br/><span style="font-size: 10px;">Quantity and detailed description ofcontents</span></td>
						<td width="70" style="border-bottom: 1px solid black;border-right:1px solid black;" align="center">重量(千克)<br/>Weight(Kg)</td>
						<td width="60" style="border-bottom: 1px solid black;" align="center">价值<br/>Value</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid black; border-right:1px solid black;" colspan="5" align="center">
						'.$allParamArr['productsType'][$allParamArr['productsInfo'][0]['products_sort']]['category_name'].'<br>
						'.$allParamArr['productsType'][$allParamArr['productsInfo'][0]['products_sort']]['category_name_en'].'
						</td>
						<td style="border-bottom: 1px solid black;border-right:1px solid black;" align="center">
						'.$allParamArr['skuWeight'].'
						</td>
						<td style="border-bottom: 1px solid black;" align="center">
						'.$allParamArr['skuValue'].'USD
						</td>
					</tr>
					
					<tr style="font-size:11px; line-height:12px;">
						<td width="240" style="border-bottom: 1px solid black; border-right:1px solid black; white-space: normal; word-break: break-all;" colspan="5" rowspan="2">
						协调系统税则号列和货物原产国(只对商品邮件填写)<br/>
						<p style="word-spacing: 0px; padding:0px; margin:0px; word-break: keep-all;">HS tariff number and country of origin of goods(For Commercial items only)</p>
						</td>
						<td style="border-bottom: 1px solid black;border-right:1px solid black;" align="center">总重量<br/>Total Weight(kg)</td>
						<td style="border-bottom: 1px solid black;" align="center">总价值<br/>Total Value</td>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid black;border-right:1px solid black;" align="center">
						'.$allParamArr['productsInfo']['totalWeight'].'
						</td>
						<td style="border-bottom: 1px solid black;" align="center">
						'.$allParamArr['productsInfo']['totalValue'].'USD
						</td>
					</tr>
					<tr>
						<td colspan="7" style="white-space:normal;">
						我保证上述申报准确无误，本函件内未装寄法律或邮件和海关规章禁止寄递的任何危险物品<br/>
						<p style="word-wrap:normal; word-break: keep-all; margin:0; padding:0;">I the undersigned,certify that the particulars given in this declaration are correct and this item does not contain any dangerous articles prohibited by legislation or by postal or customers regulations.</p>
						<p style="white-space:normal; word-break: keep-all; margin:0; padding:0; text-align: center;">寄件人签字 Sender\'s signature: '.$allParamArr['backList']['sender_signature'].'</p>
						</td>
					</tr>
				</table>
			</div>
		  </div>
		';
		
		return $reStr;
	}
	
	//打印中德专线面单
	public function de_print_orders_template($allParamArr){
		$reStr = '
		<style type="text/css">
			*{ padding:0mm; margin:0mm; font-family:Arial;}
			.ordersBox{ width:100mm; padding:2mm 3mm 3mm 2mm; height:54mm; float:left; position:relative;}
			.returnAddressBox{ height:4mm; line-height:4mm; font-size:3mm; text-align:center; position:absolute; top:1mm; width:100mm; left:0mm;}
			.remark{height:4mm;width:100mm;font-size:3mm;position:absolute; top:5mm;text-align:center;}
			.codeBox{ width:65mm; height:10mm; text-align:left; padding-left:5mm; position:absolute; left:2mm; top:10mm;}
			.ordersNOBox{ height:8mm; width:28mm; border:1mm solid #000000; position:absolute; top:10mm; right:3mm; text-align:center; line-height:4mm; font-size:3mm; font-weight:bold;}
			.anBox{ height:4mm; position:absolute; left:2mm; top:17mm; font-weight:bold; font-size:4mm;}
			.buyerInfoBox{ height:24mm; line-height:1.2em; font-weight:bold; position:absolute; top:23mm; left:5mm;}
			.cutLine{ width:100mm; position:absolute; top:46mm; left:0mm; height:3mm; text-align:right; font-size:3mm; background:url('.site_url('attachments').'/images/cutLineBg.jpg) repeat-x 0 50%;}
			.cutLineText{ background:#FFFFFF; float:right; padding-left:1mm;}
			.skuBox{ position:absolute; top:49mm; font-size:4mm;}
			.PageNext{page-break-after:always; clear:both; height:auto; overflow:auto;}
		</style>
		';
		$reStr .='
		  <div style="width:210mm;">
			  <div class="ordersBox">
				<div class="returnAddressBox">Abs:Win Trade GmbH,Behringstr,24,01159,Dresden,Deutschland</div>
				<div class="remark">Win Trade ist nur eine logistische Firma, keine Handelsfirma</div>
				<div class="codeBox"><img align="middle" src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=12&r=2&text=' .$allParamArr['ordersInfo']['erp_orders_id']. '&f1=-1&f2=8&a1=&a2=B&a3=" /></div>
				<div class="ordersNOBox">Order NO.<br>' .$allParamArr['ordersInfo']['erp_orders_id']. '</div>
				<div class="anBox">An:</div>
				<div class="buyerInfoBox">' . $allParamArr['ordersInfo'][ 'buyer_name' ] . '<br>' . $allParamArr['ordersInfo'][ 'buyer_address_1' ] . ' ' . $allParamArr['ordersInfo'][ 'buyer_address_2' ] . '<br>' . $allParamArr['ordersInfo'][ 'buyer_city' ] . ',' . $allParamArr['ordersInfo'][ 'buyer_state' ] .','.$allParamArr['ordersInfo']['buyer_zip']. '<br>Deutschland</div>
				<div class="cutLine"><div class="cutLineText">Product List(For Customer)</div></div>
				<div class="skuBox">' . $allParamArr['ordersInfo'][ 'shipmentAutoMatched' ] . '-SKU:' . $allParamArr['productsInfo']['skuString']. ' <span style="font-size:10px; color:#0099FF">' . date( 'Y-m-d' ) . '</span></div>
			  </div>
		  </div>
		';
		return $reStr;
	}
	
	//打印广州平邮小包面单模板处理
	public function printGuangZhouPostTemplate($allParamArr){
		$reStr = '
			<style>
			    *{margin:0;padding:0;}
				body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
				
				table{border-collapse:collapse;border:none;width:99mm;height:60mm;}
				div>p>table>.detail{width:280px;height:130px;border:none;}
				td{border:1px solid #000;}
			</style>
		';
		$reStr .= '
		<div style="width:378px;height:378px;margin:0 auto;overflow: hidden;">
		  <div id="main_frame_box" style="height:340px;">
		    <table>
		      <tr height="10px" style="font-size:9px;">
		        <td colspan="3">
		          <p style="width:33mm;height:10px;line-height:21px;float:left;">
		            	
		          </p>
		          <p style="width:33mm;height:10x;float:left;text-align:center;font-weight:bold;">
		          	  中国邮政
		          	 CHINA POST
		          </p>
		          <p style="width:32mm;height:10px;float:left;text-align:center;font-weight:bold;">
		             <span style="width:13mm;height:10px;line-height:10px;border:1px solid #000;display:inline-block;">
		               	邮2113
		             </span>
		             
		             CN22
		          </p>
		        </td>
		      </tr>
		      <tr height="10px">
		        <td colspan="3">
		          <span style="font-size:9px;font-weight:bold;">
		        	  协议客户号:44010100176000
		          </span>
		        </td>
		      </tr>
		      <tr height="80px">
		        <td colspan="3">
		          <span style="font-size:11px;height:80px;width:97mm;float:right;overflow:hidden;"><strong> TO:</strong> 
		            '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
			        '.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
			        '.$allParamArr['ordersInfo']['buyer_city'].' '.$allParamArr['ordersInfo']['buyer_state'].'<br/>
			        '.$allParamArr['buyerCountry'].'('.$allParamArr['country_cn'].')
			        ZIP:'.$allParamArr['ordersInfo']['buyer_zip'].' &nbsp;&nbsp;&nbsp;&nbsp; TEL:'.$allParamArr['ordersInfo']['buyer_phone'].'

		           </span>
		        </td>
		      </tr>
		      <tr height="10px">
		        <td colspan="3">
		          <span style="font-size:9px;font-weight:bold;">
		          		退件单位：广州邮政国际小包处理中心 
		          </span>
		          <span style="font-size:9px;font-weight:bold;padding-left:20px;">
		          		DCPS
		          </span>
		        </td>
		      </tr>
		      
				<tr height="10px">
		              <td colspan="3">
		                <p style="display:inline-block;width:110px;float:left;font-size:10px;font-weight:bold;">
		                	<span style="font-size:8px;">CUSTOMS DECLARATION</span>
		                </p>
		                <p style="display:inline-block;width:100px;float:left;font-size:10px;text-align:center;">
		                	<span style="font-size:8px;">May be open officially</span>
		                </p>
		                <p style="display:inline-block;width:120px;float:left;font-size:10px;text-align:center;">
		                	<span style="font-size:8px;padding-left:5px;">May be opend officially</span>
		                </p>
		              </td>
		       </tr>
		       <tr height="14px">
		              <td width="58%">
		                	<span style="font-size:10px;float:left;display:inline-block;">
		                	  邮件种类 
		                	 <b style="font-size:8px;">Category of Item</b>
		                	</span>
		                	<span style="width:15px;height:14px;border-left:1px solid #000;display:inline-block;float:right;font-size:10px;font-weight:bold;">
		                	√
		                	</span>
		              </td>
		              <td colspan="2">
		                 <span style="font-size:10px;">
		                   	其他  Other  
		                 </span>
		              </td>
		      </tr>
		      <tr height="24px">
		              <td width="58%">
		                <span style="font-size:10px;display:inline-block;height:23px;width:210px;">
		                  	内装详细名称和数量 
		                    <span style="font-size:8px;">
		                     quantity  and detailed of description
		                    </span>
		                </span>
		              </td>
		              <td width="20%">
		               <span style="font-size:10px;display:inline-block;height:23px;padding-left:5px;">
		                  	重量(千克) <br/> 
		                    <span style="font-size:8px;">
		                     Weight(Kg)
		                    </span>
		                </span>
		              </td>
		              <td width="22%">
		                <span style="font-size:10px;display:inline-block;height:23px;padding-left:10px;">
		                  	价值 <br/> 
		                    <span style="font-size:8px;">
		                     Value
		                    </span>
		                </span>
		              </td>
		       </tr>
		       <tr height="14px" style="font-size:10px;text-align:center;">
		              <td width="58%" style="font-size:10px;font-weight:bold;">
		               '.$allParamArr['productsInfo']['namefiles'].'
		              </td>
		              <td width="20%" style="font-weight:bold;font-size:10px;">
		               '.$allParamArr['productsInfo']['totalWeight'].'
		              </td>
		              <td width="22%" style="font-weight:bold;font-size:10px;">
		               USD&nbsp;'.$allParamArr['productsInfo']['totalPrice'].'&nbsp;
		              </td>
		       </tr>
		       <tr height="23px" style="text-align:center;">
		              <td width="58%">
		      
		               <span style="font-size:8px;">
		                 	<span style="font-size:8px;">协调系统税则号列和货物原产过只对商品邮件编写</span>HS tarif number and country  origin of goods For Commerci items only
		               </span>
		         
		              </td>
		              <td width="20%" rowspan="2">
		                <span style="font-size:10px;display:inline-block;height:38px;padding-left:5px;">
		                  	总重量(千克) <br/> 
		                    <span style="font-size:8px;">
		                    Total Weight(Kg)
		                    </span><br>
		                    <span style="font-size:10px;font-weight:bold;">'.$allParamArr['productsInfo']['totalWeight'].'</span>
		                </span>
		              </td>
		              <td width="22%" rowspan="2">
		                <span style="font-size:10px;display:inline-block;height:38px;padding-left:5px;">
		                  	总价值 <br/> 
		                    <span style="font-size:8px;">
		                     Total Value<br>
		                     <span style="font-size:10px;font-weight:bold;">USD&nbsp;'.$allParamArr['productsInfo']['totalPrice'].'&nbsp;</span>
		                    </span>
		                </span>
		              </td>
		       </tr>
		       <tr height="13px">
		              <td width="58%" style="text-align:center;">
		               <span style="font-size:10px;font-weight:bold;">CN</span>
		              </td>
		       </tr>
		      <tr height="40px">
		        <td colspan="3">
		         <p style="font-size:7px;padding-left:5px;">
		          	我保证上述申报准确无误，本函件内未装寄法律或邮件和海关规章禁止寄递的任何危险物品<br/>
		          	I the undersigned,certify that the particulars given in this declaration are correct and this item does not contain any
		            dangerous articles prohibited by legislation or by postal or customers regulations.
		         </p>
		         <p style="padding-left:5px;font-size:7px;border-top:1px solid #000;">
		        	 寄件人签字 Sender\'s signature:  <span style="font-weight:bold;">SLME('.$allParamArr['ordersInfo']['erp_orders_id'].')</span>&nbsp;&nbsp;&nbsp;&nbsp;
		        	 <span style="font-weight:bold;padding-left:90px;">'.$allParamArr['productsInfo']['time'].'</span>
		         </p>
		        </td>
		      </tr>
		    </table>
		    <div>
		      <p style="font-size:9px;height:12px;overflow:hidden;">
		        From:'.$allParamArr['senderInfo']['sender'].' '.$allParamArr['senderInfo']['address'].'  510000   
		      </p>
		      <div>
			      <table style="width:375px;height:33px;font-size:5px;text-align:center;">
			        <tr height="9">
			          <td width="80px">航空</td>
			          <td width="80px">Guangzhou China</td>
			          <td width="200px" rowspan="4" style="font-size:25px;">'.$allParamArr['gekou'].'</td>
			        </tr>
			        <tr height="9">
			          <td width="80px">PAP AVON</td>
			          <td width="80px">已验视</td>
			          
			        </tr>
			        <tr height="9">
			          <td width="80px">小包邮件</td>
			          <td width="80px">单位：广州小包中心</td>
			         
			        </tr>
			        <tr height="9">
			          <td width="80px">PETIT PAQUET</td>
			          <td width="80px">验视人：方静霞</td> 
			        </tr>
			      </table>
			     
		      </div>
		      <div style="margin-left:50px;">
		      	 <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=23&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3="/><br/>
			     <span style="font-size:9px;">'.$allParamArr['ordersInfo']['orders_shipping_code'].'</span>
		      </div>
		      <div style="font-size:11px;text-align:center;">
		     '.$allParamArr['productsInfo']['skufiles'].'
		  </div>
		    </div>
		    
		  </div>
	    </div>
		';
		return $reStr;
	}
	
	//打印4px面单模板处理
	public function print4pxTemplate($allParamArr){
		$reStr = '
			<style>
			    *{margin:0;padding:0;}
				body{ font-family:Tahoma,Arial,"Times New Roman","微软雅黑","Arial Unicode MS"; font-size:14px; line-height: 1.3;}
				#main_frame_box{width:75mm;height:98mm;margin:0 auto;border:1px dotted gray; position: relative; overflow:hidden;}
				.top{width:354px;height:70px;margin:10px auto;}
				table{border-collapse:collapse;border:none;width:354px;height:270px;margin:15px auto;}
				td{border:1px solid #000;}
		        div, span {word-wrap: break-word;}
			</style>
		';
		$reStr .= '
				<div id="main_frame_box">
					<div style="padding: 5px 4px;">
						<div style="width: 100%; overflow: hidden;">
							<div style="float: left; height: auto; width: auto;">
								<div style="font-size: 8px; font-weight: bold; width: 190px;">
									<div style="float: left; padding: 0 1px;">
										Sender:
									</div>
									
									<div style="font-size: 8px; font-weight: normal; line-height: 9px; float: left; padding: 0 1px;">
										4PX<br>
										P.O.Box 6880<br>
										FI-00002 HELSINKI<br>
										FINLAND
									</div>
								</div>
							</div>
				
							<div style="float: right; height: auto; width: auto;">
								<div style="font-size: 10px; line-height: 11px; text-align: center; width: 80px;">
									<div style="border: 1px solid black; padding: 3px 0;">
										Port Payé<br>
										Finlandde<br>
										855051<br>
										Itella Posti Oy
									</div>
				
									<div style="background-color: black; color: #fff; margin-top: 3px; padding: 3px 0;">
										PRIORITY
									</div>
								</div>
							</div>
						</div>
						
						<div style="overflow: hidden; width: 100%;">
							<div style="float: left; height: auto; width: auto;">
								<div style="font-size: 10px; line-height: 11px; min-height: 70px; font-weight: bold; width: 200px;">
									<div style="overflow: hidden;">
										<div style="float: left; padding: 0 1px;">
											Recipient:
										</div>
										
										<div style="width: 145px; word-break: break-all; float: left; padding: 0 1px;">
											'. $allParamArr['ordersInfo']['buyer_name'] .'
										</div>
									</div>
									
									<div style="font-weight: normal;">
										'. $allParamArr['ordersInfo']['buyer_address_1'] . $allParamArr['ordersInfo']['buyer_address_2'] .'  '.
										$allParamArr['ordersInfo']['buyer_city'] .'  '. $allParamArr['ordersInfo']['buyer_state'] .'
									</div>
									
									<div style="overflow: hidden;">
										<div style="float: left; padding: 0 1px;">
											ZIP:
										</div>
										
										<div style="font-weight: normal; width: 145px; word-break: break-all; float: left; padding: 0 1px;">
											'.$allParamArr['ordersInfo']['buyer_zip'].'
										</div>
									</div>
									
									<div style="overflow: hidden;">
										<div style="float: left; padding: 0 1px;">
											Tel:
										</div>
										
										<div style="font-weight: normal; width: 145px; word-break: break-all; float: left; padding: 0 1px;">
											'.$allParamArr['ordersInfo']['buyer_phone'].'
										</div>
									</div>
									
									<div style="line-height: 14px;">
										'.$allParamArr['countryInfo']['display_name'].'('.$allParamArr['countryInfo']['country_cn'].')
									</div>
									
								</div>
							</div>
						</div>
						
						<div style="height: 10mm; width: 100%;">
						</div>
												
						<div style="width: 100%;">
							<div style="padding: 5px 4px;">
								<div style="overflow: hidden; width: 100%;">
									<div style="text-align: center; width: 100%; float: left; height: auto;">
										<div style="padding-top: 3px; text-align: center; width: 250px;">
											<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
										</div>
									</div>
								</div>
								
								<div style="border: 1px solid black; margin-bottom: 2px; overflow: hidden; width: 100%;">
									<div style="float: left; height: auto; width: auto;">
										<div style="padding-right: 5px; font-size: 9px;">
											RU POST-LINK OMAIL
										</div>
									</div>
									
									<div style="float: left; height: auto; width: auto;">
										<div style="padding-right: 5px; font-size: 9px;">
											【755219089】
										</div>
									</div>
									
									<div style="float: left; height: auto; width: auto;">
										<div style="padding-right: 5px; font-size: 9px;">
											<span style="word-wrap: break-word;">Ref No: </span>
												LME'.$allParamArr['ordersInfo']['erp_orders_id'].'
										</div>
									</div>
								</div>
								
								<div style="border: 1px solid black; margin-bottom: 2px; overflow: hidden; width: 100%;">
									<div style="float: left; height: auto; width: auto;">
										<div style="padding-right: 5px; font-size: 9px;">
											CS: S4305
										</div>
									</div>
									
									<div style="float: left; height: auto; width: auto;">
										<div style="padding-right: 5px; font-size: 9px;">
											SD: S0365
											(X011)
										</div>
									</div>
								</div>
								
								<div style="overflow: hidden; width: 100%; height: 20mm;">
									<div style="float: left; height: auto; width: auto;">
										<div style="font-size: 9px;">
											<b>【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</b>
											'.$allParamArr['productsInfo']['sku'].'
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				';
		return $reStr;
	}
	
	/**
	 * LWE小包面单模板
	 */
	public function printLWETemplate($allParamArr){
		$reStr = '';
		
		$reStr .= '
				<style>
				*{margin:0; padding:0;}
				#main{width:100mm; height:129mm; margin:auto;}
				#main_border{width:99mm; height:128mm; margin: 2px auto 0; border:1px solid; overflow: hidden;}
				body{font-size: 10px;}
				.f_l{float:left;}
				.f_r{float:right;}
				.address tr th{text-align:left;}
				.address tr td{text-align:right;}
				</style>
				';
		$reStr .= '
				<div id="main">
					<div id="main_border">
						<div style="width:98%; margin:auto;">
							<div class="f_l" style="width:145px; font-size: 8px;">
							    If undelivered, please return to:<br/>
								Locked Bag No. 1335<br/>
								Special Project Unit,<br/>
								Pos Malaysia International Hub<br/>
								Pos Malaysia Berhad<br/>
								Jalan KLIA 1<br/>
								64009 Malaysia
							</div>
					
							<div class="f_r" style="width:133px; border:1px solid #000; text-align:center; line-height:10px;">
								<p style="font-weight:bold;">BAYARAN POS JELAS</p>
								<p style="font-weight:bold;">POSTAGE PAID</p>
								<p>POS MALAYSIA</p>
								<p>INTERNATIONAL HUB</p>
								<p>PMIH,MALAYSIA</p>
								<p>PMK 1335</p>
							</div>
				
							<div style="clear: both;"></div>
						</div>
				
						<div style="width:100%; border-top:1px solid; border-bottom:1px solid; line-height:10px;height:70px;">
							
							<p style="width:84%;height:63px;float:left;font-weight:bold;line-height:10px;">
							  SHIP TO:'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
							  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. $allParamArr['ordersInfo']['buyer_address_1'] .' '. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>
							  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. $allParamArr['ordersInfo']['buyer_city'] .','. $allParamArr['ordersInfo']['buyer_state'] .','. $allParamArr['ordersInfo']['buyer_zip'] .'<br/>
							  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. $allParamArr['countryInfo']['display_name'] . '<br/>
							  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. $allParamArr['ordersInfo']['buyer_phone'] .'
							</p>
							<p style="width:14%;height:45px;margin-top:8px;float:left;display:inline-block;border:2px solid #000;font-size:30px;font-weight:bold;line-height:45px;text-align:center;">' . $allParamArr['countryInfo']['country_en'] .'</p>
						</div>
								  		
						<div>
							<div class="f_l" style="width:170px;height:50px;line-height:50px;text-align:center;">
								<span style="font-weight:bold;font-size:18px;">Registered</span>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<span style="font-weight:bold;font-size:26px;">R</span>
							</div>
								  		
							<div class="f_l" style="text-align:center;">
								<p style="line-height:12px;"><b>MALAYSIA POST Airmail</b></p>
								 <div>
								 	<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
					  			</div>
								 <p style="line-height:12px;"><b>'. $allParamArr['ordersInfo']['orders_shipping_code'] .'</b></p>
							</div>
								 			
							<div style="clear: both;"></div>
						</div>
								 		
						<div style="border-top:1px solid; border-bottom:1px solid;">
							<div class="f_l">
								CUSTOMS DECLARATION
					 		</div>
								 		
					 		<div class="f_l" style="width:166px; text-align:center;">
								May be opened officially
					 		</div>
								 		
					 		<div class="f_l">
								<b>CN 22</b>
					 		</div>
								 		
							<div style="clear: both;"></div>
						</div>
								 		
						<div style="padding:0px; border-bottom:1px solid;">
							<div class="f_l">
								Postal administration
					 		</div>
								 		
					 		<div class="f_r">
								Tick as appropriat
					 		</div>
								 		
							<div style="clear: both;"></div>
						</div>
								 		
						<div style="border-bottom:1px solid;">
							<div class="f_l" style="padding:0px 10px; border-right:1px solid;">
							 	<input name="wx" type="checkbox">
					 		</div>
								 		
					 		<div class="f_l" style="padding:0px; width:100px;">
								Gift
					 		</div>
								 		
					 		<div class="f_l" style="padding:0px 10px; border-right:1px solid; border-left:1px solid;">
							 	<input name="wx" type="checkbox">
					 		</div>
								 		
							<div class="f_l" style="padding:0px;">
								Commercial sample
					 		</div>
								 		
							<div style="clear: both;"></div>
						</div>
								 		
						<div style="border-bottom:1px solid #000;">
							<div class="f_l" style="padding:0px 10px; border-right:1px solid;">
							 	<input name="wx" type="checkbox">
					 		</div>
								 		
					 		<div class="f_l" style="padding:0px; width:100px;">
								Document
					 		</div>
								 		
					 		<div class="f_l" style="padding:0px 10px; border-right:1px solid; border-left:1px solid;">
							 	<input name="wx" type="checkbox" checked>
					 		</div>
								 		
							<div class="f_l" style="padding:0px;">
								Other
					 		</div>
								 		
							<div style="clear: both;"></div>
						</div>
								 		
						<table style="width:100%; border-collapse:collapse; border:medium none;" >
					 		<colgroup>
						       <col width="46%">
						       <col width="26%">
						       <col width="26%">
						    </colgroup>
							<tr>
								<td style="border:1px solid #000;border-top:none;">Quantity and detailed description of<br> contents</td>
						 		<td style="border:1px solid #000;border-top:none;">Weight (in kg)</td>
						 		<td style="border:1px solid #000;border-top:none;">Value</td>
							</tr>
								 		
							<tr>
								<th style="border:1px solid #000;text-align:left;">'. $allParamArr['productsInfo'][0]['products_declared_en'] . ' x ' . $allParamArr['productsInfo'][0]['item_count'] .'</th>
						 		<th style="border:1px solid #000;">'. $allParamArr['productsInfo'][0]['products_weight'] .'</th>
						 		<th style="border:1px solid #000;">'. $allParamArr['ordersInfo']['orders_total'] . 'USD' .'</th>
							</tr>
								 		
							<tr>
								<td style="border:1px solid #000;border-top:none;"></td>
						 		<td style="border:1px solid #000;border-top:none;">Total Weight (in kg)</td>
						 		<td style="border:1px solid #000;border-top:none;">Total Value(USD)</td>
							</tr>
								 		
							<tr>
								<th style="border:1px solid #000;border-top:none;"></th>
						 		<th style="border:1px solid #000;border-top:none;">'. $allParamArr['productsInfo'][0]['products_weight'] .'</th>
						 		<th style="border:1px solid #000;border-top:none;">'. $allParamArr['ordersInfo']['orders_total'] .'</th>
							</tr>
						</table>
						 				
						 <div style="padding:3px;">
						 	<div style="font-size: 8px;">
						 		I,the undersigned,whose name and address are given on the itme, certify that the particulars given in this declaration are correct and that this item does not contain any dangerous article or articles pro-hibited by legislation or by postal or by customs regulations 
						 	</div>
						 				
					 		<div style="text-align:right; width: 97%;">
					 		   <span style="font-size:15px;font-weight:bold;">【'. $allParamArr['ordersInfo']['shipmentAutoMatched'] .'】</span>  
					 			<b>SLME</b> '. date('Y-m-d') .'
					 		</div>
						 </div>
					 					
					 	<div style="padding:3px 6px; border-top:1px solid;">
					 		<div class="f_l">
					 			<div>
				 					<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['erp_orders_id'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
					 			</div>
				 				<p style="text-align:right;">' .$allParamArr['ordersInfo']['erp_orders_id']. '</p>
		 					</div>
				 						
				 			<div class="f_r" style="width:250px;">
				 			    <p style="height:12px;text-align:right;font-weight:bold;">15613/500047</p>
				 				'. $allParamArr['productsInfo']['sku'] .'
				 			</div>
				 						
				 			<div style="clear: both;"></div>
					 	</div>
					</div>
				</div>
				';
		
		return $reStr;
	}
	
	/**
	 * 顺友平邮面单，开始启用100*100的面单
	 * 挂号与平邮面单分开
	 */
	public function printshunYouPingYouTemplate($allParamArr){
		
		$reStr = '';
		
		$reStr .= '
				<style>
				*{margin:0; padding:0;}
				#main{width:100mm; height:99mm; margin:0 auto;border:1px solid; overflow: hidden;}
				body{font-size: 10px;}
				.f_l{float:left;}
				.f_r{float:right;}
				.address tr th{text-align:left;}
				.address tr td{text-align:right;}
				</style>
				';
		$reStr .='
		  <div id="main">
		    <div style="width:100%;height:1mm;"></div>
		    <div style="width:100%;height:42mm;">
		      <p style="width:68mm;height:42mm;float:left;margin-left:5px;overflow:hidden;font-size:7px;">
		        If underliverable return to : <br/>	       
                Locked bag No      <br/>	                    
                Special Project Unit    <br/>                   
                POS MALAYSIA INTERATIONAL HUB <br/>   
                64000 MALAYSIA<br/>
                
                <span style="font-size:11px;font-weight:bold;">
                To:'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
                   '. $allParamArr['ordersInfo']['buyer_address_1'] .' '. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>
                   '. $allParamArr['ordersInfo']['buyer_city'] .','. $allParamArr['ordersInfo']['buyer_state'] .'<br/>'
					. $allParamArr['ordersInfo']['buyer_zip'] .' '.$allParamArr['ordersInfo']['buyer_phone'].'<br/>
                   '. $allParamArr['countryInfo']['display_name'] . '(' . $allParamArr['countryInfo']['country_cn'] .')' .'
                </span>
		      </p>
		      <p style="width:30mm;height:42mm;float:left;overflow:hidden;">
		        <span style="width:98%;height:24mm;border:1px solid #000;display:inline-block;">
		        	BAYARAN POS JELAS<br/>
					POSTAGE PAID<br/>
					POS MALAYSIA<br/>
					INTERNATIONAL HUB<br/>
					MALAYSIA<br/>
					PMK1348
		        </span>
		        <span style="width:98%;height:8mm;border:1px solid #000;display:inline-block;border-top:none;font-size:14px;font-weight:bold;line-height:8mm;">
		         &nbsp;Z&nbsp;:&nbsp;'.$allParamArr['areaCode'].'
		        </span>
		        <span style="width:98%;height:8mm;line-height:8mm;border:1px solid #000;display:inline-block;border-top:none;">
		        '.$allParamArr['ordersInfo']['erp_orders_id'].'<span style="font-size:14px;font-weight:bold;">【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</span>
		        </span>
		      </p>
		    </div>
		    <div style="width:100%;height:12mm;">
		      <p style="width:30%;height:12mm;float:left;font-weight:bold;font-size:30px;text-align:right;">
		         
		      </p>
		      <p style="width:45%;height:12mm;float:left;text-align:center;">
		        <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=30&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		        <br/><span style="font-size:12px;">'.$allParamArr['ordersInfo']['orders_shipping_code'].'</span>
		      </p>
		    </div>
		    <div style="width:100%;height:44mm;overflow:hidden;">
		       <table style="width:98%; border-collapse:collapse; border:medium none;margin:0 auto;" >
		         <colgroup>
			       <col width="42%">
			       <col width="18%">
			       <col width="20%">
			       <col width="20%">
				 </colgroup>
		         <tr>
		          <td style="border:1px solid #000;" colspan="2">CUSTOMS DECLARATION CN 23</td>
		          <td style="border:1px solid #000;border-left:none;" colspan="2">May be opened officially</td>
		         </tr>
		         <tr>
		          <td style="border:1px solid #000;border-top:none;" colspan="2">For commercial items only</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">Total Wt</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">Total Value</td>
		         </tr>
		         <tr>
		          <td style="border:1px solid #000;border-top:none;" colspan="2">Country of origin: CN</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">'.$allParamArr['total_weight'].'</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">$'.$allParamArr['total_price'].'</td>
		         </tr>
		         <tr>
		          <td style="border:1px solid #000;border-top:none;" rowspan="2">
		            <span style="width:7px;height:7px;border:1px solid #000;display:inline-block;font-size:8px;font-weight:bold;text-indent:-999px;">√</span>&nbsp;Gift&nbsp;
		            <span style="width:7px;height:7px;border:1px solid #000;display:inline-block;font-size:8px;font-weight:bold;text-indent:-999px;">√</span>&nbsp;Commercial sample<br/>
		            <span style="width:7px;height:7px;border:1px solid #000;display:inline-block;font-size:8px;font-weight:bold;">√</span>&nbsp;Other
		            <span style="width:7px;height:7px;border:1px solid #000;display:inline-block;font-size:8px;font-weight:bold;text-indent:-999px;">√</span> &nbsp;Documents<br/>
		          </td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;" rowspan="2">HS Tariff No:</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;" colspan="2">Certificate No:</td>
		         </tr>
		         <tr>
		          <td style="border:1px solid #000;border-left:none;border-top:none;" colspan="2">Invoice No:</td>
		         </tr>
		         <tr style="font-size:8px;">
		          <td style="border:1px solid #000;border-top:none;">Importer`s ref(taxcode/VAT no)if any:</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">Postage Fees:</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;" colspan="2">Office of origin/Date of posting:</td>
		         </tr>
		         <tr style="font-size:7px;">
		          <td style="border:1px solid #000;border-top:none;" colspan="4">
		           I certify that the particulars given in this customs declaration are correct and that this item doesnot
				   contain any dangerous article prohibited by legislation or by postal or customs regulations.
		          </td>
		         </tr>
		         <tr>
		          <td style="border:1px solid #000;border-top:none;" colspan="2">Signature of sender:'.$allParamArr['sign'].'</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;" colspan="2">Date:'.date('Y-m-d H:i:s').'</td>
		         </tr>
		         <tr>
		          <td style="border:1px solid #000;border-top:none;">Desoription of contents</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">Qty</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">Weight(kg)</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">Value</td>
		         </tr>
		         <tr>
		          <td style="border:1px solid #000;border-top:none;">
		           '.$allParamArr['productsInfo'][0]['products_declared_en'].'('.$allParamArr['productsInfo'][0]['orders_sku'].'*'.$allParamArr['productsInfo'][0]['item_count'].')
		          </td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">'.$allParamArr['productsInfo'][0]['item_count'].'</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">'.$allParamArr['productsInfo'][0]['products_weight'].'</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">'.$allParamArr['first_declarevalue'].'</td>
		         </tr>
		       </table>
		    </div>
		  </div>
		';
		return $reStr;
	}
	
	/**
	 *打印万邑通面单 
	 */
	public function printWanYiTemplate($allParamArr){

		$reStr = '';
		
		$reStr .= '
				<style>
				*{margin:0; padding:0;}
				#main{width:100mm; height:99mm; margin:0 auto;border:1px solid #000; overflow: hidden;}
				body{font-size: 10px;}
				.f_l{float:left;}
				.f_r{float:right;}
				.address tr th{text-align:left;}
				.address tr td{text-align:right;}
				</style>
				';
		$reStr .='
		    <div id="main">
		       <div style="width:100%;height:70px;border-bottom:1px solid #000;">
		          <p style="width:100%;height:60%;font-size:14px;font-weight:bold;">
		            Track No:'.$allParamArr['ordersInfo']['orders_old_shipping_code'].'
		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		  			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            '.$allParamArr['country_code'].'
		            &nbsp;&nbsp;
		            '.$allParamArr['country_cn'].'
		          </p>
		          <p style="width:100%;height:40%;font-size:14px;text-align:right;">
		           	'.$allParamArr['shipmentTitles'].'
		          </p>
		       </div>
		       <div style="width:100%;height:60px;border-bottom:1px solid #000;text-align:center;font-size:14px;">
		          <div style="width:100%;height:2px;"></div>
		          <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		          <br/> 
		          Intl Tracking No:'.$allParamArr['ordersInfo']['orders_shipping_code'].'
		       </div>
		       <div style="width:100%;height:180px;border-bottom:1px solid #000;">
		         <div style="width:100%;height:120px;overflow:hidden;font-size:12px;">
		         <span style="font-weight:bold;">To:</span>
		           '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
                   '. $allParamArr['ordersInfo']['buyer_address_1'] .' <br/>'. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>
                   '. $allParamArr['ordersInfo']['buyer_city'] .'  '. $allParamArr['ordersInfo']['buyer_state'] .'<br/>'
					. $allParamArr['ordersInfo']['buyer_zip'] .' '.$allParamArr['ordersInfo']['buyer_phone'].'<br/>
					'.$allParamArr['buyerCountry'].'
				 </div>
				 <div style="width:100%;height:60px;overflow:hidden;font-size:12px;">
				  <span style="font-weight:bold;font-size:12px;">From:</span>'.$allParamArr['senderInfo']['from'].'
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				  <span style="font-weight:bold;font-size:12px;">CN:</span>'.$allParamArr['customer_code'][$allParamArr['ordersInfo']['orders_warehouse_id']].'
				  &nbsp;&nbsp;&nbsp;&nbsp;
				  <span style="font-weight:bold;font-size:12px;">渠道:</span>'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'
				  &nbsp;&nbsp;&nbsp;&nbsp;
				  <span style="font-weight:bold;font-size:12px;">Tel:</span>'.$allParamArr['senderInfo']['mobilePhone'].'<br/>
				  <span style="font-weight:bold;font-size:12px;">Add:</span>'.$allParamArr['senderInfo']['back_street'].'
				 </div>
		       </div>
		       <div style="width:100%;height:70px;">
		         <p style="width:50%;height:100%;float:left;text-align:center;font-size:12px;">
		            <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text='.$allParamArr['ordersInfo']['erp_orders_id'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		            <br/> 
		            '.$allParamArr['ordersInfo']['erp_orders_id'].'
		         </p>
		         <p style="width:50%;height:100%;float:left;">
		           '.$allParamArr['productsInfo'][0]['products_declared_cn'].'
		           '.$allParamArr['productsInfo'][0]['products_declared_en'].'
		         </p>
		       </div>
		    </div>
		';
		return $reStr;
	}
		
	/**
	 * 打印递欧德国面单
	 * 100*100
	 */
	public function printDiOuDETemplate($allParamArr){
		$reStr = '';
		
		$reStr .= '
				<style>
				*{margin:0; padding:0;}
				#main{width:100mm; height:99mm; margin:0 auto;border:1px solid; overflow: hidden;}
				body{font-size: 10px;}
				.f_l{float:left;}
				.f_r{float:right;}
				.address tr th{text-align:left;}
				.address tr td{text-align:right;}
				</style>
				';
		
		$reStr .='
		  <div id="main" style="border:none;border-bottom:1px solid #000;">
		    <div style="width:96mm;height:94mm;border:1px solid #000;margin:0 auto;margin-top:8px;border-radius:5px;">
		       <div style="width:100%;height:7px;"></div>
		       <div style="width:100%;height:100px;text-align:right;">
		          <img src="'.site_url('attachments/images/DP_DE.jpeg').'" style="width:250px;height:100px;"/>
		       </div>
		       <div style="width:100%;height:40px;font-size:15px;font-weight:bold;">
			         <p style="width:90%;margin:0 auto;">
			          Packet<br/>
					  STANDARD
					</p>
		       </div>
		  		<div style="width:100%;height:80px;"></div>
		       <div style="width:100%;height:130px;">
		           <p style="width:90%;margin:0 auto;font-size:12px;font-weight:bold;overflow:hidden;">
				       '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
	                   '. $allParamArr['ordersInfo']['buyer_address_1'] .' <br/>'. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>
	                   '. $allParamArr['ordersInfo']['buyer_city'] .'  '. $allParamArr['ordersInfo']['buyer_state'] .'<br/>'
						. $allParamArr['ordersInfo']['buyer_zip'] .' '.$allParamArr['ordersInfo']['buyer_phone'].'<br/>
						Germany
				   </p>
		          
		       </div>
		    </div>
		  </div>
		';
		
		$reStr .='
		  <div id="main" style="border:none;">
		      <div style="width:100%;height:5px;"></div>
		      <div style="width:100%;height:55px;text-align:center;font-size:14px;font-weight:bold;">
		         '.$allParamArr['ordersInfo']['orders_shipping_code'].'<br/>
		         <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=15&r=2&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		      </div>
		    <div style="width:96mm;height:80mm;border:1px solid #000;margin:0 auto;margin-top:2px;border-radius:5px;">
		      <table style="width:100%; border-collapse:collapse; border:medium none;margin:0 auto;" border="1">
		      	 <colgroup>
			       <col width="33%">
			       <col width="33%">
			       <col width="33%">
				 </colgroup>
		        <tr height="35">
		          <td colspan="3">
		            &nbsp;&nbsp;&nbsp;
		            <span style="font-size:12px;font-weight:bold;">
		              CUSTOMS DECLARATION
		               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		               CN22
		            </span>
		            <br/>
		            &nbsp;&nbsp;&nbsp;
		            Postal Administration (May be opened offcially)
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            Important!
		          </td>
		        </tr>
		        <tr height="50">
		          <td colspan="3" style="font-size:13px;">
		            &nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;"></span>
		            Gift
		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;"></span>
		            Sample
		            <br/>
		            &nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;"></span>
		            Printed Matter
		            &nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;">√</span>
		            Others(Tick as appropriate)
		          </td>
		        </tr>
		        <tr height="15">
		          <td colspan="2" style="border-bottom:none;font-size:14px;">
		          &nbsp;&nbsp;&nbsp;
		          Detailed description of Contents
		          </td>
		          <td style="border-bottom:none;text-align:center;font-size:14px;">
		           Value
		          </td>
		        </tr>
		        <tr height="63" style="font-size:14px;font-weight:bold;">
		          <td colspan="2" style="border-top:none;">
		          &nbsp;&nbsp;&nbsp;
		            '.$allParamArr['trInfo'].'
		          </td>
		          <td style="border-top:none;">
		            '.$allParamArr['ordersInfo']['currency_type'].'
		            '.$allParamArr['productsInfo']['total_value'].'
		          </td>
		        </tr>
		        <tr height="35" style="text-align:center;">
		          <td>
		            Origin Country<br/>
		            <span style="font-weight:bold;font-size:12px;">CN</span>
		          </td>
		          <td>
		           TotalWeight(kg)<br/>
		            <span style="font-weight:bold;font-size:12px;">'.$allParamArr['productsInfo']['total_weight'].'</span>
		          </td>
		          <td>
		           Total Value<br/>
		           <span style="font-weight:bold;font-size:12px;">'.$allParamArr['ordersInfo']['currency_type'].' '.$allParamArr['productsInfo']['total_value'].'</span>
		          </td>
		        </tr>
		        <tr height="68">
		          <td colspan="3">
		            <p style="width:98%;margin:0 auto;height:68px;overflow:hidden;font-size:11px;">
			            I,hereby undersigned whose name and address are given on the
						item certify that the particulars given in the declaration are
						correct and that this itm does not contain any dangerous article
						or articles prohibited by legislation or by postal or cunstoms
						regulations.
		            </p>
		          </td>
		        </tr>
		        <tr height="33">
		          <td colspan="2" style="text-align:center;font-size:11px;">
		            Date and Sender\'s Signature<br/>
		            '.date('d/m/Y').'
		          </td>
		          <td style="line-height:30px;font-weight:bold;font-size:12px;text-align:center;">
		          BFE (EY)
		          </td>
		        </tr>
		      </table>
		    </div>
		  </div>
		';
		return $reStr;
	}
	
	/**
	 * 打印递欧意大利面单
	 * 100*100
	 */
	public function printDiOuITTemplate($allParamArr){
		$reStr = '';
		
		$reStr .= '
				<style>
				*{margin:0; padding:0;}
				#main{width:100mm; height:99mm; margin:0 auto;border:1px solid; overflow: hidden;}
				body{font-size: 10px;}
				.f_l{float:left;}
				.f_r{float:right;}
				.address tr th{text-align:left;}
				.address tr td{text-align:right;}
				</style>
				';
		
		$reStr .='
		  <div id="main" style="border:none;border-bottom:1px solid #000;">
		    <div style="width:96mm;height:94mm;border:1px solid #000;margin:0 auto;margin-top:8px;border-radius:5px;">
		       <div style="width:100%;height:7px;"></div>
		       <div style="width:100%;height:100px;text-align:right;">
		          <img src="'.site_url('attachments/images/DP_IT.jpg').'" style="width:250px;height:100px;"/>
		       </div>
		       <div style="width:100%;height:40px;font-size:15px;font-weight:bold;">
			         <p style="width:90%;margin:0 auto;">
			          Packet<br/>
					  STANDARD
					</p>
		       </div>
		  		<div style="width:100%;height:80px;"></div>
		       <div style="width:100%;height:130px;">
		           <p style="width:90%;margin:0 auto;font-size:12px;font-weight:bold;overflow:hidden;">
				       '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
	                   '. $allParamArr['ordersInfo']['buyer_address_1'] .' <br/>'. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>
	                   '. $allParamArr['ordersInfo']['buyer_city'] .'  '. $allParamArr['ordersInfo']['buyer_state'] .'<br/>'
						. $allParamArr['ordersInfo']['buyer_zip'] .' '.$allParamArr['ordersInfo']['buyer_phone'].'<br/>
						Italy
				   </p>
		          
		       </div>
		    </div>
		  </div>
		';
		
		$reStr .='
		  <div id="main" style="border:none;">
		      <div style="width:100%;height:5px;"></div>
		      <div style="width:100%;height:55px;text-align:center;font-size:14px;font-weight:bold;">
		         '.$allParamArr['ordersInfo']['orders_shipping_code'].'<br/>
		         <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=15&r=2&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		      </div>
		    <div style="width:96mm;height:80mm;border:1px solid #000;margin:0 auto;margin-top:2px;border-radius:5px;">
		      <table style="width:100%; border-collapse:collapse; border:medium none;margin:0 auto;" border="1">
		      	 <colgroup>
			       <col width="33%">
			       <col width="33%">
			       <col width="33%">
				 </colgroup>
		        <tr height="35">
		          <td colspan="3">
		            &nbsp;&nbsp;&nbsp;
		            <span style="font-size:12px;font-weight:bold;">
		              CUSTOMS DECLARATION
		               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		               CN22
		            </span>
		            <br/>
		            &nbsp;&nbsp;&nbsp;
		            Postal Administration (May be opened offcially)
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            Important!
		          </td>
		        </tr>
		        <tr height="50">
		          <td colspan="3" style="font-size:13px;">
		            &nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;"></span>
		            Gift
		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;"></span>
		            Sample
		            <br/>
		            &nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;"></span>
		            Printed Matter
		            &nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;">√</span>
		            Others(Tick as appropriate)
		          </td>
		        </tr>
		        <tr height="15">
		          <td colspan="2" style="border-bottom:none;font-size:14px;">
		          &nbsp;&nbsp;&nbsp;
		          Detailed description of Contents
		          </td>
		          <td style="border-bottom:none;text-align:center;font-size:14px;">
		           Value
		          </td>
		        </tr>
		        <tr height="63" style="font-size:14px;font-weight:bold;">
		          <td colspan="2" style="border-top:none;">
		          &nbsp;&nbsp;&nbsp;
		            '.$allParamArr['trInfo'].'
		          </td>
		          <td style="border-top:none;">
		            '.$allParamArr['ordersInfo']['currency_type'].'
		            '.$allParamArr['productsInfo']['total_value'].'
		          </td>
		        </tr>
		        <tr height="35" style="text-align:center;">
		          <td>
		            Origin Country<br/>
		            <span style="font-weight:bold;font-size:12px;">CN</span>
		          </td>
		          <td>
		           TotalWeight(kg)<br/>
		            <span style="font-weight:bold;font-size:12px;">'.$allParamArr['productsInfo']['total_weight'].'</span>
		          </td>
		          <td>
		           Total Value<br/>
		           <span style="font-weight:bold;font-size:12px;">'.$allParamArr['ordersInfo']['currency_type'].' '.$allParamArr['productsInfo']['total_value'].'</span>
		          </td>
		        </tr>
		        <tr height="68">
		          <td colspan="3">
		            <p style="width:98%;margin:0 auto;height:68px;overflow:hidden;font-size:11px;">
			            I,hereby undersigned whose name and address are given on the
						item certify that the particulars given in the declaration are
						correct and that this itm does not contain any dangerous article
						or articles prohibited by legislation or by postal or cunstoms
						regulations.
		            </p>
		          </td>
		        </tr>
		        <tr height="33">
		          <td colspan="2" style="text-align:center;font-size:11px;">
		            Date and Sender\'s Signature<br/>
		            '.date('d/m/Y').'
		          </td>
		          <td style="line-height:30px;font-weight:bold;font-size:12px;text-align:center;">
		          BFE (EY)
		          </td>
		        </tr>
		      </table>
		    </div>
		  </div>
		';
		return $reStr;
	}
	
	/**
	 * 打印递欧日本面单
	 * 100*100
	 */
	public function printDiOuJPTemplate($allParamArr){
		$reStr = '';
		
		$reStr .= '
				<style>
				*{margin:0; padding:0;}
				#main{width:100mm; height:99mm; margin:0 auto;border:1px solid; overflow: hidden;}
				body{font-size: 10px;}
				.f_l{float:left;}
				.f_r{float:right;}
				.address tr th{text-align:left;}
				.address tr td{text-align:right;}
				</style>
				';
		
		$reStr .='
		  <div id="main" style="border:none;border-bottom:1px solid #000;">
		    <div style="width:96mm;height:94mm;border:1px solid #000;margin:0 auto;margin-top:8px;border-radius:5px;">
		       <div style="width:100%;height:7px;"></div>
		       <div style="width:100%;height:100px;text-align:right;">
		          <img src="'.site_url('attachments/images/DP_JP.jpg').'" style="width:250px;height:100px;"/>
		       </div>
		       <div style="width:100%;height:40px;font-size:15px;font-weight:bold;">
			         <p style="width:90%;margin:0 auto;">
			          Packet<br/>
					  STANDARD
					</p>
		       </div>
		  		<div style="width:100%;height:80px;"></div>
		       <div style="width:100%;height:130px;">
		           <p style="width:90%;margin:0 auto;font-size:12px;font-weight:bold;overflow:hidden;">
				       '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
	                   '. $allParamArr['ordersInfo']['buyer_address_1'] .' <br/>'. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>
	                   '. $allParamArr['ordersInfo']['buyer_city'] .'  '. $allParamArr['ordersInfo']['buyer_state'] .'<br/>'
						. $allParamArr['ordersInfo']['buyer_zip'] .' '.$allParamArr['ordersInfo']['buyer_phone'].'<br/>
						Japan
				   </p>
		          
		       </div>
		    </div>
		  </div>
		';
		
		$reStr .='
		  <div id="main" style="border:none;">
		      <div style="width:100%;height:5px;"></div>
		      <div style="width:100%;height:55px;text-align:center;font-size:14px;font-weight:bold;">
		         '.$allParamArr['ordersInfo']['orders_shipping_code'].'<br/>
		         <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=15&r=2&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		      </div>
		    <div style="width:96mm;height:80mm;border:1px solid #000;margin:0 auto;margin-top:2px;border-radius:5px;">
		      <table style="width:100%; border-collapse:collapse; border:medium none;margin:0 auto;" border="1">
		      	 <colgroup>
			       <col width="33%">
			       <col width="33%">
			       <col width="33%">
				 </colgroup>
		        <tr height="35">
		          <td colspan="3">
		            &nbsp;&nbsp;&nbsp;
		            <span style="font-size:12px;font-weight:bold;">
		              CUSTOMS DECLARATION
		               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		               CN22
		            </span>
		            <br/>
		            &nbsp;&nbsp;&nbsp;
		            Postal Administration (May be opened offcially)
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            Important!
		          </td>
		        </tr>
		        <tr height="50">
		          <td colspan="3" style="font-size:13px;">
		            &nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;"></span>
		            Gift
		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;"></span>
		            Sample
		            <br/>
		            &nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;"></span>
		            Printed Matter
		            &nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;">√</span>
		            Others(Tick as appropriate)
		          </td>
		        </tr>
		        <tr height="15">
		          <td colspan="2" style="border-bottom:none;font-size:14px;">
		          &nbsp;&nbsp;&nbsp;
		          Detailed description of Contents
		          </td>
		          <td style="border-bottom:none;text-align:center;font-size:14px;">
		           Value
		          </td>
		        </tr>
		        <tr height="63" style="font-size:14px;font-weight:bold;">
		          <td colspan="2" style="border-top:none;">
		          &nbsp;&nbsp;&nbsp;
		            '.$allParamArr['trInfo'].'
		          </td>
		          <td style="border-top:none;">
		            '.$allParamArr['ordersInfo']['currency_type'].'
		            '.$allParamArr['productsInfo']['total_value'].'
		          </td>
		        </tr>
		        <tr height="35" style="text-align:center;">
		          <td>
		            Origin Country<br/>
		            <span style="font-weight:bold;font-size:12px;">CN</span>
		          </td>
		          <td>
		           TotalWeight(kg)<br/>
		            <span style="font-weight:bold;font-size:12px;">'.$allParamArr['productsInfo']['total_weight'].'</span>
		          </td>
		          <td>
		           Total Value<br/>
		           <span style="font-weight:bold;font-size:12px;">'.$allParamArr['ordersInfo']['currency_type'].' '.$allParamArr['productsInfo']['total_value'].'</span>
		          </td>
		        </tr>
		        <tr height="68">
		          <td colspan="3">
		            <p style="width:98%;margin:0 auto;height:68px;overflow:hidden;font-size:11px;">
			            I,hereby undersigned whose name and address are given on the
						item certify that the particulars given in the declaration are
						correct and that this itm does not contain any dangerous article
						or articles prohibited by legislation or by postal or cunstoms
						regulations.
		            </p>
		          </td>
		        </tr>
		        <tr height="33">
		          <td colspan="2" style="text-align:center;font-size:11px;">
		            Date and Sender\'s Signature<br/>
		            '.date('d/m/Y').'
		          </td>
		          <td style="line-height:30px;font-weight:bold;font-size:12px;text-align:center;">
		          BFE (EY)
		          </td>
		        </tr>
		      </table>
		    </div>
		  </div>
		';
		return $reStr;
	}
	
	/**
	 * 打印递欧其他面单
	 * 100*100
	 */
	public function printDiOuOtherTemplate($allParamArr){
		$reStr = '';
		
		$reStr .= '
				<style>
				*{margin:0; padding:0;}
				#main{width:100mm; height:99mm; margin:0 auto;border:1px solid; overflow: hidden;}
				body{font-size: 10px;}
				.f_l{float:left;}
				.f_r{float:right;}
				.address tr th{text-align:left;}
				.address tr td{text-align:right;}
				</style>
				';
		
		$reStr .='
		  <div id="main" style="border:none;border-bottom:1px solid #000;">
		    <div style="width:96mm;height:94mm;border:1px solid #000;margin:0 auto;margin-top:8px;border-radius:5px;">
		       <div style="width:100%;height:7px;"></div>
		       <div style="width:100%;height:100px;text-align:right;">
		          <img src="'.site_url('attachments/images/DP_Other.jpg').'" style="width:250px;height:100px;"/>
		       </div>
		       <div style="width:100%;height:40px;font-size:15px;font-weight:bold;">
			         <p style="width:90%;margin:0 auto;">
			          Packet<br/>
					  STANDARD
					</p>
		       </div>
		  		<div style="width:100%;height:80px;"></div>
		       <div style="width:100%;height:130px;">
		           <p style="width:90%;margin:0 auto;font-size:12px;font-weight:bold;overflow:hidden;">
				       '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
	                   '. $allParamArr['ordersInfo']['buyer_address_1'] .' <br/>'. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>
	                   '. $allParamArr['ordersInfo']['buyer_city'] .'  '. $allParamArr['ordersInfo']['buyer_state'] .'<br/>'
						. $allParamArr['ordersInfo']['buyer_zip'] .' '.$allParamArr['ordersInfo']['buyer_phone'].'<br/>
						France
				   </p>
		          
		       </div>
		    </div>
		  </div>
		';
		
		$reStr .='
		  <div id="main" style="border:none;">
		      <div style="width:100%;height:5px;"></div>
		      <div style="width:100%;height:55px;text-align:center;font-size:14px;font-weight:bold;">
		         '.$allParamArr['ordersInfo']['orders_shipping_code'].'<br/>
		         <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=15&r=2&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		      </div>
		    <div style="width:96mm;height:80mm;border:1px solid #000;margin:0 auto;margin-top:2px;border-radius:5px;">
		      <table style="width:100%; border-collapse:collapse; border:medium none;margin:0 auto;" border="1">
		      	 <colgroup>
			       <col width="33%">
			       <col width="33%">
			       <col width="33%">
				 </colgroup>
		        <tr height="35">
		          <td colspan="3">
		            &nbsp;&nbsp;&nbsp;
		            <span style="font-size:12px;font-weight:bold;">
		              CUSTOMS DECLARATION
		               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		               CN22
		            </span>
		            <br/>
		            &nbsp;&nbsp;&nbsp;
		            Postal Administration (May be opened offcially)
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            Important!
		          </td>
		        </tr>
		        <tr height="50">
		          <td colspan="3" style="font-size:13px;">
		            &nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;"></span>
		            Gift
		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;"></span>
		            Sample
		            <br/>
		            &nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;"></span>
		            Printed Matter
		            &nbsp;&nbsp;&nbsp;
		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;">√</span>
		            Others(Tick as appropriate)
		          </td>
		        </tr>
		        <tr height="15">
		          <td colspan="2" style="border-bottom:none;font-size:14px;">
		          &nbsp;&nbsp;&nbsp;
		          Detailed description of Contents
		          </td>
		          <td style="border-bottom:none;text-align:center;font-size:14px;">
		           Value
		          </td>
		        </tr>
		        <tr height="63" style="font-size:14px;font-weight:bold;">
		          <td colspan="2" style="border-top:none;">
		          &nbsp;&nbsp;&nbsp;
		            '.$allParamArr['trInfo'].'
		          </td>
		          <td style="border-top:none;">
		            '.$allParamArr['ordersInfo']['currency_type'].'
		            '.$allParamArr['productsInfo']['total_value'].'
		          </td>
		        </tr>
		        <tr height="35" style="text-align:center;">
		          <td>
		            Origin Country<br/>
		            <span style="font-weight:bold;font-size:12px;">CN</span>
		          </td>
		          <td>
		           TotalWeight(kg)<br/>
		            <span style="font-weight:bold;font-size:12px;">'.$allParamArr['productsInfo']['total_weight'].'</span>
		          </td>
		          <td>
		           Total Value<br/>
		           <span style="font-weight:bold;font-size:12px;">'.$allParamArr['ordersInfo']['currency_type'].' '.$allParamArr['productsInfo']['total_value'].'</span>
		          </td>
		        </tr>
		        <tr height="68">
		          <td colspan="3">
		            <p style="width:98%;margin:0 auto;height:68px;overflow:hidden;font-size:11px;">
			            I,hereby undersigned whose name and address are given on the
						item certify that the particulars given in the declaration are
						correct and that this itm does not contain any dangerous article
						or articles prohibited by legislation or by postal or cunstoms
						regulations.
		            </p>
		          </td>
		        </tr>
		        <tr height="33">
		          <td colspan="2" style="text-align:center;font-size:11px;">
		            Date and Sender\'s Signature<br/>
		            '.date('d/m/Y').'
		          </td>
		          <td style="line-height:30px;font-weight:bold;font-size:12px;text-align:center;">
		          BFE (EY)
		          </td>
		        </tr>
		      </table>
		    </div>
		  </div>
		';
		return $reStr;
	}
	
	/**
	 * 顺友挂号面单，开始启用100*100的面单
	 * 挂号有R标识，并且物品信息位置不一致
	 */
	public function printshunYouGuaHaoTemplate($allParamArr){
		
		$reStr = '';
		
		$reStr .= '
				<style>
				*{margin:0; padding:0;}
				#main{width:100mm; height:99mm; margin:0 auto;border:1px solid; overflow: hidden;}
				body{font-size: 10px;}
				.f_l{float:left;}
				.f_r{float:right;}
				.address tr th{text-align:left;}
				.address tr td{text-align:right;}
				</style>
				';
		$reStr .='
		  <div id="main">
		    <div style="width:100%;height:1mm;"></div>
		    <div style="width:100%;height:42mm;">
		      <p style="width:68mm;height:42mm;float:left;margin-left:5px;overflow:hidden;font-size:7px;">
		        If underliverable return to : <br/>	       
                Locked bag No      <br/>	                    
                Special Project Unit    <br/>                   
                POS MALAYSIA INTERATIONAL HUB <br/>   
                64000 MALAYSIA<br/>
                
                <span style="font-size:11px;font-weight:bold;">
                To:'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
                   '. $allParamArr['ordersInfo']['buyer_address_1'] .' '. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>
                   '. $allParamArr['ordersInfo']['buyer_city'] .','. $allParamArr['ordersInfo']['buyer_state'] .'<br/>'
					. $allParamArr['ordersInfo']['buyer_zip'] .' '.$allParamArr['ordersInfo']['buyer_phone'].'<br/>
                   '. $allParamArr['countryInfo']['display_name'] . '(' . $allParamArr['countryInfo']['country_cn'] .')' .'
                </span>
		      </p>
		      <p style="width:30mm;height:42mm;float:left;overflow:hidden;">
		        <span style="width:98%;height:24mm;border:1px solid #000;display:inline-block;">
		        	BAYARAN POS JELAS<br/>
					POSTAGE PAID<br/>
					POS MALAYSIA<br/>
					INTERNATIONAL HUB<br/>
					MALAYSIA<br/>
					PMK1348
		        </span>
		        <span style="width:98%;height:8mm;border:1px solid #000;display:inline-block;border-top:none;font-size:14px;font-weight:bold;line-height:8mm;">
		         &nbsp;Z&nbsp;:&nbsp;'.$allParamArr['areaCode'].'
		        </span>
		        <span style="width:98%;height:8mm;line-height:8mm;border:1px solid #000;display:inline-block;border-top:none;">
		        '.$allParamArr['ordersInfo']['erp_orders_id'].'<span style="font-size:14px;font-weight:bold;">【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</span>
		        </span>
		      </p>
		    </div>
		    <div style="width:100%;height:12mm;">
		      <p style="width:30%;height:12mm;float:left;font-weight:bold;font-size:30px;text-align:right;">
		         R
		      </p>
		      <p style="width:45%;height:12mm;float:left;text-align:center;">
		        <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=30&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		        <br/><span style="font-size:12px;">'.$allParamArr['ordersInfo']['orders_shipping_code'].'</span>
		      </p>
		    </div>
		    <div style="width:100%;height:44mm;overflow:hidden;">
		       <table style="width:98%; border-collapse:collapse; border:medium none;margin:0 auto;" >
		         <colgroup>
			       <col width="42%">
			       <col width="18%">
			       <col width="20%">
			       <col width="20%">
				 </colgroup>
		         <tr>
		          <td style="border:1px solid #000;" colspan="2">CUSTOMS DECLARATION CN 23</td>
		          <td style="border:1px solid #000;border-left:none;" colspan="2">May be opened officially</td>
		         </tr>
		         <tr>
		          <td style="border:1px solid #000;border-top:none;">Desoription of contents</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">Qty</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">Weight(kg)</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">Value</td>
		         </tr>
		         <tr>
		          <td style="border:1px solid #000;border-top:none;">
		           '.$allParamArr['productsInfo'][0]['products_declared_en'].'('.$allParamArr['productsInfo'][0]['orders_sku'].'*'.$allParamArr['productsInfo'][0]['item_count'].')
		          </td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">'.$allParamArr['productsInfo'][0]['item_count'].'</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">'.$allParamArr['productsInfo'][0]['products_weight'].'</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">'.$allParamArr['first_declarevalue'].'</td>
		         </tr>
		         <tr>
		          <td style="border:1px solid #000;border-top:none;" colspan="2">For commercial items only</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">Total Wt</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">Total Value</td>
		         </tr>
		         <tr>
		          <td style="border:1px solid #000;border-top:none;" colspan="2">Country of origin: CN</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">'.$allParamArr['total_weight'].'</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">$'.$allParamArr['total_price'].'</td>
		         </tr>
		         <tr>
		          <td style="border:1px solid #000;border-top:none;" rowspan="2">
		            <span style="width:7px;height:7px;border:1px solid #000;display:inline-block;font-size:8px;font-weight:bold;text-indent:-999px;">√</span>&nbsp;Gift&nbsp;
		            <span style="width:7px;height:7px;border:1px solid #000;display:inline-block;font-size:8px;font-weight:bold;text-indent:-999px;">√</span>&nbsp;Commercial sample<br/>
		            <span style="width:7px;height:7px;border:1px solid #000;display:inline-block;font-size:8px;font-weight:bold;">√</span>&nbsp;Other
		            <span style="width:7px;height:7px;border:1px solid #000;display:inline-block;font-size:8px;font-weight:bold;text-indent:-999px;">√</span> &nbsp;Documents<br/>
		          </td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;" rowspan="2">HS Tariff No:</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;" colspan="2">Certificate No:</td>
		         </tr>
		         <tr>
		          <td style="border:1px solid #000;border-left:none;border-top:none;" colspan="2">Invoice No:</td>
		         </tr>
		         <tr style="font-size:8px;">
		          <td style="border:1px solid #000;border-top:none;">Importer`s ref(taxcode/VAT no)if any:</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;">Postage Fees:</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;" colspan="2">Office of origin/Date of posting:</td>
		         </tr>
		         <tr style="font-size:7px;">
		          <td style="border:1px solid #000;border-top:none;" colspan="4">
		           I certify that the particulars given in this customs declaration are correct and that this item doesnot
				   contain any dangerous article prohibited by legislation or by postal or customs regulations.
		          </td>
		         </tr>
		         <tr>
		          <td style="border:1px solid #000;border-top:none;" colspan="2">Signature of sender:'.$allParamArr['sign'].'</td>
		          <td style="border:1px solid #000;border-left:none;border-top:none;" colspan="2">Date:'.date('Y-m-d H:i:s').'</td>
		         </tr>
		         
		       </table>
		    </div>
		  </div>
		';
		return $reStr;
	}
	
	//顺友面单模板(挂号和平邮共用一个模板，两者不同在于挂号多了一个R)
	public function printshunYouTemplate($allParamArr){
		$reStr = '';
		
		$reStr .= '
				<style>
				*{margin:0; padding:0;}
				#main{width:100mm; height:129mm; margin:auto;}
				#main_border{width:99mm; height:128mm; margin: 2px auto 0; border:1px solid; overflow: hidden;}
				body{font-size: 10px;}
				.f_l{float:left;}
				.f_r{float:right;}
				.address tr th{text-align:left;}
				.address tr td{text-align:right;}
				</style>
				';
		
		$reStr .= '
				<div id="main">
					<div id="main_border">
						<div style="width:98%; margin:auto;">
							<div class="f_l" style="width:145px; font-size: 8px;">
							    If underliverable return to : <br/>	       
				                Locked bag No      <br/>	                    
				                Special Project Unit    <br/>                   
				                POS MALAYSIA INTERATIONAL HUB <br/>   
				                64000 MALAYSIA
							</div>
					
							<div class="f_r" style="width:133px; border:1px solid #000; text-align:center; line-height:10px;">
								<p>BAYARAN POS JELAS</p>
								<p>POSTAGE PAID</p>
								<p>POS MALAYSIA</p>
								<p>INTERNATIONAL HUB</p>
								<p>MALAYSIA</p>
								<p>PMK1348</p>
							</div>
				
							<div style="clear: both;"></div>
						</div>
				
						<div style="width:100%; border-top:1px solid; border-bottom:1px solid; line-height:10px;">
							<table style="width:100%; margin:auto;" class="address">
								 <tr>
								  <td align="left">SHIP TO:</th>
								  <th align="right">'.$allParamArr['ordersInfo']['buyer_name'].'</th>
								 </tr>
					
								<tr>
								  <td align="left"></th>
								  <th align="right">'. $allParamArr['ordersInfo']['buyer_address_1'] .' '. $allParamArr['ordersInfo']['buyer_address_2'] .'</th>
								 </tr>
								  		
							 	<tr>
								  <td align="left"></th>
								  <th align="right">'. $allParamArr['ordersInfo']['buyer_city'] .','. $allParamArr['ordersInfo']['buyer_state'] .','. $allParamArr['ordersInfo']['buyer_zip'] .'</th>
								 </tr>
									
								<tr>
								  <td align="left"></th>
								  <th align="right">'. $allParamArr['countryInfo']['display_name'] . '(' . $allParamArr['countryInfo']['country_en'] .')' .'</th>
								 </tr>
								  		
						  		<tr>
								  <td align="left">Tel:</th>
								  <th align="right">'. $allParamArr['ordersInfo']['buyer_phone'] .'</th>
								</tr>
							</table>
						</div>
								  		
						<div>
							<div class="f_l" style="width:170px;height:50px;font-size:26px;font-weight:bold;text-align:right;line-height:50px;margin-right:5px;">
							'.$allParamArr['is_flag'].'
							</div>
								  		
							<div class="f_l" style="text-align:center;">
								<p style="line-height:12px;"><b>MALAYSIA POST Airmail</b></p>
								 <div>
								 	<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
					  			</div>
								 <p style="line-height:12px;"><b>'. $allParamArr['ordersInfo']['orders_shipping_code'] .'</b></p>
							</div>
								 			
							<div style="clear: both;"></div>
						</div>
								 		
						<div style="border-top:1px solid; border-bottom:1px solid;">
							<div class="f_l">
								CUSTOMS DECLARATION
					 		</div>
								 		
					 		<div class="f_l" style="width:166px; text-align:center;">
								May be opened officially
					 		</div>
								 		
					 		<div class="f_l">
								<b>CN 22</b>
					 		</div>
								 		
							<div style="clear: both;"></div>
						</div>
								 		
						<div style="padding:3px; border-bottom:1px solid;">
							<div class="f_l">
								Postal administration
					 		</div>
								 		
					 		<div class="f_r">
								Tick as appropriat
					 		</div>
								 		
							<div style="clear: both;"></div>
						</div>
								 		
						<div style="border-bottom:1px solid;">
							<div class="f_l" style="padding:5px 10px; border-right:1px solid;">
							 	<input name="wx" type="checkbox">
					 		</div>
								 		
					 		<div class="f_l" style="padding:4px; width:100px;">
								Gift
					 		</div>
								 		
					 		<div class="f_l" style="padding:5px 10px; border-right:1px solid; border-left:1px solid;">
							 	<input name="wx" type="checkbox">
					 		</div>
								 		
							<div class="f_l" style="padding:4px;">
								Commercial sample
					 		</div>
								 		
							<div style="clear: both;"></div>
						</div>
								 		
						<div style="border-bottom:1px solid #000;">
							<div class="f_l" style="padding:5px 10px; border-right:1px solid;">
							 	<input name="wx" type="checkbox">
					 		</div>
								 		
					 		<div class="f_l" style="padding:4px; width:100px;">
								Document
					 		</div>
								 		
					 		<div class="f_l" style="padding:5px 10px; border-right:1px solid; border-left:1px solid;">
							 	<input name="wx" type="checkbox" checked>
					 		</div>
								 		
							<div class="f_l" style="padding:4px;">
								Other
					 		</div>
								 		
							<div style="clear: both;"></div>
						</div>
								 		
						<table style="width:100%; border-collapse:collapse; border:medium none;" >
					 		<colgroup>
						       <col width="46%">
						       <col width="26%">
						       <col width="26%">
						    </colgroup>
							<tr>
								<td style="border:1px solid #000;border-top:none;">Quantity and detailed description of<br> contents</td>
						 		<td style="border:1px solid #000;border-top:none;">Weight (in kg)</td>
						 		<td style="border:1px solid #000;border-top:none;">Value</td>
							</tr>
								 		
							<tr>
								<th style="border:1px solid #000;text-align:left;">'. $allParamArr['productsInfo'][0]['products_declared_en'] . ' x ' . $allParamArr['productsInfo'][0]['item_count'] .'</th>
						 		<th style="border:1px solid #000;">'. $allParamArr['total_weight'] .'</th>
						 		<th style="border:1px solid #000;">'. $allParamArr['ordersInfo']['orders_total'] . 'USD' .'</th>
							</tr>
								 		
							<tr>
								<td style="border:1px solid #000;border-top:none;"></td>
						 		<td style="border:1px solid #000;border-top:none;">Total Weight (in kg)</td>
						 		<td style="border:1px solid #000;border-top:none;">Total Value(USD)</td>
							</tr>
								 		
							<tr>
								<th style="border:1px solid #000;border-top:none;"></th>
						 		<th style="border:1px solid #000;border-top:none;">'. $allParamArr['total_weight'] .'</th>
						 		<th style="border:1px solid #000;border-top:none;">'. $allParamArr['ordersInfo']['orders_total'] .'</th>
							</tr>
						</table>
						 				
						 <div style="padding:3px;">
						 	<div style="font-size: 8px;">
						 		I,the undersigned,whose name and address are given on the itme, certify that the particulars given in this declaration are correct and that this item does not contain any dangerous article or articles pro-hibited by legislation or by postal or by customs regulations 
						 	</div>
						 				
					 		<div style="text-align:right; width: 97%;">
					 		   <span style="font-size:15px;font-weight:bold;">【'. $allParamArr['ordersInfo']['shipmentAutoMatched'] .'】</span>  
					 			<b>SLME</b> '. date('Y-m-d') .'
					 		</div>
						 </div>
					 					
					 	<div style="padding:3px 6px; border-top:1px solid;">
					 		<div class="f_l">
					 			<div>
				 					<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['erp_orders_id'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
					 			</div>
				 				<p style="text-align:right;">' .$allParamArr['ordersInfo']['erp_orders_id']. '</p>
		 					</div>
				 						
				 			<div class="f_r" style="width:250px;">
				 				'. $allParamArr['productsInfo']['sku'] .'
				 			</div>
				 						
				 			<div style="clear: both;"></div>
					 	</div>
					</div>
				</div>
				';
		
		return $reStr;
		
	}
	
	//打印燕文瑞士面单
	public function yanWenCH_template ($allParamArr)
	{
		$reStr = '
			<style>
			    *{margin:0;padding:0;}
				body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
				#main_frame_box{width:95mm;height:99mm;margin:0px auto; overflow:hidden;}
				.bor{border-top:1px solid; border-bottom: 1px solid;}
				table{border-collapse:collapse;border:none; width: 100%;}
				.bor_l{border-left: none;}
				.bor_r{border-right: none;}
				td{font-size: 12px;}
				.input_bor {border: 2px solid; display: inline-block; width: 10px; height: 10px;}
			</style>
				';
		
		$reStr .= '
				<div id="main_frame_box">
					<div>
						<div style="float: left;">
							<img src="'.site_url('attachments').'/images/yanWenCH_logo.png"/>
						</div>
				
						<div style="width: 40mm; float: right;">
							<div style="font-size: 30px; line-height: 13mm; text-align:center;">
								CN22
							</div>		
						</div>
				
						<div style="clear:both;"></div>
					</div>
				
					<div>
						<div style="float: left;">
							Postal administration
						</div>
				
						<div style="float: right;">
							CUSTOMS DECLARATION
						</div>
				
						<div style="clear:both;"></div>
					</div>
									
					<div style="font-size: 12px; float: right;">
						(May be opened officially)
					</div>
					<div style="clear:both;"></div>
												
					<div style="font-size: 12px; float: left;">
						(Please check on appropriate option)
					</div>
					<div style="clear:both;"></div>
									
					<table border="1">
						<colgroup>
					       <col width="50%">
					       <col width="25%">
					       <col width="25%">
					    </colgroup>
						<tr>
							<td colspan="3" class="bor_l bor_r">
								<table>
									<tr>
										<td>
											<span class="input_bor">
												
											</span>
											Gift
										</td>
										<td>
											<span class="input_bor">
												
											</span>
											Commercial sample
										</td>
									</tr>
									
									<tr>
										<td>
											<span class="input_bor">
												
											</span>
											Documents
										</td>
										<td>
											<span class="input_bor">
												√
											</span>
											Other
										</td>
									</tr>
								</table>
							</td>
						</tr>
									
						<tr>
							<td class="bor_l">
								(1)Quantity and detailed description
								of contents
							</td>
							<td>
								(2)Weight
								(kg)
							</td>
							<td class="bor_r">
								(3)Value
							</td>
						</tr>
									
						<tr>
							<td class="bor_l">
								'.$allParamArr['productsInfo'][0]['products_declared_en'].'
								#'.$allParamArr['productsInfo'][0]['orders_sku'].' * '.$allParamArr['productsInfo'][0]['item_count'].'
							</td>
							<td>
								'.$allParamArr['productsInfo'][0]['products_weight'].'
							</td>
							<td class="bor_r">
								'.$allParamArr['total_price'].'USD
							</td>
						</tr>
									
						<tr>
							<td class="bor_l">
								For commercial items only
								If known,HS tariff number (4) and
								country of origin of goods (5)
							</td>
							<td>
								(6)Total Weight
								(kg)
							</td>
							<td class="bor_r">
								(7)Total Value
							</td>
						</tr>
									
						<tr>
							<td colspan="3" class="bor_l bor_r">
								<div style="font-size: 10px;">
									I,the undersigned,whose name and address are given on the itme, certify
									that particulars given in this declaration are correct and that this item dose
									not contain any dangerous article or artices prohibited by legislation or by
									postal or customs regulations
								</div>
								<div style="float: right;">
									<div style="float: left; margin: 12px 8mm 0 0; font-size: 14px;">
										82-AP-'.$allParamArr['partition'].' Zone
									</div>
									
									<div style="float: left; font-size: 28px; margin-right: 6px;">
										D
									</div>
									<div style="clear:both;"></div>
								</div>
								<div style="clear:both;"></div>
									
								<div>
									(8)Date and sender\'s signature
									&nbsp
									YANWEN
									&nbsp
									'.date('d-m-Y').'
									&nbsp
									302035
								</div>
							</td>
						</tr>
					</table>
				</div>
				';
		
		$reStr .= '
			<div id="main_frame_box">
				<div>
					<div style="float: left;">
						<img src="'.site_url('attachments').'/images/yanWenCH_logo.png"/>
					</div>
			
					<div style="float: right; width: 48mm;">
						<table border="1">
							<tr>
								<td colspan="2" style="font-size: 20px; font-weight: bold; text-align: center;">ECONOMY</td>
							</tr>
								
							<tr>
								<td style="font-size: 9px; font-weight: bold; padding-left: 2px;">
									<p>If undeliverable, please</p>
									<p>return to:</p>
									<p>Exchange Office</p>
									<p>SPI HKG 00006705</p>
									<p>8010 Zurich-Mulligen</p>
									<p>Switzerland</p>
								</td>

								<td style="font-size: 9px; font-weight: bold; text-align: center;">
									<p style="font-size: 20px;">P.P.</p>
									<p>Swiss Post</p>
									<p>CH-8010 Zurich</p>
									<p>Mulligen</p>
								</td>
							</tr>
						</table>
					</div>
					<div style="clear:both;"></div>
				</div>
								
				<div style="width: 100%;">
					<p>To:</p>
					<p>'.$allParamArr['ordersInfo']['buyer_name'].' Tel:'.$allParamArr['ordersInfo']['buyer_phone'].'</p>
					<p>'.$allParamArr['ordersInfo']['buyer_address_1']." ".$allParamArr['ordersInfo']['buyer_address_2'].'</p>
					<p>'.$allParamArr['ordersInfo']['buyer_city']." ".$allParamArr['ordersInfo']['buyer_state']." ".$allParamArr['ordersInfo']['buyer_zip'].'</p>
					<p>'.$allParamArr['country'].'</p>
								<br>
					<p>OrderNo:'.$allParamArr['ordersInfo']['erp_orders_id'].'</p>
								<br>
				</div>
					
				<div style="border-bottom: 1px solid;">
					<span>Ref:</span>
					<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
					<p style="text-align: center;">'.$allParamArr['ordersInfo']['orders_shipping_code'].'</p>
				</div>
							
				<div>
					'.$allParamArr['sku_string'].'	
				</div>
			</div>
				';
		
		return $reStr;
	}
	
	//广州挂号小包面单模板处理
	public function printForNewPostXiaobaotTemplate($allParamArr){
		$reStr = '
			<style>
			    *{margin:0;padding:0;}
				body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
				#main_frame_box{width:99mm;height:99mm;margin:0 auto;overflow:hidden;}
				table{border-collapse:collapse;border:none;width:99mm;height:90mm;}
				table .detail{width:280px;height:100px;border:none;}
				table .right{width:93px;height:100px;border:none;}
				td{border:1px solid #000;}
			</style>
		';
		$reStr .= '
		  <div id="main_frame_box">
		    <table >
		      <tr height="34px">
		        <td colspan="2">
		          <p style="width:33mm;height:8mm;float:left;">
		            <img src="'.site_url('attachments').'/images/post_logo.jpg" style="width:33mm;height:8mm;"/>
		          </p>
		          <p style="width:33mm;height:8mm;float:left;font-size:8px;text-align:center;font-weight:bold;">
		          	航空<br/>
		          	Small packet<br/>
		          	BY AIR
		          </p>
		          <p style="width:32mm;height:8mm;float:left;text-align:center;font-weight:bold;line-height:8mm;">
		            '.$allParamArr['country_cn'].'
		          </p>
		        </td>
		      </tr>
		      <tr height="16px">
		        <td colspan="2">
		          <p style="padding-left:5px;font-size:11px;">协议客户:44010100176000</p>
		        </td>
		      </tr>
		      <tr height="11px">
		        <td colspan="2">
		          <p style="height:11px;font-size:11px;padding-left:10px;overflow:hidden;">From：'.$allParamArr['senderInfo']['sender'].' '.$allParamArr['senderInfo']['address'].'</p>
		        </td>
		      </tr>
		      <tr height="68px">
		        <td colspan="2">
		          <p style="padding-left:10px;font-size:11px;height:67px;overflow:hidden;">
		            TO:'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
			        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
			        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_city'].' '.$allParamArr['ordersInfo']['buyer_state'].'<br/>
			        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['buyerCountry'].'('.$allParamArr['country_cn'].')
			        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			        ZIP:'.$allParamArr['ordersInfo']['buyer_zip'].'
			         &nbsp;&nbsp;&nbsp;&nbsp;
			        TEL:'.$allParamArr['ordersInfo']['buyer_phone'].'

		           </p>
		        </td>
		      </tr>
		      <tr height="10px">
		        <td colspan="2">
		          <p style="padding-left:10px;font-weight:bold;font-size:10px;">
		           	退件单位：广州邮政国际小包处理中心  510000
		          </p>
		        </td>
		      </tr>
		      <tr height="40px">
		        <td colspan="2">
		          <p style="float:left;width:20mm;height:40px;font-weight:bold;font-size:24px;line-height:30px;text-align:right;">
		          	<span style="font-size:25px;">R</span> 
		          <p>
		          <p style="float:left;width:47mm;height:40px;text-align:center;font-size:12px;font-weight:bold;">
		            <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=28&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" /><br/>
		          	'.$allParamArr['ordersInfo']['orders_shipping_code'].'
		          </p>
		          <p style="float:left;width:25mm;height:40px;font-weight:bold;font-size:24px;text-align:right;line-height:35px;">
		      		'.$allParamArr['gekou'].'
		          </p>
		        </td>
		      </tr>
		      <tr height="120px">
		        <td width="75%" style="border-top:none;">
		          <table class="detail">
		            <tr height="14px">
		              <td colspan="3">
		                <p style="display:inline-block;width:100px;float:left;font-size:10px;font-weight:bold;">
		                	<span style="font-size:7px;">CUSTOMS DECLARATION</span>
		                </p>
		                <p style="display:inline-block;width:78px;float:left;font-size:10px;">
		                	<span style="font-size:7px;">May be open officially</span>
		                </p>
		                <p style="display:inline-block;width:100px;float:left;font-size:10px;">
		                	<span style="font-size:7px;padding-left:5px;">May be opend officially</span>
		                </p>
		              </td>
		            </tr>
		            <tr height="15px">
		              <td width="55%">
		                	<span style="font-size:10px;float:left;display:inline-block;">
		                	  邮件种类 
		                	 <b style="font-size:8px;">Category of Item</b>
		                	</span>
		                	<span style="width:15px;height:14px;border-left:1px solid #000;display:inline-block;float:right;font-size:10px;font-weight:bold;">
		                	√
		                	</span>
		              </td>
		              <td colspan="2">
		                 <span style="font-size:10px;">
		                   	其他  Other  
		                 </span>
		              </td>
		            </tr>
		            <tr height="23px">
		              <td width="55%">
		                <span style="font-size:10px;display:inline-block;height:23px;width:140px;">
		                  	内装详细名称和数量 <br/> 
		                    <span style="font-size:8px;">
		                     quantity  and detailed of description
		                    </span>
		                </span>
		              </td>
		              <td width="23%">
		               <span style="font-size:10px;display:inline-block;height:23px;padding-left:5px;">
		                  	重量(千克) <br/> 
		                    <span style="font-size:8px;">
		                     Weight(Kg)
		                    </span>
		                </span>
		              </td>
		              <td width="22%">
		                <span style="font-size:10px;display:inline-block;height:23px;padding-left:10px;">
		                  	价值 <br/> 
		                    <span style="font-size:8px;">
		                     Value
		                    </span>
		                </span>
		              </td>
		            </tr>
		            <tr height="12px" style="font-size:10px;text-align:center;font-weight:bold;">
		              <td width="55%">
		               '.$allParamArr['productsInfo']['namefiles'].'
		              </td>
		              <td width="23%">
		               '.$allParamArr['productsInfo']['totalWeight'].'
		              </td>
		              <td width="22%">
		               USD'.$allParamArr['productsInfo']['totalPrice'].'
		              </td>
		            </tr>
		            <tr height="30px" style="text-align:center;">
		              <td width="55%">
		      
		               <span style="font-size:8px;">
		                 	<span style="font-size:8px;">协调系统税则号列和货物原产过只对商品邮件编写</span>HS tariff number and country of origin of goods For Commercial items only
		               </span>
		         
		              </td>
		              <td width="23%" rowspan="2">
		                <span style="font-size:10px;display:inline-block;height:38px;padding-left:5px;">
		                  	总重量(千克) <br/> 
		                    <span style="font-size:8px;">
		                     Weight(Kg)
		                    </span><br>
		                    <span style="font-size:10px;">'.$allParamArr['productsInfo']['totalWeight'].'</span>
		                </span>
		              </td>
		              <td width="22%" rowspan="2">
		                <span style="font-size:10px;display:inline-block;height:38px;padding-left:5px;">
		                  	总价值 <br/> 
		                    <span style="font-size:8px;">
		                     Total Value<br>
		                     <span style="font-size:10px;">USD'.$allParamArr['productsInfo']['totalPrice'].'</span>
		                    </span>
		                </span>
		              </td>
		            </tr>
		            <tr height="14px">
		              <td width="55%" style="text-align:center;">
		               <span style="font-size:12px;font-weight:bold;">CN</span>
		              </td>
		            </tr>
		          </table>
		        </td>
		        <td width="25%">
		         <table class="right" border="0">
		           <tr style="font-size:10px;text-align:center;" height="20px">
		             <td width="33px" style="border-top:none;border-left:none;">
		            	  航空
		             </td>
		             <td style="border-top:none;font-size:7px;">
		               GuangZhou<br/>China
		             </td>
		           </tr>
		           <tr style="font-size:8px;text-align:center;" height="20px">
		             <td>
		             PAR<br/>
		             AVION
		             </td>
		             <td>
		               	已验视
		             </td>
		           </tr>
		           <tr style="font-size:8px;text-align:center;" height="20px">
		             <td>
		              	小包邮件
		             </td>
		             <td>
		                                单位:广州小包中心
		             </td>
		           </tr>
		           <tr style="font-size:8px;text-align:center;" height="20px">
		             <td>
		              	PETIT PAQUET
		             </td>
		             <td>
		                                验视人：方静霞
		             </td>
		           </tr>
		         </table>
		         <p style="width:92px;height:20px;font-size:16px;text-align:center;line-height:20px;">
		         	DCPS
		         </p>
		        </td>
		      </tr>
		      <tr height="35px">
		        <td colspan="2">
		         <p style="font-size:7px;padding-left:5px;">
		          	我保证上述申报准确无误，本函件内未装寄法律或邮件和海关规章禁止寄递的任何危险物品<br/>
		          	I the undersigned,certify that the particulars given in this declaration are correct and this item does not contain any
		            dangerous articles prohibited by legislation or by postal or customers regulations.
		         </p>
		         <p style="padding-left:5px;font-size:8px;">
		        	 寄件人签字 Sender\'s signature:  SLME('.$allParamArr['ordersInfo']['erp_orders_id'].')&nbsp;&nbsp;&nbsp;&nbsp;
		        	 '.$allParamArr['productsInfo']['time'].'
		         </p>
		        </td>
		      </tr>
		    </table>
		    <div style="width:100%;height:25px;font-size:9px;text-align:center;overflow:hidden;">
		       '.$allParamArr['productsInfo']['skufiles'].'
		    </div>
		  </div>
		';
		return $reStr;
	}
	
	//DHL挂号面单
	public function printDhlGuaHao_template($allParamArr)
	{
		$reStr = '<style>
	    *{margin:0;padding:0;}
		body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
		#main_frame_box{width:99mm;height:99mm;margin:auto;border:1px solid #000; overflow:hidden;}
		.top{width:354px;height:70px;margin:10px auto;}
		table{border-collapse:collapse;border:none;width:354px;height:270px;margin:15px auto 0;}
		td{border:1px solid #000;}
	</style>';
		
		$reStr .='
		<div style="height:100mm; width:100mm; margin:auto; overflow:hidden;">
		  <div id="main_frame_box">
		    <div class="top" style="line-height: 70px;">
				
		    </div>
		    <table>
		     <tr style="height:12%;">
		       <td colspan="3">
		         <P style="width:235px;height:33px;float:left;margin-left:5px;">
		            <span style="font-weight:bold;">CUSTOMS DECLARATION</span>
		            <br/>
		            <span style="font-size:11px;">Postal Administration(Maybe opened officially)</span>
		         </P>
		         <P style="font-weight:bold;width:110px;height:33px;float:right;">
		            <span style="font-weight:bold;padding-left:50px;">CN22</span>
		            <br/>
		            <span style="font-size:11px;padding-left:50px;">important ！</span>
		         </P>
		       </td>
		     </tr>
		     <tr style="height:15%;">
		       <td colspan="3">
		        <img src="'.site_url('attachments').'/images/dhl.jpg" />
		       </td>
		     </tr>
		     <tr style="height:23%;">
		       <td colspan="2" width="70%">
		        <p style="height:57px;">
		           <span style="font-size:10px;">Detailed description of Contents</span>
		           <br>
		           <span style="margin-left:30px;margin-top:10px;display:inline-block;">
		           '.$allParamArr['productsInfo'][0]['products_declared_en'].' '.$allParamArr['productsInfo'][0]['orders_sku'].'*'.$allParamArr['productsInfo'][0]['item_count'].'
		           </span>
		        </p>
		       </td>
		       <td>
		        <p style="height:57px;">
		          <span style="font-size:12px;">&nbsp;&nbsp;&nbsp;&nbsp;	Value</span>
		          <br>
		          <span style="margin-left:10px;margin-top:10px;display:inline-block;font-weight:bold;">
		            EUR $'.$allParamArr['productsInfo']['totalPrice'].'
		           </span>
		        </p>
		       </td>
		     </tr>
		     <tr style="height:10%;">
		       <td width="33%">
		          <p style="height:17px;">
			          <span style="font-size:12px;">&nbsp;&nbsp;Origin country</span>
		          </p>
		           <p>
		            <span style="margin-left:40px;display:inline-block;font-weight:bold;">
			           CN
			        </span>
		          </p>
		       </td>
		       <td width="33%">
		          <p style="height:17px;">
			          <span style="font-size:12px;">&nbsp;&nbsp;TotalWeight(Kg)</span>
		          </p>
		          <p>
		            <span style="margin-left:40px;display:inline-block;font-weight:bold;">
			            '.$allParamArr['productsInfo']['totalWeight'].'
			        </span>
		          </p>
		       </td>
		       <td width="33%">
		          <p style="height:17px;">
			          <span style="font-size:12px;">&nbsp;&nbsp;TotalValue</span>
		          </p>
		          <p>
		            <span style="margin-left:10px;display:inline-block;font-weight:bold;">
			            EUR $'.$allParamArr['productsInfo']['totalPrice'].'
			        </span>
		          </p>
		       </td>
		     </tr>
		     <tr style="height:23%">
		       <td colspan="3">
		        <p style="height:57px;width:345px;margin:0 auto;font-size:10px;">
		          I,hereby undersigned whose name and address are given on the item<br> certify that the
		          particulars given in the declaration are correct and that<br>  this item does not contain
		          any dangerous articles or articles prohibited by<br> legislation or by postal customs regulations.
		        </p>
		       </td>
		     </tr>
		     <tr>
		       <td colspan="3">
		           <p style="height:17px;">
			          <span style="font-size:12px;">&nbsp;&nbsp;Date and Sender\'s Signature</span>
		          </p>
		          <p style="width:100%;">
		            <span style="margin-left:10px;display:inline-block;font-weight:bold;">
			            '.date('m-d-Y').' ('.$allParamArr['ordersInfo']['erp_orders_id'].')
			        </span>
			        <span style="margin-left:80px;display:inline-block;font-weight:bold;">
		
			        </span>
			        <span style="margin-left:20px;display:inline-block;font-weight:bold;">
			            SLM(X04)
			        </span>
		          </p>
		       </td>
		     </tr>
		    </table>
		  </div>
		</div>
		';
		$reStr.='
				<div style="height:100mm; width:100mm; margin:auto; overflow:hidden;">
		  <div style="margin:auto; width:99mm; border:1px solid;">
		     <div style="width:100%;margin-top:5px;">
		       <div style="margin-left:20px;float:left;">
		          <p style="width:100px;height:50px;margin:25px auto 0;">
		            PACKET PLUS,<br/>STANDARD
		          </p>
		       </div>
	       		<table style="width:61mm;height:27mm; float:right; margin: 0 10px 0 0;text-align:center;font-size:12px;">
					'.$allParamArr['country_img'].'
				</table>
				<div style="clear:both;"></div>
		     </div>
		
			 <div style="margin: 3px 0 3px 20px;">
			      <div style="height: 60px;">
			          <div style="float:left; font-size:40px; width:50px; height:100%; line-height: 86px;">
			           		R
			           </div>
		
			           <div style="float:left; height:100%;">
			           		<div style="text-align:center;">'.$allParamArr['ordersInfo']['orders_shipping_code'].'</div>
			           		<div style="width:180px; height:1px; border-top:1px solid;"></div>
			           		<div>
			           			<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
			           		</div>
			           </div>
			      </div>
		
			      '.$allParamArr['mail_num'].'
		
			      <div style="clear:both;"></div>
			 </div>
		
		     <div style="width:328px;height:150px;margin:0 auto;font-size:16px;">
		       '.$allParamArr['ordersInfo']['buyer_name'].'<br>
		       '.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br>
		       '.$allParamArr['ordersInfo']['buyer_city'].'<br>
		       '.$allParamArr['ordersInfo']['buyer_state'].'<br>
		       '.$allParamArr['ordersInfo']['buyer_country'].'<br>
		       '.$allParamArr['ordersInfo']['buyer_zip'].'<br>
		       Tel:'.$allParamArr['ordersInfo']['buyer_phone'].'
		     </div>
		  </div>
		  <div style="text-align:center;word-wrap:break-word;">
		   	<span style="display:inline-block;">【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</span>
		       '.$allParamArr['productsInfo']['sku'].'
		  </div>
		    
		  </div>
		';
		return $reStr;
	}
	
	//中邮平邮面单
	public function ZhongYouPingYou_template($allParamArr)
	{
		$reStr = '
				<style>
	    *{margin:0;padding:0;}
		body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
		#main_frame_box{width:99mm;height:99mm;margin:2px auto;}
		table{border-collapse:collapse;border:none;width:99mm;height:60mm;}
		div>p>table>.detail{width:280px;height:130px;border:none;}
		td{border:1px solid #000;}
	</style>
				';
		
		$reStr .= '
		<div style="width:99.5mm;height:98mm;margin:0 auto; overflow:hidden;">
		  <div id="main_frame_box">
		    <table>
		      <tr height="18px" style="font-size:9px;">
		        <td colspan="3">
		          <p style="width:33mm;height:20px;line-height:21px;float:left;">
		
		          </p>
		          <p style="width:33mm;height:20x;float:left;text-align:center;font-weight:bold;">
		          	  中国邮政<br>
		          	 CHINA POST
		          </p>
		          <p style="width:32mm;height:20px;float:left;text-align:center;font-weight:bold;">
		             <span style="width:13mm;height:10px;line-height:10px;border:1px solid #000;display:inline-block;">
		               	邮2113
		             </span>
		             <br>
		             CN22
		          </p>
		        </td>
		      </tr>
		      <tr height="67px">
		        <td colspan="3">
		         TO:
		          <p style="font-size:11px;height:66px;width:92mm;float:right;overflow:hidden;">
		            '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
			        '.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
			        '.$allParamArr['ordersInfo']['buyer_city'].' '.$allParamArr['ordersInfo']['buyer_state'].'<br/>
			        '.$allParamArr['buyerCountry'].'('.$allParamArr['country_cn'].')
			        ZIP:'.$allParamArr['ordersInfo']['buyer_zip'].' &nbsp;&nbsp;&nbsp;&nbsp; TEL:'.$allParamArr['ordersInfo']['buyer_phone'].'
		
		           </p>
		        </td>
		      </tr>
		      <tr height="10px">
		        <td colspan="3">
		        </td>
		      </tr>
		
			  <tr height="10px">
		              <td colspan="3">
		                <p style="display:inline-block;width:110px;float:left;font-size:10px;font-weight:bold;">
		                	<span style="font-size:8px;">CUSTOMS DECLARATION</span>
		                </p>
		                <p style="display:inline-block;width:100px;float:left;font-size:10px;text-align:center;">
		                	<span style="font-size:8px;">May be open officially</span>
		                </p>
		                <p style="display:inline-block;width:120px;float:left;font-size:10px;text-align:center;">
		                	<span style="font-size:8px;padding-left:5px;">May be opend officially</span>
		                </p>
		              </td>
		       </tr>
		 
		       <tr height="14px">
		              <td width="58%">
		                	<span style="font-size:10px;float:left;display:inline-block;">
		                	  邮件种类
		                	 <b style="font-size:8px;">Category of Item</b>
		                	</span>
		                	<span style="width:15px;height:14px;border-left:1px solid #000;display:inline-block;float:right;font-size:10px;font-weight:bold;">
		                	√
		                	</span>
		              </td>
		              <td colspan="2">
		                 <span style="font-size:10px;">
		                   	其他  Other
		                 </span>
		              </td>
		      </tr>
		      <tr height="24px">
		              <td width="58%">
		                <span style="font-size:10px;display:inline-block;height:23px;width:210px;">
		                  	内装详细名称和数量
		                    <span style="font-size:8px;">
		                     quantity  and detailed of description
		                    </span>
		                </span>
		              </td>
		              <td width="20%">
		               <span style="font-size:10px;display:inline-block;height:23px;padding-left:5px;">
		                  	重量(千克) <br/>
		                    <span style="font-size:8px;">
		                     Weight(Kg)
		                    </span>
		                </span>
		              </td>
		              <td width="22%">
		                <span style="font-size:10px;display:inline-block;height:23px;padding-left:10px;">
		                  	价值 <br/>
		                    <span style="font-size:8px;">
		                     Value
		                    </span>
		                </span>
		              </td>
		       </tr>
		       <tr height="14px" style="font-size:10px;text-align:center;">
		              <td width="58%" style="font-size:10px;font-weight:bold;">
		               '.$allParamArr['productsInfo']['namefiles'].'
		              </td>
		              <td width="20%" style="font-weight:bold;font-size:10px;">
		               '.$allParamArr['productsInfo']['totalWeight'].'
		              </td>
		              <td width="22%" style="font-weight:bold;font-size:10px;">
		               USD&nbsp;'.$allParamArr['productsInfo']['totalPrice'].'&nbsp;
		              </td>
		       </tr>
		       <tr height="23px" style="text-align:center;">
		              <td width="58%">
		
		               <span style="font-size:8px;">
		                 	<span style="font-size:8px;">协调系统税则号列和货物原产过只对商品邮件编写</span>HS tarif number and country  origin of goods For Commerci items only
		               </span>
		
		              </td>
		              <td width="20%" rowspan="2">
		                <span style="font-size:10px;display:inline-block;height:38px;padding-left:5px;">
		                  	总重量(千克) <br/>
		                    <span style="font-size:8px;">
		                    Total Weight(Kg)
		                    </span><br>
		                    <span style="font-size:10px;font-weight:bold;">'.$allParamArr['productsInfo']['totalWeight'].'</span>
		                </span>
		              </td>
		              <td width="22%" rowspan="2">
		                <span style="font-size:10px;display:inline-block;height:38px;padding-left:5px;">
		                  	总价值 <br/>
		                    <span style="font-size:8px;">
		                     Total Value<br>
		                     <span style="font-size:10px;font-weight:bold;">USD&nbsp;'.$allParamArr['productsInfo']['totalPrice'].'&nbsp;</span>
		                    </span>
		                </span>
		              </td>
		       </tr>
		       <tr height="13px">
		              <td width="58%" style="text-align:center;">
		               <span style="font-size:10px;font-weight:bold;">CN</span>
		              </td>
		       </tr>
		      <tr height="43px">
		        <td colspan="3">
		         <p style="font-size:7px;padding-left:5px;">
		          	我保证上述申报准确无误，本函件内未装寄法律或邮件和海关规章禁止寄递的任何危险物品<br/>
		          	I the undersigned,certify that the particulars given in this declaration are correct and this item does not contain any
		            dangerous articles prohibited by legislation or by postal or customers regulations.
		         </p>
		         <p style="padding-left:5px;font-size:9px;border-top:1px solid #000;">
		        	 寄件人签字 Sender\'s signature:  <span style="font-weight:bold;">SLME('.$allParamArr['ordersInfo']['erp_orders_id'].')</span>&nbsp;&nbsp;&nbsp;&nbsp;
		        	 <span style="font-weight:bold;padding-left:90px;">'.$allParamArr['productsInfo']['time'].'</span>
		         </p>
		        </td>
		      </tr>
		
		       <tr height="60px">
		        	 <td colspan="3">
		      
		      			<div style="float:left;text-align:center;">
		        	 		<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=30&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" style="margin-left:10px;"/>
		        	 		<br/>
		        	 		'.$allParamArr['ordersInfo']['orders_shipping_code'].'
		        	 	</div>
		      						
			      		<div style="float:right; font-size: 18px; margin-right:10px; line-height: 50px;">
			      		'. $allParamArr['zone'] .'区
			      		</div>
		      			<div style="cler: both;"></div>
			      				
		      		</td>
		        </tr>
		    </table>
		
		    <div style="width:98.5mm;height:30px;overflow:hidden;font-size:11px;text-align:center;">
		     '.$allParamArr['productsInfo']['skufiles'].'
		    </div>
		  </div>
	    </div>
		';
		return $reStr;
	}
	
	//打印4px新加坡平邮小包面单
	public function print4pxXinJiaPoTemplate ($allParamArr)
	{
		$sql = "select * from erp_num_code where 4px_country_code = '".$allParamArr['ordersInfo']['buyer_country_code']."'";  //根据buyer_country_code查出在表erp_num_code中对应关系
		$ret = mysql_fetch_array(mysql_query($sql));
		$reStr = '
			<style>
			    *{margin:0;padding:0;}
				body{ font-family:Tahoma,Arial,"Times New Roman","微软雅黑","Arial Unicode MS"; font-size:14px; line-height: 1.3;}
				#main_frame_box{width:300px;height:370px;margin:0 auto;position: relative; overflow:hidden;}
				.top{width:354px;height:70px;margin:10px auto;}
				table{border-collapse:collapse;border:none;width:354px;height:270px;margin:15px auto;}
				td{border:1px solid #000;}
		        div, span {word-wrap: break-word;}
			</style>
		';
		
		$reStr .= '
				<div id="main_frame_box" style="width:300px;height:370px;margin:0 auto;position: relative; overflow:hidden;">
					<div>
						<div style="width: 100%; overflow: hidden;">
							<div style="float: left; height: auto; width: auto;">
								<div style="font-size:10px; width:120px; font-style:normal; line-height:12px;">
									CHANGI AIRFREIGHT 
									CENTRE PO BOX 1192
									 SINGAPORE 918118
								</div>
							</div>
		
							<div style="float: left; height: auto; width: auto;">
								<div style="font-size:16px;font-weight:bold; width:25px; height:83px; line-height:80px; text-align:center; vertical-align:middle;">
									'. $allParamArr['fq'] .'
								</div>
							</div>
						</div>
		<div style="width: 45%;border:2px solid #000;float: center; position:absolute;left:142px;top:0px;width:153px;height:72px;z-index:2;">
										<div style="padding-center: 50px; font-size: 17px;" align="center">
											<b>PP</b> 60108<br><br></div>
											<div style="font-family:STFangsong;font-size: 13px;color: #112" align="center">SINGAPORE</div>
							</div>
						<div style="overflow: hidden; width: 100%;">
							<div style="float: left; height: auto; width: auto;">
								<div style="font-size:12px;max-height: 125px; width: 200px;">
									<div style="font-size: 13px; line-height: 13px; word-break: break-all;">
										<b>TO: '. $allParamArr['ordersInfo']['buyer_name'] .'</b>
									</div>
		
									<div style="word-wrap: break-word;">
										<b>Tel: </b>'. $allParamArr['ordersInfo']['buyer_phone'] .'
									</div>
		
									<div style="word-wrap: break-word;">
										<span>
										'. $allParamArr['ordersInfo']['buyer_address_1'] . $allParamArr['ordersInfo']['buyer_address_2'] .'  '.
												$allParamArr['ordersInfo']['buyer_city'] .'  '. $allParamArr['ordersInfo']['buyer_state'] .'
										</span>
							
										<span>'.$allParamArr['ordersInfo']['buyer_zip'].'</span>
									</div>
		
									<div style="word-wrap: break-word;">
										<b>'.$allParamArr['countryInfo']['display_name'].'</b>
									</div>
			
								</div>
							</div>
				
							<div style="float: right; height: auto; width: auto;">
								<div style="border: 1px solid black; font-weight: bold; text-align: center; width: 54px; word-wrap: break-word;">
									<div style="border-bottom: 1px solid black; font-size: 6px; padding: 2px;">
										<div>AIR MAIL</div>
										<div>航PAR AVION空</div>
									</div>
		
									<div style="border-bottom: 1px solid black; font-size: 10px;">zone</div>
							
									<div style="font-size: 18px; font-weight: bold;">'.$allParamArr['countryInfo']['country_en'].'</div>
							
									<div style="clear:both;"></div>
								</div>
								<div style="clear:none;"> &nbsp;&nbsp;&nbsp;'.$ret['4px_num'].'</div>
							</div>
						</div>
		
						<div style="height: 5mm; width: 100%;">
						</div>
		
						<div style="width: 100%;">
							<div>
								<div style="overflow: hidden; width: 100%;">
									<div style="text-align: center; width: 100%; float: left; height: auto;">
										<div><b>'. $allParamArr['ordersInfo']['orders_shipping_code'] .'</b></div>
										<div style="padding-top: 3px; text-align: center; ">
										   <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
</div>
									</div>
								</div>
								<br>
		
								<div style="border: 1px solid black; margin-bottom: 2px; overflow: hidden; width: 100%;">
									<div style="float: left; height: auto; width: auto;">
										<div style="padding-right: 5px; font-size: 9px;">
											4PSGOM+
										</div>
									</div>
			
									<div style="float: left; height: auto; width: auto;">
										<div style="padding-right: 5px; font-size: 9px;">
											【21268260】
										</div>
									</div>
			
									<div style="float: left; height: auto; width: auto;">
										<div style="padding-right: 5px; font-size: 9px;">
											<span style="word-wrap: break-word;">Ref No: </span>
												LME'.$allParamArr['ordersInfo']['erp_orders_id'].'
										</div>
									</div>
								</div>
		
								<div style="border: 1px solid black; margin-bottom: 2px; overflow: hidden; width: 100%;">
									<div style="float: left; height: auto; width: auto;">
										<div style="padding-right: 5px; font-size: 9px;">
											CS: S4305
										</div>
									</div>
			
									<div style="float: left; height: auto; width: auto;">
										<div style="padding-right: 5px; font-size: 9px;">
											SD: S0365
											(X011)
										</div>
									</div>
								</div>
		
								<div style="overflow: hidden; width: 100%; height: 20mm;">
									<div style="float: left; height: auto; width: auto;">
										<div style="font-size: 9px;">
											<b>【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</b>
											'.$allParamArr['productsInfo']['sku'].'
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				';
		$reStr .= $this->baoguan_template($allParamArr);
		return $reStr;
	}
	
	//通用报关单
	public function baoguan_template($allParamArr){
		$reStr = '<style>table{border-collapse:collapse;}td{white-space:normal;border:1px solid black;}</style>';
		$reStr .='
				<div class="tongyong" style="width:375px;height:360px;margin:0 auto;margin-top:8px;">
				  <table border="1" style="width:375px;height:360px;margin:0;padding:0;">
				    <tr height="35">
				      <td colspan="3">
				        <p style="float:left;width:100px;height:35px;line-height:35px;font-size:12px;text-align:center;">
				           '.$allParamArr['ordersInfo']['erp_orders_id'].'
				        </p>
				        <p style="float:left;width:190px;height:35px;font-size:12px;font-weight:bold;text-align:center;">
				           	报关签条<br/>
				            CUMTOMS DECLARATION
				        </p>
				        <p style="float:left;width:70px;height:35px;font-size:11px;font-weight:bold;text-align:center;">
				                         邮2113<br/>
				           CN22
				        </p>
				      </td>
				    </tr>
				    <tr height="15">
				      <td colspan="3">
				        <p style="float:left;width:170px;line-height:15px;height:15px;font-size:11px;">
				           	可以经行拆开
				        </p>
				        <p style="float:left;width:190px;line-height:15px;height:15px;font-size:11px;">
				           	May be opened officially
				        </p>
				        
				      </td>
				    </tr>
				    <tr height="70">
				      <td colspan="3">
				         <table style="width:375px;height:70px;margin:0;padding:0;border:none;">
				            <tr height="34">
				              <td width="60" style="border-top:none;border-bottom:none;border-left:none;" rowspan="2">
				                <p style="height:34px;text-align:center;line-height:34px;font-size:11px;">
				                  	邮件种类
				                </p>
				              </td>
				              <td width="30" style="border-top:none;">
				                <p style="height:34px;text-align:center;line-height:34px;font-size:14px;font-weight:bold;">
				                  	X
				                </p>
				              </td>
				              <td width="80" style="border-top:none;">
				                <p style="height:34px;font-size:12px;">
				                  	礼品<br/>
				                  	gift
				                </p>
				              </td>
				              <td width="30" style="border-top:none;"></td>
				              <td width="174" style="border-top:none;border-right:none;">
				              	<p style="height:34px;font-size:12px;">
				                  	商品货样<br/>
				                  	Commercial Sample
				                </p>
				              </td>
				            </tr>
				            <tr height="34">
				              <td width="30" style="border-bottom:none;">
				              	
				              </td>
				              <td width="80" style="border-bottom:none;">
				               <p style="height:34px;font-size:12px;">
				                  	 文件<br/>
				                  	Documents
				                </p>
				              </td>
				              <td width="30" style="border-bottom:none;"></td>
				              <td width="174" style="border-bottom:none;border-right:none;">
				              	<p style="height:34px;font-size:12px;">
				                  	 其他<br/>
				                  	Other
				                </p>
				              </td>
				            </tr>
				         </table>
				      </td>
				    </tr>
				    <tr height="30">
				      <td width="225">
				        <p style="height:30px;font-size:11px;text-align:center;">
				        	内件详细名称和数量<br/>
				        	Quantity and detailed description ofcontents
				        </p>
				      </td>
				      <td width="80">
				        <p style="height:30px;font-size:11px;text-align:center;">
				        	重量(千克)<br/>
				        	Weight(Kg)
				        </p>
				      </td>
				      <td width="70">
				        <p style="height:30px;font-size:11px;text-align:center;">
				        	价值<br/>
				        	Value
				        </p>
				      </td>
				    </tr>
				    
				   <tr height="25">
				      <td width="225">
				        <p style="height:25px;font-size:11px;text-align:center;">
				        	
				        	'.$allParamArr['productsInfo'][0]['products_declared_en'].'
				        </p>
				      </td>
				      <td width="80">
				        <p style="height:25px;font-size:11px;text-align:center;line-height:25px;">
				        	'.$allParamArr['productsInfo'][0]['item_count']*$allParamArr['productsInfo'][0]['products_weight'].'
				        </p>
				      </td>
				      <td width="70">
				        <p style="height:25px;font-size:11px;text-align:center;line-height:25px;">
				        	'.($allParamArr['ordersInfo']['orders_total'] > 20 ? 20 : $allParamArr['ordersInfo']['orders_total']).'USD
				        </p>
				      </td>
				    </tr>
				    <tr height="30">
				      <td rowspan="2">
				        <p style="height:55px;font-size:9px;">
				        	协调系统税则号列和货物原产国(只对商品邮件填写)<br/>
				        	HS tariff number and country of origin of goods(For Commercial items only)
				        </p>
				      </td>
				       <td width="80">
				        <p style="height:30px;font-size:11px;text-align:center;">
				        	重量(千克)<br/>
				        	Weight(Kg)
				        </p>
				      </td>
				      <td width="70">
				        <p style="height:30px;font-size:11px;text-align:center;">
				        	价值<br/>
				        	Value
				        </p>
				      </td>
				    </tr>
				    <tr height="25">
				      <td>
				        <p style="height:25px;font-size:11px;text-align:center;line-height:25px;">
				        	'.$allParamArr['productsInfo'][0]['item_count']*$allParamArr['productsInfo'][0]['products_weight'].'
				        </p>
				      </td>
				      <td>
				       <p style="height:25px;font-size:11px;text-align:center;line-height:25px;">
				        	'.($allParamArr['ordersInfo']['orders_total'] > 20 ? 20 : $allParamArr['ordersInfo']['orders_total']).'USD
				        </p>
				      </td>
				    </tr>
				    <tr height="80">
				      <td colspan="3">
				         <p style="height:90px;font-size:10px;">
				        	我保证上述申报准确无误，本函件内未装寄法律或邮件和海关规章禁止寄递的任何危险物品
							<br/>
							I the undersigned,certify that the particulars given in this declaration are correct and this item does not contain any dangerous articles prohibited by legislation or by postal or customers regulations.
							<br/>
							<span style="padding-left:80px;">寄件人签字 Sender\'s signature:SLME </span>
				         </p>
				      </td>
				    </tr>
				  </table>
				</div>
		';
		return $reStr;
	}
	
	
	//燕文YW荷兰面单
	public function yanWenNL_template ($allParamArr)
	{
		$reStr = '
			<style>
				*{margin:0; padding:0;}
				table{border-collapse:collapse;border:none; width: 100%;}
				body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:9px;}
		      	div td{word-break: normal; word-wrap: break-word;}
				#main{width:100mm; height:129mm; margin:auto;}
				#main_border{width:98mm; height:128mm; margin: 2px auto 0; overflow: hidden;}
				
				.f_l{float:left;}
				.f_r{float:right;}
				.address tr th{text-align:left;}
				.address tr td{text-align:right;}
		      	.input_bor {border: 2px solid; display: inline-block; width: 10px; height: 10px;}
		      	.bot_inl_blo span{display: inline-block;}
			</style>
				';
		$reStr .= '
				<div id="main">
					<div id="main_border">
						<div>
						   <div style="float: left; width:50px; height:50px;">
								<img src="'.site_url('attachments').'/images/ywreturn.png" style="width:50px; height:50px;">
							</div>
							<div style="float: left; width:40%;">
								<img src="'.site_url('attachments').'/images/yanWenNL1.png" style="width:100%;"/>
							</div>
				
							<div style="float: right; width:40%;">
								<img src="'.site_url('attachments').'/images/yanWenNL2.png" style="width:100%;"/>
							</div>
							<div style="clear:both;"></div>
						</div>
						
						<div>
							Return if undeliverable: H-10905,Postbus 7040,3109 AA Schiedam The Netherlands
						</div>
				
						<div>
							<div class="f_l">
								<p>R Registered/recommandé</p>
								<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
								<p style="margin-left: 20px;">'.$allParamArr['ordersInfo']['orders_shipping_code'].'</p>
							</div>
										
							<div class="f_l" style="margin-left:20px; width:190px;">
								<p style="font-size:16px;">Deliver to:</p>
								<p>'.$allParamArr['ordersInfo']['buyer_name'].' '.$allParamArr['ordersInfo']['buyer_phone'].'</p>
								<p>'.$allParamArr['ordersInfo']['buyer_address_1']." ".$allParamArr['ordersInfo']['buyer_address_2'].'</p>
								<p>'.$allParamArr['ordersInfo']['buyer_city']." ".$allParamArr['ordersInfo']['buyer_state']." ".$allParamArr['ordersInfo']['buyer_zip'].'</p>
								<p style="font-size:16px; font-weight:bold;">'.$allParamArr['country'].'</p>
							</div>
							<div style="clear:both;"></div>
						</div>
										
					<table border="1">
										
						<colgroup>
					       <col width="50%">
					       <col width="25%">
					       <col width="25%">
					    </colgroup>
										
						<tr>
							<td colspan="3">
								<div style="font-size: 18px;">
									<div style="float: left;">
										CUSTOMS DECLARATION
									</div>
					
									<div style="width: 22mm; float: right;">
										<div style="text-align:center;">
											CN22
										</div>
									</div>
					
									<div style="clear:both;"></div>
								</div>
								<div style="text-align: right; margin-right:20px;">
									May be opened officially
								</div>
						
								<div style="text-align: right; line-height: 24px;">
									Important! See Instructions on the back
								</div>
							</td>
						</tr>
										
						<tr>
							<td colspan="3" style="padding:3px 0 0 10px;">
								<table>
									<tr>
										<td>
											<span class="input_bor">
		
											</span>
											Gift
										</td>
										<td>
											<span class="input_bor">
		
											</span>
											Commercial sample
										</td>
									</tr>
			
									<tr>
										<td>
											<span class="input_bor">
		
											</span>
											Documents
										</td>
										<td>
											<span class="input_bor">
												√
											</span>
											Other
										</td>
									</tr>
								</table>
							</td>
						</tr>
										
						<tr>
							<td>
								Quantity and detailed description
								of contents
							</td>
							<td>
								Weight (kg)
							</td>
							<td>
								Value
							</td>
						</tr>
										
						<tr>
							<td>
								'.$allParamArr['productsInfo'][0]['products_declared_en'].'
								#'.$allParamArr['productsInfo'][0]['orders_sku'].' * '.$allParamArr['productsInfo'][0]['item_count'].'
							</td>
							<td>
								'.$allParamArr['productsInfo'][0]['products_weight'].'
							</td>
							<td>
								'.$allParamArr['total_price'].'USD
							</td>
						</tr>
										
						<tr>
							<td>
								For commercial items only
								If known,HS tariff number and
								country of origin of goods
							</td>
							<td>
								Total Weight (kg)
								<br>
								'.$allParamArr['total_weight'].'
							</td>
							<td>
								Total Value
								<br>
								'.$allParamArr['total_price'].'USD
							</td>
						</tr>
					
						<tr>
							<td colspan="3">
								<div style="font-size:8px;">
									I,the undersigned,whose name and address are given on the itme, certify
									that particulars given in this declaration are correct and that this item does
									not contain any dangerous article or articles prohibited by legislation or by
									postal or customs regulations
								</div>
								<div style="font-size: 20px; text-align:right; width:98%;">
									'. $allParamArr['country_int'] .'
								</div>
								<div style="clear:both;"></div>
			
								<div style="width:100%" class="bot_inl_blo">
									<span style="width:40%">Date and sender\'s signature</span>
									<span style="width:19%">YANWEN</span>
									<span style="width:19%">'.date('d-m-Y').'</span>
									<span style="width:16%">'.$allParamArr['yanwen_code'].'</span>
								</div>
							</td>
						</tr>
					</table>
											
						<div style="border-bottom: 1px solid;">
							<div style="float:left; font-size:24px; width:30px;">
								D
							</div>
				
							<div style="float:left; font-size:16px; line-height:26px;">
								'. $allParamArr['country_eu'] .'
							</div>
				
							<div style="float:right; margin-right:10px;">
								OrderNo:'.$allParamArr['ordersInfo']['erp_orders_id'].'
							</div>
							<div style="clear:both;"></div>
						</div>
										
						<div>
							'.$allParamArr['sku_string'].'
						</div>
					</div>
				</div>
				';
	
		return $reStr;
	}
	
	
	public function zhongMeiTemplate($allParamArr)
	{
		
		$reStr = '
				<style>
				*{margin:0; padding:0;}
				#main{width:100mm; height:99mm; margin:auto;overflow:hidden;}
				body{font-size: 10px; font-family:Arial,Helvetica,sans-serif; color:#000000;}
				</style>
				';
		$reStr .='
		   <div id="main">
		   	    <table style="width:100%; margin:auto;" border="1" cellspacing=0>
					 <tr height="30">
					  <td colspan="2" style="font-weight:bold;text-align:center;font-size:14px;">渠道：'.$allParamArr['country_cn'].'专线挂号【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</td>
					 </tr>
					 <tr height="50">
					  <td width=100 style="font-weight:bold;text-align:center;font-size:14px;">第1/1件</td>
					  <td style="font-weight:bold;font-size:14px;">
					    	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;国家：<br/>
					    	&nbsp;&nbsp;'.$allParamArr['country_cn'].'('.$allParamArr['country_code'].')
					  </td>
					 </tr>
					 <tr height="60">
					  <td colspan="2" style="font-weight:bold;text-align:center;">
					   <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" /><br/>
					    <span style="font-weight:bold;font-size:12px;">'.$allParamArr['ordersInfo']['orders_shipping_code'].'</span>
					  </td>
					 </tr>
					 <tr height="100">
					  <td colspan="2" style="font-size:12px;">
					  
					  	 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					     Ship To：'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
					     
					     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								        '.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
								        
						 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								        '.$allParamArr['ordersInfo']['buyer_city'].' '.$allParamArr['ordersInfo']['buyer_state'].'<br/>
								        
						 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								        '.$allParamArr['ordersInfo']['buyer_country'].'('.$allParamArr['ordersInfo']['buyer_country_code'].')
								        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Zip:'.$allParamArr['ordersInfo']['buyer_zip'].'
								        <br/>
								        
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								 Tel:'.$allParamArr['ordersInfo']['buyer_phone'].'
					  
					  </td>
					 </tr>
					 <tr height="60">
					  <td colspan="2" style="font-weight:bold;text-align:center;">
					    <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text=SLM'.$allParamArr['ordersInfo']['erp_orders_id'].'&f1=-1&f2=8&a1=&a2=B&a3=" /><br/>
					    <span style="font-weight:bold;font-size:12px;">SLM'.$allParamArr['ordersInfo']['erp_orders_id'].'</span>
					  </td>
					 </tr>
					 <tr height="10">
					  <td colspan="2" style="font-weight:bold;text-align:right;">
					    '.date('Y-m-d').'
					  </td>
					 </tr>
					 <tr height="50">
					  <td colspan="2" style="font-weight:bold;">
					    '.$allParamArr['skuInfo'].'
					  </td>
					 </tr>
				</table>
		   </div>
		';
		
		
		return $reStr;
	}
	
	//云途马来西亚平邮面单模版
	public function printMalaysiaPingYou_template ($allParamArr)
	{
		$reStr = '';
		
		$reStr .= '
				<style>
				*{margin:0; padding:0;}
				#main{width:100mm; height:129mm; margin:auto;}
				#main_border{width:99mm; height:128mm; margin: 2px auto 0; border:1px solid; overflow: hidden;}
				body{font-size: 10px; font-family:Arial,Helvetica,sans-serif; color:#000000;}
				.f_l{float:left;}
				.f_r{float:right;}
				.address tr th{text-align:left;}
				.address tr td{text-align:right;}
				</style>
				';
		
		$reStr .= '
				<div id="main">
					<div id="main_border">
						<div style="width:98%; margin:auto;">
							<div class="f_l" style="width:145px; font-size: 8px;">
								if undeliverable please return to: Locked
								Bag No: 1329 Special Project Unit POS
								MALAYSIA IN TERNATIONAL HUB 64000
								MALAYSIA
							</div>
			
							<div class="f_r" style="width:133px; border:1px solid; text-align:center; margin:3px 0; line-height:8px;">
								<p>BAYARAN POS JELAS</p>
								<p>POSTAGE PAID</p>
								<p>POST MALAYSIA</p>
								<p>INTERNATIONAL HUB</p>
								<p>MALAYSIA</p>
								<p>PMK 1329</p>
							</div>
		
							<div style="clear: both;"></div>
						</div>
		
						<div style="width:100%; border-top:1px solid; border-bottom:1px solid; line-height:10px;">
							<table style="width:100%; margin:auto;" class="address">
								 <tr>
								  <td align="left">SHIP TO:</th>
								  <th align="right">'.$allParamArr['ordersInfo']['buyer_name'].'</th>
								 </tr>
			
								<tr>
								  <td align="left"></th>
								  <th align="right">'. $allParamArr['ordersInfo']['buyer_address_1'] .' '. $allParamArr['ordersInfo']['buyer_address_2'] .'</th>
								 </tr>
		
							 	<tr>
								  <td align="left"></th>
								  <th align="right">'. $allParamArr['ordersInfo']['buyer_city'] .','. $allParamArr['ordersInfo']['buyer_state'] .','. $allParamArr['ordersInfo']['buyer_zip'] .'</th>
								 </tr>
		
						  		<tr>
								  <td align="left"></th>
								  <th align="right">&nbsp</th>
								 </tr>
							
								<tr>
								  <td align="left"></th>
								  <th align="right">'. $allParamArr['query']['display_name'] . '(' . $allParamArr['query']['country_en'] .')' .'</th>
								 </tr>
		
						  		<tr>
								  <td align="left">Tel:</th>
								  <th align="right">'. $allParamArr['ordersInfo']['buyer_phone'] .'</th>
								</tr>
							</table>
						</div>
		
						<div style="padding:3px;">
							<div class="f_l" style="width:170px;height:50px;"> </div>
		
							<div class="f_l" style="text-align:center;">
								<p style="line-height:12px;"><b>MALAYSIA POST Airmail</b></p>
								 <div>
								 	<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
					  			</div>
								 <p style="line-height:12px;"><b>'. $allParamArr['ordersInfo']['orders_shipping_code'] .'</b></p>
							</div>
		
							<div style="clear: both;"></div>
						</div>
						
						<div style="padding:3px; border-top:1px solid; border-bottom:1px solid;">
							<div class="f_l">
								CUSTOMS DECLARATION
					 		</div>
						
					 		<div class="f_l" style="width:166px; text-align:center;">
								May be opened officially
					 		</div>
						
					 		<div class="f_l">
								<b>CN 22</b>
					 		</div>
						
							<div style="clear: both;"></div>
						</div>
						
						<div style="padding:3px; border-bottom:1px solid;">
							<div class="f_l">
								Postal administration
					 		</div>
						
					 		<div class="f_r">
								Tick as appropriat
					 		</div>
						
							<div style="clear: both;"></div>
						</div>
						
						<div style="border-bottom:1px solid;">
							<div class="f_l" style="padding:5px 10px; border-right:1px solid;">
							 	<input name="wx" type="checkbox">
					 		</div>
						
					 		<div class="f_l" style="padding:4px; width:100px;">
								Gift
					 		</div>
						
					 		<div class="f_l" style="padding:5px 10px; border-right:1px solid; border-left:1px solid;">
							 	<input name="wx" type="checkbox">
					 		</div>
						
							<div class="f_l" style="padding:4px;">
								Commercial sample
					 		</div>
						
							<div style="clear: both;"></div>
						</div>
						
						<div>
							<div class="f_l" style="padding:5px 10px; border-right:1px solid;">
							 	<input name="wx" type="checkbox">
					 		</div>
						
					 		<div class="f_l" style="padding:4px; width:100px;">
								Document
					 		</div>
						
					 		<div class="f_l" style="padding:5px 10px; border-right:1px solid; border-left:1px solid;">
							 	<input name="wx" type="checkbox" checked>
					 		</div>
						
							<div class="f_l" style="padding:4px;">
								Other
					 		</div>
						
							<div style="clear: both;"></div>
						</div>
						
						<table style="width:100%; border-collapse:collapse; border:medium none;" border="1">
					 		<colgroup>
						       <col width="50%">
						       <col width="25%">
						       <col width="25%">
						    </colgroup>
							<tr>
								<td>Quantity and detailed description of<br> contents</td>
						 		<td>Weight (in kg)</td>
						 		<td>Value</td>
							</tr>
						
							<tr>
								<th style="text-align:left;">'. $allParamArr['productsInfo'][0]['products_declared_en'] . ' x ' . $allParamArr['productsInfo'][0]['item_count'] .'</th>
						 		<th>'. $allParamArr['productsInfo'][0]['products_weight'] .'</th>
						 		<th>'. $allParamArr['ordersInfo']['orders_total'] . 'USD' .'</th>
							</tr>
						
							<tr>
								<td></td>
						 		<td>Total Weight (in kg)</td>
						 		<td>Total Value(USD)</td>
							</tr>
						
							<tr>
								<th></th>
						 		<th>'. $allParamArr['productsInfo'][0]['products_weight'] .'</th>
						 		<th>'. $allParamArr['ordersInfo']['orders_total'] .'</th>
							</tr>
						</table>
						
						 <div style="padding:3px;">
						 	<div style="font-size: 8px;">
						 		I,the undersigned,whose name and address are given on the itme, certify that the particulars given in this declaration are correct and that this item does not contain any dangerous article or articles pro-hibited by legislation or by postal or by customs regulations
						 	</div>
						
					 		<div style="text-align:right; width: 97%;">
					 			<b>Flake</b> '. date('Y-m-d') .'
					 		</div>
						 </div>
					 
					 	<div style="padding:3px 6px; border-top:1px solid;">
					 		<div class="f_l">
					 			<div>
				 					<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['erp_orders_id'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
					 			</div>
				 				<p style="text-align:right;">' .$allParamArr['ordersInfo']['erp_orders_id']. '</p>
		 					</div>
				 	
				 			<div class="f_r" style="width:200px;">
				 				【'. $allParamArr['ordersInfo']['shipmentAutoMatched'] .'】  '. $allParamArr['productsInfo']['sku'] .'
				 			</div>
				 	
				 			<div style="clear: both;"></div>
					 	</div>
					</div>
				</div>
				';
		
		return $reStr;
	}
	
	//云途U+平邮电面单模版
	public function printYunTuUPingYou_template ($allParamArr)
	{
		$reStr = '';
		
		$reStr .= '
				<style>
					*{margin:0; padding:0;}
					#main{width:100mm; height:100mm; margin:auto;}
					#main_border{width:99mm; height:99mm; margin: auto; overflow: hidden;}
					body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:12px;}
					.f_l{float:left;}
					.f_r{float:right;}
					.address tr th{text-align:left;}
					.address tr td{text-align:right;}
					div, span, td {word-wrap: break-word;}
					td {word-break: normal;}
				</style>
		
				<style>
					.PageNext{page-break-after:always; clear:both; min-height:1px; height:auto; overflow:auto; width:100%;}
					.float_box{ position: relative; float:left; width:370px; height:364px; overflow:hidden; margin:11px 3px 1px; border:1px solid black;}
				</style>
				';
		
		$reStr .= '
		<div id="main">
			<div id="main_border">
				<table border="1" cellspacing="0" width="100%">
					<tr height="56">
						<td>
						   <table cellpadding="0" cellspacing="0" border="0" width="100%">
						   	<tr align="center">
						   		<td width="112" rowspan="3"><img src="'.site_url('attachments').'/images/post_logo.jpg" width="112" height="34" border="0" /></td>
						   		<td width="120"><strong>航空</strong></td>
						   		<td rowspan="3">
                                	<table cellpadding="0" cellspacing="0" border="0" width="100%">
                                    	<tr align="center">
                                        	<td>
                                            	'. $allParamArr['query']['country_en'] . '(' . $allParamArr['query']['country_cn'] .')' .'
                                            </td>
                                        </tr>
                                        <tr align="center">
                                        	<td><?php $targetcountry=replace(country, $rs[buyer_country]);?>
                                            	<?php echo $targetcountry getShortCountryByzhongyou($targetcountry);?>
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
						   	</tr>
						   	<tr align="center">
						   		<td><strong>Small Packet</strong></td>
						   	</tr>
						   	<tr align="center">
						   		<td><strong>BY AIR</strong></td>
						   	</tr>
						   </table>
						</td>
					</tr>
					<tr>
						<td style="padding-left: 5px;">
							协议客户：<b>福州纵腾网络科技有限公司（35010102179000）</b>
						</td>
					</tr>
					<tr height="56">
						<td>
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
                                   <td width="50" align="right" valign="top">
                                       <strong>FROM:&nbsp;</strong>
                                   </td>
                        
                                   <td colspan="3">
                                       4th.Laoguang Building Shangxiadian RoadCangshan DistricFuZhou CityChina.
                                   </td>
                                </tr>
                        
								<tr>
                                    <td align="right">
                                        <strong>ZIP:&nbsp;</strong>
                                    </td>
                        
                                    <td width="170">
                                        350011
                                    </td>
                        
                                    <td width="40" align="right">
                                         <strong>TEL:&nbsp;</strong>
                                    </td>
                        
                                    <td>
                                         13723779057
                                    </td>
                                </tr>
							</table>
						</td>
					</tr>
					<tr height="120">
						<td valign="top">
							<table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                     <td colspan="3" align="right">CustomerOrderNumber: </td>
                                     <td>'. $allParamArr['ordersInfo']['erp_orders_id'] .'</td>
                                </tr>
		
								<tr>
                                	<td width="40" valign="top" align="right">
                                		<strong>TO:&nbsp;</strong>
                                	</td>
		
                                	<td colspan="3" style="white-space: normal; word-break: keep-all;">
                                		'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
								        '.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
								        '.$allParamArr['ordersInfo']['buyer_city'].' '.$allParamArr['ordersInfo']['buyer_state'].'<br/>
								        '.$allParamArr['ordersInfo']['buyer_country'].'('.$allParamArr['ordersInfo']['buyer_country_code'].')
                                	</td>
                                </tr>
		
								<tr>
                                	<td align="right">ZIP:&nbsp;</td>
		
                                	<td width="100">'.$allParamArr['ordersInfo']['buyer_zip'].'</td>
		
                                	<td width="40" align="right">phone:&nbsp;</td>
		
                                	<td>'.$allParamArr['ordersInfo']['buyer_phone'].'</td>
                                </tr>
		
								<tr><td>&nbsp;</td><td colspan="3" style="white-space: normal; word-break: break-all; font-size:12px;"><?php echo implode(nbsp;, $data);?></td></tr>
							</table>
						</td>
					</tr>
                  
                    <tr>
                        <td>&nbsp&nbsp退件单位： 福州市国际小包收寄处理中心（35000512）</td>
                    </tr>
                  
                    <tr>
						<td style="padding: 5px 0 5px 15px">
	                        <div class="f_l">
                                <div style="border:2px solid; width:110px; line-height: 44px; text-align:center;">No Tracking</div>
                            </div>
                            <div class="f_l">
                                <div style="margin-left:25px;">
	                            	<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		                           	<p style="text-align:center">'. $allParamArr['ordersInfo']['orders_shipping_code'] .'</p>
                           		</div>
                           </div>
 						</td>
                   </tr>
				</table>
 				<div style="width:100%;">【'. $allParamArr['ordersInfo']['shipmentAutoMatched'] .'】  '. $allParamArr['productsInfo']['sku'] .'</div>
			</div>
		</div>';
			
		$reStr .= '
		<div id="main">
			<div id="main_border">
				<table border="1" cellspacing="0" width="100%" style="font-size: 11px;">
					<tr>
						<td colspan="3">
						   <table cellpadding="0" cellspacing="0" border="0" width="100%">
						   	<tr align="center">
						   		<td width="90" rowspan="2"><img src="'.site_url('attachments').'/images/post_logo.jpg" width="112" height="34" border="0" /></td>
						   		<td width="175"><strong>报关签条</strong></td>
						   		<td><strong>邮2113</strong></td>
						   	</tr>
						   	<tr align="center">
						   		<td><strong>CUMTOMS DECLARATION</strong></td>
						   		<td><strong>CN22</strong></td>
						   	</tr>
						   </table>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<div style="width:150px; float:left;">可以经行拆开</div>
							<div style="float: left;">May be opened officially</div>
						</td>
					</tr>
		
					<tr style="line-height:12px;">
						<td colspan="3">
							<table cellspacing="0" border="1" width="100%">
								<tr align="center">
									<td width="102" align="left" rowspan="2">
										<p>邮件种类</p>
										<p>Category of item(在适当的文字前划)"√"</p>
									</td>
		
									<td width="20"></td>
									<td width="80">礼品<br/>gift</td>
									<td width="20">&nbsp;</td>
									<td colspan="3">商品货样<br/>Commercial Sample</td>
								</tr>
		
								<tr align="center">
									<td>&nbsp;</td>
									<td>文件<br/>Documents</td>
									<td width="20"><b style="font-family: 宋体; font-size:16px;">√</b></td>
									<td colspan="3">其他<br/>Other</td>
								</tr>
							</table>
						</td>
					</tr>
			
					<tr style="line-height:12px;">
						<td width="240" style="padding:5px;">
							<b>内件详细名称和数量</b><br/>
							<span style="font-size: 10px;">Quantity and detailed description ofcontents</span>
						</td>
						<td width="70" align="center">重量(千克)<br/>Weight(Kg)</td>
						<td width="60" align="center">价值<br/>Value(USD)</td>
					</tr>
		
					<tr>
						<td width="240">&nbsp;&nbsp; '.$allParamArr['productsInfo'][0]['products_declared_en'] . $allParamArr['productsInfo'][0]['products_declared_cn'] .'
								#'.$allParamArr['productsInfo'][0]['orders_sku'].' * '.$allParamArr['productsInfo'][0]['item_count'].' </td>
						<td width="70" align="center">'. $allParamArr['productsInfo'][0]['products_weight'] .'</td>
						<td width="60" align="center">'. $allParamArr['total_price'] . '</td>
					</tr>
		
					<tr>
						<td width="240" rowspan="2" style="padding:5px;">
						<b>协调系统税则号列和货物原产国(只对商品邮件填写)</b><br/>
						<p>HS tariff number and country of origin of goods(For Commercial items only)</p>
						</td>
						<td align="center">总重量<br/>Total Weight(kg) <P>'. $allParamArr['productsInfo'][0]['products_weight'] .'</P></td>
						<td align="center">总价值<br/>Total Value USD <P>'. $allParamArr['total_price'] . '</P></td>
					</tr>
		
					<tr>
					</tr>
		
					<tr>
						<td colspan="3" style="padding:5px;">
						我保证上述申报准确无误，本函件内未装寄法律或邮件和海关规章禁止寄递的任何危险物品<br/>
						<p style="word-wrap:normal; word-break: keep-all; margin:0; padding:0;">I the undersigned,certify that the particulars given in this declaration are correct and this item does not contain any dangerous articles prohibited by legislation or by postal or customers regulations.</p>
						<p style="white-space:normal; word-break: keep-all; margin:0; padding:0; text-align: center;">寄件人签字 (Date and senders signature(9)): SLME</p>
						</td>
					</tr>
				</table>
			</div>
		</div>';
		
		return $reStr;
	}
	
	//SHS香港平邮面单 模板
	public function SHSHongKongPintYou_template($allParamArr){
		$reStr='<style>body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;margin:0; padding:0;}
						td{ white-space:nowrap;}
						.PageNext{page-break-after:always; clear:both; min-height:1px; height:auto; overflow:auto; width:100%;}
						.float_box{ position: relative; width:370px; height:364px; overflow:hidden; margin:0 auto; border:1px solid black;}
						div, p{margin:0; padding:0;}
				</style>';
		
		$reStr.='<div id="main_frame_box">';
		$reStr.= '<div class="float_box">
					<div align="center" style="width:100%;height:82px;">	
						<div style="padding:7px 5px 0 5px;">
				
							<div style="float:left; width: 200px; text-align:left;">
								<div><b>FROM:</b></div>
								<div style="font-size: 10px;">Rm A1，10/F, Shun Luen Factory Building, 86 Tokwawan Road, Hong Kong，</div>
							</div>
					
							<div style="float: right;">
								<table cellspacing="0" border="1" height="62">
									<tr>
										<td>
											<p style="font-size:24px;">1</p>
										</td>
										<td align="center">
											<div style="font-size: 10px; padding:0 2px; font-weight:bold;">
												POSTAGE<br>
												PAID<br>
												HONG KONG
											<div>
										</td>
										
										<td align="center">
											<div style="font-size: 10px; padding:0 5px; font-weight:bold;">
												PERMIT<br>
												NO.<br>
												<br>
												5743
											<div>
										</td>
									</tr>
								</table>
							</div>
							<div style="clear:both;"></div>
						</div>
					</div>	
					<div style="width:100%;height:95px;">
						<table style="width:368px;">
							<tr>
								<td align="left">
									<strong style="font-size:15px;text-decoration:underline;margin-left:0px;">TO:</strong>
									<strong style="font-size:15px;margin-left:5px;">'.$allParamArr['ordersInfo']['buyer_name'].'</strong>	
									<span style="font-size:13px;margin-left:5px;">';
									if(!empty($allParamArr['ordersInfo']['buyer_address_1'])){
										$reStr .= '<br>'.$allParamArr['ordersInfo']['buyer_address_1'];
									}
									if(!empty($allParamArr['ordersInfo']['buyer_address_2'])){
										$reStr .= '<br>'.$allParamArr['ordersInfo']['buyer_address_2'];
									}
									$reStr .= '
										<br> '.$allParamArr['ordersInfo']['buyer_city'].' 
										&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_state'].' 
										<br> Zip:'.$allParamArr['ordersInfo']['buyer_zip'].'
										<br> Tel:'.$allParamArr['ordersInfo']['buyer_phone'].' 
									</span>									
								</td>
								<td align="right">
									<div style="width:80px;height:90px;border:2px solid #000;font-weight:bold;">
 				     					<table>
											<tr>
												<td>
													<img width="80px;" height="70px;" style="margin-top:-3px;margin-left:-3px;" src="'.site_url('attachments').'/images/mddHkPost2.jpg"/>
												</td>
											</tr>
											<tr>
												<td align="center">
													<span style="font-size:16px;">
														' . $allParamArr['partition'] . '
													</span>
												</td>
											</tr>
										</table>
 				  					</div>
								</td>
							</tr>
						</table>
					</div>	
					<div style="width:50px;height:28px;border:1px solid #000;margin-left:5px;margin-top:5px;">
 				     	<strong style="font-size:28px;" >'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'</strong>				
 				  	</div>	
					<div style="width:100%;height:20px;margin-left:5px;margin-top:10px;">
						<span>'.$allParamArr['ordersInfo']['buyer_country'].'&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['country'].'</span>
					</div>	
					<div>
						<table style="width:370px; border: solid thin #000;BORDER-left: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;BORDER-bottom: rgb(0,0,0) 1px;">
							<tr>
								<td align="center" colspan="2">
									<strong>S'.$allParamArr['ordersInfo']['erp_orders_id'].'</strong>
								</td>
							</tr>							
						</table>
						<table style="width:370px; border: solid thin #000;BORDER-left: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;">
							<tr>
								<td align="center" colspan="2">
									<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=20&r=2&text=S'.$allParamArr['ordersInfo']['erp_orders_id'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
								</td>
							</tr>
						</table>
						<table height="52px;" style="border: solid thin #000;BORDER-left: rgb(0,0,0) 1px;BORDER-top: rgb(0,0,0) 1px;BORDER-bottom: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;border-collapse:collapse;BORDER-right: rgb(0,0,0) 1px;">
								<tr>				
									<td align="left">
										<span>BAM</span>
									</td>											
								
									<td rowspan="2" align="left" style="border: solid thin #000;BORDER-top: rgb(0,0,0) 1px;BORDER-bottom: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;">
										<textarea readonly="readonly" style=" font-size:13px; height: 45px; width: 232px; margin-top:-1px;margin-left:-1px;resize:none;border: solid thin #000;BORDER-left: rgb(0,0,0) 1px;BORDER-top: rgb(0,0,0) 1px;BORDER-bottom: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;overflow-y:hidden" rows="3">';
										foreach ($allParamArr['productsInfo'] as $product){
											$reStr .= $product['orders_sku'].'*'.$product['item_count'].'【'.$product['products_location'].'】';
										}
										$reStr .= '</textarea>
									</td>
								</tr>
								<tr>
									<td align="left" style="border: solid thin #000;BORDER-left: rgb(0,0,0) 1px;BORDER-bottom: rgb(0,0,0) 1px;BORDER-right: rgb(0,0,0) 1px;">
										<span>Ref NO:S'.$allParamArr['ordersInfo']['erp_orders_id'].'</span>
									</td>																										
								</tr>
						</table>						
					</div>										
			     </div>';
		$reStr.='</div>';

		$reStr.= $this->baoguan_template($allParamArr);
		
		return $reStr;
	}
	
	//杭州小包ZJ挂号面单 模板
	public function printForHangZhouXiaoBao_template ($allParamArr)
	{
		
		$reStr = '
				<style>
		    *{margin:0;padding:0;}
			body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
			#main_frame_box{width:99.5mm;height:99.5mm;margin:2px auto 0; overflow:hidden;}
			table{border-collapse:collapse;border:none;width:99mm;height:99mm;}
			table .detail{
				width:374px;height:93px;border:none;
			}
			td{border:1px solid #000;}
		</style>
				';
		
		$reStr .='
		   <div id="main_frame_box">
		    <table>
		      <tr height="60px">
		        <td colspan="2">
		          <p style="width:34mm;height:60px;float:left;text-align:center;">
		            <img src="'.site_url('attachments').'/images/post_logo.jpg" style="width:34mm;height:30px;"/><br/>
		            <span style="font-size:11px;">Small Packet By AIR</span><br/>
		            '.($allParamArr['ordersInfo']['buyer_country_code'] ? $allParamArr['ordersInfo']['buyer_country_code'] : $allParamArr['ordersInfo']['buyer_country']).'
		          </p>
		          <p style="width:56mm;height:60px;float:left;text-align:center;">
		          	<span style="margin-top:5px;display:inline-block;">
		          	   <img  src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" style="height:40px;"><br>
		          	  '.$allParamArr['ordersInfo']['orders_shipping_code'].'
		          	</span>
		          </p>
		
		        </td>
		      </tr>
		      <tr height="15px">
		        <td colspan="2">
		          <p style="padding-left:5px;font-size:11px;">协议客户:</p>
		        </td>
		      </tr>
		      <tr height="75px">
		        <td width="35%">
		          <p style="padding-left:5px;font-size:9px;width:125px;over-flow:hidden;">
		            FROM:OS3<br/>
		          	Yuemei group A warehouse,<br/>
          	   		FL.3,7# qianxi road<br/>
          	   		zhuji,shaoxing<br/>
          	   		Zhejiang<br/>
          	   		China<br/>
          	   		311800,<br/>
		          </p>
		        </td>
		        <td width="65%" rowspan="2">
		          <p style="padding-left:5px;font-size:12px;">
		            <span style="font-weight:bold;font-size:14px;">SHIP TO:</span><br/>
		            '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
			        '.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
			        '.$allParamArr['ordersInfo']['buyer_city'].' '.$allParamArr['ordersInfo']['buyer_state'].'<br/>
			        '.$allParamArr['ordersInfo']['buyer_zip'].'
			        '.$allParamArr['ordersInfo']['buyer_country'].'<br/>
			        Phone:'.$allParamArr['ordersInfo']['buyer_phone'].'&nbsp;&nbsp;&nbsp;'.$allParamArr['country'].'
		          </p>
		        </td>
		      </tr>
		      <tr height="35px">
		        <td>
		          <p style="padding-left:5px;font-size:11px;">
		            	自编号:<br/>
		            	'.$allParamArr['ordersInfo']['erp_orders_id'].'
		          </p>
		        </td>
		      </tr>
		      <tr height="15px">
		        <td colspan="2">
		          <div style="padding:0 5px;font-size:11px;">
		          	<div style="float:left;">退件单位:OS3</div>
		             <table style="height:20px; width:250px;float:right;">
					  <tr>
					    <td width="30px">√</td>
					    <td width="50px">Gift</td>
					    <td width="30px"></td>
					    <td width="110px">Commercial sample</td>
					  </tr>
					</table>
		            			<div style="clear:both;"></div>
		          </div>
		        </td>
		      </tr>
		      <tr height="95px">
		        <td colspan="2">
		          <table class="detail">
		            <tr height="20px">
		              <td width="60%">
		                <p style="padding-left:5px;font-size:11px;">description of contents</p>
		              </td>
		              <td width="20%">
		               <p style="padding-left:10px;font-size:11px">Kg</p>
		              </td>
		              <td width="20%">
		               <p style="padding-left:5px;font-size:11px">Val(US $)</p>
		              </td>
		            </tr>
		            <tr hegiht="53px">
		              <td width="60%">
		                <p style="padding-left:5px;font-size:11px;height:42px;overflow:hidden;">
		                '.$allParamArr['productsInfo']['sku'].'
		                </p>
		              </td>
		              <td width="20%">
		                <p style="font-size:11px;text-align:center;">
		                 '.$allParamArr['productsInfo']['totalWeight'].'
		                </p>
		              </td>
		              <td width="20%">
		                <p style="font-size:11px;text-align:center;">
		                 '.$allParamArr['productsInfo']['totalValue'].'
		                </p>
		              </td>
		            </tr>
		            <tr height="20px">
		              <td width="60%">
		                <p style="padding-left:5px;font-size:11px;">
		                 Total Gross Weight(Kg)
		                </p>
		              </td>
		              <td width="20%">
		                <p style="font-size:11px;text-align:center;">
		                 '.$allParamArr['productsInfo']['totalWeight'].'
		                </p>
		              </td>
		              <td width="20%">
		                 <p style="font-size:11px;text-align:center;">
		                 '.$allParamArr['productsInfo']['totalValue'].'
		                </p>
		              </td>
		            </tr>
		          </table>
		        </td>
		      </tr>
		      <tr height="55px">
		        <td colspan="2">
		         <p style="font-size:11px;padding-left:5px;">
		          	I the undersigned,certify that the particulars given in this declaration are correct and this item does not contain any
		            dangerous articles prohibited by legislation or by postal or customers regulations.
		         </p>
		         <p style="padding-left:5px;font-size:11px;">
		        	 <span> Sender\'s signature</span>
		        	 <span style="padding-left:15px;">SLME</span>
		        	 <span style="padding-left:100px;">CN22</span>
		         </p>
		        </td>
		      </tr>
		    </table>
		  </div>
		';
		return $reStr;
	}
	
	/**
	 * 【平邮】燕文燕邮宝(俄)模板处理
	 */
	public function PrintPingyouYWRussiaTemplate($allParamArr){
		$reStr ='
			<style>
			   *{margin:0;padding:0;}
				body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
				#main_frame_box{width:99mm;height:91mm;margin:0px auto;overflow:hidden;border:1px solid #000;}
				#top{
				 width:100%;
				 height:93px;
				}
			</style>
		';
		
		$reStr .='
			 <div id="main_frame_box">
		    <div id="top">
		      <p style="padding-left:50px;">YANWEN</p>
		      <div style="width:100%;height:75px;">
			      <div style="width:81%;float:left;height:75px;text-align:center;">
			         <br/>
			         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			          <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" /><br/>
			        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
			          '.$allParamArr['ordersInfo']['orders_shipping_code'].'
			      </div>
			      <div style="float:left;width:16%;height:75px;font-size:46px;">
			        2
			      </div>
		      </div>
		      
		    </div>
		    <div style="width:96%;margin:0 auto;border-top:2px solid #000;border-bottom:2px solid #000;text-align:center;height:15px;">
		        Y-POST  俄罗斯('.$allParamArr['ordersInfo']['erp_orders_id'].')
		    </div>
		    <div style="width:100%;height:150px;">
		      <p style="width:98%;margin-left:8px;">
		        TO:<br/>
			        '.$allParamArr['ordersInfo']['buyer_name'].' '.$allParamArr['ordersInfo']['buyer_phone'].'<br/>'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
			        '.$allParamArr['ordersInfo']['buyer_city'].' '.$allParamArr['ordersInfo']['buyer_state'].'<br/>
			        '.$allParamArr['ordersInfo']['buyer_zip'].'<br/>
			        '.$allParamArr['buyerCountry'].'<br/>
			        '.$allParamArr['country_cn'].'
		      </p>
		    </div>
		    <div style="widht:100%;height:40px;">
		       <p style="width:120px;height:40px;margin-left:200px;text-align:center;border:1px solid #000;">
		         <span style="height:20px;display:inline-block;border-bottom:1px solid #000;width:120px;">zone</span>  <br/>
		       '.$allParamArr['ordersInfo']['areaCode'].'
		       </p>
		    </div>
		    <div style="widht:100%;height:40px;margin-top:0px;">
		      <p style="width:120px;height:40px;margin-left:253px;text-align:center;text-align:right;">
		        <span style="width:100px;display:inline-block;text-align:center;">302035</span><br/>
		        <span style="font-weight:bold;font-size:16px;">'.date('Y-m-d').'</span>
		      </p>
		    </div>
		  </div>
		  <div style="width:99mm;height:30px;margin:0 auto;font-size:11px;overflow:hidden;margin-bottom:2px;">
		     '.$allParamArr['skufiles'].'
		   </div>
		';
		
		return $reStr;
	}
	
	//打印爱莎尼亚小包平邮模板
	public function PrintPingyouEstonia_template ($allParamArr)
	{
		$reStr = '';
	
		$reStr .= '
				<style>
				*{margin:0; padding:0;}
				#main{width:100mm; height:129mm; margin:auto;}
				#main_border{width:99mm; height:128mm; margin: 2px auto 0; border:1px solid; overflow: hidden;}
				body{
					font-size: 8px;
					font-family: Arial,Helvetica,sans-serif;
					color: #000000;
					}
				.f_l{float:left;}
				.f_r{float:right;}
				table{width:100%;border-collapse:collapse;border:none;}
				table,table td{border:1px solid black;}
				</style>
				';
	
		$reStr .= '
				<div id="main">
					<div id="main_border">
						<table style="margin:auto;">
							<tr>
								<td align="center" style="font-size: 10px;">
									Petit Paquet
								</td>
							</tr>
							<tr>
								<td>
									<div class="f_l">
										if undeliverable please return to:	<br>
										P.O. Box 7023,	<br>
										14002 Tallinn,	<br>
										Estonia
									</div>
				
									<div class="f_r" style="width: 80px;">
										<img src="'.site_url('attachments').'/images/EEPlogo.png" style="width:13mm;height:10mm;"/>
									</div>
				
									<div style="clear: both;"></div>
								</td>
							</tr>
				
							<tr>
								<td style="font-weight: bold;">
									<div>
										<div class="f_l" style="width: 50px;" align="right">
											<p>TO:</p>
										</div>
					
										<div class="f_l" style="font-size: 12px;">
											<p>' . $allParamArr['ordersInfo']['buyer_name'] . '</p>
											<p>'. $allParamArr['ordersInfo']['buyer_address_1'] .' '. $allParamArr['ordersInfo']['buyer_address_2'] .'</p>
											<p>'. $allParamArr['ordersInfo']['buyer_city'] .','. $allParamArr['ordersInfo']['buyer_state'] .'</p>
											<p>'. $allParamArr['ordersInfo']['buyer_zip'] .'</p>
											<p style="font-size: 12px;">'.$allParamArr['ordersInfo']['buyer_country'] .'</p>
										</div>
										<div style="clear: both;"></div>
									</div>
													
									<div style="width: 280px; font-size: 10px; text-align:right;">Tel:'. $allParamArr['ordersInfo']['buyer_phone'] .'</div>
								</td>
							</tr>
											
							<tr>
								<td align="center">
									<div>
										<p style="text-align: left;">(place for applying the customs declaration CN 22)</p>
											
										<div class="f_r" style="width: 220px;">
											<div class="f_l"><b>Tafiff</b></div>
											<div class="f_l" style="border-bottom: 2px solid; width: 130px;">&nbsp</div>
											<div style="clear: both;"></div>
										</div>
										<div style="clear: both;"></div>
										
										<p>(calendar stamp imprint of the place of</p>
										<p>acceptance</p>
									</div>
								</td>
							</tr>
											
							<tr>
								<td align="center">
									<p>'.$allParamArr['ordersInfo']['orders_shipping_code'].'</p>
									<div>
								 		<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=17&r=2&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
					  				</div>
								</td>
							</tr>
								 				
			 				<tr>
								<td>
									<p>customer code: test</p>
					 				<p>Reference ID:</p>
								</td>
							</tr>
								 				
			 				<tr>
								<td>

									<div class="f_l" style="width: 130px; height:20px;">
								 		<p>CUSTOMS</p>
						 				<p>DECLARATION</p>
								 	</div>
								 				
								 	<div class="f_l" style="width: 130px;height:20px;">
								 		<p>May be opened</p>
						 				<p>offcially</p>
								 	</div>
								 				
								 	<div class="f_l">
								 		<p>CN 22</p>
								 	</div>
					 				<div style="clear: both;"></div>
								</td>
							</tr>
								 				
			 				<tr>
								 
								<td>
					 				<div class="f_l" style="width: 49%;">
										<p>Designated operator</p>
							 		</div>		
								 				
								 	<div class="f_l" style="border: 1px solid; height:26px;"></div>
								 				
					 				<div class="f_l" style="width: 49%;">
						 				<p>Important!See instructionson the</p>
						 				<p>back</p>
					 				</div>
								</td>
							</tr>
								 				
			 				<tr>
								<td>
									<table>
						 				<tr>
											<td>&nbsp</td>
							 				<td>
												Gift
											</td>
							 				<td></td>
							 				<td>
												Commercial sample
											</td>
										</tr>
								 				
						 				<tr>
											<td>
												
											</td>
							 				<td>
												Documents
											</td>
							 				<td>
												√
											</td>
							 				<td>
												Other
											</td>
										</tr>
								 	</table>
								</td>
							</tr>
								 				
			 				<tr>
								<td align="right">
									Tick one or more boxes
								</td>
							</tr>
								 				
			 				<tr>
								<td>
									<table border="1">
						 				<tr>
											<td>
								 				<p>Quantity and detailed descriptionof</p>
								 				<p>contents(1)</p>
							 				</td>
							 				<td>Weight(in kg)(2)</td>
							 				<td>Value(3)</td>
										</tr>
								 				
						 				<tr>
											<td align="center" style="font-size:10px;">charger</td>
							 				<td>'. $allParamArr['productsInfo'][0]['products_weight'] .'</td>
							 				<td>$'. $allParamArr['ordersInfo']['orders_total'] . '</td>
										</tr>
								 				
						 				<tr>
											<td>
								 				<p>For commercial items only IF known.HS</p>
								 				<p>tariff number (4) and country of origin of</p>
						 						<p>goods(5)</p>
							 				</td>
							 				<td>Total weight(in kg)(6)</td>
							 				<td>Total value()(7)</td>
										</tr>
							 						
				 						<tr>
											<td></td>
							 				<td>'. $allParamArr['productsInfo'][0]['products_weight'] .'</td>
							 				<td>$'. $allParamArr['ordersInfo']['orders_total'] . '</td>
										</tr>
								 	</table>
								</td>
							</tr>
								 				
	 						<tr>
								<td>
									I,the undersigned,whose name and address are given on the item,certify that the particulars given in this
									declaration are correct and that this item does not contain any dangerous article or articles prohibited by
									legislation or by postal or customs regulations date and sender\'s signature(8)&nbsp;&nbsp;'.$allParamArr['ordersInfo']['erp_orders_id'].' 
								</td>
							</tr>
						</table>
							 						
						<div>包装内容：【'. $allParamArr['ordersInfo']['shipmentAutoMatched'] .'】  '. $allParamArr['productsInfo']['sku'] .'</div>
					</div>
				</div>';
	
		return $reStr;
	}
	
	public function LazadaMyTemplate($allParamArr){
	    $reStr = '
		<style>
		    *{margin:0;padding:0;}
			body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
			#main_frame_box{width:99mm;height:125mm;margin:0 auto; overflow:hidden;font-size:12px;border:1px solid #000;}
			table{border-collapse:collapse;border:none;width:99mm;height:99mm;border:1px solid black;}
			table .detail{
				width:380px;height:93px;border:none;
			}
			td{border:1px solid #000;}
		</style>
		';
	     $reStr .='<div style="width:99mm;margin:0 auto;height:4mm;text-align:right;">AS-Poslaju&nbsp;&nbsp;&nbsp;</div>';
	     $reStr .='
		   <div id="main_frame_box">';
	     
	     $reStr .='
	       <div style="width:100%;height:50mm;">
	         <p style="height:1mm;"></p>
	         <p style="height:16mm;">
	           &nbsp;&nbsp;&nbsp;
	            <span style="display:inline-block;width:45mm;height:50px;">
	              <img src="'.site_url('attachments').'/images/poslaju_logo2.png" style="width:45mm;height:50px;"/>
	            </span>
	            <span style="display:inline-block;width:47mm;backgrond:red;text-align:center;font-weight:bold;">
	              <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
	              <br/> '.$allParamArr['ordersInfo']['orders_shipping_code'].'
	            </span>
	         </p>
	         <p style="height:33mm;">
	           <span style="width:60mm;height:33mm;display:inline-block;float:left;">
	            &nbsp;&nbsp;&nbsp;From:<br/>
	             &nbsp;&nbsp;&nbsp;'.$allParamArr['shipName'].'<br/>
	             &nbsp;&nbsp;&nbsp;Logistics Worldwide Express<br/>    
	             &nbsp;&nbsp;&nbsp;Block A, G Floor, GL06<br/>
	             &nbsp;&nbsp;&nbsp;Kelana Square, 17 Jalan SS7/26<br/>
	             &nbsp;&nbsp;&nbsp;Petaling Jaya<br/>
	             &nbsp;&nbsp;&nbsp;47301&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Selangor<br/>
	             &nbsp;&nbsp;&nbsp;MALAYSIA &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tel: 60378038830
	           </span>
	           <span style="width:38mm;height:33mm;text-align:center;display:inline-block;font-size:14px;font-weight:bold;float:right;">
	           		<br/>
	            	 POS LAJU ACC #<br/>
					8800400431【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】
	           </span>
	         </p>
	       </div>
	       <hr style="height:1px;border:none;border-top:1px solid #000;" />
	       <div style="width:100%;height:32mm;">
	       	   <span style="width:60mm;height:32mm;display:inline-block;float:left;overflow:hidden;">
	            &nbsp;&nbsp;&nbsp;To:<br/>
	             &nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
	             &nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_address_1'].'<br/> 
	             &nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
	             &nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_city'].'<br/>
	             &nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_zip'].'
	             					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	             					'.$allParamArr['ordersInfo']['buyer_state'].'
	             <br/>
	             &nbsp;&nbsp;&nbsp;'.$allParamArr['buyerCountry'].'
	             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	             Tel：'.$allParamArr['ordersInfo']['buyer_phone'].'
	            
	           </span>
	           <span style="width:38mm;height:27mm;text-align:center;display:inline-block;font-size:14px;font-weight:bold;float:right;">
	           		<br/>
	           		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	           		'.$allParamArr['ordersInfo']['erp_orders_id'].'
	           		<br/><br/>
	           		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	            	 <b style="font-size:26px;border:1px solid #000;">MY</b>
	           </span>
	       </div>
	       <hr style="height:1px;border:none;border-top:1px solid #000;" />
	       <div style="width:100%;height:43mm;">
	         <p style="width:100%;height:18mm;">
	            <span style="width:55mm;height:18mm;display:inline-block;float:right;">
	            	&nbsp;&nbsp;&nbsp;Transaction Ref: '.$allParamArr['ordersInfo']['buyer_id'].'<br/><br/>
	                &nbsp;&nbsp;&nbsp;Product: Charges - Domestic<br/> 
	                &nbsp;&nbsp;&nbsp;Type: MERCHANDISE
	           </span>
	           <span style="width:43mm;height:18mm;display:inline-block;float:right;">
	           		&nbsp;&nbsp;&nbsp;Item Information<br/>
	           		&nbsp;&nbsp;&nbsp;Date:'.date('Y-m-d').'<br/><br/>
	           		&nbsp;&nbsp;&nbsp;Weight：'.$allParamArr['productsInfo']['total_weight'].'
	           </span>
	         </p>
	         <p style="width:98%;height:23mm;margin:0 auto;">
	               Please use the number above to track the shipment status through Customer Service Center (Posline) 1-300-300-300 or Pos Malaysia web at www.pos.com.my Note:Liability of PosLaju for any delay, damage or lost be limited to and subject to the terms and conditions as stated behind the consignment note (PL1A)
	         </p>
	       </div>
	     ';
	     $reStr .='</div>';
	     return $reStr;
	}
	
	//云途广州平邮模板处理
public function YunTuGuangZhouTemplate($allParamArr){
	    $reStr = '
		<style>
		    *{margin:0;padding:0;}
			body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
			#main_frame_box{width:99mm;height:129mm;margin:0 auto; overflow:hidden;font-size:12px;}
			table{border-collapse:collapse;border:none;width:99mm;height:99mm;border:1px solid black;}
			table .detail{
				width:380px;height:93px;border:none;
			}
			td{border:1px solid #000;}
		</style>
		';
	    $reStr .='
		   <div id="main_frame_box">
		    <table style="height:55mm;border-bottom:none;">
		      <tr style="height:30px;">
		        <td style="border-right:none;border-bottom:1px solid black;">
		          <p style="width:30mm;text-align:center;height:30px;">
		            <img src="'.site_url('attachments').'/images/post_logo.jpg" style="width:30mm;height:30px;"/>
		          </p>
		       </td>
		       <td style="border-left:none;border-right:none;border-bottom:1px solid black;">
		          <p style="width:39mm;height:30px;text-align:center;">
		          	<span style="display:inline-block;font-size:11px;">
		          	   航  空<br/>Small Packet BY AIR      
		          	</span>
		          </p>
		        </td>
		        <td style="border-left:none;border-bottom:1px solid black;border-right:0px;">
		          <p style="width:25mm;height:30px;text-align:center;">
		          	 <span style="font-size:14px;font-weight:bold;">(34#P)</span><br/>
		          	 <span style="font-size:11px;">'.$allParamArr['country_code'].' '.$allParamArr['country_cn'].'</span>
		          </p>
		        </td>
		      </tr>
		      <tr style="height:35px;font-size:11px;font-weight:bold;">
		        <td  style="border-bottom:none;border-top:none;" colspan="3">
		       	    协议客户:'.$allParamArr['senderInfo']['name'].'
		          <p style="padding-left:5px;font-size:12px;height:30px;">
		            FROM:'.$allParamArr['senderInfo']['street'].'
		          </p>
		        </td>
		     </tr>
		     
		     <tr style="height:110px;font-size:12px;">
		        <td colspan="3" style="word-break: break-all; word-wrap:break-word;border-bottom:none;">
		          <p style="padding-left:5px;height:110px;white-space:normal;overflow:hidden;">
		            <span style="font-size:13px;">TO:</span>
		            '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_city'].' '.$allParamArr['ordersInfo']['buyer_state'].'<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;">'.$allParamArr['country_Info']['country_cn'].'&nbsp;&nbsp;&nbsp;平邮'.$allParamArr['country_Info']['zone'].'区</span><br/>
Zip:'.$allParamArr['ordersInfo']['buyer_zip'].'	 Tel:'.$allParamArr['ordersInfo']['buyer_phone'].'<br/>
    <span style="font-weight:bold;">退件单位:'.$allParamArr['senderInfo']['back_street'].'</span> 
		          </p>
		        </td>			    
		      </tr>
		      <tr height="40px">
		        <td colspan="5">
		 		     <p style="width:10px;font-size:14px;text-align:center;line-height:40px;height:40px;float:left;font-weight:bold;margin-left:15px;">
		 		              	Untracked
		 		     </p>
		 		     <p style="width:260px;height:40px;float:right;text-align:center;font-size:11px;">
		 		                <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=30&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		 		                <br/>
		 		                '.$allParamArr['ordersInfo']['orders_shipping_code'].'
		 		      </p>
		        </td>
		      </tr>	
		    </table>
		';
	    $reStr .='
	   		 <table style="width:375px;height:210px;margin:0;padding:0;">
				    <tr height="25" style="border-top:none;">
				      <td colspan="3">
				        <p style="float:left;width:100px;height:25px;line-height:25px;font-size:12px;text-align:center;">
				           <img src="'.site_url('attachments').'/images/post_logo.jpg" style="width:100px;height:23px;"/>
				        </p>
				        <p style="float:left;width:190px;height:25px;font-size:10px;text-align:center;">
				           	报关签条<br/>
				            CUMTOMS DECLARATION
				        </p>
				        <p style="float:left;width:70px;height:25px;font-size:10px;text-align:center;">
				                         邮2113<br/>
				           CN22a
				        </p>
				      </td>
				    </tr>
				    <tr height="15">
				      <td colspan="3">
				        <p style="float:left;width:170px;line-height:15px;height:10px;font-size:11px;">
				           	可以经行拆开
				        </p>
				        <p style="float:left;width:190px;line-height:15px;height:10px;font-size:11px;">
				           	May be opened officially
				        </p>
				        
				      </td>
				    </tr>
				    <tr height="50">
				      <td colspan="3">
				         <table style="width:375px;height:50px;margin:0;padding:0;border:none;">
				            <tr height="25">
				              <td width="60" style="border-top:none;border-bottom:none;border-left:none;" rowspan="2">
				                <p style="height:20px;font-size:8px;">
				                  	邮件种类(category of item,在适当文字打√)
				                </p>
				              </td>
				              <td width="30" style="border-top:none;">
				                <p style="height:20px;text-align:center;font-size:8px;font-weight:bold;">
				                  	
				                </p>
				              </td>
				              <td width="80" style="border-top:none;">
				                <p style="height:20px;font-size:8px;">
				                  	礼品<br/>
				                  	gift
				                </p>
				              </td>
				              <td width="30" style="border-top:none;"></td>
				              <td width="174" style="border-top:none;border-right:none;">
				              	<p style="height:20px;font-size:8px;">
				                  	商品货样<br/>
				                  	Commercial Sample
				                </p>
				              </td>
				            </tr>
				            <tr height="25">
				              <td width="30" style="border-bottom:none;">
				              	
				              </td>
				              <td width="80" style="border-bottom:none;">
				               <p style="height:20px;font-size:8px;">
				                  	 文件<br/>
				                  	Documents
				                </p>
				              </td>
				              <td width="30" style="border-bottom:none;">
				              √
				              </td>
				              <td width="174" style="border-bottom:none;border-right:none;">
				              	<p style="height:20px;font-size:8px;">
				                  	 其他<br/>
				                  	Other
				                </p>
				              </td>
				            </tr>
				         </table>
				      </td>
				    </tr>
				    <tr height="25">
				      <td width="225">
				        <p style="height:25px;font-size:8px;text-align:center;">
				        	内件详细名称和数量<br/>
				        	Quantity and detailed description ofcontents
				        </p>
				      </td>
				      <td width="80">
				        <p style="height:25px;font-size:8px;text-align:center;">
				        	重量(千克)<br/>
				        	Weight(Kg)
				        </p>
				      </td>
				      <td width="70">
				        <p style="height:25px;font-size:8px;text-align:center;">
				        	价值<br/>
				        	Value
				        </p>
				      </td>
				    </tr>
				    
				   <tr height="15">
				      <td width="225">
				        <p style="height:15px;font-size:11px;text-align:center;">
				        	'.$allParamArr['productsInfo'][0]['products_declared_en'].'
				        	'.$allParamArr['productsInfo'][0]['products_declared_cn'].'
				        	X'.$allParamArr['productsInfo'][0]['item_count'].'
				        </p>
				      </td>
				      <td width="80">
				        <p style="height:15px;font-size:11px;text-align:center;line-height:15px;">
				        	'.$allParamArr['productsInfo'][0]['item_count']*$allParamArr['productsInfo'][0]['products_weight'].'
				        </p>
				      </td>
				      <td width="70">
				        <p style="height:15px;font-size:11px;text-align:center;line-height:15px;">
				        	'.($allParamArr['ordersInfo']['orders_total'] > 20 ? 20 : $allParamArr['ordersInfo']['orders_total']).'USD
				        </p>
				      </td>
				    </tr>
				    <tr height="20">
				      <td rowspan="2">
				        <p style="height:40px;font-size:8px;">
				        	协调系统税则号列和货物原产国(只对商品邮件填写)<br/>
				        	HS tariff number and country of origin of goods(For Commercial items only)
				        </p>
				      </td>
				       <td width="80">
				        <p style="height:20px;font-size:8px;text-align:center;">
				        	重量(千克)<br/>
				        	Weight(Kg)
				        </p>
				      </td>
				      <td width="70">
				        <p style="height:20px;font-size:8px;text-align:center;">
				        	价值<br/>
				        	Value
				        </p>
				      </td>
				    </tr>
				    <tr height="20">
				      <td>
				        <p style="height:20px;font-size:10px;text-align:center;line-height:20px;">
				        	'.$allParamArr['productsInfo'][0]['item_count']*$allParamArr['productsInfo'][0]['products_weight'].'
				        </p>
				      </td>
				      <td>
				       <p style="height:20px;font-size:10px;text-align:center;line-height:20px;">
				        	'.($allParamArr['ordersInfo']['orders_total'] > 20 ? 20 : $allParamArr['ordersInfo']['orders_total']).'USD
				        </p>
				      </td>
				    </tr>
				    <tr height="50">
				      <td colspan="3">
				         <p style="height:90px;font-size:8px;">
				        	我保证上述申报准确无误，本函件内未装寄法律或邮件和海关规章禁止寄递的任何危险物品
							I the undersigned,certify that the particulars given in this declaration are correct and this item does not contain any dangerous articles prohibited by legislation or by postal or customers regulations.
							<br/><span style="padding-left:80px;">
								寄件人签字 Sender\'s signature:李
								</span>
								<span style="display:inline-block;text-align:center;font-size:9px;">
									<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=35&r=1&text='.$allParamArr['ordersInfo']['erp_orders_id'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
									<br/>'.$allParamArr['ordersInfo']['erp_orders_id'].'
								</span>
				         </p>
				      </td>
				    </tr>
				  </table>
	    ';
	    $reStr .='</div>';
	   
	    return $reStr;
	}
	
	//燕文北京挂号模板处理
	public function printYWBejiingGH($allParamArr){
	    $reStr = '
		<style>
		    *{margin:0;padding:0;}
			body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
			#main_frame_box{width:99mm;height:99.5mm;margin:0 auto; overflow:hidden;}
			table{border-collapse:collapse;border:none;width:99mm;height:99mm;border:2px solid black;}
			table .detail{
				width:380px;height:93px;border:none;
			}
			td{border:1px solid #000;}
		</style>
		';
	    $reStr .='
		   <div id="main_frame_box">
		    <table style="height:89mm">
		      <tr height="30px">
		        <td style="border-right:none;border-bottom:2px solid black;">
		          <p style="width:30mm;height:30px;float:left;text-align:center;">
		            <img src="'.site_url('attachments').'/images/post_logo.jpg" style="width:34mm;height:30px;"/><br/>
		          </p>
		       </td>
		       <td style="border-left:none;border-right:none;border-bottom:2px solid black;">
		          <p style="width:39mm;height:30px;float:left;text-align:center;">
		          	<span style="display:inline-block;margin-top:-5px;font-size:11px;">
		          	   航  空<br/>Small Packet BY AIR      
		          	</span>
		          </p>
		        </td>
		        <td style="border-left:none;border-bottom:2px solid black;border-right:0px;" colspan="2">
		          <p style="width:16mm;height:30px;float:left;line-height:30px;font-size:14px;font-weight:bold;">
		            '.( $allParamArr['country']?$allParamArr['country']:$allParamArr['ordersInfo']['buyer_country_code']).'
		          </p>
		        </td>
		        <td style="border-left:none;border-bottom:2px solid black;border-left:0px;" colspan="2">
		          <p style="width:10mm;height:30px;float:left;line-height:30px;">
		            '.($allParamArr['postInfo']['sortingCode']?$allParamArr['ordersInfo']['buyer_country_code'].$allParamArr['postInfo']['sortingCode']:'').'
		          </p>
		        </td>
		      </tr>
	          <tr style="height:10px">
		                <td colspan="5" style="font-size:12px;padding-left:5px;border-bottom:2px solid black;">协议客户:'.$allParamArr['backList']['consumer_name'].'</td>
		      </tr>
		      <tr style="height:10px">
		        <td  colspan="6" style="border-bottom:none;border-top:none;height:10px">
		          <p style="padding-left:5px;font-size:12px;height:10px;">
		            FROM:'.$allParamArr['backList']['consumer_from'].'
		          </p>
		        </td>
		     </tr>
		     <tr style="height:10px;">		               
		       <td  colspan="2" rowspan="2" style="border-bottom:none;border-top:none;border-right:none;padding-left:5px;height:5px;border-bottom:2px solid black;">
		           Order:'.$allParamArr['ordersInfo']['erp_orders_id'].'
		       </td>
		       <td colspan="3"  style="font-weight:bold;font-size:16px;border-bottom:none;border-top:none;border-left:none;text-align:center;height:5px;">'.$allParamArr['yanwen_code'].'		        
		       </td>
		     </tr>
		      <tr style="height:15px;">	
                   <td style="border-bottom:2px solid black;height:5px;text-align:center;font-weight:900;">'.$allParamArr['postInfo']['postAreaGH'].'
		           </td>
		           <td style="border-bottom:2px solid black;text-align:center;font-weight:900;">'.$allParamArr['postInfo']['enCode'].'
		           </td>
		           <td style="border-bottom:2px solid black;text-align:center;font-weight:900;">'.$allParamArr['postInfo']['postArea1'].'
		           </td>		           
		     </tr>
		     <tr style="height:95px;">
		        <td colspan="5" style="word-break: break-all; word-wrap:break-word;border-bottom:none;">
		          <p style="padding-left:5px;height:55px;margin-top:-30px;">
		            <span style="font-size:15px;padding:0px;">TO:</span>
		            '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_city'].' '.$allParamArr['ordersInfo']['buyer_state'].'<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['buyer_zip'].'
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.($allParamArr['country_enname']?$allParamArr['country_enname']:$allParamArr['ordersInfo']['buyer_country']).'			   
		          </p>
		        </td>			    
		      </tr>
		      <tr height="20px">
		        <td colspan="5" style="border-bottom:none;border-top:none;border-bottom:2px solid black;">
		          <p style="width:30mm;height:20px;float:left;text-align:left;font-size:18px;line-height:26px;padding-left:5px;">
		            	Zip:'.$allParamArr['ordersInfo']['buyer_zip'].'		            	
		          </p>
		          <p style="width:63mm;height:20px;float:right;text-align:center;line-height:30px;">
		               Tel:'.$allParamArr['ordersInfo']['buyer_phone'].'/'.$allParamArr['country'].'
		          </p>
		        </td>
		      </tr>
		      <tr >
		        <td colspan="5" style="height:10px;font-size:12px;padding-left:5px;border-bottom:2px solid black;">
		         退件单位:'.$allParamArr['backList']['consumer_back'].'          
		        </td>
		      </tr>
		      <tr height="50px">
		        <td colspan="5">
	
		 		     <p style="width:10px;text-align:center;font-size:60px;line-height:60px;height:55px;float:left;margin-left:15px;">
		 		              	R
		 		     </p>
		 		     <p style="width:260px;height:50px;float:right;text-align:center;font-size:11px;">
		 		                <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=45&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
		 		                <br/>
		 		                '.$allParamArr['ordersInfo']['orders_shipping_code'].'
		 		      </p>
	
		        </td>
		      </tr>		     
		    </table><div style="height:10mm;font-size:11px;">【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】'.$allParamArr['productsInfo']['sku'].'</div>
		  </div>
		';
	   
	    $reStr .= $this->baoguan_template($allParamArr);
	
	    return $reStr;
	}
	
	
	//新秀驿COE平邮面单
	public function printNewCOEPYTemplates($allParamArr){
	   
	    $reStr = '
		<style>
	        td{border:1px solid #000;}
			body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:12px;}
	        #main_frame_box{margin:0 auto;}
		</style>
		';
	    $reStr .='
		   <div id="main_frame_box" style="width:99mm;height:99.5mm;">
	          <div style="width:100%;height:24mm;">
	            <div style="width:100%;height:2mm;"></div>
	            <p style="width:96%;height:20mm;margin:0 auto;">
	               From:CEO<br/>
	               forwarded by CLEVY<br/>
	               26,Usenbaev Street,<br/>
	               729001 Bishkek<br/>
	               KYRGYZSTAN
	            </p>
	          </div>
	          <div style="width:99%;height:40mm;border:1px solid #000;">
	             <div style="width:100%;height:4px;"></div>
	           <p style="width:90%;height:39mm;overflow:hidden;float:left;margin:0 auto;margin-left:4px;">
	            To:<br/>
	               '.$allParamArr['ordersInfo']['buyer_name'].'<br/>
                   '. $allParamArr['ordersInfo']['buyer_address_1'] .' <br/>'. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>
                   '. $allParamArr['ordersInfo']['buyer_city'] .'  '. $allParamArr['ordersInfo']['buyer_state'] .'<br/>'
					. $allParamArr['ordersInfo']['buyer_zip'] .' '.$allParamArr['buyerCountry'].'<br/>
				   '.$allParamArr['ordersInfo']['buyer_phone'].'
	           </p>
				<p style="width:8%;height:39mm;margin:0 auto;font-weight:bold;font-size:30px;overflow:hidden;float:left;">
                  <br/></br/><br/>
				       K
	           </p>
	          </div>
	          <div style="width:100%;height:24mm;text-align:center;">
				    <br/>
				<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=30&r=2&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />       
			  </div>
	          <div style="width:100%;height:11mm;">
				<p style="width:8%;height:11mm;float:left;margin:0 auto;font-weight:bold;font-size:30px;">
	              
	           </p>
				<p style="width:90%;height:11mm;margin:0 auto;font-weight:bold;font-size:14px;float:left;text-align:center;">
                 '.$allParamArr['ordersInfo']['orders_shipping_code'].'
	           </p>
		      </div>
		   </div>
		';
	
	    $reStr .= '
	    <div style="width:99.5mm;height:92mm;border:1px; solid #000;margin:5px auto;">
    		  <table style="width:99.5mm;height:92mm;" cellspacing="0">
	             <tr style="height:45px;font-weight:bold;">
	                <td colspan="3">
	                   &nbsp;&nbsp;CUSTOMS &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; May be opened<br/>
	                   &nbsp;&nbsp;DECLARATION &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; officially
	                </td>
	             </tr>
	             <tr style="height:30px;">
	                <td colspan="3">
	                    &nbsp;&nbsp;<span style="font-weight:bold;">Designated operator</span> 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	                   Kyrgyz Express Post
	                </td>
	             </tr>
	             <tr style="height:35px;">
	                <td colspan="3">
	                       &nbsp;&nbsp;&nbsp;
    		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;"></span>
    		            Gift
    		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;"></span>
    		            Commercial sample
    		            <br/>
    		            &nbsp;&nbsp;&nbsp;
    		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;"></span>
    		            Documents
    		            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    		            <span style="display:inline-block;width:15px;height:15px;border:1px solid #000;">√</span>
    		            Others Tick one or more boxes
	                </td>
	             </tr>
	             <tr style="height:35px;">
	                <td width="58%">
	        Quantity and detailed description of contents
	               </td>
        	        <td width="21%">Weight(KG)</td>
        	        <td width="21%">Value(USD)</td>
	             </tr>
	             <tr style="height:25px;">
	                <td width="58%">
                    '.$allParamArr['trInfo'].'
	                </td>
        	        <td width="21%">
                       '.$allParamArr['productsInfo']['signal_weight'].'
                    </td>
        	        <td width="21%">'.$allParamArr['productsInfo']['signal_value'].'</td>
	             </tr>
	             <tr style="height:75px;">
	                <td width="58%">
	                   For commerical items only If known,HS tariff number and country of origin of goods <br/>
	                   CHINA
	               </td>
        	        <td width="21%">
	                 Total Weight(KG)<br/><br/>
	                   '.$allParamArr['productsInfo']['total_weight'].'
	               </td>
        	        <td width="21%">
	                 Total Value(USD)<br/><br/>
	                   '.$allParamArr['productsInfo']['total_value'].'
	               </td>
	             </tr>
	             <tr style="height:100px;">
	                <td colspan="3">
	                 <span style="font-size:13px;">I,the undersigned,whose name and address are given on the item certify that the particulars given in this declaration are correct and that this item does not contain any dangerous article or articles prohibited by legislation or by postal or customs regulations.</span>
	                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	                   '.date('d-m-Y').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$allParamArr['ordersInfo']['erp_orders_id'].'
	                <br/>
	                  Date and sender\'s signature :   SLME
	                </td>
        	       
	             </tr>
	          </table>
		      
		</div>
	    ';
	
	    return $reStr;
	}
	
	
	//秀驿COE平邮面单
	public function printCOEPY($allParamArr){
	    $zone = '';//分区
	    if(in_array(substr($allParamArr['ordersInfo']['buyer_zip'],0,2),array(16,17,18,19))){
	        $zone = 'STP';
	    }elseif(in_array(substr($allParamArr['ordersInfo']['buyer_zip'],0,1),array(3,4)) || in_array(substr($allParamArr['ordersInfo']['buyer_zip'],0,2),array(60,61,62,63,64,65,66,67,68,69))){
	        $zone = 'SIB';
	    }else{
	        $zone = 'MSC';
	    }
	    $reStr = '
		<style>
	        td{border:0px solid #ccc;}
			body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:12px;}        
		</style>
		';
	    $reStr .='
		   <div id="main_frame_box" style="border:1px solid #000;width:99mm;height:99.5mm;">
	        <div style="margin:10px;">
	           <table  style="width:94mm;padding:0px;border:1px solid #000;font-size:13px;" cellspacing="0">
	                   <tr>
	                       <td>FROM： COE<br/>(Fw by CLEVY)<br/>P.d.5035,Rodunios kelias 9<br/>LT-02034,Vilnius<br/>Lithuania</td>
	                       <td style="border-left:1px solid #000;"><span style="font-size:20px;"><b>PRIORITY</b></span></td>
	                       <td style="border-left:1px solid #000;">PORT PAYE<br/>LITHUANIA<br/>03500 L 0638</td>
	                   </tr>
	           </table>
	        </div>
	        <div style="margin:10px;height:30mm;">
	           <table style="width:94mm;padding:0px;border:0px solid #000;" cellspacing="0">
        	        <tr>
            	        <td colspan="2" style="width:70mm;border-bottom:0px;border-right:0px;">TO：'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
            	            Tel:'.$allParamArr['ordersInfo']['buyer_phone'].'</td>
            	        <td style="text-align:center;border:1px solid #000;"><span style="color:red;font-size:21px;align:center;"><b>'.$zone.'</b></span></td>
        	        </tr>
	                <tr>
            	        <td colspan="3" style="border-top:0px;border-bottom:0px;">'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'</td>
            	    </tr>
	                <tr>
            	        <td colspan="2" style="border-top:0px;border-bottom:0px;border-right:0px;">'.$allParamArr['ordersInfo']['buyer_city'].','.$allParamArr['ordersInfo']['buyer_zip'].'</td>
            	        <td style="text-align:center;" rowspan="2" style="border-top:0px;border-bottom:0px;border-left:0px;"><span style="font-size:40px;"><b>E</b></span></td>
        	        </tr>
	                <tr>
            	        <td colspan="3" style="font-size:19px;border-top:0px;"><b>'.($allParamArr['country_enname']?$allParamArr['country_enname']:$allParamArr['ordersInfo']['buyer_country']).'</b></td>
        	        </tr>
	           </table>
	        </div>
            <div style="height:19mm;">
        	      <p style="width:260px;height:50px;float:right;text-align:center;font-size:11px;">
	 		                <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=45&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
	 		                <br/>
	 		                '.$allParamArr['ordersInfo']['orders_shipping_code'].'
	 		      </p>
	 		</div>
            <div style="border:1px solid #000;border-left:0px;border-right:0px;">【SZE150401】&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ref:'.$allParamArr['ordersInfo']['erp_orders_id'].'</div>
            <div>【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】'.$allParamArr['productsInfo']['sku'].'</div>	            
		  </div>
		';
	
 	    $reStr .= $this->coe_baoguan_template($allParamArr);
	
	    return $reStr;
	}
	
	//秀驿COE报关单
	public function coe_baoguan_template($allParamArr){
	  
	    $reStr = '';
	    $reStr = '	         
		<style>
		    *{margin:0;padding:0;}
			td{border:1px solid #000;}
		</style>';
	    $reStr.='
		<div style="width:99.5mm;height:99mm;border:1px; solid #000;">
		 <table style="width:99.5mm;border-bottom:none;border:0px solid #000;"  cellspacing=0>
		   <tr style="height:3mm;font-size:12px;">
		   	<td colspan="2" style="width:45%;padding:0;border-right:none;">
		   	  <span style="font-weight:bold;">CUSTOMS<br/>DECLARATION</span>
		   	</td>
		   	<td colspan="2" style="border-left:0px;">
		   	  <span style="display:inline-block;width:55%;border-left:none;">May be opened<br/>officially</span>
		   	  <span style="font-weight:bold;display:inline-block;text-align:center;width:40%;">CN22</span>
		   	</td>
		   </tr>
	        </table>
	        <table style="width:99.5mm;border-bottom:none;border:1px solid #000;"  cellspacing="0" cellpadding="0">
		   <tr style="height:3mm;font-size:12px;">
		     <td colspan="1" style="width:35%;padding:0;border-right:0px;"><b>Designated operator</b></td>
	         <td colspan="1" style="width:25%;padding:0;border-left:0px;border-right:0px;align:center;"><b>Lithuania Post</b></td>
		     <td colspan="2" style="text-align:right;border-left:0px;font-size:11px;">important!<br/>See instructions on the back</td>
		   </tr>
	        </table>
	        <table style="width:99.5mm;border-top:none;border-bottom:none;border:1px solid #000;"cellspacing=0>
		   <tr style="height:3mm;font-size:12px;">
		      <td style="width:17mm;border-top:none;"></td>
			  <td style="width:36mm;border-top:none;">Gift</td>
			  <td style="width:15mm;border-top:none;"></td>
			  <td style="width:34mm;border-top:none;">Commercial Sample</td>
		   </tr>
		   <tr style="height:3mm;font-size:12px;">
		      <td style="width:17mm;"></td>
			  <td style="width:36mm;">Documents</td>
			  <td style="width:15mm;font-size:15px;text-align:center;"><b>×</b></td>
			  <td style="width:34mm;">Other</td>
		   </tr>
		 </table>
		  <table style="width:99.5mm;height:40mm;border-top:none;border:1px solid #000;" cellspacing="0" cellpadding="0" >
		        <tr style="height:5mm;font-size:11px;">
		          <td style="width:50mm;border-top:none;border-left:1px solit #ccc;">Quantity and detailed description of contents</td>
		    	  <td style="widht:25mm;border-top:none;">Weight(in kg)</span></td>
		    	  <td style="widht:25mm;border-top:none;">Value(USD)</td>
		        </tr>
		        <tr style="height:10mm;font-size:12px;">
			          <td style="width:50mm;">'.$allParamArr['productsInfo'][0]['products_declared_en'].'</td>
			    	  <td style="widht:25mm;text-align:right;">'.$allParamArr['productsInfo'][0]['products_weight'].'</td>
			    	  <td style="widht:25mm;text-align:right;">'.$allParamArr['productsInfo']['totalValue'].'</td>
			    </tr>
		        <tr style="height:5mm;font-size:12px;">
		          <td style="width:50mm;"rowspan="2">
		           For commerical items only If known,HS tariff number and country of origin of goods
		          </td>
		    	  <td style="widht:25mm;border-bottom:0px;font-size:11px;">Total Weight(in kg)</td>
		    	  <td style="widht:25mm;border-bottom:0px;font-size:11px;">Total Value(USD)</td>
		        </tr>
		        <tr style="height:5mm;font-size:12px;">
		    	  <td style="widht:25mm;border-top:0px;text-align:right;">'.$allParamArr['productsInfo']['totalWeight'].'</td>
		    	  <td style="widht:25mm;border-top:0px;text-align:right;">'.$allParamArr['productsInfo']['totalValue'].'</td>
		        </tr>
		        <tr style="height:15mm;font-size:10px;">
		          <td colspan="3" style="border:1px solid #000;margin-top:-5px;">
		           I,the undersigned,whose name and address are given on the item certify that the particulars given in this declaration are
		           correct and that this item does not contain any dangerous article or articles prohibited by legislation or by postal
		           or customs regulations.<br/> Date and sender\'s signature &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		    	      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		    	      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		    	      <span style="width:15px;height:5px;text-align:right;"><img src='.site_url('attachments/images/slme.png').'></span>
		          </td>
		        </tr>
		    </table >
		    <div style="width:99.5mm;height:15mm;margin-top:5px;">
		     	<p style="width:260px;height:20px;float:right;text-align:center;font-size:11px;">
	 		                <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=45&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
	 		                <br/>
	 		                '.$allParamArr['ordersInfo']['orders_shipping_code'].'
	 		      </p>
		    </div>
		</div>
		';
	  return $reStr;
	}
	
	public function printTestyz_tlp($allParamArr){
	 	$reStr = '';
	 	$reStr = '
	 		<style>
			*{margin:0;padding:0;}
			.main{border:1px black solid;width:96mm;height:128mm;margin:auto;font-size:12px;word-break:break-all;}
			.header{height:31px;line-height:31px;}
			.logo{height:31px;float:left;}
			.header span{font-size:31px;font-weight:bold;}
			.byair{width:100px;float:left;float:left;font-size:10px;text-align: center;line-height:11px;font-weight:bold;margin-top:1px;}
			.header1{width:140px;height:31px;float:left;}
			.header3{float:right;font-size:10px;font-weight: bold;line-height:25px;margin-right:7px;}
			.agree{border-bottom:1px black solid;border-top:1px black solid;height:15px;text-align:left;font-size:10px;clear:both;}
			.from{border-bottom:1px black solid;height:27px;font-size:12px;line-height:13px;}
			.from div:nth-child(1){width:12mm;height:28px;float:left;}
			.from div:nth-child(2){width:80mm;height:28px;float:left;}
			.from span,.to span{font-weight:bold;}
			.to{border-bottom:1px black solid;height:80px;font-size:13px;line-height:13px}
			.to div:nth-child(1){width:10mm;height:80px;float:left;}
			.to div:nth-child(2){width:83mm;height:80px;float:right;}
			.tel{border:1px black solid;height:15px;}
			.tel div:nth-child(1),.tel div:nth-child(2){width:47mm;height:15px;float:left;font-weight:bold;}
			.return{border-bottom:1px black solid;height:18px;font-size:12px;}
			.khdm{border-bottom:1px black solid;height:50px;}
			.khimg{width:255px;height:50px;padding-top:2px;margin-left:42px;text-align:center;font-weight: bold;font-size: 15px;}
			.khdm div:nth-child(1){font-weight: bold;font-size: 25px;float:left;height:55px;line-height:55px;margin-left:10px;}
			.dm{font-weight: bold;font-size: 25px;float:right;width:75px;height:55px;line-height:55px;}
			.bgqt{border-bottom:1px black solid;height:21px;font-size:9px;}
			.bgqt div:nth-child(1){width:29mm;float:left;text-align:left;line-height: 10px;}
			.bgqt div:nth-child(1) span{font-weight:bold;}
			.bgqt div:nth-child(2){width:32mm;float:left;text-align:center;line-height: 10px;}
			.bgqt div:nth-child(3){width:35mm;float:left;text-align:center;line-height: 10px;}
			.detail{height:140px;border-bottom:1px black solid;}
			.footer{font-size:9px;line-height:9px;position:relative;}
			table{font-size:10px;line-height:10px;}
			table tr td{border-bottom: 1px solid black;vertical-align: text-top;}
			td{border-right: 1px solid black;}
	
		</style>
		<div class="main" >
			<div class="header">
				<div class="header1"><img src="'.site_url('attachments').'/images/post_logo.jpg" class="logo"/><span style="line-height:36px;">R</span></div>
				<div class="byair">航空<br>Small packed<br/>BY Air</div>
				<div class="header3">'.$allParamArr['countryInfo']['country_cn'].'&nbsp'.$allParamArr['countryInfo']['country_en'].'</div>
			</div>
			<div class="agree">协议客户 （SLME01）90000006605467 </div>
			<div class="from"><div style="float:left;margin-right:10px;"><span>From:</span></div>
				<div class="fromads" style="float:left;">'.$allParamArr['senderInfo']['sender'].'&nbsp;&nbsp;'.$allParamArr['senderInfo']['address'].'
					</div>
			</div>
			<div class="to"><div style="float:left;margin-right:10px"><span>To:</span></div>
				<div class="toads" style="float:left;">'
				.$allParamArr['ordersInfo']['buyer_name'].'<br/>'
				. $allParamArr['ordersInfo']['buyer_address_1'] .' '. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>'
				. $allParamArr['ordersInfo']['buyer_city'] .','. $allParamArr['ordersInfo']['buyer_state'] .'<br/>'
				. $allParamArr['countryInfo']['display_name'] . '(' . $allParamArr['countryInfo']['country_cn'] .')'
				.'</div>
			</div>
			<div class="tel" >
				Zip:'. $allParamArr['ordersInfo']['buyer_zip'].'
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    
				Tel:'.$allParamArr['ordersInfo']['buyer_phone'].'
			</div>
			<div class="return">退件单位&nbsp;&nbsp;'.$allParamArr['backAddress'].'</div>
			<div class="khdm" style="border-top:1px solid black;">
				 <p style="display:inline-block;width:55px;float:left;font-size:20px;text-align:center;height:50px;font-weight:bold;">
                	'.$allParamArr['geKou'].'
                </p>
                <p style="display:inline-block;width:240px;float:left;font-size:12px;height:50px;text-align:center;padding-top:2px;">
                	<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=32&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
                	    <br/>
                	    '.$allParamArr['ordersInfo']['orders_shipping_code'].'
                </p>
	
                <p style="display:inline-block;width:55px;float:left;font-size:20px;height:50px;text-align:center;">
                	AAAJ
                </p>
			</div>
			<div class="bgqt" style="height:25px">
			             <p style="display:inline-block;width:110px;float:left;font-size:10px;font-weight:bold;">
		                	<span style="font-size:8px;">报关签条<br/>CUSTOMS DECLARATION</span>
		                </p>
		                <p style="display:inline-block;width:100px;float:left;font-size:10px;text-align:center;">
		                	<span style="font-size:8px;">可以进行开拆<br/>May be open officially</span>
		                </p>
			
		                <p style="display:inline-block;width:120px;float:left;font-size:10px;text-align:center;">
		                	<span style="font-size:8px;padding-left:5px;">请先阅读背面的注意事项<br/>May be opend officially</span>
		                </p>
			</div>
			<div class="detail" style="border-top:1px solid black;">
				<div style="height:125px;width:265px;float:left;">
					<table  cellspacing="0" cellpadding="0" style="border-right:1px solid black;">
						<tr>
							<td style="width:160px;padding:1px"><p style="width:140px;border-right:1px solid black;">邮件种类 Category of item</p></td>
							<td colspan="2"></td>
							
						</tr>
						<tr style="height:30px;">
							<td >内件详情名称和数量 Quantity and<br/>detailed description of contents</td>
							<td style="text-align: center;">重量(千克)<br/>Weight(kg)</td>
							<td style="text-align: center;">价值<br/>Value</td>
						</tr>
						<tr style="height:30px;">
							<td style="text-align: center;padding-top:2px;">' . $allParamArr['productsInfo'][0]['item_count']. ' * '. $allParamArr['productsInfo'][0]['products_declared_en'].'</td>
							<td style="text-align: center;">'.$allParamArr['productsInfo'][0]['item_count']*$allParamArr['productsInfo'][0]['products_weight'].'</td>
							<td style="text-align: center;">USD'.$allParamArr['productsInfo'][0]['products_declared_value']*$allParamArr['productsInfo'][0]['item_count'].'</td>
						</tr>
						<tr >
							<td >协调系统税则号列和货物原 产国(只对商品邮件填写)<br/>HS tatiff number and country of origin of goods (For commerical items only)</td>
							<td style="text-align: center;border:1px solid black;">总重量(kg)Total weight(kg)</td>
							<td style="text-align: center;border:1px solid black;">总价值<br/>Total value</td>
						</tr>
						<tr style="border-bottom: none;">
							<td style="border-bottom: none;"></td>
							<td style="border-bottom: none;width:50px;height:10px;text-align: center;padding-top:2px;">'.$allParamArr['total_weight'].'</td>
							<td style="border-bottom: none;text-align: center;width:50px">USD'.$allParamArr['total_price'].'</td>
						</tr>
					</table>
					
				</div>
				<div style="float:right;border-bottom:1px solid black;">
					<table  style="font-size:10px;float:right;width:97px;height:50px;text-align:center;" cellspacing="0" cellpadding="0" class="table2">
					<tr style="border-bottom:1px solid black;">
						<td width="48" >航空</td>
						<td>Guangzhou<br/>China</td>
					</tr>
					<tr style="border-bottom:1px solid black;">
						<td height="30" >PAP AVON</td>
						<td>已验视</td>
					</tr>
					<tr style="border-bottom:1px solid black;">
						<td height="35">小包邮件</td>
						<td>单位:<br/>广州小包中心</td>
					</tr>
					<tr>
						<td height="30">PETIT<br/>PAQUET</td>
						<td style="line-height:13px;">验视人:<br/><span style="font-size:12px;">林文勇

</span></td>
					</tr>
				</table>
				</div>
			</div>
			<div class="footer" style="border-top:1px solid black;">
					我保证上述申报准确无误,本函件内未装寄法律或邮政和海关规章禁止寄递的任何危险物品<br/>
					I, the undersigned,certify that the particulars given inthis declaration are correct and this item does not containany dangerous articles prohibited by legislation or bypostal or customs regulations.<br/>
					寄件人签字 Sender\'s signature:<div style="font-size:12px;padding-top:2px;right:0;bottom:0;width:100px;">('.$allParamArr['ordersInfo']['erp_orders_id'].')</div>
					<div style="text-align:right;padding-right:2px;font-size:14px;font-weight:bold;width:100px;position:absolute;right:0;bottom:0">'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'</div>
				</div>
		</div>
	 	';
	 	return $reStr;
	}
	public function printLdbYZ_tlp($allParamArr){
	 	$reStr = '';
	 	$reStr = '
	 		<style>
			*{margin:0;padding:0;}
			.main{border:1px black solid;width:96mm;height:128mm;margin:auto;font-size:12px;word-break:break-all;}
			.header{height:31px;line-height:31px;}
			.logo{height:31px;float:left;}
			.header span{font-size:31px;font-weight:bold;}
			.byair{width:100px;float:left;float:left;font-size:10px;text-align: center;line-height:11px;font-weight:bold;margin-top:1px;}
			.header1{width:140px;height:31px;float:left;}
			.header3{float:right;font-size:10px;font-weight: bold;line-height:25px;margin-right:7px;}
			.agree{border-bottom:1px black solid;border-top:1px black solid;height:15px;text-align:left;font-size:10px;clear:both;}
			.from{border-bottom:1px black solid;height:27px;font-size:12px;line-height:13px;}
			.from div:nth-child(1){width:12mm;height:28px;float:left;}
			.from div:nth-child(2){width:80mm;height:28px;float:left;}
			.from span,.to span{font-weight:bold;}
			.to{border-bottom:1px black solid;height:80px;font-size:13px;line-height:13px}
			.to div:nth-child(1){width:10mm;height:80px;float:left;}
			.to div:nth-child(2){width:83mm;height:80px;float:right;}
			.tel{border:1px black solid;height:15px;}
			.tel div:nth-child(1),.tel div:nth-child(2){width:47mm;height:15px;float:left;font-weight:bold;}
			.return{border-bottom:1px black solid;height:18px;font-size:12px;}
			.khdm{border-bottom:1px black solid;height:50px;}
			.khimg{width:255px;height:50px;padding-top:2px;margin-left:42px;text-align:center;font-weight: bold;font-size: 15px;}
			.khdm div:nth-child(1){font-weight: bold;font-size: 25px;float:left;height:55px;line-height:55px;margin-left:10px;}
			.dm{font-weight: bold;font-size: 25px;float:right;width:75px;height:55px;line-height:55px;}
			.bgqt{border-bottom:1px black solid;height:21px;font-size:9px;}
			.bgqt div:nth-child(1){width:29mm;float:left;text-align:left;line-height: 10px;}
			.bgqt div:nth-child(1) span{font-weight:bold;}
			.bgqt div:nth-child(2){width:32mm;float:left;text-align:center;line-height: 10px;}
			.bgqt div:nth-child(3){width:35mm;float:left;text-align:center;line-height: 10px;}
			.detail{height:140px;border-bottom:1px black solid;}
			.footer{font-size:9px;line-height:9px;position:relative;}
			table{font-size:10px;line-height:10px;}
			table tr td{border-bottom: 1px solid black;vertical-align: text-top;}
			td{border-right: 1px solid black;}
	
		</style>
		<div class="main" >
			<div class="header">
				<div class="header1"><img src="'.site_url('attachments').'/images/post_logo.jpg" class="logo"/><span style="line-height:36px;"></span></div>
				<div class="byair">航空<br>Small packed<br/>BY Air</div>
				<div class="header3">'.$allParamArr['countryInfo']['country_cn'].'&nbsp'.$allParamArr['countryInfo']['country_en'].'</div>
			</div>
			<div class="agree">协议客户 （SLME01）90000006605467 </div>
			<div class="from"><div style="float:left;margin-right:10px;"><span>From:</span></div>
				<div class="fromads" style="float:left;">'.$allParamArr['senderInfo']['sender'].'&nbsp;&nbsp;'.$allParamArr['senderInfo']['address'].'
					</div>
			</div>
			<div class="to"><div style="float:left;margin-right:10px"><span>To:</span></div>
				<div class="toads" style="float:left;">'
				.$allParamArr['ordersInfo']['buyer_name'].'<br/>'
				. $allParamArr['ordersInfo']['buyer_address_1'] .' '. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>'
				. $allParamArr['ordersInfo']['buyer_city'] .','. $allParamArr['ordersInfo']['buyer_state'] .'<br/>'
				. $allParamArr['countryInfo']['display_name'] . '(' . $allParamArr['countryInfo']['country_cn'] .')'
				.'</div>
			</div>
			<div class="tel" >
				Zip:'. $allParamArr['ordersInfo']['buyer_zip'].'
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    
				Tel:'.$allParamArr['ordersInfo']['buyer_phone'].'
			</div>
			<div class="return">退件单位&nbsp;&nbsp;'.$allParamArr['backAddress'].'</div>
			<div class="khdm" style="border-top:1px solid black;font-size:30px;">
				 <p style="width:50px;padding:0;float:left;font-size:40px;text-align:center;height:50px;font-weight:bold;">
                	'.$allParamArr['geKou'].'
                </p>
                <p style="display:inline-block;width:240px;float:left;font-size:12px;height:50px;text-align:center;padding-top:2px;">
                	<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=32&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" />
                	    <br/>
                	    '.$allParamArr['ordersInfo']['orders_shipping_code'].'
                </p>
	
                <p style="display:inline-block;width:55px;float:left;font-size:20px;height:50px;text-align:center;">
                	AAAJ
                </p>
			</div>
			<div class="bgqt" style="height:25px">
			             <p style="display:inline-block;width:110px;float:left;font-size:10px;font-weight:bold;">
		                	<span style="font-size:8px;">报关签条<br/>CUSTOMS DECLARATION</span>
		                </p>
		                <p style="display:inline-block;width:100px;float:left;font-size:10px;text-align:center;">
		                	<span style="font-size:8px;">可以进行开拆<br/>May be open officially</span>
		                </p>
			
		                <p style="display:inline-block;width:120px;float:left;font-size:10px;text-align:center;">
		                	<span style="font-size:8px;padding-left:5px;">请先阅读背面的注意事项<br/>May be opend officially</span>
		                </p>
			</div>
			<div class="detail" style="border-top:1px solid black;">
				<div style="height:125px;width:265px;float:left;">
					<table  cellspacing="0" cellpadding="0" style="border-right:1px solid black;">
						<tr>
							<td style="width:160px;padding:1px"><p style="width:140px;border-right:1px solid black;">邮件种类 Category of item</p></td>
							<td colspan="2"></td>
							
						</tr>
						<tr style="height:30px;">
							<td >内件详情名称和数量 Quantity and<br/>detailed description of contents</td>
							<td style="text-align: center;">重量(千克)<br/>Weight(kg)</td>
							<td style="text-align: center;">价值<br/>Value</td>
						</tr>
						<tr style="height:30px;">
							<td style="text-align: center;padding-top:2px;">' . $allParamArr['productsInfo'][0]['item_count']. ' * '. $allParamArr['productsInfo'][0]['products_declared_en'].'</td>
							<td style="text-align: center;">'.$allParamArr['productsInfo'][0]['item_count']*$allParamArr['productsInfo'][0]['products_weight'].'</td>
							<td style="text-align: center;">USD'.$allParamArr['productsInfo'][0]['products_declared_value']*$allParamArr['productsInfo'][0]['item_count'].'</td>
						</tr>
						<tr >
							<td >协调系统税则号列和货物原 产国(只对商品邮件填写)<br/>HS tatiff number and country of origin of goods (For commerical items only)</td>
							<td style="text-align: center;border:1px solid black;">总重量(kg)Total weight(kg)</td>
							<td style="text-align: center;border:1px solid black;">总价值<br/>Total value</td>
						</tr>
						<tr style="border-bottom: none;">
							<td style="border-bottom: none;"></td>
							<td style="border-bottom: none;width:50px;height:10px;text-align: center;padding-top:2px;">'.$allParamArr['total_weight'].'</td>
							<td style="border-bottom: none;text-align: center;width:50px">USD'.$allParamArr['total_price'].'</td>
						</tr>
					</table>
					
				</div>
				<div style="float:right;border-bottom:1px solid black;">
					<table  style="font-size:10px;float:right;width:97px;height:50px;text-align:center;" cellspacing="0" cellpadding="0" class="table2">
					<tr style="border-bottom:1px solid black;">
						<td width="48" >航空</td>
						<td>Guangzhou<br/>China</td>
					</tr>
					<tr style="border-bottom:1px solid black;">
						<td height="30" >PAP AVON</td>
						<td>已验视</td>
					</tr>
					<tr style="border-bottom:1px solid black;">
						<td height="35">小包邮件</td>
						<td>单位:<br/>广州小包中心</td>
					</tr>
					<tr>
						<td height="30">PETIT<br/>PAQUET</td>
						<td style="line-height:13px;">验视人:<br/><span style="font-size:12px;">林文勇

</span></td>
					</tr>
				</table>
				</div>
			</div>
			<div class="footer" style="border-top:1px solid black;">
					我保证上述申报准确无误,本函件内未装寄法律或邮政和海关规章禁止寄递的任何危险物品<br/>
					I, the undersigned,certify that the particulars given inthis declaration are correct and this item does not containany dangerous articles prohibited by legislation or bypostal or customs regulations.<br/>
					寄件人签字 Sender\'s signature:<div style="float:right;font-size:12px;font-weight:bold;margin-right:30px;">CN22</div><div style="font-size:12px;padding-top:2px;right:0;bottom:0;width:100px;">('.$allParamArr['ordersInfo']['erp_orders_id'].')</div>
					<div style="text-align:right;padding-right:2px;font-size:14px;font-weight:bold;width:100px;position:absolute;right:0;bottom:0">'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'</div>
				</div>
		</div>
	 	';
	 	return $reStr;
	}
	//COE平邮面单100*100
	
		public function printCOEpy_ldb_tlp($allParamArr){
	 	$reStr = '';
	 	$reStr = '
	 		<style>
			*{margin:0;padding:0;}
	 		.main{width:98mm;height:97mm;border:1px solid black;margin:auto;font-size:10px;line-height: 12px;}
	 		.header{width:98mm;height:15mm;}
	 		.middle{width:98mm;height:77mm;}

	 		td{ border-top:0;border-bottom:1px black solid ;border-left:1px solid black;}
			table{ border-top:1px black solid;border-right:0 ;border-bottom:0 ;}
			td{border-left:0}
			.fk{display:inline-block;width:10px;height:8px;border:1px solid #000;padding-top:4px}
			.tb2 td{border-top:0;border-right:0;border-bottom:0 ;line-height: 5px;}
			.tb2{border-top:0;}
			.leftborder{border-left:1px solid black;}
		</style>
		<div style="width:100mm;height:100mm;margin:auto;">
		<div class="main">
			<div class="header">
				<div style="width:70mm;float:left;height:13mm;padding:2px;line-height: 13px;font-size:11px;">
				 FW by Clevy <br/>
				LLC TLP  <br/>
				PO box 198/1 Tbilisi, <br/>
				Georgia<b>【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</b>
				</div>
				<div style="border-left:1px solid black;border-bottom:1px solid black;width:25mm;height:12mm;float:right;text-align:center;font-size:13px;line-height:16px;">
				PORT PAYE<br/>
				GEORGIA<br/>
				</div>
			</div>
			<div class="middle">
				<div style="width:58mm;height:80mm;float:left;border-right: 1px black solid ;" >
					<table class="btable" cellspacing=0 cellpadding=0 >
						<tr >
							<td colspan=3>
								<div style="width:23mm;height:6mm;border-right:1px solid black;text-align: center;font-size:10px;line-height: 10px;float:left">
									<p><b> CUSTOMS<br> DECLARATION</b> </p>
								</div>
								<div style="width:22mm;height:6mm;border-right:1px solid black;text-align: center;font-size:10px;line-height: 10px;float:left">
									<p> May be opened<br> officially </p>
								</div>
								<div style="height:6mm;text-align: center;padding-left:1px;font-size:10px;line-height: 10px;float:left">
									<p> CN22</p>
								</div>
							</td>
	
						</tr>
						<tr>
							<td colspan=3>
								<div style="width:29mm;height:4mm;text-align: center;font-size:10px;line-height: 10px;float:left">
									<p>Designated operator</p>
								</div>
								<div style="width:24mm;height:4mm;;text-align: center;font-size:10px;line-height: 11px;float:left">
									<p><b> </b> </p>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan=3>
								 <table class="tb2" >
								 	<tr>
								 		<td style="width: 20mm;"><span class="fk" ></span>
    		            Gift</td>
								 		<td>
								 			<span class="fk"></span>
    		            Commercial sample
								 		</td>
								 	</tr>
								 	<tr>
								 		<td valign="top">
								 			 <span class="fk"></span> Documents								 			
								 		</td>
								 		<td >
								 			 <span class="fk">√</span> Others Tick one or <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;more boxes	
								 		</td>
								 	</tr>
								 </table>
							</td>

						</tr>
						<tr style="font-size:9px;text-align:center;">
							<td style="width:30mm;padding:1px;text-align:left;"><p>Quantity and detailed description of contents </p></td>
							<td class="leftborder">Weight(KG)</td>
							<td class="leftborder">Value(USD)</td>
						</tr>
						<tr style="text-align:center;">
							<td style="text-align:left;"><b> '.$allParamArr['trInfo'].'</b></td>
							<td class="leftborder"> <b>'.$allParamArr['productsInfo']['signal_weight'].'</b></td>
							<td class="leftborder"> <b>'.$allParamArr['productsInfo']['signal_value'].'</b></td>
						</tr>
						<tr style="text-align:center;">
							<td style="text-align:left;">For commerical items only If known,HS tariff number and country of origin of goods </td>
							<td class="leftborder">Total Weight(KG)</td>
							<td class="leftborder">Total Value(USD)</td>
						</tr>
						<tr style="text-align:center;">
							<td><b>CHINA </b></td>
							<td class="leftborder"><b> '.$allParamArr['productsInfo']['total_weight'].'</b></td>
							<td class="leftborder"<b>'.$allParamArr['productsInfo']['total_value'].'</b></td>
						</tr>
						<tr >
							<td colspan=3 style="height:96px;line-height: 10px;padding:1px;border-bottom: 0;" valign="top">
								<span style="font-size:12px;">I,the undersigned,whose name and address are given on the item certify that the particulars given in this declaration are correct and that this item does not contain any dangerous article or articles prohibited by legislation or by postal or customs regulations.</span><div style="text-align:right;width:100%">'.date('d-m-Y').' &nbsp;</div>
	                  Date and sender\'s signature :   SLME
							</td>

						</tr>
					</table>
				</div>
				<div style="width:39mm;height:76mm;float:right;font-size:13px;line-height: 13px;">
					<div style="width:39mm;height:50mm;float:right;font-size:13px;line-height: 13px;">
					TO:<br/>
					'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
                   '. $allParamArr['ordersInfo']['buyer_address_1'] .' <br/>'. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>
                   '. $allParamArr['ordersInfo']['buyer_city'] .'  '. $allParamArr['ordersInfo']['buyer_state'] .'<br/>'
					. $allParamArr['ordersInfo']['buyer_zip'] .' '.$allParamArr['buyerCountry'].'<br/>
				   '.$allParamArr['ordersInfo']['buyer_phone'].'
					</div>
					<div style="width:39mm;height:26mm;float:right;font-size:13px;line-height: 13px;text-align: center;">
						<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=50&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=">
							<p style="font-weight:bold;">'.$allParamArr['ordersInfo']['orders_shipping_code'].'</p>
							<div style="width:39mm;height:5mm;text-align:left;padding:0;marign:0" valign="bottom"><p><span style="font-size:17px;font-weight:bold;"></span><span style="font-size:16px;font-weight:bold;">D</span><span style="font-size:10px;">OrderNo:'.$allParamArr['ordersInfo']['erp_orders_id'].'</span></p></div>
							<div style="float:right;border:1px solid black;font-size:25px;font-weight: bold;padding:4px ;margin-right:5px;">E</div>
					</div>
				</div>
			</div>
		</div>
		</div>
	 	';
	 	return $reStr;
	}
	//迪欧比利时邮政渠道面单100*130
		public function printDiouPY_ldb_tlp($allParamArr){
	 	$reStr = '';
	 	$reStr = '
	 		<style>
			*{margin:0;padding:0;}
			.main{width:94mm;height:123mm;border:1px solid black;margin:auto;font-size:14px;line-height: 14px;padding:2mm;}
		</style>
				<div class="main">
			<table  cellspacing=0 cellpadding=0>
				<tr style="display:block;">
					<td style="border:1px solid black;width:48mm;height:30mm" valign="top">
					<img src="'.site_url('attachments').'/images/Bpostlogo1.jpg" style="width:46mm;height:14mm" />
					<p style="padding:2px;font-size:12px;">
						If undelivered please return to<br/>ECDC LOGISTICS<br/>Rue de Maastricht 106 4600,Vise Belgium
					</p>
					</td>
					<td style="border:1px solid black;width:40mm;border-left:none;padding:1mm 3mm;text-align:center;" valign="top">
					<img src="'.site_url('attachments').'/images/Bpostlogo2.jpg" style="width:37mm;height:14mm" />
					<p style="font-weight: bold;font-size:18px;">PB-PP BPI-9572</p>
					<div style="text-align:right;height:10mm;line-height: 50px;">BELGIE(N)-BELGIQUE</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding:1mm;font-size:15px;line-height: 19px;">
					TO:'.$allParamArr['ordersInfo']['buyer_name'].'<br/>'
					. $allParamArr['ordersInfo']['buyer_address_1'] .' <br/>'. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>
                   '. $allParamArr['ordersInfo']['buyer_city'] .'  '. $allParamArr['ordersInfo']['buyer_state'] .'<br/> 
					Zip Code:'.$allParamArr['ordersInfo']['buyer_zip'].'<br/>
					Country:'.$allParamArr['buyerCountry'].'('.$allParamArr['country_code'].')<br/>
					Phone:'.$allParamArr['ordersInfo']['buyer_phone'].'<br>
					Weight:'.sprintf("%01.2f",$allParamArr['productsInfo']['total_weight']).'<br/>
					Ref:'.$allParamArr['ordersInfo']['erp_orders_id'].'
					</td>
				</tr>
				<tr style="margin-top:10px;display:block;width:95mm">
					<td colspan="2" style="border:1px dashed black;padding:1mm;text-align:center;width:95mm">
									<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=50&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3="/>
									<p>'.$allParamArr['ordersInfo']['orders_shipping_code'].'</p>
					</td>
				</tr>
				<tr style="display:block;width:95mm">
					<td colspan="2" style="width:95mm;">
					<div style="border:1px solid black;height:40px;text-align:center;padding-top:15px;margin-top:10px;">
						<div style="margin:0 auto;border:1px solid black;padding:2mm;text-align:center;width:20px;height;20px;font-size:20px;font-weight: bold;">'.$allParamArr['bilishiArea'].'</div>
					</div>
					</td>
				</tr>
			</table>
		</div>
	 	';
	 	return $reStr;
	}	
	//打印lazada新加坡面单100*100
	public function printNewlazadaTemplate($allParamArr){

		$reStr = '
		<style>
			*{margin:0;padding:0;}
			.main{width:98mm;height:96mm;border:1px solid black;margin:auto;font-size:14px;padding:1mm;}
		</style>
	 	';
	   	$reStr .='
	<div class="main">
		<table cellspacing=0 cellpadding=0>
			<tr>
				<td colspan=2 style="height:20px;font-weight: bold;text-align: right;">
					SG3
				</td>
			</tr>
			<tr>
				<td style="width:50mm;" valign="top">
					<p style="font-size: 11px;line-height: 11px;">
						If undelivered, please return to:<br/>
						20 Toh Guan Road<br/>
						#08-00 CJ Korea Express Building<br/>
						Singapore 608839<br/>
					</p>
					<p style="margin-top:15px;">Deliver To:</p>
					<p>
						'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
						'.$allParamArr['ordersInfo']['buyer_address_1'].' '.$allParamArr['ordersInfo']['buyer_address_2'].'<br/>'
						.$allParamArr['ordersInfo']['buyer_city'].'<br/>
						'.$allParamArr['ordersInfo']['buyer_state'].'  '.$allParamArr['ordersInfo']['buyer_zip'].','.$allParamArr['buyerCountry'].'<br/>
						'.$allParamArr['ordersInfo']['buyer_phone'].'
					</p>
					<p style="margin-top:5px;">
						'.$allParamArr['pagenumber'].'
					</p>
				</td>
				<td valign="top" >
					<p style="text-align:right;width:47mm;">
						<img src="'.site_url('attachments').'/images/lazada.png" style="width:160px;"/>
					</p>
					<p style="text-align:center;width:143px;border:3px solid black;margin-top:10px;margin-left:22px;font-weight: bold;padding:2px;font-size: 16px;">
						Registered Mail
					</p>
					<p style="text-align:center;margin-top:20px;font-size:20px;font-weight: bold">
						'.$allParamArr['ordersInfo']['buyer_zip'].'
					</p>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="height:25mm;">
					<div style="width:14mm;height:20mm;font-size: 20px;font-weight: bold;float:left;line-height: 90px;margin-top: 10px;" >RX
						
					</div>
					<div style="width:82mm;height:20mm;float:right;text-align:center;margin-top: 10px;">
						<p>'.$allParamArr['ordersInfo']['orders_shipping_code'].'</p>
						<p><img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code39&o=2&t=40&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" /></p>
						<p>'.$allParamArr['ordersInfo']['orders_shipping_code'].'</p>
					</div>
					<div>'.$allParamArr['ordersInfo']['erp_orders_id'].'</div>
				</td>
			</tr>
		</table>
	</div>';
		return $reStr;
	}
	//云途中华小包面单100*100
	
	public function printCOEyuntoxb_tlp($allParamArr){
	 	$reStr = '';
	 	$reStr = '<style>
		*{margin:0 auto;padding:0;}
		.main{border:1px solid black;width:99mm;height:99mm;word-break:break-all;}
		.content{border:1px solid black;width:98mm;height:98mm;margin-top:1px;}
		.fk{display:inline-block;width:9px;height:7px;border:1px solid #000;padding-top:4px}
		</style>
		<div class="main">
			<div class="content">
				<div style="height:46mm;">
					<div style="height:45mm;width:38mm;float:left">
						<div style="height:29mm;width:36mm;border:1px solid black;text-align: center;font-size:14px;margin-top:2px;">
							<p>TAIPEI(TP)TAIWAN</p>
							<p>R.O.C.</p>
							<p>POSTAGE PAID</p>
							<p>LICENCE NO.TP6627</p>
						</div>
						<div style="height:12mm;width:36mm;font-size:11px;line-height: 12px;margin-top:1px;padding-left:5px;">
						<p>
							From:<br/>
							'.$allParamArr['newAddress'].'
						</p>
						</div>
					</div>
					<div style="height:46mm;width:58mm;margin-right:1px;float:right;font-size:11px;text-align: center;" >
						<p style="line-height:14px;">CHUNGHWA POST CO., LTD. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b style="font-size: 14px;">CN22</b></p>
						<img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=50&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3="/>
						<div style="font-size:16px;font-weight:bold;line-height: 14px;margin-top:1px;">'.$allParamArr['ordersInfo']['orders_shipping_code'].'</div>
						<p style="line-height:13px;width:10mm;font-size:14px;font-weight: bold;text-align: left;float:left;">TO</p><p style="width:45mm;font-size:15px;font-weight: bold;text-align: right;margin-right:10px;float:right;line-height:13px;"> '.$allParamArr['qnumb'].'</p>
						<p style="font-size:12px;text-align:left;font-weight: bold;line-height: 13px;">
							'.$allParamArr['ordersInfo']['buyer_name'].'<br/>
                   '. $allParamArr['ordersInfo']['buyer_address_1'] .' '. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>
                   '. $allParamArr['ordersInfo']['buyer_city'] .'  '. $allParamArr['ordersInfo']['buyer_state'] .','.$allParamArr['buyerCountry'].'<br/>'
					.'Postal code: '. $allParamArr['ordersInfo']['buyer_zip'] .' '.'<br/>
				   '.'Tel: '.$allParamArr['ordersInfo']['buyer_phone'].'
						</p>
					</div>
				</div>
				<div style="height:4mm;width:96mm;border:1px solid black;font-size:11px;padding-left: 2px;padding-top: 1px;font-weight: bold;">
					<span class="fk" style="line-height: 7px;">√</span>&nbsp; Gift &nbsp;
					<span class="fk" style="line-height: 7px;"></span>&nbsp; Commercial sample&nbsp;
					<span class="fk" style="line-height: 7px;"></span>&nbsp; Documents&nbsp;
					<span class="fk" style="line-height: 7px;"></span>&nbsp; Others&nbsp;
				</div>
				<div style="height:27mm;">
					<div style="width:47mm;height:23mm;float:left;margin-left:2px;">
						<table style="margin-top:2px;font-size:10px;border:1px solid black;line-height: 11px;text-align: center;" cellpadding="0" cellspacing="0">
							<tr >
								<td style="border-bottom: 1px solid black;border-right: 1px solid black;width:30mm;">Quantity and detailed description of contents</td>
								<td style="border-bottom: 1px solid black;border-right: 1px solid black;">Weight(KG)</td>
								<td style="border-bottom: 1px solid black;">Value(USD)</td>
							</tr>
							<tr>
								<td style="border-right: 1px solid black;">'.$allParamArr['trInfo'].'</td>
								<td style="border-right: 1px solid black;">'.$allParamArr['productsInfo']['signal_weight'].'</td>
								<td >'.$allParamArr['productsInfo']['signal_value'].'</td>
							</tr>
						</table>
					</div>
					<div style="margin-top:2px;border:1px solid black;width:48mm;height:25mm;float:right;margin-right:2px;font-size:11px;line-height: 10px;padding:1px;">
						<p style="line-height: 12px;border-bottom: 1px solid black;padding-bottom: 2px;"><b>'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'</b></p>
						<p>I,the undersigned,whose name and address are given on the item certify that the particulars given in this declaration are correct and that this item does not contain any dangerous article or articles prohibited by legislation or by postal or customs regulations.</p>
					</div>
				</div>
				<div style="height:12mm;width: 97mm;margin-top: 1px;">
					<table cellpadding="0" cellspacing="0" style="height:12mm;font-size:11px;line-height: 10px;text-align: center; border:1px solid black;" >
						<tr>
							<td rowspan="2" valign="top" style="width:30mm;border-right: 1px solid black;">For commerical items only If known,HS tariff number and country of origin of goods</td>
							<td valign="top" style="border-bottom: 1px solid black;border-right: 1px solid black;">Total <br/>Weight</td>
							<td valign="top" style="border-bottom: 1px solid black;border-right: 1px solid black;">Total<br/>Value</td>
							<td valign="top" style="width:45mm;border-bottom: 1px solid black;line-height:18px;">Date and sender\'s signature : </td>
						</tr>
						<tr>
							
							<td valign="top" style="width:11mm;border-right: 1px solid black;line-height:15px;"> '.$allParamArr['productsInfo']['total_weight'].'</td>
							<td valign="top" style="width:10mm;border-right: 1px solid black;line-height:15px;">'.$allParamArr['productsInfo']['total_value'].'</td>
							<td valign="top" style="line-height:15px;">TSAI TSUNG LIANG &nbsp;'.date('Y-m-d').'</td>
						</tr>
					</table>
				</div>
				<div style="height:6mm;border:1px solid black;margin-top: 2px;width:96mm;font-size:11px;line-height: 11px;">
					<p style="width:56mm;float:left;">Taipei Forever incorporated company<br/>
					This parcel is transit inTaiwan Free Zone
					</p>
					<p style="width:35mm;float:right;text-align: right;margin-right: 4px;line-height: 25px;" valign="bottom">
						'.$allParamArr['ordersInfo']['erp_orders_id'].' '.date('Y-m-d').'
					</p>
				</div>
			</div>
		</div>
	 	';
	 	return $reStr;
	}
	//COE土耳其挂号
	public function printTTPgh_ldb_tlp($allParamArr){
	 	$reStr = '';
	 	$reStr = '
			<style type="text/css">
			*{margin:0 auto;padding:0;}
			.main{border:1px solid black;width:98.5mm;height:98.5mm;}
			.fk{display:inline-block;width:10px;height:8px;border:1px solid #000;padding-top:4px;line-height: 10px;}
			</style>
			<div class="main">
				<div style="border:1px solid black;border-top:none;border-right:none;width:96.5mm;height:22mm;float:right;">
					<div style="width:19mm;height:22mm;float:left;text-align: center;">
						<p><img src="'.site_url('attachments').'/images/ttp1.png" style="width:72px;height:55px;"/></p>
						<p><img src="'.site_url('attachments').'/images/ttp2.png" style="width:35px;height:24px;"/></p>
					</div>
					<div style="width:60mm;height:22mm;float:left;text-align: center;font-size:12px;line-height: 11px;">
						<p><img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=30&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" style="margin-top:5px;"/></p>
						<p style="font-size:13px;font-weight: bold;margin-top: 1px;"> '.$allParamArr['ordersInfo']['orders_shipping_code'].'</p>
						<p style="margin-top: 2px;"> Return if undeliverable PO box 5001 istanbul-TURKEY</p>
					</div>
					<div style="width:15.5mm;height:25mm;float:right;text-align: center;">
						<p style="font-size:10px;border:1px solid black;width:35px;height:35px;float:left;margin-top:25px;">pp<br/>Turkey</p>
					</div>
				</div >
				<div style="width:97mm;height:72mm;float:right;">
					<div style="width:64.5mm;height:65mm;float:left;margin-top: 2px;line-height: 12px;">
						<table cellspacing="0" cellpadding="0" style="border:1px solid black;width:64.5mm;height:65mm;">
							<tr>
								<td colspan=3 style="text-align: center;font-size:11px;border-bottom: 1px solid black;height: 7mm;line-height: 12px;">
									<p style="border-right:1px solid black;height: 7mm;width:25mm;float:left;">CUSTOMS DECLARATION</p>
									<p style="border-right:1px solid black;height: 7mm;width:25mm;float:left;">May be opened officially</p>
									<p style="height: 7mm;width:10mm;float:left;line-height: 30px;">CN22</p>
								</td>
							</tr>
							<tr>
								<td colspan="3" style="height:4mm;font-size: 11px;line-height: 8px;">
									DESIGNATED OPERATOR Turkish Post
								</td>
							</tr>
							<tr>
								<td colspan="3" style="height:8mm;border-top:1px solid black;border-bottom:1px solid black;font-size: 11px;line-height: 8px;text-align: left;">
									<table>
										<tr><td><span class="fk" ></span></td><td>GIFT</td><td><span class="fk"></span></td><td>COMMERIAL SAMPLE</td></tr>
										<tr><td><span class="fk" ></span></td><td>PRINTED</td><td><span class="fk" >√</span></td><td>OTHERS(tich as appriate)</td></tr>
									</table>
								</td>
							</tr>
							<tr style="font-size:11px;height:7mm;text-align: center;line-height: 12px;">
								<td style="width:150px;border-right:1px solid black;border-bottom:1px solid black;">
									QUANTITY AND DETAILED DESCRÎPTiON OF
								</td>
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									WEIGHT<br/>(KG)
								</td>
								<td style="border-bottom:1px solid black;">
									VALUE<br/>(USD)
								</td>
							</tr>
							<tr style="height:4mm;font-size: 11px;text-align:center;">
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									 '.$allParamArr['trInfo'].'
								</td>
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									'.$allParamArr['productsInfo']['signal_weight'].'
								</td>
								<td style="border-bottom:1px solid black;">
									'.$allParamArr['productsInfo']['signal_value'].'
								</td>
							</tr>
							<tr style="height:4mm;font-size: 11px;text-align:center;">
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									
								</td>
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									
								</td>
								<td style="border-bottom:1px solid black;">
									
								</td>
							</tr>
							<tr style="height:7mm;font-size:11px;line-height: 12px;">
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									if know,HS Tariff number and country of origin of goods.
								</td>
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									TOTAL<br/>WEIGHT<br/>(KG)
								</td>
								<td style="border-bottom:1px solid black;">
									TOTAL<br/>VALUE<br/>(USD)
								</td>
							</tr>
							<tr style="height:4mm;font-size:11px;line-height: 11px;">
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									ORIGIN:China
								</td>
								<td style="border-right:1px solid black;border-bottom:1px solid black;text-align:center;">
									'.$allParamArr['productsInfo']['total_weight'].'
								</td>
								<td style="border-bottom:1px solid black;text-align:center;">
									'.$allParamArr['productsInfo']['total_value'].'
								</td>
							</tr>
							<tr style="font-size:10px;line-height: 10px;">
								<td colspan="3" style="border-bottom:1px solid black;">
									The undersigned whose name and address are given on the item certify that the particulars given in the declartion are correct and taht this item dose not contain any dangerous article or articles pohibited by legislation or by postal or customs regulaitions
								</td>
			
							</tr>
							<tr style="height:4.5mm;font-size: 11px;">
								<td style="border-right:1px solid black;">
									Signature： SLME
								</td>
								<td colspan="2" >
									DATE '.date('Y-m-d').'
								</td>
							</tr>
						</table>
					</div>
					<div style="border-left:1px solid black;border-bottom:1px solid black;width:31.5mm;height:70mm;float:right;font-size: 12px;">
						<div style="font-weight: bold;margin-top:25px;"><p style="width:116px;float:left;">TO:</p></div>

						<p style="margin-right: 2px;"><b>'.$allParamArr['ordersInfo']['buyer_name'].'</b><br/>
                   '. $allParamArr['ordersInfo']['buyer_address_1'] .' <br/>'. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>
                   '. $allParamArr['ordersInfo']['buyer_city'] .'  '. $allParamArr['ordersInfo']['buyer_state'] .'<br/>'
					. $allParamArr['ordersInfo']['buyer_zip'] .'<br/>
				   Tel:'.$allParamArr['ordersInfo']['buyer_phone'].'<br/>'.$allParamArr['buyerCountry'].'</p>
					</div>
				</div>
				<div style="border-top:1px solid black;height: 4mm;font-size: 12px;clear:both;">
					<p style="margin-left: 6px;width:50mm;float:left">RefNo:<b>'. $allParamArr['ordersInfo']['erp_orders_id'] .'</b></p>
					<p style="margin-left: 6px;width:10mm;float:right">'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'</p>
				</div>
			</div>	
			 	';
	 	return $reStr;
	}
//COE土耳其平邮
	public function printTTPpy_ldb_tlp($allParamArr){
	 	$reStr = '';
	 	$reStr = '
			<style type="text/css">
			*{margin:0 auto;padding:0;}
			.main{border:1px solid black;width:98.5mm;height:98.5mm;}
			.fk{display:inline-block;width:10px;height:8px;border:1px solid #000;padding-top:4px;line-height: 10px;}
			</style>
			<div class="main">
				<div style="border:1px solid black;border-top:none;border-right:none;width:96.5mm;height:22mm;float:right;">
					<div style="width:14mm;height:22mm;float:left;text-align: center;">
						<p><img src="'.site_url('attachments').'/images/ttp1.png" style="width:60px;height:55px;"/></p>
						<p><img src="'.site_url('attachments').'/images/ttp2.png" style="width:25px;height:24px;"/></p>
					</div>
					<div style="width:57mm;height:22mm;float:left;text-align: center;font-size:12px;line-height: 11px;">
						<p><img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=30&r=1&text='.$allParamArr['ordersInfo']['orders_shipping_code'].'&f1=-1&f2=8&a1=&a2=B&a3=" style="margin-top:5px;"/></p>
						<p style="font-size:13px;font-weight: bold;margin-top: 1px;"> '.$allParamArr['ordersInfo']['orders_shipping_code'].'</p>
						<p style="margin-top: 2px;"> Return if undeliverable PO box 5001 istanbul-TURKEY</p>
					</div>
					<div style="width:92px;height:22mm;float:right;text-align: center;">
					<p style="font-size:10px;width:35px;height:35px;float:left;margin-top:20px;">UNTRACK</p>
						<p style="font-size:10px;border:1px solid black;border-right:none;width:35px;height:35px;float:right;margin-top:15px;line-height:10px;">Port Payé<br/>Turkey</p>
					</div>
				</div >
				<div style="width:97mm;height:72mm;float:right;">
					<div style="width:64.5mm;height:65mm;float:left;margin-top: 2px;line-height: 12px;">
						<table cellspacing="0" cellpadding="0" style="border:1px solid black;width:64.5mm;height:65mm;">
							<tr>
								<td colspan=3 style="text-align: center;font-size:11px;border-bottom: 1px solid black;height: 7mm;line-height: 12px;">
									<p style="border-right:1px solid black;height: 7mm;width:25mm;float:left;">CUSTOMS DECLARATION</p>
									<p style="border-right:1px solid black;height: 7mm;width:25mm;float:left;">May be opened officially</p>
									<p style="height: 7mm;width:10mm;float:left;line-height: 30px;">CN22</p>
								</td>
							</tr>
							<tr>
								<td colspan="3" style="height:4mm;font-size: 11px;line-height: 8px;">
									DESIGNATED OPERATOR Turkish Post
								</td>
							</tr>
							<tr>
								<td colspan="3" style="height:8mm;border-top:1px solid black;border-bottom:1px solid black;font-size: 11px;line-height: 8px;text-align: left;">
									<table>
										<tr><td><span class="fk" ></span></td><td>GIFT</td><td><span class="fk"></span></td><td>COMMERIAL SAMPLE</td></tr>
										<tr><td><span class="fk" ></span></td><td>PRINTED</td><td><span class="fk" >√</span></td><td>OTHERS(tich as appriate)</td></tr>
									</table>
								</td>
							</tr>
							<tr style="font-size:11px;height:7mm;text-align: center;line-height: 12px;">
								<td style="width:150px;border-right:1px solid black;border-bottom:1px solid black;">
									QUANTITY AND DETAILED DESCRÎPTiON OF
								</td>
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									WEIGHT<br/>(KG)
								</td>
								<td style="border-bottom:1px solid black;">
									VALUE<br/>(USD)
								</td>
							</tr>
							<tr style="height:4mm;font-size: 11px;text-align:center;">
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									 '.$allParamArr['trInfo'].'
								</td>
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									'.$allParamArr['productsInfo']['signal_weight'].'
								</td>
								<td style="border-bottom:1px solid black;">
									'.$allParamArr['productsInfo']['signal_value'].'
								</td>
							</tr>
							<tr style="height:4mm;font-size: 11px;text-align:center;">
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									
								</td>
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									
								</td>
								<td style="border-bottom:1px solid black;">
									
								</td>
							</tr>
							<tr style="height:7mm;font-size:11px;line-height: 12px;">
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									if know,HS Tariff number and country of origin of goods.
								</td>
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									TOTAL<br/>WEIGHT<br/>(KG)
								</td>
								<td style="border-bottom:1px solid black;">
									TOTAL<br/>VALUE<br/>(USD)
								</td>
							</tr>
							<tr style="height:4mm;font-size:11px;line-height: 11px;">
								<td style="border-right:1px solid black;border-bottom:1px solid black;">
									ORIGIN:China
								</td>
								<td style="border-right:1px solid black;border-bottom:1px solid black;text-align:center;">
									 '.$allParamArr['productsInfo']['total_weight'].'
								</td>
								<td style="border-bottom:1px solid black;text-align:center;">
									'.$allParamArr['productsInfo']['total_value'].'
								</td>
							</tr>
							<tr style="font-size:10px;line-height: 10px;">
								<td colspan="3" style="border-bottom:1px solid black;">
									The undersigned whose name and address are given on the item certify that the particulars given in the declartion are correct and taht this item dose not contain any dangerous article or articles pohibited by legislation or by postal or customs regulaitions
								</td>
			
							</tr>
							<tr style="height:4.5mm;font-size: 11px;">
								<td style="border-right:1px solid black;">
									Signature： SLME
								</td>
								<td colspan="2" >
									DATE '.date('Y-m-d').'
								</td>
							</tr>
						</table>
					</div>
					<div style="border-left:1px solid black;border-bottom:1px solid black;width:31.5mm;height:70mm;float:right;font-size: 12px;">
						<div style="font-weight: bold;margin-top:25px;"><p style="width:116px;float:left;">TO:</p></div>

						<p style="margin-right: 2px;"><b>'.$allParamArr['ordersInfo']['buyer_name'].'</b><br/>
                   '. $allParamArr['ordersInfo']['buyer_address_1'] .' <br/>'. $allParamArr['ordersInfo']['buyer_address_2'] .'<br/>
                   '. $allParamArr['ordersInfo']['buyer_city'] .'  '. $allParamArr['ordersInfo']['buyer_state'] .'<br/>'
					. $allParamArr['ordersInfo']['buyer_zip'] .'<br/>
				   Tel:'.$allParamArr['ordersInfo']['buyer_phone'].'<br/>'.$allParamArr['buyerCountry'].'</p>
					</div>
				</div>
				<div style="border-top:1px solid black;height: 4mm;font-size: 12px;clear:both;">
					<p style="margin-left: 6px;width:50mm;float:left">RefNo:<b>'. $allParamArr['ordersInfo']['erp_orders_id'] .'</b></p>
					<p style="margin-left: 6px;width:10mm;float:right">'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'</p>
				</div>
			</div>
			 	';
	 	return $reStr;
	}
}
?>