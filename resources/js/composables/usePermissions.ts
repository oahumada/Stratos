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
     * Carga los datos del usuario actual y sus permisos desde la API
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
            // Si falla (ej. no autenticado), dejamos el estado por defecto
            isLoaded.value = false;
        }
    };

    /**
     * Verifica si el usuario tiene un permiso específico
     * @param permissionName e.g. 'scenarios.create'
     */
    const can = (permissionName: string): boolean => {
        // Si no ha cargado, intentamos fallar seguro (false)
        if (!isLoaded.value) return false;

        if (isAdmin.value) return true;

        return permissions.value.includes(permissionName);
    };

    /**
     * Verifica si el usuario tiene AL MENOS UNO de los permisos
     */
    const canAny = (permissionNames: string[]): boolean => {
        if (!isLoaded.value) return false;
        if (isAdmin.value) return true;

        return permissionNames.some((p) => permissions.value.includes(p));
    };

    /**
     * Verifica si el usuario pertenece al rol dado
     */
    const hasRole = (roleName: string): boolean => {
        return role.value === roleName;
    };

    return {
        isLoaded: readonly(isLoaded),
        user: readonly(user),
        role: readonly(role),
        roleDisplay: readonly(roleDisplay),
        isAdmin: readonly(isAdmin),
        permissions: readonly(permissions),
        loadPermissions,
        can,
        canAny,
        hasRole,
    };
}
