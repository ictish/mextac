<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Room</title>

<link rel="stylesheet" type="text/css" href="<? echo(base_url());?>css/mystyle.css">

<script language="javascript">
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
	
	console.log(arraySeparated);
	
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

function getXMLObject(){ 
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  return xmlhttp;
}

function ajaxPegination(str) {
  if (str=="") {
	document.getElementById("statusDisplayID").innerHTML="";
    return;
  }
  
  xmlhttp = getXMLObject();							//this is defined above for creating an xmlObject
  
  xmlhttp.open("GET","<?php echo(base_url() . "room/ajax/");?>"+str,true);
  xmlhttp.send();
  
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
		var rawData = xmlhttp.responseText;						//getting the responseText from the server
		
		console.log(rawData);
		
		var numberOfPosts = rawData.split("Array").length-2;    //number of posts is 2 lesser
		var data = new Array();              					//converting string type responseText into array
		var data = getData(rawData,numberOfPosts);				//this function is defined above
		var page = parseInt(data[0][2]) + 1;                    //to fetch next particular size of data from database 
		var message = data[numberOfPosts + 1][0];               //this checks if last entry has reached by MAX_VIEW_REACHED
		
		document.getElementById("see_moreID").href = "javascript:ajaxPegination(" + page + ")"; //pegination link
		document.getElementById("msgID").innerHTML = message;   //message     
		if(message == "MAX_VIEW_REACHED"){                      //if max view reached pegination link changes 
			document.getElementById("see_moreID").href = "<?php echo(base_url() . "room/");?>";
			document.getElementById("see_moreID").innerHTML = "recent only...";
		}
		appendAtEnd(data,numberOfPosts);						//this is defined above to append the posts at the end
    }
  }
}

function makeItBlank(){
	document.getElementById('textAreaID').value = "";
}

function showPosts(username,date,status){
	var dText="<p class='usernameClass'>" +username+"<h7 class='dateClass'>"+date+"</h7></p><p>"+status+"</p>";
	var idNum = document.getElementById('hiddenID').value;
	
	statusByInputID.insertAdjacentHTML("afterend","<br/><div id="+idNum+" class='statusDisplayClass'></div>");
	
	document.getElementById(idNum).innerHTML = dText;
	document.getElementById('postedByID').value = username;
	document.getElementById('dateID').value = date;
}

function postStatusByAjax(){
	var status = document.getElementById('textAreaID').value;
	var username = "<?php echo($di0);?>";
	var d = new Date();
	var date = d.getFullYear()+"-0"+d.getMonth()+"-"+d.getDate();
	
	xmlhttp = getXMLObject();
	
	xmlhttp.open("POST","<?php echo(base_url() . "room/ajaxPost");?>",true);
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlhttp.send("username="+username+"&status="+status+"&<?php echo urlencode($this->security->get_csrf_token_name()) ?>=<?php echo urlencode($this->security->get_csrf_hash()) ?>");
	
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			var rawData = xmlhttp.responseText;						//getting the responseText from the server	
			if(rawData){
				showPosts(username,rawData,status);                 //this function is defined above
			}
			else{
				alert("ERROR");
			}
		}
	}
}
</script>

</head>
<body>

<div id="roomMenuID">
<?php echo($di0);?>
<a href="<?php echo(base_url());?>room/logout">Logout</a>
</div>

<div id="msgID" style="display:none"></div>

<!----------------------------------------------------THE ROOM BODY--------------------------------------------------------->
<div id="roomBodyID">
<!-----------------------------------------------------STATUS INPUT--------------------------------------------------------->
<br/>
<div id="statusInputID">
<form name="statusInputForm" action="room/post">
<textarea name="text" id="textAreaID" onfocus="makeItBlank()"><?php echo(DEFAULT_STATUS);?></textarea>
<input type="hidden" name="IDnumberHolder" value="1" id="hiddenID"></input>
<input type="hidden" name="postedBy" value=" " id="postedByID"></input>
<input type="hidden" name="date" value="today" id="dateID"></input>
</form>
</div>
<br>
<div id="postButtonID"><a href="javascript:postStatusByAjax()">Post</a></div>
<!------------------------------------------------STATUS BY INPUT DISPLAY---------------------------------------------------->
<br/>
<div id="statusByInputID">	
									
</div>	
<!--------------------------------------------------DEFAULT STATUS DISPLAY---------------------------------------------------->
<?php 
for($i = 3; $i < $di1; $i++){
	echo("<br/><div id='statusDisplayID'>");
	echo("<p class='usernameClass'>".
			${'di'.$i}['username'].
			"<h7 class='dateClass'>".
			${'di'.$i}['time_of_post']
			
			."</h7></p>");
	echo("<p>".${'di'.$i}['posts']."</p>");
	echo("</div>");
}
?>
<!---------------------------------------------------AJAX STATUS DISPLAY------------------------------------------------------->
<br/>
<div id="statusByAjaxID">	
									
</div>	
<!------------------------------------------------------BOTTOM LINKS------------------------------------------------------------>    
<br/>
<div id="morePostID">
<?php echo("<a href='javascript:ajaxPegination(" . ($di2 + 1) . ")' id='see_moreID'>see more..</a>");?>
</div>
<br style="clear:both" />
</div>
<!----------------------------------------------------------FOOTER-------------------------------------------------------------->
<div id="footerID">

</div>
</body>
</html>