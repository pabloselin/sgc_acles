<?php 
//pasos Inscripción
?>	
		<div id="sgcinsc-acleinscstep1" class="acleinsc-step1">						
				<!--Lista de ACLES disponibles con filtros por curso y por horario-->
				<!--Lista de ACLES en formato Calendario-->
				<!--Lista de ACLES por áreas-->			
									
						<form id="sgcinsc_form" method="POST" action="" class="form-horizontal">
							<h2 class="stepmark">Datos del alumno(a) <i class="icon-chevron-right"></i></h2>
							<fieldset>							

							<div class="control-group">
								<legend>Datos del alumno(a)</legend>
								<label class="control-label" for="nombre_alumno">Nombre Alumno(a):</label>
								
								<div class="controls">									
									<input class="input-xlarge" type="text" name="nombre_alumno"></input>
								</div>
							</div>

							<div class="control-group">								
								<label class="control-label" for="rut_alumno">RUT Alumno(a):</label>
								
								<div class="controls">									
									<input class="input-xlarge rut-validate" type="text" name="rut_alumno" placeholder=""></input>
									<span class="help-block">Ejemplo: 17348573-5</span>
								</div>
							</div>

							<div class="control-group">								
								<label class="control-label" for="curso_alumno">Curso Alumno(a):</label>
								
								<div class="controls">									
									<select name="curso_alumno">
											<option disabled selected>Escoja curso</option>
											<option value="1">1° Básico</option>
											<option value="2">2° Básico</option>
											<option value="3">3° Básico</option>
											<option value="4">4° Básico</option>
											<option value="5">5° Básico</option>
											<option value="6">6° Básico</option>
											<option value="7">7° Básico</option>
											<option value="8">8° Básico</option>
											<option value="9">I° Medio</option>
											<option value="10">II° Medio</option>
									</select>
								</div>
							</div>

							<div class="control-group">								
								<label class="control-label" for="letracurso">Letra curso:</label>
								
								<div class="controls">									
									<select name="letracurso">
										<option disabled selected>Escoja letra de curso</option>
										<option value="A">A</option>
										<option value="B">B</option>
									</select>		
								</div>
							</div>

							<div class="control-group">								
								<label class="control-label" for="seguro_alumno_select">Seguro Escolar:</label>
								
								<div class="controls">									
									<select name="seguro_alumno_select">
										<option selected value="">Escoja seguro escolar</option>
										<option value="alemana">Clínica Alemana</option>
										<option value="santamaria">Clínica Santa María</option>
										<option value="indisa">Clínica Indisa</option>
										<option value="uc">Clínica Universidad Católica</option>
										<option value="davila">Clínica Dávila</option>
										<option value="otra">Otro (¿Cuál?)</option>
									</select>									
								</div>								
							</div>

							<div class="control-group" id="otroseguro">								
								<label class="control-label" for="seguro_alumno">Otro</label>
								
								<div class="controls">									
									<input class="input-xlarge" type="text" name="seguro_alumno"></input>
								</div>								
							</div>


							</fieldset>	
								
							<h2 class="stepmark">Datos del Apoderado(a) <i class="icon-chevron-right"></i></h2>	
							<fieldset>
								<div class="control-group">
									<legend><strong>Paso 2:</strong> Datos del apoderado(a)</legend>
									<label class="control-label" for="nombre_apoderado">Nombre apoderado(a):</label>
								
									<div class="controls">									
										<input class="input-xlarge" type="text" name="nombre_apoderado"></input>
									</div>
								</div>

								<div class="control-group">									
									<label class="control-label" for="rut_apoderado">RUT apoderado(a):</label>
								
									<div class="controls">									
										<input class="input-xlarge rut-validate" type="text" name="rut_apoderado"></input>
										<span class="help-block">Ejemplo: 17348573-5</span>
									</div>
								</div>

								<div class="control-group">
									
									<label class="control-label" for="email_apoderado">Email apoderado(a):</label>
									
								
									<div class="controls">									
										<input class="input-xlarge" type="text" name="email_apoderado"></input>
										<span class="help-block">Ejemplo: apoderado@gmail.com</span>
									</div>
								</div>

								<div class="control-group">									
									<label class="control-label" for="fono_apoderado">Teléfono fijo apoderado(a):</label>
								
									<div class="controls">									
										<input class="input-xlarge" type="text" name="fono_apoderado"></input>
										<span class="help-block">Ejemplo: 27279199</span>
									</div>
								</div>

								<div class="control-group">									
									<label class="control-label" for="celu_apoderado">Teléfono celular apoderado(a):</label>
								
									<div class="controls">									
										<div class="input-prepend">
											<span class="add-on">+56 9</span>
											<input class="input-xlarge" type="text" name="celu_apoderado"></input>
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
								<legend>Confirmación de Inscripción</legend>

								<i style="font-size:32px" class="icon icon-info-sign"></i>
								<p>Antes de enviar por favor revise su información. Si escribió mal algún dato puede volver atrás y corregir la información.</p>
								
								
								<div class="datos-alumno well">
								</div>

								<div class="datos-apoderado well">
								</div>

								<div class="datos-acle well">
								</div>

								<div class="control-group alert alert-info">
								
									<p>Una vez inscrita la/s ACLE adicional/es, el/la alumno/a tiene el deber de asistir y  responder a las exigencias planteadas en la/s ACLE, según señala el Reglamento de Actividades Curriculares de Libre Elección. En el caso de no asistir, el/la alumno/a obtendrá la nota mínima (2.0) por asistencia, la que será registrada en el sector de aprendizaje afín a la ACLE elegida</p>

									<label for="acepta_terminos" class="control-label">Acepto <i class="icon icon-arrow-right"></i></label>
							
										<div class="controls">
										<input type="checkbox" class="input-xlarge" name="acepta_terminos"></div>
									</div>								

							</fieldset>

							

						</form>									
			</div>