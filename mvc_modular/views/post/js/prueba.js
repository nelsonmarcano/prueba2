$(document).on('ready', function() {
   $(".pagina").click(function() {
      paginacion($(this).attr("pagina")); 
   });
   
   $(".pagina").live('click', function() {
      paginacion($(this).attr("pagina")); 
   });
   
   var paginacion = function(pagina){
       var pagina = 'pagina=' + pagina;
       var nombre = '&nombre=' + $("#nombre").val();
       var pais = '&pais=' + $("#pais").val();
       var ciudad = '&ciudad=' + $("#ciudad").val();
       var registros = '&registros=' + $("#registros").val();
       
       $.post('/mvc_modular/post/pruebaAjax', pagina + nombre + pais + ciudad + registros, function(data){
          $("#lista_registros").html(''); 
          $("#lista_registros").html(data);
       });
   }
   
   $("#pais").change(function(){
       var pagina = 'pagina=' + pagina;
       
       $.post('/mvc_modular/ajax/getCiudades', 'pais='+$("#pais").val(), function(datos){
            $("#ciudad").html('<option value=""> - Seleccione ciudad - </option>');
            for (var i = 0; i < datos.length; i++) {
                $("#ciudad").append('<option value="'+datos[i].id+'">'+datos[i].ciudades+'</option>');
            }
        }, 'json');
        
        $("#ciudad").val('');
        
        paginacion();
   });
   
   $("#btnEnviar").click(function(){
       paginacion();
   });
   
   $("#ciudad").change(function(){
       
       if ($("#pais").val()) {
           paginacion();
       }
   });
   
   $("#registros").live('change', function(){
       paginacion();
   });
   
});