$(document).ready(function(){
    $('#form1').validate({
        rules:{
            role:{
                required: true
            }
        },
        messages:{
            role:{
                required: "Debe introducir el role"
            }
        }
    });
});