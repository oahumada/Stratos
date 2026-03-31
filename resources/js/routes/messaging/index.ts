import conversations from './conversations'
import messages from './messages'
import participants from './participants'
import settings from './settings'
import metrics from './metrics'

const messaging = {
    conversations: Object.assign(conversations, conversations),
    messages: Object.assign(messages, messages),
    participants: Object.assign(participants, participants),
    settings: Object.assign(settings, settings),
    metrics: Object.assign(metrics, metrics),
}

export default messaging