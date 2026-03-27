import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Api\MobileController::getPendingApprovals
* @see app/Http/Controllers/Api/MobileController.php:170
* @route '/api/mobile/approvals'
*/
export const getPendingApprovals = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getPendingApprovals.url(options),
    method: 'get',
})

getPendingApprovals.definition = {
    methods: ["get","head"],
    url: '/api/mobile/approvals',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\MobileController::getPendingApprovals
* @see app/Http/Controllers/Api/MobileController.php:170
* @route '/api/mobile/approvals'
*/
getPendingApprovals.url = (options?: RouteQueryOptions) => {
    return getPendingApprovals.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MobileController::getPendingApprovals
* @see app/Http/Controllers/Api/MobileController.php:170
* @route '/api/mobile/approvals'
*/
getPendingApprovals.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getPendingApprovals.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::getPendingApprovals
* @see app/Http/Controllers/Api/MobileController.php:170
* @route '/api/mobile/approvals'
*/
getPendingApprovals.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getPendingApprovals.url(options),
    method: 'head',
})

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
* @see \App\Http\Controllers\Api\MobileController::getApprovalHistory
* @see app/Http/Controllers/Api/MobileController.php:337
* @route '/api/mobile/approvals/history'
*/
export const getApprovalHistory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getApprovalHistory.url(options),
    method: 'get',
})

getApprovalHistory.definition = {
    methods: ["get","head"],
    url: '/api/mobile/approvals/history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\MobileController::getApprovalHistory
* @see app/Http/Controllers/Api/MobileController.php:337
* @route '/api/mobile/approvals/history'
*/
getApprovalHistory.url = (options?: RouteQueryOptions) => {
    return getApprovalHistory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MobileController::getApprovalHistory
* @see app/Http/Controllers/Api/MobileController.php:337
* @route '/api/mobile/approvals/history'
*/
getApprovalHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getApprovalHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::getApprovalHistory
* @see app/Http/Controllers/Api/MobileController.php:337
* @route '/api/mobile/approvals/history'
*/
getApprovalHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getApprovalHistory.url(options),
    method: 'head',
})

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
* @see \App\Http\Controllers\Api\MobileController::getQueueStatus
* @see app/Http/Controllers/Api/MobileController.php:411
* @route '/api/mobile/offline-queue/status'
*/
export const getQueueStatus = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getQueueStatus.url(options),
    method: 'get',
})

getQueueStatus.definition = {
    methods: ["get","head"],
    url: '/api/mobile/offline-queue/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\MobileController::getQueueStatus
* @see app/Http/Controllers/Api/MobileController.php:411
* @route '/api/mobile/offline-queue/status'
*/
getQueueStatus.url = (options?: RouteQueryOptions) => {
    return getQueueStatus.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MobileController::getQueueStatus
* @see app/Http/Controllers/Api/MobileController.php:411
* @route '/api/mobile/offline-queue/status'
*/
getQueueStatus.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getQueueStatus.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::getQueueStatus
* @see app/Http/Controllers/Api/MobileController.php:411
* @route '/api/mobile/offline-queue/status'
*/
getQueueStatus.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getQueueStatus.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\MobileController::getDeviceStats
* @see app/Http/Controllers/Api/MobileController.php:440
* @route '/api/mobile/stats/devices'
*/
export const getDeviceStats = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDeviceStats.url(options),
    method: 'get',
})

getDeviceStats.definition = {
    methods: ["get","head"],
    url: '/api/mobile/stats/devices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\MobileController::getDeviceStats
* @see app/Http/Controllers/Api/MobileController.php:440
* @route '/api/mobile/stats/devices'
*/
getDeviceStats.url = (options?: RouteQueryOptions) => {
    return getDeviceStats.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MobileController::getDeviceStats
* @see app/Http/Controllers/Api/MobileController.php:440
* @route '/api/mobile/stats/devices'
*/
getDeviceStats.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDeviceStats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MobileController::getDeviceStats
* @see app/Http/Controllers/Api/MobileController.php:440
* @route '/api/mobile/stats/devices'
*/
getDeviceStats.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getDeviceStats.url(options),
    method: 'head',
})

const MobileController = { registerDevice, getDevices, deactivateDevice, getPendingApprovals, approveRequest, rejectRequest, getApprovalHistory, syncQueue, getQueueStatus, getDeviceStats }

export default MobileController