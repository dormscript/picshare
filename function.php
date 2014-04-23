<?php 
define('PIC_DIR',  "/Network/photo");  
define('PIC_URL',  "/share");    
define('SMALL_PIC_DIR', "s_pic"); 
$db = array(
	'host'=>'localhost',
	'user'=>'root',
	'pswd'=>'123456',
	'database' => 'picshare'
);
/**
 * ??ȡ?ļ?Ŀ¼?б???Ŀ¼?е?1??ͼƬ,?÷???????????
 * 
 * @author ??˧Ӫ
 * @param $dir ??Ŀ¼
* @param $startno ?ӵڼ???Ŀ¼??ʼ??ȡ
 * @param $dirno ??Ҫ??ȡ????Ŀ¼?ĸ???
 */
/*
$dir  = getDirlist('/',1,30);
 print_r($dir);
*/
function getDirlist($dir, $dirno, $startno = 0) {
	if($dir != '/') $dir .= "/";
//$dir = "";
	$dirArray= array();
	if (false != ($handle = opendir ( PIC_DIR.$dir ))) {
		$i=0;
		while ( false !== ($file = readdir ( $handle )) )
			{
				if ($file != "." && $file != ".." && !strpos($file,".")  &&  strpos($file, SMALL_PIC_DIR ) === false && is_dir(PIC_DIR.$dir.$file)  ) 
					{   
							if($i == $dirno+$startno)    break;
							if($i >= $startno) {
							$dirArray[$i]['dir']= $dir.$file;
							$dirArray[$i]['dirname']= $file;
							if(file_exists(PIC_DIR.$dir.$file.'\index.jpg'))  $dirArray[$i]['dirpic'] = PIC_URL.$dir.$file."/".'index.jpg';
							else if(file_exists(PIC_DIR.$dir.$file.'\index.gif')) $dirArray[$i]['dirpic']=PIC_URL.$dir.$file."/".'index.gif';
							else 
							{ 
								if( ($childhandle = opendir(PIC_DIR.$dir.$file) ) !== false )
								{
									while($childpic = readdir ($childhandle))   //??Ŀ¼???ҳ???1??ͼƬ ??
									{
										if ( strpos($childpic,".jpg") || strpos($childpic,".gif") || strpos($childpic,".JPG") || strpos($childpic,".JPEG")  ) 
										{
											$dirArray[$i]['dirpic'] = PIC_URL.$dir.$file."/".$childpic;
											break;
										}
									}	
									if(!$dirArray[$i]['dirpic'])  
									{
										//echo "child dir no pic!   continue read child dir!";
										rewinddir($childhandle);
										while($childpic = readdir ($childhandle))   //??Ŀ¼???ҳ???1??ͼƬ ??
										{ 
											if ($childpic != "." && $childpic != ".." && !strpos($childpic,".")  &&  strpos($childpic, SMALL_PIC_DIR ) === false && is_dir(PIC_DIR.$dir.$file."/".$childpic)  ) 
											{
												$pic = getdirpic($dir.$file."/".$childpic);
												if($pic)
												{
													$dirArray[$i]['dirpic'] = $pic;
													break;
												}
											}
										}	
										if(!$dirArray[$i]['dirpic'] )
											$dirArray[$i]['dirpic'] = '/images/nopic.jpg';
									}
								}
							}
						
						}
						$i++;
					}
			}
		//?رվ???
		closedir ( $handle );
	}
	return $dirArray;
}
 
//??ָ??Ŀ¼?²???ͼƬ,???ȱ???
function getdirpic ($dir)
{
	if( ($childhandle = opendir(PIC_DIR.$dir) ) !== false )
	 {
		 while($childpic = readdir ($childhandle))   //??Ŀ¼???ҳ???1??ͼƬ ??
			{
				if ( strpos($childpic,".jpg") || strpos($childpic,".gif") || strpos($childpic,".JPG") || strpos($childpic,".JPEG")  ) 
					{
						return PIC_URL.$dir."/".$childpic;
				   }
			}	
		rewinddir($childhandle);
		while($childpic = readdir ($childhandle))   //??Ŀ¼???ҳ???1??ͼƬ
		{ 
			if ($childpic != "." && $childpic != ".." && !strpos($childpic,".")  &&  strpos($childpic, SMALL_PIC_DIR ) === false && is_dir(PIC_DIR.$dir."/".$childpic)  ) 
			{
					$pic = getdirpic($dir."/".$childpic);
					if($pic)
					 {
						 return  $pic;
					}
			}
		}	
		
	 }
	 else return '';
}
 
