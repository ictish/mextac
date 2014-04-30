// JavaScript Document
function appendAtEnd(data,numberOfPosts){
	var arrayDiv = new Array();      //to hold div element

	var length = <?php echo(LENGTH);?>;
	var increment = length * (parseInt(data[0][2]) - 2);
	for(var k = 0; k < numberOfPosts; k++){
		var dText="<p class='usernameClass'>" + data[k+1][0]+"<h7 class='dateClass'>"+data[k+1][2]+"</h7></p><p>"+data[k+1][1]+"</p>";
		var br = document.createElement('br');
		arrayDiv[increment] = document.createElement('div');
		arrayDiv[increment].id = 'statusDisplayID' + increment;
		arrayDiv[increment].className = 'statusDisplayClass';
		statusByAjaxID.appendChild(arrayDiv[increment]);
		statusByAjaxID.appendChild(br);
		document.getElementById("statusDisplayID" + increment).innerHTML = dText;
		increment++;
	}
	return true;
}

function getData(rawData,numberOfPosts){
	var arraySeparated = new Array();
	var data = new Array();
	
	var tableColumn = ["username","posts","time_of_post"];          //only in case if the table contains any other columns
	var numCol = tableColumn.length;
	
	for(var i = 0; i <= numberOfPosts + 1; i++){            
		arraySeparated[i] = rawData.split("Array")[i].split("=>");  //a 2-dimensional dirty but yet useful array
	}
		
	for(var i = 0;i <3; i++){       //loop=3 because index=1 of arraySeparated contains LOGGED_IN_USER,TOTAL_DISPLAYING,PAGE
		data[i] = arraySeparated[1][i + 1].substring(1,arraySeparated[1][i + 1].indexOf("\n"));
	}
	for(var j = 2; j <= numberOfPosts+1; j++){        //index 2 to numberOfPosts+1 contains all the posts fetched in one time
		for(var k = 1; k <= numCol; k++){
			data[i] = arraySeparated[j][k].substring(1,arraySeparated[j][k].indexOf("\n"));
			i++;
		}
	}
	data[i] = arraySeparated[j-1][k].substring(1,arraySeparated[j-1][k].indexOf("\n"));   //the last index contains message
	
	//objectification of the data
	var post = new Array();
	
	post[0] = new Array();
	for(var j=0; j < 3;j++){
		post[0][j] = data[j];
	}
	for(var i = 1;i <= numberOfPosts; i++){
		post[i] = new Array();
		for(var k=0; k < numCol; k++){
			post[i][k] = data[j];
			j++;	
		}
	}
	post[i] = new Array();
	post[i][0] = data[j];
	
	return post;
}

function ajaxPegination(str) {
  if (str=="") {
	document.getElementById("statusDisplayID").innerHTML="";
    return;
  } 
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
		var rawData = xmlhttp.responseText;
		
		var numberOfPosts = rawData.split("Array").length-2;    //number of posts is 2 lesser
		
		var data = new Array();              					//converting string type responseText into array
		var data = getData(rawData,numberOfPosts);
		
		var page = parseInt(data[0][2]) + 1;                    //to fetch next particular size of data from database 
		var message = data[numberOfPosts + 1][0];               //this checks if last entry has reached by MAX_VIEW_REACHED
		
		document.getElementById("see_moreID").href = "javascript:ajaxPegination(" + page + ")"; //pegination link
		
		document.getElementById("msgID").innerHTML = message;   //message     
		
		if(message == "MAX_VIEW_REACHED"){                      //if max view reached pegination link changes 
			document.getElementById("see_moreID").href = "<?php echo(base_url() . "room/");?>";
			document.getElementById("see_moreID").innerHTML = "recent only...";
		}
		
		appendAtEnd(data,numberOfPosts);						//to append the posts at the end
		//console.log(data);
    }
  }
  xmlhttp.open("GET","<?php echo(base_url() . "room/ajax/");?>"+str,true);
  xmlhttp.send();
}