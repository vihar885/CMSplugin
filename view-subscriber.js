function _elp_redirect() {
	window.location = "admin.php?page=elp-view-subscribers";
}

function _elp_help() {
	window.open("http://www.gopiplus.com/work/2014/03/28/wordpress-plugin-email-subscriber-widget/");
}

function _elp_addemail() {
	if( document.form_addemail.elp_email_group_txt.value != "" )
	{
		document.form_addemail.elp_email_group_txt.value = document.form_addemail.elp_email_group_txt.value.replace(/[^a-zA-Z0-9]/g, '');
	}
	
	if(document.form_addemail.elp_email_mail.value=="")
	{
		alert(elp_subscribers_script.elp_subscribers_add_email)
		document.form_addemail.elp_email_mail.focus();
		return false;
	}
	else if(document.form_addemail.elp_email_status.value=="" || document.form_addemail.elp_email_status.value=="Select")
	{
		alert(elp_subscribers_script.elp_subscribers_add_status)
		document.form_addemail.elp_email_status.focus();
		return false;
	}
	else if(document.form_addemail.elp_email_group.value=="" && document.form_addemail.elp_email_group_txt.value=="")
	{
		alert(elp_subscribers_script.elp_subscribers_email_group)
		document.form_addemail.elp_email_group_txt.focus();
		return false;
	}
}

function _elp_delete(id, query) {
	if(confirm(elp_subscribers_script.elp_subscribers_del_record1)) {
		document.getElementById("searchquery").value = document.frm_elp_display.searchquery.value;;
		document.getElementById("searchquery_cnt").value = document.frm_elp_display.searchquery_cnt.value;
		document.getElementById("searchquery_group").value = document.frm_elp_display.searchquery_group.value;
		document.getElementById("searchquery_sts").value = document.frm_elp_display.searchquery_sts.value;
		document.getElementById("wp_create_nonce").value = document.frm_elp_display.wp_create_nonce.value;
		document.frm_elp_display.action="admin.php?page=elp-view-subscribers&ac=del&did="+id;
		document.frm_elp_display.submit();
	}
}

function _elp_resend(id,query) {
	document.getElementById("searchquery").value = document.frm_elp_display.searchquery.value;;
	document.getElementById("searchquery_cnt").value = document.frm_elp_display.searchquery_cnt.value;
	document.getElementById("searchquery_group").value = document.frm_elp_display.searchquery_group.value;
	document.getElementById("searchquery_sts").value = document.frm_elp_display.searchquery_sts.value;
	document.getElementById("wp_create_nonce").value = document.frm_elp_display.wp_create_nonce.value;
	document.frm_elp_display.action="admin.php?page=elp-view-subscribers&ac=resend&did="+id;
	document.frm_elp_display.submit();
}

function _elp_search_sub_action(alphabets) {
	document.getElementById("frm_elp_bulkaction").value = 'search_sts';
	document.getElementById("searchquery").value = alphabets;
	document.getElementById("searchquery_cnt").value = document.frm_elp_display.searchquery_cnt.value;
	document.getElementById("searchquery_group").value = document.frm_elp_display.searchquery_group.value;
	document.getElementById("searchquery_sts").value = document.frm_elp_display.searchquery_sts.value;
	document.getElementById("wp_create_nonce").value = document.frm_elp_display.wp_create_nonce.value;
	document.frm_elp_display.action="admin.php?page=elp-view-subscribers";
	document.frm_elp_display.submit();
}

function _elp_search_group_action(group) {
	document.getElementById("frm_elp_bulkaction").value = 'search_group';
	document.getElementById("searchquery").value = document.frm_elp_display.searchquery.value;
	document.getElementById("searchquery_cnt").value = document.frm_elp_display.searchquery_cnt.value;
	document.getElementById("searchquery_group").value = group;
	document.getElementById("searchquery_sts").value = document.frm_elp_display.searchquery_sts.value;
	document.getElementById("wp_create_nonce").value = document.frm_elp_display.wp_create_nonce.value;
	document.frm_elp_display.action="admin.php?page=elp-view-subscribers";
	document.frm_elp_display.submit();
}

function _elp_search_sts_action(status) {
	document.getElementById("frm_elp_bulkaction").value = 'search_sts';
	document.getElementById("searchquery").value = document.frm_elp_display.searchquery.value;
	document.getElementById("searchquery_cnt").value = document.frm_elp_display.searchquery_cnt.value;
	document.getElementById("searchquery_group").value = document.frm_elp_display.searchquery_group.value;
	document.getElementById("searchquery_sts").value = status;
	document.getElementById("wp_create_nonce").value = document.frm_elp_display.wp_create_nonce.value;
	document.frm_elp_display.action="admin.php?page=elp-view-subscribers";
	document.frm_elp_display.submit();
}

function _elp_search_count_action(cnt) {
	document.getElementById("frm_elp_bulkaction").value = 'search_cnt';
	document.getElementById("searchquery").value = document.frm_elp_display.searchquery.value;
	document.getElementById("searchquery_cnt").value = cnt;
	document.getElementById("searchquery_group").value = document.frm_elp_display.searchquery_group.value;
	document.getElementById("searchquery_sts").value = document.frm_elp_display.searchquery_sts.value;
	document.getElementById("wp_create_nonce").value = document.frm_elp_display.wp_create_nonce.value;
	document.frm_elp_display.action="admin.php?page=elp-view-subscribers";
	document.frm_elp_display.submit();
}

