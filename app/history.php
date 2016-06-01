<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class history extends Model
{
    //
    protected $table = 'history';
    
    public $timestamps = false;
    static function newhistory($text,$userid)
    {
    	 $ID=DB::table('history')->insertGetId(
            array(
					
					'userid'=>$userid,
					'text'=>$text
                 ));
        return $ID;
    }
    static function get_history($search,$num)
    {
    	$finds=DB::table('history')->where('text', 'like', $search."%")->orderBy('id', 'desc')->take($num)->get();
        return $finds;
    }
}
