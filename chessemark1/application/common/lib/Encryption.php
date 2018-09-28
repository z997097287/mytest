<?php
namespace app\common\lib;
class Encryption 
{
	public function __construct($str) 
	{
		$this->enstr = $str;
	}

    private function get_shal()
	{
		return sha1($this->enstr);
	}

	private function get_md5()
	{
		return md5($this->enstr);
	}

	private function get_jxqy3()
	{
		$tmpMS = $this->get_shal().$this->get_md5();
		$tmpNewStr = substr($tmpMS,0,9).'o'.substr($tmpMS,10,9).'u'.substr($tmpMS,20,9).'x'.substr($tmpMS,30,9).'u'.substr($tmpMS,40,9).'a'.substr($tmpMS,50,9).'n'.substr
($tmpMS,60,12);
		$tmpNewStr = substr($tmpNewStr,-36).substr($tmpNewStr,0,36);
		$tmpNewStr = substr($tmpNewStr,0,70);
		$tmpNewStr = substr($tmpNewStr,0,14).'l'.substr($tmpNewStr,14,14).'o'.substr($tmpNewStr,28,14).'t'.substr($tmpNewStr,32,14).'u'.substr($tmpNewStr,56,14).'s';
		return $tmpNewStr;
	}
	
	public function to_string()
	{
		$tmpstr = $this->get_jxqy3();
		$tmpstr = substr($tmpstr,-35).substr($tmpstr,0,40);
		return $tmpstr;
	}
}