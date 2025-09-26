import React from 'react';
import { useAuth } from '../contexts/AuthContext';
import './AdminDashboard.css';

const AdminDashboard: React.FC = () => {
  const { user, logout } = useAuth();
  return (
    <div className="admin-dashboard">
      <header className="dash-header">
        <h1>Administrator Portal</h1>
        <div className="user-meta">
          <span>{user?.first_name} {user?.last_name}</span>
          <button onClick={logout}>Logout</button>
        </div>
      </header>
      <section className="dash-grid">
        <div className="dash-card">
          <h3>User Management</h3>
          <p>Coming soon: create, edit, deactivate users and bulk import.</p>
        </div>
        <div className="dash-card">
          <h3>System Settings</h3>
          <p>Configuration panel placeholder.</p>
        </div>
        <div className="dash-card">
          <h3>Analytics & Health</h3>
          <p>System performance, storage and usage metrics (planned).</p>
        </div>
        <div className="dash-card">
          <h3>Audit Logs</h3>
          <p>Security & activity monitoring placeholder.</p>
        </div>
      </section>
    </div>
  );
};

export default AdminDashboard;
