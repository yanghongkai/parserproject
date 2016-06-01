<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\cidian;
use App\history;
use Redirect, Input, Auth;
class cidianController extends Controller
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
					<button type="button" onclick=\' location.href="./newcidian" \' class="btn btn-default">增加</button>
				</div>';
	        if($request->input('search'))
	        {
	        	$search=$request->input('search');
            history::newhistory("搜索词条：".$search,Auth::user()->id);
	        	$finds=cidian::get($search);
	        	if(count($finds)==0)
	        	{
	        		$info="没有相关的记录";
	        	}
	        	else if(count($finds)==1)
	        	{
	        		$item=$finds[0];
		        	$info='<div class="leftspace"><label for="name">词条:</label>
					<input type="text" class="form-control" id="name" name="name" value="'.$item->name.'">
					<label for="json">词条内容:</label>
	    			<textarea class="form-control" rows="15" id="json" name="json">'.$item->json.'</textarea></div>';
	        	}
	        	else
	        	{
	        		$item=array_shift($finds);
	        		$historys=$finds;
	        		$info='<div class="leftspace"><label for="name">规则名:</label>
					<input type="text" class="form-control" id="name" name="name" value="'.$item->name.'">
					<label for="json">词条内容:</label>
	    			<textarea class="form-control" rows="15" id="json" name="json">'.$item->json.'</textarea></div>';

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
					<button type="button" onclick="editcidian('.$item->id.')" class="btn btn-default">修改</button>
				</div>
				<div class="col-md-2 ">
					<button type="button" onclick=" deletecidian('.$item->id.',\''.$item->name.'\')" class="btn btn-default">删除</button>
				</div>';
	        		
	        	}
	        }
	        return view('user.cidian')->with('search',$search)->with('info',$info)->with('buttons',$buttons);  
        }
    	
    }
    public function newcidian()
    {
    	 if(Auth::guest())
        {
           $errors[0]='请先登录';
           return Redirect::to('/auth/namelogin');
        }
        else
        {
        	return view('user.newcidian');
        }
    }
    
   public function create(Request $request)
   {
   		$name=$request->input('name');
       history::newhistory("新建词条：".$name,Auth::user()->id);
   		$json=$request->input('json');
   		$userid=Auth::user()->id;
   		return cidian::newcidian($name,$json,$userid);
   		
    
   }

   public function update(Request $request)
   {
   		$name=$request->input('name');
      history::newhistory("更新词条：".$name,Auth::user()->id);
   		$json=$request->input('json');
   		$userid=Auth::user()->id;
   		return cidian::newcidian($name,$json,$userid);
   		
   }

   public function delete($id,$name)
   {
      history::newhistory("删除词条：".$name,Auth::user()->id);
   		return cidian::softdelete($id,$name);
   		
   }
}
