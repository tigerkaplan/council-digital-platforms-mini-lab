const DEFAULT_ENDPOINT = "/api/service-request/demo";

const WORKFLOW_STEPS = [
  "New",
  "Awaiting review",
  "Assigned",
  "In progress",
  "Resolved",
  "Closed",
];

const NEXT_ACTION_BY_STATUS = {
  New: "Review request details",
  "Awaiting review": "Confirm asset location",
  Assigned: "Arrange inspection",
  "In progress": "Complete repair work",
  Resolved: "Confirm resolution with the resident",
  Closed: "No further action",
};

const ASSIGNED_TEAM = "Street Lighting";

export function formatRequestType(requestType) {
  return String(requestType || "")
    .split("_")
    .filter(Boolean)
    .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
    .join(" ");
}

export function formatStatus(status) {
  if (status === "new") {
    return "New";
  }

  return String(status || "")
    .split("_")
    .filter(Boolean)
    .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
    .join(" ");
}

export function getEndpoint(search = "") {
  const params = new URLSearchParams(search);
  return params.get("endpoint") || DEFAULT_ENDPOINT;
}

export function buildViewModel(payload) {
  const isImmediateRisk = Boolean(payload?.risk?.immediateSafetyRisk);
  const currentStatus = formatStatus(payload?.status) || "Not available";

  return {
    reference: payload?.reference || "Not available",
    requestType: formatRequestType(payload?.requestType) || "Not available",
    location: payload?.location?.description || "Not available",
    postcode: payload?.location?.postcode || "Not available",
    assetReference: payload?.location?.assetReference || "Not available",
    safetyRisk: isImmediateRisk ? "Immediate safety risk" : "No immediate safety risk",
    riskDetails: payload?.risk?.details || "No additional risk details provided",
    contactName: payload?.contact?.name || "Not available",
    contactEmail: payload?.contact?.email || "Not available",
    assignedTeam: ASSIGNED_TEAM,
    responsibleService: ASSIGNED_TEAM,
    currentStatus,
    nextAction: getNextAction(currentStatus),
  };
}

export function getNextAction(status) {
  return NEXT_ACTION_BY_STATUS[status] || "Review request details";
}

export function createInitialWorkflowState(status = "New", timestamp = new Date()) {
  return {
    currentStatus: status,
    activityHistory: [
      createActivityEntry(
        "Initial request loaded",
        status,
        getNextAction(status),
        timestamp,
      ),
    ],
  };
}

export function createActivityEntry(label, status, nextAction, timestamp = new Date()) {
  return {
    label,
    status,
    nextAction,
    timestamp: formatTimestamp(timestamp),
  };
}

export function formatTimestamp(timestamp) {
  return new Intl.DateTimeFormat("en-GB", {
    dateStyle: "medium",
    timeStyle: "short",
  }).format(timestamp);
}

export function getNextStatus(currentStatus) {
  const currentIndex = WORKFLOW_STEPS.indexOf(currentStatus);

  if (currentIndex === -1 || currentIndex === WORKFLOW_STEPS.length - 1) {
    return currentStatus;
  }

  return WORKFLOW_STEPS[currentIndex + 1];
}

export function advanceWorkflow(workflowState, timestamp = new Date()) {
  const nextStatus = getNextStatus(workflowState.currentStatus);

  if (nextStatus === workflowState.currentStatus) {
    return workflowState;
  }

  const nextAction = getNextAction(nextStatus);

  return {
    currentStatus: nextStatus,
    activityHistory: [
      ...workflowState.activityHistory,
      createActivityEntry("Status changed", nextStatus, nextAction, timestamp),
    ],
  };
}

export function resetWorkflow(timestamp = new Date()) {
  return createInitialWorkflowState("New", timestamp);
}

export function renderItems(items) {
  return items
    .map(
      (item) => `
        <div>
          <dt>${item.label}</dt>
          <dd>${item.value}</dd>
        </div>
      `,
    )
    .join("");
}

export function renderWorkflow(currentStatus) {
  return WORKFLOW_STEPS.map((step) => {
    const isCurrent = step === currentStatus;
    const stateLabel = isCurrent ? "Current preview status" : "Preview step";

    return `
      <li class="workflow-step${isCurrent ? " workflow-step--current" : ""}">
        <strong>${step}</strong>
        <span>${stateLabel}</span>
      </li>
    `;
  }).join("");
}

export function renderActivityHistory(activityHistory) {
  return activityHistory
    .map(
      (entry) => `
        <li>
          <strong>${entry.label}</strong>
          <span>${entry.timestamp}</span>
          <span>Status: ${entry.status}</span>
          <span>Next action: ${entry.nextAction}</span>
        </li>
      `,
    )
    .join("");
}

