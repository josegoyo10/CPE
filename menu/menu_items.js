window.alert('li');
var MENU_ITEMS = [
	[' Inicio', 'home.htm', null,
		// Submenu;
	],
	[' Cotizaci�n/OS', null, null,
		// Submenu;
		
		[' Cear Cotizaci�n', 'cot1.htm'],
		[' Monitor Cotizaciones', 'monitor1.htm'],				
	],
	[' Coordinador', null, null,
		// Submenu;		
		[' Monitor OT', 'coordinador1.htm'],
						
	],	
	[' Utilidades', null, null,
		// Submenu;
		[' Datos Personales', ''],
		[' Ayuda', ''],		
	],
	[' Sistema', null, null,
		// Submenu;
		[' Adm. Usuarios', ''],
		[' Adm. Perfiles', ''],		
		[' Adm. M�dulos', ''],		
		[' Adm. Cat�logo Proveedor', ''],				
	],	
	[' Salir', 'login.htm', null,
		// Submenu;
	],
];
new menu (MENU_ITEMS, MENU_POS);