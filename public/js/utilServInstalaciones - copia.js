function lol() {
   $('#instalaciones_servAuxCon').on('change',function () {
       $('#modificador').empty();
      switch (parseInt($(this).val())){
          case 8: {
              var ramas= MuestraRama();
              $(ramas).appendTo($('#modificador'));
          };break;
          case 10: console.log(10);break;
          case 11: console.log(11);break;
          case 12: console.log(12);break;
          case 13: console.log(13);break;
      }
   })

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
    var roles= $('#producto_precios')[0];
    var checks = $('input[type=checkbox]');
    var valor= "[";
    for(var i = 0; i < checks.length; i++){
//                checks.prop('indeterminate', true);
        if(checks[i].checked ===true){
            valor+="\"";
            valor+= checks[i].value ;
            valor+="\",";
        }
    }
    valor+="]";
    valor =  valor.replace(",]","]");
    roles.value = valor;
}

function afterLoad() {
    var roles= $('#producto_precios')[0].value;
    var checks = $('input[type=checkbox]');

    var cad= roles.substr(2,roles.length -4);
    cad=cad.replace(/"/g ,"");
    var rolArray= cad.split(",");
    for(var i=0;i< checks.length;i++){
        for(var j=0;j< rolArray.length;j++)
            if(rolArray[j]== checks[i].value)
                checks[i].checked=true;
    }

    console.log(rolArray);

}

function toprol() {
    afterLoad();
    $('input[type=checkbox]').on('click',editRol);
}
