(function () {
  const root = document.documentElement;
  const body = document.body;
  const toggle = document.querySelector(".menu-toggle");
  const drawer = document.querySelector(".mobile-drawer");
  const closeButton = document.querySelector(".mobile-drawer__close");
  const backdrop = document.querySelector(".mobile-drawer-backdrop");
  const currentSection = document.querySelector("#current-section");
  const sectionLabels = new Map([
    ["overview", "Overview"],
    ["users", "Users"],
    ["webform", "Webform"],
    ["accessibility", "Accessibility"],
    ["workflow", "Workflow"],
    ["mendix", "Mendix"],
    ["interoperability", "Interoperability"],
    ["sql", "SQL"],
    ["devops", "DevOps"],
    ["boomi", "Boomi design"],
    ["evidence", "Evidence"],
    ["limitations", "Limitations"],
    ["learning", "Learning"],
    ["next-steps", "Next steps"]
  ]);

  if (!toggle || !drawer || !closeButton || !backdrop) {
    return;
  }

  root.classList.add("js");
  drawer.setAttribute("aria-hidden", "true");
  drawer.inert = true;
  updateCurrentSection("overview");

  const focusableSelector = [
    "a[href]",
    "button:not([disabled])",
    "textarea:not([disabled])",
    "input:not([disabled])",
    "select:not([disabled])",
    "[tabindex]:not([tabindex='-1'])"
  ].join(",");

  function getDrawerFocusable() {
    return Array.from(drawer.querySelectorAll(focusableSelector)).filter((element) => {
      return element.offsetParent !== null || element === closeButton;
    });
  }

  function updateCurrentSection(sectionId) {
    if (!currentSection || !sectionLabels.has(sectionId)) {
      return;
    }

    currentSection.textContent = sectionLabels.get(sectionId);
  }

  function openDrawer() {
    drawer.inert = false;
    drawer.setAttribute("aria-hidden", "false");
    drawer.classList.add("is-open");
    body.classList.add("drawer-open");
    backdrop.hidden = false;
    toggle.setAttribute("aria-expanded", "true");
    closeButton.focus();
  }

  function closeDrawer() {
    drawer.classList.remove("is-open");
    body.classList.remove("drawer-open");
    backdrop.hidden = true;
    drawer.setAttribute("aria-hidden", "true");
    drawer.inert = true;
    toggle.setAttribute("aria-expanded", "false");
    toggle.focus();
  }

  function handleKeydown(event) {
    if (!drawer.classList.contains("is-open")) {
      return;
    }

    if (event.key === "Escape") {
      event.preventDefault();
      closeDrawer();
      return;
    }

    if (event.key !== "Tab") {
      return;
    }

    const focusable = getDrawerFocusable();
    if (focusable.length === 0) {
      event.preventDefault();
      closeButton.focus();
      return;
    }

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
      return;
    }

    if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  }

  toggle.addEventListener("click", function () {
    if (drawer.classList.contains("is-open")) {
      closeDrawer();
      return;
    }

    openDrawer();
  });

  closeButton.addEventListener("click", function () {
    closeDrawer();
  });

  backdrop.addEventListener("click", function () {
    closeDrawer();
  });

  drawer.addEventListener("click", function (event) {
    const link = event.target.closest("a[href]");

    if (link) {
      const sectionId = link.hash.slice(1);
      updateCurrentSection(sectionId);
      closeDrawer();
    }
  });

  document.addEventListener("keydown", handleKeydown);

  document.addEventListener("click", function (event) {
    const link = event.target.closest("a[href^='#']");

    if (!link || !sectionLabels.has(link.hash.slice(1))) {
      return;
    }

    updateCurrentSection(link.hash.slice(1));
  });

  if ("IntersectionObserver" in window) {
    const sections = Array.from(sectionLabels.keys())
      .map((sectionId) => document.getElementById(sectionId))
      .filter(Boolean);
    const visibleSections = new Map();
    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          visibleSections.set(entry.target.id, entry.intersectionRatio);
          return;
        }

        visibleSections.delete(entry.target.id);
      });

      if (visibleSections.size === 0) {
        return;
      }

      const activeSection = Array.from(visibleSections.entries()).sort(function (first, second) {
        return second[1] - first[1];
      })[0][0];

      updateCurrentSection(activeSection);
    }, {
      rootMargin: "-35% 0px -50% 0px",
      threshold: [0, 0.25, 0.5, 0.75]
    });

    sections.forEach(function (section) {
      observer.observe(section);
    });

    window.addEventListener("scroll", function () {
      const nearBottom = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 8;

      if (nearBottom) {
        updateCurrentSection("next-steps");
      }
    }, { passive: true });
  }
})();
