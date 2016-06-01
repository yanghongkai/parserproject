<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Redirect, Input, Auth;
use App\acessCApi;
use App\history;
class fenxiController extends Controller
{
   

    public function index(Request $request)
    {
        
        if(Auth::guest())
        {
           $errors[0]='请先登录';
           return Redirect::to('/auth/namelogin');
        }
        else
        {
        	
			$info="";
			$search="";
			$selectDebug="";
			$selectDebugInfo="";
			$acessCApi=new acessCApi();
			$selectDebug=$acessCApi->getFunc();
			$selects=explode("^",$selectDebug);
			$input_debug="";
        	if($request->input('search'))
        	{
				 $search=$request->input('search');
				 history::newhistory("例句查询：".$search,Auth::user()->id);
				 
				 
				$selectMode="";
				
				if($request->has('input_debug')&&($request->input('input_debug')!=''))
				{
					
					$input_debug=$request->input('input_debug');
				}
				

				$debug=$request->input('debug');
					$k=0;
					foreach ($selects as $select) 
					{
						if($select==$debug)
						{
							$selectMode.=$select;
							break;
						}
						else
						{
							$selectMode.=$select."^";
							$k++;
						}
					}
				$selectDebugInfo="<div class='selectDebug'><input type='hidden' id='debug' name='debug' value='".$debug."'>";
			
				
				$mount=count($selects);

				for($i=0;$i<$mount-1;$i++)
				{
					if($k<$i)
					{
						$selectDebugInfo.="<div class='selectmode' data='".$selects[$i]."'>".$selects[$i]."</div>";
					}else
					{
						$selectDebugInfo.="<div class='selectmode selectedmode' data='".$selects[$i]."'>".$selects[$i]."</div>";
					}
					
				}
				//$selectDebugInfo.="<div class='selectmode' data='".$selects[$mount-2]."'>".$selects[$mount-2]."</div>";
				$selectDebugInfo.="</div>";
				//调用老师的接口 返回的lattice
				
				$lattice='<Lattice Sent="我们大大家" ColNum="5">
<Column Name="我" NextNum="3" Boundary="Left" Value="0.000000" RuleInfo="tmp/cat2.old:我们+大家-&gt;Bounary(0-1;NULL)" BoundaryParam=";NULL" >
<Unit Name="" POS="" DynInfo="" ColNo="0" UnitNo="0" From="0" To="0" Tree="[生病[小孩/D:S[赵老师/D:Mod]][很严重/D:Buyu][医院/D:NN[效果好/D:good]]]" Type="_INIT" RuleName="" RuleInfo="" />
<Unit Name="我" POS="" DynInfo="" ColNo="0" UnitNo="1" From="0" To="0" Tree="[生病[小孩/D:S[赵老师/D:Mod]][很严重/D:Buyu][医院/D:NN[效果好/D:good]]]" Type="_ADD" RuleName="" RuleInfo="" />
</Column>
<Column Name="家" NextNum="0" Next="" Boundary="Right">
<Unit Name="家" POS="" DynInfo="" ColNo="4" UnitNo="0" From="4" To="4" Tree="[生病[小孩/D:S[赵老师/D:Mod]][很严重/D:Buyu][医院/D:NN[效果好/D:good]]]" Type="_INIT" RuleName="" RuleInfo="" />
<Unit Name="大家" POS="" DynInfo="" ColNo="4" UnitNo="1" From="3" To="4" Tree="[生病[小孩/D:S[赵老师/D:Mod]][很严重/D:Buyu][医院/D:NN[效果好/D:good]]]" Type="_DICT" RuleName="" RuleInfo="" />
<Unit Name="我们大大家" POS="NP" DynInfo=" D=A" ColNo="5" UnitNo="2" From="0" To="5" Tree="[生病[小孩/D:S[赵老师/D:Mod]][很严重/D:Buyu][医院/D:NN[效果好/D:good]]]" Type="_MERGE" RuleName="Cat1" RuleInfo="Cat1:我们+^大家->Reduce(;D=APOS=NP;W:1[W:0/D:Tag])" />
</Column>
<Column Name="们" NextNum="4" Next="(2 0) (3 0) (4 0) (2 1) ">
<Unit Name="们" POS="" DynInfo="" ColNo="1" UnitNo="0" From="1" To="1" Tree="[生病[小孩/D:S[赵老师/D:Mod]][很严重/D:Buyu][医院/D:NN[效果好/D:good]]]" Type="_INIT" RuleName="" RuleInfo="" />
<Unit Name="我们" POS="" DynInfo="" ColNo="1" UnitNo="1" From="0" To="1" Tree="" Type="_DICT" RuleName="" RuleInfo="" />
</Column>
<Column Name="大" NextNum="4" Next="(3 0) (4 0) (3 1) (4 1) ">
<Unit Name="大" POS="" DynInfo="" ColNo="2" UnitNo="0" From="2" To="2" Tree="" Type="_INIT" RuleName="" RuleInfo="" />
<Unit Name="大" POS="" DynInfo="" ColNo="2" UnitNo="1" From="2" To="2" Tree="" Type="_ADD" RuleName="" RuleInfo="" />
</Column>
<Column Name="大" NextNum="1" Next="(4 0) ">
<Unit Name="大" POS="" DynInfo="" ColNo="3" UnitNo="0" From="3" To="3" Tree="" Type="_INIT" RuleName="" RuleInfo="" />
<Unit Name="大" POS="" DynInfo="" ColNo="3" UnitNo="1" From="3" To="3" Tree="" Type="_ADD" RuleName="" RuleInfo="" />
</Column>
<Column Name="家" NextNum="0" Next="">
<Unit Name="家" POS="" DynInfo="" ColNo="4" UnitNo="0" From="4" To="4" Tree="" Type="_INIT" RuleName="" RuleInfo="" />
<Unit Name="大家" POS="" DynInfo="" ColNo="4" UnitNo="1" From="3" To="4" Tree="" Type="_DICT" RuleName="" RuleInfo="" />
<Unit Name="我们大大家" POS="NP" DynInfo=" D=A" ColNo="5" UnitNo="2" From="0" To="5" Tree="W:1[W:0/D:Tag]" Type="_MERGE" RuleName="Cat1" RuleInfo="Cat1:我们+^大家->Reduce(;D=APOS=NP;W:1[W:0/D:Tag])" />
</Column>
<Column Name="家" NextNum="0" Next="">
<Unit Name="家" POS="" DynInfo="" ColNo="4" UnitNo="0" From="4" To="4" Tree="" Type="_INIT" RuleName="" RuleInfo="" />
<Unit Name="大家" POS="" DynInfo="" ColNo="4" UnitNo="1" From="3" To="4" Tree="" Type="_DICT" RuleName="" RuleInfo="" />
<Unit Name="我们大大家" POS="NP" DynInfo=" D=A" ColNo="5" UnitNo="2" From="0" To="5" Tree="W:1[W:0/D:Tag]" Type="_MERGE" RuleName="Cat1" RuleInfo="Cat1:我们+^大家->Reduce(;D=APOS=NP;W:1[W:0/D:Tag])" />
</Column>
<Column Name="家" NextNum="0" Next="">
<Unit Name="家" POS="" DynInfo="" ColNo="4" UnitNo="0" From="4" To="4" Tree="" Type="_INIT" RuleName="" RuleInfo="" />
<Unit Name="大家" POS="" DynInfo="" ColNo="4" UnitNo="1" From="3" To="4" Tree="" Type="_DICT" RuleName="" RuleInfo="" />
<Unit Name="我们大大家" POS="NP" DynInfo=" D=A" ColNo="5" UnitNo="2" From="0" To="5" Tree="W:1[W:0/D:Tag]" Type="_MERGE" RuleName="Cat1" RuleInfo="Cat1:我们+^大家->Reduce(;D=APOS=NP;W:1[W:0/D:Tag])" />
</Column>
</Lattice>
';
				if($input_debug!="")
				{
					$lattice=$acessCApi->getLattice($search,$input_debug);
				}
				else
				{
					$lattice=$acessCApi->getLattice($search,$selectMode);
				}
				
				$lattice=str_replace('&', '&amp;', $lattice);
				$dom = new \ DOMDocument('1.0', 'UTF-8');
				$dom->recover = true;
				$dom->strictErrorChecking = false;
                $dom->loadXML($lattice);
                $info.="<div class='col-md-10 col-md-offset-1 Columntable'>";
                
                $Columns=$dom->getElementsByTagName('Column');
                $i=0;
                $info.="<div class='hang'>";
                foreach ($Columns as $Column ) {
                	
                	if((($i%5)==0)&&($i>0))
                	{
                		 $info.="</div><div class='hang'>";
                	}
                	$columnArray="";
					$attrs = $Column->attributes;
    
		            foreach( $attrs as $attr )
		            {
						$attr_key=$attr->name;
						if($attr->value!="")
		            		$columnArray.=$attr_key." ： ".$attr->value."</br>";
		            }
                	$info.="<div class='Column'>";
                	$info.="<div class='ColumnName ColumnName".$Column->getAttribute("Boundary")."'>第".$i."列：".$Column->getAttribute("Name")."<div class='showSpace'>".$columnArray."</div></div>";
                	$i++;
            	

                	$Units=$Column->getElementsByTagName('Unit');
                	$y=0;
					foreach ($Units as $Unit ) {
						$info.="<div class='Unitspace'><div class='unitnum'>".$y."</div><div class='Unit ".$Unit->getAttribute("Type")."'>";
						$infoArray="";
						$attrs = $Unit->attributes;
        
			            foreach( $attrs as $attr )
			            {
							$attr_key=$attr->name;
							if($attr->value!="")
			            		$infoArray.=$attr_key." ： ".$attr->value."</br>";
			            }
				        

						if($Unit->getAttribute("Tree")!=="")
						{
							$info.='<button type="button" class="btn  tree_btn"  data_array="'.$infoArray.'" data="'.$Unit->getAttribute("Tree").'" data-toggle="modal" data-target=".bs-example-modal-lg">';
						}
						else 
						{
							
							$info.='<button type="button" class="btn tree_btn1" data_array="'.$infoArray.'" data-toggle="modal" data-target=".bs-example-modal-sm">';
						}
						
						$y++;
						$info.=$Unit->getAttribute("Name")."</button><div class='showSpace'>".$this->zhuanyi($dom->saveXML($Unit))."</div></div></div>";
					}
                	$info.="</div>";

                }
                $info.="</div></div>";
        	}
        	if($selectDebugInfo=="")
        	{
        		$selectDebugInfo="<div class='selectDebug'><input type='hidden' id='debug' name='debug' value='语块'>";
			
				$mount=count($selects);

				for($i=0;$i<$mount-2;$i++)
				{
					if(1<$i)
					{
						$selectDebugInfo.="<div class='selectmode' data='".$selects[$i]."'>".$selects[$i]."</div>";
					}else
					{
						$selectDebugInfo.="<div class='selectmode selectedmode' data='".$selects[$i]."'>".$selects[$i]."</div>";
					}
				}
				$selectDebugInfo.="<div class='selectmode' data='".$selects[$mount-2]."'>".$selects[$mount-2]."</div>";
				$selectDebugInfo.="</div>";
        	}
        	$history='';
    		$historys=history::get_history('例句查询',5);

			foreach ($historys as $myhistory ) {
			    $history.='<li><a href="#">'.mb_substr($myhistory->text, 5).'</a></li>';
			}
        	 return view('user.fenxi')->with('search',$search)->with('info',$info)->with('input_debug',$input_debug)->with('selectDebugInfo',$selectDebugInfo)->with('history',$history);

        }
        
    }

    function zhuanyi($str)
    {
    	$str=str_replace('<', '&lt;', $str);
    	$str=str_replace('>', '&gt;', $str);
		$str=str_replace('"', '&quot;', $str);
		return $str;
	}
	
}
