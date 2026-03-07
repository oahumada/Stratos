<template>
    <div
        class="careers-portal min-h-screen bg-slate-950 text-slate-200 selection:bg-indigo-500/30"
    >
        <v-app-bar
            flat
            color="transparent"
            height="80"
            class="border-b border-white/5 px-6 backdrop-blur-md"
        >
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 shadow-lg shadow-indigo-500/20"
                >
                    <v-icon color="white">mdi-gravity</v-icon>
                </div>
                <div>
                    <div
                        class="text-lg leading-tight font-black tracking-tight text-white uppercase"
                    >
                        Stratos Magnet
                    </div>
                    <div
                        class="text-[10px] font-bold tracking-[0.2em] text-indigo-400 uppercase opacity-80"
                    >
                        {{ orgName }}
                    </div>
                </div>
            </div>
            <v-spacer></v-spacer>
            <div class="hidden items-center gap-8 text-sm font-medium md:flex">
                <a href="#" class="transition-colors hover:text-indigo-400"
                    >Cultura</a
                >
                <a href="#" class="transition-colors hover:text-indigo-400"
                    >Beneficios</a
                >
                <a
                    href="#"
                    class="rounded-full border border-white/10 bg-white/5 px-5 py-2 transition-all hover:bg-indigo-600"
                    >Ver Vacantes</a
                >
            </div>
        </v-app-bar>

        <main class="relative pt-20">
            <!-- Hero Section -->
            <section class="relative overflow-hidden px-6 py-24">
                <div
                    class="pointer-events-none absolute top-0 left-1/2 h-full w-full -translate-x-1/2 overflow-hidden"
                >
                    <div
                        class="absolute top-[-10%] left-[-10%] h-[60%] w-[40%] rounded-full bg-indigo-600/10 blur-[120px]"
                    ></div>
                    <div
                        class="absolute right-[-10%] bottom-[-10%] h-[60%] w-[40%] rounded-full bg-blue-600/10 blur-[120px]"
                    ></div>
                </div>

                <div class="relative z-10 mx-auto max-w-5xl text-center">
                    <v-chip
                        size="small"
                        color="indigo-lighten-4"
                        class="mb-6 border border-indigo-500/20 bg-indigo-500/10 font-black tracking-widest uppercase"
                    >
                        Join the Elite
                    </v-chip>
                    <h1
                        class="mb-8 text-5xl leading-[1.1] font-black tracking-tighter text-white md:text-7xl"
                    >
                        Tu próximo salto <br />
                        <span class="text-indigo-500">hacia el futuro.</span>
                    </h1>
                    <p
                        class="mx-auto mb-12 max-w-2xl text-xl leading-relaxed font-light text-slate-400"
                    >
                        En {{ orgName }}, no buscamos empleados tradicionales.
                        Buscamos el
                        <span class="font-bold text-indigo-400"
                            >ADN perfecto</span
                        >
                        que resonará con nuestra visión estratégica. Nuestras
                        audiciones están diseñadas y analizadas impulsadas por
                        el
                        <strong class="text-indigo-300"
                            >Córtex de IA de Stratos Magnet</strong
                        >.
                    </p>
                    <div
                        class="flex flex-col items-center justify-center gap-4 sm:flex-row"
                    >
                        <v-btn
                            size="x-large"
                            color="indigo-600"
                            class="h-14 rounded-xl px-8 font-bold"
                            elevation="0"
                            >Explorar Oportunidades</v-btn
                        >
                        <v-btn
                            size="x-large"
                            variant="outlined"
                            class="h-14 rounded-xl border-white/20 px-8 font-bold hover:bg-white/5"
                            >Nuestra Misión</v-btn
                        >
                    </div>
                </div>
            </section>

            <!-- Jobs List -->
            <section class="bg-slate-900/50 px-6 py-24">
                <div class="mx-auto max-w-5xl">
                    <div
                        class="mb-12 flex items-end justify-between border-b border-white/5 pb-8"
                    >
                        <div>
                            <h2 class="mb-2 text-3xl font-black text-white">
                                Vacantes Abiertas
                            </h2>
                            <p class="text-slate-500">
                                Filtrado por relevancia estratégica para tu
                                perfil.
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-4xl font-black text-indigo-500">{{
                                openings.length
                            }}</span>
                            <div
                                class="text-[10px] font-bold tracking-widest uppercase opacity-50"
                            >
                                Posiciones
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="loading"
                        class="flex flex-col items-center py-20"
                    >
                        <v-progress-circular
                            indeterminate
                            color="indigo-600"
                            size="64"
                            width="6"
                            class="mb-4"
                        ></v-progress-circular>
                        <div class="font-medium text-slate-500">
                            Sincronizando con Stratos Core...
                        </div>
                    </div>

                    <div v-else class="grid gap-6">
                        <div
                            v-for="job in openings"
                            :key="job.id"
                            class="group relative cursor-pointer overflow-hidden rounded-2xl border border-white/5 bg-slate-900 p-8 transition-all hover:border-indigo-500/50"
                            @click="selectJob(job)"
                        >
                            <div
                                class="pointer-events-none absolute top-0 right-0 h-32 w-32 bg-indigo-600/5 blur-3xl transition-colors group-hover:bg-indigo-600/10"
                            ></div>

                            <div
                                class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center"
                            >
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/5 transition-transform group-hover:scale-110"
                                >
                                    <v-icon color="indigo-400" size="32"
                                        >mdi-briefcase-variant</v-icon
                                    >
                                </div>
                                <div class="flex-1">
                                    <div class="mb-2 flex items-center gap-3">
                                        <v-chip
                                            size="x-small"
                                            color="indigo"
                                            class="text-[10px] font-black uppercase"
                                            >Full Time</v-chip
                                        >
                                        <span
                                            class="text-xs font-bold tracking-widest text-slate-500 uppercase"
                                            >{{ job.department }}</span
                                        >
                                    </div>
                                    <h3
                                        class="text-2xl leading-tight font-black text-white transition-colors group-hover:text-indigo-400"
                                    >
                                        {{ job.title }}
                                    </h3>
                                </div>
                                <div class="flex flex-col items-end">
                                    <v-btn
                                        variant="text"
                                        append-icon="mdi-arrow-right"
                                        class="font-black text-indigo-400"
                                        >Postularme</v-btn
                                    >
                                    <div
                                        class="mt-2 text-[10px] font-bold text-slate-600 uppercase"
                                    >
                                        Publicado recientemente
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Application Dialog -->
        <v-dialog v-model="dialog" max-width="700" persistent>
            <v-card
                v-if="selectedJob"
                class="overflow-hidden rounded-2xl border border-white/10 bg-slate-900 text-slate-200"
            >
                <div
                    class="relative flex h-48 flex-col justify-end bg-linear-to-br from-indigo-900 to-slate-900 p-8"
                >
                    <v-btn
                        icon="mdi-close"
                        variant="text"
                        class="absolute top-4 right-4"
                        @click="dialog = false"
                    ></v-btn>
                    <div class="relative z-10">
                        <v-chip
                            size="x-small"
                            color="white"
                            variant="outlined"
                            class="mb-3 font-bold tracking-widest uppercase"
                            >{{ selectedJob.department }}</v-chip
                        >
                        <h2 class="text-h4 font-weight-black text-white">
                            {{ selectedJob.title }}
                        </h2>
                    </div>
                </div>

                <v-card-text class="pa-8">
                    <div class="grid gap-8 md:grid-cols-3">
                        <div class="md:col-span-2">
                            <h3
                                class="mb-4 text-sm font-black tracking-widest text-indigo-400 uppercase"
                            >
                                Descripción del Rol
                            </h3>
                            <p class="mb-8 leading-relaxed text-slate-300">
                                {{
                                    selectedJob.description ||
                                    'Estamos buscando un talento visionario para unirse a nuestro equipo y liderar la transformación mediante el uso de tecnologías de vanguardia.'
                                }}
                            </p>

                            <h3
                                class="mb-4 text-sm font-black tracking-widest text-indigo-400 uppercase"
                            >
                                ADN Requerido
                            </h3>
                            <ul class="mb-8 space-y-3">
                                <li
                                    v-for="skill in selectedJob.role?.skills"
                                    :key="skill.id"
                                    class="flex items-center gap-3 text-sm text-slate-400"
                                >
                                    <v-icon color="indigo" size="18"
                                        >mdi-hexagon-outline</v-icon
                                    >
                                    {{ skill.name }} (Nivel
                                    {{ skill.pivot?.required_level || 3 }})
                                </li>
                            </ul>
                        </div>

                        <div class="space-y-6">
                            <div
                                class="pa-6 relative overflow-hidden rounded-2xl border border-indigo-500/20 bg-indigo-500/5 backdrop-blur-md"
                            >
                                <div
                                    class="pointer-events-none absolute inset-0 bg-linear-to-b from-indigo-500/10 to-transparent"
                                ></div>
                                <div class="relative z-10">
                                    <h4
                                        class="mb-2 flex items-center gap-2 text-xs font-black tracking-widest text-indigo-300 uppercase"
                                    >
                                        <v-icon size="16"
                                            >mdi-lightning-bolt</v-icon
                                        >
                                        Activar Audición
                                    </h4>
                                    <p
                                        class="mb-4 text-[11px] leading-tight text-slate-400"
                                    >
                                        Nuestra IA generará un
                                        <strong class="text-indigo-300"
                                            >Fitness Match Base</strong
                                        >
                                        a partir de tu perfil en tiempo real.
                                    </p>
                                    <v-form @submit.prevent="submitApplication">
                                        <v-text-field
                                            v-model="form.first_name"
                                            label="Nombre"
                                            variant="solo-filled"
                                            density="compact"
                                            class="mb-2"
                                            bg-color="#ffffff05"
                                        ></v-text-field>
                                        <v-text-field
                                            v-model="form.last_name"
                                            label="Apellido"
                                            variant="solo-filled"
                                            density="compact"
                                            class="mb-2"
                                            bg-color="#ffffff05"
                                        ></v-text-field>
                                        <v-text-field
                                            v-model="form.email"
                                            label="Email Profesional"
                                            variant="solo-filled"
                                            density="compact"
                                            class="mb-4"
                                            bg-color="#ffffff05"
                                        ></v-text-field>
                                        <v-btn
                                            block
                                            color="indigo-500"
                                            class="mt-2 font-bold tracking-wide"
                                            height="48"
                                            elevation="0"
                                            :loading="submitting"
                                            type="submit"
                                            >Conectar a Matchmaker</v-btn
                                        >
                                    </v-form>
                                </div>
                            </div>
                        </div>
                    </div>
                </v-card-text>
            </v-card>
        </v-dialog>

        <!-- Notification -->
        <v-snackbar v-model="snackbar" :color="snackbarColor" rounded="xl">
            {{ snackbarText }}
        </v-snackbar>
    </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref } from 'vue';

