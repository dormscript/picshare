<?php
class statistics
{
	public $type;
	public $starttime;
	public $endtime;
	function statistics($starttime, $endtime)
	{
		$this->starttime = $starttime;
		$this->endtime = $endtime;
		include("config/type.config.php");
		$this->querytype = $querytype;
		$this->db = $db;
		$this->getdb();
	}
	
	function getdb()
	{
		$this->dbconnect = mysql_connect($this->db['host'], $this->db['user'], $this->db['pswd']) or die("Could not connect: " . mysql_error());
		mysql_select_db("statisticsys");
		mysql_query("SET NAMES UTF-8", $this->dbconnect);
	}
	
	function getmenu()
	{
		 foreach($this->querytype as $key => $val)
				$ret[$key] = $val['name'];
		 return $ret;
	}
	function gettypename($type,$child_type = '', $group = '' )
	{
		if(!empty($this->querytype[$type]['replacenamefunction']))
		{
			$func = $this->querytype[$type]['replacenamefunction']['function'];
			$param = $this->querytype[$type]['replacenamefunction']['param'];
			$replacename = call_user_func($func, $param);
		}
		else if(empty($this->querytype[$type]['replacename']))
			$replacename = $this->querytype[$type]['child'][$child_type]['replacename'];  
		else $replacename = $this->querytype[$type]['replacename'];
		
		if(empty($child_type))
			return $this->querytype[$type]['name'];
		else if(empty($group))
			return $this->querytype[$type]['child'][$child_type]['name'];
		else return $this->querytype[$type]['child'][$child_type]['name']."(".$replacename[$group];
	}
	function getchildresult($type,$ismerge = 0 )  //ismerge 是否合并子分类的数组  0表示合并，1表示不合并 
	{
		$ret = array();
		if(!empty($this->querytype[$type]['replacenamefunction']))
		{
			$func = $this->querytype[$type]['replacenamefunction']['function'];
			$param = $this->querytype[$type]['replacenamefunction']['param'];
			$replacename = call_user_func($func, $param);
		}
		else if(!empty($this->querytype[$type]['replacename']))
			$replacename = $this->querytype[$type]['replacename'];  
		
		foreach($this->querytype[$type]['child'] as $child_type => $child_param)
		{
			$retarray = $this->getqueryresult($type,$child_type);	 
			//echo "$child_type";print_r($retarray);
			if(count($retarray) > 1){
				foreach($retarray as $key => $val)
				{
					if(!empty($replacename))  $rep = $replacename;  
					elseif($child_param['replacename'])   $rep = $child_param['replacename'];   
					if($rep) {
						foreach($rep as $k => $v)
							$replacearray[$k] = $child_param['name']."(".$v.")";
						$group  = array_search($key, $replacearray);
						$result[] = array($key, $val, $type,$child_type, $group);
					}
					else {
						$result[] = array($key, $val, $type,$child_type, $key);
					}
				}
			}else{ 
				foreach($retarray as $k => $v)
				{
						$result[] = array($k,$v,$type,$child_type,''); 
				}
			}
			if($ismerge) {$ret[] = $result;  $result = array();}
		}
		 
		if($ismerge)    return $ret;  
		return $result; 
	}

