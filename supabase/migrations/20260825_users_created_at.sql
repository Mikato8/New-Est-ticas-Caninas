alter table public.users
  add column if not exists created_at timestamptz not null default now();
