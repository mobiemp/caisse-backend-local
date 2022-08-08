// this is the id of the form
$("#formLogin").submit(function(e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    var actionUrl = form.attr('action');

    $.ajax({
        type: "POST",
        url: actionUrl,
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {
            console.log(data)
            var result = JSON.parse(data)
            if(result.response === 1 ){
                $('#msgLogin').addClass('text-success text-center').text(result.message)
                $('#formLogin').hide()
                $('#chooseCaisse').show()
            }
            else{
                $('#msgLogin').text(result.message).addClass('text-danger text-center')
            }
        }
    });

});

function setIdCaisse(idcaisse) {
    $.ajax({
        type: "POST",
        url: 'updateIdCaisse.php',
        contentType: "application/json",
        data: JSON.stringify({
            "idcaisse": idcaisse,
        }),
        success: function(data)
        {
            console.log(data)
            var result = JSON.parse(data)
            if(result.response === 1){
                var base_url = window.location.origin;
                window.location.href = base_url + '/caisse-backend/caisse/'
            }
        }
    });
}
