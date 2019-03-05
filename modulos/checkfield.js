// JavaScript Document
function KeyIsRut(evt)
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)

	if (isNav) {
		if  (evt.which >= 48 && evt.which <=  57) /*[0-9]*/
			
		return true;
			if(evt.keyCode == 13){
			 checkDV();
			}
	return false;
	}
	else if (isIE)
		{
		evt = window.event;
		if (evt.keyCode >= 48 && evt.keyCode <=  57) /*[0-9]*/
			
			return true;
			if(evt.keyCode == 13){
			 checkDV();
			}
		return false;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}
////////////////////////////////////////////////
function KeyIsNumber(evt)
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)

	if (isNav) {
		if ( evt.which == 13 || evt.which == 8 || (evt.which >= 48 &&  evt.which <=57) )
		return true;
	return false;
	}
	else if (isIE)
		{evt = window.event;
		if ( evt.keyCode == 13 || evt.keyCode == 8 || (evt.keyCode >= 48 && evt.keyCode <= 57) )
			return true;
		return false;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}
////////////////////////////////////////////////


////////////////////////////////////////////////
function KeyIsNumberDv(evt)
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)

	if (isNav) {
		if ( evt.which == 13 || evt.which == 8 || evt.which == 45 || (evt.which >= 48 &&  evt.which <=57) )
		return true;
	return false;
	}
	else if (isIE)
		{evt = window.event;
		if ( evt.keyCode == 13 || evt.keyCode == 8 || evt.keyCode == 45 || (evt.keyCode >= 48 && evt.keyCode <= 57) )
			return true;
		return false;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}
////////////////////////////////////////////////

function KeyIsDecimalP(evt)
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)

	if (isNav) {
		if ( evt.which == 13 || evt.wich == 46 /*Punto decimal*/ || evt.which == 8 || (evt.which >= 48 &&  evt.which <=57) )
		return true;
	return false;
	}
	else if (isIE)
		{evt = window.event;
		if ( evt.keyCode == 13 || evt.keyCode == 46 /*Punto decimal*/ || evt.keyCode == 8 || (evt.keyCode >= 48 && evt.keyCode <= 57) )
			return true;
		return false;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}
////////////////////////////////////////////////
function KeyIsNumberCo(evt) /* acepta numeros y comas*/
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)
	if (isNav) {
		if ( evt.which == 13 || evt.which == 44 || evt.which == 8 || (evt.which >= 48 &&  evt.which <=57) )
		return true;
	return false;
	}
	else if (isIE)
		{evt = window.event;
		if ( evt.keyCode == 13 || evt.keyCode == 44 || evt.keyCode == 8 || (evt.keyCode >= 48 && evt.keyCode <= 57) )
			return true;
		return false;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}
	////////////////////////////////////////////////
function KeyIsBoleta(evt)
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)

	if (isNav) {
		if ( evt.which == 13  || evt.which == 8 || (evt.which >= 48 &&  evt.which <=57) )
		return true;
	return false;
	}
	else if (isIE)
		{evt = window.event;
		if ( evt.keyCode == 13 || evt.keyCode == 8 || (evt.keyCode >= 48 && evt.keyCode <= 57) )
			return true;
		return false;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}
////////////////////////////////////////////////
function KeyIsLetra(evt)
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)

	if (isNav) {
		if ( evt.which == 209 || evt.which == 241 || evt.which == 13 || evt.which == 8 || (evt.which >= 65 &&  evt.which <=90) || (evt.which >= 97 &&  evt.which <=122) || evt.which == 32)
		return true;
	return false;
	}
	else if (isIE)
		{evt = window.event;
		//if ( evt.keyCode == 13 || evt.keyCode == 8 || (evt.keyCode >= 65 && evt.keyCode <= 90) || (evt.keyCode >= 97 && evt.keyCode <= 122) || evt.keyCode == 225 || evt.keyCode == 233 || evt.keyCode == 237 || evt.keyCode == 243 || evt.keyCode == 250 || evt.keyCode == 193 || evt.keyCode == 201 || evt.keyCode == 205 || evt.keyCode == 211 || evt.keyCode == 218 )
		if ( evt.keyCode == 209 || evt.keyCode == 241 || evt.keyCode == 13 || evt.keyCode == 8 || (evt.keyCode >= 65 && evt.keyCode <= 90) || (evt.keyCode >= 97 && evt.keyCode <= 122) || evt.keyCode == 32 )
			return true;
		return false;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}
////////////////////////////////////////////////
function KeyIsTelefono(evt)
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)

	if (isNav) {
		if ( evt.which == 32 || evt.which == 40 || evt.which == 41 || evt.which == 45 || (evt.which >= 48 &&  evt.which <=57) || (evt.which <= 34 &&  evt.which >=39)  )
		return true;
	return false;
	}
	else if (isIE)
		{evt = window.event;
		if ( evt.keyCode == 32 || evt.keyCode == 40 || evt.keyCode == 41  || evt.keyCode == 45 || (evt.keyCode >= 48 && evt.keyCode <= 57)|| (evt.keyCode <= 34 && evt.keyCode >= 39) )
			return true;
		return false;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}
	////////////////////////////////////////////////
