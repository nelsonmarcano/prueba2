$(document).ready(function(){
    var getCiudades = function(){
        $.post('ajax/getCiudades', 'pais='+$("#pais").val(), function(datos){
            $("#ciudad").html('');
            for (var i = 0; i < datos.length; i++) {
                $("#ciudad").append('<option value="'+datos[i].id+'">'+datos[i].ciudades+'</option>');
            }
        }, 'json');
    }
    
    $("#pais").change(function(){
        if (!$("#pais").val()) {
            $("#ciudad").html('');
        } else {
            getCiudades();
        }
    });
    
    $("#btn_insertar").click(function(){
        $.post('ajax/insertarCiudad', 'pais='+$("#pais").val()+'&ciudad='+$("#ins_ciudad").val())
        $("#ins_ciudad").val('');
        alert('Insertado');
        getCiudades();
    });
});