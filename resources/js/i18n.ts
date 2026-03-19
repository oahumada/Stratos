export const messages = {
    en: {
        scenario_iq: 'Scenario IQ',
        active_simulation: 'Active Simulation',
        ai_analyzing: 'AI ANALYZING',
        success_prob: 'Success Prob',
        est_synergy: 'Est. Synergy',
        cultural_friction: 'Cultural Friction',
        time_to_peak: 'Time to Peak',
        recalculate: 'Recalculate Scenario',
        generate_remediation: 'Generate Remediation Plan',
        sentinel_plan: 'Sentinel Remediation Plan:',
        training_target: 'Training Target:',
        close: 'Close',
        approve: 'Approve',
        reject: 'Reject',
        execute_apply: 'Execute Apply',
        agent_proposals: {
            title: 'Agentic Role Design Proposals',
            strategy_engine: 'Strategy Engine:',
            alignment_score: 'Alignment Score',
            loading_title: 'Agents are collaborating...',
            loading_desc:
                'Analyzing strategic blueprint, organizational catalog, and talent archetypes',
            empty_title: 'No proposals available.',
            empty_desc: 'Try re-consulting the agents to generate new designs.',
            protocol_title: 'Design Protocol',
            protocol_desc:
                'The <strong>Role Designer</strong> has proposed adjustments based on the strategic session. Review each role, verify its <strong>competency mapping</strong> and <strong>archetype</strong>, then approve to commit changes to the scenario matrix.',
            proposed_roles: 'Proposed Roles',
            approved: '{count} / {total} Approved',
            approve_all: 'Approve All',
            reject_all: 'Reject All',
            undo: 'Undo',
            approve_role: 'Approve',
            role_archetype: 'Role Archetype',
            target_fte: 'Target FTE',
            units: 'Units',
            talent_composition: 'Talent Composition',
            proposed_mapping: 'Proposed Mapping ({count})',
            col_competency: 'Competency',
            col_change_type: 'Change Type',
            col_req_level: 'Required Level',
            col_core: 'Core',
            col_diagnostic: 'Diagnostic',
            restore_role: 'Restore Role',
            proposed_catalog: 'Proposed Catalog Updates',
            selected_blueprint: 'Selected Blueprint',
            roles_count: '{count} Roles',
            competencies_count: '{count} Competencies',
            cancel: 'Cancel',
            commit_changes: 'Commit Changes',
        },
        pending_feedback: {
            title: 'Pending Feedback 360',
            subtitle:
                'Synthesize professional insights for {count} collaborators.',
            analysis_for: 'Analysis for {name}',
            correlation: '{relationship} Correlation',
            strategic_insight: 'Strategic Insight: {question}',
            capture_details: 'Capture detailed qualitative observations...',
            close: 'Close',
            submit: 'Submit Assessment',
            unknown_skill: 'Unknown Skill',
        },
        role_wizard: {
            title: 'Role Architect',
            ai_powered: 'AI-Powered',
            core_orchestrator: 'Core Orchestrator',
            cerbero_engine: 'Cerbero Neural Engine',
            protocol_note: 'Protocol Note',
            protocol_desc:
                'Structural design ensures role depth aligns with strategic organizational velocity.',
            step1_title: 'Goal Definition',
            step1_desc: 'Purpose & Mission',
            step2_title: 'Cube Mapping',
            step2_desc: 'Structural Coords',
            step3_title: 'Blueprint',
            step3_desc: 'Skill Architecture',
            define_node: 'Define the Execution Node',
            define_node_desc:
                'Describe the primary mission and critical priorities. AI will synthesize the multi-dimensional coordinates.',
            architectural_label: 'Architectural Label',
            architectural_placeholder: 'e.g., Strategic Horizon Lead',
            mission_synthesis: 'Mission Synthesis',
            mission_placeholder:
                'What is the strategic reason for this role? What are the top impact domains?',
            initiate_synthesis: 'Initiate Synthesis',
            cube_dimensions: 'Cube Dimensions',
            cube_desc:
                'Neural engine suggested these coordinates based on the organizational lattice.',
            analysis_complete: 'Analysis Complete',
            axis_x: 'Axis X: Behavioral Archetype',
            axis_x_strategic: 'Strategic',
            axis_x_tactical: 'Tactical',
            axis_x_operational: 'Operational',
            horizon_3: 'Horizon 3',
            horizon_2: 'Horizon 2',
            horizon_1: 'Horizon 1',
            axis_y: 'Axis Y: Mastery Level',
            tier: 'Tier {level}',
            axis_z: 'Axis Z: Business Process Anchor',
            anchor_placeholder: 'Specify process domain...',
            synthesis_rationale: 'Synthesis Rationale',
            optimization_tip: 'Optimization Tip',
            back_stage: 'Back Stage',
            confirm_arch: 'Confirm Architecture',
            capacity_blueprint: 'Capacity Blueprint',
            capacity_desc:
                'Define the core strategic assets required for this architectural node.',
            strategic_capacity: 'Strategic Capacity',
            mastery_req: 'Mastery Req.',
            ai_rationale: 'AI Rationale',
            ops: 'Ops',
            add_manual: 'Add Manual Definition',
            adjustment_phase: 'Adjustment Phase',
            deploy_arch: 'Deploy To Architecture',
            new_capacity: 'New Capacity',
            manual_rationale: 'Manually defined by architect.',
        },
        px_command: {
            title: 'Strategy',
            title_highlight: 'People Experience',
            subtitle:
                'Preventive monitoring of organizational health and engagement.',
            launch_campaign: 'Launch Campaign',
            metrics: {
                active: 'Active',
                avg_engagement: 'Avg Engagement',
                sentiment_index: 'Sentiment Index',
                burnout_risk: 'Burnout Risk',
            },
            history_title: 'Active Campaigns & History',
            search_placeholder: 'Search campaign...',
            headers: {
                name: 'Campaign Name',
                topics: 'Topics',
                mode: 'Deployment',
                impact: 'AI Impact',
                status: 'Status',
            },
            no_campaigns: 'No campaigns configured for People Experience.',
            launch_first: 'Launch First Campaign',
            wizard: {
                title: 'PX Design',
                subtitle: 'Configure the active listening of the organization.',
                step1_title: 'Topics & Goal',
                step2_title: 'Frequency & Mode',
                step3_title: 'Scope',
                step4_title: 'Validation',
                close: 'Close',
                step1: {
                    title: 'Measurement Focus',
                    desc: 'What dimensions of the human experience do we want to monitor?',
                    name_label: 'Campaign Name',
                    name_placeholder: 'e.g., Burnout Thermometer 2026',
                    dimensions: 'Dimensions to Evaluate',
                },
                step2: {
                    title: 'Distribution Engine',
                    desc: 'Define the level of AI autonomy to capture organizational sentiment.',
                    ai_enabled: 'Sentinel Intelligence Active:',
                    ai_desc:
                        "AI will analyze each user's workload and launch micro-surveys only when it detects a low emotional friction window.",
                },
                step3: {
                    title: 'Prospecting Scope',
                    desc: 'Configure what percentage of the population should be impacted by this campaign.',
                    segmentation: 'Sample Segmentation',
                    random_traffic: 'Daily random traffic per user',
                    sample_desc:
                        'A sample of {pct}% guarantees anonymity and reduces survey fatigue.',
                },
                step4: {
                    title: 'Campaign Ready',
                    desc: 'Review the campaign DNA before deployment in Sentinel.',
                    name_summary: 'Campaign Name',
                    distribution_summary: 'Distribution',
                    metrics_summary: '{count} Dimensions',
                    metrics_label: 'Metrics',
                    sentinel_alert:
                        'Sentinel will monitor traffic and send preventive notifications if it detects burnout risks in the selected departments.',
                },
                actions: {
                    back: 'Go Back',
                    next: 'Next Step',
                    launch: 'Establish Campaign',
                },
            },
            default_desc: 'Active prospecting',
            modes: {
                ai_autonomous: 'Autonomous AI',
                ai_autonomous_desc: 'Sentinel decides the best time to ask.',
                recurring: 'Recurring',
                recurring_desc: 'Scheduled periodic measurements.',
                specific_date: 'Specific Pulse',
                specific_date_desc: 'Mass delivery on an exact date and time.',
            },
            topics: {
                clima: 'Work Climate',
                stress: 'Stress & Burnout',
                happiness: 'Happiness (eNPS)',
                health: 'Occupational Health',
                leadership: 'Leadership & Support',
            },
            scopes: {
                randomized_sample: 'Random Sample (AI)',
                all: 'Total Population',
                department: 'Critical Departments',
            },
            date_ia_decides: 'AI Decides',
            risk_low: 'Low',
        },
        assessment_command: {
            title: 'Cerbero Command Unit',
            subtitle:
                'Intelligent orchestration of assessment cycles and 360 talent.',
            config_cycle: 'Configure Cycle',
            stats: {
                active_cycles: 'Active Cycles',
                collaborators: 'Collaborators',
                avg_completion: 'Avg. Completion',
                pending_evals: 'Pending Evals',
            },
            history_title: 'Orchestration History',
            search_placeholder: 'Search cycle...',
            headers: {
                id: 'Cycle Identification',
                mode: 'Modality',
                progress: 'Progress',
                status: 'Status',
                launch: 'Launch',
            },
            no_data: 'Launch first cycle',
            wizard: {
                title: 'Configuration',
                subtitle: 'Define the DNA of your assessment cycle.',
                steps: {
                    identity: 'Identity & Type',
                    scope: 'Population & Scope',
                    instruments: 'Instruments & Network',
                    confirmation: 'Confirmation',
                },
                step1: {
                    label: 'STEP 1',
                    title: 'Cycle Identity',
                    desc: 'Name this cycle and select how the AI should trigger the assessments.',
                    name_label: 'Cycle Name',
                    name_placeholder: 'e.g. Potential Assessment Q1 2026',
                    description_label: 'Description / Goal',
                    description_placeholder:
                        'Brief explanation of the purpose of this measurement...',
                    mode_title: 'Trigger Modality',
                },
                step2: {
                    label: 'STEP 2',
                    title: 'Population & Scope',
                    desc: 'Who do we want to evaluate this time?',
                    segmentation_label: 'Population Segmentation',
                    segmentation_options: {
                        all: 'Entire Organization',
                        department: 'By Department',
                        scenario: 'By Strategic Scenario',
                        hipo: 'High Potentials Only (HiPo)',
                    },
                    scenario_alert:
                        'This option synchronizes the cycle with Scenario IQ scenarios, evaluating only the impacted roles.',
                    schedule_title: 'Projected Schedule',
                    start_date: 'Start Date',
                    end_date: 'End Date',
                },
                step3: {
                    label: 'STEP 3',
                    title: 'Instruments & 360 Network',
                    desc: 'Configure the measurement tools and who will participate as evaluators.',
                    instruments_title: 'Measurement Models',
                    network_title: 'Feedback Network (Views)',
                    network_options: {
                        self: 'Self-assessment',
                        manager: 'Direct Manager',
                        reports: 'Direct Reports',
                        ai: 'AI Cerbero',
                    },
                    peers_label: 'No. of random Peers per person',
                },
                step4: {
                    title: 'Launch Summary',
                    desc: 'Review the final configuration before registering the cycle in the system.',
                    summary_labels: {
                        name: 'Cycle Name',
                        mode: 'Modality',
                        instruments: '{count} Selected',
                        instruments_label: 'Instruments',
                        ai_active: 'Artificial Intelligence Active',
                        ai_inactive: 'AI Deactivated',
                        ai_label: 'Cerbero Engine',
                    },
                    preview_title: 'Estimated Scope Preview',
                    preview_loading:
                        'Cerbero is calculating the universe of participants...',
                    preview_participants: 'Target Participants',
                    preview_areas: '360 Areas / Networks',
                    warning:
                        'The cycle will be saved as <strong>Draft</strong>. You can edit the detailed scope of people before launching it officially.',
                },
                actions: {
                    cancel: 'Cancel',
                    back: 'Go Back',
                    continue: 'Continue',
                    launch: 'Launch Configuration',
                },
            },
            modes: {
                specific_date: {
                    title: 'Specific Date',
                    desc: 'One-time launch on a specific date.',
                },
                quarterly: {
                    title: 'Quarterly',
                    desc: 'Measurement cycles every 3 months.',
                },
                annual: {
                    title: 'Annual (Anniversary)',
                    desc: 'Triggered on the start date of each collaborator.',
                },
                continuous: {
                    title: 'Random Continuous',
                    desc: 'AI decides when to measure to avoid saturation.',
                },
            },
            instruments: {
                bars: 'BARS Assessment (Competencies)',
                pulse: 'Pulse Survey (Climate)',
                disc: 'Psychometric Profile (DISC)',
                interview: 'AI Interview (Cerbero)',
            },
            status: {
                draft: 'Draft',
                scheduled: 'Scheduled',
                active: 'Active',
                completed: 'Completed',
                cancelled: 'Cancelled',
            },
            actions_tooltips: {
                activate: 'Activate Cycle Officially',
                dashboard: 'View Tracking Dashboard',
            },
            date_none: 'Not defined',
        },
        roles_module: {
            title: 'Repository of Roles',
            ai_cube_design: 'Design with Cube AI',
            tabs: {
                info: 'Information',
                skills: 'Skills ({count})',
                people: 'People ({count})',
                ai_design: 'Role Design (AI)',
                historical: 'Simulation History',
            },
            info_section: {
                title: 'Role Information',
                name: 'Name',
                description: 'Description',
                ai_agent: 'AI Agent',
                blueprint: 'Strategic Blueprint',
                design_btn: 'Design with AI (Cube Model)',
            },
            skills_section: {
                title: 'Required Skills',
                empty: 'No skills assigned yet',
                level: 'Required Level',
                critical: 'Critical',
            },
            people_section: {
                title: 'People in this Role',
                empty: 'No people assigned yet',
                no_email: 'No email provided',
            },
            ai_section: {
                title: 'Design Analysis (Role Cube Methodology)',
                reanalyze: 'Re-analyze',
                not_analyzed_title: 'Design Not Analyzed',
                not_analyzed_desc:
                    'Use Artificial Intelligence to define high-resolution coordinates for this role within the organization lattice.',
                analyze_btn: 'Analyze with AI',
                coordinates: 'CUBE COORDINATES',
                axis_x: 'Axis X: Behavioral Archetype',
                axis_y: 'Axis Y: Mastery Level',
                axis_z: 'Axis Z: Business Process Anchor',
                mastery_suffix: '/5',
                justification: 'DESIGN RATIONALE',
                core_competencies: 'Suggested Core Competencies',
                org_clarity: 'Organizational Clarity & Recommendations',
            },
        },
        competencies_module: {
            title: 'Competency Framework',
            tabs: {
                info: 'Base Information',
                ai_design: 'AI Design',
            },
            info_section: {
                no_description: 'No description provided',
                status_active: 'Active',
                status_draft: 'Draft',
                skills_count: '{count} associated Skills',
                ai_agent: 'Design Agent',
            },
            ai_section: {
                title: 'AI Curation ({agent})',
                description:
                    'Automatically generate details, BARS, indicators, and learning paths.',
                curate_btn: 'Curate Competency',
                alert_title: 'Attention',
                alert_desc:
                    'This competency has no associated skills yet. AI will derive sub-skills first.',
                ready_desc:
                    'This competency is ready for AI curation. Click the button above to start.',
            },
        },
        form_schema: {
            active_badge: 'Active',
            new_btn: 'New {title}',
            filters_title: 'Smart Filters',
            global_search: 'Global Search',
            search_placeholder: 'Search records...',
            clear_filters: 'Clear Filters',
            loading_records: 'Loading records...',
            no_records: 'No records found matching your criteria',
            reset_filters: 'Reset all filters',
            id_placeholder: 'ID or Name',
            dialog: {
                edit: 'Edit',
                create: 'Create',
                cancel: 'Cancel',
                save: 'Save Changes',
                create_record: 'Create Record',
                delete_confirm: 'Confirm Delete',
                delete_ask: 'Are you sure you want to delete this record?',
                delete_warning: 'This action cannot be undone.',
                delete_btn: 'Delete Permanently',
                close: 'Close',
            },
        },
        people_module: {
            title: 'People Management',
            description:
                'Manage people, their skills and professional development.',
            tabs: {
                active_skills: 'Active Skills',
                potential: 'AI Potential',
                development: 'Development',
                history: 'History',
            },
            profile: {
                dept: 'Dept',
                role: 'Role',
                hired: 'Hired',
                sync_btn: 'Sync with Role',
                view_profile: 'View Full Profile',
            },
            skills: {
                title: 'Active Skills',
                empty: 'No active skills in `people_role_skills` pivot.',
                level_label: 'Required Level',
            },
            potential: {
                analyzed_ok: 'Profile successfully analyzed.',
                blind_spots: 'Visualizing Blind Spots',
                traits_title: 'Detected Traits & Aptitudes',
                reevaluate_btn: 'Re-evaluate Potential',
            },
            history: {
                empty: 'History not loaded (requires inactive `people_role_skills` endpoint).',
            },
        },
        talent_development: {
            title: 'Development Path',
            empty_state:
                'This person does not have an active growth or gap closure plan assigned.',
            generate_btn: 'Generate Plan',
            request_mentorship: 'Request Mentorship',
            months: 'months',
            actions_title: 'Development Actions',
            session_log: 'Session Log',
            searching_mentor: 'Searching for Mentor...',
            pending_assignment: 'Pending assignment',
            manage_evidence: 'Manage Evidence',
            launch_lms: 'Launch LMS Course',
            launch_btn: 'Launch',
            sync_lms: 'Sync Progress',
            status: {
                pending: 'Pending',
                in_progress: 'In Progress',
                completed: 'Completed',
                cancelled: 'Cancelled',
            },
            mentorship_sessions: {
                title: 'Mentorship Sessions',
                tabs: {
                    history: 'History',
                    new: 'New Session',
                },
                empty: 'No sessions registered yet.',
                next_steps: 'Next Steps',
                form: {
                    date: 'Session Date',
                    duration: 'Duration (min)',
                    summary: 'Session Summary',
                    summary_placeholder:
                        'What did you talk about? What was covered?',
                    next_steps: 'Commitments / Next Steps',
                    next_steps_placeholder: 'Actions for the next meeting',
                    save: 'Save Session',
                },
            },
            evidence: {
                title: 'Progress Evidence',
                subtitle: 'Upload documents, links or notes',
                tabs: {
                    history: 'History',
                    new: 'New Evidence',
                },
                empty: 'No evidence has been uploaded yet.',
                confirm_delete:
                    'Are you sure you want to delete this evidence?',
                types: {
                    file: 'File',
                    link: 'Link',
                    text: 'Note',
                },
                form: {
                    title: 'Evidence Title',
                    title_placeholder: 'E.g. Project completion certificate',
                    description: 'Brief Description',
                    description_placeholder:
                        'What does this evidence represent?',
                    type: 'Evidence Type',
                    file: 'Select File',
                    file_click: 'Click to select or drag file',
                    url: 'External URL',
                    save: 'Save Evidence',
                },
            },
            create_plan: {
                trigger: 'New Plan',
                title: 'Generate Development Plan',
                subtitle: 'AI-driven gap analysis and roadmap',
                form: {
                    select_skill: 'Select Skill to Improve',
                    level_evolution: 'Level {current} ➔ {target}',
                },
                analysis: {
                    title: 'Gap Analysis',
                    current: 'Current Level',
                    target: 'Required Level',
                    description:
                        'Generating a {months}-month plan with training, mentoring and projects (70-20-10) to close this gap.',
                },
                actions: {
                    cancel: 'Cancel',
                    generate: 'Generate Plan',
                },
            },
        },
        gamification: {
            points_title: 'Stratos Points',
            badges_title: 'Achievement Badges',
            recent_achievements: 'Recent Achievements',
            no_badges: 'No badges earned yet.',
            points_earned: 'Points earned:',
            level: 'Level {n}',
        },
        smart_alerts: {
            title: 'Smart Alerts',
            pending: '{count} pendings',
            everything_clear: 'Everything clear',
            view_all: 'View All Notifications',
            categories: {
                talent: 'Talent Insight',
                scenario: 'Scenario Update',
                learning: 'Learning Opportunity',
                infrastructure: 'Infrastructure',
                system: 'System Alert',
            },
        },
        landings: {
            core: {
                title: 'Stratos Core',
                slogan: 'Inventory, roles, competencies, and skills — The foundation of your talent.',
                modules: {
                    people_inventory: {
                        title: 'People Inventory',
                        description: 'People management and lifecycle.',
                    },
                    role_catalog: {
                        title: 'Role Catalog',
                        description: 'Strategic role definition.',
                    },
                    competency_dictionary: {
                        title: 'Competency Dictionary',
                        description: 'Competency taxonomy.',
                    },
                    skills_matrix: {
                        title: 'Skills Matrix',
                        description: 'Detailed technical inventory.',
                    },
                    organization_flow: {
                        title: 'Organization Flow',
                        description: 'Hierarchical visualization.',
                    },
                    stratos_map: {
                        title: 'Stratos Map',
                        description: 'Real-time skills radiography.',
                    },
                },
            },
            px: {
                title: 'Stratos PX',
                slogan: 'Experience and growth — Employee retention and development.',
                modules: {
                    mi_stratos: {
                        title: 'My Stratos',
                        description:
                            'Personal dashboard, KPIs, and gamification.',
                    },
                    comando_px: {
                        title: 'PX Command',
                        description: 'Burnout and climate dashboard.',
                    },
                    stratos_match: {
                        title: 'Stratos Match',
                        description: 'Internal mobility and fitness match.',
                    },
                },
            },
            growth: {
                title: 'Stratos Growth',
                slogan: 'Boost development — Paths, mentoring, and opportunities.',
                modules: {
                    stratos_navigator: {
                        title: 'Stratos Navigator',
                        description: 'AI for learning paths.',
                    },
                    stratos_360: {
                        title: 'Stratos 360',
                        description: 'Holistic assessment and feedback.',
                    },
                    commander_360: {
                        title: 'Commander 360',
                        description: 'Psychometric triangulation.',
                    },
                    social_learning_hub: {
                        title: 'Social Learning Hub',
                        description: 'Peer-to-peer platform.',
                    },
                    mentoring: {
                        title: 'Mentoring',
                        description: 'AI and human mentoring.',
                    },
                    opportunity_marketplace: {
                        title: 'Opportunity Marketplace',
                        description: 'Internal opportunities.',
                    },
                },
            },
            magnet: {
                title: 'Stratos Magnet',
                slogan: 'Talent attraction — Recruiting and external matching.',
                modules: {
                    magnet_home: {
                        title: 'Magnet Home',
                        description: 'External recruiting portal.',
                    },
                    candidate_portal: {
                        title: 'Candidate Portal',
                        description: 'Application tracking.',
                    },
                },
            },
            control_center: {
                title: 'Control Center',
                slogan: 'Governance, security, and quality — Monitor and manage the platform.',
                modules: {
                    rbac_manager: {
                        title: 'RBAC Manager',
                        description: 'Roles and permissions management.',
                    },
                    ai_agent_supervisor: {
                        title: 'AI Agent Supervisor',
                        description: 'Supervision of LLM agents.',
                    },
                    quality_sentinel: {
                        title: 'Quality Sentinel',
                        description: 'Reliability and RAGAS monitoring.',
                    },
                    stratos_compliance: {
                        title: 'Stratos Compliance',
                        description:
                            'Auditing, ISO, GDPR, and externally verifiable evidence.',
                    },
                    ragas_neural_dash: {
                        title: 'RAGAS Neural Dash',
                        description: 'LLM output analytics.',
                    },
                    comando_360: {
                        title: '360 Command',
                        description:
                            'Assessment cycle and policy orchestration.',
                    },
                    comando_px: {
                        title: 'PX Command',
                        description: 'Burnout and climate dashboard.',
                    },
                    cultural_blueprint: {
                        title: 'Stratos Identity',
                        description:
                            'Management of the Organizational Constitution: Mission, Vision, and Values.',
                    },
                },
            },
            radar: {
                title: 'Stratos Radar',
                slogan: 'Predictive prevention — Anticipate risks and talent churn.',
                modules: {
                    strategic_scenario_list: {
                        title: 'Strategic Scenario List',
                        description: 'Strategic simulations.',
                    },
                    executive_dashboard: {
                        title: 'Executive Dashboard',
                        description:
                            'CEO-focused strategic oversight and KPI direction.',
                    },
                    gap_analysis_engine: {
                        title: 'Gap Analysis Engine',
                        description:
                            'Contrast between inventory and target state.',
                    },
                    investor_dashboard: {
                        title: 'Investor Dashboard',
                        description:
                            'Financial-oriented ROI and value visibility.',
                    },
                },
            },
        },
    },
    es: {
        scenario_iq: 'Scenario IQ',
        active_simulation: 'Simulación Activa',
        ai_analyzing: 'IA ANALIZANDO',
        success_prob: 'Probabilidad Éxito',
        est_synergy: 'Sinergia Estimada',
        cultural_friction: 'Fricción Cultural',
        time_to_peak: 'Tiempo Pico',
        recalculate: 'Recalcular Escenario',
        generate_remediation: 'Generar Plan de Remediación',
        sentinel_plan: 'Plan de Remediación (Sentinel):',
        training_target: 'Capacitación Objetivo:',
        close: 'Cerrar',
        approve: 'Aprobar',
        reject: 'Rechazar',
        execute_apply: 'Ejecutar Aplicación',
        agent_proposals: {
            title: 'Propuestas de Diseño de Rol de IA',
            strategy_engine: 'Motor Estratégico:',
            alignment_score: 'Puntaje de Alineación',
            loading_title: 'Los agentes están colaborando...',
            loading_desc:
                'Analizando plano estratégico, catálogo organizacional y arquetipos de talento',
            empty_title: 'No hay propuestas disponibles.',
            empty_desc:
                'Intenta reconsultar a los agentes para generar nuevos diseños.',
            protocol_title: 'Protocolo de Diseño',
            protocol_desc:
                'El <strong>Diseñador de Roles</strong> ha propuesto ajustes basados en la sesión estratégica. Revisa cada rol, verifica su <strong>mapeo de competencias</strong> y <strong>arquetipo</strong>, luego aprueba para confirmar cambios en la matriz del escenario.',
            proposed_roles: 'Roles Propuestos',
            approved: '{count} / {total} Aprobados',
            approve_all: 'Aprobar Todos',
            reject_all: 'Rechazar Todos',
            undo: 'Deshacer',
            approve_role: 'Aprobar',
            role_archetype: 'Arquetipo de Rol',
            target_fte: 'FTE Objetivo',
            units: 'Unidades',
            talent_composition: 'Composición de Talento',
            proposed_mapping: 'Mapeo Propuesto ({count})',
            col_competency: 'Competencia',
            col_change_type: 'Tipo de Cambio',
            col_req_level: 'Nivel Requerido',
            col_core: 'Core',
            col_diagnostic: 'Diagnóstico',
            restore_role: 'Restaurar Rol',
            proposed_catalog: 'Actualizaciones de Catálogo',
            selected_blueprint: 'Plano Seleccionado',
            roles_count: '{count} Roles',
            competencies_count: '{count} Competencias',
            cancel: 'Cancelar',
            commit_changes: 'Confirmar Cambios',
        },
        pending_feedback: {
            title: 'Retroalimentación 360 pendiente',
            subtitle:
                'Sintetiza la visión profesional de {count} colaboradores.',
            analysis_for: 'Análisis para {name}',
            correlation: 'Correlación {relationship}',
            strategic_insight: 'Visión Estratégica: {question}',
            capture_details: 'Captura observaciones cualitativas detalladas...',
            close: 'Cerrar',
            submit: 'Enviar Evaluación',
            unknown_skill: 'Habilidad Desconocida',
        },
        role_wizard: {
            title: 'Arquitecto de Roles',
            ai_powered: 'Impulsado por IA',
            core_orchestrator: 'Orquestador Central',
            cerbero_engine: 'Motor Neuronal Cerbero',
            protocol_note: 'Nota de Protocolo',
            protocol_desc:
                'El diseño estructural asegura que la profundidad del rol se alinee con la velocidad estratégica organizacional.',
            step1_title: 'Definición de Metas',
            step1_desc: 'Propósito y Misión',
            step2_title: 'Mapeo del Cubo',
            step2_desc: 'Coord. Estructurales',
            step3_title: 'Plano',
            step3_desc: 'Arquitectura de Habil.',
            define_node: 'Definir el Nodo de Ejecución',
            define_node_desc:
                'Describe la misión principal y las prioridades críticas. La IA sintetizará las coordenadas multidimensionales.',
            architectural_label: 'Etiqueta Arquitectónica',
            architectural_placeholder: 'ej. Líder de Horizonte Estratégico',
            mission_synthesis: 'Síntesis de la Misión',
            mission_placeholder:
                '¿Cuál es la razón estratégica para este rol? ¿Cuáles son los dominios de mayor impacto?',
            initiate_synthesis: 'Iniciar Síntesis',
            cube_dimensions: 'Dimensiones del Cubo',
            cube_desc:
                'El motor neuronal sugirió estas coordenadas basándose en la estructura organizacional.',
            analysis_complete: 'Análisis Completo',
            axis_x: 'Eje X: Arquetipo Conductual',
            axis_x_strategic: 'Estratégico',
            axis_x_tactical: 'Táctico',
            axis_x_operational: 'Operacional',
            horizon_3: 'Horizonte 3',
            horizon_2: 'Horizonte 2',
            horizon_1: 'Horizonte 1',
            axis_y: 'Eje Y: Nivel de Maestría',
            tier: 'Nivel {level}',
            axis_z: 'Eje Z: Ancla de Proceso de Negocio',
            anchor_placeholder: 'Especifica el dominio del proceso...',
            synthesis_rationale: 'Justificación de Síntesis',
            optimization_tip: 'Consejo de Optimización',
            back_stage: 'Etapa Anterior',
            confirm_arch: 'Confirmar Arquitectura',
            capacity_blueprint: 'Plano de Capacidades',
            capacity_desc:
                'Define los activos estratégicos clave requeridos para este nodo arquitectónico.',
            strategic_capacity: 'Capacidad Estratégica',
            mastery_req: 'Req. Maestría',
            ai_rationale: 'Justificación IA',
            ops: 'Ops',
            add_manual: 'Agregar Definición Manual',
            adjustment_phase: 'Fase de Ajuste',
            deploy_arch: 'Desplegar en Arquitectura',
            new_capacity: 'Nueva Capacidad',
            manual_rationale: 'Definición manual por el arquitecto.',
        },
        px_command: {
            title: 'Estrategia',
            title_highlight: 'People Experience',
            subtitle:
                'Monitoreo preventivo de salud organizacional y engagement.',
            launch_campaign: 'Lanzar Campaña',
            metrics: {
                active: 'Activas',
                avg_engagement: 'Compromiso prom.',
                sentiment_index: 'Índice de Sentimiento',
                burnout_risk: 'Riesgo de Burnout',
            },
            history_title: 'Campañas Vigentes & Historial',
            search_placeholder: 'Buscar campaña...',
            headers: {
                name: 'Nombre de Campaña',
                topics: 'Tópicos',
                mode: 'Despliegue',
                impact: 'Impacto IA',
                status: 'Estado',
            },
            no_campaigns:
                'No hay campañas configuradas para Experiencia de Personas.',
            launch_first: 'Lanzar Primera Campaña',
            wizard: {
                title: 'Diseño PX',
                subtitle: 'Configura la escucha activa de la organización.',
                step1_title: 'Tópicos & Objetivo',
                step2_title: 'Frecuencia & Modo',
                step3_title: 'Alcance',
                step4_title: 'Validación',
                close: 'Cerrar',
                step1: {
                    title: 'Foco de Medición',
                    desc: '¿Qué dimensiones de la experiencia humana queremos monitorear?',
                    name_label: 'Nombre de la Campaña',
                    name_placeholder: 'Ej. Termómetro de Burnout 2026',
                    dimensions: 'Dimensiones a Evaluar',
                },
                step2: {
                    title: 'Motor de Distribución',
                    desc: 'Define el nivel de autonomía de la IA para capturar el sentimiento organizacional.',
                    ai_enabled: 'Inteligencia Sentinel Activa:',
                    ai_desc:
                        'La IA analizará la carga de trabajo de cada usuario y lanzará las micro-encuestas solo cuando detecte una ventana de baja fricción emocional.',
                },
                step3: {
                    title: 'Alcance de Prospección',
                    desc: 'Configura a qué porcentaje de la población debe impactar esta campaña.',
                    segmentation: 'Segmentación de Muestra',
                    random_traffic: 'Tráfico aleatorio diario por usuario',
                    sample_desc:
                        'Una muestra del {pct}% garantiza anonimato y reduce la fatiga de encuesta.',
                },
                step4: {
                    title: 'Campaña Lista',
                    desc: 'Revisa el ADN de la campaña antes del despliegue en Sentinel.',
                    name_summary: 'Nombre de la Campaña',
                    distribution_summary: 'Distribución',
                    metrics_summary: '{count} Dimensiones',
                    metrics_label: 'Métricas',
                    sentinel_alert:
                        'Sentinel monitoreará el tráfico y enviará notificaciones preventivas si detecta riesgos de burnout en los departamentos seleccionados.',
                },
                actions: {
                    back: 'Regresar',
                    next: 'Siguiente Paso',
                    launch: 'Establecer Campaña',
                },
            },
            default_desc: 'Prospección activa',
            modes: {
                ai_autonomous: 'IA Autónoma',
                ai_autonomous_desc:
                    'Sentinel decide el mejor momento para preguntar.',
                recurring: 'Recurrente',
                recurring_desc: 'Mediciones periódicas programadas.',
                specific_date: 'Pulso Específico',
                specific_date_desc: 'Envío masivo en una fecha y hora exacta.',
            },
            topics: {
                clima: 'Clima Laboral',
                stress: 'Estrés & Burnout',
                happiness: 'Felicidad (eNPS)',
                health: 'Salud Ocupacional',
                leadership: 'Liderazgo & Apoyo',
            },
            scopes: {
                randomized_sample: 'Muestra Aleatoria (IA)',
                all: 'Población Total',
                department: 'Departamentos Críticos',
            },
            date_ia_decides: 'IA Decide',
            risk_low: 'Bajo',
        },
        assessment_command: {
            title: 'Unidad de Comando Cerbero',
            subtitle:
                'Orquestación inteligente de ciclos de evaluación y talento 360.',
            config_cycle: 'Configurar Ciclo',
            stats: {
                active_cycles: 'Ciclos Activos',
                collaborators: 'Colaboradores',
                avg_completion: 'Completitud prom.',
                pending_evals: 'Evals. Pendientes',
            },
            history_title: 'Historial de Orquestaciones',
            search_placeholder: 'Buscar ciclo...',
            headers: {
                id: 'Identificación del Ciclo',
                mode: 'Modalidad',
                progress: 'Progreso',
                status: 'Estado',
                launch: 'Lanzamiento',
            },
            no_data: 'Lanzar primer ciclo',
            wizard: {
                title: 'Configuración',
                subtitle: 'Define el ADN de tu ciclo de evaluación.',
                steps: {
                    identity: 'Identidad & Tipo',
                    scope: 'Población & Alcance',
                    instruments: 'Instrumentos & Red',
                    confirmation: 'Confirmación',
                },
                step1: {
                    label: 'PASO 1',
                    title: 'Identidad del Ciclo',
                    desc: 'Nombra este ciclo y selecciona cómo la IA debe disparar las evaluaciones.',
                    name_label: 'Nombre del Ciclo',
                    name_placeholder: 'Ej. Evaluación de Potencial Q1 2026',
                    description_label: 'Descripción / Objetivo',
                    description_placeholder:
                        'Breve explicación del propósito de esta medición...',
                    mode_title: 'Modalidad de Disparo',
                },
                step2: {
                    label: 'PASO 2',
                    title: 'Población & Alcance',
                    desc: '¿A quiénes queremos evaluar en esta ocasión?',
                    segmentation_label: 'Segmentación de Población',
                    segmentation_options: {
                        all: 'Toda la Organización',
                        department: 'Por Departamento',
                        scenario: 'Por Escenario Estratégico',
                        hipo: 'Solo alto potencial (HiPo)',
                    },
                    scenario_alert:
                        'Esta opción sincroniza el ciclo con los escenarios de Scenario IQ, evaluando solo a los roles impactados.',
                    schedule_title: 'Cronograma Proyectado',
                    start_date: 'Fecha de Inicio',
                    end_date: 'Fecha de Cierre',
                },
                step3: {
                    label: 'PASO 3',
                    title: 'Instrumentos & Red 360',
                    desc: 'Configura las herramientas de medición y quiénes participarán como evaluadores.',
                    instruments_title: 'Modelos de Medición',
                    network_title: 'Red de Retroalimentación (Vistas)',
                    network_options: {
                        self: 'Autoevaluación',
                        manager: 'Jefe Directo',
                        reports: 'Reportes Directos',
                        ai: 'IA Cerbero',
                    },
                    peers_label: 'Nº de Pares aleatorios por persona',
                },
                step4: {
                    title: 'Resumen de Lanzamiento',
                    desc: 'Revisa la configuración final antes de registrar el ciclo en el sistema.',
                    summary_labels: {
                        name: 'Nombre del Ciclo',
                        mode: 'Modalidad',
                        instruments: '{count} Seleccionados',
                        instruments_label: 'Instrumentos',
                        ai_active: 'Inteligencia Artificial Activada',
                        ai_inactive: 'IA Desactivada',
                        ai_label: 'Motor Cerbero',
                    },
                    preview_title: 'Vista previa del alcance estimado',
                    preview_loading:
                        'Cerbero está calculando el universo de participantes...',
                    preview_participants: 'Participantes objetivo',
                    preview_areas: 'Áreas / Redes 360',
                    warning:
                        'El ciclo se guardará como <strong>Borrador</strong>. Podrás editar el alcance detallado de personas antes de lanzarlo oficialmente.',
                },
                actions: {
                    cancel: 'Cancelar',
                    back: 'Regresar',
                    continue: 'Continuar',
                    launch: 'Lanzar Configuración',
                },
            },
            modes: {
                specific_date: {
                    title: 'Fecha Específica',
                    desc: 'Lanzamiento único en una fecha puntual.',
                },
                quarterly: {
                    title: 'Trimestral',
                    desc: 'Ciclos de medición cada 3 meses.',
                },
                annual: {
                    title: 'Anual (Aniversario)',
                    desc: 'Se dispara en la fecha de ingreso de cada colaborador.',
                },
                continuous: {
                    title: 'Continuo Aleatorio',
                    desc: 'La IA decide cuándo medir para no saturar.',
                },
            },
            instruments: {
                bars: 'Evaluación BARS (Competencias)',
                pulse: 'Encuesta de pulso (clima)',
                disc: 'Perfil Psicométrico (DISC)',
                interview: 'Entrevista de IA (Cerbero)',
            },
            status: {
                draft: 'Borrador',
                scheduled: 'Programado',
                active: 'Activo',
                completed: 'Completado',
                cancelled: 'Cancelado',
            },
            actions_tooltips: {
                activate: 'Activar Ciclo Oficialmente',
                dashboard: 'Ver tablero de seguimiento',
            },
            date_none: 'Sin definir',
        },
        roles_module: {
            title: 'Repositorio de Roles',
            ai_cube_design: 'Diseñar con Cubo IA',
            tabs: {
                info: 'Información',
                skills: 'Habilidades ({count})',
                people: 'Personas ({count})',
                ai_design: 'Diseño de Rol (IA)',
                historical: 'Historial de Simulación',
            },
            info_section: {
                title: 'Información del Rol',
                name: 'Nombre',
                description: 'Descripción',
                ai_agent: 'Agente IA',
                blueprint: 'Plantilla Estratégica',
                design_btn: 'Diseñar con IA (Modelo Cubo)',
            },
            skills_section: {
                title: 'Habilidades requeridas',
                empty: 'No hay habilidades asignadas',
                level: 'Nivel Requerido',
                critical: 'Crítica',
            },
            people_section: {
                title: 'Personas en este rol',
                empty: 'No hay personas asignadas',
                no_email: '(Sin correo)',
            },
            ai_section: {
                title: 'Análisis de Diseño (Metodología Cubo de Roles)',
                reanalyze: 'Volver a analizar',
                not_analyzed_title: 'Diseño no analizado',
                not_analyzed_desc:
                    'Usa la Inteligencia Artificial para definir las coordenadas de alta resolución de este rol en la red organizacional.',
                analyze_btn: 'Analizar con IA',
                coordinates: 'COORDENADAS DEL CUBO',
                axis_x: 'Eje X: Arquetipo Conductual',
                axis_y: 'Eje Y: Nivel de Maestría',
                axis_z: 'Eje Z: Ancla de Proceso de Negocio',
                mastery_suffix: '/5',
                justification: 'JUSTIFICACIÓN DEL DISEÑO',
                core_competencies: 'Competencias clave sugeridas',
                org_clarity: 'Nitidez Organizacional & Recomendaciones',
            },
        },
        competencies_module: {
            title: 'Marco de Competencias',
            tabs: {
                info: 'Información Base',
                ai_design: 'Diseño IA',
            },
            info_section: {
                no_description: 'Sin descripción',
                status_active: 'Activo',
                status_draft: 'Borrador',
                skills_count: '{count} habilidades asociadas',
                ai_agent: 'Agente de Diseño',
            },
            ai_section: {
                title: 'Curación con IA ({agent})',
                description:
                    'Genera los detalles, BARS, indicadores y rutas de aprendizaje automáticamente.',
                curate_btn: 'Curar Competencia',
                alert_title: 'Atención',
                alert_desc:
                    'Esta competencia aún no tiene habilidades asociadas. La IA intentará derivar las sub-habilidades primero.',
                ready_desc:
                    'Esta competencia está lista para la curación por IA. Haz clic en el botón de arriba para comenzar.',
            },
        },
        form_schema: {
            active_badge: 'Activo',
            new_btn: 'Nuevo {title}',
            filters_title: 'Filtros Inteligentes',
            global_search: 'Búsqueda Global',
            search_placeholder: 'Buscar registros...',
            clear_filters: 'Limpiar Filtros',
            loading_records: 'Cargando registros...',
            no_records: 'No se encontraron registros que coincidan',
            reset_filters: 'Reiniciar todos los filtros',
            id_placeholder: 'ID o Nombre',
            dialog: {
                edit: 'Editar',
                create: 'Crear',
                cancel: 'Cancelar',
                save: 'Guardar Cambios',
                create_record: 'Crear Registro',
                delete_confirm: 'Confirmar Eliminación',
                delete_ask:
                    '¿Estás seguro de que deseas eliminar este registro?',
                delete_warning: 'Esta acción no se puede deshacer.',
                delete_btn: 'Eliminar Permanentemente',
                close: 'Cerrar',
            },
        },
        people_module: {
            title: 'Gestión de Personas',
            description:
                'Gestionar personas, sus habilidades y desarrollo profesional.',
            tabs: {
                active_skills: 'Habilidades Activas',
                potential: 'Potencial IA',
                development: 'Desarrollo',
                history: 'Historial',
            },
            profile: {
                dept: 'Depto',
                role: 'Rol',
                hired: 'Contratación',
                sync_btn: 'Sincronizar con rol',
                view_profile: 'Ver Perfil Completo',
            },
            skills: {
                title: 'Habilidades activas',
                empty: 'Sin habilidades activas en el pivote `people_role_skills`.',
                level_label: 'Nivel Requerido',
            },
            potential: {
                analyzed_ok: 'Perfil analizado satisfactoriamente.',
                blind_spots: 'Visualización de Puntos Ciegos',
                traits_title: 'Rasgos y Aptitudes Detectadas',
                reevaluate_btn: 'Re-evaluar Potencial',
            },
            history: {
                empty: 'Historial no cargado (requiere endpoint de `people_role_skills` inactivos).',
            },
        },
        talent_development: {
            title: 'Plan de Desarrollo',
            empty_state:
                'Esta persona no tiene asignado ningún plan de crecimiento o cierre de brechas.',
            generate_btn: 'Generar Plan',
            request_mentorship: 'Solicitar Mentoría',
            months: 'meses',
            actions_title: 'Acciones de Desarrollo',
            session_log: 'Bitácora de Sesiones',
            searching_mentor: 'Buscando Mentor...',
            pending_assignment: 'Pendiente de asignación',
            manage_evidence: 'Gestionar Evidencias',
            launch_lms: 'Lanzar Curso LMS',
            launch_btn: 'Lanzar',
            sync_lms: 'Sincronizar Progreso',
            status: {
                pending: 'Pendiente',
                in_progress: 'En curso',
                completed: 'Completado',
                cancelled: 'Cancelado',
            },
            mentorship_sessions: {
                title: 'Sesiones de Mentoría',
                tabs: {
                    history: 'Historial',
                    new: 'Nueva Sesión',
                },
                empty: 'No hay sesiones registradas actualmente.',
                next_steps: 'Siguientes Pasos',
                form: {
                    date: 'Fecha de la Sesión',
                    duration: 'Duración (min)',
                    summary: 'Resumen de la Sesión',
                    summary_placeholder: '¿De qué hablaron? ¿Qué se cubrió?',
                    next_steps: 'Compromisos / Siguientes Pasos',
                    next_steps_placeholder: 'Acciones para la próxima reunión',
                    save: 'Guardar Sesión',
                },
            },
            evidence: {
                title: 'Evidencias de Progreso',
                subtitle: 'Sube documentos, enlaces o notas',
                tabs: {
                    history: 'Historial',
                    new: 'Nueva Evidencia',
                },
                empty: 'Aún no se han subido evidencias.',
                confirm_delete:
                    '¿Estás seguro de que deseas eliminar esta evidencia?',
                types: {
                    file: 'Archivo',
                    link: 'Enlace',
                    text: 'Nota',
                },
                form: {
                    title: 'Título de la evidencia',
                    title_placeholder:
                        'Ej. Certificado de finalización de curso',
                    description: 'Descripción resumida',
                    description_placeholder: '¿Qué representa esta evidencia?',
                    type: 'Tipo de Evidencia',
                    file: 'Seleccionar archivo',
                    file_click:
                        'Haz clic para seleccionar o arrastra un archivo',
                    url: 'URL externa',
                    save: 'Guardar Evidencia',
                },
            },
            create_plan: {
                trigger: 'Nuevo Plan',
                title: 'Generar Plan de Desarrollo',
                subtitle: 'Análisis de brechas y hoja de ruta con IA',
                form: {
                    select_skill: 'Seleccionar habilidad a mejorar',
                    level_evolution: 'Nivel {current} ➔ {target}',
                },
                analysis: {
                    title: 'Análisis de Brecha',
                    current: 'Nivel Actual',
                    target: 'Nivel Requerido',
                    description:
                        'Se generará un plan de {months} meses con formación, mentoría y proyectos (70-20-10) para cerrar esta brecha.',
                },
                actions: {
                    cancel: 'Cancelar',
                    generate: 'Generar Plan',
                },
            },
        },
        gamification: {
            points_title: 'Puntos Stratos',
            badges_title: 'Insignias de Logro',
            recent_achievements: 'Logros Recientes',
            no_badges: 'Aún no has ganado insignias.',
            points_earned: 'Puntos ganados:',
            level: 'Nivel {n}',
        },
        smart_alerts: {
            title: 'Alertas Inteligentes',
            pending: '{count} pendientes',
            everything_clear: 'Todo despejado',
            view_all: 'Ver todas las notificaciones',
            categories: {
                talent: 'Talento',
                scenario: 'Escenario',
                learning: 'Aprendizaje',
                infrastructure: 'Infraestructura',
                system: 'Sistema',
            },
        },
        landings: {
            core: {
                title: 'Stratos Core',
                slogan: 'Inventario, roles, competencias y habilidades — La base de tu talento.',
                modules: {
                    people_inventory: {
                        title: 'Inventario de Personas',
                        description: 'Gestión de personas y ciclo de vida.',
                    },
                    role_catalog: {
                        title: 'Catálogo de Roles',
                        description: 'Definición estratégica de roles.',
                    },
                    competency_dictionary: {
                        title: 'Diccionario de Competencias',
                        description: 'Taxonomía de competencias.',
                    },
                    skills_matrix: {
                        title: 'Matriz de Habilidades',
                        description: 'Inventario técnico detallado.',
                    },
                    organization_flow: {
                        title: 'Flujo Organizacional',
                        description: 'Visualización jerárquica.',
                    },
                    stratos_map: {
                        title: 'Stratos Map',
                        description:
                            'Radiografía de habilidades en tiempo real.',
                    },
                },
            },
            px: {
                title: 'Stratos PX',
                slogan: 'Experiencia y crecimiento — Retención y desarrollo del colaborador.',
                modules: {
                    mi_stratos: {
                        title: 'Mi Stratos',
                        description: 'Tablero personal, KPIs, gamificación.',
                    },
                    comando_px: {
                        title: 'Comando PX',
                        description: 'Tablero de burnout y clima.',
                    },
                    stratos_match: {
                        title: 'Stratos Match',
                        description: 'Movilidad interna y ajuste de afinidad.',
                    },
                },
            },
            growth: {
                title: 'Stratos Growth',
                slogan: 'Impulsa el desarrollo — Rutas, mentoría y oportunidades.',
                modules: {
                    stratos_navigator: {
                        title: 'Stratos Navigator',
                        description: 'IA para rutas de aprendizaje.',
                    },
                    stratos_360: {
                        title: 'Stratos 360',
                        description:
                            'Evaluación holística y retroalimentación.',
                    },
                    commander_360: {
                        title: 'Commander 360',
                        description: 'Triangulación psicométrica.',
                    },
                    social_learning_hub: {
                        title: 'Social Learning Hub',
                        description: 'Plataforma de aprendizaje entre pares.',
                    },
                    mentoring: {
                        title: 'Mentoría',
                        description: 'Mentoría IA y humana.',
                    },
                    opportunity_marketplace: {
                        title: 'Mercado de Oportunidades',
                        description: 'Oportunidades internas.',
                    },
                },
            },
            magnet: {
                title: 'Stratos Magnet',
                slogan: 'Atracción de talento — Reclutamiento y encaje externo.',
                modules: {
                    magnet_home: {
                        title: 'Inicio Magnet',
                        description: 'Portal de reclutamiento externo.',
                    },
                    candidate_portal: {
                        title: 'Portal de Candidatos',
                        description: 'Seguimiento de postulaciones.',
                    },
                },
            },
            control_center: {
                title: 'Centro de Control',
                slogan: 'Gobierno, seguridad y calidad — Supervisa y administra la plataforma.',
                modules: {
                    rbac_manager: {
                        title: 'Gestor RBAC',
                        description: 'Gestión de roles y permisos.',
                    },
                    ai_agent_supervisor: {
                        title: 'Supervisor de Agentes IA',
                        description: 'Supervisión de agentes LLM.',
                    },
                    quality_sentinel: {
                        title: 'Centinela de Calidad',
                        description: 'Monitoreo de fiabilidad y RAGAS.',
                    },
                    stratos_compliance: {
                        title: 'Stratos Compliance',
                        description:
                            'Auditoría, ISO, GDPR y evidencia verificable externamente.',
                    },
                    ragas_neural_dash: {
                        title: 'Panel Neural RAGAS',
                        description: 'Analítica de salidas LLM.',
                    },
                    comando_360: {
                        title: 'Comando 360',
                        description:
                            'Orquestación de ciclos de evaluación y políticas.',
                    },
                    comando_px: {
                        title: 'Comando PX',
                        description: 'Tablero de burnout y clima.',
                    },
                    cultural_blueprint: {
                        title: 'Stratos Identity',
                        description:
                            'Gestión de la Constitución Organizacional: Misión, Visión y Valores.',
                    },
                },
            },
            radar: {
                title: 'Stratos Radar',
                slogan: 'Prevención predictiva — Anticipa riesgos y fuga de talento.',
                modules: {
                    strategic_scenario_list: {
                        title: 'Lista de Escenarios Estratégicos',
                        description: 'Simulaciones estratégicas.',
                    },
                    executive_dashboard: {
                        title: 'Dashboard Ejecutivo',
                        description:
                            'Vista estratégica para CEOs con foco en KPIs clave.',
                    },
                    gap_analysis_engine: {
                        title: 'Motor de Análisis de Brechas',
                        description: 'Contraste entre inventario y objetivo.',
                    },
                    investor_dashboard: {
                        title: 'Dashboard Inversor',
                        description:
                            'Vista financiera orientada a ROI y creación de valor.',
                    },
                },
            },
        },
    },
};
