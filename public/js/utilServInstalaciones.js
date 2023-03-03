function lol() {
   // $('#instalaciones_servAuxCon').on('change',function () {
   //     $('#modificador').empty();
   //    switch (parseInt($(this).val())){
   //        case 8: {
   //            var ramas= MuestraRama();
   //            $(ramas).appendTo($('#modificador'));
   //        };break;
   //        case 10: console.log(10);break;
   //        case 11: console.log(11);break;
   //        case 12: console.log(12);break;
   //        case 13: console.log(13);break;
   //    }
   // })

   // $("<input type='radio' />").appendTo($('#modificador'));
   //$('#modificador').add( "<p id='new'>new paragraph</p>" );
       // .css( "background-color", "red" )
    //console.log(select);
}

function MuestraRama() {
   return "<div class=\"custom-control custom-checkbox custom-control-inline\">\n" +
       "        <input type=\"checkbox\" id=\"customRadioInline1\" name=\"customRadioInline1\" class=\"custom-control-input\">\n" +
       "        <label class=\"custom-control-label\" for=\"customRadioInline1\">Automotor</label>\n" +
       "    </div>\n" +
       "    <div class=\"custom-control custom-checkbox custom-control-inline\">\n" +
       "        <input type=\"checkbox\" id=\"customRadioInline2\" name=\"customRadioInline1\" class=\"custom-control-input\">\n" +
       "        <label class=\"custom-control-label\" for=\"customRadioInline2\">Maritimo</label>\n" +
       "    </div> \
            <div class='custom-control-inline custom-checkbox custom-control'>\
                <input type='checkbox' id='customcheckbox3' class='custom-control-input'>\
                <label class='custom-control-label' for='customcheckbox3'>Ferroviario</label>\
            </div> \
       ";
}

function editRol() {
    $("#instalaciones_servicioshtml input:checkbox:checked").each(function() {
        var serv1obj= $('#instalaciones_servicios1');
        var serv1= serv1obj[0].value;
        var serv1array = JSON.parse(serv1);
       if(serv1array.indexOf($(this).val())===-1){
           serv1array.push($(this).val());
           var outarray = JSON.stringify(serv1array);
           serv1obj.val(outarray);
       }
    });
    var serv1array = JSON.parse($('#instalaciones_servicios1')[0].value);
    var checkeds =  $("#instalaciones_servicioshtml input:checkbox:checked");
    var cad = '';
    for (var i = 0; i < serv1array.length ; i++) {
        var esta= false;
        for (var j = 0; j < checkeds.length ; j++) {
            if(serv1array[i]=== checkeds[j].value){
                esta=true;
            }
        }
        if(!esta){
            serv1array.splice(i,1);
            i--;
        }
    }
    var outarray = JSON.stringify(serv1array);
    $('#instalaciones_servicios1').val(outarray);

    var servvisual = $('#instalaciones_servvisual');
    // servvisual.empty();

    for (var i = 0; i < checkeds.length; i++) {
        cad+= ($(checkeds[i]).next().text()) + '.\n';
    }
   servvisual.val(cad);
    $('#servaimprimir').val(cad);

}

function afterLoad() {
    var serv1= $('#instalaciones_servicios1')[0].value; //valor del campo servicios1
    var serv1array = JSON.parse(serv1);

    var checks = $('#instalaciones_servicioshtml input[type=checkbox]');

     for(var i=0;i< checks.length;i++){
         for(var j=0;j < serv1array.length;j++)
         if(checks[i].value === serv1array[j])
             checks[i].checked = true;
     }

    var servvisual = $('#instalaciones_servvisual');
    var checkeds =  $("#instalaciones_servicioshtml input:checkbox:checked");
    // servvisual.empty();
    var cad='';
    for (var i = 0; i < checkeds.length; i++) {
        cad+= ($(checkeds[i]).next().text()) + '.\n';
    }
    servvisual.val(cad);
    $('#servaimprimir').val(cad);
}

function toprol() {
    afterLoad();
    $('#instalaciones_servicioshtml input[type=checkbox]').on('change',editRol);
}
