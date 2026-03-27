/**
 * Simple API Client Helper
 *
 * Wraps fetch calls with common defaults
 */

export const apiClient = {
    async get(url: string) {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
        });

        if (!response.ok) throw new Error(`API Error: ${response.statusText}`);
        return response.json();
    },

    async post(url: string, data?: any) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: data ? JSON.stringify(data) : undefined,
        });

        if (!response.ok) throw new Error(`API Error: ${response.statusText}`);
        return response.json();
    },

    async put(url: string, data?: any) {
        const response = await fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: data ? JSON.stringify(data) : undefined,
        });

        if (!response.ok) throw new Error(`API Error: ${response.statusText}`);
        return response.json();
    },

    async delete(url: string) {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
        });

        if (!response.ok) throw new Error(`API Error: ${response.statusText}`);
        return response.json();
    },
};
