<script src="https://cdn.socket.io/4.5.0/socket.io.min.js"></script>
<script>
  const socket = io("http://localhost:3001");

  const sala = "empresa_123"; // Exemplo: baseado no ID da empresa
  socket.emit("join_room", sala);

  function enviarMensagem() {
    const msg = document.getElementById("mensagem").value;
    socket.emit("send_message", {
      room: sala,
      message: msg,
      sender: "cliente", // ou empresa
      timestamp: new Date()
    });
  }

  socket.on("receive_message", (data) => {
    const chat = document.getElementById("chat");
    chat.innerHTML += `<div><strong>${data.sender}:</strong> ${data.message}</div>`;
  });
</script>
