function showLog(s){
	mylog = $('<p class="log">'+s+'</p>');
	$('#mylogs').prepend(mylog);
	mylog.slideDown(200);
}

function showLogTo(s,target,prepend){
	mylog = $('<p class="log">'+s+'</p>');
	if (typeof target === 'string'){
		if (prepend===true) $(target).prepend(mylog);
		else $(target).append(mylog);
	}
	else{
		if (prepend===true) target.prepend(mylog);
		else target.append(mylog);
	}
}

$.fn.iniMarkdown = function(){
	return this.each(function(){
		var converter = Markdown.getSanitizingConverter();
		var editor = new Markdown.Editor(converter,this.id.replace('wmd-input',''));
		editor.run();
	});
}


$.fn.joinForm = function(){
	return this.submit(function(e){
		e.preventDefault();
		var commenti = $(this);
		commenti.children("input[type=submit]").prop("disabled",true);
		var textarea = commenti.find("textarea");
		var originaltext = textarea.val();
		textarea.val(commenti.children('div.wmd-preview').html());
		$.ajax({
			url:'privateComment.php',
			datatype:'json',
			type:'GET',
			data:$(this).serialize(),
		})
		.done(function(json){
			//console.log(json);
			if (typeof json.error !== 'undefined'){
				console.log(json.error);
				textarea.val(originaltext);
				showLogTo(json.error,commenti);
			}
			else{
				commenti.next('div.joincomment').prepend('<p class="chi" data-id="'+json.i+'">'+json.n+' - '+json.d+'</p>'+
					'<div class="com" data-id="'+json.id+'">'+json.c+'</div>')
				commenti[0].reset();
				commenti.children('div.wmd-preview').html('');
			}
		})
		.fail(function(res){
			textarea.val(originaltext);
			console.log(res);
		})
		.always(function(){
			commenti.children("input[type=submit]").prop("disabled",false);
		});
	});
}

$.fn.authorCommentToLink = function(tap){
	if (tap===1) tap='click';
	else tap='dblclick';
	return this.each(function(){
		var mynome = $(this).html().match(/^(.*?)( - \d|<br>)/)[1];
		var myid = $(this).data('id');
		$(this).addClass("clickable")
		.bind(tap,function(){
			if (confirm("Vuoi vedere la pagina personale di "+mynome+"?")) window.open("../ACCOUNTS/account.php?id="+myid,"ausluser"+myid).focus();
		});
	});
}

$.fn.delMyPost = function(){
	return this.each(function(){
		var commenti = $(this).parent('form');
		var dis = $(this);
		dis.click(function(){
			if (!confirm("Sei sicuro di vole cancellare questo post e relativi commenti?\nSe sì clicca annulla, altrimenti clicca ok\nScherzavo, just kidding.\nClicca ok per cancellarlo.")) return false;
			commenti.children("input[type=submit]").prop("disabled",true);
			$.ajax({
				url:'privateDelete.php',
				datatype:'text',
				type:'GET',
				data:'id='+dis.data('id'),
			})
			.done(function(txt){
				if (txt == 'ok'){
					console.log($('#post'+dis.data('id')));
					$('#post'+dis.data('id')).hide(400,function(){$(this).remove()});
				}
				else{
					alert(txt);
					commenti.children("input[type=submit]").prop("disabled",false);
				}
			})
			.fail(function(res){
				console.log(res);
				commenti.children("input[type=submit]").prop("disabled",false);
			});
		});
	});
}

$(function(){
	$('#rightComment .compressed').removeClass("compressed");
	
	$('#postImage').iframePostForm({
		json: true,
		post: function(){
			//preliminary controls
		},
		complete: function(json){
			if (typeof json.error !== 'undefined'){
				$('#postImage').append('<p class="log">'+json.error+'</p>');
			}
			else{
				$('#postImage').append('<p class="log">'+json.ok+'</p>')[0].reset();
				var profile = $('#myWonderProfile');
				profile.attr("src","../../IMAGES/account/allpeoples/user"+profile.data("id")+".jpg");
			}
			//$('#postImage')[0].reset();
		}
	});
	
	$('#changePass').submit(function(e){
		e.preventDefault();
		var result = $(this);
		//protezione pass
		var hashpass1 = $('<input type="hidden" id="hashed1" name="p1" value="'+hex_sha512($('#vp').val())+'">');
		var hashpass2 = $('<input type="hidden" id="hashed2" name="p2" value="'+hex_sha512($('#np').val())+'">');
		result.append(hashpass1).append(hashpass2);
		$('#vp, #np').val('');
		//invio form
		$.ajax({
			url:'switchp.php',
			datatype:'json',
			type:'POST',
			data:$(this).serialize(),
		})
		.done(function(json){
			console.log(json);
			if (typeof json.error !== 'undefined'){
				result.append('<p class="log">'+json.error+'</p>');
			}
			else{
				result.append('<p class="log">'+json.ok+'</p>')[0].reset();
			}
		})
		.fail(function(res){
			console.log(res);
		})
		.always(function(){hashpass1.remove(); hashpass2.remove();});
	});

	
	$('#postGallery').submit(function(e){
		e.preventDefault();
		var result = $(this);
		//imposto descrizione html
		$('#wmd-input0').val($('#wmd-preview0').html());
		//invio form
		$.ajax({
			url:'myAlbum.php',
			datatype:'json',
			data:$(this).serialize(),
		})
		.done(function(json){
			console.log(json);
			if (typeof json.error !== 'undefined'){
				result.append('<p class="log">'+json.error+'</p>');
			}
			else{
				var code = (typeof json.code !== 'undefined' ?
					"<p class='privates'><b>link al post privato:</b><br>http://ausl.altervista.org/PAGES/ACCOUNTS/account.php?id="+json.user+"&code="+json.code+"</i></p>" : '')
				$('#profileEdit').after('<table class="fumetto green"><tr><th><div></div></th><td>'+
				code+
				'<h3>'+$('#albumTitle').val().replace(/</g,"&lt;").replace(/>/g,"&gt;")+'</h3>'+
				$('#wmd-preview0').html()+
				'</td></tr></table>');
				$('#wmd-preview0').html("");
				result.append('<p class="log">Link inserito con successo</p>')[0].reset();
				$('#albumIframe').slideUp(400).attr("src","");
			}
		})
		.fail(function(res){
			console.log(res);
		});
	});

	$('#prefer').submit(function(e){
		e.preventDefault();
		var result = $(this);
		//invio form
		$.ajax({
			url:'preference.php',
			datatype:'json',
			type:'GET',
			data:$(this).serialize(),
		})
		.done(function(json){
			console.log(json);
			if (typeof json.error !== 'undefined'){
				result.append('<p class="log">'+json.error+'</p>');
			}
			else{
				result.append('<p class="log">'+json.ok+'</p>');
			}
		})
		.fail(function(res){
			console.log(res);
		});
	});
	
	$('textarea.wmd-input').iniMarkdown();
	
	$('#rightComment form.join').joinForm();
	
	$('.joincomment .chi').authorCommentToLink(2);
	
	$('.deletePost').delMyPost();
});
