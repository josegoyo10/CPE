
/***************************************************************************************
archivo: calendario.js
version: 3
creacion: 03052002:0834
modificacion: 17052002:1631

calendario(x,y,tag1,tag2)
***************************************************************************************/

//movement();
//variables globales, pueden ser accedidas en cualquier parte del documento
var fecha = new Date();
var dia = fecha.getDate();
var mes = fecha.getMonth();
var anho = fecha.getFullYear();
fecha.setMonth(fecha.getMonth());
fecha.setDate(1);
var inicio = fecha.getDay();
var tag1 = "";
var tag2 = "";
var x = "";
var y = "";

// variable con la ruta a la carpeta de imagenes
var path_img="../img/"

totalmes = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
totaldia = [31,28,31,30,31,30,31,31,30,31,30,31];
totalsemana = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"];


//function setx()
function setx(){
	return event.clientX;
}

//function sety() 
function sety(){
	return event.clientY;
}

//function calendario()
function calendario(a,b,id,field, display){
	var obj = document.all['PopUpCalendar'];
	x = a;
	y = b;
	obj.style.left = x;
	obj.style.top = y;
	obj.style.visibility = "visible";

	tag1 = id;
	tag2 = field;

	var content = generateContent(display);
	document.all["PopUpCalendar"].innerHTML = content;
}


//function generateContent()
function generateContent(display){
	var table = "";
	var largo = totaldia[mes];
	var temp = "";
	var header = "";
	var pos = 0;
	mouse = " onMouseOver=\"this.style.backgroundColor='#FFFF00'\" onMouseOut=\"this.style.backgroundColor=\'#FFFFFF\'\" align='right' ";
	var close = "<img width=\"12\" height=\"10\" alt=\"cerrar\" src=\""+path_img+"close.gif\" border=\"0\">";

	table += "<table border=\"1\" cellspacing=\"1\" cellpadding=\"1\"><tr><td bgcolor=\"#FFFFFF\" bordercolor=\"#FFFFFF\">";
	table += "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>"

	table += "<table width=100% border=0 cellpadding=0 cellspacing=0><tr>";
	table += "<td bgcolor=#FF9900 align=left valign=top style=\"font-family:tahoma;cursor:move;font-size:7pt;\" >&nbsp;</td>";
	table += "<td bgcolor=#FF9900 align=right valign=top><a href=\"#\" onclick=\"cerrar('"+tag1+"','"+tag2+"');\" >"+close+"</a></td>";
	table += "</tr></table>";

	table += "</td></tr>";
	table += "<tr><td>";
	table += "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
	table += hMonth(display);
	table += hDay();

	//verificando si es año ¿bisciesto?
	//if ( (anho%4==0) && (mes==1) && (anho%400!=0) )
	if ( (anho%4==0) && (mes==1) )
		largo = 29;

	var pos=0;
	for (var p=0; p<inicio; p++)
	{
		temp += "<td bgcolor=\"FFFFFF\" style=\"font-family:tahoma; font-size:7pt;\">&nbsp;</td>";
		pos++;
	}

	for (var day=1; day<=largo; day++){
		temp += "<td width=\"15\" bgcolor=\'#FFFFFF\' "+mouse+" style=\'font-family:tahoma; cursor:hand; font-size:7pt;\' onclick=\"seleccion("+pos+","+day+",mes,anho, "+display+")\">"+day+"</td>";
		pos++;
		p++;

		if (p % 7 == 0){
			table += "<tr>"+temp+"</tr>";
			pos=0;
			temp = "";
		}
	}

	if (pos!=0)
		for (var j=pos; j<7 ;j++ )
			temp += "<td bgcolor=\"#FFFFFF\" style=\"font-family:tahoma; font-size:7pt;\">&nbsp;</td>";

	table += "<tr>" + temp+"</tr></table>";
	table += "</td></tr></table></td></tr></table>";

	return table;
}



//function hMonth()
function hMonth(display)
{
	var mANT = mes-1;
	var mSIG = mes+1;
	var mACT = mes;

	var aANT = "";
	var aSIG = "";

	if (mANT == -1){
		mANT = 11;
		aANT = (anho-1);
	}

	if (mSIG == 12){
		mSIG = 0;
		aSIG = (anho+1);
	}

	var anterior = totalmes[mANT].toUpperCase().slice(0,3);
	var siguiente = totalmes[mSIG].toUpperCase().slice(0,3);
	var actual = totalmes[mACT].toUpperCase().slice(0,3);

	var tmp1 = "";

	var header1 = " colspan='7' align='center' style='font-family:verdana; font-size:7pt;' bgcolor='#DDDDDD' ";
	tmp1 += "<tr><td"+header1+">&nbsp;";

	if (mACT == 0)
		tmp1 += "<a href=\"#\" onclick=\"setcalendar("+mANT+","+aANT+","+display+");\" style=\"color:#666666; font-weight:bold;\">" +anterior+ "</a></font>";
	else
	tmp1 += "<a href=\"#\" onclick=\"setcalendar("+mANT+","+anho+","+display+");\" style=\"color:#666666; font-weight:bold;\">" +anterior+ "</a></font>";



	tmp1 += "<font style=\"color:#000000; font-weight:bold;\">" + "&nbsp;[" +actual+ "-"+anho+"]&nbsp;";


	if (mACT == 11)
		tmp1 += "<a href=\"#\" onclick=\"setcalendar("+mSIG+","+aSIG+","+display+");\" style=\"color:#666666; font-weight:bold;\">" +siguiente+ "</font>&nbsp;";
	else
		tmp1 += "<a href=\"#\" onclick=\"setcalendar("+mSIG+","+anho+","+display+");\" style=\"color:#666666; font-weight:bold;\">" +siguiente+ "</font>&nbsp;";


	tmp1 += "</td></tr>"
	return tmp1;
}


