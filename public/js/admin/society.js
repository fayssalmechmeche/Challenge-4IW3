function openSocietyCreateModal() {
  fetch(`/admin/society/new`)
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

function openSocietyEditModal(id) {
  fetch(`/admin/society/edit/${id}`)
    .then((response) => response.text())
    .then((html) => {
      console.log(html);
      document.getElementById("content").innerHTML = html;
      // document.getElementById(
      //   "formEditSociety"
      // ).action = `/admin/society/edit/${id}`;
      displayModal(true);
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function openSocietyShowModal(id) {
  fetch(`/admin/society/show/${id}`)
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

/// AJAX request SOCIETY //// 

function addNewSociety() {
  let form = document.getElementById('formNewSociety');
  let formData = new FormData(form);
  let data = {};
  formData.forEach(function (value, key) {
    data[key] = value;
  });

  fetch('/admin/society/new', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify(data)
  }).then(response => response.json()).then(responseData => {

    addFlash(responseData.success ? 'success' : 'danger', responseData.message);
    refreshCardSociety();

    loadGridSociety();
  }).catch(error => {
    console.error('Erreur:', error);
  });
}

function editSociety(id) {
  let form = document.getElementById('formEditSociety');
  let formData = new FormData(form);
  let data = {};
  formData.forEach(function (value, key) {
    data[key] = value;
  });

  fetch('/admin/society/edit/' + id, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify(data)
  }).then(response => response.json()).then(responseData => {

    addFlash(responseData.success ? 'success' : 'danger', responseData.message);
    refreshCardSociety();

    loadGridSociety();
  }).catch(error => {
    console.error('Erreur:', error);
  });
}


function deleteSociety(id, token) {
  if (confirm('Voulez-vous vraiment supprimer cette société ? Ceci effacera les utilisateurs de la société.')) {
    fetch('/admin/society/delete/' + id + '/' + token, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    }).then(response => response.json()).then(responseData => {

      addFlash(responseData.success ? 'success' : 'danger', responseData.message);
      refreshCardSociety();

      loadGridSociety();
    }).catch(error => {
      console.error('Erreur:', error);
    });
  }

}


function refreshCardSociety() {
  let cardCountSocieties = document.getElementById('cardSocietiesLength');
  fetch('/admin/society', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
  })
    .then(response => {

      if (!response.ok) {
        throw new Error('Erreur lors de la récupération des sociétés');
      }
      return response.json();
    })
    .then(data => {
      cardCountSocieties.innerHTML = data.data.countSocieties
    })
    .catch(error => {
      console.error('Erreur:', error);
    });

}
