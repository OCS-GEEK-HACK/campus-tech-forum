const express = require("express");
const http = require("http");
const socketIo = require("socket.io");

const app = express();
const server = http.createServer(app);
const io = socketIo(server, {
  cors: {
    origin: process.env.CORS_URL || "*", // CORS_URLを環境変数から読み込む
    methods: ["GET", "POST"],
  },
});

// JSONのパースを追加
app.use(express.json()); // これがないとPOSTリクエストのJSONがパースされない

app.post("/new_event", (req, res) => {
  // 受け取ったイベント情報を全クライアントに通知
  io.emit("new_event", req.body);

  res.status(200).json({ message: "イベント作成情報を送信しました。" });
});

app.post("/new_event_comment", (req, res) => {
  // 受け取った投稿を対象ルームのクライアントに送信
  const { event_id, ...rest } = req.body;

  // roomIdを使って限定的に送信（ルームごとの通知）
  io.to(event_id).emit("new_event_comment", rest);

  res.status(200).json({ message: "送信成功" });
});

app.post("/new_idea", (req, res) => {
  // 受け取ったイベント情報を全クライアントに通知
  io.emit("new_idea", req.body);

  res.status(200).json({ message: "イベント作成情報を送信しました。" });
});

app.post("/new_idea_comment", (req, res) => {
  // 受け取った投稿を対象ルームのクライアントに送信
  const { idea_id, ...rest } = req.body;

  // roomIdを使って限定的に送信（ルームごとの通知）
  io.to(idea_id).emit("new_idea_comment", rest);

  res.status(200).json({ message: "送信成功" });
});

// Socket.ioの接続イベント
io.on("connection", (socket) => {
  console.log("A user connected");
  socket.on("disconnect", () => {
    console.log("User disconnected");
  });
  socket.on("join_room", (roomId) => {
    console.log(`User joined room: ${roomId}`);
    socket.join(roomId);
  });

  socket.on("disconnect", () => {
    console.log("User disconnected");
  });
});

server.listen(3000, () => {
  console.log("Express and Socket.io server running on port 3000");
});
