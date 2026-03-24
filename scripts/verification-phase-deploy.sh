#!/bin/bash

###############################################################################
# Tarea 5: Verification Phases Deployment Script
# 
# Use this script to safely deploy verification phases with monitoring
# Usage: ./scripts/verification-phase-deploy.sh [silent|flagging|reject|tuning]
###############################################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DEPLOYMENT_LOG="storage/logs/verification-phase-deployment.log"
METRICS_FILE="storage/verification-metrics.json"

# Functions
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] [INFO] $1" >> "$DEPLOYMENT_LOG"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] [SUCCESS] $1" >> "$DEPLOYMENT_LOG"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] [WARNING] $1" >> "$DEPLOYMENT_LOG"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] [ERROR] $1" >> "$DEPLOYMENT_LOG"
}

check_environment() {
    log_info "Checking environment..."
    
    if [ ! -f ".env" ]; then
        log_error ".env file not found"
        exit 1
    fi
    
    if ! command -v php &> /dev/null; then
        log_error "PHP not found"
        exit 1
    fi
    
    log_success "Environment check passed"
}

get_current_phase() {
    php artisan tinker --execute "echo config('verification.phase');" 2>/dev/null | tail -1
}

set_phase() {
    local phase=$1
    
    log_info "Setting VERIFICATION_PHASE to: $phase"
    
    if [ -f ".env" ]; then
        # Update .env file
        if grep -q "^VERIFICATION_PHASE=" .env; then
            sed -i.bak "s/^VERIFICATION_PHASE=.*/VERIFICATION_PHASE=$phase/" .env
        else
            echo "VERIFICATION_PHASE=$phase" >> .env
        fi
        
        # Clear config cache
        php artisan config:cache > /dev/null 2>&1
        log_success "Phase set to: $phase"
    fi
}

collect_metrics() {
    local phase=$1
    
    log_info "Collecting verification metrics for phase: $phase"
    
    php artisan tinker --execute "
        \$stats = [
            'phase' => '$phase',
            'timestamp' => now(),
            'total_requests' => \DB::table('agents')->count(),
            'verification_enabled' => config('verification.enabled'),
            'current_phase' => config('verification.phase'),
        ];
        
        echo json_encode(\$stats, JSON_PRETTY_PRINT);
    " > "$METRICS_FILE"
    
    log_success "Metrics collected"
}

validate_phase_readiness() {
    local phase=$1
    
    log_info "Validating readiness for phase: $phase"
    
    case $phase in
        silent)
            log_success "Silent phase is always ready (zero-risk)"
            return 0
            ;;
        flagging)
            # Check that Phase 1 (silent) has been monitoring for minimum time
            log_warning "Ensure Phase 1 (silent) was active for at least 24 hours before proceeding"
            echo -e "${YELLOW}Continue? (yes/no):${NC} "
            read -r confirm
            if [ "$confirm" != "yes" ]; then
                log_error "Phase deployment cancelled"
                exit 1
            fi
            ;;
        reject)
            # Check flagging metrics
            log_warning "Ensure Phase 2 (flagging) validation is complete"
            log_warning "Check that false positive rate is < 10%"
            echo -e "${YELLOW}Continue? (yes/no):${NC} "
            read -r confirm
            if [ "$confirm" != "yes" ]; then
                log_error "Phase deployment cancelled"
                exit 1
            fi
            ;;
        tuning)
            # Check error rate
            log_warning "Ensure Phase 3 (reject) error_rate data is analyzed"
            log_warning "If error_rate < 5%, Phase 4 is optional"
            echo -e "${YELLOW}Continue? (yes/no):${NC} "
            read -r confirm
            if [ "$confirm" != "yes" ]; then
                log_error "Phase deployment cancelled"
                exit 1
            fi
            ;;
    esac
    
    log_success "Readiness validation passed"
}

rollback_phase() {
    local previous_phase=$1
    
    log_warning "Rolling back to phase: $previous_phase"
    set_phase "$previous_phase"
    php artisan config:cache
    log_success "Rollback completed"
}

