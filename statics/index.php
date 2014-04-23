<?php
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	 ini_set("max_execution_time", "1800"); 
	date_default_timezone_set('Asia/Shanghai');
	include("config/type.config.php");
	include("class/statistics.class.php");
	$type = $_GET['type'];  
	
	/*
	$endtime = strtotime($_GET['endtime']);
	$starttime = strtotime($_GET['starttime']);
	*/
	/** 
	* 2012/12/13修改，处理时间
	*/
	if(!empty($_GET['endtime']) && is_numeric($_GET['endtime']) && strlen($_GET['endtime']) == 10) {
		$endtime = $_GET['endtime'];
	}
	else {
		$endtime = strtotime($_GET['endtime']);
	}

	if(!empty($_GET['starttime']) && is_numeric($_GET['starttime']) && strlen($_GET['starttime']) == 10) {
		$starttime = $_GET['starttime'];
	}
	else {
		$starttime = strtotime($_GET['starttime']);
	}

	if(!$endtime || !$starttime || $endtime > time() || $starttime > time() || $starttime > $endtime)  //设置默认时间
	{
		$endtime  = strtotime(date("Y-m-d 0:0:0",time()));
		$starttime = $endtime - 3600 * 24 * 7;
	}
	if($starttime < 1104595200 ) $starttime = 0;
	$detailurl = "getphoto.php?type=$type&starttime=$starttime&endtime=$endtime";
	$statisinstance = new statistics($starttime, $endtime);	
	
	
	$menu = $statisinstance->getmenu();   //左侧导航菜单
	$iscreateline = 1;  
	$ismerge = 0;
	
	if(empty($type)) {
		$type = 'user';
	}

	if($type == 'club_industy') {  //特殊分类下的操作
		$listarray = $statisinstance->getchildresult($type, 1);
		foreach($listarray as $key=>$val) {
			$allresult[] = $val;
		}
	} 
	else if(empty($type))   //获取所有分类下的数据 
	{
		$iscreateline = 0;   
		$allresult = array();
		foreach($menu as $key => $val)
		{
			if(  $key == 'industry' || $key == 'comtype')  $ismerge = 1;
			else $ismerge = 0;
			$allresult[] =  $statisinstance->getchildresult($key,$ismerge); 
		}	 
	}
	else{	
		if( $type == 'industry' || $type == 'comtype' ) { $ismerge = 1; $iscreateline = 0;}
		$child_result_array = $statisinstance->getchildresult($type, $ismerge);   //获取当前分类下的所有数据 
	}
 
//将数据显示到excel
if($_GET['output'] == 'excel')
{
	include("class/SimpleExcel.class.php");
	$filename = $type."(".date("Ymd", $starttime)."-".date("Ymd", $endtime).").xls"; 
	header("Content-Type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=".$filename); 
	header("Pragma: no-cache"); 
	header("Expires: 0"); 

	$excel=new SimpleExcel();//调用类开始 
	$excel->colsAttrib(array("a","a","a","a","a","a","a","a","a","a"));//定义属性，数字型为"1"，字符型为"a" 
	
	if(!empty($child_result_array)){
		if(!$ismerge){
			$excel->excelWrite(array('分类','数据')); 
			foreach($child_result_array as $key => $val)
			{
				$excel->excelWrite(array($val['0'],$val['1'])); 
			}
		}
		else {   
			$switchchildarray = array();
			$switchchildarray = switchdate($child_result_array);//将数组格式进行转换，方便输出
			foreach($switchchildarray as $k => $v)
			{
				$tmparray = array();
				foreach($v as $key => $val )
					$tmparray[] = $val;
				$excel->excelWrite($tmparray); 
			}
		}
	}
	else {    //显示所有数据
	foreach($allresult as $key => $childresult)
		{
			if(!is_array($childresult['0']['0'])){
				$excel->excelWrite(array()); 
				$excel->excelWrite(array($menu[$childresult['0']['2']])); 
				foreach($childresult as $key => $val)
				{
					$url  = "detail.php?starttime=".$starttime."&endtime=".$endtime."&type=".$val['2']."&child_type=".$val['3']."&group=".$val['4']."&per=";
					$excel->excelWrite(array($val['0'], $val['1'])); 
				}
			}
			else {
				$excel->excelWrite(array()); 
				$excel->excelWrite(array($menu[$childresult['0']['0']['2']])); 
				$switchchildarray = array();
				$switchchildarray = switchdate($childresult);//将数组格式进行转换，方便输出
				
				foreach($switchchildarray as $k => $v)
				{
					$tmparray1 = array();
					foreach($v as $key => $val )
						$tmparray1[] =  $val;
					$excel->excelWrite($tmparray1); 
				}
			}
		}
	}
	$excel->excelEnd();
	exit();
}

