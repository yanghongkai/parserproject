<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class acessCApi
{
	public $scip="http://localhost:8080/";

 	function acessCApi()
 	{

 	}
  	function getLattice($sentence,$selectDebug)
	{

		$scip=$this->scip;
		$opts = Array('http' => Array('method' => "GET"));
		$context = stream_context_create($opts);

		$sentence=iconv('utf-8','gbk', $sentence);
		$sentence=urlencode($sentence);
		$selectDebug=iconv('utf-8','gbk', $selectDebug);
		$selectDebug=urlencode($selectDebug);
		$url='input='.$sentence.'&operation='.$selectDebug;
		$url=$scip.'jparse?'.$url;

		$page = file_get_contents($url, false, $context) OR die("Error connecting to ".$scip.": connection failure or incorrect settings!");
		$page=iconv('gbk','utf-8', $page);
		return $page;
 	}

	function refreshDict($name,$json)
	{

	   	$path=$this->getpath();
	    $filename=$path."\\dict\\".$name;
        $filename=iconv('utf-8','gbk', $filename);
        $json=iconv('utf-8','gbk', $json);
        $fp= fopen($filename, "w");
        if($fp)
        {
            fwrite($fp,$json);
        }
        fclose($fp);
		$scip=$this->scip;
		$opts = Array('http' => Array('method' => "GET"));
		$context = stream_context_create($opts);

		$name=iconv('utf-8','gbk',$name);
		$name=urlencode($name);
		$url='input='.$name;
		$url=$scip.'idxdict?'.$url;
		$page = file_get_contents($url, false, $context) OR die("Error connecting to ".$scip.": connection failure or incorrect settings!");
		$page=iconv('gbk','utf-8', $page);
		return $page;
	}
	function refreshRule($name,$json)
	{
		$path=$this->getpath();
	    $filename=$path."\\rule\\".$name;
        $filename=iconv('utf-8','gbk', $filename);
        $json=iconv('utf-8','gbk', $json);
        $fp= fopen($filename, "w");
        if($fp)
        {
            fwrite($fp,$json);
        }
        fclose($fp);
		$scip=$this->scip;
		$opts = Array('http' => Array('method' => "GET"));
		$context = stream_context_create($opts);


		$name=iconv('utf-8','gbk',$name);
		$name=urlencode($name);

		$url='input='.$name;
		$url=$scip.'idxrule?'.$url;
		$page = file_get_contents($url, false, $context) OR die("Error connecting to ".$scip.": connection failure or incorrect settings!");

		$page=iconv('gbk','utf-8', $page);
		return $page;
	}
	function getpath()
	{
		$scip=$this->scip;
		$opts = Array('http' => Array('method' => "GET"));
		$context = stream_context_create($opts);
		$url=$scip.'getpath';
		$page = file_get_contents($url, false, $context) OR die("Error connecting to ".$scip.": connection failure or incorrect settings!");

		$page=iconv('gbk','utf-8', $page);
		return $page;


	}
	function getFunc()
	{
		$scip=$this->scip;
		$opts = Array('http' => Array('method' => "GET"));
		$context = stream_context_create($opts);
		$url=$scip.'getfunc';
		$page = file_get_contents($url, false, $context) OR die("Error connecting to ".$scip.": connection failure or incorrect settings!");
		$page=iconv('gbk','utf-8', $page);
		return $page;
	}


}
