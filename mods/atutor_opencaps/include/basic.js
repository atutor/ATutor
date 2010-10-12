<!--
function show_hide(a){
  var e=document.getElementById(a);
  //if(!e)return true;
  if(e.style.display=="none"){
    e.style.display="block"
  } else {
    e.style.display="none"
  }
  //return false;
}
	 
function changeDivContent(nameOfDiv,newContent)
{
	var div = document.getElementById(nameOfDiv);
	if(div)
	{
		div.innerHTML = newContent;
	}
}

function confirmDelete(action)
{
	if(confirm("Do you want to delete this project?\nNote: your media files will not be deleted."))
	{
		document.getElementById(action).value = "deleteProject";
		document.getElementById('atocForm').submit();
	} 
}

-->