//function hDay()
function hDay()
{
		var tmp = "";
		var header2 = " align='right' style='font-family:verdana; font-size:7pt; color:#000000; font-weight:bold;' bgcolor='#FFFFFF' width='15'";
		tmp += "<tr>"
		tmp += "<td"+header2+">D</td>";
		tmp += "<td"+header2+">L</td>";
		tmp += "<td"+header2+">M</td>";
		tmp += "<td"+header2+">M</td>";
		tmp += "<td"+header2+">J</td>";
		tmp += "<td"+header2+">V</td>";
		tmp += "<td"+header2+">S</td>";
		tmp += "</tr>";
		return tmp;
}



//function seleccion()
function seleccion(p,d,m,a, display)
{
		var mm = "";
		var dd = "";
		var p1 = "";
		var p2 = "";

		if (d<10)
			dd = "0" + d;
		else
			dd = d;

		if ( (m+1)<10 )
			mm = "0" + (m+1);
		else
			mm = (m+1);

		if (display == 2)
			p1 = tag1+".innerHTML = '"+dd+"/"+mm+"/"+a+"';";
		else 
			p1 = tag1+".innerHTML = '"+totalsemana[p]+", "+dd+" de "+totalmes[m]+" de "+a+ "';";

		p2 = tag2+ ".value = '"+dd+"/"+mm+"/"+a+"';";

		eval(p1);
		eval(p2);

		var obj = document.all['PopUpCalendar'];
		obj.style.visibility = "hidden";
}



//function setcalendar()
function setcalendar(m,a, display)
{
		if (a != anho)
			anho = a;

		fecha.setFullYear(a);
		fecha.setMonth(m);
		fecha.setDate(1);

		mes = fecha.getMonth();
		anho = fecha.getYear();
		inicio = fecha.getDay();

		var obj = document.all['PopUpCalendar'];
		calendario(x,y,tag1,tag2, display);
}


//cierra ventanas de calendario en forma independiente
//function cerrar()
function cerrar(layer,value)
{
	var p1, p2 = "";
	p1 = layer + ".innerHTML = '"+layer+"'; "
	p2 = value + ".value = ''; "

	eval(p1);
	eval(p2);

	var obj = document.all['PopUpCalendar'];
	obj.style.visibility = "hidden";
}


//function edate(layer)
//extrae fecha completa a partir de una fecha estandar
function edate(layer,tag)
{
	var fecha = "";
	var content = "";
	var f = new Date();
	var anho = tag.slice(0,4);
	var mes = tag.slice(4,6);
	var dia = tag.slice(6);
	var dd = "";

	mes = mes*1;
	mes = mes-1;
	dia = dia*1;
	anho=anho*1;

	f.setFullYear(anho);
	f.setMonth(mes);
	f.setDate(dia);
	var posicion = f.getDay();

	if (dia<10)
		dd = "0"+dia;
	else
		dd = dia;

	fecha = totalsemana[posicion]+", "+dd+" de "+totalmes[mes]+" de "+anho;
	content = layer+".innerHTML = '"+fecha+"'; "
	eval(content);
}



function movement()
{
	currentX = currentY = 0;
	whichEl = null;

	document.onmouseover = cursEl;
	document.onmousedown = grabEl;
	document.onmousemove = moveEl;
	document.onmouseup = dropEl;
}



function grabEl()// function for onmousedown
{						
	whichEl = event.srcElement;
	while (whichEl.id.indexOf("PopUpCalendar") == -1){
		whichEl = whichEl.parentElement;
		alert( whichEl.parentElement );
			if (whichEl == null) { return }
	}

	x=whichEl.style.pixelLeft = whichEl.offsetLeft;
	y=whichEl.style.pixelTop = whichEl.offsetTop;

	currentX = (event.clientX + document.body.scrollLeft);
	currentY = (event.clientY + document.body.scrollTop); 
}


// function for onmousemove
function moveEl(){
	if (whichEl == null) { return };

	newX = (event.clientX + document.body.scrollLeft);
	newY = (event.clientY + document.body.scrollTop);

	distanceX = (newX - currentX);
	distanceY = (newY - currentY);
	currentX = newX;
	currentY = newY;

	x=whichEl.style.pixelLeft += distanceX;
	y=whichEl.style.pixelTop += distanceY;

	var obj = document.all['PopUpCalendar'];
	obj.style.left = whichEl.style.pixelLeft ;
	obj.style.top = whichEl.style.pixelTop ;

	event.returnValue = false;
}

// function for onmouseup
function dropEl(){
	whichEl = null;
}

function cursEl(){
	if (event.srcElement.id.indexOf("PopUpCalendar") != -1)
		event.srcElement.style.cursor = "move";
}

