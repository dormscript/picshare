<?php
include"function.php";
$dir = "/";
$dir .= $argv['1'];
$dirArr = array();

$queuedir =  array( PIC_DIR.$dir);
$i = 0;
$dirnum = $filenum = 1;
while ( !empty($queuedir)) {
	$nextdir = array();
	foreach ($queuedir as $key => $value) {
		echo "\r\n\r\n\r\n 进入目录 :".$value;
		if (false != ($handle = opendir ( $value ))) {
			$i=0;
			while ( false !== ($file = readdir ( $handle )) ) {
				if ($file != "." && $file != ".." && !strpos($file,".")  &&  strpos($file, SMALL_PIC_DIR ) === false && is_dir($value.$file)) { //是目录
					$nextdir[] = $value.$file."/";
					$dirnum ++;
				} elseif(stripos($file, ".jpg") !== false || stripos($file, ".gif") !== false || stripos($file, ".png") !== false ) { //是文件
					$fileurl = preg_replace("%".PIC_DIR."%", PIC_URL , $value.$file);
					echo "\r\n 扫描第". $filenum++ ."张图片:".$value.$file;
					getsmallpic($fileurl);
				}
			}
		}
	}
	$queuedir = $nextdir;
}
echo "\r\n -------------生成缩略图完成-------------\r\n共扫描目录{$dirnum}个，扫描图片{$filenum}张\r\n----------------------------------------\r\n\r\n";