function _elp_bulkaction() {
	if(document.frm_elp_display.bulk_action.value=="") {
		alert(elp_subscribers_script.elp_subscribers_bulk_action); 
		document.frm_elp_display.bulk_action.focus();
		return false;
	}
	
	if(document.frm_elp_display.bulk_action.value == "delete") {
		if(confirm(elp_subscribers_script.elp_subscribers_del_record1)) {
			if(confirm(elp_subscribers_script.elp_subscribers_del_record2)) {
				document.getElementById("frm_elp_bulkaction").value = 'delete';
				document.getElementById("searchquery").value = document.frm_elp_display.searchquery.value;
				document.getElementById("searchquery_cnt").value = document.frm_elp_display.searchquery_cnt.value;
				document.getElementById("searchquery_group").value = document.frm_elp_display.searchquery_group.value;
				document.getElementById("searchquery_sts").value = document.frm_elp_display.searchquery_sts.value;
				document.getElementById("wp_create_nonce").value = document.frm_elp_display.wp_create_nonce.value;
				document.frm_elp_display.action="admin.php?page=elp-view-subscribers";
				document.frm_elp_display.submit();
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	else if(document.frm_elp_display.bulk_action.value == "resend") {
		if(confirm(elp_subscribers_script.elp_subscribers_resend)) {
			document.getElementById("frm_elp_bulkaction").value = 'resend';
			document.getElementById("searchquery").value = document.frm_elp_display.searchquery.value;
			document.getElementById("searchquery_cnt").value = document.frm_elp_display.searchquery_cnt.value;
			document.getElementById("searchquery_group").value = document.frm_elp_display.searchquery_group.value;
			document.getElementById("searchquery_sts").value = document.frm_elp_display.searchquery_sts.value;
			document.getElementById("wp_create_nonce").value = document.frm_elp_display.wp_create_nonce.value;
			document.frm_elp_display.action="admin.php?page=elp-view-subscribers";
			document.frm_elp_display.submit();
		}
		else {
			return false;
		}
	}
	else if(document.frm_elp_display.bulk_action.value == "groupupdate") {
		if(document.frm_elp_display.elp_email_group.value=="") {
			alert(elp_subscribers_script.elp_subscribers_add_group);
			document.frm_elp_display.elp_email_group.focus();
			return false;
		}

		if(confirm(elp_subscribers_script.elp_subscribers_update_group)) {
			document.getElementById("frm_elp_bulkaction").value = 'groupupdate';
			document.getElementById("searchquery").value = document.frm_elp_display.searchquery.value;
			document.getElementById("searchquery_cnt").value = document.frm_elp_display.searchquery_cnt.value;
			document.getElementById("searchquery_group").value = document.frm_elp_display.searchquery_group.value;
			document.getElementById("searchquery_sts").value = document.frm_elp_display.searchquery_sts.value;
			document.getElementById("wp_create_nonce").value = document.frm_elp_display.wp_create_nonce.value;
			document.frm_elp_display.action="admin.php?page=elp-view-subscribers";
			document.frm_es_display.submit();
		} 
		else {
			return false;
		}
	}
}

function _elp_action_visible(val) {
	if(val == "groupupdate") {
		document.getElementById('elp_email_group').disabled = false;
	} 
	else {
		document.getElementById('elp_email_group').disabled = true;
	}
}

function _elp_exportcsv(url, option) {
	if(confirm(elp_subscribers_script.elp_subscribers_export_email)) {
		document.frm_elp_subscriberexport.action= url+"&option="+option;
		document.frm_elp_subscriberexport.submit();
	}
	else {
		return false;
	}
}

function _elp_importemail() {
	if( document.form_addemail.elp_email_group_txt.value != "" ) {
		document.form_addemail.elp_email_group_txt.value = document.form_addemail.elp_email_group_txt.value.replace(/[^a-zA-Z0-9]/g, '');
	}
	
	var filename = document.getElementById('elp_csv_name').value;
	var extension = filename.substr(filename.lastIndexOf('.')+1).toLowerCase();
	if(extension == 'csv') {
        return true;
    } 
	else {
        alert(elp_subscribers_script.elp_subscribers_csv);
        return false;
    }
}

function _elp_numericandtext(inputtxt) {  
	var numbers = /^[0-9a-zA-Z]+$/;  
	if(inputtxt.value.match(numbers)) {  
		return true;  
	}  
	else {  
		alert(elp_subscribers_script.elp_subscribers_num_letters); 
		newinputtxt = inputtxt.value.substring(0, inputtxt.value.length - 1);
		document.getElementById('elp_email_group_txt').value = newinputtxt;
		return false;  
	}  
}

function _elp_checkall(FormName, FieldName, CheckValue) {
	if(!document.forms[FormName])
		return;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
		return;
	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes)
		objCheckBoxes.checked = CheckValue;
	else
		// set the check value for all check boxes
		for(var i = 0; i < countCheckBoxes; i++)
			objCheckBoxes[i].checked = CheckValue;
}