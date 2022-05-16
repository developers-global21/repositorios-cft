function guardar() {
    var nombre=document.getElementById("nombre").value;
    var categoria=document.getElementById("categoria").value;
    var subcategoria=document.getElementById("subcategoria").value;
    if (nombre!="" && categoria!="-99" && subcategoria!="-99") {
        var f = $(this);
        var formData = new FormData(document.getElementById("f1"));
        formData.append("dato", "valor");
        titulo = 'Atención'
        parrafo = "<span class='text-success'>Espere por favor<p  align='center'><img src='../../assets/images/wait2.gif' width='50' height='50'></span>"
        $('#title_modal').html(titulo)
        $('#content_modal').html(parrafo)
        $('#myModal').modal()
        $.ajax({
            url: "save_subproceso",
            type: "post",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        }).done(function (res) {
            var data = JSON.parse(res)
            console.log(data);
            if (data.length > 0) {
                switch (data[0]) {
                    case "1": // --- todo salio bien
                        titulo = 'Atención'
                        parrafo = "<span class='text-success'>Se registró el Subproceso de forma exitosa, actualizando contenido de la página</span><p  align='center'><img src='../../assets/images/wait2.gif' width='50' height='50'></p>"
                        $('#title_modal').html(titulo)
                        $('#content_modal').html(parrafo)
                        $('#myModal').modal()
                        setTimeout(function () {
                        $('#myModal').modal('hide');
                        document.location = '/subproceso/'
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
    }else{
        titulo = 'Atención'
        parrafo = "<span class='text-danger'>Debe seleccionar una categoría, el proceso  y sumnistrar un nombre para el Subproceso</span>"
        $('#title_modal').html(titulo)
        $('#content_modal').html(parrafo)
        $('#myModal').modal()    
    }
}

function cambio(){
    var categoria=document.getElementById("categoria").value;
    if (categoria!="-99") {
        $.ajax({
            url: "/categoria/get_subcategoria/",
            type: "post",
            dataType: "html",
            data: {id: categoria}
        }).done(function (res) {
            var data = JSON.parse(res)
            if (data.length > 0) {
                document.getElementById("subcategoria").length=0;
                var html = "<option value='-99'>Seleccione una subcategoría</option>"
                for (var i = 0; i < data.length; i++) {
                    html += "<option value='" + data[i]['id'] + "'>" + data[i]['nombre'] + "</option>"

                }
                document.getElementById("subcategoria").innerHTML = html;
            } else {
                titulo = 'Atención'
                parrafo = "<span class='text-danger'>Ocurrió un error que no pudo ser controlado<br>Inténtelo de nuevo</span>"
                $('#title_modal').html(titulo)
                $('#content_modal').html(parrafo)
                $('#myModal').modal()                
            }
        });
    }else{
        titulo = 'Atención'
        parrafo = "<span class='text-danger'>Debe seleccionar una categoria</span>"
        $('#title_modal').html(titulo)
        $('#content_modal').html(parrafo)
        $('#myModal').modal()            
    }

}
        

