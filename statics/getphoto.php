<?php
	error_reporting(E_ERROR  | E_PARSE);
	date_default_timezone_set('Asia/Shanghai');
	include("config/type.config.php");
	include("class/statistics.class.php");
	foreach($_GET as $key => $val)
		$$key = $val;

	$statisinstance = new statistics($starttime, $endtime);
	
	if($type == '') return '';
	$detaildate = $datex = $datey = array();
	if($child_type == '' ) 
	{
		foreach($querytype[$type]['child'] as $key => $val)
		{  
			 $tmp =  $statisinstance->getdetailresult($type, $key, 'none');	
			 if($tmp)
				$detaildate[] = $tmp;
		}
		$i = 0;
		foreach($detaildate as $k => $v)
		{
			foreach($v as $m => $n)
			{
				$datey[$i][] = $n['0'];
				if(!$i) $datex[$i][] = $n['2'];
			}
			$i++;
		}
	}
	else {
		$detaildate =  $statisinstance->getdetailresult($type, $child_type, 'none', $group);	
		foreach($detaildate as $m => $n)
		{
			$datey['0'][] = $n['0'];
			if(!$m)$datex['0'][] = $n['2'];
		} 
	}
	if(empty($datey))  {  echo json_encode("false");exit();   }
	$ytitle = $statisinstance->gettypename($type);
	foreach($datex['0'] as $key => $val) 
		if($key)
			$s .= "'".$val."',"; 
	$categories = substr($s,0, -1);
	foreach($datey as $k => $v)
	{
		foreach($v as $key => $val)
		{
			if(!$key)   
			{ 
				$str .= " {name: '".$val."',data: [";   
				continue; 
			}
			$str = $str.$val.",";
		}
		$str = substr($str, 0, -1);
		$str .= "]},";
	}

	$series = substr($str, 0, -1);
	$photodata = array(
		'title' => $ytitle, 
		'x' => '['.$categories.']', 
		'y' => '['.$series.']'
		);
	echo  json_encode($photodata);
?>