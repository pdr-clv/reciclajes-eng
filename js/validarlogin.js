function validar() {
  var usuario, password;
  usuario = document.getElementById("usuario").value;
  password = document.getElementById("password").value;
  if (usuario === "" || password === "") {
    eModal.alert("<p class='text-danger'>Todos los campos deben estar rellenados</p>"," ");
    return false;
  } else if (usuario.length > 25 || password.length > 25) {
    eModal.alert("<p class='text-danger'>El valor máximo de caracteres a introducir es 25</p>"," ");
    return false;
  }
}

