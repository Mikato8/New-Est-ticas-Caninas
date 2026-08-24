import { Navigate, Outlet, Routes, Route } from "react-router-dom";
import Login from "../pages/Login/Login";
import ResetPassword from "../pages/Login/ResetPassword";
import Home from "../pages/Home/Home";
import Customers from "../pages/Customers/Customers";
import Pets from "../pages/Pets/Pets";
import Appointments from "../pages/Appointments/Appointments";
import Services from "../pages/Services/Services";
import Species from "../pages/Species/Species";
import Products from "../pages/Products/Products";
import Packages from "../pages/Packages/Packages";
import Sales from "../pages/Sales/Sales";
import Expenses from "../pages/Expenses/Expenses";
import PaymentMethods from "../pages/PaymentMethods/PaymentMethods";
import Contracts from "../pages/Contracts/Contracts";
import Settings from "../pages/Settings/Settings";
import Users from "../pages/Users/Users";
import ProtectedRoute from "../components/ProtectedRoute";
import AppLayout from "../components/layout/AppLayout";
import { useAuth } from "../context/auth";

function RequireAdmin() {
  const { profile } = useAuth();
  if (profile?.id_rol !== 1) {
    return <Navigate to="/home" replace />;
  }
  return <Outlet />;
}

function AppRoutes() {
  return (
    <Routes>
      <Route path="/" element={<Login />} />
      <Route path="/reset-password" element={<ResetPassword />} />
      <Route element={<ProtectedRoute />}>
        <Route element={<AppLayout />}>
          <Route path="/home" element={<Home />} />
          <Route path="/customers" element={<Customers />} />
          <Route path="/pets" element={<Pets />} />
          <Route path="/appointments" element={<Appointments />} />
          <Route path="/services" element={<Services />} />
          <Route path="/species" element={<Species />} />
          <Route path="/products" element={<Products />} />
          <Route path="/packages" element={<Packages />} />
          <Route path="/sales" element={<Sales />} />
          <Route path="/expenses" element={<Expenses />} />
          <Route path="/payment-methods" element={<PaymentMethods />} />
          <Route path="/contracts" element={<Contracts />} />
          <Route element={<RequireAdmin />}>
            <Route path="/users" element={<Users />} />
            <Route path="/settings" element={<Settings />} />
          </Route>
        </Route>
      </Route>
    </Routes>
  );
}

export default AppRoutes;
