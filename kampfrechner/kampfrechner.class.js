/*
	Implemented classes
*/

// Einheit

Einheit.prototype.__id = 0;
Einheit.prototype.__reg = new Array();
Einheit.prototype.__defaultAngriffsWert = 0.5;
Einheit.prototype.__globalKampfReihenfolge = new Array();
Einheit.prototype.isMonk = false;
Einheit.prototype.monkBonus = 0.005;
Einheit.prototype.monkBonusMax = 0.5;
Einheit.prototype.monkMalus = 0.003;
Einheit.prototype.monkMalusMax = 0.3;

Einheit.prototype.getName = function (id) {
	if(id instanceof Einheit)
		return id.name;
	return Einheit.prototype.getEinheit(id).name;
}
Einheit.prototype.getEinheit = function (id) {
	if(id instanceof Einheit)
		return id;
	return Einheit.prototype.__reg[id];
}
Einheit.prototype.setGlobalKampfReihenfolge = function (arr) {
	Einheit.prototype.__globalKampfReihenfolge = arr;
}
Einheit.prototype.getGlobalKampfReihenfolge = function () {
	if(Einheit.prototype.__globalKampfReihenfolge.length == Einheit.prototype.__reg.length)
		return Einheit.prototype.__globalKampfReihenfolge;
	else
		return Einheit.prototype.__reg;
}

function Einheit(name, trefferPunkte, lebensPunkte) {
	// unique id
	this.id = Einheit.prototype.__id++;
	Einheit.prototype.__reg[this.id] = this;
	
	// specific value
	this.name = name;
	this.lebensPunkte = lebensPunkte;
	this.trefferPunkte = trefferPunkte;
	
	// init
	this.reihenfolge = new Array();
	this.angriffsWerte = new Array();
}

Einheit.prototype.addToReihenfolge = function (einheit) {
	this.reihenfolge.push(einheit);
	return this;
}
Einheit.prototype.getReihenfolgePosition = function (einheit) {
	var id = getEinheit(einheit);
	var pos = 1;
	for(var i in this.reihenfolge) {
		if(i == id)
			return pos
		pos++;
	}
	return -1;
}

Einheit.prototype.addToAngriffswert = function (einheit, value) {
	this.angriffsWerte[getEinheit(einheit).id] = value;
	return this;
}
Einheit.prototype.getAngriffswert = function (einheit) {
	var ret = this.angriffsWerte[getEinheit(einheit).id];
	if(ret)
		return ret;
	else
		return this.__defaultAngriffsWert;
}
Einheit.prototype.toString = function() {
	return this.name +"(id: "+ this.id +")";
}

Einheit.prototype.toHTML = function () {
	s = this.name +" (TP: "+ this.trefferPunkte + ", LP: " + this.lebensPunkte +")<br />\nReihenfolge: ";
	for(var i in this.reihenfolge) {
		s += this.reihenfolge[i].name;
		if(i < this.reihenfolge.length - 1)
			s += ", ";
	}
	s += "<br />Angriffswerte: ";
	for(var i in this.angriffsWerte) {
		s += "("+ this.getName(i) +" = "+ (this.angriffsWerte[i] * 100) +") ";
	}
	return s;
}

Einheit.prototype.setMonk = function() {
	this.isMonk = true;
}

// Armee

Armee.prototype.monkBonus = 0;
Armee.prototype.monkMalus = 0;
Armee.prototype.__initeinheiten = function() {
	for(e in getEinheiten()) {
		this.einheiten[e] = 0;
	}
}
function Armee(moral) {
	this.moral = moral;
	this.einheiten = new Array();
	this.__initeinheiten();
}

Armee.prototype.addEinheiten = function (einheit, anzahl) {
	var add = anzahl * einheit.lebensPunkte
	this.einheiten[einheit.id] = (this.einheiten[einheit.id]) ? this.einheiten[einheit.id] + add : add;
	if(einheit.isMonk) {
		this.monkBonus = Math.min(this.monkBonus + anzahl * einheit.monkBonus, einheit.monkBonusMax);
		this.monkMalus = Math.min(this.monkMalus + anzahl * einheit.monkMalus, einheit.monkMalusMax);
	}
	return this;
}
Armee.prototype.getEinheitenAnzahl = function (einheit) {
	var e = getEinheit(einheit);
	if(this.einheiten[e.id])
		return Math.ceil(this.einheiten[e.id] / e.lebensPunkte);
	else
		return 0;
}

