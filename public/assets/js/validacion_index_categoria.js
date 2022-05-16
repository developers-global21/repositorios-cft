function mostrar(idcategoria) {
    $.ajax({
            url: "search_categoria",
            type: "POST",
            dataType: "html",
            data: {
                id: idcategoria,
            },
        }).done(function (res) {
            console.log(res);
            document.location = res;
        });
}