//??ȡ?ļ???????
function getDircount($dir) {
	if($dir != '/') $dir .= "/";
   $dirArray[]=NULL;
   $i = 0;
	if (false != ($handle = opendir ( PIC_DIR.$dir ))) { 
		while ( false !== ($file = readdir ( $handle )) )
			{ 
				if ($file != "." && $file != ".."&& !strpos($file,".")  && strpos($file, SMALL_PIC_DIR ) === false && is_dir(PIC_DIR.$dir.$file)  ) 
					{
						$i ++;
					}
			}
		//?رվ???
		closedir ( $handle );
	}
	return $i;
}


//??ȡ?ļ??б?
function getFile($dir, $dirno, $startno = 0) {
	if($dir != '/') $dir .= "/";
	$fileArray = array();
	if (false != ($handle = opendir ( PIC_DIR.$dir ))) 
		{
		$i=0;
		while ( false !== ($file = readdir ( $handle )))  {
			$newfile = strtolower($file);
			if(substr($newfile,0,1) != "." && $newfile != $file) {
				$file = filenamerename(PIC_DIR.$dir.$file);
			}
			if($file != "." && $file != ".." &&  (strpos($file,".jpg")  || strpos($file,".gif") ||  strpos($file,".png") ) )
			{ 
					if($i== $dirno + $startno)  break;
					if($i >= $startno)
						{
						 	$fileArray[$i]['name'] =  PIC_URL.$dir.$file;
							//$fileArray[$i]['up'] =  getupcount(PIC_URL.$dir.$file);
						}
					$i++;
			}
		 }
		closedir ( $handle );
	 }
	return $fileArray;
}
function filenamerename($filename) {	
	$dir = dirname($filename);
	$oldname = basename($filename);
	$newname = strtolower($oldname);
	if(rename($filename,$dir."/".$newname)) {
		return $newname;
	} else {
		return '.';
	}
}
function getupcount($url)
{
	$db = getdb();
	$md5value = md5($url);
	$sql = "select a.up  from picture a left join pinglun b on a.id = b.pid where a.md5value = '".$md5value."'    ";
	$query = mysql_query($sql);
	$row = mysql_fetch_array($query, MYSQL_ASSOC);
	if(empty($row)) return 0;
	else return $row['comment'];
}

//??ȡ?ļ?????
 function getFilecount($dir) {
	if (false != ($handle = opendir ( PIC_DIR.$dir ))) 
		{
		$i=0;
		while ( false !== ($file = readdir ( $handle ))  ) 
			{
			$file = strtolower($file);
			if($file != "." && $file != ".." &&  (strpos($file,".jpg")  || strpos($file,".gif") ||  strpos($file,".png") ) )
				{
					$i++;
				}
			}
		closedir ( $handle );
	 }
	return $i;
}

function getsmallpic($picurl)
{
	if($picurl == '/images/nopic.jpg' ) return $picurl;
	$realurl = preg_replace("%".PIC_URL."%", PIC_DIR , $picurl);
	$dir  = dirname($realurl); 
//	if(substr($picurl, -1) != '\\') $dir .= '\\';
	$file = basename($realurl);
	if(!file_exists($dir."/".SMALL_PIC_DIR."/".$file))
	{
		if(!file_exists($dir."/".SMALL_PIC_DIR))
			mkdir($dir."/".SMALL_PIC_DIR);
		img2thumb($realurl, $dir."/".SMALL_PIC_DIR."/".$file, 170, 150, 0);
	}
	$picurl = preg_replace("%".addslashes(PIC_DIR)."%", PIC_URL , $dir."/".SMALL_PIC_DIR."/".$file); 
        $picurl = str_replace("\\", '/',  $picurl);
	$picurl = str_replace("//", '/',  $picurl);
	return $picurl;
}
 
function fileext($file)
{
    return pathinfo($file, PATHINFO_EXTENSION);
}
 
