export type ServiceSize = "small" | "medium" | "large" | "xl";

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
