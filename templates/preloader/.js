// preloader
let shownAt = new Ndate().getTime()
window.preloader = function preloader() {
  if (!$("#preloader").hasClass("hidden")) { // Hiding preloader
    let till_2_seconds = 1000 - new Ndate().getTime() + shownAt
    setTimeout(function () {
      $("#preloader").toggleClass('hidden')
    }, till_2_seconds)
  }
  else { // Showing Preloader
    shownAt = new Ndate().getTime()
    $("#preloader").toggleClass('hidden')
  }
}