/**
 * ????????ͼ
 * @author yangzhiguo0903@163.com
 * @param string     Դͼ??????????ַ{???ļ???????׺??}
 * @param string     Ŀ??ͼ??????????ַ{???ļ???????׺??}
 * @param int        ????ͼ??{0:??ʱĿ???߶Ȳ???Ϊ0??Ŀ??????ΪԴͼ??*(Ŀ???߶?/Դͼ??)}
 * @param int        ????ͼ??{0:??ʱĿ?????Ȳ???Ϊ0??Ŀ???߶?ΪԴͼ??*(Ŀ??????/Դͼ??)}
 * @param int        ?Ƿ?????{?문߱?????0}
 * @param int/float  ????{0:??????, 0<this<1:???ŵ???Ӧ????(??ʱ???????ƺͲ??о?ʧЧ)}
 * @return boolean
 */
function img2thumb($src_img, $dst_img, $width = 75, $height = 75, $cut = 0, $proportion = 0)
{
    if(!is_file($src_img))
    {
        return false;
    }
    $ot =  strtolower(fileext($dst_img));
    $otfunc = 'image' . ($ot == 'jpg' ? 'jpeg' : $ot);
    $srcinfo = getimagesize($src_img);
    $src_w = $srcinfo[0];
    $src_h = $srcinfo[1];
    $type  = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
    $createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);

    $dst_h = $height;
    $dst_w = $width;
    $x = $y = 0;

    /**
     * ????ͼ??????Դͼ?ߴ磨ǰ???ǿ?????ֻ??һ????
     */
    if(($width> $src_w && $height> $src_h) || ($height> $src_h && $width == 0) || ($width> $src_w && $height == 0))
    {
        $proportion = 1;
    }
    if($width> $src_w)
    {
        $dst_w = $width = $src_w;
    }
    if($height> $src_h)
    {
        $dst_h = $height = $src_h;
    }

    if(!$width && !$height && !$proportion)
    {
        return false;
    }
    if(!$proportion)
    {
        if($cut == 0)
        {
            if($dst_w && $dst_h)
            {
                if($dst_w/$src_w> $dst_h/$src_h)
                {
                    $dst_w = $src_w * ($dst_h / $src_h);
                    $x = 0 - ($dst_w - $width) / 2;
                }
                else
                {
                    $dst_h = $src_h * ($dst_w / $src_w);
                    $y = 0 - ($dst_h - $height) / 2;
                }
            }
            else if($dst_w xor $dst_h)
            {
                if($dst_w && !$dst_h)  //?п??޸?
                {
                    $propor = $dst_w / $src_w;
                    $height = $dst_h  = $src_h * $propor;
                }
                else if(!$dst_w && $dst_h)  //?и??޿?
                {
                    $propor = $dst_h / $src_h;
                    $width  = $dst_w = $src_w * $propor;
                }
            }
        }
        else
        {
            if(!$dst_h)  //?ü?ʱ?޸?
            {
                $height = $dst_h = $dst_w;
            }
            if(!$dst_w)  //?ü?ʱ?޿?
            {
                $width = $dst_w = $dst_h;
            }
            $propor = min(max($dst_w / $src_w, $dst_h / $src_h), 1);
            $dst_w = (int)round($src_w * $propor);
            $dst_h = (int)round($src_h * $propor);
            $x = ($width - $dst_w) / 2;
            $y = ($height - $dst_h) / 2;
        }
    }
    else
    {
        $proportion = min($proportion, 1);
        $height = $dst_h = $src_h * $proportion;
        $width  = $dst_w = $src_w * $proportion;
    }
 
    $src = $createfun($src_img);
    $dst = imagecreatetruecolor($width ? $width : $dst_w, $height ? $height : $dst_h);
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefill($dst, 0, 0, $white);

    if(function_exists('imagecopyresampled'))
    {
        imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
    }
    else
    {
        imagecopyresized($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
    }
    $otfunc($dst, $dst_img);
    imagedestroy($dst);
    imagedestroy($src);
    return true;
}

 
function getdb()
{
	global $db;
	$dbread = mysql_connect($db['host'], $db['user'], $db['pswd']) or die('???????ݿ?ʧ??');
	mysql_query("SET NAMES 'UTF8'",	$dbread);
	if($db['database'])	mysql_select_db($db['database'], $dbread);
	return $dbread;
}
?>
