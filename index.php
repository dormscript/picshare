<?php
ini_set('memory_limit','128M');
error_reporting(E_ALL & ~E_NOTICE);  //可以忽略此行；设置只提示错误，忽略警告信息
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="common.css" />
<title>图片查看器 </title>
<script type="text/javascript" src="jquery-1.4.3.min.js"></script>
<script type="text/javascript" src="./fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="./fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="./fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<link rel="stylesheet" href="style.css" />
<script type="text/javascript">
		$(document).ready(function() {
 
			$("a[rel=example_group]").fancybox({
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'titlePosition' 	: 'over',
				'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
					return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
				}
			});
 
 
		});
	</script>
</head>
<body>
<div class="box">
  <h1> 图片查看器  </h1>
 
  <?php
include"function.php";
include"page.class.php";
$url = $_GET['u'];
$start = intval($_GET['page']);
if(empty($start))  $page = 1;
else $page = intval($start);
if(!file_exists(PIC_DIR.$url) || !$url ) $url = '/';
$pos = explode('/',$url);
$index = "<a href= '/' class= 'index' >图库</a> ";
foreach($pos as $name)
{
	if(!empty($name))
		{
		    $purl .= "/".$name;
			$postion .= "<a href= '/?u=$purl ' >$name</a> <span>></span>  ";
		}
}
//echo $postion;
 if($postion) 
 {
	 $postion = substr($postion ,0, -1);
	  $postion = $index."<span>:</span>".$postion;
 }
 else $postion = $index;
?>

  <div class="show"> <div class="postion"><?php echo $postion; ?>    </div>
    <ul class="show_pic">
<?php

if($url)
{ 
	$page_size = 30 ;     //每页图片数量
	$star = $page_size * ($page - 1 );   //图片开始值 
	//目录
	$dir =  getDirlist($url, $page_size, $star);
	$dircount = getDircount($url);
	foreach($dir as $file)
	{
	 	echo "<li><a href='?u=".$file['dir']."'><img src=\"".getsmallpic($file['dirpic'])."\" /></a><span>".$file['dirname']."</span></li>\r\n";
	}
 
	if($dircount <  $page_size +  $star )
		{
		   //文件
		   $picno = $page_size * ($page - 1)  > $dircount ? $page_size :  $page_size * $page - $dircount;
		   $picstart =  $page_size * $page - $dircount  - $page_size;
		   if($picstart < 0) $picstart = 0 ;
			$dirpic =  getFile($url, $picno, $picstart);
			foreach($dirpic as $file)
 				{ 
					 echo "<li ><a href='$file[name]' rel='example_group' ><img src=\"".getsmallpic($file['name'])."\"  /></a><span> </span></li>\r\n";
 				}
		}
		 
	$dircount += getFilecount($url);
}
?>
    </ul>
	<span class="clear"></span>
<span class="page"><center>

<?php
 $sub_pages = 10;
 $url = "index.php?";
 foreach($_GET as $key => $value)
 { 
 	if($value && $key != 'page' )
 		$p .= $key."=".urlencode($value)."&";
 }
$p = substr($p, 0,  - 1 ); 
$url .= $p;
 $subPages=new SubPages($page_size,$dircount,$page, $sub_pages, $url."&page=",2);   

?></center>
    </span> 
    <div class="clear"> </div>
  </div>
   
  </div>
</div>
</body>
</html>
