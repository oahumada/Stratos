import people from './people'
import skills from './skills'
import departments from './departments'
import roles from './roles'
import CompetencyLevelBars from './CompetencyLevelBars'
import SkillQuestionBank from './SkillQuestionBank'

const api = {
    people: Object.assign(people, people),
    skills: Object.assign(skills, skills),
    departments: Object.assign(departments, departments),
    roles: Object.assign(roles, roles),
    CompetencyLevelBars: Object.assign(CompetencyLevelBars, CompetencyLevelBars),
    SkillQuestionBank: Object.assign(SkillQuestionBank, SkillQuestionBank),
}

export default api