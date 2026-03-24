(() => {
  // resources/js/landing.js
  console.log("Landing loaded");
  document.querySelectorAll('a[href^="#"]').forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      const el = document.querySelector(link.getAttribute("href"));
      if (el) el.scrollIntoView({ behavior: "smooth" });
    });
  });
  var sections = document.querySelectorAll("section[id]");
  var navLinks = document.querySelectorAll(".nav a");
  window.addEventListener("scroll", () => {
    let current = "";
    sections.forEach((sec) => {
      const top = sec.offsetTop - 100;
      if (window.scrollY >= top) current = sec.id;
    });
    navLinks.forEach((link) => {
      link.style.color = "#8b949e";
      if (link.getAttribute("href") === "#" + current) {
        link.style.color = "#fff";
      }
    });
  });
  var revealEls = document.querySelectorAll(".card, .step, pre");
  var observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = 1;
        entry.target.style.transform = "translateY(0)";
      }
    });
  }, {
    threshold: 0.1
  });
  revealEls.forEach((el) => {
    el.style.opacity = 0;
    el.style.transform = "translateY(20px)";
    el.style.transition = "0.5s ease";
    observer.observe(el);
  });
  var codeEl = document.querySelector(".hero-code code");
  if (codeEl) {
    let type = function() {
      if (i < text.length) {
        codeEl.textContent += text[i];
        i++;
        setTimeout(type, 20);
      }
    };
    type2 = type;
    const text = codeEl.textContent;
    codeEl.textContent = "";
    let i = 0;
    type();
  }
  var type2;
  document.querySelectorAll(".btn").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const circle = document.createElement("span");
      circle.classList.add("ripple");
      const rect = btn.getBoundingClientRect();
      circle.style.left = e.clientX - rect.left + "px";
      circle.style.top = e.clientY - rect.top + "px";
      btn.appendChild(circle);
      setTimeout(() => circle.remove(), 500);
    });
  });
})();
//# sourceMappingURL=landing.js.map
