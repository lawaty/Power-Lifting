let page = window.location.pathname.split('/').pop()
// Need to sign in
if (page != 'membership' && page != 'membership.html' && (!local.get('token') || !local.get('id'))) {
  local.remove('token')
  local.remove('id')
  window.location = "membership";
}

// Already signed in
if ((page == 'membership' || page == 'membership.html') && local.get('token') && local.get('id')) {
  window.location = "home"
}

function getRandomInt(min, max) {
  min = Math.ceil(min);
  max = Math.floor(max);
  return Math.floor(Math.random() * (max - min + 1)) + min;
}