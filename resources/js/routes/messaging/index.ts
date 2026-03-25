import conversations from './conversations'
import messages from './messages'
import participants from './participants'

const messaging = {
    conversations: Object.assign(conversations, conversations),
    messages: Object.assign(messages, messages),
    participants: Object.assign(participants, participants),
}

export default messaging