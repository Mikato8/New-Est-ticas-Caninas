import { useState, useCallback, type ReactNode } from "react";
import type { CredentialResponse } from "@react-oauth/google";
import { AuthContext, type User } from "./AuthContext";

function parseJwt(token: string): User | null {
  try {
    const base64Url = token.split(".")[1];
    const base64 = base64Url.replace(/-/g, "+").replace(/_/g, "/");
    const jsonPayload = decodeURIComponent(
      atob(base64)
        .split("")
        .map((c) => "%" + ("00" + c.charCodeAt(0).toString(16)).slice(-2))
        .join("")
    );
    const decoded = JSON.parse(jsonPayload);
    return {
      email: decoded.email,
      name: decoded.name,
      picture: decoded.picture,
    };
  } catch {
    return null;
  }
}

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null);

  const handleGoogleLogin = useCallback((response: CredentialResponse) => {
    if (response.credential) {
      const userData = parseJwt(response.credential);
      if (userData) {
        setUser(userData);
      }
    }
  }, []);

  const handleLogout = useCallback(() => {
    setUser(null);
  }, []);

  return (
    <AuthContext.Provider
      value={{
        user,
        isAuthenticated: user !== null,
        handleGoogleLogin,
        handleLogout,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}
