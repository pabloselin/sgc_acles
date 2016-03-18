<?php 
//pasos Inscripción

//Variable de modificación de inscripción
$modcond = false;

//Ver si inscripciones están abiertas
$options = get_option('sgcinsc_config_options');
$openinsc = $options['sgcinsc_open_insc'];
$stage = $options['sgcinsc_etapa_insc'];
//variable que regula si se puede o no uno inscribir

$insc = false;

if($openinsc == 1 || is_user_logged_in() ):

	if(isset($_GET['mod']) && $_GET['mod'] == 1 && isset($_GET['id']) && isset($_GET['ih']) ) {
	
	if(sgcinsc_validatehash($_GET['id'], $_GET['ih']) ) {
		//Hay que ver si el link de modificación es para etapa uno o dos
		if(sgcinsc_getinscstage($_GET['id']) == $stage) {
			$modcond = true;
			$data = sgcinsc_getinsc($_GET['id']);	
			$insc = true;
		} else {
			$modcond = false;
			$insc = false;
		}
		
		//var_dump($data);	
	}

	} else {

	$insc = true;

	}

endif;

if($openinsc == 1 && $insc == true || is_user_logged_in()):
?>	
		<div id="sgcinsc-acleinscstep1" class="acleinsc-step1">						
				<!--Lista de ACLES disponibles con filtros por curso y por horario-->
				<!--Lista de ACLES en formato Calendario-->
				<!--Lista de ACLES por áreas-->			
									
						<form id="sgcinsc_form" method="POST" action="" class="form-horizontal" data-mod="<?php echo $modcond;?>" data-id="<?php echo $_GET['id'];?>" data-stage="<?php echo $stage;?>">
						<!--campos escondidos de info-->
						<input name="modcond" id="modcond" type="text" class="hidden" value="<?php echo $modcond;?>"></input>
						<input name="inscid" id="inscid" type="text" class="hidden" value="<?php echo $_GET['id'];?>"></input>
						<input name="stage" id="stage" type="text" class="hidden" value="<?php echo $stage;?>"></input>

							<h2 class="stepmark">Datos del alumno(a) <i class="icon-chevron-right"></i></h2>
							<fieldset>							

							<div class="control-group">
								<legend>Datos del alumno(a)</legend>
								<label class="control-label" for="nombre_alumno">Nombre Alumno(a):</label>
								
								<div class="controls">									
									
									<?php if($modcond):?>
										
										<input class="input-xlarge" type="text" name="nombre_alumno" value="<?php echo $data[0]->nombre_alumno;?>"></input>

									<?php else:?>
										
										<input class="input-xlarge" type="text" name="nombre_alumno"></input>

									<?php endif;?>

								</div>
							</div>

							<div class="control-group">								
								<label class="control-label" for="rut_alumno">RUT Alumno(a):</label>
								
								<div class="controls">									
									<?php if($modcond):?>

									<input class="input-xlarge rut-validate" type="text" name="rut_alumno" placeholder="" value="<?php echo sgcinsc_nicerut($data[0]->rut_alumno);?>"></input>

									<?php else:?>

									<input class="input-xlarge rut-validate" type="text" name="rut_alumno" placeholder=""></input>

									<?php endif;?>

									<span class="help-block">Ejemplo: 17348573-5</span>
								</div>
							</div>

							<div class="control-group">								
								<label class="control-label" for="curso_alumno">Curso Alumno(a) <strong>2016</strong>:</label>
								
								<div class="controls">									
									<select name="curso_alumno">


										<option disabled <?php if(!$modcond):?> selected <?php endif;?> >Escoja curso</option>
										<option value="1" <?php if($modcond && $data[0]->curso_alumno == 1):?> selected  <?php endif;?> >1° Básico</option>
										<option value="2" <?php if($modcond && $data[0]->curso_alumno == 2):?> selected <?php endif;?> >2° Básico</option>
										<option value="3" <?php if($modcond && $data[0]->curso_alumno == 3):?> selected <?php endif;?> >3° Básico</option>
										<option value="4" <?php if($modcond && $data[0]->curso_alumno == 4):?> selected <?php endif;?> >4° Básico</option>
										<option value="5" <?php if($modcond && $data[0]->curso_alumno == 5):?> selected <?php endif;?> >5° Básico</option>
										<option value="6" <?php if($modcond && $data[0]->curso_alumno == 6):?> selected <?php endif;?> >6° Básico</option>
										<option value="7" <?php if($modcond && $data[0]->curso_alumno == 7):?> selected <?php endif;?> >7° Básico</option>
										<option value="8" <?php if($modcond && $data[0]->curso_alumno == 8):?> selected <?php endif;?> >8° Básico</option>
										<option value="9" <?php if($modcond && $data[0]->curso_alumno == 9):?> selected <?php endif;?> >I° Medio</option>
										<option value="10" <?php if($modcond && $data[0]->curso_alumno == 10):?> selected <?php endif;?> >II° Medio</option>

										<?php if($modcond):?>

											<script>
												var cursel = <?php echo $data[0]->curso_alumno;?>
												//Requerimientos de cursos mínimos y máximos
											    if((cursel == 1) || (cursel == 2) || (cursel == 7) || (cursel == 8) || (cursel == 9) || (cursel == 10)) {
											      minacle = 1;      
											      maxacle = 3;          
											    } else {
											      minacle = 1;            
											      maxacle = 3;
											      }; 
											      //Máximo igual para todos
											</script>

										<?php endif;?>

									</select>
								</div>
							</div>

							<div class="control-group">								
								<label class="control-label" for="letracurso">Letra curso:</label>
								
								<div class="controls">									
									<select name="letracurso">

										<option disabled <?php if(!$modcond):?> selected <?php endif;?> >Escoja letra de curso</option>
										
										<option value="A" <?php if($modcond && $data[0]->letracurso_alumno == 'A'):?> selected <?php endif;?> >A</option>
										<option value="B" <?php if($modcond && $data[0]->letracurso_alumno == 'B'):?> selected <?php endif;?> >B</option>

									</select>		
								</div>
							</div>

							<div class="control-group">								
								<label class="control-label" for="seguro_alumno_select">Seguro Escolar:</label>
								
								<div class="controls">									
									<select name="seguro_alumno_select">

									<?php 
									$clinicas = array('alemana', 'santamaria', 'indisa', 'uc', 'davila');
									$otraclinica = false;

									if($modcond && !in_array($data[0]->seguro_escolar, $clinicas)) {
										$otraclinica = true;
									}



									?>
										
										<option <?php if(!$modcond):?> selected <?php endif;?> value="">Escoja seguro escolar</option>
										<option value="alemana" <?php if($modcond && $data[0]->seguro_escolar == 'alemana'):?> selected <?php endif;?> >Clínica Alemana</option>
										<option value="santamaria" <?php if($modcond && $data[0]->seguro_escolar == 'santamaria'):?> selected <?php endif;?> >Clínica Santa María</option>
										<option value="indisa" <?php if($modcond && $data[0]->seguro_escolar == 'indisa'):?> selected <?php endif;?> >Clínica Indisa</option>
										<option value="uc" <?php if($modcond && $data[0]->seguro_escolar == 'uc'):?> selected <?php endif;?> >Clínica Universidad Católica</option>
										<option value="davila" <?php if($modcond && $data[0]->seguro_escolar == 'davila'):?> selected <?php endif;?> >Clínica Dávila</option>
										<option value="otra" <?php if($otraclinica == true):?> selected <?php endif;?> >Otro (¿Cuál?)</option>

									</select>									
								</div>								
							</div>

							<div class="control-group" id="otroseguro">								
								<label class="control-label" for="seguro_alumno">Otro</label>
								
								<div class="controls">									
									<?php if($modcond):?>

										<input class="input-xlarge" type="text" name="seguro_alumno" value="<?php echo $data[0]->seguro_escolar;?>"></input>

									<?php else:?>

										<input class="input-xlarge" type="text" name="seguro_alumno"></input>

									<?php endif;?>
								</div>								
							</div>


							</fieldset>	
								
							<h2 class="stepmark">Datos del Apoderado(a) <i class="icon-chevron-right"></i></h2>	
							<fieldset>
								<div class="control-group">
									<legend><strong>Paso 2:</strong> Datos del apoderado(a)</legend>
									<label class="control-label" for="nombre_apoderado">Nombre apoderado(a):</label>
								
									<div class="controls">									

										<?php if($modcond):?>
										
											<input class="input-xlarge" type="text" name="nombre_apoderado" value="<?php echo $data[0]->nombre_apoderado;?>" ></input>

										<?php else:?>

											<input class="input-xlarge" type="text" name="nombre_apoderado"></input>

										<?php endif;?>

									</div>
								</div>

								<div class="control-group">									
									<label class="control-label" for="rut_apoderado">RUT apoderado(a):</label>
								
									<div class="controls">									
										
									<?php if($modcond):?>
										
										<input class="input-xlarge rut-validate" type="text" name="rut_apoderado" value="<?php echo sgcinsc_nicerut($data[0]->rut_apoderado);?>"></input>

									<?php else:?>

										<input class="input-xlarge rut-validate" type="text" name="rut_apoderado"></input>

									<?php endif;?>
										<span class="help-block">Ejemplo: 17348573-5</span>
									</div>
								</div>

								<div class="control-group">
									
									<label class="control-label" for="email_apoderado">Email apoderado(a):</label>
									
								
									<div class="controls">									
									<?php if($modcond):?>
										
										<input class="input-xlarge" type="text" name="email_apoderado" value="<?php echo $data[0]->email_apoderado;?>" ></input>

									<?php else:?>

										<input class="input-xlarge" type="text" name="email_apoderado"></input>

									<?php endif;?>

										<span class="help-block">Ejemplo: apoderado@gmail.com</span>
									</div>
								</div>

								<div class="control-group">									
									<label class="control-label" for="fono_apoderado">Teléfono fijo apoderado(a): <br><span class="badge">opcional</span></label>
								
									<div class="controls">									
										<div class="input-prepend">
										<span class="add-on">+56 2</span>
										<?php if($modcond):?>

											<input class="input-xlarge" type="text" name="fono_apoderado" value="<?php echo $data[0]->redfija_apoderado;?>"></input>

										<?php else:?>

											<input class="input-xlarge" type="text" name="fono_apoderado"></input>

										<?php endif;?>
										</div>
										<span class="help-block">Ejemplo: 27279199</span>
									</div>
								</div>

								<div class="control-group">									
									<label class="control-label" for="celu_apoderado">Teléfono celular apoderado(a):</label>
								
									<div class="controls">									
										<div class="input-prepend">
											<span class="add-on">+56 9</span>

											<?php if($modcond):?>

												<input class="input-xlarge" type="text" name="celu_apoderado" value="<?php echo $data[0]->celu_apoderado;?>" ></input>

											<?php else:?>

												<input class="input-xlarge" type="text" name="celu_apoderado"></input>

											<?php endif;?>
										</div>
										<span class="help-block">Ejemplo: 99858456</span>
									</div>
								</div>
							</fieldset>							
								
							<?php wp_nonce_field('submit_stepone', 'sgcinsc_nonce');?>
							

							<h2 class="stepmark">Selección de A.C.L.E. <i class="icon-chevron-right"></i></h2>
							<fieldset>								
									<legend>Selección de  A.C.L.E.</legend>
									<!--hacer llamada via ajax que muestre solo los cursos disponibles-->									

											
									<div class="well">
										<p>
										<i style="font-size:32px;" class="icon icon-info-sign"></i>
										</p>

										<p>Se muestran sólo los cursos disponibles para: <strong><span class="cursel"></span></strong> (seleccionado en el paso 1)</p>
										<p class="maxcursos"></p>										
									</div>

									<div id="ajaxErrorPlace">

									</div>

									<div id="ajaxAclesPlace">
									</div>									


							</fieldset>		

							<h2 class="stepmark">Confirmación</h2>					
							<fieldset>
								<legend>Confirmación de Inscripción ACLE</legend>

								<i style="font-size:32px" class="icon icon-info-sign"></i>
								<p>Antes de enviar por favor revise su información. Si escribió mal algún dato puede volver atrás y corregir la información.</p>
								
								
								<div class="datos-alumno well">
								</div>

								<div class="datos-apoderado well">
								</div>

								<div class="datos-acle well">
								</div>

								<?php if($stage > 1):?>

								<div class="control-group alert alert-info">
								
									<p>Una vez inscrita la/s ACLE adicional/es, el/la alumno/a tiene el deber de asistir y  responder a las exigencias planteadas en la/s ACLE, según señala el Reglamento de Actividades Curriculares de Libre Elección. En el caso de no asistir, el/la alumno/a obtendrá la nota mínima (2.0) por asistencia, la que será registrada en el sector de aprendizaje afín a la ACLE elegida</p>

									<label for="acepta_terminos" class="control-label final-control-label">Acepto <i class="icon icon-arrow-right"></i></label>
							
										<div class="controls finalcheckbox">
										<input type="checkbox" class="input-xlarge" name="acepta_terminos"></div>
									</div>								
								
								<?php endif;?>

							</fieldset>

							

						</form>									
			</div>

	<?php else:
	?>

	<div class="alert alert-warning">
		
		<?php if($_GET['mod']):?>
			<h2>Modificación de Inscripciones para la <?php echo sgcinsc_getinscstage($_GET['id']);?>ª etapa cerradas</h2>
		<?php else:?>
			<h2>Inscripciones Cerradas</h2>
		<?php endif;?>

		<p>Consultas a <a href="mailto:<?php echo SGCINSC_MAILINSC;?>"><?php echo SGCINSC_MAILINSC;?></a></p>

	</div>

	<?php
		endif;
		//Fin comprobador de inscripciones si están abiertas o cerradas
	?>