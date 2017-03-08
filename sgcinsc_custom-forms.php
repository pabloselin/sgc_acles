<?php 



function sgcinsc_verifydata($data) {
	global $wpdb, $post;
	// 1.Verificar que el nonce funcione

	if(!wp_verify_nonce( $_POST['sgcinsc_nonce'], 'submit_stepone' )) {
		echo 'El nonce es inválido';
		die();
	} else {
		//verificar que no estoy recargando url

		//Escapar todos los datos
		$data = esc_sql( $data );
		
		//Arreglar las variables
		$rut_apoderado = sgcinsc_processrut($data['rut_apoderado']);
		$rut_alumno = sgcinsc_processrut($data['rut_alumno']);

		//Verificar de que tipo de envío se trata
		if($data['modcond'] == 1) {

			sgcinsc_fixinsc($data);

		} else {

			sgcinsc_inscribe($data);
		

		}
		
	}	
}

function sgcinsc_inscribe($data) {
	/**
	 * Función general para inscripción de A.C.L.E.
	 */
	$options = get_option('sgcinsc_config_options');
	$stage = $options['sgcinsc_etapa_insc'];

	$rutal = sgcinsc_processrut($data['rut_alumno']);
	$mod = $data['modcond'];

	if($mod != 1) {
		//Se trata de una inscripción nueva

		 // 1. Verificar que los ruts no estén repetidos dependiendo de la etapa puede haber 1 o 2 etapas de inscripción, también puede ser una corrección a una inscripción anterior.
		
		if(sgcinsc_checkrep($rutal, 'rut_alumno')):
			
			//Verificar que los cursos no se hayan llenado mientras se producía la postulación.
			
			$preserialize = sgcinsc_serializeacles($data);
			
			//Verificar que haya llenado el mínimo de cursos requeridos		
			
			foreach($preserialize[0] as $precupo) {						
					$cupos = sgcinsc_cupos($precupo);					
					if( $cupos <= 0):
						$cuposurl = esc_url_raw( add_query_arg('excode', 3, get_permalink()) );
							wp_redirect($cuposurl, 303);
						die();
					endif;
			}


			//Verificar que no hayan cosas raras

			//el ingreso devuelve un ID de regalo
			$ID_inscripcion = sgcinsc_storeformdata($data);		
			
			//añado un validador de nonce par ala URL
			if($ID_inscripcion) {
				$nonce = wp_create_nonce( 'checkinsc' );
			
				$successarr = array(
								'excode' => 1,
								'idinsc' => $ID_inscripcion,
								'checkinsc' => $nonce
								);

				$finalurl = add_query_arg($successarr, get_permalink());
				
				sgcinsc_confirmail($ID_inscripcion);				

				wp_redirect($finalurl, 303);	

			} else {

				die();
			}
			

		else:

			$errorurl = esc_url_raw( add_query_arg('excode', 2, get_permalink()) );
			wp_redirect($errorurl, 303);
			die();

		endif;

	} else {
		//Se trata de una inscripción a modificar
		sgcinsc_modifydata($data);
	}

	// Chequea rut con etapa

}

