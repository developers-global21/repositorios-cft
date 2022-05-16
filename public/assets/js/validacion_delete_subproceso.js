function borrar(idproceso) {
    var rrr=confirm("¿Está seguro de que desea eliminar este Subproceso?");
    if (rrr==true){
    titulo = 'Atención'
    parrafo = "<span class='text-success'>Espere por favor<p  align='center'><img src='../../assets/images/wait2.gif' width='50' height='50'></span>"
    $('#title_modal').html(titulo)
    $('#content_modal').html(parrafo)
    $('#myModal').modal()
    $.ajax({
        url: "/subproceso/delete_subproceso/",
        type: "post",
        dataType: "html",
        data: {
            id: idproceso,
        },
    }).done(function (res) {
        var data = JSON.parse(res)
        console.log(data);
        if (data.length > 0) {
            switch (data[0]) {
                case "1": // --- todo salio bien
                    titulo = 'Atención'
                    parrafo = "<span class='text-success'>Se eliminó el Proceso de forma exitosa, actualizando contenido de la página</span><p  align='center'><img src='../../assets/images/wait2.gif' width='50' height='50'></p>"
                    $('#title_modal').html(titulo)
                    $('#content_modal').html(parrafo)
                    $('#myModal').modal()
                    setTimeout(function () {
                    $('#myModal').modal('hide');
                    document.location = '/subproceso/'
                    }, 5000);
                    break;
                case "-1": // --- error
                    titulo = 'Atención'
                    parrafo = "<span class='text-danger'>Existen elementos dependientes de este Proceso<br>No puede eliminarlos hasta que estos sean removidos previamente</span>"
                    $('#title_modal').html(titulo)
                    $('#content_modal').html(parrafo)
                    $('#myModal').modal()
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
} else {
    titulo = 'Atención'
    parrafo = "<span class='text-info'>No se efectuo ningun cambio</span>"
    $('#title_modal').html(titulo)
    $('#content_modal').html(parrafo)
    $('#myModal').modal()    
}
        
}
