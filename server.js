const http = require('http');
const server = http.createServer();
const { Server } = require("socket.io");
const io = new Server(server, {
  cors: {
    origin: "http://collectbooks.test"
  }
});

io.on('connection', (socket) => {
  console.log('a user connected');
  
  socket.on('message', (msg) => {
    io.emit('message', msg);  
  });

  socket.on('disconnect', () => {
    console.log('user disconnected');
  });
});

server.listen(3000, () => {
  console.log('listening on *:3000');
});
