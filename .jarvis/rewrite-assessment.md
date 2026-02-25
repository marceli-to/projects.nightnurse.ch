# projects.nightnurse.ch — Rewrite Assessment

## Status
Pending client consultation.

## Current Stack
- Laravel 9
- Vue 2.7 (EOL since Dec 2023)
- Vuex
- Laravel Mix (Webpack)
- TailwindCSS
- Pusher (broadcasting)
- gecche/laravel-multidomain

## Proposed Stack
- Laravel 12
- Vue 3 (Composition API)
- Pinia
- Vite
- TailwindCSS
- Laravel Reverb or Pusher
- Multi-domain: re-evaluate (possibly drop the package)

## Scope
- 165 PHP files
- 118 Vue components (57 views, 60 UI components)
- 52 migrations / ~23 models
- 20 API controllers
- 5 background tasks (cron-based)
- 5 mail classes
- Integrations: Vertec API, DeepL, Mailgun webhooks, Salesforce

## Key Modules
1. Auth (login, invitation, registration)
2. Projects (states, access control, quotes/PDF)
3. Messages (feed, replies, files, broadcasting)
4. Markup / Annotation system (Konva/Fabric, image annotations)
5. File uploads (previews, image processing)
6. Users & Companies (roles, teams, multi-company)
7. Notifications (mail, cron-based)
8. Feedback system
9. SBB-specific flows
10. Quotes (PDF generation, DomPDF)

## Effort Estimate

### Solo human: 200–350h
### Marcelito + Jarvis (with requirements docs): ~7–10 working days

| Day | Focus |
|-----|-------|
| 1 | Stack setup, auth, DB, multi-domain decision |
| 2–3 | Core modules: projects, messages, users, companies |
| 4 | Files, uploads, notifications, mail |
| 5 | Markup/annotation system, quotes |
| 6 | Integrations (Vertec, DeepL, Mailgun), SBB flows |
| 7–10 | QA, edge cases, staging deploy, client sign-off |

## Notes
- Requirements doc per module is critical — eliminates guesswork and back-and-forth
- Markup system: ~half a day implementation + a few hours visual QA
- Vue 2 EOL is the main technical driver for urgency
- Laravel 9 → 12 backend is largely copy-clean, lower risk
- Multi-domain setup needs a decision upfront (keep package vs. restructure)

## Next Steps
- [ ] Client consultation
- [ ] Decision: rewrite yes/no
- [ ] If yes: draft requirements docs per module before starting
