<html>
	<head>
		<script type="text/javascript">

			var isFirefox = testCSS('MozBoxSizing');                 // FF 0.8+
			var isIE = /*@cc_on!@*/false || testCSS('msTransform');  // At least IE6
			function testCSS(prop) {
				return prop in document.documentElement.style;
			}
			if( !isIE ) {
				function hookKeyboardEvents(e) {
					// get key code
					var key_code = (window.event) ? event.keyCode : e.which;
					// case :if it is IE event
					if (window.event) {
						if (!event.shiftKey && !event.ctrlKey) {
							window.event.returnValue = null;
							event.keyCode = 0;
						}
					} // case: if it is firefox event 
					else e.preventDefault();
				}
				window.document.onkeydown = hookKeyboardEvents;
			}
			if( isIE ) {
				document.onkeydown = function () { 
					if (event.keyCode == 17) alert('No se puede imprimir la guia mas de una vez.'); 
					  if (event.ctrlKey && event.keyCode == 80) {

									  alert('No se puede imprimir la guia mas de una vez.');

									  window.event.returnValue = null; 

									  event.keyCode = 0; 

					  }
				};
			}
			
			function windows_close()
			{
				window.close();
			}
		</script>

		<frameset rows="100%" border=0 frameborder=0 framespacing=0 marginwidth=0 marginheight=0 onkeypress="validar(event);">
			<frame name="Frame1" src="imprimeAll_guiaDespacho.php" noresize frameborder=0 framespacing=0 marginwidth=0 marginheight=0>
		</frameset>
	</head>
	<body>
		
	</body>
</html>