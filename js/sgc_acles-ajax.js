/*Javascript para SGC ACLES*/



function validaRut(campo){
  if ( campo.length == 0 ){ return false; }
  if ( campo.length < 8 ){ return false; }

  campo = campo.replace('-','')
  campo = campo.replace(/\./g,'')

  var suma = 0;
  var caracteres = "1234567890kK";
  var contador = 0;    
  for (var i=0; i < campo.length; i++){
    u = campo.substring(i, i + 1);
    if (caracteres.indexOf(u) != -1)
    contador ++;
  }
  if ( contador==0 ) { return false }
  
  var rut = campo.substring(0,campo.length-1)
  var drut = campo.substring( campo.length-1 )
  var dvr = '0';
  var mul = 2;
  
  for (i= rut.length -1 ; i >= 0; i--) {
    suma = suma + rut.charAt(i) * mul
                if (mul == 7)   mul = 2
            else  mul++
  }
  res = suma % 11
  if (res==1)   dvr = 'k'
                else if (res==0) dvr = '0'
  else {
    dvi = 11-res
    dvr = dvi + ""
  }
  if ( dvr != drut.toLowerCase() ) { return false; }
  else { return true; }
}

function sgcinsc_niceCurso(curso) {
  switch (curso) {
    case "1":
      var nicecurso = '1° Básico';
    break;
    case "2":
      var nicecurso = '2° Básico';
    break;
    case "3":
      var nicecurso = '3° Básico';
    break;
    case "4":
      var nicecurso = '4° Básico';
    break;
    case "5":
      var nicecurso = '5° Básico';
    break;
    case "6":
      var nicecurso = '6° Básico';
    break;
    case "7":
      var nicecurso = '7° Básico';
    break;
    case "8":
      var nicecurso = '8° Básico';
    break;
    case "9":
      var nicecurso = 'I° Medio';
    break;
    case "10":
      var nicecurso = 'II° Medio';
    break;    
  }
  return nicecurso;
}


function sgcinsc_niceSeguro(seguro) {
  switch(seguro) {
    case "alemana":
      var niceseguro = 'Clínica Alemana';
    break;
    case "santamaria":
      var niceseguro = 'Clínica Santa María';
    break;
    case "indisa":
      var niceseguro = 'Clínica Indisa';
    break;
    case "uc":
      var niceseguro = 'Clínica Universidad Católica';
    break;
    case "davila":
      var niceseguro = 'Clínica Dávila';
    default:
      var niceseguro = seguro;
    break;
  }
  return niceseguro;
}

