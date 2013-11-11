$(document).ready(function(){
    $('#form1').validate({
        rules:{
            permiso:{
                required: true
            },
            llave:{
                required: true
            }
        },
        messages:{
            permiso:{
                required: "Debe introducir el permiso"
            },
            llave:{
                required: "Debe introducir el Key"
            }
        }
    });
});