	function getqueryresult($type, $child_type, $group = '', $starttime = '', $endtime = '' )//根据开始时间和结束时间，查找指定类型的结果，返回数组
	{
		$querytype = $this->querytype;
		if(empty($starttime) || empty($endtime)){
		$starttime = $this->starttime;
		$endtime = $this->endtime;
		}
		
		if($endtime < $starttime ) return false;
		$querymd5 = md5($type.$child_type.$starttime.$endtime);
		
		$queryname =  $querytype[$type]['child'][$child_type]['name'];  //当前操作的名字
		if(!empty($this->querytype[$type]['replacenamefunction']))
		{
			$func = $this->querytype[$type]['replacenamefunction']['function'];
			$param = $this->querytype[$type]['replacenamefunction']['param'];
			$replacename = call_user_func($func, $param);
		}
		else if(empty($querytype[$type]['replacename']))
			$replacename = $querytype[$type]['child'][$child_type]['replacename'];  
		else $replacename = $querytype[$type]['replacename'];

		//先判断此参数是否已经查过，如果存在直接返回结果；若未查过，执行SQL查询，并存入结果表
		$sql = "select  id,result  from statisticsys.sc_result where param = '".$querymd5."'";
		$result = mysql_query($sql, $this->dbconnect);
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$temp = array();
		if(!empty($row['id'])){ //  echo $row['id'];
			$temp = json_decode($row['result'], true);
		}else { 
			$temp = array();
			$querysql = $querytype[$type]['child'][$child_type]['sql'];  
			$sql = str_replace("{starttime}", $starttime,$querysql); 
			//echo "\r\n  查询SQL："; 
			$sql = str_replace("{endtime}", $endtime,$sql);    
			echo "\r\n $sql";  
			$result = mysql_query($sql, $this->dbconnect) or die("Invalid query: " . mysql_error());
			while($row = mysql_fetch_array($result, MYSQL_NUM))
			{
				$temp[$row['0']] = $row['1'];
			}			
			//将查询结果存入表中
			$sql = "insert into statisticsys.sc_result(param, result) values('".$querymd5."', '".addslashes(json_encode($temp))."') ";
			mysql_query($sql, $this->dbconnect) or die("将结果插入表时出错 ：".mysql_error()); 
		}  
		//if(empty($temp)) return '';
		$ret = array();
		if(!empty($replacename)){
				foreach($replacename as $k => $v)
				{
					if(!empty($temp) && array_key_exists($k, $temp)){
						$ret[$queryname."(".$v.")"] = $temp[$k];
						unset($temp[$k]);
					}else $ret[$queryname."(".$v.")"] = 0;
				}  
				//以下是处理在配置文件中不存在的分组ID
				 /*if(!empty($temp))
				{
					foreach($temp as $key => $val)
						$ret[$queryname."(<b>无法识别的group by  :$key</b>)"] = $val;
				}*/
		}
		elseif($this->querytype[$type]['orireplace']) {
			$func = $this->querytype[$type]['orireplace']['function'];
			$param = $this->querytype[$type]['orireplace']['param'];
			$replacename1 = call_user_func($func, $param);
			foreach($temp as $key=>$val) {
				if($replacename1[$key]) {
					$ret[$replacename1[$key]."(ID:".$key.")"] = $val;
				}
				else {
					$ret["".$key."(<b>未定义的groupby 值</b>)"] = $val; 
				}
			}
		}
		else $ret = $temp; 
		if(empty($group)) return $ret;
		else {
			$val = $ret[$queryname."(".$replacename[$group].")"];
			return  array($queryname."(".$replacename[$group].")" => $val);
		}
	}

