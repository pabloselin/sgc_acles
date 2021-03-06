<?php
/*
Página de administración para ver:
	- Lista de alumnos inscritos por curso
	- Lista de inscritos en formato CSV o Excel
	- Borrar inscripciones defectuosas
	- Filtrar alumno por RUT u otro campo
*/

add_action( 'admin_menu', 'sgcinsc_putpage' );

function sgcinsc_aclesinsc() {
	//Agarrar todos los acles
	$args = array(
		'post_type' => 'sginsc_acles2014',
		'numberposts' => -1
		);
	$acles = get_posts($args);
	$year = date('Y');
	echo '<table class="widefat wp-list-table aclelist">';
	echo '<thead>';
	echo '<th>A.C.L.E.</th>';
	echo '<th>Cursos</th>';
	echo '<th>Día</th>';
	echo '<th>Horario</th>';
	echo '<th>Cupos totales</th>';
	echo '<th>Cupos disponibles</th>';
	echo '<th>Ver inscripciones 1ª Etapa</th>';
	echo '<th>Ver inscripciones 2ª Etapa</th>';
	echo '<th>Ver inscripciones Totales</th>';
	echo '</thead>';


	foreach($acles as $key=>$acle):		
		if($key % 2 == 0):			
			echo '<tr class="alternate">';
		else:
			echo '<tr>';
		endif;
		$nicecursos = array();
		$cursos = get_post_meta($acle->ID, 'sgcinsc_cursos', false);
		foreach($cursos as $curso) {
			$nicecursos[] = sgcinsc_nicecurso($curso);
		}
		$nicecursos = implode(', ', $nicecursos);
		$tableurl = esc_url( add_query_arg(array('acle'=> $acle->ID, 'etapa' => 1), admin_url('options-general.php?page=sgc_aclesadmin')) );
		$tableurl1 = esc_url( add_query_arg(array('acle'=> $acle->ID, 'etapa' => 2), admin_url('options-general.php?page=sgc_aclesadmin')) );
		$tableurl2 = esc_url( add_query_arg(array('acle'=> $acle->ID, 'etapa' => 'all'), admin_url('options-general.php?page=sgc_aclesadmin')) );
		echo '<td><strong>'.$acle->post_title.'</strong></td>';
		echo '<td>'.$nicecursos. '</td>';
		echo '<td>'.sgcinsc_nicedia(get_post_meta($acle->ID, 'sgcinsc_diaacle', true)).'</td>';
		echo '<td>'.sgcinsc_renderhorario(get_post_meta($acle->ID, 'sgcinsc_horaacle', true)). '</td>';
		echo '<td>'.get_post_meta($acle->ID, 'sgcinsc_cuposacle', true). '</td>';
		echo '<td>'.sgcinsc_cupos($acle->ID). '</td>';
		echo '<td><a class="button button-primary" href="'.$tableurl.'">Inscripciones 1º Etapa</a></td>';
		echo '<td><a class="button button-primary" href="'.$tableurl1.'">Inscripciones 2º Etapa</a></td>';
		echo '<td><a class="button button-primary" href="'.$tableurl2.'">Total</a></td>';
		echo '</tr>';
	endforeach;
	echo '</table>';
	//sgcinsc_aclestable($acles);

	$apoderados_url_1 = esc_url( add_query_arg(array('vista'=> 'apoderados', 'etapa' => 1), admin_url('options-general.php?page=sgc_aclesadmin')) );
	$apoderados_url_2 = esc_url( add_query_arg(array('vista'=> 'apoderados', 'etapa' => 2), admin_url('options-general.php?page=sgc_aclesadmin')) );

	echo '<p>Ver lista de apoderados: <a href="' . $apoderados_url_1 . '">Etapa 1</a> - <a href="' . $apoderados_url_2 . '">Etapa 2</a></p>';
}