function sgcinsc_fixinsc($data) {
	/**
	 * Función para la modificación de una inscripción previa
	 */
	global $wpdb, $table_name, $table2_name;


	$options = get_option('sgcinsc_config_options');
	$stage = $options['sgcinsc_etapa_insc'];

	//Nonce
	if(!wp_verify_nonce( $_POST['sgcinsc_nonce'], 'submit_stepone' )) {
		echo 'El nonce es inválido';
		die();
	} else {
		/**
		 * Estamos listos para empezar a ingresar una modificación
		 */
		 //var_dump($data);
		 $id = $data['inscid'];
		 
		 $oldinsc = sgcinsc_getinsc($id);		 
		 $oldacles = unserialize($oldinsc[0]->acles_inscritos);

		 $newacles = sgcinsc_serializeacles($data);
		 $newaclesk = $newacles[0];


		 //Comparar datos de acles con datos antiguos
		 $diff = sgcinsc_identical_values($newaclesk, $oldacles);
		 //var_dump($diff);
		 
		 if($diff == true) {
		 	//NO Hay diferencia de ACLES
		 	
		 	//Procesar el resto de la info
		 	sgcinsc_inscribe($data);

		 } else {
		 	//SI hay diferencia de ACLES
		 	
		 	//Buscar la diferencia

		 	//Curso-s que llega-n
		 	$arrdiff = array_diff($newaclesk, $oldacles);

		 	//Curso-s que se va-n
		 	$dumpacles = array_diff($oldacles, $newaclesk);

		 	//Cursos que quedan (para crear luego la cadena serializada)
			$stayacles = array_intersect($oldacles, $newaclesk);		 	

		 	//Buscar la entrada y el cupo correspondiente
		 	

		 	//Boto los cupos viejos
		 	
		 	foreach($dumpacles as $dumpacle) {
		 		$where = array(
		 			'id_inscripcion' => $id,
		 			'id_curso' => $dumpacle
		 			);
		 		$dump = $wpdb->delete($table2_name, $where);

		 	}

		 	//Nuevos ACLES
		 	foreach($arrdiff as $newacle) {
		 		//Revisar si quedan cupos en el curso
		 		if(sgcinsc_cupos($newacle) > 0){
		 			//Reingresar los cupos
		 			$insdata = array(
		 				'id_curso' => $newacle,
		 				'id_inscripcion' => $id,
		 				'etapa_insc' => $stage
		 				);
		 			$new = $wpdb->insert($table2_name, $insdata);

		 			//Añadir al array de stayacles
		 			$stayacles[] = $newacle;
		 		}
		 		
		 	}

		 	//Reingresar la inscripción
		 	$aclesdata = serialize($stayacles);

		 	$pmod = array(
		 				'fecha_modificacion' => current_time('mysql'),
		 				'cursos_anteriores' => serialize($oldacles)
		 			);

		 	$mod_data = serialize($pmod);

		 	$updatedata = array(
		 		'acles_inscritos' => $aclesdata,
		 		'mod_data' => $mod_data
		 		);

		 	$whereupdata = array(
		 		'id' => $id
		 		);

		 	$update = $wpdb->update($table_name, $updatedata, $whereupdata);
		 	//modifica los otros datos
		 	$updateotherdata = sgcinsc_modifydata($data, false);

		 	$nonce = wp_create_nonce( 'checkinsc' );
			
			$successarr = array(
							'excode' => 5,
							'idinsc' => $id,
							'checkinsc' => $nonce
							);
			$modacleurl = add_query_arg($successarr, get_permalink());

			//Envío el mail con los nuevos datos y los datos viejos de referencia.
			sgcinsc_modifymail($id, $oldinsc);

			wp_redirect($modacleurl, 303);
			die();

		 }
	}

}

function sgcinsc_storeformdata($data) {
	global $wpdb, $table_name, $table2_name;

	$options = get_option('sgcinsc_config_options');
	$stage = $options['sgcinsc_etapa_insc'];

	//Preparar info de cursos
	// $acles = array();
	// foreach($data as $key=>$dat) {		
	// 	if(in_string('aclecurso-', $key)):
	// 		$acles[] = $dat;
	// 	endif;
	// }

	$acles = sgcinsc_serializeacles($data);
	$aclestring = serialize($acles[0]);
	$rut_apoderado = sgcinsc_processrut($data['rut_apoderado']);
	$rut_alumno = sgcinsc_processrut($data['rut_alumno']);

	//chequea que no haya otra inscripción en el mismo momento para el mismo curso
	

	//chequea si hubo o no antes inscripción
	$prev = 0;

	$consprev = $wpdb->get_var("SELECT id FROM $table_name WHERE rut_alumno = $rut_alumno");
	if($consprev > 0) {
		$prev = 1;
	}

	//Genera el hash para la inscripción
	$hash = wp_hash( $rut_apoderado );
	//Inserta inscripción y detalles
	$insertinsc = $wpdb->insert($table_name, 
					array(
						'time' => current_time('mysql'),
						'rut_alumno' => $rut_alumno,
						'rut_apoderado' => $rut_apoderado,
						'nombre_alumno' => $data['nombre_alumno'],
						'nombre_apoderado' => $data['nombre_apoderado'],
						'curso_alumno' => $data['curso_alumno'],
						'letracurso_alumno' => $data['letracurso'],
						'email_apoderado' => $data['email_apoderado'],
						'redfija_apoderado' => $data['fono_apoderado'],
						'celu_apoderado' => $data['celu_apoderado'],
						'seguro_escolar' => $data['seguro_alumno'],
						'acles_inscritos' => $aclestring,
						'hash_inscripcion' => $hash
						)
					);	
	$lastid = $wpdb->insert_id;	

	//Inserta cupos son todas inscripciones de segunda etapa
	foreach($acles[0] as $acle){
		$wpdb->insert($table2_name,
			array(				
				'id_curso' => $acle,
				'id_inscripcion' => $lastid,
				'etapa_insc' => $stage				
				)
			);
	}
	return $lastid;
}

