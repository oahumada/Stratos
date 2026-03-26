# Talent Pass API Quick Reference

## Public Endpoints (No Authentication)

### View Public Talent Pass

```
GET /api/talent-pass/{publicId}
Response: { data: TalentPass, completeness: int }
```

---

## Authenticated Endpoints (Requires: `Authorization: Bearer {token}`)

### Talent Pass CRUD

#### List Talent Passes

```
GET /api/talent-passes?page=1
Response: { data: TalentPass[], pagination }
```

#### Create Talent Pass

```
POST /api/talent-passes
Body: {
  title: string,
  summary?: string,
  visibility: 'private'|'public'
}
Response: { data: TalentPass }
```

#### Get Talent Pass

```
GET /api/talent-passes/{id}
Response: { data: TalentPass }
```

#### Update Talent Pass

```
PUT /api/talent-passes/{id}
Body: { title?: string, summary?: string, visibility?: 'private'|'public' }
Response: { data: TalentPass }
```

#### Delete Talent Pass

```
DELETE /api/talent-passes/{id}
Response: 204 No Content
```

### Advanced Operations

#### Publish Talent Pass

```
POST /api/talent-passes/{id}/publish
Response: { message: 'Talent pass published', status: 'published' }
```

#### Archive Talent Pass

```
POST /api/talent-passes/{id}/archive
Response: { message: 'Talent pass archived', status: 'archived' }
```

#### Clone Talent Pass

```
POST /api/talent-passes/{id}/clone
Response: { data: ClonedTalentPass }
```

#### Export Talent Pass

```
GET /api/talent-passes/{id}/export?format=json|linkedin|pdf
Formats:
  - json: JSON structure
  - linkedin: LinkedIn profile format
  - pdf: PDF file download
Response: Varies by format
```

#### Share Talent Pass

```
POST /api/talent-passes/{id}/share
Response: { link: string (public URL), publicId: string (ULID) }
```

---

## Search Endpoints

### Global Search

```
GET /api/search?q=string
Response: { query, count, results: [] }
```

### Search by Skills

```
GET /api/search/skills?skills[]=PHP&skills[]=Laravel
Response: { skills, count, results: [] }
```

### Find by Skill Level

```
GET /api/search/skill-level?skill=PHP&level=expert
Levels: beginner|intermediate|expert|master
Response: { skill, level, count, results: [] }
```

### Find by Company Experience

```
GET /api/search/experience?company=Google&min_years=3
Response: { company, min_years, count, results: [] }
```

### Find by Credential

```
GET /api/search/credential?credential=AWS
Response: { credential, count, results: [] }
```

### Find Similar Talent

```
GET /api/search/similar?talent_pass_id=123
Response: { reference_talent_pass, similar_count, results: [] }
```

---

## Analytics Endpoints

### Get Trending Skills

```
GET /api/analytics/trending?limit=10
Response: { trending: [], total: int }
```

### Analyze Skill Gaps

```
POST /api/analytics/gaps
Body: { target_skills: [string] }
Response: { coverage: float, skills_coverage: {}, gap_analysis: {} }
```

---

## Response Formats

### TalentPass Object

```json
{
    "id": 123,
    "organization_id": 1,
    "people_id": 456,
    "ulid": "01ARZ3NDEKTSV4RRFFQ69G5FAV",
    "title": "Senior Full Stack Developer",
    "summary": "...",
    "status": "draft|in_review|approved|active|completed|archived",
    "visibility": "private|public",
    "views_count": 42,
    "featured_at": "2026-03-26...",
    "skills": [],
    "credentials": [],
    "experiences": [],
    "created_at": "2026-03-26..."
}
```

---

## Error Responses

```json
{
    "message": "Database record not found",
    "exception": "Illuminate\\Database\\Eloquent\\ModelNotFoundException",
    "file": "..."
}
```

### Common Status Codes

- `200 OK` - Success
- `201 Created` - Resource created
- `204 No Content` - Delete success
- `400 Bad Request` - Validation error
- `401 Unauthorized` - Missing/invalid token
- `403 Forbidden` - Authorization denied
- `404 Not Found` - Resource not found
- `500 Server Error` - Server error

---

## Examples

### Create a Talent Pass

```bash
curl -X POST https://stratos.local/api/talent-passes \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Software Engineer",
    "summary": "Full stack developer",
    "visibility": "public"
  }'
```

### Export as LinkedIn

```bash
curl -X GET "https://stratos.local/api/talent-passes/1/export?format=linkedin" \
  -H "Authorization: Bearer $TOKEN"
```

### Get Trending Skills

```bash
curl -X GET "https://stratos.local/api/analytics/trending?limit=5" \
  -H "Authorization: Bearer $TOKEN"
```

### Search by Skills

```bash
curl -X GET "https://stratos.local/api/search/skills?skills[]=PHP&skills[]=Laravel" \
  -H "Authorization: Bearer $TOKEN"
```
