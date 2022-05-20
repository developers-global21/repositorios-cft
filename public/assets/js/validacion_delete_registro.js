function borrar(idregistro) {
    var rrr=confirm("¿Está seguro de que desea eliminar este registro?");
    if (rrr==true){
        titulo = 'Atención'
        parrafo = "<span class='text-success'>Espere por favor<p  align='center'><img src='../../assets/images/wait2.gif' width='50' height='50'></span>"
        $('#title_modal').html(titulo)
        $('#content_modal').html(parrafo)
        $('#myModal').modal()
        $.ajax({
            url: "/registro/delete_registro/",
            type: "post",
            dataType: "html",
            data: {
                id: idregistro,
            },
        }).done(function (res) {
            var data = JSON.parse(res)
            console.log(data);
            if (data.length > 0) {
                switch (data[0]) {
                    case "1": // --- todo salio bien
                        titulo = 'Atención'
                        parrafo = "<span class='text-success'>Se eliminó el registro de forma exitosa, actualizando contenido de la página</span><p  align='center'><img src='../../assets/images/wait2.gif' width='50' height='50'></p>"
                        $('#title_modal').html(titulo)
                        $('#content_modal').html(parrafo)
                        $('#myModal').modal()
                        setTimeout(function () {
                        $('#myModal').modal('hide');
                        document.location = '/registro/'
                        }, 5000);
                        break;
                    case "-1": // --- error
                        titulo = 'Atención'
                        parrafo = "<span class='text-danger'>Existen elementos dependientes de esta categoría<br>No puede eliminarlos hasta que estos sean removidos previamente</span>"
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

function pagina(){
    var can_reg=document.getElementById("can_reg").value;
    var categoriaId=document.getElementById("categoria").value;
    var procesoId=document.getElementById("proceso").value;
    var subprocesoId=document.getElementById("subproceso").value;
    var periodoId=document.getElementById("periodo").value;    
    titulo = 'Atención'
    parrafo = "<span class='text-success'>Espere por favor<p  align='center'><img src='../../assets/images/wait2.gif' width='50' height='50'></span>"
    $('#title_modal').html(titulo)
    $('#content_modal').html(parrafo)
    $('#myModal').modal()
    document.location = '/registro/?can_reg='+can_reg+'&pag=1'+'&categoriaId='+categoriaId+'&procesoId='+procesoId+'&subprocesoId='+subprocesoId+'&periodoId='+periodoId ;  
    

}

function cambio1(){
    var idcategoria=document.getElementById("categoria").value;
    if (idcategoria!="-99") {
        $.ajax({
            url: "/dashboard/get_procesos/",
            type: "post",
            dataType: "html",
            data: {id: idcategoria}
        }).done(function (res) {
            var data = JSON.parse(res)    
            document.getElementById("proceso").length=0;
            document.getElementById("subproceso").length=1;

            var html = "<option value='-99'>Seleccione Proceso</option>";
            if (data[0].length > 0) {
                for (var i = 0; i < data[0].length; i++) {
                       html += "<option value='" + data[0][i]['id'] + "'>" + data[0][i]['nombre'] + "</option>"
                }

            } else {
                titulo = 'Atención'
                parrafo = "<span class='text-danger'>No se consiguieron Procesos para esta Categoria<br>Inténtelo de nuevo</span>"
                $('#title_modal').html(titulo)
                $('#content_modal').html(parrafo)
                $('#myModal').modal()                
            }
            document.getElementById("proceso").innerHTML = html;
        });
    }else{
        titulo = 'Atención'
        parrafo = "<span class='text-danger'>Debe seleccionar una categoria</span>"
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
                for (var i = 0; i < data[0].length; i++) {
                       html += "<option value='" + data[0][i]['id'] + "'>" + data[0][i]['nombre'] + "</option>"
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

function filtrar(){
    var can_reg=document.getElementById("can_reg").value;
    var categoriaId=document.getElementById("categoria").value;
    var procesoId=document.getElementById("proceso").value;
    var subprocesoId=document.getElementById("subproceso").value;
    var periodoId=document.getElementById("periodo").value;
    titulo = 'Atención'
    parrafo = "<span class='text-success'>Espere por favor<p  align='center'><img src='../../assets/images/wait2.gif' width='50' height='50'></span>"
    $('#title_modal').html(titulo)
    $('#content_modal').html(parrafo)
    $('#myModal').modal()
    document.location = '/registro/?can_reg='+can_reg+'&pag=1&categoriaId='+categoriaId+'&procesoId='+procesoId+'&subprocesoId='+subprocesoId+'&periodoId='+periodoId ; 

}

function Limpiar(){
    var can_reg=document.getElementById("can_reg").value;
    titulo = 'Atención'
    parrafo = "<span class='text-success'>Espere por favor<p  align='center'><img src='../../assets/images/wait2.gif' width='50' height='50'></span>"
    $('#title_modal').html(titulo)
    $('#content_modal').html(parrafo)
    $('#myModal').modal()
    document.location = '/registro/?can_reg='+can_reg+'&pag=1' ;    
}
