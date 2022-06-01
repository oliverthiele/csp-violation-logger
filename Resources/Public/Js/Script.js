function initPopover() {
  let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
  let popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl)
  });
}

document.onreadystatechange = function () {
  "use strict";
  if (document.readyState === "interactive") {
    initPopover();
  }
}
