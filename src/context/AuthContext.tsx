import { useEffect, useState, type ReactNode } from "react";
import { supabase } from "../lib/supabase";
import {
  signIn as signInRequest,
  register as registerRequest,
  signOut as signOutRequest,
} from "../lib/auth";
import type { RegisterInput } from "../lib/auth";
import { AuthContext } from "./auth";
import type { UserProfile } from "../types";

export function AuthProvider({ children }: { children: ReactNode }) {
  const [profile, setProfile] = useState<UserProfile | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    supabase.auth.getSession().then(async ({ data }) => {
      const email = data.session?.user.email;
      if (email) {
        const { data: user } = await supabase
          .from("users")
          .select(
            "id_user, user_name, email, id_rol, id_business, is_super_admin",
          )
          .eq("email", email)
          .maybeSingle();
        if (user) {
          setProfile(user as UserProfile);
        }
      }
      setLoading(false);
    });
  }, []);

  async function signIn(email: string, password: string) {
    const user = await signInRequest(email, password);
    setProfile(user);
  }

  async function signUp(input: RegisterInput) {
    const user = await registerRequest(input);
    setProfile(user);
  }

  async function signOut() {
    await signOutRequest();
    setProfile(null);
  }

  return (
    <AuthContext.Provider value={{ profile, loading, signIn, signUp, signOut }}>
      {children}
    </AuthContext.Provider>
  );
}
