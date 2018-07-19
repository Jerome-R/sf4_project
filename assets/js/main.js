// main.js


// require the JavaScript node module
// add window. to be be able to use libraries
window.$ = window.jQuery = require('jquery');
window.moment = require('moment');
require('bootstrap');

// require the JavaScript assets module
require('./temporusdominus.js');

$(document).ready(function() {
    //$('[data-toggle="popover"]').popover();

    // Display or hide password, Checkou out the DOM order to make sure it works properly
    $('.visible_pwd').on('click', function(event){
        if($(this).attr('data') == "hidden"){
            $(this).children('i').removeClass('fa-eye').addClass('fa-eye-slash');
            $(this).attr('data','visible')
            $(this).parent().prev('input').attr('type','text');
        }
        else{
            $(this).children('i').removeClass('fa-eye-slash').addClass('fa-eye');
            $(this).attr('data','hidden')
            $(this).parent().prev('input').attr('type','password');
        }
    });



    // Change arrow on menu collapse
    $(".nav-link.active").children('i.fa-caret-left').removeClass('fa-caret-left').addClass('fa-caret-down');
    
    $('.nav-link').on('click', function(event){
        if( $(this).attr('aria-expanded') == "true"){
            $(this).children('i.fa-caret-down').removeClass('fa-caret-down').addClass('fa-caret-left');
        }
        else{
            $(this).children('i.fa-caret-left').removeClass('fa-caret-left').addClass('fa-caret-down');
        }
    });

});