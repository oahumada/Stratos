<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Compliance Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        h1 { color: #1a56db; font-size: 20px; }
        .meta { color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; color: #fff; }
        .badge-completed { background-color: #22c55e; }
        .badge-pending { background-color: #f59e0b; }
        .badge-overdue { background-color: #ef4444; }
        .summary { background: #eff6ff; padding: 12px; border-radius: 6px; margin-bottom: 16px; }
        .summary span { display: inline-block; margin-right: 24px; }
    </style>
</head>
<body>
    <h1>🛡️ LMS Compliance Report</h1>
    <p class="meta">Generated: {{ $generatedAt->format('Y-m-d H:i') }} UTC</p>

    @if(!empty($data['summary']))
    <div class="summary">
        <span><strong>Total Records:</strong> {{ $data['summary']['total_records'] ?? 0 }}</span>
        <span><strong>Completed:</strong> {{ $data['summary']['completed'] ?? 0 }}</span>
        <span><strong>Compliance Rate:</strong> {{ $data['summary']['compliance_rate'] ?? 0 }}%</span>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Course</th>
                <th>Category</th>
                <th>Total</th>
                <th>Completed</th>
                <th>Pending</th>
                <th>Overdue</th>
                <th>Compliance Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['courses'] ?? [] as $row)
            <tr>
                <td>{{ $row['title'] ?? 'N/A' }}</td>
                <td>{{ $row['compliance_category'] ?? 'N/A' }}</td>
                <td>{{ $row['total_records'] }}</td>
                <td><span class="badge badge-completed">{{ $row['completed'] }}</span></td>
                <td><span class="badge badge-pending">{{ $row['pending'] }}</span></td>
                <td><span class="badge badge-overdue">{{ $row['overdue'] }}</span></td>
                <td>{{ $row['compliance_rate'] }}%</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;">No data available</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
