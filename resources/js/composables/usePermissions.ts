import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { readonly, ref } from 'vue';

// Estado global para compartir entre componentes
const isLoaded = ref(false);
const user = ref<any>(null);
const role = ref<string>('collaborator');
const roleDisplay = ref<string>('');
const permissions = ref<string[]>([]);
const isAdmin = ref<boolean>(false);

export function usePermissions() {
    /**
     * Inicializa permisos desde Inertia shared props (instantáneo, sin API call).
     * Útil para renderizado condicional en la primera carga de cualquier página.
     */
    const initFromInertia = () => {
        try {
            const page = usePage();
            const auth = (page.props as any)?.auth;
            if (auth?.user) {
                user.value = auth.user;
                role.value = auth.role ?? auth.user?.role ?? 'collaborator';
                permissions.value = auth.permissions ?? [];
                isAdmin.value = role.value === 'admin';
                isLoaded.value = true;
            }
        } catch {
            // Si se llama fuera de contexto Inertia, silenciar
        }
    };

    // Intentar inicializar desde Inertia al instanciar
    if (!isLoaded.value) {
        initFromInertia();
    }

    /**
     * Carga los datos del usuario actual y sus permisos desde la API.
     * Fallback cuando los datos no están disponibles via Inertia.
     */
    const loadPermissions = async () => {
        if (isLoaded.value) return;

        try {
            const { data } = await axios.get('/api/auth/me');

            user.value = data.data;
            role.value = data.data.role;
            roleDisplay.value = data.data.role_display;
            permissions.value = data.data.permissions || [];
            isAdmin.value = data.data.is_admin || false;

            isLoaded.value = true;
        } catch (error) {
            console.error('Error loading RBAC permissions:', error);
            isLoaded.value = false;
        }
    };

    /**
     * Verifica si el usuario tiene un permiso específico.
     * @param permissionName e.g. 'scenarios.create'
     */
    const can = (permissionName: string): boolean => {
        if (!isLoaded.value) return false;
        if (isAdmin.value) return true;
        return permissions.value.includes(permissionName);
    };

    /**
     * Verifica si el usuario tiene AL MENOS UNO de los permisos.
     */
    const canAny = (permissionNames: string[]): boolean => {
        if (!isLoaded.value) return false;
        if (isAdmin.value) return true;
        return permissionNames.some((p) => permissions.value.includes(p));
    };

    /**
     * Verifica si el usuario tiene acceso a un módulo (cualquier acción).
     * e.g. canModule('scenarios') → true si tiene 'scenarios.view' o 'scenarios.create', etc.
     */
    const canModule = (module: string): boolean => {
        if (!isLoaded.value) return false;
        if (isAdmin.value) return true;
        return permissions.value.some((p) => p.startsWith(`${module}.`));
    };

    /**
     * Verifica si el usuario pertenece al rol dado.
     */
    const hasRole = (...roles: string[]): boolean => {
        return roles.includes(role.value);
    };

    /**
     * Verifica si el usuario está al menos en cierto nivel de rol.
     * Jerarquía: admin > hr_leader > manager > collaborator > observer
     */
    const roleHierarchy: Record<string, number> = {
        admin: 100,
        hr_leader: 80,
        manager: 60,
        collaborator: 40,
        observer: 20,
    };

    const isAtLeast = (minimumRole: string): boolean => {
        const currentLevel = roleHierarchy[role.value] ?? 0;
        const requiredLevel = roleHierarchy[minimumRole] ?? 0;
        return currentLevel >= requiredLevel;
    };

    return {
        isLoaded: readonly(isLoaded),
        user: readonly(user),
        role: readonly(role),
        roleDisplay: readonly(roleDisplay),
        isAdmin: readonly(isAdmin),
        permissions: readonly(permissions),
        loadPermissions,
        initFromInertia,
        can,
        canAny,
        canModule,
        hasRole,
        isAtLeast,
    };
}
