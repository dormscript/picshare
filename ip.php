<?php
date_default_timezone_set('PRC');
session_start();
if(!$_SESSION['uid'])
{
	$_SESSION['uid'] = rand(0,1000000);
    $iipp = $_SERVER["REMOTE_ADDR"];
	if(!$iipp) $iipp =  '000.000.0.000';
	
	$filename = 'test.txt';
	$somecontent = "$iipp			".date('Y-m-d H:i:s')."\n";
	if (is_writable($filename)) {
		if (!$handle = fopen($filename, 'a')) {
			echo "���ܴ��ļ� $filename";
		     exit;
		  }

       if (fwrite($handle, $somecontent) === FALSE) {
			echo "����д�뵽�ļ� $filename";
			exit;
		 }
	  fclose($handle);
	}
else {
    echo "�ļ� $filename ����д";
	}
}
?>