$(function(){
	$('#rightComment .compressed').removeClass("compressed");
	
	$('#iscriviti').submit(function(e){
		e.preventDefault();
		var result = $(this);
		//controllo uguaglianza pass:
		var p = $("#p1, #p2");
		if (p.eq(0).val() !== p.eq(1).val()){
			$('#resRegistra').prepend('<p class="log">Ooops! Le password non concidono...</p>');
			return false;
		}
		//protezione pass
		var hashpass = $('<input type="hidden" id="hashed" name="p" value="'+hex_sha512(p.eq(0).val())+'">');
		result.append(hashpass);
		p.eq(0).val('');
		p.eq(1).val('');
		//invio form
		$.ajax({
			url:'PHP/7Zip.php',
			datatype:'json',
			type:'POST',
			data:$(this).serialize(),
		})
		.done(function(json){
			if (typeof json.error !== 'undefined'){
				$('#resRegistra').prepend('<p class="log">'+json.error+'</p>');
			}
			else{
				$('#emaillog').val($('#emailreg').val());
				$('#resRegistra').prepend('<p class="log">'+json.ok+'</p>');
				result[0].reset();
			}
		})
		.fail(function(res){
			console.log(res);
		})
		.always(function(){hashpass.remove();});
	});
	
	$('#loggati').submit(function(e){
		e.preventDefault();
		var result = $(this);
		//protezione pass
		var hashpass = $('<input type="hidden" id="hashed2" name="p" value="'+hex_sha512($('#pl').val())+'">');
		result.append(hashpass);
		$('#pl').val('');
		this.submit();
		//invio form
		
		/*
		$.ajax({
			url:'PHP/gZipped.php',
			datatype:'json',
			type:'POST',
			data:$(this).serialize(),
		})
		.done(function(json){
			console.log(json);
			if (typeof json.error !== 'undefined'){
				$('#resLogin').prepend('<p class="log">'+json.error+'</p>');
			}
			else{
				$('#resLogin').prepend('<p class="log">'+json.ok+'</p>');
				result[0].reset();
				setTimeout(function(){
					$('#login').addClass("inactive");
					$('#logout').removeClass("inactive");
				},2000);
			}
		})
		.fail(function(res){
			console.log(res);
		})
		.always(function(){hashpass.remove();});
		*/
	});
	
	$('#sloggati').submit(function(e){
		e.preventDefault();
		$.ajax({
			url:'PHP/helloW.php',
			type:'GET',
		})
		.done(function(json){
			$('#logout').addClass("inactive");
			$('#login').removeClass("inactive");
			$('#leftComment h3.welcome').slideUp(200);
		})
		.fail(function(res){
			console.log(res);
		});
	});
	
	$('#lostpass').submit(function(e){
		e.preventDefault();
		$.ajax({
			dataType:'html',
			url:'PHP/lostpass.php',
			type:'GET',
			data:$(this).serialize()
		})
		.done(function(html){
			$('#resLostPass').prepend('<p class="log">'+html+'</p>');
		})
		.fail(function(res){
			console.log(res);
		});
	});
});
