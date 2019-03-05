function ir_pagina( pagina ) {
	location.href = pagina;
}

function validar_eliminar( mensaje, pagina ) {
    if ( confirm( mensaje ) ) {
        location.href = pagina;
    }
}

function validar_obligatorio( objeto, mensaje ) {
	// alert("Objeto:" + objeto.value);
    
	if(( objeto.value == '' )) {
		alert( mensaje );
		objeto.focus();
		return true;
	}
}

function isEmailAddress(theElement, nombre_del_elemento ){
	var s = theElement.value;
	var filter=/^[A-Za-z][A-Za-z0-9_.-]*@[A-Za-z0-9_]+\.[A-Za-z0-9_.]+[A-za-z]$/;
	if (s.length == 0 ) return false;
	if (filter.test(s))
	return false;
	else
	alert("Por favor ingrese un correo Electrónico válido.");
	theElement.focus();
	return true;
}


function validar_obligatorio_sin_focus( objeto, mensaje ) {
	if( objeto.value == '' ) {
		alert( mensaje );
		return true;
	}
}
function validar_numero( objeto, mensaje ) {
	if( isNaN(objeto.value) ) {
		alert( mensaje );
		objeto.focus();
		objeto.select();
		return true;
	}
}

function cambiar_color_on( id ){
	id.style.background = '#FFCC33';
}
function cambiar_color_off( id ){
	id.style.background = '#FFFFFF';
}

function makeOptionList(objeto, name,value, sel) {
	var o=new Option( name,value);
	objeto.options[objeto.length]=o;
	if( sel == 1 )
		objeto.selectedIndex = objeto.length-1;
}

function cerrar_caja( obj ) {
	obj.style.visibility = "hidden";
}

function generar_caja( x, y, contenido, obj ) {
	obj.style.left = x;
	obj.style.top = y;
	obj.style.visibility = "visible";

	aux_contenido = "<A HREF='#' onclick=\"cerrar_caja(document.all['PopUpCalendar']);\">cerrar</A><br>" + contenido;

	document.all["PopUpCalendar"].innerHTML = aux_contenido;
}
// JavaScript Document
function RutIsKey(evt)
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)

	if (isNav) {
		if  ( evt.which == 46 || /*punto*/ evt.which == 45 ||
			(evt.which >= 48 && evt.which <=  57) || /*[0-9]*/
			(evt.which == 75 || evt.which ==  107)    /*[kK]*/ )
		return true;
	return false;
	}
	else if (isIE)
		{evt = window.event;
		if ( evt.keyCode == 46 || /*punto*/ evt.keyCode == 45 ||
			(evt.keyCode >= 48 && evt.keyCode <=  57) || /*[0-9]*/
			(evt.keyCode == 75 || evt.keyCode ==  107)    /*[kK]*/ )
			return true;
		return false;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}
////////////////////////////////////////////////
function NumberIsKey(evt)
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
function LetraIsKey(evt)
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)

	if (isNav) {
		if ( evt.which == 209 || evt.which == 241 || evt.which == 13 || evt.which == 8 || (evt.which >= 65 &&  evt.which <=90) || (evt.which >= 97 &&  evt.which <=122) || evt.which == 32)
		return true;
	return false;
	}
	else if (isIE)
		{
			evt = window.event;		
		if ( evt.keyCode == 209 || evt.keyCode == 241 || evt.keyCode == 13 || evt.keyCode == 8 || (evt.keyCode >= 65 && evt.keyCode <= 90) || (evt.keyCode >= 97 && evt.keyCode <= 122) || evt.keyCode == 32 )
			return true;
		return false;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}

/////////////////////////////////////////////////////
/*Funcion que convierte UPPER CASE el valor de un campo de texto*/
function Upper(campo) {
	campo.value = campo.value.toUpperCase();
}
