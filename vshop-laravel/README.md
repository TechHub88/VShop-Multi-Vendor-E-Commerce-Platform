# VShop

VShop eCommerce Project.

---

### For testing Access Panels

**Buyer Panel**
- **URL**: [https://vshop.zelofx.com/vshoplogin](https://vshop.zelofx.com/vshoplogin)
- **Email**: customer1@gmail.com
- **Password**: 12341234

**Seller Panel**
- **URL**: [https://vshop.zelofx.com/seller/login](https://vshop.zelofx.com/seller/login)
- **Email**: sayaniroy.webmantra@gmail.com
- **Password**: abc123

**Admin Panel**
- **URL**: [https://vshop.zelofx.com/admin/login](https://vshop.zelofx.com/admin/login)

---

### Deployment & Running Instructions

This application requires two separate servers running concurrently: the main Laravel application (PHP) and the real-time Live Chat WebSocket server (Node.js).

**1. Setup & Installation**
- Run `composer install` to install PHP dependencies.
- Open the `websocket-server` directory and run `npm install` to install the Socket.io Node.js dependencies.
- Ensure your `.env` is configured correctly (including `OPENROUTER_API_KEY` for the AI chat).

**2. Running the Servers Locally (Development)**
We have included a `start.bat` script for convenience on Windows. Simply run:
```bash
.\start.bat
```
*This will automatically start the Node.js Socket.io server on Port 3000 in the background and the Laravel server on Port 8000.*

**3. Running on Production Server**
- Set up a daemon process (like Supervisor or PM2) to keep the Laravel Queue and the Node.js WebSocket Server running continuously in the background.
- Command for PM2 (Node.js): `pm2 start websocket-server/index.cjs --name "vshop-chat-server"`
- Command for Laravel (Nginx/Apache): Serve the `/public` folder normally.

> **Note to Developers:** The Socket.io backend is completely isolated from the Laravel PHP logic. It lives exclusively in the `websocket-server/` directory to prevent any environment confusion.

---

### Live Server Deployment (.env Configuration)

When deploying to the live server, you must manually create and configure the `.env` file in the `vshop-laravel` directory (since `.env` files are ignored in Git for security).

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
*(Replace the URL and DB credentials with the actual live server details).*
