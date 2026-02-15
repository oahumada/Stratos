
    BEGIN
        INSERT INTO workforce_plans (
            id, organization_id, name, code, description, start_date, end_date,
            planning_horizon_months, scope_type, scope_notes, strategic_context, budget_constraints,
            legal_constraints, labor_relations_constraints, status, owner_user_id, sponsor_user_id,
            created_by, updated_by, created_at, updated_at
        ) VALUES (
            NEW.id, NEW.organization_id, NEW.name, NEW.code, NEW.description, NEW.start_date, NEW.end_date,
            NEW.horizon_months, NEW.scope_type, NEW.scope_notes, NEW.strategic_context, NEW.budget_constraints,
            NEW.legal_constraints, NEW.labor_relations_constraints, NEW.status, NEW.owner_user_id, NEW.sponsor_user_id,
            NEW.created_by, NEW.updated_by, NEW.created_at, NEW.updated_at
        )
        ON CONFLICT (id) DO UPDATE SET
            organization_id = EXCLUDED.organization_id,
            name = EXCLUDED.name,
            code = EXCLUDED.code,
            description = EXCLUDED.description,
            start_date = EXCLUDED.start_date,
            end_date = EXCLUDED.end_date,
            planning_horizon_months = EXCLUDED.planning_horizon_months,
            scope_type = EXCLUDED.scope_type,
            scope_notes = EXCLUDED.scope_notes,
            strategic_context = EXCLUDED.strategic_context,
            budget_constraints = EXCLUDED.budget_constraints,
            legal_constraints = EXCLUDED.legal_constraints,
            labor_relations_constraints = EXCLUDED.labor_relations_constraints,
            status = EXCLUDED.status,
            owner_user_id = EXCLUDED.owner_user_id,
            sponsor_user_id = EXCLUDED.sponsor_user_id,
            created_by = EXCLUDED.created_by,
            updated_by = EXCLUDED.updated_by,
            updated_at = EXCLUDED.updated_at;
        RETURN NEW;
    END;
    