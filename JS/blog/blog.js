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

var createdEvents = new Array();
function creaEvento(json,evento){
	titolo = $('#evTitle').val();
	$('#inserimento').after(getPostHTML(
			json.id,
			$('#evTipo').val(),
			json.i,
			json.n,
			json.d,
			titolo,
			$('#evData').val(),
			json.c
		)
	).removeClass("active");
	$('#newposted'+json.id).joinForm();
	$('#newposted'+json.id+" .loader").checkForComment();
	$('#jf'+json.id).toggleComments();
	evento[0].reset();
	createdEvents.push(json.id);
	showLog('Hai creato il post "'+titolo+'"');
}

function getPostHTML(id,tipo,idAutore,nome,data,titolo,dataEvento,desc){
	switch(tipo){
		case 1: case '1': tipo='green'; titolo="EVENTO: "+titolo; break;
		case 2: case '2': tipo='blue'; titolo="PROPOSTA: "+titolo; break;
		case 3: case '3': tipo='red'; titolo="DISCUSSIONE: "+titolo; break;
		default: tipo='white';
	}
	return '<table class="fumetto '+tipo+'" id="post'+id+'">'+
	'<tr>'+
	'<th><div><p class="user'+idAutore+'">'+nome+'<br>'+data+'</p></div></th>'+
		'<td>'+
		'<h3>'+titolo+'</h3>'+
		'<h4>Data prevista: '+dataEvento+'</h4>'+
		'<div>'+desc+'</div>'+
		'<h4>Join or Comment!</h4>'+
		'<form class="join" id="newposted'+id+'" method="get" action="blogComment.php">'+
			'<p>inserisci qui un tuo commento o la tua sottoscrizione all\'evento:</p>'+
			'<input type="hidden" name="id" value="'+id+'">'+
			'<input type="hidden" name="b" value="'+blogId+'">'+//blogId = global!!!
			'<textarea name="c"></textarea>'+
			'<input type="submit" value="COMMENTA!"><i class="loader" data-id="'+id+'"></i>'+
		'</form>'+
		'<div class="joincomment" id="jc'+id+'"></div>'+
		'<div class="joinflag" id="jf'+id+'"></div>'+
		'</td>'+
	'</tr>'+
	'</table>';
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
			url:'blogComment.php',
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

var blogId;
var lastId;

function controllaPost(){
	var inputs = $('#rightComment table.fumetto:eq(1) form.join input');
	//get type of blog
	blogId = inputs.eq(1).val();
	//get last id of blog
	lastId = inputs.eq(0).val();
	
	function nextControl(){
		setTimeout(function(){
			$.ajax({
				url:'blogPostUpdates.php',
				datatype:'json',
				type:'GET',
				data:{b:blogId,i:lastId},
			})
			.done(function(json){
				if (typeof json.error !== 'undefined'){
					showLog(json.error);
					return false;
				}
				else{
					//insert new post if there is one
					if (typeof json.post !== 'undefined'){
						var count = 0;
						$.each(json.post,function(id,p){
							if (createdEvents.indexOf(Number(id))===-1){
								$('#inserimento').after(getPostHTML(
										id,
										p.t,
										p.a,
										json.auth[p.a],
										p.d,
										p.ti,
										p.de,
										p.c
									)
								);
								lastId = id; count++;
								$('#newposted'+id).joinForm();
							}
						});
						if (count>0){
							console.log(count+" nuovi post aggiungi alla pagina!");
							showLog(count+" nuovi post aggiungi alla pagina!");
						}
					}
					//else console.log("Nessun nuovo post!");
				}
				nextControl();
			})
			.fail(function(res){
				console.log(res);
			});
		},
		420000 //7 minuti
		);
	}
	
	//start check for updates
	nextControl();
}

var requestComments="";

function checkNewComments(){
	//start check for updates
	if (requestComments==="") nextControl();
	else return false;
	
	console.log("Ho cominciato ad effettuare il check dei commenti!");
	showLog("Ho cominciato ad effettuare il check dei commenti!");
	
	function nextControl(){
		setTimeout(function(){
			if (requestComments!==""){
				$.ajax({
					url:'blogCommentUpdates.php',
					datatype:'json',
					type:'GET',
					data:requestComments,
				})
				.done(function(json){
					console.log(json);
					if (typeof json.error !== 'undefined'){
						showLog(json.error);
						return false;
					}
					//insert new post if there is one
					if (typeof json.comm !== 'undefined'){
						$.each(json.comm,function(id,com){
							//replace id last comment
							var commentid = $('#jc'+com.p+' div.com:first').data("id");
							if (commentid === null) commentid=0;
							requestComments = requestComments.replace('&p['+com.p+']='+commentid,'&p['+com.p+']='+id);
							//add last comment
							$("#jc"+com.p).prepend('<p class="chi" data-id="'+com.a+'">'+json.auth[com.a]+' - '+com.d+'</p>'+
								'<div class="com" data-id="'+id+'">'+com.c+'</div>');
						});
						console.log("Nuovi commenti aggiunti!");
						showLog("Nuovi commenti aggiunti!");
					}
					else console.log("Nessun commento da aggiornare!");
					//control: if required comments continue check
					nextControl();
				})
				.fail(function(res){
					console.log(res);
				});
			}
			else{
				console.log("Ho smesso di controllare i commenti.");
				showLog("Ho smesso di controllare i commenti.");
			}
		},
		30000 //30 secondi
		);
	}
}

$.fn.checkForComment = function(){
	return this.each(function(){
		var postid = $(this).data("id");
		var commentid;
		$(this).click(function(){
			if ($(this).toggleClass("active").hasClass("active")){
				//start check for updates in comments
				checkNewComments();
				//add post to search
				commentid = $('#jc'+postid+' div.com:first').data("id");
				if (commentid === null) commentid=0;
				requestComments += '&p['+postid+']='+commentid;//post['id']='lastcomment'
			}
			else{
				//stop check for updates in comments
				requestComments = requestComments.replace('&p['+postid+']='+commentid,"");
			}
		});
	});
}

$.fn.toggleComments = function(){
	return this.each(function(){
		$(this).html("<i class='o'>apri</i><i class='c'>chiudi</i> <i>i commenti</i>");
		$(this).click(function(){
			$(this).toggleClass("opened")
			$('#'+$(this)[0].id.replace("f","c")).toggleClass('maxh');
		});
	});
}


$.fn.iniMarkdown = function(){
	return this.each(function(){
		var converter = Markdown.getSanitizingConverter();
		var editor = new Markdown.Editor(converter,this.id.replace('wmd-input',''));
		editor.run();
	});
}



function setDatePickerHeader(elem){
	elem.DatePicker({
		format:'d/m/Y',
		date: elem.val(),
		current: elem.val(),
		position: 'bottom',
		onBeforeShow: function(){
			if (elem.val()!='') elem.DatePickerSetDate(elem.val(), true);
		},
		onChange: function(formated, dates){
			console.log(elem);
			elem.val(formated);
			elem.DatePickerHide();
		}
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

$.fn.authorExpose = function(){
	return this.each(function(){
		$(this).click(function(){
			$(this).toggleClass("exposed");
		});
	});
}

$(function(){
	$('#rightComment .compressed').removeClass("compressed");
	
	$('#newpost').click(function(){
		$('#inserimento').toggleClass('active');
	});
	
	$('.joinflag').toggleComments();
	
	$('i.loader').checkForComment();
	
	$('#rightComment form.join').joinForm();
	
	setDatePickerHeader($("#eventDatePick"));
	
	$('#evento').submit(function(e){
		e.preventDefault();
		//stop double submit
		$('#inviaSubmit').addClass("wait");
		$('#inviaWait').addClass("active");
		//substitute textarea content with preview content
		var textarea = $('#wmd-input0');
		var originaltext = textarea.val();
		textarea.val($('#wmd-preview0').html());
		var evento = $(this);
		$.ajax({
			url:'blogPost.php',
			datatype:'json',
			type:'POST',
			data:$(this).serialize(),
		})
		.done(function(json){
			console.log(json);
			if (typeof json.error !== 'undefined'){
				textarea.val(originaltext);
				alert(json.error);
				return false;
			}
			creaEvento(json,evento);
			textarea.val('');
			$('#wmd-preview0').html('');
		})
		.fail(function(res){
			console.log(res);
			textarea.val(originaltext);
		})
		.always(function(){
			$('#inviaSubmit').removeClass("wait");
			$('#inviaWait').removeClass("active");
		});
	});
	
	controllaPost();
	
	$('textarea.wmd-input').iniMarkdown();
	
	$('.joincomment .chi').authorCommentToLink(2);
	$('.users').authorCommentToLink(2).authorExpose();
});
