export interface TableHeader {
    title?: string;
    text?: string;
    value: string;
    key?: string;
    type?: 'date' | 'text' | 'number';
    sortable?: boolean;
    filterable?: boolean;
}

export interface FormField {
    key: string;
    label: string;
    type:
        | 'text'
        | 'email'
        | 'number'
        | 'password'
        | 'select'
        | 'checkbox'
        | 'textarea'
        | 'date'
        | 'time'
        | 'switch';
    rules?: ((v: any) => boolean | string)[];
    placeholder?: string;
    items?: any[];
    required?: boolean;
}

export interface Config {
    endpoints: {
        index: string;
        apiUrl: string;
    };
    titulo: string;
    descripcion?: string;
    permisos?: {
        crear: boolean;
        editar: boolean;
        eliminar: boolean;
    };
    detail?: boolean;
}

export interface TableConfig {
    headers: TableHeader[];
    options?: Record<string, any>;
}

export interface ItemForm {
    fields: FormField[];
    catalogs?: any[];
    layout?: string;
}

export interface FilterConfig {
    field: string;
    type: 'text' | 'select' | 'date';
    label: string;
    items?: any[];
    placeholder?: string;
    catalogKey?: string;
}
