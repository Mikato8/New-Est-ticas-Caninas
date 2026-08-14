export type ServiceSize = "small" | "medium" | "large" | "xl";
export type SaleStatus = "pending" | "paid" | "cancelled" | "refunded";

export interface UserProfile {
  id_user: number;
  user_name: string;
  email: string;
  id_rol: number;
  id_business: number;
}

export interface Role {
  id_role: number;
  rol_name: string;
}

export interface Business {
  id_business: number;
  business_name: string;
  address: string | null;
  phone: string | null;
  email: string | null;
}

export interface Custom {
  id_custom: number;
  main_color: string | null;
  secondary_color: string | null;
  text_color: string | null;
  logo: string | null;
  id_business: number;
}

export interface Specie {
  id_specie: number;
  specie_name: string;
  id_business: number;
}

export interface Customer {
  id_customer: number;
  customer_name: string;
  address: string | null;
  email: string | null;
  phone: string | null;
  id_business: number;
}

export interface Pet {
  id_pet: number;
  pet_name: string;
  id_specie: number;
  photo: string | null;
  id_customer: number;
  id_business: number;
}

export interface PetWithDetails extends Pet {
  species: { specie_name: string } | null;
  customers: { customer_name: string } | null;
}

export interface Service {
  id_service: number;
  service_name: string;
  size: ServiceSize | null;
  price: number;
  id_business: number;
}

export interface ServiceWithDetails extends Service {
  service_species: { species: { id_specie: number; specie_name: string } }[];
}

export interface Product {
  id_product: number;
  product_name: string;
  purchase_price: number;
  sale_price: number;
  stock: number;
  id_business: number;
}

export interface Package {
  id_package: number;
  package_name: string;
  description: string | null;
  price: number;
  id_business: number;
}

export interface PackageWithDetails extends Package {
  package_services: { services: { id_service: number; service_name: string } }[];
  package_products: { products: { id_product: number; product_name: string } }[];
}

export interface Appointment {
  id_appointment: number;
  id_pet: number;
  appointment_date: string | null;
  appointment_time: string | null;
  id_service: number;
  id_business: number;
}

export interface AppointmentWithDetails extends Appointment {
  pets: { pet_name: string; customers: { customer_name: string } | null } | null;
  services: { service_name: string } | null;
}

export interface Sale {
  id_sale: number;
  total_price: number;
  id_payment_method: number | null;
  sale_date: string | null;
  status: SaleStatus;
  id_business: number;
}

export interface SaleWithDetails extends Sale {
  payment_methods: { method_name: string } | null;
  sale_services: {
    id_service: number;
    services: { service_name: string; price: number } | null;
  }[];
  sale_products: {
    id_product: number;
    products: { product_name: string; sale_price: number } | null;
  }[];
}

export interface Expense {
  id_expense: number;
  expense_name: string;
  price: number;
  payment_date: string | null;
  id_payment_method: number | null;
  id_business: number;
}

export interface ExpenseWithDetails extends Expense {
  payment_methods: { method_name: string } | null;
}

export interface PaymentMethod {
  id_payment_method: number;
  method_name: string;
  account_number: string | null;
  id_business: number;
}

export interface Contract {
  id_contract: number;
  contract_name: string;
  id_client: number;
  signed: boolean;
  signed_date: string | null;
  id_business: number;
}

export interface ContractWithDetails extends Contract {
  customers: { customer_name: string } | null;
}

export interface User {
  id_user: number;
  user_name: string;
  email: string;
  password: string;
  id_rol: number;
  id_business: number;
}
