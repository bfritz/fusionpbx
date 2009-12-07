<?php
//http://tinymce.moxiecode.com/download.php

//original
//plugins : devkit,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template
//theme_advanced_buttons3_add : "emotions,iespell,media,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",

//modified
//plugins : style,layer,table,save,advhr,advimage,advlink,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,spellchecker
//theme_advanced_buttons3_add : "spellchecker,media,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",

//<script language="javascript" type="text/javascript" src="/includes/tiny_mce/tiny_mce.js"></script>

?>

<script type="text/javascript" src="<?php echo PROJECT_PATH; ?>/includes/tiny_mce/tiny_mce_gzip.js"></script>
<script type="text/javascript">
tinyMCE_GZ.init({
	plugins : 'style,layer,table,save,advhr,advimage,advlink,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,spellchecker',
	themes : 'simple,advanced',
	languages : 'en',
	disk_cache : true,
	debug : false
});
</script>
<!-- Needs to be seperate script tags! -->
<script language="javascript" type="text/javascript">
	tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		plugins : "style,layer,table,save,advhr,advimage,advlink,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,spellchecker",
		theme_advanced_buttons1_add_before : "save,newdocument,separator",
		theme_advanced_buttons1_add : "fontselect,fontsizeselect",
		theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,separator,forecolor,backcolor",
		theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
		theme_advanced_buttons3_add_before : "tablecontrols,separator",
		theme_advanced_buttons3_add : "spellchecker,media,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,|,code",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_path_location : "bottom",
		content_css : "example_full.css",
	    plugin_insertdate_dateFormat : "%Y-%m-%d",
	    plugin_insertdate_timeFormat : "%H:%M:%S",
		extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
		external_link_list_url : "example_link_list.js",
		external_image_list_url : "example_image_list.js",
		flash_external_list_url : "example_flash_list.js",
		media_external_list_url : "example_media_list.js",
		template_external_list_url : "example_template_list.js",
		file_browser_callback : "ajaxfilemanager",
		theme_advanced_resize_horizontal : false,
		theme_advanced_resizing : true,
		nonbreaking_force_tab : true,
		apply_source_formatting : true,
		template_replace_values : {
			username : "Jack Black",
			staffid : "991234"
		}
	});


	function ajaxfilemanager(field_name, url, type, win) {
		var ajaxfilemanagerurl = "<?php echo PROJECT_PATH; ?>/includes/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php";
		switch (type) {
			case "image":
				ajaxfilemanagerurl += "?type=img";
				break;
			case "media":
				ajaxfilemanagerurl += "?type=media";
				break;
			case "flash": //for older versions of tinymce
				ajaxfilemanagerurl += "?type=media";
				break;
			case "file":
				ajaxfilemanagerurl += "?type=files";
				break;
			default:
				return false;
		}
		var fileBrowserWindow = new Array();
		fileBrowserWindow["file"] = ajaxfilemanagerurl;
		fileBrowserWindow["title"] = "Ajax File Manager";
		fileBrowserWindow["width"] = "782";
		fileBrowserWindow["height"] = "440";
		fileBrowserWindow["close_previous"] = "no";
		tinyMCE.openWindow(fileBrowserWindow, {
		  window : win,
		  input : field_name,
		  resizable : "yes",
		  inline : "yes",
		  editor_id : tinyMCE.getWindowArg("editor_id")
		});

		return false;
	}

	//function fileBrowserCallBack(field_name, url, type, win) {
		// This is where you insert your custom filebrowser logic
	//	alert("Example of filebrowser callback: field_name: " + field_name + ", url: " + url + ", type: " + type);

		// Insert new URL, this would normaly be done in a popup
	//	win.document.forms[0].elements[field_name].value = "someurl.htm";
	//}

  	function fileBrowserCallBack(field_name, url, type, win) {
  		var connector = "../../filemanager/browser.html?Connector=connectors/php/connector.php";
  		var enableAutoTypeSelection = true;

  		var cType;
  		tinymcpuk_field = field_name;
  		tinymcpuk = win;

  		switch (type) {
  			case "image":
  				cType = "Image";
  				break;
  			case "flash":
  				cType = "Flash";
  				break;
  			case "file":
  				cType = "File";
  				break;
  		}

  		if (enableAutoTypeSelection && cType) {
  			connector += "&Type=" + cType;
  		}

  		window.open(connector, "tinymcpuk", "modal,width=600,height=400");
  	}


    var tinyMCEmode = true;
    function toogleEditorMode(sEditorID) {
        try {
            if(tinyMCEmode) {
                tinyMCE.removeMCEControl(tinyMCE.getEditorId(sEditorID));
                tinyMCEmode = false;
            } else {
                tinyMCE.addMCEControl(document.getElementById(sEditorID), sEditorID);
                tinyMCEmode = true;
            }
        } catch(e) {
            //error handling
        }
    }
    
    function ajaxLoad(id, txt) {
    	var inst = tinyMCE.getInstanceById(id);

    	// Do you ajax call here
    	inst.setHTML(txt);
    }
    
    function ajaxSave() {
    	var inst = tinyMCE.getInstanceById('content');
    
    	// Do you ajax call here
    	alert(inst.getHTML());
    }

</script>
<!-- /TinyMCE -->
