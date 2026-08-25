create table if not exists public.pet_history (
  id_history integer generated always as identity primary key,
  id_pet integer not null references public.pets(id_pet) on delete cascade,
  note text not null,
  id_business integer not null references public.business(id_business) on delete cascade,
  created_at timestamptz not null default now()
);

create index if not exists pet_history_id_pet_idx on public.pet_history (id_pet);
create index if not exists pet_history_id_business_idx on public.pet_history (id_business);

alter table public.pet_history enable row level security;

create policy "pet_history_select" on public.pet_history
  for select to authenticated
  using (id_business = public.get_business_id());

create policy "pet_history_insert" on public.pet_history
  for insert to authenticated
  with check (id_business = public.get_business_id());

create policy "pet_history_update" on public.pet_history
  for update to authenticated
  using (id_business = public.get_business_id())
  with check (id_business = public.get_business_id());

create policy "pet_history_delete" on public.pet_history
  for delete to authenticated
  using (id_business = public.get_business_id());

grant select, insert, update, delete on public.pet_history to authenticated;
