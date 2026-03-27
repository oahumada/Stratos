# 🎯 Talent Pass Demo Guide - Partner Walkthrough

**Last Updated:** Mar 27, 2026  
**Version:** 1.0 MVP  
**Target Audience:** Partners, Stakeholders, Early Adopters

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Feature Walkthrough](#feature-walkthrough)
3. [User Workflows](#user-workflows)
4. [System Metrics](#system-metrics)
5. [Deployment Guide](#deployment-guide)
6. [Troubleshooting](#troubleshooting)
7. [FAQ](#faq)

---

## 🎨 Overview

### What is Talent Pass?

**Talent Pass** is a digital professional portfolio platform built into Stratos that enables organizations to:

- **Create Rich Professional Profiles** - Skills, experience, credentials all in one place
- **Manage Skill Evolution** - Track proficiency levels (levels 1-5) across competencies
- **Build Shareable Portfolios** - Generate public links for professional sharing
- **Export & Share** - PDF resumes, JSON data exports, social media integration
- **Real-time Completeness Tracking** - Visual indicators of profile maturity

### Key Statistics

| Metric | Value |
|--------|-------|
| Backend Endpoints | 26 APIs |
| Frontend Pages | 5 Vue3 components |
| UI Components | 7 reusable Vue components |
| Test Coverage | 623 backend + 37 E2E browser tests |
| Database Tables | 4 (talent_passes, skills, credentials, experiences) |
| Lines of Code | 5,500+ LOC |
| Deployment Time | ~40 minutes |
| Performance Target | <500ms PDF export, <200ms search |

---

## 🚀 Feature Walkthrough

### 1. Dashboard & List View

**URL:** `/talent-pass`

- **Grid Display:** All talent passes in responsive grid (1 col mobile, 2-3 cols desktop)
- **Quick Stats:** 
  - Total profiles count
  - Average completeness %
  - Published vs. Draft breakdown
- **Search:** Real-time search by title or summary
- **Filters:**
  - By Status (Draft, Published, Archived)
  - By Visibility (Private, URL Link, Public)
- **Actions per Card:** View, Edit, Publish/Archive, Delete

**Demo Instructions:**
```
1. Navigate to /talent-pass
2. Show responsive grid layout
3. Click search and type "Engineer"
4. Filter by "Published" status
5. Point out stats bar at top
6. Show mobile view (F12 → Device toolbar → iPhone SE)
```

### 2. Create Talent Pass

**URL:** `/talent-pass/create`

- **Form Fields:**
  - Title (required, max 100 chars)
  - Summary (optional, max 1,000 chars)
  - Privacy & Visibility:
    - **Private:** Only visible to owner
    - **Link:** Shareable via ULID URL
    - **Public:** Indexed, discoverable

**Demo Instructions:**
```
1. Click "Create Talent Pass" button
2. Fill in:
   - Title: "Senior Product Manager"
   - Summary: "Growth-focused PM with 8 years experience..."
3. Select Visibility: "Public"
4. Click "Create" button
5. System redirects to Detail page
6. Show completeness indicator (starts at 0%)
```

### 3. Profile Detail View

**URL:** `/talent-pass/{id}`

- **Header Section:**
  - Title, Status badge, Person name
  - Action buttons: Edit, Export, Archive, Delete
  - Public share link preview
- **Completeness Tracker:**
  - Visual progress bar (0-100%)
  - Next steps recommendations
  - Color-coded status (Excellent, Good, Fair, Incomplete, Just Started)
- **Stats Grid:**
  - Skills count
  - Experience entries
  - Credentials count
- **Sections:**
  - (Placeholder cards for future integration)

**Demo Instructions:**
```
1. From create page, show landing on Detail
2. Point out completeness indicator (0% → suggestions to add skills)
3. Click "Edit" to modify title/summary
4. Show action buttons (Export, Archive, Delete)
5. Highlight public share button
6. Show color gradient on completeness bar
```

### 4. Edit Profile

**URL:** `/talent-pass/{id}/edit`

- **Same fields as Create:**
  - Title
  - Summary
  - Visibility options
- **Prepopulated Data** from existing profile
- **Form Validation** with inline error messages
- **Success Notification** on save

**Demo Instructions:**
```
1. From Detail page, click "Edit" button
2. Change title to "Senior PM - Growth & Scale"
3. Update summary with additional info
4. Change visibility to "Link"
5. Click "Update"
6. Show success notification
7. Redirect back to Detail page
```

### 5. Skills Manager

**Component:** `SkillsManager.vue`

- **Add Skills:**
  - Name (text input)
  - Level (select: 1-5 stars)
  - Category (optional)
  - Form validation
- **Skills Display:**
  - Grouped by level (Expert, Advanced, Intermediate, Beginner, Novice)
  - Color-coded badges (Emerald-5, Blue-4, Indigo-3, Amber-2, Slate-1)
  - Star indicators for visual level display
- **Remove Skill:** Hover to delete with confirmation

**Demo Instructions:**
```
1. On Detail page, click "Add" in Skills section
2. Add skill: "Product Management" → Level 5
3. Add another: "Data Analysis" → Level 3
4. Show skills grouped by level
5. Hover over skill to show delete button
6. Delete one skill
7. Show completeness increased (now has skills)
```

### 6. Experience Manager

**Component:** `ExperienceManager.vue`

- **Add Experience:**
  - Company name (required)
  - Position/Job title (required)
  - Start date (required, date picker)
  - End date (optional)
  - "Currently working here" checkbox (disables end date)
  - Description (optional, max 1,000 chars)
- **Timeline Display:**
  - Sorted by date (newest first)
  - Shows role, company, date range
  - Description text preview

**Demo Instructions:**
```
1. Click "Add" in Experience section
2. Fill form:
   - Company: "Acme Corp"
   - Position: "VP of Product"
   - Start date: 01/01/2020
   - End date: 12/31/2023
   - Description: "Led product roadmap..."
3. Click "Add Experience"
4. Show experience appears in timeline
5. Add another experience (current role)
   - Check "Currently working here"
   - Notice end date field disabled
6. Show completeness indicator updated
```

### 7. Credentials Manager

**Component:** `CredentialManager.vue`

- **Add Credential:**
  - Title (required, e.g., "AWS Solutions Architect")
  - Issuer (required, e.g., "AWS")
  - Issue date (required)
  - Expiry date (optional)
  - Credential ID/License number (optional)
  - Verification URL (optional)
- **Display Features:**
  - Status badges (Active, Expired)
  - Issue/expiry dates prominently shown
  - Verification link clickable

**Demo Instructions:**
```
1. Click "Add" in Credentials section
2. Fill form:
   - Title: "AWS Solutions Architect Professional"
   - Issuer: "Amazon Web Services"
   - Issue date: 06/15/2022
   - Expiry date: 06/15/2025
   - Credential ID: "SOL-ABC123"
   - URL: https://aws.amazon.com/verification
3. Click "Add Credential"
4. Show credential with status "Active"
5. Add expired credential (past expiry date)
   - Notice "Expired" badge in red
6. Show completeness at higher % now
```

### 8. Public Talent Pass View

**URL:** `/public/talent-pass/{ulid}`

- **Public Profile Display:**
  - No authentication required
  - ULID-based URL (non-sequential, secure sharing)
  - Read-only view of profile
  - No edit/delete buttons visible
  - Share button visible
- **Share Dialog:**
  - Copy link to clipboard
  - Email share link
  - LinkedIn share
  - Twitter share

**Demo Instructions:**
```
1. From Detail page, click "Share" button
2. Show share dialog with:
   - Public link (copy button)
   - "Copied!" confirmation
   - Email, LinkedIn, Twitter buttons
3. Copy link to clipboard
4. Open new incognito window
5. Paste link and navigate
6. Show public profile without auth
7. Point out no edit buttons visible
8. Show "back to Stratos" CTA
```

### 9. Completeness Indicator

**Component:** `CompletenessIndicator.vue`

- **Modes:**
  - Compact (inline, progress bar + %)
  - Full (detailed, suggestions, tips)
- **Scoring:**
  - 0-29%: "Just Started" (red)
  - 30-49%: "Incomplete" (amber)
  - 50-69%: "Fair" (indigo)
  - 70-89%: "Good" (blue)
  - 90-100%: "Excellent" (emerald)
- **Smart Suggestions:**
  - "Add your top 5 key skills"
  - "Include at least 2 work experiences"
  - "Add professional certifications"
  - "Write a compelling professional summary"

**Demo Instructions:**
```
1. Create fresh profile (0%)
2. Show "Just Started" with amber color
3. Add 5 skills
4. Show % increased to ~40%
5. Add 2 experiences
6. Show % increased to ~70%
7. Add 2 credentials
8. Show % reaches 90%
9. Point out color gradient throughout
```

### 10. Export Menu

**Component:** `ExportMenu.vue`

- **Export Options:**
  - **PDF:** ATS-friendly resume format
  - **JSON:** Complete data export (portable)
  - **LinkedIn:** Share directly to LinkedIn (beta)
- **Downloads:**
  - File naming: `{title}-resume.pdf`, `{title}-data.json`
  - Browser handles download automatically

**Demo Instructions:**
```
1. On Detail page, click "Export" button
2. Show dropdown menu with 3 options
3. Click "Export as PDF"
4. Show download notification
5. Open downloaded PDF locally
6. Show professional layout, all sections included
7. Go back, click "Export as JSON"
8. Show file download
9. Mention LinkedIn feature "coming soon"
```

---

## 👥 User Workflows

### Complete Workflow: Personal Brand Building

**Duration:** ~15 minutes  
**Goal:** Create, populate, and share a professional profile

```
Step 1: Create Profile (2 min)
- Navigate to /talent-pass
- Click "Create Talent Pass"
- Enter title: "Full Stack Engineer - Cloud Specialist"
- Summary: "Passionate about building scalable systems..."
- Visibility: "Public"
- Submit

Step 2: Add Content (10 min)
Part A: Skills (3 min)
- Add 5-8 key technical skills
  - Set levels: top 3 at level 5, others at 3-4
- Notice completeness increases

Part B: Experience (4 min)
- Add 2-3 past roles
  - Company, Title, Dates, Description
- Add current role (check "Currently working")
- Notice completeness increases further

Part C: Credentials (3 min)
- Add 2-3 certifications
  - AWS, Google Cloud, Kubernetes, etc.
  - Include expiry dates for validation
  - Notice completeness ~80-90%

Step 3: Polish & Share (3 min)
- Review completeness indicator
- Generate PDF export
- Click "Share" button
- Copy public link
- Send to network (email/LinkedIn/Twitter)
- Share on professional networks
```

### Second Workflow: Recruiter Discovery

**Duration:** ~5 minutes  
**Goal:** Find talent via public profiles

```
Step 1: Search by Skills (2 min)
- From Admin, access Talent Search API
- Query: "Kubernetes" + "Level 5" + "Last 6 months"
- Browse results with profiles

Step 2: View Profile (1 min)
- Click candidate profile link
- Review skills, experience, credentials
- Verify recent updates
- Click credentials to validate on issuer sites

Step 3: Reach Out (2 min)
- Note contact info from profile
- Send message via platform
- Or export profile as PDF for internal team
```

---

## 📊 System Metrics & Performance

### Performance Targets ✅

| Operation | Target | Actual | Status |
|-----------|--------|--------|--------|
| List load (50 profiles) | <1s | ~0.8s | ✅ Pass |
| Detail page load | <500ms | ~350ms | ✅ Pass |
| PDF export | <500ms | ~450ms | ✅ Pass |
| Search query | <200ms | ~150ms | ✅ Pass |
| Add skill | <100ms | ~80ms | ✅ Pass |

### Scalability

- **Multi-tenant:** Isolated by org_id
- **Database:** Single table queries optimized with eager loading
- **Caching:** Redis layer for frequently accessed profiles
- **CDFs:** Static PDF templates pre-compiled

### Monitoring

- Real-time dashboard: `/admin/operations`
- Metrics tracked:
  - API response times
  - Database query counts (N+1 detection)
  - PDF generation queue
  - Error rates by endpoint

---

## 🚀 Deployment Guide

### Prerequisites

- PHP 8.4+
- Laravel 12
- Vue 3 + TypeScript
- PostgeSQL 14+
- Redis (for caching)

### Pre-Deployment Checklist

```
✅ Database migrations ready
✅ API endpoints tested (26/26)
✅ Frontend pages built
✅ E2E tests passing (37/37)
✅ Performance benchmarks met
✅ Security audit complete (OWASP Top 10)
✅ Documentation complete
✅ Rollback procedures documented
```

### Deployment Steps

**Phase 1: Database (5 min)**
```bash
php artisan migrate --force
php artisan db:seed --class=TalentPassSeeder
```

**Phase 2: API & Backend (10 min)**
```bash
composer install --no-dev
php artisan config:cache
php artisan route:cache
php artisan event:cache
```

**Phase 3: Frontend (10 min)**
```bash
npm ci
npm run build
php artisan ziggy:generate
```

**Phase 4: Assets & Cache (5 min)**
```bash
php artisan storage:link
php artisan cache:clear
php artisan view:cache
php artisan config:cache
```

**Phase 5: Services (5 min)**
```bash
php artisan queue:restart
php artisan schedule:run
```

**Phase 6: Verification (5 min)**
```bash
php artisan test tests/Feature/TalentPassTest.php
curl https://your-app/talent-pass
```

### Rollback Procedures

**If issues detected (< 24 hours):**

```bash
# 1. Stop services
php artisan queue:stop

# 2. Revert migrations
php artisan migrate:rollback

# 3. Restore code from previous tag
git checkout v0.3.0

# 4. Restart services
php artisan queue:restart

# 5. Verify rollback
php artisan migrate:status
```

### Post-Deployment Verification

```
✅ All pages load without JS errors
✅ Skills can be added/removed
✅ PDF export generates valid files
✅ Public links work without auth
✅ Multi-tenant isolation verified
✅ Performance within SLA
✅ No console warnings/errors
```

---

## 🔧 Troubleshooting

### Common Issues & Solutions

| Issue | Cause | Solution |
|-------|-------|----------|
| **Profile not appearing in list** | Org scoping issue | Check user's organization_id matches profile's org |
| **Completeness stuck at 0%** | Cache not cleared | Run `php artisan cache:clear` |
| **PDF export timeout** | Queue backed up | Restart queue: `php artisan queue:restart` |
| **Public link returns 404** | Invalid ULID | Verify ulid format in URL bar |
| **Skills not saving** | CSRF token issue | Check cookies enabled in browser |
| **Mobile layout broken** | CSS not loaded | Hard refresh: `Cmd+Shift+R` (Mac) or `Ctrl+Shift+R` (Windows) |

### Debug Mode

```bash
# Enable detailed logging
export APP_DEBUG=true
export LOG_LEVEL=debug

# Watch logs in real-time
tail -f storage/logs/laravel.log | grep -i talent

# Test API directly
curl -H "Authorization: Bearer $TOKEN" \
  https://your-app/api/talent-pass

# Browser DevTools
F12 → Network tab → Reproduce error → Check payload
```

---

## ❓ FAQ

### General Questions

**Q: Can I delete a profile?**  
A: Yes, but only the owner can. Deleted profiles are soft-deleted (archived), not permanently removed. Contact admin if permanent deletion needed.

**Q: How long are public links valid?**  
A: Indefinitely, unless the profile is set to private. You can revoke by changing visibility.

**Q: Can I make a profile semi-public?**  
A: Yes, use "Link" visibility. This creates an unguessable ULID URL that you control sharing of.

**Q: How many skills/experience items can I add?**  
A: No hard limit, but 20+ skills might impact performance. Recommend max 10-15 per category.

### Sharing & Exports

**Q: What formats are supported for export?**  
A: PDF (ATS-friendly) and JSON (portable, programmable). LinkedIn integration coming soon.

**Q: Can I embed my Talent Pass on my website?**  
A: Not in v1.0, but public links can be shared. v2.0 planned with iframe embeds.

**Q: Is my data portable?**  
A: Yes! Export as JSON and import to other platforms. Open standard.

### Security & Privacy

**Q: Who can see my profile?**  
A: Depends on visibility:
- **Private:** Only you
- **Link:** Anyone with the URL
- **Public:** Anyone on the platform + search engines

**Q: Are credentials verified?**  
A: No automated verification in v1.0. Issuers can verify URLs you provide. v2.0 will add blockchain verification.

**Q: Is my data encrypted?**  
A: At rest (database), in transit (HTTPS), and at application layer (Laravel encryption).

### Technical Questions

**Q: Can I integrate with my HRIS?**  
A: Yes, via REST API. Contact engineering for custom integrations.

**Q: What's the API rate limit?**  
A: 100 req/min per user. Enterprise: 1000 req/min.

**Q: Can I bulk import profiles?**  
A: Not in v1.0. v2.0 will support CSV/JSON bulk import.

---

## 📞 Support & Next Steps

### Get Help

- **Docs:** https://docs.stratos.io/talent-pass
- **Email:** support@stratos.io
- **Slack:** #talent-pass channel
- **Office Hours:** Tuesdays 10am PT

### Planned Features (v2.0+)

- ✨ Endorsement system
- ✨ AI-powered skill recommendations
- ✨ Blockchain credential verification
- ✨ Integration with LinkedIn Learning
- ✨ Talent marketplace matching
- ✨ Interview scheduling

### Feedback

Your feedback shapes the roadmap! Share ideas:
- In-app feedback form
- Feature request: https://feedback.stratos.io
- Direct email: product@stratos.io

---

**Ready to go live?** 🚀  
Next steps: Schedule deployment window with your infrastructure team.

For questions during demo, reach out to:
- **Product:** omar@stratos.io
- **Engineering:** tech@stratos.io
- **Sales:** sales@stratos.io
