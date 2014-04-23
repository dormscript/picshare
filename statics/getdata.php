<?php
	//error_reporting(E_ERROR | E_WARNING | E_PARSE);
	 ini_set("max_execution_time", "1800"); 
	date_default_timezone_set('Asia/Shanghai');
	include("class/statistics.class.php");

$data = date("Y-m-d"); 
$data = strtotime($data); 
while($i < 200) {
	$i++;
	$daydomain = $data - 3600 * 24;
	$weekdoamin = $data - 3600*24*7; 
	$monthdomain = getlasettime($data, 'month'); 
	$yeardomain= getlasettime($data, 'year');

	getdaydate($data, $daydomain,"day"); //获取昨天一天数据
	getdaydate($data, $weekdoamin,"week"); //获取上一周数据
	getdaydate($data, $monthdomain,"month");//获取上一月数据
	getdaydate($data, $yeardomain,"year");//获取上一年数据
	$data -= 86400; 
	sleep(10);
}
function getlasettime($time, $type) {
	$date = date("Y-m-d",$time);
	list($year, $month, $day) = explode("-", $date);
	if($type == 'month') {
		if($month == 1) {
			$month = 12; 
			$year--;
		}else {
			$month--;
		}
	}
	if($type == 'year' ) $year --;
	return strtotime($year."-".$month."-".$day);
}
 
function getdaydate($endtime, $starttime, $type) 
{
	include	__dir__."/config/type.config.php";
	$statisinstance = new statistics($starttime, $endtime);	
	$menu = $querytype;
	foreach($menu as $key => $val)
	{
		foreach($val['child'] as $k=>$v) {
			//echo "$key, $k, $type \r\n"; 
			$detail = $statisinstance->getdetailresult($key, $k, $type);
		}
	}
}