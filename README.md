# VShop Project Root

This project contains two separated modules for ease of development, management, and independent deployment:

## Structure

```text
vshop/
├── vshop-laravel/      # Main eCommerce Laravel PHP application (Backend & Frontend)
└── vshop-websocket/    # Standalone Node.js Socket.io WebSocket server (Live Chat)
```

---

## 1. Laravel Application (`vshop-laravel`)

This is the main eCommerce application built on Laravel.

- **Directory:** `./vshop-laravel`
- **Installation:**
  ```bash
  cd vshop-laravel
  composer install
  npm install
  npm run build
  ```
- **Configuration:** Copy `.env.example` to `.env` and set environment variables. Set `WEBSOCKET_URL` to point to the WebSocket server URL.
- **Deployment:** Deployed on standard PHP servers (like Hostinger Shared Hosting) by serving the `public/` directory.

---

## 2. WebSocket Server (`vshop-websocket`)

This is the standalone Node.js server that handles real-time live chat connections.

- **Directory:** `./vshop-websocket`
- **Installation:**
  ```bash
  cd vshop-websocket
  npm install
  ```
- **Configuration:** Copy `.env.example` to `.env` and configure `PORT` (default 3000) and `CORS_ORIGIN` (allowed frontend origin).
- **Deployment:** Deployed to a VPS or Node.js hosting platform (using PM2 or Supervisor). Cannot run on standard shared hosting.

---

## Local Development (Running Both Services)

To run both services concurrently on your local machine, run the start scripts from the root directory:

### Windows
```cmd
start.bat
```

### macOS/Linux
```bash
chmod +x start.sh
./start.sh
```

---

## Live Server Deployment (.env Configuration)

When deploying to the live server, the developer must manually create and configure the `.env` file in the `vshop-laravel` directory (as `.env` files are not pushed to Git for security).

Ensure the following variables are correctly set for the production environment:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-live-domain.com
ASSET_URL=https://your-live-domain.com

DB_DATABASE=your_live_database_name
DB_USERNAME=your_live_database_user
DB_PASSWORD=your_live_database_password
```
*(Replace the URL and DB credentials with the actual server details).*
