# 🌐 Talent Pass i18n Keys Reference

**Status:** Ready to integrate into `resources/js/i18n.ts`  
**Languages:** English (en), Spanish (es)  
**Module Namespace:** `talent_pass`

---

## Usage in Components

```typescript
// In Vue components (with useI18n)
const { t } = useI18n()

// Access keys
t('talent_pass.title.create')
t('talent_pass.sections.skills.label')
t('talent_pass.status.draft')
```

---

## Complete Key Map

### Navigation & Titles

```typescript
talent_pass: {
  // Page titles
  title: {
    list: 'My Talent Passes',
    create: 'Create New Talent Pass',
    view: 'Talent Pass',
    edit: 'Edit Talent Pass',
    public_view: 'Public Profile',
  },

  // Breadcrumbs & labels
  breadcrumb: {
    home: 'Home',
    talent_passes: 'Talent Passes',
    current: '{title}',
  },

  shortcut: 'CV 2.0',
  description: 'Your professional digital profile',
}
```

### Status & Visibility

```typescript
status: {
  draft: 'Draft',
  in_review: 'In Review',
  approved: 'Approved',
  active: 'Published',
  completed: 'Completed',
  archived: 'Archived',
},

visibility: {
  private: 'Private',
  public: 'Public',
},

status_badge: {
  draft: 'Draft - Changes not visible',
  in_review: 'Awaiting approval',
  approved: 'Ready to publish',
  active: 'Published & Visible',
  completed: 'Completed',
  archived: 'Archived',
},
```

### Sections & Fields

```typescript
sections: {
  // Main sections
  profile: 'Profile',
  skills: 'Skills',
  experience: 'Experience',
  credentials: 'Credentials',
  summary: 'Summary',

  // Section labels
  skills: {
    label: 'Skills & Expertise',
    help_text: 'Add your professional skills with proficiency levels',
    empty_state: {
      title: 'No skills added yet',
      description: 'Start by adding your key professional skills',
      action: 'Add Your First Skill',
    },
  },

  experience: {
    label: 'Professional Experience',
    help_text: 'Showcase your career journey and achievements',
    empty_state: {
      title: 'No experience added yet',
      description: 'Add roles, companies, and timelines to build your experience profile',
      action: 'Add Experience',
    },
  },

  credentials: {
    label: 'Certifications & Credentials',
    help_text: 'Include degrees, certifications, and achievements',
    empty_state: {
      title: 'No credentials added yet',
      description: 'Add your educational background and professional certifications',
      action: 'Add Credential',
    },
  },

  profile: {
    label: 'Profile Information',
    title: 'Title',
    summary: 'Summary',
    title_placeholder: 'e.g., Senior Product Manager',
    summary_placeholder: 'Write a brief summary about yourself...',
  },
},
```

### Skills Management

```typescript
skills: {
  add: 'Add Skill',
  edit: 'Edit Skill',
  remove: 'Remove Skill',
  
  form: {
    name_label: 'Skill Name',
    name_placeholder: 'e.g., Product Leadership',
    proficiency_label: 'Proficiency Level',
    years_label: 'Years of Experience',
    years_placeholder: 'e.g., 5',
  },

  proficiency_levels: {
    beginner: 'Beginner',
    intermediate: 'Intermediate',
    advanced: 'Advanced',
    expert: 'Expert',
  },

  endorsements: '{count} Endorsements',
  no_endorsements: 'Be the first to endorse',

  actions: {
    endorse: 'Endorse',
    edit: 'Edit',
    remove: 'Remove Skill',
  },
},
```

### Experience Management