	function getdetailresult($type,$child_type,$pertype = 'none',$group = '') //返回
	{
		$ret = array();
		$limittime = 0;
		$starttime = $this->starttime;
		$endtime = $this->endtime;
		if($pertype == 'none')
		{
			$days = ( $endtime - $starttime) /3600 /24 ; 
			if($days < 1.1 )  $pertype = 'hour';   
			else if($days < 28) $pertype = 'day';
			else if($days < 150) $pertype = 'week';
			else if($days < 720) $pertype = 'month';
			else $pertype = 'year';
		}
		switch($pertype)
		{
			case "hour":   $timeformat = "H时";  break;  //按小时获取数据
			case "day" :  $timeformat = "d日 "; break;  //按天获取数据
			case "week":   $timeformat = "m月d日 "; break;	//按周获取数据 ，,需要继续调整
			case "month": $timeformat = "m月d日"; break;  //按月获取数据,需要继续调整
			case "year" :   $timeformat = "Y年m月"; break;   // 按年获取数据,需要继续调整
			default :	 $timeformat = "d日 ";  break;
		}
		$end = 0;
		$total = 0;
		$totalname = $this->gettypename($type) ."-". $this->gettypename($type,$child_type, $group);  //当前操作的名字
		$ret['0'] = array();
		
		
		while( $end = getnexttime($starttime,$endtime,$pertype) ){ 
			$result = $this->getqueryresult($type,$child_type, $group, $starttime, $end);
			if(count($result) > 1 ) return false;
			foreach($result as $name => $val)
			{
				$ret[] = array($val, date($timeformat,$starttime),date($timeformat,$end));
				$total += $val;
			}
			$starttime = $end;
		}
		$ret['0'] = array($totalname,$total, date($timeformat,$this->starttime), date($timeformat,$endtime));
		return $ret;
	}
}
//用到的函数
function getnexttime($starttime, $endtime, $type)
	{
		if($type == 'hour') $nexttime = $starttime + 3600;
		else if($type == 'day') $nexttime = $starttime + 3600*24;
		else if($type == 'week') $nexttime = $starttime + 3600*24*7;
		else if($type == 'month') $nexttime = getonetime($starttime, 'month');
		else if($type == 'year')  $nexttime = getonetime($starttime, 'year');
		if($nexttime > $endtime ) $nexttime = 0;
		return $nexttime;
	}
	
	function getonetime($time, $type)
	{
		$date = date("Y-m-d",$time);
		list($year, $month, $day) = explode("-", $date);
		if($type == 'month') {
			if($month + 1 > 12) {
				$month = 1; 
				$year++;
			}else {
				$month++;
			}
		}
		if($type == 'year' ) $year ++;
		return strtotime($year."-".$month."-".$day);
	}

function getcatefromjson($p)
{
	$ret = array();
	$url = $p['0'];
	$json = file_get_contents($url);
	$cate = json_decode($json, true);
	foreach($cate as $k => $v)
	{
		$ret[$k] = $v['catename'];
	}
	return $ret;
}

function createurl($rep)
{
	$url = "http://".$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$flag = 0;
	foreach($rep as $key => $val)
	{
		if(preg_match("/&".($key)."=[^&]*/", $url, $mat))
		{
			$url = preg_replace("/&".($key)."=[^&]*/", "", $url) ;
		}
		else if(preg_match("/[\\?]".($key)."=[^&]*[&]*/", $url, $mat))
		{
			$url = preg_replace("/[\\?]".($key)."=[^&]*[&]*/", "?", $url) ;
		}
		if (strpos($url,"?") && !empty($val) )  
			$url .= '&'.$key.'='.$val; 
		else if (!empty($val)) $url .= '?'.$key.'='.$val;
	}
	return $url;
}

function switchdate($child_result_array)//将数组格式进行转换，方便输出
{
	$row = count($child_result_array);   //列数
	$line = count($child_result_array['0']);   //行数

	$rowname[] = '分类/数据';
	$linename[] = '';
	foreach($child_result_array as $key => $val )
	{
		$rowname[] = substr($val['0']['0'],0, strpos($val['0']['0'],'('));
	    if(!$key)
	 	foreach($val as $k => $v)
		{
			$linename[] = substr($v['0'], strpos($v['0'], '(') + 1, -1 );
		}
	}
	$switchchildarray['0'] = $rowname;
	for($m = 0; $m <= $line; $m ++)
	{
		for($i = 0; $i <= $row ; $i ++)
		{
			if(!$m)  $switchchildarray[$m][$i] = $rowname[$i];
			else if(!$i)     $switchchildarray[$m]['0'] = $linename[$m];
			else $switchchildarray[$m][$i] = $child_result_array[$i-1][$m-1]['1']; 
		}
	}
	return $switchchildarray;
}
function getcate2($p) {
		include "f_cate.php";
		return $f_cate;
}
?>