#!/bin/bash

# 🌱 populate-compliance-demo.sh
# Populates the database with realistic Compliance data for demo purposes

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
SRC_DIR="$PROJECT_ROOT/src"

echo "🌱 Compliance Demo Seeder"
echo "=========================="
echo ""
echo "📍 Project Root: $PROJECT_ROOT"
echo "📍 Source Dir: $SRC_DIR"
echo ""

# Check if src/ exists
if [ ! -d "$SRC_DIR" ]; then
    echo "❌ ERROR: src/ directory not found at $SRC_DIR"
    exit 1
fi

# Change to src directory
cd "$SRC_DIR" || exit 1

echo "⏳ Running ComplianceDemoSeeder..."
echo ""

# Run seeder
php artisan db:seed --class=ComplianceDemoSeeder

echo ""
echo "✅ Seeder completed!"
echo ""
echo "📊 Next steps:"
echo "   1. Open browser: http://localhost:8000/quality/compliance-audit"
echo "   2. You should see:"
echo "      • ~200+ audit trail events"
echo "      • 24 critical roles (12 signed, 6 expired, 6 missing)"
echo "      • 89 people assigned"
echo "      • ~12 skills with gaps"
echo "      • $48M+ total replacement cost"
echo ""
echo "🎯 Demo Checklist:"
echo "   ☐ Filter by event_name='role.updated'"
echo "   ☐ Check ISO 30414 KPIs and department maturity"
echo "   ☐ Check Internal Audit Wizard: 87.5% compliance"
echo "   ☐ Export VC for Role ID 1"
echo "   ☐ Verify VC signature"
echo ""
echo "📚 For full guide, see: docs/COMPLIANCE_DEMO_SEEDER_GUIDE.md"
