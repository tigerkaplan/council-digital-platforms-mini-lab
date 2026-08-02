# Release Checklist

This checklist records release gates for the fictional learning project. It is not a claim that the repository is approved for public release. A checked item records completed accepted work; an unchecked item remains a release gate.

## Repository safety

- [x] Accepted project records state that the service scenario and all demonstration data are fictional.
- [x] Accepted evidence boundaries distinguish local learning work from production systems and integrations.
- [ ] Complete a final public-safety scan of the selected release content immediately before publication.
- [ ] Obtain independent review of the Phase 9 public documentation, release decision and selected evidence context.

## Git-history review

- [x] Complete the separately authorised Phase 9 Git-history review without rewriting history; the result requires a clean-history public export.
- [x] Record the Phase 9 history-related release decision: do not publish the current history directly.

## Tracked files

- [x] Remove the private SQLite binary from the release-facing tree, add a repository-relative ignore rule and retain the reproducible schema, seed, queries and runner.
- [x] Confirm that the Phase 9A clean public release candidate excludes database dumps, environment files, credentials, tokens and generated runtime material; it is an intentionally incomplete configuration-import package, pending final independent review.

## Evidence and media

- [x] Public-safe Priya, workflow and Twig evidence is classified for its limited fictional claims.
- [x] Complete the Phase 9 claim-context review for every item currently selected by public documents or the case study.
- [x] Recheck current public-facing evidence links against their classification in Phase 9.
- [x] Use deterministic public-safe derivatives in place of the two unapproved Studio Pro case-study images, and record the limited approval of the three runtime screenshots.

## Documentation

- [x] Phase 8 independent review of the README, evidence register and release checklist was accepted and closed.
- [x] Complete the Phase 9 documentation consistency check; final independent review of the updated release decision remains required.
- [x] Reconfirm that setup instructions do not imply a public database dump, fresh-install path or deployment path that the repository does not provide.

## Runtime verification

- [x] Accepted local runtime checks recorded Drupal bootstrap, the single-page Webform, postcode behaviour, JSON endpoint, staff preview and Twig boundaries.
- [x] Complete the Phase 9 final local runtime audit with fictional data only; no administrative view or unrelated submission was inspected.
- [ ] Repeat runtime checks in any separately approved future release environment.

## Accessibility-focused verification

- [x] Accepted manual checks cover keyboard use, visible focus, errors, confirmation behaviour, zoom/reflow and narrow viewports within stated conditions.
- [ ] Complete any separately scoped end-to-end screen-reader testing if a release claim requires it.
- [ ] Recheck accessible keyboard and responsive behaviour on the final release candidate.

## Static case-study verification

- [x] The Phase 7 static case study received recorded desktop, mobile, keyboard and link checks.
- [x] Complete Phase 9 static source, local-server, link, asset and bounded-claim checks.
- [x] Confirm that current case-study wording remains bounded to fictional, local and evidence-supported learning work.
- [x] Complete the Phase 9A static case-study and media checks after correcting the two Studio Pro image references.

## GitHub release decision

- [ ] Approve whether this repository may be made public on GitHub after all preceding gates are complete.
- [ ] Confirm repository visibility, selected files and public metadata before changing GitHub visibility.

## Netlify/static deployment decision

- [ ] Approve any static deployment separately after final case-study and media checks.
- [ ] Confirm the deployment contains only selected public-safe files and no environment-specific material.

## Portfolio handoff readiness

- [ ] Approve use of the static case study in a portfolio only after the evidence, history and public-release gates are complete.
- [ ] Confirm all portfolio wording retains the prototype, fictional-data and non-production boundaries.

## Final approval

- [ ] Approve public release only after every unchecked release gate above is resolved and independently reviewed.

### Stop conditions for public release

Do not publish the repository, deploy the static case study or hand it to a portfolio while any of the following remains unresolved:

- independent review of the clean-history export strategy;
- final independent review of the Phase 9A Mendix-media decision and clean release candidate;
- public GitHub approval;
- static-deployment approval; or
- portfolio handoff approval.

**Current result: stop. The repository is not cleared for public release.**