Armee.prototype.kaempfen = function (feind, follow) {
	var kaempfen = function (aA, e, aB) {
		var einheitA = getEinheit(e);
		var anzahlA = aA.getEinheitenAnzahl(e);
		if(anzahlA > 0)
			anzahlA = anzahlA - (anzahlA - aA.einheiten[e]/getEinheit(e).lebensPunkte);
		if(anzahlA == 0)
			return;
		var gesAngriffsPunkte = anzahlA * einheitA.trefferPunkte;
		for(var r in einheitA.reihenfolge) {
			r = einheitA.reihenfolge[r].id;
			if(aB.getEinheitenAnzahl(r) > 0) {
				var einheitB = Einheit.prototype.getEinheit(r);
				var anzahlB = aB.getEinheitenAnzahl(r);
				var gesLebensPunkte = aB.einheiten[r]; // anzahlB * einheitB.lebensPunkte;
				if(anzahlB <= 0) continue;
				if(gesAngriffsPunkte * einheitA.getAngriffswert(r) * aA.moral > gesLebensPunkte) {
					gesAngriffsPunkte *= 1 - (gesLebensPunkte / (gesAngriffsPunkte * einheitA.getAngriffswert(r)) * aA.moral);
					aB.einheiten[r] = 0;
				}
				else {
					aB.einheiten[r] = gesLebensPunkte - (gesAngriffsPunkte * einheitA.getAngriffswert(r) * aA.moral);
					gesAngriffsPunkte = 0;
				}
			
			}
			if(gesAngriffsPunkte <= 0) {
				break;
			}
		}		
	}
	var kR = getGlobalKampfReihenfolge();
	var copyA = clone(this), copyB = clone(feind);
	for(var e in kR) {
		var id = kR[e].id;
		kaempfen(this, id, copyB);
		kaempfen(feind, id, copyA);
	}
	clone(copyA, this);
	clone(copyB, feind);

}

Armee.prototype.vernichtet = function () {
	var sum = 0
	for(e in this.einheiten)		
		sum += this.einheiten[e]
	return sum == 0;
}
Armee.prototype.count = function () {
	var sum = 0
	for(e in this.einheiten)		
		sum += this.getEinheitenAnzahl(e);
	return sum;
}
Armee.prototype.concat = function (a) {
	for(var e in getEinheiten()) {
		this.einheiten[e] += a.einheiten[e];		
	}
	this.monkBonus = Math.min(this.monkBonus + a.monkBonus, Einheit.prototype.monkBonusMax);
	this.monkMalus = Math.min(this.monkMalus + a.monkMalus, Einheit.prototype.monkMalusMax);
}
Armee.prototype.concatArmeen = function(armeeArr) {
	var ret = new Armee(0);
	var sum = 0;
	for(var i in armeeArr)
		sum += armeeArr[i].count();
	for(var i in armeeArr) {
		ret.concat(armeeArr[i]);
		ret.moral += armeeArr[i].moral * (armeeArr[i].count() / sum);
	}
	return ret;
}

// Kampf mit Angreifer und Verdeitiger mit mehreren Armeen
Kampf.prototype.angMonkBonus = new Array();
Kampf.prototype.angMonkMalus = new Array();
Kampf.prototype.deffMonkBonus = new Array();
Kampf.prototype.deffMonkMalus = new Array();
Kampf.prototype.festungsBonus = 0;
Kampf.prototype.lastAddedEinheiten = 0;

