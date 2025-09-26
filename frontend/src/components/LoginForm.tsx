import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import './LoginForm.css';

interface LoginFormProps {
  onLoginSuccess?: () => void;
}

const LoginForm: React.FC<LoginFormProps> = ({ onLoginSuccess }) => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  
  const { login, error, clearError } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!email || !password) {
      return;
    }

    setIsSubmitting(true);
    clearError();

    try {
      await login(email, password);
      onLoginSuccess?.();
      navigate('/dashboard');
    } catch (error) {
      // Error is handled by AuthContext
    } finally {
      setIsSubmitting(false);
    }
  };

  const togglePasswordVisibility = () => {
    setShowPassword(!showPassword);
  };

  return (
    <div className="login-page">
      {/* Decorative stars */}
      <div className="star star-1"></div>
      <div className="star star-2"></div>
      <div className="star star-3"></div>
      <div className="star star-4"></div>
      <div className="star star-5"></div>

      <div className="login-container">
        <div className="login-card">
          <div className="login-logo">
            <div className="logo-star"></div>
          </div>
          
          <h1 className="login-title">Login</h1>
          
          <form className="login-form" onSubmit={handleSubmit} noValidate>
            <div className="form-group">
              <label htmlFor="email" className="form-label" style={{ color: 'white' }}>Email</label>
              <input
                id="email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="username@gmail.com"
                className="form-input"
                autoComplete="username"
                required
                disabled={isSubmitting}
              />
            </div>
            
            <div className="form-group">
              <label htmlFor="password" className="form-label" style={{ color: 'white' }}>Password</label>
              <div className="password-input-wrapper">
                <input
                  id="password"
                  type={showPassword ? "text" : "password"}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  placeholder="Password"
                  className="form-input"
                  autoComplete="current-password"
                  required
                  disabled={isSubmitting}
                />
                <button
                  type="button"
                  className="password-toggle"
                  onClick={togglePasswordVisibility}
                  aria-label="Toggle password visibility"
                >
                  <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clipPath="url(#clip0_2_23226)">
                      <path d="M11.2125 10.3985C11.3312 10.0281 11.3914 9.6355 11.3903 9.23992C11.3903 8.36158 11.0916 7.51923 10.5599 6.89815C10.0282 6.27708 9.30707 5.92816 8.55516 5.92816C8.22068 5.92862 7.88897 5.99892 7.57568 6.13575L8.24106 6.93714C8.34221 6.91823 8.44446 6.90855 8.54689 6.90817C9.07855 6.90687 9.58905 7.15136 9.96692 7.58824C10.3448 8.02511 10.5593 8.61889 10.5637 9.23992C10.5634 9.35957 10.5551 9.47901 10.5389 9.59716L11.2125 10.3985Z" fill="#C7D2D6"/>
                      <path d="M14.9733 9.013C13.5806 6.00538 11.1381 4.18536 8.43524 4.18536C7.69933 4.18738 6.96837 4.32589 6.26965 4.59571L6.93503 5.37779C7.42512 5.22984 7.92905 5.15362 8.43524 5.15089C10.762 5.15089 12.878 6.66677 14.1385 9.22058C13.6761 10.1681 13.0631 11.0037 12.3325 11.6827L12.9193 12.3682C13.7649 11.5713 14.4672 10.5869 14.9857 9.47162L15.0932 9.23989L14.9733 9.013Z" fill="#C7D2D6"/>
                      <path d="M2.81471 3.34053L4.65793 5.49366C3.49287 6.36992 2.54066 7.58198 1.8931 9.013L1.78564 9.2399L1.8931 9.47162C3.28585 12.4792 5.72833 14.2993 8.43117 14.2993C9.48613 14.299 10.5274 14.0201 11.477 13.4834L13.5434 15.8972L14.2667 15.1731L3.52142 2.62122L2.81471 3.34053ZM6.84418 8.04747L9.59249 11.2578C9.28196 11.4824 8.92448 11.6026 8.55929 11.6054C8.29393 11.6054 8.0312 11.5442 7.7862 11.4251C7.5412 11.306 7.31877 11.1316 7.13172 10.9117C6.94466 10.6919 6.79667 10.431 6.69625 10.1441C6.59584 9.85714 6.54499 9.54986 6.54662 9.2399C6.55128 8.81823 6.65414 8.40601 6.84418 8.04747ZM6.24493 7.34747C5.85828 7.98442 5.67705 8.76065 5.73333 9.53872C5.78962 10.3168 6.07978 11.0465 6.55245 11.5986C7.02512 12.1508 7.64979 12.4897 8.31587 12.5555C8.98195 12.6212 9.64646 12.4095 10.1917 11.9579L10.853 12.7303C10.0875 13.1125 9.26366 13.3096 8.43117 13.3096C6.10441 13.3096 3.98842 11.7937 2.72792 9.2399C3.33284 7.98868 4.20404 6.94108 5.25719 6.19849L6.24493 7.34747Z" fill="#C7D2D6"/>
                    </g>
                    <defs>
                      <clipPath id="clip0_2_23226">
                        <rect x="0.802002" y="0.550171" width="14.8781" height="17.3795" rx="7.43903" fill="white"/>
                      </clipPath>
                    </defs>
                  </svg>
                </button>
              </div>
              <button type="button" className="forgot-password">Forgot Password?</button>
            </div>

            {error && <div className="error-message">{error}</div>}
            
            <button
              type="submit"
              className="login-button"
              disabled={isSubmitting || !email || !password}
            >
              {isSubmitting ? 'SIGNING IN...' : 'LOGIN'}
            </button>
          </form>
        </div>
      </div>
    </div>
  );
};

export default LoginForm;
