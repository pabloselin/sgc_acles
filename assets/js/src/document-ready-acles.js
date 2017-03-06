$(document).ready(function() {
	//$('#article-acleinscstep1 .step').hide();
  //$('#article-acleinscstep1 #sgcinsc_submit').hide();
  checkedarray = new Array();
  modcond = $('form#sgcinsc_form').data('mod');
  idinsc = $('form#sgcinsc_form').data('id');
  stage = $('form#sgcinsc_form').data('stage');

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
        } else if(element.is('input[name="celu_apoderado"]') || element.is('input[name="fono_apoderado"]') ) {
          error.appendTo(element.closest('.controls'));
        }
        else {            
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
      required: {
        depends:function(){
                    $(this).val($.trim($(this).val()));
                        return true;
                      }
                  },
      customemail:true
    },
    fono_apoderado: {
      minlength: 8,
      maxlength: 8
    },
    celu_apoderado: {
      minlength: 8,
      maxlength: 8,
      required: true
    },
    acepta_terminos: {
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
      minlength: 'El número es demasiado corto, deben ser 8 dígitos',
      maxlength: 'El número es demasiado largo, deben ser 8 dígitos'
    },
    fono_apoderado: {
      required: 'Falta el teléfono fijo del apoderado(a)',
      minlength: 'El número es demasiado corto, deben ser 8 dígitos',
      maxlength: 'El número es demasiado largo, deben ser 8 dígitos'
    },
    acepta_terminos: {
      required: 'Debe aceptar los términos para inscribir'
    },    
    confirmar_envio: {
      required: 'Por favor, confirme los datos para enviar la inscripción'
    }

  },
  highlight: function(element) {
    $(element).closest('.control-group').removeClass('success').addClass('error');
  },
  success: function(element) {
    if(element.is('label[for="rut_alumno"]') || element.is('label[for="rut_apoderado"]')) {
      var curinput = $('input[name="' + element.attr('for') + '"]');
      console.log(curinput.val());
      var elval = $.Rut.formatear(curinput.val(), true);
      curinput.val(elval);
    };
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
                    if(currentIndex == 1) {
                      alumrut = $('#sgcinsc_form input[name="rut_alumno"]').val();
                      console.log(parseInt(alumrut));
                    }                    
                    else if(currentIndex == 2){
                      sgcinsc_renderAcles(cursel, alumrut, modcond, idinsc, stage);                     
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

  //Mostrar el seguro cuando modcond esté presente
  if(modcond == true && $('#sgcinsc_form select[name="seguro_alumno_select"] option[value="otra"]').prop('selected') == true ) {
      $('#sgcinsc_form select[name="seguro_alumno_select"] option[value="otra"]').prop('selected', true);
      $('#sgcinsc_form #otroseguro').show(); 
  }

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
   if(stage == 1) {
      //Requerimientos de cursos mínimos y máximos
      if((cursel == 1) || (cursel == 2) || (cursel == 7) || (cursel == 8) || (cursel == 9) || (cursel == 10)) {
        minacle = 1;      
        maxacle = 1;          
      } else {
        minacle = 2;            
        maxacle = 2;
        }; 
        //Máximo igual para todos
    } else {
        minacle = 1;
        maxacle = 3;
    }
      
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

  var defaultcurso = 'todos los cursos';
  var alertbox = $('div.alertacle');
  var defaultarea = 'todas las areas';
  var defaulthorario = 'todos los horarios';

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
        $('.tipcurso', alertbox).html($('.filteracles select[name="filtercurso"] option:selected').text());
      } else {
        $('.tipcurso', alertbox).html(defaultcurso);
      }
      if(selectedarea != 0) {
        $(acleitems).not('[data-area~="'+selectedarea+'"]').hide();
        $('.tiparea', alertbox).html('del área ' + $('.filteracles select[name="aclesareas"] option:selected').text());
      } else {
        $('.tiparea', alertbox).html(defaultarea);
      }
      if(selectedhorario != 0) {
        $(acleitems).not('[data-horario~="'+selectedhorario+'"]').hide();
        $('.tiphorario', alertbox).html('en horario ' + $('.filteracles select[name="acleshorario"] option:selected').text());
      } else {
        $('.tiphorario', alertbox).html(defaulthorario);
      }

      alertbox.addClass('alert-success');

      countemptyacles('.publicacles .dia', 'No hay A.C.L.E. para el día');
      countemptyacles('.publicacles .dia .horario', 'No hay A.C.L.E. para el horario');

  });

  $('.filteracles .btn.clear').on('click', function(event) {

    $('.filteracles select').prop('selectedIndex', 0);
    $('div.acleitem').removeClass('filtered').show();
    countemptyacles('.publicacles .dia', 'No hay A.C.L.E. para el día');
    countemptyacles('.publicacles .dia .horario', 'No hay A.C.L.E. para el horario');
    $('.dia.noacles').removeClass('noacles');
    $('.tipcurso', alertbox).html(defaultcurso);
    $('.tiparea', alertbox).html(defaultarea);
    $('.tiphorario', alertbox).html(defaulthorario);

    alertbox.removeClass('alert-success');

  })

  $('a.populateacles').on('click', function() {
      var curso = $('reinfo').data('curso');
      var rut = $('reinfo').data('rut');
      sgcinsc_renderAcles(curso, rut, modcond, stage);
  });

    });