const logoutBtn = document.getElementById('logoutBtn');

logoutBtn.addEventListener('click', () => {

  let handleLogout = "PHP/handleLogout.php";
  fetch(handleLogout, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    }
  }).then(data => {
    console.log(data);
    if (data.success) {
      // reload the page
      window.location.reload();
    } else {
      alert("Umm... We failed to logout? Please try again.")
    }
  }).catch(e => {
    console.error('Error:', e);
  });
})