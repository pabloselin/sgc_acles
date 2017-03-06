/*
Funciones AJAX para ACLES
 */

//Muestra los cursos disponibles para cada nivel
function sgcinsc_renderAcles(curso, rut, modcond, inscparam, stage) {
  console.log('Stage:' + stage);
  inscparam = typeof idinsc !== 'undefined' ? idinsc : 0;
  var ajaxPlace = $('#ajaxAclesPlace');

  var nicecurso = sgcinsc_niceCurso(curso);
  ajaxPlace.empty().append('<i class="icon-refresh icon-spin"></i><br/>Cargando cursos disponibles...');
  jQuery.ajax({
    type: 'POST',
    url: sgcajax.ajaxurl,
    data: {
      action: 'sgcinsc_displaycursos',
      nivel: curso,
      rutalumno: rut,
      mod: modcond,
      idinsc: idinsc
    },
    success: function(data, textStatus, XMLHttpRequest) {
      ajaxPlace.empty().append(data);
      $('input[name="aclecurso[]"]').rules('add', {
        minlength: minacle,
        maxlength: maxacle,
        required: true,
        messages: {
          required: 'Necesitas inscribir A.C.L.E.s',
          minlength: 'Necesitas inscribir tu segundo A.C.L.E.',
          maxlength: 'Revisa tu selección. Sólo puedes inscribir ' + maxacle + ' A.C.L.E.'
        }

      });
      $('#sgcinsc_form span.cursel').empty().append(nicecurso);

       if(modcond == true) {
        //Requerimientos de cursos mínimos y máximos
        cursel = curso;
        if(stage == 1) {
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
       
      }

      if(minacle == 2) {
        $('#sgcinsc_form p.maxcursos').empty().append('Usted debe inscribir <strong>' + minacle + ' ACLE</strong>');  
      } else if(minacle !== maxacle) {
        $('#sgcinsc_form p.maxcursos').empty().append('Usted debe inscribir entre <strong>' + minacle + ' ACLE</strong> y <strong>' + maxacle + ' ACLE</strong>');
      } else {
        $('#sgcinsc_form p.maxcursos').empty().append('Usted debe inscribir <strong>' + minacle + ' ACLE</strong>');
      }
      
      //Chequeo las que se chequearon en otros pasos.
      if(checkedarray.length > 0) {
        jQuery.each(checkedarray, function(index, element) {
          var exselected = $('input.aclecheckbox[value="'+element+'"]');
          exselected.prop('checked', true);
          $('div#curso-'+ element).addClass('selected');
          });
      }

      var preinsc = $('#ajaxAclesPlace p.oldacle');
      if(preinsc) {
        preinsc.each(function(idx, obj) {
          
          var preinscid = $(obj).data('id');
          var preinsc = $('.acleitemcurso[data-id="' + preinscid + '"]');

          $('span.aclename', preinsc).after('<span class="in">inscrito</span>');
          preinsc.addClass('preinsc');

          if(checkedarray == 0) {
            preinsc.addClass('selected');
            $('input', preinsc).prop('checked', true);  
          }
            

        });
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      ajaxPlace.empty().append('<h1 class="error">ERROR: ' + errorThrown + '</h1><p>Pruebe a volver al paso 1 y luego a éste.</p>');
    }
  });
}

//Muestra los cursos disponibles para cada nivel
function sgcinsc_renderAcleInfo(acleids, container) {    
  container.append('<p>Cargando...</p>');
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
  //console.log(data);
  var datalen = data.length;
  var dataObj = {};

  var aclesinscs = [];

  for(i=0; i < datalen; i++) {
    if(data[i].name == 'aclecurso[]') {

      aclesinscs.push(data[i].value);

    } else {

      dataObj[data[i].name] = data[i].value;  

    }
  }

  

  var datosalumno = $('#sgcinsc_form .datos-alumno');
  var datosapoderado = $('#sgcinsc_form .datos-apoderado');
  var datosacles = $('#sgcinsc_form .datos-acle');
  var acles;
  var appendstuffalumno = '<h3>Datos del alumno(a)</h3> <ul>' + 
                    '<li><span class="fieldcont">Nombre: </span>' + dataObj['nombre_alumno'] + '</li>' +
                    '<li><span class="fieldcont">RUT: </span>' + dataObj['rut_alumno'] + '</li>' +
                    '<li><span class="fieldcont">Curso: </span>' + sgcinsc_niceCurso(dataObj['curso_alumno']) + ' ' + dataObj['letracurso'] + '</li>';
    appendstuffalumno += '<li><span class="fieldcont">Seguro Médico: </span>' + sgcinsc_niceSeguro(dataObj['seguro_alumno']) + '</li>';                  
  var appendstuffapoderado = '<h3>Datos del apoderado(a)</h3> <ul>' + 
                    '<li><span class="fieldcont">Nombre: </span>' + dataObj['nombre_apoderado'] + '</li>' +
                    '<li><span class="fieldcont">RUT: </span>' + dataObj['rut_apoderado'] + '</li>' +
                    '<li><span class="fieldcont">Email: </span>' + dataObj['email_apoderado'] + '</li>' +
                    '<li><span class="fieldcont">Teléfono: +56 2 </span>' + dataObj['fono_apoderado'] + '</li>' +
                    '<li><span class="fieldcont">Celular: +56 9 </span>' + dataObj['celu_apoderado'] + '</li>';                  
  datosalumno.empty().append(appendstuffalumno);
  datosapoderado.empty().append(appendstuffapoderado);
  
  sgcinsc_renderAcleInfo(aclesinscs, datosacles);
}

function countemptyacles(container, message) {
  $('.noacles').show();
  $(container).each(function(element) {
      var countacle = $('div.acleitem:visible', this).length;
      if(countacle == 0) {
        $(this).hide().addClass('noacles');
      }
  });
}

function sgc_getprevinsc(RUT) {
  //Obtiene los IDs de las inscripciones previas de un alumno por su RUT
  jQuery.ajax({
    type: 'POST',
    url: sgcajax.ajaxurl,
    data: {
      action: 'sgcinsc_getprevinsc',
      rut: RUT
    },
    success: function(data, textStatus, XMLHttpRequest) {
      console.log(data);
    }, 
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
    }
  })
}

function sgc_getprevinscid(idinsc) {
  //Obtiene los IDs de las inscripciones previas de un alumno por su RUT
  jQuery.ajax({
    type: 'POST',
    url: sgcajax.ajaxurl,
    data: {
      action: 'sgcinsc_getprevinscid',
      id: idinsc
    },
    success: function(data, textStatus, XMLHttpRequest) {
      console.log(data);
    }, 
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
    }
  })
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

jQuery.validator.addMethod("rut2", function(value, element) { 
        return this.optional(element) || $.Rut.validar(value); 
}, "Revise el RUT, puede que esté mal escrito");

//Validator para emails
$.validator.addMethod("customemail", 
    function(value, element) {
        return /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i.test(value);
    }, 
    "Su email no parece ser válido"
);