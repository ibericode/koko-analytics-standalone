/* DATEPICKER */
// update date_start and date_end <input>'s whenever a preset is selected
var datePresetSelect = document.querySelector('#date-range-input');
var dateStartInput = document.querySelector('#date-start-input');
var dateEndInput = document.querySelector('#date-end-input');
datePresetSelect && datePresetSelect.addEventListener('change', function() {
  dateStartInput.disabled = true;
  dateEndInput.disabled = true;
  this.form.submit();
});

// set <select> value for date preset/view to custom whenever date input is used
function setPresetToCustom() {
  datePresetSelect.value = 'custom';
}

dateStartInput && dateStartInput.addEventListener('change', setPresetToCustom);
dateEndInput && dateEndInput.addEventListener('change', setPresetToCustom);

// click "prev date range" or "next date range" when using arrow keys
document.addEventListener('keydown', function (evt) {
  if (evt.defaultPrevented) {
    return; // Do nothing if the event was already processed
  }

  switch (evt.key) {
  case 'ArrowLeft':
    document.querySelector('.ka-datepicker--quicknav-prev').click();
    break;
  case 'ArrowRight':
    document.querySelector('.ka-datepicker--quicknav-next').click();
    break;
  }
})
/* END DATEPICKER */



/* AUTO RELOAD: every 61 seconds without mouse activity, reload the page (but only if tab is active) */
var reloadTimeout = window.setTimeout(reloadIfActive, 61000);
function reloadIfActive() {
  if (!document.hidden) {
    window.location.reload();
  } else {
    // if document hidden, try again in 61s
    reloadTimeout = window.setTimeout(reloadIfActive, 61000);
  }
}
document.addEventListener('mouseover', function() {
  window.clearTimeout(reloadTimeout);
  reloadTimeout = window.setTimeout(reloadIfActive, 61000);
})
/* END AUTO RELOAD */



/* CHART */
var chart = document.querySelector('#ka-chart');
(function() {
  if (!chart) return;

  var tooltip = document.querySelector('.ka-chart--tooltip');
  var bars = chart.querySelectorAll('.bars g');
  if (bars.length === 0) return;

  var barWidth;

  // move tooltip to outer scope (so it's easier to position absolutely)
  tooltip.remove();
  document.body.appendChild(tooltip);

  chart.addEventListener('mouseover', function(e) {
    if (e.target.tagName !== 'rect') {
      tooltip.style.display = 'none'
      return;
    }

    // update tooltip content
    var data = e.target.parentElement.dataset;
    tooltip.querySelector('.ka-chart--tooltip-heading').textContent = data.date;
    tooltip.querySelector('.ka--visitors').children[0].textContent = data.visitors;
    tooltip.querySelector('.ka--pageviews').children[0].textContent = data.pageviews;

    // set tooltip position relative to top-left of document
    tooltip.style.display = 'block';
    var scrollY = window.pageYOffset !== undefined ? window.pageYOffset : window.scrollTop
    var scrollX = window.pageXOffset !== undefined ? window.pageXOffset : window.scrollLeft
    var styles = e.target.parentElement.getBoundingClientRect() // <g> element
    var left = Math.round(styles.left + scrollX - 0.5 * tooltip.clientWidth + 0.5 * barWidth) + 'px';
    var top = Math.round(styles.top + scrollY - tooltip.clientHeight) + 'px';
    tooltip.style.left = left;
    tooltip.style.top = top;
  })

  var yTicks = chart.querySelectorAll('.axes-y text');
  var i;
  var leftOffset = 0;
  for (i = 0; i < yTicks.length; i++) {
    leftOffset = Math.max(leftOffset, 8 + Math.max(5, yTicks[i].textContent.length * 8));
  }
  var tickWidth = (chart.clientWidth - leftOffset) / bars.length;
  barWidth = tickWidth - 2;

  // update width of each bar now that we know the client width
  bars[0].parentElement.style.display = 'none';
  for (i = 0; i < bars.length; i++) {
    var x = i * tickWidth + leftOffset + 1;

    // pageviews <rect>
    bars[i].children[0].setAttribute('x', x);
    bars[i].children[0].setAttribute('width', barWidth);

    // visitors <rect>
    bars[i].children[1].setAttribute('x', x);
    bars[i].children[1].setAttribute('width', barWidth);

    // tick <line>
    x = i * tickWidth + leftOffset + 0.5 * tickWidth;
    bars[i].children[2].setAttribute('x1', x);
    bars[i].children[2].setAttribute('x2', x);
  }

  bars[0].parentElement.style.display = '';
})();

/* END CHART */




