<!DOCTYPE html>
<html lang="zh-CN">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>JParser</title>

<script src="{{ asset('/js/jquery-2.1.4.min.js')}}"></script>
<script src="{{ asset('/js/bootstrap.min.js')}}"></script>
<script src="{{ asset('/js/op.js')}}" ></script>
<link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('/css/all.css') }}" rel="stylesheet">
</head>
<body >
<div class="container-fluid">
<div class="row head">
 <div class="col-md-2 col-md-offset-2">
 <h2>
  <a href="{{ url('/fenxi') }}">JParser</a>
 
 </h2>
  </div>
 <div class="col-md-4">
 <ul class="list-inline ulspace">
   <li><a target="_blank" href="{{ url('/guize') }}">规则</a></li>
   <li><a target="_blank" href="{{ url('/cidian') }}">词典</a></li>
   <li><a target="_blank" href="{{ url('/tree') }}">树编辑</a></li>
   <li><a target="_blank" href="{{ url('http://202.112.195.192:23339/bcc') }}">BCC</a></li>

  @if ((!Auth::guest())&&(Auth::user()->auth==3))
 
 @endif

</ul>
 </div>
 <div class="col-md-3 ">
 	<div class="userspace">
	 @if (Auth::guest())
		<a href="{{ url('/auth/namelogin') }}">登录</a>&nbsp;|&nbsp;<a href="{{ url('/auth/register') }}" \">注册</a> &nbsp;&nbsp;&nbsp; &nbsp;<a target="_blank" href="{{ url('/help') }}" \">Help</a>
	@else
		您好！&nbsp;&nbsp;<b> {{ Auth::user()->name }} </b>&nbsp;&nbsp;<a href="{{ url('/usermanagement') }}">用户管理</a>&nbsp;&nbsp;<a href="{{ url('/auth/logout') }}" onclick="return confirm('确认要退出?');">退出</a> &nbsp;&nbsp;&nbsp; &nbsp;<a target="_blank" href="{{ url('/help') }}" \">Help</a>
	@endif
	</div>
 </div>

</div>

<div class="row maincontent">
  @yield('content')
</div>


</div>
</body>
</html>
