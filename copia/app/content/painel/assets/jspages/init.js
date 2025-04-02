$(document).ready(function() {

    $('.loader').fadeOut();

});

$('#createServer').on('submit', function (e) {

    e.preventDefault();

    let id = $("#createServer [name='plan']");
    let title = $("#createServer [name='title']");
    let currency = $("#createServer [name='currency']");
    let domain = $("#createServer [name='domain']");

    title.removeClass('is-invalid');
    currency.removeClass('is-invalid');
    domain.removeClass('is-invalid');

    if(title.val() === "")
    {
        title.addClass('is-invalid');
        return;
    }

    if(currency.val() === "")
    {
        currency.addClass('is-invalid');
        return;
    }

    if(domain.val() === "")
    {
        domain.addClass('is-invalid');
        return;
    }

    if(checkSubdomain(domain.val()) === true)
    {
        domain.addClass('is-invalid');
        return;
    }

    location.href = '/webstores/create?plan=' + id.val() + '&title=' + title.val() + "&currency=" + currency.val() + "&domain=" + domain.val();
});

function checkSubdomain(subdomain)
{
    $.ajax({
        url: '/webstores/create/checkSubdomain',
        method: 'POST',
        data: { 'subdomain':subdomain },
        dataType: 'JSON',
        complete: function (response) {
            console.log(response.responseText);

            let res = JSON.parse(response.responseText);

            return (res.status === 'ok');
        }
    });
    return false;
}

function startFreePeriod() {

    let plan = findGetParameter('plan');
    let name = findGetParameter('title');
    let currency = findGetParameter('currency');
    let subdomain = findGetParameter('domain');

    let button = $('.freeperiod');

    button.html('Configurando...');

    let data = { 'plan':plan, 'name':name, 'currency':currency, 'subdomain':subdomain };

    $.ajax({
        url: '/webstores/create/free',
        method: 'POST',
        data: data,
        dataType: 'JSON',
        complete: function (response) {

            console.log(response.responseText);
            let res = JSON.parse(response.responseText);

            if(res.status === 'ok')
            {
                swal({
                    type: 'success',
                    title: 'Sucesso!',
                    text: res.message,
                });
                setTimeout(function () {
                    location.href = '/';
                }, 2500);
            }else{
                swal({
                    type: 'error',
                    title: 'Oops...',
                    text: res.message,
                });
            }

        }
    });
}

function planFirstPayment(gateway) {
    $('.loader').fadeIn();

    let plan = findGetParameter('plan');
    let name = findGetParameter('title');
    let currency = findGetParameter('currency');
    let subdomain = findGetParameter('domain');

    let data = { 'gateway': gateway, 'plan':plan, 'name':name, 'currency':currency, 'subdomain':subdomain };

    $.ajax({
        url: '/webstores/create/checkout',
        method: 'POST',
        data: data,
        dataType: 'JSON',
        complete: function (response) {

            location.href = response.responseText;

        }
    });
}

function findGetParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(especialCharMask(sParameterName[1]));
        }
    }
}

function especialCharMask (especialChar){
    especialChar = especialChar.replace('/[áàãâä]/ui', 'a');
    especialChar = especialChar.replace('/[éèêë]/ui', 'e');
    especialChar = especialChar.replace('/[íìîï]/ui', 'i');
    especialChar = especialChar.replace('/[óòõôö]/ui', 'o');
    especialChar = especialChar.replace('/[úùûü]/ui', 'u');
    especialChar = especialChar.replace('/[ç]/ui', 'c');
    especialChar = especialChar.replace('/[^a-z0-9]/i', '');
    especialChar = especialChar.replace('/_+/', '');
    return especialChar;
}