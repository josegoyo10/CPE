var arreglo = new Array();
var count=0;
var cadena = '';

function cadenaId_ot(){
	var boton, i;
	
	for(i=0; i<count; i++)
	{
	boton = document.getElementById(arreglo[i]);
	alert(boton.value);
	}
}

function recibir(check){
	arreglo[count] = check; 
	count++;
	cadena = cadena + check + ',';
}

function retorna_Idot(){
	var longitud= cadena.length;
	var cadena2 = '';
    cadena2 = cadena.substring(0,longitud-1);
	
	arreglo = '';
	count=0;
	cadena = '';

	return cadena2;	
	//alert(cadena);
	//document.monitor.cadena.value= cadena;
}

function refrescar(){
	window.location.reload();
}
