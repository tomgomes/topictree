/*
Exemplo de uso
$("#input_text_nome").blur(function(){
    capitalizar(this);
});
*/

notCapitalize = ['do', 'de', 'da', 'dos'];

function capitalizar(input_text){
  var pieces = input_text.value.split(' '),
  capitalized = [];  
  pieces.forEach(function(each) {
    if (notCapitalize.indexOf(each) !== -1) {
      capitalized.push(each);
    } else {
      capitalized.push(upperFirstLetter(each));
    }
  });  
  input_text.value = capitalized.join(' ');                  
}

function upperFirstLetter(word) {
  return word.substring(0,1).toUpperCase() + word.substring(1, word.length);
}                
