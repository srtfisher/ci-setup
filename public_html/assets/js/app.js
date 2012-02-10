/**
 * Core Application
 *
 * @author srtfisher
**/
var Simple = {};
var Billing = {};

// The current user ID
Simple.user_id			= 0;

// The form key (CSRF)
Simple.csrf_token_value	= '';

// The form key name (CSRF)
Simple.csrf_token_name 	= '';

// The Enviorment
Simple.env               = 'production';

$(document).ready(function () { Simple.init(); });

/*
 * The setup function we call to setup the app.
 *
 * @access public
**/
Simple.init = function() {
	//	The CSRF key and the UID are in the meta tags of the page.
	Simple.csrf_token_name = $("meta[name=csrf-name]").attr("content");
	Simple.csrf_token_value = $("meta[name=csrf-value]").attr("content");
	Simple.user_id = $("meta[name=current-uid]").attr("content");
	Simple.env = $("meta[name=simple-env]").attr("content");
	
	//	Notice bar close button
	$(".notice-bar a.close").click(function () {
		return Simple.hideNoticeBar();
	});
	
	// Setup tipsy
	Simple.tipsify();
	
	// Setup Spiffy Links!
	Simple.setupSpiffyLink();
	
	$(".modal a.close").bind('click', function() {
	    $(this).parents(".well").fadeOut();
     });
     
     $(".alert-message a.close").bind('click', function() {
	    $(this).parents(".alert-message").slideUp();
     });    
};

// Create tipsy to run
Simple.tipsify = function() {
	$("a.[title][rel!=\"popover\"], .short-url input, form abbr[title], .doTipsy").tooltip();
	
	$("[rel=popover]")
                .popover({offset: 10});
};

/**
 * A simple function to show an error (easter egg ;) )
 *
 * @access     public
 * @param      void
**/
Simple.ajaxError = function()
{
	$.facebox('<center><img src="/assets/images/icons/panda.png" height="100" width="100" /></center><p>Whoops! An error has occurred. <strong>Sorry!</strong></p>', 'error-box');
};


/**
 * Perform an AJAX request with jQuery but with the CSRF keys
 *
 * We pass the URL via the 'href' attribute on a link.
 * We execute the javascript that it returns.
**/
Simple.setupSpiffyLink = function() {
	// Unbind them
	$('a.spiffy').unbind('click');
	
	$('a.spiffy').click(function (theObj) {
		theObj.preventDefault(); // This disables the link
		Simple.SpiffyLink($(this));
	});
	
	
	// It works for forms with a "spiffy_form" class on it.
	$('form.spiffy_form input.submit').click(function (theObj) {
		Simple.SpiffyForm($(this));
		return false;
	});
};

/**
 * Execute a Spiffy Link
 * Loading a link with AJAX
 *
 * @access     private
**/
Simple.SpiffyLink = function(obj) {
	$("#loading").show();
	
	// Create an AJAX Request
	$.ajax({
		// URL is the 'href' attribute of the tag
		url: obj.attr('href'),
		data: {
			form_key: Simple.csrf_token_value,
			'user_id':	Simple.user_id
		},
		cache: false,
		dataType: "script",
		type: 'POST',
		
		success: function (data)
		{
			// They can specify a callback function in the 'callback' attribute
			if (obj.attr("callback"))
			{
				eval(obj.attr("callback"));
			}
		},
		
		// On error, display an "nice" message!
		error: function() {
			Simple.ajaxError();
		},
		
		// After the error and success callbacks are done
		complete: function() { $("#loading").hide(); },
			
	});
};

/**
 * Execute a Spiffy Form
 * Submitting a form with AJAX
 *
 * @access     private
**/
Simple.SpiffyForm = function(obj) {
	$("#loading").show();
	
	// Create an AJAX Request
	$.ajax({
		// URL is the 'href' attribute of the tag
		url: obj.attr('action'),
		data: {
			form_key: Connect.csrf_token_value,
			'user_id':	Connect.user_id,
			'form_data': obj.serialize(),
		},
		cache: false,
		dataType: "script",
		type: 'POST',
		
		success: function (data)
		{
			// They can specify a callback function in the 'callback' attribute
			if (obj.attr("callback"))
			{
				eval(obj.attr("callback"));
			}
		},
		
		// On error, display an "nice" message!
		error: function() {
			Connect.ajaxError();
		},
		
		// After the error and success callbacks are done
		complete: function() { $("#loading").hide(); },
			
	});
};

/*
 * Write a success message that will fade away
 *
 * @param      string
*/
Simple.successMessage = function(msg)
{
     $("#header").before('<div id="" class="notice-bar alert-message success fixed-top" style="position:fixed; display:none;"><span><strong>Woot!</strong> '+msg+'</span></div>');
     
     // Slide it in to make it seem more in realtime.
     $('div.fixed-top.notice-bar').slideDown('fast');
     
	$(".notice-bar").delay(5000).slideUp();
	
	return true;
};

/*
 * Write a failure message (not a panda but a a small one
 *
 * @param      string
*/
Simple.failureMessage = function(msg)
{
	$("#header").before('<div id="" class="notice-bar alert-message error fixed-top" style="position:fixed; display:none;"><span><strong>Oops.</strong> '+msg+'</span></div>');
	
     // Slide it in to make it seem more in realtime.
     $('div.fixed-top.notice-bar').slideDown('fast');
     
	$(".notice-bar").delay(5000).slideUp();
	
	return true;
};

/**
 * Show the Table Overlay
 *
 * @access     public
**/
Simple.showTableOverlay = function()
{
     $('div.loading-overlay').css('opacity', 0.5);
     $('div.loading-status').show();
};

/**
 * Hide the Table Overlay
 *
 * @access     public
**/
Simple.hideTableOverlay = function()
{
     $('div.loading-overlay').css('opacity', 1);
     $('div.loading-status').hide();
};