Kampf.prototype.__festungsBonus = 0.15;
Kampf.prototype.__festungsBonusMax = 0.3;
Kampf.prototype.__ticks = null;
function Kampf() {
	this.ang = new Array();
	this.deff = new Array();
}
Kampf.prototype.addAng = function (tick, armee) {
	if(typeof tick != 'number' || tick < 0) return;
	if(tick > this.lastAddedEinheiten) this.lastAddedEinheiten = tick;
	if(!(this.ang[tick] instanceof Array)) { 
		this.ang[tick] = new Array();
		this.angMonkBonus[tick] = 0;
		this.angMonkMalus[tick] = 0; 
	}
	this.ang[tick].push(armee);
	this.angMonkBonus[tick] = Math.min(this.angMonkBonus[tick] + armee.monkBonus, getMaxMonkBonus());
	this.angMonkMalus[tick] = Math.min(this.angMonkMalus[tick] + armee.monkMalus, getMaxMonkMalus());
}
Kampf.prototype.addDeff = function (tick, armee) {
	if(typeof tick != 'number' || tick < 0) return;
	if(tick > this.lastAddedEinheiten) this.lastAddedEinheiten = tick;
	if(!(this.deff[tick] instanceof Array)) { 
		this.deff[tick] = new Array();
		this.deffMonkBonus[tick] = 0;
		this.deffMonkMalus[tick] = 0; 
	}
	this.deff[tick].push(armee);
	this.deffMonkBonus[tick] = Math.min(this.deffMonkBonus[tick] + armee.monkBonus, getMaxMonkBonus());
	this.deffMonkMalus[tick] = Math.min(this.deffMonkMalus[tick] + armee.monkMalus, getMaxMonkMalus());
}
Kampf.prototype.addFestung = function (anzahl) {
	this.festungsBonus = Math.min(this.festungsBonus + anzahl * this.__festungsBonus, this.__festungsBonusMax);
}

