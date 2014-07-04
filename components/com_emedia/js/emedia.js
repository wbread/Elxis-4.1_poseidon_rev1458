/**
COMPONENT EMEDIA JAVASCRIPT
Author: Ioannis Sannos - http://www.elxis.org
Based on filemanager.js by Jason Huck and Simon Georget
Original authors comments removed to reduce file size

ATTENTION: THIS FILE IS UTF-8 ENCODED!
*/

(function($) {
$.urlParam = function(name){
	var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
	if (results) {
		return results[1]; 
	} else {
		return 0;
	}
}

var cfg_imagesext = ['jpg', 'jpeg', 'gif', 'png'];
if($.urlParam('langCode') != 0 && file_exists(cfg_urlbase+'/components/com_emedia/js/language/'+ $.urlParam('langCode')+'.js')) { cfg_lang = $.urlParam('langCode'); }
var lg = [];
$.ajax({
	url: cfg_urlbase+'/components/com_emedia/js/language/'+cfg_lang+'.js',
	async: false, dataType: 'json',
	success: function (json) { lg = json; }
});

$.prompt.setDefaults({ overlayspeed:'fast', show:'fadeIn', opacity:0.4 });

/* CALCULATE UI HEIGHT */
var setDimensions = function() {
	if (cfg_editor == 1) { var removeH = 40; } else { var removeH = 240; }
	var newH = $(window).height() - $('#uploader').height() - removeH;
	$('#splitter, #filetree, #fileinfo, .vsplitbar').height(newH);
}

/* DISPLAY PATHWAY */
var displayPath = function(path) {
	if (cfg_showfullpath == false) {
		return 'function' === (typeof displayPathDecorator) ? displayPathDecorator(path) : path.replace(cfg_fileroot, "/");
	} else {
		return path;
	}
}

/* SET VIEW BUTTONS STATE */
var setViewButtonsFor = function(viewMode) {
    if (viewMode == 'grid') { $('#grid').addClass('ON'); $('#list').removeClass('ON'); } else { $('#list').addClass('ON'); $('#grid').removeClass('ON'); }
}

/* SMART LIMIT STRING */
function smartLimit(str, maxlength) {
	if (isNaN(maxlength)) { maxlength = 20; }
	var slength = str.length;
	if (slength <= maxlength) { return str; }
	if (slength < 8) { return str; }
	if (slength > 17) { var flen = maxlength - 9; var startpoint = slength - 6; } else { var flen = maxlength - 6; var startpoint = slength - 3; }
	var newstring = str.substring(0,flen)+'...'+str.substr(startpoint);
	return newstring;
}

/* CHECK IF URL EXISTS */
function file_exists (url) {
    var req = this.window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
    if (!req) { throw new Error('XMLHttpRequest not supported'); }
    req.open('HEAD', url, false);
    req.send(null);
    if (req.status == 200) { return true; }
    return false;
}

/* PREG_REPLACE FOR JAVASCRIPT */
var preg_replace = function(array_pattern, array_pattern_replace, str)  {
	var new_str = String (str);
	for (i=0; i<array_pattern.length; i++) {
		var reg_exp= RegExp(array_pattern[i], "g");
		var val_to_replace = array_pattern_replace[i];
		new_str = new_str.replace (reg_exp, val_to_replace);
	}
	return new_str;
}


/* CLEAN A STRING */
var cleanString = function(str) {
	var cleaned = "";
	var p_search  = new Array("Š", "š", "Đ", "đ", "Ž", "ž", "Č", "č", "Ć", "ć", "À", 
		"Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï", 
		"Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ő", "Ø", "Ù", "Ú", "Û", "Ü", "Ý", "Þ", "ß", 
		"à", "á", "â", "ã", "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì",  "í",  
		"î", "ï", "ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ő", "ø", "ù", "ú", "û", "ý", 
		"ý", "þ", "ÿ", "Ŕ", "ŕ", " ", "'", "/", "Γ", "Δ", "Θ", "Λ", "Ξ", "Π", "Σ", 
		"Φ", "Ψ", "Ω", "β", "γ", "δ", "ζ", "η", "θ", "κ", "λ", "μ", "ν", "ξ", "π", "ρ", "σ", "τ", "υ", "φ", "χ", "ψ", "ω"
	);
	var p_replace = new Array("S", "s", "Dj", "dj", "Z", "z", "C", "c", "C", "c", "A", 
		"A", "A", "A", "A", "A", "A", "C", "E", "E", "E", "E", "I", "I", "I", "I", 
		"N", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "Y", "B", "Ss", 
		"a", "a", "a", "a", "a", "a", "a", "c", "e", "e", "e", "e", "i", "i",
		"i", "i", "o", "n", "o", "o", "o", "o", "o", "o", "o", "u", "u", "u", "y", 
		"y", "b", "y", "R", "r", "_", "_", "", "G", "D", "TH", "L", "KS", "P", "S", 
		"F", "PS", "O", "b", "g", "d", "z", "i", "th", "k", "l", "m", "n", "ks", "p", "r", "s", "t", "u", "f", "x", "ps", "o"
	);
	cleaned = preg_replace(p_search, p_replace, str);
	cleaned = cleaned.replace(/[^\-\_a-zA-Z0-9]/g, "");
	cleaned = cleaned.replace(/[_]+/g, "_");
	return cleaned;
}

/* GET FILENAME WITHOUT EXTENSION */
var nameFormat = function(input) {
	filename = '';
	if(input.lastIndexOf('.') != -1) {
		filename  = cleanString(input.substr(0, input.lastIndexOf('.')));
		filename += '.'+input.split('.').pop();
	} else {
		filename = cleanString(input);
	}
	return filename;
}

/* SET ERROR */
var handleError = function(errMsg) {
	$('#fileinfo').html('<h3>'+errMsg+ '</h3>');
	$('#newfile').attr("disabled", "disabled");
	$('#upload').attr("disabled", "disabled");
	$('#newfolder').attr("disabled", "disabled");
}

/* CHECK IF ITEM HAS A CAPABILITY */
function has_capability(data, cap) {
	if ((data['filetype'] == 'dir') && (cap == 'download')) { return false; }
	if ((data['filetype'] == 'dir') && (cap == 'resize')) { return false; }
	if (cap == 'compress') {
		if (data['filetype'] == 'dir') { return true; } else { return false; }
	}
	if (typeof(data['capabilities']) == "undefined") { return true; }
	if (cap == 'resize') {
		var ext = data['filetype'].toLowerCase();
		if ((ext == 'jpg') || (ext == 'jpeg') || (ext == 'png') || (ext == 'gif')) {
			return $.inArray(cap, data['capabilities']) > -1;
		} else {
			return false;
		}
	}
	return $.inArray(cap, data['capabilities']) > -1;
}

/* BASENAME FOR JAVASCRIPT */
var basename = function(path, suffix) {
    var b = path.replace(/^.*[\/\\]/g, '');
    if (typeof(suffix) == 'string' && b.substr(b.length-suffix.length) == suffix) { b = b.substr(0, b.length-suffix.length); }
    return b;
}

/* ADD A NEW FOLDER */
var setUploader = function(path){
	$('#currentpath').val(path);
	$('#uploader h3').text(lg.current_folder + displayPath(path));
	$('#newfolder').unbind().click(function(){
		var foldername = '';
		var msg = lg.prompt_foldername+' : <input id="fname" name="fname" type="text" value="'+foldername+'" />';
		var getFolderName = function(v, m){
			if(v != 1) return false;		
			var fname = m.children('#fname').val();
			if (fname != '') {
				foldername = cleanString(fname);
				var d = new Date();
				$.getJSON(cfg_connector+'?mode=addfolder&path='+$('#currentpath').val()+'&name='+foldername+'&time='+d.getMilliseconds(), function(result){
					if(result['code'] == 0) {
						addFolder(result['parent'], result['name']);
						getFolderInfo(result['parent']);
                        $('#filetree').find('a[rel="'+result['parent'] +'/"]').click().click();
					} else {
						$.prompt(result['error']);
					}
				});
			} else {
				$.prompt(lg.no_foldername);
			}
		}
		var btns = {}; 
		btns[lg.create_folder] = true; 
		btns[lg.cancel] = false; 
		$.prompt(msg, { callback: getFolderName, buttons: btns });	
	});	
}

/* BIND ACTIONS TO TOOLBAR IN DETAILS VIEW */
var bindToolbar = function(data) {
	$('#fileinfo').find('button').wrapInner('<span></span>');
	if (!has_capability(data, 'select')) {
		$('#fileinfo').find('button#select').hide();
	} else {
		$('#fileinfo').find('button#select').click(function(){ selectItem(data); }).show();
	}
	if (!has_capability(data, 'rename')) {
		$('#fileinfo').find('button#rename').hide();
	} else {
		$('#fileinfo').find('button#rename').click(function(){ var newName = renameItem(data); if(newName.length) $('#fileinfo > h3').text(newName); }).show();
	}
	if (!has_capability(data, 'delete')) {
		$('#fileinfo').find('button#delete').hide();
	} else {
		$('#fileinfo').find('button#delete').click(function(){ if(deleteItem(data)) $('#fileinfo').html('<h3>'+lg.select_from_left+'</h3>'); }).show();
	}
	if (!has_capability(data, 'resize')) {
		$('#fileinfo').find('button#resize').hide();
	} else {
		$('#fileinfo').find('button#resize').click(function(){ resizeItem(data); }).show();
	}
	if (!has_capability(data, 'compress')) {
		$('#fileinfo').find('button#compress').hide();
	} else {
		$('#fileinfo').find('button#compress').click(function(){ compressItem(data); }).show();
	}
	if (!has_capability(data, 'download')) {
		$('#fileinfo').find('button#download').hide();
	} else {
		$('#fileinfo').find('button#download').click(function(){ window.location = cfg_connector+'?mode=download&path='+encodeURIComponent(data['path']); }).show();
	}
}

/* HUMAN FRIENDLY BYTES FORMAT */
var formatBytes = function(bytes){
	var n = parseFloat(bytes);
	var d = parseFloat(1024);
	var c = 0;
	var u = ['bytes','kb','mb','gb'];
	while(true){
		if (n < d){ n = Math.round(n * 100) / 100; return n + u[c]; } else { n /= d; c += 1; }
	}
}

/* SELECT ITEM FOR EDITOR */
var selectItem = function(data) {
    var url = cfg_urlbase+'/'+cfg_relpath+''+data['path'];
	if (window.opener) {
		if($.urlParam('CKEditor')){
			window.opener.CKEDITOR.tools.callFunction($.urlParam('CKEditorFuncNum'), url);
		} else { //FCKEditor 2.0
			if(data['properties']['width'] != ''){
				var p = url;
				var w = data['properties']['width'];
				var h = data['properties']['height'];			
				window.opener.SetUrl(p,w,h);
			} else {
				window.opener.SetUrl(url);
			}		
		}
		window.close();
	} else {
		$.prompt('The Select function is only used for integration with FCKEditor.');
	}
}

/* RENAME ITEM */
var renameItem = function(data) {
	var finalName = '';
	var msg = lg.new_filename+' : <input id="rname" name="rname" type="text" value="'+data['filename']+'" />';
	var getNewName = function(v, m) {
		if(v != 1) return false;
		rname = m.children('#rname').val();
		if(rname != ''){
			var givenName = nameFormat(rname);	
			var oldPath = data['path'];	
			var connectString = cfg_connector+'?mode=rename&old='+data['path']+'&new='+givenName;
			$.ajax({
				type: 'GET', url: connectString, dataType: 'json', async: false,
				success: function(result){
					if (result['code'] == 0) {
						var newPath = result['new_path'];
						var newName = result['new_name'];
						updateNode(oldPath, newPath, newName);
						var title = $("#preview h3").attr("title");
						if (typeof title !="undefined" && title == oldPath) {
							$('#preview h3').text(newName);
						}
						if($('#fileinfo').data('view') == 'grid'){
							$('#fileinfo img[alt="'+oldPath+'"]').parent().next('p').text(newName);
							$('#fileinfo img[alt="'+oldPath+'"]').attr('alt', newPath);
						} else {
							$('#fileinfo td[title="'+oldPath+'"]').text(newName);
							$('#fileinfo td[title="'+oldPath+'"]').attr('title', newPath);
						}
					} else {
						$.prompt(result['error']);
					}
					finalName = result['new_name'];		
				}
			});	
		}
	}
	var btns = {}; 
	btns[lg.rename] = true; 
	btns[lg.cancel] = false; 
	$.prompt(msg, { callback: getNewName, buttons: btns });
	return finalName;
}


/* RESIZE IMAGE */
var resizeItem = function(data) {
	var msg = lg.new_width+' : <input id="rwidth" name="rwidth" type="text" value="'+data['properties']['width']+'" />';
	var getNewWidth = function(v, m) {
		if (v != 1) { return false; }
		var rwidth = m.children('#rwidth').val();
		rwidth = parseInt(rwidth, 10);
		if (rwidth > 0) {
			var connectString = cfg_connector+'?mode=resize&path='+data['path']+'&rwidth='+rwidth;
			$.ajax({
				type: 'GET', url: connectString, dataType: 'json', async: false,
				success: function(result){
					if (result['code'] == 0) {
                		$('#filetree').find('a[rel="'+result['dirname'] +'/"]').click().click();
					} else {
						$.prompt(result['error']);
					}		
				}
			});	
		}
	}
	var btns = {}; 
	btns[lg.resize] = true; 
	btns[lg.cancel] = false; 
	$.prompt(msg, { callback: getNewWidth, buttons: btns });
}

/* DELETE FILE/FOLDER */
var deleteItem = function(data) {
	var isDeleted = false;
	var msg = lg.confirmation_delete;
	var doDelete = function(v, m) {
		if (v != 1) { return false; }	
		var connectString = cfg_connector+'?mode=delete&path='+encodeURIComponent(data['path']),
        parent = data['path'].split('/').reverse().slice(1).reverse().join('/') + '/';
		$.ajax({
			type: 'GET', url: connectString, dataType: 'json', async: false,
			success: function(result) {
				if (result['code'] == 0) {
					removeNode(result['path']);
					var rootpath = result['path'].substring(0, result['path'].length-1);
					rootpath = rootpath.substr(0, rootpath.lastIndexOf('/') + 1);
					$('#uploader h3').text(lg.current_folder + displayPath(rootpath));
					isDeleted = true;
                    $('#filetree').find('a[rel="'+parent +'/"]').click().click();
				} else {
					isDeleted = false;
					$.prompt(result['error']);
				}
			}
		});
	}
	var btns = {}; 
	btns[lg.yes] = true; 
	btns[lg.no] = false; 
	$.prompt(msg, { callback: doDelete, buttons: btns });
	return isDeleted;
}

/* COMPRESS FOLDER */
var compressItem = function(data) {
	var connectString = cfg_connector+'?mode=compress&path='+encodeURIComponent(data['path']),
	parent = data['path'].split('/').reverse().slice(1).reverse().join('/') + '/';
		$.ajax({
		type: 'GET', url: connectString, dataType: 'json', async: false,
		success: function(result) {
			if (result['code'] == 0) {
				addNode(result['path'], result['archive']);
                $('#filetree').find('a[rel="'+data['path'] +'/"]').click().click();
			} else {
				$.prompt(result['error']);
			}
		}
	});
}

/* ADD NEW NODE AFTER FILE UPLOAD OR ZIP */
var addNode = function(path, name) {
	var ext = name.substr(name.lastIndexOf('.') + 1);
	var thisNode = $('#filetree').find('a[rel="'+path+'"]');
	var parentNode = thisNode.parent();
	var newNode = '<li class="file ext_'+ext+'"><a rel="'+path + name+'" href="#">'+name+'</a></li>';
	if(!parentNode.find('ul').size()) parentNode.append('<ul></ul>');		
	parentNode.find('ul').prepend(newNode);
	thisNode.click().click();
	getFolderInfo(path);
}

/* UPDATE NODE AFTER RENAME */
var updateNode = function(oldPath, newPath, newName){
	var thisNode = $('#filetree').find('a[rel="'+oldPath+'"]');
	var parentNode = thisNode.parent().parent().prev('a');
	thisNode.attr('rel', newPath).text(newName);
	parentNode.click().click();
}

/* REMOVE NODE AFTER DELETE */
var removeNode = function(path){
    $('#filetree').find('a[rel="'+path+'"]').parent().fadeOut('slow', function(){ $(this).remove(); });
    if($('#fileinfo').data('view') == 'grid'){
        $('#contents img[alt="'+path+'"]').parent().parent().fadeOut('slow', function(){ $(this).remove(); });
    } else {
        $('table#contents').find('td[title="'+path+'"]').parent().fadeOut('slow', function(){ $(this).remove(); });
    }
    if ($('#preview').length) {
    	getFolderInfo(path.substr(0, path.lastIndexOf('/') + 1));
	}
}

/* CREATE A NEW FOLDER */
var addFolder = function(parent, name) {
	var newNode = '<li class="directory collapsed"><a rel="'+parent + name+'/" href="#">'+name+'</a><ul class="jqueryFileTree" style="display: block;"></ul></li>';
	var parentNode = $('#filetree').find('a[rel="'+parent+'"]');
	if (parent != cfg_fileroot) {
		parentNode.next('ul').prepend(newNode).prev('a').click().click();
	} else {
		$('#filetree > ul').prepend(newNode); 
		$('#filetree').find('li a[rel="'+parent + name+'/"]').click(function(){
				getFolderInfo(parent + name + '/');
			}).each(function() {
				$(this).contextMenu(
					{ menu: getContextMenuOptions($(this)) }, 
					function(action, el, pos){
						var path = $(el).attr('rel');
						setMenus(action, path);
					});
				}
			);
	}
}

/* GET FILE OR FOLDER INFO */
var getDetailView = function(path){
	if(path.lastIndexOf('/') == path.length - 1){
		getFolderInfo(path);
		$('#filetree').find('a[rel="'+path+'"]').click();
	} else {
		getFileInfo(path);
	}
}

/* GET CONTEXT MENU OPTIONS */
function getContextMenuOptions(elem) {
	var optionsID = elem.attr('class').replace(/ /g, '_');
	if (optionsID == '') return 'itemOptions';
	if (!($('#' + optionsID).length)) {
		var newOptions = $('#itemOptions').clone().attr('id', optionsID);
		if (!elem.hasClass('cap_select')) $('.select', newOptions).remove();
		if (!elem.hasClass('cap_download')) $('.download', newOptions).remove();
		if (!elem.hasClass('cap_rename')) $('.rename', newOptions).remove();
		if (!elem.hasClass('cap_resize')) $('.resize', newOptions).remove();
		if (!elem.hasClass('cap_compress')) $('.compress', newOptions).remove();
		if (!elem.hasClass('cap_delete')) $('.delete', newOptions).remove();
		$('#itemOptions').after(newOptions);
	}
	return optionsID;
}

/* BIND CONTEXT MENUS TO ITEMS IN LIST/GRID VIEW */
var setMenus = function(action, path){
	var d = new Date();
	$.getJSON(cfg_connector+'?mode=getinfo&path='+path +'&time='+d.getMilliseconds(), function(data){
		if($('#fileinfo').data('view') == 'grid'){
			var item = $('#fileinfo').find('img[alt="'+data['path']+'"]').parent();
		} else {
			var item = $('#fileinfo').find('td[title="'+data['path']+'"]').parent();
		}
		switch(action) {
			case 'select': selectItem(data); break;
			case 'download': window.location = cfg_connector+'?mode=download&path='+data['path']; break;
			case 'rename': var newName = renameItem(data); break;
			case 'resize': resizeItem(data); break;
			case 'compress': compressItem(data); break;
			case 'delete': deleteItem(data); break;
		}
	});
}

/* FILE DETAILS VIEW */
var getFileInfo = function(file) {
	var currentpath = file.substr(0, file.lastIndexOf('/') + 1);
	setUploader(currentpath);
	var template = '<div id="preview"><img /><h3></h3><dl></dl></div>';
	template += '<form id="toolbar">';
	if (window.opener != null) { template += '<button id="select" name="select" type="button" value="Select" class="emed_btn">'+lg.select+'</button>'; }
	if (cfg_editor != 1) { template += '<button id="download" name="download" type="button" value="Download" class="emed_btn">'+lg.download+'</button>'; }
	
	if( cfg_browseonly != true) { template += '<button id="rename" name="rename" type="button" value="Rename" class="emed_btn">'+lg.rename+'</button>'; }
	if (cfg_browseonly != true) { template += '<button id="resize" name="resize" type="button" value="Resize" class="emed_btn">'+lg.resize+'</button>'; }
	if (cfg_browseonly != true) {
		if (cfg_editor != 1) { template += '<button id="compress" name="compress" type="button" value="Compress" class="emed_btn">'+lg.compress+'</button>'; }
	}
	if (cfg_browseonly != true) { template += '<button id="delete" name="delete" type="button" value="Delete" class="emed_btn">'+lg.del+'</button>'; }
	template += '<button id="parentfolder" class="emed_btn">'+lg.parentfolder+'</button>';
	template += '</form>';
	$('#fileinfo').html(template);
	$('#parentfolder').click(function() {getFolderInfo(currentpath);});
	var d = new Date();
	$.getJSON(cfg_connector+'?mode=getinfo&path='+encodeURIComponent(file)+'&time='+d.getMilliseconds(), function(data){
		if(data['code'] == 0){
			$('#fileinfo').find('h3').text(data['filename']).attr('title', file);
			$('#fileinfo').find('img').attr('src',data['preview']);
			var properties = '';
			if(data['properties']['width'] && data['properties']['width'] != '') properties += '<dt>'+lg.dimensions+'</dt><dd>'+data['properties']['width']+'x'+data['properties']['height']+'</dd>';
			if(data['properties']['date_created'] && data['properties']['date_created'] != '') properties += '<dt>'+lg.created+'</dt><dd>'+data['properties']['date_created']+'</dd>';
			if(data['properties']['date_modified'] && data['properties']['date_modified'] != '') properties += '<dt>'+lg.modified+'</dt><dd>'+data['properties']['date_modified']+'</dd>';
			if(data['properties']['size'] || parseInt(data['properties']['size'])==0) properties += '<dt>'+lg.size+'</dt><dd>'+formatBytes(data['properties']['size'])+'</dd>';
			$('#fileinfo').find('dl').html(properties);
			bindToolbar(data);
		} else {
			$.prompt(data['error']);
		}
	});
}

/* FOLDER VIEW */
var getFolderInfo = function(path) {
	setUploader(path);
	$('#fileinfo').html('<img id="activity" src="'+cfg_urlbase+'/components/com_emedia/css/images/wait30trans.gif" width="30" height="30" alt="loading" />');
	var d = new Date();
	var url = cfg_connector+'?path='+encodeURIComponent(path)+'&mode=getfolder&showThumbs='+cfg_showthumbs+'&tree=0&time='+d.getMilliseconds();
	if ($.urlParam('type')) { url += '&type='+$.urlParam('type'); }
	$.getJSON(url, function(data){
		var result = '';
		if (data.code == '-1') { handleError(data.error); return; };
		if(data){
			if($('#fileinfo').data('view') == 'grid') {
				result += '<ul id="contents" class="grid">';
				for(key in data){
					var props = data[key]['properties'];
					var cap_classes = '';
					var cap_space = '';
					for (cap in cfg_capabilities) {
						if (has_capability(data[key], cfg_capabilities[cap])) { cap_classes += cap_space+'cap_'+cfg_capabilities[cap]; cap_space = ' '; }
					}
					var scaledWidth = 64;
					var actualWidth = props['width'];
					if (actualWidth > 1 && actualWidth < scaledWidth) { scaledWidth = actualWidth; }
					var titletext = '';
					var titlesep = '';
					var showntitle = smartLimit(data[key]['filename'], 20);
					if (showntitle != data[key]['filename']) { titletext += data[key]['filename']; titlesep = ', '; }
					if (props['size'] && props['size'] > 0) { titletext += titlesep+''+formatBytes(props['size']); titlesep = ', ';}					
					if (props['width'] && props['width'] > 0) { titletext += titlesep+''+props['width']+'x'+props['height']+'px'; }
					if (titletext != '') {
						result += '<li class="'+cap_classes+'"><div class="clip"><img src="'+data[key]['preview']+'" width="'+scaledWidth+'" alt="'+data[key]['path']+'" title="'+titletext+'" /></div><p>'+showntitle+'</p>';
					} else {
						result += '<li class="'+cap_classes+'"><div class="clip"><img src="'+data[key]['preview']+'" width="'+scaledWidth+'" alt="'+data[key]['path']+'" /></div><p>'+showntitle+'</p>';
					}
					if (props['width'] && props['width'] > 0) { result += '<span class="meta dimensions">'+props['width']+'x'+props['height']+'</span>'; }
					if (props['size'] && props['size'] > 0) { result += '<span class="meta size">'+props['size']+'</span>'; }
					if (props['date_created'] && props['date_created'] != '') { result += '<span class="meta created">'+props['date_created']+'</span>'; }
					if (props['date_modified'] && props['date_modified'] != '') { result += '<span class="meta modified">'+props['date_modified']+'</span>'; }
					result += '</li>';
				}
				result += '</ul>';
			} else {
				result += '<table id="contents" class="list">';
				result += '<thead><tr><th class="headerSortDown"><span>'+lg.name+'</span></th><th><span>'+lg.dimensions+'</span></th><th><span>'+lg.size+'</span></th><th><span>'+lg.modified+'</span></th></tr></thead>';
				result += '<tbody>';
				for(key in data){
					var path = data[key]['path'];
					var props = data[key]['properties'];
					var cap_classes = "";
					for (cap in cfg_capabilities) {
						if (has_capability(data[key], cfg_capabilities[cap])) { cap_classes += " cap_"+cfg_capabilities[cap]; }
					}
					result += '<tr class="'+cap_classes+'">';
					if (cfg_tree_files == 1) {
						result += '<td title="'+path+'">'+data[key]['filename']+'</td>';
					} else {
						var mediaicon = getNodeIcon(data[key]['filetype']);
						result += '<td title="'+path+'" style="background-image:url('+cfg_urlbase+'/components/com_emedia/css/images/'+mediaicon+'.png);">'+data[key]['filename']+'</td>';
					}
					if (props['width'] && props['width'] > 0){
						result += '<td>'+props['width']+'x'+props['height']+'</td>';
					} else {
						result += '<td></td>';
					}

					if(props['size'] && props['size'] > 0){
						result += '<td><abbr title="'+props['size']+'">'+formatBytes(props['size'])+'</abbr></td>';
					} else {
						result += '<td></td>';
					}

					if(props['date_modified'] && props['date_modified'] != '') {
						result += '<td>'+props['date_modified']+'</td>';
					} else {
						result += '<td></td>';
					}
					result += '</tr>';					
				}
				result += '</tbody>';
				result += '</table>';
			}			
		} else {
			result += '<h3>Could not retrieve folder contents.</h3>';
		}

		$('#fileinfo').html(result);
		if($('#fileinfo').data('view') == 'grid'){
			$('#fileinfo').find('#contents li').click(function(){
				var path = $(this).find('img').attr('alt');
				getDetailView(path);
			}).each(function() {
				$(this).contextMenu(
					{ menu: getContextMenuOptions($(this)) },
					function(action, el, pos){
						var path = $(el).find('img').attr('alt');
						setMenus(action, path);
					}
				);
			});
		} else {
			if (cfg_tree_files == 1) {
				$('#fileinfo').find('td:first-child').each(function() {
					var path = $(this).attr('title');
					var treenode = $('#filetree').find('a[rel="'+path+'"]').parent();
					$(this).css('background-image', treenode.css('background-image'));
				});
			}

			$('#fileinfo tbody tr').click(function(){
				var path = $('td:first-child', this).attr('title');
				getDetailView(path);		
			}).each(function() {
				$(this).contextMenu(
					{ menu: getContextMenuOptions($(this)) },
					function(action, el, pos){
						var path = $('td:first-child', el).attr('title');
						setMenus(action, path);
					}
				);
			});

			$('#fileinfo').find('table').tablesorter({
				textExtraction: function(node){					
					if($(node).find('abbr').size()){
						return $(node).find('abbr').attr('title');
					} else {					
						return node.innerHTML;
					}
				}
			});
		}
	});
}

/* GET NODE ICON */
function getNodeIcon(filetype) {
	var filetype = filetype.toLowerCase();
	if ((filetype == '') || (filetype == "undefined")) { return 'file'; }
	if (filetype == 'dir') { return 'folder_open'; }
	if ((filetype == 'html') || (filetype == 'htm')) { return 'html'; }
	if (filetype == 'css') { return 'css'; }
	if (filetype == 'php') { return 'php'; }
	if ((filetype == 'fla') || (filetype == 'swf')) { return 'flash'; }
	if ((filetype == 'txt') || (filetype == 'log')) { return 'txt'; }
	var nodeimage = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'pcx', 'svg', 'tif', 'tiff', 'ico'];
	var nodefilm = ['3gp', 'avi', 'wmv', 'mp4', 'mpg', 'mpeg', 'ogv', 'm4v', 'mov', 'asf', 'asx', 'flv', 'mkv', 'rm'];
	var nodemusic = ['mp3', 'ogg', 'm4p', 'wav', 'wma', 'mpa', 'ra', 'm4a', 'mid', 'xx'];
	var nodecode = ['afp', 'afpa', 'afpa', 'asp', 'aspx', 'c', 'cfm', 'cgi', 'cpp', 'vb', 'h', 'lasso', 'xml'];
	var nodedoc = ['doc', 'docx', 'odt', 'rtf'];
	var nodexls = ['xls', 'xlsx', 'ods', 'csv'];
	var nodescript = ['js', 'pl', 'py'];
	var nodezip = ['zip', 'rar', 'tar', 'gz', 'bzip2', 'gzip'];
	if (nodeimage.indexOf(filetype) > -1) { return 'picture'; }
	if (nodefilm.indexOf(filetype) > -1) { return 'film'; }
	if (nodemusic.indexOf(filetype) > -1) { return 'music'; }
	if (nodecode.indexOf(filetype) > -1) { return 'code'; }
	if (nodedoc.indexOf(filetype) > -1) { return 'doc'; }
	if (nodexls.indexOf(filetype) > -1) { return 'xls'; }
	if (nodescript.indexOf(filetype) > -1) { return 'script'; }
	if (nodezip.indexOf(filetype) > -1) { return 'zip'; }
	if ((filetype == 'pdf') || (filetype == 'odf')) { return 'pdf'; }
	if ((filetype == 'ppt') || (filetype == 'odp')) { return 'ppt'; }
	if (filetype == 'pdf') { return 'pdf'; }
	if (filetype == 'psd') { return 'psd'; }
	if ((filetype == 'sql') || (filetype == 'mdb')) { return 'db'; }
	return 'file';
}

/* GENERATE JQUERY FILETREE */
var populateFileTree = function(path, callback) {
	var d = new Date();
	var url = cfg_connector+'?path='+encodeURIComponent(path)+'&mode=getfolder&showThumbs='+cfg_showthumbs+'&tree=1&time='+d.getMilliseconds();
	if ($.urlParam('type')) { url += '&type='+$.urlParam('type'); }
	$.getJSON(url, function(data) {
		var result = '';
		if (data.code == '-1') { handleError(data.error); return; };
		if (data) {
			result += '<ul class="jqueryFileTree" style="display:none;">';
			for(key in data) {
				var cap_classes = "";
				for (cap in cfg_capabilities) {
					if (has_capability(data[key], cfg_capabilities[cap])) { cap_classes += " cap_"+cfg_capabilities[cap]; }
				}
				if (data[key]['filetype'] == 'dir') {
					result += "<li class=\"directory collapsed\"><a href=\"#\" class=\""+cap_classes+"\" rel=\""+data[key]['path']+"\">"+data[key]['filename']+"</a></li>";
				} else {
					result += "<li class=\"file ext_"+data[key]['filetype'].toLowerCase()+"\"><a href=\"#\" class=\""+cap_classes+"\" rel=\""+data[key]['path']+"\">"+data[key]['filename']+"</a></li>";
				}
			}
			result += '</ul>';
		} else {
			result += '<h3>Could not retrieve folder contents.</h3>';
		}
		callback(result);
	});
}

/* INITIALIZATION */
$(function(){
	if($.urlParam('expandedFolder') != 0) {
		expandedFolder = $.urlParam('expandedFolder');
		fullexpandedFolder = cfg_fileroot + expandedFolder;
	} else {
		expandedFolder = '';
		fullexpandedFolder = null;
	}

	setDimensions();
	$(window).resize(setDimensions);
	if (cfg_autoload == true) {
		if (cfg_editor == 1) {
			$('#upload').append('');
			$('#upload').attr('title', lg.upload);
			$('#newfolder').append('');
			$('#newfolder').attr('title', lg.new_folder);
		} else {
			$('#upload').append(lg.upload);
			$('#newfolder').append(lg.new_folder);
		}
		$('#grid').attr('title', lg.grid_view);
		$('#list').attr('title', lg.list_view);
		$('#fileinfo h3').append(lg.select_from_left);
		$('#itemOptions a[href$="#select"]').append(lg.select);
		$('#itemOptions a[href$="#download"]').append(lg.download);
		$('#itemOptions a[href$="#rename"]').append(lg.rename);
		$('#itemOptions a[href$="#resize"]').append(lg.resize);
		$('#itemOptions a[href$="#compress"]').append(lg.compress);
		$('#itemOptions a[href$="#delete"]').append(lg.del);
	}

	$('#splitter').splitter({ sizeLeft: 200 });
	$('button').wrapInner('<span></span>');
	$('#fileinfo').data('view', cfg_viewmode);
	setViewButtonsFor(cfg_viewmode);

	$('#home').click(function(){
		var currentViewMode = $('#fileinfo').data('view');
		$('#fileinfo').data('view', currentViewMode);
		$('#filetree>ul>li.expanded>a').trigger('click');
		getFolderInfo(cfg_fileroot);
	});

	$('#grid').click(function(){
		setViewButtonsFor('grid');
		$('#fileinfo').data('view', 'grid');
		getFolderInfo($('#currentpath').val());
	});

	$('#list').click(function(){
		setViewButtonsFor('list');
		$('#fileinfo').data('view', 'list');
		getFolderInfo($('#currentpath').val());
	});

	setUploader(cfg_fileroot);
	$('#uploader').attr('action', cfg_connector);
	$('#uploader').ajaxForm({
		target: '#uploadresponse',
		beforeSubmit: function(arr, form, options) {
			$('#upload').attr('disabled', true);
			$('#upload span').addClass('loading').text(lg.loading_data);
			if ($.urlParam('type').toString().toLowerCase() == 'images') {
				var newfileSplitted = $('#newfile', form).val().toLowerCase().split('.');
				for (key in cfg_imagesext) {
					if (cfg_imagesext[key] == newfileSplitted[newfileSplitted.length-1]) { return true; }
				}
				$.prompt(lg.UPLOAD_IMAGES_ONLY);
				return false;
			}
		},
		success: function(result){
			//var data = jQuery.parseJSON($('#uploadresponse').find('textarea').text());
			var data = jQuery.parseJSON($('#uploadresponse').text());
			if (data['code'] == 0) {
				addNode(data['path'], data['name']);
                $('#filetree').find('a[rel="'+data['path'] +'/"]').click().click();
			} else {
				$.prompt(data['error']);
			}
			$('#upload').removeAttr('disabled');
			$('#upload span').removeClass('loading').text(lg.upload);
			$("#newfile").replaceWith('<input id="newfile" type="file" name="newfile" />');
		}
	});

    $('#filetree').fileTree({
		root: cfg_fileroot,
		datafunc: populateFileTree,
		multiFolder: false,
		folderCallback: function(path){ getFolderInfo(path); },
		expandedFolder: fullexpandedFolder,
		after: function(data){
			$('#filetree').find('li a').each(function() {
				$(this).contextMenu(
					{ menu: getContextMenuOptions($(this)) },
					function(action, el, pos){
						var path = $(el).attr('rel');
						setMenus(action, path);
					}
				)
			});
		}
	}, function(file){
		getFileInfo(file);
	});

	if(window.opener == null) { $('#itemOptions a[href$="#select"]').remove(); }
	if(cfg_browseonly == true) {
		$('#newfile').remove();
		$('#upload').remove();
		$('#newfolder').remove();
		$('#toolbar').remove('#rename');
		$('.contextMenu .rename').remove();
		$('#toolbar').remove('#resize');
		$('.contextMenu .resize').remove();
		$('#toolbar').remove('#compress');
		$('.contextMenu .compress').remove();
		$('.contextMenu .delete').remove();
	} else {
		if ($.inArray('upload', cfg_capabilities) == -1) {
			$('#newfile').remove();
			$('#upload').remove();
		}
		if ($.inArray('rename', cfg_capabilities) == -1) {
			$('#toolbar').remove('#rename');
			$('.contextMenu .rename').remove();
		}
		if ($.inArray('resize', cfg_capabilities) == -1) {
			$('#toolbar').remove('#resize');
			$('.contextMenu .resize').remove();
		}
		if ($.inArray('compress', cfg_capabilities) == -1) {
			$('#toolbar').remove('#compress');
			$('.contextMenu .compress').remove();
		}
		if ($.inArray('delete', cfg_capabilities) == -1) {
			$('.contextMenu .delete').remove();
		}
	}

    getDetailView(cfg_fileroot + expandedFolder);
});

})(jQuery);
