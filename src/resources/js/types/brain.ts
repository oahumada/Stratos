export interface NodeItem {
  id: number;
  name: string;
  x?: number | null;
  y?: number | null;
  is_critical?: boolean;
  // optional metadata fields used by Brain/PrototypeMap
  importance?: number;
  level?: number;
  required?: number;
  description?: string;
  competencies?: any[];
}

export interface Edge {
  source: number;
  target: number;
  isCritical?: boolean;
}

export interface ConnectionPayload {
  // support both naming conventions used in the codebase
  source?: number | string;
  target?: number | string;
  source_id?: number | string;
  target_id?: number | string;
  is_critical?: boolean;
}

export interface ScenarioShape {
  id?: number;
  name?: string;
  capabilities?: NodeItem[];
  connections?: ConnectionPayload[];
}
