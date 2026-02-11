export interface TalentComposition {
    human_percentage: number;
    synthetic_percentage: number;
    strategy_suggestion?:
        | 'Buy'
        | 'Build'
        | 'Borrow'
        | 'Synthetic'
        | 'Hybrid'
        | string;
    logic_justification?: string;
}

export interface Role {
    id?: number;
    name: string;
    estimated_fte?: number;
    suggested_agent_type?: string | null;
    talent_composition: TalentComposition;
}

export {};
