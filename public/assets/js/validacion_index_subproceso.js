function mostrar(idproceso) {
    $.ajax({
            url: "search_subproceso",
            type: "POST",
            dataType: "html",
            data: {
                id: idproceso,
            },
        }).done(function (res) {
            //console.log(res);
            document.location = res;
        });
}
