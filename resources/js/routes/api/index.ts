import CompetencyLevelBars from './CompetencyLevelBars';
import SkillQuestionBank from './SkillQuestionBank';
import competencies from './competencies';
import departments from './departments';
import people from './people';
import peopleRelationships from './people-relationships';
import roles from './roles';
import skills from './skills';

const api = {
    competencies: Object.assign(competencies, competencies),
    roles: Object.assign(roles, roles),
    people: Object.assign(people, people),
    skills: Object.assign(skills, skills),
    departments: Object.assign(departments, departments),
    CompetencyLevelBars: Object.assign(
        CompetencyLevelBars,
        CompetencyLevelBars,
    ),
    SkillQuestionBank: Object.assign(SkillQuestionBank, SkillQuestionBank),
    peopleRelationships: Object.assign(
        peopleRelationships,
        peopleRelationships,
    ),
};

export default api;
