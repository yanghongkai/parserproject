@extends('app')

@section('content')
		<div >
			<form class="form-horizontal" role="form" method="POST" action="{{ url('/fenxi') }}">
				<div class="col-md-2 col-md-offset-1" style="text-align:right;font-size:15px;margin-top:5px;">
					
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<label for="search">输入:</label>
				</div>
				<div class="col-md-6">
				 <input type="text" class="form-control" id="search" name="search" value="{{ $search }}">
		        <div id='drop'role="presentation" class="dropdown">
				        <a id="drop6" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
				 			<span class="caret"></span>
				        </a>
				        <ul id="menu3" class="dropdown-menu" aria-labelledby="drop6">
				          {!! $history !!}
				        </ul>
				</div>
				</div>
				<div class="col-md-1">
	  				<button type="submit" class="btn btn-default">分析</button>
				</div>
				<div class="col-md-8 col-md-offset-3">
				
				

				<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
					 	<div class="modal-header">
				          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				          <h4 class="modal-title" id="mySmallModalLabel">Small modal</h4>
				        </div> 
				       
					</div>
				</div>
				</div>

				<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
				  <div class="modal-dialog modal-lg">
				    <div class="modal-content">

			        <div class="modal-header">
			          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			          <h4 class="modal-title" id="myLargeModalLabel">Large modal</h4>
			        </div> 
			        <div class="modal-body">
				        <div class="container-fluid">
				        	<div></div>
				         	<textarea id="pattern" autofocus style="display: none;">
							,@#(新华社
							</textarea>
							<input type=hidden value="[生病[小孩 /D:S[赵老师 /D:Mod]][很严重 /D:Buyu]]" id="textInput" autofocus />
							<input type="range" id="range" min="1" max="20" step="1" name="range" value="10" onchange="onRangeChange(event)"/> 
							<br/>
							<br/>

							<div id="treeContainer" class="canvasContainer" style="background-color:white">
							    <canvas id="tree" width="800" height="600" style="background-color: transparent;">
							        Your browser dose not support the  canvas tag.   
							    </canvas>
							</div>
							 
								
				        <script src="{{ asset('/js/tree.js')}}"></script>
				        </div>
			      </div>
			      </div>
				  </div>
				</div>
				
				</div>
				<div>
				<div class="col-md-8 col-md-offset-3">
				<div class='col-md-4' style='margin-top:20px;'>分析路径：<input type='text' name='input_debug' value='{!! $input_debug !!}'/></div>
				<div class='col-md-8'>{!! $selectDebugInfo !!}</div>
				
				</div>
				<div>
			</form>
	  	</div>
	  	<div  id='tablespace'>
	  	
			{!! $info !!}
	

	  	</div>
@endsection	 