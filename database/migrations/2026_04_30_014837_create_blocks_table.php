<script setup>
import { ref, computed, onMounted } from 'vue';
import { apiPieces } from '../api/axios';

// --- ESTADOS ---
const showModal = ref(false);
const esEdicion = ref(false);
const isLoading = ref(false);
const isActionLoading = ref(false);

const bloques = ref([]);
const proyectos = ref([]); // Para el select
const erroresBackend = ref({});

// Fecha para mostrar en el diseño (opcional)
const fechaHoy = new Date().toLocaleDateString('es-CO', {
  year: 'numeric', month: 'long', day: 'numeric'
});

// Modelo ajustado a la migración: code, project_id, name, description, status
const bloqueForm = ref({
  id: null,
  name: '',
  code: '',
  project_id: '',
  status: 'active',
  description: ''
});

// --- VALIDACIONES ---
const esValido = computed(() => {
  return bloqueForm.value.name.length > 2 && 
         bloqueForm.value.code.length > 1 && 
         bloqueForm.value.project_id !== '' &&
         (bloqueForm.value.description || '').length > 5;
});

// --- ACCIONES API ---

const cargarDatosIniciales = async () => {
  isLoading.value = true;
  try {
    // Cargamos bloques y proyectos en paralelo
    const [resBloques, resProyectos] = await Promise.all([
      apiPieces.get('/blocks'),
      apiPieces.get('/projects')
    ]);
    
    bloques.value = resBloques.data.data || resBloques.data;
    proyectos.value = resProyectos.data.data || resProyectos.data;
  } catch (error) {
    console.error("Error cargando datos:", error);
  } finally {
    isLoading.value = false;
  }
};

const guardarBloque = async () => {
  isActionLoading.value = true;
  erroresBackend.value = {};

  try {
    if (esEdicion.value) {
      await apiPieces.put(`/blocks/${bloqueForm.value.id}`, bloqueForm.value);
    } else {
      await apiPieces.post('/blocks', bloqueForm.value);
    }
    
    alert(esEdicion.value ? "✅ Bloque actualizado" : "✅ Bloque registrado");
    cerrarModal();
    cargarDatosIniciales();
  } catch (err) {
    if (err.response?.data?.errors) {
      erroresBackend.value = err.response.data.errors;
    } else {
      alert("Error al procesar el bloque");
    }
  } finally {
    isActionLoading.value = false;
  }
};

const eliminarBloque = async (id) => {
  if (confirm('¿Deseas eliminar este bloque?')) {
    try {
      await apiPieces.delete(`/blocks/${id}`);
      cargarDatosIniciales();
    } catch (error) {
      console.error("Error al eliminar:", error);
    }
  }
};

// --- UTILIDADES ---
const abrirEdicion = (bloque) => {
  esEdicion.value = true;
  bloqueForm.value = { ...bloque };
  showModal.value = true;
};

const cerrarModal = () => {
  showModal.value = false;
  bloqueForm.value = { id: null, name: '', code: '', project_id: '', status: 'active', description: '' };
  erroresBackend.value = {};
};

onMounted(cargarDatosIniciales);
</script>