//显示数据到页面  
$echodate = '';
if(!empty($child_result_array)){
	if(!$ismerge){
		$echodate =  "<table width='800' border='0' cellpadding='0' cellspacing='0'><tr>
					<th>分类</th><th>数据</th><th>查看详情</th>
					</tr>";
		foreach($child_result_array as $key => $val)
		{
			$url  = "detail.php?starttime=".$starttime."&endtime=".$endtime."&type=".$val['2']."&child_type=".$val['3']."&group=".$val['4']."&per=";
			$echodate .= "<tr>
			<td  width='300' >".$val['0']."</td><td>".$val['1']."</td> 
			<td><a href='".$url."day'>天</a>
			<a href='".$url."week'>周</a>
			<a href='".$url."month'>月</a> 
			</td></tr>";  
		}
		$echodate .= "</table>";
	}
	else 
	{   
		$switchchildarray = array();
		$switchchildarray = switchdate($child_result_array);
		//将数组格式进行转换，方便输出
		$echodate .= "<table width='800' border='0' cellpadding='0' cellspacing='0'>";
		foreach($switchchildarray as $k => $v)
		{
			$echodate .= "<tr>";
			foreach($v as $key => $val )
				$echodate .= "<td>$val</td>";
			$echodate .= "</tr>";
		}
	}
}
else {    //显示所有数据
	foreach($allresult as $key => $childresult)
	{
		$echodate .= "<table width='800' border='0' cellpadding='0' cellspacing='0'>";
		if(!is_array($childresult['0']['0'])){
			if($type == 'club_industy')
				$echodate .= "<tr><th colspan='2' height='35'> ".$statisinstance->gettypename($childresult['0']['2'],$childresult['0']['3'])." </th></tr>";
			else $echodate .= "<tr><th colspan='2' height='35'><a href='".createurl(array('type' => $childresult['0']['2'] ) )."' >".$menu[$childresult['0']['2']]."</a></th></tr>";
			foreach($childresult as $key => $val)
			{
				$url  = "detail.php?starttime=".$starttime."&endtime=".$endtime."&type=".$val['2']."&child_type=".$val['3']."&group=".$val['4']."&per=";
				 $echodate .=  "<tr>
			<td  width='350'>".$val['0']."</td><td>".$val['1']."</td></tr>";
			}
			$echodate .= "</table>";
		}
		else {
			$echodate .= "<tr><th colspan='".(count($childresult)+1)."'  height='35'><a href='".createurl(array('type' => $childresult['0']['0']['2'] ) )."' >".$menu[$childresult['0']['0']['2']]."</a></th></tr>";
			$switchchildarray = array();
			$switchchildarray = switchdate($childresult);
			//将数组格式进行转换，方便输出
			foreach($switchchildarray as $k => $v)
			{
				$echodate .=  "<tr>";
				foreach($v as $key => $val )
					$echodate .=  "<td>$val</td>";
				$echodate .=  "</tr>";
			}
			$echodate .= "</table>";
		}
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>工厂网 数据统计</title>
<link rel="stylesheet" href="common.css" />
<script src="calendar.js" type="text/javascript"></script>  
</head>

<body>
<div class="line"><h1>工厂网数据统计 </h1></div>
<div class="line border">
<form action="index.php" method="get" name="settime" >
<?php
	$param = $_GET;
	unset($param['starttime'], $param['endtime'], $param['submit']);
	foreach($param as $p => $val )
	{	
		echo "<input type='hidden' name = '".$p."' value='".$val."' />";
	}
?>
开始时间： <input type="text"  value='<?php echo date("Y-m-d",$starttime); ?>' maxlength="100" id="date" name="starttime" onclick="SelectDate(this,'yyyy-MM-dd')" style="width:265px;cursor:pointer" />
结束时间： <input type="text"  value='<?php echo date("Y-m-d",$endtime); ?>' maxlength="100" id="date" name="endtime" 	onclick="SelectDate(this,'yyyy-MM-dd')" style="width:265px;cursor:pointer" />
<input type="submit"   name="" value="查询" />
</form>
</div>
<div class="line border">
	<div class="menu">
		<ul class="menu_title">
			<?php
				if(empty($type)) $m = " class = 'now'";
				else $m = '';
				echo "<li".$m."><a href='".createurl(array('type' => ''))."' >全部</a></li>";
				foreach($menu as $key => $val)
				{
					if($key == $type){ 
						echo "<li class='now'><a href='".createurl(array('type' => $key))."' >$val</a></li>";
					}else{
						echo "<li><a href='".createurl(array('type' => $key))."' >$val</a></li>";
					}
				}
			?>
		</ul>
	</div>
	<div class="data">
		 <span class="export"><a href="<?php echo createurl(array('output' => 'excel'));  ?>" >导出到EXCEL</a></span>
		  <?php if ($iscreateline)  {  ?><span class="export"><a href="javascript:;" class="view">查看走势图</a></span>  <?php } ?>
		<span>
			<?php echo $statisinstance->gettypename($type);   echo "（";  echo date("Y-m-d ",$starttime)."  到  ".date("Y-m-d ",$endtime).") ";     ?>
		的相关信息
		</span>
		<div class="detail">	
			<p class="loading" style="display:none"><img src="http://cn-style.gcimg.net/v6/js/p/dialog/skins/icons/loading.gif" />正在加载数据....</p>
			<div id="container" style="min-width: 802px; height: 400px; margin: 10 auto; display:none;"></div>
			<?php
				echo $echodate;
			?>
			
		</div>
	</div> 
</div>
		<script type="text/javascript" src="jquery-1.8.2.min.js"></script>
		<script src="js/highcharts.js"></script>
		<script src="js/modules/exporting.js"></script>

<script type="text/javascript">
	function creatHighCharts(title,x,y){
		chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'spline'
            },
            title: {
                text: '',
                x: -20 //center
            },
            subtitle: {
                text:title,
                x: -20
            },
            xAxis: {
                categories: x
            },
            yAxis: {
                title: {
                    text: '数量'
                },
                labels: {
                    formatter: function() {
                        return this.value +''
                    }
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true
            },
            plotOptions: {
                spline: {
                    marker: {
                        radius: 4,
                        lineColor: '#666666',
                        lineWidth: 1
                    }
                }
            },
            series: y
        });
	}
	$('.view').bind('click',function(){
		$('.loading').show();
		var u= '<?php echo $detailurl; ?>';
		$.getJSON(u,function(d){
		if(d == "false"){
			$('#container').html("不支持加载此曲线！");
			$('#container').css("height", 30); 
			$('#container').show(); 
			$('.loading').hide();
			exit();
		}
		 var t=d.title;
		 var x=eval(d.x);
		 var y=eval(d.y);
		creatHighCharts(t,x,y);
		$('.loading').hide();
		$('#container').show();
	});
	});
</script>
</body>
</html>