show_deployment_status() {
    echo ""
    echo "╔════════════════════════════════════════════╗"
    echo "║  Verification Phase Deployment Status     ║"
    echo "╚════════════════════════════════════════════╝"
    echo ""
    
    current_phase=$(get_current_phase)
    echo "Current Phase: ${BLUE}$current_phase${NC}"
    echo "Timestamp: $(date)"
    echo ""
    
    if [ -f "$METRICS_FILE" ]; then
        echo "Latest Metrics:"
        cat "$METRICS_FILE" | head -20
    fi
    echo ""
}

print_help() {
    cat << EOF

Usage: ./scripts/verification-phase-deploy.sh [OPTIONS]

Commands:
  silent       Deploy Phase 1 (Silent) - Log violations without rejecting
  flagging     Deploy Phase 2 (Flagging) - Flag violations but allow output
  reject       Deploy Phase 3 (Reject) - Reject invalid outputs
  tuning       Deploy Phase 4 (Tuning) - Auto-retry with refinement
  status       Show current deployment status
  rollback     Rollback to previous phase
  help         Show this help message

Examples:
  ./scripts/verification-phase-deploy.sh silent
  ./scripts/verification-phase-deploy.sh flagging
  ./scripts/verification-phase-deploy.sh status

Key Points:
  - Each phase builds on the previous one
  - Always validate before moving to next phase
  - Rollback takes 5 minutes
  - Phases are reversible - no permanent changes

EOF
}

###############################################################################
# Main Script
###############################################################################

if [ $# -eq 0 ]; then
    print_help
    exit 0
fi

command=$1

check_environment

case "$command" in
    silent)
        log_info "Deploying Phase 1 (Silent)..."
        current=$(get_current_phase)
        validate_phase_readiness "silent"
        set_phase "silent"
        collect_metrics "silent"
        log_success "Phase 1 (Silent) deployed successfully"
        log_info "ℹ️  System is now in SILENT mode - violations logged but not rejected"
        show_deployment_status
        ;;
    
    flagging)
        log_info "Deploying Phase 2 (Flagging)..."
        current=$(get_current_phase)
        validate_phase_readiness "flagging"
        set_phase "flagging"
        collect_metrics "flagging"
        log_success "Phase 2 (Flagging) deployed successfully"
        log_info "ℹ️  System is now in FLAGGING mode - violations flagged in response"
        show_deployment_status
        ;;
    
    reject)
        log_info "Deploying Phase 3 (Reject)..."
        current=$(get_current_phase)
        validate_phase_readiness "reject"
        set_phase "reject"
        collect_metrics "reject"
        log_success "Phase 3 (Reject) deployed successfully"
        log_info "ℹ️  System is now in REJECT mode - invalid outputs rejected with 422"
        log_warning "Monitor retry_rate - if > 10%, activate Phase 4 (Tuning)"
        show_deployment_status
        ;;
    
    tuning)
        log_info "Deploying Phase 4 (Tuning)..."
        current=$(get_current_phase)
        validate_phase_readiness "tuning"
        set_phase "tuning"
        collect_metrics "tuning"
        log_success "Phase 4 (Tuning) deployed successfully"
        log_info "ℹ️  System is now in TUNING mode - auto-retry with refinement enabled"
        log_warning "⏱️  Expect +2-4 seconds latency per request"
        log_warning "💸 Token consumption will increase by ~10-20%"
        show_deployment_status
        ;;
    
    status)
        show_deployment_status
        ;;
    
    rollback)
        if [ -z "$2" ]; then
            log_error "Please specify phase to rollback to: silent|flagging|reject|tuning"
            echo ""
            log_info "Example: ./scripts/verification-phase-deploy.sh rollback silent"
            exit 1
        fi
        rollback_phase "$2"
        show_deployment_status
        ;;
    
    help)
        print_help
        ;;
    
    *)
        log_error "Unknown command: $command"
        print_help
        exit 1
        ;;
esac

echo ""