<template>
  <div class="space-y-8">
    <!-- Cabecera -->
    <div class="flex justify-between items-end">
      <div>
        <h1 class="text-2xl font-black text-slate-800">Módulos y Bloques</h1>
        <p class="text-slate-500">Gestión de las unidades estructurales de cada proyecto.</p>
      </div>
      <button @click="showModal = true; esEdicion = false" 
              class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-blue-200 transition transform hover:-translate-y-1 active:scale-95 flex items-center gap-2 text-sm">
        <span class="text-xl">+</span> Nuevo Bloque
      </button>
    </div>

    <!-- Lista de Bloques -->
    <div v-if="isLoading" class="p-20 text-center">
      <div class="animate-spin inline-block w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full mb-4"></div>
      <p class="text-slate-400 font-bold">Cargando unidades estructurales...</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="b in bloques" :key="b.id" class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm hover:shadow-md transition group">
        <div class="flex justify-between items-start mb-4">
          <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-bold uppercase tracking-wider">
            {{ b.code }}
          </span>
          <span :class="b.status === 'active' ? 'text-emerald-500' : 'text-slate-300'" class="font-bold text-xs">
            ● {{ b.status === 'active' ? 'Activo' : 'Inactivo' }}
          </span>
        </div>
        
        <h3 class="font-black text-slate-800 text-lg mb-1">{{ b.name }}</h3>
        <!-- Buscamos el nombre del proyecto vinculado -->
        <p class="text-xs text-slate-400 italic">
          Proyecto: {{ proyectos.find(p => p.id === b.project_id)?.name || 'Cargando...' }}
        </p>

        <div class="mt-6 flex justify-between items-center border-t border-slate-50 pt-4">
          <div class="flex gap-2">
             <button @click="abrirEdicion(b)" class="p-2 bg-slate-50 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition text-slate-400">✏️</button>
             <button @click="eliminarBloque(b.id)" class="p-2 bg-slate-50 rounded-xl hover:bg-red-50 hover:text-red-600 transition text-slate-400">🗑️</button>
          </div>
          <button class="text-[10px] font-black text-slate-300 uppercase tracking-widest hover:text-blue-600 transition">Configurar ⚙️</button>
        </div>
      </div>
    </div>

    <!-- MODAL DE REGISTRO -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
      <div class="bg-white w-full max-w-xl rounded-[40px] p-10 shadow-2xl relative overflow-y-auto max-h-[90vh]">
        
        <div class="mb-8 text-center">
          <h3 class="text-2xl font-black text-slate-800">{{ esEdicion ? 'Editar Bloque' : 'Registrar Nuevo Bloque' }}</h3>
          <p class="text-sm text-slate-400">Fecha de gestión: {{ fechaHoy }}</p>
        </div>

        <form @submit.prevent="guardarBloque" class="space-y-5">
          
          <div class="grid grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Nombre del Bloque *</label>
              <input v-model="bloqueForm.name" required type="text" 
                     class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition">
              <p v-if="erroresBackend.name" class="text-[10px] text-red-500 mt-1">{{ erroresBackend.name[0] }}</p>
            </div>

            <div>
              <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Código *</label>
              <input v-model="bloqueForm.code" required type="text" 
                     class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition uppercase">
              <p v-if="erroresBackend.code" class="text-[10px] text-red-500 mt-1">{{ erroresBackend.code[0] }}</p>
            </div>
          </div>

          <div>
            <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Vincular a Proyecto *</label>
            <select v-model="bloqueForm.project_id" required
                    class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 appearance-none font-medium">
              <option value="" disabled>Selecciona un proyecto...</option>
              <option v-for="proy in proyectos" :key="proy.id" :value="proy.id">
                {{ proy.name }}
              </option>
            </select>
            <p v-if="erroresBackend.project_id" class="text-[10px] text-red-500 mt-1">{{ erroresBackend.project_id[0] }}</p>
          </div>

          <div>
            <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Estado</label>
            <select v-model="bloqueForm.status" :disabled="!esEdicion"
                    class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none disabled:opacity-50 font-bold text-blue-600">
              <option value="active">Activo</option>
              <option value="inactive">Inactivo</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Descripción</label>
            <textarea v-model="bloqueForm.description" rows="3"
                      class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition"></textarea>
          </div>

          <div class="flex gap-4 pt-4">
            <button type="button" @click="cerrarModal" class="flex-1 py-4 text-slate-400 font-bold hover:text-slate-600 transition">Cancelar</button>
            <button type="submit" :disabled="!esValido || isActionLoading"
                    class="flex-1 py-4 bg-blue-600 text-white rounded-2xl font-bold shadow-lg shadow-blue-200 disabled:bg-slate-200 flex justify-center items-center gap-2">
              <div v-if="isActionLoading" class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              <span>{{ isActionLoading ? 'Procesando...' : (esEdicion ? 'Guardar Cambios' : 'Registrar Bloque') }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>