Kampf.prototype.fromJSON = function(s) {
	var jsonObj = $.parseJSON(s);
	for (var i in jsonObj) {
		if(i == "ang" || i == "deff") {
			this[i] = [];
			for(var n in jsonObj[i]) {
				this[i][n] = [];
				for(var m in jsonObj[i][n])
					this[i][n].push(clone(jsonObj[i][n][m], new Armee(1)));
			}
		}
		else if(jsonObj[i] && typeof jsonObj[i] == "object")
			this[i] = clone(jsonObj[i]);
		else
			this[i] = jsonObj[i];
	}
	
}
Kampf.prototype.ticks = function () {
	if(this.__ticks == null)
		this.__ticks = new Array();
	var funcTick = 0; var retTicks = this.__ticks;
	var retFunc = function() {
		return retTicks[funcTick++];
	}
	if(this.__ticks.length > 0)
		return retFunc;

	var kaempfen = function (aA, e, m, aB) {
		var einheitA = getEinheit(e);
		var anzahlA = aA.getEinheitenAnzahl(e);
		if(anzahlA > 0)
			anzahlA = anzahlA - (anzahlA - aA.einheiten[e]/getEinheit(e).lebensPunkte);
		if(anzahlA == 0)
			return;
		var gesAngriffsPunkte = anzahlA * einheitA.trefferPunkte * m;
		for(var r in einheitA.reihenfolge) {
			r = einheitA.reihenfolge[r].id;
			if(aB.getEinheitenAnzahl(r) > 0) {
				var einheitB = Einheit.prototype.getEinheit(r);
				var anzahlB = aB.getEinheitenAnzahl(r);
				var gesLebensPunkte = aB.einheiten[r];
				var angriffsWertGegen = gesAngriffsPunkte * einheitA.getAngriffswert(r) * aA.moral;
				if(gesAngriffsPunkte * einheitA.getAngriffswert(r) * aA.moral > gesLebensPunkte) {
					// FIX ME
					gesAngriffsPunkte *= 1 - (gesLebensPunkte / angriffsWertGegen);
					aB.einheiten[r] = 0;
				}
				else {
					aB.einheiten[r] = gesLebensPunkte - angriffsWertGegen;
					gesAngriffsPunkte = 0;
				}
			
			}
			if(gesAngriffsPunkte <= 0) {
				break;
			}
		}		
	}
	var verlusteAbziehen = function (armeeArr, vorher, nachher) {
		var verluste = new Array();
		for(var e in getEinheiten()) {
			if(vorher.einheiten[e] > 0) {
				// verluste Berechnen
				var lost = vorher.einheiten[e] - nachher.einheiten[e];
				verluste[e] = vorher.getEinheitenAnzahl(e) - nachher.getEinheitenAnzahl(e);
				// anteilig abziehen
				for(var i in armeeArr) {
					armeeArr[i].einheiten[e] = Math.floor(armeeArr[i].einheiten[e] - ((armeeArr[i].einheiten[e]/vorher.einheiten[e]) * lost));
				}
			}
			else {
				verluste[e] = 0;
			}
		}
		return verluste;
	}
	
	var kampfTick = 0;
	var angArr = [];
	var deffArr = [];
	var angTemp = new Armee(0);
	var deffTemp = new Armee(0);
	var kR = getGlobalKampfReihenfolge();
	do {
		if(this.ang[kampfTick])
			angArr = angArr.concat(this.ang[kampfTick]);
		if(this.deff[kampfTick])
			deffArr = deffArr.concat(this.deff[kampfTick]);
		
		angTemp = Armee.prototype.concatArmeen(angArr);
		deffTemp = Armee.prototype.concatArmeen(deffArr);
		if(kampfTick > 0) {
			var angCopy = clone(angTemp), deffCopy = clone(deffTemp);
			var copyA = clone(angTemp), copyB = clone(deffTemp);
			for(var e in kR) {
				var id = kR[e].id;
				kaempfen(angTemp, id, (1 + angTemp.monkBonus) * (1 - deffTemp.monkMalus) * (1 - this.festungsBonus) * GLOBAL_MALUS, copyB);
				kaempfen(deffTemp, id, (1 + deffTemp.monkBonus) * (1 - angTemp.monkMalus) * GLOBAL_MALUS, copyA);
			}
			clone(copyA, angTemp);
			clone(copyB, deffTemp);
			
			// Verluste zurückschreiben
			var angVerlust = verlusteAbziehen(angArr, angCopy, angTemp);
			var deffVerlust = verlusteAbziehen(deffArr, deffCopy, deffTemp);
		}
		this.__ticks[kampfTick] = {
			tick: kampfTick,
			ang: {gesamt: angTemp, verluste: angVerlust},
			deff: {gesamt: deffTemp, verluste: deffVerlust}
		};
		kampfTick++;
	} while((!angTemp.vernichtet() && !deffTemp.vernichtet()) || kampfTick <= this.lastAddedEinheiten)
	
	return retFunc;
}
Kampf.prototype.getGesamtAng = function() {return Armee.prototype.concatArmeen(this.ang) };
Kampf.prototype.getGesamtDeff = function() {return Armee.prototype.concatArmeen(this.deff) };

// global functions
getEinhietName = Einheit.prototype.getName;
getEinheit = Einheit.prototype.getEinheit;
function getEinheiten() {
	return Einheit.prototype.__reg;
}
setGlobalKampfReihenfolge = Einheit.prototype.setGlobalKampfReihenfolge;
getGlobalKampfReihenfolge = Einheit.prototype.getGlobalKampfReihenfolge;
function getMaxMonkBonus() { return Einheit.prototype.monkBonusMax }
function getMaxMonkMalus() { return Einheit.prototype.monkMalusMax }
Number.prototype.myToString = function() {
	var temp = Math.floor(this);
	var devide = function() {
		var zahl = temp % 1000;
		var s1 = ".";
		for(var i = 2; i > 0; i--) {
			if((zahl / Math.pow(10,i)) < 1) 
				s1 += "0";
		}
		temp = Math.floor(temp / 1000);
		return s1 + zahl;
	}
	var s = "";
	while((temp / 1000) >= 1)
		s = devide() + s;
	return temp + s;
}
function myParseInt(s) {
	var zahl = 0;
	var sa = s.split(".");
	if(sa.length > 1)
		for(var i = sa.length - 1; i >= 0; i--)
			zahl += parseInt(sa[i], 10) * Math.pow(1000,sa.length-i-1);
	else
		zahl = parseInt(s);
	return zahl;
		
}
/* ?nsende=true
	init objects
*/