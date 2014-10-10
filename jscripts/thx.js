/**
 *
 * Thanks Plugin
 * Developed by SaeedGH (SaeedGhMail@Gmail.com)
 * Last edit: September 2nd, 2014 
 *
 */

var pid=-1;
function thx_common(action, xml)
{
	try
	{
		can_remove = $(xml).find('del').text() == "1";
		pid = $(xml).find('pid').text();
		if (action == "add") {
			thxbtn = $('.thx_btn_add[data-thx="'+pid+'"]');
		} else {
			thxbtn = $('.thx_btn_remove[data-thx="'+pid+'"]');
		}
		thxbtn.removeClass().addClass($(xml).find('btnclass').text()).html('<span>'+$(xml).find('btntext').text()+'</span>');
		
		if ($(xml).find('display').text() != 0){
			$('#thx' + pid).show();
		} else {
			$('#thx' + pid).hide();
		}
		
		if ($(xml).find('display').text() == '1')
		{
			$('#thx_list' + pid).removeClass("hide").show();
			$('#thx_entry' + pid).html($(xml).find('list').text());
		} else {
			$('#thx_list' + pid).hide();
		}
			
		if (!can_remove) {
			thxbtn.hide();
		}
	}
	catch(err)
	{
		alert("an Error had occured please contact administrator")
		alert(err);
	}
	finally
	{
		return thxbtn;
	}
	
}
function thx_action(response)
{
	lin = thx_common("add", response)
	if(lin!=null)
	{
		thxbtn.unbind("click").click(function(){
			thxbtn.html('<span>' + lang.processing + '</span>');
			return rthx(pid);
		});
		thxbtn.attr({href: 'showthread.php?action=remove_thank&pid='+pid });
	}
}

function rthx_action(response)
{
	lin = thx_common("remove", response)
	if (lin!=null) 
	{
		thxbtn.unbind("click").click(function(){
			thxbtn.html('<span>' + lang.processing + '</span>');
			return thx(pid);
		});
		thxbtn.attr({href: 'showthread.php?action=thank&pid='+pid });
	}
}

function thx(id)
{
	pid=id;
	pb="pid="+pid;
	
	$.ajax({
		url: "xmlhttp.php?action=thankyou",
		type: 'POST',
		data: pb,
		dataType: 'XML',
		success: thx_action
	});
	return false;
}

function rthx(id)
{
	pid=id;
	b="pid="+pid;
	
	$.ajax({
		url: "xmlhttp.php?action=remove_thankyou",
		type: 'POST',
		data: b,
		dataType: 'XML',
		success: rthx_action
	});
	return false;
}

$(document).ready(function() {
	if(typeof lang.processing == 'undefined')
	{
		lang.processing = 'Processing...';
	}

	$('.thx_btn_add').each(function() {
		var el = $(this);
		el.click(function(e){
			e.preventDefault();
			el.html('<span>' + lang.processing + '</span>');
			return thx(el.data('thx'));
		});
	});
	
	$('.thx_btn_remove').each(function() {
		var el = $(this);
		el.click(function(e){
			e.preventDefault();
			el.html('<span>' + lang.processing + '</span>');
			return rthx(el.data('thx'));
		});
	});
});