function sgcinsc_modifydata($data, $mail = true) {
	global $wpdb, $table_name, $table2_name;

	/**
	 * Modifica una inscripción
	 */

	$id = $data['inscid'];
	$oldrut = $wpdb->get_var("SELECT rut_alumno FROM $table_name WHERE id = $id" );
	$rutal = sgcinsc_processrut($data['rut_alumno']);
	$oldinsc = sgcinsc_getinsc($id);
	$allowed_date = sgcinsc_modrange(sgcinsc_getinscdate($id));
	$reprut = true;

	if($allowed_date == 1): 
		if($oldrut != $rutal) {
			$reprut = sgcinsc_checkrep($rutal, 'rut_alumno');
		}

		if($reprut) {
			//No se repite RUT, puedo seguir con la inscripción

			$mod_data = array(
				'fecha_modificacion' => current_time( 'mysql' )
				);

			$mdata = serialize($mod_data);

			$newdata = array(
							'rut_alumno' => sgcinsc_processrut($data['rut_alumno']),
							'rut_apoderado' => sgcinsc_processrut($data['rut_apoderado']),
							'nombre_alumno' => $data['nombre_alumno'],
							'nombre_apoderado' => $data['nombre_apoderado'],
							'curso_alumno' => $data['curso_alumno'],
							'letracurso_alumno' => $data['letracurso'],
							'email_apoderado' => $data['email_apoderado'],
							'redfija_apoderado' => $data['fono_apoderado'],
							'celu_apoderado' => $data['celu_apoderado'],
							'seguro_escolar' => $data['seguro_alumno'],
							'mod_data' => $mdata
				);

			$whereupdate = array( 'id'=> $id );

			$updateinsc = $wpdb->update($table_name, $newdata, $whereupdate );

			//Un nonce pa la url
			$nonce = wp_create_nonce('checkinsc');
			$successarr = array(
				'excode' => 4,
				'idinsc' => $id,
				'checkinsc' => $nonce
				);

			$modurl = add_query_arg($successarr, get_permalink());

			if($mail == true):
				//Envío mail de confirmación
				sgcinsc_modifymail($id, $oldinsc);
			endif;

			wp_redirect($modurl, 303);
			die();

		} else {
			
			$errorurl = esc_url_raw( add_query_arg('excode', 2, get_permalink()) );
			wp_redirect($errorurl, 303);
			die();

		}

	else:
		$errorurl_6 = esc_url_raw( add_query_arg('excode', 6, get_permalink()) );
		wp_redirect($errorurl_6, 303);
		xdebug_break();
		die();
		
	endif;

}

function sgcinsc_changeinsc($id, $hash) {
	//comprobar el hash con el ID
	if(sgcinsc_validatehash($id, $hash)) {
		return '<div id="sgcinsc_reinscripcion">' . sgcinsc_insctemplate($id) . '</div>';
	} else {
		return 'Hash inválido';
	}
}