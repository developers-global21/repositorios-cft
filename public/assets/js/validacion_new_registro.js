function guardar() {
    var idcategoria=document.getElementById("idcategoria").value;
    var proceso=document.getElementById("proceso").value;
    var subproceso=document.getElementById("subproceso").value;
    var periodo=document.getElementById("periodo").value;
    if (categoria!="-99" && proceso!="-99" && subproceso!="-99" && periodo!="-99") {
        var f = $(this);
        var formData = new FormData(document.getElementById("f1"));
        formData.append("dato", "valor");
        titulo = 'Atención'
        parrafo = "<span class='text-success'>Espere por favor<p  align='center'><img src='../../assets/images/wait2.gif' width='50' height='50'></span>"
        $('#title_modal').html(titulo)
        $('#content_modal').html(parrafo)
        $('#myModal').modal()
        $.ajax({
            url: "/registro/save_registro/",
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
                        parrafo = "<span class='text-success'>Se anexo el Registro de forma exitosa, actualizando contenido de la página</span><p  align='center'><img src='../../assets/images/wait2.gif' width='50' height='50'></p>"
                        $('#title_modal').html(titulo)
                        $('#content_modal').html(parrafo)
                        $('#myModal').modal()
                        setTimeout(function () {
                        $('#myModal').modal('hide');
                        document.location = '/registro/'
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
        parrafo = "<span class='text-danger'>Debe seleccionar un proceso, subproceso y periodo</span>"
        $('#title_modal').html(titulo)
        $('#content_modal').html(parrafo)
        $('#myModal').modal()    
    }
}

function cambio(){
    var proceso=document.getElementById("proceso").value;
    if (proceso!="-99") {
        $.ajax({
            url: "/dashboard/get_subprocesos/",
            type: "post",
            dataType: "html",
            data: {id: proceso}
        }).done(function (res) {
            var data = JSON.parse(res)
            document.getElementById("subproceso").length=0;
            var html = "<option value='-99'>Seleccione un Subproceso</option>";
            if (data[0].length > 0) {
                for (var i = 0; i < data.length; i++) {
                       html += "<option value='" + data[i][0]['id'] + "'>" + data[i][0]['nombre'] + "</option>"
                }

            } else {
                titulo = 'Atención'
                parrafo = "<span class='text-danger'>No se consiguieron Subprocesos para este proceso<br>Inténtelo de nuevo</span>"
                $('#title_modal').html(titulo)
                $('#content_modal').html(parrafo)
                $('#myModal').modal()                
            }
            document.getElementById("subproceso").innerHTML = html;
        });
    }else{
        titulo = 'Atención'
        parrafo = "<span class='text-danger'>Debe seleccionar una categoria</span>"
        $('#title_modal').html(titulo)
        $('#content_modal').html(parrafo)
        $('#myModal').modal()            
    }

}
        

