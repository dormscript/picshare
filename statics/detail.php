<?php
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	date_default_timezone_set('Asia/Shanghai');
	require_once("config/type.config.php");
	require_once("class/statistics.class.php");

	$type = $_GET['type'];
	$child_type = $_GET['child_type'];
	$group = $_GET['group'];
	$endtime  = $_GET['endtime'];
	$starttime = $_GET['starttime'];
	$per = $_GET['per'];
	$output = $_GET['output'];
	
	$nexturl = "index.php?type=$type&endtime=$endtime&starttime=$starttime"; 
	$statisinstance = new statistics($starttime, $endtime);
	$detail = $statisinstance->getdetailresult($type, $child_type, $per, $group);

	list($name, $totalnum, $startdate, $enddate) = $detail['0'];
	unset($detail['0']);
	if($output != 'excel'){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>工厂网 数据统计</title>
<link rel="stylesheet" href="common.css" />
</head>

<body>
<div class="line"><h1>工厂网数据统计 </h1></div>
<div class="line_small">
	<span class="export">
		<a href="<?php echo "http://".$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>&output=excel" >导出到EXCEL</a>
	</span>
	<a href="<?php echo $nexturl;  ?>" style="float:right; margin-right:10px; line-height:25px; color:#0000ff" >返回</a>
	<span> <?php  echo $startdate." - ".$enddate." ".$name;  ?> 的详细信息</span> 
	<div class="detail">
	 <table width="800" border="0" cellpadding="0" cellspacing="0">
		 	<tr>
				<th>日期</th><th>数据</th>
			</tr>
			<?php
			foreach($detail as $key => $val)
				echo "<tr><td width='300'>".$val['1']." 到 ".$val['2']."</td><td>".intval($val['0'])."</td></tr>";
			echo "<tr><td>总计</td><td>".$totalnum."</td></tr>";
			?>
	</table>
	</div>
</div>
</body>
</html>

<?php
	}else { 
	include("class/SimpleExcel.class.php");
	$filename = $name."(".$startdate."-".$enddate.").xls"; 
	header("Content-Type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=".$filename); 
	header("Pragma: no-cache"); 
	header("Expires: 0"); 
	
	$excel=new SimpleExcel();//调用类开始 
	$excel->excelItem(array("日期","数据"));  //第一行标题，可以不要 
	$excel->colsAttrib(array("a","1"));//定义属性，数字型为"1"，字符型为"a" 
	
	foreach($detail as $key => $val)
	{
		$excel->excelWrite(array($val['1']." 到 ".$val['2'],$val['0'])); 
	}
	$excel->excelWrite(array("总计",$totalnum)); 
	$excel->excelEnd();
}
?>