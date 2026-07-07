Execute targeted UI refinements, implement data-driven summary statistics, and fix an image rendering bug across the admin panel.
Strict Execution Constraints

    Targeted Modifications Only: Do not overwrite entire files or refactor unrelated codebases. Limit edits strictly to the component or lines requiring changes.

    DRY Principle & Integration: Reuse existing functions, hooks, utilities, and API selectors. Do not recreate existing features.

    Regression Control: Ensure modifications do not break existing business logic, states, or adjacent components.

    Design System Adherence: Do not use hardcoded hex codes or inline styles for colors. Utilize the existing global CSS variables/design tokens (e.g., --brand-primary). Do not modify the core site configuration codebase.

Task Breakdown
Task 1: UI Refinement & Styling (/admin/status)

    Component: page-header & User/Skill/TV Display lists.

    Requirements:

        Remove the slider menu from the page-header component.

        Convert the display layout for users, skills, and tv display into ordered (numbered) lists.

        Refer to the provided UI reference images for exact visual alignment.

        Styling: Ensure the new list elements dynamically inherit the theme colors using the global design tokens (e.g., var(--brand-primary)).

    Scope: Visual/CSS changes only. Do not modify or overwrite the underlying component lifecycle, event handlers, or data flows.

Task 2: Dynamic Summary Stats Implementation (/admin/tv)

    Route: /admin/tv?factory=[factory]&shift=[A/B]

    Component: summary-stats

    Requirements:

        Data Fetching: Integrate the existing data layer from the /admin/skills resource/endpoint to dynamically read operator skill metrics based on the active factory and shift query parameters.

        Aggregation Logic: Categorize the total headcount into three distinct skill tiers based on their percentage scores:

            Multi-skill ≥75%: Skill score is greater than or equal to 75%.

            Pada pengembangan: Skill score is between 40% and 74% (inclusive).

            Operator baru (<40%): Skill score is strictly less than 40%.

        UI Display Output Example:

            "20 Total operator, 10 Multi-skill ≥75%, 5 Pada pengembangan, 5 Operator baru (<40%)"

Task 3: Image Upload & Display Bug Fix (/admin/floor-plan-manager)

    Route: Floor Plan Management page.

    Issue: Users can successfully upload images, but the uploaded images fail to render/display on the UI.

    Requirements:

        Investigate the image upload component and state management lifecycle.

        Verify if the issue stems from an incorrect relative/absolute asset path, a missing backend URL prefix, a state sync delay after upload, or a broken image source URL string.

        Fix the rendering logic so the image updates and displays immediately upon a successful upload response.

How to proceed:

    Review the existing components for the routes specified above.

    Propose the exact file paths and code diffs before applying the changes.