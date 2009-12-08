//function below added by logan (cailongqun@yahoo.com.cn) from www.phpletter.com
function selectFile(msgNoFileSelected)
{
	var selectedFileRowNum = $('#selectedFileRowNum').val();
  if(selectedFileRowNum != '' && $('#row' + selectedFileRowNum))
  {

	  // insert information now
	  var url = $('#fileUrl'+selectedFileRowNum).val();  	
		window.opener.SetUrl( url ) ;
		window.close() ;
		
  }else
  {
  	alert(msgNoFileSelected);
  }
  

}



function cancelSelectFile()
{
  // close popup window
  window.close() ;
}