function KeyIsTexto(evt)/* NO permite ' " $ / \   */
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)

	if (isNav) {
		if ( evt.which == 34 ||  evt.which == 36 || evt.which == 39 || evt.which == 47 || evt.which == 92  || evt.which == 176 || evt.which == 38 || evt.which == 180 || evt.which == 60 || evt.which == 62)
		return false;
	return true;
	}
	else if (isIE)
		{evt = window.event;
		if (evt.keyCode == 34 || evt.keyCode == 36 || evt.keyCode == 39 || evt.keyCode == 47 || evt.keyCode == 92 || evt.keyCode == 176 || evt.keyCode == 38 || evt.keyCode == 180 || evt.keyCode == 60 || evt.keyCode == 62)
		return false;
	return true;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}

// Validar texto y número
function KeyIsAlfaNumeric(evt)
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)

	if (isNav) {
		if ( evt.which == 209 || evt.which == 241 || evt.which == 13 || evt.which == 8 || (evt.which >= 65 &&  evt.which <=90) || (evt.which >= 97 &&  evt.which <=122) || evt.which == 32 || (evt.which >= 48 &&  evt.which <=57) )
		return true;
	return false;
	}
	else if (isIE)
		{evt = window.event;
		//if ( evt.keyCode == 13 || evt.keyCode == 8 || (evt.keyCode >= 65 && evt.keyCode <= 90) || (evt.keyCode >= 97 && evt.keyCode <= 122) || evt.keyCode == 225 || evt.keyCode == 233 || evt.keyCode == 237 || evt.keyCode == 243 || evt.keyCode == 250 || evt.keyCode == 193 || evt.keyCode == 201 || evt.keyCode == 205 || evt.keyCode == 211 || evt.keyCode == 218 )
		if ( evt.keyCode == 209 || evt.keyCode == 241 || evt.keyCode == 13 || evt.keyCode == 8 || (evt.keyCode >= 65 && evt.keyCode <= 90) || (evt.keyCode >= 97 && evt.keyCode <= 122) || evt.keyCode == 32 || (evt.keyCode >= 48 &&  evt.keyCode <= 57) )
			return true;
		return false;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}
function KeyIsEnter(evt)
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)

	if (isNav) {
		if  ( evt.which == 13  )
		return true;
	return false;
	}
	else if (isIE)
		{evt = window.event;
		if ( evt.keyCode ==13 )
			return true;
		return false;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}

function parsePorcentDec(campo) {
var coma = -1;   //Posición inicial de la coma decimal
var numint = 3; //Número de enteros a formatear
var numdec = 2; //Número de decimales a formatear
var mystrin = campo.value; 
var mystroutint = ''; 
var mystroutdec = ''; 
var largo=campo.value.length;
var fin = ''; 
var ci=0;
var aux='';
	for (i=0; i<largo+1; ++i ){
	if (mystrin.charAt(i) == '.') 
		coma = i;
	else 
		if (coma == -1) {
			mystroutint += mystrin.charAt(i) + '';
		}
		else {
			mystroutdec += mystrin.charAt(i) + '';
		}
		
	
		if (formatndig(mystroutint, numint, 'left')==''){	
			valor='0';
		}
		if (formatndig(mystroutint, numint, 'left')=='.'){
			valor='0.';
		}
		if (formatndig(mystroutint, numint, 'left')=='0'){
			ci=ci+1;
			valor=' ';
		}
		if ((formatndig(mystroutint, numint, 'left')!='.')&&(formatndig(mystroutint, numint, 'left')!='')){
			if (ci>=1){
				valor=((mystroutint)*1);
			}else{
				valor=formatndig(mystroutint, numint, 'left');
			}
		}

			campo.value = valor + '.' + formatndig(mystroutdec, numdec, 'right'); 
	}
return false;
}

function formatndig(number, length, sense) {
var ret = '';
	if (sense == 'right') 
	for (j = 0; j < length; ++j) {
		if (number.charAt(j)==""){
			ret = ret + '0';}
		else
			ret = ret + number.charAt(j);
		}
	else
	for (j = number.length-1; j >= number.length - length; --j) {
		if (number.charAt(j)==""){
			ret = '' + ret;
		}		else
			ret = number.charAt(j) + ret ;
		}

	return ret;
}
////////////////////////////////////////////////
////////////////////////////////////////////////
function KeyIsRuta(evt)/*  acepta una ruta para impresora */
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)

	if (isNav) {
		if (evt.which==32 || evt.which==95 || evt.which==92 || evt.which==47 || (evt.which >= 48 &&  evt.which <=57) || (evt.which >= 65 &&  evt.which <=90) || (evt.which >= 97 &&  evt.which <=122) )
		return true;
	return false;
	}
	else if (isIE)
		{evt = window.event;
		if (evt.keyCode==32 || evt.keyCode==95 || evt.keyCode==92 || evt.keyCode==47 || (evt.keyCode >= 48 && evt.keyCode <= 57) || (evt.keyCode >= 65 && evt.keyCode <= 90) || (evt.keyCode >= 97 && evt.keyCode <= 122))
			return true;
		return false;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}

////////////////////////////////////////////////