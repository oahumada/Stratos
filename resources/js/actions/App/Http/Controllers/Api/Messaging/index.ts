import ConversationController from './ConversationController'
import MessageController from './MessageController'
import ParticipantController from './ParticipantController'
import MessagingSettingsController from './MessagingSettingsController'

const Messaging = {
    ConversationController: Object.assign(ConversationController, ConversationController),
    MessageController: Object.assign(MessageController, MessageController),
    ParticipantController: Object.assign(ParticipantController, ParticipantController),
    MessagingSettingsController: Object.assign(MessagingSettingsController, MessagingSettingsController),
}

export default Messaging