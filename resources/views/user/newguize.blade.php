@extends('guize')

@section('content')
		<div >
			<form class="form-horizontal" role="form" onsubmit="return false;" method="POST" action="{{ url('/newguize') }}">
				
				<div class="col-md-8 col-md-offset-2">	
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<label for="name">规则名:</label>
				<input type="text" class="form-control" id="name" name="name" value="">
				<label for="json">内容</label>
    			<textarea class="form-control" rows="40" name="json" id='json'></textarea>
	  			<button  class="btn btn-default" onclick="newguize();">保存</button>
				</div>

				<div class='row'>
		  			<div class="col-md-7 col-md-offset-2" >
						<div id='feedback'></div>
					</div>
				</div >
			</form>
	  	</div>
	  	
@endsection