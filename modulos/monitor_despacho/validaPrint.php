<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
<script type="text/javascript" language="javascript">
function objetoAjax(){
	var xmlhttp=false;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
  		}
	}
	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

function validar(){
  //donde se mostrará lo resultados
  divResultado = document.getElementById('resultado');
  //valores de las cajas de texto
  OT=document.valida.OT.value;
//instanciamos el objetoAjax
  ajax=objetoAjax();
  //uso del medoto POST
  //archivo que realizará la operacion
  //registro.php
  ajax.open("GET", "registro.php",true);
  ajax.onreadystatechange=function() {
  if (ajax.readyState==4) {
  //mostrar resultados en esta capa
  divResultado.innerHTML = ajax.responseText
  }
 }
  ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
  //enviando los valores
  ajax.send("OT="+OT)
}
</script>
</head>
<!--  onload='window.print();window.close();'-->
<body>
	
<?
	session_start();
	$id_ot = $_SESSION['id_ot2']; 
?>

<form name="valida" action="#" onSubmit="validar(); return false" >
 <input name="OT" value="<? echo $id_ot ?>"type="text" />
 <input name="Submit" type="submit" class="boton" value="GRABAR" />
</form>
<div id="resultado"></div>
</body>
</html>