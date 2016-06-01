$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});

function authedit(userid)  
{

	myurl="./authedit/"+userid;
	myurl=encodeURI(myurl);
	htmlobj=$.ajax({url:myurl,async:false});
	if(htmlobj.responseText==1)
	{
		location.reload();
	}
	else
	{
		alert('error');
	}
}

function deleteguize(id,name)  
{

	myurl="./deleteguize/"+id+"/"+name;
	myurl=encodeURI(myurl);
	htmlobj=$.ajax({url:myurl,async:false});
	if(htmlobj.responseText!=1)
	{
		alert(htmlobj.responseText);
		$('#feedback').html(htmlobj.responseText);
	}
	else
	{
		alert('error');
	}
}
function editguize(id)  
{
	

	myurl="./editguize";
	myurl=encodeURI(myurl);
	data={
		name:$('#name').val() ,
		json:$('#json').val()
	}
	$.post(myurl , data ,function(data1) {
			
		$('#feedback').html(data1);	
      
    });
	
}
function deletecidian(id,name)  
{

	myurl="./deletecidian/"+id+"/"+name;;
	myurl=encodeURI(myurl);
	htmlobj=$.ajax({url:myurl,async:false});
	$('#feedback').html(htmlobj.responseText);
		    /*if(htmlobj.responseText!=1)
			{
				alert(htmlobj.responseText);
				$('#feedback').html(htmlobj.responseText);
			}
			else
			{
				alert('error');
			}*/
}

function editcidian(id)  
{
	

	myurl="./editcidian";
	
	myurl=encodeURI(myurl);
	data={
		name:$('#name').val() ,
		json:$('#json').val()
	}
	$.post(myurl , data ,function(data1) {
			
		$('#feedback').html(data1);	
      
    });
	
}
function newcidian()  
{
	

	myurl="./newcidian";
	
	myurl=encodeURI(myurl);
	data={
		name:$('#name').val() ,
		json:$('#json').val()
	}
	$.post(myurl , data ,function(data1) {
			
		$('#feedback').html(data1);	
      
    });
	
}
function newguize()  
{
	

	myurl="./newguize";
	
	myurl=encodeURI(myurl);
	data={
		name:$('#name').val() ,
		json:$('#json').val()
	}
	
	$.post(myurl , data ,function(data1) {
			
		$('#feedback').html(data1);	
      
    });
}


$(document).ready(function () {

	$(".Unit").hover(

	function(){

	  $(this).find('.showSpace').show();

	},

	function(){

	   $(this).find('.showSpace').hide();

	});

	$(".ColumnName").hover(

	function(){

	  $(this).find('.showSpace').show();

	},

	function(){

	   $(this).find('.showSpace').hide();

	});

	$(".selectmode").click( function(){

		$("#debug").val($(this).attr('data'));
		 $(".selectmode").removeClass('selectedmode');
		var y=$(this).index();
		for(x=0;x<y;x++)
		{
			$(".selectmode").eq(x).addClass('selectedmode');
		}
		
	});
	$(".history_content").click( function(){

		$("#json").val($(this).attr('title'));
		 
		
	});

	$("#menu3>li>a").click( function(){

		 $('#search').val($(this).html());
		
	});
	$(".tree_btn").click( function(){

		$('#textInput').val($(this).attr('data'));
		$('#myLargeModalLabel').html($(this).attr('data_array'));
		
		pattern = document.getElementById("pattern").value.trim().split("@#");
		init();
		onClick();
		
	});
	$(".tree_btn1").click( function(){

	
	$('#mySmallModalLabel').html($(this).attr('data_array'));
	
	
		
	});
	function formatStr(str)
	{
		
		str=str.replace(/&lt;/ig,"<");
		str=str.replace(/&gt;/ig,">");
		str=str.replace(/&quot;/ig,"\"");
		return str;
	}
		
		
		

});
