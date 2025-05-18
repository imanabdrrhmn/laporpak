<template>
  <AppLayout>
    <Head title="User Management" />
    <div class="container mt-4">
      <h2 class="mb-4">User Management</h2>

      <div v-if="$page.props.flash.success" class="alert alert-success">
        {{ $page.props.flash.success }}
      </div>

      <!-- Tabel User -->
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="thead-light">
            <tr>
              <th>#</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Role</th>
              <th>Ganti Role</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in users" :key="user.id">
              <td>{{ index + 1 }}</td>
              <td>{{ user.name }}</td>
              <td>{{ user.email }}</td>
              <td>
                <span v-if="user.roles.length">{{ user.roles[0].name }}</span>
                <span v-else class="text-muted">Belum ada role</span>
              </td>
              <td>
                <form
                  :action="route('admin.users.assignRole', user.id)"
                  method="POST"
                >
                  <input type="hidden" name="_token" :value="csrf" />
                  <select
                    name="role"
                    class="form-select form-select-sm"
                    @change="(e) => submitForm(e, user.name)"
                  >
                    <option disabled selected>Pilih role</option>
                    <option
                      v-for="role in roles"
                      :key="role"
                      :value="role"
                      :selected="user.roles[0]?.name === role"
                    >
                      {{ role }}
                    </option>
                  </select>
                </form>
              </td>
              <td>
                <form
                  :action="route('admin.users.delete', user.id)"
                  method="POST"
                >
                  <input type="hidden" name="_method" value="DELETE" />
                  <input type="hidden" name="_token" :value="csrf" />
                  <button
                    class="btn btn-danger btn-sm"
                    @click.prevent="confirmDelete(user.name, $event.target.closest('form'))"
                  >
                    Hapus
                  </button>
                </form>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <hr />

      <!-- Form Tambah Role Baru -->
      <div class="mb-4">
        <h4>Tambah Role Baru</h4>
        <form :action="route('admin.roles.store')" method="POST" @submit.prevent="submitNewRole">
          <input type="hidden" name="_token" :value="csrf" />
          <div class="input-group" style="max-width: 400px;">
            <input
              type="text"
              v-model="newRoleName"
              class="form-control"
              placeholder="Nama role baru"
              required
            />
            <button class="btn btn-primary" type="submit">Tambah</button>
          </div>
        </form>
      </div>

      <!-- Daftar Role dengan tombol hapus -->
      <div class="table-responsive" style="max-width: 400px;">
        <h4>Daftar Role</h4>
        <table class="table table-sm table-bordered table-hover">
          <thead>
            <tr>
              <th>Role</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="role in roles" :key="role">
              <td>{{ role }}</td>
              <td>
                <form
                  :action="route('admin.roles.destroy', role)"
                  method="POST"
                  @submit.prevent="confirmDeleteRole(role, $event.target)"
                >
                  <input type="hidden" name="_method" value="DELETE" />
                  <input type="hidden" name="_token" :value="csrf" />
                  <button class="btn btn-danger btn-sm" type="submit">
                    Hapus
                  </button>
                </form>
              </td>
            </tr>
            <tr v-if="roles.length === 0">
              <td colspan="2" class="text-center text-muted">Belum ada role</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>


<script setup>
import Swal from 'sweetalert2'
import { ref } from 'vue'
import { usePage, Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const { props } = usePage()
const users = props.users
const roles = props.roles

const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
const newRoleName = ref('')

function submitForm(e, userName) {
  e.preventDefault()
  const form = e.target.form

  Swal.fire({
    title: `Ganti role ${userName}?`,
    text: 'Perubahan akan langsung disimpan!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, ganti!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit()
    }
  })
}

function confirmDelete(userName, form) {
  Swal.fire({
    title: `Hapus ${userName}?`,
    text: 'Data tidak bisa dikembalikan!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit()
    }
  })
}

function confirmDeleteRole(role, form) {
  Swal.fire({
    title: `Hapus role "${role}"?`,
    text: 'Perubahan akan berpengaruh pada user yang memakai role ini!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit()
    }
  })
}

function submitNewRole() {
  if (!newRoleName.value.trim()) {
    Swal.fire('Error', 'Nama role tidak boleh kosong', 'error')
    return
  }
  const data = {
    name: newRoleName.value.trim(),
  }
  window.axios.post(route('roles.store'), data)
    .then(() => {
      Swal.fire('Berhasil', 'Role baru berhasil dibuat', 'success').then(() => {
        window.location.reload()
      })
    })
    .catch(err => {
      Swal.fire('Gagal', 'Gagal membuat role baru', 'error')
      console.error(err)
    })
}
</script>
