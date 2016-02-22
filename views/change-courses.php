<?php 
/* 
Revisión de cursos

1. El usuario llega a través de una URL personalizada (codificada) desde la que puede cambiar sus cursos.
2. En dicha url están los datos de la inscripción previa y una tabla con los cursos disponibles (y las inscripciones premarcadas) que permiten modificar la inscripción.
3. Una vez que se marcan se confirma y la inscripción queda guardada con una señal de que fue corregida.
4. La URL tiene el ID y el hash. Primero busca el ID y si coincide con el Hash se procede a modificar.
*/
?>

<p><?php echo $_GET['ih'];?></p>
<p><?php echo $_GET['id'];?></p>