//Muestra los cursos disponibles para cada nivel
function sgcinsc_renderAcles(curso) {
  var ajaxPlace = $('#ajaxAclesPlace');
  ajaxPlace.append('<i class="icon-refresh icon-spin"></i><br/>Cargando cursos disponibles...');
  jQuery.ajax({
    type: 'POST',
    url: sgcajax.ajaxurl,
    data: {
      action: 'sgcinsc_displaycursos',
      nivel: curso
    },
    success: function(data, textStatus, XMLHttpRequest) {
      ajaxPlace.empty().append(data);
      $('input[name="aclecurso[]"]').rules('add', {
        minlength: minacle,
        maxlength: maxacle,
        required: true,
        messages: {
          required: 'Necesitas inscribir un A.C.L.E.',
          minlength: 'Necesitas inscribir el mínimo de A.C.L.E.s para tu curso',
          maxlength: 'No puedes inscribir más A.C.L.E.s según tu curso'
        }
      });
      $('#sgcinsc_form span.cursel').empty().append(sgcinsc_niceCurso(curso));
      if(minacle == 2) {
        $('#sgcinsc_form p.maxcursos').empty().append('Usted debe inscribir <strong>2 curso(s)</strong> haciendo click sobre la casilla. NO se pueden escoger 2 A.C.L.E. que se encuentren en el mismo horario, ni más de 2 acles.');  
      } else {
        $('#sgcinsc_form p.maxcursos').empty().append('Usted debe inscribir <strong>1 curso(s)</strong> haciendo click sobre la casilla. NO se puede escoger más de 1 acles.');
      }
      
      //Chequeo las que se chequearon en otros pasos.
      if(checkedarray.length > 0) {
        jQuery.each(checkedarray, function(index, element) {
          var exselected = $('input.aclecheckbox[value="'+element+'"]');
          exselected.prop('checked', true);
          $('div#curso-'+ element).addClass('selected');
          });
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      ajaxPlace.empty().append('<h1 class="error">' + errorThrown + '</h1>');
    }
  });
}

//Muestra los cursos disponibles para cada nivel
function sgcinsc_renderAcleInfo(acleids, container) {    
  container.append('Cargando cursos disponibles...');
  jQuery.ajax({
    type: 'POST',
    url: sgcajax.ajaxurl,
    data: {
      action: 'sgcinsc_getacles',
      acles : acleids
    },
    success: function(data, textStatus, XMLHttpRequest) {
      container.empty().append(data);      
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      container.empty().append('<h1 class="error">' + errorThrown + '</h1>');
    }
  });
}

function sgcinsc_renderFinalInfo(data) {
  var datosalumno = $('#sgcinsc_form .datos-alumno');
  var datosapoderado = $('#sgcinsc_form .datos-apoderado');
  var datosacles = $('#sgcinsc_form .datos-acle');
  var acles;
  var appendstuffalumno = '<h3>Datos del alumno(a)</h3> <ul>' + 
                    '<li><span class="fieldcont">Nombre: </span>' + data[0].value + '</li>' +
                    '<li><span class="fieldcont">RUT: </span>' + data[1].value + '</li>' +
                    '<li><span class="fieldcont">Curso: </span>' + sgcinsc_niceCurso(data[2].value) + ' ' + data[3].value + '</li>';
    appendstuffalumno += '<li><span class="fieldcont">Seguro Médico: </span>' + sgcinsc_niceSeguro(data[6].value) + '</li>';                  
  var appendstuffapoderado = '<h3>Datos del apoderado(a)</h3> <ul>' + 
                    '<li><span class="fieldcont">Nombre: </span>' + data[7].value + '</li>' +
                    '<li><span class="fieldcont">RUT: </span>' + data[8].value + '</li>' +
                    '<li><span class="fieldcont">Email: </span>' + data[9].value + '</li>' +
                    '<li><span class="fieldcont">Teléfono: </span>' + data[10].value + '</li>' +
                    '<li><span class="fieldcont">Celular: </span>' + data[11].value + '</li>';                  
  datosalumno.empty().append(appendstuffalumno);
  datosapoderado.empty().append(appendstuffapoderado);
  if(typeof data[13] != 'undefined') {
    var value1 = data[12].value;
    var value2 = data[13].value;
    var acles = [value1, value2];
  } else {
    var value1 = data[12].value;
    acles = [value1];
  }
  sgcinsc_renderAcleInfo(acles, datosacles);
}

function countemptyacles(container, message) {
  $('div.message').remove();
  $(container).each(function(element) {
      var countacle = $('div.acleitem:visible', this).length;
      console.log('count:' + countacle);
      if(countacle == 0) {
        $(this).append('<div class="message">' + message + '</div>');
      }
  });
}

/* La siguiente instrucción extiende las capacidades de jquery.validate() para que
  admita el método RUT, por ejemplo:

$('form').validate({
  rules : { rut : { required:true, rut:true} } ,
  messages : { rut : { required:'Escriba el rut', rut:'Revise que esté bien escrito'} }
})
// Nota: el meesage:rut sobrescribe la definición del mensaje de más abajo
*/
// comentar si jquery.Validate no se está usando

jQuery.validator.addMethod("rut", function(value, element) { 
        return this.optional(element) || validaRut(value); 
}, "Revise el RUT, puede que esté mal escrito");

jQuery.validator.addMethod("rut2", function(value, element) { 
        return this.optional(element) || $.Rut.validar(value); 
}, "Revise el RUT, puede que esté mal escrito");

$(document).ready(function() {
	//$('#article-acleinscstep1 .step').hide();
  //$('#article-acleinscstep1 #sgcinsc_submit').hide();
  checkedarray = new Array();


  $('#otroseguro, #emailalumno').hide();

  //Paso 1 se muestra por defecto
  $('#article-acleinscstep1 .step-one').show();

  $('.rut-validate').Rut({
    validation:false
  });

$('#sgcinsc_form').validate(
{ 
  debug: false,
  errorPlacement: function(error, element) {
        if(element.is('input[name="aclecurso[]"]')) {
            error.appendTo('#ajaxErrorPlace');
        } else {            
            error.appendTo(element.parent('.controls'));
        }
      },  
  rules: {
    nombre_alumno: {
      minlength: 10,
      required: true
    },
    rut_alumno: {      
      required:true,
      rut2: true
    },
    curso_alumno: {
      required: true      
    },
    letracurso: {
      required: true      
    },
    seguro_alumno_select: {
      required: true
    },
    seguro_alumno: {
      required: true      
    },
    nombre_apoderado: {
      minlength: 10,
      required: true
    },
    rut_apoderado: {
      rut2: true,
      required: true
    },
    email_apoderado: {
      minlength: 10,
      required: true,
      email:true
    },
    fono_apoderado: {
      minlength: 6,
      required: true
    },
    celu_apoderado: {
      minlength: 6,
      required: true
    },
    aclecurso: {
      required: true
    }    
  },
  messages: {    
    nombre_alumno: {
      required: 'Falta poner el nombre del alumno(a)',        
      minlength: 'El nombre es demasiado corto'
    },
    nombre_apoderado: {
      required: 'Falta poner el nombre del apoderado(a)',        
      minlength: 'El nombre es demasiado corto'
    },
    curso_alumno: 'Falta seleccionar curso alumno(a)',
    letracurso: 'Falta seleccionar la letra del curso del alumno(a)',
    email_apoderado: {
      required: 'Falta el email del alumno(a)',
      email: 'Por favor, escriba un email válido',
      minlength: 'El email parece ser demasiado corto'
    },
    seguro_alumno: {
      required: 'Por favor, indique el seguro médico del alumno(a)'
    },
    seguro_alumno_select: {
      required: 'Por favor, indique el seguro médico del alumno(a)'
    },
    rut_apoderado: {
      required: 'Falta el RUT del apoderado(a)'
    },
    rut_alumno: {
      required: 'Falta el RUT del alumno(a)'
    },    
    letracurso: 'Falta la letra del curso',
    celu_apoderado: {
      required: 'Falta el celular del apoderado(a)',
      minlength: 'El número es demasiado corto'
    },
    fono_apoderado: {
      required: 'Falta el teléfono fijo del apoderado(a)',
      minlength: 'El número es demasiado corto'
    },    
    confirmar_envio: {
      required: 'Por favor, confirme los datos para enviar la inscripción'
    }

  },
  highlight: function(element) {
    $(element).closest('.control-group').removeClass('success').addClass('error');
  },
  success: function(element) {
    element
      .text('Ok!').addClass('valid')
      .closest('.control-group').removeClass('error').addClass('success');
  }
});
  

  //Pasos  

  $('#sgcinsc_form').steps(
    {
    headerTag: 'h2.stepmark',
    bodyTag: 'fieldset',
    transitionEffect: 'slideLeft',    
    onStepChanging: function (event, currentIndex, newIndex)
                {                    
                    $("#sgcinsc_form").validate().settings.ignore = ":disabled,:hidden";
                    return $("#sgcinsc_form").valid();                                                                                                                     
                },
    onStepChanged: function(event, currentIndex, priorIndex) {                    
                    if(currentIndex == 2){
                      sgcinsc_renderAcles(cursel);                     
                    } else if(currentIndex == 3) {
                      formdata = $("#sgcinsc_form").serializeArray();                      
                      sgcinsc_renderFinalInfo(formdata);
                    }                    
                },
    onFinishing: function (event, currentIndex)
                {
                    $("#sgcinsc_form").validate().settings.ignore = ":disabled";
                    return $("#sgcinsc_form").valid();
                },
    onFinished: function (event, currentIndex)                
                    {                                                             
                      $("#sgcinsc_form").submit();                                       
                  },
    labels: {
            finish: 'Enviar Inscripción',
            next: 'Siguiente <i class="icon icon-chevron-right"></i>',
            previous: '<i class="icon icon-chevron-left"></i> Anterior',
            current: 'Paso actual:',
            pagination: 'Páginas',
            loading: 'Cargando',
            }
        }
    );

  //Mostrar casilla de otro cuando corresponda
  $('#sgcinsc_form select[name="seguro_alumno_select"]').on('change', function(){
    var selected = $('option:selected',this).attr('value');
    var trufield = $('#sgcinsc_form input[name="seguro_alumno"]');
    var otroseguro = $('#otroseguro');
      if(selected == 'otra'){
        otroseguro.show();
        trufield.attr('value', '');
      } else {        
        $('#sgcinsc_form input[name="seguro_alumno"]').attr('value', selected);
        otroseguro.hide();
      }

  });

  $('#sgcinsc_form .actions ul li a[href="#next"]').addClass('btn btn-success btn-large');
  $('#sgcinsc_form .actions ul li a[href="#previous"]').addClass('btn btn-success btn-large');
  $('#sgcinsc_form .actions ul li a[href="#finish"]').addClass('btn btn-danger btn-large');

	//Ajax request for showing available courses

  // 1. Guardar en algún lado la selección del curso.
  $('#sgcinsc_form select[name="curso_alumno"]').on('change', function(){
    cursel = $('option:selected', this).attr('value'); 

    //Vacío los cursos seleccionados si es que el apoderado cambia de curso.
    checkedarray = [];

    //Requerimientos de cursos mínimos y máximos
    if((cursel == 1) || (cursel == 2) || (cursel == 7) || (cursel == 8) || (cursel == 9) || (cursel == 10)) {
      minacle = 1;      
      maxacle = 1;          
    } else {
      minacle = 2;            
      maxacle = 2;
      }; 
      //Máximo igual para todos
      
  });

//aclechecks = $('#sgcinsc_form input.aclecheckbox'); 
//console.log(aclechecks);

  $('#sgcinsc_form #ajaxAclesPlace').on('click', 'input.aclecheckbox', function() {
       //Contar los chequeados y guardarlos en variables       
       $('.acleitemcurso').removeClass('selected');
      checkedarray = [];     
             

       //Revisa los chequeados en un mismo td
       if($(this).prop('checked') == true){
          var thistd = $(this).closest('div.curso');
          var notchecked = thistd.find('input.aclecheckbox:checked').not(this);
          $('div#curso-' + $(notchecked).attr('value')).removeClass('selected');
          notchecked.prop('checked', false);          
       }       

       $("#sgcinsc_form").validate();

       checkel = $('input.aclecheckbox:checked');       

       checkel.each(function(index) {
          //Poblar array para rellenar campos luego.          
          checkedarray.push($(this).attr('value'));          
          $('div#curso-'+ $(this).attr('value')).addClass('selected');          
          //Deschequear los que están en el mismo horario                   
       }); 

       //Avisar si tienes más de un curso seleccionado
          // if(checkel.length > maxacle) {
            
          // };      
       
       //No hay que dejar que pasen el máximo
  });
  // 2. Usar la variable para mostrar cursos disponibles.


	//Ajax request for storing user data


	//Generar certificado
  $('a#certinsc').on('click', function() {      
      var w = window.open('', "", "width=600, height=700, scrollbars=yes");
        //alert(ICJX_JXPath);      
      var html = $('#certificado').html();
    $(w.document.body).html(html);

  });

  //Limpiar filtro cacheado

  $('.filteracles select').prop('selectedIndex', 0);
  //Chequear días vacíos
  countemptyacles('.publicacles .dia', 'No hay A.C.L.E. para el día');
  countemptyacles('.publicacles .dia .horario', 'No hay A.C.L.E. para el horario');

  //Filtrar
  $('.filteracles select').on('change', function(event) {
      var filters = $('.filteracles select');
      var filteraction = $(this).data('filter');
      var acleitems = 'div.acleitem';
      var selectedvalue = $('option:selected', this).attr('value');
      
      var selectedcurso = $('.filteracles select[name="filtercurso"] option:selected').attr('value');
      var selectedarea = $('.filteracles select[name="aclesareas"] option:selected').attr('value');
      var selectedhorario = $('.filteracles select[name="acleshorario"] option:selected').attr('value');

      var filterstring = '[data-'+ filteraction + '~="' + selectedvalue + '"]';
      $(acleitems).show();
      //esconder todos los que no son
      //$(acleitems).not(filterstring).hide();
      //chequear si hay elementos filtrados previamente
      if(selectedcurso != 0) {
        $(acleitems).not('[data-curso~="'+selectedcurso+'"]').hide();
      }
      if(selectedarea != 0) {
        $(acleitems).not('[data-area~="'+selectedarea+'"]').hide();
      }
      if(selectedhorario != 0) {
        $(acleitems).not('[data-horario~="'+selectedhorario+'"]').hide();
      }

      countemptyacles('.publicacles .dia', 'No hay A.C.L.E. para el día');
      countemptyacles('.publicacles .dia .horario', 'No hay A.C.L.E. para el horario');

  });

  $('.filteracles .btn.clear').on('click', function(event) {
    $('.filteracles select').prop('selectedIndex', 0);
    $('div.acleitem').removeClass('filtered').show();
    countemptyacles('.publicacles .dia', 'No hay A.C.L.E. para el día');
    countemptyacles('.publicacles .dia .horario', 'No hay A.C.L.E. para el horario');
  })

    });