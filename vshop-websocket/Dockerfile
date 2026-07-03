FROM node:24-alpine

WORKDIR /app

# Copy package files and lockfile (if exists)
COPY package.json package-lock.json* ./

# Install production dependencies only
RUN npm install --omit=dev

# Copy application source code
COPY . .

# Expose port (default 3000)
EXPOSE 3000

# Start application
CMD ["npm", "start"]
