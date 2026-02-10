import people from './people'
import skills from './skills'
import departments from './departments'
import roles from './roles'

const api = {
    people: Object.assign(people, people),
    skills: Object.assign(skills, skills),
    departments: Object.assign(departments, departments),
    roles: Object.assign(roles, roles),
}

export default api