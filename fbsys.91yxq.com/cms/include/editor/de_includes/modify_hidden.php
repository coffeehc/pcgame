<script language=JavaScript>
var myPage = window.opener;
window.onload = setValues;

var hiddenName = myPage.selectedHidden.name;
var hiddenValue = myPage.selectedHidden.value;

function setValues() {

	hiddenForm.hidden_value.value = hiddenValue;
	hiddenForm.hidden_name.value = hiddenName;
	this.focus();
}

function doModify() {
	var sel = window.opener.document.selection;
		if (sel!=null) {
			var rng = sel.createRange();
		}

		name = document.hiddenForm.hidden_name.value
		value = document.hiddenForm.hidden_value.value

		if (value != "") {
			value = ' value="' + value + '"'
		} else {
			value = ""
		}

		if (name != "") {
			name = ' name="' + name + '"'
		} else {
			name = ""
		}

		HTMLTextField = '<input id=ewp_element_to_style type=hidden' + name + value + '>'
		myPage.selectedHidden.outerHTML = HTMLTextField

		oHidden = window.opener.foo.document.getElementById("ewp_element_to_style")
					
			if (window.opener.borderShown == "yes") {
					oHidden.runtimeStyle.border = "0px"
					oHidden.runtimeStyle.width = "20px"
					oHidden.runtimeStyle.height = "20px"
					oHidden.runtimeStyle.backgroundImage = "url(de/de_images/hidden.gif)"
					oHidden.runtimeStyle.fontSize = "99px"
			}

		oHidden.removeAttribute("id")
    
    window.close()
}

document.onkeydown = function () { 
	if (event.keyCode == 13) {	// ENTER
		doModify()
	}
	if(event.ctrlKey) {
		if(event.keyCode == 09 || event.keyCode == 90) {	//Ctrl+Tab
		window.close()
		}
	}
	if(event.ctrlKey) {
		if(event.keyCode == 83) {	//Ctrl+S
		doModify()
		}
	}
};

document.onkeypress = onkeyup = function () {
	if (event.keyCode == 13) {	// ENTER
	event.cancelBubble = true;
	event.returnValue = false;
	return false;			
	}
};

</script>
<title>[sTxtModifyField]</title>
<link rel="stylesheet" href="de_includes/de_styles.css" type="text/css">
<body bgcolor=threedface style="border: 1px buttonhighlight;">
<form name=hiddenForm>

<div class="appOutside">
<div style="border: solid 1px #000000; background-color: #FFFFEE; padding:5px;">
	<img src="de_images/popups/bulb.gif" align=left width=16 height=17>
	<span>[sTxtModifyFieldInst]</span>
</div>
<br>

<table border="0" cellspacing="0" cellpadding="5" style="width:92%">
  <tr>
	<td class="body" width="80">[sTxtName]:</td>
	<td class="body" width="200">
	  <input type="text" name="hidden_name" size="10" class="Text150" maxlength="50">
  </td>
  </tr>
  <tr>
	<td class="body" width="80">[sTxtInitialValue]:</td>
	<td class="body">
	  <input type="text" name="hidden_value" size="10" class="Text150">
	</td>
  </tr>
</table>

</div>
<div style="padding-top: 6px; float: right;">
<input type="button" name="modifyHiddenField" value="[sTxtOK]" class="Text75" onClick="javascript:doModify();">
<input type="button" name="Submit" value="[sTxtCancel]" class="Text75" onClick="javascript:window.close()">
</div>

</form>