function sgcinsc_inscporacle($acleid, $etapa) {
	global $wpdb, $table2_name, $table_name;
	if($etapa == 'all'):
		$infoinsc = $wpdb->get_results("SELECT id_inscripcion FROM $table2_name WHERE id_curso = $acleid");
	else:	
		$infoinsc = $wpdb->get_results("SELECT id_inscripcion FROM $table2_name WHERE id_curso = $acleid AND etapa_insc = $etapa");
	endif;
	//$infoinsc = $wpdb->get_results("SELECT id_inscripcion FROM $table2_name WHERE second_insc = $etapa");
	foreach($infoinsc as $key=>$infinsc){
		$inscritos = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $infinsc->id_inscripcion");
		if($key % 2 == 0):			
			echo '<tr class="alternate">';
		else:
			echo '<tr>';
		endif;
				foreach($inscritos as $inscrito):	

					$aclevar = $_GET['acle'];
					$stagevar = $_GET['etapa'];
					$modurl = esc_url( sgcinsc_url($inscrito->id) );

					echo '<td>'.$inscrito->id.'</td>';
					echo '<td>'.$inscrito->time.'</td>';
					echo '<td>'.sgcinsc_nicecurso($inscrito->curso_alumno).' ' . $inscrito->letracurso_alumno. '</td>';
					echo '<td>'.$inscrito->nombre_alumno.'</td>';					
					echo '<td>'.$inscrito->rut_alumno. '-' . dv($inscrito->rut_alumno) .'</td>';
					echo '<td>'.$inscrito->nombre_apoderado.'</td>';
					echo '<td>'.$inscrito->celu_apoderado.'</td>';
					echo '<td>'.$inscrito->redfija_apoderado.'</td>';
					echo '<td>'.$inscrito->email_apoderado.'</td>';
					echo '<td>'.sgcinsc_niceseguro($inscrito->seguro_escolar).'</td>';
					echo '<td>'.$inscrito->rut_apoderado. '-' . dv($inscrito->rut_apoderado) .'</td>';			
					echo '<td><a class="button-primary" id="modinsc" target="_blank" href="' . $modurl . '">Modificar inscripción</a></td>';
				endforeach;
			echo '</tr>';	
	}	
}

//Genera un csv para descarga con inscritos.
function sgcinsc_putcsv($acleid, $etapa) {
		global $wpdb, $table2_name, $table_name;
		if($etapa == 'all'):
			$infoinsc = $wpdb->get_results("SELECT id_inscripcion FROM $table2_name WHERE id_curso = $acleid");
		else:
			$infoinsc = $wpdb->get_results("SELECT id_inscripcion FROM $table2_name WHERE id_curso = $acleid AND etapa_insc = $etapa");
		endif;

		if($infoinsc) {
		// output headers so that the file is downloaded rather than displayed
		//header('Content-Type: text/csv; charset=utf-8');
		//header('Content-Disposition: attachment; filename=data.csv');

		$acle = get_post($acleid);
		$filename = $acle->post_name . '-' . $acle->ID .'-' .$etapa . '.csv';
		
		// create a file pointer connected to the output stream
		$output = fopen(SGCINSC_CSVPATH . $filename, 'w');

		// output the column headings
		fputcsv($output, array('A.C.L.E.', 'ID Inscripción', 'Fecha Inscripción', 'Curso del Alumno','Nombre Alumno', 'RUT del Alumno', 'Nombre del Apoderado', 'Celular del Apoderado', 'Teléfono de Red Fija del Apoderado', 'Email del apoderado', 'Seguro Escolar del Apoderado', 'RUT del Apoderado'), "\t");

		// fetch the data
		
		foreach($infoinsc as $key=>$infinsc) {
			$inscritos = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $infinsc->id_inscripcion");
			foreach($inscritos as $inscrito) {				
				//vaciar array
				$inscritoarr = array();
				$inscritoarr[] = $acle->post_title;
				$inscritoarr[] = $inscrito->id;
				$inscritoarr[] = $inscrito->time;
				$inscritoarr[] = sgcinsc_nicecurso($inscrito->curso_alumno) . ' ' . $inscrito->letracurso_alumno;
				$inscritoarr[] = $inscrito->nombre_alumno;
				$inscritoarr[] = $inscrito->rut_alumno . '-' . dv($inscrito->rut_alumno);
				$inscritoarr[] = $inscrito->nombre_apoderado;
				$inscritoarr[] = $inscrito->celu_apoderado;
				$inscritoarr[] = $inscrito->redfija_apoderado;
				$inscritoarr[] = $inscrito->email_apoderado;
				$inscritoarr[] = sgcinsc_niceseguro($inscrito->seguro_escolar);
				$inscritoarr[] = $inscrito->rut_apoderado . '-' . dv($inscrito->rut_apoderado);
				fputcsv($output, $inscritoarr, "\t");
			}
		}

		$csvfile = SGCINSC_CSVURL . $filename;
		return $csvfile;
		}
}

function sgcinsc_aclestable($acleid, $etapa) {
	
	$acle = get_post($acleid);
		echo '<h2>' . $acle->post_title. '</h2>';

		echo '<table class="acleinsc widefat wp-list-table">';			
			echo '<thead>';
			echo '<th>ID</th>';
			echo '<th>Fecha de Inscripción</th>';
			echo '<th>Curso alumno</th>';			
			echo '<th>Nombre Alumno</th>';
			echo '<th>RUT Alumno</th>';
			echo '<th>Nombre Apoderado</th>';
			echo '<th>Celular</th>';
			echo '<th>Red Fija</th>';
			echo '<th>E-Mail Apoderado</th>';
			echo '<th>Seguro escolar</th>';
			echo '<th>RUT Apoderado</th>';
			echo '<th>Acciones</th></thead>';
			
				sgcinsc_inscporacle($acle->ID, $etapa);			

		echo '</table>';

		$csv = sgcinsc_putcsv($acle->ID, $etapa);
		echo '<p><a class="button" href="'.$csv.'"> Descargar CSV de inscripciones de ' . $acle->post_title .'</a> </p>';
		echo '<p><a class="button button-primary" href="'.admin_url('options-general.php?page=sgc_aclesadmin' ).'"> Volver a la lista de cursos</a> </p>';
	
	
}

