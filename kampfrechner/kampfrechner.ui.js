// UI-Element

function kampftabelle(target, kampf) {
	var href = window.location.protocol +"//"+ window.location.host + window.location.pathname +"?kampf="+ encodeURIComponent($.toJSON(kampf));
	var next = kampf.ticks();
	
	target.append('<table class="kampftabelle">'
		+'<colgroup width="'+ (100 / (getEinheiten().length*2+1)) +'%" span="'+ (getEinheiten().length*2+1) +'"></colgroup>'
		+'<thead><tr><th rowspan="2" colspan="2">Tick</th></tr><tr></tr></thead><tfoot></tfoot><tbody></tbody></table>');
	var th = target.find('thead');
	for(var e in getEinheiten()) {
		th.children('tr:first').append('<th colspan="2">'+ getEinheit(e).name +'</th>');
		th.children('tr:last').append('<th class="angreifer">A</th><th class="verteidiger">V</th>');
	}
	
	var tick = 0;
	var tb = target.find('tbody');
	var now = next();
	while(now) {
		if(now.tick == 0)
			tb.append('<tr><th colspan="2">Start</th></tr>');
		else {
			tb.append('<tr><th rowspan="2">'+ now.tick +'</th><th>Verluste</th></tr><tr><th>Rest</th></tr>');
		}
		
		for(var e in getEinheiten()) {
			if(now.tick > 0)
				tb.children('tr:last').prev().append('<td class="angreifer zahl">'+ now.ang.verluste[e] +'</td>'
					+'<td class="verteidiger zahl">'+ now.deff.verluste[e] +'</td>');
			tb.children('tr:last').append('<td class="angreifer zahl">'+ now.ang.gesamt.getEinheitenAnzahl(e) +'</td>'
				+'<td class="verteidiger zahl">'+ now.deff.gesamt.getEinheitenAnzahl(e) +'</td>');
		}
		now = next();
	} // while(!kampf.getGesamtAng().vernichtet() && !kampf.getGesamtDeff().vernichtet());
	tb.children('tr:last').clone().appendTo(target.find('tfoot'));
	target.find('tfoot tr:last th').attr("colSpan","2");
	// tb.children('tr:last').remove();

	//~ $(target).find("tbody tr").hover(
		//~ function(){$(this).addClass("highlight")},
		//~ function(){$(this).removeClass("highlight")}
	//~ );
	target.find("tbody tr th[rowspan=2]").css("borderRight", "none").css("fontSize", "2em").css("text-align", "right");
	target.find("tbody tr:even").css("borderBottom", "0.2em solid rgba(64,64,64,0.9)");
	target.find("tbody tr").each(function(i, e) {
		var elem = (i == 0 ) ? $(e): (i % 2 == 0) ? $(e).add($(e).prev()) : $(e).add($(e).next());
		$(e).hover(
			function(){elem.addClass("highlight")},
			function(){elem.removeClass("highlight")}
		);
	});
	target.prepend('<p></p>');
	target.append('<fieldset>'
			+'<legend>Link</legend>'
			+'<input style="width:90.5%" type="text" id="link" value="'+ href +'" readonly="readonly" />'
			+'<input style="width:9%" type="button" id="short-it" value="Link kürzen" /><br />'
			+'<span><a class="inputlink" href="'+ href +'">Link</a>. Einfach markieren, kopieren und irgendwo einfügen.</span>'
		+'</fieldset>');
	target.find("#link").focus(function(){this.select()});
	target.find("#short-it").click(function(){
		shortLink(href, target.find("#link"), target.find("a.inputlink"));
		$(this).hide();
	});
}

function shortLink(href, target, aLink) {
	$.get('../shortener.php?longurl='+ href, null,
		function (data) {
			target.val(data);
			aLink.attr("href", data);
		}
	);
}

function createKampfForm(target, ticknumber, kampf) {
	var ang = new Armee(1);
	var deff = new Armee(1);
	if(kampf.ang[ticknumber])
		ang = kampf.ang[ticknumber][0];
	if(kampf.deff[ticknumber])
		deff =  kampf.deff[ticknumber][0];
	$(target).append('<form name="t'+ ticknumber +'"></form>');
	target = $(target).children("form");
	$(target).append('<table id="kampfform-'+ ticknumber +'" class="form kampfform"><thead></thead><tfoot></tfoot><tbody></tbody></table>');
	var table = $(target).children("table");
	$(table).children("thead").append("<tr><th>Einheiten</th><th>Angreifer</th><th>Verteidiger</th></tr>");
	$(table).children("tfoot").append('<tr><td colspan="3"><input type="submit" value="Berechnen"></td></tr>');
	for(var e in getEinheiten()) {
		var einheit = getEinheit(e);
		$(table).children("tbody").append('<tr>'
				+'<th>'+ einheit.name +'</th>'
				+'<td class="zahl"><input class="" name="t'+ ticknumber +'e'+ e +'A" value='+ ang.getEinheitenAnzahl(e) +' /></td>'
				+'<td class="zahl"><input name="t'+ ticknumber +'e'+ e +'B" value='+ deff.getEinheitenAnzahl(e) +' /></td>'
			+'</tr>');
	}
	$(table).children("tbody").append('<tr class="moral">'
				+'<th>Moral</th>'
				+'<td class="zahl"><input name="t'+ ticknumber +'moralA" value="'+ (ang.moral * 100) +'" />%</td>'
				+'<td class="zahl"><input name="t'+ ticknumber +'moralB" value="'+ (deff.moral * 100) +'" />%</td>'
			+'</tr>');
	$(table).children("tbody").append('<tr class="festung">'
				+'<th>Festung(en)</th>'
				+'<td class="zahl"></td>'
				+'<td class="zahl"><input name="t'+ ticknumber +'festungB" value="0" /></td>'
			+'</tr>');
			
	// default values for empty
	$(table).find("tbody tr").not(".moral").find("td input").focusin(function() {
			var wert = myParseInt($(this).val(), 10)
			if(wert <= 0 || wert.isNaN)
				$(this).val("");
			else
				$(this).val(wert);
		});
	$(table).find("tbody tr").not(".moral").find("td input").focusout(function() {
			if($(this).val() == "")
				$(this).val("0");
			var wert = parseInt($(this).val(), 10);
			wert = (wert <= 0) ? 0 : wert;
			$(this).val(wert.myToString());
		});
	$(table).find("tbody tr.moral td input").focusin(function() {
			if($(this).val() == "100")
				$(this).val("");
		});
	$(table).find("tbody tr.moral").find("td input").focusout(function() {
			if($(this).val() == "")
				$(this).val("100");
		});
	
	var submit = function() {
		var armeeA = new Armee(parseFloat($(table).find('input[name$=moralA]').val()) / 100);
		var armeeB = new Armee(parseFloat($(table).find('input[name$=moralB]').val()) / 100);
		
		for(var e in getEinheiten()) {
			armeeA.addEinheiten(getEinheit(e), myParseInt($(table).find('input[name$=e'+ e +'A]').val()));
			armeeB.addEinheiten(getEinheit(e), myParseInt($(table).find('input[name$=e'+ e +'B]').val()));
		}
		var kampf = new Kampf();
		kampf.addAng(ticknumber, armeeA); kampf.addDeff(ticknumber, armeeB)
		kampf.addFestung(myParseInt($(table).find('input[name$=festungB]').val()));
		$("#kampf").empty()
		kampftabelle($("#kampf"), kampf);
		return false;
	}
	//~ $(target).append('<span id="button">ersatz</span>');
	//~ $("#button").click(submit);
	$(target).submit(submit);
}

// UI - dynamisch
$( function() {
	
});