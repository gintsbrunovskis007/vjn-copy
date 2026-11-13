/* Mobile menu */
(function () {
  var toggle = document.querySelector(".menu-toggle");
  var nav = document.getElementById("main-navigation");
  if (!toggle || !nav) return;

  function setOpen(open) {
    nav.classList.toggle("is-open", open);
    toggle.setAttribute("aria-expanded", open ? "true" : "false");
    toggle.setAttribute(
      "aria-label",
      open ? "Aizvērt navigāciju" : "Atvērt navigāciju",
    );
  }

  toggle.addEventListener("click", function () {
    setOpen(toggle.getAttribute("aria-expanded") !== "true");
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && nav.classList.contains("is-open")) {
      setOpen(false);
      toggle.focus();
    }
  });

  document.addEventListener("click", function (e) {
    if (
      nav.classList.contains("is-open") &&
      !nav.contains(e.target) &&
      !toggle.contains(e.target)
    ) {
      setOpen(false);
    }
  });

  window
    .matchMedia("(min-width: 769px)")
    .addEventListener("change", function (m) {
      if (m.matches) setOpen(false);
    });
})();
