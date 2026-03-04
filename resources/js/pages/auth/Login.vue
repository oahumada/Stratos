<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
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
        title="Acceda a su cuenta"
        description="Ingrese su correo y clave para acceder al sistema."
    >
        <Head title="Ingresar" />

        <!-- Notificaciones de éxito de Inertia o session('status') -->
        <div
            v-if="status || flashStatus"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ status || flashStatus }}
        </div>

        <div
            v-if="flashError"
            class="mb-4 text-center text-sm font-medium text-red-600"
        >
            {{ flashError }}
        </div>

        <!-- Formulario por Contraseña -->
        <Form
            v-if="!isMagicLink"
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Dirección de correo</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Clave</Label>
                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-sm"
                            :tabindex="5"
                        >
                            Olvidó su clave?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Clave de ingreso"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" name="remember" :tabindex="3" />
                        <span>Recordar</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <Spinner v-if="processing" />
                    Ingresar
                </Button>

                <!-- Divisor -->
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <span class="w-full border-t" />
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-background px-2 text-muted-foreground"
                            >O</span
                        >
                    </div>
                </div>

                <!-- Botón de Magic Link (Toggle) -->
                <Button
                    type="button"
                    variant="outline"
                    class="flex w-full items-center gap-2"
                    @click="isMagicLink = true"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="lucide lucide-sparkles"
                    >
                        <path
                            d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"
                        />
                    </svg>
                    Ingresar con Magic Link
                </Button>
            </div>

            <div
                class="mt-2 text-center text-sm text-muted-foreground"
                v-if="canRegister"
            >
                No tiene una cuenta?
                <TextLink :href="register()" :tabindex="5">Registrar</TextLink>
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
                    <p class="mb-4 text-sm text-muted-foreground">
                        Ingresa tu correo y te enviaremos un enlace mágico para
                        iniciar sesión sin contraseña.
                    </p>
                    <div class="grid gap-2">
                        <Label for="magic-email">Dirección de correo</Label>
                        <Input
                            id="magic-email"
                            type="email"
                            v-model="magicForm.email"
                            required
                            autofocus
                            placeholder="email@example.com"
                        />
                        <InputError :message="magicForm.errors.email" />
                    </div>
                </div>

                <Button
                    type="submit"
                    class="w-full"
                    :disabled="magicForm.processing"
                >
                    <Spinner v-if="magicForm.processing" />
                    Enviar Enlace Mágico
                </Button>

                <Button
                    type="button"
                    variant="ghost"
                    class="w-full"
                    @click="isMagicLink = false"
                >
                    Volver a inicio tradicional
                </Button>
            </div>
        </form>
    </AuthBase>
</template>
