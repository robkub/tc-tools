// Konstanten
GLOBAL_MALUS = 1; // DIRTY Da der Rechner immer zu viel ausrechnet, einfach alles abschwächen

/* **********************************************************************************************
 * 					Bogenschütze	Axtwerfer	Speerkämpfer	Schwertkämpfer	Ritter	Weiße Wölfe	Milizen
 * Angriffspunkte	30				30			30				30				56		56			
 * Hitpoints		100				150			100				150				200		250			
 * Bogenschütze		50,00%			100,00%		25,00%			50,00%			100,00%	25,00%		
 * Axtwerfer		50,00%			100,00%		25,00%			50,00%			100,00%	25,00%		
 * Speerkämpfer		100,00%			25,00%		50,00%			100,00%			25,00%	50,00%		
 * Schwertkämpfer	100,00%			25,00%		50,00%			100,00%			25,00%	50,00%		
 * Ritter			25,00%			50,00%		100,00%			25,00%			50,00%	100,00%		
 * Weiße Wölfe		25,00%			50,00%		100,00%			25,00%			50,00%	100,00%		
 * Milizen
********************************************************************************************** */

// einheiten typen
var ab = new Einheit("Arbeiter", 2, 10); // FIX ME
var bs = new Einheit("Bogenschützen", 30, 100);
var aw = new Einheit("Axtwerfer", 30, 150);
var sp = new Einheit("Speerkämpfer", 30, 100);
var sk = new Einheit("Schwertkämpfer", 30, 150);
var rr = new Einheit("Ritter", 56, 200);
var ww = new Einheit("Weiße Wölfe", 56, 250);

// müsste nochmal geprüft werden
var mi = new Einheit("Milizen", 30, 125); // FIX ME
var mo = new Einheit("Mönche", 10, 100);
mo.setMonk();

// Kampfreihenfolge
setGlobalKampfReihenfolge([ww,rr,mi,aw,sk,bs,sp,mo,ab]);

// Angriffswerte
bs.addToAngriffswert(bs, 0.5).addToAngriffswert(aw, 0.5).addToAngriffswert(sp, 0.9).addToAngriffswert(sk, 0.9).addToAngriffswert(rr, 0.25).addToAngriffswert(ww, 0.25).addToAngriffswert(mi, 0.5);
aw.addToAngriffswert(bs, 0.9).addToAngriffswert(aw, 0.9).addToAngriffswert(sp, 0.25).addToAngriffswert(sk, 0.25).addToAngriffswert(rr, 0.5).addToAngriffswert(ww, 0.5).addToAngriffswert(mi, 0.5);
sp.addToAngriffswert(bs, 0.25).addToAngriffswert(aw, 0.25).addToAngriffswert(sp, 0.5).addToAngriffswert(sk, 0.5).addToAngriffswert(rr, 0.9).addToAngriffswert(ww, 0.9).addToAngriffswert(mi, 0.5);
sk.addToAngriffswert(bs, 0.5).addToAngriffswert(aw, 0.5).addToAngriffswert(sp, 0.9).addToAngriffswert(sk, 0.9).addToAngriffswert(rr, 0.25).addToAngriffswert(ww, 0.25).addToAngriffswert(mi, 0.5);
rr.addToAngriffswert(bs, 0.9).addToAngriffswert(aw, 0.9).addToAngriffswert(sp, 0.25).addToAngriffswert(sk, 0.25).addToAngriffswert(rr, 0.5).addToAngriffswert(ww, 0.5).addToAngriffswert(mi, 0.5);
ww.addToAngriffswert(bs, 0.25).addToAngriffswert(aw, 0.25).addToAngriffswert(sp, 0.5).addToAngriffswert(sk, 0.5).addToAngriffswert(rr, 0.9).addToAngriffswert(ww, 0.9).addToAngriffswert(mi, 0.5);
mi.addToAngriffswert(bs, 0.25).addToAngriffswert(aw, 0.25).addToAngriffswert(sp, 0.5).addToAngriffswert(sk, 0.5).addToAngriffswert(rr, 0.9).addToAngriffswert(ww, 0.9).addToAngriffswert(mi, 0.5);
mo.addToAngriffswert(bs, 0.9).addToAngriffswert(aw, 0.9).addToAngriffswert(sp, 0.9).addToAngriffswert(sk, 0.9).addToAngriffswert(rr, 0.9).addToAngriffswert(ww, 0.9).addToAngriffswert(mi, 0.5);
ab.addToAngriffswert(bs, 0.9).addToAngriffswert(aw, 0.9).addToAngriffswert(sp, 0.5).addToAngriffswert(sk, 0.5).addToAngriffswert(rr, 0.25).addToAngriffswert(ww, 0.25).addToAngriffswert(mi, 0.5);

// Reihenfolge
bs.addToReihenfolge(sp).addToReihenfolge(sk).addToReihenfolge(bs).addToReihenfolge(aw).addToReihenfolge(mi);
bs.addToReihenfolge(rr).addToReihenfolge(ww).addToReihenfolge(mo).addToReihenfolge(ab);
aw.addToReihenfolge(aw).addToReihenfolge(bs).addToReihenfolge(rr).addToReihenfolge(ww).addToReihenfolge(mi);
aw.addToReihenfolge(sp).addToReihenfolge(sk).addToReihenfolge(mo).addToReihenfolge(ab);
sp.addToReihenfolge(rr).addToReihenfolge(ww).addToReihenfolge(sp).addToReihenfolge(sk).addToReihenfolge(mi);
sp.addToReihenfolge(bs).addToReihenfolge(aw).addToReihenfolge(mo).addToReihenfolge(ab);
sk.addToReihenfolge(sk).addToReihenfolge(sp).addToReihenfolge(bs).addToReihenfolge(aw).addToReihenfolge(mi);
sk.addToReihenfolge(rr).addToReihenfolge(ww).addToReihenfolge(mo).addToReihenfolge(ab);
rr.addToReihenfolge(bs).addToReihenfolge(aw).addToReihenfolge(rr).addToReihenfolge(mi).addToReihenfolge(sk);
rr.addToReihenfolge(ww).addToReihenfolge(sp).addToReihenfolge(mo).addToReihenfolge(ab);
ww.addToReihenfolge(ww).addToReihenfolge(rr).addToReihenfolge(sp).addToReihenfolge(sk).addToReihenfolge(mi);
ww.addToReihenfolge(bs).addToReihenfolge(aw).addToReihenfolge(mo).addToReihenfolge(ab);
mi.addToReihenfolge(rr).addToReihenfolge(ww).addToReihenfolge(sp).addToReihenfolge(sk).addToReihenfolge(mi);
mi.addToReihenfolge(bs).addToReihenfolge(aw).addToReihenfolge(mo).addToReihenfolge(ab);
mo.addToReihenfolge(bs).addToReihenfolge(aw).addToReihenfolge(sk).addToReihenfolge(sp).addToReihenfolge(mi);
mo.addToReihenfolge(rr).addToReihenfolge(ww).addToReihenfolge(mo).addToReihenfolge(ab);
ab.addToReihenfolge(bs).addToReihenfolge(aw).addToReihenfolge(sp).addToReihenfolge(sk).addToReihenfolge(mi);
ab.addToReihenfolge(rr).addToReihenfolge(ww).addToReihenfolge(mo).addToReihenfolge(ab);