const props = defineProps({
    tenant: String,
});

const orgName = ref('Stratos Elite');
const openings = ref([]);
const loading = ref(true);
const dialog = ref(false);
const selectedJob = ref(null);
const submitting = ref(false);
const snackbar = ref(false);
const snackbarText = ref('');
const snackbarColor = ref('success');

const form = ref({
    first_name: '',
    last_name: '',
    email: '',
    resume_url: '',
});

const fetchJobs = async () => {
    try {
        const response = await axios.get(`/api/career/${props.tenant}`);
        openings.value = response.data.openings;
        orgName.value = response.data.organization.name;
    } catch (error) {
        console.error('Error fetching jobs:', error);
    } finally {
        loading.value = false;
    }
};

const selectJob = (job) => {
    selectedJob.value = job;
    dialog.value = true;
};

const submitApplication = async () => {
    submitting.value = true;
    try {
        const response = await axios.post(
            `/api/career/${props.tenant}/jobs/${selectedJob.value.slug}/apply`,
            form.value,
        );
        snackbarText.value = response.data.message;
        snackbarColor.value = 'success';
        snackbar.value = true;
        dialog.value = false;
        // Reset form
        form.value = {
            first_name: '',
            last_name: '',
            email: '',
            resume_url: '',
        };
    } catch (error) {
        console.error('Application submission error:', error);
        snackbarText.value = 'Error al enviar la postulación';
        snackbarColor.value = 'error';
        snackbar.value = true;
    } finally {
        submitting.value = false;
    }
};

onMounted(() => {
    fetchJobs();
});
</script>

<style scoped>
.careers-portal {
    font-family: 'Outfit', sans-serif;
}
/* Glassmorphism utility can be added if needed but using Tailwind for most */
</style>
