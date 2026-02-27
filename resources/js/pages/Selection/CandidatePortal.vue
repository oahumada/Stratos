<template>
    <div
        class="candidate-portal min-h-screen bg-slate-950 font-sans text-white"
    >
        <!-- Header -->
        <header
            class="sticky top-0 z-50 flex items-center justify-between border-b border-white/5 bg-slate-900/50 px-8 py-6 backdrop-blur-md"
        >
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-pink-500 to-rose-600 text-xl font-bold shadow-lg shadow-pink-500/20"
                >
                    S
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">
                        Stratos
                        <span
                            class="ml-1 rounded-full border border-pink-500/20 bg-pink-500/10 px-2 py-0.5 text-xs font-medium tracking-widest text-pink-500 uppercase"
                            >Candidate Experience</span
                        >
                    </h1>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <div class="hidden text-right md:block">
                    <div class="text-sm font-medium">{{ candidate.name }}</div>
                    <div class="text-xs text-white/40">
                        Ref: #{{ candidate.id }} — Sr. Data Architect
                    </div>
                </div>
                <v-avatar size="44" class="border-2 border-pink-500/30">
                    <img
                        src="https://ui-avatars.com/api/?name=Omar+Ahumada&background=ec4899&color=fff"
                        alt="Avatar"
                    />
                </v-avatar>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-8 py-12">
            <!-- Hero Banner / Resonance Overview -->
            <section
                class="relative mb-12 overflow-hidden rounded-3xl border border-white/5 bg-gradient-to-br from-slate-900 to-slate-800 p-8 md:p-12"
            >
                <div
                    class="pointer-events-none absolute top-0 right-0 h-full w-1/3 bg-gradient-to-l from-pink-500/10 to-transparent"
                ></div>
                <div
                    class="relative z-10 grid items-center gap-12 md:grid-cols-2"
                >
                    <div>
                        <v-chip
                            color="pink-lighten-4"
                            variant="tonal"
                            class="mb-4 font-bold text-pink-400"
                            size="small"
                            >RESONANCIA ADN DETECTADA</v-chip
                        >
                        <h2
                            class="mb-6 text-4xl leading-tight font-extrabold md:text-5xl"
                        >
                            Tu potencial resuena con <br />
                            nuestra
                            <span
                                class="bg-gradient-to-r from-pink-400 to-rose-500 bg-clip-text text-transparent"
                                >visión técnica.</span
                            >
                        </h2>
                        <p class="mb-8 text-lg leading-relaxed text-white/60">
                            No solo evaluamos habilidades; buscamos el
                            acoplamiento perfecto entre tu trayectoria y el
                            Blueprint de crecimiento de Stratos.
                        </p>
                        <div class="flex gap-4">
                            <v-btn
                                color="pink-darken-1"
                                size="large"
                                class="text-none rounded-xl px-8 shadow-xl shadow-pink-500/20"
                                prepend-icon="mdi-message-text-outline"
                                >Hablar con Matchmaker</v-btn
                            >
                            <v-btn
                                variant="outlined"
                                size="large"
                                class="text-none rounded-xl border-white/20 px-8"
                                >Ver Blueprint del Rol</v-btn
                            >
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <div class="relative">
                            <!-- Circular Progress for Resonance -->
                            <v-progress-circular
                                :model-value="88"
                                :size="240"
                                :width="20"
                                color="pink-lighten-1"
                                class="resonance-ring"
                            >
                                <div class="text-center">
                                    <div class="mb-1 text-5xl font-black">
                                        88<span class="text-2xl text-pink-400"
                                            >%</span
                                        >
                                    </div>
                                    <div
                                        class="text-xs tracking-widest text-white/40 uppercase"
                                    >
                                        Resonancia
                                    </div>
                                </div>
                            </v-progress-circular>
                            <!-- Decorative elements -->
                            <div
                                class="absolute -top-4 -right-4 h-12 w-12 animate-pulse rounded-full bg-pink-500 opacity-50 blur-2xl"
                            ></div>
                            <div
                                class="absolute -bottom-8 -left-8 h-16 w-16 rounded-full bg-blue-500 opacity-30 blur-3xl"
                            ></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Analysis Cards -->
            <div class="mb-12 grid gap-8 md:grid-cols-3">
                <!-- Technical Core -->
                <v-card
                    class="pa-6 rounded-3xl border border-white/5 bg-slate-900/50 transition-all hover:border-pink-500/30"
                >
                    <div
                        class="mb-6 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-500/10"
                    >
                        <v-icon icon="mdi-xml" color="blue-lighten-2"></v-icon>
                    </div>
                    <h3 class="mb-3 text-xl font-bold">Núcleo Técnico</h3>
                    <p class="mb-6 text-sm text-white/60">
                        Tu dominio de arquitecturas RAG y Vector Databases
                        supera el 92% de los perfiles analizados.
                    </p>
                    <div class="space-y-4">
                        <div
                            v-for="skill in [
                                'Python / FastAPI',
                                'Neo4j / Graphs',
                                'PyTorch',
                            ]"
                            :key="skill"
                            class="pa-3 flex items-center justify-between rounded-xl border border-white/5 bg-white/5"
                        >
                            <span class="text-sm font-medium">{{ skill }}</span>
                            <v-icon
                                icon="mdi-check-decagram"
                                color="cyan-accent-2"
                                size="18"
                            ></v-icon>
                        </div>
                    </div>
                </v-card>

                <!-- Cultural Alignment -->
                <v-card
                    class="pa-6 rounded-3xl border border-white/5 bg-slate-900/50 transition-all hover:border-pink-500/30"
                >
                    <div
                        class="mb-6 flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-500/10"
                    >
                        <v-icon
                            icon="mdi-account-group-outline"
                            color="purple-lighten-2"
                        ></v-icon>
                    </div>
                    <h3 class="mb-3 text-xl font-bold">Alineación Cultural</h3>
                    <p class="mb-6 text-sm text-white/60">
                        Tu estilo de liderazgo agéntico encaja con nuestra
                        filosofía de Radical Meritocracy.
                    </p>
                    <div class="space-y-4">
                        <div
                            v-for="trait in [
                                'Propiedad Radical',
                                'Simplicidad Extrema',
                                'Velocidad Iterativa',
                            ]"
                            :key="trait"
                            class="pa-3 flex items-center justify-between rounded-xl border border-white/5 bg-white/5"
                        >
                            <span class="text-sm font-medium">{{ trait }}</span>
                            <v-icon
                                icon="mdi-star"
                                color="amber-accent-2"
                                size="18"
                            ></v-icon>
                        </div>
                    </div>
                </v-card>

                <!-- Future Trajectory -->
                <v-card
                    class="pa-6 rounded-3xl border border-white/5 bg-slate-900/50 transition-all hover:border-pink-500/30"
                >
                    <div
                        class="mb-6 flex h-12 w-12 items-center justify-center rounded-2xl bg-pink-500/10"
                    >
                        <v-icon
                            icon="mdi-trending-up"
                            color="pink-lighten-2"
                        ></v-icon>
                    </div>
                    <h3 class="mb-3 text-xl font-bold">
                        Trayectoria de Crecimiento
                    </h3>
                    <p class="mb-6 text-sm text-white/60">
                        Estimamos una progresión a 'Principal Agent Architect'
                        en menos de 18 meses.
                    </p>
                    <div
                        class="pa-4 rounded-2xl border border-pink-500/10 bg-pink-500/5"
                    >
                        <div
                            class="mb-2 text-xs font-bold tracking-wider text-pink-400 uppercase"
                        >
                            Recomendación Matchmaker:
                        </div>
                        <div class="text-sm italic">
                            "Tu experiencia en MLOps es la pieza que falta para
                            escalar nuestro motor de Scenario IQ."
                        </div>
                    </div>
                </v-card>
            </div>

            <!-- Agent Interaction (Simplified Mock) -->
            <section
                class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900 shadow-2xl"
            >
                <div
                    class="flex items-center justify-between border-b border-white/5 bg-slate-800/80 px-8 py-4"
                >
                    <div class="flex items-center gap-3">
                        <v-avatar color="pink-darken-4" size="32">
                            <v-icon icon="mdi-heart-flash" size="18"></v-icon>
                        </v-avatar>
                        <span class="font-bold">Matchmaker de Resonancia</span>
                        <div class="loading-dots flex items-center gap-1">
                            <div
                                class="h-1 w-1 animate-bounce rounded-full bg-pink-500"
                            ></div>
                            <div
                                class="h-1 w-1 animate-bounce rounded-full bg-pink-500"
                                style="animation-delay: 0.2s"
                            ></div>
                            <div
                                class="h-1 w-1 animate-bounce rounded-full bg-pink-500"
                                style="animation-delay: 0.4s"
                            ></div>
                        </div>
                    </div>
                    <v-btn
                        variant="text"
                        density="comfortable"
                        icon="mdi-close"
                    ></v-btn>
                </div>
                <div class="h-64 space-y-6 overflow-y-auto bg-slate-950/20 p-8">
                    <div class="flex max-w-2xl gap-4">
                        <v-avatar color="pink-darken-4" size="36">
                            <v-icon icon="mdi-heart-flash" size="20"></v-icon>
                        </v-avatar>
                        <div
                            class="pa-4 rounded-2xl rounded-tl-none border border-white/5 bg-slate-800 text-sm leading-relaxed"
                        >
                            Hola Omar. He analizado tu sesión de assessment y
                            los resultados de resonancia son excepcionales. Veo
                            que tienes una duda sobre cómo integramos los
                            modelos de grafos con la capa agéntica. ¿Te gustaría
                            profundizar en el stack técnico o prefieres ver cómo
                            sería tu plan de carrera aquí?
                        </div>
                    </div>
                    <!-- Candidate Message -->
                    <div class="ml-auto flex max-w-2xl flex-row-reverse gap-4">
                        <v-avatar size="36" class="border border-white/10">
                            <img
                                src="https://ui-avatars.com/api/?name=Omar+Ahumada&background=1e293b&color=fff"
                                alt="Avatar"
                            />
                        </v-avatar>
                        <div
                            class="pa-4 rounded-2xl rounded-tr-none bg-pink-600 text-sm leading-relaxed shadow-lg shadow-pink-600/10"
                        >
                            Me interesa mucho entender el plan de carrera y cómo
                            mi experiencia en grafos impactará en Scenario IQ.
                        </div>
                    </div>
                </div>
                <div
                    class="flex gap-4 border-t border-white/5 bg-slate-900 px-8 py-6"
                >
                    <v-text-field
                        placeholder="Escribe tu duda al Matchmaker..."
                        variant="solo"
                        flat
                        hide-details
                        density="comfortable"
                        bg-color="white/5"
                        class="candidate-input rounded-xl"
                    ></v-text-field>
                    <v-btn
                        color="pink"
                        icon="mdi-send"
                        class="rounded-xl"
                    ></v-btn>
                </div>
            </section>
        </main>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const candidate = ref({
    id: 1042,
    name: 'Omar Ahumada',
    role: 'Sr. Data Architect',
    resonance: 88,
});
</script>

<style scoped>
.candidate-portal {
    background-image:
        radial-gradient(
            at 0% 0%,
            rgba(236, 72, 153, 0.05) 0px,
            transparent 50%
        ),
        radial-gradient(
            at 100% 100%,
            rgba(59, 130, 246, 0.05) 0px,
            transparent 50%
        );
}

.resonance-ring :deep(.v-progress-circular__underlay) {
    stroke: rgba(255, 255, 255, 0.05) !important;
}

.candidate-input :deep(.v-field) {
    border-radius: 12px !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@keyframes bounce {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-3px);
    }
}
</style>
