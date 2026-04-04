<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Completion Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        h1 { color: #1a56db; font-size: 20px; }
        .meta { color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .summary { background: #eff6ff; padding: 12px; border-radius: 6px; margin-bottom: 16px; }
        .summary span { display: inline-block; margin-right: 24px; }
    </style>
</head>
<body>
    <h1>📊 LMS Completion Report</h1>
    <p class="meta">Generated: {{ $generatedAt->format('Y-m-d H:i') }} UTC</p>

    @if(!empty($data['totals']))
    <div class="summary">
        <span><strong>Total Enrollments:</strong> {{ $data['totals']['total_enrollments'] }}</span>
        <span><strong>Completed:</strong> {{ $data['totals']['completed'] }}</span>
        <span><strong>Avg Progress:</strong> {{ $data['totals']['avg_progress'] }}%</span>
        <span><strong>Completion Rate:</strong> {{ $data['totals']['overall_completion_rate'] }}%</span>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Total Enrollments</th>
                <th>Completed</th>
                <th>In Progress</th>
                <th>Avg Progress</th>
                <th>Completion Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['by_category'] ?? [] as $row)
            <tr>
                <td>{{ $row['category'] ?? 'N/A' }}</td>
                <td>{{ $row['total_enrollments'] }}</td>
                <td>{{ $row['completed'] }}</td>
                <td>{{ $row['in_progress'] ?? 0 }}</td>
                <td>{{ $row['avg_progress'] }}%</td>
                <td>{{ $row['completion_rate'] }}%</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;">No data available</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
