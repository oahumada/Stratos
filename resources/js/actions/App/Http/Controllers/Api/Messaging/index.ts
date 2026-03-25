import ConversationController from './ConversationController'
import MessageController from './MessageController'
import ParticipantController from './ParticipantController'

const Messaging = {
    ConversationController: Object.assign(ConversationController, ConversationController),
    MessageController: Object.assign(MessageController, MessageController),
    ParticipantController: Object.assign(ParticipantController, ParticipantController),
}

export default Messaging