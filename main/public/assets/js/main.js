$.fn.hasAttr = function(name) {
    return this.attr(name) !== undefined;
};

function isset ()
{
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: FremyCompany
    // +   improved by: Onno Marsman
    // +   improved by: Rafał Kukawski
    // *     example 1: isset( undefined, true);
    // *     returns 1: false
    // *     example 2: isset( 'Kevin van Zonneveld' );
    // *     returns 2: true

	  var a = arguments,
	    l = a.length,
	    i = 0,
	    undef;

	  if (l === 0)
	  {
	    throw new Error('Empty isset');
	  }

	  while (i !== l)
	  {
	    if (a[i] === undef || a[i] === null)
	    {
	      return false;
	    }
	    i++;
	  }
	  return true;
}

function arrayHasOwnIndex(array, prop) {
    return array.hasOwnProperty(prop) && /^0$|^[1-9]\d*$/.test(prop) && prop <= 4294967294; // 2^32 - 2
}

function tryParseJSON (jsonString){
    try {
        var o = JSON.parse(jsonString);

        // Handle non-exception-throwing cases:
        // Neither JSON.parse(false) or JSON.parse(1234) throw errors, hence the type-checking,
        // but... JSON.parse(null) returns 'null', and typeof null === "object",
        // so we must check for that, too.
        if (o && typeof o === "object" && o !== null) {
            return o;
        }else{

        }
    }
    catch (e) { }

    return jsonString;
};

$.fn.editable.defaults.params = function (params) {
    params._token = $(this).data("token");
    return params;
};



$(document).ready(function(){
    var tOut;
    var lastBtn;
    //$('#page-wrapper').css('margin-left', $('.navbar-left').css('width'));

    $(document).on('click', "button[type='button'], .btn", function () {

        var btn = $(this);

        if( btn.hasAttr('data-loading-text') ) {
            btn.button('loading');
        }

        if( btn.hasAttr('off-disable') ){
            return true;
        }
        if( btn.hasAttr('href') || btn.hasAttr('hrf') || btn.hasAttr('target') || btn.attr('data-toggle') == 'modal' || btn.hasClass('let_disable')  ) {
            lastBtn = btn;
            setTimeout(function () {
                btn.attr('disabled', 'disabled');
            }, 100);
        }

        return true;
    });

    $(document).on('click', "button[data-dismiss='modal']",function(){
        if(isset(lastBtn))
            lastBtn.removeAttr('disabled');

        if( $(this).hasAttr('reload'))
            self.location.reload();
    });

    $(document).on('keyup keypress', 'form', function(e){
        return e.which !== 13
    });

    $("form").validate();

    $(document).on('keydown', '.currency_input', function(e)
    {
        if(e.keyCode == 188)
        {
            e.preventDefault();
            $(this).val($(this).val() + '.');
        }
    });

    $(document).on('keyup', '.uppercase', function()
    {
        $(this).val($(this).val().toUpperCase());
    });

	$('body').tooltip({
		delay: { show: 300 },
		selector: '.tips',
        html: true
	});

    $('body').delegate('#l-menu-show','mouseover', function(e){
        $(this).css('left', '-100px');
        $('#l-menu').css('left', '0px');
    }).delegate('#l-menu','mouseleave', function(e){
        $(this).css('left', '-240px');
        $('#l-menu-show').css('left', '0px');
    });

    $('.search-el').on('change', function(){
        $.ajax({
          type: "POST",
          url: $('#search-form').prop( 'action' ),
          data: $('#search-form').serialize(),
          assync:false,
          cache:false,
          success: function( data ) {
            if(data == '0') location.reload();
          }
        });
    });

    $('body').delegate('.show-search','click', function(e){
        if($('.search-adv').css('display') === 'block')
        {
            $('.search-adv').hide('fade');
        }
        else
        {
            $('.search-adv').show('fade', 'fast');
        }
    });

    // $('html').click(function() {
    //     clearTimeout(tOut);
    //     $('.search-adv').hide('fade');
    // });

    $('.search-box').click(function(event){
        event.stopPropagation();
    });

    $('.show-search').on('click', function(){
        if($('.search-adv').css('display') === 'block')
        {
            $('.search-adv').hide('fade');
        }
        else
        {
            $('.search-adv').show('fade', 'fast');
        }
    });
    $('.tr_progress-bar-before td').on({
        mouseenter: function () {
            $(this).parent().next().css('background-color', '#f5f5f5');
            console.log('ts');
        },
        mouseleave: function () {
            $(this).parent().next().css('background-color', 'white');
            console.log('out');
        }
    });

    $('.tr_progress-bar td').on({
        mouseenter: function () {
            $(this).parent().prev().css('background-color', '#f5f5f5');
            console.log('ts');
        },
        mouseleave: function () {
            $(this).parent().prev().css('background-color', 'white');
            console.log('out');
        }
    });

    $('body').popover({
        selector: '.btn-popover',
        html: true,
        trigger: 'focus'
    });

});

/*
 * Translated default messages for the jQuery validation plugin.
 * Language: PL
 */
jQuery.extend(jQuery.validator.messages, {
        required: "To pole jest wymagane.",
        remote: "Proszę o wypełnienie tego pola.",
        email: "Proszę o podanie prawidłowego adresu email.",
        url: "Proszę o podanie prawidłowego URL.",
        date: "Proszę o podanie prawidłowej daty.",
        dateISO: "Proszę o podanie prawidłowej daty (ISO).",
        number: "Proszę o podanie prawidłowej liczby.",
        digits: "Proszę o podanie samych cyfr.",
        creditcard: "Proszę o podanie prawidłowej karty kredytowej.",
        equalTo: "Proszę o podanie tej samej wartości ponownie.",
        accept: "Proszę o podanie wartości z prawidłowym rozszerzeniem.",
        maxlength: jQuery.validator.format("Proszę o podanie nie więcej niż {0} znaków."),
        minlength: jQuery.validator.format("Proszę o podanie przynajmniej {0} znaków."),
        rangelength: jQuery.validator.format("Proszę o podanie wartości o długości od {0} do {1} znaków."),
        range: jQuery.validator.format("Proszę o podanie wartości z przedziału od {0} do {1}."),
        max: jQuery.validator.format("Proszę o podanie wartości mniejszej bądź równej {0}."),
        min: jQuery.validator.format("Proszę o podanie wartości większej bądź równej {0}.")
});


/* Polish initialisation for the jQuery UI date picker plugin. */
/* Written by Jacek Wysocki (jacek.wysocki@gmail.com). */
jQuery(function($){
        $.datepicker.regional['pl'] = {
                closeText: 'Zamknij',
                prevText: '&#x3c;Poprzedni',
                nextText: 'Następny&#x3e;',
                currentText: 'Dziś',
                monthNames: ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec',
                'Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
                monthNamesShort: ['Sty','Lu','Mar','Kw','Maj','Cze',
                'Lip','Sie','Wrz','Pa','Lis','Gru'],
                dayNames: ['Niedziela','Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota'],
                dayNamesShort: ['Nie','Pn','Wt','Śr','Czw','Pt','So'],
                dayNamesMin: ['N','Pn','Wt','Śr','Cz','Pt','So'],
                weekHeader: 'Tydz',
                dateFormat: 'yy-mm-dd',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['pl']);
});

