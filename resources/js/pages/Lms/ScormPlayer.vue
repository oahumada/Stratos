<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'

const props = defineProps<{
  packageId: number
}>()

interface LaunchData {
  package: {
    id: number
    title: string
    version: string
    entry_point: string
    identifier: string
    status: string
  }
  tracking: {
    id: number
    lesson_status: string
    score_raw: number | null
    score_min: number | null
    score_max: number | null
    total_time: string
    session_count: number
    suspend_data: string | null
    lesson_location: string | null
    cmi_data: Record<string, string> | null
    completed_at: string | null
  }
  launch_url: string
}

const launchData = ref<LaunchData | null>(null)
const loading = ref(true)
const error = ref<string | null>(null)
const iframeRef = ref<HTMLIFrameElement | null>(null)

// Local CMI data store
const cmiState = ref<Record<string, string>>({})
let initialized = false
let finished = false

function buildScormApi() {
  return {
    LMSInitialize(_param: string): string {
      if (initialized) return 'true'
      initialized = true
      finished = false

      // Pre-populate CMI state from tracking data
      if (launchData.value?.tracking) {
        const t = launchData.value.tracking
        cmiState.value = { ...(t.cmi_data || {}) }
        cmiState.value['cmi.core.lesson_status'] = t.lesson_status || 'not attempted'
        if (t.score_raw !== null) cmiState.value['cmi.core.score.raw'] = String(t.score_raw)
        if (t.score_min !== null) cmiState.value['cmi.core.score.min'] = String(t.score_min)
        if (t.score_max !== null) cmiState.value['cmi.core.score.max'] = String(t.score_max)
        cmiState.value['cmi.core.total_time'] = t.total_time || '0000:00:00'
        if (t.suspend_data) cmiState.value['cmi.suspend_data'] = t.suspend_data
        if (t.lesson_location) cmiState.value['cmi.core.lesson_location'] = t.lesson_location
        cmiState.value['cmi.core.lesson_mode'] = 'normal'
        cmiState.value['cmi.core.credit'] = 'credit'
        cmiState.value['cmi.core.entry'] = t.lesson_status === 'not attempted' ? 'ab-initio' : 'resume'
      }

      return 'true'
    },

    LMSFinish(_param: string): string {
      if (finished) return 'true'
      finished = true
      commitToServer()
      return 'true'
    },

    LMSGetValue(element: string): string {
      return cmiState.value[element] ?? ''
    },

    LMSSetValue(element: string, value: string): string {
      cmiState.value[element] = value
      return 'true'
    },

    LMSCommit(_param: string): string {
      commitToServer()
      return 'true'
    },

    LMSGetLastError(): string {
      return '0'
    },

    LMSGetErrorString(_errorCode: string): string {
      return 'No error'
    },

    LMSGetDiagnostic(_errorCode: string): string {
      return 'No diagnostic information available'
    },
  }
}

async function commitToServer() {
  if (!launchData.value) return
  try {
    await axios.post(`/api/lms/scorm/${props.packageId}/cmi`, {
      cmi_data: cmiState.value,
    })
    // Refresh tracking status
    const resp = await axios.get(`/api/lms/scorm/${props.packageId}/tracking`)
    if (launchData.value && resp.data?.data) {
      launchData.value.tracking.lesson_status = resp.data.data.lesson_status
      launchData.value.tracking.completed_at = resp.data.data.completed_at
    }
  } catch {
    // Silently handle save errors
  }
}

function getStatusColor(status: string): string {
  switch (status) {
    case 'completed':
    case 'passed':
      return 'success'
    case 'incomplete':
      return 'warning'
    case 'failed':
      return 'error'
    default:
      return 'default'
  }
}

function handleClose() {
  if (initialized && !finished) {
    ;(window as any).API?.LMSFinish('')
  }
  window.history.back()
}

onMounted(async () => {
  try {
    const response = await axios.get(`/api/lms/scorm/${props.packageId}/launch`)
    launchData.value = response.data.data

    // Install SCORM RTE API on window
    const api = buildScormApi()
    ;(window as any).API = api

    loading.value = false
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Error loading SCORM package'
    loading.value = false
  }
})

onBeforeUnmount(() => {
  if (initialized && !finished) {
    ;(window as any).API?.LMSFinish('')
  }
  delete (window as any).API
})
</script>

<template>
  <div class="scorm-player">
    <!-- Header bar -->
    <div class="scorm-header">
      <div class="scorm-header__left">
        <h3 v-if="launchData" class="scorm-header__title">{{ launchData.package.title }}</h3>
        <span v-if="launchData" class="scorm-header__chip" :class="`chip--${getStatusColor(launchData.tracking.lesson_status)}`">
          {{ launchData.tracking.lesson_status }}
        </span>
      </div>
      <button class="scorm-header__close" @click="handleClose">&times; Close</button>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="scorm-loading">
      <p>Loading SCORM package…</p>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="scorm-error">
      <p>{{ error }}</p>
    </div>

    <!-- SCORM iframe -->
    <iframe
      v-else-if="launchData"
      ref="iframeRef"
      :src="launchData.launch_url"
      class="scorm-iframe"
      allow="autoplay; fullscreen"
    />
  </div>
</template>

<style scoped>
.scorm-player {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background: #f5f5f5;
}
.scorm-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 16px;
  background: #1e293b;
  color: white;
  flex-shrink: 0;
}
.scorm-header__left {
  display: flex;
  align-items: center;
  gap: 12px;
}
.scorm-header__title {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}
.scorm-header__chip {
  padding: 2px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}
.chip--success { background: #22c55e; color: white; }
.chip--warning { background: #f59e0b; color: white; }
.chip--error { background: #ef4444; color: white; }
.chip--default { background: #6b7280; color: white; }
.scorm-header__close {
  background: transparent;
  border: 1px solid rgba(255,255,255,0.3);
  color: white;
  padding: 4px 12px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}
.scorm-header__close:hover { background: rgba(255,255,255,0.1); }
.scorm-loading, .scorm-error {
  display: flex;
  justify-content: center;
  align-items: center;
  flex: 1;
  font-size: 16px;
}
.scorm-error { color: #ef4444; }
.scorm-iframe {
  flex: 1;
  border: none;
  width: 100%;
}
</style>
