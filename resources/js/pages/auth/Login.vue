<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import TextLink from '@/components/TextLink.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const page = usePage();
const flashStatus = computed(() => (page.props as any).flash?.status);
const flashError = computed(() => (page.props as any).flash?.error);

const isMagicLink = ref(false);

const magicForm = useForm({
    email: '',
});

const submitMagicLink = () => {
    magicForm.post('/magic-link', {
        preserveScroll: true,
        onSuccess: () => magicForm.reset(),
    });
};
</script>

<template>
    <AuthBase
        title="Ingreso"
        description="Portal de orquestación de Ingeniería de Talento"
    >
        <template #default>
            <div class="px-6 py-10 md:px-12 md:py-14">
                <Head title="Ingresar" />

                <!-- Notifications -->
                <div
                    v-if="status || flashStatus"
                    class="mb-6 text-center text-sm font-medium text-emerald-400"
                >
                    {{ status || flashStatus }}
                </div>

                <div
                    v-if="flashError"
                    class="mb-6 text-center text-sm font-medium text-red-400"
                >
                    {{ flashError }}
                </div>

                <!-- Form -->
                <Form
                    v-if="!isMagicLink"
                    v-bind="store.form()"
                    :reset-on-success="['password']"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-6"
                >
                    <div class="grid gap-6">
                        <div class="grid gap-2">
                            <Label
                                for="email"
                                class="ml-2 text-[10px] font-black tracking-[0.2em] text-white/40 uppercase"
                                >Dirección de correo</Label
                            >
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                class="h-12 border-white/10 bg-white/5 px-4 text-white placeholder:text-white/20 focus-visible:ring-indigo-500/30"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="email"
                                placeholder="nombre@empresa.com"
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <div class="flex items-center justify-between px-1">
                                <Label
                                    for="password"
                                    class="ml-2 text-[10px] font-black tracking-[0.2em] text-white/40 uppercase"
                                    >Clave</Label
                                >
                                <TextLink
                                    v-if="canResetPassword"
                                    :href="request()"
                                    class="px-2 text-[10px] font-black tracking-widest text-indigo-400! uppercase no-underline! hover:text-indigo-300!"
                                    :tabindex="5"
                                >
                                    Olvidó su clave?
                                </TextLink>
                            </div>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                class="h-12 border-white/10 bg-white/5 px-4 text-white placeholder:text-white/20 focus-visible:ring-indigo-500/30"
                                required
                                :tabindex="2"
                                autocomplete="current-password"
                                placeholder="Su clave de acceso"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <div class="flex items-center justify-between px-1">
                            <Label
                                for="remember"
                                class="group flex cursor-pointer items-center space-x-3"
                            >
                                <Checkbox
                                    id="remember"
                                    name="remember"
                                    :tabindex="3"
                                    class="border-white/50! bg-white/5! data-[state=checked]:border-indigo-400! data-[state=checked]:bg-indigo-500!"
                                />
                                <span
                                    class="text-[11px] font-bold text-white/40 transition-colors group-hover:text-white/70"
                                    >Recordar sesión</span
                                >
                            </Label>
                        </div>

                        <StButtonGlass
                            type="submit"
                            variant="primary"
                            block
                            size="lg"
                            class="mt-2"
                            :tabindex="4"
                            :loading="processing"
                            data-test="login-button"
                        >
                            Iniciar Sesión
                        </StButtonGlass>

                        <!-- Divisor -->
                        <div class="relative py-4">
                            <div class="absolute inset-0 flex items-center">
                                <span class="w-full border-t border-white/5" />
                            </div>
                            <div
                                class="relative flex justify-center text-[10px] font-black tracking-[0.3em] uppercase"
                            >
                                <span class="px-6 text-white/10"
                                    >O continúe con</span
                                >
                            </div>
                        </div>

                        <!-- Botón de Magic Link (Toggle) -->
                        <StButtonGlass
                            type="button"
                            variant="glass"
                            block
                            class="group"
                            @click="isMagicLink = true"
                        >
                            <v-icon
                                size="18"
                                class="mr-2 text-indigo-400 transition-transform group-hover:scale-110"
                                >mdi-auto-fix</v-icon
                            >
                            Acceso con Magic Link
                        </StButtonGlass>
                    </div>

                    <div
                        class="mt-6 text-center text-xs font-medium text-white/30"
                        v-if="canRegister"
                    >
                        No tiene una cuenta?
                        <TextLink
                            :href="register()"
                            :tabindex="5"
                            class="ml-2 text-[10px] font-black tracking-widest text-indigo-400! uppercase no-underline! hover:text-indigo-300!"
                            >Solicitar Acceso</TextLink
                        >
                    </div>
                </Form>

                <!-- Formulario de Magic Link -->
                <form
                    v-else
                    @submit.prevent="submitMagicLink"
                    class="flex flex-col gap-6"
                >
                    <div class="grid gap-6">
                        <div>
                            <p
                                class="mb-6 px-1 text-center text-xs leading-relaxed font-medium text-white/40"
                            >
                                Ingrese su correo institucional y le enviaremos
                                un
                                <span class="text-indigo-400"
                                    >Enlace Mágico</span
                                >
                                para una autenticación instantánea.
                            </p>
                            <div class="grid gap-2">
                                <Label
                                    for="magic-email"
                                    class="ml-1 text-[10px] font-black tracking-widest text-white/40 uppercase"
                                    >Correo Institucional</Label
                                >
                                <Input
                                    id="magic-email"
                                    type="email"
                                    class="h-12 border-white/10 bg-white/5 px-4 text-white placeholder:text-white/20 focus-visible:ring-indigo-500/30"
                                    v-model="magicForm.email"
                                    required
                                    autofocus
                                    placeholder="nombre@empresa.com"
                                />
                                <InputError :message="magicForm.errors.email" />
                            </div>
                        </div>

                        <StButtonGlass
                            type="submit"
                            variant="primary"
                            block
                            size="lg"
                            :loading="magicForm.processing"
                        >
                            Enviar Protocolo de Acceso
                        </StButtonGlass>

                        <StButtonGlass
                            type="button"
                            variant="ghost"
                            block
                            size="sm"
                            @click="isMagicLink = false"
                        >
                            Volver al método tradicional
                        </StButtonGlass>
                    </div>
                </form>
            </div>
        </template>
    </AuthBase>
</template>
