import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\MobileController::registerDevice
* @see app/Http/Controllers/Api/MobileController.php:51
* @route '/api/mobile/register-device'
*/
export const registerDevice = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: registerDevice.url(options),
    method: 'post',
})

registerDevice.definition = {
    methods: ["post"],
    url: '/api/mobile/register-device',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\MobileController::registerDevice
* @see app/Http/Controllers/Api/MobileController.php:51
* @route '/api/mobile/register-device'
*/
registerDevice.url = (options?: RouteQueryOptions) => {
    return registerDevice.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MobileController::registerDevice
* @see app/Http/Controllers/Api/MobileController.php:51
* @route '/api/mobile/register-device'
*/
registerDevice.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: registerDevice.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\MobileController::registerDevice
* @see app/Http/Controllers/Api/MobileController.php:51
* @route '/api/mobile/register-device'
*/
const registerDeviceForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: registerDevice.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\MobileController::registerDevice
* @see app/Http/Controllers/Api/MobileController.php:51
* @route '/api/mobile/register-device'
*/
registerDeviceForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: registerDevice.url(options),
    method: 'post',
})

registerDevice.form = registerDeviceForm

/**
* @see \App\Http\Controllers\Api\MobileController::getDevices
* @see app/Http/Controllers/Api/MobileController.php:108
* @route '/api/mobile/devices'
*/
export const getDevices = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDevices.url(options),
    method: 'get',
})

