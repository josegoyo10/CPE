/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */

$(document).ready(function() {
    $('input')
    .focus(function(event) {
        $(this).addClass('focused-input');
    })
    .blur(function(event) {
        $(this).removeClass('focused-input');
    });
    $('input.uppercase')
    .blur(function(event) {
        this.value = this.value.toUpperCase();
    });
});

