$('.add-to-cart').on('click', function (e) {

    e.preventDefault();

    $.ajax({
        url: '/cart/actions/add',
        method: 'POST',
        data: { 'id':$(this).attr('id') },
        complete: function () {

            window.location.href = "/cart";

        }
    });

    return false;

});

$('.cart-minus').on('click', function (e) {

    e.preventDefault();

    $.ajax({
        url: '/cart/actions/minus',
        method: 'POST',
        data: { 'id':$(this).attr('id') },
        complete: function () {

            window.location.reload();

        }
    });

    return false;

});

$('.cart-delete').on('click', function (e) {

    e.preventDefault();

    $.ajax({
        url: '/cart/actions/remove',
        method: 'POST',
        data: { 'id':$(this).attr('id') },
        complete: function () {

            window.location.reload();

        }
    });

    return false;

});

function checkout() {

    var gateway = $('input[name=gateway]:checked').val();

    if(gateway === '')
    {
        alert('Selecione a gateway');
        return false;
    }

    $.ajax({
       url: '/checkout',
       method: 'POST',
       data: { gateway: gateway },
       dataType: 'JSON',
       complete: function (result) {
           console.log(result);

           let res = JSON.parse(result.responseText);

           location.href = res.link;
       }
    });

}