```typescript
experience: {
  add: 'Add Experience',
  edit: 'Edit Experience',
  remove: 'Remove Experience',

  form: {
    title_label: 'Job Title',
    title_placeholder: 'e.g., Product Manager',
    company_label: 'Company',
    company_placeholder: 'e.g., Tech Corp',
    employment_type_label: 'Employment Type',
    location_label: 'Location',
    location_placeholder: 'e.g., San Francisco, CA',
    start_date_label: 'Start Date',
    end_date_label: 'End Date',
    is_current_label: 'I currently work here',
    description_label: 'Description',
    description_placeholder: 'Describe your responsibilities and achievements...',
  },

  employment_types: {
    full_time: 'Full-time',
    part_time: 'Part-time',
    contract: 'Contract',
    freelance: 'Freelance',
    internship: 'Internship',
  },

  current_position: 'Current',
  years: '{years} years',
  months: '{months} months',
  
  timeline_view: 'Timeline',
  list_view: 'List View',
},
```

### Credentials Management

```typescript
credentials: {
  add: 'Add Credential',
  edit: 'Edit Credential',
  remove: 'Remove Credential',

  form: {
    title_label: 'Credential Title',
    title_placeholder: 'e.g., MBA',
    issuer_label: 'Issuing Organization',
    issuer_placeholder: 'e.g., Harvard University',
    issue_date_label: 'Issue Date',
    expiry_date_label: 'Expiration Date',
    expiry_date_placeholder: 'Leave empty if no expiration',
    credential_url_label: 'Credential URL',
    credential_url_placeholder: 'https://...',
    credential_id_label: 'Credential ID',
    credential_id_placeholder: 'Optional ID from issuer',
  },

  expires: 'Expires {date}',
  expired: 'Expired',
  no_expiration: 'No expiration',
  view: 'View Credential',
},
```

### Completeness Indicator

```typescript
completeness: {
  label: 'Profile Completeness',
  percentage: '{percentage}%',
  help_text: 'Complete all sections to maximize visibility',
  
  levels: {
    empty: 'Getting started...',
    partial: 'Profile growing',
    good: 'Looking solid',
    excellent: 'Profile complete',
  },

  missing_elements: {
    title: 'Add your title',
    summary: 'Write a summary',
    skills: 'Add at least 3 skills',
    experience: 'Include your experience',
    credentials: 'Add a credential',
  },

  sync_status: 'Syncing with neural...',
  sync_complete: 'Profile synced ✓',
},
```

### Actions & Buttons

```typescript
actions: {
  create: 'Create Talent Pass',
  edit: 'Edit',
  delete: 'Delete',
  publish: 'Publish',
  unpublish: 'Unpublish',
  archive: 'Archive',
  unarchive: 'Restore',
  clone: 'Duplicate',
  export: 'Export',
  share: 'Share',
  save: 'Save Changes',
  cancel: 'Cancel',
  confirm: 'Confirm',
  download: 'Download',
  copy_link: 'Copy Link',
  
  view_public: 'View Public Profile',
  view_all: 'View All',
  manage: 'Manage',
},
```

### Dialogs & Modals

```typescript
dialogs: {
  create_talent_pass: {
    title: 'Create New Talent Pass',
    description: 'Start building your professional profile',
    button_label: 'Create',
  },

  edit_skill: {
    title: 'Edit Skill',
    button_label: 'Update Skill',
  },

  export: {
    title: 'Export Talent Pass',
    description: 'Choose your export format',
    formats: {
      pdf: 'PDF Resume',
      json: 'JSON Data',
      linkedin: 'LinkedIn Format',
    },
  },

  share: {
    title: 'Share Your Talent Pass',
    description: 'Generate a public link to share',
    expires_label: 'Expiration (days)',
    copy_success: 'Link copied to clipboard!',
    button_label: 'Generate Link',
  },

  delete: {
    title: 'Delete Talent Pass?',
    description: 'This action cannot be undone. All data will be permanently deleted.',
    confirm_button: 'Delete',
    cancel_button: 'Cancel',
  },

  archive: {
    title: 'Archive Talent Pass?',
    description: 'You can restore it anytime from the Archive section.',
  },

  publish: {
    title: 'Publish Talent Pass?',
    description: 'Your profile will be visible to others when searching by skills.',
  },
},
```

