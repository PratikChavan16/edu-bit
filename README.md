# 🎓 Bitflow LMS - Complete Learning Management System

A comprehensive, modern Learning Management System designed for medical colleges and educational institutions, built with React + TypeScript frontend and Laravel backend.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![Version](https://img.shields.io/badge/version-1.0.0-green.svg)
![Status](https://img.shields.io/badge/status-Production%20Ready-brightgreen.svg)

## 🚀 Live Demo

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000/api
- **Documentation**: Available in `/docs` folder

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Quick Start](#-quick-start)
- [Project Structure](#-project-structure)
- [User Roles](#-user-roles)
- [API Documentation](#-api-documentation)
- [Screenshots](#-screenshots)
- [Contributing](#-contributing)
- [License](#-license)

## ✨ Features

### 🎯 Multi-Role Authentication System
- **5 User Roles**: Student, Faculty, HOD, Principal, Admin
- **Role-based Dashboards**: Customized interfaces for each role
- **Multi-role Support**: Users can have multiple roles with role switching
- **Secure Authentication**: Laravel Sanctum with token-based auth

### 📚 Student Portal
- Course enrollment and subject access
- Download study materials (PDFs, documents)  
- Stream educational videos with progress tracking
- Subject-wise content organization
- Responsive design for mobile and desktop

### 👨‍🏫 Faculty Portal
- Upload and manage course content
- Organize study materials by subjects
- Upload video lectures with metadata
- Content management (create, edit, delete)
- Subject assignment and management

### 🏛️ Administrative Portals
- **HOD Dashboard**: Department-level content oversight
- **Principal Dashboard**: Institution-wide management
- **Admin Dashboard**: Complete system administration

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 12 + PHP 8.2+
- **Database**: SQLite (Development) / MySQL (Production)
- **Authentication**: Laravel Sanctum
- **Storage**: Local/S3-compatible storage
- **API**: RESTful API with OpenAPI documentation

### Frontend  
- **Framework**: React 18 + TypeScript
- **Styling**: Custom CSS with responsive design
- **State Management**: Context API + React Hooks
- **HTTP Client**: Axios
- **Routing**: React Router v6

### Infrastructure
- **Development**: Local development environment
- **Production Ready**: AWS deployment configuration
- **Database**: SQLite for development, MySQL for production
- **File Storage**: Local storage with S3 migration path

## 🚀 Quick Start

### Prerequisites
- Node.js 18+ and npm
- PHP 8.2+ and Composer
- Git

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/PratikChavan16/edu-bit.git
cd edu-bit
```

2. **Backend Setup**
```bash
cd lms-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

3. **Frontend Setup**
```bash
cd ../frontend
npm install
npm start
```

4. **Access the Application**
- Frontend: http://localhost:3000
- Backend API: http://localhost:8000/api

### Demo Credentials

**Student Account:**
- Email: student@lms.com
- Password: password

**Faculty Account:**
- Email: faculty@lms.com  
- Password: password

**Admin Account:**
- Email: admin@lms.com
- Password: password