export function renderPreview(viewModel, workflowState = createInitialWorkflowState(viewModel.currentStatus)) {
  const currentStatus = workflowState.currentStatus;
  const nextAction = getNextAction(currentStatus);
  const isClosed = currentStatus === "Closed";
  const overviewItems = [
    { label: "Reference", value: viewModel.reference },
    { label: "Request type", value: viewModel.requestType },
    {
      label: "Risk",
      value: `<span class="status-pill">${viewModel.safetyRisk}</span>`,
    },
    {
      label: "Status",
      value: `<span class="status-pill">${currentStatus}</span>`,
    },
  ];

  const detailItems = [
    { label: "Reference", value: viewModel.reference },
    { label: "Request type", value: viewModel.requestType },
    { label: "Location", value: viewModel.location },
    { label: "Postcode", value: viewModel.postcode },
    { label: "Streetlight reference", value: viewModel.assetReference },
    { label: "Safety risk", value: viewModel.safetyRisk },
    { label: "Risk details", value: viewModel.riskDetails },
    { label: "Contact name", value: viewModel.contactName },
    { label: "Contact email", value: viewModel.contactEmail },
    { label: "Responsible service", value: viewModel.responsibleService },
    { label: "Assigned team", value: viewModel.assignedTeam },
    { label: "Current status", value: currentStatus },
    { label: "Next action", value: nextAction },
  ];

  return `
    <section class="panel-grid" aria-label="Case preview panels">
      <section class="panel" aria-labelledby="request-overview-heading">
        <h2 id="request-overview-heading">Request overview</h2>
        <dl class="summary-list">
          ${renderItems(overviewItems)}
        </dl>
      </section>

      <section class="panel" aria-labelledby="request-details-heading">
        <h2 id="request-details-heading">Request details</h2>
        <dl class="detail-list">
          ${renderItems(detailItems)}
        </dl>
      </section>
    </section>

    <section class="panel" aria-labelledby="workflow-heading">
      <h2 id="workflow-heading">Workflow</h2>
      <p class="workflow-note">
        This workflow is a non-persistent demonstration. Status changes are not persisted and the source JSON is unchanged.
      </p>
      <div class="workflow-current" aria-live="polite">
        <span>Current status</span>
        <strong>${currentStatus}</strong>
      </div>
      <div class="workflow-actions">
        <button
          class="button"
          type="button"
          data-action="advance-status"
          ${isClosed ? "disabled aria-describedby=\"closed-status-note\"" : ""}
        >
          Advance status
        </button>
        <button class="button button--secondary" type="button" data-action="reset-workflow">
          Reset workflow
        </button>
        ${isClosed ? "<p id=\"closed-status-note\" class=\"button-note\">Advance status is disabled because the workflow is at Closed.</p>" : ""}
      </div>
      <ol class="workflow-list">
        ${renderWorkflow(currentStatus)}
      </ol>
      <p class="supporting-text">
        The preview shows a fictional request flowing from New through to Closed. The activity history exists only in the current browser session.
      </p>
      <section class="activity-panel" aria-labelledby="activity-history-heading">
        <h3 id="activity-history-heading">Activity history</h3>
        <ul class="activity-list">
          ${renderActivityHistory(workflowState.activityHistory)}
        </ul>
      </section>
    </section>
  `;
}

export function renderLoading() {
  return `
    <section class="panel panel--loading" aria-labelledby="loading-heading">
      <h2 id="loading-heading">Loading case preview</h2>
      <p>Loading the fictional service request from the preview endpoint.</p>
    </section>
  `;
}

export function renderError(endpoint) {
  return `
    <section class="panel message message--error" aria-labelledby="error-heading" role="alert">
      <h2 id="error-heading">Case preview unavailable</h2>
      <p>
        The preview could not load data from <code>${endpoint}</code>.
      </p>
      <p>Please try again. If the problem continues, check that the Drupal endpoint is available in DDEV.</p>
      <div class="message__actions">
        <button class="button" type="button" data-action="retry">Try again</button>
      </div>
    </section>
  `;
}

export async function fetchRequestData(endpoint = DEFAULT_ENDPOINT, fetchImpl = fetch) {
  const response = await fetchImpl(endpoint, {
    headers: {
      Accept: "application/json",
    },
  });

  if (!response.ok) {
    throw new Error(`Request failed with status ${response.status}`);
  }

  return response.json();
}

export async function init({
  root = document.getElementById("app"),
  fetchImpl = fetch,
  search = window.location.search,
} = {}) {
  if (!root) {
    return;
  }

  const endpoint = getEndpoint(search);
  root.setAttribute("aria-busy", "true");
  root.innerHTML = renderLoading();

  try {
    const payload = await fetchRequestData(endpoint, fetchImpl);
    const viewModel = buildViewModel(payload);
    let workflowState = createInitialWorkflowState(viewModel.currentStatus);

    const renderWorkflowPreview = (announcement = "") => {
      root.innerHTML = renderPreview(viewModel, workflowState);
      const announcer = document.createElement("p");
      announcer.className = "visually-hidden";
      announcer.setAttribute("aria-live", "polite");
      announcer.textContent = announcement || `Current status: ${workflowState.currentStatus}. Next action: ${getNextAction(workflowState.currentStatus)}.`;
      root.prepend(announcer);

      const advanceButton = root.querySelector('[data-action="advance-status"]');
      const resetButton = root.querySelector('[data-action="reset-workflow"]');

      if (advanceButton) {
        advanceButton.addEventListener("click", () => {
          workflowState = advanceWorkflow(workflowState);
          renderWorkflowPreview(`Status changed to ${workflowState.currentStatus}. Next action: ${getNextAction(workflowState.currentStatus)}.`);
        });
      }

      if (resetButton) {
        resetButton.addEventListener("click", () => {
          workflowState = resetWorkflow();
          renderWorkflowPreview("Workflow reset to New. Next action: Review request details.");
        });
      }
    };

    renderWorkflowPreview();
  } catch (error) {
    root.innerHTML = renderError(endpoint);
    const retryButton = root.querySelector('[data-action="retry"]');

    if (retryButton) {
      retryButton.addEventListener("click", () => {
        init({ root, fetchImpl, search });
      });
    }
  } finally {
    root.setAttribute("aria-busy", "false");
  }
}

if (typeof window !== "undefined" && typeof document !== "undefined") {
  window.addEventListener("DOMContentLoaded", () => {
    init();
  });
}
