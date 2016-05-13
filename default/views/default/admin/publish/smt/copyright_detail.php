<style type="text/css">
*{font-size:14px;}
table.altrowstable {
	font-family: verdana,arial,sans-serif;
	font-size:11px;
	color:#333333;
	border-width: 1px;
	border-color: #a9c6c9;
	border-collapse: collapse;
}
table.altrowstable th {
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	border-color: #a9c6c9;
}
table.altrowstable td {
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	border-color: #a9c6c9;
}
.oddrowcolor{
	background-color:#d4e3e5;
}
.evenrowcolor{
	background-color:#c3dde0;
}
</style>
<h1 style="font-size:20px;">信息详情</h1>
<table border=1 cellpadding=0 cellspacing=0 class="altrowstable" id="alternatecolor">
	<tr>
		<td style="width:130px;">账号:</td>
		<td><?php echo $data->account;?></td>
	</tr>
	<tr>
		<td>SKU:</td>
		<td><?php echo $data->sku;?></td>
	</tr>
	<tr>
		<td>产品广告ID:</td>
		<td><?php echo $data->pro_id ;?></td>
	</tr>
	<tr>
		<td>投诉人:</td>
		<td><?php echo $data->complainant ;?></td>
	</tr>
	<tr>
		<td>侵权原因:</td>
		<td><?php echo $data->reason  ;?></td>
	</tr>
	<tr>
		<td>商标名:</td>
		<td><?php echo $data->trademark ;?></td>
	</tr>
	<tr>
		<td>知识产权编号:</td>
		<td><?php echo $data->ip_number ;?></td>
	</tr>
	<tr>
		<td>严重程度:</td>
		<td><?php echo $data->degree ;?></td>
	</tr>
	<tr>
		<td>违规大类:</td>
		<td><?php echo $data->violatos_number ;?></td>
	</tr>
	<tr>
		<td>违规小类:</td>
		<td><?php echo $data->violatos_big_type ;?></td>
	</tr>
	<tr>
		<td>是否有效:</td>
		<td><?php echo $data->status?'有效':'无效'  ;?></td>
	</tr>
	<tr>
		<td>分值:</td>
		<td><?php echo $data->score  ;?></td>
	</tr>
	<tr>
		<td>违规生效时间:</td>
		<td><?php echo $data->violatos_start_time  ;?></td>
	</tr>
	<tr>
		<td>违规失效时间:</td>
		<td><?php echo $data->violatos_fail_time   ;?></td>
	</tr>
	<tr>
		<td>销售:</td>
		<td><?php echo $data->seller  ;?></td>
	</tr>
	<tr>
		<td>备注信息:</td>
		<td><?php echo $data->remarks  ;?></td>
	</tr>
	<tr>
		<td>导入时间:</td>
		<td><?php echo $data->import_time  ;?></td>
	</tr>
	<tr>
		<td>导入用户:</td>
		<td><?php echo $data->douser;?></td>
	</tr>
</table>