function sgcinsc_putpage() {
	add_options_page( __( 'Inscritos A.C.L.E.', 'sgc' ), __( 'Inscritos A.C.L.E.', 'sgc' ), 'manage_options', 'sgc_aclesadmin', 'sgcinsc_doadmin' );
}

function sgcinsc_doadmin() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('No tienes permisos suficientes para ver esta página.') );
	}
	global $wpdb;
	?>

	<script>
		jQuery(document).ready(function($) {
			$('#delinsc').on('click', function() {
				return confirm('¿Está seguro de querer borrar esta inscripción?');
			})
		});
	</script>

	<div class="wrap">
	<?php 
			$acleesc = $_GET['acle'];
			$delacle = $_GET['delid'];			
			$aclestage = $_GET['etapa'];
			$vista = $_GET['vista'];

			if($delacle) {
				$deleted = sgcinsc_deleteinsc($delacle, $acleesc);
				if($deleted) {

					echo '<div class="notice updated">Inscripción Nº ' . $delacle . ' borrada</div>';

				}
 			}

			if($aclestage == 'all'):
				$stagetitle = 'Inscripciones Totales (incluye ambas etapas)';
			elseif($aclestage == 2):
				$stagetitle = 'Segunda Etapa';
			elseif($aclestage == '1'):
				$stagetitle = 'Primera Etapa';
			endif;
	?>
		<h2>Inscripciones A.C.L.E.</h2>
		<h3><?php echo $stagetitle;?></h3>

		<?php 
			
			if($acleesc):
				sgcinsc_aclestable($acleesc, $aclestage);
			elseif($vista == 'apoderados' && isset($_GET['etapa']) ):
				sgcinsc_inscporapoderado($_GET['etapa']);
			else:
				sgcinsc_aclesinsc();
			endif;
		?>		
	</div>

	<?php
}

function sgcinsc_deletednotice() {
	?>
	<div class="notice success">Inscripción borrada</div>
	<?php
}

function sgcinsc_inscporapoderado($stage) {
	global $wpdb, $table_name, $table2_name;

	$inscripciones = $wpdb->get_results(
		"SELECT DISTINCT(id_inscripcion) FROM $table2_name WHERE etapa_insc = $stage"
	);
	
	echo '<h2>Inscripciones ACLE por Apoderado</h2>';

	echo '<table class="widefat wp-list-table aclelist">';
	echo '<thead>';
	echo '<th>ID inscripción</th>';
	echo '<th>RUT Apoderado</th>';
	echo '<th>Nombre Apoderado</th>';
	echo '<th>RUT Alumno</th>';
	echo '<th>Nombre Alumno</th>';
	echo '<th>ACLES inscritos</th>';
	echo '<th>Acciones</th>';
	echo '</thead>';

	foreach($inscripciones as $inscripcion) {
		
		$apoderado = sgcinsc_getinsc($inscripcion->id_inscripcion);
		$aclestring = array();

		$acles = unserialize($apoderado[0]->acles_inscritos);
		foreach($acles as $acle) {
			$aclestring[] = get_the_title( $acle ) . ' [ID:' . $acle . ']';
		}

		$aclestring_titles = implode('<br>', $aclestring);

		$modurl = esc_url( sgcinsc_url($apoderado[0]->id) );
		$delurl = esc_url( add_query_arg(array('vista'=> 'apoderados', 'delid' => $apoderado[0]->id), admin_url('options-general.php?page=sgc_aclesadmin')) );

		echo '<tr>';
		echo '<td>' . $apoderado[0]->id .'</td>';
		echo '<td>' . $apoderado[0]->rut_apoderado .'</td>';
		echo '<td>' . $apoderado[0]->nombre_apoderado .'</td>';
		echo '<td>' . $apoderado[0]->rut_alumno .'</td>';
		echo '<td>' . $apoderado[0]->nombre_alumno .'</td>';
		echo '<td>' . $aclestring_titles .'</td>';
		echo '<td> <a class="button button-primary" href="' . $delurl . '" id="delinsc">Borrar inscripción</a> <br> <a class="button primary" href="' . $modurl . '" id="modinsc">Modificar inscripción</a></td>';
		echo '</tr>';

	}

	echo '</table>';
}