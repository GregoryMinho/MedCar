// socket-server.js

const { Server } = require("socket.io");
const http = require("http");
const mysql = require("mysql2/promise");

// === SUA CONFIGURAÇÃO DO BANCO ===
const pool = mysql.createPool({
  host: 'localhost',
  user: 'root',
  password: '', // coloque 'cimatec' se usar senha
  database: 'medcar_chat'
});

const server = http.createServer();
const io = new Server(server, {
  cors: {
    origin: "*",
  }
});

io.on("connection", (socket) => {
  console.log("Novo cliente conectado:", socket.id);

  socket.on("join_room", (room) => {
    socket.join(room);
    console.log(`Cliente ${socket.id} entrou na sala ${room}`);
  });

  socket.on("send_message", async (data) => {
    // Esperado: data = { room, sender, message }
    console.log(`Mensagem recebida: ${data.message}`);

    try {
      await pool.query(
        "INSERT INTO mensagens_chat (sala, remetente, mensagem) VALUES (?, ?, ?)",
        [data.room, data.sender, data.message]
      );
      io.to(data.room).emit("receive_message", data);
    } catch (err) {
      console.error("Erro ao salvar mensagem no banco:", err);
    }
  });

  socket.on("disconnect", () => {
    console.log("Cliente desconectado:", socket.id);
  });
});

server.listen(3001, () => {
  console.log("Servidor WebSocket rodando na porta 3001");
});
