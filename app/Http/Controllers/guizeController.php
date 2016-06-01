<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\guize;
use App\history;
use Redirect, Input, Auth;
class guizeController extends Controller
{
    //
   	public function index(Request $request)
    {
    	if(Auth::guest())
        {
           $errors[0]='请先登录';
           return Redirect::to('/auth/namelogin');
        }
        else
        {
        	$item="";
  	    	$history="";
  	    	$info="";
  			  $search="";
  			  $buttons='<div class="col-md-2 col-md-offset-3">
  					<button type="button" onclick=\' location.href="./newguize" \' class="btn btn-default">增加</button>
  				</div>';
	        if($request->input('search'))
	        {
	        	$search=$request->input('search');
            history::newhistory("搜索规则：".$search,Auth::user()->id);
	        	$finds=guize::get($search);
	        	if(count($finds)==0)
	        	{
	        		$info="没有相关的记录";
	        	}
	        	else if(count($finds)==1)
	        	{
	        	$item=$finds[0];
		        $info='<div class="leftspace"><label for="name">规则名:</label>
					<input type="text" class="form-control" id="name" name="name" value="'.$item->name.'">
					<label for="json">规则内容:</label>
	    			<textarea class="form-control" rows="20" id="json" name="json">'.$item->json.'</textarea></div>';
	        	}
	        	else
	        	{
	        	$item=array_shift($finds);
	        	$historys=$finds;
	        	$info='<div class="leftspace"><label for="name">规则名:</label>
					   <input type="text" class="form-control" id="name" name="name" value="'.$item->name.'">
					   <label for="json">规则内容:</label>
	    			<textarea class="form-control" rows="20" id="json" name="json">'.$item->json.'</textarea></div>';

	    			$info.="<div class='rightspace'>";

	    			$info.="<ul>";
	    			foreach ($historys as $history) {
	    				
	    				$info.="<li><a title='".$history->json."' class='history_content'>".$history->time."</a></li>";
	    			}
	    			
    				$info.="</ul>";
    				$info.="</div>";

	        	}
	        	

	        	if($item!="")
	        	{
					$buttons.='<div class="col-md-2 ">
					<button type="button" onclick="editguize('.$item->id.')" class="btn btn-default">修改</button>
				</div>
				<div class="col-md-2 ">
					<button type="button" onclick=" deleteguize('.$item->id.',\''.$item->name.'\')" class="btn btn-default">删除</button>
				</div>';
	        		
	        	}
	        }
	        return view('user.guize')->with('search',$search)->with('info',$info)->with('buttons',$buttons);  
        }
    	
    }
    public function newguize()
    {
    	 if(Auth::guest())
        {
           $errors[0]='请先登录';
           return Redirect::to('/auth/namelogin');
        }
        else
        {
        	return view('user.newguize');
        }
    }
    
   public function create(Request $request)
   {
   		$name=$request->input('name');
      history::newhistory("新建规则：".$name,Auth::user()->id);
   		$json=$request->input('json');
   		$userid=Auth::user()->id;
   		return guize::newguize($name,$json,$userid);
   		
    
   }

   public function update(Request $request)
   {
   		$name=$request->input('name');
      history::newhistory("更新规则：".$name,Auth::user()->id);
   		$json=$request->input('json');
   		$userid=Auth::user()->id;
   		return guize::newguize($name,$json,$userid);
   		
   }

   public function delete($id,$name)
   {
      history::newhistory("删除规则：".$name,Auth::user()->id);
   		return guize::softdelete($id,$name); 
   		
   }
}
