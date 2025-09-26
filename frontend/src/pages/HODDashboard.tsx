import React from 'react';
import { useAuth } from '../contexts/AuthContext';
import './HODDashboard.css';

const HODDashboard: React.FC = () => {
  const { user, logout } = useAuth();
  return (
    <div className="hod-dashboard">
      <header className="dash-header">
        <h1>Head of Department Portal</h1>
        <div className="user-meta">
          <span>{user?.first_name} {user?.last_name} ({user?.department?.name})</span>
          <button onClick={logout}>Logout</button>
        </div>
      </header>
      <section className="dash-grid">
        <div className="dash-card">
          <h3>Faculty Overview</h3>
          <p>Upcoming: faculty workload & content contribution metrics.</p>
        </div>
        <div className="dash-card">
          <h3>Student Progress</h3>
          <p>Department student performance charts (planned).</p>
        </div>
        <div className="dash-card">
          <h3>Curriculum Resources</h3>
          <p>Manage subject mapping & term resources (planned).</p>
        </div>
      </section>
    </div>
  );
};

export default HODDashboard;
