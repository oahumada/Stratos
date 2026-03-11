<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    cardData: {
        did: string;
        holder_name: string;
        department: string;
        role: string;
        photo_url?: string;
        level?: number;
        points?: number;
    };
}>();

const formattedDid = computed(() => {
    if (!props.cardData.did) return 'DID:PENDING';
    const parts = props.cardData.did.split(':');
    return `DID:ST:${parts[parts.length - 1].substring(0, 8)}...`;
});

const masteryLevel = computed(() => props.cardData.level || 1);
const progressToNext = computed(
    () => ((props.cardData.points || 0) % 1000) / 10,
);
</script>

<template>
    <div class="talent-pass-card">
        <div class="card-inner">
            <div class="card-bg">
                <div class="glow-1"></div>
                <div class="glow-2"></div>
                <div class="mesh-pattern"></div>
            </div>

            <div class="card-content">
                <div class="card-header">
                    <div class="logo-area">
                        <div class="st-logo">S</div>
                        <span class="platform-name">STRATOS</span>
                    </div>
                    <div class="status-badge">ACTIVE PASS</div>
                </div>

                <div class="card-body">
                    <div class="user-info">
                        <v-avatar size="64" class="user-avatar">
                            <v-img
                                :src="
                                    cardData.photo_url ||
                                    '/placeholder-avatar.png'
                                "
                                cover
                            />
                        </v-avatar>
                        <div class="text-details">
                            <h2 class="holder-name">
                                {{ cardData.holder_name }}
                            </h2>
                            <p class="holder-role">{{ cardData.role }}</p>
                            <p class="holder-dept">{{ cardData.department }}</p>
                        </div>
                    </div>

                    <div class="progress-section">
                        <div
                            class="d-flex justify-space-between text-caption mb-1"
                        >
                            <span class="text-white"
                                >Mastery Level {{ masteryLevel }}</span
                            >
                            <span class="text-indigo-accent-1"
                                >{{ cardData.points || 0 }} SP</span
                            >
                        </div>
                        <v-progress-linear
                            :model-value="progressToNext"
                            height="6"
                            rounded
                            bg-color="rgba(255,255,255,0.1)"
                            color="indigo-accent-2"
                        />
                    </div>
                </div>

                <div class="card-footer">
                    <div class="did-box">
                        <span class="label">IDENTIFIER</span>
                        <span class="value">{{ formattedDid }}</span>
                    </div>
                    <div class="qr-box">
                        <v-icon icon="mdi-qrcode" size="40" color="white" />
                    </div>
                </div>
            </div>

            <!-- Holographic Overlay -->
            <div class="hologram"></div>
        </div>
    </div>
</template>

<style scoped>
.talent-pass-card {
    width: 100%;
    max-width: 360px;
    height: 220px;
    perspective: 1000px;
    margin: 0 auto;
}

.card-inner {
    width: 100%;
    height: 100%;
    position: relative;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    transition: transform 0.6s cubic-bezier(0.23, 1, 0.32, 1);
    background: #0f172a;
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.card-bg {
    position: absolute;
    inset: 0;
    overflow: hidden;
    z-index: 0;
}

.mesh-pattern {
    position: absolute;
    inset: 0;
    background-image: radial-gradient(
        rgba(99, 102, 241, 0.15) 1px,
        transparent 1px
    );
    background-size: 20px 20px;
    opacity: 0.5;
}

.glow-1 {
    position: absolute;
    top: -20%;
    right: -20%;
    width: 60%;
    height: 60%;
    background: radial-gradient(
        circle,
        rgba(99, 102, 241, 0.4) 0%,
        transparent 70%
    );
    filter: blur(40px);
}

.glow-2 {
    position: absolute;
    bottom: -10%;
    left: -10%;
    width: 50%;
    height: 50%;
    background: radial-gradient(
        circle,
        rgba(168, 85, 247, 0.3) 0%,
        transparent 70%
    );
    filter: blur(40px);
}

.card-content {
    position: relative;
    z-index: 2;
    padding: 18px;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.logo-area {
    display: flex;
    align-items: center;
    gap: 8px;
}

.st-logo {
    width: 24px;
    height: 24px;
    background: white;
    color: #0f172a;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 14px;
}

.platform-name {
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 2px;
    color: white;
}

.status-badge {
    font-size: 9px;
    font-weight: 700;
    color: #4ade80;
    background: rgba(74, 222, 128, 0.1);
    padding: 2px 8px;
    border-radius: 100px;
    border: 1px solid rgba(74, 222, 128, 0.2);
}

.user-info {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 16px;
}

.user-avatar {
    border: 2px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.holder-name {
    font-size: 18px;
    font-weight: 700;
    color: white;
    line-height: 1.2;
}

.holder-role {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
}

.holder-dept {
    font-size: 10px;
    color: rgba(255, 255, 255, 0.4);
}

.progress-section {
    margin-top: auto;
    margin-bottom: 12px;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: auto;
}

.did-box {
    display: flex;
    flex-direction: column;
}

.did-box .label {
    font-size: 8px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.3);
    letter-spacing: 1px;
}

.did-box .value {
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    color: rgba(255, 255, 255, 0.6);
}

.qr-box {
    background: rgba(255, 255, 255, 0.05);
    padding: 4px;
    border-radius: 8px;
}

.hologram {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        125deg,
        transparent 0%,
        rgba(255, 255, 255, 0.05) 45%,
        rgba(255, 255, 255, 0.2) 50%,
        rgba(255, 255, 255, 0.05) 55%,
        transparent 100%
    );
    background-size: 200% 200%;
    z-index: 3;
    pointer-events: none;
    animation: hologram-swipe 4s infinite linear;
}

@keyframes hologram-swipe {
    0% {
        background-position: -200% -200%;
    }
    100% {
        background-position: 200% 200%;
    }
}
</style>
