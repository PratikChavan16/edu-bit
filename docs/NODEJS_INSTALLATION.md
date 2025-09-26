# Bitflow LMS - Node.js Installation Guide

## Prerequisites
Before setting up the React frontend, you need to install Node.js on your Windows system.

## Step 1: Download Node.js

1. **Visit the official Node.js website**: 
   - Go to https://nodejs.org/
   - You'll see two versions: LTS (Recommended for most users) and Current
   - **Download the LTS version** (currently v18 or v20) as it's more stable

2. **Choose the correct installer**:
   - For Windows 64-bit: `node-v[version]-x64.msi`
   - For Windows 32-bit: `node-v[version]-x86.msi`

## Step 2: Install Node.js

1. **Run the installer**:
   - Double-click the downloaded `.msi` file
   - Click "Next" through the setup wizard
   - Accept the license agreement
   - Choose installation directory (default is fine: `C:\Program Files\nodejs\`)
   - **IMPORTANT**: Make sure "Add to PATH" is checked
   - Click "Install"

2. **Verify installation**:
   - Open PowerShell as Administrator
   - Run these commands:
   ```powershell
   node --version
   npm --version
   ```
   - You should see version numbers for both

## Step 3: Set Up React Frontend

After Node.js is installed, follow these steps:

### Option A: Create New React App (Recommended)
```powershell
# Navigate to the project directory
cd "C:\Project\Bitflow_LMS\frontend"

# Remove existing files (if any)
Remove-Item -Path "src" -Recurse -Force -ErrorAction SilentlyContinue

# Initialize new React TypeScript app
npx create-react-app . --template typescript

# Install additional dependencies for our LMS
npm install axios react-router-dom @types/react-router-dom
```

### Option B: Manual Setup (Alternative)
```powershell
# Navigate to frontend directory
cd "C:\Project\Bitflow_LMS\frontend"

# Initialize package.json
npm init -y

# Install React and TypeScript dependencies
npm install react react-dom @types/react @types/react-dom typescript
npm install --save-dev @vitejs/plugin-react vite

# Install additional dependencies
npm install axios react-router-dom @types/react-router-dom
```

## Step 4: Configure the Frontend

1. **Update package.json scripts**:
```json
{
  "scripts": {
    "dev": "vite",
    "build": "tsc && vite build",
    "preview": "vite preview",
    "start": "vite"
  }
}
```

2. **Create vite.config.ts** (if using Vite):
```typescript
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  server: {
    port: 3000,
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      }
    }
  }
})
```

## Step 5: Start Development

1. **Start Laravel backend**:
```powershell
cd "C:\Project\Bitflow_LMS\lms-backend"
php artisan serve
```

2. **Start React frontend** (in new terminal):
```powershell
cd "C:\Project\Bitflow_LMS\frontend"
npm start
```

## Troubleshooting

### If Node.js installation fails:
- Run PowerShell as Administrator
- Try installing with Chocolatey: `choco install nodejs`
- Or use Windows Package Manager: `winget install OpenJS.NodeJS`

### If npm commands don't work:
- Restart your terminal/PowerShell
- Check if Node.js is in your PATH: `echo $env:PATH`
- Manually add Node.js to PATH if needed

### Common Node.js versions for Windows:
- **Latest LTS**: v20.x.x (Recommended)
- **Previous LTS**: v18.x.x (Still supported)
- **Current**: v21.x.x (Latest features, but less stable)

## Next Steps After Installation

Once Node.js is installed and React is set up:

1. Copy the existing React components from `frontend/src/` to the new React app
2. Configure API service to connect to Laravel backend
3. Set up routing for student/faculty dashboards
4. Test the complete application stack

The backend API is ready and fully functional - it just needs the proper React frontend to consume it!