<?php   
class SimpleExcel
{               
var $rowsNum=0;

var $attrib=array();

var $in_charset = 'UTF-8';

function SimpleExcel()
{   
echo pack("ssssss",0x809,0x8,0x0,0x10,0x0,0x0);
return;
}           

function __construct($inCharset='')
{       
if (!empty($inCharset))
{
$this->in_charset = $inCharset;
}

$this->SimpleExcel();
}

function excelItem($string=array())
{       
for ($i=0;$i<count($string);$i++)
{
$curStr = $string[$i];
$curStr = $this->iconvToData($curStr);

$L = strlen($curStr);
echo pack("ssssss",0x204,8+$L,$this->rowsNum,$i,0x0,$L);
echo $curStr; 
}
$this->rowsNum++;
return;
}           

function colsAttrib($string=array())
{           
$this->attrib=$string;
return;
}       

function excelWrite($string=array())
{   
for ($i=0;$i<count($string);$i++)
{
$curStr = $string[$i];
$curStr = $this->iconvToData($curStr);

if ($this->attrib[$i]=="1")
{
echo pack("sssss",0x203,14,$this->rowsNum,$i,0x0);
echo pack("d",$curStr);
} else {
$L = strlen($curStr);
echo pack("ssssss",0x204,8+$L,$this->rowsNum,$i,0x0,$L);
echo $curStr;
}
}

$this->rowsNum++;
}

function excelEnd()
{
echo pack("ss",0x0A,0x00);
return;
}

function iconvToData($data)
{
return iconv($this->in_charset,'gb2312',$data);
}
}
?>