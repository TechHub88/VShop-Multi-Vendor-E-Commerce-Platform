const express = require('express');
const app = express();
const http = require('http').createServer(app);
const io = require('socket.io')(http, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"]
    }
});

app.use(express.json());

// API endpoint for Laravel to publish events
app.post('/publish', (req, res) => {
    const { room, event, data } = req.body;
    if (!room || !event || !data) {
        return res.status(400).json({ error: 'Missing room, event, or data' });
    }
    io.to(room).emit(event, data);
    return res.json({ success: true });
});

io.on('connection', (socket) => {
    console.log('A user connected:', socket.id);

    socket.on('join', (room) => {
        socket.join(room);
        console.log(`Socket ${socket.id} joined room: ${room}`);
    });

    socket.on('disconnect', () => {
        console.log('User disconnected:', socket.id);
    });
});

const PORT = process.env.PORT || 3000;

http.on('error', (err) => {
    if (err.code === 'EADDRINUSE') {
        console.log(`Port ${PORT} is busy. Killing old process and retrying...`);
        const { execSync } = require('child_process');
        try {
            const result = execSync(`netstat -ano | findstr ":${PORT}" | findstr "LISTENING"`, { encoding: 'utf8' }).trim();
            const pid = result.split(/\s+/).pop();
            if (pid && pid !== '0') {
                execSync(`taskkill /PID ${pid} /F`, { encoding: 'utf8' });
                console.log(`Killed process ${pid}. Restarting...`);
                setTimeout(() => {
                    http.listen(PORT, '0.0.0.0', () => {
                        console.log(`Socket.io server running on port ${PORT}`);
                    });
                }, 1000);
            }
        } catch (killErr) {
            console.error('Could not free port:', killErr.message);
            process.exit(1);
        }
    } else {
        console.error('Server error:', err);
        process.exit(1);
    }
});

http.listen(PORT, '0.0.0.0', () => {
    console.log(`Socket.io server running on port ${PORT}`);
});
