function guardar() {
    var f = $(this);
    var formData = new FormData(document.getElementById("f1"));
    formData.append("dato", "valor");
    titulo = 'Atención'
    parrafo = "<span class='text-success'>Espere por favor<p  align='center'><img src='../../assets/images/wait2.gif' width='50' height='50'></span>"
    $('#title_modal').html(titulo)
    $('#content_modal').html(parrafo)
    $('#myModal').modal()
    $.ajax({
        url: "save_categoria",
        type: "post",
        dataType: "html",
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    }).done(function (res) {
        console.log(res);
        var data = JSON.parse(res)
        console.log(data);
        if (data.length > 0) {
            switch (data[0]) {
                case "1": // --- todo salio bien
                    titulo = 'Atención'
                    parrafo = "<span class='text-success'>Se registró la categoria de forma exitosa, actualizando contenido de la página</span><p  align='center'><img src='../../assets/images/wait2.gif' width='50' height='50'></p>"
                    $('#title_modal').html(titulo)
                    $('#content_modal').html(parrafo)
                    $('#myModal').modal()
                    setTimeout(function () {
                    $('#myModal').modal('hide');
                    document.location = '/categoria/'
                    }, 5000);
                break;
                case "0": // --- error
                    titulo = 'Atención'
                    parrafo = "<span class='text-danger'>Ocurrió un error que no pudo ser controlado<br>Inténtelo de nuevo</span>"
                    $('#title_modal').html(titulo)
                    $('#content_modal').html(parrafo)
                    $('#myModal').modal()
                    break;
            }
        
        } else {
            titulo = 'Atención'
            parrafo = "<span class='text-danger'>Ocurrió un error que no pudo ser controlado<br>Inténtelo de nuevo</span>"
            $('#title_modal').html(titulo)
            $('#content_modal').html(parrafo)
            $('#myModal').modal()
        }
    });
        
}
