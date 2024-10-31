// JavaScript Document
jQuery(document).ready(function(){
	jQuery('a.poplight').click(function() {
			var popID = jQuery(this).attr('href');
			
			var popWidth = jQuery(popID).width()  ;
			var popHeight = jQuery(popID).height() ;
			var winWidth = document.all ? document.body.clientWidth : window.innerWidth; 
			var winHeight = document.all ? document.body.clientHeight-40 : window.innerHeight-40;
			
			disWidth=((winWidth-popWidth)/2)-10;
			disHeight=((winHeight-popHeight)/2)+20;
								
			jQuery(popID).fadeIn().prepend('');			
			
			var num = Math.floor(Math.random()*9)+1;
			
			num=9;
			if(num==1){
				var left=0; var top=0;
			}else if(num==2){
				var left=winWidth/2; var top=0;
			}else if(num==3){
				var left=winWidth; var top=0;
			}else if(num==4){
				var left=winWidth; var top=winHeight/2;
			}else if(num==5){
				var left=winWidth; var top=winHeight;
			}else if(num==6){
				var left=winWidth/2; var top=winHeight;
			}else if(num==7){
				var left=0; var top=winHeight;	
			}else if(num==8){
				var left=0; var top=winHeight/2;
			}else if(num==9){
				var left=(winWidth/2);
				var top=(winHeight/2);
			}
					
			
			jQuery(popID).css({
				position: 'fixed',
				top : top+'px',
				left : left+'px',
				width: '0px',
				height: '0px',
				opacity: '0'
			});		
			
			jQuery(popID).animate({position: 'fixed',top : disHeight+'px',left : disWidth+'px',width: popWidth+'px',height: popHeight+'px',opacity: '1'},500);
			
			jQuery('body').append('<div id="invfade"></div>'); 
			jQuery('#invfade').css({'filter' : 'alpha(opacity=0.03)'}).fadeIn(); 
			return false;
	});
	//Close Popups and Fade Layer
	jQuery('.btn_close, #invfade').live('click', function() { //When clicking on the close or fade layer...
		jQuery('#invfade , .popup_block ').fadeOut(function() {
			jQuery('#invfade, a.close').remove();  //fade them both out
			jQuery('.popup_block').css({
				opacity: '0'
			});
		});
		return true;
	});
});


var result;
var xmlhttp;
var theurl;
var setdiv;
function ajax_state(url,setdiv1)
{
	theurl = url;
	setdiv=setdiv1;
	xmlhttp=null
	// code for IE7+, Firefox, Chrome, Opera, Safari
	if (window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest()
	}
	// code for IE6, IE5
	else if (window.ActiveXObject){
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")
	}	
	if (xmlhttp!=null){
		xmlhttp.onreadystatechange=state_Change
		xmlhttp.open("GET",url,true)
		xmlhttp.send(null)
	}else{
		alert("Your browser does not support XMLHTTP.")
	}
}
function state_Change(){
	// if xmlhttp shows "loaded"
	if (xmlhttp.readyState==4){
		// if "OK"
		if (xmlhttp.status==200){
			result = xmlhttp.responseText
			document.getElementById(setdiv).innerHTML = result;
		}else{
			alert("Problem retrieving XML data")
		}
	}
}




jQuery(document).ready(function($){
	jQuery(".csvdownload").click(function(){		
		var post_url = jQuery(this).attr('dataurl');		
		document.location.href = post_url+'&caseselect=csvdownload';			
	});	
});