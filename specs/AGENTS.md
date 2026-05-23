# Specs AGENTS.md

> OpenSpec conventions for Deshi Fishery. Supplement to root `AGENTS.md`.

## Module ID Prefixes

| Prefix | Module |
|--------|--------|
| AUTH | Authentication & Authorization |
| FARM | Farm Management |
| POND | Pond Management |
| STOCK | Stock/Inventory |
| SALE | Sales |
| FEED | Feed Management |
| MED | Medicine/Medical |
| EXP | Expenses |
| PARTNER | Partners/Workers |
| DASH | Dashboard & Reporting |
| SYNC | Offline Sync |
| UI | UI/UX Improvements |

## Spec Format

```
SPEC {ID}  {Title}
  {Description of observable behaviour}

  PRECONDITION:  {what must be true before}
  POSTCONDITION: {what is guaranteed after}
  INVARIANT:     {condition always true}
  ERROR:         {named failure mode}
```

## Keywords

- `MUST` / `MUST NOT` — non-negotiable
- `SHOULD` — strongly recommended default
- `MAY` — optional
- `INVARIANT` — always true; violation = bug
- `PRECONDITION` — must be true before operation
- `POSTCONDITION` — guaranteed after success
- `ERROR` — named failure mode surfaced explicitly

## Workflow

1. `/opsx propose` — generate spec + design + tasks
2. `/opsx explore` — think through edge cases
3. Write spec in `specs/{module}.md`
4. `/opsx apply` — implement code
5. Verify with tests asserting POSTCONDITION and INVARIANT
6. `/opsx archive` — finalize
7. **After archive**: Update `docs/sprint-progress.md`
8. Commit spec + code together

## Review Checklist

- [ ] Spec uses correct ID prefix
- [ ] All PRECONDITIONs are testable
- [ ] All POSTCONDITIONs are testable
- [ ] INVARIANTs are actually invariant (always true)
- [ ] ERROR cases have named failure modes
- [ ] No ambiguous "SHOULD" without justification
- [ ] Spec references existing specs for dependencies
