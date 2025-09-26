import React from 'react';
import { useAuth } from '../contexts/AuthContext';
import './PrincipalDashboard.css';

const PrincipalDashboard: React.FC = () => {
  const { user, logout } = useAuth();
  return (
    <div className="principal-dashboard">
      <header className="dash-header">
        <h1>Principal Portal</h1>
        <div className="user-meta">
          <span>{user?.first_name} {user?.last_name}</span>
          <button onClick={logout}>Logout</button>
        </div>
      </header>
      <section className="dash-grid">
        <div className="dash-card">
          <h3>Institution Overview</h3>
          <p>Planned: cross-department KPIs & enrollment stats.</p>
        </div>
        <div className="dash-card">
          <h3>Department Comparison</h3>
          <p>Planned: performance benchmarking & resource allocation.</p>
        </div>
        <div className="dash-card">
          <h3>Strategic Reports</h3>
          <p>Planned: exportable summary reports and trends.</p>
        </div>
      </section>
    </div>
  );
};

export default PrincipalDashboard;
