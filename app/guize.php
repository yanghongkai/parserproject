<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\acessCApi;
class guize extends Model
{
    //
   
    protected $table = 'guize';
    
    public $timestamps = false;
    static function get($search)
    {
    	
        $finds=DB::table('guize')->where('name', '=', $search)->whereNull('deleted_at')->orderBy('id', 'desc')->take(13)->get();
        return $finds;
    }
    static function newguize($name,$json,$userid)
    {
    	 $ID=DB::table('guize')->insertGetId(
            array(
					
					'userid'=>$userid,
					'name'=>$name,
					'json'=>$json
                 ));
        $acessCApi=new acessCApi();
        $selectDebug=$acessCApi->refreshRule($name,$json);
        return $selectDebug;
    }
    static function softdelete($id,$name)
    {

        $date = date('Y-m-d H:i:s');
        
        DB::table('guize')->where('name', '=', $name)->whereNull('deleted_at')->update(array('deleted_at' => $date));
        
        $acessCApi=new acessCApi();
        $selectDebug=$acessCApi->refreshRule($name,"");
        return $selectDebug;
    }

}