### Messages & Feedback

```typescript
messages: {
  // Success messages
  created_success: 'Talent Pass created successfully!',
  updated_success: 'Changes saved successfully!',
  published_success: 'Talent Pass published!',
  archived_success: 'Talent Pass archived',
  deleted_success: 'Talent Pass deleted',
  cloned_success: 'Talent Pass duplicated successfully',
  exported_success: 'Export started. Download link will appear shortly.',
  shared_success: 'Public link generated!',
  skill_added: 'Skill added successfully',
  skill_removed: 'Skill removed',
  skill_updated: 'Skill updated',

  // Error messages
  error_title: 'Oops!',
  create_failed: 'Failed to create Talent Pass',
  update_failed: 'Failed to save changes',
  delete_failed: 'Failed to delete',
  load_failed: 'Failed to load profile',
  network_error: 'Network error. Please try again.',
  invalid_form: 'Please fill in all required fields',

  // Loading states
  loading: 'Loading...',
  creating: 'Creating...',
  updating: 'Saving...',
  deleting: 'Deleting...',
  exporting: 'Preparing export...',

  // Empty states
  no_results: 'No Talent Passes found',
  no_results_search: 'No results for "{query}"',
},
```

### Search & Filtering

```typescript
search: {
  placeholder: 'Search talents...',
  filter_by: 'Filter by',
  status: 'Status',
  visibility: 'Visibility',
  sort_by: 'Sort by',
  
  sort_options: {
    newest: 'Newest',
    oldest: 'Oldest',
    alphabetical: 'A-Z',
    most_viewed: 'Most Viewed',
  },

  clear_filters: 'Clear Filters',
  no_filters: 'No active filters',
},
```

### Micro-copy

```typescript
microcopy: {
  // On hover
  hover_edit: 'Click to edit',
  hover_delete: 'Remove this item',
  hover_endorse: 'Endorse this skill',

  // On empty state
  empty_start: 'Start by creating your first Talent Pass',
  empty_action: 'Get Started',

  // On loading
  skeleton_loading: 'Building your profile...',

  // On sync
  syncing_neural: 'Syncing neural patterns...',
  sync_complete: 'Profile synced',

  // Confirmations
  confirm_delete: 'Are you sure? This cannot be undone.',
  confirm_archive: 'Archive this profile? You can restore it anytime.',

  // Time-based
  created_today: 'Created today',
  modified_today: 'Modified today',
  modified_ago: 'Modified {time} ago',

  // Privacy
  shared_publicly: 'Shared publicly',
  private_only: 'Private - only you can see this',
},
```

### Accessibility

```typescript
accessibility: {
  // ARIA labels
  open_menu: 'Open options menu',
  close_menu: 'Close menu',
  expand_section: 'Expand {section}',
  collapse_section: 'Collapse {section}',
  
  // Skip links
  skip_to_main: 'Skip to main content',
  skip_to_search: 'Skip to search',

  // Form labels
  required: 'Required field',
  optional: 'Optional',
},
```

---

## Integration Steps

1. **Add to `resources/js/i18n.ts`:**
   ```typescript
   const messages = {
     en: {
       talent_pass: { /* keys above */ },
     },
     es: {
       talent_pass: { /* Spanish translations */ },
     },
   }
   ```

2. **In Components:**
   ```vue
   <script setup lang="ts">
   import { useI18n } from 'vue-i18n'
   
   const { t } = useI18n()
   </script>
   
   <template>
     <h1>{{ t('talent_pass.title.list') }}</h1>
     <button>{{ t('talent_pass.actions.create') }}</button>
   </template>
   ```

3. **Fallback for untranslated keys:**
   - Defaults to English if ES missing
   - Use `t('key', 'Fallback text')` for safety

---

## Status: Ready

All keys prepared. Ready to integrate into i18n.ts during Phase 1 of frontend sprint (30 min pre-work).