getDevices.definition = {
    methods: ["get","head"],
    url: '/api/mobile/devices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\MobileController::getDevices
* @see app/Http/Controllers/Api/MobileController.php:108
* @route '/api/mobile/devices'
*/
getDevices.url = (options?: RouteQueryOptions) => {
    return getDevices.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MobileController::getDevices
* @see app/Http/Controllers/Api/MobileController.php:108
* @route '/api/mobile/devices'
*/
getDevices.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDevices.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::getDevices
* @see app/Http/Controllers/Api/MobileController.php:108
* @route '/api/mobile/devices'
*/
getDevices.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getDevices.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\MobileController::getDevices
* @see app/Http/Controllers/Api/MobileController.php:108
* @route '/api/mobile/devices'
*/
const getDevicesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getDevices.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::getDevices
* @see app/Http/Controllers/Api/MobileController.php:108
* @route '/api/mobile/devices'
*/
getDevicesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getDevices.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::getDevices
* @see app/Http/Controllers/Api/MobileController.php:108
* @route '/api/mobile/devices'
*/
getDevicesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getDevices.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getDevices.form = getDevicesForm

/**
* @see \App\Http\Controllers\Api\MobileController::deactivateDevice
* @see app/Http/Controllers/Api/MobileController.php:137
* @route '/api/mobile/devices/{deviceId}'
*/
export const deactivateDevice = (args: { deviceId: string | number } | [deviceId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deactivateDevice.url(args, options),
    method: 'delete',
})

deactivateDevice.definition = {
    methods: ["delete"],
    url: '/api/mobile/devices/{deviceId}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\MobileController::deactivateDevice
* @see app/Http/Controllers/Api/MobileController.php:137
* @route '/api/mobile/devices/{deviceId}'
*/
deactivateDevice.url = (args: { deviceId: string | number } | [deviceId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { deviceId: args }
    }

    if (Array.isArray(args)) {
        args = {
            deviceId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        deviceId: args.deviceId,
    }

    return deactivateDevice.definition.url
            .replace('{deviceId}', parsedArgs.deviceId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MobileController::deactivateDevice
* @see app/Http/Controllers/Api/MobileController.php:137
* @route '/api/mobile/devices/{deviceId}'
*/
deactivateDevice.delete = (args: { deviceId: string | number } | [deviceId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deactivateDevice.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\MobileController::deactivateDevice
* @see app/Http/Controllers/Api/MobileController.php:137
* @route '/api/mobile/devices/{deviceId}'
*/
const deactivateDeviceForm = (args: { deviceId: string | number } | [deviceId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deactivateDevice.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\MobileController::deactivateDevice
* @see app/Http/Controllers/Api/MobileController.php:137
* @route '/api/mobile/devices/{deviceId}'
*/
deactivateDeviceForm.delete = (args: { deviceId: string | number } | [deviceId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deactivateDevice.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

deactivateDevice.form = deactivateDeviceForm

/**
* @see \App\Http\Controllers\Api\MobileController::getApprovals
* @see app/Http/Controllers/Api/MobileController.php:170
* @route '/api/mobile/approvals'
*/
export const getApprovals = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getApprovals.url(options),
    method: 'get',
})

getApprovals.definition = {
    methods: ["get","head"],
    url: '/api/mobile/approvals',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\MobileController::getApprovals
* @see app/Http/Controllers/Api/MobileController.php:170
* @route '/api/mobile/approvals'
*/
getApprovals.url = (options?: RouteQueryOptions) => {
    return getApprovals.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MobileController::getApprovals
* @see app/Http/Controllers/Api/MobileController.php:170
* @route '/api/mobile/approvals'
*/
getApprovals.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getApprovals.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::getApprovals
* @see app/Http/Controllers/Api/MobileController.php:170
* @route '/api/mobile/approvals'
*/
getApprovals.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getApprovals.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\MobileController::getApprovals
* @see app/Http/Controllers/Api/MobileController.php:170
* @route '/api/mobile/approvals'
*/
const getApprovalsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getApprovals.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::getApprovals
* @see app/Http/Controllers/Api/MobileController.php:170
* @route '/api/mobile/approvals'
*/
getApprovalsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getApprovals.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::getApprovals
* @see app/Http/Controllers/Api/MobileController.php:170
* @route '/api/mobile/approvals'
*/
getApprovalsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getApprovals.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getApprovals.form = getApprovalsForm

/**
* @see \App\Http\Controllers\Api\MobileController::approveRequest
* @see app/Http/Controllers/Api/MobileController.php:203
* @route '/api/mobile/approvals/{approvalId}/approve'
*/
export const approveRequest = (args: { approvalId: string | number } | [approvalId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approveRequest.url(args, options),
    method: 'post',
})

approveRequest.definition = {
    methods: ["post"],
    url: '/api/mobile/approvals/{approvalId}/approve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\MobileController::approveRequest
* @see app/Http/Controllers/Api/MobileController.php:203
* @route '/api/mobile/approvals/{approvalId}/approve'
*/
approveRequest.url = (args: { approvalId: string | number } | [approvalId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { approvalId: args }
    }

    if (Array.isArray(args)) {
        args = {
            approvalId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        approvalId: args.approvalId,
    }

    return approveRequest.definition.url
            .replace('{approvalId}', parsedArgs.approvalId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MobileController::approveRequest
* @see app/Http/Controllers/Api/MobileController.php:203
* @route '/api/mobile/approvals/{approvalId}/approve'
*/
approveRequest.post = (args: { approvalId: string | number } | [approvalId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approveRequest.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\MobileController::approveRequest
* @see app/Http/Controllers/Api/MobileController.php:203
* @route '/api/mobile/approvals/{approvalId}/approve'
*/
const approveRequestForm = (args: { approvalId: string | number } | [approvalId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approveRequest.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\MobileController::approveRequest
* @see app/Http/Controllers/Api/MobileController.php:203
* @route '/api/mobile/approvals/{approvalId}/approve'
*/
approveRequestForm.post = (args: { approvalId: string | number } | [approvalId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approveRequest.url(args, options),
    method: 'post',
})

approveRequest.form = approveRequestForm

/**
* @see \App\Http\Controllers\Api\MobileController::rejectRequest
* @see app/Http/Controllers/Api/MobileController.php:272
* @route '/api/mobile/approvals/{approvalId}/reject'
*/
export const rejectRequest = (args: { approvalId: string | number } | [approvalId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: rejectRequest.url(args, options),
    method: 'post',
})

rejectRequest.definition = {
    methods: ["post"],
    url: '/api/mobile/approvals/{approvalId}/reject',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\MobileController::rejectRequest
* @see app/Http/Controllers/Api/MobileController.php:272
* @route '/api/mobile/approvals/{approvalId}/reject'
*/
rejectRequest.url = (args: { approvalId: string | number } | [approvalId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { approvalId: args }
    }

    if (Array.isArray(args)) {
        args = {
            approvalId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        approvalId: args.approvalId,
    }

    return rejectRequest.definition.url
            .replace('{approvalId}', parsedArgs.approvalId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MobileController::rejectRequest
* @see app/Http/Controllers/Api/MobileController.php:272
* @route '/api/mobile/approvals/{approvalId}/reject'
*/
rejectRequest.post = (args: { approvalId: string | number } | [approvalId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: rejectRequest.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\MobileController::rejectRequest
* @see app/Http/Controllers/Api/MobileController.php:272
* @route '/api/mobile/approvals/{approvalId}/reject'
*/
const rejectRequestForm = (args: { approvalId: string | number } | [approvalId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: rejectRequest.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\MobileController::rejectRequest
* @see app/Http/Controllers/Api/MobileController.php:272
* @route '/api/mobile/approvals/{approvalId}/reject'
*/
rejectRequestForm.post = (args: { approvalId: string | number } | [approvalId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: rejectRequest.url(args, options),
    method: 'post',
})

rejectRequest.form = rejectRequestForm

/**
* @see \App\Http\Controllers\Api\MobileController::approvalHistory
* @see app/Http/Controllers/Api/MobileController.php:337
* @route '/api/mobile/approvals/history'
*/
export const approvalHistory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: approvalHistory.url(options),
    method: 'get',
})

approvalHistory.definition = {
    methods: ["get","head"],
    url: '/api/mobile/approvals/history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\MobileController::approvalHistory
* @see app/Http/Controllers/Api/MobileController.php:337
* @route '/api/mobile/approvals/history'
*/
approvalHistory.url = (options?: RouteQueryOptions) => {
    return approvalHistory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MobileController::approvalHistory
* @see app/Http/Controllers/Api/MobileController.php:337
* @route '/api/mobile/approvals/history'
*/
approvalHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: approvalHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::approvalHistory
* @see app/Http/Controllers/Api/MobileController.php:337
* @route '/api/mobile/approvals/history'
*/
approvalHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: approvalHistory.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\MobileController::approvalHistory
* @see app/Http/Controllers/Api/MobileController.php:337
* @route '/api/mobile/approvals/history'
*/
const approvalHistoryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: approvalHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::approvalHistory
* @see app/Http/Controllers/Api/MobileController.php:337
* @route '/api/mobile/approvals/history'
*/
approvalHistoryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: approvalHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::approvalHistory
* @see app/Http/Controllers/Api/MobileController.php:337
* @route '/api/mobile/approvals/history'
*/
approvalHistoryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: approvalHistory.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

approvalHistory.form = approvalHistoryForm

/**
* @see \App\Http\Controllers\Api\MobileController::syncQueue
* @see app/Http/Controllers/Api/MobileController.php:375
* @route '/api/mobile/offline-queue/sync'
*/
export const syncQueue = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: syncQueue.url(options),
    method: 'post',
})

syncQueue.definition = {
    methods: ["post"],
    url: '/api/mobile/offline-queue/sync',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\MobileController::syncQueue
* @see app/Http/Controllers/Api/MobileController.php:375
* @route '/api/mobile/offline-queue/sync'
*/
syncQueue.url = (options?: RouteQueryOptions) => {
    return syncQueue.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MobileController::syncQueue
* @see app/Http/Controllers/Api/MobileController.php:375
* @route '/api/mobile/offline-queue/sync'
*/
syncQueue.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: syncQueue.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\MobileController::syncQueue
* @see app/Http/Controllers/Api/MobileController.php:375
* @route '/api/mobile/offline-queue/sync'
*/
const syncQueueForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: syncQueue.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\MobileController::syncQueue
* @see app/Http/Controllers/Api/MobileController.php:375
* @route '/api/mobile/offline-queue/sync'
*/
syncQueueForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: syncQueue.url(options),
    method: 'post',
})

syncQueue.form = syncQueueForm

/**
* @see \App\Http\Controllers\Api\MobileController::queueStatus
* @see app/Http/Controllers/Api/MobileController.php:411
* @route '/api/mobile/offline-queue/status'
*/
export const queueStatus = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: queueStatus.url(options),
    method: 'get',
})

queueStatus.definition = {
    methods: ["get","head"],
    url: '/api/mobile/offline-queue/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\MobileController::queueStatus
* @see app/Http/Controllers/Api/MobileController.php:411
* @route '/api/mobile/offline-queue/status'
*/
queueStatus.url = (options?: RouteQueryOptions) => {
    return queueStatus.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MobileController::queueStatus
* @see app/Http/Controllers/Api/MobileController.php:411
* @route '/api/mobile/offline-queue/status'
*/
queueStatus.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: queueStatus.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::queueStatus
* @see app/Http/Controllers/Api/MobileController.php:411
* @route '/api/mobile/offline-queue/status'
*/
queueStatus.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: queueStatus.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\MobileController::queueStatus
* @see app/Http/Controllers/Api/MobileController.php:411
* @route '/api/mobile/offline-queue/status'
*/
const queueStatusForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: queueStatus.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::queueStatus
* @see app/Http/Controllers/Api/MobileController.php:411
* @route '/api/mobile/offline-queue/status'
*/
queueStatusForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: queueStatus.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::queueStatus
* @see app/Http/Controllers/Api/MobileController.php:411
* @route '/api/mobile/offline-queue/status'
*/
queueStatusForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: queueStatus.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

queueStatus.form = queueStatusForm

/**
* @see \App\Http\Controllers\Api\MobileController::deviceStats
* @see app/Http/Controllers/Api/MobileController.php:440
* @route '/api/mobile/stats/devices'
*/
export const deviceStats = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: deviceStats.url(options),
    method: 'get',
})

deviceStats.definition = {
    methods: ["get","head"],
    url: '/api/mobile/stats/devices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\MobileController::deviceStats
* @see app/Http/Controllers/Api/MobileController.php:440
* @route '/api/mobile/stats/devices'
*/
deviceStats.url = (options?: RouteQueryOptions) => {
    return deviceStats.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MobileController::deviceStats
* @see app/Http/Controllers/Api/MobileController.php:440
* @route '/api/mobile/stats/devices'
*/
deviceStats.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: deviceStats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::deviceStats
* @see app/Http/Controllers/Api/MobileController.php:440
* @route '/api/mobile/stats/devices'
*/
deviceStats.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: deviceStats.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\MobileController::deviceStats
* @see app/Http/Controllers/Api/MobileController.php:440
* @route '/api/mobile/stats/devices'
*/
const deviceStatsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: deviceStats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::deviceStats
* @see app/Http/Controllers/Api/MobileController.php:440
* @route '/api/mobile/stats/devices'
*/
deviceStatsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: deviceStats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::deviceStats
* @see app/Http/Controllers/Api/MobileController.php:440
* @route '/api/mobile/stats/devices'
*/
deviceStatsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: deviceStats.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

deviceStats.form = deviceStatsForm

const mobile = {
    registerDevice: Object.assign(registerDevice, registerDevice),
    getDevices: Object.assign(getDevices, getDevices),
    deactivateDevice: Object.assign(deactivateDevice, deactivateDevice),
    getApprovals: Object.assign(getApprovals, getApprovals),
    approveRequest: Object.assign(approveRequest, approveRequest),
    rejectRequest: Object.assign(rejectRequest, rejectRequest),
    approvalHistory: Object.assign(approvalHistory, approvalHistory),
    syncQueue: Object.assign(syncQueue, syncQueue),
    queueStatus: Object.assign(queueStatus, queueStatus),
    deviceStats: Object.assign(deviceStats, deviceStats),
}

export default mobile