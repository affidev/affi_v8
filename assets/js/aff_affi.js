$(document).ready(function () {

    let inf = $('.wb-inf-');
    let _pg = $('.ii_switch').attr('data-js') === 'cu';
    let agent = inf.attr("data-agent") !== "mobile/";  //ici agent true c'est desk/ ??
    let notices = null;

    // init plugin placernotice :
    let panneau = $('.flex-notb_v6-fix');
    let replace = inf.attr("data-replacejs") !== "no";
    if (agent && replace) {
        notices = $('.last-postall_v6-fix');
    } else if (replace) {
        notices = $('.last-postall_mob');
    }
    if (agent && replace) panneau.placernotice(notices); // plugin sur panneau

    // navigation pop notif :
    let btnotif = $('#nav_notifs'), pnotif = $('#popnotif');
    btnotif.on('click', function () {
        pnotif.data('state', !pnotif.data('state'));
        pnotif.toggle("slide:right");
    });

    // navigation pop menu horizontal :
    let btuser = $('#nav_user'), btclose = $('#nav_close'), puser = $('#popuser');
    btuser.on('click', function () {
        puser.data('state', !puser.data('state'));
        puser.toggle("slide:right");
    });
    btclose.on('click', function () {
        puser.data('state', !puser.data('state'));
        puser.toggle("slide:right");
    });

    // toogle menu :
    let accordion = $("[data-toogle-accordion]")
    if (accordion.length > 0) {
        accordion.on('click', function (e) {
            e.stopPropagation();
            let link = $(this).data('toogle-accordion')
            let lilink = $("[data-element-toogle=" + link + "]")
            if (lilink.data('gnav-action') === "open") {
                lilink.removeClass('AccordionList-collapsed')
                lilink.data('gnav-action', 'close')
            } else {
                lilink.addClass('AccordionList-collapsed')
                lilink.data('gnav-action', 'open')
            }
        });
    }

    // navigation pop kebabMenu-button :
    let kebab=$('.kebabMenu-button'),  kebabMenu=$('.kebabMenu-items');
    kebab.on('click', function(){
        kebabMenu.data('state',!kebabMenu.data('state'));
        kebabMenu.toggle("slide:right");
    })

    // navigation bullecargo :
    let btcargo=$('#shwlaff'),  cargobulle=$('#tgl_b');
    btcargo.on('click', function(){
        cargobulle.data('state',!cargobulle.data('state'));
        cargobulle.toggle();
    });

    // navigation bullecatch :
    let btcatch=$('#catchaffi'),  catchbulle=$('#tgl_catch');
    btcatch.on('click', function(){
        catchbulle.data('state',!catchbulle.data('state'));
        catchbulle.toggle();
    });

    // toogle board publication :
    let tbnav=$('.nav-bar')
    tbnav.on('click', function(){
        if(!$(this).hasClass('baract-on')){ //si off
            let tog=$(this).data('navbar')
            let bar=$('#bar-'+tog)
            tbnav.removeClass('baract-on').addClass('baract-of')
            $(this).removeClass('baract-of').addClass('baract-on')
            $('.onglaff-bar').hide()
            bar.show()
        }
    });

    //toogle shop bouton acheter
    $('.moregoa').on('click', function(){
        $('#morinf').fadeToggle(600)
    });

    $(window).scroll(function(){
        let posi=$(window).scrollTop();
        if(posi>=70){
            $('#city').hide()
            if(!agent){
                btcargo.removeClass('bull-ov').addClass('bull-ov-lite')
            }
        }
        if(posi<70){
            $('#city').show()
            if(!agent){
                btcargo.removeClass('bull-ov-lite').addClass('bull-ov')
            }
        }
    });

    // toogle affi
    let tabtoggle="tgl_0";

    $('.bulletoggle').on('click', function(e){
        e.stopPropagation();
        bulletooglenav($(this))
    });

    $('.affitoggle').on('click', function(e){
        e.stopPropagation();
        affitooglenav($(this))
    });

    $('.buttontoggle').on('click', function(e){
        e.stopPropagation();
        buttontooglenav($(this))
    });

    // toogle button swith-tgll :
    let swiths = $("[data-switch]")
    if (swiths.length > 0) {
        swiths.on('click', function (e) {
            e.stopPropagation();
            let tog = $(this).data('switch')
            if ($(this).data('action') === "sh") {
                $('.tglaff').hide();
                swiths.removeClass('no-switch')
                swiths.data('action', "sh")
                $(this).addClass('no-switch')
                $(this).data('action', "nh")
                $('#'+tog).fadeIn(800)
            }
        });
    }

    // menu button swith-tgll :
    let textswiths = $("[data-textswitch]")
    if (textswiths.length > 0) {
        textswiths.on('click', function (e) {
            e.stopPropagation();
            let tog = $(this).data('textswitch')
            if ($(this).data('action') === "sh") {
                $('.tglaff').hide();
                textswiths.removeClass('shtext-switch')
                textswiths.data('action', "sh")
                $(this).addClass('shtext-switch')
                $(this).data('action', "nh")
                $('#'+tog).fadeIn(800)
            }
        });
    }
    // button va-et-viens avec close modal :
    let tglswitch = $("[data-tglswitch]")
    if (tglswitch.length > 0) {
        tglswitch.on('click', function (e) {
            e.stopPropagation();
            let tog = $(this).data('tglswitch')
            if ($(this).data('action') === "sh") {
                tglswitch.removeClass('no-switch')
                tglswitch.data('action', "sh")
                $(this).addClass('no-switch')
                $(this).data('action', "nh")
                $('#'+tog).toggle('slow');
            }
        });
    }

    function buttontooglenav(sel) {
        let tog =sel.data("affitgl")
        $('.tglaff').hide();
        $('[data-affitgl]').addClass('bt-of').removeClass('_bulle-act');
        sel.removeClass('bt-of').addClass('_bulle-act');
        $('#'+tog).fadeIn(800)
    }

    function affitooglenav(sel) {
        let tog =sel.data("affitgl")
        $('.tglaff').hide();
        $('#'+tog).fadeIn(800)
    }

    function bulletooglenav(sel) {
        let tog =sel.data("affitgl")
        if(tog === "tgl_9"){ //conversation
            $('#'+tog).toggle('slow')
        }
        else if(tog === "tgl_7"){ //menu website (customer)
            $('#'+tog).toggle('slow')
        }
        else if(tog === "tgl_info"){ //menu website (customer)
            $('#'+tog).toggle('slow')
        }
        else{
            $('.tglaff').hide();
            $('[data-affitgl]').addClass('bt-of').removeClass('_bulle-act');
            if(tog === "tgl_10" || tog === "tgl_5"){
                sel.removeClass('bt-of');
            }else{
                sel.removeClass('bt-of').addClass('_bulle-act');
            }
            exectoogle(tog)
        }
    }

    function exectoogle(tgl){
        tabtoggle=tgl;
        if(tabtoggle ==="tgl_10") tabtoggle ="tgl_0"
        $('#'+tabtoggle).fadeIn(800)
    }

    function dsptime() {
        let heure;
        let date = new Date();
        h = date.getHours();
        let m = date.getMinutes();
        let s = date.getSeconds();
        let hh = date.getHours();
        h = h > 12 ? h % 12 : h;

        heure = (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s + (hh < 12 ? ' am' : ' pm');
        document.querySelector('#time').innerHTML = heure;
        return heure;
    }
    if(document.querySelector('#time')!== null)dsptime();

    $('.suiv_data').on('click', function(e){
        e.stopPropagation();
        let data={id:$(this).attr("data-suiv")}
        fetch('/sp/msg/notifications/change-state-read', {
            method: 'post',
            body: JSON.stringify(data)
        })
            .then(r => r.json())
            .then(r => {
                console.log(r)
            })
    });

});