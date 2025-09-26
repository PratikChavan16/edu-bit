import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './contexts/AuthContext';
import LoginForm from './components/LoginForm';
import StudentDashboard from './pages/StudentDashboard';
import FacultyDashboard from './pages/FacultyDashboard';
import AdminDashboard from './pages/AdminDashboard';
import HODDashboard from './pages/HODDashboard';
import PrincipalDashboard from './pages/PrincipalDashboard';
import RoleSelectorModal from './components/RoleSelectorModal';
import LoadingSpinner from './components/LoadingSpinner';
import './App.css';

// Protected route component
const ProtectedRoute: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const { isAuthenticated, isLoading } = useAuth();

  if (isLoading) {
    return <LoadingSpinner />;
  }

  return isAuthenticated ? <>{children}</> : <Navigate to="/login" replace />;
};

// Dashboard router based on user role
const DashboardRouter: React.FC = () => {
  const { user, isLoading, activeRole, setActiveRole, logout } = useAuth();

  if (isLoading) {
    return <LoadingSpinner />;
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  const roles = (user.role_names || []).map(r => r.toLowerCase());
  const multiple = roles.length > 1;

  if (!activeRole) {
    if (!multiple && roles.length === 1) {
      setActiveRole(roles[0]);
    } else if (multiple) {
      return <RoleSelectorModal roles={roles} onSelect={setActiveRole} onCancel={logout} />;
    }
  }

  const r = activeRole || roles[0];
  switch (r) {
    case 'admin': return <AdminDashboard />;
    case 'principal': return <PrincipalDashboard />;
    case 'hod': return <HODDashboard />;
    case 'faculty': return <FacultyDashboard />;
    case 'student': return <StudentDashboard />;
    default:
      return (
        <div style={{ padding: '20px', textAlign: 'center' }}>
          <h2>Access Denied</h2>
          <p>No valid role detected on your account.</p>
          <p>Roles found: {roles.join(', ') || 'None'}</p>
          <button onClick={() => window.location.reload()}>Retry</button>
        </div>
      );
  }
};

function App() {
  return (
    <AuthProvider>
      <Router>
        <div className="App">
          <Routes>
            <Route path="/login" element={<LoginForm />} />
            <Route
              path="/dashboard"
              element={
                <ProtectedRoute>
                  <DashboardRouter />
                </ProtectedRoute>
              }
            />
            <Route path="/" element={<Navigate to="/dashboard" replace />} />
          </Routes>
        </div>
      </Router>
    </AuthProvider>
  );
}

export default App;
