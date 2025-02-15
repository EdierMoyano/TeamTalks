// Función para obtener la cantidad de usuarios
async function actualizarUsuarios() {
    try {
      const respuesta = await fetch('contador_usuarios.php'); // Ruta del archivo PHP
      const datos = await respuesta.json(); // Convertir respuesta a JSON
      document.getElementById('cantidad-usuarios').textContent = datos.total; // Mostrar cantidad
    } catch (error) {
      console.error('Error al obtener la cantidad de usuarios:', error);
      document.getElementById('cantidad-usuarios').textContent = 'Error';
    }
  }
  
  // Llamar a la función al cargar la página
  actualizarUsuarios();
  
  // Opcional: Actualizar automáticamente cada 10 segundos
  setInterval(actualizarUsuarios, 10000);
  