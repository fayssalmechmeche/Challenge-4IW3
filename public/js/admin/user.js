function openUserCreateModal() {
  fetch(`/admin/user/new`)
    .then((response) => response.text())
    .then((html) => {
      console.log(html);
      document.getElementById("content").innerHTML = html;
      displayModal(true);
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function openUserEditModal(id) {
  fetch(`/admin/user/edit/${id}`)
    .then((response) => response.text())
    .then((html) => {
      console.log(html);
      document.getElementById("content").innerHTML = html;
      displayModal(true);
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function openUserShowModal(id) {
  fetch(`/admin/user/show/${id}`)
    .then((response) => response.text())
    .then((html) => {
      console.log(html);
      document.getElementById("content").innerHTML = html;
      displayModal(true);
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function openSocietyCreateModal() {
  fetch(`/admin/society/new`)
    .then((response) => response.text())
    .then((html) => {
      document.getElementById("content").innerHTML = html;
      displayModal(true);
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function displayModal(isVisibleBoolean) {
  if (isVisibleBoolean) {
    document.getElementById("modal_content_id").style.display = "block";
    document.getElementById("modal_id").style.display = "block";
    setTimeout(() => {
      document.getElementById("modal_content_id").style.top = "50%";
    }, 20);
  } else {
    document.getElementById("modal_content_id").style.top = "-500px";
    setTimeout(() => {
      document.getElementById("modal_content_id").style.display = "none";
      document.getElementById("modal_id").style.display = "none";
    }, 200);
  }
}

/// AJAX request USER //// 

function addNewUser() {
  let form = document.getElementById('formNewUser');
  let formData = new FormData(form);
  let data = {};
  formData.forEach(function (value, key) {
    data[key] = value;
  });
  fetch('/admin/user/new', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify(data)
  }).then(response => response.json()).then(responseData => {

    addFlash(responseData.success ? 'success' : 'danger', responseData.message);
    refreshCardUser();

    loadGridUser();
  }).catch(error => {
    console.error('Erreur:', error);
  });
}
function deleteUser(id, token) {
  console.log('Deleting user');
  fetch('/admin/user/delete/' + id + '/' + token, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    }
  }).then(response => response.json()).then(responseData => {

    addFlash(responseData.success ? 'success' : 'danger', responseData.message);
    refreshCardUser();

    loadGridUser();
  }).catch(error => {
    console.error('Erreur:', error);
  });

}
function editUser(id) {
  let form = document.getElementById('formEditUser');
  let formData = new FormData(form);
  let data = {};
  formData.forEach(function (value, key) {
    data[key] = value;
  });

  fetch('/admin/user/edit/' + id, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify(data)
  }).then(response => response.json()).then(responseData => {

    addFlash(responseData.success ? 'success' : 'danger', responseData.message);
    refreshCardUser();

    loadGridUser();
  }).catch(error => {
    console.error('Erreur:', error);
  });
}



function refreshCardUser() {
  let cardCountUsers = document.getElementById('cardNumberLength');
  let cardCountUsersVerified = document.getElementById('cardUserVerfied');
  let cardCountUsersNotVerified = document.getElementById('cardUserNotVerfied');
  fetch('/admin/user', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
  })
    .then(response => {

      if (!response.ok) {
        throw new Error('Erreur lors de la récupération des utilisateurs');
      }
      return response.json();
    })
    .then(data => {
      cardCountUsers.innerHTML = data.data.countUsers
      cardCountUsersVerified.innerHTML = data.data.countUsersVerified
      cardCountUsersNotVerified.innerHTML = data.data.countUsersNotVerified
    })
    .catch(error => {
      console.error('Erreur:', error);
    });

}