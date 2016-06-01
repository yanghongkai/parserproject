@extends('guize')

@section('content')
		<div class='row'>
			<form class="form-horizontal" role="form" method="POST" action="{{ url('/guize') }}">
				<div class="col-md-2 col-md-offset-1" style="text-align:right;font-size:15px;margin-top:5px;">
					
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<label for="search">请输入规则名:</label>
				</div>
				<div class="col-md-6">
				 <input type="text" class="form-control" id="search" name="search" value="{{$search}}">
				</div>
				<div class="col-md-1">
	  				<button type="submit" class="btn btn-default">查询</button>
				</div>
			</form>
	  	</div>
	  	<div  class='row'>
	  	<div class="col-md-7 col-md-offset-2" style="text-align:left;font-size:15px;margin-top:40px;">
			{!! $info !!}
			</div>
	  	</div>

	  	<div  class='row'>
	  	 @if ((!Auth::guest())&&(Auth::user()->auth>1))
 
	 		{!! $buttons !!}
 		@endif
	  	
	  	
	  	</div>
	  	<div class='row'>
	  		<div class="col-md-7 col-md-offset-2" >
				<div id='feedback'></div>
			</div>
		</div >
@endsection	 