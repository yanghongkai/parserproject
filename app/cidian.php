<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\acessCApi;

class cidian extends Model
{
    //
   
    protected $table = 'cidian';
    
    public $timestamps = false;
    static function get($search)
    {
        	
        $finds=DB::table('cidian')->where('name', '=', $search)->whereNull('deleted_at')->orderBy('id', 'desc')->get();
        return $finds;
    }
    static function newcidian($name,$json,$userid)
    {
    	 $ID=DB::table('cidian')->insertGetId(
            array(
					
					'userid'=>$userid,
					'name'=>$name,
					'json'=>$json
                 ));
        $acessCApi=new acessCApi();
        $selectDebug=$acessCApi->refreshDict($name,$json);
        return $selectDebug;
    }
    static function softdelete($id,$name)
    {
       
    	$date = date('Y-m-d H:i:s');
        
        DB::table('cidian')->where('name', '=', $name)->whereNull('deleted_at')->update(array('deleted_at' => $date));
        
        $acessCApi=new acessCApi();
        $selectDebug=$acessCApi->refreshDict($name,"");
		return $selectDebug;
    }

}
