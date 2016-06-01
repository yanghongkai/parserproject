@extends('app')
<script src="/js/op.js"></script>
<style>
table{
width:800px;margin-left:0px;margin-top:20px ;	border-collapse:collapse;  
}
tr
{
	height:40px;
	text-align:left;
}
td{
padding: 10px  !important;	
border:1px solid;
}
span{
	color: red;
}
.title
{
width:800px;
margin:0 auto;
font-size:20px;
height:30px;
margin-top:10px;
}
a{
	text-decoration: none;
}
</style>
@section('content')
		<div id='middle'>

			<div class='title'>用户管理</div>
			<div style='width:800px;margin:0 auto;font-size:20px;margin-top:10px;'>
			<table >
				<tr >
					<td>id</td><td>名字</td><td>权限</td><td>操作</td>
				</tr>		
			@foreach ($users as $user)
				<tr >
				<td>{{$user->id}}</td><td>{{$user->name}}</td><td>{{$user->auth}}</td><td><a href="javascript:authedit({{$user->id}})">更改权限</a></td>
				</tr>
			@endforeach

			</table>
		<div>{!! $users->render(); !!}</div>
		
		</div>
	
	  	</div>
@endsection	 