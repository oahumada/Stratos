<?php

use App\Enums\MessageState;

describe('MessageState Enum', function () {
    it('provides correct labels', function () {
        expect(MessageState::SENT->label())->toBe('Enviado');
        expect(MessageState::DELIVERED->label())->toBe('Entregado');
        expect(MessageState::READ->label())->toBe('Leído');
        expect(MessageState::FAILED->label())->toBe('Fallido');
    });

    it('identifies terminal states correctly', function () {
        expect(MessageState::SENT->isTerminal())->toBeFalse();
        expect(MessageState::DELIVERED->isTerminal())->toBeFalse();
        expect(MessageState::READ->isTerminal())->toBeTrue();
        expect(MessageState::FAILED->isTerminal())->toBeTrue();
    });

    it('allows valid state transitions', function () {
        // SENT → DELIVERED
        expect(MessageState::SENT->canTransition(MessageState::DELIVERED))->toBeTrue();

        // SENT → FAILED
        expect(MessageState::SENT->canTransition(MessageState::FAILED))->toBeTrue();

        // DELIVERED → READ
        expect(MessageState::DELIVERED->canTransition(MessageState::READ))->toBeTrue();

        // DELIVERED → FAILED
        expect(MessageState::DELIVERED->canTransition(MessageState::FAILED))->toBeTrue();

        // FAILED → SENT (retry)
        expect(MessageState::FAILED->canTransition(MessageState::SENT))->toBeTrue();

        // FAILED → DELIVERED (retry after failure)
        expect(MessageState::FAILED->canTransition(MessageState::DELIVERED))->toBeTrue();
    });

    it('rejects invalid state transitions', function () {
        // SENT → READ (must go through DELIVERED first)
        expect(MessageState::SENT->canTransition(MessageState::READ))->toBeFalse();

        // READ → any other state (terminal)
        expect(MessageState::READ->canTransition(MessageState::SENT))->toBeFalse();
        expect(MessageState::READ->canTransition(MessageState::DELIVERED))->toBeFalse();
        expect(MessageState::READ->canTransition(MessageState::FAILED))->toBeFalse();

        // SENT → SENT (no self-transitions)
        expect(MessageState::SENT->canTransition(MessageState::SENT))->toBeFalse();
    });
});
