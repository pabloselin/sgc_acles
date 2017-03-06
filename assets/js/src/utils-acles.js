function sgcinsc_niceCurso(curso) {
  intcurso = parseInt(curso);
  switch (intcurso) {
    case 1:
      var nicecurso = '1° Básico';
    break;
    case 2:
      var nicecurso = '2° Básico';
    break;
    case 3:
      var nicecurso = '3° Básico';
    break;
    case 4:
      var nicecurso = '4° Básico';
    break;
    case 5:
      var nicecurso = '5° Básico';
    break;
    case 6:
      var nicecurso = '6° Básico';
    break;
    case 7:
      var nicecurso = '7° Básico';
    break;
    case 8:
      var nicecurso = '8° Básico';
    break;
    case 9:
      var nicecurso = 'I° Medio';
    break;
    case 10:
      var nicecurso = 'II° Medio';
    break;    
  }
  return nicecurso;
}


function sgcinsc_niceSeguro(seguro) {
  switch(seguro) {
    case 'alemana':
      var niceseguro = 'Clínica Alemana';
    break;
    case 'santamaria':
      var niceseguro = 'Clínica Santa María';
    break;
    case 'indisa':
      var niceseguro = 'Clínica Indisa';
    break;
    case 'uc':
      var niceseguro = 'Clínica Universidad Católica';
    break;
    case 'davila':
      var niceseguro = 'Clínica Dávila';
    break;
    default:
      var niceseguro = seguro;
    break;
  }
  return niceseguro;
}