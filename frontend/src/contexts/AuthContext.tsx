import React, { createContext, useContext, useReducer, useEffect, ReactNode } from 'react';
import { User } from '../services/apiService';
import apiService from '../services/apiService';

// Export User type for other components
export type { User };

// Auth state type
interface AuthState {
  user: User | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  error: string | null;
  activeRole: string | null; // currently selected role when user has multiple
}

// Auth actions
type AuthAction =
  | { type: 'AUTH_START' }
  | { type: 'AUTH_SUCCESS'; payload: { user: User; activeRole: string | null } }
  | { type: 'AUTH_FAILURE'; payload: string }
  | { type: 'AUTH_LOGOUT' }
  | { type: 'AUTH_CLEAR_ERROR' }
  | { type: 'AUTH_SET_ROLE'; payload: string }
  | { type: 'AUTH_CLEAR_ROLE' };

// Initial state
const initialState: AuthState = {
  user: null,
  isAuthenticated: false,
  isLoading: false,
  error: null,
  activeRole: null,
};

// Auth reducer
function authReducer(state: AuthState, action: AuthAction): AuthState {
  switch (action.type) {
    case 'AUTH_START':
      return {
        ...state,
        isLoading: true,
        error: null,
      };
    case 'AUTH_SUCCESS':
      return {
        ...state,
        user: action.payload.user,
        isAuthenticated: true,
        isLoading: false,
        error: null,
        activeRole: action.payload.activeRole,
      };
    case 'AUTH_FAILURE':
      return {
        ...state,
        user: null,
        isAuthenticated: false,
        isLoading: false,
        error: action.payload,
      };
    case 'AUTH_LOGOUT':
      return {
        ...state,
        user: null,
        isAuthenticated: false,
        isLoading: false,
        error: null,
        activeRole: null,
      };
    case 'AUTH_CLEAR_ERROR':
      return {
        ...state,
        error: null,
      };
    case 'AUTH_SET_ROLE':
      return {
        ...state,
        activeRole: action.payload,
      };
    case 'AUTH_CLEAR_ROLE':
      return {
        ...state,
        activeRole: null,
      };
    default:
      return state;
  }
}

// Context type
interface AuthContextType extends AuthState {
  login: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
  clearError: () => void;
  checkAuth: () => Promise<void>;
  setActiveRole: (role: string) => void;
  clearActiveRole: () => void;
}

// Create context
const AuthContext = createContext<AuthContextType | undefined>(undefined);

// Auth provider props
interface AuthProviderProps {
  children: ReactNode;
}

// Auth provider component
export function AuthProvider({ children }: AuthProviderProps) {
  const [state, dispatch] = useReducer(authReducer, initialState);

  // Check authentication status on mount
  useEffect(() => {
    checkAuth();
  }, []);

  const determineInitialRole = (user: User): string | null => {
    const roles = (user.role_names || []).map(r => r.toLowerCase());
    if (roles.length === 1) return roles[0];
    // If previously stored role still valid reuse it
    const stored = localStorage.getItem('active_role');
    if (stored && roles.includes(stored)) return stored;
    return null; // force selection
  };

  const login = async (email: string, password: string): Promise<void> => {
    try {
      dispatch({ type: 'AUTH_START' });
      const { user } = await apiService.login({ email, password });
      const activeRole = determineInitialRole(user);
      if (activeRole) {
        localStorage.setItem('active_role', activeRole);
      } else {
        localStorage.removeItem('active_role');
      }
      dispatch({ type: 'AUTH_SUCCESS', payload: { user, activeRole } });
    } catch (error: any) {
      console.error('Login failed:', error);
      const errorMessage = error.response?.data?.message || 'Login failed';
      dispatch({ type: 'AUTH_FAILURE', payload: errorMessage });
      throw error;
    }
  };

  const logout = async (): Promise<void> => {
    try {
      await apiService.logout();
    } catch (error) {
      // Handle logout error silently
    } finally {
      dispatch({ type: 'AUTH_LOGOUT' });
    }
  };

  const clearError = (): void => {
    dispatch({ type: 'AUTH_CLEAR_ERROR' });
  };

  const checkAuth = async (): Promise<void> => {
    if (!apiService.isAuthenticated()) {
      return;
    }

    try {
      dispatch({ type: 'AUTH_START' });
      const user = await apiService.getCurrentUser();
      const activeRole = (() => {
        const stored = localStorage.getItem('active_role');
        const roles = (user.role_names || []).map(r => r.toLowerCase());
        if (stored && roles.includes(stored)) return stored;
        if (roles.length === 1) return roles[0];
        return null;
      })();
      if (activeRole) localStorage.setItem('active_role', activeRole); else localStorage.removeItem('active_role');
      dispatch({ type: 'AUTH_SUCCESS', payload: { user, activeRole } });
    } catch (error) {
      // Token is invalid, clear auth
      dispatch({ type: 'AUTH_LOGOUT' });
    }
  };

  const setActiveRole = (role: string) => {
    localStorage.setItem('active_role', role);
    dispatch({ type: 'AUTH_SET_ROLE', payload: role });
  };

  const clearActiveRole = () => {
    localStorage.removeItem('active_role');
    dispatch({ type: 'AUTH_CLEAR_ROLE' });
  };

  const contextValue: AuthContextType = {
    ...state,
    login,
    logout,
    clearError,
    checkAuth,
    setActiveRole,
    clearActiveRole,
  };

  return (
    <AuthContext.Provider value={contextValue}>
      {children}
    </AuthContext.Provider>
  );
}

// Custom hook to use auth context
export function useAuth(): AuthContextType {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}

export default AuthContext;