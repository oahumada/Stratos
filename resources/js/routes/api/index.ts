import competencies from './competencies'
import roles from './roles'
import people from './people'
import skills from './skills'
import departments from './departments'
import CompetencyLevelBars from './CompetencyLevelBars'
import SkillQuestionBank from './SkillQuestionBank'
import peopleRelationships from './people-relationships'

const api = {
    competencies: Object.assign(competencies, competencies),
    roles: Object.assign(roles, roles),
    people: Object.assign(people, people),
    skills: Object.assign(skills, skills),
    departments: Object.assign(departments, departments),
    CompetencyLevelBars: Object.assign(CompetencyLevelBars, CompetencyLevelBars),
    SkillQuestionBank: Object.assign(SkillQuestionBank, SkillQuestionBank),
    peopleRelationships: Object.assign(peopleRelationships, peopleRelationships),
}

export default api