import { createContext } from "react";
import type { CredentialResponse } from "@react-oauth/google";

export interface User {
  email: string;
  name: string;
  picture: string;
}

export interface AuthContextType {
  user: User | null;
  isAuthenticated: boolean;
  handleGoogleLogin: (response: CredentialResponse) => void;
  handleLogout: () => void;
}

export const AuthContext = createContext<AuthContextType | null>(null);
