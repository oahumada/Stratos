<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Engagement Report</title>
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
    <h1>📈 LMS Engagement Report</h1>
    <p class="meta">Generated: {{ $generatedAt->format('Y-m-d H:i') }} UTC</p>

    <div class="summary">
        <span><strong>Report Type:</strong> Engagement Trends</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Enrollments</th>
                <th>Completions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['months'] ?? [] as $row)
            <tr>
                <td>{{ $row['month'] }}</td>
                <td>{{ $row['enrollments'] }}</td>
                <td>{{ $row['completions'] }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center;">No data available</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
