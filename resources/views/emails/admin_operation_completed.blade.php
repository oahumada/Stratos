@props(['operation'])
<p>Operación: {{ $operation['operation_name'] }} ({{ $operation['operation_type'] }})</p>
<p>Estado: {{ $operation['status'] }}</p>
<p>Registros afectados: {{ $operation['records_affected'] ?? 0 }}</p>
<p><a href="{{ config('app.url') }}/admin/operations">Ver en Admin Operations</a></p>
